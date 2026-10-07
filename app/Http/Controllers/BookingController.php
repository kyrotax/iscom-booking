<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Step 1: Form Data Diri Mahasiswa
     */
    public function step1()
    {
        $user = Auth::user();

        // Check if user already has an active pending or accepted booking
        $existingBooking = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingBooking) {
            return redirect()->route('status')->with('info', 'Anda sudah memiliki pendaftaran mentoring aktif dengan kode ' . $existingBooking->booking_code . '.');
        }

        $sessionData = session('booking_step1', [
            'full_name' => $user->name,
            'nim' => $user->nim,
            'major' => $user->major ?? 'Sistem Informasi',
            'semester' => $user->semester ?? 3,
            'whatsapp' => $user->whatsapp,
            'email' => $user->email,
        ]);

        return view('booking.step1_data_diri', compact('sessionData', 'user'));
    }

    /**
     * Process Step 1
     */
    public function postStep1(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'nim' => 'required|string|max:20',
            'major' => 'required|string|max:100',
            'semester' => 'required|integer|min:1|max:14',
            'whatsapp' => 'required|string|max:25',
            'email' => 'required|email|max:255',
        ]);

        session(['booking_step1' => $validated]);

        return redirect()->route('booking.step2');
    }

    /**
     * Step 2: Pilih Jadwal Mentoring
     */
    public function step2()
    {
        if (!session()->has('booking_step1')) {
            return redirect()->route('booking.step1');
        }

        $schedules = Schedule::orderBy('schedule_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        $selectedScheduleId = session('booking_schedule_id');

        return view('booking.step2_pilih_jadwal', compact('schedules', 'selectedScheduleId'));
    }

    /**
     * Process Step 2
     */
    public function postStep2(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ], [
            'schedule_id.required' => 'Silakan pilih salah satu jadwal mentoring yang tersedia.',
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);

        if ($schedule->remaining_slots <= 0) {
            return back()->with('error', 'Mohon maaf, kuota jadwal yang Anda pilih sudah penuh. Silakan pilih jadwal lain.');
        }

        session(['booking_schedule_id' => $schedule->id]);

        return redirect()->route('booking.step3');
    }

    /**
     * Step 3: Konfirmasi Data & Jadwal
     */
    public function step3()
    {
        if (!session()->has('booking_step1')) {
            return redirect()->route('booking.step1');
        }

        if (!session()->has('booking_schedule_id')) {
            return redirect()->route('booking.step2');
        }

        $studentData = session('booking_step1');
        $schedule = Schedule::findOrFail(session('booking_schedule_id'));

        return view('booking.step3_konfirmasi', compact('studentData', 'schedule'));
    }

    /**
     * Final Submission: Simpan Booking
     */
    public function confirmBooking(Request $request)
    {
        if (!session()->has('booking_step1') || !session()->has('booking_schedule_id')) {
            return redirect()->route('booking.step1');
        }

        $user = Auth::user();

        // Enforce 1 active booking rule
        $existingBooking = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingBooking) {
            return redirect()->route('status')->with('error', 'Anda sudah memiliki booking aktif yang sedang diproses atau diterima.');
        }

        $studentData = session('booking_step1');
        $scheduleId = session('booking_schedule_id');
        $schedule = Schedule::findOrFail($scheduleId);

        if ($schedule->remaining_slots <= 0) {
            return redirect()->route('booking.step2')->with('error', 'Kuota jadwal baru saja habis. Silakan pilih jadwal lain.');
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'full_name' => $studentData['full_name'],
            'nim' => $studentData['nim'],
            'major' => $studentData['major'],
            'semester' => $studentData['semester'],
            'whatsapp' => $studentData['whatsapp'],
            'email' => $studentData['email'],
            'status' => 'pending',
        ]);

        // Clean up wizard session
        session()->forget(['booking_step1', 'booking_schedule_id']);
        session(['recent_booking_id' => $booking->id]);

        return redirect()->route('booking.sukses');
    }

    /**
     * Step 4: Booking Sukses / Tiket Awal
     */
    public function sukses()
    {
        $recentBookingId = session('recent_booking_id');
        $booking = null;

        if ($recentBookingId) {
            $booking = Booking::with('schedule')->find($recentBookingId);
        }

        if (!$booking && Auth::check()) {
            $booking = Auth::user()->latestBooking;
            if ($booking) {
                $booking->load('schedule');
            }
        }

        if (!$booking) {
            return redirect()->route('home');
        }

        return view('booking.step4_sukses', compact('booking'));
    }

    /**
     * Status Booking & Daftar Mentoring
     */
    public function status()
    {
        $user = Auth::user();
        $userBooking = null;

        if ($user) {
            $userBooking = Booking::with('schedule')
                ->where('user_id', $user->id)
                ->latest()
                ->first();
        }

        $acceptedBookings = Booking::with('schedule')
            ->where('status', 'accepted')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('status', compact('userBooking', 'acceptedBookings'));
    }
}
