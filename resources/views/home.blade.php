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
      <a href="#sesi-terdekat" class="btn btn-hero-secondary">
        <span>Lihat Sesi</span>
        <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
      </a>
    </div>
  </div>

  <!-- Scroll Down Indicator Cue -->
  <a href="#sesi-terdekat" class="hero-scroll-cue" aria-label="Gulir ke sesi mentoring di bawah">
    <span class="scroll-label">Lihat Sesi</span>
    <span class="material-symbols-outlined">expand_more</span>
  </a>
</section>

<!-- Section: Sesi yang tersedia (Di bawah satu layar penuh, scroll ke bawah) -->
<div class="container">
  <section id="sesi-terdekat" class="jadwal-section-home">
    <div class="section-header">
      <div>
        <h2 class="section-title">Sesi yang tersedia</h2>
        <p class="section-subtitle">Pilih topik sesi bimbingan aktif ISCOM untuk melihat berbagai jadwal tatap muka</p>
      </div>
      <a href="{{ route('sesi') }}" class="section-link">
        <span>Lihat semua sesi</span>
        <span>→</span>
      </a>
    </div>

    <!-- Mentoring Sessions Grid -->
    <div class="schedule-grid">
      @forelse($activeSessions as $session)
        <div class="schedule-card">
          <div>
            <div class="schedule-card-header">
              <div>
                <p class="schedule-badge-type">{{ $session->badge_label }}</p>
                <h3 class="schedule-date-title" style="font-size: 20px;">
                  {{ $session->title }}
                </h3>
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

            <p class="font-body-sm" style="color: var(--on-surface-variant); margin-bottom: 16px; line-height: 1.5;">
              {{ $session->description }}
            </p>

            <div class="schedule-meta-list">
              <div class="schedule-meta-item">
                <span class="material-symbols-outlined">event_available</span>
                <span class="value-highlight">{{ $session->schedules->count() }} Pilihan Jadwal Tersedia</span>
              </div>
              <div class="schedule-meta-item">
                <span class="material-symbols-outlined">person</span>
                <span>{{ $session->mentors_summary ?: 'Mentor ISCOM' }}</span>
              </div>
            </div>
          </div>

          <div style="margin-top: 20px;">
            <form action="{{ route('sesi.pilih') }}" method="POST">
              @csrf
              <input type="hidden" name="session_id" value="{{ $session->slug ?? $session->id }}">
              <button type="submit" class="btn btn-outline btn-full" style="background-color: var(--surface-low); color: var(--primary); font-weight: 600;">
                <span>Buka Sesi &amp; Pilih Jadwal</span>
                <span class="material-symbols-outlined" style="font-size: 16px;">arrow_forward</span>
              </button>
            </form>
          </div>
        </div>
      @empty
        <div class="card" style="grid-column: 1 / -1; text-align: center; padding: 48px 24px;">
          <p class="font-body-md" style="color: var(--on-surface-variant);">Belum ada sesi mentoring aktif saat ini.</p>
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
