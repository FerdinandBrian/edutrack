-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 08, 2026 at 05:49 AM
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
CREATE DEFINER=`` PROCEDURE `sp_bayar_tagihan` (IN `tagihan_id` INT)   BEGIN
                UPDATE tagihan SET status = 'Lunas', updated_at = NOW() WHERE id = tagihan_id;
            END$$

CREATE DEFINER=`` PROCEDURE `sp_get_matkul_by_jurusan` (IN `p_jurusan` VARCHAR(255))   BEGIN
                SELECT * 
                FROM mata_kuliah 
                WHERE jurusan = p_jurusan COLLATE utf8mb4_unicode_ci
                ORDER BY semester ASC, nama_mk ASC;
            END$$

CREATE DEFINER=`` PROCEDURE `sp_get_perkuliahan_by_ta` (IN `p_ta` VARCHAR(255))   BEGIN
    SELECT 
        p.id_perkuliahan,
        p.kode_mk,
        m.nama_mk,
        p.kelas,
        p.hari,
        p.jam_mulai,
        p.jam_berakhir
    FROM perkuliahan p
    JOIN mata_kuliah m ON p.kode_mk = m.kode_mk
    WHERE p.tahun_ajaran = p_ta
    ORDER BY m.nama_mk ASC, p.kelas ASC;
END$$

CREATE DEFINER=`` PROCEDURE `sp_get_perkuliahan_by_ta_and_jurusan` (IN `p_ta` VARCHAR(255), IN `p_jurusan` VARCHAR(255))   BEGIN
                SELECT 
                    p.id_perkuliahan,
                    p.kode_mk,
                    m.nama_mk,
                    m.jurusan,
                    p.kelas,
                    p.hari,
                    p.jam_mulai,
                    p.jam_berakhir
                FROM perkuliahan p
                JOIN mata_kuliah m ON p.kode_mk = m.kode_mk
                WHERE p.tahun_ajaran = p_ta
                  AND m.jurusan = p_jurusan
                ORDER BY m.nama_mk ASC, p.kelas ASC;
            END$$

CREATE DEFINER=`` PROCEDURE `sp_get_va` (IN `student_nrp` VARCHAR(50))   BEGIN
                SELECT CONCAT('2911', student_nrp) AS va;
            END$$

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

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `get_ipk` (`p_nrp` VARCHAR(20) COLLATE utf8mb4_unicode_ci) RETURNS DECIMAL(3,2) DETERMINISTIC BEGIN
    DECLARE v_total_bobot FLOAT DEFAULT 0;
    DECLARE v_total_sks INT DEFAULT 0;
    DECLARE v_ipk DECIMAL(3,2) DEFAULT 0;

    SELECT 
        SUM(CASE 
            WHEN n.nilai_akhir = 'A' THEN 4.0 * mk.sks
            WHEN n.nilai_akhir = 'B' THEN 3.0 * mk.sks
            WHEN n.nilai_akhir = 'C' THEN 2.0 * mk.sks
            WHEN n.nilai_akhir = 'D' THEN 1.0 * mk.sks
            ELSE 0 
        END),
        SUM(mk.sks)
    INTO v_total_bobot, v_total_sks
    FROM nilai n
    JOIN mata_kuliah mk ON n.kode_mk = mk.kode_mk
    WHERE n.nrp = p_nrp;

    IF v_total_sks > 0 THEN
        SET v_ipk = v_total_bobot / v_total_sks;
    END IF;

    RETURN v_ipk;
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `get_total_sks` (`p_nrp` VARCHAR(20) COLLATE utf8mb4_unicode_ci, `p_ta` VARCHAR(50) COLLATE utf8mb4_unicode_ci) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;
    
    SELECT SUM(mk.sks) INTO total
    FROM dkbs d
    JOIN mata_kuliah mk ON d.kode_mk = mk.kode_mk
    WHERE d.nrp = p_nrp AND d.tahun_ajaran = p_ta;
    
    RETURN IFNULL(total, 0);
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
  `admin_level` enum('super','second') NOT NULL DEFAULT 'second',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`kode_admin`, `user_id`, `nama`, `email`, `tanggal_lahir`, `no_telepon`, `alamat`, `jenis_kelamin`, `admin_level`, `created_at`, `updated_at`) VALUES
('ADM001', 1, 'Super Admin', 'admin@edutrack.com', '1990-01-01', '08123456789', NULL, 'Laki-laki', 'super', '2025-12-28 22:47:56', '2025-12-29 02:24:07'),
('ADM002', 22, 'Seccond Admin', 'admin2@edutrack.com', '1991-01-01', '089876543212', NULL, 'Laki-laki', 'second', '2026-01-07 12:00:59', '2026-01-07 12:00:59');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs_nilai`
--

CREATE TABLE `audit_logs_nilai` (
  `id` int(11) NOT NULL,
  `nrp` varchar(20) DEFAULT NULL,
  `kode_mk` varchar(20) DEFAULT NULL,
  `nilai_lama_total` float DEFAULT NULL,
  `nilai_baru_total` float DEFAULT NULL,
  `perubahan_oleh` varchar(100) DEFAULT NULL,
  `waktu_perubahan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(8, '2472021', 'MK004', 4, '3', 'Terdaftar', '2025-12-29 00:26:41', '2026-01-03 00:09:30', '2025/2026 - Ganjil'),
(9, '2472021', 'MK001T', 10, '3', 'Terdaftar', '2025-12-29 00:27:00', '2025-12-29 04:39:23', '2025/2026 - Ganjil'),
(10, '2472021', 'MK001P', 11, '3', 'Terdaftar', '2025-12-29 00:27:18', '2025-12-29 04:39:34', '2025/2026 - Ganjil'),
(11, '2472021', 'MK002T', 6, '3', 'Terdaftar', '2025-12-29 00:27:31', '2025-12-29 04:39:45', '2025/2026 - Ganjil'),
(12, '2472021', 'MK002P', 7, '3', 'Terdaftar', '2025-12-29 00:27:44', '2025-12-29 04:40:06', '2025/2026 - Ganjil'),
(14, '2472021', 'MK005', 5, '3', 'Terdaftar', '2025-12-29 04:40:40', '2025-12-29 04:40:40', '2025/2026 - Ganjil'),
(15, '2472021', 'MK003', 8, '3', 'Terdaftar', '2026-01-02 22:25:30', '2026-01-02 22:25:30', '2025/2026 - Ganjil'),
(16, '2472021', 'MK006T', 2, '3', 'Terdaftar', '2026-01-02 22:33:04', '2026-01-02 22:33:04', '2025/2026 - Ganjil'),
(17, '2472021', 'MK006P', 3, '3', 'Terdaftar', '2026-01-02 22:33:14', '2026-01-02 22:33:14', '2025/2026 - Ganjil'),
(18, '2472021', 'MK007', 9, '3', 'Terdaftar', '2026-01-02 22:33:28', '2026-01-02 22:33:28', '2025/2026 - Ganjil'),
(19, '2472022', 'MK004', 4, '3', 'Terdaftar', '2026-01-03 00:13:40', '2026-01-03 00:13:40', '2025/2026 - Ganjil'),
(20, '2472022', 'MK002T', 6, '3', 'Terdaftar', '2026-01-03 00:25:33', '2026-01-03 00:25:33', '2025/2026 - Ganjil'),
(21, '2472022', 'MK002P', 7, '3', 'Terdaftar', '2026-01-03 00:25:33', '2026-01-03 00:25:33', '2025/2026 - Ganjil'),
(22, '2472022', 'MK006T', 2, '3', 'Terdaftar', '2026-01-03 00:25:58', '2026-01-03 00:25:58', '2025/2026 - Ganjil'),
(23, '2472022', 'MK006P', 3, '3', 'Terdaftar', '2026-01-03 00:25:58', '2026-01-03 00:25:58', '2025/2026 - Ganjil'),
(24, '2472022', 'MK003', 8, '3', 'Terdaftar', '2026-01-03 00:26:24', '2026-01-03 00:26:24', '2025/2026 - Ganjil'),
(25, '2472022', 'MK005', 5, '3', 'Terdaftar', '2026-01-03 00:26:39', '2026-01-03 00:26:39', '2025/2026 - Ganjil'),
(26, '2472022', 'MK007', 9, '3', 'Terdaftar', '2026-01-03 00:26:54', '2026-01-03 00:26:54', '2025/2026 - Ganjil'),
(27, '2472022', 'MK001T', 10, '3', 'Terdaftar', '2026-01-03 00:28:00', '2026-01-03 00:28:00', '2025/2026 - Ganjil'),
(28, '2472022', 'MK001P', 11, '3', 'Terdaftar', '2026-01-03 00:28:00', '2026-01-03 00:28:00', '2025/2026 - Ganjil'),
(29, '2473020', 'MK103', 31, '1', 'Terdaftar', '2026-01-07 11:35:04', '2026-01-07 11:35:04', '2025/2026 - Ganjil'),
(30, '2473020', 'MK101T', 33, '1', 'Terdaftar', '2026-01-07 11:46:20', '2026-01-07 11:46:20', '2025/2026 - Ganjil'),
(31, '2473020', 'MK101P', 34, '1', 'Terdaftar', '2026-01-07 11:46:20', '2026-01-07 11:46:20', '2025/2026 - Ganjil'),
(32, '2473020', 'MK106T', 35, '2', 'Terdaftar', '2026-01-07 12:19:30', '2026-01-07 12:19:30', '2025/2026 - Ganjil'),
(33, '2473020', 'MK106P', 36, '2', 'Terdaftar', '2026-01-07 12:19:30', '2026-01-07 12:19:30', '2025/2026 - Ganjil'),
(37, '2473021', 'MK106T', 39, '2', 'Terdaftar', '2026-01-07 21:35:49', '2026-01-07 21:35:49', '2025/2026 - Ganjil'),
(38, '2473021', 'MK106P', 40, '2', 'Terdaftar', '2026-01-07 21:35:49', '2026-01-07 21:35:49', '2025/2026 - Ganjil'),
(39, '2473021', 'MK103', 37, '1', 'Terdaftar', '2026-01-07 21:36:17', '2026-01-07 21:36:17', '2025/2026 - Ganjil'),
(40, '2473021', 'MK101T', 43, '1', 'Terdaftar', '2026-01-07 21:37:05', '2026-01-07 21:37:05', '2025/2026 - Ganjil'),
(41, '2473021', 'MK101P', 44, '1', 'Terdaftar', '2026-01-07 21:37:05', '2026-01-07 21:37:05', '2025/2026 - Ganjil');

--
-- Triggers `dkbs`
--
DELIMITER $$
CREATE TRIGGER `trg_cek_kapasitas_dkbs` BEFORE INSERT ON `dkbs` FOR EACH ROW BEGIN
    DECLARE cap INT;
    DECLARE enrolled INT;
    
    -- Ambil kapasitas dari ruangan melalui tabel perkuliahan
    SELECT r.kapasitas INTO cap 
    FROM perkuliahan p
    JOIN ruangan r ON p.kode_ruangan = r.kode_ruangan
    WHERE p.id_perkuliahan = NEW.id_perkuliahan;

    -- Hitung yang sudah terdaftar
    SELECT COUNT(*) INTO enrolled FROM dkbs WHERE id_perkuliahan = NEW.id_perkuliahan;

    IF enrolled >= cap THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Kelas sudah penuh!';
    END IF;
END
$$
DELIMITER ;

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
  `jurusan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`nip`, `user_id`, `nama`, `tanggal_lahir`, `jenis_kelamin`, `email`, `no_telepon`, `alamat`, `fakultas`, `jurusan`, `created_at`, `updated_at`) VALUES
('0072201', 2, 'Tjatur Kandaga, S.Si., M.T.', '1985-05-20', 'Laki-laki', '0072201@edutrack.com', '08987654321', 'Jl. Melati No. 12', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-28 22:47:57', '2025-12-29 04:51:07'),
('0072202', 9, 'Ir. Teddy Marcus Zakaria, M.T.', '1986-11-05', 'Laki-laki', '0072202@edutrack.com', '081234567890', 'Jl. Kenanga Raya No. 45', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 01:41:28', '2025-12-29 02:19:07'),
('0072203', 8, 'Erico Darmawan Handoyo, S. Kom., M.T.', '1990-01-09', 'Laki-laki', '0072203@edutrack.com', '087656728372', 'Jl. Mawar Indah No. 7', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 01:37:49', '2025-12-29 02:19:17'),
('0072204', 10, 'Andreas Widjaja, S.Si., M.Sc., Ph.D', '1980-10-20', 'Laki-laki', '0072204@edutrack.com', '083822334455', 'Jl. Anggrek Lestari No. 88', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:22:52', '2025-12-29 02:22:52'),
('0072205', 11, 'Maresha Caroline Wijanto, S.Kom., M.T., Ph.D.', '1990-07-19', 'Perempuan', '0072205@edutrack.com', '081333445566', 'Jl. Cendana No. 23', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:26:23', '2026-01-02 23:47:23'),
('0072206', 12, 'Wenny Franciska Senjaya, S.Kom., M.T., Ph.D.', '1992-03-17', 'Perempuan', '0072206@edutrack.com', '081245678901', 'Jl. Flamboyan No. 56', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:27:57', '2026-01-02 23:48:22'),
('0072207', 13, 'Meliana Christianti J., S. Kom., M.T.', '1996-09-10', 'Perempuan', '0072207@edutrack.com', '082134567890', 'Jl. Dahlia No. 89', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:29:33', '2025-12-29 02:29:33'),
('0072208', 14, 'Rossevine Artha Nathasya, S.Kom., M.T.', '1997-01-14', 'Perempuan', '0072208@edutrack.com', '089526374688', 'Jl. Tulip No 11A', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-04 03:16:47', '2026-01-04 03:16:47'),
('0072209', 15, 'Hendra Bunyamin, S.Si., M.T.', '1979-05-15', 'Laki-laki', '0072209@edutrack.com', '089537462987', 'Jl. Anyelir no 20', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-04 03:27:53', '2026-01-04 03:27:53'),
('0072210', 19, 'Jimmy Agustian Loekito, S.T., M.T.', '1986-05-13', 'Laki-laki', '0072210@edutrack.com', '0895723019', 'Jl. Gerbera No 42', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 10:40:07', '2026-01-07 10:40:07'),
('0072211', 20, 'Pin Panji Yapinus, S.T., M.T.', '1983-11-16', 'Laki-laki', '0072211@edutrack.com', '089526048382', 'Jl. Sakura No 2B', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 11:38:22', '2026-01-07 11:38:22'),
('0072212', 23, 'Semuil Tjiharjadi, S.T., M.M., M.T.', '1983-05-09', 'Laki-laki', '0072212@edutrack.com', '08967439163', 'Jl. Hydrangea No 20', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 12:14:47', '2026-01-07 12:14:47'),
('0072213', 24, 'Markus Tanubrata, S.T., M.M., M.T.', '1980-12-09', 'Laki-laki', '0072213@edutrack.com', '089563748193', 'Jl. Rafflesia Arnoldi No 25', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 12:23:02', '2026-01-07 12:23:02');

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
('2472022', 4, 'Bryan Christian', 'Teknik Informatika', '2472022@edutrack.com', 'Laki-laki', '2006-07-28', 'Jl. Kopo', '089626312738', '2025-12-28 23:07:50', '2025-12-28 23:07:50'),
('2473020', 17, 'Juan Alexander', 'Sistem Komputer', '2473020@edutrack.com', 'Laki-laki', '2005-06-14', 'Jl. Margaasih No 21', '089527384910', '2026-01-07 01:13:07', '2026-01-07 10:35:55'),
('2473021', 18, 'Rafael Adiputra', 'Sistem Komputer', '2473021@edutrack.com', 'Laki-laki', '2004-03-04', 'Jl. Babakan Jeruk No 23', '089573648291', '2026-01-07 01:18:32', '2026-01-07 10:36:33');

-- --------------------------------------------------------

--
-- Table structure for table `mata_kuliah`
--

CREATE TABLE `mata_kuliah` (
  `kode_mk` varchar(255) NOT NULL,
  `nama_mk` varchar(255) NOT NULL,
  `jurusan` varchar(255) NOT NULL DEFAULT 'Teknik Informatika',
  `sks` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `jurusan`, `sks`, `semester`, `created_at`, `updated_at`) VALUES
('MK001P', 'Paradigma Pemrograman (Praktikum)', 'Teknik Informatika', 1, 3, '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK001T', 'Paradigma Pemrograman (Teori)', 'Teknik Informatika', 3, 3, '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK002P', 'Desain Basis Data Lanjut (Praktikum)', 'Teknik Informatika', 1, 3, '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK002T', 'Desain Basis Data Lanjut (Teori)', 'Teknik Informatika', 2, 3, '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK003', 'Rekayasa Perangkat Lunak', 'Teknik Informatika', 3, 3, '2025-12-29 06:54:13', '2025-12-29 01:04:02'),
('MK004', 'Teknologi Multimedia', 'Teknik Informatika', 2, 3, '2025-12-29 00:40:18', '2025-12-29 00:58:31'),
('MK005', 'Matematika Diskrit', 'Teknik Informatika', 3, 3, '2025-12-29 00:59:19', '2025-12-29 00:59:19'),
('MK006P', 'Algoritma Struktur Data (Praktikum)', 'Teknik Informatika', 1, 3, '2025-12-29 01:17:34', '2026-01-03 03:22:32'),
('MK006T', 'Algoritma Struktur Data (Teori)', 'Teknik Informatika', 3, 3, '2025-12-29 01:05:13', '2025-12-29 01:17:44'),
('MK007', 'Sistem Operasi Komputer', 'Teknik Informatika', 2, 3, '2025-12-29 01:18:06', '2025-12-29 01:18:06'),
('MK101P', 'Pengantar Teknologi Komputer (Praktikum)', 'Sistem Komputer', 1, 1, '2026-01-07 11:01:16', '2026-01-07 11:01:16'),
('MK101T', 'Pengantar Teknologi Komputer (Teori)', 'Sistem Komputer', 2, 1, '2026-01-07 10:52:22', '2026-01-07 11:00:31'),
('MK102', 'Probabilitas & Statistika', 'Sistem Komputer', 2, 1, '2026-01-07 10:56:27', '2026-01-07 10:56:27'),
('MK103', 'Kalkulus', 'Sistem Komputer', 3, 1, '2026-01-07 10:56:52', '2026-01-07 10:56:52'),
('MK104', 'Matematika Diskrit', 'Sistem Komputer', 3, 2, '2026-01-07 10:57:13', '2026-01-07 10:57:13'),
('MK105', 'Logika dan Sistem Digital', 'Sistem Komputer', 3, 2, '2026-01-07 10:59:31', '2026-01-07 10:59:31'),
('MK106P', 'Pemrograman Tingkat Dasar (Praktikum)', 'Sistem Komputer', 1, 2, '2026-01-07 12:08:33', '2026-01-07 12:08:33'),
('MK106T', 'Pemrograman Tingkat Dasar (Teori)', 'Sistem Komputer', 3, 2, '2026-01-07 12:07:49', '2026-01-07 12:07:58');

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
(12, '2025_12_31_125040_create_pengumumen_table', 7),
(13, '2026_01_06_142000_add_details_to_tagihan_table', 8),
(14, '2026_01_06_150206_create_sp_bayar_tagihan', 9),
(15, '2026_01_06_150641_add_sp_generate_va', 10),
(16, '2026_01_06_165247_add_jurusan_to_mata_kuliah_table', 11),
(17, '2026_01_08_000000_add_jurusan_to_dosen_table', 12),
(18, '2026_01_08_005500_create_sp_get_matkul_by_jurusan', 13),
(19, '2026_01_08_011000_fix_sp_get_matkul_by_jurusan', 14),
(20, '2026_01_08_012500_create_sp_get_perkuliahan_by_ta', 15),
(21, '2026_01_08_013000_create_sp_get_perkuliahan_by_ta_and_jurusan', 16),
(22, '2026_01_08_015500_add_admin_level_to_admin_table', 17);

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

--
-- Dumping data for table `nilai`
--

INSERT INTO `nilai` (`id`, `nrp`, `kode_mk`, `p1`, `p2`, `p3`, `p4`, `p5`, `p6`, `p7`, `uts`, `p9`, `p10`, `p11`, `p12`, `p13`, `p14`, `p15`, `uas`, `nilai_total`, `nilai_akhir`, `created_at`, `updated_at`) VALUES
(1, '2472021', 'MK001P', 80, 80, 85, 90, 80, 80, 80, 75, 89, 80, 85, 80, 80, 0, 0, 0, 64.45, 'C', '2026-01-02 22:48:46', '2026-01-02 22:51:01'),
(2, '2472021', 'MK001T', 90, 90, 90, 98, 87, 90, 95, 75, 80, 87, 89, 94, 85, 0, 0, 0, 68.75, 'C', '2026-01-02 22:51:55', '2026-01-02 22:51:55');

--
-- Triggers `nilai`
--
DELIMITER $$
CREATE TRIGGER `trg_log_perubahan_nilai` BEFORE UPDATE ON `nilai` FOR EACH ROW BEGIN
    IF OLD.nilai_total <> NEW.nilai_total THEN
        INSERT INTO audit_logs_nilai (nrp, kode_mk, nilai_lama_total, nilai_baru_total, perubahan_oleh)
        VALUES (OLD.nrp, OLD.kode_mk, OLD.nilai_total, NEW.nilai_total, USER());
    END IF;
END
$$
DELIMITER ;

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
(1, 'Libur Natal & Tahun Baru', 'Seluruh kegiatan akademik diliburkan', 'libur', '2025-12-22 00:00:00', '2026-01-04 00:00:00', '2026-01-03 03:09:59', '2026-01-03 03:10:16');

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
(2, 'L8002', '0072206', 'MK006T', 'B', 'Selasa', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2025-12-29 04:03:46', '2026-01-02 23:30:01'),
(3, 'L8002', '0072206', 'MK006P', 'B', 'Selasa', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2025-12-29 04:22:39', '2026-01-02 23:30:01'),
(4, 'L8002', '0072203', 'MK004', 'B', 'Senin', '09:30:00', '11:40:00', '2025/2026 - Ganjil', '2025-12-29 04:24:07', '2026-01-02 23:30:01'),
(5, 'L8006', '0072204', 'MK005', 'C', 'Senin', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2025-12-29 04:24:47', '2026-01-02 23:30:01'),
(6, 'L8002', '0072202', 'MK002T', 'C', 'Senin', '17:30:00', '19:10:00', '2025/2026 - Ganjil', '2025-12-29 04:25:18', '2026-01-02 23:30:01'),
(7, 'L8001', '0072202', 'MK002P', 'C', 'Rabu', '17:00:00', '19:00:00', '2025/2026 - Ganjil', '2025-12-29 04:26:12', '2026-01-02 23:30:01'),
(8, 'L8005', '0072205', 'MK003', 'B', 'Selasa', '15:00:00', '17:30:00', '2025/2026 - Ganjil', '2025-12-29 04:26:50', '2026-01-02 23:30:01'),
(9, 'L8005', '0072207', 'MK007', 'A', 'Jumat', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2025-12-29 04:27:21', '2026-01-02 23:30:01'),
(10, 'L8004', '0072201', 'MK001T', 'A', 'Kamis', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2025-12-29 04:37:14', '2026-01-02 23:30:01'),
(11, 'L8004', '0072201', 'MK001P', 'A', 'Kamis', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2025-12-29 04:37:48', '2026-01-02 23:30:01'),
(12, 'L8005', '0072205', 'MK003', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(13, 'L8002', '0072203', 'MK004', 'A', 'Senin', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(14, 'L8002', '0072203', 'MK004', 'C', 'Senin', '12:30:00', '14:10:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(15, 'L8006', '0072204', 'MK005', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(16, 'L8006', '0072204', 'MK005', 'B', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(17, 'L8002', '0072206', 'MK006T', 'A', 'Kamis', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-08 02:51:04'),
(18, 'L8003', '0072206', 'MK006T', 'C', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(19, 'L8002', '0072206', 'MK006P', 'A', 'Kamis', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-08 02:52:25'),
(20, 'L8003', '0072206', 'MK006P', 'C', 'Rabu', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(21, 'L8004', '0072201', 'MK001T', 'B', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(22, 'L8004', '0072201', 'MK001T', 'C', 'Selasa', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(23, 'L8004', '0072201', 'MK001P', 'B', 'Rabu', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(24, 'L8004', '0072201', 'MK001P', 'C', 'Selasa', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(25, 'L8001', '0072202', 'MK002T', 'A', 'Jumat', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(26, 'L8002', '0072202', 'MK002T', 'B', 'Senin', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(27, 'L8001', '0072202', 'MK002P', 'A', 'Jumat', '13:00:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(28, 'L8001', '0072202', 'MK002P', 'B', 'Rabu', '15:00:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(29, 'L8005', '0072207', 'MK007', 'B', 'Jumat', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(30, 'L8005', '0072207', 'MK007', 'C', 'Jumat', '17:30:00', '19:10:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-02 23:30:01'),
(31, 'L8007', '0072210', 'MK103', 'A', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 11:20:27', '2026-01-07 12:12:52'),
(33, 'L8007', '0072211', 'MK101T', 'A', 'Rabu', '09:30:00', '11:10:00', '2025/2026 - Ganjil', '2026-01-07 11:45:50', '2026-01-07 11:45:50'),
(34, 'L8007', '0072211', 'MK101P', 'A', 'Rabu', '11:40:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-07 11:45:50', '2026-01-07 11:45:50'),
(35, 'L8005', '0072212', 'MK106T', 'A', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 12:15:39', '2026-01-07 20:11:15'),
(36, 'L8005', '0072212', 'MK106P', 'A', 'Senin', '12:30:00', '13:20:00', '2025/2026 - Ganjil', '2026-01-07 12:15:39', '2026-01-07 20:13:57'),
(37, 'L8007', '0072210', 'MK103', 'B', 'Selasa', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-07 20:04:06', '2026-01-07 20:04:19'),
(38, 'L8008', '0072210', 'MK103', 'C', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 20:06:31', '2026-01-07 20:06:31'),
(39, 'L8005', '0072212', 'MK106T', 'B', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 21:28:37', '2026-01-07 21:34:05'),
(40, 'L8005', '0072212', 'MK106P', 'B', 'Rabu', '12:30:00', '13:20:00', '2025/2026 - Ganjil', '2026-01-07 21:28:37', '2026-01-07 21:34:15'),
(41, 'L8004', '0072211', 'MK101T', 'B', 'Senin', '08:00:00', '09:40:00', '2025/2026 - Ganjil', '2026-01-07 21:31:00', '2026-01-07 21:31:00'),
(42, 'L8004', '0072211', 'MK101P', 'B', 'Senin', '10:10:00', '11:00:00', '2025/2026 - Ganjil', '2026-01-07 21:31:00', '2026-01-07 21:31:00'),
(43, 'L8010', '0072211', 'MK101T', 'C', 'Senin', '12:00:00', '13:40:00', '2025/2026 - Ganjil', '2026-01-07 21:32:30', '2026-01-07 21:32:48'),
(44, 'L8010', '0072211', 'MK101P', 'C', 'Senin', '14:10:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-07 21:32:30', '2026-01-07 21:33:05');

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

--
-- Dumping data for table `presensi`
--

INSERT INTO `presensi` (`id`, `nrp`, `jadwal_id`, `tanggal`, `status`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, '2472021', 10, '2025-02-20', 'Hadir', NULL, '2026-01-02 22:58:39', '2026-01-02 22:58:39'),
(2, '2472021', 10, '2026-01-03', 'Hadir', NULL, '2026-01-02 23:02:06', '2026-01-02 23:02:06'),
(3, '2472021', 2, '2025-02-18', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(4, '2472021', 2, '2025-02-25', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(5, '2472021', 2, '2025-03-04', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(6, '2472021', 2, '2025-03-11', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(7, '2472021', 2, '2025-03-18', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(8, '2472021', 2, '2025-03-25', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(9, '2472021', 2, '2025-04-01', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(10, '2472021', 2, '2025-04-08', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(11, '2472021', 2, '2025-04-15', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(12, '2472021', 2, '2025-04-22', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(13, '2472021', 2, '2025-04-29', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(14, '2472021', 2, '2025-05-06', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(15, '2472021', 2, '2025-05-13', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(16, '2472021', 2, '2025-05-20', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(17, '2472021', 2, '2025-05-27', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(18, '2472021', 2, '2025-06-03', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(19, '2472021', 3, '2025-02-18', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(20, '2472021', 3, '2025-02-25', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(21, '2472021', 3, '2025-03-04', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(22, '2472021', 3, '2025-03-11', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(23, '2472021', 3, '2025-03-18', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(24, '2472021', 3, '2025-03-25', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(25, '2472021', 3, '2025-04-01', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(26, '2472021', 3, '2025-04-08', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(27, '2472021', 3, '2025-04-15', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(28, '2472021', 3, '2025-04-22', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(29, '2472021', 3, '2025-04-29', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(30, '2472021', 3, '2025-05-06', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(31, '2472021', 3, '2025-05-13', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(32, '2472021', 3, '2025-05-20', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(33, '2472021', 3, '2025-05-27', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(34, '2472021', 3, '2025-06-03', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(35, '2472021', 4, '2025-02-17', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(36, '2472021', 4, '2025-02-24', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(37, '2472021', 4, '2025-03-03', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(38, '2472021', 4, '2025-03-10', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(39, '2472021', 4, '2025-03-17', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(40, '2472021', 4, '2025-03-24', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(41, '2472021', 4, '2025-03-31', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(42, '2472021', 4, '2025-04-07', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(43, '2472021', 4, '2025-04-14', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(44, '2472021', 4, '2025-04-21', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(45, '2472021', 4, '2025-04-28', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(46, '2472021', 4, '2025-05-05', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(47, '2472021', 4, '2025-05-12', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(48, '2472021', 4, '2025-05-19', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(49, '2472021', 4, '2025-05-26', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(50, '2472021', 4, '2025-06-02', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(51, '2472021', 5, '2025-02-17', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(52, '2472021', 5, '2025-02-24', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(53, '2472021', 5, '2025-03-03', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(54, '2472021', 5, '2025-03-10', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(55, '2472021', 5, '2025-03-17', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(56, '2472021', 5, '2025-03-24', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(57, '2472021', 5, '2025-03-31', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(58, '2472021', 5, '2025-04-07', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(59, '2472021', 5, '2025-04-14', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(60, '2472021', 5, '2025-04-21', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(61, '2472021', 5, '2025-04-28', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(62, '2472021', 5, '2025-05-05', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(63, '2472021', 5, '2025-05-12', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(64, '2472021', 5, '2025-05-19', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(65, '2472021', 5, '2025-05-26', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(66, '2472021', 5, '2025-06-02', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(67, '2472021', 6, '2025-02-17', 'Hadir', NULL, '2026-01-02 23:04:33', '2026-01-02 23:04:33'),
(68, '2472021', 6, '2025-02-24', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(69, '2472021', 6, '2025-03-03', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(70, '2472021', 6, '2025-03-10', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(71, '2472021', 6, '2025-03-17', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(72, '2472021', 6, '2025-03-24', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(73, '2472021', 6, '2025-03-31', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(74, '2472021', 6, '2025-04-07', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(75, '2472021', 6, '2025-04-14', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(76, '2472021', 6, '2025-04-21', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(77, '2472021', 6, '2025-04-28', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(78, '2472021', 6, '2025-05-05', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(79, '2472021', 6, '2025-05-12', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(80, '2472021', 6, '2025-05-19', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(81, '2472021', 6, '2025-05-26', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(82, '2472021', 6, '2025-06-02', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(83, '2472021', 7, '2025-02-19', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(84, '2472021', 7, '2025-02-26', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(85, '2472021', 7, '2025-03-05', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(86, '2472021', 7, '2025-03-12', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(87, '2472021', 7, '2025-03-19', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(88, '2472021', 7, '2025-03-26', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(89, '2472021', 7, '2025-04-02', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(90, '2472021', 7, '2025-04-09', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(91, '2472021', 7, '2025-04-16', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(92, '2472021', 7, '2025-04-23', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(93, '2472021', 7, '2025-04-30', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(94, '2472021', 7, '2025-05-07', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(95, '2472021', 7, '2025-05-14', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(96, '2472021', 7, '2025-05-21', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(97, '2472021', 7, '2025-05-28', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(98, '2472021', 7, '2025-06-04', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(99, '2472021', 8, '2025-02-18', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(100, '2472021', 8, '2025-02-25', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(101, '2472021', 8, '2025-03-04', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(102, '2472021', 8, '2025-03-11', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(103, '2472021', 8, '2025-03-18', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(104, '2472021', 8, '2025-03-25', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(105, '2472021', 8, '2025-04-01', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(106, '2472021', 8, '2025-04-08', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(107, '2472021', 8, '2025-04-15', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(108, '2472021', 8, '2025-04-22', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(109, '2472021', 8, '2025-04-29', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(110, '2472021', 8, '2025-05-06', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(111, '2472021', 8, '2025-05-13', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(112, '2472021', 8, '2025-05-20', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(113, '2472021', 8, '2025-05-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(114, '2472021', 8, '2025-06-03', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(115, '2472021', 9, '2025-02-21', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(116, '2472021', 9, '2025-02-28', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(117, '2472021', 9, '2025-03-07', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(118, '2472021', 9, '2025-03-14', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(119, '2472021', 9, '2025-03-21', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(120, '2472021', 9, '2025-03-28', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(121, '2472021', 9, '2025-04-04', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(122, '2472021', 9, '2025-04-11', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(123, '2472021', 9, '2025-04-18', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(124, '2472021', 9, '2025-04-25', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(125, '2472021', 9, '2025-05-02', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(126, '2472021', 9, '2025-05-09', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(127, '2472021', 9, '2025-05-16', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(128, '2472021', 9, '2025-05-23', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(129, '2472021', 9, '2025-05-30', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(130, '2472021', 9, '2025-06-06', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(131, '2472021', 10, '2025-02-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(132, '2472021', 10, '2025-03-06', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(133, '2472021', 10, '2025-03-13', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(134, '2472021', 10, '2025-03-20', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(135, '2472021', 10, '2025-03-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(136, '2472021', 10, '2025-04-03', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(137, '2472021', 10, '2025-04-10', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(138, '2472021', 10, '2025-04-17', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(139, '2472021', 10, '2025-04-24', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(140, '2472021', 10, '2025-05-01', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(141, '2472021', 10, '2025-05-08', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(142, '2472021', 10, '2025-05-15', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(143, '2472021', 10, '2025-05-22', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(144, '2472021', 10, '2025-05-29', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(145, '2472021', 10, '2025-06-05', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(146, '2472021', 11, '2025-02-20', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(147, '2472022', 11, '2025-02-20', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(148, '2472021', 11, '2025-02-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(149, '2472022', 11, '2025-02-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(150, '2472021', 11, '2025-03-06', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(151, '2472022', 11, '2025-03-06', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(152, '2472021', 11, '2025-03-13', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(153, '2472022', 11, '2025-03-13', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(154, '2472021', 11, '2025-03-20', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(155, '2472022', 11, '2025-03-20', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(156, '2472021', 11, '2025-03-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(157, '2472022', 11, '2025-03-27', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(158, '2472021', 11, '2025-04-03', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(159, '2472022', 11, '2025-04-03', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(160, '2472021', 11, '2025-04-10', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(161, '2472022', 11, '2025-04-10', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(162, '2472021', 11, '2025-04-17', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(163, '2472022', 11, '2025-04-17', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(164, '2472021', 11, '2025-04-24', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(165, '2472022', 11, '2025-04-24', 'Hadir', NULL, '2026-01-02 23:04:34', '2026-01-02 23:04:34'),
(166, '2472021', 11, '2025-05-01', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(167, '2472022', 11, '2025-05-01', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(168, '2472021', 11, '2025-05-08', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(169, '2472022', 11, '2025-05-08', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(170, '2472021', 11, '2025-05-15', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(171, '2472022', 11, '2025-05-15', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(172, '2472021', 11, '2025-05-22', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(173, '2472022', 11, '2025-05-22', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(174, '2472021', 11, '2025-05-29', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(175, '2472022', 11, '2025-05-29', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(176, '2472021', 11, '2025-06-05', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(177, '2472022', 11, '2025-06-05', 'Hadir', NULL, '2026-01-02 23:04:35', '2026-01-02 23:04:35'),
(178, '2472022', 11, '2026-01-03', 'Hadir', NULL, '2026-01-02 23:06:20', '2026-01-02 23:06:20'),
(179, '2472021', 11, '2026-01-03', 'Hadir', NULL, '2026-01-02 23:06:20', '2026-01-02 23:06:20');

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
('p3bPtjOA7j3rmRq5o6MZWcZJCAsCilexNN1cnFAG', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVFNwVHJwM3FEQ1lxR2h6bmFIa1J3Qm1SMzlUSGhaRWVZWTRCUVdFUCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzI6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9ka2JzIjtzOjU6InJvdXRlIjtOO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1767847044);

-- --------------------------------------------------------

--
-- Table structure for table `tagihan`
--

CREATE TABLE `tagihan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nrp` varchar(255) NOT NULL,
  `jenis` varchar(255) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `batas_pembayaran` date DEFAULT NULL,
  `tipe_pembayaran` int(11) DEFAULT NULL,
  `cicilan_ke` int(11) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tagihan`
--

INSERT INTO `tagihan` (`id`, `nrp`, `jenis`, `jumlah`, `batas_pembayaran`, `tipe_pembayaran`, `cicilan_ke`, `status`, `created_at`, `updated_at`) VALUES
(10, '2472021', 'Tagihan Semester', 6300000.00, '2026-02-09', NULL, NULL, 'Belum Lunas', '2026-01-06 09:15:11', '2026-01-06 09:15:29');

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
(4, 'Bryan Christian', '2472022@edutrack.com', '$2y$12$TBxLGZmNmf7.HOPmgOv/bOyM31ciRQVgDGFZaTRxdtCZw0NsoFSzq', 'mahasiswa', '2025-12-28 23:07:49', '2025-12-28 23:07:49'),
(8, 'Erico Darmawan Handoyo, S. Kom., M.T.', '0072203@edutrack.com', '$2y$12$ZkhJeKnn8uk1r47keAw8Iu.LSTozgsMR71tcdmzzWfHj7nEpuK1RO', 'dosen', '2025-12-29 01:37:49', '2025-12-29 01:37:49'),
(9, 'Ir. Teddy Marcus Zakaria, M.T.', '0072202@edutrack.com', '$2y$12$hWI4nORCg218JJJvBq5sL.jM8YO7sUcczMLtO8cmMgGeXoI/a4ofG', 'dosen', '2025-12-29 01:41:28', '2025-12-29 01:41:28'),
(10, 'Andreas Widjaja, S.Si., M.Sc., Ph.D', '0072204@edutrack.com', '$2y$12$mQjSGR8xatAV9hWs4u3YAu6COj3HerCvlh5pDV/wKtEBkF8vtGLAe', 'dosen', '2025-12-29 02:22:52', '2025-12-29 02:22:52'),
(11, 'Maresha Caroline Wijanto, S.Kom., M.T., Ph.D.', '0072205@edutrack.com', '$2y$12$LFIH2.6ZM6ch8BrELeYtue0zoRWxwZj2SCqMiN6LajNk9u1gkxQ3S', 'dosen', '2025-12-29 02:26:23', '2026-01-02 23:47:23'),
(12, 'Wenny Franciska Senjaya, S.Kom., M.T., Ph.D.', '0072206@edutrack.com', '$2y$12$k6T/1O/KDSHxOZJkoJL.S.uTAVZ1AAUWTnsJnC9FTtLTyb3OHLUUy', 'dosen', '2025-12-29 02:27:57', '2026-01-02 23:48:22'),
(13, 'Meliana Christianti J., S. Kom., M.T.', '0072207@edutrack.com', '$2y$12$C97naCO4OmqMPpkFmYkffOw.UL5tu5t.n17lxAeqFJ40CUoAc4Sxe', 'dosen', '2025-12-29 02:29:33', '2025-12-29 02:29:33'),
(14, 'Rossevine Artha Nathasya, S.Kom., M.T.', '0072208@edutrack.com', '$2y$12$G5I.apu04eeNE.PmuBGARe5o6x01KEC0rNoZONrfEB3ogAvuFWmlG', 'dosen', '2026-01-04 03:16:47', '2026-01-04 03:16:47'),
(15, 'Hendra Bunyamin, S.Si., M.T.', '0072209@edutrack.com', '$2y$12$FIrba1NSJtW97tgAxFVzheMTM.wpTdcC8hHRI8YZfVdMbIm5.lk/e', 'dosen', '2026-01-04 03:27:53', '2026-01-04 03:27:53'),
(17, 'Juan Alexander', '2473020@edutrack.com', '$2y$12$Ifq2ZfGz9EvNKdCiXKWGJez76n7DkQfuSpRwm7JZXkms.ejMBSTtC', 'mahasiswa', '2026-01-07 01:13:07', '2026-01-07 01:13:07'),
(18, 'Rafael Adiputra', '2473021@edutrack.com', '$2y$12$uh/vjWwCSzOk.ltRsrQVYOhl7CbnbaGkEau0LG8/6gjTjJItMizza', 'mahasiswa', '2026-01-07 01:18:32', '2026-01-07 01:18:32'),
(19, 'Jimmy Agustian Loekito, S.T., M.T.', '0072210@edutrack.com', '$2y$12$mFwk7vkA2ypSZ9QpKUsZ6uOs8Kl9qVjtCCnS1Jap.qBbJaTeDt0Am', 'dosen', '2026-01-07 10:40:07', '2026-01-07 10:40:07'),
(20, 'Pin Panji Yapinus, S.T., M.T.', '0072211@edutrack.com', '$2y$12$lfd1ovvEnXzN7Fk7x327fOtybGUPtTkM0m/0lkh6c1uvp4PRC8ONu', 'dosen', '2026-01-07 11:38:22', '2026-01-07 11:38:22'),
(22, 'Seccond Admin', 'admin2@edutrack.com', '$2y$12$JNY8JGxD50jB2dl5R92ILe1uf4aBfpAHro4poYkaxEn35B3tvdhuy', 'admin', '2026-01-07 12:00:59', '2026-01-07 12:00:59'),
(23, 'Semuil Tjiharjadi, S.T., M.M., M.T.', '0072212@edutrack.com', '$2y$12$GrsREkNLOj0xPZCg.hqlZ.ar3v66WXLKKYqAkB/KG.RHiRLUXT7bW', 'dosen', '2026-01-07 12:14:47', '2026-01-07 12:14:47'),
(24, 'Markus Tanubrata, S.T., M.M., M.T.', '0072213@edutrack.com', '$2y$12$XFyFzFUzRaZ8yleOXFhH6eHrmgGjSU.0.QYknRbwUiZtpCVivRIvK', 'dosen', '2026-01-07 12:23:02', '2026-01-07 12:23:02');

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
-- Indexes for table `audit_logs_nilai`
--
ALTER TABLE `audit_logs_nilai`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `audit_logs_nilai`
--
ALTER TABLE `audit_logs_nilai`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dkbs`
--
ALTER TABLE `dkbs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `nilai`
--
ALTER TABLE `nilai`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `perkuliahan`
--
ALTER TABLE `perkuliahan`
  MODIFY `id_perkuliahan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
