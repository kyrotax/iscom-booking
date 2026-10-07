@extends('layouts.app')

@section('content')
<!-- Hero Section: Full Screen PatternWaves Background (Edge-to-Edge) -->
<section class="hero-fullscreen">
  <!-- WebGL PatternWaves Mount Canvas -->
  <div id="heroPatternWaves" class="hero-waves-canvas"></div>

  <!-- Centered Content without Card (Colors & Typography alone provide high contrast) -->
  <div class="hero-center-content">
    <h1 class="hero-title-main">
      Belajar bareng,<br/>berkembang bareng.
    </h1>

    <p class="hero-subtitle-main">
      Temukan jadwal mentoring yang sesuai dan booking sesi mentoringmu bersama mentor terbaik ISCOM.
    </p>

    <div class="hero-actions-main">
      <a href="{{ route('booking.step1') }}" class="btn btn-hero-primary">
        Booking Mentoring
      </a>
      <a href="#jadwal-terdekat" class="btn btn-hero-secondary">
        <span>Lihat Jadwal</span>
        <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
      </a>
    </div>
  </div>

  <!-- Scroll Down Indicator Cue -->
  <a href="#jadwal-terdekat" class="hero-scroll-cue" aria-label="Gulir ke jadwal mentoring di bawah">
    <span class="scroll-label">Lihat Jadwal</span>
    <span class="material-symbols-outlined">expand_more</span>
  </a>
</section>

<!-- Section: Jadwal yang tersedia (Di bawah satu layar penuh, scroll ke bawah) -->
<div class="container">
  <section id="jadwal-terdekat" class="jadwal-section-home">
    <div class="section-header">
      <div>
        <h2 class="section-title">Jadwal yang tersedia</h2>
        <p class="section-subtitle">Sesi bimbingan aktif minggu ini bersama mentor ISCOM</p>
      </div>
      <a href="{{ route('jadwal') }}" class="section-link">
        <span>Lihat semua jadwal</span>
        <span>→</span>
      </a>
    </div>

    <!-- 3 Schedule Cards Grid -->
    <div class="schedule-grid">
      @forelse($upcomingSchedules as $sched)
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
              <a href="{{ route('booking.step1') }}" class="btn btn-outline btn-full" style="background-color: var(--surface-low); color: var(--primary);">
                Pilih Jadwal
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
          <p class="font-body-md" style="color: var(--on-surface-variant);">Belum ada jadwal mentoring aktif saat ini.</p>
        </div>
      @endforelse
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script type="module">
  import { initPatternWaves } from "{{ asset('js/pattern-waves.js') }}";

  const container = document.getElementById('heroPatternWaves');
  if (container) {
    initPatternWaves(container, {
      preset: "silk",
      color: "#ffffff",
      backgroundColor: "#F97316",
      fade: "center",
      interactive: true,
      cursorSize: 45,
      cursorStrength: 0.1,
      pattern: "square",
      wave: "ripple",
      markSize: 0.8,
      depth: 0.5,
      shine: 1.1,
      contrast: 1.5,
      speed: 0.45,
      scale: 1.35,
      direction: 92,
      fadeSize: 1
    });
  }
</script>
@endpush
