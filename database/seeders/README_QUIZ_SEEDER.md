# Quiz Seeder Documentation

> Seeder untuk data kuis lengkap dengan berbagai tipe dan model soal.

---

## 📋 Isi Seeder

### 3 Kuis yang Dibuat

| ID | Tipe | Judul | Jumlah Soal | Time Limit | Lesson |
|----|------|-------|-------------|------------|--------|
| 10 | UH (Ulangan Harian) | Ulangan Harian Matematika - Bilangan Bulat | 10 soal | 30 menit | Matematika |
| 11 | PTS (Penilaian Tengah Semester) | PTS Bahasa Indonesia - Tata Bahasa | 15 soal | 45 menit | Bahasa Indonesia |
| 12 | AKM (Asesmen Kompetensi Minimum) | AKM Literasi - Membaca dan Memahami Teks | 12 soal | 60 menit | Bahasa Indonesia |

---

## 🎯 Detail Soal per Kuis

### 1. UH Matematika (ID: 10)
**10 soal pilihan ganda, 30 menit**

- Model Soal: Pilihan Ganda (model_id: 1)
- Topik: Bilangan bulat, operasi hitung, KPK, FPB
- Contoh soal:
  - Hasil dari 15 + 28 adalah...
  - Bilangan prima antara 10 dan 20 adalah...
  - KPK dari 6 dan 8 adalah...

### 2. PTS Bahasa Indonesia (ID: 11)
**15 soal pilihan ganda, 45 menit**

- Model Soal: Pilihan Ganda (model_id: 1) — semua soal
- Topik: Kata baku, sinonim, antonim, jenis kata, tanda baca, imbuhan, kalimat efektif
- Contoh soal:
  - Kata baku dari "apotek" adalah...
  - Sinonim dari kata "rajin" adalah...
  - Antonim dari kata "gelap" adalah...
  - Jenis kata "berlari" adalah...
  - Kata baku dari "nggak" adalah...

### 3. AKM Literasi (ID: 12)
**12 soal berbagai model, 60 menit**

| Model Soal | Jumlah | Nomor Soal |
|------------|--------|------------|
| Pilihan Ganda (1) | 4 soal | 1-4 |
| Pilihan Ganda Banyak (2) | 2 soal | 5-6 |
| Pernyataan True/False (3) | 2 soal | 7-8 |
| Isian (4) | 2 soal | 9-10 |
| Uraian/Essay (5) | 2 soal | 11-12 |

- Topik: Membaca dan memahami teks tentang "Andi siswa rajin"
- Fokus: Pemahaman bacaan, ide pokok, kesimpulan, analisis
- Contoh soal:
  - Apa yang dapat disimpulkan tentang Andi?
  - Pilih semua pernyataan yang sesuai dengan teks!
  - Andi adalah contoh siswa yang baik. (Benar/Salah)
  - Jelaskan mengapa Andi dapat dikatakan sebagai siswa yang rajin! (Essay)

---

## 🚀 Cara Menggunakan

### 1. Jalankan Seeder

```bash
# Jalankan seeder QuizSeeder saja
php artisan db:seed --class=QuizSeeder

# Atau tambahkan ke DatabaseSeeder.php
```

### 2. Tambahkan ke DatabaseSeeder (Opsional)

Edit `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        LmsSeeder::class,
        QuizSeeder::class, // ← tambahkan ini
    ]);
}
```

Lalu jalankan:
```bash
php artisan db:seed
```

### 3. Jalankan di Railway (Production)

```bash
# Via Railway Terminal
php artisan db:seed --class=QuizSeeder --force
```

---

## 📊 Mapping Model Soal

| ID | Nama Model | Deskripsi | Flutter Type |
|----|------------|-----------|--------------|
| 1 | Pilihan Ganda | Single choice (radio button) | `multipleChoice` |
| 2 | Pilihan Ganda Banyak | Multiple choice (checkbox) | `multipleAnswer` |
| 3 | Pernyataan | True/False statement | `trueFalse` |
| 4 | Isian | Fill in the blank | `fillInTheBlank` |
| 5 | Uraian | Essay/long answer | `essay` |
| 6 | Iya Tidak | Yes/No question | `yesNo` |
| 7 | Argumen | Essay with reasoning | `essay` |

---

## 🧪 Testing Seeder

### 1. Cek Data Exercises
```sql
SELECT id, title, exercise_type_id, time_limit
FROM exercises
WHERE id IN (10, 11, 12);
```

Expected output:
```
+----+--------------------------------------------------+------------------+------------+
| id | title                                            | exercise_type_id | time_limit |
+----+--------------------------------------------------+------------------+------------+
| 10 | Ulangan Harian Matematika - Bilangan Bulat       |                1 |         30 |
| 11 | PTS Bahasa Indonesia - Tata Bahasa               |                2 |         45 |
| 12 | AKM Literasi - Membaca dan Memahami Teks         |                4 |         60 |
+----+--------------------------------------------------+------------------+------------+
```

### 2. Cek Jumlah Soal per Kuis
```sql
SELECT exercise_id, COUNT(*) as total_soal
FROM exercise_items
WHERE exercise_id IN (10, 11, 12)
GROUP BY exercise_id;
```

Expected output:
```
+-------------+------------+
| exercise_id | total_soal |
+-------------+------------+
|          10 |         10 |
|          11 |         15 |
|          12 |         12 |
+-------------+------------+
```

### 3. Cek Model Soal di AKM
```sql
SELECT exercise_model_id, COUNT(*) as jumlah
FROM exercise_items
WHERE exercise_id = 12
GROUP BY exercise_model_id;
```

Expected output:
```
+-------------------+--------+
| exercise_model_id | jumlah |
+-------------------+--------+
|                 1 |      4 |
|                 2 |      2 |
|                 3 |      2 |
|                 4 |      2 |
|                 5 |      2 |
+-------------------+--------+
```

---

## 🎨 Customisasi Seeder

### Menambah Kuis Baru

Edit `QuizSeeder.php`, tambahkan di array `exercises`:

```php
[
    'id' => 13,
    'lesson_id' => 1, // sesuaikan
    'serial_id' => 1,
    'exercise_type_id' => 3, // UAS
    'title' => 'Ujian Akhir Semester Matematika',
    'time_limit' => 90, // 90 menit
    'is_admin' => 1,
    'created_at' => now(),
    'updated_at' => now(),
],
```

### Menambah Soal

Tambahkan di array `$uhMathQuestions` (atau array soal lainnya):

```php
[
    'question' => 'Pertanyaan baru...',
    'options' => ['A', 'B', 'C', 'D'],
    'answer' => 'a',
],
```

---

## ⚠️ Catatan Penting

### 1. ID Exercises
Seeder ini menggunakan ID 10, 11, 12 untuk exercises. Pastikan ID ini belum dipakai di database.

### 2. Lesson ID
- `lesson_id = 1` → Matematika
- `lesson_id = 2` → Bahasa Indonesia

Sesuaikan dengan data `lessons` di database kamu.

### 3. Serial ID
Semua kuis menggunakan `serial_id = 1`. Sesuaikan dengan data `serials` di database.

### 4. Format Jawaban

| Model | Format Jawaban | Contoh |
|-------|----------------|--------|
| Pilihan Ganda | `a`, `b`, `c`, `d` | `'c'` |
| Pilihan Ganda Banyak | `a,c,e` (comma-separated) | `'a,c,e'` |
| Pernyataan | `true` atau `false` | `'true'` |
| Isian | Text jawaban | `'Jakarta'` |
| Uraian | Text panjang | `'Penjelasan...'` |
| Iya Tidak | `yes` atau `no` | `'yes'` |

### 5. AKM Perlu Review Manual
Soal AKM (ID: 12) menggunakan model Uraian (essay) yang perlu penilaian manual oleh guru. Backend akan set `is_pending_review = true` untuk tipe AKM.

---

## 🔄 Reset Data (Jika Perlu)

```bash
# Hapus data kuis yang dibuat seeder ini
php artisan tinker
>>> DB::table('exercise_items')->whereIn('exercise_id', [10, 11, 12])->delete();
>>> DB::table('exercises')->whereIn('id', [10, 11, 12])->delete();
```

Lalu jalankan seeder lagi:
```bash
php artisan db:seed --class=QuizSeeder
```

---

**Created:** 19 Mei 2026
**Status:** ✅ Ready to use
