<?php

namespace Database\Seeders;

use App\Models\MentoringSession;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Mentoring Sessions
        $session1 = MentoringSession::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Sesi Basis Data & Web',
                'slug' => 'sesi-basis-data-dan-web',
                'description' => 'Sesi mentoring praktikum penguasaan relasi basis data SQL, perancangan ERD, dan integrasi backend web.',
                'badge_label' => 'Sesi Terbuka',
                'is_active' => true,
            ]
        );

        $session2 = MentoringSession::updateOrCreate(
            ['id' => 2],
            [
                'title' => 'Sesi UI/UX & Prototyping',
                'slug' => 'sesi-ui-ux-dan-prototyping',
                'description' => 'Eksplorasi pembuatan user persona, wireframing, dan desain prototype interaktif menggunakan Figma.',
                'badge_label' => 'Sesi Terbuka',
                'is_active' => true,
            ]
        );

        $session3 = MentoringSession::updateOrCreate(
            ['id' => 3],
            [
                'title' => 'Sesi Algoritma Pemrograman',
                'slug' => 'sesi-algoritma-pemrograman',
                'description' => 'Peningkatan pemahaman logic programming, algoritma pencarian, dan problem solving praktikum.',
                'badge_label' => 'Sesi Terbuka',
                'is_active' => true,
            ]
        );

        // 2. Seed Multiple Schedules per Session
        $schedules = [
            // Sesi 1 Schedules
            [
                'id' => 1,
                'mentoring_session_id' => $session1->id,
                'day_name' => 'Kamis',
                'schedule_date' => '2024-10-24',
                'time_slot' => '13:30 - 15:30 WIB',
                'start_time' => '13:30:00',
                'end_time' => '15:30:00',
                'mentor_names' => 'Kak Aditya W. & Kak Fadhil R.',
                'location' => 'Lab Komputer C301',
                'topic' => 'Basis Data & Web (Sesi A)',
                'quota' => 10,
                'status' => 'tersedia',
            ],
            [
                'id' => 4,
                'mentoring_session_id' => $session1->id,
                'day_name' => 'Sabtu',
                'schedule_date' => '2024-10-26',
                'time_slot' => '09:00 - 11:30 WIB',
                'start_time' => '09:00:00',
                'end_time' => '11:30:00',
                'mentor_names' => 'Kak Aditya W.',
                'location' => 'Lab Komputer C301',
                'topic' => 'Basis Data & Web (Sesi B)',
                'quota' => 8,
                'status' => 'tersedia',
            ],

            // Sesi 2 Schedules
            [
                'id' => 2,
                'mentoring_session_id' => $session2->id,
                'day_name' => 'Jumat',
                'schedule_date' => '2024-10-25',
                'time_slot' => '09:00 - 11:30 WIB',
                'start_time' => '09:00:00',
                'end_time' => '11:30:00',
                'mentor_names' => 'Kak Nabila Putri',
                'location' => 'Lab Komputer C304',
                'topic' => 'UI/UX & Prototyping (Kelas Pagi)',
                'quota' => 10,
                'status' => 'tersedia',
            ],
            [
                'id' => 5,
                'mentoring_session_id' => $session2->id,
                'day_name' => 'Sabtu',
                'schedule_date' => '2024-10-26',
                'time_slot' => '09:00 - 11:30 WIB',
                'start_time' => '09:00:00',
                'end_time' => '11:30:00',
                'mentor_names' => 'Kak Nabila Putri',
                'location' => 'Lab Komputer C304',
                'topic' => 'UI/UX & Prototyping (Kelas Weekend)',
                'quota' => 8,
                'status' => 'tersedia',
            ],

            // Sesi 3 Schedules
            [
                'id' => 3,
                'mentoring_session_id' => $session3->id,
                'day_name' => 'Sabtu',
                'schedule_date' => '2024-10-26',
                'time_slot' => '13:30 - 15:30 WIB',
                'start_time' => '13:30:00',
                'end_time' => '15:30:00',
                'mentor_names' => 'Kak Kevin Sanjaya',
                'location' => 'Lab Multimedia C202',
                'topic' => 'Algoritma Pemrograman (Sesi Siang)',
                'quota' => 6,
                'status' => 'tersedia',
            ],
            [
                'id' => 6,
                'mentoring_session_id' => $session3->id,
                'day_name' => 'Minggu',
                'schedule_date' => '2024-10-27',
                'time_slot' => '10:00 - 12:00 WIB',
                'start_time' => '10:00:00',
                'end_time' => '12:00:00',
                'mentor_names' => 'Kak Kevin Sanjaya',
                'location' => 'Lab Multimedia C202',
                'topic' => 'Algoritma Pemrograman (Sesi Minggu)',
                'quota' => 10,
                'status' => 'tersedia',
            ],
        ];

        foreach ($schedules as $sched) {
            $schedule = Schedule::updateOrCreate(['id' => $sched['id']], $sched);
            $schedule->recalculateStatus();
        }
    }
}
