<?php

namespace Database\Seeders;

use App\Models\ClassLevel;
use Illuminate\Database\Seeder;

class ClassLevelSeeder extends Seeder
{
    /**
     * Seeds the 6 Nigerian secondary school class levels.
     *
     * level_order is critical — it drives the promotion engine:
     *   1 → 2 → 3 → 4 → 5 → 6 (SS3 graduates, no further promotion)
     *
     * Admin adds class ARMS (JSS1A, JSS1B etc.) separately under each level
     * via: Admin → Classes → Class Arms → Add Arm
     */
    public function run(): void
    {
        $levels = [
            [
                'name'        => 'JSS1',
                'level_order' => 1,
                'category'    => 'Junior',
            ],
            [
                'name'        => 'JSS2',
                'level_order' => 2,
                'category'    => 'Junior',
            ],
            [
                'name'        => 'JSS3',
                'level_order' => 3,
                'category'    => 'Junior',
                // Note: JSS3 students sit BECE (Basic Education Certificate Exam)
            ],
            [
                'name'        => 'SS1',
                'level_order' => 4,
                'category'    => 'Senior',
            ],
            [
                'name'        => 'SS2',
                'level_order' => 5,
                'category'    => 'Senior',
            ],
            [
                'name'        => 'SS3',
                'level_order' => 6,
                'category'    => 'Senior',
                // Note: SS3 students sit WAEC/NECO — they graduate, not promoted
            ],
        ];

        foreach ($levels as $level) {
            ClassLevel::updateOrCreate(
                ['name' => $level['name']],
                $level
            );
        }

        $this->command->info('  ✓ Class levels seeded: JSS1, JSS2, JSS3, SS1, SS2, SS3');
    }
}
