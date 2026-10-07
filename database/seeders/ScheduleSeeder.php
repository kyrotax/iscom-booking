<?php

namespace Database\Seeders;

use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'id' => 1,
                'day_name' => 'Kamis',
                'schedule_date' => '2024-10-24',
                'time_slot' => '13:30 - 15:30 WIB',
                'mentor_names' => 'Kak Aditya W. & Kak Fadhil R.',
                'location' => 'Lab Komputer C301',
                'topic' => 'Basis Data & Web',
                'quota' => 10,
                'status' => 'tersedia',
            ],
            [
                'id' => 2,
                'day_name' => 'Jumat',
                'schedule_date' => '2024-10-25',
                'time_slot' => '09:00 - 11:30 WIB',
                'mentor_names' => 'Kak Nabila Putri',
                'location' => 'Lab Komputer C304',
                'topic' => 'UI/UX & Prototyping',
                'quota' => 10,
                'status' => 'tersedia',
            ],
            [
                'id' => 3,
                'day_name' => 'Sabtu',
                'schedule_date' => '2024-10-26',
                'time_slot' => '10:00 - 12:00 WIB',
                'mentor_names' => 'Kak Kevin Sanjaya',
                'location' => 'Lab Multimedia C202',
                'topic' => 'Algoritma Pemrograman',
                'quota' => 5,
                'status' => 'hampir_penuh',
            ],
        ];

        foreach ($schedules as $sched) {
            Schedule::updateOrCreate(['id' => $sched['id']], $sched);
        }
    }
}
