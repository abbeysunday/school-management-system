<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Seeds all common Nigerian secondary school subjects.
     *
     * Covers: Junior (JSS1–3) and Senior (SS1–3) subjects.
     * Admin can add more subjects via: Admin → Subjects → Add Subject
     *
     * is_core    = compulsory for ALL students (English, Maths, Civic Education)
     * is_waec    = examinable by WAEC
     * is_neco    = examinable by NECO
     * category   = General | Science | Arts | Commercial | Technical | Vocational
     */
    public function run(): void
    {
        $subjects = [

            // ─────────────────────────────────────────────────────────────
            // GENERAL / CORE — All classes, all arms
            // ─────────────────────────────────────────────────────────────
            [
                'name'            => 'English Language',
                'code'            => 'ENG',
                'category'        => 'General',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => true,
                'is_active'       => true,
            ],
            [
                'name'            => 'Mathematics',
                'code'            => 'MTH',
                'category'        => 'General',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => true,
                'is_active'       => true,
            ],
            [
                'name'            => 'Civic Education',
                'code'            => 'CVE',
                'category'        => 'General',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => true,
                'is_active'       => true,
            ],
            [
                'name'            => 'Christian Religious Studies',
                'code'            => 'CRS',
                'category'        => 'General',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Islamic Religious Studies',
                'code'            => 'IRS',
                'category'        => 'General',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Physical & Health Education',
                'code'            => 'PHE',
                'category'        => 'General',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Social Studies',
                'code'            => 'SST',
                'category'        => 'General',
                'is_waec_subject' => false,
                'is_neco_subject' => false,
                'is_core'         => false,
                'is_active'       => true,
                // Primarily JSS1–3
            ],
            [
                'name'            => 'Literature in English',
                'code'            => 'LIT',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],

            // ─────────────────────────────────────────────────────────────
            // SCIENCE
            // ─────────────────────────────────────────────────────────────
            [
                'name'            => 'Biology',
                'code'            => 'BIO',
                'category'        => 'Science',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Chemistry',
                'code'            => 'CHE',
                'category'        => 'Science',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Physics',
                'code'            => 'PHY',
                'category'        => 'Science',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Further Mathematics',
                'code'            => 'FMT',
                'category'        => 'Science',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Agricultural Science',
                'code'            => 'AGR',
                'category'        => 'Science',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Basic Science',
                'code'            => 'BSC',
                'category'        => 'Science',
                'is_waec_subject' => false,
                'is_neco_subject' => false,
                'is_core'         => false,
                'is_active'       => true,
                // Primarily JSS1–3
            ],

            // ─────────────────────────────────────────────────────────────
            // ARTS / HUMANITIES
            // ─────────────────────────────────────────────────────────────
            [
                'name'            => 'Government',
                'code'            => 'GOV',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'History',
                'code'            => 'HIS',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Fine Arts',
                'code'            => 'FNA',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Music',
                'code'            => 'MUS',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'French Language',
                'code'            => 'FRN',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Yoruba Language',
                'code'            => 'YOR',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Igbo Language',
                'code'            => 'IGB',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Hausa Language',
                'code'            => 'HAU',
                'category'        => 'Arts',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],

            // ─────────────────────────────────────────────────────────────
            // COMMERCIAL
            // ─────────────────────────────────────────────────────────────
            [
                'name'            => 'Economics',
                'code'            => 'ECO',
                'category'        => 'Commercial',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Commerce',
                'code'            => 'COM',
                'category'        => 'Commercial',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Financial Accounting',
                'code'            => 'ACC',
                'category'        => 'Commercial',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Office Practice',
                'code'            => 'OFP',
                'category'        => 'Commercial',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Marketing',
                'code'            => 'MKT',
                'category'        => 'Commercial',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],

            // ─────────────────────────────────────────────────────────────
            // TECHNICAL / VOCATIONAL
            // ─────────────────────────────────────────────────────────────
            [
                'name'            => 'Computer Studies',
                'code'            => 'CST',
                'category'        => 'Technical',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Technical Drawing',
                'code'            => 'TDR',
                'category'        => 'Technical',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Basic Technology',
                'code'            => 'BTY',
                'category'        => 'Technical',
                'is_waec_subject' => false,
                'is_neco_subject' => false,
                'is_core'         => false,
                'is_active'       => true,
                // Primarily JSS1–3
            ],
            [
                'name'            => 'Food & Nutrition',
                'code'            => 'FDN',
                'category'        => 'Vocational',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Home Economics',
                'code'            => 'HEC',
                'category'        => 'Vocational',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
            [
                'name'            => 'Health Science',
                'code'            => 'HLS',
                'category'        => 'Science',
                'is_waec_subject' => true,
                'is_neco_subject' => true,
                'is_core'         => false,
                'is_active'       => true,
            ],
        ];

        $count = 0;
        foreach ($subjects as $subject) {
            Subject::updateOrCreate(
                ['name' => $subject['name']],
                $subject
            );
            $count++;
        }

        $this->command->info("  ✓ Subjects seeded: {$count} Nigerian secondary school subjects");
    }
}
