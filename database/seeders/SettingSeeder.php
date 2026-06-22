<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Seeds all application-wide key-value settings.
     *
     * These are miscellaneous settings that don't belong on school_profile.
     * Admin edits them via: Admin → Settings → General Settings
     *
     * Groups:
     *   general    = app behaviour and branding
     *   results    = result card and scoring rules
     *   attendance = attendance rules
     *   cbt        = CBT exam defaults
     *   sms        = SMS templates
     *   fees       = fee behaviour
     *   promotion  = promotion engine behaviour
     */
    public function run(): void
    {
        $settings = [

            // ── GENERAL ───────────────────────────────────────────────────
            [
                'key'   => 'app_version',
                'value' => '1.0.0',
                'group' => 'general',
            ],
            [
                'key'   => 'admission_number_prefix',
                'value' => 'ADM',
                // Format: ADM/2024/001, ADM/2024/002 etc.
                'group' => 'general',
            ],
            [
                'key'   => 'admission_number_include_year',
                'value' => '1',
                // 1 = include year in admission number, 0 = sequential only
                'group' => 'general',
            ],
            [
                'key'   => 'staff_id_prefix',
                'value' => 'STF',
                // Format: STF/001, STF/002 etc.
                'group' => 'general',
            ],
            [
                'key'   => 'receipt_number_prefix',
                'value' => 'RCP',
                // Format: RCP/2024/0001
                'group' => 'general',
            ],
            [
                'key'   => 'date_format',
                'value' => 'd M Y',
                // e.g. 15 Jan 2025
                'group' => 'general',
            ],

            // ── RESULTS ───────────────────────────────────────────────────
            [
                'key'   => 'result_promotion_basis',
                'value' => 'third_term',
                // Options: 'third_term' | 'annual_average'
                // third_term = use only Third Term results for promotion decision
                // annual_average = average of all three terms' percentages
                'group' => 'results',
            ],
            [
                'key'   => 'show_class_average_on_report_card',
                'value' => '1',
                'group' => 'results',
            ],
            [
                'key'   => 'show_highest_score_on_report_card',
                'value' => '1',
                'group' => 'results',
            ],
            [
                'key'   => 'show_lowest_score_on_report_card',
                'value' => '0',
                'group' => 'results',
            ],
            [
                'key'   => 'show_subject_position_on_report_card',
                'value' => '1',
                'group' => 'results',
            ],
            [
                'key'   => 'principal_remark_auto_generate',
                'value' => '1',
                // 1 = auto-generate based on percentage thresholds below
                // 0 = admin types individual remarks manually
                'group' => 'results',
            ],
            [
                'key'   => 'principal_remark_90_plus',
                'value' => 'An outstanding performance. Continue to strive for excellence.',
                'group' => 'results',
            ],
            [
                'key'   => 'principal_remark_75_plus',
                'value' => 'Excellent performance. Keep up the good work.',
                'group' => 'results',
            ],
            [
                'key'   => 'principal_remark_60_plus',
                'value' => 'Good performance. There is room for improvement.',
                'group' => 'results',
            ],
            [
                'key'   => 'principal_remark_50_plus',
                'value' => 'Fair performance. More effort is required.',
                'group' => 'results',
            ],
            [
                'key'   => 'principal_remark_below_50',
                'value' => 'Below average. Please work harder and seek help where needed.',
                'group' => 'results',
            ],

            // ── PSYCHOMOTOR / AFFECTIVE DOMAIN TRAITS ────────────────────
            // These are the traits rated by Form Teachers on the report card.
            // Stored as a JSON array so admin can customise the trait list.
            [
                'key'   => 'affective_traits',
                'value' => json_encode([
                    'Punctuality',
                    'Neatness & Appearance',
                    'Politeness',
                    'Honesty',
                    'Self-Control',
                    'Perseverance',
                    'Co-operation',
                    'Leadership',
                ]),
                'group' => 'results',
            ],
            [
                'key'   => 'psychomotor_traits',
                'value' => json_encode([
                    'Handwriting',
                    'Drawing & Painting',
                    'Sports & Games',
                    'Crafts',
                    'Musical Skills',
                    'Computer Skills',
                ]),
                'group' => 'results',
            ],
            [
                'key'   => 'psychomotor_rating_labels',
                'value' => json_encode([
                    '5' => 'Excellent',
                    '4' => 'Very Good',
                    '3' => 'Good',
                    '2' => 'Fair',
                    '1' => 'Poor',
                ]),
                'group' => 'results',
            ],

            // ── ATTENDANCE ────────────────────────────────────────────────
            [
                'key'   => 'late_arrival_grace_minutes',
                'value' => '15',
                // Students arriving within 15 minutes of assembly are Late, not Absent
                'group' => 'attendance',
            ],
            [
                'key'   => 'minimum_attendance_percentage',
                'value' => '75',
                // Students below this % get a flag on their report card
                // Does NOT block promotion — that's a separate promotion criteria setting
                'group' => 'attendance',
            ],

            // ── CBT ───────────────────────────────────────────────────────
            [
                'key'   => 'cbt_auto_save_interval_seconds',
                'value' => '30',
                // How often student's answers auto-save during an active exam
                'group' => 'cbt',
            ],
            [
                'key'   => 'cbt_default_duration_minutes',
                'value' => '60',
                // Default duration when admin creates a new CBT exam
                'group' => 'cbt',
            ],
            [
                'key'   => 'cbt_show_answers_after_submission',
                'value' => '1',
                // 1 = student sees correct answers after submitting
                // 0 = only score shown, answers hidden
                'group' => 'cbt',
            ],

            // ── SMS TEMPLATES ────────────────────────────────────────────
            // {student_name}, {class}, {date}, {balance}, {term}, {school_name}
            // are replaced dynamically when SMS is sent.
            [
                'key'   => 'sms_template_absence',
                'value' => 'Dear Parent, {student_name} of {class} was marked ABSENT on {date}. Please contact {school_name} if this is unexpected.',
                'group' => 'sms',
            ],
            [
                'key'   => 'sms_template_payment_confirmed',
                'value' => 'Payment of {amount} received for {student_name} ({class}) - {term}. Balance: {balance}. Thank you. - {school_name}',
                'group' => 'sms',
            ],
            [
                'key'   => 'sms_template_fee_reminder',
                'value' => 'Dear Parent, {student_name} ({class}) has an outstanding balance of {balance} for {term}. Kindly pay at your earliest convenience. - {school_name}',
                'group' => 'sms',
            ],
            [
                'key'   => 'sms_template_result_published',
                'value' => 'Dear Parent, {term} results for {student_name} ({class}) are now available. Log in to your parent portal to view and download the report card. - {school_name}',
                'group' => 'sms',
            ],

            // ── FEES ──────────────────────────────────────────────────────
            [
                'key'   => 'paystack_payment_description',
                'value' => 'School Fees Payment',
                // Text shown on the Paystack checkout page
                'group' => 'fees',
            ],
            [
                'key'   => 'allow_partial_payment',
                'value' => '1',
                // 1 = parents can pay any amount (partial)
                // 0 = must pay the full outstanding balance at once
                'group' => 'fees',
            ],
            [
                'key'   => 'fee_reminder_auto_schedule',
                'value' => '0',
                // 1 = automatically send fee reminders on a schedule
                // 0 = bursar sends reminders manually
                'group' => 'fees',
            ],
            [
                'key'   => 'fee_reminder_day_of_week',
                'value' => 'Monday',
                // If auto_schedule = 1, send reminders every Monday
                'group' => 'fees',
            ],

            // ── PROMOTION ─────────────────────────────────────────────────
            [
                'key'   => 'promotion_requires_full_fee_payment',
                'value' => '0',
                // 1 = student must have paid all fees to be promoted
                // 0 = promotion is based purely on academic performance
                'group' => 'promotion',
            ],
            [
                'key'   => 'max_repeat_years',
                'value' => '2',
                // A student can only be kept in the same class for 2 sessions max
                // After that, admin must manually decide their fate
                'group' => 'promotion',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                ]
            );
        }

        $count = count($settings);
        $this->command->info("  ✓ Settings seeded: {$count} key-value configuration entries");
    }
}
