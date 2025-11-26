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
            ->with('assignment:id,title,due_date,mapel_id') // opsional: relasi assignment
            ->orderBy('id', 'desc')
            /*************  ✨ Windsurf Command ⭐  *************/
            /**
             * Buat tugas baru untuk siswa.
             *
             * @param Request $request
             * @return \Illuminate\Http\JsonResponse
             *
             * @throws \Illuminate\Validation\ValidationException
             * @throws \Illuminate\Http\Exceptions\ConflictException
             */
            /*******  715abc51-0301-4590-a692-dcd2fb4da358  *******/
            ->get()
            ->map(function ($task) {
                return [
                    'id' => $task->id,
                    'post_id' => $task->post_id,
                    'assignment_title' => optional($task->assignment)->title,
                    'description' => $task->description,
                    'attachment' => $task->attachment,
                    'submitted_at' => $task->created_at->format('Y-m-d H:i:s'),
                    'status' => 'Sudah Dikerjakan',
                ];
            });

        return response()->json($tasks);
    }

    /**
     * Menyimpan tugas baru (submit tugas).
     */
    public function store(Request $request)
    {
        $student = $request->user('student');

        $validated = $request->validate([
            'post_id' => 'required|integer|exists:posts,id',
            'description' => 'nullable|string',
            'attachment' => 'nullable|file|max:20480', // 20MB
        ]);

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
}
