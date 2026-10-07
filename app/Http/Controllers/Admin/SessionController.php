<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MentoringSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SessionController extends Controller
{
    /**
     * Tampilkan daftar seluruh sesi mentoring
     */
    public function index()
    {
        $sessions = MentoringSession::withCount(['schedules'])
            ->with(['schedules.bookings'])
            ->orderBy('id', 'asc')
            ->get()
            ->map(function ($session) {
                $allBookings = $session->schedules->flatMap->bookings;
                $session->total_bookings_count = $allBookings->count();
                $session->accepted_bookings_count = $allBookings->where('status', 'accepted')->count();
                $session->pending_bookings_count = $allBookings->where('status', 'pending')->count();
                return $session;
            });

        return view('admin.sessions.index', compact('sessions'));
    }

    /**
     * Form tambah sesi mentoring baru
     */
    public function create()
    {
        return view('admin.sessions.create');
    }

    /**
     * Simpan sesi mentoring baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_label' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['badge_label'] = $validated['badge_label'] ?: 'Sesi Terbuka';
        $validated['slug'] = Str::slug($validated['title']);

        // Pastikan slug unik
        $baseSlug = $validated['slug'];
        $count = 1;
        while (MentoringSession::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = "{$baseSlug}-{$count}";
            $count++;
        }

        MentoringSession::create($validated);

        return redirect()->route('admin.sessions.index')
            ->with('success', 'Sesi mentoring baru berhasil dibuat.');
    }

    /**
     * Lihat detail sesi mentoring, jadwal di dalamnya, dan daftar peserta
     */
    public function show(MentoringSession $session)
    {
        $session->load([
            'schedules' => function ($q) {
                $q->withCount([
                    'bookings as accepted_count' => fn ($b) => $b->where('status', 'accepted'),
                    'bookings as pending_count' => fn ($b) => $b->where('status', 'pending'),
                ])->orderBy('schedule_date', 'asc');
            },
        ]);

        $scheduleIds = $session->schedules->pluck('id');

        $participants = Booking::whereIn('schedule_id', $scheduleIds)
            ->with(['schedule', 'user'])
            ->latest()
            ->get();

        return view('admin.sessions.show', compact('session', 'participants'));
    }

    /**
     * Form edit sesi mentoring
     */
    public function edit(MentoringSession $session)
    {
        return view('admin.sessions.edit', compact('session'));
    }

    /**
     * Update data sesi mentoring
     */
    public function update(Request $request, MentoringSession $session)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'badge_label' => 'nullable|string|max:100',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['badge_label'] = $validated['badge_label'] ?: 'Sesi Terbuka';

        if ($session->title !== $validated['title']) {
            $validated['slug'] = Str::slug($validated['title']);
            $baseSlug = $validated['slug'];
            $count = 1;
            while (MentoringSession::where('slug', $validated['slug'])->where('id', '!=', $session->id)->exists()) {
                $validated['slug'] = "{$baseSlug}-{$count}";
                $count++;
            }
        }

        $session->update($validated);

        return redirect()->route('admin.sessions.index')
            ->with('success', "Sesi '{$session->title}' berhasil diperbarui.");
    }

    /**
     * Hapus sesi mentoring
     */
    public function destroy(MentoringSession $session)
    {
        $title = $session->title;
        $session->delete();

        return redirect()->route('admin.sessions.index')
            ->with('success', "Sesi '{$title}' berhasil dihapus.");
    }
}
