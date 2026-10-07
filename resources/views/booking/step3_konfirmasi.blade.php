@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <!-- Progress Bar (Step 3 Active) -->
  <div class="wizard-progress">
    <div class="wizard-track-container">
      <div class="wizard-track-bg"></div>
      <div class="wizard-track-active" style="width: 66%;"></div>

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

      <!-- Step 3: Active -->
      <div class="wizard-step">
        <div class="step-circle active">3</div>
        <span class="step-title active">Konfirmasi</span>
      </div>

      <!-- Step 4: Pending -->
      <div class="wizard-step">
        <div class="step-circle pending">4</div>
        <span class="step-title pending">Status</span>
      </div>
    </div>
  </div>

  <!-- Summary Card Container -->
  <div class="card card-max-md">
    <div class="card-header">
      <h1 class="font-headline-lg">Konfirmasi Booking</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px;">
        Pastikan data dan jadwalmu sudah benar.
      </p>
    </div>

    <!-- Section 1: Data Diri Mahasiswa -->
    <div class="summary-block">
      <h2 class="summary-heading">Data Diri Mahasiswa</h2>
      <div class="summary-box">
        <div>
          <span class="summary-item-label">Nama Lengkap</span>
          <span class="summary-item-val">{{ $studentData['full_name'] }}</span>
        </div>
        <div>
          <span class="summary-item-label">NIM</span>
          <span class="summary-item-val font-mono">{{ $studentData['nim'] }}</span>
        </div>
        <div>
          <span class="summary-item-label">Program Studi</span>
          <span class="summary-item-val">{{ $studentData['major'] }} (Semester {{ $studentData['semester'] }})</span>
        </div>
        <div>
          <span class="summary-item-label">Nomor WhatsApp</span>
          <span class="summary-item-val">{{ $studentData['whatsapp'] }}</span>
        </div>
        <div style="grid-column: 1 / -1;">
          <span class="summary-item-label">Email Mahasiswa</span>
          <span class="summary-item-val">{{ $studentData['email'] }}</span>
        </div>
      </div>
    </div>

    <!-- Section 2: Jadwal & Detail Sesi -->
    <div class="summary-block">
      <h2 class="summary-heading">Jadwal & Detail Sesi</h2>
      <div class="summary-box">
        <div style="display: flex; align-items: flex-start; gap: 8px;">
          <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px; margin-top: 2px;">calendar_today</span>
          <div>
            <span class="summary-item-label">Tanggal</span>
            <span class="summary-item-val">{{ $schedule->day_name }}, {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d F Y') }}</span>
          </div>
        </div>

        <div style="display: flex; align-items: flex-start; gap: 8px;">
          <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px; margin-top: 2px;">schedule</span>
          <div>
            <span class="summary-item-label">Waktu Sesi</span>
            <span class="summary-item-val">{{ $schedule->time_slot }}</span>
          </div>
        </div>

        <div style="display: flex; align-items: flex-start; gap: 8px;">
          <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px; margin-top: 2px;">school</span>
          <div>
            <span class="summary-item-label">Mentor Terpilih</span>
            <span class="summary-item-val">{{ $schedule->mentor_names }}</span>
          </div>
        </div>

        <div style="display: flex; align-items: flex-start; gap: 8px;">
          <span class="material-symbols-outlined" style="color: var(--primary); font-size: 20px; margin-top: 2px;">meeting_room</span>
          <div>
            <span class="summary-item-label">Lokasi Pertemuan</span>
            <span class="summary-item-val">{{ $schedule->location }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Alert Note -->
    <div class="info-note" style="margin-bottom: 24px;">
      <span class="material-symbols-outlined">info</span>
      <p class="font-body-sm">
        Pastikan data dan jadwalmu sudah benar. Verifikasi kehadiran akan dicek saat sesi mentoring dimulai di lab.
      </p>
    </div>

    <!-- Action Form -->
    <form action="{{ route('booking.confirm') }}" method="POST">
      @csrf
      <div class="form-actions">
        <a href="{{ route('booking.step2') }}" class="btn btn-ghost">
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
          <span>Kembali</span>
        </a>

        <button type="submit" class="btn btn-primary">
          <span>Konfirmasi Booking</span>
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
