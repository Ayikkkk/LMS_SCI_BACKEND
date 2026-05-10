-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 03, 2026 at 04:08 AM
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
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` tinyint NOT NULL,
  `date_in` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `username`, `password`, `role`, `date_in`, `position`, `phone`, `img`, `login_at`, `created_at`, `updated_at`) VALUES
(1, 'Fahri Kurniawan', 'fahri', '$2y$10$cP/SinrmMHi5ZxApNXHeL.RjMod8ikpB63QJ/MGdCGTo28ZmX6t7O', 7, '2025-10-13', 'Administrator', '08467377832', NULL, '2025-12-18 05:46:10', '2025-12-18 05:46:10', '2025-12-18 05:47:44');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('lms-backend-cache-4faaa9235bf8ac4e1d31a493fcda5396de9922fc', 'i:1;', 1776916832),
('lms-backend-cache-4faaa9235bf8ac4e1d31a493fcda5396de9922fc:timer', 'i:1776916832;', 1776916832),
('lms-backend-cache-9d5f331f50279c2af7e659a884cb8f509074224c', 'i:1;', 1777523906),
('lms-backend-cache-9d5f331f50279c2af7e659a884cb8f509074224c:timer', 'i:1777523906;', 1777523906),
('lms-backend-cache-e08b9a26509e9d332927b7a59b24c3bfdd74d73d', 'i:1;', 1776323013),
('lms-backend-cache-e08b9a26509e9d332927b7a59b24c3bfdd74d73d:timer', 'i:1776323013;', 1776323013);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classrooms`
--

CREATE TABLE `classrooms` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
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
(1, 1, '5A', 'V', 'CLS5A2025', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(2, 1, '5B', 'V', 'CLASS5B2025', '2025-11-26 09:31:41', '2025-11-26 09:31:41');

-- --------------------------------------------------------

--
-- Table structure for table `competences`
--

CREATE TABLE `competences` (
  `id` int UNSIGNED NOT NULL,
  `lesson_id` int UNSIGNED NOT NULL,
  `mapel_id` int UNSIGNED NOT NULL,
  `point` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `competences`
--

INSERT INTO `competences` (`id`, `lesson_id`, `mapel_id`, `point`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 2, '3.1', 'Menjelaskan bilangan bulat dan operasi hitung sederhana.', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(2, 2, 3, '3.1', 'Siswa mampu membaca dengan lancar', '2025-12-08 23:21:04', '2025-12-08 23:21:04');

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int UNSIGNED NOT NULL,
  `lesson_id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED DEFAULT NULL,
  `exercise_type_id` int UNSIGNED NOT NULL,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `lesson_id`, `serial_id`, `exercise_type_id`, `title`, `is_admin`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 'Latihan Bilangan Bulat', 1, '2025-11-18 01:26:57', '2025-11-18 01:26:57', NULL),
(2, 2, 1, 1, 'Pengenalan Kata Kerja', 1, '2025-12-08 23:22:43', '2025-12-08 23:22:43', NULL),
(3, 1, 1, 2, 'Latihan Bilangan Kuadrat', 1, '2025-12-09 01:08:24', '2025-12-09 01:08:24', NULL),
(4, 2, 1, 2, 'Macam Kosa Kata', 1, '2025-12-18 05:41:12', '2025-12-18 05:41:12', NULL),
(5, 2, 1, 1, 'Ulangan Kata Dasar', 1, '2026-01-02 09:30:01', '2026-01-02 09:30:01', NULL),
(6, 2, 1, 4, 'Soal AKM', 1, '2026-02-25 01:32:32', '2026-02-25 01:32:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `exercise_items`
--

CREATE TABLE `exercise_items` (
  `id` int UNSIGNED NOT NULL,
  `admin_id` int UNSIGNED DEFAULT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `competence_id` int UNSIGNED DEFAULT NULL,
  `exercise_id` int UNSIGNED NOT NULL,
  `exercise_type_id` int UNSIGNED NOT NULL,
  `exercise_model_id` int UNSIGNED NOT NULL,
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
(1, NULL, 1, 1, 1, 1, 1, 4, 1, 'Hasil dari 25 + (-10) adalah ...', '[\"<p>15<\\/p>\",\"<p>100<\\/p>\",\"<p>1<\\/p>\",\"<p>-1<\\/p>\"]', '[\"a\"]', 0, '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(2, 1, NULL, 1, 1, 1, 1, 4, 2, '\"<p>satu kuadrat berapa ...<\\/p>\"', '[\"<p>10<\\/p>\",\"<p>100<\\/p>\",\"<p>1<\\/p>\",\"<p>-1<\\/p>\"]', '[\"c\"]', 0, '2021-11-10 12:29:06', '2022-03-14 13:57:31'),
(3, NULL, 1, 2, 2, 1, 1, 4, 1, '\"<p>Berikut ini yang merupakan keberagaman pada masyarakat Indonesia adalah <\\/p>\"', '[\"<p>Suku, agama, jenis kelamin, dan tempat tinggal<\\/p>\",\"<p>Suku, budaya, dan hak<\\/p>\",\"<p>Suku, agama, ras, budaya, dan jenis kelamin<\\/p>\",\"<p>Suku, agama, dan hukum<\\/p>\"]', '[\"c\"]', 1, '2021-11-10 13:23:22', '2021-11-10 13:23:22'),
(4, 1, NULL, 1, 4, 2, 1, 4, 1, 'Apakah yang dimaksud kosa kata', '[\"<p>Suku, agama, jenis kelamin, dan tempat tinggal<\\/p>\",\"<p>Suku, budaya, dan hak<\\/p>\",\"<p>Suku, agama, ras, budaya, dan jenis kelamin<\\/p>\",\"<p>Suku, agama, dan hukum<\\/p>\"]', '[\"c\"]', 0, '2025-12-18 06:09:42', '2025-12-18 06:09:42'),
(5, 1, NULL, 2, 4, 2, 1, 4, 2, 'Kosa kata adalah', '[\"<p>Suku, agama, jenis kelamin, dan tempat tinggal<\\/p>\",\"<p>Suku, budaya, dan hak<\\/p>\",\"<p>Suku, agama, ras, budaya, dan jenis kelamin<\\/p>\",\"<p>Suku, agama, dan hukum<\\/p>\"]', '[\"b\"]', 0, '2025-12-18 06:33:17', '2025-12-18 06:33:17'),
(6, 1, NULL, 2, 4, 2, 1, 4, 3, 'Keberagaman budaya di Indonesia dapat memperkuat persatuan karena', '[\"<p>Mendorong sikap saling menghargai antar masyarakat<\\/p>\",\"<p>Menimbulkan perpecahan antar suku<\\/p>\",\"<p>Menghilangkan identitas daerah<\\/p>\",\"<p>Membuat masyarakat hidup sendiri-sendiri<\\/p>\"]', '[\"a\"]', 0, '2025-12-18 06:40:00', '2025-12-18 06:40:00'),
(7, 1, NULL, 2, 4, 2, 1, 4, 4, 'Sikap yang mencerminkan toleransi dalam kehidupan sehari-hari adalah', '[\"<p>Menghargai perbedaan pendapat orang lain<\\/p>\",\"<p>Memaksakan pendapat pribadi<\\/p>\",\"<p>Menghindari kerja sama<\\/p>\",\"<p>Menolak perbedaan budaya<\\/p>\"]', '[\"a\"]', 0, '2025-12-18 06:41:00', '2025-12-18 06:41:00'),
(8, 1, NULL, 2, 5, 1, 1, 4, 1, 'Apa ibu kota Indonesia?', '[\"<p>Jakarta<\\/p>\",\"<p>Medan<\\/p>\",\"<p>Yogyakarta<\\/p>\",\"<p>Bandung<\\/p>\"]', '[\"a\"]', 0, '2026-01-02 09:37:50', '2026-01-02 09:37:50'),
(9, 1, NULL, 2, 5, 1, 1, 4, 2, 'Kalimat yang digunakan untuk menanyakan sesuatu disebut kalimat?', '[\"<p>Perintah<\\/p>\",\"<p>Berita<\\/p>\",\"<p>Tanya<\\/p>\",\"<p>Seruan<\\/p>\"]', '[\"c\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(10, 1, NULL, 2, 5, 1, 1, 4, 3, 'Tanda baca yang digunakan di akhir kalimat tanya adalah?', '[\"<p>Titik (.)<\\/p>\",\"<p>Koma (,)<\\/p>\",\"<p>Tanda seru (!)<\\/p>\",\"<p>Tanda tanya (?)<\\/p>\"]', '[\"d\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(11, 1, NULL, 2, 5, 1, 1, 4, 4, 'Antonim dari kata \"besar\" adalah?', '[\"<p>Panjang<\\/p>\",\"<p>Tinggi<\\/p>\",\"<p>Kecil<\\/p>\",\"<p>Lebar<\\/p>\"]', '[\"c\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(12, 1, NULL, 2, 5, 1, 1, 4, 5, 'Sinonim dari kata \"pandai\" adalah?', '[\"<p>Bodoh<\\/p>\",\"<p>Cerdas<\\/p>\",\"<p>Malas<\\/p>\",\"<p>Lemah<\\/p>\"]', '[\"b\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(13, 1, NULL, 2, 5, 1, 1, 4, 6, 'Kalimat yang berisi perintah disebut kalimat?', '[\"<p>Tanya<\\/p>\",\"<p>Berita<\\/p>\",\"<p>Perintah<\\/p>\",\"<p>Seruan<\\/p>\"]', '[\"c\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(14, 1, NULL, 2, 5, 1, 1, 4, 7, 'Cerita yang bersifat khayalan disebut?', '[\"<p>Dongeng<\\/p>\",\"<p>Laporan<\\/p>\",\"<p>Berita<\\/p>\",\"<p>Pengumuman<\\/p>\"]', '[\"a\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(15, 1, NULL, 2, 5, 1, 1, 4, 8, 'Tokoh utama dalam sebuah cerita disebut?', '[\"<p>Latar<\\/p>\",\"<p>Alur<\\/p>\",\"<p>Tokoh utama<\\/p>\",\"<p>Amanat<\\/p>\"]', '[\"c\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(16, 1, NULL, 2, 5, 1, 1, 4, 9, 'Kalimat \"Ibu memasak di dapur\" termasuk kalimat?', '[\"<p>Perintah<\\/p>\",\"<p>Berita<\\/p>\",\"<p>Tanya<\\/p>\",\"<p>Seruan<\\/p>\"]', '[\"b\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(17, 1, NULL, 2, 5, 1, 1, 4, 10, 'Huruf kapital digunakan pada awal?', '[\"<p>Kata sifat<\\/p>\",\"<p>Kata kerja<\\/p>\",\"<p>Kalimat<\\/p>\",\"<p>Kata sambung<\\/p>\"]', '[\"c\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(18, 1, NULL, 2, 5, 1, 1, 4, 11, 'Pesan moral dalam sebuah cerita disebut?', '[\"<p>Alur<\\/p>\",\"<p>Latar<\\/p>\",\"<p>Amanat<\\/p>\",\"<p>Tokoh<\\/p>\"]', '[\"c\"]', 0, '2026-01-02 09:43:01', '2026-01-02 09:43:01'),
(19, 1, NULL, 2, 6, 4, 1, 4, 1, 'Hasil dari 12 x 5 adalah ...', '[\"<p>60<\\/p>\",\"<p>50<\\/p>\",\"<p>65<\\/p>\",\"<p>55<\\/p>\"]', '[\"a\"]', 0, '2026-02-25 01:33:29', '2026-02-25 01:33:29'),
(20, 1, NULL, 2, 6, 4, 2, 5, 2, 'Manakah bilangan prima berikut ini?', '[\"<p>2<\\/p>\",\"<p>4<\\/p>\",\"<p>5<\\/p>\",\"<p>9<\\/p>\",\"<p>11<\\/p>\"]', '[\"a\",\"c\",\"e\"]', 0, '2026-02-25 01:35:35', '2026-02-25 01:35:35'),
(21, 1, NULL, 2, 6, 4, 3, 2, 3, 'Pernyataan: 0 adalah bilangan genap.', '[\"<p>Benar<\\/p>\",\"<p>Salah<\\/p>\"]', '[\"a\"]', 0, '2026-02-25 05:41:14', '2026-02-25 05:41:14'),
(22, 1, NULL, 2, 6, 4, 4, 0, 4, 'Sebutkan hasil dari 7 + 8!', NULL, '[\"15\"]', 0, '2026-02-25 05:43:09', '2026-02-25 05:43:09'),
(23, 1, NULL, 1, 6, 4, 5, 0, 5, 'Jelaskan perbedaan bilangan prima dan komposit!', NULL, '[\"Bilangan prima adalah bilangan yang hanya memiliki dua faktor yaitu 1 dan dirinya sendiri.\"]', 0, '2026-02-25 05:44:21', '2026-02-25 05:44:21');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_models`
--

CREATE TABLE `exercise_models` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_models`
--

INSERT INTO `exercise_models` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Pilihan Ganda', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(2, 'Pilihan Ganda Banyak', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(3, 'Pernyataan', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(4, 'Isian', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(5, 'Uraian', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(6, 'Iya Tidak', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(7, 'Argumen', '2025-11-18 01:26:57', '2025-11-18 01:26:57');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_points`
--

CREATE TABLE `exercise_points` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
  `exercise_id` int UNSIGNED NOT NULL,
  `student_id` int UNSIGNED NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `competence_point` text COLLATE utf8mb4_unicode_ci,
  `exercise_point` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_points`
--

INSERT INTO `exercise_points` (`id`, `serial_id`, `exercise_id`, `student_id`, `answer`, `competence_point`, `exercise_point`, `created_at`, `updated_at`) VALUES
(6, 1, 2, 1, '{\"3\":\"c\"}', NULL, '100', '2026-04-16 01:06:14', '2026-04-16 01:06:14'),
(7, 1, 5, 1, '{\"11\":\"c\",\"12\":\"b\",\"9\":\"c\",\"14\":\"a\",\"8\":\"a\",\"10\":\"d\",\"15\":\"c\",\"16\":\"b\",\"18\":\"c\",\"17\":\"c\",\"13\":\"b\"}', NULL, '91', '2026-04-16 01:09:17', '2026-04-16 01:09:17'),
(8, 1, 2, 2, '{\"3\":\"b\"}', NULL, '0', '2026-04-22 20:50:07', '2026-04-22 20:50:07'),
(9, 1, 1, 1, '{\"1\":\"a\",\"2\":\"c\"}', NULL, '100', '2026-04-22 23:59:38', '2026-04-22 23:59:38'),
(10, 1, 4, 1, '{\"7\":\"a\",\"5\":\"b\",\"6\":\"c\",\"4\":\"c\"}', NULL, '75', '2026-04-23 00:02:27', '2026-04-23 00:02:27'),
(11, 1, 6, 1, '{\"22\":\"15\",\"19\":\"a\",\"21\":\"true\",\"20\":\"a,c,e\",\"23\":\"prima itu bukan komposit\"}', NULL, NULL, '2026-04-29 21:40:11', '2026-04-29 21:40:11');

-- --------------------------------------------------------

--
-- Table structure for table `exercise_types`
--

CREATE TABLE `exercise_types` (
  `id` int UNSIGNED NOT NULL,
  `kode` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exercise_types`
--

INSERT INTO `exercise_types` (`id`, `kode`, `name`, `created_at`, `updated_at`) VALUES
(1, 'UH', 'Ulangan Harian', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(2, 'PTS', 'Penilaian Tengah Semester', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(3, 'UAS', 'Ujian Akhir Semester', '2025-11-18 01:26:57', '2025-11-18 01:26:57'),
(4, 'AKM', 'Asesmen Kompetensi Minimum', '2025-12-08 22:20:10', '2025-12-08 22:20:10'),
(5, 'ASPD', 'Asesmen Standardisasi Pendidikan Daerah', '2025-12-08 22:21:06', '2025-12-08 22:21:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int UNSIGNED NOT NULL,
  `mapel_id` int UNSIGNED NOT NULL,
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
(1, 2, 'Kurikulum Merdeka Matematika', 'V', 1, 1, '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(2, 3, 'Kurikulum Merdeka Bahasa Indonesia', 'V', 1, 1, '2025-12-08 23:17:45', '2025-12-08 23:17:45');

-- --------------------------------------------------------

--
-- Table structure for table `mapels`
--

CREATE TABLE `mapels` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mapels`
--

INSERT INTO `mapels` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'PPKN', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(2, 'Matematika', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(3, 'Bhs. Indonesia', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(4, 'IPA', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(5, 'IPS', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(6, 'SBDP', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(7, 'PJOK', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(8, 'PADBP Islam', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(9, 'Bhs. Arab', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(10, 'Al-Quran Hadis', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(11, 'SKI', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(12, 'Fiqih', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(13, 'Akidah Akhlak', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(14, 'Bhs. Inggris', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(15, 'Bhs. Jawa', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(16, 'BTQ', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(17, 'Tematik', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(18, 'AKM', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(19, 'IPAS', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(20, 'Kepercayaan', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(21, 'Informatika', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(22, 'Kesenian', '2025-11-18 01:26:56', '2025-11-18 01:26:56'),
(23, 'P5', '2025-11-18 01:26:56', '2025-11-18 01:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_11_18_000001_create_admins_table', 1),
(4, '2025_11_18_000002_create_mapels_table', 1),
(5, '2025_11_18_000003_create_lessons_table', 1),
(6, '2025_11_18_000004_create_products_table', 1),
(7, '2025_11_18_000005_create_users_table', 1),
(8, '2025_11_18_000006_create_serials_table', 1),
(9, '2025_11_18_000007_create_classrooms_table', 1),
(10, '2025_11_18_000008_create_students_table', 1),
(11, '2025_11_18_000009_create_competences_table', 1),
(12, '2025_11_18_000010_create_exercise_types_table', 1),
(13, '2025_11_18_000011_create_exercise_models_table', 1),
(14, '2025_11_18_000012_create_exercises_table', 1),
(15, '2025_11_18_000013_create_exercise_items_table', 1),
(16, '2025_11_18_000014_create_exercise_points_table', 1),
(17, '2025_11_18_000015_create_posts_table', 1),
(18, '2025_11_18_000016_create_post_comments_table', 1),
(19, '2025_11_18_000017_create_post_child_comments_table', 1),
(20, '2025_11_18_000018_create_tasks_table', 1),
(21, '2025_11_18_000019_create_online_meetings_table', 1),
(22, '2025_11_18_000020_create_online_meeting_participants_table', 1),
(23, '2025_11_18_000021_create_reports_table', 1),
(24, '2025_11_18_000022_create_personal_access_tokens_table', 1),
(25, '2025_11_18_000023_create_sessions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `online_meetings`
--

CREATE TABLE `online_meetings` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
  `classroom_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `meeting_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meeting_link` text COLLATE utf8mb4_unicode_ci,
  `platform` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('upcoming','live','ended','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_meeting_participants`
--

CREATE TABLE `online_meeting_participants` (
  `id` int UNSIGNED NOT NULL,
  `online_meeting_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `role` enum('teacher','student') COLLATE utf8mb4_unicode_ci NOT NULL,
  `joined_at` datetime NOT NULL,
  `left_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` int UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(3, 'App\\Models\\Student', 1, 'student_token', 'a1e7f674fd8fc39a1790009f50ba4c4d62b9726dc0ce6a8b9abeba31b6a6acdb', '[\"*\"]', '2026-04-23 00:03:03', NULL, '2026-04-22 20:59:32', '2026-04-23 00:03:03'),
(4, 'App\\Models\\Student', 1, 'student_token', '77fffd4b2aadcda51d4630f5b52d903b5f231998c2b6b9ecc999288666df3956', '[\"*\"]', '2026-04-29 23:01:48', NULL, '2026-04-29 21:37:27', '2026-04-29 23:01:48');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `mapel_id` int UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `serial_id`, `user_id`, `mapel_id`, `title`, `description`, `slug`, `link`, `attachment`, `embed`, `due_date`, `category`, `is_task`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 2, 'Hitung Dasar', 'Penjumlahan dan Perkalian', 'hitung-dasar-1776327208', 'https://www.uhamka.ac.id/berita/Rumus-Hasil-Bilangan-Positif-Ditambah-Negatif-dan-Operasi-Bilangan-Bulat-Lainnya', 'posts/1776327208_5220311108-fahri-kurniawan-n-buktibayarta.pdf', '<iframe src=\"https://www.youtube.com/embed/TBh0KlmdPjY?si=MZUSpNYmm5XmtqXr\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', NULL, NULL, 0, '2026-04-16 01:13:29', '2026-04-16 01:13:29', NULL),
(2, 1, 1, 4, 'Tumbuhan Lumut', 'Lumut hijau dan merah', 'tumbuhan-lumut-1776327957', 'https://www.ruangguru.com/blog/organ-tumbuhan', 'posts/1776327957_183-sertifikat.pdf', NULL, NULL, NULL, 0, '2026-04-16 01:25:57', '2026-04-16 01:25:57', NULL),
(3, 1, 1, 7, 'Lompat Jauh', 'Buat vidio lompat jauh', 'lompat-jauh-1776498592', 'https://www.sacindonesia.com/r/841/mengenal-lompat-jauh-pengertian-sejarah-teknik-dan-gaya', 'posts/1776498592_5220311108-fahri-kurniawan-n-buktibayarta.pdf', NULL, '2026-05-06 05:00:53', NULL, 1, '2026-04-18 00:49:53', '2026-04-18 00:49:53', NULL),
(4, 1, 1, 4, 'Tanaman Hias', 'Mencari Daftar Tanaman Hias Sebanyak 10', 'tanaman-hias-1777525836', 'https://astralandindonesia.co.id/id/articles/tanaman-hias-depan-rumah', NULL, '<iframe src=\"https://www.youtube.com/embed/HaH91jTkGJ8?si=4pCsSFeF5rH2Wf_p\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" referrerpolicy=\"strict-origin-when-cross-origin\" allowfullscreen></iframe>', '2026-05-26 05:11:16', NULL, 1, '2026-04-29 22:10:36', '2026-04-29 22:10:36', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `post_child_comments`
--

CREATE TABLE `post_child_comments` (
  `id` int UNSIGNED NOT NULL,
  `post_comment_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `student_id` int UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int UNSIGNED NOT NULL,
  `post_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `student_id` int UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_user` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_comments`
--

INSERT INTO `post_comments` (`id`, `post_id`, `user_id`, `student_id`, `message`, `code`, `is_user`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 'oke', 'CMT69e09b6202f5a', 0, '2026-04-16 01:18:42', '2026-04-16 01:18:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `lesson_id` int UNSIGNED NOT NULL,
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
(1, 1, 'Paket SD Kelas V', 'V', 'SD', '1', '2025-11-18 01:26:56', '2025-11-18 01:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
  `student_id` int UNSIGNED NOT NULL,
  `report` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `serials`
--

CREATE TABLE `serials` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `product_id` int UNSIGNED NOT NULL,
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
(1, 1, 1, 'SERIAL12345', '4', 'yes', '2026-05-18 01:26:56', '2025-11-18 01:26:56', '2025-11-18 01:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('RowtG0XaVyNaxUhxZrZmB8UT9nNee2hAmJ5Atisy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiYW1LMkQ1aTNuWEI2U2dXd0FuYVZEanhySG1ZbURHeER0MGY3aUFocCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC90ZWFjaGVyL3Bvc3RzL2NyZWF0ZSI7czo1OiJyb3V0ZSI7czoyMDoidGVhY2hlci5wb3N0cy5jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1777525837);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `classroom_id` int UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `serial_id`, `user_id`, `classroom_id`, `name`, `username`, `password`, `password_text`, `nis`, `absen_number`, `email`, `phone`, `photo`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 'Reno Saputra', 'reno', '$2y$12$en4h66VY0JPVc1bXjwf/We.nxW9goxBSOgBjeY2Vq/ma80K3AtKDW', 'reno123', '2025001', 1, 'reno@student.com', '08122334455', 'students/twz2bJw0BcLggnXfcBpOn6r91whcDrYU2K0V3fQT.jpg', '2025-11-18 01:26:57', '2026-02-25 05:56:09', NULL),
(2, 1, 1, 1, 'Budi Santoso Raul', 'budi', '$2y$12$RUsCa0Itl4t8QFGqr3/E8OM8kJMuIWTgP61hMKOMBsWhZw0zrkb4S', 'budi12345', '8983978923', 2, 'budiraul34@gmail.com', '08395566877', 'students/GbXqFMLWPh8MjuyhVNtULB6oTnLujZuKBpDH2aZC.jpg', '2025-11-22 03:21:15', '2026-03-05 15:45:31', NULL),
(3, 1, 1, 2, 'Heri Kopling', 'heri', '$2y$12$c9TMGJ4HhKqvk6rqqX1OB.B9AYCWuCe.OQoyQQTLM0PGRRwfXD6sS', 'heri123', '83926362', NULL, 'heri@gmail.com', '08382263548', NULL, '2025-11-26 09:33:06', '2025-11-26 09:33:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int UNSIGNED NOT NULL,
  `serial_id` int UNSIGNED NOT NULL,
  `post_id` int UNSIGNED NOT NULL,
  `student_id` int UNSIGNED NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment` text COLLATE utf8mb4_unicode_ci,
  `point` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `serial_id`, `post_id`, `student_id`, `description`, `attachment`, `point`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 1, 4, 1, 'tugas Reno', '1777528880_Screenshot_2026-04-27-20-26-30-791_com.gojek.resto.jpg', NULL, '2026-04-29 23:01:21', '2026-04-29 23:01:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
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
(1, 'Fahri Kurniawan S.Kom', 'guru_mtk', '$2y$12$qc.OfrU1CVu3TsoH5DwBrugiHi7xYpY3XLy3cI339m8ZJQQZr5q0W', 'guru@sekolah.com', 1, 'Jl. Pendidikan No.1', '081234567890', NULL, NULL, '2025-11-18 01:26:56', '2025-12-26 00:46:03'),
(2, 'Guru Kedua', 'gurukedua', '$2y$10$UxSJr9TFHj3fYTGTrkrGRuO6IbCJMZdu80NAbFBgMiMKCvQJcKR6i', 'guru2@example.com', 1, 'Sekolah Indonesia', '081234567890', NULL, '2025-12-25 23:49:15', '2025-12-25 23:49:15', '2025-12-25 23:49:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_username_unique` (`username`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

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
  ADD KEY `exercises_serial_id_foreign` (`serial_id`),
  ADD KEY `exercises_exercise_type_id_foreign` (`exercise_type_id`),
  ADD KEY `exercises_lesson_id_serial_id_index` (`lesson_id`,`serial_id`);

--
-- Indexes for table `exercise_items`
--
ALTER TABLE `exercise_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exercise_items_admin_id_foreign` (`admin_id`),
  ADD KEY `exercise_items_user_id_foreign` (`user_id`),
  ADD KEY `exercise_items_competence_id_foreign` (`competence_id`),
  ADD KEY `exercise_items_exercise_id_foreign` (`exercise_id`),
  ADD KEY `exercise_items_exercise_type_id_foreign` (`exercise_type_id`),
  ADD KEY `exercise_items_exercise_model_id_foreign` (`exercise_model_id`);

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
  ADD KEY `exercise_points_student_id_exercise_id_index` (`student_id`,`exercise_id`);

--
-- Indexes for table `exercise_types`
--
ALTER TABLE `exercise_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lessons_mapel_id_grade_index` (`mapel_id`,`grade`);

--
-- Indexes for table `mapels`
--
ALTER TABLE `mapels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_meetings`
--
ALTER TABLE `online_meetings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `online_meetings_meeting_code_unique` (`meeting_code`),
  ADD KEY `online_meetings_serial_id_foreign` (`serial_id`),
  ADD KEY `online_meetings_classroom_id_index` (`classroom_id`),
  ADD KEY `online_meetings_user_id_index` (`user_id`),
  ADD KEY `online_meetings_start_time_index` (`start_time`);

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
  ADD KEY `posts_user_id_foreign` (`user_id`),
  ADD KEY `posts_mapel_id_foreign` (`mapel_id`),
  ADD KEY `posts_serial_id_is_task_index` (`serial_id`,`is_task`);

--
-- Indexes for table `post_child_comments`
--
ALTER TABLE `post_child_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_child_comments_post_comment_id_index` (`post_comment_id`),
  ADD KEY `post_child_comments_user_id_index` (`user_id`),
  ADD KEY `post_child_comments_student_id_index` (`student_id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_comments_post_id_index` (`post_id`),
  ADD KEY `post_comments_user_id_index` (`user_id`),
  ADD KEY `post_comments_student_id_index` (`student_id`);

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
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_serial_id_index` (`serial_id`),
  ADD KEY `students_classroom_id_index` (`classroom_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_serial_id_foreign` (`serial_id`),
  ADD KEY `tasks_post_id_foreign` (`post_id`),
  ADD KEY `tasks_student_id_post_id_index` (`student_id`,`post_id`);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `classrooms`
--
ALTER TABLE `classrooms`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `competences`
--
ALTER TABLE `competences`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exercise_items`
--
ALTER TABLE `exercise_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `exercise_models`
--
ALTER TABLE `exercise_models`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exercise_points`
--
ALTER TABLE `exercise_points`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `exercise_types`
--
ALTER TABLE `exercise_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mapels`
--
ALTER TABLE `mapels`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `online_meetings`
--
ALTER TABLE `online_meetings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_meeting_participants`
--
ALTER TABLE `online_meeting_participants`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `post_child_comments`
--
ALTER TABLE `post_child_comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `serials`
--
ALTER TABLE `serials`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `exercise_items_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exercise_items_competence_id_foreign` FOREIGN KEY (`competence_id`) REFERENCES `competences` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `exercise_items_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_items_exercise_model_id_foreign` FOREIGN KEY (`exercise_model_id`) REFERENCES `exercise_models` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_items_exercise_type_id_foreign` FOREIGN KEY (`exercise_type_id`) REFERENCES `exercise_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exercise_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `online_meetings_serial_id_foreign` FOREIGN KEY (`serial_id`) REFERENCES `serials` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_meetings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_meeting_participants`
--
ALTER TABLE `online_meeting_participants`
  ADD CONSTRAINT `online_meeting_participants_online_meeting_id_foreign` FOREIGN KEY (`online_meeting_id`) REFERENCES `online_meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `online_meeting_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `post_child_comments_post_comment_id_foreign` FOREIGN KEY (`post_comment_id`) REFERENCES `post_comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_child_comments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `post_child_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD CONSTRAINT `post_comments_post_id_foreign` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_comments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `post_comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

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
