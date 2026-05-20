<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Seeder untuk data kuis lengkap dengan berbagai tipe dan model soal.
     *
     * Tipe Kuis:
     * - UH (Ulangan Harian) — 10 soal pilihan ganda
     * - PTS (Penilaian Tengah Semester) — 15 soal pilihan ganda
     * - AKM (Asesmen Kompetensi Minimum) — 12 soal berbagai model
     *
     * Model Soal (hanya untuk AKM):
     * 1 = Pilihan Ganda (single choice)
     * 2 = Pilihan Ganda Banyak (multiple choice)
     * 3 = Pernyataan (true/false)
     * 4 = Isian (fill in the blank)
     * 5 = Uraian (essay)
     * 6 = Iya Tidak (yes/no)
     * 7 = Argumen (essay with reasoning)
     */
    public function run(): void
    {
        // =====================================================
        // EXERCISES (Kuis)
        // =====================================================
        DB::table('exercises')->insert([
            // UH Matematika — 10 soal pilihan ganda, 30 menit
            [
                'id' => 10,
                'lesson_id' => 1, // Matematika
                'serial_id' => 1,
                'exercise_type_id' => 1, // UH
                'title' => 'Ulangan Harian Matematika - Bilangan Bulat',
                'time_limit' => 30, // 30 menit
                'is_admin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // PTS Bahasa Indonesia — 15 soal pilihan ganda, 45 menit
            [
                'id' => 11,
                'lesson_id' => 2, // Bahasa Indonesia
                'serial_id' => 1,
                'exercise_type_id' => 2, // PTS
                'title' => 'PTS Bahasa Indonesia - Tata Bahasa',
                'time_limit' => 45, // 45 menit
                'is_admin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // AKM Literasi — 12 soal berbagai model, 60 menit
            [
                'id' => 12,
                'lesson_id' => 2, // Bahasa Indonesia
                'serial_id' => 1,
                'exercise_type_id' => 4, // AKM
                'title' => 'AKM Literasi - Membaca dan Memahami Teks',
                'time_limit' => 60, // 60 menit
                'is_admin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =====================================================
        // EXERCISE ITEMS — UH Matematika (10 soal pilihan ganda)
        // =====================================================
        $uhMathQuestions = [
            [
                'question' => 'Hasil dari 15 + 28 adalah...',
                'options' => ['40', '41', '42', '43'],
                'answer' => 'c', // 43
            ],
            [
                'question' => 'Hasil dari 56 - 19 adalah...',
                'options' => ['35', '36', '37', '38'],
                'answer' => 'c', // 37
            ],
            [
                'question' => 'Hasil dari 12 × 7 adalah...',
                'options' => ['82', '83', '84', '85'],
                'answer' => 'c', // 84
            ],
            [
                'question' => 'Hasil dari 144 ÷ 12 adalah...',
                'options' => ['10', '11', '12', '13'],
                'answer' => 'c', // 12
            ],
            [
                'question' => 'Bilangan prima antara 10 dan 20 adalah...',
                'options' => ['11, 13, 15, 17', '11, 13, 17, 19', '10, 13, 17, 19', '11, 15, 17, 19'],
                'answer' => 'b', // 11, 13, 17, 19
            ],
            [
                'question' => 'Faktor dari 24 adalah...',
                'options' => ['1, 2, 3, 4, 6, 8, 12, 24', '1, 2, 4, 6, 12, 24', '2, 3, 4, 6, 8, 12', '1, 3, 6, 8, 12, 24'],
                'answer' => 'a',
            ],
            [
                'question' => 'KPK dari 6 dan 8 adalah...',
                'options' => ['12', '18', '24', '48'],
                'answer' => 'c', // 24
            ],
            [
                'question' => 'FPB dari 18 dan 24 adalah...',
                'options' => ['3', '6', '9', '12'],
                'answer' => 'b', // 6
            ],
            [
                'question' => 'Hasil dari (-5) + 12 adalah...',
                'options' => ['5', '6', '7', '8'],
                'answer' => 'c', // 7
            ],
            [
                'question' => 'Hasil dari 8 × (-3) adalah...',
                'options' => ['-21', '-22', '-23', '-24'],
                'answer' => 'd', // -24
            ],
        ];

        foreach ($uhMathQuestions as $index => $q) {
            DB::table('exercise_items')->insert([
                'exercise_id' => 10,
                'exercise_type_id' => 1, // UH
                'exercise_model_id' => 1, // Pilihan Ganda
                'exercise_choice' => 4, // 4 pilihan
                'exercise_number' => $index + 1,
                'question' => $q['question'],
                'selection' => json_encode($q['options']),
                'answer' => $q['answer'],
                'is_user' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =====================================================
        // EXERCISE ITEMS — PTS Bahasa Indonesia (15 soal pilihan ganda)
        // =====================================================

        $ptsBahasaQuestions = [
            [
                'question' => 'Kata baku dari "apotek" adalah...',
                'options' => ['apotik', 'apotex', 'apotek', 'apothek'],
                'answer' => 'c',
            ],
            [
                'question' => 'Sinonim dari kata "rajin" adalah...',
                'options' => ['malas', 'tekun', 'lambat', 'cepat'],
                'answer' => 'b',
            ],
            [
                'question' => 'Antonim dari kata "gelap" adalah...',
                'options' => ['terang', 'redup', 'suram', 'buram'],
                'answer' => 'a',
            ],
            [
                'question' => 'Kalimat yang menggunakan kata baku adalah...',
                'options' => ['Saya sudah makan nasi goreng', 'Aku udah makan nasi goreng', 'Gue udah makan nasgor', 'Saya udah makan nasi goreng'],
                'answer' => 'a',
            ],
            [
                'question' => 'Jenis kata "berlari" adalah...',
                'options' => ['kata benda', 'kata kerja', 'kata sifat', 'kata keterangan'],
                'answer' => 'b',
            ],
            [
                'question' => 'Kata "fotokopi" termasuk kata...',
                'options' => ['kata baku', 'kata tidak baku', 'kata serapan', 'kata asing'],
                'answer' => 'a',
            ],
            [
                'question' => 'Imbuhan "ber-" pada kata "berlari" berfungsi sebagai...',
                'options' => ['awalan', 'akhiran', 'sisipan', 'konfiks'],
                'answer' => 'a',
            ],
            [
                'question' => 'Kalimat efektif adalah kalimat yang...',
                'options' => ['panjang dan rumit', 'singkat dan jelas', 'menggunakan kata asing', 'tidak menggunakan tanda baca'],
                'answer' => 'b',
            ],
            [
                'question' => 'Kata "sistem" merupakan kata...',
                'options' => ['kata baku', 'kata tidak baku', 'kata slang', 'kata gaul'],
                'answer' => 'a',
            ],
            [
                'question' => 'Tanda baca yang tepat untuk kalimat tanya adalah...',
                'options' => ['titik (.)', 'koma (,)', 'tanda tanya (?)', 'seru (!)'],
                'answer' => 'c',
            ],
            [
                'question' => 'Kata "cantik" termasuk jenis kata...',
                'options' => ['kata benda', 'kata kerja', 'kata sifat', 'kata keterangan'],
                'answer' => 'c',
            ],
            [
                'question' => 'Kata baku dari "nggak" adalah...',
                'options' => ['tidak', 'enggak', 'gak', 'ndak'],
                'answer' => 'a',
            ],
            [
                'question' => 'Kalimat "Saya pergi ke sekolah" termasuk kalimat...',
                'options' => ['kalimat majemuk', 'kalimat tunggal', 'kalimat kompleks', 'kalimat tanya'],
                'answer' => 'b',
            ],
            [
                'question' => 'Huruf kapital digunakan pada...',
                'options' => ['awal kalimat', 'tengah kalimat', 'akhir kalimat', 'semua kata'],
                'answer' => 'a',
            ],
            [
                'question' => 'Kata "meja" termasuk jenis kata...',
                'options' => ['kata benda', 'kata kerja', 'kata sifat', 'kata keterangan'],
                'answer' => 'a',
            ],
        ];

        foreach ($ptsBahasaQuestions as $index => $q) {
            DB::table('exercise_items')->insert([
                'exercise_id' => 11,
                'exercise_type_id' => 2, // PTS
                'exercise_model_id' => 1, // Pilihan Ganda
                'exercise_choice' => 4, // 4 pilihan
                'exercise_number' => $index + 1,
                'question' => $q['question'],
                'selection' => json_encode($q['options']),
                'answer' => $q['answer'],
                'is_user' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // =====================================================
        // EXERCISE ITEMS — AKM Literasi (12 soal berbagai model)
        // =====================================================

        $akmQuestions = [
            // Soal 1-4: Pilihan Ganda
            [
                'model' => 1,
                'question' => 'Bacalah teks berikut!\n\n"Andi adalah siswa yang rajin. Setiap hari ia bangun pagi untuk belajar sebelum berangkat ke sekolah. Ia juga selalu mengerjakan PR tepat waktu."\n\nApa yang dapat disimpulkan tentang Andi?',
                'options' => ['Andi adalah siswa yang malas', 'Andi adalah siswa yang disiplin', 'Andi tidak suka sekolah', 'Andi sering terlambat'],
                'answer' => 'b',
            ],
            [
                'model' => 1,
                'question' => 'Ide pokok dari paragraf di atas adalah...',
                'options' => ['Andi bangun pagi', 'Andi pergi ke sekolah', 'Andi adalah siswa yang rajin', 'Andi mengerjakan PR'],
                'answer' => 'c',
            ],
            [
                'model' => 1,
                'question' => 'Tujuan penulis menulis teks tersebut adalah...',
                'options' => ['Menghibur pembaca', 'Memberikan contoh siswa yang baik', 'Menceritakan kegiatan Andi', 'Mengajak pembaca bangun pagi'],
                'answer' => 'b',
            ],
            [
                'model' => 1,
                'question' => 'Kata "rajin" dalam teks memiliki makna...',
                'options' => ['malas', 'tekun', 'cepat', 'lambat'],
                'answer' => 'b',
            ],

            // Soal 5-6: Pilihan Ganda Banyak
            [
                'model' => 2,
                'question' => 'Pilih semua pernyataan yang sesuai dengan teks tentang Andi!',
                'options' => ['Andi bangun pagi', 'Andi malas belajar', 'Andi mengerjakan PR tepat waktu', 'Andi sering terlambat', 'Andi belajar sebelum sekolah'],
                'answer' => 'a,c,e',
            ],
            [
                'model' => 2,
                'question' => 'Pilih semua kegiatan positif yang dilakukan Andi!',
                'options' => ['Bangun pagi', 'Bermain game', 'Belajar', 'Mengerjakan PR', 'Menonton TV'],
                'answer' => 'a,c,d',
            ],

            // Soal 7-8: Pernyataan
            [
                'model' => 3,
                'question' => 'Andi adalah contoh siswa yang baik.',
                'options' => ['Benar', 'Salah'],
                'answer' => 'true',
            ],
            [
                'model' => 3,
                'question' => 'Andi sering terlambat ke sekolah.',
                'options' => ['Benar', 'Salah'],
                'answer' => 'false',
            ],

            // Soal 9-10: Isian
            [
                'model' => 4,
                'question' => 'Andi bangun pagi untuk ... sebelum berangkat ke sekolah.',
                'options' => [],
                'answer' => 'belajar',
            ],
            [
                'model' => 4,
                'question' => 'Sikap Andi yang patut ditiru adalah...',
                'options' => [],
                'answer' => 'rajin',
            ],

            // Soal 11-12: Uraian (Essay)
            [
                'model' => 5,
                'question' => 'Jelaskan mengapa Andi dapat dikatakan sebagai siswa yang rajin! (Minimal 3 kalimat)',
                'options' => [],
                'answer' => 'Andi dapat dikatakan rajin karena ia bangun pagi untuk belajar. Ia juga selalu mengerjakan PR tepat waktu. Kebiasaan baik ini menunjukkan kedisiplinan dan tanggung jawab Andi sebagai siswa.',
            ],
            [
                'model' => 5,
                'question' => 'Apa yang dapat kamu pelajari dari kebiasaan Andi? Jelaskan pendapatmu!',
                'options' => [],
                'answer' => 'Dari kebiasaan Andi, saya belajar bahwa kedisiplinan dan kerja keras sangat penting untuk meraih kesuksesan. Bangun pagi dan belajar secara teratur dapat membantu kita memahami pelajaran dengan lebih baik.',
            ],
        ];

        foreach ($akmQuestions as $index => $q) {
            DB::table('exercise_items')->insert([
                'exercise_id' => 12,
                'exercise_type_id' => 4, // AKM
                'exercise_model_id' => $q['model'],
                'exercise_choice' => count($q['options']),
                'exercise_number' => $index + 1,
                'question' => $q['question'],
                'selection' => !empty($q['options']) ? json_encode($q['options']) : null,
                'answer' => $q['answer'],
                'is_user' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
