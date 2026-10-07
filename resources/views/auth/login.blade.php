@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <div class="card card-max-md" style="max-width: 440px;">
    <div class="card-header text-center">
      <img src="{{ asset('images/iscom-logo.png') }}" alt="ISCOM" style="height: 44px; width: auto; object-fit: contain; margin: 0 auto 12px auto; display: block;">
      <h1 class="font-headline-lg">Masuk ke ISCOM</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px;">
        Gunakan akun mahasiswa atau admin Anda.
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

    <form action="{{ route('login') }}" method="POST">
      @csrf

      <div style="display: flex; flex-direction: column; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="email">Alamat Email</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="{{ old('email') }}"
            placeholder="nama@student.ac.id"
            required
            autofocus
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Kata Sandi</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            placeholder="••••••••"
            required
          />
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; font-size: 13px;">
          <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; color: var(--on-surface-variant);">
            <input type="checkbox" name="remember" style="accent-color: var(--primary);"/>
            <span>Ingat Saya</span>
          </label>
        </div>

        <button type="submit" class="btn btn-primary btn-full" style="margin-top: 8px;">
          <span>Masuk</span>
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
        </button>
      </div>
    </form>

    <!-- Demo Account Helper Box -->
    <div style="margin-top: 24px; padding: 14px; background-color: var(--surface-low); border-radius: var(--radius-md); font-size: 12px; color: var(--on-surface-variant); line-height: 1.6;">
      <p style="font-weight: 600; color: var(--on-surface); margin-bottom: 4px;">Akun Demo Siap Pakai:</p>
      <div>👑 <strong>Admin:</strong> <code>admin@iscom.org</code> / sandi: <code>password123</code></div>
      <div>🎓 <strong>Mahasiswa:</strong> <code>dimas@student.ac.id</code> / sandi: <code>password123</code></div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--on-surface-variant);">
      Belum punya akun?
      <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Daftar di sini</a>
    </div>
  </div>
</div>
@endsection
