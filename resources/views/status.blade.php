@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <div style="max-width: 960px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px;">
    <!-- Header Section -->
    <div>
      <div style="display: flex; align-items: center; gap: 8px; color: var(--primary); margin-bottom: 4px;">
        <span class="material-symbols-outlined" style="font-size: 20px;">assignment_turned_in</span>
        <span class="font-label-sm" style="letter-spacing: 0.1em; text-transform: uppercase;">PORTAL MENTORING ISCOM</span>
      </div>
      <h1 class="font-headline-xl">Status Sesi Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); max-width: 620px; margin-top: 4px;">
        Informasi sesi mentoring yang kamu ikuti, rincian jadwal lab, serta status apakah pendaftaran kamu diterima atau tidak.
      </p>
    </div>

    @guest
      <!-- GUEST STATE: Minta Pengguna Login Terlebih Dahulu -->
      <div class="card" style="padding: 40px; text-align: center;">
        <div style="width: 56px; height: 56px; border-radius: var(--radius-full); background: rgba(0, 102, 204, 0.08); color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
          <span class="material-symbols-outlined" style="font-size: 32px;">account_circle</span>
        </div>
        <h2 class="font-headline-md" style="margin-bottom: 8px;">Silakan Masuk untuk Melihat Status</h2>
        <p class="font-body-md" style="color: var(--on-surface-variant); max-width: 480px; margin: 0 auto 24px;">
          Masuk dengan akun mahasiswa kamu untuk melihat daftar sesi mentoring yang kamu ikuti, jadwal lab, dan status konfirmasi pendaftaran.
        </p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
          <a href="{{ route('login') }}" class="btn btn-primary">
            <span class="material-symbols-outlined" style="font-size: 18px;">login</span>
            <span>Masuk / Login</span>
          </a>
          <a href="{{ route('sesi') }}" class="btn btn-outline">
            <span>Lihat Sesi Mentoring</span>
          </a>
        </div>
      </div>
    @else
      @php
        $bookingsList = $userBookings ?? collect();
      @endphp

      @if($bookingsList->isNotEmpty())
        <!-- Ringkasan Mahasiswa & Status Sesi -->
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; background: var(--surface-container); padding: 14px 20px; border-radius: var(--radius-md); border: 1px solid var(--border);">
          <div style="display: flex; align-items: center; gap: 10px;">
            <span class="material-symbols-outlined" style="color: var(--primary); font-size: 22px;">badge</span>
            <div>
              <span class="font-label-sm" style="color: var(--on-surface-variant); display: block; font-size: 11px;">Akun Mahasiswa</span>
              <strong style="color: var(--on-surface);">{{ Auth::user()->name }}</strong>
              <span style="color: var(--on-surface-variant); font-size: 13px;">({{ Auth::user()->nim ?? '-' }})</span>
            </div>
          </div>
          <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
            <span class="slot-badge slot-badge-gray" style="font-size: 12px;">Total {{ $bookingsList->count() }} Sesi</span>
            @if($bookingsList->where('status', 'accepted')->count() > 0)
              <span class="slot-badge slot-badge-green" style="font-size: 12px;">{{ $bookingsList->where('status', 'accepted')->count() }} Diterima</span>
            @endif
            @if($bookingsList->where('status', 'pending')->count() > 0)
              <span class="slot-badge slot-badge-orange" style="font-size: 12px;">{{ $bookingsList->where('status', 'pending')->count() }} Menunggu</span>
            @endif
            @if($bookingsList->where('status', 'rejected')->count() > 0)
              <span class="slot-badge slot-badge-red" style="font-size: 12px;">{{ $bookingsList->where('status', 'rejected')->count() }} Tidak Diterima</span>
            @endif
          </div>
        </div>
      @endif

      <!-- Daftar Sesi Mentoring yang Diikuti Mahasiswa -->
      <div style="display: flex; flex-direction: column; gap: 24px;">
        @forelse($bookingsList as $bItem)
          @if($bItem->status === 'accepted')
            <!-- 1. STATUS: DITERIMA -->
            <section class="status-card-accepted" aria-label="Status Booking Diterima">
              <div class="status-header">
                <div style="display: flex; align-items: center; gap: 16px;">
                  <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background-color: var(--surface-low); color: var(--tertiary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span class="material-symbols-outlined" style="font-size: 28px;">check_circle</span>
                  </div>
                  <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                      <span class="font-label-sm" style="color: var(--tertiary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">
                        {{ $bItem->schedule->mentoringSession->title ?? 'Sesi Mentoring' }}
                      </span>
                      <span style="font-size: 11px; padding: 2px 8px; border-radius: var(--radius-full); background: var(--surface-container); color: var(--on-surface-variant); font-family: monospace;">
                        {{ $bItem->booking_code }}
                      </span>
                    </div>
                    <h2 class="font-headline-md" style="margin-top: 2px;">Pendaftaran Diterima</h2>
                  </div>
                </div>

                <span class="slot-badge slot-badge-green" style="font-size: 13px; padding: 6px 14px;">
                  <span class="dot"></span>
                  <span>Diterima • Siap Masuk Lab</span>
                </span>
              </div>

              <!-- Rincian Jadwal -->
              <div class="status-grid-details">
                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span>
                    Hari & Tanggal
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">
                    {{ $bItem->schedule->day_name }}, {{ \Carbon\Carbon::parse($bItem->schedule->schedule_date)->format('d M Y') }}
                  </p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                    Waktu / Jam
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->time_slot }}</p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">meeting_room</span>
                    Ruangan / Lab
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->location }}</p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                    Mentor
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->mentor_names }}</p>
                </div>
              </div>

              <!-- Petunjuk Kehadiran -->
              <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border); margin-top: 8px;">
                <p class="font-body-sm" style="color: var(--on-surface-variant);">
                  Harap hadir 10 menit sebelum sesi dimulai dengan membawa KTM fisik atau digital untuk presensi di lab.
                </p>
                @if($bItem->schedule->mentoring_session_id)
                  <a href="{{ route('sesi.show', $bItem->schedule->mentoringSession->slug ?? $bItem->schedule->mentoring_session_id) }}" class="btn btn-outline btn-sm">
                    <span>Lihat Detail Sesi</span>
                    <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
                  </a>
                @endif
              </div>
            </section>

          @elseif($bItem->status === 'pending')
            <!-- 2. STATUS: MENUNGGU KONFIRMASI -->
            <section class="status-card-pending" aria-label="Status Booking Menunggu Konfirmasi">
              <div class="status-header">
                <div style="display: flex; align-items: center; gap: 16px;">
                  <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background-color: var(--warning-bg); color: var(--secondary); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span class="material-symbols-outlined" style="font-size: 28px;">hourglass_top</span>
                  </div>
                  <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                      <span class="font-label-sm" style="color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">
                        {{ $bItem->schedule->mentoringSession->title ?? 'Sesi Mentoring' }}
                      </span>
                      <span style="font-size: 11px; padding: 2px 8px; border-radius: var(--radius-full); background: var(--surface-container); color: var(--on-surface-variant); font-family: monospace;">
                        {{ $bItem->booking_code }}
                      </span>
                    </div>
                    <h2 class="font-headline-md" style="margin-top: 2px;">Menunggu Konfirmasi</h2>
                  </div>
                </div>

                <span class="slot-badge slot-badge-orange" style="font-size: 13px; padding: 6px 14px;">
                  <span class="dot"></span>
                  <span>Dalam Proses Antrean</span>
                </span>
              </div>

              <!-- Rincian Jadwal -->
              <div class="status-grid-details">
                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span>
                    Hari & Tanggal
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">
                    {{ $bItem->schedule->day_name }}, {{ \Carbon\Carbon::parse($bItem->schedule->schedule_date)->format('d M Y') }}
                  </p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                    Waktu / Jam
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->time_slot }}</p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">meeting_room</span>
                    Ruangan / Lab
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->location }}</p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                    Mentor
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->mentor_names }}</p>
                </div>
              </div>

              <!-- Petunjuk Antrean -->
              <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border); margin-top: 8px;">
                <p class="font-body-sm" style="color: var(--on-surface-variant);">
                  Pendaftaran kamu sedang dalam proses validasi oleh tim lab ISCOM. Silakan cek berkala status kamu di halaman ini.
                </p>
                <button type="button" class="btn btn-outline btn-sm btn-recheck">
                  <span class="material-symbols-outlined icon-spin" style="font-size: 16px;">refresh</span>
                  <span>Cek Lagi</span>
                </button>
              </div>
            </section>

          @elseif($bItem->status === 'rejected')
            <!-- 3. STATUS: TIDAK DITERIMA -->
            <section class="status-card-rejected" aria-label="Status Booking Tidak Diterima">
              <div class="status-header">
                <div style="display: flex; align-items: center; gap: 16px;">
                  <div style="width: 44px; height: 44px; border-radius: var(--radius-full); background-color: rgba(239, 68, 68, 0.1); color: #dc2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <span class="material-symbols-outlined" style="font-size: 28px;">cancel</span>
                  </div>
                  <div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                      <span class="font-label-sm" style="color: #dc2626; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;">
                        {{ $bItem->schedule->mentoringSession->title ?? 'Sesi Mentoring' }}
                      </span>
                      <span style="font-size: 11px; padding: 2px 8px; border-radius: var(--radius-full); background: var(--surface-container); color: var(--on-surface-variant); font-family: monospace;">
                        {{ $bItem->booking_code }}
                      </span>
                    </div>
                    <h2 class="font-headline-md" style="margin-top: 2px;">Pendaftaran Tidak Diterima</h2>
                  </div>
                </div>

                <span class="slot-badge slot-badge-red" style="font-size: 13px; padding: 6px 14px;">
                  <span class="dot"></span>
                  <span>Tidak Diterima</span>
                </span>
              </div>

              <!-- Rincian Jadwal -->
              <div class="status-grid-details">
                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span>
                    Hari & Tanggal
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">
                    {{ $bItem->schedule->day_name }}, {{ \Carbon\Carbon::parse($bItem->schedule->schedule_date)->format('d M Y') }}
                  </p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                    Waktu / Jam
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->time_slot }}</p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">meeting_room</span>
                    Ruangan / Lab
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->location }}</p>
                </div>

                <div>
                  <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                    <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                    Mentor
                  </span>
                  <p class="font-label-lg" style="margin-top: 4px;">{{ $bItem->schedule->mentor_names }}</p>
                </div>
              </div>

              <!-- Keterangan & Tindakan -->
              <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; padding-top: 16px; border-top: 1px solid var(--border); margin-top: 8px;">
                <p class="font-body-sm" style="color: var(--on-surface-variant);">
                  Mohon maaf, kamu belum diterima pada jadwal sesi ini (kuota penuh atau belum memenuhi kriteria sesi).
                  @if($bItem->admin_notes)
                    <br/><strong style="color: var(--on-surface);">Catatan Pengurus:</strong> {{ $bItem->admin_notes }}
                  @endif
                </p>
                <a href="{{ route('sesi') }}" class="btn btn-outline btn-sm">
                  <span>Cari Sesi Lain</span>
                  <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
                </a>
              </div>
            </section>
          @endif
        @empty
          <!-- BELUM ADA SESI YANG DIIKUTI -->
          <div class="card" style="padding: 40px; text-align: center;">
            <div style="width: 56px; height: 56px; border-radius: var(--radius-full); background: var(--surface-container); color: var(--on-surface-variant); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
              <span class="material-symbols-outlined" style="font-size: 32px;">event_busy</span>
            </div>
            <h2 class="font-headline-md" style="margin-bottom: 8px;">Belum Ada Sesi yang Diikuti</h2>
            <p class="font-body-md" style="color: var(--on-surface-variant); max-width: 480px; margin: 0 auto 24px;">
              Kamu belum mendaftar di sesi mentoring manapun minggu ini. Buka halaman Sesi untuk memilih topik dan jadwal mentoring yang kamu butuhkan.
            </p>
            <a href="{{ route('sesi') }}" class="btn btn-primary">
              <span class="material-symbols-outlined" style="font-size: 18px;">calendar_month</span>
              <span>Pilih Sesi Mentoring</span>
            </a>
          </div>
        @endforelse
      </div>
    @endguest
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-recheck').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var icon = btn.querySelector('.icon-spin');
        if (icon) {
          icon.style.transition = 'transform 0.5s ease';
          icon.style.transform = 'rotate(360deg)';
        }
        setTimeout(function() {
          window.location.reload();
        }, 350);
      });
    });
  });
</script>
@endpush
