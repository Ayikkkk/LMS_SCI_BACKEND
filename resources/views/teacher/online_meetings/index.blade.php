@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Online Meetings Saya</h3>

        <a href="{{ route('teacher.meetings.create') }}" class="btn btn-primary">
            + Buat Online Meeting
        </a>
    </div>

    @forelse ($meetings as $meeting)
    <div class="card mb-3 shadow-sm">
        <div class="card-body">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-start">
                <h5 class="mb-1">{{ $meeting->title }}</h5>

                {{-- Status Badge --}}
                @if ($meeting->status === 'live')
                    <span class="badge bg-danger">LIVE</span>
                @elseif ($meeting->status === 'upcoming')
                    <span class="badge bg-info text-dark">Upcoming</span>
                @else
                    <span class="badge bg-secondary">Ended</span>
                @endif
            </div>

            {{-- Meeting Info --}}
            <p class="mb-1">
                <strong>Kelas:</strong> {{ $meeting->classroom->name ?? '-' }}<br>
                <strong>Mulai:</strong> {{ \Carbon\Carbon::parse($meeting->start_time)->format('d M Y, H:i') }}<br>
                <small class="text-muted">Kode: {{ $meeting->meeting_code }}</small>
            </p>

            {{-- Participants Count --}}
            @php
                $participantsCount = $meeting->meetingParticipants?->participants ?
                    count($meeting->meetingParticipants->participants) : 0;
            @endphp

            @if($participantsCount > 0)
                <p class="text-success small mb-2">
                    👍 {{$participantsCount}} peserta telah join
                </p>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex gap-2 mt-2">

                @if ($meeting->status === 'upcoming')
                    <form method="POST"
                          action="{{ route('teacher.meetings.start', $meeting->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            Mulai Meeting
                        </button>
                    </form>

                @elseif ($meeting->status === 'live')
                    <a href="{{ config('services.jitsi.domain') . '/' . $meeting->meeting_code }}"
                       target="_blank"
                       class="btn btn-primary btn-sm">
                        Join Meeting
                    </a>

                    <form method="POST"
                          action="{{ route('teacher.meetings.end', $meeting->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            Akhiri Meeting
                        </button>
                    </form>
                @endif

            </div>

        </div>
    </div>
    @empty
    <div class="alert alert-info">
        Belum ada meeting. Buat meeting sekarang.
    </div>
    @endforelse
</div>
@endsection
