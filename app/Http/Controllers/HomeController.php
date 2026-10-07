<?php

namespace App\Http\Controllers;

use App\Models\MentoringSession;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Beranda (Home Page): Menampilkan Sesi Mentoring yang Tersedia
     */
    public function index()
    {
        $activeSessions = MentoringSession::with(['schedules'])
            ->where('is_active', true)
            ->get();

        // Also provide schedules for backward compatibility or metrics
        $upcomingSchedules = Schedule::with('mentoringSession')
            ->orderBy('schedule_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->take(3)
            ->get();

        return view('home', compact('activeSessions', 'upcomingSchedules'));
    }

    /**
     * Menu Sesi: Daftar Seluruh Sesi Mentoring
     */
    public function sesi()
    {
        $sessions = MentoringSession::with(['schedules'])
            ->where('is_active', true)
            ->get();

        return view('sesi', compact('sessions'));
    }

    /**
     * Pilih Sesi menggunakan POST sehingga ID numerik tidak terekspos di URL
     */
    public function pilihSesi(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required',
        ]);

        $session = MentoringSession::where('id', $validated['session_id'])
            ->orWhere('slug', $validated['session_id'])
            ->firstOrFail();

        return redirect()->route('sesi.show', $session->slug ?? $session->id);
    }

    /**
     * Detail Sesi: Membuka 1 sesi untuk melihat bermacam-macam jadwal di dalamnya
     */
    public function showSesi(MentoringSession $mentoringSession)
    {
        $mentoringSession->load(['schedules' => function ($query) {
            $query->orderBy('schedule_date', 'asc')->orderBy('time_slot', 'asc');
        }]);

        $user = Auth::user();
        $userBookedScheduleIds = [];
        $isSessionBookedByUser = false;
        $conflictMap = [];

        if ($user) {
            $userBookings = $user->activeBookings()->with('schedule.mentoringSession')->get();
            $userBookedScheduleIds = $userBookings->pluck('schedule_id')->toArray();

            // Check if user already booked ANY schedule in this session
            $isSessionBookedByUser = $user->hasBookingForSession($mentoringSession->id);

            // Compute conflict map for each schedule in this session
            foreach ($mentoringSession->schedules as $sched) {
                if (in_array($sched->id, $userBookedScheduleIds)) {
                    continue;
                }
                $conflict = $user->findConflictingSchedule($sched);
                if ($conflict) {
                    $conflictMap[$sched->id] = $conflict;
                }
            }
        }

        return view('sesi_detail', compact('mentoringSession', 'userBookedScheduleIds', 'isSessionBookedByUser', 'conflictMap'));
    }

    /**
     * Backward-compatibility redirect for /jadwal
     */
    public function jadwal()
    {
        return redirect()->route('sesi');
    }
}
