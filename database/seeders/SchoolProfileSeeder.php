<?php

namespace Database\Seeders;

use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;

class SchoolProfileSeeder extends Seeder
{
    /**
     * Seeds exactly ONE row (ID = 1).
     * Uses updateOrCreate so re-running the seeder never duplicates.
     *
     * The school admin fills in real values via:
     *   Admin → School Setup → School Profile
     */
    public function run(): void
    {
        SchoolProfile::updateOrCreate(
            ['id' => 1],
            [
                // ── Identity ────────────────────────────────────────────
                'name'                   => 'Your School Name',
                'short_name'             => 'YSN',
                'address'                => '123 School Road, Lagos, Nigeria',
                'logo'                   => null,          // uploaded by admin
                'stamp'                  => null,          // uploaded by admin
                'motto'                  => 'Excellence in Education',
                'phone'                  => null,
                'email'                  => null,
                'website'                => null,
                'principal_name'         => null,
                'waec_centre_number'     => null,
                'neco_centre_number'     => null,
                'rc_number'              => null,
                'state'                  => 'Lagos',
                'lga'                    => null,
                'city'                   => null,

                // ── Academic weights ─────────────────────────────────────
                // These drive score validation & result calculation.
                // Must always sum to 100.
                'ca_weight'              => 30,    // Total CA marks (default: 30)
                'exam_weight'            => 70,    // Terminal exam marks (default: 70)

                // ── Localisation ─────────────────────────────────────────
                'currency_symbol'        => '₦',
                'timezone'               => 'Africa/Lagos',

                // ── Integrations (filled in during setup wizard) ─────────
                'paystack_public_key'    => null,
                'paystack_secret_key'    => null,
                'termii_api_key'         => null,
                'termii_sender_id'       => null,

                // ── Mail settings ────────────────────────────────────────
                'mail_from_address'      => null,
                'mail_from_name'         => null,

                // ── SMS toggle switches ───────────────────────────────────
                'sms_on_absence'         => true,
                'sms_on_payment'         => true,
                'sms_on_result_publish'  => true,
            ]
        );

        $this->command->info('  ✓ SchoolProfile row created (ID = 1)');
    }
}
