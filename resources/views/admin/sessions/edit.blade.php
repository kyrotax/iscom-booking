@extends('layouts.admin')

@section('content')
<div style="max-width: 700px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">
  <!-- Header -->
  <div style="display: flex; align-items: center; justify-content: space-between;">
    <div>
      <h1 class="font-headline-lg">Edit Sesi Mentoring</h1>
      <p class="font-body-md" style="color: var(--on-surface-variant); margin-top: 2px;">
        Perbarui data dan informasi sesi mentoring.
      </p>
    </div>
    <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline btn-sm">
      <span class="material-symbols-outlined" style="font-size: 16px;">arrow_back</span>
      <span>Kembali</span>
    </a>
  </div>

  @if($errors->any())
    <div class="alert alert-error">
      <span class="material-symbols-outlined">error</span>
      <div>
        <ul style="margin: 0; padding-left: 16px;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    </div>
  @endif

  <!-- Form -->
  <form action="{{ route('admin.sessions.update', $session->id) }}" method="POST" class="card" style="padding: 28px; display: flex; flex-direction: column; gap: 20px;">
    @csrf
    @method('PUT')

    <div class="form-group">
      <label class="form-label" for="title">Judul Sesi Mentoring <span style="color: var(--error);">*</span></label>
      <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $session->title) }}" required>
    </div>

    <div class="form-group">
      <label class="form-label" for="badge_label">Badge Label Kategori</label>
      <input type="text" id="badge_label" name="badge_label" class="form-input" value="{{ old('badge_label', $session->badge_label) }}">
      <span style="font-size: 11px; color: var(--on-surface-variant); margin-top: 4px;">Label penanda topik atau batch sesi.</span>
    </div>

    <div class="form-group">
      <label class="form-label" for="description">Deskripsi Singkat Sesi</label>
      <textarea id="description" name="description" class="form-input" rows="4">{{ old('description', $session->description) }}</textarea>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; padding: 12px 16px; background: var(--surface-container); border-radius: var(--radius-md);">
      <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $session->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: var(--primary);">
      <label for="is_active" style="font-weight: 500; font-size: 14px; cursor: pointer;">
        Aktifkan sesi ini (dapat dilihat dan dipilih oleh mahasiswa)
      </label>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 12px; border-top: 1px solid var(--border); padding-top: 16px;">
      <a href="{{ route('admin.sessions.index') }}" class="btn btn-ghost">Batal</a>
      <button type="submit" class="btn btn-primary">
        <span class="material-symbols-outlined" style="font-size: 18px;">save</span>
        <span>Perbarui Sesi</span>
      </button>
    </div>
  </form>
</div>
@endsection
