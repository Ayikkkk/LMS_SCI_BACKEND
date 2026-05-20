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
     * Generate full photo URL, selalu pakai https://.
     * Railway dan hosting modern menggunakan reverse proxy — request internal
     * bisa masuk sebagai http:// meskipun user akses via https://.
     * Solusi: paksa https:// dari APP_URL, bukan dari request host.
     */
    private function photoUrl(?string $path, Request $request): ?string
    {
        if (!$path) return null;

        // Ambil base URL dari APP_URL (sudah di-set ke https di Railway)
        // Fallback ke request host jika APP_URL belum di-set
        $appUrl = (string) rtrim((string) config('app.url', ''), '/');

        // Jika APP_URL masih default atau localhost, gunakan request host
        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $appUrl = $request->getSchemeAndHttpHost();
        }

        // Paksa https:// — Railway selalu HTTPS dari sisi user
        $appUrl = str_replace('http://', 'https://', (string) $appUrl);

        // Legacy data: path sudah berupa full URL → ambil path-nya saja
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $storagePath = parse_url($path, PHP_URL_PATH); // e.g. /storage/students/xxx.jpg
            return $appUrl . $storagePath;
        }

        // Normal case: relative path seperti "students/xxx.jpg"
        return $appUrl . '/storage/' . $path;
    }

    /**
     * LOGIN — POST /student/login
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
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah',
            ], 401);
        }

        $expiresAt = now()->addDays(7);
        $token = $student->createToken(
            'student_token',
            ['*'],
            $expiresAt
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'student' => [
                'id'        => $student->id,
                'name'      => $student->name,
                'username'  => $student->username,
                'email'     => $student->email,
                'phone'     => $student->phone ?? $student->telephone ?? null,
                'nis'       => $student->nis,
                'className' => optional($student->classroom)->name,
                'photo'     => $this->photoUrl($student->photo, $request),
                'guru'      => $student->guru ? [
                    'id'    => $student->guru->id,
                    'name'  => $student->guru->name,
                    'email' => $student->guru->email,
                    'phone' => $student->guru->phone,
                ] : null,
            ],
        ]);
    }

    /**
     * PROFILE — GET /student/profile
     */
    public function profile(Request $request)
    {
        $student = $request->user()
            ->load(['classroom', 'guru:id,name,email,phone']);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $student->id,
                'name'         => $student->name,
                'username'     => $student->username,
                'email'        => $student->email,
                'phone'        => $student->phone ?? $student->telephone ?? null,
                'nis'          => $student->nis,
                'absen_number' => $student->absen_number,
                'className'    => optional($student->classroom)->name,
                'photo'        => $this->photoUrl($student->photo, $request),
                'guru'         => $student->guru ? [
                    'id'    => $student->guru->id,
                    'name'  => $student->guru->name,
                    'email' => $student->guru->email,
                    'phone' => $student->guru->phone,
                ] : null,
            ],
        ]);
    }

    /**
     * UPDATE PROFILE — PUT /student/profile
     */
    public function updateProfile(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'name'  => 'sometimes|nullable|string|max:255',
            'email' => [
                'sometimes', 'nullable', 'email', 'max:255',
                Rule::unique('students', 'email')->ignore($student->id),
            ],
            'phone' => 'sometimes|nullable|string|max:50',
            'photo' => 'sometimes|nullable|image|max:5120',
        ]);

        if (empty($validated) && !$request->hasFile('photo')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data untuk diperbarui',
            ], 400);
        }

        $student->fill($validated);

        if ($request->hasFile('photo')) {
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            $filename = Str::random(40) . '.' .
                $request->file('photo')->getClientOriginalExtension();

            $student->photo = $request->file('photo')
                ->storeAs('students', $filename, 'public');
        }

        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
            'data'    => [
                'name'  => $student->name,
                'email' => $student->email,
                'phone' => $student->phone,
                'photo' => $this->photoUrl($student->photo, $request),
            ],
        ]);
    }

    /**
     * CHANGE PASSWORD — POST /student/change-password
     */
    public function changePassword(Request $request)
    {
        $student = $request->user();

        $request->validate([
            'current_password' => 'required|string',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai',
            ], 422);
        }

        $student->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah',
        ]);
    }

    /**
     * DELETE PHOTO — DELETE /student/photo
     */
    public function deletePhoto(Request $request)
    {
        $student = $request->user();

        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->update(['photo' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus',
            'data'    => ['photo' => null],
        ]);
    }

    /**
     * LOGOUT — POST /student/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
