@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 24px; padding-bottom: 64px;">
  <div style="margin-bottom: 32px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
      <span class="dot-blue" style="width: 10px; height: 10px;"></span>
      <span class="font-label-sm" style="color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em;">JADWAL RESMI ISCOM</span>
    </div>
    <h1 class="font-headline-xl">Jadwal Mentoring Mahasiswa</h1>
    <p class="font-body-md" style="color: var(--on-surface-variant); max-width: 600px; margin-top: 4px;">
      Lihat seluruh jadwal sesi pendampingan lab yang terbuka minggu ini. Pilih sesi yang cocok dan booking sebelum kuota habis.
    </p>
  </div>

  <div class="schedule-grid">
    @forelse($allSchedules as $sched)
      <div class="schedule-card">
        <div>
          <div class="schedule-card-header">
            <div>
              <p class="schedule-badge-type">Sesi Terbuka</p>
              <h3 class="schedule-date-title">
                {{ $sched->day_name }}, {{ \Carbon\Carbon::parse($sched->schedule_date)->format('d M Y') }}
              </h3>
            </div>

            @if($sched->remaining_slots > 3)
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
              <span>Topik: {{ $sched->topic }}</span>
            </div>
          </div>
        </div>

        <div style="margin-top: 16px;">
          @if($sched->remaining_slots > 0)
            <a href="{{ route('booking.step1') }}" class="btn btn-primary btn-full">
              Pilih Jadwal Ini
            </a>
          @else
            <button class="btn btn-ghost btn-full" disabled style="opacity: 0.6; cursor: not-allowed;">
              Kuota Penuh
            </button>
          @endif
        </div>
      </div>
    @empty
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px 24px;">
        <p class="font-body-md" style="color: var(--on-surface-variant);">Saat ini belum ada jadwal mentoring yang ditambahkan.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
