<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dimas = User::where('email', 'dimas@student.ac.id')->first();
        $siti = User::where('email', 'siti.rahma@student.ac.id')->first();
        $farhan = User::where('email', 'farhan@student.ac.id')->first();
        $anisa = User::where('email', 'anisa@student.ac.id')->first();
        $budi = User::where('email', 'budi.santoso@student.ac.id')->first();

        $schedule1 = Schedule::find(1);
        $schedule2 = Schedule::find(2);

        if ($dimas && $schedule1) {
            Booking::updateOrCreate(
                ['booking_code' => '#ISCOM-2024-884'],
                [
                    'user_id' => $dimas->id,
                    'schedule_id' => $schedule1->id,
                    'full_name' => $dimas->name,
                    'nim' => $dimas->nim,
                    'major' => $dimas->major,
                    'semester' => $dimas->semester,
                    'whatsapp' => $dimas->whatsapp,
                    'email' => $dimas->email,
                    'status' => 'accepted',
                ]
            );
        }

        if ($siti && $schedule1) {
            Booking::updateOrCreate(
                ['booking_code' => '#ISCOM-2024-885'],
                [
                    'user_id' => $siti->id,
                    'schedule_id' => $schedule1->id,
                    'full_name' => $siti->name,
                    'nim' => $siti->nim,
                    'major' => $siti->major,
                    'semester' => $siti->semester,
                    'whatsapp' => $siti->whatsapp,
                    'email' => $siti->email,
                    'status' => 'accepted',
                ]
            );
        }

        if ($farhan && $schedule2) {
            Booking::updateOrCreate(
                ['booking_code' => '#ISCOM-2024-886'],
                [
                    'user_id' => $farhan->id,
                    'schedule_id' => $schedule2->id,
                    'full_name' => $farhan->name,
                    'nim' => $farhan->nim,
                    'major' => $farhan->major,
                    'semester' => $farhan->semester,
                    'whatsapp' => $farhan->whatsapp,
                    'email' => $farhan->email,
                    'status' => 'accepted',
                ]
            );
        }

        if ($anisa && $schedule2) {
            Booking::updateOrCreate(
                ['booking_code' => '#ISCOM-2024-887'],
                [
                    'user_id' => $anisa->id,
                    'schedule_id' => $schedule2->id,
                    'full_name' => $anisa->name,
                    'nim' => $anisa->nim,
                    'major' => $anisa->major,
                    'semester' => $anisa->semester,
                    'whatsapp' => $anisa->whatsapp,
                    'email' => $anisa->email,
                    'status' => 'accepted',
                ]
            );
        }

        if ($budi && $schedule2) {
            Booking::updateOrCreate(
                ['booking_code' => '#ISCOM-2024-888'],
                [
                    'user_id' => $budi->id,
                    'schedule_id' => $schedule2->id,
                    'full_name' => $budi->name,
                    'nim' => $budi->nim,
                    'major' => $budi->major,
                    'semester' => $budi->semester,
                    'whatsapp' => $budi->whatsapp,
                    'email' => $budi->email,
                    'status' => 'accepted',
                ]
            );
        }

        // Recalculate status for all schedules
        foreach (Schedule::all() as $s) {
            $s->recalculateStatus();
        }
    }
}
