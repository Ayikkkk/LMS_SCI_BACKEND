# Deploy Backend API ke Railway — Quick Start

> File ini untuk teman yang akan deploy backend API Laravel ke Railway.

---

## 📦 File yang Sudah Disiapkan

- ✅ `Procfile` — Railway start command
- ✅ `nixpacks.toml` — Railway build configuration
- ✅ `.env.example` — Template environment variables
- ✅ Semua routes API di `routes/api.php`
- ✅ Semua controllers di `app/Http/Controllers/Student/`
- ✅ Semua models di `app/Models/`
- ✅ Semua migrations di `database/migrations/`

---

## 🚀 Langkah Deploy (5 Menit)

### 1. Buat Project di Railway
- Login https://railway.app
- New Project → Deploy from GitHub (atau Empty Project)
- Upload folder `lms-backend` ini

### 2. Tambahkan 2 Database MySQL
- Klik **New** → **Database** → **Add MySQL** (untuk database utama)
- Klik **New** → **Database** → **Add MySQL** lagi (untuk database log)

### 3. Set Environment Variables
Di Railway project → **Variables**, copy-paste ini:

```env
APP_NAME=LMS_BACKEND
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-backend.up.railway.app
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=mysql
DB_HOST=${{MYSQL_HOST}}
DB_PORT=${{MYSQL_PORT}}
DB_DATABASE=${{MYSQL_DATABASE}}
DB_USERNAME=${{MYSQL_USER}}
DB_PASSWORD=${{MYSQL_PASSWORD}}

DB_LOG_HOST=<copy dari MySQL service kedua>
DB_LOG_PORT=<copy dari MySQL service kedua>
DB_LOG_DATABASE=<copy dari MySQL service kedua>
DB_LOG_USERNAME=<copy dari MySQL service kedua>
DB_LOG_PASSWORD=<copy dari MySQL service kedua>

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
SANCTUM_TOKEN_EXPIRATION=10080
LOG_CHANNEL=stack
LOG_LEVEL=error
JITSI_DOMAIN=https://meet.jit.si
```

**Penting:** Untuk `DB_LOG_*`, copy manual dari kredensial MySQL service kedua di Railway dashboard.

### 4. Deploy & Tunggu Build Selesai
Railway akan otomatis:
- Install dependencies (`composer install`)
- Generate app key
- Cache config & routes

### 5. Jalankan Migration
Setelah deploy selesai, buka **Railway Terminal**:

```bash
# Migration database utama
php artisan migrate --force

# Migration database log (terpisah)
php artisan migrate --database=mysql_log --path=database/migrations/log --force

# Buat symlink storage
php artisan storage:link
```

### 6. Test API
Buka browser:
```
https://your-backend.up.railway.app/api/student/login
```
Harusnya return JSON error (bukan 404).

---

## 📋 Endpoint API yang Tersedia

### Auth
- `POST /api/student/login` — Login siswa
- `POST /api/student/logout` — Logout
- `GET /api/student/profile` — Profil siswa
- `PUT /api/student/profile` — Update profil
- `POST /api/student/change-password` — Ganti password

### Dashboard
- `GET /api/student/dashboard` — Statistik & tugas pending

### Materi & Tugas
- `GET /api/student/materials` — Daftar materi
- `GET /api/student/assignments` — Daftar tugas
- `GET /api/student/posts/{id}` — Detail materi/tugas
- `POST /api/student/submit-task` — Submit tugas

### Kuis
- `GET /api/student/exercise-lessons` — Daftar pelajaran yang punya kuis
- `GET /api/student/lesson/{lessonId}/exercises` — Daftar kuis per pelajaran
- `GET /api/student/exercises/{id}` — Soal kuis
- `POST /api/student/exercises/{id}/submit` — Submit jawaban kuis
- `GET /api/student/exercises/{id}/result` — Hasil kuis
- `POST /api/student/quiz/log` — Log aktivitas kuis

### Nilai
- `GET /api/student/grades/rekap-mapel` — Rekap nilai per mapel
- `GET /api/student/grades/rekap-mapel/pdf` — Download PDF rekap nilai

### Meeting Online
- `GET /api/student/meetings` — Daftar meeting
- `POST /api/student/meetings/{id}/join` — Join meeting
- `POST /api/student/meetings/{id}/leave` — Leave meeting

### Laporan Harian
- `GET /api/student/reports` — Daftar laporan
- `POST /api/student/reports` — Buat laporan baru
- `GET /api/student/reports/check/today` — Cek sudah lapor hari ini

Semua endpoint (kecuali login) butuh header:
```
Authorization: Bearer {token}
```

---

## 🔧 Troubleshooting

### Error: "No application encryption key"
```bash
php artisan key:generate --force
```

### Error: "Connection refused" (Database)
- Cek environment variables `DB_*` sudah benar
- Pastikan MySQL service running

### Error: "storage/logs/laravel.log could not be opened"
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### CORS Error
Sudah di-handle, `allowed_origins: ['*']` untuk testing.

---

## 📞 Setelah Deploy Berhasil

Kirim ke teman yang buat Flutter app:
1. ✅ URL backend: `https://your-backend.up.railway.app`
2. ✅ URL API: `https://your-backend.up.railway.app/api/`
3. ✅ Kredensial database (jika dia perlu akses langsung)

---

**Deploy selesai!** Backend siap dipakai untuk testing Flutter app.
