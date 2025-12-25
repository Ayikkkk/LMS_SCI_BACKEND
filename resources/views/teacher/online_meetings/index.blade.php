{{-- resources/views/teacher/online_meetings/index.blade.php --}}

@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Online Meetings Saya</h3>

    <a href="/teacher/online-meetings/create" class="btn btn-primary mb-3">
        + Buat Online Meeting
    </a>

    @foreach ($meetings as $meeting)
    <div class="card mb-2">
        <div class="card-body">
            <h5>{{ $meeting->title }}</h5>
            <p>
                Kelas: {{ $meeting->classroom->name ?? '-' }} <br>
                Mulai: {{ $meeting->start_time }} <br>
                Status: <strong>{{ strtoupper($meeting->status) }}</strong>
            </p>


            @if ($meeting->status === 'upcoming')
            <form method="POST" action="/teacher/online-meetings/{{ $meeting->id }}/start">
                @csrf
                <button class="btn btn-success">
                    Mulai Meeting
                </button>
            </form>
            @elseif ($meeting->status === 'live')
            <form method="POST" action="/teacher/online-meetings/{{ $meeting->id }}/end">
                @csrf
                <button class="btn btn-danger">
                    Akhiri Meeting
                </button>
            </form>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection