-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 02, 2026 at 08:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edutrack`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `update_nilai_akhir` ()   BEGIN
    UPDATE nilai
    SET nilai_akhir = CASE
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 81 THEN 'A'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 71 THEN 'B+'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 61 THEN 'B'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 51 THEN 'C+'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 41 THEN 'C'
        ELSE 'D'
    END;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `kode_admin` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `no_telepon` varchar(255) NOT NULL,
  `alamat` text DEFAULT NULL,
  `jenis_kelamin` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`kode_admin`, `user_id`, `nama`, `email`, `tanggal_lahir`, `no_telepon`, `alamat`, `jenis_kelamin`, `created_at`, `updated_at`) VALUES
('ADM001', 1, 'Super Admin', 'admin@edutrack.com', '1990-01-01', '08123456789', NULL, 'Laki-laki', '2025-12-28 22:47:56', '2025-12-29 02:24:07');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dkbs`
--

CREATE TABLE `dkbs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nrp` varchar(255) NOT NULL,
  `kode_mk` varchar(255) NOT NULL,
  `id_perkuliahan` bigint(20) UNSIGNED DEFAULT NULL,
  `semester` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tahun_ajaran` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dkbs`
--

INSERT INTO `dkbs` (`id`, `nrp`, `kode_mk`, `id_perkuliahan`, `semester`, `status`, `created_at`, `updated_at`, `tahun_ajaran`) VALUES
(8, '2472021', 'MK004', 4, '3', 'Terdaftar', '2025-12-29 00:26:41', '2025-12-29 04:32:28', '2025/2026 - Ganjil'),
(9, '2472021', 'MK001T', 10, '3', 'Terdaftar', '2025-12-29 00:27:00', '2025-12-29 04:39:23', '2025/2026 - Ganjil'),
(10, '2472021', 'MK001P', 11, '3', 'Terdaftar', '2025-12-29 00:27:18', '2025-12-29 04:39:34', '2025/2026 - Ganjil'),
(11, '2472021', 'MK002T', 6, '3', 'Terdaftar', '2025-12-29 00:27:31', '2025-12-29 04:39:45', '2025/2026 - Ganjil'),
(12, '2472021', 'MK002P', 7, '3', 'Terdaftar', '2025-12-29 00:27:44', '2025-12-29 04:40:06', '2025/2026 - Ganjil'),
(13, '2472022', 'MK001P', 11, '3', 'Terdaftar', '2025-12-29 00:35:40', '2025-12-29 04:40:15', '2025/2026 - Ganjil'),
(14, '2472021', 'MK005', 5, '3', 'Terdaftar', '2025-12-29 04:40:40', '2025-12-29 04:40:40', '2025/2026 - Ganjil'),
(15, '2472022', 'MK005', 5, '3', 'Terdaftar', '2026-01-02 00:07:47', '2026-01-02 00:07:47', '2025/2026 - Ganjil');

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `nip` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `no_telepon` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `fakultas` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`nip`, `user_id`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `email`, `no_telepon`, `alamat`, `fakultas`, `created_at`, `updated_at`) VALUES
('0072201', 2, 'Tjatur Kandaga, S.Si., M.T.', '1985-05-20', 'Laki-laki', '0072201@edutrack.com', '08987654321', 'Jl. Melati No. 12', 'Teknologi Rekayasa Cerdas', '2025-12-28 22:47:57', '2025-12-29 04:51:07'),
('0072202', 9, 'Ir. Teddy Marcus Zakaria, M.T.', '1986-11-05', 'Laki-laki', '0072202@edutrack.com', '081234567890', 'Jl. Kenanga Raya No. 45', 'Teknologi Rekayasa Cerdas', '2025-12-29 01:41:28', '2025-12-29 02:19:07'),
('0072203', 8, 'Erico Darmawan Handoyo, S. Kom., M.T.', '1990-01-09', 'Laki-laki', '0072203@edutrack.com', '087656728372', 'Jl. Mawar Indah No. 7', 'Teknologi Rekayasa Cerdas', '2025-12-29 01:37:49', '2025-12-29 02:19:17'),
('0072204', 10, 'Andreas Widjaja, S.Si., M.Sc., Ph.D', '1980-10-20', 'Laki-laki', '0072204@edutrack.com', '083822334455', 'Jl. Anggrek Lestari No. 88', 'Teknologi Rekayasa Cerdas', '2025-12-29 02:22:52', '2025-12-29 02:22:52'),
('0072205', 11, 'Maresha Caroline Wijanto', '1990-07-19', 'Perempuan', '0072205@edutrack.com', '081333445566', 'Jl. Cendana No. 23', 'Teknologi Rekayasa Cerdas', '2025-12-29 02:26:23', '2025-12-29 02:28:04'),
('0072206', 12, 'Wenny Franciska Senjaya', '1992-03-17', 'Perempuan', '0072206@edutrack.com', '081245678901', 'Jl. Flamboyan No. 56', 'Teknologi Rekayasa Cerdas', '2025-12-29 02:27:57', '2025-12-29 02:27:57'),
('0072207', 13, 'Meliana Christianti J., S. Kom., M.T.', '1996-09-10', 'Perempuan', '0072207@edutrack.com', '082134567890', 'Jl. Dahlia No. 89', 'Teknologi Rekayasa Cerdas', '2025-12-29 02:29:33', '2025-12-29 02:29:33');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_mk` varchar(255) NOT NULL,
  `nidn` varchar(255) DEFAULT NULL,
  `hari` varchar(255) DEFAULT NULL,
  `jam` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `nrp` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jurusan` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `jenis_kelamin` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telepon` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nrp`, `user_id`, `nama`, `jurusan`, `email`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `no_telepon`, `created_at`, `updated_at`) VALUES
('2472021', 3, 'Ferdinand Brian', 'Teknik Informatika', '2472021@edutrack.com', 'Laki-laki', '2004-10-15', 'Jl. Kebon Jeruk No. 12', '087712345678', '2025-12-28 22:47:57', '2025-12-29 04:29:01'),
('2472022', 4, 'Bryan Christian Miercoles', 'Teknik Informatika', '2472022@edutrack.com', 'Laki-laki', '2006-07-28', 'Kopo', '089626312728', '2025-12-28 23:07:50', '2026-01-01 23:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `kode_mk` varchar(255) NOT NULL,
  `nama_mk` varchar(255) NOT NULL,
  `sks` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `sks`, `semester`, `created_at`, `updated_at`) VALUES
('MK001P', 'Paradigma Pemrograman (Praktikum)', 2, 3, '2025-12-29 06:54:13', '2025-12-29 06:54:13'),
('MK001T', 'Paradigma Pemrograman (Teori)', 2, 3, '2025-12-29 06:54:13', '2025-12-29 06:54:13'),
('MK002P', 'Desain Basis Data Lanjut (Praktikum)', 3, 3, '2025-12-29 06:54:13', '2025-12-29 06:54:13'),
('MK002T', 'Desain Basis Data Lanjut (Teori)', 3, 3, '2025-12-29 06:54:13', '2025-12-29 00:59:37'),
('MK003', 'Rekayasa Perangkat Lunak', 3, 3, '2025-12-29 06:54:13', '2025-12-29 01:04:02'),
('MK004', 'Teknologi Multimedia', 2, 3, '2025-12-29 00:40:18', '2025-12-29 00:58:31'),
('MK005', 'Matematika Diskrit', 3, 3, '2025-12-29 00:59:19', '2025-12-29 00:59:19'),
('MK006P', 'Algoritma Struktur Data (Praktikum)', 3, 3, '2025-12-29 01:17:34', '2025-12-29 01:17:34'),
('MK006T', 'Algoritma Struktur Data (Teori)', 3, 3, '2025-12-29 01:05:13', '2025-12-29 01:17:44'),
('MK007', 'Sistem Operasi Komputer', 2, 3, '2025-12-29 01:18:06', '2025-12-29 01:18:06');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_12_28_095340_create_sessions_table', 1),
(4, '2025_12_28_102000_create_presensi_table', 1),
(5, '2025_12_28_103000_create_nilai_jadwal_tagihan_dkbs_tables', 1),
(6, '2025_12_29_100000_setup_identity_system', 1),
(7, '2025_12_29_061613_create_mata_kuliahs_table', 2),
(8, '2025_12_29_075654_add_details_to_mata_kuliah_table', 3),
(9, '2025_12_29_081043_add_id_perkuliahan_to_dkbs', 4),
(10, '2025_12_29_084255_add_alamat_to_admin_and_dosen_tables', 5),
(11, '2025_12_29_115615_add_meeting_scores_to_nilai_table', 6),
(12, '2025_12_31_125040_create_pengumumen_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `nilai`
--

CREATE TABLE `nilai` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nrp` varchar(255) NOT NULL,
  `kode_mk` varchar(255) NOT NULL,
  `p1` double NOT NULL DEFAULT 0,
  `p2` double NOT NULL DEFAULT 0,
  `p3` double NOT NULL DEFAULT 0,
  `p4` double NOT NULL DEFAULT 0,
  `p5` double NOT NULL DEFAULT 0,
  `p6` double NOT NULL DEFAULT 0,
  `p7` double NOT NULL DEFAULT 0,
  `uts` double NOT NULL DEFAULT 0,
  `p9` double NOT NULL DEFAULT 0,
  `p10` double NOT NULL DEFAULT 0,
  `p11` double NOT NULL DEFAULT 0,
  `p12` double NOT NULL DEFAULT 0,
  `p13` double NOT NULL DEFAULT 0,
  `p14` double NOT NULL DEFAULT 0,
  `p15` double NOT NULL DEFAULT 0,
  `uas` double NOT NULL DEFAULT 0,
  `nilai_total` double NOT NULL DEFAULT 0,
  `nilai_akhir` varchar(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text DEFAULT NULL,
  `kategori` varchar(255) NOT NULL DEFAULT 'umum',
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `kategori`, `waktu_mulai`, `waktu_selesai`, `created_at`, `updated_at`) VALUES
(1, 'Libur Natal & Tahun Baru', 'Seluruh kegiatan akademik diliburkan dalam rangka libur Natal dan Tahun Baru.', 'libur', '2025-12-30 00:00:00', '2026-01-04 00:00:00', '2025-12-31 05:56:07', '2026-01-02 00:01:04'),
(2, 'Masa Ujian Akhir Semester (UAS)', 'Pelaksanaan UAS', 'akademik', '2026-01-19 00:00:00', '2026-01-23 00:00:00', '2026-01-01 23:46:34', '2026-01-02 00:01:47'),
(4, 'Masa Ujian Akhir Semester (UAS)', 'Pelaksanaan UAS', 'akademik', '2026-01-26 00:00:00', '2026-01-27 00:00:00', '2026-01-02 00:01:25', '2026-01-02 00:01:39');

-- --------------------------------------------------------

--
-- Table structure for table `perkuliahan`
--

CREATE TABLE `perkuliahan` (
  `id_perkuliahan` bigint(20) UNSIGNED NOT NULL,
  `kode_ruangan` varchar(50) NOT NULL,
  `nip_dosen` varchar(50) NOT NULL,
  `kode_mk` varchar(255) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `hari` varchar(10) NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_berakhir` time NOT NULL,
  `tahun_ajaran` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `perkuliahan`
--

INSERT INTO `perkuliahan` (`id_perkuliahan`, `kode_ruangan`, `nip_dosen`, `kode_mk`, `kelas`, `hari`, `jam_mulai`, `jam_berakhir`, `tahun_ajaran`, `created_at`, `updated_at`) VALUES
(2, 'L8002', '0072206', 'MK006T', 'B', 'Selasa', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2025-12-29 04:03:46', '2025-12-29 11:05:24'),
(3, 'L8002', '0072206', 'MK006P', 'B', 'Selasa', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2025-12-29 04:22:39', '2025-12-29 04:22:39'),
(4, 'L8001', '0072203', 'MK004', 'B', 'Senin', '09:30:00', '11:10:00', '2025/2026 - Ganjil', '2025-12-29 04:24:07', '2025-12-29 04:24:07'),
(5, 'L8005', '0072204', 'MK005', 'C', 'Senin', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2025-12-29 04:24:47', '2025-12-29 04:24:47'),
(6, 'L8003', '0072202', 'MK002T', 'C', 'Senin', '17:30:00', '19:30:00', '2025/2026 - Ganjil', '2025-12-29 04:25:18', '2025-12-29 04:25:18'),
(7, 'L8003', '0072202', 'MK002P', 'C', 'Rabu', '17:30:00', '19:30:00', '2025/2026 - Ganjil', '2025-12-29 04:26:12', '2025-12-29 04:26:12'),
(8, 'L8005', '0072205', 'MK003', 'B', 'Selasa', '15:00:00', '17:30:00', '2025/2026 - Ganjil', '2025-12-29 04:26:50', '2025-12-29 04:26:50'),
(9, 'L8006', '0072207', 'MK007', 'A', 'Jumat', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2025-12-29 04:27:21', '2025-12-29 04:27:21'),
(10, 'L8004', '0072201', 'MK001T', 'A', 'Kamis', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2025-12-29 04:37:14', '2025-12-29 04:37:14'),
(11, 'L8004', '0072201', 'MK001P', 'A', 'Kamis', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2025-12-29 04:37:48', '2025-12-29 04:37:48');

-- --------------------------------------------------------

--
-- Table structure for table `presensi`
--

CREATE TABLE `presensi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nrp` varchar(255) NOT NULL,
  `jadwal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal` date NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Hadir',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ruangan`
--

CREATE TABLE `ruangan` (
  `kode_ruangan` varchar(50) NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `kapasitas` int(10) UNSIGNED NOT NULL,
  `deskripsi_fasilitas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ruangan`
--

INSERT INTO `ruangan` (`kode_ruangan`, `nama_ruangan`, `kapasitas`, `deskripsi_fasilitas`, `created_at`, `updated_at`) VALUES
('L8001', 'ADV 1', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:29'),
('L8002', 'ADV 2', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:33'),
('L8003', 'ADV 3', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:35'),
('L8004', 'ADV 4', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:38'),
('L8005', 'Ruang Multimedia', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8006', 'Ruang Lab Inter', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8007', 'Lab Int 1', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8008', 'Lab Int 2', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8009', 'Lab Int 3', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8010', 'Lab Int 4', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('FOrmCoFtZDapUEOs3xPiKf4lo2l8XzMp0blCz4um', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoick81YmRZeTJldW5MMXlTeGRTcEw0cjJDc2VzRzFKUlo3dnBHQlJEayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9tYWhhc2lzd2EvZGFzaGJvYXJkIjtzOjU6InJvdXRlIjtOO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTo0O30=', 1767337692);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nrp` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dosen','mahasiswa') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@edutrack.com', '$2y$12$u7ulJYN1pJyUv5QE.bpe.uOvPdLrDICmoBptA4f/HChpPVQIfL5z6', 'admin', '2025-12-28 22:47:56', '2025-12-29 02:24:07'),
(2, 'Tjatur Kandaga, S.Si., M.T.', '0072201@edutrack.com', '$2y$12$e5Kaw4D55V36Rw.wdMcQ1e/E5hCdPMrcnmpIm11dVKj/uRov3sdlu', 'dosen', '2025-12-28 22:47:56', '2025-12-29 04:51:07'),
(3, 'Ferdinand Brian', '2472021@edutrack.com', '$2y$12$/ktDUMf/NA/DodKRUSfS4OV/0B4sXdJ0fj0KmtOm35m5So4wOnMQS', 'mahasiswa', '2025-12-28 22:47:57', '2025-12-29 04:29:01'),
(4, 'Bryan Christian Miercoles', '2472022@edutrack.com', '$2y$12$TBxLGZmNmf7.HOPmgOv/bOyM31ciRQVgDGFZaTRxdtCZw0NsoFSzq', 'mahasiswa', '2025-12-28 23:07:49', '2025-12-30 23:02:30'),
(8, 'Erico Darmawan Handoyo, S. Kom., M.T.', '0072203@edutrack.com', '$2y$12$ZkhJeKnn8uk1r47keAw8Iu.LSTozgsMR71tcdmzzWfHj7nEpuK1RO', 'dosen', '2025-12-29 01:37:49', '2025-12-29 01:37:49'),
(9, 'Ir. Teddy Marcus Zakaria, M.T.', '0072202@edutrack.com', '$2y$12$hWI4nORCg218JJJvBq5sL.jM8YO7sUcczMLtO8cmMgGeXoI/a4ofG', 'dosen', '2025-12-29 01:41:28', '2025-12-29 01:41:28'),
(10, 'Andreas Widjaja, S.Si., M.Sc., Ph.D', '0072204@edutrack.com', '$2y$12$mQjSGR8xatAV9hWs4u3YAu6COj3HerCvlh5pDV/wKtEBkF8vtGLAe', 'dosen', '2025-12-29 02:22:52', '2025-12-29 02:22:52'),
(11, 'Maresha Caroline Wijanto', '0072205@edutrack.com', '$2y$12$LFIH2.6ZM6ch8BrELeYtue0zoRWxwZj2SCqMiN6LajNk9u1gkxQ3S', 'dosen', '2025-12-29 02:26:23', '2025-12-29 02:26:23'),
(12, 'Wenny Franciska Senjaya', '0072206@edutrack.com', '$2y$12$k6T/1O/KDSHxOZJkoJL.S.uTAVZ1AAUWTnsJnC9FTtLTyb3OHLUUy', 'dosen', '2025-12-29 02:27:57', '2025-12-29 02:27:57'),
(13, 'Meliana Christianti J., S. Kom., M.T.', '0072207@edutrack.com', '$2y$12$C97naCO4OmqMPpkFmYkffOw.UL5tu5t.n17lxAeqFJ40CUoAc4Sxe', 'dosen', '2025-12-29 02:29:33', '2025-12-29 02:29:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`kode_admin`),
  ADD UNIQUE KEY `admin_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `admin_email_unique` (`email`);

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
-- Indexes for table `dkbs`
--
ALTER TABLE `dkbs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dkbs_nrp_index` (`nrp`),
  ADD KEY `fk_dkbs_kode_mk` (`kode_mk`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`nip`),
  ADD UNIQUE KEY `dosen_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `dosen_email_unique` (`email`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`nrp`),
  ADD UNIQUE KEY `mahasiswa_user_id_unique` (`user_id`),
  ADD UNIQUE KEY `mahasiswa_email_unique` (`email`);

--
-- Indexes for table `mata_kuliah`
--
ALTER TABLE `mata_kuliah`
  ADD PRIMARY KEY (`kode_mk`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nilai`
--
ALTER TABLE `nilai`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nilai_nrp_index` (`nrp`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perkuliahan`
--
ALTER TABLE `perkuliahan`
  ADD PRIMARY KEY (`id_perkuliahan`),
  ADD KEY `fk_ruangan` (`kode_ruangan`),
  ADD KEY `fk_dosen` (`nip_dosen`),
  ADD KEY `fk_mk` (`kode_mk`);

--
-- Indexes for table `presensi`
--
ALTER TABLE `presensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `presensi_nrp_index` (`nrp`);

--
-- Indexes for table `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`kode_ruangan`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tagihan`
--
ALTER TABLE `tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tagihan_nrp_index` (`nrp`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dkbs`
--
ALTER TABLE `dkbs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `perkuliahan`
--
ALTER TABLE `perkuliahan`
  MODIFY `id_perkuliahan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dkbs`
--
ALTER TABLE `dkbs`
  ADD CONSTRAINT `fk_dkbs_kode_mk` FOREIGN KEY (`kode_mk`) REFERENCES `mata_kuliah` (`kode_mk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perkuliahan`
--
ALTER TABLE `perkuliahan`
  ADD CONSTRAINT `fk_dosen` FOREIGN KEY (`nip_dosen`) REFERENCES `dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mk` FOREIGN KEY (`kode_mk`) REFERENCES `mata_kuliah` (`kode_mk`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ruangan` FOREIGN KEY (`kode_ruangan`) REFERENCES `ruangan` (`kode_ruangan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
