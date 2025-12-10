<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Login student
     * POST /student/login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::with(['classroom', 'guru:id,name,email,phone'])
            ->where('username', $request->username)
            ->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json(['success' => false, 'message' => 'Username atau password salah'], 401);
        }

        // Buat token baru
        $token = $student->createToken('student_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'data' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone ?? $student->telephone ?? null,
                'nis' => $student->nis ?? null,
                'username' => $student->username,
                'className' => optional($student->classroom)->name ?? $student->class_name ?? null,
                'photo' => $student->photo ? url(Storage::url($student->photo)) : null,
                'guru' => $student->guru ? [
                    'id' => $student->guru->id,
                    'name' => $student->guru->name,
                    'email' => $student->guru->email,
                    'phone' => $student->guru->phone ?? null,
                ] : null,
            ],
        ]);
    }

    /**
     * Ambil profile siswa (GET /student/profile)
     */
    public function profile(Request $request)
    {
        $student = $request->user()->load(['classroom', 'guru:id,name,email,phone']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone ?? $student->telephone ?? null,
                'nis' => $student->nis ?? null,
                'username' => $student->username ?? null,
                'className' => optional($student->classroom)->name ?? $student->class_name ?? null,
                'photo' => $student->photo ? url(Storage::url($student->photo)) : null,
                'guru' => $student->guru ? [
                    'id' => $student->guru->id,
                    'name' => $student->guru->name,
                    'email' => $student->guru->email,
                    'phone' => $student->guru->phone ?? null,
                ] : null,
            ],
        ]);
    }

    /**
     * Update profile (PUT /student/profile)
     * menerima multipart/form-data (photo optional)
     * Hanya field yang dikirim akan di-update.
     */
    public function updateProfile(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'name' => 'sometimes|nullable|string|max:255',
            'email' => [
                'sometimes',
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone' => 'sometimes|nullable|string|max:50',
            'photo' => 'sometimes|nullable|file|image|max:5120', // max 5MB
        ]);

        // Kalau tidak ada perubahan sama sekali, kembalikan response informatif
        $hasAny = collect($validated)->isNotEmpty() || $request->hasFile('photo');
        if (!$hasAny) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data yang dikirim untuk diperbarui'
            ], 400);
        }

        // Update hanya field yang hadir di request
        if ($request->has('name')) {
            $student->name = $validated['name'];
        }
        if ($request->has('email')) {
            $student->email = $validated['email'];
        }
        if ($request->has('phone')) {
            $student->phone = $validated['phone'];
        }

        // Handle photo upload (hapus lama, simpan baru)
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');

            // Hapus file lama bila ada
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            // Simpan file baru di storage/app/public/students/...
            $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('students', $filename, 'public'); // returns 'students/<name>'
            $student->photo = $path;
        }

        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data' => [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
                'photo' => $student->photo ? url(Storage::url($student->photo)) : null,
            ],
        ]);
    }

    /**
     * Change password (POST /student/change-password)
     * Menerima: old_password, new_password
     * NOTE: tidak menggunakan 'confirmed' supaya kompatibel dengan client yang hanya mengirim old/new.
     */
    public function changePassword(Request $request)
    {
        $student = $request->user();

        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6',
        ]);

        $old = $request->input('old_password');
        $new = $request->input('new_password');

        if (!Hash::check($old, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai'
            ], 422);
        }

        $student->password = Hash::make($new);
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah'
        ]);
    }

    /**
     * Delete photo (DELETE /student/photo)
     */
    public function deletePhoto(Request $request)
    {
        $student = $request->user();

        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->photo = null;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus',
            'data' => [
                'photo' => null,
            ],
        ]);
    }

    /**
     * Logout
     * POST /student/logout
     */
    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken();
        if ($token) {
            $token->delete();
        }

        return response()->json(['success' => true, 'message' => 'Logout berhasil']);
    }
}
