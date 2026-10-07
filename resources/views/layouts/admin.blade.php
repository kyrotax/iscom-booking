<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $title ?? 'Admin Panel' }} - ISCOM Mentoring</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

  <link rel="stylesheet" href="{{ asset('css/iscom.css') }}"/>
  @stack('styles')
</head>
<body style="background-color: #F8FAFC;">
  <!-- Admin Header -->
  <header class="site-header" style="background-color: #FFFFFF; border-bottom: 2px solid var(--primary-fixed);">
    <div class="container nav-wrapper">
      <div style="display: flex; align-items: center; gap: 24px;">
        <a href="{{ route('admin.dashboard') }}" class="brand-logo" aria-label="ISCOM Admin">
          <img src="{{ asset('images/iscom-logo.png') }}" alt="ISCOM Admin" class="brand-logo-img">
          <span class="admin-badge">ADMIN</span>
        </a>

        <nav class="nav-links">
          <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
          <a href="{{ route('admin.sessions.index') }}" class="nav-link {{ request()->is('admin/sessions*') ? 'active' : '' }}">Kelola Sesi</a>
          <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->is('admin/schedules*') ? 'active' : '' }}">Kelola Jadwal</a>
          <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->is('admin/bookings*') ? 'active' : '' }}">Data Peserta</a>
        </nav>
      </div>

      <div class="nav-actions">
        <a href="{{ route('home') }}" class="btn btn-outline btn-sm" target="_blank">
          <span class="material-symbols-outlined" style="font-size: 16px;">open_in_new</span>
          <span class="hide-on-mobile-table">Lihat Web</span>
        </a>

        <div class="user-profile-badge">
          <span class="hide-on-mobile-table">{{ Auth::user()->name }}</span>
          <div class="user-avatar" style="background-color: var(--secondary);">
            <span class="material-symbols-outlined" style="font-size: 18px;">admin_panel_settings</span>
          </div>
          <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 4px;">
            @csrf
            <button type="submit" class="btn btn-ghost btn-sm" title="Keluar">
              <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
            </button>
          </form>
        </div>

        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Menu Admin">
          <span class="material-symbols-outlined">menu</span>
        </button>
      </div>
    </div>

    <!-- Mobile Nav Drawer -->
    <div id="mobileNav" class="mobile-nav">
      <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>
      <a href="{{ route('admin.sessions.index') }}" class="nav-link {{ request()->is('admin/sessions*') ? 'active' : '' }}">Kelola Sesi</a>
      <a href="{{ route('admin.schedules.index') }}" class="nav-link {{ request()->is('admin/schedules*') ? 'active' : '' }}">Kelola Jadwal</a>
      <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->is('admin/bookings*') ? 'active' : '' }}">Data Peserta</a>
      <a href="{{ route('home') }}" class="nav-link">Lihat Website Mahasiswa</a>
    </div>
  </header>

  <main class="main-content">
    <div class="container" style="padding-top: 24px; padding-bottom: 48px;">
      @if(session('success'))
        <div class="alert alert-success">
          <span class="material-symbols-outlined" style="font-size: 20px;">check_circle</span>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-error">
          <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
          <div>{{ session('error') }}</div>
        </div>
      @endif

      @yield('content')
    </div>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p class="footer-text">© 2025 ISCOM Mentoring Management Portal.</p>
    </div>
  </footer>

  <script src="{{ asset('js/iscom.js') }}"></script>
  @stack('scripts')
</body>
</html>
