<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $sessionId = $request->query('session_id');

        $query = Booking::with(['schedule.mentoringSession', 'user'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($sessionId) {
            $query->whereHas('schedule', function ($q) use ($sessionId) {
                $q->where('mentoring_session_id', $sessionId);
            });
        }

        $bookings = $query->paginate(20)->withQueryString();
        $sessions = \App\Models\MentoringSession::all();

        return view('admin.bookings.index', compact('bookings', 'status', 'sessionId', 'sessions'));
    }

    public function accept(Request $request, ?Booking $booking = null)
    {
        if ((!$booking || !$booking->exists) && $request->filled('booking_id')) {
            $booking = Booking::with('schedule')->findOrFail($request->booking_id);
        } elseif (!$booking || !$booking->exists) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $schedule = $booking->schedule;

        if ($schedule->remaining_slots <= 0 && $booking->status !== 'accepted') {
            return back()->with('error', 'Gagal menerima booking: Kuota pada jadwal ini sudah penuh.');
        }

        $booking->status = 'accepted';
        $booking->save();

        $schedule->recalculateStatus();

        return back()->with('success', "Booking {$booking->booking_code} milik {$booking->full_name} berhasil DITERIMA (Accepted).");
    }

    public function reject(Request $request, ?Booking $booking = null)
    {
        if ((!$booking || !$booking->exists) && $request->filled('booking_id')) {
            $booking = Booking::with('schedule')->findOrFail($request->booking_id);
        } elseif (!$booking || !$booking->exists) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $booking->status = 'rejected';
        if ($request->filled('admin_notes')) {
            $booking->admin_notes = $request->admin_notes;
        }
        $booking->save();

        $booking->schedule->recalculateStatus();

        return back()->with('success', "Booking {$booking->booking_code} telah DITOLAK (Rejected).");
    }

    public function destroy(Request $request, ?Booking $booking = null)
    {
        if ((!$booking || !$booking->exists) && $request->filled('booking_id')) {
            $booking = Booking::with('schedule')->findOrFail($request->booking_id);
        } elseif (!$booking || !$booking->exists) {
            abort(404, 'Booking tidak ditemukan.');
        }

        $schedule = $booking->schedule;
        $booking->delete();

        if ($schedule) {
            $schedule->recalculateStatus();
        }

        return back()->with('success', "Data booking berhasil dihapus.");
    }
}
