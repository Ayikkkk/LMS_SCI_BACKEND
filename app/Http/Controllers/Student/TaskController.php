<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->limit(100) // batasi 100 task — cukup untuk satu semester
            ->get()
            ->map(function ($task) {
                return [
                    'id'               => $task->id,
                    'post_id'          => $task->post_id,
                    'assignment_title' => optional($task->post)->title,
                    'description'      => $task->description,
                    'attachment'       => $task->attachment,
                    'submitted_at'     => $task->created_at->format('Y-m-d H:i:s'),
                    'point'            => $task->point ?? 0,
                    'status'           => $task->point > 0 ? 'Sudah Dinilai' : 'Sudah Dikerjakan',
                ];
            });

        return response()->json($tasks);
    }

    /**
     * Menyimpan tugas baru (submit tugas).
     *
     * Race condition protection:
     * - Cek awal (quick check, non-atomik) untuk early return
     * - Upload file dilakukan SEBELUM insert DB
     * - Insert DB dilindungi UNIQUE constraint (student_id, post_id)
     * - Jika UniqueConstraintViolationException → return 409 (sudah submit)
     * - Jika insert gagal → hapus file yang sudah diupload (rollback manual)
     */
    public function store(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'post_id'     => 'required|integer|exists:posts,id',
            'description' => 'nullable|string',
            'attachment'  => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png,mp4,mov,avi,mkv|max:10240',
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

        // Cek awal — quick check sebelum proses upload
        // Race condition tetap ditangani oleh unique constraint di bawah
        $existingTask = Task::where('student_id', $student->id)
            ->where('post_id', $validated['post_id'])
            ->first();

        if ($existingTask) {
            return response()->json([
                'success' => false,
                'message' => '❌ Kamu sudah mengirim tugas ini sebelumnya.'
            ], 409);
        }

        // Upload file SEBELUM insert DB
        // Jika insert gagal, file akan dihapus (rollback manual)
        $fileName = null;
        if ($request->hasFile('attachment')) {
            // Gunakan Str::random(40) untuk nama unik — tidak bergantung time()
            // Mencegah collision saat dua siswa upload file bersamaan
            $ext      = $request->file('attachment')->getClientOriginalExtension();
            $fileName = Str::random(40) . '.' . strtolower($ext);
            $request->file('attachment')->storeAs('tasks', $fileName, 'public');
        }

        try {
            $task = DB::transaction(function () use ($student, $validated, $request, $fileName) {
                return Task::create([
                    'serial_id'   => $student->serial_id,
                    'post_id'     => $validated['post_id'],
                    'student_id'  => $student->id,
                    'description' => $request->description,
                    'attachment'  => $fileName,
                ]);
            });

        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Race condition: dua request bersamaan lolos cek awal, tapi
            // hanya satu yang berhasil insert karena unique constraint
            if ($fileName) {
                Storage::disk('public')->delete('tasks/' . $fileName);
            }
            return response()->json([
                'success' => false,
                'message' => '❌ Kamu sudah mengirim tugas ini sebelumnya.'
            ], 409);

        } catch (\Exception $e) {
            // DB error lain — hapus file yang sudah diupload
            if ($fileName) {
                try {
                    Storage::disk('public')->delete('tasks/' . $fileName);
                } catch (\Exception $deleteErr) {
                    Log::warning('TaskController@store: failed to delete orphan file after DB error', [
                        'file' => $fileName,
                        'error' => $deleteErr->getMessage(),
                    ]);
                }
            }
            Log::error('TaskController@store DB error: ' . $e->getMessage(), [
                'post_id'    => $validated['post_id'],
                'student_id' => $student->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server. Coba lagi.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Tugas berhasil dikirim!',
            'data'    => $task
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

            $newFileName = null;
            if ($request->hasFile('attachment')) {
                // Upload file baru dulu, nama unik menggunakan Str::random
                $ext         = $request->file('attachment')->getClientOriginalExtension();
                $newFileName = Str::random(40) . '.' . strtolower($ext);
                $request->file('attachment')->storeAs('tasks', $newFileName, 'public');

                // Hapus file lama setelah upload baru berhasil
                if ($task->attachment) {
                    try {
                        Storage::disk('public')->delete('tasks/' . $task->attachment);
                    } catch (\Exception $e) {
                        // Lanjutkan meski hapus file lama gagal
                    }
                }

                $task->attachment = $newFileName;
            }

            $newDescription = $validated['description'] ?? null;
            if ($newDescription !== null && trim($newDescription) !== '') {
                $task->description = $newDescription;
            }

            $task->point = null;
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban tugas berhasil diperbarui.',
                'data'    => $task
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('TaskController@update error: ' . $e->getMessage(), [
                'post_id' => $postId,
                'trace'   => $e->getTraceAsString()
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

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $fileName = $task->attachment;

        return response()->file($path, [
            'Content-Type'                => $mimeType,
            'Content-Disposition'         => 'attachment; filename="' . $fileName . '"',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    private function isPastDeadline(Post $post): bool
    {
        return $post->due_date !== null && now()->greaterThan(\Carbon\Carbon::parse($post->due_date));
    }
}
