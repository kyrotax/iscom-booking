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
      <h1 class="font-headline-lg">Pilih Jadwal Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px;">
        Jadwal dikelompokkan berdasarkan Sesi Mentoring. Kamu dapat mendaftar lebih dari 1 sesi asalkan waktu tidak bertabrakan, namun hanya boleh memilih 1 jadwal pada setiap sesi.
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

      <!-- Sessions & Schedules Grouped List -->
      @forelse($sessions as $session)
        @php
          $isThisSessionBooked = in_array($session->id, $userBookedSessionIds ?? []);
        @endphp

        <div style="margin-bottom: 32px; background: var(--surface-white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; box-shadow: var(--shadow-sm);">
          <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
            <div>
              <span class="schedule-badge-type" style="color: var(--primary);">{{ $session->badge_label }}</span>
              <h2 class="font-headline-md" style="margin-top: 2px;">{{ $session->title }}</h2>
              <p class="font-body-sm" style="color: var(--on-surface-variant); margin-top: 2px;">{{ $session->description }}</p>
            </div>

            @if($isThisSessionBooked)
              <div class="slot-badge slot-badge-orange" style="background-color: #FFF7ED; color: #C2410C;">
                <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
                <span>Kamu sudah terdaftar di sesi ini</span>
              </div>
            @endif
          </div>

          <div class="schedule-selection-list">
            @forelse($session->schedules as $sched)
              @php
                $isSelected = (old('schedule_id', $selectedScheduleId) == $sched->id);
                $isFull = ($sched->remaining_slots <= 0);
                $isAlreadyChosenByUser = in_array($sched->id, $userBookedScheduleIds ?? []);
                $hasConflict = isset($conflictMap[$sched->id]);
                $conflictItem = $hasConflict ? $conflictMap[$sched->id] : null;
                $isDisabled = $isFull || ($isThisSessionBooked && !$isAlreadyChosenByUser) || $hasConflict || $isAlreadyChosenByUser;
              @endphp

              <article
                class="schedule-select-item {{ $isSelected ? 'selected' : '' }}"
                data-schedule-id="{{ $sched->id }}"
                data-remaining="{{ $sched->remaining_slots }}"
                style="{{ $isDisabled && !$isSelected ? 'opacity: 0.65; cursor: not-allowed;' : 'cursor: pointer;' }}"
              >
                <div class="schedule-select-info">
                  <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <h3 class="font-headline-sm">
                      {{ $sched->day_name }}, {{ \Carbon\Carbon::parse($sched->schedule_date)->format('d F Y') }}
                    </h3>

                    @if($isAlreadyChosenByUser)
                      <span class="slot-badge slot-badge-green" style="background-color: var(--tertiary); color: #fff;">
                        ✓ Sudah Kamu Pilih
                      </span>
                    @elseif($isThisSessionBooked)
                      <span class="slot-badge slot-badge-gray">
                        Terkunci (1 Jadwal/Sesi)
                      </span>
                    @elseif($hasConflict)
                      <span class="slot-badge slot-badge-orange" style="background-color: #FFEDD5; color: #9A3412;">
                        ⚠️ Bentrok Waktu
                      </span>
                    @elseif($sched->remaining_slots > 3)
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

                  <div class="schedule-select-details font-body-sm" style="color: var(--on-surface-variant); margin-top: 8px;">
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

                  @if($hasConflict)
                    <div style="margin-top: 8px; font-size: 12px; color: #9A3412;">
                      * Bentrok dengan <strong>{{ $conflictItem->mentoringSession->title ?? 'Sesi Lain' }}</strong> pada waktu yang bersamaan.
                    </div>
                  @endif
                </div>

                <div style="flex-shrink: 0; min-width: 140px; text-align: right;">
                  @if($isAlreadyChosenByUser)
                    <span class="font-label-sm" style="color: var(--tertiary-dark); font-weight: 600;">Terdaftar</span>
                  @elseif($isThisSessionBooked)
                    <span class="font-label-sm" style="color: var(--on-surface-variant);">1 Jadwal/Sesi</span>
                  @elseif($hasConflict)
                    <span class="font-label-sm" style="color: #9A3412;">Bentrok Waktu</span>
                  @elseif($isFull)
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
              <p class="font-body-sm" style="color: var(--on-surface-variant);">Belum ada jadwal untuk sesi ini.</p>
            @endforelse
          </div>
        </div>
      @empty
        <div class="card text-center py-lg">
          <p class="font-body-md" style="color: var(--on-surface-variant);">Tidak ada sesi mentoring yang tersedia saat ini.</p>
        </div>
      @endforelse

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
