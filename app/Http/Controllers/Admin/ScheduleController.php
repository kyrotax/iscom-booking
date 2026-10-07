<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::withCount([
            'bookings as accepted_count' => function ($query) {
                $query->where('status', 'accepted');
            },
            'bookings as pending_count' => function ($query) {
                $query->where('status', 'pending');
            }
        ])->orderBy('schedule_date', 'asc')->get();

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('admin.schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_name' => 'required|string|max:50',
            'schedule_date' => 'required|date',
            'time_slot' => 'required|string|max:100',
            'mentor_names' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'quota' => 'required|integer|min:1|max:100',
        ]);

        $schedule = Schedule::create($validated);
        $schedule->recalculateStatus();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal mentoring berhasil ditambahkan.');
    }

    public function edit(Schedule $schedule)
    {
        return view('admin.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'day_name' => 'required|string|max:50',
            'schedule_date' => 'required|date',
            'time_slot' => 'required|string|max:100',
            'mentor_names' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'quota' => 'required|integer|min:1|max:100',
        ]);

        $schedule->update($validated);
        $schedule->recalculateStatus();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal mentoring berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal mentoring berhasil dihapus.');
    }
}
