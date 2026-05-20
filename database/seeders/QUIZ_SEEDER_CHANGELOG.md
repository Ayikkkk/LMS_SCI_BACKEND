# Quiz Seeder — Changelog

## Perubahan Terbaru (19 Mei 2026)

### ✅ Yang Diubah

**PTS Bahasa Indonesia (ID: 11)** — Sekarang hanya menggunakan **pilihan ganda**

- **Sebelumnya:** 15 soal campuran (pilihan ganda, pilihan ganda banyak, pernyataan, isian, iya tidak)
- **Sekarang:** 15 soal pilihan ganda saja
- **Alasan:** Berbagai model soal hanya untuk AKM, PTS cukup pilihan ganda standar

### 📋 Struktur Kuis Sekarang

| ID | Tipe | Judul | Jumlah Soal | Model Soal | Time Limit |
|----|------|-------|-------------|------------|------------|
| 10 | UH | Ulangan Harian Matematika | 10 soal | Pilihan Ganda | 30 menit |
| 11 | PTS | PTS Bahasa Indonesia | 15 soal | **Pilihan Ganda** | 45 menit |
| 12 | AKM | AKM Literasi | 12 soal | **Berbagai Model** | 60 menit |

### 🎯 Model Soal per Kuis

#### 1. UH Matematika (ID: 10)
- ✅ Pilihan Ganda (model 1) — 10 soal

#### 2. PTS Bahasa Indonesia (ID: 11) — **UPDATED**
- ✅ Pilihan Ganda (model 1) — 15 soal

**Topik soal PTS:**
1. Kata baku dari "apotek"
2. Sinonim dari kata "rajin"
3. Antonim dari kata "gelap"
4. Kalimat yang menggunakan kata baku
5. Jenis kata "berlari"
6. Kata "fotokopi" termasuk kata...
7. Imbuhan "ber-" pada kata "berlari"
8. Kalimat efektif adalah...
9. Kata "sistem" merupakan kata...
10. Tanda baca untuk kalimat tanya
11. Kata "cantik" termasuk jenis kata...
12. Kata baku dari "nggak"
13. Kalimat "Saya pergi ke sekolah" termasuk...
14. Huruf kapital digunakan pada...
15. Kata "meja" termasuk jenis kata...

#### 3. AKM Literasi (ID: 12) — Tidak Berubah
- ✅ Pilihan Ganda (model 1) — 4 soal
- ✅ Pilihan Ganda Banyak (model 2) — 2 soal
- ✅ Pernyataan True/False (model 3) — 2 soal
- ✅ Isian (model 4) — 2 soal
- ✅ Uraian/Essay (model 5) — 2 soal

---

## 🚀 Cara Menggunakan Seeder yang Sudah Diupdate

### 1. Hapus Data Lama (Jika Sudah Pernah Run Seeder)

```bash
php artisan tinker
```

```php
// Hapus soal PTS lama
DB::table('exercise_items')->where('exercise_id', 11)->delete();

// Hapus kuis PTS lama
DB::table('exercises')->where('id', 11)->delete();

// Exit tinker
exit
```

### 2. Jalankan Seeder Baru

```bash
php artisan db:seed --class=QuizSeeder
```

### 3. Verifikasi Data

```sql
-- Cek jumlah soal PTS
SELECT exercise_id, COUNT(*) as total_soal
FROM exercise_items
WHERE exercise_id = 11;
-- Expected: 15 soal

-- Cek model soal PTS (harus semua model 1)
SELECT exercise_model_id, COUNT(*) as jumlah
FROM exercise_items
WHERE exercise_id = 11
GROUP BY exercise_model_id;
-- Expected: model_id = 1, jumlah = 15
```

---

## 📊 Perbandingan Sebelum vs Sesudah

### PTS Bahasa Indonesia (ID: 11)

| Aspek | Sebelumnya | Sekarang |
|-------|------------|----------|
| Total Soal | 15 soal | 15 soal ✅ |
| Model Soal | 5 model berbeda | 1 model (pilihan ganda) ✅ |
| Pilihan Ganda | 5 soal | 15 soal ✅ |
| Pilihan Ganda Banyak | 3 soal | 0 soal ❌ |
| Pernyataan | 3 soal | 0 soal ❌ |
| Isian | 2 soal | 0 soal ❌ |
| Iya Tidak | 2 soal | 0 soal ❌ |

---

## ✅ Keuntungan Perubahan Ini

1. **Lebih Sederhana** — PTS fokus pada pilihan ganda standar
2. **Lebih Cepat Dikerjakan** — Tidak perlu switch antara berbagai tipe soal
3. **Lebih Mudah Dinilai** — Auto-grading untuk semua soal PTS
4. **Konsisten dengan Standar** — UH dan PTS sama-sama pilihan ganda
5. **AKM Tetap Kompleks** — Berbagai model soal tetap ada di AKM untuk testing

---

## 🧪 Testing Checklist

Setelah run seeder baru, test di Flutter app:

- [ ] PTS Bahasa Indonesia tampil di daftar kuis
- [ ] PTS memiliki 15 soal
- [ ] Semua soal PTS adalah pilihan ganda (4 pilihan)
- [ ] Timer PTS 45 menit berjalan normal
- [ ] Submit PTS berhasil dan langsung dapat nilai (tidak pending review)
- [ ] AKM tetap memiliki berbagai model soal
- [ ] AKM dengan essay tetap trigger pending review

---

**Updated:** 19 Mei 2026
**Status:** ✅ Ready to use
