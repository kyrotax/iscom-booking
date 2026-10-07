<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingSchedules = Schedule::orderBy('schedule_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->take(3)
            ->get();

        return view('home', compact('upcomingSchedules'));
    }

    public function jadwal()
    {
        $allSchedules = Schedule::orderBy('schedule_date', 'asc')
            ->orderBy('time_slot', 'asc')
            ->get();

        return view('jadwal', compact('allSchedules'));
    }
}
