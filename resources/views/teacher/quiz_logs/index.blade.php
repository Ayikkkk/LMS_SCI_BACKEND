@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">📊 Monitoring Aktivitas Quiz</h3>
            <small class="text-muted">Pantau perilaku siswa saat mengerjakan kuis</small>
        </div>
    </div>

    @if ($exerciseList->isEmpty())
        <div class="alert alert-info">Belum ada data log aktivitas quiz.</div>
    @else
        <div class="row g-3">
            @foreach ($exerciseList as $ex)
            @php
                $hasSuspicious = $ex['suspicious'] > 0;
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-{{ $hasSuspicious ? 'warning' : 'light' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0 fw-bold">{{ $ex['title'] }}</h6>
                            @if ($hasSuspicious)
                                <span class="badge bg-warning text-dark">⚠️ Mencurigakan</span>
                            @else
                                <span class="badge bg-success">✅ Normal</span>
                            @endif
                        </div>

                        <p class="text-muted small mb-2">Tipe: {{ $ex['type'] }}</p>

                        <div class="row text-center g-2 mb-3">
                            <div class="col-4">
                                <div class="bg-light rounded p-2">
                                    <div class="fw-bold fs-5 text-primary">{{ $ex['students'] }}</div>
                                    <div class="small text-muted">Siswa</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-2">
                                    <div class="fw-bold fs-5 {{ $ex['suspicious'] > 0 ? 'text-warning' : 'text-success' }}">
                                        {{ $ex['suspicious'] }}
                                    </div>
                                    <div class="small text-muted">Mencurigakan</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-2">
                                    <div class="fw-bold fs-5 text-secondary">
                                        {{ $ex['latest'] ? \Carbon\Carbon::parse($ex['latest'])->format('d/m') : '-' }}
                                    </div>
                                    <div class="small text-muted">Terakhir</div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('teacher.quiz-logs.show', $ex['id']) }}"
                           class="btn btn-sm btn-outline-primary w-100">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
