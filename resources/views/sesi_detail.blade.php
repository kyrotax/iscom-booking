@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 32px; padding-bottom: 64px;">
  <!-- Breadcrumb -->
  <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
    <a href="{{ route('home') }}" class="font-label-md" style="color: var(--on-surface-variant);">Beranda</a>
    <span style="color: var(--outline-variant);">/</span>
    <a href="{{ route('sesi') }}" class="font-label-md" style="color: var(--on-surface-variant);">Sesi Mentoring</a>
    <span style="color: var(--outline-variant);">/</span>
    <span class="font-label-md" style="color: var(--primary);">{{ $mentoringSession->title }}</span>
  </div>

  <!-- Session Overview Hero Card -->
  <div class="card" style="padding: 32px; margin-bottom: 32px; background: linear-gradient(135deg, var(--surface-white) 0%, var(--surface-low) 100%); border-left: 4px solid var(--primary);">
    <div style="display: flex; flex-wrap: wrap; align-items: flex-start; justify-content: space-between; gap: 16px;">
      <div style="max-width: 760px;">
        <span class="schedule-badge-type" style="color: var(--primary);">{{ $mentoringSession->badge_label }}</span>
        <h1 class="font-headline-lg" style="margin-top: 4px; margin-bottom: 10px;">{{ $mentoringSession->title }}</h1>
        <p class="font-body-md" style="color: var(--on-surface-variant); line-height: 1.6;">
          {{ $mentoringSession->description }}
        </p>
      </div>

      <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
        @if($mentoringSession->total_remaining_slots > 0)
          <div class="slot-badge slot-badge-green" style="font-size: 13px; padding: 6px 14px;">
            <span class="dot"></span>
            <span>Total {{ $mentoringSession->total_remaining_slots }} Slot Tersedia</span>
          </div>
        @else
          <div class="slot-badge slot-badge-gray" style="font-size: 13px; padding: 6px 14px;">
            <span class="dot"></span>
            <span>Semua Kuota Penuh</span>
          </div>
        @endif
        <span class="font-label-sm" style="color: var(--on-surface-variant);">{{ $mentoringSession->schedules->count() }} Pilihan Jadwal</span>
      </div>
    </div>

    <!-- Booking Rules Context Box -->
    <div style="margin-top: 24px; padding: 14px 18px; border-radius: var(--radius-md); background-color: rgba(22, 116, 184, 0.08); border: 1px solid rgba(22, 116, 184, 0.2); display: flex; align-items: center; gap: 12px;">
      <span class="material-symbols-outlined" style="color: var(--primary); font-size: 22px;">info</span>
      <p class="font-body-sm" style="color: var(--on-surface); margin: 0;">
        <strong>Aturan Booking:</strong> Kamu hanya diperbolehkan memilih <strong>1 jadwal</strong> pada sesi ini. Kamu tetap bisa mendaftar sesi mentoring lain asalkan hari dan jamnya <strong>tidak bersamaan</strong>.
      </p>
    </div>

    @if($isSessionBookedByUser)
      <div style="margin-top: 12px; padding: 14px 18px; border-radius: var(--radius-md); background-color: var(--warning-bg); border: 1px solid var(--warning-border); display: flex; align-items: center; gap: 12px;">
        <span class="material-symbols-outlined" style="color: var(--warning-text); font-size: 22px;">check_circle</span>
        <p class="font-body-sm" style="color: var(--warning-text); margin: 0;">
          Kamu sudah terdaftar di sesi <strong>{{ $mentoringSession->title }}</strong>. Jadwal lain di sesi ini dinonaktifkan sesuai aturan 1 jadwal per sesi.
        </p>
      </div>
    @endif
  </div>

  <!-- Section Header -->
  <div class="section-header" style="margin-bottom: 24px;">
    <div>
      <h2 class="section-title">Pilihan Jadwal di Sesi Ini</h2>
      <p class="section-subtitle">Pilih salah satu jadwal sesi tatap muka yang paling cocok dengan waktu luangmu</p>
    </div>
    <span class="font-label-md" style="color: var(--on-surface-variant);">
      {{ $mentoringSession->schedules->count() }} Jadwal Tersedia
    </span>
  </div>

  <!-- Schedules Grid -->
  <div class="schedule-grid">
    @forelse($mentoringSession->schedules as $sched)
      @php
        $isMyChosenSchedule = in_array($sched->id, $userBookedScheduleIds);
        $hasConflict = isset($conflictMap[$sched->id]);
        $conflictItem = $hasConflict ? $conflictMap[$sched->id] : null;
      @endphp

      <div class="schedule-card" style="{{ $isMyChosenSchedule ? 'border: 2px solid var(--tertiary); background-color: var(--tertiary-bg);' : ($hasConflict ? 'border-color: #FDBA74;' : '') }}">
        <div>
          <div class="schedule-card-header">
            <div>
              <p class="schedule-badge-type">Opsi Jadwal {{ $loop->iteration }}</p>
              <h3 class="schedule-date-title">
                {{ $sched->day_name }}, {{ \Carbon\Carbon::parse($sched->schedule_date)->format('d M Y') }}
              </h3>
            </div>

            @if($isMyChosenSchedule)
              <div class="slot-badge slot-badge-green" style="background-color: var(--tertiary); color: #fff;">
                <span class="material-symbols-outlined" style="font-size: 14px;">check</span>
                <span>Pilihan Kamu</span>
              </div>
            @elseif($isSessionBookedByUser)
              <div class="slot-badge slot-badge-gray">
                <span>Terkunci</span>
              </div>
            @elseif($hasConflict)
              <div class="slot-badge slot-badge-orange" style="background-color: #FFEDD5; color: #9A3412;">
                <span class="material-symbols-outlined" style="font-size: 14px;">warning</span>
                <span>Bentrok Waktu</span>
              </div>
            @elseif($sched->remaining_slots > 3)
              <div class="slot-badge slot-badge-green">
                <span class="dot"></span>
                <span>Sisa {{ $sched->remaining_slots }} slot</span>
              </div>
            @elseif($sched->remaining_slots > 0)
              <div class="slot-badge slot-badge-orange">
                <span class="dot"></span>
                <span>Sisa {{ $sched->remaining_slots }} slot</span>
              </div>
            @else
              <div class="slot-badge slot-badge-gray">
                <span class="dot"></span>
                <span>Penuh</span>
              </div>
            @endif
          </div>

          <div class="schedule-meta-list">
            <div class="schedule-meta-item">
              <span class="material-symbols-outlined">schedule</span>
              <span class="value-highlight">{{ $sched->time_slot }}</span>
            </div>
            <div class="schedule-meta-item">
              <span class="material-symbols-outlined">person</span>
              <span class="value-highlight">{{ $sched->mentor_names }}</span>
            </div>
            <div class="schedule-meta-item">
              <span class="material-symbols-outlined">location_on</span>
              <span>{{ $sched->location }}</span>
            </div>
            <div class="schedule-meta-item">
              <span class="material-symbols-outlined">school</span>
              <span>{{ $sched->topic }}</span>
            </div>
          </div>

          <!-- Conflict Warning Message -->
          @if($hasConflict)
            <div style="margin-top: 14px; padding: 10px 12px; border-radius: var(--radius-sm); background-color: #FFF7ED; border: 1px dashed #F97316; font-size: 12px; color: #9A3412; line-height: 1.4;">
              <strong>⚠️ Waktu Bentrok:</strong> Jadwal ini bersamaan dengan <em>{{ $conflictItem->mentoringSession->title ?? 'Sesi Lain' }}</em> pada tanggal dan jam yang sama.
            </div>
          @endif
        </div>

        <div style="margin-top: 20px;">
          @if($isMyChosenSchedule)
            <a href="{{ route('status') }}" class="btn btn-primary btn-full" style="background-color: var(--tertiary);">
              Lihat Status Tiket
            </a>
          @elseif($isSessionBookedByUser)
            <button class="btn btn-ghost btn-full" disabled style="opacity: 0.5; cursor: not-allowed; border: 1px solid var(--border);">
              Maks 1 Jadwal Per Sesi
            </button>
          @elseif($hasConflict)
            <button class="btn btn-ghost btn-full" disabled style="opacity: 0.6; cursor: not-allowed; color: #9A3412; border: 1px solid #FDBA74; background: #FFF7ED;">
              Bentrok dengan Sesi Lain
            </button>
          @elseif($sched->remaining_slots > 0)
            <form action="{{ route('booking.mulai') }}" method="POST">
              @csrf
              <input type="hidden" name="schedule_id" value="{{ $sched->id }}">
              <button type="submit" class="btn btn-primary btn-full">
                Pilih Jadwal Ini
              </button>
            </form>
          @else
            <button class="btn btn-ghost btn-full" disabled style="opacity: 0.6; cursor: not-allowed;">
              Kuota Penuh
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px 24px;">
        <p class="font-body-md" style="color: var(--on-surface-variant);">Belum ada jadwal yang dibuka untuk sesi ini.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
