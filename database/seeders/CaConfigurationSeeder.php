<?php

namespace Database\Seeders;

use App\Models\CaConfiguration;
use Illuminate\Database\Seeder;

class CaConfigurationSeeder extends Seeder
{
    /**
     * Seeds the default CA (Continuous Assessment) breakdown.
     *
     * Default: 30-mark CA split as follows:
     *   Test 1      = 10 marks
     *   Test 2      = 10 marks
     *   Assignment  =  5 marks
     *   Practical   =  5 marks
     *   TOTAL       = 30 marks  ← must equal school_profile.ca_weight
     *
     * Admin can adjust max scores and toggle components via:
     *   Admin → Settings → CA Configuration
     *
     * IMPORTANT: If admin changes max scores, the sum must still
     * equal school_profile.ca_weight (validated in the controller).
     */
    public function run(): void
    {
        $components = [
            [
                'component_name' => 'Test 1',
                'max_score'      => 10.00,
                'order'          => 1,
                'is_active'      => true,
                // First mid-term test — written exam in class
            ],
            [
                'component_name' => 'Test 2',
                'max_score'      => 10.00,
                'order'          => 2,
                'is_active'      => true,
                // Second test before terminal exam
            ],
            [
                'component_name' => 'Assignment',
                'max_score'      => 5.00,
                'order'          => 3,
                'is_active'      => true,
                // Class work, homework, or project submission
            ],
            [
                'component_name' => 'Practical',
                'max_score'      => 5.00,
                'order'          => 4,
                'is_active'      => true,
                // Lab work, oral test, or practical demonstration
                // For non-science subjects this can be repurposed as "Class Work"
            ],
        ];

        foreach ($components as $component) {
            CaConfiguration::updateOrCreate(
                ['component_name' => $component['component_name']],
                $component
            );
        }

        $total = collect($components)->sum('max_score');
        $this->command->info("  ✓ CA configuration seeded: {$total} total CA marks (Test1=10, Test2=10, Assignment=5, Practical=5)");
    }
}
