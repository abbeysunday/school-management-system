<?php

namespace Database\Seeders;

use App\Models\GradingScale;
use Illuminate\Database\Seeder;

class GradingScaleSeeder extends Seeder
{
    /**
     * Nigerian A1–F9 grading system.
     *
     * These are the WAEC/NECO standard boundaries used across all
     * Nigerian public and private secondary schools.
     *
     * Admin can adjust score boundaries via:
     *   Admin → Settings → Grading Scale
     *
     * grade_order: 1 = best (A1), 9 = worst (F9)
     * is_pass:     false only for F9
     */
    public function run(): void
    {
        $grades = [
            [
                'grade'       => 'A1',
                'min_score'   => 75.00,
                'max_score'   => 100.00,
                'remark'      => 'Excellent',
                'is_pass'     => true,
                'grade_order' => 1,
            ],
            [
                'grade'       => 'B2',
                'min_score'   => 70.00,
                'max_score'   => 74.99,
                'remark'      => 'Very Good',
                'is_pass'     => true,
                'grade_order' => 2,
            ],
            [
                'grade'       => 'B3',
                'min_score'   => 65.00,
                'max_score'   => 69.99,
                'remark'      => 'Good',
                'is_pass'     => true,
                'grade_order' => 3,
            ],
            [
                'grade'       => 'C4',
                'min_score'   => 60.00,
                'max_score'   => 64.99,
                'remark'      => 'Credit',
                'is_pass'     => true,
                'grade_order' => 4,
            ],
            [
                'grade'       => 'C5',
                'min_score'   => 55.00,
                'max_score'   => 59.99,
                'remark'      => 'Credit',
                'is_pass'     => true,
                'grade_order' => 5,
            ],
            [
                'grade'       => 'C6',
                'min_score'   => 50.00,
                'max_score'   => 54.99,
                'remark'      => 'Credit',
                'is_pass'     => true,
                'grade_order' => 6,
            ],
            [
                'grade'       => 'D7',
                'min_score'   => 45.00,
                'max_score'   => 49.99,
                'remark'      => 'Pass',
                'is_pass'     => true,
                'grade_order' => 7,
            ],
            [
                'grade'       => 'E8',
                'min_score'   => 40.00,
                'max_score'   => 44.99,
                'remark'      => 'Pass',
                'is_pass'     => true,
                'grade_order' => 8,
            ],
            [
                'grade'       => 'F9',
                'min_score'   => 0.00,
                'max_score'   => 39.99,
                'remark'      => 'Fail',
                'is_pass'     => false,
                'grade_order' => 9,
            ],
        ];

        foreach ($grades as $grade) {
            GradingScale::updateOrCreate(
                ['grade' => $grade['grade']],
                $grade
            );
        }

        $this->command->info('  ✓ Grading scale seeded: A1, B2, B3, C4, C5, C6, D7, E8, F9');
    }
}
