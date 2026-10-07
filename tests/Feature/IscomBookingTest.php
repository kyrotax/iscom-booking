<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IscomBookingTest extends TestCase
{
    public function test_home_page_loads_with_branding_and_schedules(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('ISCOM');
        $response->assertSee('Mentoring');
        $response->assertSee('Belajar bareng');
        $response->assertSee('Jadwal yang tersedia');
    }

    public function test_jadwal_page_loads_successfully(): void
    {
        $response = $this->get('/jadwal');

        $response->assertStatus(200);
        $response->assertSee('Jadwal Mentoring Mahasiswa');
    }

    public function test_status_page_loads_successfully(): void
    {
        $response = $this->get('/status');

        $response->assertStatus(200);
        $response->assertSee('Status Booking Mentoring');
        $response->assertSee('Daftar Mentoring');
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
        // Create a new unique student
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

    public function test_admin_can_approve_and_reject_booking(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // Create a pending booking
        $schedule = Schedule::first();
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

        // Admin accepts booking
        $response = $this->actingAs($admin)->patch("/admin/bookings/{$booking->id}/accept");
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('accepted', $booking->status);

        // Admin rejects booking
        $response = $this->actingAs($admin)->patch("/admin/bookings/{$booking->id}/reject", [
            'admin_notes' => 'Kuota sesi lab sudah penuh.',
        ]);
        $response->assertSessionHas('success');

        $booking->refresh();
        $this->assertEquals('rejected', $booking->status);
        $this->assertEquals('Kuota sesi lab sudah penuh.', $booking->admin_notes);
    }
}
