@extends('layouts.app')

@section('content')
<div class="container" style="padding-top: 32px; padding-bottom: 64px;">
  <!-- Page Header -->
  <div style="margin-bottom: 32px;">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
      <a href="{{ route('home') }}" class="font-label-md" style="color: var(--on-surface-variant);">Beranda</a>
      <span style="color: var(--outline-variant);">/</span>
      <span class="font-label-md" style="color: var(--primary);">Sesi Mentoring</span>
    </div>
    <h1 class="font-headline-xl">Sesi Mentoring Mahasiswa</h1>
    <p class="font-body-md" style="color: var(--on-surface-variant); max-width: 680px; margin-top: 6px;">
      Pilih topik sesi bimbingan yang ingin kamu ikuti. Setiap sesi menyediakan beberapa pilihan jadwal tatap muka di lab. Kamu dapat mengikuti lebih dari 1 sesi asalkan waktu jadwal tidak bertabrakan.
    </p>
  </div>

  <!-- Sessions Grid -->
  <div class="schedule-grid">
    @forelse($sessions as $session)
      <div class="schedule-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
          <div class="schedule-card-header">
            <div>
              <p class="schedule-badge-type">{{ $session->badge_label }}</p>
              <h2 class="schedule-date-title" style="font-size: 20px;">
                {{ $session->title }}
              </h2>
            </div>

            @if($session->total_remaining_slots > 5)
              <div class="slot-badge slot-badge-green">
                <span class="dot"></span>
                <span>Sisa {{ $session->total_remaining_slots }} slot</span>
              </div>
            @elseif($session->total_remaining_slots > 0)
              <div class="slot-badge slot-badge-orange">
                <span class="dot"></span>
                <span>Sisa {{ $session->total_remaining_slots }} slot</span>
              </div>
            @else
              <div class="slot-badge slot-badge-gray">
                <span class="dot"></span>
                <span>Penuh</span>
              </div>
            @endif
          </div>

          <p class="font-body-sm" style="color: var(--on-surface-variant); margin-bottom: 20px; line-height: 1.6;">
            {{ $session->description }}
          </p>

          <div class="schedule-meta-list">
            <div class="schedule-meta-item">
              <span class="material-symbols-outlined">event_available</span>
              <span class="value-highlight">{{ $session->schedules->count() }} Pilihan Jadwal di Sesi Ini</span>
            </div>
            <div class="schedule-meta-item">
              <span class="material-symbols-outlined">person</span>
              <span>Mentor: {{ $session->mentors_summary ?: 'Mentor ISCOM' }}</span>
            </div>
          </div>
        </div>

        <div style="margin-top: 24px;">
          <form action="{{ route('sesi.pilih') }}" method="POST">
            @csrf
            <input type="hidden" name="session_id" value="{{ $session->slug ?? $session->id }}">
            <button type="submit" class="btn btn-primary btn-full" style="background-color: var(--primary);">
              <span>Buka Sesi &amp; Lihat Jadwal</span>
              <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
            </button>
          </form>
        </div>
      </div>
    @empty
      <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 64px 24px;">
        <span class="material-symbols-outlined" style="font-size: 48px; color: var(--on-surface-variant); margin-bottom: 12px;">event_busy</span>
        <h3 class="font-headline-md" style="margin-bottom: 8px;">Belum Ada Sesi Mentoring Aktif</h3>
        <p class="font-body-md" style="color: var(--on-surface-variant);">Saat ini belum ada sesi mentoring yang dibuka oleh pengurus ISCOM.</p>
      </div>
    @endforelse
  </div>
</div>
@endsection
