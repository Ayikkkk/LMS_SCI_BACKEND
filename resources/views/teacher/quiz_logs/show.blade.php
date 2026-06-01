@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('teacher.quiz-logs.index') }}" class="text-decoration-none text-muted small">
                ← Kembali
            </a>
            <h3 class="mb-0 mt-1">{{ $exercise->title ?? 'Quiz #' . $exercise->id }}</h3>
            <small class="text-muted">Tipe: {{ $exercise->exerciseType->name ?? '-' }} &bull; {{ $data->count() }} siswa</small>
        </div>
    </div>

    {{-- Ringkasan --}}
    @php
        $totalSuspicious = $data->sum('suspicious');
        $highRisk        = $data->filter(fn($d) => $d['risk_level'] === 'high')->count();
        $medRisk         = $data->filter(fn($d) => $d['risk_level'] === 'medium')->count();
        $autoSubmits     = $data->filter(fn($d) => $d['is_auto_submit'])->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-light">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-primary">{{ $data->count() }}</div>
                    <div class="small text-muted">Total Siswa</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-danger bg-opacity-10">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-danger">{{ $highRisk }}</div>
                    <div class="small text-muted">Risiko Tinggi</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-warning bg-opacity-10">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-warning">{{ $medRisk }}</div>
                    <div class="small text-muted">Risiko Sedang</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center border-0 bg-secondary bg-opacity-10">
                <div class="card-body py-3">
                    <div class="fs-3 fw-bold text-secondary">{{ $autoSubmits }}</div>
                    <div class="small text-muted">Auto Submit</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel per siswa --}}
    @foreach ($data as $item)
    @php
        $risk = $item['risk_level'];
        $borderClass = match($risk) {
            'high'   => 'border-danger',
            'medium' => 'border-warning',
            default  => 'border-success',
        };
        $badgeClass = match($risk) {
            'high'   => 'bg-danger',
            'medium' => 'bg-warning text-dark',
            default  => 'bg-success',
        };
        $riskLabel = match($risk) {
            'high'   => '🔴 Risiko Tinggi',
            'medium' => '🟡 Risiko Sedang',
            default  => '🟢 Normal',
        };
    @endphp

    <div class="card mb-3 shadow-sm {{ $borderClass }}" style="border-left-width: 4px !important;">
        <div class="card-body">

            {{-- Header siswa --}}
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h6 class="mb-0 fw-bold">{{ $item['student']->name }}</h6>
                    <small class="text-muted">NIS: {{ $item['student']->nis ?? '-' }}</small>
                    @if ($item['device_info'])
                        <div class="text-muted mt-1" style="font-size:11px">
                            📱 {{ $item['device_info'] }}
                        </div>
                    @endif
                </div>
                <span class="badge {{ $badgeClass }}">{{ $riskLabel }}</span>
            </div>

            {{-- Statistik siswa --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center gap-2 p-2 bg-light rounded">
                        <span class="fs-5">⏱️</span>
                        <div>
                            <div class="fw-bold small">
                                @if ($item['duration'])
                                    {{ floor($item['duration'] / 60) }}m {{ $item['duration'] % 60 }}s
                                @else
                                    -
                                @endif
                            </div>
                            <div class="text-muted" style="font-size:11px">Durasi</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center gap-2 p-2 {{ $item['bg_count'] > 0 ? 'bg-warning bg-opacity-25' : 'bg-light' }} rounded">
                        <span class="fs-5">📱</span>
                        <div>
                            <div class="fw-bold small">{{ $item['bg_count'] }}x</div>
                            <div class="text-muted" style="font-size:11px">Keluar App</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center gap-2 p-2 {{ $item['back_blocked'] > 0 ? 'bg-warning bg-opacity-25' : 'bg-light' }} rounded">
                        <span class="fs-5">🔒</span>
                        <div>
                            <div class="fw-bold small">{{ $item['back_blocked'] }}x</div>
                            <div class="text-muted" style="font-size:11px">Coba Keluar</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="d-flex align-items-center gap-2 p-2 {{ $item['is_auto_submit'] ? 'bg-secondary bg-opacity-25' : 'bg-light' }} rounded">
                        <span class="fs-5">{{ $item['is_auto_submit'] ? '⏰' : '✅' }}</span>
                        <div>
                            <div class="fw-bold small">{{ $item['is_auto_submit'] ? 'Auto' : 'Manual' }}</div>
                            <div class="text-muted" style="font-size:11px">Submit</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline log --}}
            <details>
                <summary class="text-primary small" style="cursor:pointer">
                    Lihat Timeline Aktivitas ({{ $item['logs']->count() }} event)
                </summary>
                <div class="mt-2 table-responsive">
                    <table class="table table-sm table-bordered mb-0" style="font-size:12px">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Event</th>
                                <th>Durasi</th>
                                <th>Flag</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item['logs'] as $log)
                            @php
                                $rowClass = $log->suspicious_flag ? 'table-warning' : '';
                                $eventIcon = match(true) {
                                    str_starts_with($log->event_type, 'APP_BACKGROUND') => '📱',
                                    str_starts_with($log->event_type, 'APP_RESUME')     => '🔄',
                                    str_starts_with($log->event_type, 'BACK_BUTTON')    => '🔒',
                                    str_starts_with($log->event_type, 'SUBMIT')         => '✅',
                                    str_starts_with($log->event_type, 'AUTO_SUBMIT')    => '⏰',
                                    str_starts_with($log->event_type, 'START')          => '▶️',
                                    str_starts_with($log->event_type, 'SUSPICIOUS')     => '⚠️',
                                    str_starts_with($log->event_type, 'RECONNECTED')    => '🌐',
                                    str_starts_with($log->event_type, 'DISCONNECTED')   => '📵',
                                    default => '•',
                                };
                            @endphp
                            <tr class="{{ $rowClass }}">
                                <td class="text-nowrap">
                                    {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}
                                </td>
                                <td>{{ $eventIcon }} {{ $log->event_type }}</td>
                                <td>
                                    @if ($log->duration_seconds !== null)
                                        {{ $log->duration_seconds }}s
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($log->suspicious_flag)
                                        <span class="badge bg-warning text-dark">⚠️</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>

        </div>
    </div>
    @endforeach

</div>
@endsection
