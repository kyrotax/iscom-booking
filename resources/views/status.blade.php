@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <div style="max-width: 1000px; margin: 0 auto; display: flex; flex-direction: column; gap: 32px;">
    <!-- Header Section -->
    <div>
      <div style="display: flex; align-items: center; gap: 8px; color: var(--primary); margin-bottom: 4px;">
        <span class="material-symbols-outlined" style="font-size: 20px;">assignment_turned_in</span>
        <span class="font-label-sm" style="letter-spacing: 0.1em; text-transform: uppercase;">PORTAL MENTORING ISCOM</span>
      </div>
      <h1 class="font-headline-xl">Status Booking Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); max-width: 600px; margin-top: 4px;">
        Cek apakah pendaftaran mentoringmu telah diterima di lab dan verifikasi jadwal sesi tatap muka kamu.
      </p>
    </div>

    <!-- Active User State Card -->
    <div>
      @if($userBooking)
        @if($userBooking->status === 'accepted')
          <!-- 1. ACCEPTED STATE CARD -->
          <section class="status-card-accepted" aria-label="Status Booking Diterima">
            <div class="status-header">
              <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-full); background-color: var(--surface-low); color: var(--tertiary); display: flex; align-items: center; justify-content: center;">
                  <span class="material-symbols-outlined" style="font-size: 26px;">check_circle</span>
                </div>
                <div>
                  <span class="font-label-sm" style="color: var(--tertiary); text-transform: uppercase; letter-spacing: 0.05em;">Terkonfirmasi • {{ $userBooking->booking_code }}</span>
                  <h2 class="font-headline-md">Booking kamu diterima.</h2>
                </div>
              </div>

              <span class="slot-badge slot-badge-green" style="font-size: 13px; padding: 6px 14px;">
                <span class="dot"></span>
                <span>Siap Masuk Lab</span>
              </span>
            </div>

            <!-- Details Grid -->
            <div class="status-grid-details">
              <div>
                <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                  <span class="material-symbols-outlined" style="font-size: 16px;">calendar_today</span>
                  Tanggal
                </span>
                <p class="font-label-lg" style="margin-top: 4px;">{{ $userBooking->schedule->day_name }}, {{ \Carbon\Carbon::parse($userBooking->schedule->schedule_date)->format('d M Y') }}</p>
              </div>

              <div>
                <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                  <span class="material-symbols-outlined" style="font-size: 16px;">schedule</span>
                  Jam
                </span>
                <p class="font-label-lg" style="margin-top: 4px;">{{ $userBooking->schedule->time_slot }}</p>
              </div>

              <div>
                <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                  <span class="material-symbols-outlined" style="font-size: 16px;">person</span>
                  Mentor
                </span>
                <p class="font-label-lg" style="margin-top: 4px;">{{ $userBooking->schedule->mentor_names }}</p>
              </div>

              <div>
                <span class="font-label-sm" style="color: var(--on-surface-variant); display: flex; align-items: center; gap: 4px;">
                  <span class="material-symbols-outlined" style="font-size: 16px;">meeting_room</span>
                  Lokasi
                </span>
                <p class="font-label-lg" style="margin-top: 4px;">{{ $userBooking->schedule->location }}</p>
              </div>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: gap-sm; padding-top: 16px; border-top: 1px solid var(--border); margin-top: 8px;">
              <p class="font-body-sm" style="color: var(--on-surface-variant);">
                Harap hadir 10 menit sebelum sesi dimulai dengan membawa KTM fisik atau digital.
              </p>
              <a href="#daftar-peserta" class="btn btn-blue btn-sm">
                <span>Lihat Daftar Mentoring</span>
                <span class="material-symbols-outlined" style="font-size: 16px;">arrow_downward</span>
              </a>
            </div>
          </section>

        @elseif($userBooking->status === 'pending')
          <!-- PENDING STATE CARD -->
          <section class="status-card-pending" aria-label="Status Booking Menunggu">
            <div class="status-header">
              <div style="display: flex; align-items: center; gap: 16px;">
                <div style="width: 40px; height: 40px; border-radius: var(--radius-full); background-color: var(--warning-bg); color: var(--secondary); display: flex; align-items: center; justify-content: center;">
                  <span class="material-symbols-outlined" style="font-size: 26px;">hourglass_top</span>
                </div>
                <div>
                  <span class="font-label-sm" style="color: var(--secondary); text-transform: uppercase; letter-spacing: 0.05em;">Sedang Diverifikasi • {{ $userBooking->booking_code }}</span>
                  <h2 class="font-headline-md">Menunggu Konfirmasi</h2>
                </div>
              </div>

              <span class="slot-badge slot-badge-orange" style="font-size: 13px; padding: 6px 14px;">
                <span class="dot"></span>
                <span>Dalam Proses Antrean</span>
              </span>
            </div>

            <p class="font-body-md" style="color: var(--on-surface-variant); padding: 16px 0;">
              Booking kamu sudah tercatat untuk sesi <strong>{{ $userBooking->schedule->day_name }} ({{ $userBooking->schedule->time_slot }})</strong> di <strong>{{ $userBooking->schedule->location }}</strong>. Pengurus lab sedang memvalidasi data mahasiswa. Cek kembali statusmu secara berkala.
            </p>

            <div style="text-align: right; border-top: 1px solid var(--border); padding-top: 12px;">
              <button type="button" id="btn-recheck" class="btn btn-outline btn-sm">
                <span class="material-symbols-outlined" id="refresh-icon" style="font-size: 16px;">refresh</span>
                <span>Cek Lagi</span>
              </button>
            </div>
          </section>

        @elseif($userBooking->status === 'rejected')
          <!-- REJECTED STATE CARD (Friendly, soft notification) -->
          <section class="status-card-rejected" aria-label="Status Booking Belum Masuk">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
              <div style="width: 36px; height: 36px; border-radius: var(--radius-full); background-color: var(--surface-high); color: var(--on-surface-variant); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <span class="material-symbols-outlined" style="font-size: 20px;">info</span>
              </div>
              <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                  <h3 class="font-headline-sm">Belum masuk list.</h3>
                  <span class="font-label-sm" style="background-color: var(--surface-container); padding: 2px 8px; border-radius: var(--radius-sm); color: var(--on-surface-variant);">Batch Terkini</span>
                </div>
                <p class="font-body-sm" style="color: var(--on-surface-variant); margin-top: 4px;">
                  Mohon maaf, kamu belum masuk list mentoring ISCOM di lab. Kuota sesi mungkin penuh atau pendaftaran belum memenuhi kriteria sesi lab saat ini.
                  @if($userBooking->admin_notes)
                    <br/><strong>Catatan Pengurus:</strong> {{ $userBooking->admin_notes }}
                  @endif
                </p>
              </div>
            </div>

            <button type="button" id="btn-recheck" class="btn btn-outline btn-sm" style="flex-shrink: 0;">
              <span class="material-symbols-outlined" id="refresh-icon" style="font-size: 16px;">refresh</span>
              <span>Cek Lagi</span>
            </button>
          </section>
        @endif
      @else
        <!-- NO ACTIVE BOOKING -->
        <div class="card" style="padding: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
          <div>
            <h3 class="font-headline-sm">Belum ada booking mentoring aktif</h3>
            <p class="font-body-sm" style="color: var(--on-surface-variant); margin-top: 2px;">
              Kamu belum mendaftar sesi mentoring ISCOM minggu ini. Silakan pilih jadwal yang tersedia.
            </p>
          </div>
          <a href="{{ route('booking.step1') }}" class="btn btn-primary btn-sm">
            Booking Mentoring Sekarang
          </a>
        </div>
      @endif
    </div>

    <!-- Bottom Section: DAFTAR MENTORING (Tabel Peserta Diterima) -->
    <div id="daftar-peserta" style="scroll-margin-top: 80px;">
      <div style="display: flex; align-items: flex-end; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
        <div>
          <h2 class="font-headline-lg">Daftar Mentoring</h2>
          <p class="font-body-md" style="color: var(--on-surface-variant);">
            Daftar peserta yang telah terkonfirmasi masuk jadwal lab.
          </p>
        </div>

        <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; color: var(--on-surface-variant);">
          <span class="dot-green" style="width: 8px; height: 8px;"></span>
          <span>Total {{ $acceptedBookings->count() }} Peserta Terjadwal</span>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="table-container">
        <div class="table-responsive">
          <table class="iscom-table">
            <thead>
              <tr>
                <th style="width: 50px;">No.</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Program Studi</th>
                <th>Jadwal</th>
                <th style="text-align: right;">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($acceptedBookings as $index => $item)
                @php
                  $isCurrentUser = (Auth::check() && $item->user_id == Auth::id());
                @endphp
                <tr class="{{ $isCurrentUser ? 'highlight-user' : '' }}">
                  <td style="color: var(--on-surface-variant); font-weight: 500;">
                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                  </td>
                  <td>
                    <div style="display: flex; align-items: center;">
                      <span style="font-weight: 600; color: var(--on-surface);">{{ $item->full_name }}</span>
                      @if($isCurrentUser)
                        <span class="badge-you">Kamu</span>
                      @endif
                    </div>
                  </td>
                  <td style="font-family: monospace; font-size: 13px; color: var(--on-surface-variant);">
                    {{ $item->nim }}
                  </td>
                  <td>{{ $item->major }}</td>
                  <td>
                    <span style="font-weight: 500;">{{ $item->schedule->day_name }}, {{ \Carbon\Carbon::parse($item->schedule->schedule_date)->format('d M') }}</span>
                    <span style="color: var(--on-surface-variant); font-size: 12px;">({{ explode(' - ', $item->schedule->time_slot)[0] ?? '' }})</span>
                  </td>
                  <td style="text-align: right;">
                    <span class="status-pill-accepted">
                      <span class="dot"></span>
                      <span>Diterima</span>
                    </span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align: center; padding: 32px; color: var(--on-surface-variant);">
                    Belum ada peserta yang diterima di daftar mentoring.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="table-footer-info">
          <span>Menampilkan {{ $acceptedBookings->count() }} peserta terdaftar</span>
          <span style="font-size: 11px;">Diperbarui baru saja</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
