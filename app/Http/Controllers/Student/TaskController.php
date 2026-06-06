<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    /**
     * Menampilkan semua tugas yang sudah dikumpulkan oleh siswa.
     */
    public function index(Request $request)
    {
        $student = $request->user();

        $tasks = Task::where('student_id', $student->id)
            ->with('post:id,title,due_date,mapel_id')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'post_id' => $task->post_id,
                    'assignment_title' => optional($task->post)->title,
                    'description' => $task->description,
                    'attachment' => $task->attachment,
                    'submitted_at' => $task->created_at->format('Y-m-d H:i:s'),
                    // Tambahkan baris di bawah ini
                    'point' => $task->point ?? 0,
                    'status' => $task->point > 0 ? 'Sudah Dinilai' : 'Sudah Dikerjakan',
                ];
            });

        return response()->json($tasks);
    }
    /**
     * Menyimpan tugas baru (submit tugas).
     */
    public function store(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'post_id'     => 'required|integer|exists:posts,id',
            'description' => 'nullable|string',
            'attachment'  => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,mp4,mov,avi,mkv|max:10240', // 10MB
        ]);

        $post = Post::where('id', $validated['post_id'])
            ->where('serial_id', $student->serial_id)
            ->where('is_task', 1)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })
            ->firstOrFail();

        if ($this->isPastDeadline($post)) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu pengumpulan tugas sudah lewat.'
            ], 403);
        }

        // Cek apakah sudah pernah submit
        $existingTask = Task::where('student_id', $student->id)
            ->where('post_id', $validated['post_id'])
            ->first();

        if ($existingTask) {
            return response()->json([
                'success' => false,
                'message' => '❌ Kamu sudah mengirim tugas ini sebelumnya.'
            ], 409);
        }

        // Upload file
        $fileName = null;
        if ($request->hasFile('attachment')) {
            $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
            $request->file('attachment')->storeAs('tasks', $fileName, 'public');
        }

        // Simpan
        $task = Task::create([
            'serial_id' => $student->serial_id,
            'post_id' => $validated['post_id'],
            'student_id' => $student->id,
            'description' => $request->description,
            'attachment' => $fileName,
        ]);

        return response()->json([
            'success' => true,
            'message' => '✅ Tugas berhasil dikirim!',
            'data' => $task
        ], 201);
    }

    /**
     * Mengubah jawaban tugas jika deadline belum lewat.
     */
    public function update(Request $request, $postId)
    {
        try {
            $student = $request->user();

            $post = Post::where('id', $postId)
                ->where('serial_id', $student->serial_id)
                ->where('is_task', 1)
                ->where(function ($q) use ($student) {
                    $q->whereNull('classroom_id')
                      ->orWhere('classroom_id', $student->classroom_id);
                })
                ->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tugas tidak ditemukan atau tidak dapat diakses.'
                ], 404);
            }

            if ($this->isPastDeadline($post)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas waktu edit tugas sudah lewat.'
                ], 403);
            }

            $task = Task::where('student_id', $student->id)
                ->where('post_id', $post->id)
                ->first();

            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kamu belum pernah mengumpulkan tugas ini.'
                ], 404);
            }

            $validated = $request->validate([
                'description' => 'nullable|string',
                'attachment'  => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,mp4,mov,avi,mkv|max:10240',
            ]);

            if ($request->hasFile('attachment')) {
                // Hapus file lama jika ada (ignore error jika file tidak ada)
                if ($task->attachment) {
                    try {
                        Storage::disk('public')->delete('tasks/' . $task->attachment);
                    } catch (\Exception $e) {
                        // Lanjutkan meski hapus file lama gagal
                    }
                }

                $fileName = time() . '_' . $request->file('attachment')->getClientOriginalName();
                $request->file('attachment')->storeAs('tasks', $fileName, 'public');
                $task->attachment = $fileName;
            }

            // Gunakan deskripsi baru jika ada, jika tidak pakai yang lama
            // Pastikan description tidak null karena kolom NOT NULL
            $newDescription = $validated['description'] ?? null;
            if ($newDescription !== null && trim($newDescription) !== '') {
                $task->description = $newDescription;
            }
            // Jika description kosong/null dari request, biarkan nilai lama

            $task->point = null;
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban tugas berhasil diperbarui.',
                'data' => $task
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('TaskController@update error: ' . $e->getMessage(), [
                'post_id' => $postId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download file jawaban tugas siswa.
     */
    public function download(Request $request, $postId)
    {
        $student = $request->user();

        $task = Task::where('student_id', $student->id)
            ->where('post_id', $postId)
            ->firstOrFail();

        if (!$task->attachment) {
            return response()->json([
                'success' => false,
                'message' => 'File jawaban tidak tersedia.'
            ], 404);
        }

        $path = storage_path('app/public/tasks/' . $task->attachment);

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File jawaban tidak ditemukan.'
            ], 404);
        }

        return response()->download($path);
    }

    private function isPastDeadline(Post $post): bool
    {
        return $post->due_date !== null && now()->greaterThan(\Carbon\Carbon::parse($post->due_date));
    }
}
