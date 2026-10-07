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

        $totalSchedules = Schedule::count();
        $totalStudents = User::where('role', 'mahasiswa')->count();

        $recentBookings = Booking::with(['schedule', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'acceptedBookings',
            'rejectedBookings',
            'totalSchedules',
            'totalStudents',
            'recentBookings'
        ));
    }
}
