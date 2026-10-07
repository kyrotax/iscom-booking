<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ $title ?? 'ISCOM Mentoring' }}</title>
  
  <!-- Preload Suisse International Fonts -->
  <link rel="preload" href="{{ asset('fonts/SuisseIntl-Regular.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous"/>
  <link rel="preload" href="{{ asset('fonts/SuisseIntl-Bold.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous"/>
  <link rel="preload" href="{{ asset('fonts/SuisseIntl-Black.woff2') }}" as="font" type="font/woff2" crossorigin="anonymous"/>

  <!-- Fallback Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin=""/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  
  <!-- Material Symbols Outlined -->
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>

  <!-- Pure Vanilla CSS Design System -->
  <link rel="stylesheet" href="{{ asset('css/iscom.css') }}"/>
  @stack('styles')
</head>
<body>
  <!-- Fixed Top Header -->
  <header class="site-header">
    <div class="container nav-wrapper">
      <div style="display: flex; align-items: center; gap: 32px;">
        <!-- Brand Logo -->
        <a href="{{ route('home') }}" class="brand-logo">
          <div class="brand-dots">
            <span class="dot-blue"></span>
            <span class="dot-orange"></span>
            <span class="dot-green"></span>
          </div>
          <span class="brand-text-blue">ISCOM</span>
          <span class="brand-text-dark">Mentoring</span>
        </a>

        <!-- Desktop Navigation Links -->
        <nav class="nav-links">
          <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
          <a href="{{ route('jadwal') }}" class="nav-link {{ request()->routeIs('jadwal') ? 'active' : '' }}">Jadwal</a>
          <a href="{{ route('booking.step1') }}" class="nav-link {{ request()->is('booking*') ? 'active' : '' }}">Booking</a>
          <a href="{{ route('status') }}" class="nav-link {{ request()->routeIs('status') ? 'active' : '' }}">Status</a>
          @if(Auth::check() && Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color: var(--primary); font-weight: 700;">
              <span class="admin-badge">Admin Panel</span>
            </a>
          @endif
        </nav>
      </div>

      <!-- User Profile / Auth Area -->
      <div class="nav-actions">
        @auth
          <div class="user-profile-badge">
            <span class="hide-on-mobile-table">{{ Auth::user()->name }}</span>
            <div class="user-avatar" title="{{ Auth::user()->name }}">
              <span class="material-symbols-outlined" style="font-size: 18px;">person</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display: inline; margin-left: 4px;">
              @csrf
              <button type="submit" class="btn btn-ghost btn-sm" title="Keluar">
                <span class="material-symbols-outlined" style="font-size: 18px;">logout</span>
              </button>
            </form>
          </div>
        @else
          <a href="{{ route('login') }}" class="btn btn-outline btn-sm">Masuk</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
        @endauth

        <!-- Mobile Menu Hamburger Button -->
        <button id="mobileMenuBtn" class="mobile-menu-btn" type="button" aria-label="Buka Menu">
          <span class="material-symbols-outlined">menu</span>
        </button>
      </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div id="mobileNav" class="mobile-nav">
      <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a>
      <a href="{{ route('jadwal') }}" class="nav-link {{ request()->routeIs('jadwal') ? 'active' : '' }}">Jadwal</a>
      <a href="{{ route('booking.step1') }}" class="nav-link {{ request()->is('booking*') ? 'active' : '' }}">Booking Mentoring</a>
      <a href="{{ route('status') }}" class="nav-link {{ request()->routeIs('status') ? 'active' : '' }}">Status Booking</a>
      @if(Auth::check() && Auth::user()->isAdmin())
        <a href="{{ route('admin.dashboard') }}" class="nav-link" style="color: var(--primary); font-weight: 700;">Admin Dashboard</a>
      @endif
      @guest
        <div style="display: flex; gap: 8px; padding-top: 8px; border-top: 1px solid var(--border);">
          <a href="{{ route('login') }}" class="btn btn-outline btn-sm btn-full">Masuk</a>
          <a href="{{ route('register') }}" class="btn btn-primary btn-sm btn-full">Daftar</a>
        </div>
      @endguest
    </div>
  </header>

  <!-- Main Content Area -->
  <main class="main-content">
    <!-- Flash Alert Notifications -->
    @if(session('success') || session('error') || session('info'))
      <div class="container" style="padding-top: 16px;">
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

        @if(session('info'))
          <div class="alert alert-info">
            <span class="material-symbols-outlined" style="font-size: 20px;">info</span>
            <div>{{ session('info') }}</div>
          </div>
        @endif
      </div>
    @endif

    @yield('content')
  </main>

  <!-- Site Footer -->
  <footer class="site-footer">
    <div class="container">
      <p class="footer-text">© 2025 ISCOM - Information System Community. Mentoring Booking Mahasiswa.</p>
    </div>
  </footer>

  <!-- Interactivity Scripts -->
  <script src="{{ asset('js/iscom.js') }}"></script>
  @stack('scripts')
</body>
</html>
