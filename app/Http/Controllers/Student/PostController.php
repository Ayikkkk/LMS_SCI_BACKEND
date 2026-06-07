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
     * Filter: serial_id cocok DAN (classroom_id NULL atau classroom_id = classroom siswa)
     */
    public function materials(Request $request)
    {
        $student = $request->user();
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));

        $materials = Post::with('mapel')
            ->where('serial_id', $student->serial_id)
            ->where('is_task', 0)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);

        $materials->getCollection()->transform(function ($material) {
            $material->subject_name = $material->mapel->name ?? 'Mapel Tidak Diketahui';
            unset($material->mapel);
            return $material;
        });

        return response()->json([
            'success' => true,
            'materials' => $materials->items(),
            'meta' => [
                'current_page' => $materials->currentPage(),
                'last_page'    => $materials->lastPage(),
                'per_page'     => $materials->perPage(),
                'total'        => $materials->total(),
            ],
        ]);
    }

    /**
     * 📄 Ambil daftar tugas siswa
     * Filter: serial_id cocok DAN (classroom_id NULL atau classroom_id = classroom siswa)
     */
    public function assignments(Request $request)
    {
        $student = $request->user();
        $studentId = $student->id;
        $perPage = max(1, min((int) $request->query('per_page', 15), 100));

        $assignments = DB::table('posts')
            ->leftJoin('mapels', 'posts.mapel_id', '=', 'mapels.id')
            ->leftJoin('tasks', function ($join) use ($studentId) {
                $join->on('posts.id', '=', 'tasks.post_id')
                    ->where('tasks.student_id', '=', $studentId);
            })
            ->where('posts.serial_id', $student->serial_id)
            ->where('posts.is_task', 1)
            ->where(function ($q) use ($student) {
                $q->whereNull('posts.classroom_id')
                  ->orWhere('posts.classroom_id', $student->classroom_id);
            })
            ->orderBy('posts.id', 'desc')
            ->select([
                'posts.id', 'posts.serial_id', 'posts.classroom_id', 'posts.user_id', 'posts.mapel_id',
                'posts.title', 'posts.description', 'posts.slug',
                'posts.link', 'posts.attachment', 'posts.embed',
                'posts.due_date', 'posts.category', 'posts.is_task',
                'posts.created_at', 'posts.updated_at',
                'mapels.name as subject_name',
                'tasks.id as task_id',
                'tasks.description as student_description',
                'tasks.attachment as student_attachment',
                'tasks.point',
                'tasks.created_at as submitted_at',
                'tasks.updated_at as submission_updated_at',
                DB::raw('CASE WHEN tasks.id IS NOT NULL THEN TRUE ELSE FALSE END as is_submitted'),
            ])
            ->paginate($perPage);

        $items = collect($assignments->items())->map(function ($ass) {
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
            'assignments' => $items,
            'meta' => [
                'current_page' => $assignments->currentPage(),
                'last_page'    => $assignments->lastPage(),
                'per_page'     => $assignments->perPage(),
                'total'        => $assignments->total(),
            ],
        ]);
    }

    /**
     * 📘 Ambil detail satu tugas
     */
    public function show(Request $request, $id)
    {
        $student = $request->user();
        $studentId = $student->id;

        $post = DB::table('posts')
            ->leftJoin('mapels', 'posts.mapel_id', '=', 'mapels.id')
            ->leftJoin('tasks', function ($join) use ($studentId) {
                $join->on('posts.id', '=', 'tasks.post_id')
                    ->where('tasks.student_id', '=', $studentId);
            })
            ->where('posts.id', $id)
            ->where('posts.serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('posts.classroom_id')
                  ->orWhere('posts.classroom_id', $student->classroom_id);
            })
            ->select([
                'posts.id', 'posts.serial_id', 'posts.user_id', 'posts.mapel_id',
                'posts.title', 'posts.description', 'posts.slug',
                'posts.link', 'posts.attachment', 'posts.embed',
                'posts.due_date', 'posts.category', 'posts.is_task',
                'posts.created_at', 'posts.updated_at',
                'mapels.name as subject_name',
                'tasks.id as task_id',
                'tasks.description as student_description',
                'tasks.attachment as student_attachment',
                'tasks.point',
                'tasks.created_at as submitted_at',
                'tasks.updated_at as submission_updated_at',
                DB::raw('CASE WHEN tasks.id IS NOT NULL THEN TRUE ELSE FALSE END as is_submitted'),
            ])
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
     * ⬇️ Download lampiran materi / tugas (AUTH)
     */
    public function downloadAttachment(Request $request, $id)
    {
        $student = $request->user('student');

        $post = Post::where('id', $id)
            ->where('serial_id', $student->serial_id)
            ->firstOrFail();

        if (!$post->attachment) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak tersedia'
            ], 404);
        }

        // Attachment bisa berupa path relatif atau full URL lama
        $attachment = $post->attachment;
        if (str_starts_with($attachment, 'http://') || str_starts_with($attachment, 'https://')) {
            // Legacy: ambil path relatif saja
            $parsed = parse_url($attachment, PHP_URL_PATH);
            $attachment = ltrim(str_replace('/storage/', '', $parsed), '/');
        }

        $path = storage_path('app/public/' . $attachment);

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';
        $fileName = basename($path);

        return response()->file($path, [
            'Content-Type'              => $mimeType,
            'Content-Disposition'       => 'attachment; filename="' . $fileName . '"',
            'Access-Control-Allow-Origin' => '*',
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
