@extends('layouts.admin')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
    <div>
      <h1 class="font-headline-lg">Data Peserta Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Pantau seluruh mahasiswa yang mendaftar pada sesi dan jadwal lab, serta verifikasi status penerimaan.
      </p>
    </div>
  </div>

  <!-- Filter Status & Sesi Bar -->
  <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; border-bottom: 1px solid var(--border); padding-bottom: 12px;">
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
      <a href="{{ route('admin.bookings.index', array_merge(request()->query(), ['status' => 'all'])) }}" class="btn btn-sm {{ $status == 'all' ? 'btn-blue' : 'btn-outline' }}">
        Semua ({{ \App\Models\Booking::when($sessionId, fn($q)=>$q->whereHas('schedule', fn($sq)=>$sq->where('mentoring_session_id', $sessionId)))->count() }})
      </a>
      <a href="{{ route('admin.bookings.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="btn btn-sm {{ $status == 'pending' ? 'btn-blue' : 'btn-outline' }}">
        Menunggu ({{ \App\Models\Booking::where('status', 'pending')->when($sessionId, fn($q)=>$q->whereHas('schedule', fn($sq)=>$sq->where('mentoring_session_id', $sessionId)))->count() }})
      </a>
      <a href="{{ route('admin.bookings.index', array_merge(request()->query(), ['status' => 'accepted'])) }}" class="btn btn-sm {{ $status == 'accepted' ? 'btn-blue' : 'btn-outline' }}">
        Diterima ({{ \App\Models\Booking::where('status', 'accepted')->when($sessionId, fn($q)=>$q->whereHas('schedule', fn($sq)=>$sq->where('mentoring_session_id', $sessionId)))->count() }})
      </a>
      <a href="{{ route('admin.bookings.index', array_merge(request()->query(), ['status' => 'rejected'])) }}" class="btn btn-sm {{ $status == 'rejected' ? 'btn-blue' : 'btn-outline' }}">
        Ditolak ({{ \App\Models\Booking::where('status', 'rejected')->when($sessionId, fn($q)=>$q->whereHas('schedule', fn($sq)=>$sq->where('mentoring_session_id', $sessionId)))->count() }})
      </a>
    </div>

    @if(isset($sessions) && $sessions->isNotEmpty())
      <form method="GET" action="{{ route('admin.bookings.index') }}" style="display: flex; align-items: center; gap: 8px;">
        @if(request('status'))
          <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <label for="session_filter" class="font-label-sm" style="color: var(--on-surface-variant); white-space: nowrap;">Filter Sesi:</label>
        <select id="session_filter" name="session_id" class="form-input" style="padding: 6px 12px; font-size: 13px;" onchange="this.form.submit()">
          <option value="">Semua Sesi</option>
          @foreach($sessions as $sess)
            <option value="{{ $sess->id }}" {{ $sessionId == $sess->id ? 'selected' : '' }}>
              {{ $sess->title }}
            </option>
          @endforeach
        </select>
        @if($sessionId)
          <a href="{{ route('admin.bookings.index', ['status' => $status]) }}" class="btn btn-ghost btn-sm" title="Reset filter sesi">Reset</a>
        @endif
      </form>
    @endif
  </div>

  <!-- Bookings List Table -->
  <div class="table-container">
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
          @forelse($bookings as $b)
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
                <span style="display: block; font-size: 11px; color: var(--on-surface-variant);">Sem {{ $b->semester }}</span>
              </td>
              <td>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $b->whatsapp) }}" target="_blank" style="color: var(--primary); font-size: 12px; display: inline-flex; align-items: center; gap: 4px;">
                  <span class="material-symbols-outlined" style="font-size: 14px;">chat</span>
                  {{ $b->whatsapp }}
                </a>
              </td>
              <td>
                <span style="font-weight: 600; color: var(--primary); display: block; font-size: 13px;">
                  {{ $b->schedule->mentoringSession->title ?? 'Sesi Mentoring' }}
                </span>
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
                  <span class="slot-badge slot-badge-gray">Ditolak</span>
                @endif
              </td>
              <td style="text-align: right;">
                <div style="display: inline-flex; gap: 6px;">
                  @if($b->status !== 'accepted')
                    <form action="{{ route('admin.bookings.accept') }}" method="POST" style="display: inline;">
                      @csrf
                      <input type="hidden" name="booking_id" value="{{ $b->id }}">
                      <button type="submit" class="btn btn-blue btn-sm" title="Terima Booking & Masukkan ke Lab">
                        <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                        <span>Terima</span>
                      </button>
                    </form>
                  @endif

                  @if($b->status !== 'rejected')
                    <form action="{{ route('admin.bookings.reject') }}" method="POST" style="display: inline;">
                      @csrf
                      <input type="hidden" name="booking_id" value="{{ $b->id }}">
                      <button type="submit" class="btn btn-outline btn-sm" title="Tolak Booking" style="color: var(--error);">
                        <span class="material-symbols-outlined" style="font-size: 16px;">close</span>
                        <span>Tolak</span>
                      </button>
                    </form>
                  @endif

                  <form action="{{ route('admin.bookings.destroy') }}" method="POST" onsubmit="return confirm('Hapus data booking ini?')" style="display: inline;">
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
              <td colspan="7" style="text-align: center; padding: 32px; color: var(--on-surface-variant);">
                Tidak ada data booking dengan filter status ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
      <div style="padding: 16px; border-top: 1px solid var(--border);">
        {{ $bookings->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
