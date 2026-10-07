@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
  <!-- Header & Back Navigation -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <a href="{{ route('admin.sessions.index') }}" class="section-link" style="font-size: 13px;">← Kembali ke Kelola Sesi</a>
      </div>
      <h1 class="font-headline-lg">{{ $session->title }}</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        {{ $session->badge_label }} • Slug: <code>/{{ $session->slug }}</code> • Status: {{ $session->is_active ? 'Aktif' : 'Nonaktif' }}
      </p>
    </div>

    <div style="display: flex; gap: 8px;">
      <a href="{{ route('admin.sessions.edit', $session->id) }}" class="btn btn-outline btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
        <span>Edit Sesi</span>
      </a>
      <a href="{{ route('admin.schedules.create') }}?session_id={{ $session->id }}" class="btn btn-blue btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
        <span>Tambah Jadwal di Sesi Ini</span>
      </a>
    </div>
  </div>

  <!-- Metric Pills -->
  <div style="display: flex; gap: 12px; flex-wrap: wrap;">
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 180px;">
      <span class="font-label-sm" style="color: var(--on-surface-variant);">Pilihan Jadwal</span>
      <h3 class="font-headline-md" style="margin-top: 4px;">{{ $session->schedules->count() }} Jadwal</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 180px;">
      <span class="font-label-sm" style="color: var(--on-surface-variant);">Total Peserta Mendaftar</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--primary);">{{ $participants->count() }} Orang</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 180px;">
      <span class="font-label-sm" style="color: var(--tertiary);">Peserta Diterima</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--tertiary);">{{ $participants->where('status', 'accepted')->count() }} Orang</h3>
    </div>
    <div class="card" style="padding: 16px 20px; flex: 1; min-width: 180px;">
      <span class="font-label-sm" style="color: var(--secondary);">Menunggu Approval</span>
      <h3 class="font-headline-md" style="margin-top: 4px; color: var(--secondary);">{{ $participants->where('status', 'pending')->count() }} Orang</h3>
    </div>
  </div>

  <!-- 1. DAFTAR PESERTA DI SESI INI -->
  <div class="table-container">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
      <div>
        <h2 class="font-headline-sm">Daftar Peserta di Sesi Ini</h2>
        <p class="font-body-sm" style="color: var(--on-surface-variant);">Mahasiswa yang telah mendaftar pada salah satu jadwal di sesi mentoring ini.</p>
      </div>

      <span class="slot-badge slot-badge-gray">Total {{ $participants->count() }} Mahasiswa</span>
    </div>

    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Kode</th>
            <th>Nama & Mahasiswa</th>
            <th>Program Studi</th>
            <th>Kontak (WA)</th>
            <th>Jadwal Terpilih</th>
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
                <span style="display: block; font-family: monospace; font-size: 12px; color: var(--on-surface-variant);">
                  NIM: {{ $b->nim }}
                </span>
              </td>
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
                <span style="font-weight: 500;">{{ $b->schedule->day_name }} ({{ $b->schedule->time_slot }})</span>
                <span style="display: block; font-size: 11px; color: var(--on-surface-variant);">
                  {{ $b->schedule->location }}
                </span>
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
                Belum ada mahasiswa yang mendaftar di sesi ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- 2. DAFTAR JADWAL DI SESI INI -->
  <div class="table-container">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between;">
      <div>
        <h2 class="font-headline-sm">Pilihan Jadwal di Sesi Ini</h2>
        <p class="font-body-sm" style="color: var(--on-surface-variant);">Jadwal lab tatap muka yang dibuka untuk sesi mentoring ini.</p>
      </div>

      <a href="{{ route('admin.schedules.create') }}?session_id={{ $session->id }}" class="btn btn-outline btn-sm">
        <span class="material-symbols-outlined" style="font-size: 16px;">add</span>
        <span>Tambah Jadwal</span>
      </a>
    </div>

    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Hari & Tanggal</th>
            <th>Waktu / Jam</th>
            <th>Ruangan / Lab</th>
            <th>Mentor</th>
            <th>Kuota</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($session->schedules as $sched)
            <tr>
              <td>
                <span style="font-weight: 600;">{{ $sched->day_name }}</span>
                <span style="display: block; font-size: 12px; color: var(--on-surface-variant);">
                  {{ \Carbon\Carbon::parse($sched->schedule_date)->format('d M Y') }}
                </span>
              </td>
              <td>{{ $sched->time_slot }}</td>
              <td>{{ $sched->location }}</td>
              <td>{{ $sched->mentor_names }}</td>
              <td>
                <span style="font-weight: 600;">{{ $sched->remaining_slots }} / {{ $sched->quota }} Slot</span>
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px;">
                  <a href="{{ route('admin.schedules.show', $sched->id) }}" class="btn btn-blue btn-sm" title="Lihat Peserta di Jadwal Ini">
                    <span class="material-symbols-outlined" style="font-size: 16px;">group</span>
                    <span>Peserta ({{ $sched->bookings->count() }})</span>
                  </a>
                  <a href="{{ route('admin.schedules.edit', $sched->id) }}" class="btn btn-outline btn-sm" title="Edit Jadwal">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" style="text-align: center; padding: 32px; color: var(--on-surface-variant);">
                Belum ada jadwal yang dibuat untuk sesi ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
