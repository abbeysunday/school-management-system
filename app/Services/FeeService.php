<?php

namespace App\Services;

use App\Models\ClassArm;
use App\Models\FeeStructure;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\StudentFeeLedger;
use App\Models\Term;

class FeeService
{
    /**
     * Generate ledger entries for a single student in a term
     */
    public function generateLedgerForStudent(Student $student, Term $term): int
    {
        $enrollment = $student->currentEnrollment;
        if (!$enrollment) return 0;

        $classLevelId = $enrollment->classArm->class_level_id;
        $structures = FeeStructure::where('class_level_id', $classLevelId)
            ->where('term_id', $term->id)
            ->get();

        $created = 0;
        foreach ($structures as $structure) {
            $ledger = StudentFeeLedger::firstOrCreate(
                [
                    'student_id'       => $student->id,
                    'fee_structure_id' => $structure->id,
                    'term_id'          => $term->id,
                ],
                [
                    'original_amount' => $structure->amount,
                    'discount_amount' => 0,
                    'discount_reason' => null,
                    'net_amount'      => $structure->amount,
                    'amount_paid'     => 0,
                    'status'          => 'Unpaid',
                ]
            );

            if ($ledger->wasRecentlyCreated) $created++;
        }

        return $created;
    }

    /**
     * Generate ledger for all students in a class arm
     */
    public function generateLedgerForArm(ClassArm $classArm, Term $term): int
    {
        $enrollments = StudentEnrollment::where('class_arm_id', $classArm->id)
            ->where('session_id', $term->session_id)
            ->where('is_active', true)
            ->with('student')
            ->get();

        $total = 0;
        foreach ($enrollments as $enrollment) {
            $total += $this->generateLedgerForStudent($enrollment->student, $term);
        }

        return $total;
    }

    /**
     * Generate ledger for ALL students in current term
     */
    public function generateLedgerForAllStudents(Term $term): int
    {
        $enrollments = StudentEnrollment::where('session_id', $term->session_id)
            ->where('is_active', true)
            ->with('student')
            ->get();

        $total = 0;
        foreach ($enrollments as $enrollment) {
            $total += $this->generateLedgerForStudent($enrollment->student, $term);
        }

        return $total;
    }

    /**
     * Apply discount/scholarship to a ledger item
     */
    public function applyDiscount(int $ledgerId, float $amount, ?string $reason = null): StudentFeeLedger
    {
        $ledger = StudentFeeLedger::lockForUpdate()->findOrFail($ledgerId);

        if ($amount >= $ledger->original_amount) {
            throw new \InvalidArgumentException('Discount cannot exceed original amount');
        }

        $ledger->discount_amount = $amount;
        $ledger->discount_reason = $reason;
        $ledger->net_amount = $ledger->original_amount - $amount;

        // Recalculate status based on new net amount
        if ($ledger->amount_paid >= $ledger->net_amount) {
            $ledger->status = 'Paid';
        } elseif ($ledger->amount_paid > 0) {
            $ledger->status = 'Partial';
        } else {
            $ledger->status = 'Unpaid';
        }

        $ledger->save();

        return $ledger;
    }

    /**
     * Remove discount from a ledger item
     */
    public function removeDiscount(int $ledgerId): StudentFeeLedger
    {
        $ledger = StudentFeeLedger::lockForUpdate()->findOrFail($ledgerId);

        $ledger->discount_amount = 0;
        $ledger->discount_reason = null;
        $ledger->net_amount = $ledger->original_amount;

        if ($ledger->amount_paid >= $ledger->net_amount) {
            $ledger->status = 'Paid';
        } elseif ($ledger->amount_paid > 0) {
            $ledger->status = 'Partial';
        } else {
            $ledger->status = 'Unpaid';
        }

        $ledger->save();

        return $ledger;
    }

    /**
     * Distribute payment across unpaid ledger items (oldest first)
     */
    public function allocatePayment(\App\Models\Payment $payment): void
    {
        $remaining = $payment->amount;

        $ledgerItems = StudentFeeLedger::where('student_id', $payment->student_id)
            ->where('term_id', $payment->term_id)
            ->where('status', '!=', 'Paid')
            ->whereRaw('net_amount - amount_paid > 0')
            ->orderBy('created_at')
            ->lockForUpdate()
            ->get();

        foreach ($ledgerItems as $ledger) {
            if ($remaining <= 0) break;

            $balance = $ledger->net_amount - $ledger->amount_paid;
            $toPay = min($remaining, $balance);

            \App\Models\PaymentAllocation::create([
                'payment_id'       => $payment->id,
                'ledger_id'        => $ledger->id,
                'amount_allocated' => $toPay,
            ]);

            $ledger->amount_paid += $toPay;
            $ledger->status = $ledger->amount_paid >= $ledger->net_amount ? 'Paid' : 'Partial';
            $ledger->save();

            $remaining -= $toPay;
        }

        if ($remaining > 0) {
            \Log::info('Overpayment detected', [
                'payment_id' => $payment->id,
                'remaining'  => $remaining,
            ]);
        }
    }
}
