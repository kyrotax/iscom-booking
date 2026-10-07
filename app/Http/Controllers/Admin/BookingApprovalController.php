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

        $query = Booking::with(['schedule', 'user'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(15);

        return view('admin.bookings.index', compact('bookings', 'status'));
    }

    public function accept(Booking $booking)
    {
        $schedule = $booking->schedule;

        if ($schedule->remaining_slots <= 0 && $booking->status !== 'accepted') {
            return back()->with('error', 'Gagal menerima booking: Kuota pada jadwal ini sudah penuh.');
        }

        $booking->status = 'accepted';
        $booking->save();

        $schedule->recalculateStatus();

        return back()->with('success', "Booking {$booking->booking_code} milik {$booking->full_name} berhasil DITERIMA (Accepted).");
    }

    public function reject(Request $request, Booking $booking)
    {
        $booking->status = 'rejected';
        if ($request->filled('admin_notes')) {
            $booking->admin_notes = $request->admin_notes;
        }
        $booking->save();

        $booking->schedule->recalculateStatus();

        return back()->with('success', "Booking {$booking->booking_code} telah DITOLAK (Rejected).");
    }

    public function destroy(Booking $booking)
    {
        $schedule = $booking->schedule;
        $booking->delete();

        if ($schedule) {
            $schedule->recalculateStatus();
        }

        return back()->with('success', "Data booking berhasil dihapus.");
    }
}
