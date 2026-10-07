@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <div class="card card-max-md">
    <div class="card-header">
      <h1 class="font-headline-lg">Daftar Akun Mahasiswa</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px;">
        Buat akun baru untuk mulai booking jadwal mentoring ISCOM.
      </p>
    </div>

    @if ($errors->any())
      <div class="alert alert-error">
        <span class="material-symbols-outlined" style="font-size: 20px;">error</span>
        <div>
          <ul style="padding-left: 16px; margin: 0;">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <form action="{{ route('register') }}" method="POST">
      @csrf

      <div class="form-grid-2">
        <!-- Nama Lengkap -->
        <div class="form-group col-span-2">
          <label class="form-label" for="name">Nama Lengkap</label>
          <input
            type="text"
            id="name"
            name="name"
            class="form-control"
            value="{{ old('name') }}"
            placeholder="Dimas Arya Pratama"
            required
            autofocus
          />
        </div>

        <!-- NIM -->
        <div class="form-group">
          <label class="form-label" for="nim">NIM</label>
          <input
            type="text"
            id="nim"
            name="nim"
            class="form-control"
            value="{{ old('nim') }}"
            placeholder="22081010045"
            required
          />
        </div>

        <!-- Email -->
        <div class="form-group">
          <label class="form-label" for="email">Email Mahasiswa</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="{{ old('email') }}"
            placeholder="nama@student.ac.id"
            required
          />
        </div>

        <!-- Program Studi -->
        <div class="form-group">
          <label class="form-label" for="major">Program Studi</label>
          <div class="form-select-wrapper">
            <select id="major" name="major" class="form-control" required>
              <option value="Sistem Informasi" {{ old('major') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
              <option value="Informatika" {{ old('major') == 'Informatika' ? 'selected' : '' }}>Informatika</option>
              <option value="Teknologi Informasi" {{ old('major') == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
              <option value="Sains Data" {{ old('major') == 'Sains Data' ? 'selected' : '' }}>Sains Data</option>
            </select>
            <span class="material-symbols-outlined form-select-icon">expand_more</span>
          </div>
        </div>

        <!-- Semester -->
        <div class="form-group">
          <label class="form-label" for="semester">Semester</label>
          <div class="form-select-wrapper">
            <select id="semester" name="semester" class="form-control" required>
              @for($i = 1; $i <= 8; $i++)
                <option value="{{ $i }}" {{ old('semester', 3) == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
              @endfor
            </select>
            <span class="material-symbols-outlined form-select-icon">expand_more</span>
          </div>
        </div>

        <!-- WhatsApp -->
        <div class="form-group col-span-2">
          <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
          <input
            type="tel"
            id="whatsapp"
            name="whatsapp"
            class="form-control"
            value="{{ old('whatsapp') }}"
            placeholder="0812-3456-7890"
            required
          />
        </div>

        <!-- Password -->
        <div class="form-group">
          <label class="form-label" for="password">Kata Sandi</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-control"
            placeholder="Minimal 6 karakter"
            required
          />
        </div>

        <!-- Konfirmasi Password -->
        <div class="form-group">
          <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-control"
            placeholder="Ulangi kata sandi"
            required
          />
        </div>
      </div>

      <div style="margin-top: 24px;">
        <button type="submit" class="btn btn-primary btn-full">
          <span>Daftar Sekarang</span>
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
        </button>
      </div>
    </form>

    <div style="text-align: center; margin-top: 20px; font-size: 13px; color: var(--on-surface-variant);">
      Sudah memiliki akun?
      <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Masuk di sini</a>
    </div>
  </div>
</div>
@endsection
