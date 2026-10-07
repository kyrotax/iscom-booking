<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $acceptedBookings = Booking::where('status', 'accepted')->count();
        $rejectedBookings = Booking::where('status', 'rejected')->count();

        $totalSessions = \App\Models\MentoringSession::count();
        $totalSchedules = Schedule::count();
        $totalStudents = User::where('role', 'mahasiswa')->count();

        $sessionsSummary = \App\Models\MentoringSession::withCount('schedules')
            ->with(['schedules.bookings'])
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($session) {
                $bookings = $session->schedules->flatMap->bookings;
                $session->total_participants = $bookings->count();
                $session->accepted_participants = $bookings->where('status', 'accepted')->count();
                $session->pending_participants = $bookings->where('status', 'pending')->count();
                return $session;
            });

        $recentBookings = Booking::with(['schedule.mentoringSession', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalSessions',
            'totalBookings',
            'pendingBookings',
            'acceptedBookings',
            'rejectedBookings',
            'totalSchedules',
            'totalStudents',
            'sessionsSummary',
            'recentBookings'
        ));
    }
}
