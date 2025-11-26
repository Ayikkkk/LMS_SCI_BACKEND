<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * 📘 Ambil daftar materi siswa.
     */
    public function materials(Request $request)
    {
        $student = $request->user('student');

        $materials = Post::with('mapel')
            ->where('serial_id', $student->serial_id)
            ->where('is_task', 0)
            ->orderBy('id', 'desc')
            ->get();

        $materials->each(function ($material) {
            $material->subject_name = $material->mapel->name ?? 'Mapel Tidak Diketahui';
            unset($material->mapel);
        });

        return response()->json([
            'success' => true,
            'materials' => $materials
        ]);
    }

    /**
     * 📄 Ambil daftar tugas siswa (dengan status & file pengumpulan).
     */
    public function assignments(Request $request)
    {
        $student = $request->user('student');
        $studentId = $student->id;

        $assignments = DB::table('posts')
            ->leftJoin('mapels', 'posts.mapel_id', '=', 'mapels.id')
            ->leftJoin('tasks', function ($join) use ($studentId) {
                $join->on('posts.id', '=', 'tasks.post_id')
                    ->where('tasks.student_id', '=', $studentId);
            })
            ->where('posts.serial_id', $student->serial_id)
            ->where('posts.is_task', 1)
            ->orderBy('posts.id', 'desc')
            ->select(
                'posts.*',
                'mapels.name as subject_name',
                'tasks.attachment as student_attachment',
                DB::raw('CASE WHEN tasks.id IS NOT NULL THEN TRUE ELSE FALSE END as is_submitted')
            )
            ->get()
            ->map(function ($assignment) {
                // Tentukan status otomatis
                $assignment->status = $assignment->is_submitted
                    ? 'Sudah Mengumpulkan'
                    : 'Belum Mengerjakan';
                return $assignment;
            });

        return response()->json([
            'success' => true,
            'assignments' => $assignments
        ]);
    }

    /**
     * 📘 Ambil detail satu tugas atau materi (termasuk status & file tugas jika tugas).
     * * PERBAIKAN: Menggunakan key 'material' jika bukan tugas.
     */
    public function show(Request $request, $id)
    {
        $student = $request->user('student');
        $studentId = $student->id;

        $post = DB::table('posts')
            ->leftJoin('mapels', 'posts.mapel_id', '=', 'mapels.id')
            ->leftJoin('tasks', function ($join) use ($studentId) {
                $join->on('posts.id', '=', 'tasks.post_id')
                    ->where('tasks.student_id', '=', $studentId);
            })
            ->where('posts.id', $id)
            ->select(
                'posts.*',
                'mapels.name as subject_name',
                'tasks.attachment as student_attachment',
                DB::raw('CASE WHEN tasks.id IS NOT NULL THEN TRUE ELSE FALSE END as is_submitted')
            )
            ->first();

        if (!$post) {
            return response()->json([
                'success' => false,
                'message' => 'Tugas atau materi tidak ditemukan.'
            ], 404);
        }

        // Tentukan status otomatis
        $post->status = $post->is_submitted
            ? 'Sudah Mengumpulkan'
            : 'Belum Mengerjakan';

        if ($post->is_task) {
            // Jika itu tugas, gunakan key 'assignment'
            return response()->json([
                'success' => true,
                'assignment' => $post
            ]);
        } else {
            // Jika itu materi, gunakan key 'material'
            // Ini yang akan dipanggil oleh rute /posts/{id} untuk materi.
            return response()->json([
                'success' => true,
                'material' => $post
            ]);
        }
    }


    //tambah data posts
    public function store(Request $request)
    {

        // Validasi
        $validated = $request->validate([
            'mapel_id'   => 'required|integer',
            'title'      => 'required|string|max:255',
            'description' => 'nullable|string',
            'link'       => 'nullable|string',
            'is_task'    => 'required|boolean',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png,mp4,doc,docx|max:20480',
            'due_date'  => 'nullable|date',
            // Tambahan
            'serial_id'  => 'required|integer',
            'user_id'    => 'required|integer',
        ]);
        $slug = Str::slug($validated['title'] . '-' . time());
        // Upload file
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = $file->getClientOriginalName(); // nama file custom
            $file->storeAs('posts', $fileName, 'public');
            $attachmentPath = $fileName; // hanya nama file
        }

        // Simpan ke database
        $post = Post::create([
            'serial_id'   => $validated['serial_id'],
            'user_id'     => $validated['user_id'],
            'mapel_id'    => $validated['mapel_id'],
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'link'        => $validated['link'],
            'is_task'     => $validated['is_task'],
            'attachment'  => $attachmentPath,
            'due_date'    => $validated['due_date'] ?? null,
            'slug'        => $slug,
        ]);

        return back()->with('success', 'Berhasil menambah data!');
    }


    public function create()
    {
        $mapels = \App\Models\Mapel::all();

        return view('posts.create', compact('mapels'));
    }
}
