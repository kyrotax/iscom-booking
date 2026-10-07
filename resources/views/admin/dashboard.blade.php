@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
  <!-- Dashboard Header -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 class="font-headline-lg">Dashboard Pengurus ISCOM</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Pantau ringkasan pendaftaran mentoring, kuota sesi lab, dan status approval mahasiswa.
      </p>
    </div>

    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.schedules.create') }}" class="btn btn-blue btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
        <span>Tambah Jadwal</span>
      </a>
      <a href="{{ route('admin.bookings.index') }}" class="btn btn-primary btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">assignment_turned_in</span>
        <span>Approval Booking</span>
      </a>
    </div>
  </div>

  <!-- Metric Statistics Cards -->
  <div class="metrics-grid">
    <div class="metric-card">
      <div class="metric-icon-wrap metric-icon-blue">
        <span class="material-symbols-outlined" style="font-size: 26px;">group</span>
      </div>
      <div>
        <div class="metric-value">{{ $totalBookings }}</div>
        <div class="metric-label">Total Booking Masuk</div>
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-icon-wrap metric-icon-orange">
        <span class="material-symbols-outlined" style="font-size: 26px;">hourglass_top</span>
      </div>
      <div>
        <div class="metric-value">{{ $pendingBookings }}</div>
        <div class="metric-label">Menunggu Approval</div>
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-icon-wrap metric-icon-green">
        <span class="material-symbols-outlined" style="font-size: 26px;">check_circle</span>
      </div>
      <div>
        <div class="metric-value">{{ $acceptedBookings }}</div>
        <div class="metric-label">Diterima di Lab</div>
      </div>
    </div>

    <div class="metric-card">
      <div class="metric-icon-wrap metric-icon-blue">
        <span class="material-symbols-outlined" style="font-size: 26px;">calendar_month</span>
      </div>
      <div>
        <div class="metric-value">{{ $totalSchedules }}</div>
        <div class="metric-label">Sesi Jadwal Aktif</div>
      </div>
    </div>
  </div>

  <!-- Recent Bookings Table -->
  <div class="table-container">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
      <h2 class="font-headline-sm">Pendaftaran Mentoring Terbaru</h2>
      <a href="{{ route('admin.bookings.index') }}" class="section-link">Lihat Semua →</a>
    </div>

    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Mahasiswa</th>
            <th>NIM</th>
            <th>Jadwal Sesi</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi Cepat</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recentBookings as $b)
            <tr>
              <td style="font-weight: 700; color: var(--primary); font-size: 12px;">{{ $b->booking_code }}</td>
              <td>
                <span style="font-weight: 600;">{{ $b->full_name }}</span>
                <span style="font-size: 11px; color: var(--on-surface-variant); display: block;">{{ $b->major }}</span>
              </td>
              <td style="font-family: monospace;">{{ $b->nim }}</td>
              <td>
                <span style="font-weight: 500;">{{ $b->schedule->day_name }}</span>
                <span style="font-size: 12px; color: var(--on-surface-variant); display: block;">{{ $b->schedule->time_slot }} ({{ $b->schedule->location }})</span>
              </td>
              <td>
                @if($b->status === 'accepted')
                  <span class="slot-badge slot-badge-green">Diterima</span>
                @elseif($b->status === 'pending')
                  <span class="slot-badge slot-badge-orange">Pending</span>
                @else
                  <span class="slot-badge slot-badge-gray">Ditolak</span>
                @endif
              </td>
              <td style="text-align: right;">
                @if($b->status === 'pending')
                  <div style="display: inline-flex; gap: 6px;">
                    <form action="{{ route('admin.bookings.accept', $b->id) }}" method="POST" style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-blue btn-sm" title="Terima Booking">
                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                        <span>Terima</span>
                      </button>
                    </form>

                    <form action="{{ route('admin.bookings.reject', $b->id) }}" method="POST" style="display: inline;">
                      @csrf
                      @method('PATCH')
                      <button type="submit" class="btn btn-outline btn-sm" title="Tolak Booking" style="color: var(--error);">
                        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                        <span>Tolak</span>
                      </button>
                    </form>
                  </div>
                @else
                  <span style="font-size: 12px; color: var(--outline);">Selesai</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 32px; color: var(--on-surface-variant);">
                Belum ada pendaftaran booking masuk.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
