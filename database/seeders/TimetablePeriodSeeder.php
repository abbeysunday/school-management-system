<?php

namespace Database\Seeders;

use App\Models\TimetablePeriod;
use Illuminate\Database\Seeder;

class TimetablePeriodSeeder extends Seeder
{
    /**
     * Seeds a typical Nigerian secondary school daily timetable structure.
     *
     * Total: Assembly + 8 teaching periods + Short Break + Long Break + Games
     *
     * Admin can edit times and add/remove periods via:
     *   Admin → Settings → Timetable Periods
     *
     * period_type:
     *   Teaching  = assignable to a subject + teacher
     *   Break     = free period (not assignable)
     *   Assembly  = morning assembly (not assignable)
     *   Games     = sports/games period (not assignable)
     *   Closing   = end of day marker
     */
    public function run(): void
    {
        $periods = [
            [
                'period_name'  => 'Assembly',
                'start_time'   => '07:30:00',
                'end_time'     => '07:45:00',
                'period_order' => 1,
                'period_type'  => 'Assembly',
            ],
            [
                'period_name'  => 'Period 1',
                'start_time'   => '07:45:00',
                'end_time'     => '08:30:00',
                'period_order' => 2,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Period 2',
                'start_time'   => '08:30:00',
                'end_time'     => '09:15:00',
                'period_order' => 3,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Period 3',
                'start_time'   => '09:15:00',
                'end_time'     => '10:00:00',
                'period_order' => 4,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Short Break',
                'start_time'   => '10:00:00',
                'end_time'     => '10:15:00',
                'period_order' => 5,
                'period_type'  => 'Break',
            ],
            [
                'period_name'  => 'Period 4',
                'start_time'   => '10:15:00',
                'end_time'     => '11:00:00',
                'period_order' => 6,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Period 5',
                'start_time'   => '11:00:00',
                'end_time'     => '11:45:00',
                'period_order' => 7,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Long Break',
                'start_time'   => '11:45:00',
                'end_time'     => '12:15:00',
                'period_order' => 8,
                'period_type'  => 'Break',
            ],
            [
                'period_name'  => 'Period 6',
                'start_time'   => '12:15:00',
                'end_time'     => '13:00:00',
                'period_order' => 9,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Period 7',
                'start_time'   => '13:00:00',
                'end_time'     => '13:45:00',
                'period_order' => 10,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Period 8',
                'start_time'   => '13:45:00',
                'end_time'     => '14:30:00',
                'period_order' => 11,
                'period_type'  => 'Teaching',
            ],
            [
                'period_name'  => 'Games / Sports',
                'start_time'   => '14:30:00',
                'end_time'     => '15:15:00',
                'period_order' => 12,
                'period_type'  => 'Games',
            ],
            [
                'period_name'  => 'Closing',
                'start_time'   => '15:15:00',
                'end_time'     => '15:30:00',
                'period_order' => 13,
                'period_type'  => 'Closing',
            ],
        ];

        foreach ($periods as $period) {
            TimetablePeriod::updateOrCreate(
                ['period_name' => $period['period_name']],
                $period
            );
        }

        $this->command->info('  ✓ Timetable periods seeded: Assembly, 8 Teaching periods, Short Break, Long Break, Games, Closing');
    }
}
