@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
  <!-- Header & Back Navigation -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <a href="{{ route('admin.schedules.index') }}" class="section-link" style="font-size: 13px;">← Kembali ke Kelola Jadwal</a>
      </div>
      <h1 class="font-headline-lg">{{ $schedule->day_name }}, {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Sesi: <strong>{{ $schedule->mentoringSession->title ?? 'Umum' }}</strong> • {{ $schedule->time_slot }} • {{ $schedule->location }}
      </p>
    </div>

    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn btn-outline btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
        <span>Edit Jadwal</span>
      </a>
      @if($schedule->mentoring_session_id)
        <a href="{{ route('admin.sessions.show', $schedule->mentoring_session_id) }}" class="btn btn-blue btn-sm">
          <span class="material-symbols-outlined" style="font-size: 16px;">category</span>
          <span>Lihat Sesi Induk</span>
        </a>
      @endif
    </div>
  </div>

  <!-- Metric Details -->
  <div style="display: flex; gap: 12px; flex-wrap: wrap;">
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 160px;">
      <span class="font-label-sm" style="color: var(--on-surface-variant);">Kapasitas Kuota</span>
      <h3 class="font-headline-md" style="margin-top: 4px;">{{ $schedule->quota }} Kursi</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 160px;">
      <span class="font-label-sm" style="color: var(--on-surface-variant);">Sisa Kuota</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--tertiary);">{{ $schedule->remaining_slots }} Kursi</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 160px;">
      <span class="font-label-sm" style="color: var(--on-surface-variant);">Total Mendaftar</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--primary);">{{ $participants->count() }} Orang</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 160px;">
      <span class="font-label-sm" style="color: var(--tertiary);">Peserta Diterima</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--tertiary);">{{ $participants->where('status', 'accepted')->count() }} Orang</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 160px;">
      <span class="font-label-sm" style="color: var(--secondary);">Menunggu Approval</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--secondary);">{{ $participants->where('status', 'pending')->count() }} Orang</h3>
    </div>
  </div>

  <!-- DAFTAR PESERTA DI JADWAL INI -->
  <div class="table-container">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
      <div>
        <h2 class="font-headline-sm">Daftar Peserta di Jadwal Ini</h2>
        <p class="font-body-sm" style="color: var(--on-surface-variant);">Mahasiswa yang mendaftar pada jadwal ini beserta status approval lab.</p>
      </div>

      <span class="slot-badge slot-badge-gray">Total {{ $participants->count() }} Pendaftar</span>
    </div>

    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama Mahasiswa</th>
            <th>NIM</th>
            <th>Program Studi</th>
            <th>Kontak (WA)</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi Approval</th>
          </tr>
        </thead>
        <tbody>
          @forelse($participants as $b)
            <tr>
              <td style="font-weight: 700; color: var(--primary); font-size: 12px;">{{ $b->booking_code }}</td>
              <td>
                <span style="font-weight: 600;">{{ $b->full_name }}</span>
              </td>
              <td style="font-family: monospace; font-size: 13px;">{{ $b->nim }}</td>
              <td>
                <span>{{ $b->major }}</span>
                <span style="display: block; font-size: 11px; color: var(--on-surface-variant);">Semester {{ $b->semester }}</span>
              </td>
              <td>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $b->whatsapp) }}" target="_blank" style="color: var(--primary); font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                  <span class="material-symbols-outlined" style="font-size: 14px;">chat</span>
                  {{ $b->whatsapp }}
                </a>
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
                      <button type="submit" class="btn btn-blue btn-sm" title="Terima Peserta">
                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                        <span>Terima</span>
                      </button>
                    </form>
                  @endif

                  @if($b->status !== 'rejected')
                    <form action="{{ route('admin.bookings.reject') }}" method="POST" style="display: inline;">
                      @csrf
                      <input type="hidden" name="booking_id" value="{{ $b->id }}">
                      <button type="submit" class="btn btn-outline btn-sm" title="Tolak Peserta" style="color: var(--error);">
                        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                        <span>Tolak</span>
                      </button>
                    </form>
                  @endif

                  <form action="{{ route('admin.bookings.destroy') }}" method="POST" onsubmit="return confirm('Hapus data pendaftaran ini?')" style="display: inline;">
                    @csrf
                    <input type="hidden" name="booking_id" value="{{ $b->id }}">
                    <button type="submit" class="btn btn-ghost btn-sm" title="Hapus Permanen" style="color: var(--outline);">
                      <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 36px; color: var(--on-surface-variant);">
                Belum ada mahasiswa yang mendaftar di jadwal ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
