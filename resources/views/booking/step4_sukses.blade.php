@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <!-- Progress Bar (Step 4 Complete) -->
  <div class="wizard-progress">
    <div class="wizard-track-container">
      <div class="wizard-track-bg"></div>
      <div class="wizard-track-active" style="width: 100%;"></div>

      <!-- Step 1: Done -->
      <div class="wizard-step">
        <div class="step-circle done">
          <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
        </div>
        <span class="step-title done">Data Diri</span>
      </div>

      <!-- Step 2: Done -->
      <div class="wizard-step">
        <div class="step-circle done">
          <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
        </div>
        <span class="step-title done">Pilih Jadwal</span>
      </div>

      <!-- Step 3: Done -->
      <div class="wizard-step">
        <div class="step-circle done">
          <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
        </div>
        <span class="step-title done">Konfirmasi</span>
      </div>

      <!-- Step 4: Active -->
      <div class="wizard-step">
        <div class="step-circle active">4</div>
        <span class="step-title active">Status</span>
      </div>
    </div>
  </div>

  <!-- Success Card -->
  <div class="card card-max-md success-card">
    <div class="success-icon-wrap">
      <span class="material-symbols-outlined" style="font-size: 32px;">check_circle</span>
    </div>

    <h1 class="font-headline-lg">Booking berhasil.</h1>
    <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px; max-width: 400px;">
      Bookingmu sudah tercatat. Cek kembali statusmu untuk melihat hasilnya.
    </p>

    <!-- Status Badge -->
    <div style="margin-top: 16px;">
      <span class="slot-badge slot-badge-orange" style="font-size: 12px; padding: 6px 14px;">
        <span class="dot"></span>
        <span>Menunggu konfirmasi</span>
      </span>
    </div>

    <!-- Ticket Summary Box -->
    <div class="ticket-badge-box">
      <div class="ticket-header">
        <span style="color: var(--on-surface-variant); font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Ringkasan Sesi</span>
        <span class="ticket-code">{{ $booking->booking_code }}</span>
      </div>

      <div class="ticket-details">
        <span style="font-weight: 600;">{{ $booking->full_name }}</span>
        <span style="color: var(--outline);">•</span>
        <span>{{ $booking->schedule->day_name }}, {{ \Carbon\Carbon::parse($booking->schedule->schedule_date)->format('d M Y') }}, {{ explode(' ', $booking->schedule->time_slot)[0] ?? '' }} WIB</span>
        <span style="color: var(--outline);">•</span>
        <span>{{ $booking->schedule->mentor_names }}</span>
        <span style="color: var(--outline);">•</span>
        <span>{{ $booking->schedule->location }}</span>
      </div>
    </div>

    <!-- Actions -->
    <div style="display: flex; gap: 12px; justify-content: flex-end; width: 100%;">
      <button type="button" class="btn btn-outline" onclick="window.print()">
        <span class="material-symbols-outlined" style="font-size: 18px;">print</span>
        <span>Cetak Tiket</span>
      </button>

      <a href="{{ route('status') }}" class="btn btn-blue">
        <span>Cek Status</span>
        <span class="material-symbols-outlined" style="font-size: 18px;">chevron_right</span>
      </a>
    </div>
  </div>
</div>
@endsection
