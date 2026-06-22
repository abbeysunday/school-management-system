<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ClassArm;
use App\Models\FeeStructure;
use App\Models\StudentFeeLedger;
use App\Models\Term;

trait GeneratesFeeLedger
{
    /**
     * Generates fee ledger entries for a student based on their class and the current term's fee structure.
     * It uses firstOrCreate, so it will not overwrite existing entries, only add new ones.
     */
    private function generateFeeLedger(int $studentId, ClassArm $classArm, Term $term): void
    {
        $feeStructures = FeeStructure::where('class_level_id', $classArm->class_level_id)
            ->where('term_id', $term->id)
            ->get();

        foreach ($feeStructures as $fee) {
            StudentFeeLedger::firstOrCreate(
                [
                    'student_id'       => $studentId,
                    'fee_structure_id' => $fee->id,
                    'term_id'          => $term->id,
                ],
                [
                    'original_amount' => $fee->amount,
                    'discount_amount' => 0.00,
                    'net_amount'      => $fee->amount,
                    'amount_paid'     => 0.00,
                    'status'          => 'Unpaid',
                ]
            );
        }
    }
}
