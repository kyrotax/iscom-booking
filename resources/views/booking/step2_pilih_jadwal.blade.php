@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <!-- Progress Bar (Step 2 Active) -->
  <div class="wizard-progress">
    <div class="wizard-track-container">
      <div class="wizard-track-bg"></div>
      <div class="wizard-track-active" style="width: 33%;"></div>

      <!-- Step 1: Done -->
      <div class="wizard-step">
        <div class="step-circle done">
          <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
        </div>
        <span class="step-title done">1. Data Diri</span>
      </div>

      <!-- Step 2: Active -->
      <div class="wizard-step">
        <div class="step-circle active">2</div>
        <span class="step-title active">2. Pilih Jadwal</span>
      </div>

      <!-- Step 3: Pending -->
      <div class="wizard-step">
        <div class="step-circle pending">3</div>
        <span class="step-title pending">3. Konfirmasi</span>
      </div>

      <!-- Step 4: Pending -->
      <div class="wizard-step">
        <div class="step-circle pending">4</div>
        <span class="step-title pending">4. Status</span>
      </div>
    </div>
  </div>

  <div class="card card-max-lg" style="background-color: transparent; border: none; box-shadow: none; padding: 0;">
    <div style="margin-bottom: 24px;">
      <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
        <span class="font-label-md" style="color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">Tahap Booking Mentoring</span>
        <span class="dot-orange" style="width: 6px; height: 6px; margin: 0;"></span>
      </div>
      <h1 class="font-headline-lg">Pilih Jadwal</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px;">
        Pilih waktu yang bisa kamu hadiri bersama mentor terkait.
      </p>
    </div>

    @if ($errors->any())
      <div class="alert alert-error">
        <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
        <div>
          @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
          @endforeach
        </div>
      </div>
    @endif

    <form id="scheduleForm" action="{{ route('booking.step2.post') }}" method="POST">
      @csrf
      <input type="hidden" name="schedule_id" id="selected_schedule_id" value="{{ old('schedule_id', $selectedScheduleId ?? '') }}" required/>

      <!-- Interactive Schedule Cards Selection -->
      <div class="schedule-selection-list">
        @forelse($schedules as $sched)
          @php
            $isSelected = (old('schedule_id', $selectedScheduleId) == $sched->id);
            $isFull = ($sched->remaining_slots <= 0);
          @endphp
          <article
            class="schedule-select-item {{ $isSelected ? 'selected' : '' }}"
            data-schedule-id="{{ $sched->id }}"
            data-remaining="{{ $sched->remaining_slots }}"
            style="{{ $isFull ? 'opacity: 0.6; cursor: not-allowed;' : '' }}"
          >
            <div class="schedule-select-info">
              <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h3 class="font-headline-sm">
                  {{ $sched->day_name }}, {{ \Carbon\Carbon::parse($sched->schedule_date)->format('d F Y') }}
                </h3>

                @if($sched->remaining_slots > 3)
                  <span class="slot-badge slot-badge-green">
                    <span class="dot"></span>
                    Sisa {{ $sched->remaining_slots }} slot
                  </span>
                @elseif($sched->remaining_slots > 0)
                  <span class="slot-badge slot-badge-orange">
                    <span class="dot"></span>
                    Sisa {{ $sched->remaining_slots }} slot
                  </span>
                @else
                  <span class="slot-badge slot-badge-gray">
                    <span class="dot"></span>
                    Penuh
                  </span>
                @endif
              </div>

              <div class="schedule-select-details font-body-sm" style="color: var(--on-surface-variant);">
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="material-symbols-outlined" style="font-size: 18px; color: var(--primary);">schedule</span>
                  <span style="color: var(--on-surface); font-weight: 500;">{{ $sched->time_slot }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="material-symbols-outlined" style="font-size: 18px; color: var(--outline);">group</span>
                  <span>{{ $sched->mentor_names }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="material-symbols-outlined" style="font-size: 18px; color: var(--outline);">location_on</span>
                  <span>{{ $sched->location }}</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                  <span class="material-symbols-outlined" style="font-size: 18px; color: var(--outline);">school</span>
                  <span>Topik: {{ $sched->topic }}</span>
                </div>
              </div>
            </div>

            <div style="flex-shrink: 0; min-width: 130px; text-align: right;">
              @if($isFull)
                <button type="button" class="btn btn-ghost btn-sm btn-full" disabled style="cursor: not-allowed;">
                  Penuh
                </button>
              @elseif($isSelected)
                <button type="button" class="btn btn-blue btn-sm btn-full action-select-btn">
                  <span class="material-symbols-outlined" style="font-size: 16px;">check</span>
                  <span>Terpilih</span>
                </button>
              @else
                <button type="button" class="btn btn-outline btn-sm btn-full action-select-btn">
                  <span>Pilih Jadwal</span>
                </button>
              @endif
            </div>
          </article>
        @empty
          <div class="card text-center py-lg">
            <p class="font-body-md" style="color: var(--on-surface-variant);">Tidak ada jadwal mentoring yang tersedia saat ini.</p>
          </div>
        @endforelse
      </div>

      <!-- Lab Guidelines Info Box -->
      <div class="info-note" style="margin-top: 20px;">
        <span class="material-symbols-outlined">info</span>
        <p class="font-body-sm">
          Satu sesi mentoring berdurasi 120 menit dengan kapasitas maksimal 10 peserta. Pastikan kamu membawa laptop dan modul materi kuliah yang ingin didiskusikan.
        </p>
      </div>

      <!-- Action Navigation Footer -->
      <div class="form-actions" style="margin-top: 24px; background: transparent;">
        <a href="{{ route('booking.step1') }}" class="btn btn-ghost">
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
          <span>Kembali</span>
        </a>

        <button type="submit" id="btnNextSchedule" class="btn btn-primary" {{ empty($selectedScheduleId) ? 'disabled' : '' }}>
          <span>Lanjutkan ke Konfirmasi</span>
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
