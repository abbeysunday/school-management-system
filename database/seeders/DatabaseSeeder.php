<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run for a fresh production install:
     *   php artisan db:seed --class=DatabaseSeeder
     *
     * Run demo data (for testing / showcasing):
     *   php artisan db:seed --class=DemoSeeder
     */
    public function run(): void
    {
        $this->call([
            // ── STEP 1: School profile (must be first — ID = 1) ──────────
            SchoolProfileSeeder::class,

            // ── STEP 2: Lookup / reference tables ────────────────────────
            ClassLevelSeeder::class,        // JSS1 → SS3
            SubjectSeeder::class,           // ~30 Nigerian subjects
            GradingScaleSeeder::class,      // A1 → F9
            CaConfigurationSeeder::class,   // Test 1, Test 2, Assignment, Practical
            TimetablePeriodSeeder::class,   // Assembly, Period 1-8, Break, Games
            FeeCategorySeeder::class,       // Tuition, PTA Levy, Exam Fee, etc.
            PromotionCriteriaSeeder::class, // Criteria per class level
            SettingSeeder::class,           // App-wide key-value settings
        ]);

        $this->command->info('✅ Production seed complete. Visit /setup to configure your school.');
    }
}
