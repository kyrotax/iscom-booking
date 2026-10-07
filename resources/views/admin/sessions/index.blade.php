@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 24px;">
  <!-- Header -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 class="font-headline-lg">Kelola Sesi Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Atur daftar sesi mentoring mahasiswa, jadwal di dalamnya, dan pantau peserta terdaftar.
      </p>
    </div>

    <a href="{{ route('admin.sessions.create') }}" class="btn btn-blue">
      <span class="material-symbols-outlined" style="font-size: 18px;">add</span>
      <span>Tambah Sesi Baru</span>
    </a>
  </div>

  <!-- Sesi Table -->
  <div class="table-container">
    <div class="table-responsive">
      <table class="iscom-table">
        <thead>
          <tr>
            <th style="width: 50px;">No.</th>
            <th>Judul Sesi</th>
            <th>Slug & Label</th>
            <th>Jadwal Dibuka</th>
            <th>Peserta Terdaftar</th>
            <th>Status</th>
            <th style="text-align: right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sessions as $index => $sess)
            <tr>
              <td style="color: var(--on-surface-variant); font-weight: 500;">
                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
              </td>
              <td>
                <span style="font-weight: 600; color: var(--on-surface); font-size: 14px;">{{ $sess->title }}</span>
                <span style="display: block; font-size: 12px; color: var(--on-surface-variant); max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                  {{ $sess->description ?: 'Tidak ada deskripsi' }}
                </span>
              </td>
              <td>
                <span style="font-family: monospace; font-size: 12px; background: var(--surface-container); padding: 2px 6px; border-radius: var(--radius-sm);">
                  /{{ $sess->slug }}
                </span>
                <span style="display: block; font-size: 11px; color: var(--primary); font-weight: 500; margin-top: 2px;">
                  {{ $sess->badge_label }}
                </span>
              </td>
              <td>
                <span style="font-weight: 600;">{{ $sess->schedules_count }} Jadwal</span>
              </td>
              <td>
                <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                  <span class="slot-badge slot-badge-green" style="font-size: 11px;">
                    {{ $sess->accepted_bookings_count }} Diterima
                  </span>
                  @if($sess->pending_bookings_count > 0)
                    <span class="slot-badge slot-badge-orange" style="font-size: 11px;">
                      {{ $sess->pending_bookings_count }} Menunggu
                    </span>
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
                <div style="display: inline-flex; gap: 6px;">
                  <!-- Lihat Peserta & Jadwal -->
                  <a href="{{ route('admin.sessions.show', $sess->id) }}" class="btn btn-blue btn-sm" title="Lihat Peserta di Sesi Ini">
                    <span class="material-symbols-outlined" style="font-size: 16px;">group</span>
                    <span>Peserta ({{ $sess->total_bookings_count }})</span>
                  </a>

                  <!-- Edit Sesi -->
                  <a href="{{ route('admin.sessions.edit', $sess->id) }}" class="btn btn-outline btn-sm" title="Edit Sesi">
                    <span class="material-symbols-outlined" style="font-size: 16px;">edit</span>
                  </a>

                  <!-- Hapus Sesi -->
                  <form action="{{ route('admin.sessions.destroy', $sess->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi ini? Seluruh jadwal dan pendaftaran di dalamnya akan ikut terhapus.')" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost btn-sm" title="Hapus Sesi" style="color: var(--error);">
                      <span class="material-symbols-outlined" style="font-size: 16px;">delete</span>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" style="text-align: center; padding: 40px; color: var(--on-surface-variant);">
                Belum ada sesi mentoring. Klik tombol "Tambah Sesi Baru" untuk membuat sesi pertama.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
