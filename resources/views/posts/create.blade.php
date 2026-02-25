@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3 class="mb-4">Tambah Post / Materi</h3>

    {{-- Error Message --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan!</strong>
            <ul class="mt-2 mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('teacher.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- HIDDEN: serial_id & user_id --}}
        <input type="hidden" name="serial_id" value="1">
        <input type="hidden" name="user_id" value="1">

        {{-- Mapel --}}
        <div class="mb-3">
            <label class="form-label">Mata Pelajaran</label>
            <select name="mapel_id" class="form-control" required>
                <option value="">-- Pilih Mapel --</option>
                @foreach ($mapels as $mapel)
                    <option value="{{ $mapel->id }}"
                        {{ old('mapel_id') == $mapel->id ? 'selected' : '' }}>
                        {{ $mapel->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Judul --}}
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input
                type="text"
                name="title"
                class="form-control"
                placeholder="Judul materi"
                value="{{ old('title') }}"
                required
            >
        </div>

        {{-- Deskripsi --}}
        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea
                name="description"
                class="form-control"
                rows="4"
                placeholder="Tulis deskripsi (opsional)"
            >{{ old('description') }}</textarea>
        </div>

        {{-- Link --}}
        <div class="mb-3">
            <label class="form-label">Link (opsional)</label>
            <input
                type="url"
                name="link"
                class="form-control"
                placeholder="https://contoh.com"
                value="{{ old('link') }}"
            >
        </div>

        {{-- Jenis --}}
        <div class="mb-3">
            <label class="form-label">Jenis Post</label>
            <select name="is_task" class="form-control">
                <option value="0" {{ old('is_task') == 0 ? 'selected' : '' }}>Materi</option>
                <option value="1" {{ old('is_task') == 1 ? 'selected' : '' }}>Tugas</option>
            </select>
        </div>

        {{-- Attachment --}}
        <div class="mb-3">
            <label class="form-label">Attachment (opsional)</label>
            <input
                type="file"
                name="attachment"
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.mp4,.doc,.docx"
            >
            <small class="text-muted">Format: pdf, jpg, png, mp4, doc, docx</small>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

</div>
@endsection
