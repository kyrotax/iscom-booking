<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MentoringSession;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Memulai Alur Booking menggunakan POST sehingga ID jadwal tidak terlihat di URL
     */
    public function mulaiBooking(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        $user = Auth::user();
        $preselected = Schedule::with('mentoringSession')->findOrFail($validated['schedule_id']);

        // Check Rule 1: 1 jadwal per sesi
        if ($user && $user->hasBookingForSession($preselected->mentoring_session_id)) {
            $sessionTarget = $preselected->mentoringSession->slug ?? $preselected->mentoring_session_id;
            return redirect()->route('sesi.show', $sessionTarget)
                ->with('error', 'Kamu sudah terdaftar pada ' . $preselected->mentoringSession->title . '. Setiap mahasiswa hanya boleh memilih 1 jadwal per sesi.');
        }

        // Check Rule 2: Bentrok jam dan tanggal
        if ($user) {
            $conflict = $user->findConflictingSchedule($preselected);
            if ($conflict) {
                $sessionTarget = $preselected->mentoringSession->slug ?? $preselected->mentoring_session_id;
                $conflictSessionTitle = $conflict->mentoringSession->title ?? 'Sesi Lain';
                $conflictDate = \Carbon\Carbon::parse($conflict->schedule_date)->format('d M Y');
                return redirect()->route('sesi.show', $sessionTarget)
                    ->with('error', "Jadwal ini bentrok dengan {$conflictSessionTitle} yang sudah kamu booking pada tanggal {$conflictDate} ({$conflict->time_slot}).");
            }
        }

        session(['booking_schedule_id' => $preselected->id]);

        return redirect()->route('booking.step1');
    }

    /**
     * Step 1: Form Data Diri Mahasiswa
     */
    public function step1(Request $request)
    {
        $user = Auth::user();

        // Optional pre-selected schedule from Sesi detail page
        if ($request->has('schedule_id')) {
            $preselected = Schedule::with('mentoringSession')->find($request->schedule_id);
            if ($preselected) {
                // Check Rule 1: 1 jadwal per sesi
                if ($user->hasBookingForSession($preselected->mentoring_session_id)) {
                    return redirect()->route('sesi.show', $preselected->mentoring_session_id)
                        ->with('error', 'Kamu sudah terdaftar pada ' . $preselected->mentoringSession->title . '. Setiap mahasiswa hanya boleh memilih 1 jadwal per sesi.');
                }

                // Check Rule 2: Bentrok jam dan tanggal
                $conflict = $user->findConflictingSchedule($preselected);
                if ($conflict) {
                    $conflictSessionTitle = $conflict->mentoringSession->title ?? 'Sesi Lain';
                    $conflictDate = \Carbon\Carbon::parse($conflict->schedule_date)->format('d M Y');
                    return redirect()->route('sesi.show', $preselected->mentoring_session_id)
                        ->with('error', "Jadwal ini bentrok dengan {$conflictSessionTitle} yang sudah kamu booking pada tanggal {$conflictDate} ({$conflict->time_slot}).");
                }

                session(['booking_schedule_id' => $preselected->id]);
            }
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
     * Step 2: Pilih Jadwal Mentoring (Dikelompokkan berdasarkan Sesi)
     */
    public function step2(Request $request)
    {
        if (!session()->has('booking_step1')) {
            return redirect()->route('booking.step1');
        }

        $user = Auth::user();
        $sessions = MentoringSession::with(['schedules' => function ($query) {
            $query->orderBy('schedule_date', 'asc')->orderBy('time_slot', 'asc');
        }])->where('is_active', true)->get();

        $selectedScheduleId = session('booking_schedule_id');
        $userBookedSessionIds = [];
        $userBookedScheduleIds = [];
        $conflictMap = [];

        if ($user) {
            $userBookings = $user->activeBookings()->with('schedule.mentoringSession')->get();
            $userBookedScheduleIds = $userBookings->pluck('schedule_id')->toArray();
            $userBookedSessionIds = $userBookings->pluck('schedule.mentoring_session_id')->filter()->unique()->toArray();

            // Check collision for every schedule against user's active bookings
            foreach ($sessions as $session) {
                foreach ($session->schedules as $sched) {
                    if (in_array($sched->id, $userBookedScheduleIds)) {
                        continue;
                    }
                    $conflict = $user->findConflictingSchedule($sched);
                    if ($conflict) {
                        $conflictMap[$sched->id] = $conflict;
                    }
                }
            }
        }

        return view('booking.step2_pilih_jadwal', compact('sessions', 'selectedScheduleId', 'userBookedSessionIds', 'userBookedScheduleIds', 'conflictMap'));
    }

    /**
     * Process Step 2 with Business Validations
     */
    public function postStep2(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ], [
            'schedule_id.required' => 'Silakan pilih salah satu jadwal mentoring yang tersedia.',
        ]);

        $user = Auth::user();
        $schedule = Schedule::with('mentoringSession')->findOrFail($validated['schedule_id']);

        // 1. Quota Check
        if ($schedule->remaining_slots <= 0) {
            return back()->with('error', 'Mohon maaf, kuota jadwal yang Anda pilih sudah penuh. Silakan pilih jadwal lain.');
        }

        // 2. Rule 1: Maksimal 1 jadwal per sesi
        if ($user && $user->hasBookingForSession($schedule->mentoring_session_id)) {
            $sessionTitle = $schedule->mentoringSession->title ?? 'Sesi ini';
            return back()->with('error', "Kamu sudah terdaftar pada {$sessionTitle}. Setiap mahasiswa hanya boleh memilih 1 jadwal pada satu sesi.");
        }

        // 3. Rule 2: Bentrok jam dan tanggal dengan sesi lain yang sudah dipilih
        if ($user) {
            $conflict = $user->findConflictingSchedule($schedule);
            if ($conflict) {
                $conflictSessionTitle = $conflict->mentoringSession->title ?? 'Sesi Lain';
                $conflictDate = \Carbon\Carbon::parse($conflict->schedule_date)->format('d M Y');
                return back()->with('error', "Jadwal ini bentrok dengan {$conflictSessionTitle} yang sudah kamu booking pada tanggal {$conflictDate} ({$conflict->time_slot}). Mahasiswa tidak dapat memilih jadwal pada waktu yang bersamaan.");
            }
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
        $schedule = Schedule::with('mentoringSession')->findOrFail(session('booking_schedule_id'));

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
        $studentData = session('booking_step1');
        $scheduleId = session('booking_schedule_id');
        $schedule = Schedule::with('mentoringSession')->findOrFail($scheduleId);

        // 1. Quota Check
        if ($schedule->remaining_slots <= 0) {
            return redirect()->route('booking.step2')->with('error', 'Kuota jadwal baru saja habis. Silakan pilih jadwal lain.');
        }

        // 2. Rule 1: Enforce 1 schedule per session
        if ($user->hasBookingForSession($schedule->mentoring_session_id)) {
            $sessionTitle = $schedule->mentoringSession->title ?? 'Sesi ini';
            return redirect()->route('sesi')->with('error', "Kamu sudah terdaftar pada {$sessionTitle}. Setiap mahasiswa hanya boleh memilih 1 jadwal pada satu sesi.");
        }

        // 3. Rule 2: Enforce no date/time collision with other booked sessions
        $conflict = $user->findConflictingSchedule($schedule);
        if ($conflict) {
            $conflictSessionTitle = $conflict->mentoringSession->title ?? 'Sesi Lain';
            $conflictDate = \Carbon\Carbon::parse($conflict->schedule_date)->format('d M Y');
            return redirect()->route('booking.step2')->with('error', "Jadwal ini bentrok dengan {$conflictSessionTitle} yang sudah kamu booking pada tanggal {$conflictDate} ({$conflict->time_slot}).");
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
            $booking = Booking::with(['schedule.mentoringSession', 'user'])->find($recentBookingId);
        }

        if (!$booking && Auth::check()) {
            $booking = Auth::user()->latestBooking;
            if ($booking) {
                $booking->load(['schedule.mentoringSession', 'user']);
            }
        }

        if (!$booking) {
            return redirect()->route('home');
        }

        return view('booking.step4_sukses', compact('booking'));
    }

    /**
     * Status Booking Sesi Mentoring Mahasiswa
     */
    public function status()
    {
        $user = Auth::user();
        $userBookings = collect();

        if ($user) {
            $userBookings = Booking::with('schedule.mentoringSession')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('status', compact('userBookings'));
    }
}
