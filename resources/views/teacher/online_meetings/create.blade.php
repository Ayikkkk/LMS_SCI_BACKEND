{{-- resources/views/teacher/online_meetings/create.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Buat Online Meeting</h3>

    <form method="POST" action="/teacher/online-meetings">
        @csrf

        <div class="mb-3">
            <label>Kelas</label>
            <select name="classroom_id" class="form-control" required>
                <option value="">-- Pilih Kelas --</option>

                @foreach ($classrooms as $classroom)
                <option value="{{ $classroom->id }}">
                    {{ $classroom->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Judul Meeting</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <div class="mb-3">
            <label>Waktu Mulai</label>
            <input type="datetime-local" name="start_time" class="form-control" required>
        </div>

        <button class="btn btn-primary">Simpan</button>
        <a href="/teacher/online-meetings" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection