@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 class="font-headline-lg">Kelola Jadwal Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Atur sesi mentoring ISCOM, tetapkan mentor, ruang lab, dan kapasitas kuota peserta.
      </p>
    </div>

    <a href="{{ route('admin.schedules.create') }}" class="btn btn-primary">
      <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
      <span>Tambah Jadwal Baru</span>
    </a>
  </div>

  <div class="table-container">
    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th>Sesi</th>
            <th>Hari & Tanggal</th>
            <th>Waktu Sesi</th>
            <th>Mentor</th>
            <th>Lokasi & Topik</th>
            <th>Kuota / Terisi</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedules as $sched)
            <tr>
              <td>
                <span style="font-weight: 600; color: var(--primary);">
                  {{ $sched->mentoringSession->title ?? '-' }}
                </span>
              </td>
              <td>
                <span style="font-weight: 600;">{{ $sched->day_name }}</span>
                <span style="display: block; font-size: 12px; color: var(--on-surface-variant);">
                  {{ \Carbon\Carbon::parse($sched->schedule_date)->format('d F Y') }}
                </span>
              </td>
              <td style="font-weight: 500;">{{ $sched->time_slot }}</td>
              <td>{{ $sched->mentor_names }}</td>
              <td>
                <span style="font-weight: 500;">{{ $sched->location }}</span>
                <span style="display: block; font-size: 12px; color: var(--on-surface-variant);">{{ $sched->topic }}</span>
              </td>
              <td>
                <span style="font-weight: 600; color: var(--primary);">{{ $sched->accepted_count }} / {{ $sched->quota }}</span>
                <span style="display: block; font-size: 11px; color: var(--on-surface-variant);">
                  Sisa: {{ $sched->remaining_slots }}
                </span>
              </td>
              <td>
                @if($sched->remaining_slots > 3)
                  <span class="slot-badge slot-badge-green">Tersedia</span>
                @elseif($sched->remaining_slots > 0)
                  <span class="slot-badge slot-badge-orange">Hampir Penuh</span>
                @else
                  <span class="slot-badge slot-badge-gray">Penuh</span>
                @endif
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px;">
                  <a href="{{ route('admin.schedules.show', $sched->id) }}" class="btn btn-blue btn-sm" title="Lihat Peserta di Jadwal Ini">
                    <span class="material-symbols-outlined" style="font-size: 16px;">group</span>
                    <span>Peserta ({{ $sched->accepted_count + $sched->pending_count }})</span>
                  </a>
                  <a href="{{ route('admin.schedules.edit', $sched->id) }}" class="btn btn-outline btn-sm" title="Edit Jadwal">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                  </a>

                  <form action="{{ route('admin.schedules.destroy', $sched->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini? Seluruh data booking terkait akan ikut terhapus.')" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline btn-sm" title="Hapus Jadwal" style="color: var(--error);">
                      <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 32px; color: var(--on-surface-variant);">
                Belum ada jadwal mentoring. Klik "Tambah Jadwal Baru" untuk membuat sesi pertama.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
