@extends('layouts.app')

@section('content')
<div class="container py-lg">
  <!-- 4-Step Progress Indicator -->
  <div class="wizard-progress">
    <div class="wizard-track-container">
      <div class="wizard-track-bg"></div>
      <div class="wizard-track-active" style="width: 0%;"></div>

      <!-- Step 1: Active -->
      <div class="wizard-step">
        <div class="step-circle active">1</div>
        <span class="step-title active">Data Diri</span>
      </div>

      <!-- Step 2: Pending -->
      <div class="wizard-step">
        <div class="step-circle pending">2</div>
        <span class="step-title pending">Pilih Jadwal</span>
      </div>

      <!-- Step 3: Pending -->
      <div class="wizard-step">
        <div class="step-circle pending">3</div>
        <span class="step-title pending">Konfirmasi</span>
      </div>

      <!-- Step 4: Pending -->
      <div class="wizard-step">
        <div class="step-circle pending">4</div>
        <span class="step-title pending">Status</span>
      </div>
    </div>
  </div>

  <!-- Main Form Card Container -->
  <div class="card card-max-md">
    <div class="card-header">
      <h1 class="font-headline-lg">Data Diri</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 4px;">
        Isi data berikut untuk melanjutkan booking mentoring.
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

    <form action="{{ route('booking.step1.post') }}" method="POST">
      @csrf

      <div class="form-grid-2">
        <!-- Nama Lengkap -->
        <div class="form-group">
          <label class="form-label" for="full_name">Nama Lengkap</label>
          <input
            type="text"
            id="full_name"
            name="full_name"
            class="form-control"
            value="{{ old('full_name', $sessionData['full_name'] ?? '') }}"
            placeholder="Dimas Arya Pratama"
            required
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
            value="{{ old('nim', $sessionData['nim'] ?? '') }}"
            placeholder="22081010045"
            required
          />
        </div>

        <!-- Program Studi -->
        <div class="form-group">
          <label class="form-label" for="major">Program Studi</label>
          <div class="form-select-wrapper">
            <select id="major" name="major" class="form-control" required>
              @php $selectedMajor = old('major', $sessionData['major'] ?? 'Sistem Informasi'); @endphp
              <option value="Sistem Informasi" {{ $selectedMajor == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
              <option value="Informatika" {{ $selectedMajor == 'Informatika' ? 'selected' : '' }}>Informatika</option>
              <option value="Teknologi Informasi" {{ $selectedMajor == 'Teknologi Informasi' ? 'selected' : '' }}>Teknologi Informasi</option>
              <option value="Sains Data" {{ $selectedMajor == 'Sains Data' ? 'selected' : '' }}>Sains Data</option>
            </select>
            <span class="material-symbols-outlined form-select-icon">expand_more</span>
          </div>
        </div>

        <!-- Semester -->
        <div class="form-group">
          <label class="form-label" for="semester">Semester</label>
          <div class="form-select-wrapper">
            <select id="semester" name="semester" class="form-control" required>
              @php $selectedSem = old('semester', $sessionData['semester'] ?? 3); @endphp
              @for($i = 1; $i <= 8; $i++)
                <option value="{{ $i }}" {{ $selectedSem == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
              @endfor
            </select>
            <span class="material-symbols-outlined form-select-icon">expand_more</span>
          </div>
        </div>

        <!-- Nomor WhatsApp -->
        <div class="form-group">
          <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
          <input
            type="tel"
            id="whatsapp"
            name="whatsapp"
            class="form-control"
            value="{{ old('whatsapp', $sessionData['whatsapp'] ?? '') }}"
            placeholder="0812-3456-7890"
            required
          />
        </div>

        <!-- Email Mahasiswa -->
        <div class="form-group">
          <label class="form-label" for="email">Email Mahasiswa</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-control"
            value="{{ old('email', $sessionData['email'] ?? '') }}"
            placeholder="dimas.arya@student.ac.id"
            required
          />
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="form-actions">
        <a href="{{ route('home') }}" class="btn btn-ghost">
          Kembali
        </a>
        <button type="submit" class="btn btn-primary">
          <span>Lanjutkan</span>
          <span class="material-symbols-outlined" style="font-size: 18px;">arrow_forward</span>
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
