-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 10, 2026 at 07:09 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lmssiswa`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` tinyint(1) NOT NULL,
  `date_in` varchar(50) DEFAULT NULL,
  `position` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `img` varchar(100) DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `username`, `password`, `role`, `date_in`, `position`, `phone`, `img`, `login_at`, `created_at`, `updated_at`) VALUES
(1, 'Fahri Kurniawan', 'fahri', '$2y$10$cP/SinrmMHi5ZxApNXHeL.RjMod8ikpB63QJ/MGdCGTo28ZmX6t7O', 7, '2025-10-13', 'Administrator', '08467377832', NULL, '2025-12-18 12:46:10', '2025-12-18 12:46:10', '2025-12-18 12:47:44');

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classrooms`
--

INSERT INTO `classrooms` (`id`, `serial_id`, `name`, `grade`, `code`, `created_at`, `updated_at`) VALUES
(1, 1, '5A', 'V', 'CLS5A2025', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(2, 1, '5B', 'V', 'CLASS5B2025', '2025-11-26 16:31:41', '2025-11-26 16:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `competences`
--

CREATE TABLE `competences` (
  `id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `mapel_id` int(10) UNSIGNED NOT NULL,
  `point` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competences`
--

INSERT INTO `competences` (`id`, `lesson_id`, `mapel_id`, `point`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '3.1', 'Menjelaskan bilangan bulat dan operasi hitung sederhana.', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(2, 2, 3, '3.1', 'Siswa mampu membaca dengan lancar', '2025-12-09 06:21:04', '2025-12-09 06:21:04');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED DEFAULT NULL,
  `exercise_type_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `lesson_id`, `serial_id`, `exercise_type_id`, `title`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Latihan Bilangan Bulat', 1, '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(2, 2, 1, 1, 'Pengenalan Kata Kerja', 1, '2025-12-09 06:22:43', '2025-12-09 06:22:43'),
(3, 1, 1, 2, 'Latihan Bilangan Kuadrat', 1, '2025-12-09 08:08:24', '2025-12-09 08:08:24'),
(4, 2, 1, 2, 'Macam Kosa Kata', 1, '2025-12-18 12:41:12', '2025-12-18 12:41:12'),
(5, 2, 1, 1, 'Ulangan Kata Dasar', 1, '2026-01-02 16:30:01', '2026-01-02 16:30:01');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_items`
--

CREATE TABLE `exercise_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `competence_id` int(10) UNSIGNED DEFAULT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `exercise_type_id` int(10) UNSIGNED NOT NULL,
  `exercise_model_id` int(10) UNSIGNED NOT NULL,
  `exercise_choice` tinyint NOT NULL,
  `exercise_number` int NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `selection` text COLLATE utf8mb4_unicode_ci,
  `answer` text COLLATE utf8mb4_unicode_ci,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_items`
--

INSERT INTO `exercise_items` (`id`, `admin_id`, `user_id`, `competence_id`, `exercise_id`, `exercise_type_id`, `exercise_model_id`, `exercise_choice`, `exercise_number`, `question`, `selection`, `answer`, `is_user`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, 1, 1, 1, 4, 1, 'Hasil dari 25 + (-10) adalah ...', '[\"<p>15<\\/p>\",\"<p>100<\\/p>\",\"<p>1<\\/p>\",\"<p>-1<\\/p>\"]', '[\"a\"]', 0, '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(2, 1, NULL, 1, 1, 1, 1, 4, 1, '\"<p>satu kuadrat berapa ...<\\/p>\"', '[\"<p>10<\\/p>\",\"<p>100<\\/p>\",\"<p>1<\\/p>\",\"<p>-1<\\/p>\"]', '[\"c\"]', 0, '2021-11-10 19:29:06', '2022-03-14 20:57:31'),
(3, NULL, 1, 2, 2, 1, 1, 4, 1, '\"<p>Berikut ini yang merupakan keberagaman pada masyarakat Indonesia adalah <\\/p>\"', '[\"<p>Suku, agama, jenis kelamin, dan tempat tinggal<\\/p>\",\"<p>Suku, budaya, dan hak<\\/p>\",\"<p>Suku, agama, ras, budaya, dan jenis kelamin<\\/p>\",\"<p>Suku, agama, dan hukum<\\/p>\"]', '[\"c\"]', 1, '2021-11-10 20:23:22', '2021-11-10 20:23:22'),
(4, 1, NULL, 1, 4, 2, 1, 4, 1, 'Apakah yang dimaksud kosa kata', '[\"<p>Suku, agama, jenis kelamin, dan tempat tinggal<\\/p>\",\"<p>Suku, budaya, dan hak<\\/p>\",\"<p>Suku, agama, ras, budaya, dan jenis kelamin<\\/p>\",\"<p>Suku, agama, dan hukum<\\/p>\"]', '[\"c\"]', 0, '2025-12-18 13:09:42', '2025-12-18 13:09:42'),
(5, 1, NULL, 2, 4, 2, 1, 4, 2, 'Kosa kata adalah', '[\"<p>Suku, agama, jenis kelamin, dan tempat tinggal<\\/p>\",\"<p>Suku, budaya, dan hak<\\/p>\",\"<p>Suku, agama, ras, budaya, dan jenis kelamin<\\/p>\",\"<p>Suku, agama, dan hukum<\\/p>\"]', '[\"b\"]', 0, '2025-12-18 13:33:17', '2025-12-18 13:33:17'),
(6, 1, NULL, 2, 4, 2, 1, 4, 4, 'Keberagaman budaya di Indonesia dapat memperkuat persatuan karena', '[\"<p>Mendorong sikap saling menghargai antar masyarakat</p>\",\"<p>Menimbulkan perpecahan antar suku</p>\",\"<p>Menghilangkan identitas daerah</p>\",\"<p>Membuat masyarakat hidup sendiri-sendiri</p>\"]', '[\"a\"]', 0, '2025-12-18 13:40:00', '2025-12-18 13:40:00'),
(7, 1, NULL, 2, 4, 2, 1, 4, 5, 'Sikap yang mencerminkan toleransi dalam kehidupan sehari-hari adalah', '[\"<p>Menghargai perbedaan pendapat orang lain</p>\",\"<p>Memaksakan pendapat pribadi</p>\",\"<p>Menghindari kerja sama</p>\",\"<p>Menolak perbedaan budaya</p>\"]', '[\"a\"]', 0, '2025-12-18 13:41:00', '2025-12-18 13:41:00'),
(8, 1, NULL, 2, 5, 1, 1, 4, 1, 'Apa ibu kota Indonesia?', '[\"<p>Jakarta</p>\",\"<p>Medan</p>\",\"<p>Yogyakarta</p>\",\"<p>Bandung</p>\"]', '[\"a\"]', 0, '2026-01-02 16:37:50', '2026-01-02 16:37:50'),
(9, 1, NULL, 2, 5, 1, 1, 4, 2, 'Kalimat yang digunakan untuk menanyakan sesuatu disebut kalimat?', '[\"<p>Perintah</p>\",\"<p>Berita</p>\",\"<p>Tanya</p>\",\"<p>Seruan</p>\"]', '[\"c\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(10, 1, NULL, 2, 5, 1, 1, 4, 3, 'Tanda baca yang digunakan di akhir kalimat tanya adalah?', '[\"<p>Titik (.)</p>\",\"<p>Koma (,)</p>\",\"<p>Tanda seru (!)</p>\",\"<p>Tanda tanya (?)</p>\"]', '[\"d\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(11, 1, NULL, 2, 5, 1, 1, 4, 4, 'Antonim dari kata “besar” adalah?', '[\"<p>Panjang</p>\",\"<p>Tinggi</p>\",\"<p>Kecil</p>\",\"<p>Lebar</p>\"]', '[\"c\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(12, 1, NULL, 2, 5, 1, 1, 4, 5, 'Sinonim dari kata “pandai” adalah?', '[\"<p>Bodoh</p>\",\"<p>Cerdas</p>\",\"<p>Malas</p>\",\"<p>Lemah</p>\"]', '[\"b\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(13, 1, NULL, 2, 5, 1, 1, 4, 6, 'Kalimat yang berisi perintah disebut kalimat?', '[\"<p>Tanya</p>\",\"<p>Berita</p>\",\"<p>Perintah</p>\",\"<p>Seruan</p>\"]', '[\"c\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(14, 1, NULL, 2, 5, 1, 1, 4, 7, 'Cerita yang bersifat khayalan disebut?', '[\"<p>Dongeng</p>\",\"<p>Laporan</p>\",\"<p>Berita</p>\",\"<p>Pengumuman</p>\"]', '[\"a\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(15, 1, NULL, 2, 5, 1, 1, 4, 8, 'Tokoh utama dalam sebuah cerita disebut?', '[\"<p>Latar</p>\",\"<p>Alur</p>\",\"<p>Tokoh utama</p>\",\"<p>Amanat</p>\"]', '[\"c\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(16, 1, NULL, 2, 5, 1, 1, 4, 9, 'Kalimat “Ibu memasak di dapur” termasuk kalimat?', '[\"<p>Perintah</p>\",\"<p>Berita</p>\",\"<p>Tanya</p>\",\"<p>Seruan</p>\"]', '[\"b\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(17, 1, NULL, 2, 5, 1, 1, 4, 10, 'Huruf kapital digunakan pada awal?', '[\"<p>Kata sifat</p>\",\"<p>Kata kerja</p>\",\"<p>Kalimat</p>\",\"<p>Kata sambung</p>\"]', '[\"c\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01'),
(18, 1, NULL, 2, 5, 1, 1, 4, 11, 'Pesan moral dalam sebuah cerita disebut?', '[\"<p>Alur</p>\",\"<p>Latar</p>\",\"<p>Amanat</p>\",\"<p>Tokoh</p>\"]', '[\"c\"]', 0, '2026-01-02 16:43:01', '2026-01-02 16:43:01');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_models`
--

CREATE TABLE `exercise_models` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_models`
--

INSERT INTO `exercise_models` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Pilihan Ganda', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(2, 'Pilihan Ganda Banyak', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(3, 'Pernyataan', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(4, 'Isian', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(5, 'Uraian', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(6, 'Iya Tidak', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(7, 'Argumen', '2025-11-18 08:26:57', '2025-11-18 08:26:57');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_points`
--

CREATE TABLE `exercise_points` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `exercise_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `exercise_point` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_points`
--

INSERT INTO `exercise_points` (`id`, `serial_id`, `exercise_id`, `student_id`, `answer`, `exercise_point`, `created_at`, `updated_at`) VALUES
(6, 1, 4, 1, '{\"4\":\"b\",\"5\":\"c\",\"6\":\"a\",\"7\":\"b\"}', '25', '2025-12-19 21:15:10', '2025-12-19 21:15:10'),
(7, 1, 2, 2, '{\"3\":\"a\"}', '0', '2025-12-27 22:35:46', '2025-12-27 22:35:46'),
(9, 1, 4, 2, '{\"4\":\"a\",\"5\":\"b\",\"6\":\"a\",\"7\":\"a\"}', '75', '2026-01-01 21:09:07', '2026-01-01 21:09:07'),
(10, 1, 5, 2, '{\"8\":\"a\",\"9\":\"c\",\"10\":\"d\",\"11\":\"c\",\"12\":\"b\",\"13\":\"c\",\"14\":\"a\",\"15\":\"c\",\"16\":\"b\",\"17\":\"c\",\"18\":\"c\"}', '100', '2026-01-02 09:48:50', '2026-01-02 09:48:50'),
(11, 1, 1, 2, '{\"1\":\"a\",\"2\":\"c\"}', '100', '2026-01-03 09:13:24', '2026-01-03 09:13:24'),
(14, 1, 2, 1, '{\"3\":\"a\"}', '0', '2026-01-07 10:00:27', '2026-01-07 10:00:27'),
(15, 1, 1, 1, '{\"1\":\"a\",\"2\":\"c\"}', '100', '2026-01-07 10:11:51', '2026-01-07 10:11:51'),
(16, 1, 5, 1, '{\"8\":\"a\",\"9\":\"c\",\"10\":\"d\",\"11\":\"c\",\"12\":\"b\",\"13\":\"c\",\"14\":\"a\",\"15\":\"c\",\"16\":\"b\",\"17\":\"c\",\"18\":\"c\"}', '100', '2026-01-07 21:04:30', '2026-01-07 21:04:30');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_types`
--

CREATE TABLE `exercise_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_types`
--

INSERT INTO `exercise_types` (`id`, `kode`, `name`, `created_at`, `updated_at`) VALUES
(1, 'UH', 'Ulangan Harian', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(2, 'PTS', 'Penilaian Tengah Semester', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(3, 'UAS', 'Ujian Akhir Semester', '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(4, 'AKM', 'Asesmen Kompetensi Minimum', '2025-12-09 05:20:10', '2025-12-09 05:20:10'),
(5, 'ASPD', 'Asesmen Standardisasi Pendidikan Daerah', '2025-12-09 05:21:06', '2025-12-09 05:21:06');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(10) UNSIGNED NOT NULL,
  `mapel_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` tinyint NOT NULL,
  `category` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `mapel_id`, `name`, `grade`, `semester`, `category`, `created_at`, `updated_at`) VALUES
(1, 2, 'Kurikulum Merdeka Matematika', 'V', 1, 1, '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(2, 3, 'Kurikulum Merdeka Bahasa Indonesia', 'V', 1, 1, '2025-12-09 06:17:45', '2025-12-09 06:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `mapels`
--

CREATE TABLE `mapels` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mapels`
--

INSERT INTO `mapels` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'PPKN', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(2, 'Matematika', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(3, 'Bhs. Indonesia', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(4, 'IPA', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(5, 'IPS', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(6, 'SBDP', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(7, 'PJOK', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(8, 'PADBP Islam', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(9, 'Bhs. Arab', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(10, 'Al-Quran Hadis', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(11, 'SKI', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(12, 'Fiqih', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(13, 'Akidah Akhlak', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(14, 'Bhs. Inggris', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(15, 'Bhs. Jawa', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(16, 'BTQ', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(17, 'Tematik', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(18, 'AKM', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(19, 'IPAS', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(20, 'Kepercayaan', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(21, 'Informatika', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(22, 'Kesenian', '2025-11-18 08:26:56', '2025-11-18 08:26:56'),
(23, 'P5', '2025-11-18 08:26:56', '2025-11-18 08:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `online_meetings`
--

CREATE TABLE `online_meetings` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `meeting_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('upcoming','live','ended','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `online_meetings`
--

INSERT INTO `online_meetings` (`id`, `serial_id`, `classroom_id`, `user_id`, `title`, `description`, `meeting_code`, `start_time`, `end_time`, `status`, `created_at`, `updated_at`) VALUES
(30, 1, 1, 1, 'MTK 1', 'klnnadnnaa', 'meet-zlMayOItMm', '2025-12-26 15:36:49', '2025-12-26 15:52:34', 'ended', '2025-12-26 08:20:30', '2025-12-26 08:52:34'),
(31, 1, 1, 1, 'TTT', 'adafaa', 'meet-4pPDSLaN55', '2025-12-26 15:53:04', '2025-12-26 15:57:59', 'ended', '2025-12-26 08:52:56', '2025-12-26 08:57:59'),
(32, 1, 1, 1, 'YYYY', 'sdfsafa', 'meet-dNeWCRxfaS', '2025-12-26 16:00:44', '2025-12-26 16:02:44', 'ended', '2025-12-26 08:58:13', '2025-12-26 09:02:44'),
(33, 1, 1, 1, 'MTKK', 'asda', 'meet-wotOUhZa4o', '2025-12-26 16:13:27', '2025-12-26 16:15:02', 'ended', '2025-12-26 09:10:48', '2025-12-26 09:15:02'),
(34, 1, 1, 1, 'IPA Bab1', 'hdoifhaoifda', 'meet-qv0bNx05yB', '2025-12-28 04:15:17', '2025-12-28 04:17:54', 'ended', '2025-12-27 21:14:41', '2025-12-27 21:17:54'),
(35, 1, 1, 1, 'MTKKK', 'kelas online', 'meet-StmTZUkeNa', '2025-12-28 04:52:00', '2025-12-28 05:03:24', 'ended', '2025-12-27 21:51:48', '2025-12-27 22:03:24'),
(36, 1, 1, 1, 'IPA', 'isdoiioa', 'meet-JENqBo6TiH', '2025-12-28 05:03:53', '2025-12-28 05:08:02', 'ended', '2025-12-27 22:03:41', '2025-12-27 22:08:02'),
(37, 1, 1, 1, 'MTK', 'accfaf', 'meet-Mj7GNaHFv3', '2025-12-28 05:09:50', '2025-12-28 05:12:04', 'ended', '2025-12-27 22:09:45', '2025-12-27 22:12:04'),
(38, 1, 1, 1, 'Ipa', 'aafafa', 'meet-TafjHLpZZe', '2025-12-28 05:23:34', '2025-12-28 05:25:17', 'ended', '2025-12-27 22:23:07', '2025-12-27 22:25:17'),
(39, 1, 1, 1, 'IPA', 'sndlkak', 'meet-EmoUdfHMNV', '2025-12-28 06:15:36', '2025-12-28 06:17:18', 'ended', '2025-12-27 23:06:09', '2025-12-27 23:17:18'),
(40, 1, 1, 1, 'MTK qqq', 'online class', 'meet-BaFoItBqdn', '2025-12-28 14:18:00', NULL, 'upcoming', '2025-12-27 23:18:57', '2025-12-27 23:18:57'),
(41, 1, 1, 1, 'IPA', 'uinin', 'meet-hmEmKMI4Y6', '2025-12-28 16:04:49', '2025-12-28 16:07:07', 'ended', '2025-12-27 23:35:02', '2025-12-28 09:07:07'),
(42, 1, 1, 1, 'IPA 2', 'online meet', 'meet-pC5yGUNube', '2026-01-02 04:10:40', '2026-01-02 04:13:22', 'ended', '2025-12-27 23:37:48', '2026-01-01 21:13:22'),
(43, 1, 1, 1, 'B indo', 'oonon', 'meet-BZHq0LDF1T', '2025-12-28 08:40:00', NULL, 'upcoming', '2025-12-27 23:39:20', '2025-12-27 23:39:20'),
(44, 1, 1, 1, 'LLLL', 'bbikbkkbj', 'meet-zYkVbzrnsr', '2025-12-31 15:41:00', NULL, 'upcoming', '2025-12-27 23:41:12', '2025-12-27 23:41:12'),
(45, 1, 1, 1, 'Meet Online MTK', 'kelas online mtk', 'meet-g8QpIWizk1', '2026-01-08 04:06:10', '2026-01-08 04:08:04', 'ended', '2026-01-07 20:52:03', '2026-01-07 21:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `online_meeting_participants`
--

CREATE TABLE `online_meeting_participants` (
  `id` int(10) UNSIGNED NOT NULL,
  `online_meeting_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `role` enum('teacher','student') NOT NULL,
  `joined_at` datetime NOT NULL,
  `left_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `online_meeting_participants`
--

INSERT INTO `online_meeting_participants` (`id`, `online_meeting_id`, `user_id`, `role`, `joined_at`, `left_at`, `created_at`, `updated_at`) VALUES
(4, 38, 1, 'teacher', '2025-12-28 05:23:34', '2025-12-28 05:25:17', '2025-12-27 22:23:34', '2025-12-27 22:25:17'),
(5, 38, 2, 'student', '2025-12-28 05:24:02', '2025-12-28 05:24:43', '2025-12-27 22:24:02', '2025-12-27 22:24:43'),
(6, 39, 1, 'teacher', '2025-12-28 06:15:36', '2025-12-28 06:17:18', '2025-12-27 23:15:36', '2025-12-27 23:17:18'),
(7, 39, 2, 'student', '2025-12-28 06:16:26', '2025-12-28 06:17:02', '2025-12-27 23:16:26', '2025-12-27 23:17:02'),
(8, 41, 1, 'teacher', '2025-12-28 16:04:49', '2025-12-28 16:07:07', '2025-12-28 09:04:49', '2025-12-28 09:07:07'),
(9, 41, 2, 'student', '2025-12-28 16:05:54', '2025-12-28 16:06:36', '2025-12-28 09:05:54', '2025-12-28 09:06:36'),
(10, 42, 1, 'teacher', '2026-01-02 04:10:40', '2026-01-02 04:13:22', '2026-01-01 21:10:40', '2026-01-01 21:13:22'),
(11, 42, 2, 'student', '2026-01-02 04:11:56', '2026-01-02 04:13:22', '2026-01-01 21:11:56', '2026-01-01 21:13:22'),
(12, 45, 1, 'student', '2026-01-08 04:06:40', '2026-01-08 04:08:04', '2026-01-07 21:06:10', '2026-01-07 21:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` int(10) UNSIGNED NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(62, 'App\\Models\\Student', 3, 'student_token', 'dad45c2715f177bb2f1e9ea7665612112e0fa51a18ef8799592edd481d245e46', '[\"*\"]', NULL, NULL, '2025-11-26 10:11:38', '2025-11-26 10:11:38'),
(78, 'App\\Models\\Student', 1, 'student_token', '6192fbccbbb5d14a2400dd91695ea925daf01466bae3cb6033f6c3ab90ff529a', '[\"*\"]', NULL, NULL, '2025-11-26 23:35:40', '2025-11-26 23:35:40'),
(79, 'App\\Models\\Student', 1, 'student_token', 'cccf61e55c707887ab168edacd9e0a9d2948502c90f02cfe5512de3c45112fc4', '[\"*\"]', NULL, NULL, '2025-11-26 23:35:42', '2025-11-26 23:35:42'),
(81, 'App\\Models\\Student', 1, 'student_token', 'c3782d7d6b2e5af4da328d2dec40834873a592d9e322cfc1db5e4c23c2ec5cec', '[\"*\"]', NULL, NULL, '2025-11-26 23:41:02', '2025-11-26 23:41:02'),
(83, 'App\\Models\\Student', 1, 'student_token', '7855ca513e0c35b20a827aaa13cea0a104308c5f69e0c4910e9e9280053b3589', '[\"*\"]', NULL, NULL, '2025-11-27 06:23:19', '2025-11-27 06:23:19'),
(84, 'App\\Models\\Student', 1, 'student_token', '039f0a9b7caab22d8f628e7f9f0060f3b3ae9874d603b6e67c1790060252f544', '[\"*\"]', NULL, NULL, '2025-11-27 06:23:20', '2025-11-27 06:23:20'),
(85, 'App\\Models\\Student', 1, 'student_token', '131629767c71fd61136e7720a59705fa4c1b71b3d8283a02e49eb82ebcf941de', '[\"*\"]', NULL, NULL, '2025-11-27 06:23:22', '2025-11-27 06:23:22'),
(86, 'App\\Models\\Student', 1, 'student_token', 'b9e2dc39eb115f7e607b65eb330fba3c7a303c256c7bb25a499bddc7833dd4bc', '[\"*\"]', NULL, NULL, '2025-11-27 06:23:23', '2025-11-27 06:23:23'),
(87, 'App\\Models\\Student', 1, 'student_token', '9783a59559a5af2ec0fd59597622d8ceeba2004ceebd690a9438ed518d8ae1e0', '[\"*\"]', NULL, NULL, '2025-11-27 06:23:25', '2025-11-27 06:23:25'),
(89, 'App\\Models\\Student', 1, 'student_token', '829905bf788b2b57305f035e7ebcd6af3726129bd83ae13b08336d9879409093', '[\"*\"]', NULL, NULL, '2025-11-27 06:32:46', '2025-11-27 06:32:46'),
(90, 'App\\Models\\Student', 1, 'student_token', '59b4c67bf8989e5c5b307ca0fdb6a328595c416457e3e218141bbe5239ae65d6', '[\"*\"]', NULL, NULL, '2025-11-27 06:32:51', '2025-11-27 06:32:51'),
(92, 'App\\Models\\Student', 2, 'student_token', 'a935690aeace74bda76b937d196acb4601f9af7a15355a7f7f5e4f88cf80f78e', '[\"*\"]', NULL, NULL, '2025-11-27 06:39:58', '2025-11-27 06:39:58'),
(93, 'App\\Models\\Student', 2, 'student_token', '05e4d0dbcd346a45b740aa04a7a2b9f6120fce035187d5a7272053d5bdd918f7', '[\"*\"]', '2025-11-27 06:41:31', NULL, '2025-11-27 06:41:21', '2025-11-27 06:41:31'),
(100, 'App\\Models\\Student', 3, 'student_token', '666a803f3a22ac84542c853d8b435526071fd4feb910704ecdf8cadc347a751f', '[\"*\"]', '2025-11-27 07:08:44', NULL, '2025-11-27 07:08:28', '2025-11-27 07:08:44'),
(101, 'App\\Models\\Student', 3, 'student_token', '36c2a0996f7dccc0974a56eb24365c96eaa8babec3e88c447da3a0fbebd652cc', '[\"*\"]', '2025-11-30 08:06:07', NULL, '2025-11-30 08:04:26', '2025-11-30 08:06:07'),
(102, 'App\\Models\\Student', 1, 'student_token', '2d9acc869ad7ce0dd73cf8b470eb0a1b869d6eb863c43cccbae5c1c1245c164a', '[\"*\"]', '2025-12-05 02:59:26', NULL, '2025-11-30 08:07:03', '2025-12-05 02:59:26'),
(103, 'App\\Models\\Student', 1, 'student_token', '62c7298120fc6c50d0c28f7edbbeffd5b9a4bc9d8a8f9f213bac201abd8d5c64', '[\"*\"]', '2025-11-30 19:34:30', NULL, '2025-11-30 19:05:02', '2025-11-30 19:34:30'),
(104, 'App\\Models\\Student', 1, 'student_token', 'ac337ded1e45479926f038b381d0fc2026cfac45b47e0f96890dfb659fa9a50a', '[\"*\"]', '2025-11-30 20:23:36', NULL, '2025-11-30 19:51:56', '2025-11-30 20:23:36'),
(106, 'App\\Models\\Student', 2, 'student_token', '97ba5a5cf005925817f45c451c158d88e9a6cfc5f44808200c1973921a3384d2', '[\"*\"]', '2025-12-04 08:55:01', NULL, '2025-12-04 08:54:01', '2025-12-04 08:55:01'),
(111, 'App\\Models\\Student', 2, 'student_token', '54ccec73eb693ffc405ed88674d88e4b2742b9f4d018bcd2e401cdaa0c75dd34', '[\"*\"]', '2025-12-04 09:15:54', NULL, '2025-12-04 09:15:49', '2025-12-04 09:15:54'),
(113, 'App\\Models\\Student', 1, 'student_token', '6827b6c47a40276e38c3992a377eca82795a21057e6a111948c57fc913c4f09c', '[\"*\"]', '2025-12-04 09:21:30', NULL, '2025-12-04 09:20:56', '2025-12-04 09:21:30'),
(116, 'App\\Models\\Student', 1, 'student_token', '0e27b372b7230f45bc54dff245414703824751d32b3fed15c9f8b943b9b59417', '[\"*\"]', '2025-12-04 09:32:21', NULL, '2025-12-04 09:31:55', '2025-12-04 09:32:21'),
(117, 'App\\Models\\Student', 1, 'student_token', '831272dab00e7513804d4b8edb85575fec843f79318d811ac7594552d3e2c239', '[\"*\"]', '2025-12-04 17:44:50', NULL, '2025-12-04 17:36:24', '2025-12-04 17:44:50'),
(122, 'App\\Models\\Student', 1, 'student_token', '71d5c4bbb54a46f984c2f7ff684ea3aab10bc3d0c696c2b5168049b6892fec2d', '[\"*\"]', '2025-12-05 03:00:11', NULL, '2025-12-05 02:59:45', '2025-12-05 03:00:11'),
(123, 'App\\Models\\Student', 2, 'student_token', 'e96a9366871a769afe1d0a3a4788d97800f084e94c0a3a74ccaffafebdb8ceaf', '[\"*\"]', '2025-12-05 03:28:44', NULL, '2025-12-05 03:00:20', '2025-12-05 03:28:44'),
(124, 'App\\Models\\Student', 1, 'student_token', '8b6b734b1b4ab9bcddbad0e36390a7b0e906b4e40185610fdfeec64855944367', '[\"*\"]', '2025-12-05 03:03:58', NULL, '2025-12-05 03:03:33', '2025-12-05 03:03:58'),
(129, 'App\\Models\\Student', 1, 'student_token', '61452eb0ff7e26abb3964cb9931ada127645edddba7df03024822953e53d8e7a', '[\"*\"]', '2025-12-10 09:14:37', NULL, '2025-12-05 03:29:41', '2025-12-10 09:14:37'),
(133, 'App\\Models\\Student', 2, 'student_token', '6ca61a4d2b97991ee0595f2b5009ea5102a5acc9089ce21657285129f6013016', '[\"*\"]', '2025-12-05 04:25:02', NULL, '2025-12-05 04:23:04', '2025-12-05 04:25:02'),
(136, 'App\\Models\\Student', 1, 'student_token', '65267c18ae52b829c0500eed0f25a78f8b7db07608c5556898692fbc9b3c3408', '[\"*\"]', '2025-12-08 02:48:48', NULL, '2025-12-08 02:48:46', '2025-12-08 02:48:48'),
(137, 'App\\Models\\Student', 1, 'student_token', '07dfbc660b40be7bb95b8e9bb5d4bc74354c7b1723c895787b61fec3cba17a8d', '[\"*\"]', '2025-12-08 03:13:20', NULL, '2025-12-08 03:00:56', '2025-12-08 03:13:20'),
(138, 'App\\Models\\Student', 2, 'student_token', 'd5b54012a807bd980736455191538528c13ffcf5ee04e6dee810e8a6a4141db4', '[\"*\"]', '2025-12-08 08:32:33', NULL, '2025-12-08 08:32:20', '2025-12-08 08:32:33'),
(139, 'App\\Models\\Student', 2, 'student_token', '7ef2023528b2abdc0b6a92433651e26ea4b3a8d988a041e8aacf17dbcd250d4c', '[\"*\"]', '2025-12-08 08:54:15', NULL, '2025-12-08 08:51:30', '2025-12-08 08:54:15'),
(140, 'App\\Models\\Student', 1, 'student_token', 'a67a5b65c482c758051e04c08ea3dd8ca631d9d2f5a47858142850a467993284', '[\"*\"]', '2025-12-09 00:34:57', NULL, '2025-12-09 00:14:26', '2025-12-09 00:34:57'),
(142, 'App\\Models\\Student', 2, 'student_token', 'ca9cce6a83fc2116a61602b35fa1de5debe897111c1de0c44bd5c6da39e6f656', '[\"*\"]', '2025-12-09 01:10:38', NULL, '2025-12-09 01:05:45', '2025-12-09 01:10:38'),
(144, 'App\\Models\\Student', 2, 'student_token', '861deb7c3cf7542cdb4563dfc1b5d523a82e72b721816c5bebe34a95d1a509fb', '[\"*\"]', '2025-12-10 10:25:02', NULL, '2025-12-10 08:26:17', '2025-12-10 10:25:02'),
(145, 'App\\Models\\Student', 2, 'student_token', '345ff71b3ec56e196c1e0442e63ff7ec3f8eb12d8b843c51751fa917b44820ab', '[\"*\"]', '2025-12-16 04:42:39', NULL, '2025-12-10 09:15:01', '2025-12-16 04:42:39'),
(147, 'App\\Models\\Student', 2, 'student_token', 'fe8ae7232531715afb5bdfbcadc6827ff186d3b764ea8ea85727c4ab614a0ca0', '[\"*\"]', '2025-12-15 22:31:56', NULL, '2025-12-14 10:11:26', '2025-12-15 22:31:56'),
(149, 'App\\Models\\Student', 2, 'student_token', '0d10c64128d0ce66496b8c433b585f1aacd21fcf813958dcbe144bb64dc58c76', '[\"*\"]', '2025-12-19 21:10:58', NULL, '2025-12-16 04:44:17', '2025-12-19 21:10:58'),
(150, 'App\\Models\\Student', 1, 'student_token', 'ef7f104c28636605aad19d867face22954185ce9599a9fa5e19eb9c901b715a3', '[\"*\"]', '2025-12-18 08:07:54', NULL, '2025-12-18 05:35:32', '2025-12-18 08:07:54'),
(151, 'App\\Models\\Student', 1, 'student_token', '4538f807b3146866d83575ee37b821d99a4590cac020144616702b0b09b795be', '[\"*\"]', '2025-12-23 20:33:28', NULL, '2025-12-18 08:14:50', '2025-12-23 20:33:28'),
(152, 'App\\Models\\Student', 1, 'student_token', '2f9dde0bc37084bce43f074e638da88c00c2ed2a1b4968f63c3cb09b3e30521e', '[\"*\"]', '2026-01-02 09:58:55', NULL, '2025-12-19 21:12:07', '2026-01-02 09:58:55'),
(163, 'App\\Models\\Student', 2, 'student_token', 'dddb2b27772f99d95d9f4936ea861be83c7a64dce23e5b0af8db9a5a6e1f2a06', '[\"*\"]', '2026-01-02 12:41:29', NULL, '2026-01-02 09:59:53', '2026-01-02 12:41:29'),
(164, 'App\\Models\\Student', 2, 'student_token', '4517887db7022b6cb2a67537e85bd1fc68a7486b44392b9c8d7c08bc406eeee2', '[\"*\"]', '2026-01-03 10:16:32', NULL, '2026-01-03 10:02:45', '2026-01-03 10:16:32'),
(169, 'App\\Models\\Student', 1, 'student_token', '7a05fc1fe85448d33caaae2eb3264d9070d5900b959dbdddac4189221a50ebe6', '[\"*\"]', '2026-01-08 00:24:48', NULL, '2026-01-08 00:17:08', '2026-01-08 00:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `mapel_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` text COLLATE utf8mb4_unicode_ci,
  `attachment` text COLLATE utf8mb4_unicode_ci,
  `embed` text COLLATE utf8mb4_unicode_ci,
  `due_date` timestamp NULL DEFAULT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_task` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `serial_id`, `user_id`, `mapel_id`, `title`, `description`, `slug`, `link`, `attachment`, `embed`, `due_date`, `category`, `is_task`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2, 'Tugas Operasi Hitung', 'Kerjakan soal latihan operasi hitung di buku paket halaman 20.', 'tugas-operasi-hitung', 'https://katadata.co.id/berita/nasional/62e78ed5538da/memahami-pengertian-operasi-hitung-dan-contoh-soal-bilangan-bulat', 'operasihitung.pdf', NULL, '2025-11-19 14:00:29', '[\"VII\"]', 1, '2025-11-18 08:26:57', '2025-11-18 08:26:57'),
(2, 1, 1, 8, 'Pengertian Norma', NULL, 'pengertian-norma', NULL, NULL, '<iframe src=\"https://www.youtube.com/embed/YRC17fWgpQQ?=rel0\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>', NULL, '[\"VII\"]', 0, '2021-11-10 18:51:41', '2021-11-10 18:51:41'),
(3, 1, 1, 5, 'Sosial Keberagaman', 'sosial sangat beragam', '', 'www.youtube.com', NULL, NULL, NULL, '[\"VII\"]', 0, '2025-11-19 15:21:32', '2025-11-19 15:21:32'),
(4, 1, 1, 1, 'BPUPKI', 'Pengertian BPUPKI adalah', 'BPUPKI & PPKI', NULL, NULL, NULL, '2025-11-20 03:07:51', '[\"VII\"]', 1, '2025-11-20 03:07:51', '2025-11-20 03:07:51'),
(5, 1, 1, 15, 'Wayang', 'Tokoh-tokoh dalam pewayangan', '', NULL, 'wayang.pdf', '', NULL, '[\"VII\"]', 0, '2025-11-20 05:51:15', '2025-11-20 05:51:15'),
(6, 1, 1, 7, 'Kebugaran Jasmani', 'Olahraga sehaari hari', '', 'https://www.halodoc.com/artikel/15-jenis-olahraga-kardio-yang-bisa-dilakukan-di-rumah?srsltid=AfmBOooVvhIUxfPU_CgbGihb70wihRmWNKOpfEBsgY4nib-73wrOCFPY', 'olahrga.pdf', '<iframe src=\"https://www.youtube.com/embed/CpUKc28uPEk?si=b71fmAKQ8OoE-1i3\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, '[\"IV\"]', 0, '2025-11-20 11:43:14', '2025-11-20 11:43:14'),
(11, 1, 1, 4, 'Tumbuhan', 'bagian tumbuhan air', 'tumbuhan-1763823676', 'https://www.ruangguru.com/blog/organ-tumbuhan', 'Pemberitahuan Bantuan UKT_SPP Tahun 2025.pdf', NULL, '2025-11-24 15:01:32', '[\"IV\"]', 1, '2025-11-22 08:01:16', '2025-11-22 08:01:16'),
(12, 1, 1, 3, 'Belajar Nama Hewan', 'Hewan darat, air, dan amphibi', 'belajar-nama-hewan-1765768582', 'https://www.ruangguru.com/blog/organ-tumbuhan', 'Surat_Undangan_Tamu.pdf', NULL, NULL, '[\"VII\"]', 0, '2025-12-14 20:16:25', '2025-12-14 20:16:25'),
(18, 1, 1, 7, 'Diet Karbo', 'Enakkkk', 'diet-karbo-1765866059', 'https://www.ruangguru.com/blog/organ-tumbuhan', 'posts/1765866059_tugas-4a-tari-14-may-2025.pdf', NULL, NULL, NULL, 0, '2025-12-15 23:20:59', '2025-12-15 23:20:59'),
(19, 1, 1, 3, 'Kosa Kata', 'kosakata indonesia', 'kosa-kata-1765884463', 'https://leit.co.id/500-kosakata-bahasa-inggris-sehari-hari-dan-artinya/', 'posts/1765884463_use-case-diagram.pdf', NULL, '2025-12-31 11:28:05', '[\"VII\"]', 1, '2025-12-16 04:27:43', '2025-12-16 04:27:43'),
(20, 1, 1, 14, 'Inggris Lesson', 'Tugas Inggres', 'inggris-lesson', 'https://www.vocabulary.com/dictionary/', NULL, '<iframe src=\"https://www.youtube.com/embed/NoIYUJFu0ck?si=nQE6NADXIFxYJF_7\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', '2026-01-04 17:11:24', '[\"VII\"]', 1, '2025-12-30 17:11:24', '2025-12-30 17:11:24'),
(21, 1, 1, 15, 'Wayang Kulit', 'Macam-macam wayang', 'wayang-kulit', 'https://id.wikipedia.org/wiki/Wayang', NULL, '<iframe src=\"https://www.youtube.com/embed/NoIYUJFu0ck?si=p81c_MfjytD9ZI8t\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', '2026-01-13 18:52:41', '[\"VI\"]', 1, '2025-12-30 18:52:41', '2025-12-30 18:52:41');

-- --------------------------------------------------------

--
-- Table structure for table `post_child_comments`
--

CREATE TABLE `post_child_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_comment_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_child_comments`
--

INSERT INTO `post_child_comments` (`id`, `post_comment_id`, `user_id`, `student_id`, `message`, `is_user`, `created_at`, `updated_at`) VALUES
(2, 4, NULL, 2, 'halo reno', 0, '2025-12-28 11:31:34', '2025-12-28 11:31:34'),
(10, 4, NULL, 1, 'halowww', 0, '2025-12-28 13:21:21', '2025-12-28 13:21:21'),
(12, 9, NULL, 2, 'yoii', 0, '2025-12-28 15:53:10', '2025-12-28 15:53:10'),
(13, 7, NULL, 2, 'tes budi', 0, '2025-12-30 07:44:38', '2025-12-30 07:44:38'),
(15, 8, NULL, 2, 'hewan kuda', 0, '2025-12-30 08:16:35', '2025-12-30 09:57:23'),
(16, 15, NULL, 2, 'tesss 555', 0, '2025-12-30 09:37:33', '2025-12-30 10:02:30'),
(17, 12, NULL, 2, 'haloo tyytttt', 0, '2025-12-30 10:02:52', '2025-12-30 10:02:52'),
(18, 1, NULL, 2, 'oi buguru', 0, '2025-12-30 12:22:56', '2025-12-30 12:22:56');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `student_id` int(10) UNSIGNED DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_comments`
--

INSERT INTO `post_comments` (`id`, `post_id`, `user_id`, `student_id`, `message`, `code`, `is_user`, `created_at`, `updated_at`) VALUES
(1, 5, 1, NULL, 'haloo', 'CMT695163a79afcb', 1, '2025-12-28 10:06:47', '2025-12-28 10:06:47'),
(4, 18, NULL, 1, 'haloo', 'CMT6951775e28ecb', 0, '2025-12-28 11:30:54', '2025-12-28 11:30:54'),
(5, 18, NULL, 1, 'tess', 'CMT6951b0da0e6f4', 0, '2025-12-28 15:36:10', '2025-12-28 15:36:10'),
(6, 18, NULL, 1, 'gg', 'CMT6951b1ddd3223', 0, '2025-12-28 15:40:29', '2025-12-28 15:40:29'),
(7, 6, NULL, 1, 'ttttestst', 'CMT6951b20fc2dff', 0, '2025-12-28 15:41:19', '2025-12-28 15:41:19'),
(8, 12, NULL, 1, 'hewan apa yaa', 'CMT6951b2d8c47c7', 0, '2025-12-28 15:44:40', '2025-12-28 15:44:40'),
(9, 18, 1, NULL, 'saya guru lhoo', '5e1da3ad-f169-4d7d-ab81-ca1da1bbe958', 1, '2025-12-28 15:52:55', '2025-12-28 15:52:55'),
(12, 11, NULL, 2, 'haloo tygas', 'CMT6953e6583cb4c', 0, '2025-12-30 07:48:56', '2025-12-30 07:48:56'),
(13, 18, NULL, 2, 'oii semuaa', 'CMT6953ecbbaaf43', 0, '2025-12-30 08:16:11', '2025-12-30 09:07:54'),
(14, 12, NULL, 2, 'halo sapi', 'CMT6953fa4e82d9c', 0, '2025-12-30 09:14:06', '2025-12-30 09:42:48'),
(15, 19, NULL, 2, 'tess 333', 'CMT6953ffb7641a6', 0, '2025-12-30 09:37:11', '2025-12-30 09:37:19'),
(16, 19, NULL, 2, 'oii', 'CMT69541d6fc4216', 0, '2025-12-30 11:43:59', '2025-12-30 11:43:59'),
(17, 5, NULL, 2, 'halo bu', 'CMT6954268a0713c', 0, '2025-12-30 12:22:50', '2025-12-30 12:22:50'),
(18, 21, NULL, 2, 'apa ini bu', 'CMT6954269abf370', 0, '2025-12-30 12:23:06', '2025-12-30 12:23:06'),
(21, 21, NULL, 1, 'tes', 'CMT695f2c8a0e92a', 0, '2026-01-07 21:03:22', '2026-01-07 21:03:22');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `lesson_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `lesson_id`, `name`, `grade`, `grade_category`, `semester`, `created_at`, `updated_at`) VALUES
(1, 1, 'Paket Belajar Matematika Kelas 5', '5', 'SD', '1', '2025-11-18 08:26:56', '2025-11-18 08:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `report` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `serial_id`, `student_id`, `report`, `img`, `created_at`, `updated_at`) VALUES
(5, 1, 1, '[\"Ya\",\"Tidak\",\"Ya\",\"Ya\",\"Ya\",\"Sehat\",\"belajar\"]', 'reports/foto-1764933360296-1764933363.jpg', '2025-12-05 04:16:03', '2025-12-05 04:16:03'),
(6, 1, 2, '[\"Ya\",\"Ya\",\"Ya\",\"Ya\",\"Tidak\",\"Sakit\",\"tidur\"]', 'foto-1764933831499-1764933834.jpg', '2025-12-04 04:23:54', '2025-12-04 04:23:54'),
(7, 1, 1, '[\"Tidak\",\"Ya\",\"Ya\",\"Ya\",\"Tidak\",\"Sehat\",\"tidur\"]', 'foto-1765380225034-1765380229.jpg', '2025-12-10 08:23:50', '2025-12-10 08:23:50'),
(8, 1, 2, '[\"Tidak\",\"Ya\",\"Ya\",\"Tidak\",\"Tidak\",\"Kurang Sehat\",\"main\"]', 'foto-1767106018701-1767106022.jpg', '2025-12-30 07:47:02', '2025-12-30 07:47:02'),
(9, 1, 2, '[\"Ya\",\"Tidak\",\"Ya\",\"Tidak\",\"Ya\",\"Sehat\",\"main ps\"]', NULL, '2026-01-01 21:10:15', '2026-01-01 21:10:15'),
(10, 1, 1, '[\"Tidak\",\"Ya\",\"Tidak\",\"Ya\",\"Tidak\",\"Sehat\",\"main\"]', 'foto-1767845107785-1767845109.jpg', '2026-01-07 21:05:10', '2026-01-07 21:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `serials`
--

CREATE TABLE `serials` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `serial` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `paket` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `serials`
--

INSERT INTO `serials` (`id`, `user_id`, `product_id`, `serial`, `paket`, `active`, `expired_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'SERIAL12345', '4', 'yes', '2026-05-18 08:26:56', '2025-11-18 08:26:56', '2025-11-18 08:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('21V5tLBufw7qSL16cveS0C9PhxcSB7xFnJsOXTG4', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaXRJRVNaUFl6ZTFvRmI2cTVMZ0lCY3lObG5qSlRDMXRyMDNrVHQzQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL3Bvc3RzLzUvY29tbWVudHMiO3M6NToicm91dGUiO3M6MjI6InRlYWNoZXIuY29tbWVudHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1766941608),
('8mmGk7BXeMBmKHMVUQNt3UPghcJHtexxKJkq8IAD', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNEY0bFZMUTJqYUVWazBsMVZRYndNdFZCWm16ZjNDMVRzNE8zMjhwZiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767319716),
('91TB8GRc6B4VLnihCE3mgtjATWrYpp890KT0KCLB', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiajhBU2RoTUE2OEhoRU40RVd3OGZsRzVHY1pZeFJEYldEM05uS09xYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767506609),
('CAV3Fbzaa1YzHZyLzamniQmU5SK0GwYmvI5TpzXA', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTdNUHJhSVlRelJZUHhkSTJCc1RFWTJVRlc0cFM5YkY5WnpvTll0WCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767845285),
('ciNkFbylhpvj1NZg6PTYbIy6jIx1UaTffAfEwQ3Q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMHVPUnZnUEJ6cTNDdzY3Tk16Q2JGd2d1VWJ2QmtwOENQb2dIejRNWCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL3Bvc3RzLzE4L2NvbW1lbnRzIjtzOjU6InJvdXRlIjtzOjIyOiJ0ZWFjaGVyLmNvbW1lbnRzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1766962395),
('esC7TLDuEqbZvoAcxPJ0cmx8qbLAQbB5HhHvXeQg', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRlNZNkpBZTd4YnhxVWxCZlQwb0toenQxT1NpNW9GWHhsTnlGdjBZUiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767811209),
('gINqgIYfU1cE6kBZJBk9OJTLrKx76jrg6fpwWqcS', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVlVpU0puclVJQnNnVmJyS3hJaGFLdEd2YVZsYzljQ2VDVmJQVElGdCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1766765703),
('kG55CW56dK2L4XGVEsiR7tvUM5A1UHbbOJHwhTOV', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibVEzVDBqUXR3MUM0WjVpbzJlOEFCdm5QN1lTN0EzdjlBQTZvMll6QyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1766735121),
('MrgxgvZuz5zAOlh0aQitqQxGObm6GWD2rtdUrk2q', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiT0xLb3V3cUN6Qjl5N0N6akIzdzVGc091Yzd3aWVmRHhmWFhQSjNOdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767327203),
('MTAxDUmQYPEDuNshzlHbgITMWvSNhNmPnxRUeWL8', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoid2VnQzdxWXN1N3lDaXNraDFEWVJ2R1JPRE9LYW9EQ043Wk5YUkczSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL3Bvc3RzLzUvY29tbWVudHMiO3M6NToicm91dGUiO3M6MjI6InRlYWNoZXIuY29tbWVudHMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1766950425),
('Qf4ZTHNUCxvHhkG94XsFYDqumpCtNeFR0dvczQBd', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWDFYa2FPelBudlBpUXFuVEZiM3BlNmRBTFZ5WHNMSE1hYWR6c2xMSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1768067679),
('QJDH22moae8JPk3c1xqetrIUGs2jG2xik4RVS7Ir', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiVVpnTDVkVEZqME43dVFPYXFoSnBTbEZ5cEhwYm93a1ltd1JTaE9VeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767548175),
('QukMh3ILVGe77iJBp6RBqmK46ZY3wggFzNsQ7MQE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibmduVW1nUG1GdnVLSVpqRlhRR3ZOMUl2RmRPSTkxSVRLMUZCcVpVYyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1766904073),
('smM4rFx8EIH4PrXehto6Xet5au3blfMCfkAzef4k', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiczdPM0YweDA5U0s1bXVPWElsUU9VY09xdmpCeEhjeWgyS0dnT0VJTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767327041),
('ZNNk01TNMNi6dJCHzn5uzNJBSDHyvW03aLEppMMG', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaW5VRWtSdzdvTFR4MVROUjVvWlcxR254akFjbzhyejl0aDZOZmxUZSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDU6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL29ubGluZS1tZWV0aW5ncyI7czo1OiJyb3V0ZSI7czoyMjoidGVhY2hlci5tZWV0aW5ncy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1767811261);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `classroom_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_text` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `absen_number` int DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `serial_id`, `user_id`, `classroom_id`, `name`, `username`, `password`, `password_text`, `nis`, `absen_number`, `email`, `phone`, `photo`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Reno Saputra', 'reno', '$2y$12$en4h66VY0JPVc1bXjwf/We.nxW9goxBSOgBjeY2Vq/ma80K3AtKDW', 'reno123', '2025001', 1, 'reno@student.com', '08122334455', 'students/FxMZKrR7K7nwW59Q7uWrd3R7gpYFA9HEvd9Rm4bD.jpg', '2025-11-18 08:26:57', '2025-12-28 12:03:11'),
(2, 1, 1, 1, 'Budi Santoso Raul', 'budi', '$2y$12$RUsCa0Itl4t8QFGqr3/E8OM8kJMuIWTgP61hMKOMBsWhZw0zrkb4S', 'budi12345', '8983978923', 2, 'budiraul34@gmail.com', '08395566877', 'students/0GHqOwKcIWIupI69Cq5YDn33v7IhnKv1FMeQRFLY.jpg', '2025-11-22 10:21:15', '2026-01-01 21:09:51'),
(3, 1, 1, 2, 'Heri Kopling', 'heri', '$2y$12$c9TMGJ4HhKqvk6rqqX1OB.B9AYCWuCe.OQoyQQTLM0PGRRwfXD6sS', 'heri123', '83926362', NULL, 'heri@gmail.com', '08382263548', NULL, '2025-11-26 16:33:06', '2025-11-26 16:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(10) UNSIGNED NOT NULL,
  `serial_id` int(10) UNSIGNED NOT NULL,
  `post_id` int(10) UNSIGNED NOT NULL,
  `student_id` int(10) UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` text COLLATE utf8mb4_unicode_ci,
  `point` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `serial_id`, `post_id`, `student_id`, `description`, `attachment`, `point`, `created_at`, `updated_at`) VALUES
(9, 1, 4, 1, 'ini tugas saya bu', 'Screenshot_2025-11-19-17-54-34-629_com.example.lms_frontend.jpg', '85', '2025-11-17 06:19:03', '2025-11-17 06:19:03'),
(10, 1, 1, 1, 'tugas saya', 'KRS_5220311133_Ganjil_21_09_2025_11_54_03.pdf', '78', '2025-11-21 06:31:38', '2025-11-21 06:31:38'),
(11, 1, 11, 1, 'tugas tumbuhan', '1763827641_admin,+Pengujian+dengan+Unit+Testing+dan+Test+case+pada+Proyek+Pengembangan+Modul+Manajemen+Pengguna.pdf', '83', '2025-11-22 09:07:21', '2025-11-22 09:07:21'),
(12, 1, 19, 2, 'tugas indo', '1765884804_IMG_20251212_181102.png', '84', '2025-12-16 04:33:24', '2025-12-16 04:33:24'),
(13, 1, 20, 2, 'tugas inggris', '1767117245_1765866059_tugas-4a-tari-14-may-2025 (3).pdf', '98', '2025-12-30 10:54:05', '2025-12-30 10:54:05'),
(14, 1, 21, 2, 'tugas saya', '1767326901_1765866059_tugas-4a-tari-14-may-2025 (4).pdf', '78', '2026-01-01 21:08:21', '2026-01-01 21:08:21'),
(15, 1, 21, 1, 'tugas a', '1767845306_Screenshot_2026-01-08-00-26-46-160_com.example.lms_frontend.jpg', '90', '2026-01-07 21:08:26', '2026-01-07 21:08:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` tinyint NOT NULL DEFAULT '0',
  `address` text COLLATE utf8mb4_unicode_ci,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `email`, `role`, `address`, `phone`, `img`, `login_at`, `created_at`, `updated_at`) VALUES
(1, 'Fahri Kurniawan S.Kom', 'guru_mtk', '$2y$12$qc.OfrU1CVu3TsoH5DwBrugiHi7xYpY3XLy3cI339m8ZJQQZr5q0W', 'guru@sekolah.com', 1, 'Jl. Pendidikan No.1', '081234567890', NULL, NULL, '2025-11-18 08:26:56', '2025-12-26 07:46:03'),
(2, 'Guru Kedua', 'gurukedua', '$2y$10$UxSJr9TFHj3fYTGTrkrGRuO6IbCJMZdu80NAbFBgMiMKCvQJcKR6i', 'guru2@example.com', 1, 'Sekolah Indonesia', '081234567890', NULL, '2025-12-26 06:49:15', '2025-12-26 06:49:15', '2025-12-26 06:49:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classrooms_serial_id_foreign` (`serial_id`);

--
-- Indexes for table `competences`
--
ALTER TABLE `competences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `competences_lesson_id_foreign` (`lesson_id`),
  ADD KEY `competences_mapel_id_foreign` (`mapel_id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercises_lesson_id_foreign` (`lesson_id`),
  ADD KEY `exercises_serial_id_foreign` (`serial_id`),
  ADD KEY `exercises_exercise_type_id_foreign` (`exercise_type_id`);

--
-- Indexes for table `exercise_items`
--
ALTER TABLE `exercise_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_items_user_id_foreign` (`user_id`),
  ADD KEY `exercise_items_competence_id_foreign` (`competence_id`),
  ADD KEY `exercise_items_exercise_id_foreign` (`exercise_id`),
  ADD KEY `exercise_items_exercise_type_id_foreign` (`exercise_type_id`),
  ADD KEY `exercise_items_exercise_model_id_foreign` (`exercise_model_id`),
  ADD KEY `fk_exercise_items_admin` (`admin_id`);

--
-- Indexes for table `exercise_models`
--
ALTER TABLE `exercise_models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exercise_points`
--
ALTER TABLE `exercise_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_points_serial_id_foreign` (`serial_id`),
  ADD KEY `exercise_points_exercise_id_foreign` (`exercise_id`),
  ADD KEY `exercise_points_student_id_foreign` (`student_id`);

--
-- Indexes for table `exercise_types`
--
ALTER TABLE `exercise_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_mapel_id_foreign` (`mapel_id`);

--
-- Indexes for table `mapels`
--
ALTER TABLE `mapels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_meetings`
--
ALTER TABLE `online_meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_meeting_code` (`meeting_code`),
  ADD KEY `idx_classroom_id` (`classroom_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_start_time` (`start_time`),
  ADD KEY `online_meetings_serial_id_fk` (`serial_id`);

--
-- Indexes for table `online_meeting_participants`
--
ALTER TABLE `online_meeting_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_meeting_user` (`online_meeting_id`,`user_id`),
  ADD KEY `idx_meeting` (`online_meeting_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_serial_id_foreign` (`serial_id`),
  ADD KEY `posts_user_id_foreign` (`user_id`),
  ADD KEY `posts_mapel_id_foreign` (`mapel_id`);

--
-- Indexes for table `post_child_comments`
--
ALTER TABLE `post_child_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_comment_id` (`post_comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_lesson_id_foreign` (`lesson_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_serial_id_foreign` (`serial_id`),
  ADD KEY `reports_student_id_foreign` (`student_id`);

--
-- Indexes for table `serials`
--
ALTER TABLE `serials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `serials_user_id_foreign` (`user_id`),
  ADD KEY `serials_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_username_unique` (`username`),
  ADD KEY `students_serial_id_foreign` (`serial_id`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_classroom_id_foreign` (`classroom_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_serial_id_foreign` (`serial_id`),
  ADD KEY `tasks_post_id_foreign` (`post_id`),
  ADD KEY `tasks_student_id_foreign` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `exercise_items`
--
ALTER TABLE `exercise_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `exercise_models`
--
ALTER TABLE `exercise_models`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exercise_points`
--
ALTER TABLE `exercise_points`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `exercise_types`
--
ALTER TABLE `exercise_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mapels`
--
ALTER TABLE `mapels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `online_meetings`
--
ALTER TABLE `online_meetings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `online_meeting_participants`
--
ALTER TABLE `online_meeting_participants`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=170;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `post_child_comments`
--
ALTER TABLE `post_child_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `serials`
--
ALTER TABLE `serials`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classrooms`
--
ALTER TABLE `classrooms`
  ADD CONSTRAINT `classrooms_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `competences`
--
ALTER TABLE `competences`
  ADD CONSTRAINT `competences_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `competences_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mapels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exercises`
--
ALTER TABLE `exercises`
  ADD CONSTRAINT `exercises_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercises_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercises_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exercise_items`
--
ALTER TABLE `exercise_items`
  ADD CONSTRAINT `exercise_items_competence_id_foreign` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exercise_items_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_items_exercise_model_id_foreign` FOREIGN KEY (`exercise_model_id`) REFERENCES `exercise_models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_items_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exercise_items_admin` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `exercise_points`
--
ALTER TABLE `exercise_points`
  ADD CONSTRAINT `exercise_points_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_points_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_points_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mapels` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_meetings`
--
ALTER TABLE `online_meetings`
  ADD CONSTRAINT `online_meetings_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_meetings_serial_id_fk` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_meetings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_meeting_participants`
--
ALTER TABLE `online_meeting_participants`
  ADD CONSTRAINT `fk_participant_meeting` FOREIGN KEY (`online_meeting_id`) REFERENCES `online_meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_participant_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_mapel_id_foreign` FOREIGN KEY (`mapel_id`) REFERENCES `mapels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `post_child_comments`
--
ALTER TABLE `post_child_comments`
  ADD CONSTRAINT `fk_child_comments_comment_id` FOREIGN KEY (`post_comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_child_comments_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_child_comments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `fk_post_comments_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_post_comments_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_post_comments_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_lesson_id_foreign` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `serials`
--
ALTER TABLE `serials`
  ADD CONSTRAINT `serials_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `serials_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_classroom_id_foreign` FOREIGN KEY (`classroom_id`) REFERENCES `classrooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
