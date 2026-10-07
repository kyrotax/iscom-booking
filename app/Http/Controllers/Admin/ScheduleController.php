<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MentoringSession;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('mentoringSession')->withCount([
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
        $sessions = MentoringSession::all();
        return view('admin.schedules.create', compact('sessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mentoring_session_id' => 'nullable|exists:mentoring_sessions,id',
            'day_name' => 'required|string|max:50',
            'schedule_date' => 'required|date',
            'time_slot' => 'required|string|max:100',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'mentor_names' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'quota' => 'required|integer|min:1|max:100',
        ]);

        if (empty($validated['start_time']) && preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $validated['time_slot'], $matches)) {
            $validated['start_time'] = $matches[1] . ':00';
            $validated['end_time'] = $matches[2] . ':00';
        }

        $schedule = Schedule::create($validated);
        $schedule->recalculateStatus();

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal mentoring berhasil ditambahkan.');
    }

    public function show(Schedule $schedule)
    {
        $schedule->load(['mentoringSession', 'bookings.user']);
        $participants = $schedule->bookings()->latest()->get();

        return view('admin.schedules.show', compact('schedule', 'participants'));
    }

    public function edit(Schedule $schedule)
    {
        $sessions = MentoringSession::all();
        return view('admin.schedules.edit', compact('schedule', 'sessions'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'mentoring_session_id' => 'nullable|exists:mentoring_sessions,id',
            'day_name' => 'required|string|max:50',
            'schedule_date' => 'required|date',
            'time_slot' => 'required|string|max:100',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'mentor_names' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'topic' => 'required|string|max:255',
            'quota' => 'required|integer|min:1|max:100',
        ]);

        if (empty($validated['start_time']) && preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $validated['time_slot'], $matches)) {
            $validated['start_time'] = $matches[1] . ':00';
            $validated['end_time'] = $matches[2] . ':00';
        }

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
