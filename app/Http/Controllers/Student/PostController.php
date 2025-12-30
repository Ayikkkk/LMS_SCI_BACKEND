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
     * 📘 Ambil daftar materi siswa
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
     * 📄 Ambil daftar tugas siswa
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
                'tasks.id as task_id',
                'tasks.attachment as student_attachment',
                'tasks.point',
                DB::raw('CASE WHEN tasks.id IS NOT NULL THEN TRUE ELSE FALSE END as is_submitted')
            )
            ->get()
            ->map(function ($ass) {
                if ($ass->is_submitted) {
                    $ass->status = ($ass->point !== null)
                        ? 'Sudah Dinilai'
                        : 'Belum Dinilai';
                } else {
                    $ass->status = 'Belum Mengerjakan';
                }
                return $ass;
            });

        return response()->json([
            'success' => true,
            'assignments' => $assignments
        ]);
    }

    /**
     * 📘 Ambil detail satu tugas
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
                'tasks.id as task_id',
                'tasks.attachment as student_attachment',
                'tasks.point',
                DB::raw('CASE WHEN tasks.id IS NOT NULL THEN TRUE ELSE FALSE END as is_submitted')
            )
            ->first();

        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        if ($post->is_submitted) {
            $post->status = ($post->point !== null)
                ? 'Sudah Dinilai'
                : 'Belum Dinilai';
        } else {
            $post->status = 'Belum Mengerjakan';
        }

        return response()->json([
            'success' => true,
            $post->is_task ? 'assignment' : 'material' => $post
        ]);
    }

    /**
     * 📝 Tambah data Post
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mapel_id'    => 'required|integer',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'link'        => 'nullable|string',
            'is_task'     => 'required|boolean',
            'attachment'  => 'nullable|file|mimes:pdf,jpg,jpeg,png,mp4,doc,docx|max:20480',
            'due_date'    => 'nullable|date',
            'serial_id'   => 'required|integer',
            'user_id'     => 'required|integer',
        ]);

        $slug = Str::slug($validated['title'] . '-' . time());
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $safeName = time() . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('posts', $safeName, 'public');
        }

        Post::create([
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
