# LMS SCI Media — Backend (Laravel 12)

REST API backend untuk aplikasi LMS SCI Media Online. Dibangun dengan **Laravel 12**, **PHP 8.2+**, dan **MySQL**.

---

## Prasyarat

| Tool | Versi Minimum |
|------|--------------|
| PHP | 8.2 |
| Composer | 2.x |
| MySQL | 5.7 / 8.0 |
| Node.js | 18+ (untuk asset build) |

> Disarankan menggunakan **Laragon** (Windows) atau **Herd** untuk development lokal.

---

## Instalasi Lokal (Development)

### 1. Clone repository

```bash
git clone https://github.com/Ayikkkk/LMS_SCI_BACKEND.git
cd LMS_SCI_BACKEND
```

### 2. Install dependensi PHP

```bash
composer install
```

### 3. Buat file environment

```bash
cp .env.example .env
```

Edit `.env` sesuai konfigurasi lokal:

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://192.168.1.x:8000   # Sesuaikan dengan IP lokal Anda

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lmssiswa_db
DB_USERNAME=root
DB_PASSWORD=

# Database terpisah untuk logging quiz activity
DB_LOG_HOST=127.0.0.1
DB_LOG_PORT=3306
DB_LOG_DATABASE=lmssiswa_log
DB_LOG_USERNAME=root
DB_LOG_PASSWORD=

APP_TIMEZONE=Asia/Jakarta
SANCTUM_TOKEN_EXPIRATION=10080
JITSI_DOMAIN=https://meet.jit.si
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Buat database

Buat dua database di MySQL:
- `lmssiswa_db` — database utama
- `lmssiswa_log` — database khusus log aktivitas quiz

```sql
CREATE DATABASE lmssiswa_db;
CREATE DATABASE lmssiswa_log;
```

### 6. Jalankan migrasi

```bash
php artisan migrate
```

Migrasi database log:

```bash
php artisan migrate --database=mysql_log --path=database/migrations/log
```

### 7. (Opsional) Jalankan seeder

```bash
php artisan db:seed
```

### 8. Buat symlink storage

```bash
php artisan storage:link
```

### 9. Jalankan server lokal

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

> Gunakan `--host=0.0.0.0` agar bisa diakses dari HP di jaringan yang sama saat testing Flutter.

---

## Struktur Database

| Database | Fungsi |
|----------|--------|
| `lmssiswa_db` | Data utama: siswa, kelas, materi, tugas, quiz, nilai |
| `lmssiswa_log` | Log aktivitas quiz (event: start, submit, suspicious) |

---

## Endpoint Utama

Base URL: `{APP_URL}/api/`

| Grup | Prefix | Keterangan |
|------|--------|------------|
| Auth | `/student/login` | Login siswa |
| Dashboard | `/student/dashboard` | Data dashboard |
| Materi | `/student/materials` | Daftar materi |
| Tugas | `/student/assignments` | Daftar & detail tugas |
| Submit Tugas | `/student/submit-task` | Pengumpulan tugas |
| Quiz | `/student/exercises` | Daftar & kerjakan quiz |
| Online Meeting | `/student/meetings` | Kelas online Jitsi |
| Nilai | `/student/grades/rekap-mapel` | Rekap nilai per mapel |
| Laporan Harian | `/student/reports` | Laporan harian siswa |
| Proxy Gambar | `/api/proxy-image` | Proxy gambar soal eksternal |

Semua endpoint (kecuali login) memerlukan header:
```
Authorization: Bearer {token}
```

---

## Deployment ke VPS (Production)

### 1. Pull & install

```bash
cd /var/www/Backend_Siswa
git pull
composer install --no-dev --optimize-autoloader
```

### 2. Buat/update `.env` production

Gunakan template yang tersedia:

```bash
cp .env.production.no-redis .env
# Edit sesuai konfigurasi VPS Anda
```

### 3. Jalankan migrasi

```bash
php artisan migrate --force
php artisan migrate --database=mysql_log --path=database/migrations/log --force
```

### 4. Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan storage:link
```

### 5. Setup cron (scheduler)

Tambahkan ke crontab (`crontab -e`):

```
* * * * * cd /var/www/Backend_Siswa && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled jobs yang berjalan otomatis:
- `02:00` — Hapus quiz activity log > 30 hari
- `03:00` — Hapus cache gambar proxy > 30 hari

### 6. Buat folder cache gambar proxy

```bash
mkdir -p storage/app/proxy-images
chmod 775 storage/app/proxy-images
```

---

## Konfigurasi Tambahan

### Sanctum (Token Auth)

Token berlaku 7 hari (10080 menit). Dikonfigurasi di `.env`:
```
SANCTUM_TOKEN_EXPIRATION=10080
```

### Jitsi (Online Meeting)

```
JITSI_DOMAIN=https://meet.jit.si
```

### Image Proxy Internal Routing

Jika VPS tidak bisa akses domain publiknya sendiri via port 443, konfigurasi di `routes/api.php`:
- `tak-scimediaonline.my.id` → di-fetch via `http://127.0.0.1:30080` (internal)

---

## Tech Stack

- **Framework**: Laravel 12
- **Auth**: Laravel Sanctum
- **PDF**: barryvdh/laravel-dompdf
- **Database**: MySQL (dua database terpisah)
- **File Storage**: Laravel Storage (public disk)
- **Scheduler**: Laravel Task Scheduling
