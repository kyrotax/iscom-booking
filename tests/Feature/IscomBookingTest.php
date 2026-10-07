<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\MentoringSession;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class IscomBookingTest extends TestCase
{
    use DatabaseTransactions;
    public function test_home_page_loads_with_branding_and_sessions(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ISCOM');
        $response->assertSee('Mentoring');
        $response->assertSee('Belajar bareng');
        $response->assertSee('Sesi yang tersedia');
    }

    public function test_sesi_pages_load_successfully(): void
    {
        // 1. Sesi index
        $response = $this->get('/sesi');
        $response->assertStatus(200);
        $response->assertSee('Sesi Mentoring Mahasiswa');

        // 2. Legacy /jadwal redirect
        $responseJadwal = $this->get('/jadwal');
        $responseJadwal->assertRedirect('/sesi');

        // 3. Sesi detail (via POST and Slug)
        $session = MentoringSession::first();
        if ($session) {
            $postResponse = $this->post('/sesi/pilih', ['session_id' => $session->id]);
            $postResponse->assertRedirect('/sesi/' . ($session->slug ?? $session->id));

            $responseDetail = $this->get('/sesi/' . ($session->slug ?? $session->id));
            $responseDetail->assertStatus(200);
            $responseDetail->assertSee($session->title);
            $responseDetail->assertSee('Pilihan Jadwal di Sesi Ini');
        }
    }

    public function test_status_page_loads_successfully(): void
    {
        $response = $this->get('/status');

        $response->assertStatus(200);
        $response->assertSee('Status Sesi Mentoring');
        $response->assertSee('Silakan Masuk untuk Melihat Status');

        // Test authenticated student with booking
        $student = User::where('role', 'mahasiswa')->first();
        if ($student) {
            $booking = Booking::with('schedule.mentoringSession')->where('user_id', $student->id)->first();
            if ($booking) {
                $authResponse = $this->actingAs($student)->get('/status');
                $authResponse->assertStatus(200);
                $authResponse->assertSee($booking->schedule->mentoringSession->title);
                $authResponse->assertSee($booking->schedule->day_name);
            }
        }
    }

    public function test_login_and_admin_role_authorization(): void
    {
        // 1. Unauthenticated user cannot access admin dashboard
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        // 2. Regular student user cannot access admin dashboard
        $student = User::where('role', 'mahasiswa')->first();
        if ($student) {
            $response = $this->actingAs($student)->get('/admin/dashboard');
            $response->assertRedirect('/login');
        }

        // 3. Admin user can access admin dashboard
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Pengurus ISCOM');
    }

    public function test_student_booking_flow_and_quota(): void
    {
        $uniqueId = rand(1000, 9999);
        $student = User::create([
            'name' => "Tester Mahasiswa {$uniqueId}",
            'email' => "tester{$uniqueId}@student.ac.id",
            'nim' => "2208101{$uniqueId}",
            'major' => 'Sistem Informasi',
            'semester' => 3,
            'whatsapp' => '081234567899',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        $schedule = Schedule::first();
        $this->assertNotNull($schedule);
        $schedule->quota = 50;
        $schedule->status = 'available';
        $schedule->save();

        // Step 1: Submit data diri
        $step1Data = [
            'full_name' => $student->name,
            'nim' => $student->nim,
            'major' => $student->major,
            'semester' => $student->semester,
            'whatsapp' => $student->whatsapp,
            'email' => $student->email,
        ];

        $response = $this->actingAs($student)->post('/booking/data-diri', $step1Data);
        $response->assertRedirect('/booking/pilih-jadwal');

        // Step 2: Choose schedule
        $response = $this->actingAs($student)->post('/booking/pilih-jadwal', [
            'schedule_id' => $schedule->id,
        ]);
        $response->assertRedirect('/booking/konfirmasi');

        // Step 3: Confirm booking
        $response = $this->actingAs($student)->post('/booking/konfirmasi');
        $response->assertRedirect('/booking/sukses');

        // Verify booking in database
        $booking = Booking::where('user_id', $student->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals('pending', $booking->status);
        $this->assertStringStartsWith('#ISCOM-', $booking->booking_code);

        // Step 4: Access success page
        $response = $this->actingAs($student)->get('/booking/sukses');
        $response->assertStatus(200);
        $response->assertSee('Booking berhasil');
        $response->assertSee($booking->booking_code);
    }

    public function test_student_cannot_book_two_schedules_in_the_same_session(): void
    {
        $uniqueId = rand(1000, 9999);
        $student = User::create([
            'name' => "Tester Multi {$uniqueId}",
            'email' => "multi{$uniqueId}@student.ac.id",
            'nim' => "2208201{$uniqueId}",
            'major' => 'Sistem Informasi',
            'semester' => 3,
            'whatsapp' => '081234567811',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        // Sesi 1 has Schedule 1 and Schedule 4
        $sched1 = Schedule::find(1);
        $sched4 = Schedule::find(4);
        $this->assertNotNull($sched1);
        $this->assertNotNull($sched4);
        $this->assertEquals($sched1->mentoring_session_id, $sched4->mentoring_session_id);

        // Student creates first booking on Schedule 1 (Sesi 1)
        Booking::create([
            'user_id' => $student->id,
            'schedule_id' => $sched1->id,
            'full_name' => $student->name,
            'nim' => $student->nim,
            'major' => $student->major,
            'semester' => $student->semester,
            'whatsapp' => $student->whatsapp,
            'email' => $student->email,
            'status' => 'accepted',
        ]);

        // Attempt to book Schedule 4 in the same session
        $this->actingAs($student)->withSession(['booking_step1' => ['name' => $student->name]]);
        $response = $this->actingAs($student)->post('/booking/pilih-jadwal', [
            'schedule_id' => $sched4->id,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_student_cannot_book_conflicting_schedule_on_same_date_and_time(): void
    {
        $uniqueId = rand(1000, 9999);
        $student = User::create([
            'name' => "Tester Conflict {$uniqueId}",
            'email' => "conflict{$uniqueId}@student.ac.id",
            'nim' => "2208301{$uniqueId}",
            'major' => 'Sistem Informasi',
            'semester' => 3,
            'whatsapp' => '081234567822',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        // Schedule 4 (Sesi 1, Sabtu 2024-10-26 09:00 - 11:30)
        // Schedule 5 (Sesi 2, Sabtu 2024-10-26 09:00 - 11:30) -> Collision!
        $sched4 = Schedule::find(4);
        $sched5 = Schedule::find(5);
        $this->assertNotNull($sched4);
        $this->assertNotNull($sched5);
        $this->assertNotEquals($sched4->mentoring_session_id, $sched5->mentoring_session_id);

        // Student books Schedule 4 in Sesi 1
        Booking::create([
            'user_id' => $student->id,
            'schedule_id' => $sched4->id,
            'full_name' => $student->name,
            'nim' => $student->nim,
            'major' => $student->major,
            'semester' => $student->semester,
            'whatsapp' => $student->whatsapp,
            'email' => $student->email,
            'status' => 'accepted',
        ]);

        // Attempt to book Schedule 5 in Sesi 2 (same date & time)
        $this->actingAs($student)->withSession(['booking_step1' => ['name' => $student->name]]);
        $response = $this->actingAs($student)->post('/booking/pilih-jadwal', [
            'schedule_id' => $sched5->id,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_student_can_book_two_sessions_if_times_do_not_conflict(): void
    {
        $uniqueId = rand(1000, 9999);
        $student = User::create([
            'name' => "Tester Multi-Session {$uniqueId}",
            'email' => "multisess{$uniqueId}@student.ac.id",
            'nim' => "2208401{$uniqueId}",
            'major' => 'Sistem Informasi',
            'semester' => 3,
            'whatsapp' => '081234567833',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        // Schedule 1: Sesi 1, Kamis 2024-10-24 13:30 - 15:30
        // Schedule 2: Sesi 2, Jumat 2024-10-25 09:00 - 11:30 (Different day, no conflict!)
        $sched1 = Schedule::find(1);
        $sched2 = Schedule::find(2);

        // First booking in Sesi 1
        Booking::create([
            'user_id' => $student->id,
            'schedule_id' => $sched1->id,
            'full_name' => $student->name,
            'nim' => $student->nim,
            'major' => $student->major,
            'semester' => $student->semester,
            'whatsapp' => $student->whatsapp,
            'email' => $student->email,
            'status' => 'accepted',
        ]);

        // Second booking in Sesi 2 (should be allowed!)
        $step1Data = [
            'full_name' => $student->name,
            'nim' => $student->nim,
            'major' => $student->major,
            'semester' => $student->semester,
            'whatsapp' => $student->whatsapp,
            'email' => $student->email,
        ];
        $this->actingAs($student)->withSession(['booking_step1' => $step1Data]);
        $response = $this->actingAs($student)->post('/booking/pilih-jadwal', [
            'schedule_id' => $sched2->id,
        ]);

        $response->assertRedirect('/booking/konfirmasi');
        $this->assertEquals(1, $student->activeBookings()->count());

        // Confirm second booking
        $this->actingAs($student)->withSession([
            'booking_step1' => $step1Data,
            'booking_schedule_id' => $sched2->id,
        ]);
        $responseConfirm = $this->actingAs($student)->post('/booking/konfirmasi');
        $responseConfirm->assertRedirect('/booking/sukses');

        // Now student has 2 active bookings in 2 different sessions!
        $this->assertEquals(2, $student->activeBookings()->count());
    }

    public function test_admin_can_approve_and_reject_booking(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $schedule = Schedule::first();
        $schedule->quota = 50;
        $schedule->status = 'available';
        $schedule->save();
        $student = User::where('role', 'mahasiswa')->first();

        $booking = Booking::create([
            'user_id' => $student->id,
            'schedule_id' => $schedule->id,
            'full_name' => $student->name,
            'nim' => $student->nim,
            'major' => $student->major,
            'semester' => $student->semester,
            'whatsapp' => $student->whatsapp,
            'email' => $student->email,
            'status' => 'pending',
        ]);

        // Admin accepts booking via POST with booking_id payload (ID hidden from URL)
        $response = $this->actingAs($admin)->post('/admin/bookings/accept', [
            'booking_id' => $booking->id,
        ]);
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('accepted', $booking->status);

        // Admin rejects booking via POST with booking_id payload (ID hidden from URL)
        $response = $this->actingAs($admin)->post('/admin/bookings/reject', [
            'booking_id' => $booking->id,
            'admin_notes' => 'Kuota sesi lab sudah penuh.',
        ]);
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('rejected', $booking->status);
        $this->assertEquals('Kuota sesi lab sudah penuh.', $booking->admin_notes);
    }

    public function test_admin_session_crud_and_participants_view(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // 1. Admin views sessions index
        $response = $this->actingAs($admin)->get('/admin/sessions');
        $response->assertStatus(200);
        $response->assertSee('Kelola Sesi Mentoring');

        // 2. Admin creates a session
        $uniqueName = 'Sesi Baru Test ' . rand(100, 999);
        $responseCreate = $this->actingAs($admin)->post('/admin/sessions', [
            'title' => $uniqueName,
            'description' => 'Deskripsi testing sesi mentoring',
            'badge_label' => 'Uji Coba',
            'is_active' => '1',
        ]);
        $responseCreate->assertRedirect('/admin/sessions');

        $session = MentoringSession::where('title', $uniqueName)->first();
        $this->assertNotNull($session);

        // 3. Admin views session detail with participants table
        $responseShow = $this->actingAs($admin)->get("/admin/sessions/{$session->id}");
        $responseShow->assertStatus(200);
        $responseShow->assertSee($uniqueName);
        $responseShow->assertSee('Daftar Peserta di Sesi Ini');

        // 4. Admin updates session
        $responseUpdate = $this->actingAs($admin)->put("/admin/sessions/{$session->id}", [
            'title' => $uniqueName . ' Updated',
            'description' => 'Deskripsi updated',
            'badge_label' => 'Updated',
            'is_active' => '1',
        ]);
        $responseUpdate->assertRedirect('/admin/sessions');

        // 5. Admin views schedule detail with participants table
        $schedule = Schedule::first();
        if ($schedule) {
            $responseSchedShow = $this->actingAs($admin)->get("/admin/schedules/{$schedule->id}");
            $responseSchedShow->assertStatus(200);
            $responseSchedShow->assertSee('Daftar Peserta di Jadwal Ini');
        }

        // 6. Admin filters participants by session in /admin/bookings
        $responseFilter = $this->actingAs($admin)->get('/admin/bookings?session_id=' . $session->id);
        $responseFilter->assertStatus(200);
        $responseFilter->assertSee('Data Peserta Mentoring');
    }
}
