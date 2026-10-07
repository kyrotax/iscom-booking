@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 28px;">
  <!-- Dashboard Header -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 class="font-headline-lg">Dashboard Pengurus ISCOM</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Pusat kendali sesi mentoring, jadwal praktikum lab, dan daftar peserta mahasiswa.
      </p>
    </div>

    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
      <a href="{{ route('admin.sessions.create') }}" class="btn btn-blue btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">category</span>
        <span>Tambah Sesi</span>
      </a>
      <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
        <span>Tambah Jadwal</span>
      </a>
    </div>
  </div>

  <!-- Metric Statistics Cards -->
  <div class="metrics-grid">
    <!-- Card Sesi -->
    <a href="{{ route('admin.sessions.index') }}" class="metric-card" style="text-decoration: none; color: inherit; transition: transform 0.15s ease;">
      <div class="metric-icon-wrap metric-icon-blue">
        <span class="material-symbols-outlined" style="font-size: 26px;">category</span>
      </div>
      <div>
        <div class="metric-value">{{ $totalSessions }}</div>
        <div class="metric-label">Sesi Mentoring</div>
      </div>
    </a>

    <!-- Card Jadwal -->
    <a href="{{ route('admin.schedules.index') }}" class="metric-card" style="text-decoration: none; color: inherit; transition: transform 0.15s ease;">
      <div class="metric-icon-wrap metric-icon-blue">
        <span class="material-symbols-outlined" style="font-size: 26px;">calendar_month</span>
      </div>
      <div>
        <div class="metric-value">{{ $totalSchedules }}</div>
        <div class="metric-label">Jadwal Lab Aktif</div>
      </div>
    </a>

    <!-- Card Total Peserta -->
    <a href="{{ route('admin.bookings.index') }}" class="metric-card" style="text-decoration: none; color: inherit; transition: transform 0.15s ease;">
      <div class="metric-icon-wrap metric-icon-blue">
        <span class="material-symbols-outlined" style="font-size: 26px;">group</span>
      </div>
      <div>
        <div class="metric-value">{{ $totalBookings }}</div>
        <div class="metric-label">Total Peserta Terdaftar</div>
      </div>
    </a>

    <!-- Card Menunggu Approval -->
    <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="metric-card" style="text-decoration: none; color: inherit; transition: transform 0.15s ease;">
      <div class="metric-icon-wrap metric-icon-orange">
        <span class="material-symbols-outlined" style="font-size: 26px;">hourglass_top</span>
      </div>
      <div>
        <div class="metric-value">{{ $pendingBookings }}</div>
        <div class="metric-label">Menunggu Approval</div>
      </div>
    </a>

    <!-- Card Diterima -->
    <a href="{{ route('admin.bookings.index', ['status' => 'accepted']) }}" class="metric-card" style="text-decoration: none; color: inherit; transition: transform 0.15s ease;">
      <div class="metric-icon-wrap metric-icon-green">
        <span class="material-symbols-outlined" style="font-size: 26px;">check_circle</span>
      </div>
      <div>
        <div class="metric-value">{{ $acceptedBookings }}</div>
        <div class="metric-label">Diterima di Lab</div>
      </div>
    </a>
  </div>

  <!-- BAGIAN 1: RINGKASAN SESI MENTORING & PESERTA -->
  <div class="table-container">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h2 class="font-headline-sm">Sesi Mentoring & Peserta</h2>
        <p class="font-body-sm" style="color: var(--on-surface-variant);">Ringkasan topik sesi, jadwal, dan mahasiswa yang telah mendaftar.</p>
      </div>
      <a href="{{ route('admin.sessions.index') }}" class="section-link">Kelola Semua Sesi →</a>
    </div>

    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Judul Sesi</th>
            <th>Badge Kategori</th>
            <th>Jadwal Dibuka</th>
            <th>Peserta Terdaftar</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sessionsSummary as $sess)
            <tr>
              <td>
                <span style="font-weight: 600; color: var(--on-surface);">{{ $sess->title }}</span>
                <span style="display: block; font-family: monospace; font-size: 11px; color: var(--on-surface-variant);">/{{ $sess->slug }}</span>
              </td>
              <td>
                <span style="font-size: 12px; color: var(--primary); font-weight: 500;">{{ $sess->badge_label }}</span>
              </td>
              <td>
                <span style="font-weight: 600;">{{ $sess->schedules_count }} Jadwal</span>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="slot-badge slot-badge-green" style="font-size: 11px;">{{ $sess->accepted_participants }} Diterima</span>
                  @if($sess->pending_participants > 0)
                    <span class="slot-badge slot-badge-orange" style="font-size: 11px;">{{ $sess->pending_participants }} Menunggu</span>
                  @endif
                </div>
              </td>
              <td>
                @if($sess->is_active)
                  <span class="slot-badge slot-badge-green">Aktif</span>
                @else
                  <span class="slot-badge slot-badge-gray">Nonaktif</span>
                @endif
              </td>
              <td style="text-align: right;">
                <a href="{{ route('admin.sessions.show', $sess->id) }}" class="btn btn-blue btn-sm">
                  <span class="material-symbols-outlined" style="font-size: 16px;">group</span>
                  <span>Lihat Peserta ({{ $sess->total_participants }})</span>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 28px; color: var(--on-surface-variant);">
                Belum ada sesi mentoring.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- BAGIAN 2: PENDAFTARAN PESERTA TERBARU -->
  <div class="table-container">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h2 class="font-headline-sm">Pendaftaran Peserta Terbaru</h2>
        <p class="font-body-sm" style="color: var(--on-surface-variant);">Daftar mahasiswa yang baru saja memesan jadwal sesi di lab.</p>
      </div>
      <a href="{{ route('admin.bookings.index') }}" class="section-link">Semua Peserta ({{ $totalBookings }}) →</a>
    </div>

    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Mahasiswa</th>
            <th>NIM & Prodi</th>
            <th>Sesi & Jadwal Terpilih</th>
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
              </td>
              <td>
                <span style="font-family: monospace; font-size: 12px;">{{ $b->nim }}</span>
                <span style="display: block; font-size: 11px; color: var(--on-surface-variant);">{{ $b->major }}</span>
              </td>
              <td>
                <span style="font-weight: 600; color: var(--primary); display: block; font-size: 13px;">
                  {{ $b->schedule->mentoringSession->title ?? 'Sesi Mentoring' }}
                </span>
                <span style="font-weight: 500;">{{ $b->schedule->day_name }} ({{ $b->schedule->time_slot }})</span>
                <span style="font-size: 11px; color: var(--on-surface-variant); display: block;">{{ $b->schedule->location }}</span>
              </td>
              <td>
                @if($b->status === 'accepted')
                  <span class="slot-badge slot-badge-green">Diterima</span>
                @elseif($b->status === 'pending')
                  <span class="slot-badge slot-badge-orange">Menunggu</span>
                @else
                  <span class="slot-badge slot-badge-red">Ditolak</span>
                @endif
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px;">
                  @if($b->status !== 'accepted')
                    <form action="{{ route('admin.bookings.accept') }}" method="POST" style="display: inline;">
                      @csrf
                      <input type="hidden" name="booking_id" value="{{ $b->id }}">
                      <button type="submit" class="btn btn-blue btn-sm" title="Terima Mahasiswa">
                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                        <span>Terima</span>
                      </button>
                    </form>
                  @endif

                  @if($b->status !== 'rejected')
                    <form action="{{ route('admin.bookings.reject') }}" method="POST" style="display: inline;">
                      @csrf
                      <input type="hidden" name="booking_id" value="{{ $b->id }}">
                      <button type="submit" class="btn btn-outline btn-sm" title="Tolak" style="color: var(--error);">
                        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                        <span>Tolak</span>
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 32px; color: var(--on-surface-variant);">
                Belum ada pendaftaran masuk dari mahasiswa.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
