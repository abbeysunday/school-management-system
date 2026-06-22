<?php

namespace Database\Seeders;

use App\Models\FeeCategory;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    /**
     * Seeds the standard Nigerian school fee categories.
     *
     * These are categories only — no amounts are set here.
     * Amounts are configured by admin per class level per term via:
     *   Admin → Fees → Fee Structure
     *
     * is_compulsory = true  → automatically added to every student's ledger
     * is_compulsory = false → optional; bursar adds manually per student
     *
     * display_order controls the order they appear on fee receipts
     * and the student fee overview page.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'Tuition Fee',
                'description'   => 'The main school fees covering teaching and academic activities for the term.',
                'is_compulsory' => true,
                'display_order' => 1,
                'is_active'     => true,
            ],
            [
                'name'          => 'PTA Levy',
                'description'   => 'Parent-Teacher Association levy for school improvement projects and activities.',
                'is_compulsory' => true,
                'display_order' => 2,
                'is_active'     => true,
            ],
            [
                'name'          => 'Development Levy',
                'description'   => 'Annual levy for school infrastructure development and capital projects.',
                'is_compulsory' => true,
                'display_order' => 3,
                'is_active'     => true,
            ],
            [
                'name'          => 'Book Levy',
                'description'   => 'Covers textbooks, exercise books, and other learning materials supplied by the school.',
                'is_compulsory' => true,
                'display_order' => 4,
                'is_active'     => true,
            ],
            [
                'name'          => 'Examination Fee',
                'description'   => 'Internal examination fee covering printing of exam papers and invigilation.',
                'is_compulsory' => true,
                'display_order' => 5,
                'is_active'     => true,
            ],
            [
                'name'          => 'Sports Levy',
                'description'   => 'Covers sports equipment, inter-house sports, and physical education materials.',
                'is_compulsory' => true,
                'display_order' => 6,
                'is_active'     => true,
            ],
            [
                'name'          => 'ICT Levy',
                'description'   => 'Computer lab maintenance, internet access, and digital learning tools.',
                'is_compulsory' => false,
                'display_order' => 7,
                'is_active'     => true,
            ],
            [
                'name'          => 'Library Fee',
                'description'   => 'Library maintenance, new book acquisitions, and reading materials.',
                'is_compulsory' => false,
                'display_order' => 8,
                'is_active'     => true,
            ],
            [
                'name'          => 'WAEC/NECO Registration Fee',
                'description'   => 'External examination registration fee for SS3 students sitting WAEC and/or NECO.',
                'is_compulsory' => false,
                'display_order' => 9,
                'is_active'     => true,
                // Only charged to SS3 students in Third Term
            ],
            [
                'name'          => 'Excursion Fee',
                'description'   => 'Educational field trips and excursion activities.',
                'is_compulsory' => false,
                'display_order' => 10,
                'is_active'     => true,
            ],
            [
                'name'          => 'Medical/Health Fee',
                'description'   => 'School clinic, first aid supplies, and basic health screening.',
                'is_compulsory' => false,
                'display_order' => 11,
                'is_active'     => true,
            ],
            [
                'name'          => 'Uniform/Sportswear',
                'description'   => 'School uniform or sportswear supplied by the school.',
                'is_compulsory' => false,
                'display_order' => 12,
                'is_active'     => true,
                // Typically a one-time charge at admission or start of session
            ],
        ];

        foreach ($categories as $category) {
            FeeCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('  ✓ Fee categories seeded: Tuition, PTA, Development, Book, Exam, Sports + 6 optional categories');
    }
}
