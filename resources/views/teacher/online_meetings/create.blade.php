@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Buat Online Meeting</h3>

    <form method="POST"
          action="{{ route('teacher.meetings.store') }}">
        @csrf

        {{-- Pilih Kelas --}}
        <div class="mb-3">
            <label class="form-label">Kelas *</label>
            <select name="classroom_id"
                    class="form-control @error('classroom_id') is-invalid @enderror"
                    required>
                <option value="">-- Pilih Kelas --</option>

                @foreach ($classrooms as $classroom)
                <option value="{{ $classroom->id }}"
                    {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                    {{ $classroom->name }}
                </option>
                @endforeach
            </select>
            @error('classroom_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Judul --}}
        <div class="mb-3">
            <label class="form-label">Judul Meeting *</label>
            <input type="text" name="title"
                   class="form-control @error('title') is-invalid @enderror"
                   value="{{ old('title') }}"
                   placeholder="Contoh: Matematika Bab 1"
                   required>
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" rows="3"
                      class="form-control @error('description') is-invalid @enderror"
                      placeholder="Opsional — Pahami dulu rumus segitiga, siapkan buku dan alat tulis">
                {{ old('description') }}
            </textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Waktu Mulai --}}
        <div class="mb-3">
            <label class="form-label">Waktu Mulai *</label>
            <input type="datetime-local"
                   name="start_time"
                   class="form-control @error('start_time') is-invalid @enderror"
                   value="{{ old('start_time') }}"
                   min="{{ now()->format('Y-m-d\TH:i') }}"
                   required>
            @error('start_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">
                Meeting otomatis LIVE ketika jam sudah sampai atau guru memulai lebih dulu
            </small>
        </div>

        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                Simpan
            </button>

            <a href="{{ route('teacher.meetings.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>
        </div>

    </form>
</div>
@endsection
