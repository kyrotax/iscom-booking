@extends('layouts.admin')

@section('content')
<div class="card card-max-md">
  <div class="card-header">
    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
      <a href="{{ route('admin.schedules.index') }}" style="color: var(--on-surface-variant); display: flex; align-items: center;">
        <span class="material-symbols-outlined" style="font-size: 20px;">arrow_back</span>
      </a>
      <h1 class="font-headline-lg">Tambah Jadwal Mentoring</h1>
    </div>
    <p class="font-body-md" style="color: var(--on-surface-variant);">
      Tentukan waktu sesi tatap muka dan kapasitas mahasiswa di lab.
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

  <form action="{{ route('admin.schedules.store') }}" method="POST">
    @csrf

    <div class="form-grid-2">
      <div class="form-group">
        <label class="form-label" for="day_name">Nama Hari</label>
        <select name="day_name" id="day_name" class="form-control" required>
          <option value="Senin" {{ old('day_name') == 'Senin' ? 'selected' : '' }}>Senin</option>
          <option value="Selasa" {{ old('day_name') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
          <option value="Rabu" {{ old('day_name') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
          <option value="Kamis" {{ old('day_name') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
          <option value="Jumat" {{ old('day_name') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
          <option value="Sabtu" {{ old('day_name') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="schedule_date">Tanggal</label>
        <input type="date" name="schedule_date" id="schedule_date" class="form-control" value="{{ old('schedule_date') }}" required/>
      </div>

      <div class="form-group">
        <label class="form-label" for="time_slot">Waktu Sesi</label>
        <input type="text" name="time_slot" id="time_slot" class="form-control" value="{{ old('time_slot') }}" placeholder="13:30 - 15:30 WIB" required/>
      </div>

      <div class="form-group">
        <label class="form-label" for="quota">Kapasitas Kuota (Peserta)</label>
        <input type="number" name="quota" id="quota" class="form-control" value="{{ old('quota', 10) }}" min="1" max="100" required/>
      </div>

      <div class="form-group col-span-2">
        <label class="form-label" for="mentor_names">Nama Mentor</label>
        <input type="text" name="mentor_names" id="mentor_names" class="form-control" value="{{ old('mentor_names') }}" placeholder="Kak Aditya W. & Kak Fadhil R." required/>
      </div>

      <div class="form-group">
        <label class="form-label" for="location">Lokasi Pertemuan</label>
        <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}" placeholder="Lab Komputer C301" required/>
      </div>

      <div class="form-group">
        <label class="form-label" for="topic">Topik Pembahasan</label>
        <input type="text" name="topic" id="topic" class="form-control" value="{{ old('topic') }}" placeholder="Basis Data & Web" required/>
      </div>
    </div>

    <div class="form-actions">
      <a href="{{ route('admin.schedules.index') }}" class="btn btn-ghost">Batal</a>
      <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
    </div>
  </form>
</div>
@endsection
