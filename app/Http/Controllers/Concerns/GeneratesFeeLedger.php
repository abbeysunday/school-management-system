<?php

namespace App\Http\Controllers\Concerns;

use App\Models\ClassArm;
use App\Models\FeeStructure;
use App\Models\StudentFeeLedger;
use App\Models\Term;

trait GeneratesFeeLedger
{
    protected function generateFeeLedger(int $studentId, ClassArm $classArm, Term $term): void
    {
        $structures = FeeStructure::where('class_level_id', $classArm->class_level_id)
            ->where('term_id', $term->id)
            ->get();

        foreach ($structures as $structure) {
            StudentFeeLedger::firstOrCreate(
                [
                    'student_id'       => $studentId,
                    'fee_structure_id' => $structure->id,
                    'term_id'          => $term->id,
                ],
                [
                    'original_amount' => $structure->amount,
                    'discount_amount' => 0,
                    'net_amount'      => $structure->amount,
                    'amount_paid'     => 0,
                    'status'          => 'Unpaid',
                ]
            );
        }
    }
}
