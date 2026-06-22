<?php

namespace Database\Seeders;

use App\Models\ClassLevel;
use App\Models\PromotionCriteria;
use Illuminate\Database\Seeder;

class PromotionCriteriaSeeder extends Seeder
{
    /**
     * Seeds default promotion criteria for each class level.
     *
     * The promotion engine uses these to classify students at end of session:
     *   Promoted  → meets all criteria, moves to next class level
     *   Repeated  → fails criteria, stays in same level for another session
     *   Graduated → SS3 students (no promotion, they leave school)
     *
     * Criteria applied:
     *   min_percentage     = student's annual percentage must be >= this
     *   min_subjects_passed = student must pass at least this many subjects
     *   min_ca             = student's CA average must be >= this (optional floor)
     *
     * Admin can adjust per level via: Admin → Settings → Promotion Criteria
     *
     * NOTE: SS3 criteria don't matter in practice — all SS3 students
     *       are marked as Graduated regardless of score (they sit WAEC/NECO).
     */
    public function run(): void
    {
        $criteria = [
            'JSS1' => [
                'min_percentage'      => 40.00,
                'min_subjects_passed' => 5,
                'min_ca'              => 0.00,
            ],
            'JSS2' => [
                'min_percentage'      => 40.00,
                'min_subjects_passed' => 5,
                'min_ca'              => 0.00,
            ],
            'JSS3' => [
                // JSS3 → SS1 is a significant transition.
                // Schools sometimes use stricter criteria here.
                'min_percentage'      => 45.00,
                'min_subjects_passed' => 6,
                'min_ca'              => 0.00,
            ],
            'SS1' => [
                'min_percentage'      => 40.00,
                'min_subjects_passed' => 5,
                'min_ca'              => 0.00,
            ],
            'SS2' => [
                // SS2 → SS3 transition: student must be ready for WAEC
                'min_percentage'      => 45.00,
                'min_subjects_passed' => 6,
                'min_ca'              => 0.00,
            ],
            'SS3' => [
                // SS3 students are Graduated, not Promoted.
                // These values are effectively unused by the engine.
                'min_percentage'      => 0.00,
                'min_subjects_passed' => 0,
                'min_ca'              => 0.00,
            ],
        ];

        $levels = ClassLevel::all()->keyBy('name');

        foreach ($criteria as $levelName => $criterion) {
            $level = $levels->get($levelName);

            if (! $level) {
                $this->command->warn("  ⚠ Class level '{$levelName}' not found. Run ClassLevelSeeder first.");
                continue;
            }

            PromotionCriteria::updateOrCreate(
                ['class_level_id' => $level->id],
                $criterion
            );
        }

        $this->command->info('  ✓ Promotion criteria seeded for JSS1, JSS2, JSS3, SS1, SS2, SS3');
    }
}
