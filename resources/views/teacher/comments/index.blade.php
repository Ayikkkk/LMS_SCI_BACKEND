@extends('layouts.app')

@section('content')
<div style="max-width: 780px; margin: auto; padding-top: 20px;">

    <h4><strong>Komentar untuk Post:</strong> {{ $post->title }}</h4>
    <p>{{ $post->description }}</p>
    <hr>

    {{-- Tambah Komentar --}}
    <form action="{{ route('teacher.comments.store', $post->id) }}" method="POST" style="margin-bottom: 20px;">
        @csrf
        <textarea name="message" class="form-control" rows="2"
            placeholder="Tulis komentar..." required></textarea>
        <button class="btn btn-primary btn-sm" style="margin-top: 8px;">Kirim</button>
    </form>

    @forelse ($comments as $comment)
        <div class="p-3 mb-3 bg-light rounded" style="border:1px solid #dcdcdc;">

            {{-- Avatar & Nama --}}
            <div class="d-flex align-items-center mb-1">
                <img src="{{ $comment->student?->photo
                            ? asset('storage/'.$comment->student->photo)
                            : ($comment->user?->img ? asset('storage/'.$comment->user->img) : 'https://ui-avatars.com/api/?name=User') }}"
                    class="rounded-circle me-2"
                    width="32" height="32">

                <strong>{{ $comment->student?->name ?? $comment->user?->name ?? 'Tidak diketahui' }}</strong>
                <small class="ms-2 text-muted">
                    ({{ $comment->is_user ? 'Guru' : 'Siswa' }})
                </small>

                <small class="ms-auto text-muted">
                    {{ $comment->created_at->diffForHumans() }}
                </small>
            </div>

            {{-- Isi Komentar --}}
            <p class="mb-1">{{ $comment->message }}</p>

            {{-- Tombol Hapus Komentar (Guru bebas hapus semua) --}}
            <form action="{{ route('teacher.comments.destroy', $comment->id) }}"
                  method="POST"
                  class="d-inline">
                @csrf
                @method('DELETE')
                <button class="btn btn-link text-danger p-0"
                    onclick="return confirm('Hapus komentar ini?')">
                    Hapus
                </button>
            </form>

            {{-- Balasan --}}
            @foreach ($comment->replies as $reply)
                <div class="mt-3 ms-4 p-2 bg-white rounded border">

                    <div class="d-flex align-items-center mb-1">
                        <img src="{{ $reply->student?->photo
                                    ? asset('storage/'.$reply->student->photo)
                                    : ($reply->user?->img ? asset('storage/'.$reply->user->img) : 'https://ui-avatars.com/api/?name=User') }}"
                            class="rounded-circle me-2"
                            width="26" height="26">

                        <strong style="font-size: 13px;">
                            {{ $reply->student?->name ?? $reply->user?->name ?? '-' }}
                        </strong>
                        <small class="ms-2 text-muted" style="font-size: 11px;">
                            ({{ $reply->is_user ? 'Guru' : 'Siswa' }})
                        </small>

                        <small class="ms-auto text-muted" style="font-size: 11px;">
                            {{ $reply->created_at->diffForHumans() }}
                        </small>
                    </div>

                    <p class="mb-1" style="font-size: 13px;">{{ $reply->message }}</p>

                    {{-- Guru boleh hapus semua reply --}}
                    <form action="{{ route('teacher.comments.reply.destroy', $reply->id) }}"
                          method="POST"
                          class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-link text-danger p-0"
                            onclick="return confirm('Hapus balasan ini?')">
                            Hapus
                        </button>
                    </form>
                </div>
            @endforeach

            {{-- Balas Komentar --}}
            <form action="{{ route('teacher.comments.reply.store', $comment->id) }}"
                  method="POST"
                  class="mt-2">
                @csrf
                <input type="text"
                    name="message"
                    class="form-control form-control-sm"
                    placeholder="Balas komentar..." required>
            </form>

        </div>
    @empty
        <p class="text-center text-muted">Belum ada komentar.</p>
    @endforelse

</div>
@endsection
