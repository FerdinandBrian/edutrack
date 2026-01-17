-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 17, 2026 at 03:05 PM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_bayar_tagihan` (IN `tagihan_id` INT)   BEGIN
                UPDATE tagihan SET status = 'Lunas', updated_at = NOW() WHERE id = tagihan_id;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_cek_status_kelas` (IN `p_ta` VARCHAR(255))   BEGIN
                SELECT 
                    p.id_perkuliahan,
                    p.kode_mk,
                    mk.nama_mk,
                    p.kelas,
                    r.kapasitas,
                    COUNT(d.id) AS jumlah_mahasiswa,
                    CASE 
                        WHEN COUNT(d.id) >= 5 THEN 'Dibuka'
                        ELSE 'Ditutup (Kurang Quota)'
                    END AS status_kelas
                FROM perkuliahan p
                JOIN mata_kuliah mk ON p.kode_mk = mk.kode_mk
                JOIN ruangan r ON p.kode_ruangan = r.kode_ruangan
                LEFT JOIN dkbs d ON p.id_perkuliahan = d.id_perkuliahan
                WHERE p.tahun_ajaran = p_ta COLLATE utf8mb4_unicode_ci
                GROUP BY p.id_perkuliahan
                ORDER BY mk.nama_mk ASC, p.kelas ASC;
            END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_matkul_by_jurusan` (IN `p_jurusan` VARCHAR(255))   BEGIN
    SELECT * 
    FROM mata_kuliah 
    WHERE jurusan = p_jurusan COLLATE utf8mb4_unicode_ci
    ORDER BY 
        CASE 
            WHEN semester REGEXP '^[0-9]+$' THEN CAST(semester AS UNSIGNED)
            WHEN semester = 'Ganjil' THEN 9
            WHEN semester = 'Genap' THEN 10
            ELSE 11
        END ASC,
        nama_mk ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_perkuliahan_by_ta` (IN `p_ta` VARCHAR(255))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_perkuliahan_by_ta_and_jurusan` (IN `p_ta` VARCHAR(255), IN `p_jurusan` VARCHAR(255))   BEGIN
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_get_va` (IN `student_nrp` VARCHAR(50))   BEGIN
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

--
-- Dumping data for table `audit_logs_nilai`
--

INSERT INTO `audit_logs_nilai` (`id`, `nrp`, `kode_mk`, `nilai_lama_total`, `nilai_baru_total`, `perubahan_oleh`, `waktu_perubahan`) VALUES
(1, '2472021', 'MK001T', 68.75, 84.5, 'root@', '2026-01-12 10:18:12'),
(2, '2472021', 'MK001P', 64.45, 80.03, 'root@', '2026-01-12 10:19:14'),
(3, '2472021', 'MK001T', 84.5, 82.49, 'root@', '2026-01-12 10:35:04');

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
(32, '2473020', 'MK106T', 35, '2', 'Terdaftar', '2026-01-07 12:19:30', '2026-01-07 12:19:30', '2025/2026 - Ganjil'),
(33, '2473020', 'MK106P', 36, '2', 'Terdaftar', '2026-01-07 12:19:30', '2026-01-07 12:19:30', '2025/2026 - Ganjil'),
(42, '2430001', 'MK201', 67, '3', 'Terdaftar', '2026-01-11 20:36:50', '2026-01-11 20:36:50', '2025/2026 - Ganjil'),
(43, '2430001', 'MK204T', 49, '3', 'Terdaftar', '2026-01-11 20:37:44', '2026-01-11 20:37:44', '2025/2026 - Ganjil'),
(44, '2430001', 'MK204P', 50, '3', 'Terdaftar', '2026-01-11 20:37:44', '2026-01-11 20:37:44', '2025/2026 - Ganjil'),
(45, '2430001', 'MK203', 64, '3', 'Terdaftar', '2026-01-11 20:39:01', '2026-01-11 20:39:01', '2025/2026 - Ganjil'),
(46, '2430001', 'MK202', 70, '3', 'Terdaftar', '2026-01-11 20:39:24', '2026-01-11 20:39:24', '2025/2026 - Ganjil'),
(48, '2473020', 'MK101T', 41, '3', 'Terdaftar', '2026-01-11 20:57:48', '2026-01-11 20:57:48', '2025/2026 - Ganjil'),
(49, '2473020', 'MK101P', 42, '3', 'Terdaftar', '2026-01-11 20:57:49', '2026-01-11 20:57:49', '2025/2026 - Ganjil'),
(50, '2473020', 'MK102', 60, '3', 'Terdaftar', '2026-01-11 20:58:14', '2026-01-11 20:58:14', '2025/2026 - Ganjil'),
(51, '2473020', 'MK103', 38, '3', 'Terdaftar', '2026-01-11 21:00:01', '2026-01-11 21:00:01', '2025/2026 - Ganjil'),
(52, '2473020', 'MK105', 63, '3', 'Terdaftar', '2026-01-11 21:01:03', '2026-01-11 21:01:03', '2025/2026 - Ganjil'),
(53, '2473020', 'MK104', 55, '3', 'Terdaftar', '2026-01-11 21:04:55', '2026-01-11 21:04:55', '2025/2026 - Ganjil'),
(54, '2473021', 'MK102', 58, '3', 'Terdaftar', '2026-01-11 21:10:05', '2026-01-11 21:10:05', '2025/2026 - Ganjil');

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
('0071101', 19, 'Jimmy Agustian Loekito, S.T., M.T.', '1986-05-13', 'Laki-laki', '0071101@edutrack.com', '0895723019', 'Jl. Gerbera No 42', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 10:40:07', '2026-01-07 10:40:07'),
('0071102', 20, 'Pin Panji Yapinus, S.T., M.T.', '1983-11-16', 'Laki-laki', '0071102@edutrack.com', '089526048382', 'Jl. Sakura No 2', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 11:38:22', '2026-01-07 11:38:22'),
('0071103', 23, 'Semuil Tjiharjadi, S.T., M.M., M.T.', '1983-05-09', 'Laki-laki', '0071103@edutrack.com', '08967439163', 'Jl. Hydrangea No 20', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 12:14:47', '2026-01-07 12:14:47'),
('0071104', 24, 'Markus Tanubrata, S.T., M.M., M.T.', '1980-12-09', 'Laki-laki', '0071104@edutrack.com', '089563748193', 'Jl. Rafflesia Arnoldi No 25', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-07 12:23:02', '2026-01-07 12:23:02'),
('0071105', 28, 'Hendry Wong, S.T., M.Kom.', '1979-08-13', 'Laki-laki', '0071105@edutrack.com', '089527849012', 'Jl. Krisan No 12', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-09 22:48:57', '2026-01-09 22:48:57'),
('0071106', 29, 'Andrew Sebastian Lehman, S.T., M.Eng.', '1999-06-13', 'Laki-laki', '0071106@edutrack.com', '089573908192', 'Jl. Kantong Semar No 20', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-09 22:56:35', '2026-01-09 22:56:35'),
('0071107', 30, 'Jonathan Chandra, S.T., M.T.', '1996-09-17', 'Laki-laki', '0071107@edutrack.com', '08950942718', 'Jl. Lavender No 4', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-09 22:57:54', '2026-01-09 22:57:54'),
('0071112', 43, 'Dr. Lina Marlina, M.Kom', '1988-04-10', 'Perempuan', '0071112@edutrack.com', '081234567901', 'Jl. Sistem Komputer No. 12, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0071113', 44, 'Dr. Bambang Suryadi, M.T', '1985-09-25', 'Laki-laki', '0071113@edutrack.com', '081234567902', 'Jl. Sistem Komputer No. 13, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0071114', 45, 'Dr. Yuni Astuti, M.Kom', '1991-12-08', 'Perempuan', '0071114@edutrack.com', '081234567903', 'Jl. Sistem Komputer No. 14, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0071115', 46, 'Dr. Eko Prasetyo, M.T', '1984-06-14', 'Laki-laki', '0071115@edutrack.com', '081234567904', 'Jl. Sistem Komputer No. 15, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Komputer', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072201', 2, 'Tjatur Kandaga, S.Si., M.T.', '1985-05-20', 'Laki-laki', '0072201@edutrack.com', '08987654321', 'Jl. Melati No. 12', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-28 22:47:57', '2025-12-29 04:51:07'),
('0072202', 9, 'Ir. Teddy Marcus Zakaria, M.T.', '1986-11-05', 'Laki-laki', '0072202@edutrack.com', '081234567890', 'Jl. Kenanga Raya No. 45', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 01:41:28', '2025-12-29 02:19:07'),
('0072203', 8, 'Erico Darmawan Handoyo, S. Kom., M.T.', '1990-01-09', 'Laki-laki', '0072203@edutrack.com', '087656728372', 'Jl. Mawar Indah No. 7', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 01:37:49', '2025-12-29 02:19:17'),
('0072204', 10, 'Andreas Widjaja, S.Si., M.Sc., Ph.D', '1980-10-20', 'Laki-laki', '0072204@edutrack.com', '083822334455', 'Jl. Anggrek Lestari No. 88', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:22:52', '2025-12-29 02:22:52'),
('0072205', 11, 'Maresha Caroline Wijanto, S.Kom., M.T., Ph.D.', '1990-07-19', 'Perempuan', '0072205@edutrack.com', '081333445566', 'Jl. Cendana No. 23', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:26:23', '2026-01-02 23:47:23'),
('0072206', 12, 'Wenny Franciska Senjaya, S.Kom., M.T., Ph.D.', '1992-03-17', 'Perempuan', '0072206@edutrack.com', '081245678901', 'Jl. Flamboyan No. 56', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:27:57', '2026-01-02 23:48:22'),
('0072207', 13, 'Meliana Christianti J., S. Kom., M.T.', '1996-09-10', 'Perempuan', '0072207@edutrack.com', '082134567890', 'Jl. Dahlia No. 89', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2025-12-29 02:29:33', '2025-12-29 02:29:33'),
('0072208', 14, 'Rossevine Artha Nathasya, S.Kom., M.T.', '1997-01-14', 'Perempuan', '0072208@edutrack.com', '089526374688', 'Jl. Tulip No 11', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-04 03:16:47', '2026-01-04 03:16:47'),
('0072209', 15, 'Hendra Bunyamin, S.Si., M.T.', '1979-05-15', 'Laki-laki', '0072209@edutrack.com', '089537462987', 'Jl. Anyelir no 20', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-04 03:27:53', '2026-01-04 03:27:53'),
('0072215', 38, 'Dr. Ahmad Fauzi, M.Kom', '1985-05-20', 'Laki-laki', '0072215@edutrack.com', '081234567801', 'Jl. Informatika No. 15, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072216', 39, 'Dr. Dewi Kusuma, M.T', '1987-08-15', 'Perempuan', '0072216@edutrack.com', '081234567802', 'Jl. Informatika No. 16, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072217', 40, 'Dr. Budi Santoso, M.Kom', '1983-11-10', 'Laki-laki', '0072217@edutrack.com', '081234567803', 'Jl. Informatika No. 17, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072218', 41, 'Dr. Rina Widya, M.T', '1990-03-22', 'Perempuan', '0072218@edutrack.com', '081234567804', 'Jl. Informatika No. 18, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072219', 42, 'Dr. Hendra Kusuma, M.Kom', '1982-07-18', 'Laki-laki', '0072219@edutrack.com', '081234567805', 'Jl. Informatika No. 19, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Informatika', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072301', 49, 'Dr. Ir. Faisal Rahman, M.T', '1984-08-15', 'Laki-laki', '0072301@edutrack.com', '081234568101', 'Jl. Elektro No. 1, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072302', 50, 'Dr. Ir. Sinta Dewi, M.T', '1986-11-20', 'Perempuan', '0072302@edutrack.com', '081234568102', 'Jl. Elektro No. 2, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072303', 51, 'Dr. Ir. Andi Firmansyah, M.T', '1983-04-10', 'Laki-laki', '0072303@edutrack.com', '081234568103', 'Jl. Elektro No. 3, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072304', 52, 'Dr. Ir. Dina Mariana, M.T', '1988-07-25', 'Perempuan', '0072304@edutrack.com', '081234568104', 'Jl. Elektro No. 4, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072305', 53, 'Dr. Ir. Teguh Santoso, M.T', '1985-12-30', 'Laki-laki', '0072305@edutrack.com', '081234568105', 'Jl. Elektro No. 5, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072306', 54, 'Dr. Ir. Mega Putri, M.T', '1990-03-18', 'Perempuan', '0072306@edutrack.com', '081234568106', 'Jl. Elektro No. 6, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072307', 55, 'Dr. Ir. Rizki Ramadhan, M.T', '1987-09-05', 'Laki-laki', '0072307@edutrack.com', '081234568107', 'Jl. Elektro No. 7, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072308', 56, 'Dr. Ir. Wulan Dari, M.T', '1989-06-12', 'Perempuan', '0072308@edutrack.com', '081234568108', 'Jl. Elektro No. 8, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072309', 57, 'Dr. Ir. Yoga Pratama, M.T', '1986-02-28', 'Laki-laki', '0072309@edutrack.com', '081234568109', 'Jl. Elektro No. 9, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072310', 58, 'Dr. Ir. Tari Susanti, M.T', '1991-10-15', 'Perempuan', '0072310@edutrack.com', '081234568110', 'Jl. Elektro No. 10, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0072311', 59, 'Dr. Ir. Bayu Aji, M.T', '1984-05-20', 'Laki-laki', '0072311@edutrack.com', '081234568111', 'Jl. Elektro No. 11, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Elektro', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0073301', 25, 'Marissa Chitra Sulastra, S.Psi., M.Psi., Psikolog', '1997-03-18', 'Perempuan', '0073301@edutrack.com', '089573849012', 'Jl. Seruni No 14', 'Psikologi', 'Psikologi', '2026-01-08 00:24:54', '2026-01-08 00:24:54'),
('0073302', 31, 'Lisa Imelia Satyawan, M.Psi., Psikolog.', '1982-05-12', 'Perempuan', '0073302@edutrack.com', '089583019283', 'Jl. Matahari No 7', 'Psikologi', 'Psikologi', '2026-01-09 23:07:50', '2026-01-09 23:07:50'),
('0073303', 32, 'Cindy Maria, M.Psi., Psikolog', '1992-12-15', 'Perempuan', '0073303@edutrack.com', '089582093821', 'Jl. Lili No 24', 'Psikologi', 'Psikologi', '2026-01-09 23:09:06', '2026-01-09 23:09:06'),
('0073304', 33, 'Indah Puspitasari, M.Psi., Psikolog.', '1990-06-13', 'Perempuan', '0073304@edutrack.com', '089582901029', 'Jl. Edelweis No 10', 'Psikologi', 'Psikologi', '2026-01-09 23:10:21', '2026-01-09 23:10:21'),
('0073305', 34, 'Meta Dwijayanthy, M.Psi., Psikolog.', '1989-11-28', 'Perempuan', '0073305@edutrack.com', '089582739102', 'Jl. Wijayakusuma No 13', 'Psikologi', 'Psikologi', '2026-01-09 23:12:37', '2026-01-09 23:12:37'),
('0073306', 35, 'Dr. Tery Setiawan, B.A., S.Psi., M.Si.', '1990-07-13', 'Laki-laki', '0073306@edutrack.com', '089582703912', 'Jl. Bougenville No 9', 'Psikologi', 'Psikologi', '2026-01-09 23:16:11', '2026-01-09 23:16:11'),
('0073307', 36, 'Dr. Yuspendi, M.Psi., Psikolog, M.Pd.', '1983-05-23', 'Laki-laki', '0073307@dutrack.com', '089592038291', 'Jl. Calla Lily', 'Psikologi', 'Psikologi', '2026-01-09 23:18:57', '2026-01-09 23:18:57'),
('0073308', 37, 'Heliany Kiswantomo, S.Psi., M.Si., Psikolog.', '1995-06-23', 'Perempuan', '0073308@edutrack.com', '089592038494', 'Jl. Alamanda No 7', 'Psikologi', 'Psikologi', '2026-01-09 23:21:08', '2026-01-09 23:21:08'),
('0073309', 47, 'Dr. Nurul Hidayah, M.Psi., Psikolog', '1989-11-28', 'Perempuan', '0073309@edutrack.com', '081234568001', 'Jl. Psikologi No. 9, Bandung', 'Psikologi', 'Psikologi', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0073310', 48, 'Dr. Wawan Setiawan, M.Psi., Psikolog', '1987-05-12', 'Laki-laki', '0073310@edutrack.com', '081234568002', 'Jl. Psikologi No. 10, Bandung', 'Psikologi', 'Psikologi', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074101', 60, 'Dr. Ir. Raden Adiputra, M.T', '1985-07-10', 'Laki-laki', '0074101@edutrack.com', '081234568201', 'Jl. Industri No. 1, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074102', 61, 'Dr. Ir. Citra Kusuma, M.T', '1988-11-22', 'Perempuan', '0074102@edutrack.com', '081234568202', 'Jl. Industri No. 2, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074103', 62, 'Dr. Ir. Andri Wijaya, M.T', '1983-04-15', 'Laki-laki', '0074103@edutrack.com', '081234568203', 'Jl. Industri No. 3, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074104', 63, 'Dr. Ir. Nisa Amelia, M.T', '1990-08-30', 'Perempuan', '0074104@edutrack.com', '081234568204', 'Jl. Industri No. 4, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074105', 64, 'Dr. Ir. Farhan Maulana, M.T', '1986-12-05', 'Laki-laki', '0074105@edutrack.com', '081234568205', 'Jl. Industri No. 5, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074106', 65, 'Dr. Ir. Sari Wahyuni, M.T', '1989-03-18', 'Perempuan', '0074106@edutrack.com', '081234568206', 'Jl. Industri No. 6, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074107', 66, 'Dr. Ir. Irfan Hakim, M.T', '1987-09-25', 'Laki-laki', '0074107@edutrack.com', '081234568207', 'Jl. Industri No. 7, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074108', 67, 'Dr. Ir. Rina Safitri, M.T', '1991-06-12', 'Perempuan', '0074108@edutrack.com', '081234568208', 'Jl. Industri No. 8, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074109', 68, 'Dr. Ir. Zaki Permadi, M.T', '1984-02-28', 'Laki-laki', '0074109@edutrack.com', '081234568209', 'Jl. Industri No. 9, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0074110', 69, 'Dr. Ir. Linda Kartika, M.T', '1988-10-15', 'Perempuan', '0074110@edutrack.com', '081234568210', 'Jl. Industri No. 10, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Industri', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0075101', 70, 'Dr. Ir. Gunawan Tri, M.T', '1985-05-15', 'Laki-laki', '0075101@edutrack.com', '081234568301', 'Jl. Sipil No. 1, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Sipil', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0075102', 71, 'Dr. Ir. Ratih Puspita, M.T', '1987-09-20', 'Perempuan', '0075102@edutrack.com', '081234568302', 'Jl. Sipil No. 2, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Sipil', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0075103', 72, 'Dr. Ir. Hendro Susilo, M.T', '1983-12-10', 'Laki-laki', '0075103@edutrack.com', '081234568303', 'Jl. Sipil No. 3, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Sipil', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0075104', 73, 'Dr. Ir. Diana Sari, M.T', '1990-04-25', 'Perempuan', '0075104@edutrack.com', '081234568304', 'Jl. Sipil No. 4, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Sipil', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0075105', 74, 'Dr. Ir. Ridwan Kamil, M.T', '1986-08-30', 'Laki-laki', '0075105@edutrack.com', '081234568305', 'Jl. Sipil No. 5, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Sipil', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0075106', 75, 'Dr. Ir. Vina Panduwinata, M.T', '1989-02-18', 'Perempuan', '0075106@edutrack.com', '081234568306', 'Jl. Sipil No. 6, Bandung', 'Teknologi Rekayasa Cerdas', 'Teknik Sipil', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0076101', 76, 'Dr. Arief Budiman, M.Ds', '1986-07-15', 'Laki-laki', '0076101@edutrack.com', '081234568401', 'Jl. Desain No. 1, Bandung', 'Seni & Desain', 'Desain Komunikasi Visual', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0076102', 77, 'Dr. Putri Melati, M.Ds', '1988-11-20', 'Perempuan', '0076102@edutrack.com', '081234568402', 'Jl. Desain No. 2, Bandung', 'Seni & Desain', 'Desain Komunikasi Visual', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0076103', 78, 'Dr. Dimas Prasetyo, M.Ds', '1985-03-10', 'Laki-laki', '0076103@edutrack.com', '081234568403', 'Jl. Desain No. 3, Bandung', 'Seni & Desain', 'Desain Komunikasi Visual', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0076104', 79, 'Dr. Ayu Lestari, M.Ds', '1990-06-25', 'Perempuan', '0076104@edutrack.com', '081234568404', 'Jl. Desain No. 4, Bandung', 'Seni & Desain', 'Desain Komunikasi Visual', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0076105', 80, 'Dr. Rahman Hakim, M.Ds', '1987-12-30', 'Laki-laki', '0076105@edutrack.com', '081234568405', 'Jl. Desain No. 5, Bandung', 'Seni & Desain', 'Desain Komunikasi Visual', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077101', 81, 'Dr. Ir. Surya Darma, M.Ars', '1984-05-18', 'Laki-laki', '0077101@edutrack.com', '081234568501', 'Jl. Arsitektur No. 1, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077102', 82, 'Dr. Ir. Intan Cahaya, M.Ars', '1987-09-22', 'Perempuan', '0077102@edutrack.com', '081234568502', 'Jl. Arsitektur No. 2, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077103', 83, 'Dr. Ir. Satria Wijaya, M.Ars', '1983-12-15', 'Laki-laki', '0077103@edutrack.com', '081234568503', 'Jl. Arsitektur No. 3, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077104', 84, 'Dr. Ir. Melinda Sari, M.Ars', '1990-04-28', 'Perempuan', '0077104@edutrack.com', '081234568504', 'Jl. Arsitektur No. 4, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077105', 85, 'Dr. Ir. Faisal Akbar, M.Ars', '1986-08-10', 'Laki-laki', '0077105@edutrack.com', '081234568505', 'Jl. Arsitektur No. 5, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077106', 86, 'Dr. Ir. Siska Amelia, M.Ars', '1989-02-14', 'Perempuan', '0077106@edutrack.com', '081234568506', 'Jl. Arsitektur No. 6, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077107', 87, 'Dr. Ir. Rangga Aditya, M.Ars', '1987-06-20', 'Laki-laki', '0077107@edutrack.com', '081234568507', 'Jl. Arsitektur No. 7, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077108', 88, 'Dr. Ir. Bella Safira, M.Ars', '1991-10-25', 'Perempuan', '0077108@edutrack.com', '081234568508', 'Jl. Arsitektur No. 8, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0077109', 89, 'Dr. Ir. Fajar Ramadhan, M.Ars', '1985-03-30', 'Laki-laki', '0077109@edutrack.com', '081234568509', 'Jl. Arsitektur No. 9, Bandung', 'Seni & Desain', 'Arsitektur', '2026-01-17 13:37:32', '2026-01-17 13:37:32'),
('0078101', 90, 'Dr. Rahmat Hidayat, M.M', '1985-03-15', 'Laki-laki', '0078101@edutrack.com', '081234569001', 'Jl. Manajemen No. 1, Bandung', 'Ekonomi & Bisnis', 'Manajemen', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0078102', 91, 'Dr. Cindy Permata, M.M', '1987-07-20', 'Perempuan', '0078102@edutrack.com', '081234569002', 'Jl. Manajemen No. 2, Bandung', 'Ekonomi & Bisnis', 'Manajemen', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0078103', 92, 'Dr. Fikri Ramadhan, M.M', '1983-11-10', 'Laki-laki', '0078103@edutrack.com', '081234569003', 'Jl. Manajemen No. 3, Bandung', 'Ekonomi & Bisnis', 'Manajemen', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0078104', 93, 'Dr. Nadya Kartika, M.M', '1990-04-25', 'Perempuan', '0078104@edutrack.com', '081234569004', 'Jl. Manajemen No. 4, Bandung', 'Ekonomi & Bisnis', 'Manajemen', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0078105', 94, 'Dr. Reza Pratama, M.M', '1986-09-18', 'Laki-laki', '0078105@edutrack.com', '081234569005', 'Jl. Manajemen No. 5, Bandung', 'Ekonomi & Bisnis', 'Manajemen', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0079101', 95, 'Dr. Wahyu Santoso, M.Ak', '1984-05-12', 'Laki-laki', '0079101@edutrack.com', '081234569101', 'Jl. Akuntansi No. 1, Bandung', 'Ekonomi & Bisnis', 'Akuntansi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0079102', 96, 'Dr. Lia Kurnia, M.Ak', '1988-08-22', 'Perempuan', '0079102@edutrack.com', '081234569102', 'Jl. Akuntansi No. 2, Bandung', 'Ekonomi & Bisnis', 'Akuntansi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0079103', 97, 'Dr. Gilang Ramadhan, M.Ak', '1982-12-05', 'Laki-laki', '0079103@edutrack.com', '081234569103', 'Jl. Akuntansi No. 3, Bandung', 'Ekonomi & Bisnis', 'Akuntansi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0079104', 98, 'Dr. Erna Yulianti, M.Ak', '1991-03-30', 'Perempuan', '0079104@edutrack.com', '081234569104', 'Jl. Akuntansi No. 4, Bandung', 'Ekonomi & Bisnis', 'Akuntansi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0079105', 99, 'Dr. Yoga Aditya, M.Ak', '1987-10-14', 'Laki-laki', '0079105@edutrack.com', '081234569105', 'Jl. Akuntansi No. 5, Bandung', 'Ekonomi & Bisnis', 'Akuntansi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0080101', 100, 'Dr. Rini Novita, M.A', '1986-06-18', 'Perempuan', '0080101@edutrack.com', '081234569201', 'Jl. Sastra No. 1, Bandung', 'Sastra & Bahasa', 'Sastra Inggris', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0080102', 101, 'Dr. David Anderson, M.A', '1983-09-25', 'Laki-laki', '0080102@edutrack.com', '081234569202', 'Jl. Sastra No. 2, Bandung', 'Sastra & Bahasa', 'Sastra Inggris', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0080103', 102, 'Dr. Maya Anggraini, M.A', '1989-02-14', 'Perempuan', '0080103@edutrack.com', '081234569203', 'Jl. Sastra No. 3, Bandung', 'Sastra & Bahasa', 'Sastra Inggris', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0080104', 103, 'Dr. John Smith, M.A', '1985-11-08', 'Laki-laki', '0080104@edutrack.com', '081234569204', 'Jl. Sastra No. 4, Bandung', 'Sastra & Bahasa', 'Sastra Inggris', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0080105', 104, 'Dr. Ratna Kusuma, M.A', '1990-07-22', 'Perempuan', '0080105@edutrack.com', '081234569205', 'Jl. Sastra No. 5, Bandung', 'Sastra & Bahasa', 'Sastra Inggris', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0081101', 105, 'Dr. Li Wei, M.A', '1984-04-10', 'Laki-laki', '0081101@edutrack.com', '081234569301', 'Jl. Sastra No. 6, Bandung', 'Sastra & Bahasa', 'Sastra China', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0081102', 106, 'Dr. Wang Mei, M.A', '1988-08-15', 'Perempuan', '0081102@edutrack.com', '081234569302', 'Jl. Sastra No. 7, Bandung', 'Sastra & Bahasa', 'Sastra China', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0081103', 107, 'Dr. Chen Hao, M.A', '1982-12-20', 'Laki-laki', '0081103@edutrack.com', '081234569303', 'Jl. Sastra No. 8, Bandung', 'Sastra & Bahasa', 'Sastra China', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0081104', 108, 'Dr. Liu Ying, M.A', '1991-05-28', 'Perempuan', '0081104@edutrack.com', '081234569304', 'Jl. Sastra No. 9, Bandung', 'Sastra & Bahasa', 'Sastra China', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0081105', 109, 'Dr. Zhang Lei, M.A', '1987-09-12', 'Laki-laki', '0081105@edutrack.com', '081234569305', 'Jl. Sastra No. 10, Bandung', 'Sastra & Bahasa', 'Sastra China', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0082101', 110, 'Dr. Tanaka Yuki, M.A', '1986-07-15', 'Perempuan', '0082101@edutrack.com', '081234569401', 'Jl. Sastra No. 11, Bandung', 'Sastra & Bahasa', 'Sastra Jepang', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0082102', 111, 'Dr. Yamamoto Akira, M.A', '1983-10-22', 'Laki-laki', '0082102@edutrack.com', '081234569402', 'Jl. Sastra No. 12, Bandung', 'Sastra & Bahasa', 'Sastra Jepang', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0082103', 112, 'Dr. Sato Haruka, M.A', '1989-03-18', 'Perempuan', '0082103@edutrack.com', '081234569403', 'Jl. Sastra No. 13, Bandung', 'Sastra & Bahasa', 'Sastra Jepang', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0082104', 113, 'Dr. Suzuki Ren, M.A', '1985-11-25', 'Laki-laki', '0082104@edutrack.com', '081234569404', 'Jl. Sastra No. 14, Bandung', 'Sastra & Bahasa', 'Sastra Jepang', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0082105', 114, 'Dr. Watanabe Mai, M.A', '1990-06-08', 'Perempuan', '0082105@edutrack.com', '081234569405', 'Jl. Sastra No. 15, Bandung', 'Sastra & Bahasa', 'Sastra Jepang', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0083101', 115, 'Dr. H. Suryanto, S.H., M.H', '1984-05-10', 'Laki-laki', '0083101@edutrack.com', '081234569501', 'Jl. Hukum No. 1, Bandung', 'Hukum', 'Ilmu Hukum', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0083102', 116, 'Dr. Hj. Ratna Dewi, S.H., M.H', '1988-09-15', 'Perempuan', '0083102@edutrack.com', '081234569502', 'Jl. Hukum No. 2, Bandung', 'Hukum', 'Ilmu Hukum', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0083103', 117, 'Dr. Arief Hidayat, S.H., M.H', '1982-01-20', 'Laki-laki', '0083103@edutrack.com', '081234569503', 'Jl. Hukum No. 3, Bandung', 'Hukum', 'Ilmu Hukum', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0083104', 118, 'Dr. Susi Susanti, S.H., M.H', '1991-06-28', 'Perempuan', '0083104@edutrack.com', '081234569504', 'Jl. Hukum No. 4, Bandung', 'Hukum', 'Ilmu Hukum', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0083105', 119, 'Dr. Bambang Soepeno, S.H., M.H', '1987-10-12', 'Laki-laki', '0083105@edutrack.com', '081234569505', 'Jl. Hukum No. 5, Bandung', 'Hukum', 'Ilmu Hukum', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0084101', 120, 'Dr. Agung Kurniawan, M.Sn', '1986-04-18', 'Laki-laki', '0084101@edutrack.com', '081234569601', 'Jl. Seni No. 1, Bandung', 'Seni & Desain', 'Seni Rupa Murni', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0084102', 121, 'Dr. Retno Maruti, M.Sn', '1983-08-22', 'Perempuan', '0084102@edutrack.com', '081234569602', 'Jl. Seni No. 2, Bandung', 'Seni & Desain', 'Seni Rupa Murni', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0084103', 122, 'Dr. Danu Wicaksono, M.Sn', '1989-12-14', 'Laki-laki', '0084103@edutrack.com', '081234569603', 'Jl. Seni No. 3, Bandung', 'Seni & Desain', 'Seni Rupa Murni', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0084104', 123, 'Dr. Karina Putri, M.Sn', '1985-03-25', 'Perempuan', '0084104@edutrack.com', '081234569604', 'Jl. Seni No. 4, Bandung', 'Seni & Desain', 'Seni Rupa Murni', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0084105', 124, 'Dr. Fadhil Akbar, M.Sn', '1990-07-08', 'Laki-laki', '0084105@edutrack.com', '081234569605', 'Jl. Seni No. 5, Bandung', 'Seni & Desain', 'Seni Rupa Murni', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0085101', 125, 'Dr. Ricky Pratama, M.T', '1984-06-12', 'Laki-laki', '0085101@edutrack.com', '081234569701', 'Jl. Sistem Informasi No. 1, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Informasi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0085102', 126, 'Dr. Nina Sari, M.T', '1988-09-18', 'Perempuan', '0085102@edutrack.com', '081234569702', 'Jl. Sistem Informasi No. 2, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Informasi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0085103', 127, 'Dr. Hadi Wijaya, M.T', '1982-02-22', 'Laki-laki', '0085103@edutrack.com', '081234569703', 'Jl. Sistem Informasi No. 3, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Informasi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0085104', 128, 'Dr. Diah Ayu, M.T', '1991-05-15', 'Perempuan', '0085104@edutrack.com', '081234569704', 'Jl. Sistem Informasi No. 4, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Informasi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0085105', 129, 'Dr. Yusuf Rahman, M.T', '1987-11-28', 'Laki-laki', '0085105@edutrack.com', '081234569705', 'Jl. Sistem Informasi No. 5, Bandung', 'Teknologi Rekayasa Cerdas', 'Sistem Informasi', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0086101', 130, 'Prof. Dr. dr. Andi Wijaya, Sp.PD', '1975-03-15', 'Laki-laki', '0086101@edutrack.com', '081234569801', 'Jl. Kedokteran No. 1, Bandung', 'Kedokteran', 'Kedokteran', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0086102', 131, 'Prof. Dr. dr. Siti Rahayu, Sp.OG', '1978-07-20', 'Perempuan', '0086102@edutrack.com', '081234569802', 'Jl. Kedokteran No. 2, Bandung', 'Kedokteran', 'Kedokteran', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0086103', 132, 'Dr. dr. Budi Santosa, Sp.B', '1983-11-10', 'Laki-laki', '0086103@edutrack.com', '081234569803', 'Jl. Kedokteran No. 3, Bandung', 'Kedokteran', 'Kedokteran', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0086104', 133, 'Dr. dr. Rina Kusuma, Sp.A', '1985-04-25', 'Perempuan', '0086104@edutrack.com', '081234569804', 'Jl. Kedokteran No. 4, Bandung', 'Kedokteran', 'Kedokteran', '2026-01-17 13:59:10', '2026-01-17 13:59:10'),
('0086105', 134, 'Dr. dr. Hendra Gunawan, Sp.JP', '1980-09-18', 'Laki-laki', '0086105@edutrack.com', '081234569805', 'Jl. Kedokteran No. 5, Bandung', 'Kedokteran', 'Kedokteran', '2026-01-17 13:59:10', '2026-01-17 13:59:10');

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
('2430001', 26, 'Errvin Junius', 'Psikologi', '2430001@edutrack.com', 'Laki-laki', '2005-08-05', 'Jl. Babakan Jeruk No 13', '089574092836', '2026-01-08 00:28:17', '2026-01-08 00:28:17'),
('2430002', 27, 'Hans Maulana', 'Psikologi', '2430002@edutrack.com', 'Laki-laki', '2004-09-06', 'Jl. Sukakarya No 20', '089573485901', '2026-01-08 00:29:37', '2026-01-08 00:29:37'),
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
  `semester` varchar(11) NOT NULL,
  `sifat` varchar(255) NOT NULL DEFAULT 'Wajib',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mata_kuliah`
--

INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `jurusan`, `sks`, `semester`, `sifat`, `created_at`, `updated_at`) VALUES
('AC-101', 'Basic Accounting + Assistance', 'Akuntansi', 3, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-102', 'Intermediate Financial Accounting I', 'Akuntansi', 3, '2', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-103', 'Business Math', 'Akuntansi', 3, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-104', 'Financial Management', 'Akuntansi', 3, '6', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-105', 'Business and Management Basic', 'Akuntansi', 3, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-106', 'Management Information System', 'Akuntansi', 3, '6', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-107', 'Micro Economics', 'Akuntansi', 3, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-108', 'Macro Economics', 'Akuntansi', 3, '6', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-109', 'Pancasila Philosophy', 'Akuntansi', 2, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-110', 'Business Communication and Presentation Skills', 'Akuntansi', 3, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-111', 'Indonesian Language/Scientific Paper Writing Technique', 'Akuntansi', 2, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-112', 'Business and Corporation Law', 'Akuntansi', 2, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-113', 'Christian Education', 'Akuntansi', 2, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-114', 'Business English / Basic Chinese Conversation', 'Akuntansi', 2, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-115', 'Religion Phenomenology', 'Akuntansi', 2, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-117', 'Ethics', 'Akuntansi', 2, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-202', 'Intermediate Financial Accounting II + SAP Modul Fundamental Software Practice', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-202S5', 'Intermediate Financial Accounting III + SAP Modul Fundamental Software Practice', 'Akuntansi', 3, '5', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-203', 'Accounting Theory', 'Akuntansi', 3, '2', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-204', 'Intermediate Management Accounting', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-205', 'Basic Management Accounting', 'Akuntansi', 3, '2', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-206', 'Economic Statistics + Data Processing Application', 'Akuntansi', 3, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-207', 'Advanced Financial Management', 'Akuntansi', 3, '7', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-208', 'Taxation II', 'Akuntansi', 3, '2', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-209', 'Accounting Information System 1 + Software Accurate Application', 'Akuntansi', 3, '2', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-210', 'Operations Management', 'Akuntansi', 3, '6', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-211', 'Taxation I', 'Akuntansi', 3, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-212', 'Interpersonal and Leadership Skills', 'Akuntansi', 3, '6', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-213', 'Civic Education', 'Akuntansi', 2, '1', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-214', 'Negotiation', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-216', 'Advanced Mandarin Conversation', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-218', 'Behavioral Aspects in Accounting', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-301', 'Advanced Financial Accounting I', 'Akuntansi', 3, '4', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-302', 'Advanced Financial Accounting II', 'Akuntansi', 3, '6', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-303', 'Advanced Management Accounting', 'Akuntansi', 3, '4', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-304', 'Research Methodology', 'Akuntansi', 3, '4', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-305', 'Accounting Information System 2 + SAP Modul Financial & Controlling Software Practice', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-306', 'Auditing II + Atlas Application Practice', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-307', 'Financial Report Analysis', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-308', 'Taxation Management', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-309', 'Auditing I', 'Akuntansi', 3, '2', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-310', 'Business Ethics and Governance', 'Akuntansi', 3, '7', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-311', 'Statistics II + Data Processing Application', 'Akuntansi', 3, '7', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-312', 'Entrepreneurship', 'Akuntansi', 3, '7', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-313', 'Tax Accounting', 'Akuntansi', 3, '7', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-314', 'Strategic Planning and Risk Analysis', 'Akuntansi', 3, '7', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-401', 'Portfolio Theory & Investment Analysis', 'Akuntansi', 3, '4', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-402', 'Thesis', 'Akuntansi', 6, '8', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-403', 'Information System Auditing + ACL/ Tableau/ Clickview Software Practice', 'Akuntansi', 3, '4', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-405', 'Public Sector Accounting', 'Akuntansi', 3, '4', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-407', 'Internal Auditing', 'Akuntansi', 3, '3', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-417', 'Standard Aspects in Preparation of Financial Reports', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-421', 'Sustainability Reporting', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-423', 'Government Financial Accounting', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-425', 'Government Management Accounting', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-427', 'Banking Accounting', 'Akuntansi', 3, 'Ganjil', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC-SEM', 'Seminar Course', 'Akuntansi', 3, '8', 'Wajib', '2026-01-13 18:20:00', '2026-01-13 18:20:00'),
('AC101h', 'Akuntansi Dasar Dan Asistensi', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('AC205h', 'Akuntansi Dan Manajemen Dasar', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('AC209h', 'Sistem Informasi Akuntansi Dan Software Accurate', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('AC309h', 'Pengauditan', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('AF101', 'Komposisi 2D', 'Seni Rupa Murni', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF101AR', 'Komposisi 2D', 'Arsitektur', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF102', 'Komposisi 3D', 'Seni Rupa Murni', 4, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF102AR', 'Komposisi 3D', 'Arsitektur', 4, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF103', 'Menggambar I', 'Seni Rupa Murni', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF103AR', 'Menggambar 1', 'Arsitektur', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF104', 'Menggambar II', 'Seni Rupa Murni', 4, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF104AR', 'Menggambar 2', 'Arsitektur', 4, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF105', 'Sejarah Seni Rupa dan Desain I', 'Seni Rupa Murni', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF105AR', 'Sejarah Seni Rupa Desain dan Arsitektur 1', 'Arsitektur', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF106', 'Sejarah Seni Rupa dan Desain 2', 'Seni Rupa Murni', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF106AR', 'Sejarah Seni Rupa Desain dan Arsitektur 2', 'Arsitektur', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF107', 'Kreatif Visual 1', 'Seni Rupa Murni', 3, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF107AR', 'Kreatif Visual 1', 'Arsitektur', 3, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF107SRD', 'Kreatif Visual I', 'Seni Rupa dan Desain', 3, '1', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AF108', 'Kreatif Visual 2', 'Seni Rupa Murni', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF108AR', 'Kreatif Visual 2', 'Arsitektur', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF109', 'Pengantar Seni Rupa dan Desain 1', 'Seni Rupa Murni', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF109AR', 'Pengantar Seni Rupa Desain dan Arsitektur 1', 'Arsitektur', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF110', 'Metode Seni Rupa dan Desain 2', 'Seni Rupa Murni', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF110AR', 'Pengantar Seni Rupa Desain dan Arsitektur 2', 'Arsitektur', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF111', 'Menggambar Dasar', 'Desain Interior', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF111AR', 'Bahasa Inggris', 'Arsitektur', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF112', 'Grafis Digital I', 'Seni Rupa Murni', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF112AR', 'Grafis Digital', 'Arsitektur', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF113', 'Komposisi Dua Dimensi', 'Desain Interior', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF114', 'Pengembangan Karakter', 'Desain Interior', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF114AR', 'Pengembangan Karakter', 'Arsitektur', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF115', 'Komposisi Tiga Dimensi', 'Desain Interior', 4, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF117', 'Kreatif Visual', 'Desain Interior', 3, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF119', 'Pengantar Seni Rupa dan Desain', 'Desain Interior', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF121', 'Sejarah Seni Rupa, Desain dan Arsitektur', 'Desain Interior', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF201', 'Bahasa Inggris', 'Seni Rupa Murni', 2, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF202', 'Minor 1', 'Arsitektur', 4, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF206', 'Minor Studio Interior I', 'Seni Rupa Murni', 4, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF208', 'Minor Rancangan Komunikasi Visual I', 'Seni Rupa Murni', 4, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF301', 'Minor 2', 'Arsitektur', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF303', 'Minor Lukis II', 'Seni Rupa Murni', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF305', 'Minor Studio Interior II', 'Seni Rupa Murni', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AF307', 'Minor Rancangan Komunikasi Visual II', 'Seni Rupa Murni', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AG101', 'Menggambar I', 'Seni Rupa dan Desain', 4, '1', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG102', 'Menggambar II', 'Seni Rupa dan Desain', 4, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG103', 'Komposisi 2D', 'Seni Rupa dan Desain', 4, '1', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG104', 'Komposisi 3D', 'Seni Rupa dan Desain', 4, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG105', 'Sejarah Tekstil, Busana dan Mode Dunia', 'Seni Rupa dan Desain', 2, '1', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG106', 'Sejarah Tekstil, Busana dan Mode Kontemporer', 'Seni Rupa dan Desain', 2, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG107', 'Studi Profesi', 'Seni Rupa dan Desain', 3, '1', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG108', 'Sejarah Tekstil, Busana dan Mode Indonesia', 'Seni Rupa dan Desain', 2, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG110', 'Pola dan Jahit Dasar', 'Seni Rupa dan Desain', 3, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG112', 'Reka Tekstil I', 'Seni Rupa dan Desain', 3, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG114', 'Grafis Digital', 'Seni Rupa dan Desain', 3, '2', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG201', 'Studio Desain Busana dan Mode I', 'Seni Rupa dan Desain', 4, '3', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG202', 'Studio Desain Busana dan Mode II', 'Seni Rupa dan Desain', 4, '4', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG203', 'Pola dan Jahit I', 'Seni Rupa dan Desain', 4, '3', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG204', 'Pola dan Jahit II', 'Seni Rupa dan Desain', 4, '4', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG205', 'Ilustrasi Busana dan Mode I', 'Seni Rupa dan Desain', 3, '3', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG206', 'Ilustrasi Busana dan Mode II', 'Seni Rupa dan Desain', 3, '4', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG207', 'Gambar Teknik Busana I', 'Seni Rupa dan Desain', 3, '3', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG208', 'Gambar Teknik Busana II', 'Seni Rupa dan Desain', 3, '4', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG209', 'Proses Kreatif', 'Seni Rupa dan Desain', 2, '3', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG210', 'Grafis Busana dan Mode', 'Seni Rupa dan Desain', 3, '4', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG211', 'Reka Tekstil II', 'Seni Rupa dan Desain', 3, '3', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG212', 'Teknik Presentasi dan Penulisan Kreatif', 'Seni Rupa dan Desain', 3, '4', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG301', 'Studio Desain Busana dan Mode III', 'Seni Rupa dan Desain', 4, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG302', 'Proyek Akhir', 'Seni Rupa dan Desain', 4, '6', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG303', 'Pola dan Jahit III', 'Seni Rupa dan Desain', 4, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG305', 'Produk Busana dan Mode', 'Seni Rupa dan Desain', 3, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG307', 'Teknik Drape', 'Seni Rupa dan Desain', 3, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG309', 'Tinjauan Tren Busana dan Mode', 'Seni Rupa dan Desain', 3, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG311', 'Manajemen Busana dan Mode', 'Seni Rupa dan Desain', 3, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AG313', 'Kerja Praktik', 'Seni Rupa dan Desain', 3, '5', 'Wajib', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AH301', 'Visual Merchandising for Design', 'Seni Rupa dan Desain', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AH302', 'Desain Perhiasan Logam', 'Seni Rupa Murni', 3, '6', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AH302SRD', 'Desain Perhiasan Logam', 'Seni Rupa dan Desain', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AH303', 'Desain Alas Kaki', 'Seni Rupa dan Desain', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AH304', 'Fotografi Busana dan Mode', 'Seni Rupa dan Desain', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AH305', 'Desain Busana dan Mode Berkelanjutan', 'Seni Rupa dan Desain', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:10:00', '2026-01-13 15:10:00'),
('AR201', 'Studio Desain Arsitektur I', 'Arsitektur', 6, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR202', 'Studio Desain Arsitektur II', 'Arsitektur', 6, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR203', 'Teknologi Bangunan dan Konstruksi I', 'Arsitektur', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR204', 'Teknologi Bangunan dan Konstruksi II', 'Arsitektur', 3, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR205', 'Bahan Bangunan dan Konstruksi', 'Arsitektur', 2, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR206', 'Fisika Bangunan dan Desain Ramah Lingkungan', 'Arsitektur', 2, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR207', 'Kewirausahaan', 'Arsitektur', 2, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR208', 'Dasar Perencanaan Tapak dan Lansekap', 'Arsitektur', 2, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR209', 'Perilaku dan Arsitektur', 'Arsitektur', 2, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR210', 'Sejarah Seni Rupa dan Desain Arsitektur Indonesia', 'Arsitektur', 2, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR301', 'Studio Desain Arsitektur III', 'Arsitektur', 6, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR302', 'Studio Desain Arsitektur IV', 'Arsitektur', 6, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR303', 'Sistem Utilitas Bangunan', 'Arsitektur', 2, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR304', 'BIM dalam Arsitektur', 'Arsitektur', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR305', 'Pranata Pembangunan', 'Arsitektur', 2, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR306', 'Perumahan dan Pengembangan Properti', 'Arsitektur', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR307', 'Presentasi Arsitektur', 'Arsitektur', 3, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR308', 'Metodologi Penelitian', 'Arsitektur', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR309', 'Pengantar Arsitektur Perkotaan', 'Arsitektur', 2, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR310', 'Telaah dan Kritik Arsitektur', 'Arsitektur', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR321', 'Pemasaran Properti', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR323', 'Pembangunan Perumahan dan Perkotaan', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR325', 'Ekonomi Bangunan', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR327', 'Permodelan Digital Lanjutan', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR401', 'Studio Desain Arsitektur V', 'Arsitektur', 6, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR402', 'Proyek Akhir Desain Arsitektur', 'Arsitektur', 8, '8', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR403', 'Manajemen Konstruksi dan Kerja Praktek', 'Arsitektur', 3, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR405', 'Pengendalian Lingkungan Bangunan', 'Arsitektur', 2, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR407', 'Studi Mandiri dan Seminar Arsitektur', 'Arsitektur', 2, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR421', 'Makna dalam Arsitektur', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR423', 'Arsitektur Akulturatif', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR425', 'Fengshui dalam Arsitektur', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR427', 'Produksi Bangunan', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('AR429', 'Fenomenologi dalam Arsitektur', 'Arsitektur', 3, 'Ganjil', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('BI930h', 'Teknik Komunikasi dan Negosiasi', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('BID01', 'Pengenalan Data Science', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('BIE03', 'Pengenalan Data Engineering', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('CE607', 'Manajemen Proyek', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('CE827', 'Manajemen Proyek', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('CH-111', 'Menulis Aksara Han', 'Sastra China', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-112', 'Penulisan Teks Naratif', 'Sastra China', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-141', 'Tata Bahasa: Kategori Gramatikal', 'Sastra China', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-142', 'Tata Bahasa: Fungsi Gramatikal', 'Sastra China', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-151', 'Pemahaman Teks untuk Sehari-hari', 'Sastra China', 4, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-152', 'Pemahaman Teks Naratif', 'Sastra China', 4, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-161', 'Pemahaman Lisan untuk Sehari-hari', 'Sastra China', 4, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-162', 'Pemahaman Lisan untuk Interaksi Sosial', 'Sastra China', 4, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-171', 'Percakapan Sehari-hari', 'Sastra China', 4, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-172', 'Percakapan untuk Interaksi Sosial', 'Sastra China', 4, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-182', 'Pengolahan Info Bahasa China', 'Sastra China', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-213', 'Penulisan Teks Deskriptif', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-214', 'Penulisan Teks Argumentatif', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-224', 'Teori Dasar Linguistik', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-233', 'Sejarah Dinasti China', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-234', 'Sejarah China Modern', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-243', 'Tata Bahasa Komunikatif: Kalimat Kontekstual', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-244', 'Tata Bahasa Komunikatif: Adverbia', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-253', 'Pemahaman Teks Deskriptif', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-254', 'Pemahaman Teks Argumentatif', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-263', 'Pemahaman Lisan untuk Kehidupan Kampus', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-264', 'Pemahaman Lisan Kontekstual', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-273', 'Percakapan untuk Kehidupan Kampus', 'Sastra China', 4, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-274', 'Percakapan Kontekstual', 'Sastra China', 4, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-293', 'Pengetahuan Dasar Filsafat', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-294', 'Sejarah Pemikiran Modern', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-315', 'Korespondensi Dunia Kerja', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-325', 'Fonologi dan Filologi', 'Sastra China', 3, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-326', 'Morfologi dan Sintaksis', 'Sastra China', 3, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-335', 'Penerjemahan Karya Tulis', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-336', 'Penerjemahan Lisan Satu Arah', 'Sastra China', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-345', 'Tata Bahasa: Komposisi Bahasa Lisan dan Tulis', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-346', 'Pengajaran Keterampilan Berbahasa', 'Sastra China', 4, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-355', 'Pemahaman Teks Tematis', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-356', 'Pemahaman Teks Media Massa', 'Sastra China', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-365', 'Pemahaman Lisan Tematis', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-375', 'Percakapan Tematis', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-376', 'Percakapan Argumentatif', 'Sastra China', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-385', 'Bahasa China Klasik', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-386', 'Apresiasi Sastra China Klasik', 'Sastra China', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-393', 'Pengenalan Budaya China', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-395', 'Masyarakat Tionghoa Indonesia', 'Sastra China', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-396', 'Metodologi Penelitian', 'Sastra China', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-437', 'Penerjemahan Dokumen', 'Sastra China', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-438', 'Penerjemahan Lisan Dua Arah', 'Sastra China', 2, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-447', 'Pengajaran Ilmu Bahasa', 'Sastra China', 4, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-487', 'Apresiasi Sastra China Moderen', 'Sastra China', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-488', 'Etika Kerja', 'Sastra China', 2, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-497', 'Seminar Pra-skripsi', 'Sastra China', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH-498', 'Skripsi', 'Sastra China', 6, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('CH171', 'Percakapan Sehari-hari (Bahasa Mandarin)', 'Teknik Industri', 4, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('CH171h', 'Percakapan Sehari-Hari (Mandarin)', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('DCE100202221', 'Geologi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE101202221', 'Kimia', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE102202221', 'Fisika 1', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE103202221', 'Statistika & Probabilitas', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE104202221', 'Analisis Struktur 1', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE105202221', 'Mekanika Fluida', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE106202221', 'Algoritma dan Pemrograman', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE107202221', 'Mekanika Tanah 1', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE108202221', 'Matematika Dasar 1', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1110202221', 'Geologi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE11202221', 'Kimia', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE12202221', 'Fisika 1', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE13202221', 'Statistika & Probabilitas', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE133202221', 'Perancangan Geoteknik', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE14202221', 'Analisis Struktur 1', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE15202221', 'Mekanika Fluida', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE16202221', 'Algoritma dan Pemrograman', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE17202221', 'Mekanika Tanah 1', 'Teknik Sipil', 2, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1801202221', 'Struktur Beton Prategang', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1802202221', 'Struktur Jembatan Beton', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1803202221', 'Struktur Jembatan Baja', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1804202221', 'Struktur Bangunan Tinggi Beton', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1805202221', 'Perbaikan dan Perkuatan Struktur', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1806202221', 'Pemrograman Komputer', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1807202221', 'Metode Eksperimental Laboratorium', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1808202221', 'Teknologi Bahan Lanjut/Topik Khusus Material', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1809202221', 'Pemodelan Lalu Lintas', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1810202221', 'Perencanaan Transportasi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1811202221', 'Perencanaan Transportasi Publik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1812202221', 'Perancangan Bandar Udara', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1813202221', 'Perancangan Pelabuhan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1814202221', 'Perancangan Jalan Rel', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1815202221', 'Perancangan Drainase (jalan) Perkotaan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1816202221', 'Manajemen Transportasi Perkotaan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1817202221', 'Sistem Manajemen Pemeliharaan Jalan & Jembatan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1818202221', 'Rekayasa Lalulintas Lanjutan (MKJI + PKJI)', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1819202221', 'Bangunan Air Khusus', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1820202221', 'Pengembangan Sumber Daya Air', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE18202221', 'Matematika Dasar 1', 'Teknik Sipil', 3, '1', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1821202221', 'Bangunan Lepas Pantai', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1822202221', 'Teknik Pantai', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1823202221', 'Perancangan Geoteknik Lanjut', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1824202221', 'Desain Pondasi Lanjut', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1825202221', 'Rekayasa Pelaksanaan Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1826202221', 'Manajemen Proyek Lanjut', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1827202221', 'Ekonomi Teknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1828202221', 'Keamanan, Kesehatan, Keselamatan dan Lingkungan dalam Proyek Geoteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1829202221', 'Introduction Building Information Modelling', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1830202221', 'Penggunaan Aplikasi dalam Rekayasa Struktur', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1831202221', 'Penggunaan Aplikasi dalam Rekayasa Geoteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1832202221', 'Penggunaan Aplikasi dalam Transportasi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1833202221', 'Penggunaan Aplikasi dalam Hidroteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1834202221', 'Tender Proyek Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1835202221', 'Studi Kelayakan dan Bisnis Plan Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1836202221', 'Manajemen Developer Perumahan/Pengembang Perumahan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1837202221', 'Building Maintenance/Pemeliharaan Bangunan Publik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1838202221', 'Mitigasi Bencana', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1839202221', 'Pengantar Penyelidikan Geoteknik Lepas Pantai', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1840202221', 'Aplikasi Geoteknik Pada Timbunan dan Pemadatan Jalan Tambang', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1841202221', 'Penanggulangan Banjir/Drainase Perkotaan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1842202221', 'Desain Struktur Baja Canai Dingin', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1843202221', 'Manajemen Bisnis Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1844202221', 'Manajemen Risiko', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1845202221', 'Manajemen K4', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1847202221', 'Komunikasi, Negosiasi, dan Kepemimpinan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1848202221', 'Topik Khusus Rekayasa Struktur', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1849202221', 'Topik Khusus Rekayasa Geoteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1850202221', 'Topik Khusus Rekayasa Transportasi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1851202221', 'Topik Khusus Rekayasa Hidroteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1852202221', 'Topik Khusus Rekayasa Manajemen Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1853202221', 'Manajemen Perparkiran', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1854202221', 'Mekanikal Elektrikal Plumbing', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1855202221', 'Ilmu Sosial dan Budaya Dasar', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE1857202221', 'Perencanaan & Pengendalian Proyek', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE200202221', 'Analisis Tegangan Bahan', 'Teknik Sipil', 3, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE201202221', 'Menggambar Teknik', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE202202221', 'Pemetaan', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE210202221', 'Matematika Dasar 2', 'Teknik Sipil', 3, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE21202221', 'Matematika Dasar 2', 'Teknik Sipil', 3, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE212202221', 'Fisika 2', 'Teknik Sipil', 3, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE213202221', 'Statistika dan Probabilitas Lanjutan', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE214202221', 'Analisis Struktur 2', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE217202221', 'Mekanika Tanah 2', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE2202221', 'Aplikasi BIM', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE22202221', 'Fisika 2', 'Teknik Sipil', 3, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE23202221', 'Analisis Tegangan Bahan', 'Teknik Sipil', 3, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE233202221', 'Lab. Lalulintas', 'Teknik Sipil', 1, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE24202221', 'Analisis Struktur 2', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE25202221', 'Menggambar Teknik', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE26202221', 'Pemetaan', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE27202221', 'Mekanika Tanah 2', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE300202221', 'Metode Numerik', 'Teknik Sipil', 3, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE301202221', 'Hidrologi', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE30202221', 'Metode Numerik', 'Teknik Sipil', 3, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE302202221', 'Desain Pondasi 1', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE303202221', 'Rekayasa Lalulintas', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE304202221', 'Teknologi Bahan', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE31202221', 'Matematika Dasar 3', 'Teknik Sipil', 3, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE312202221', 'Aplikasi BIM', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE313202221', 'Lab. Pemetaan', 'Teknik Sipil', 1, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE314202221', 'Lab. Teknologi Beton', 'Teknik Sipil', 1, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE315202221', 'Dasar Rekayasa Transportasi', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE321202221', 'Matematika Dasar 3', 'Teknik Sipil', 3, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE32202221', 'Hidrologi', 'Teknik Sipil', 3, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE327202221', 'Lab. Mekanika Tanah', 'Teknik Sipil', 1, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE330202221', 'Dinamika Struktur dan Rekayasa Gempa', 'Teknik Sipil', 3, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE33202221', 'Aplikasi BIM', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE333202221', 'Statistika dan Probabilitas Lanjutan', 'Teknik Sipil', 2, '2', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE334202221', 'Kewirausahaan dan Inovasi', 'Teknik Sipil', 2, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE335202221', 'Dasar Rekayasa Transportasi', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE336202221', 'Perancangan Bangunan Gedung Baja', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE337202221', 'Perancangan Jembatan', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE34202221', 'Desain Pondasi 1', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE343202221', 'Bahasa Inggris', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE35202221', 'Teknologi Bahan', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE36202221', 'Lab. Teknologi Beton', 'Teknik Sipil', 1, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE37202221', 'Rekayasa Lalulintas', 'Teknik Sipil', 2, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE38202221', 'Lab. Pemetaan', 'Teknik Sipil', 1, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE393202221', 'Hidraulika', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE394202221', 'Teknik Drainase', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE400202221', 'Struktur Beton Bertulang 1', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE401202221', 'Struktur Baja 1', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE402202221', 'Metode Pelaksanaan Konstruksi', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE403202221', 'Hidraulika', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE404202221', 'Geometri Jalan Raya', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE405202221', 'Ilmu Lingkungan', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE41202221', 'Matematika Dasar 4', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE412202221', 'Desain Pondasi 2', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE413202221', 'Lab Mekanika Fluida dan Hidraulika', 'Teknik Sipil', 1, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE42202221', 'Struktur Baja 1', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE431202221', 'Matematika Dasar 4', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE43202221', 'Metode Pelaksanaan Konstruksi', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE433202221', 'Geometri Jalan Raya', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE435202221', 'Perancangan Bangunan Gedung Beton', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE44202221', 'Desain Pondasi 2', 'Teknik Sipil', 2, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE500202221', 'Lab. Material Jalan', 'Teknik Sipil', 1, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE501202221', 'Rekayasa Irigasi', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE502202221', 'Metodologi Penelitian', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE504202221', 'Teknik Drainase', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE510202221', 'Struktur Beton Bertulang 2', 'Teknik Sipil', 3, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE511202221', 'Struktur Baja 2', 'Teknik Sipil', 3, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE51202221', 'Struktur Baja 2', 'Teknik Sipil', 3, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE512202221', 'Lab. Lalulintas', 'Teknik Sipil', 1, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE513202221', 'Struktur Kayu', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE514202221', 'Lab. Manajemen Proyek', 'Teknik Sipil', 1, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE515202221', 'Perkerasan Jalan Raya', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE516202221', 'Etika', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE517202221', 'Manajemen Proyek', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE52202221', 'Rekayasa Irigasi', 'Teknik Sipil', 3, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE53202221', 'Struktur Beton Bertulang 2', 'Teknik Sipil', 3, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE533202221', 'Perkerasan Jalan Raya', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE54202221', 'Lab. Material Jalan', 'Teknik Sipil', 1, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE55202221', 'Hidraulika', 'Teknik Sipil', 3, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE56202221', 'Lab Mekanika Fluida dan Hidraulika', 'Teknik Sipil', 1, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE600202221', 'Kerja Praktek', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE601202221', 'Perancangan Geoteknik', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE603202221', 'Bahasa Inggris', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE610202221', 'Perancangan Jalan', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE61202221', 'Kerja Praktek', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE62202221', 'Perancangan Jalan', 'Teknik Sipil', 3, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE633202221', 'Etika', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE700202221', 'Dinamika Struktur dan Rekayasa Gempa', 'Teknik Sipil', 3, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE704202221', 'Kewirausahaan dan Inovasi', 'Teknik Sipil', 2, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE705202221', 'Perancangan Bangunan Gedung Beton', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE706202221', 'Perancangan Bangunan Gedung Baja', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE707202221', 'Perancangan Jembatan', 'Teknik Sipil', 2, '6', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE711202221', 'Perancangan Irigasi', 'Teknik Sipil', 2, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE71202221', 'Teknik Drainase', 'Teknik Sipil', 3, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE713202221', 'Perancangan Irigasi', 'Teknik Sipil', 2, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE7202221', 'Lab. Mekanika Tanah', 'Teknik Sipil', 1, '3', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE72202221', 'Lab. Manajemen Proyek', 'Teknik Sipil', 1, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE728202221', 'Tugas Akhir', 'Teknik Sipil', 4, '8', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE730202221', 'Struktur Beton Bertulang 1', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE73202221', 'Analisis Struktur dengan Metode Elemen Hingga', 'Teknik Sipil', 3, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE734202221', 'Analisis Struktur dengan Metode Elemen Hingga', 'Teknik Sipil', 2, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE74202221', 'Kewirausahaan dan Inovasi', 'Teknik Sipil', 2, '7', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE742202221', 'Metodologi Penelitian', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE800202221', 'Tugas Akhir', 'Teknik Sipil', 4, '8', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE801202221', 'Struktur Beton Prategang', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE802202221', 'Struktur Jembatan Beton', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE803202221', 'Struktur Jembatan Baja', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00');
INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `jurusan`, `sks`, `semester`, `sifat`, `created_at`, `updated_at`) VALUES
('DCE804202221', 'Struktur Bangunan Tinggi Beton', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE805202221', 'Perbaikan dan Perkuatan Struktur', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE806202221', 'Pemrograman Komputer', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE807202221', 'Metode Eksperimental Laboratorium', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE808202221', 'Teknologi Bahan Lanjut/Topik Khusus Material', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE809202221', 'Pemodelan Lalu Lintas', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE810202221', 'Perencanaan Transportasi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE811202221', 'Perencanaan Transportasi Publik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE812202221', 'Perancangan Bandar Udara', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE813202221', 'Perancangan Pelabuhan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE814202221', 'Perancangan Jalan Rel', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE815202221', 'Perancangan Drainase (jalan) Perkotaan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE816202221', 'Manajemen Transportasi Perkotaan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE817202221', 'Sistem Manajemen Pemeliharaan Jalan & Jembatan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE818202221', 'Rekayasa Lalulintas Lanjutan (MKJI + PKJI)', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE819202221', 'Bangunan Air Khusus', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE820202221', 'Pengembangan Sumber Daya Air', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE821202221', 'Bangunan Lepas Pantai', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE822202221', 'Teknik Pantai', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE823202221', 'Perancangan Geoteknik Lanjut', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE824202221', 'Desain Pondasi Lanjut', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE825202221', 'Rekayasa Pelaksanaan Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE826202221', 'Manajemen Proyek Lanjut', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE827202221', 'Ekonomi Teknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE828202221', 'Keamanan, Kesehatan, Keselamatan dan Lingkungan dalam Proyek Geoteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE829202221', 'Introduction Building Information Modelling', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE830202221', 'Penggunaan Aplikasi dalam Rekayasa Struktur', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE831202221', 'Penggunaan Aplikasi dalam Rekayasa Geoteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE832202221', 'Penggunaan Aplikasi dalam Transportasi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE833202221', 'Penggunaan Aplikasi dalam Hidroteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE834202221', 'Tender Proyek Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE835202221', 'Studi Kelayakan dan Bisnis Plan Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE836202221', 'Manajemen Developer Perumahan/Pengembang Perumahan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE837202221', 'Building Maintenance/Pemeliharaan Bangunan Publik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE838202221', 'Mitigasi Bencana', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE839202221', 'Pengantar Penyelidikan Geoteknik Lepas Pantai', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE840202221', 'Aplikasi Geoteknik Pada Timbunan dan Pemadatan Jalan Tambang', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE841202221', 'Penanggulangan Banjir/Drainase Perkotaan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE842202221', 'Desain Struktur Baja Canai Dingin', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE843202221', 'Manajemen Bisnis Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE844202221', 'Manajemen Risiko', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE845202221', 'Manajemen K4', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE847202221', 'Komunikasi, Negosiasi, dan Kepemimpinan', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE848202221', 'Topik Khusus Rekayasa Struktur', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE849202221', 'Topik Khusus Rekayasa Geoteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE850202221', 'Topik Khusus Rekayasa Transportasi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE851202221', 'Topik Khusus Rekayasa Hidroteknik', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE852202221', 'Topik Khusus Rekayasa Manajemen Konstruksi', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE853202221', 'Manajemen Perparkiran', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE854202221', 'Mekanikal Elektrikal Plumbing', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE855202221', 'Ilmu Sosial dan Budaya Dasar', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE856202221', 'Analisis Struktur dengan Metode Matriks', 'Teknik Sipil', 2, '8', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE857202221', 'Perencanaan & Pengendalian Proyek', 'Teknik Sipil', 2, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE873202221', 'Struktur Kayu', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE907202221', 'Manajemen Proyek', 'Teknik Sipil', 2, '5', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE928202221', 'Analisis Struktur dengan Metode Matriks', 'Teknik Sipil', 2, '8', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DCE935202221', 'Ilmu Lingkungan', 'Teknik Sipil', 3, '4', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('DI102', 'Menggambar Interior', 'Desain Interior', 4, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI104', 'Grafis Digital', 'Desain Interior', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI106', 'Kreatif Spasial', 'Desain Interior', 3, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI108', 'Pengantar Desain Interior', 'Desain Interior', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI110', 'Sejarah Interior dan Furniture', 'Desain Interior', 2, '2', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI212', 'Studio Desain Interior 2', 'Desain Interior', 4, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI214', 'Sistem Bangunan', 'Desain Interior', 3, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI216', 'Desain Tata Cahaya', 'Desain Interior', 3, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI218', 'Furniture 1', 'Desain Interior', 3, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI221', 'Studio Desain Interior 1', 'Desain Interior', 4, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI222', 'Minor 1', 'Desain Interior', 4, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI223', 'Studi Faktor Manusia', 'Desain Interior', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI225', 'Pengetahuan Bahan', 'Desain Interior', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI227', 'Presentasi Interior', 'Desain Interior', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI229', 'Pewarnaan dan Sketsa Interior', 'Desain Interior', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI318', 'Studio Desain Interior 4', 'Desain Interior', 4, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI320', 'Pemasaran & Perdagangan Produk Interior', 'Desain Interior', 3, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI322', 'Furniture 2', 'Desain Interior', 3, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI324', 'Metodologi Penelitian', 'Desain Interior', 3, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI361', 'Studio Desain Interior 3', 'Desain Interior', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI363', 'Konstruksi dan Detail', 'Desain Interior', 3, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI365', 'Desain Produk Interior', 'Desain Interior', 3, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI367', 'Studi Profesi Desain Interior', 'Desain Interior', 3, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI375', 'Minor 2', 'Desain Interior', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI404', 'Studio Desain Interior 6', 'Desain Interior', 6, '8', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI409', 'Studio Desain Interior 5', 'Desain Interior', 4, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI411', 'Studi Mandiri', 'Desain Interior', 3, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI413', 'Tren dan Gaya Hidup', 'Desain Interior', 3, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI417', 'Manajemen Proyek 1 (Organisasi & SDM)', 'Desain Interior', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DI419', 'Manajemen Proyek 2 (Finansial & Sarana-Prasarana)', 'Desain Interior', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('DKV101202564', 'Komposisi 2D', 'Desain Komunikasi Visual', 4, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV102202564', 'Eksperimen Warna', 'Desain Komunikasi Visual', 3, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV103202564', 'Menggambar Dasar', 'Desain Komunikasi Visual', 4, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV104202564', 'Menggambar Eksploratif', 'Desain Komunikasi Visual', 4, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV105202564', 'Komposisi Bentuk 3D', 'Desain Komunikasi Visual', 3, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV106202564', 'Produksi Grafis', 'Desain Komunikasi Visual', 2, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV107202564', 'Grafis Digital', 'Desain Komunikasi Visual', 3, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV108202564', 'Sejarah Desain Grafis', 'Desain Komunikasi Visual', 2, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV109202564', 'Tinjauan Karya Seni Rupa dan Desain', 'Desain Komunikasi Visual', 2, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV110202564', 'Visual Kreatif', 'Desain Komunikasi Visual', 3, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV201202564', 'Tata Bahasa Visual', 'Desain Komunikasi Visual', 4, '3', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV202202564', 'Praktik Bahasa Visual', 'Desain Komunikasi Visual', 4, '4', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV203202564', 'Ilustrasi Digital', 'Desain Komunikasi Visual', 3, '3', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV204202564', 'Desain Interaktif dalam Media Mobile', 'Desain Komunikasi Visual', 3, '4', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV205202564', 'Tipografi', 'Desain Komunikasi Visual', 3, '3', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV206202564', 'Fotografi Komersial', 'Desain Komunikasi Visual', 3, '4', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV207202564', 'Antarmuka Digital', 'Desain Komunikasi Visual', 3, '3', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV208202564', 'Psikologi Komunikasi', 'Desain Komunikasi Visual', 2, '4', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV209202564', 'Fotografi', 'Desain Komunikasi Visual', 3, '3', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV210202564', 'Visual Design Thinking', 'Desain Komunikasi Visual', 2, '4', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV301202564', 'Desain Grafis Terintegrasi', 'Desain Komunikasi Visual', 4, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV302202564', 'Desain Grafis Eksploratif', 'Desain Komunikasi Visual', 4, '6', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV303202564', 'Aktivasi Jenama', 'Desain Komunikasi Visual', 4, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV304202564', 'Kampanye Sosial Kreatif', 'Desain Komunikasi Visual', 4, '6', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV305202564', 'Grafis untuk Permainan Atas Meja', 'Desain Komunikasi Visual', 4, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV306202564', 'Grafis untuk Permainan Digital', 'Desain Komunikasi Visual', 4, '6', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV307202564', 'Desain Editorial', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV308202564', 'Desain Jenama', 'Desain Komunikasi Visual', 3, '6', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV309202564', 'Copywriting', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV310202564', 'Wirausaha Kreatif', 'Desain Komunikasi Visual', 3, '6', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV311202564', 'Purwarupa Permainan', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV313202564', 'Desain Buku', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV315202564', 'Narasi Visual', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV317202564', 'Karakter dan Aset Permainan', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV319202564', 'Motion Graphic', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV321202564', 'Manajemen Desain Strategis', 'Desain Komunikasi Visual', 3, '5', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV401202564', 'Kolaborasi Desain Grafis', 'Desain Komunikasi Visual', 4, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV402202564', 'Tugas Akhir Desain Grafis', 'Desain Komunikasi Visual', 6, '8', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV403202564', 'Kolaborasi Komunikasi Visual Strategis', 'Desain Komunikasi Visual', 4, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV404202564', 'Tugas Akhir Komunikasi Visual Strategis', 'Desain Komunikasi Visual', 6, '8', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV405202564', 'Kolaborasi Desain Grafis Permainan', 'Desain Komunikasi Visual', 4, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV406202564', 'Tugas Akhir Grafis Permainan', 'Desain Komunikasi Visual', 6, '8', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV407202564', 'Metode dan Presentasi Penelitian', 'Desain Komunikasi Visual', 4, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKV409202564', 'Wawasan Profesi Industri', 'Desain Komunikasi Visual', 3, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('DKVE001202564', 'Fotografi Fashion', 'Desain Komunikasi Visual', 3, 'Ganjil', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE002202564', 'Ilustrasi Fashion', 'Desain Komunikasi Visual', 3, 'Ganjil', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE003202564', 'Kebudayaan Tionghoa Peranakan', 'Desain Komunikasi Visual', 2, 'Ganjil', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE004202564', 'Desain Portofolio', 'Desain Komunikasi Visual', 3, 'Ganjil', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE005202564', 'Videografi', 'Desain Komunikasi Visual', 3, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE006202564', 'Komik', 'Desain Komunikasi Visual', 3, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE007202564', 'Wawasan Media Kreatif', 'Desain Komunikasi Visual', 3, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE008202564', 'Pemodelan 3D Digital', 'Desain Komunikasi Visual', 3, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE009202564', 'Animasi 2D', 'Desain Komunikasi Visual', 3, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVE010202564', 'Desain Kemasan', 'Desain Komunikasi Visual', 3, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVI001202564', 'Kecerdasan Buatan', 'Desain Komunikasi Visual', 2, 'Ganjil', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('DKVI002202564', 'Data Analitik', 'Desain Komunikasi Visual', 2, 'Genap', 'Pilihan', '2026-01-13 14:21:00', '2026-01-13 14:30:00'),
('ED017', 'Pancasila', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED101', 'Pronunciation', 'Sastra Inggris', 4, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED102', 'Rhythm & Intonation', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED103', 'Appreciative Reading', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED104', 'Factual Reading', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED105', 'Appreciative & Accurate Listening', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED105h', 'Menyimak 1/Appreciative And Accurate Listening', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('ED106', 'Selective & Gist Listening', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED107', 'Grammar: Basic Principles about Parts of Speech', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED108', 'Narrative & Descriptive Writing', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED109', 'Sentence & Paragraph Writing', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED110', 'Grammar: Basics of Sentence Construction', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED112', 'Introduction to Linguistics', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED114', 'Survey of English Literature', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED116', 'Introduction to ELT', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED118', 'Preparation for Conversation', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED201', 'Scientific Reading', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED201h', 'Membaca Bacaan Ilmiah/Scientific Reading', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('ED202', 'Critical & Reflective Reading', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED203', 'Combo Listening', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED203h', 'Menyimak 3 (Combo Listening)', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('ED204', 'Critical & Argumentative Listening', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED205', 'Writing for Specific Purposes', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED206', 'Argumentative & Reflective Writing', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED207', 'Phonology & Morphology', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED208', 'Functional Grammar', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED209', 'Theory of Prose', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED210', 'Grammar: Verb Patterns', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED211', 'Survey of American Literature', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED212', 'Semantics', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED213', 'Learning Styles & Strategies', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED214', 'Theory of Drama & Poetry', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED215', 'Methods & Approaches in ELT', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED216', 'Lesson Planning', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED217', 'British Culture and Institution', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED218', 'Argumentative Conversation', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED219', 'Daily Conversation', 'Sastra Inggris', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED219h', 'Percakapan Untuk Sehari-Hari/Daily Conversation', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('ED220', 'Grammar: Noun Patterns', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED301', 'Formal Conversation', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED302', 'Translation: Style (English-Indonesian Translation)', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED303', 'Theatre Production', 'Sastra Inggris', 4, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED304', 'Conversation for Business Purposes', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED305', 'American Culture & Institution', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED306', 'Film Studies', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED307', 'Introduction to Cultural Studies', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED307h', 'Pengantar Kajian Budaya (Introduction To Cultural Studies)', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('ED308', 'Classical Novel', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED309', 'Critical Essay Writing', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED310', 'Grammar: Adjective Patterns', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED310h', 'Percakapan Formal (Formal Conversation)', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('ED311', 'Contemporary Novel', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED312', 'Classical Poetry', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED313', 'Contemporary Poetry', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED314', 'Classical Drama', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED315', 'Contemporary Drama', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED316', 'Stylistics', 'Sastra Inggris', 3, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED317', 'Academic Writing', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED318', 'Linguistics: Thematic Analysis', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED319', 'Pragmatics', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED320', 'Grammar: Connecting Ideas', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED321', 'Discourse Analysis', 'Sastra Inggris', 3, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED322', 'Semiotics', 'Sastra Inggris', 3, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED323', 'Sociolinguistics', 'Sastra Inggris', 3, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED335', 'Translation: Principle and Awareness', 'Sastra Inggris', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED401', 'Translation: Style (Indonesian-English Translation)', 'Sastra Inggris', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED402', 'Interpreting', 'Sastra Inggris', 2, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED403', 'Indonesian Culture', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED405', 'Critical Theories', 'Sastra Inggris', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED410', 'Proposal Seminar Sastra', 'Sastra Inggris', 4, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED420', 'Proposal Seminar Linguistik', 'Sastra Inggris', 4, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('ED425', 'Discussion on Linguistic Issues', 'Sastra Inggris', 2, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('EE641202222', 'Dasar Anatomi dan Fisiologi', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED101202222', 'Probabilitas & Statistik I', 'Teknik Elektro', 2, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED102202222', 'Matematika I', 'Teknik Elektro', 3, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED103202222', 'Fisika I', 'Teknik Elektro', 3, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED104202222', 'Dasar Komputer & Pemrograman', 'Teknik Elektro', 3, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED105202222', 'Rangkaian Listrik I', 'Teknik Elektro', 3, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED106202222', 'Dasar Elektronika', 'Teknik Elektro', 3, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED107202222', 'Kimia', 'Teknik Elektro', 2, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED117202222', 'Praktikum I', 'Teknik Elektro', 1, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED201202222', 'Probabilitas & Statistik II', 'Teknik Elektro', 2, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED202202222', 'Matematika II', 'Teknik Elektro', 3, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED203202222', 'Fisika II', 'Teknik Elektro', 3, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED204202222', 'Dasar Konversi Energi Listrik', 'Teknik Elektro', 2, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED205202222', 'Perancangan Sistem Digital', 'Teknik Elektro', 3, '1', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED206202222', 'Rangkaian Listrik II', 'Teknik Elektro', 3, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED207202222', 'Matematika Diskrit', 'Teknik Elektro', 3, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED208202222', 'Biologi', 'Teknik Elektro', 3, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED218202222', 'Praktikum II', 'Teknik Elektro', 1, '2', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED300202222', 'Bahasa Inggris', 'Teknik Elektro', 2, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED301202222', 'Matematika III', 'Teknik Elektro', 3, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED302202222', 'Jaringan Komputer', 'Teknik Elektro', 3, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED303202222', 'Pemrograman Berorientasi Objek', 'Teknik Elektro', 2, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED304202222', 'Elektronika Analog', 'Teknik Elektro', 3, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED305202222', 'Dasar Telekomunikasi', 'Teknik Elektro', 2, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED306202222', 'Komunikasi Data', 'Teknik Elektro', 3, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED307202222', 'Pengukuran Besaran Listrik', 'Teknik Elektro', 2, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED318202222', 'Praktikum III', 'Teknik Elektro', 1, '3', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED401202222', 'Metodologi Penelitian', 'Teknik Elektro', 2, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED402202222', 'Medan Elektromagnetik', 'Teknik Elektro', 2, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED403202222', 'Matematika IV', 'Teknik Elektro', 3, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED404202222', 'Sinyal dan Sistem', 'Teknik Elektro', 2, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED405202222', 'Sistem Mikroprosesor', 'Teknik Elektro', 3, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED406202222', 'Dasar Sistem Kontrol', 'Teknik Elektro', 2, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED407202222', 'Penguat Operasional', 'Teknik Elektro', 3, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED418202222', 'Praktikum IV', 'Teknik Elektro', 1, '4', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED501202222', 'Manajemen Proyek', 'Teknik Elektro', 2, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED502202222', 'Antarmuka dan Peripheral', 'Teknik Elektro', 3, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED503202222', 'Komponen Sistem Kontrol', 'Teknik Elektro', 3, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED504202222', 'Pengolahan Sinyal Digital', 'Teknik Elektro', 2, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED505202222', 'Elektronika Industri', 'Teknik Elektro', 3, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED516202222', 'Praktikum V', 'Teknik Elektro', 1, '5', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED601202222', 'Kerja Praktik', 'Teknik Elektro', 2, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED611202222', 'Pengantar Robotika', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED612202222', 'Identifikasi Sistem', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED613202222', 'Otomasi Industri', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED614202222', 'Sistem Kontrol Non Linier', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED615202222', 'Sistem Pengendalian Proses', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED616202222', 'Sistem Kontrol Multivariabel', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED617202222', 'Sistem Kontrol Digital', 'Teknik Elektro', 2, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED621202222', 'Saluran & Sistem Transmisi', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED622202222', 'Jaringan Telekomunikasi & Rekayasa Trafik', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED623202222', 'Antena & Propagasi Gelombang', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED624202222', 'Sistem Komunikasi Bergerak', 'Teknik Elektro', 2, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED625202222', 'Elektronika Telekomunikasi', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED626202222', 'Sistem Komunikasi Serat Optik', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED627202222', 'Komunikasi Digital', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED632202222', 'Rekayasa Perangkat Lunak', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED633202222', 'Algoritma dan Struktur Data', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED634202222', 'Pengantar Sistem Cerdas', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED635202222', 'Sistem Berbasis Mikrokontroler', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED636202222', 'Pengantar IoT Praktis', 'Teknik Elektro', 2, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED637202222', 'Arsitektur Sistem Komputer', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED638202222', 'Sistem Operasi', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED642202222', 'Instrumentasi Biomedik', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED643202222', 'Informatika Medis', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED644202222', 'Pengolahan Sinyal Medis', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED645202222', 'Teknologi Pencitraan Medis', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED646202222', 'Desain Sistem Biomedis', 'Teknik Elektro', 3, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED647202222', 'Teknik Biomedik', 'Teknik Elektro', 2, '6', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED701202222', 'Kewirausahaan', 'Teknik Elektro', 2, '7', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED704202222', 'Pemrograman Web', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED705202222', 'Menggambar Teknik', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED801202222', 'Ekonomi Teknik', 'Teknik Elektro', 2, '8', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED803202222', 'Pemrograman Aplikasi Telepon Seluler', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED805202222', 'Tugas Akhir (Capstone Design)', 'Teknik Elektro', 4, '8', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED901202222', 'Kapita Selekta', 'Teknik Elektro', 1, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED902202222', 'Kualitas Energi Listrik', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED903202222', 'Pengenalan Pola', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED904202222', 'Pemodelan dan Simulasi', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED905202222', 'Pengolahan Citra Digital', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED906202222', 'Topik Kontrol Tingkat Menengah (Intermediate)', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED907202222', 'Topik Kontrol Tingkat Lanjut (Advance)', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED908202222', 'Topik Komputer Tingkat Menengah (Intermediate)', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED909202222', 'Topik Komputer Tingkat Lanjut (Advance)', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED910202222', 'Topik Telkom Tingkat Menengah (Intermediate)', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED911202222', 'Topik Telkom Tingkat Lanjut (Advance)', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED912202222', 'Teknik Elektro Lanjut I', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED913202222', 'Teknik Elektro Lanjut II', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED914202222', 'Kapita Selekta I', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED915202222', 'Kapita Selekta II', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED916202222', 'Topik Elektronika Lanjut I', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED917202222', 'Topik Elektronika Lanjut II', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED918202222', 'Topik Kecerdasan Buatan Lanjut I', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED919202222', 'Topik Kecerdasan Buatan Lanjut II', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('EED920202222', 'Topik Aplikasi Teknik Elektro', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('ELA468', 'Smart Grid', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('ELA488', 'Mekatronika', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('GX102202252', 'Wawasan Pelanggan', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('GX111202252', 'Manajemen & Bisnis', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('GX112202252', 'Logika Ekonomi & Proposisi Nilai', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('GX113202252', 'Komunikasi & Negosiasi Bisnis', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('GX211202252', 'Rencana Strategik & Analisis Risiko', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('GX314202252', 'Manajemen Rantai Pasok', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('GX401202252', 'Manajemen Jasa', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('HSK401202564', 'Presentasi Proyek Kolaborasi', 'Desain Komunikasi Visual', 3, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('HSK403202564', 'Evaluasi Proyek Kolaborasi', 'Desain Komunikasi Visual', 3, '7', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('IC801', 'Kewirausahaan', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IEC001202223', 'Sistem Manusia dan Mesin', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC002202223', 'Ergonomi Makro', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC003202223', 'Rekayasa Sistem Kerja', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC004202223', 'Kualitas Jasa', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC005202223', 'Perancangan Eksperimen', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC006202223', 'Manajemen Strategi', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC007202223', 'Manajemen Perbankan', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC008202223', 'Pengendalian Lantai Pabrik', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC009202223', 'Sistem Pengendalian Persediaan Lanjut', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC010202223', 'Sistem Transportasi', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC011202223', 'Hukum Bisnis dan Hak Milik Intelektual', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC012202223', 'Warehouse Management', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC013202223', 'Risk Management', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC014202223', 'Komunikasi Profesional', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC015202223', 'Lean Six Sigma', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC016202223', 'Multicriteria Decision Making', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC017202223', 'Office Ergonomics', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC018202223', 'Pemodelan Rantai Pasok', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC019202223', 'Green Supply Chain', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC020202223', 'Resilient Supply Chain', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC021202223', 'Advance Leadership', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC022202223', 'Creative Thinking', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC023202223', 'Simulasi Dinamika Sistem', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC024202223', 'E-Bisnis', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC025202223', 'Manajemen Proyek', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC101202223', 'Pengantar Teknik Industri', 'Teknik Industri', 2, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC102202223', 'Matematika Dasar 1', 'Teknik Industri', 3, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC103202223', 'Pengantar Ekonomika', 'Teknik Industri', 2, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC105202223', 'Menggambar Teknik', 'Teknik Industri', 2, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC107202223', 'Fisika 1', 'Teknik Industri', 3, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC108202223', 'Statistika 1', 'Teknik Industri', 3, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC109202223', 'Pengetahuan Bahan', 'Teknik Industri', 2, '1', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC201202223', 'Analisis Perancangan Kerja dan Ergonomi 1', 'Teknik Industri', 2, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC202202223', 'Matematika Dasar 2', 'Teknik Industri', 3, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC203202223', 'Sistem Lingkungan Industri', 'Teknik Industri', 2, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC206202223', 'Manajemen Pemasaran', 'Teknik Industri', 2, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC207202223', 'Fisika 2', 'Teknik Industri', 3, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC208202223', 'Praktikum Fisika', 'Teknik Industri', 1, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC209202223', 'Praktikum Menggambar Teknik', 'Teknik Industri', 1, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC210202223', 'Matriks dan Vektor', 'Teknik Industri', 2, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC211202223', 'Statistika 2', 'Teknik Industri', 3, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC212202223', 'Praktikum Statistika', 'Teknik Industri', 1, '2', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC301202223', 'Penelitian Operasional 1', 'Teknik Industri', 3, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC302202223', 'Analisis Perancangan Kerja dan Ergonomi 2', 'Teknik Industri', 2, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC303302223', 'Psikologi Industri', 'Teknik Industri', 3, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC304202223', 'Pemrograman Komputer', 'Teknik Industri', 3, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC305202223', 'Mekanika Teknik', 'Teknik Industri', 2, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC306202223', 'Proses Manufaktur', 'Teknik Industri', 2, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC307202223', 'Keselamatan dan Kesehatan Kerja', 'Teknik Industri', 2, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC308202223', 'Perencanaan dan Pengendalian Produksi 1', 'Teknik Industri', 2, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC309202223', 'Praktikum Terintegrasi Teknik Industri 1', 'Teknik Industri', 2, '3', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC401202223', 'Perencanaan dan Pengendalian Produksi 2', 'Teknik Industri', 2, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC402202223', 'Analisis Biaya', 'Teknik Industri', 2, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC403202223', 'Penelitian Operasional 2', 'Teknik Industri', 3, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC404202223', 'Praktikum Penelitian Operasional', 'Teknik Industri', 1, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC405202223', 'Data Analytics', 'Teknik Industri', 2, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC406202223', 'Ekonomi Teknik', 'Teknik Industri', 3, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC407202223', 'Pemodelan Sistem', 'Teknik Industri', 2, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC408202223', 'Manajemen Rantai Pasok', 'Teknik Industri', 3, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC409202223', 'Praktikum Terintegrasi Teknik Industri 2', 'Teknik Industri', 2, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC410202223', 'Anatomi dan Fisiologi', 'Teknik Industri', 3, '4', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC501202223', 'Pengendalian dan Penjaminan Mutu', 'Teknik Industri', 3, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC502202223', 'Simulasi Komputer', 'Teknik Industri', 3, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC503202223', 'Analisis dan Perancangan Sistem Informasi', 'Teknik Industri', 3, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC504202223', 'Praktikum Analisis dan Perancangan Sistem Informasi', 'Teknik Industri', 1, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC505202223', 'Perancangan Tata Letak Fasilitas', 'Teknik Industri', 3, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC506202223', 'Organisasi dan Manajemen Perusahaan Industri', 'Teknik Industri', 3, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC507202223', 'Metodologi Penelitian', 'Teknik Industri', 2, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC508202223', 'Praktikum Terintegrasi Teknik Industri 3', 'Teknik Industri', 2, '5', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC601202223', 'Manajemen Retail & Logistik', 'Teknik Industri', 2, '7', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC603202223', 'Smart Industry', 'Teknik Industri', 2, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC604202223', 'Kepemimpinan dan Kerja Tim', 'Teknik Industri', 2, '7', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC605202223', 'Percakapan Bahasa Inggris Formal', 'Teknik Industri', 2, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00');
INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `jurusan`, `sks`, `semester`, `sifat`, `created_at`, `updated_at`) VALUES
('IEC606202223', 'Etika & Hukum Bisnis', 'Teknik Industri', 2, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC690202223', 'System Thinking', 'Teknik Industri', 4, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC691202223', 'Penerapan Keilmuan Teknik Industri', 'Teknik Industri', 4, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC692202223', 'Etika dan Profesionalisme', 'Teknik Industri', 4, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC701202223', 'Perancangan Produk', 'Teknik Industri', 2, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC702202223', 'ERP dan CRM', 'Teknik Industri', 3, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC703202223', 'Perancangan Sistem Terpadu', 'Teknik Industri', 4, '7', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC705202223', 'Creativepreneurship', 'Teknik Industri', 3, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC706202223', 'Studium Generale', 'Teknik Industri', 0, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC707202223', 'Praktikum Teknologi Industri', 'Teknik Industri', 1, '6', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC801202223', 'Tugas Akhir', 'Teknik Industri', 4, '8', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IEC802202223', 'Pancasila', 'Teknik Industri', 2, '8', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('IN110', 'Jaringan Komputer', 'Teknik Informatika', 3, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN120', 'Dasar Pemrograman', 'Teknik Informatika', 4, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN121', 'Arsitektur dan Keamanan Jaringan', 'Teknik Informatika', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN142', 'Kecerdasan Mesin', 'Teknik Informatika', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN167', 'Administrasi Jaringan Komputer', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN210', 'Jaringan Komputer', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN211', 'Logika Informatika', 'Teknik Informatika', 3, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN212', 'Web Dasar', 'Teknik Informatika', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN213', 'Bahasa Inggris', 'Teknik Informatika', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN214', 'Pengantar Aplikasi Komputer', 'Teknik Informatika', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN214h', 'Pengantar Aplikasi Komputer', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('IN215', 'Sibernetika', 'Teknik Informatika', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN215h', 'Sibernetika', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('IN216', 'Computational Thinking', 'Teknik Informatika', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN217', 'Teknik Komunikasi Bahasa Inggris', 'Teknik Informatika', 2, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN220', 'Dasar Pemrograman', 'Teknik Elektro', 4, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN221', 'Arsitektur dan Keamanan Jaringan', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN222', 'Arsitektur Komputer Modern', 'Teknik Informatika', 2, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN223', 'Aljabar Linier', 'Teknik Informatika', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN224', 'Desain Basis Data', 'Teknik Informatika', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN235', 'Pola Desain Perangkat Lunak', 'Teknik Informatika', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN236', 'Pemrograman Terapan', 'Teknik Informatika', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN240', 'Pemrograman Web Lanjut', 'Teknik Informatika', 4, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN241', 'Statistika', 'Teknik Informatika', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN242', 'Kecerdasan Mesin', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN244', 'Strategi Algoritmik', 'Teknik Informatika', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN250', 'Manajemen Proyek', 'Teknik Informatika', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN252', 'Desain Antarmuka', 'Teknik Informatika', 2, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN253', 'Grafika Komputer', 'Teknik Informatika', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN254', 'Proyek Perangkat Lunak', 'Teknik Informatika', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN255', 'Proses Bisnis', 'Teknik Informatika', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN260', 'Metode Penelitian Informatika', 'Teknik Informatika', 2, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN261', 'Start-up Technopreneur', 'Teknik Informatika', 3, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN262', 'Pemrograman Mobile', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN263', 'Competitive Programming', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN264', 'Web Semantik', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN265', 'Pemrosesan Data Berbasis Cloud', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN266', 'Pengenalan Pemrograman Game', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN267', 'Administrasi Jaringan Komputer', 'Teknik Elektro', 4, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN268', 'Ethical Hacking 1', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN269', 'Kecerdasan Bisnis', 'Teknik Informatika', 3, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN270', 'Kerja Praktik', 'Teknik Informatika', 4, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN271', 'Internet of Things', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN272', 'Pengolahan Citra Digital', 'Teknik Informatika', 3, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN273', 'Pemrograman Game', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN274', 'Ethical Hacking 2', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN275', 'Progressive Web Apps', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN276', 'Pencarian Informasi Media Online', 'Teknik Informatika', 3, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN280', 'Seminar Tugas Akhir', 'Teknik Informatika', 2, '8', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN281', 'Tugas Akhir', 'Teknik Informatika', 4, '8', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN285', 'Pemrograman Multi-Platform', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN286', 'Pemrosesan Bahasa Alami', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('IN287', 'Computer Vision', 'Teknik Elektro', 4, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN288', 'AI Computing Platform', 'Teknik Elektro', 4, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN290', 'Pengantar Health Informatics', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('IN291', 'Desain Sistem Kesehatan', 'Teknik Elektro', 3, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('INF29420227 2', 'Digital Marketing', 'Teknik Informatika', 4, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KE201h', 'Videografi', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('KE203h', 'Animasi 2d', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('KUK017', 'Pancasila', 'Kedokteran', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK020', 'Bahasa Indonesia', 'Kedokteran', 2, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK024', 'Civil Education', 'Kedokteran', 2, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK060', 'Christian Religious Education / Phenomenology of Religion', 'Kedokteran', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK062', 'Pendidikan Agama Kristen / Fenomenologi Agama', 'Kedokteran', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK101', 'Basic Medical Science 1 & Study Skills', 'Kedokteran', 4, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK102', 'Basic Medical Science 2', 'Kedokteran', 4, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK103', 'Basic Medical Science 3 & Bioethic', 'Kedokteran', 4, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK104', 'Basic Medical Science 4 & Communication', 'Kedokteran', 4, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK105', 'Musculosceletal System', 'Kedokteran', 5, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK106', 'Hematology & Immunology', 'Kedokteran', 6, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK107', 'Endocrine System', 'Kedokteran', 5, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK108', 'Urinary Tract System & Body Fluid', 'Kedokteran', 6, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK209', 'Gastrointestinal System', 'Kedokteran', 6, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK210', 'Hepatobiliary System', 'Kedokteran', 6, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK211', 'Cardiovascular System', 'Kedokteran', 7, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK213', 'Respiratory System', 'Kedokteran', 8, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK215', 'Reproductive System', 'Kedokteran', 8, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK317', 'Nervous System', 'Kedokteran', 7, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK318', 'Eyes & Integumentary System', 'Kedokteran', 6, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK319', 'Ear, Nose, & Throat', 'Kedokteran', 6, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK320', 'Medical Research', 'Kedokteran', 5, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK321', 'Infectious Diseases 1', 'Kedokteran', 5, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK322', 'Infectious Diseases 2', 'Kedokteran', 5, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK323', 'Emergency & Traumatology', 'Kedokteran', 8, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK425', 'Growth & Development', 'Kedokteran', 5, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK426', 'Behavioral Science & Clinical Psychiatry', 'Kedokteran', 6, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK427', 'Public Health & Family Medicine', 'Kedokteran', 7, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK429', 'Mini Thesis', 'Kedokteran', 4, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK430', 'Medical Nutrition', 'Kedokteran', 1, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK431', 'Medical Accupuncture', 'Kedokteran', 1, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('KUK432', 'Herbal Medicine', 'Kedokteran', 1, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('ME208h', 'Manajemen Sumber Daya Insani', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('ME214h', 'Inovasi Dan Kewirausahaan', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('ME312h', 'Riset Pasar', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('MGX102202252', 'Wawasan Pelanggan', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MGX111202252', 'Manajemen & Bisnis', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MGX112202252', 'Logika Ekonomi & Proposisi Nilai', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MGX113202252', 'Komunikasi & Negosiasi Bisnis', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MGX211202252', 'Rencana Strategik & Analisis Risiko', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MGX314202252', 'Manajemen Rantai Pasok', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MGX401202252', 'Manajemen Jasa', 'Teknik Sipil', 3, 'Ganjil', 'Wajib', '2026-01-13 15:30:00', '2026-01-13 15:30:00'),
('MK-017', 'Pancasila', 'Sastra China', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK-024', 'Pendidikan Kewarganegaraan', 'Sastra China', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK-039', 'Teknik Penulisan Ilmiah', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK-060', 'Fenomenologi Agama', 'Sastra China', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK-062', 'Pendidikan Agama Kristen', 'Sastra China', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK001P', 'Paradigma Pemrograman (Praktikum)', 'Teknik Informatika', 1, '3', 'Wajib', '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK001T', 'Paradigma Pemrograman (Teori)', 'Teknik Informatika', 3, '3', 'Wajib', '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK002P', 'Desain Basis Data Lanjut (Praktikum)', 'Teknik Informatika', 1, '3', 'Wajib', '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK002T', 'Desain Basis Data Lanjut (Teori)', 'Teknik Informatika', 2, '3', 'Wajib', '2025-12-29 06:54:13', '2026-01-03 03:22:32'),
('MK003', 'Rekayasa Perangkat Lunak', 'Teknik Informatika', 3, '3', 'Wajib', '2025-12-29 06:54:13', '2025-12-29 01:04:02'),
('MK004', 'Teknologi Multimedia', 'Teknik Informatika', 2, '3', 'Wajib', '2025-12-29 00:40:18', '2025-12-29 00:58:31'),
('MK005', 'Matematika Diskrit', 'Teknik Informatika', 3, '3', 'Wajib', '2025-12-29 00:59:19', '2025-12-29 00:59:19'),
('MK006P', 'Algoritma Struktur Data (Praktikum)', 'Teknik Informatika', 1, '3', 'Wajib', '2025-12-29 01:17:34', '2026-01-03 03:22:32'),
('MK006T', 'Algoritma Struktur Data (Teori)', 'Teknik Informatika', 3, '3', 'Wajib', '2025-12-29 01:05:13', '2025-12-29 01:17:44'),
('MK007', 'Sistem Operasi Komputer', 'Teknik Informatika', 2, '3', 'Wajib', '2025-12-29 01:18:06', '2025-12-29 01:18:06'),
('MK024', 'Kewarganegaraan', 'Sastra Inggris', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK039', 'Bahasa Indonesia', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK052', 'Komputer', 'Sastra Inggris', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK060', 'Fenomenologi Agama', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK061', 'Etika', 'Sastra Inggris', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK062', 'Pendidikan Agama Kristiani', 'Sastra Inggris', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MK101P', 'Pengantar Teknologi Komputer (Praktikum)', 'Sistem Komputer', 1, '3', 'Wajib', '2026-01-07 11:01:16', '2026-01-07 23:41:27'),
('MK101T', 'Pengantar Teknologi Komputer (Teori)', 'Sistem Komputer', 2, '3', 'Wajib', '2026-01-07 10:52:22', '2026-01-07 23:41:31'),
('MK102', 'Probabilitas & Statistika', 'Sistem Komputer', 2, '3', 'Wajib', '2026-01-07 10:56:27', '2026-01-07 23:41:35'),
('MK103', 'Kalkulus', 'Sistem Komputer', 3, '3', 'Wajib', '2026-01-07 10:56:52', '2026-01-07 23:41:10'),
('MK104', 'Matematika Diskrit', 'Sistem Komputer', 3, '3', 'Wajib', '2026-01-07 10:57:13', '2026-01-07 23:41:17'),
('MK105', 'Logika dan Sistem Digital', 'Sistem Komputer', 3, '3', 'Wajib', '2026-01-07 10:59:31', '2026-01-07 23:41:12'),
('MK106P', 'Pemrograman Tingkat Dasar (Praktikum)', 'Sistem Komputer', 1, '3', 'Wajib', '2026-01-07 12:08:33', '2026-01-07 23:41:22'),
('MK106T', 'Pemrograman Tingkat Dasar (Teori)', 'Sistem Komputer', 3, '3', 'Wajib', '2026-01-07 12:07:49', '2026-01-07 23:41:05'),
('MK107', 'Pendidikan Pancasila', 'Arsitektur', 2, '1', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('MK201', 'Psikologi Pendidikan', 'Psikologi', 4, '3', 'Wajib', '2026-01-07 23:58:21', '2026-01-07 23:58:21'),
('MK202', 'Statistika', 'Psikologi', 3, '3', 'Wajib', '2026-01-07 23:58:43', '2026-01-07 23:58:43'),
('MK203', 'Kode Etik', 'Psikologi', 3, '3', 'Wajib', '2026-01-07 23:59:02', '2026-01-07 23:59:02'),
('MK204P', 'PSIKOPATOLOGI (Praktikum)', 'Psikologi', 3, '3', 'Wajib', '2026-01-08 00:01:38', '2026-01-08 00:01:38'),
('MK204T', 'PSIKOPATOLOGI (Teori)', 'Psikologi', 3, '3', 'Wajib', '2026-01-08 00:01:06', '2026-01-08 00:01:06'),
('MKI00120207 2', 'Digital Rintisan', 'Teknik Informatika', 3, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKI001202223', 'Digital Rintisan', 'Teknik Industri', 3, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('MKI00220207 2', 'Modul Nusantara', 'Teknik Informatika', 2, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKI002202222', 'Modul Nusantara', 'Teknik Elektro', 2, 'Ganjil', 'Wajib', '2026-01-13 15:40:00', '2026-01-13 15:40:00'),
('MKI002202223', 'Modul Nusantara', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('MKI00320207 2', 'Etika dan Kepemimpinan', 'Teknik Informatika', 3, 'Ganjil', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKI003202223', 'Etika dan Kepemimpinan', 'Teknik Industri', 3, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('MKI004202223', 'Berpikir Kritis', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('MKI005202223', 'Kecerdasan Buatan', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('MKU001202373', 'Bahasa Indonesia dan Teknik Pelaporan', 'Sistem Informasi', 2, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKU002202373', 'Fenomenologi Agama', 'Sistem Informasi', 2, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKU003202373', 'Pendidikan Agama Kristen', 'Sistem Informasi', 2, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKU004202373', 'Kewarganegaraan', 'Sistem Informasi', 2, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKU005202373', 'Pancasila', 'Sistem Informasi', 2, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('MKU017202242', 'Pancasila', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MKU017202564', 'Pancasila', 'Desain Komunikasi Visual', 2, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('MKU024202242', 'Kewarganegaraan', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MKU024202564', 'Kewarganegaraan', 'Desain Komunikasi Visual', 2, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('MKU039202242', 'Bahasa Indonesia', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MKU039202564', 'Bahasa Indonesia', 'Desain Komunikasi Visual', 2, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('MKU060202242', 'Fenomenologi Agama', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MKU060202564', 'Fenomenologi Agama', 'Desain Komunikasi Visual', 2, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('MKU061202242', 'Etika', 'Sastra Jepang', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MKU061202564', 'Etika', 'Desain Komunikasi Visual', 2, '2', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('MKU062202242', 'Pendidikan Agama Kristen', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('MKU062202564', 'Pendidikan Agama Kristen', 'Desain Komunikasi Visual', 2, '1', 'Wajib', '2026-01-13 14:21:00', '2026-01-13 14:21:00'),
('MS-410', 'Thesis', 'Manajemen', 6, '8', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS101', 'Christian Education', 'Manajemen', 2, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS102', 'Civic Education', 'Manajemen', 2, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS103', 'Pancasila Philosophy', 'Manajemen', 2, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS104', 'English for Business', 'Manajemen', 2, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS105', 'Interpersonal and Leadership Skills', 'Manajemen', 3, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS106', 'Tax and Law', 'Manajemen', 3, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS107', 'Indonesian Language for Academic Writing', 'Manajemen', 2, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS108', 'Business Economics I', 'Manajemen', 3, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS109', 'Critical and Reflective Thinking', 'Manajemen', 3, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS110', 'Business Statistics I and Practice', 'Manajemen', 3, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS111', 'Management', 'Manajemen', 3, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS112', 'Cost Accounting', 'Manajemen', 3, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS113', 'Business Basic', 'Manajemen', 3, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS114', 'Quantitative Analysis for Business', 'Manajemen', 3, '2', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS115', 'Accounting for Business and Practice', 'Manajemen', 3, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS117', 'Religion Phenomenology', 'Manajemen', 2, '1', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS201', 'Business Economics II', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS202', 'Business Communication and Negotiation', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS203', 'Marketing Management', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS204', 'Marketing Mix Management', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS205', 'Operations Management', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS206', 'Advanced Operations Management', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS207', 'Manajemen Keuangan', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00'),
('MS207b', 'Financial Management', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS207h', 'Manajemen Keuangan', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('MS208', 'Advanced Financial Management', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS209', 'Organizational Behaviour', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS210', 'Human Resources Management', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS211', 'Managerial Accounting', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS212', 'Indonesian Economics In Global Context', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS213', 'Business Statistics II and Practice', 'Manajemen', 3, '3', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS214', 'Entrepreneurship and Innovation', 'Manajemen', 3, '4', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS301', 'Business Planning', 'Manajemen', 3, '5', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS302', 'Change Management', 'Manajemen', 3, '6', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS303', 'Business Research Method and Practice', 'Manajemen', 3, '5', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS304', 'Business Budgeting', 'Manajemen', 3, '6', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS305', 'International Business', 'Manajemen', 3, '5', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS306', 'Business Development', 'Manajemen', 3, '6', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS307', 'Strategic Planning and Risk Analysis', 'Manajemen', 3, '5', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS308', 'E-Business', 'Manajemen', 3, '6', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS309', 'Market Research', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS310', 'Capital Market and Practice', 'Manajemen', 3, '6', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS311', 'Consumer Behavior', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS312', 'Brand Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS313', 'Marketing Communication', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS314', 'Customer Relations Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS315', 'Investment Analysis and Portfolio Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS316', 'Financial Report Analysis', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS317', 'International Finance Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS318', 'Business Valuation', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS319', 'Behaviour Finance', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS320', 'International Human Resource Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS321', 'Organizational Design', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS322', 'Human Resource Development', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS323', 'Organizational Psychology', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS324', 'Project Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS325', 'Organizational Culture', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS326', 'Operation Scheduling', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS327', 'Facility Planning', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS328', 'Micro, Small & Medium Enterprises Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS329', 'Quality Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS330', 'Family Business Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS331', 'Supply Chain Management', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS333', 'Ultimate Entrepreneurial Challenge', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS335', 'Entrepreneurial Professionalism', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS337', 'Integrated Business Experience', 'Manajemen', 3, 'Ganjil', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS401', 'Financial Planning', 'Manajemen', 3, '7', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS403', 'Business Architecture and Governance', 'Manajemen', 3, '7', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS405', 'Service Management', 'Manajemen', 3, '7', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS407', 'Business Ethics', 'Manajemen', 2, '7', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('MS409', 'Major Seminar', 'Manajemen', 3, '7', 'Wajib', '2026-01-13 18:10:00', '2026-01-13 18:10:00'),
('PM-001', 'Pancasila', 'Psikologi', 2, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-002', 'Religion Phenomenology', 'Psikologi', 2, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-003', 'Christian Education', 'Psikologi', 2, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-004', 'Civics', 'Psikologi', 2, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-005', 'Scientific Writing', 'Psikologi', 2, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-006', 'Ethics', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-111', 'The History of Psychology', 'Psikologi', 2, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-112', 'Personality Psychology', 'Psikologi', 4, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-113', 'Introduction Psychology', 'Psikologi', 4, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-114', 'Psychology of Individual and Environmental', 'Psikologi', 4, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-115', 'Childhood Lifespan Development', 'Psikologi', 3, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-116', 'Basic Theories in Psychological Measurement', 'Psikologi', 2, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-117', 'Adolescence and Adulthood Lifespan Development', 'Psikologi', 4, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-118', 'Experimental Psychology', 'Psikologi', 2, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-119', 'Human and Logical Philosophy', 'Psikologi', 3, '1', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-120', 'Code of Conduct in Psychology', 'Psikologi', 3, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-122', 'Statistical in Psychological Research', 'Psikologi', 3, '2', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-211', 'Application of Psychology to Industrial and Organizational Field', 'Psikologi', 3, '3', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-212', 'Quantitative Methods', 'Psikologi', 3, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-213', 'Application of Psychology to Educational Field', 'Psikologi', 3, '3', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-214', 'Qualitative Method', 'Psikologi', 2, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-215', 'Basic Concept in Positive Psychology', 'Psikologi', 3, '3', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-216', 'Counselling Psychology', 'Psikologi', 4, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-217', 'Basic Principles of Observation and Interview', 'Psikologi', 4, '3', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-218', 'Application of Positive Psychology in Community', 'Psikologi', 4, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-219', 'Psychological Assessment Instruments', 'Psikologi', 5, '3', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-220', 'Psychodynamics', 'Psikologi', 2, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-221', 'Construction of Psychological Test', 'Psikologi', 3, '3', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-222', 'Mental Health', 'Psikologi', 3, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-224', 'Training and Psychoeducation', 'Psikologi', 4, '4', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-311', 'Psychology of Communication', 'Psikologi', 2, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-312', 'Learning Strategy in Educational Psychology', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-313', 'Family Psychology', 'Psikologi', 3, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-314', 'Enrichment of Child Developmental Psychology', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-315', 'Observation and Interview in Specific Area', 'Psikologi', 4, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-316', 'Basics Coaching in Industrial Field', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-317', 'Personality Description', 'Psikologi', 5, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-318', 'Organizational Development', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-319', 'Biopsychology', 'Psikologi', 3, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-320', 'Psychology of Entrepreneurship', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-321', 'Scientific Reading', 'Psikologi', 2, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-322', 'Writing Research Proposal', 'Psikologi', 3, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-323', 'Daily and Formal Conversation', 'Psikologi', 2, '5', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-324', 'Self and Career Development', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-326', 'Psychological First Aid', 'Psikologi', 2, '6', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-411', 'Writing Thesis Proposal', 'Psikologi', 2, '7', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-412', 'Undergraduate Thesis', 'Psikologi', 4, '8', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-413', 'Assessment and Psychological Intervention for Early Childhood Education', 'Psikologi', 5, 'Ganjil', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-415', 'Observation and Interview in Psychology of Personnel', 'Psikologi', 5, 'Ganjil', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-417', 'Assistant Psychologist Scope of Practice', 'Psikologi', 5, 'Ganjil', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-419', 'Assessment and Psychological Intervention in School Settings', 'Psikologi', 5, 'Ganjil', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PM-421', 'Training and Psychoeducation in Industrial Field and Community', 'Psikologi', 5, 'Ganjil', 'Wajib', '2026-01-13 18:00:00', '2026-01-13 18:00:00'),
('PR417h', 'Psikologi Lintas Budaya', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SB203', 'Dasar Filsafat', 'Sastra Inggris', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SB301', 'History of Modern Thought', 'Sastra Inggris', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SH101h', 'Pengantar Ilmu Hukum', 'Ilmu Hukum', 4, '1', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH102h', 'Pengantar Hukum Indonesia', 'Ilmu Hukum', 4, '1', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH103h', 'Logika', 'Ilmu Hukum', 2, '1', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH104h', 'Bahasa Inggris Hukum', 'Ilmu Hukum', 2, '1', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH201', 'Lukis I', 'Seni Rupa Murni', 4, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH201h', 'Hukum dan Hak Asasi Manusia', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH202', 'Lukis II', 'Seni Rupa Murni', 4, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH202h', 'Ilmu Negara', 'Ilmu Hukum', 2, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH203', 'Gambar Anatomi', 'Seni Rupa Murni', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH203h', 'Hukum Perdata', 'Ilmu Hukum', 3, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH204', 'Seni Rupa Tradisi Nusantara', 'Seni Rupa Murni', 2, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH204h', 'Hukum Pidana', 'Ilmu Hukum', 3, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH205', 'Ilustrasi', 'Seni Rupa Murni', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH206', 'Gambar Model', 'Seni Rupa Murni', 3, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH206h', 'Hukum Internasional', 'Ilmu Hukum', 3, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH207', 'Estetika', 'Seni Rupa Murni', 2, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH208', 'Filsafat Seni', 'Seni Rupa Murni', 2, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH209', 'Sejarah Seni Rupa Asia', 'Seni Rupa Murni', 2, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH210', 'Bahasa Rupa', 'Seni Rupa Murni', 2, '4', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH211', 'Pengetahuan Alat dan Bahan', 'Seni Rupa Murni', 3, '3', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH212', 'Kaligrafi China', 'Seni Rupa Murni', 3, '4', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH301', 'Lukis III', 'Seni Rupa Murni', 4, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH301h', 'Hukum Tata Negara', 'Ilmu Hukum', 3, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH302', 'Lukis IV/Jalur TA', 'Seni Rupa Murni', 4, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH302h', 'Hukum Administrasi Negara', 'Ilmu Hukum', 3, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH303', 'Lukis Model', 'Seni Rupa Murni', 3, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH303h', 'Hukum Acara Pidana', 'Ilmu Hukum', 3, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH304', 'Teori Seni Rupa I/Jalur Skripsi', 'Seni Rupa Murni', 4, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH304h', 'Hukum Perikatan', 'Ilmu Hukum', 3, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH305', 'Seni Rupa Fantastik', 'Seni Rupa Murni', 3, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH305h', 'Hukum Dagang', 'Ilmu Hukum', 2, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH306', 'Eksplorasi Seni Rupa', 'Seni Rupa Murni', 3, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH306h', 'Hukum Agraria', 'Ilmu Hukum', 3, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH307', 'Metode Penelitian dan Seminar', 'Seni Rupa Murni', 2, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH308', 'Sejarah Seni Rupa Indonesia', 'Seni Rupa Murni', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH309', 'Kritik Seni I', 'Seni Rupa Murni', 2, '5', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH310', 'Seni Lukis Digital', 'Seni Rupa Murni', 2, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH312', 'Kritik Seni Rupa', 'Seni Rupa Murni', 3, '6', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH313', 'Lukis Keramik', 'Seni Rupa Murni', 3, '5', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH314', 'Lukis Patung', 'Seni Rupa Murni', 3, '6', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH315', 'Chinese Painting', 'Seni Rupa Murni', 3, '5', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH316', 'Lukis Cat Air', 'Seni Rupa Murni', 3, '6', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH401', 'Lukis V/Jalur TA', 'Seni Rupa Murni', 4, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH401h', 'Hukum Acara Perdata', 'Ilmu Hukum', 3, '4', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH402', 'Tugas Akhir', 'Seni Rupa Murni', 6, '8', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH403', 'Teori Seni Rupa II/Jalur Skripsi', 'Seni Rupa Murni', 4, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH403h', 'Hukum Bisnis Internasional', 'Ilmu Hukum', 2, '4', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH404h', 'Hukum Kepailitan', 'Ilmu Hukum', 3, '6', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH405', 'Topik Seni Rupa', 'Seni Rupa Murni', 2, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH405h', 'Hukum Ketenagakerjaan', 'Ilmu Hukum', 2, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH406h', 'Hukum Lingkungan dan Sumber Daya Alam', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH407', 'Kritik Seni II', 'Seni Rupa Murni', 2, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH407h', 'Hukum Perizinan', 'Ilmu Hukum', 2, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH408h', 'Hukum Pajak', 'Ilmu Hukum', 2, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH409', 'Seni Rupa Kontemporer', 'Seni Rupa Murni', 2, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH409h', 'Hukum Persaingan Usaha', 'Ilmu Hukum', 2, '4', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH410h', 'Hukum Waris', 'Ilmu Hukum', 3, '4', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH411', 'Manajemen Seni Rupa', 'Seni Rupa Murni', 2, '7', 'Wajib', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH413', 'Public Art', 'Seni Rupa Murni', 3, '7', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH415', 'Lukis Wastra (Batik & Sutra)', 'Seni Rupa Murni', 3, '7', 'Pilihan', '2026-01-13 15:00:00', '2026-01-13 15:00:00'),
('SH491h', 'Hukum Adat', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH501h', 'Hukum Acara PTUN', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH502h', 'Kemahiran Hukum Pidana', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH503h', 'Kemahiran Hukum Perdata', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH504h', 'Kemahiran Penyusunan Kontrak Bisnis', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH505h', 'Kemahiran Penyusunan Undang-undang', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH506h', 'Hukum Perusahaan', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH509h', 'Aspek Hukum Pidana dalam Bisnis', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH510h', 'Hukum Telematika', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH511h', 'Hukum Perlindungan Konsumen', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH512h', 'Hukum Perbankan', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH513h', 'Metode Penelitian dan Penulisan Hukum', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH514h', 'Dasar-dasar Kemahiran Hukum', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH601h', 'Hukum Kondominium dan Real Estate', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH602h', 'Hukum Hak Kekayaan Intelektual', 'Ilmu Hukum', 2, '2', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH603h', 'Hukum Ekonomi Internasional', 'Ilmu Hukum', 2, '4', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH605h', 'Penalaran Hukum', 'Ilmu Hukum', 2, '6', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH607h', 'Hukum Pasar Modal', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH608h', 'Hukum Waralaba', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH609h', 'Hukum Bangunan Gedung', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH610h', 'Hukum Asuransi', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH611h', 'Hukum Pemerintahan Daerah', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH612h', 'Hukum Investasi', 'Ilmu Hukum', 2, '6', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH613h', 'Hukum Perjanjian Kredit dan Jaminan', 'Ilmu Hukum', 2, '6', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH614h', 'Entertainment law', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH615h', 'Hukum Kesehatan', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH616h', 'Hukum Ekonomi Syariah', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH621h', 'Teknik Penyelesaian Perkara Perdata', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH622h', 'Teknik Penyelesaian Perkara Pidana', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH623h', 'Teknik Penyelesaian Perkara Tata Usaha Negara', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH624h', 'Penyusunan Opini Hukum', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH625h', 'Teknik Penyelesaian Sengketa Non Litigasi', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH701h', 'Praktik Persidangan', 'Ilmu Hukum', 2, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH702h', 'Alternatif Penyelesaian Sengketa', 'Ilmu Hukum', 2, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH703h', 'Etika dan Tanggung Jawab Profesi', 'Ilmu Hukum', 2, '3', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH704h', 'Filsafat Hukum', 'Ilmu Hukum', 2, '5', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH705h', 'Magang', 'Ilmu Hukum', 2, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH706h', 'Perbandingan Hukum Kontrak', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH707h', 'Mediasi', 'Ilmu Hukum', 2, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH708h', 'Hukum Lembaga Pembiayaan', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH709h', 'Hukum Pengadaan Barang dan Jasa', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH710h', 'Scientific Crime investigation', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH711h', 'Hukum E-Commerce', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH712h', 'Hukum Agribisnis', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH721h', 'Teknik Perancangan Kontrak', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH722h', 'Teknik Penyelesaian Perkara Niaga', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH723h', 'Audit Hukum / Legal Due Dilligence', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH724h', 'Teknik Penyelesaian Sengketa Konsumen', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH725h', 'Legislative Drafting', 'Ilmu Hukum', 4, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH783h', 'Pertukaran Pelajar', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH784h', 'Magang / Praktik Kerja', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH785h', 'Asistensi Mengajar Di Satuan Pendidikan', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH786h', 'Penelitian / Riset', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH787h', 'Proyek Kemanusiaan', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH788h', 'Kegiatan Wirausaha', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH789h', 'Studi / Proyek Independen', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH790h', 'Membangun Desa / Kuliah Kerja Nyata Tematik', 'Ilmu Hukum', 20, '7', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH801h', 'Sosiologi Hukum', 'Ilmu Hukum', 3, '6', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SH802h', 'Manajemen Korporasi', 'Ilmu Hukum', 2, 'Ganjil', 'Wajib', '2026-01-13 18:30:00', '2026-01-13 18:30:00'),
('SH803h', 'Penulisan Hukum', 'Ilmu Hukum', 4, '8', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('SIB001202373', 'Sistem Informasi', 'Sistem Informasi', 3, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB002202373', 'Pemrograman Dasar', 'Sistem Informasi', 4, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB003202373', 'Statistika Deskriptif dan Probabilitas', 'Sistem Informasi', 3, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00');
INSERT INTO `mata_kuliah` (`kode_mk`, `nama_mk`, `jurusan`, `sks`, `semester`, `sifat`, `created_at`, `updated_at`) VALUES
('SIB004202373', 'Pengenalan Enterprise Architecture', 'Sistem Informasi', 3, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB005202373', 'E-Bisnis Fundamental dan Manajemen E-Commerce', 'Sistem Informasi', 3, '1', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB006202373', 'Statistika Bisnis', 'Sistem Informasi', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB007202373', 'Proses Bisnis dan Fundamental ERP', 'Sistem Informasi', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB008202373', 'Algoritma dan Struktur Data', 'Sistem Informasi', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB009202373', 'Pemrograman Berorientasi Objek', 'Sistem Informasi', 4, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB010202373', 'Perancangan Basis Data', 'Sistem Informasi', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB011202373', 'Perancangan Sistem Informasi', 'Sistem Informasi', 3, '2', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB012202373', 'Visualisasi Data', 'Sistem Informasi', 3, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB013202373', 'Sistem Informasi Manajemen Rantai Pasokan', 'Sistem Informasi', 3, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB014202373', 'Pemasaran Online', 'Sistem Informasi', 3, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB015202373', 'Administrasi Basis Data', 'Sistem Informasi', 3, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB016202373', 'Dasar Desain Antarmuka Pengguna', 'Sistem Informasi', 2, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB017202373', 'Organisasi dan Manajemen Perusahaan Industri', 'Sistem Informasi', 3, '3', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB018202373', 'Pengenalan Data Science', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB019202373', 'Pengendalian dan Audit Teknologi Informasi', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB020202373', 'Technopreneurship', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB021202373', 'Pemodelan Sistem Informasi', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB022202373', 'Pemrograman Web', 'Sistem Informasi', 4, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB023202373', 'Sistem Informasi Manajemen Sumber Daya Manusia', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB024202373', 'Desain Pengalaman Pengguna', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB025202373', 'Kerja Praktek Kompetensi', 'Sistem Informasi', 3, '4', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB026202373', 'Pengenalan Data Engineering', 'Sistem Informasi', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB027202373', 'Bahasa Inggris untuk Bisnis', 'Sistem Informasi', 2, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB028202373', 'Sistem Operasi dan Jaringan Komputer', 'Sistem Informasi', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB029202373', 'Keamanan Sistem Informasi', 'Sistem Informasi', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB030202373', 'Manajemen Proyek Teknologi Informasi', 'Sistem Informasi', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB031202373', 'Manajemen Resiko Teknologi Informasi', 'Sistem Informasi', 3, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB032202373', 'Etika Profesional dan Pengembangan Diri', 'Sistem Informasi', 2, '5', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB033202373', 'Digital Marketing Analysis and Strategy', 'Sistem Informasi', 3, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB034202373', 'Bisnis Pemrograman Aplikasi', 'Sistem Informasi', 4, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB035202373', 'Kecerdasan Bisnis', 'Sistem Informasi', 3, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB036202373', 'Rekayasa Proses Bisnis', 'Sistem Informasi', 3, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB037202373', 'Teknologi Cerdas', 'Sistem Informasi', 3, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB038202373', 'Sistem Pendukung Keputusan', 'Sistem Informasi', 3, '6', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB039202373', 'Kapita Selekta', 'Sistem Informasi', 3, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB040202373', 'Pengembangan Aplikasi Enterprise', 'Sistem Informasi', 3, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB041202373', 'Strategi Penelitian', 'Sistem Informasi', 3, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB042202373', 'Sistem Manajemen Pengetahuan', 'Sistem Informasi', 3, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB043202373', 'Teknologi Penyimpanan Data', 'Sistem Informasi', 3, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB044202373', 'Keamanan Siber', 'Sistem Informasi', 3, '7', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SIB045202373', 'Tugas Akhir', 'Sistem Informasi', 3, '8', 'Wajib', '2026-01-13 17:00:00', '2026-01-13 17:00:00'),
('SK-102', 'Bahasa Inggris', 'Sastra China', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SK-104', 'Kebudayaan Indonesia', 'Sastra China', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ110202242', 'Nihongo Kiso Bunpo 1A', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ111202242', 'Nihongo Kiso Kaiwa 1A', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ112202242', 'Nihongo Kiso Enshu 1A', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ113202242', 'Goi/Kanji 1A', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ114202242', 'Nihongo Kiso Bunpo 1B', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ115202242', 'Nihongo Kiso Kaiwa 1B', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ116202242', 'Nihongo Kiso Enshu 1B', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ117202242', 'Goi/Kanji 1B', 'Sastra Jepang', 2, '1', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ118202242', 'Nihongo Kiso Bunpo 2A', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ119202242', 'Nihongo Kiso Kaiwa 2A', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ120202242', 'Nihongo Kiso Enshu 2A', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ121202242', 'Goi/Kanji 2A', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ122202242', 'Nihongo Kiso Bunpo 2B', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ123202242', 'Nihongo Kiso Kaiwa 2B', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ124202242', 'Nihongo Kiso Enshu 2B', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ125202242', 'Goi/Kanji 2B', 'Sastra Jepang', 2, '2', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ210202242', 'Nihongo Chukyu Bunpo 1', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ211202242', 'Nihongo Chukyu Kaiwa 1', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ212202242', 'Nihongo Chukyu Dokkai 1', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ213202242', 'JLPT Preparation Bunpo 1', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ214202242', 'JLPT Preparation Goi/Kanji 1', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ215202242', 'JLPT Preparation Dokkai 1', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ216202242', 'JLPT Preparation Chokai', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ217202242', 'Hikaku Shokubunkaron', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ218202242', 'Japanese Entertainment', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ219202242', 'Leadership', 'Sastra Jepang', 2, '3', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ220202242', 'Nihongo Chukyu Bunpo 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ221202242', 'Nihongo Chukyu Kaiwa 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ222202242', 'Nihongo Chukyu Dokkai 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ223202242', 'JLPT Preparation Bunpo 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ224202242', 'JLPT Preparation Goi/Kanji 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ225202242', 'JLPT Preparation Dokkai 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ226202242', 'JLPT Preparation Chokai 2', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ227202242', 'Hikaku Folklore', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ228202242', 'Nihonshi', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ229202242', 'Shakaigengogaku', 'Sastra Jepang', 2, '4', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ310202242', 'Business Nihongo 1', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ311202242', 'Academic Nihongo Sakubun 1', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ312202242', 'JLPT Preparation Bunpo 3', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ313202242', 'JLPT Preparation Goi/Kanji 3', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ314202242', 'JLPT Preparation Dokkai 3', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ315202242', 'JLPT Preparation Chokai 3', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ316202242', 'Hikaku Bunkaron 1', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ317202242', 'Eiga Bunseki 1', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ318202242', 'Nihongo Bunseki 1', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ319202242', 'Joho to Nihongo', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ320202242', 'Metode Penelitian Budaya Populer', 'Sastra Jepang', 2, '5', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ321202242', 'Business Nihongo 2', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ322202242', 'Academic Nihongo Sakubun 2', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ323202242', 'JLPT Preparation Bunpo 4', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ324202242', 'JLPT Preparation Goi/Kanji 4', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ325202242', 'JLPT Preparation Dokkai 4', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ326202242', 'JLPT Preparation Chokai 4', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ327202242', 'Hikaku Bunkaron 2', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ328202242', 'Eiga Bunseki 2', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ329202242', 'Nihon no Bunka to Shakai', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ330202242', 'Nihongo Bunseki 2', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ331202242', 'Nihongo no Sekai', 'Sastra Jepang', 2, '6', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ410202242', 'Business Nihongo 3', 'Sastra Jepang', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ411202242', 'Tsuyaku no Nihongo', 'Sastra Jepang', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ412202242', 'Honyaku', 'Sastra Jepang', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ413202242', 'Academic Nihongo Kaiwa', 'Sastra Jepang', 2, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ414202242', 'Seminar Budaya', 'Sastra Jepang', 4, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ415202242', 'Seminar Linguistik', 'Sastra Jepang', 4, '7', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ416202242', 'Skripsi Budaya', 'Sastra Jepang', 6, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('SSJ417202242', 'Skripsi Linguistik', 'Sastra Jepang', 6, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('TA220', 'Tugas Akhir (Sastra)', 'Sastra Inggris', 4, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('TA221', 'Tugas Akhir (Linguistik)', 'Sastra Inggris', 4, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('TA420', 'Skripsi (Linguistik)', 'Sastra Inggris', 6, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('TA440', 'Skripsi (Sastra)', 'Sastra Inggris', 6, '8', 'Wajib', '2026-01-13 14:50:00', '2026-01-13 14:50:00'),
('TK304h', 'Keterampilan profesional', 'Ilmu Hukum', 3, 'Ganjil', 'Wajib', '2026-01-13 18:40:00', '2026-01-13 18:40:00'),
('TK401', 'Pengantar Kecerdasan Buatan', 'Teknik Industri', 2, 'Ganjil', 'Wajib', '2026-01-13 15:50:00', '2026-01-13 15:50:00');

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
(22, '2026_01_08_015500_add_admin_level_to_admin_table', 17),
(23, '2026_01_14_070148_add_sifat_to_mata_kuliah_table', 18);

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
(1, '2472021', 'MK001P', 80, 80, 85, 90, 80, 80, 80, 75, 89, 80, 85, 80, 80, 85, 70, 80, 80.03, 'B', '2026-01-02 22:48:46', '2026-01-12 03:19:14'),
(2, '2472021', 'MK001T', 90, 90, 90, 98, 70, 60, 95, 75, 80, 87, 89, 94, 85, 70, 80, 85, 82.49, 'B', '2026-01-02 22:51:55', '2026-01-12 03:35:04');

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
(19, 'L8002', '0072206', 'MK006P', 'A', 'Kamis', '15:30:00', '17:30:00', '2025/2026 - Ganjil', '2026-01-02 23:20:40', '2026-01-09 22:08:18'),
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
(31, 'L8007', '0071101', 'MK103', 'A', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 11:20:27', '2026-01-07 12:12:52'),
(33, 'L8007', '0071102', 'MK101T', 'A', 'Rabu', '09:30:00', '11:10:00', '2025/2026 - Ganjil', '2026-01-07 11:45:50', '2026-01-07 11:45:50'),
(34, 'L8007', '0071102', 'MK101P', 'A', 'Rabu', '11:40:00', '13:40:00', '2025/2026 - Ganjil', '2026-01-07 11:45:50', '2026-01-09 22:08:18'),
(35, 'L8005', '0071103', 'MK106T', 'A', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 12:15:39', '2026-01-07 20:11:15'),
(36, 'L8005', '0071103', 'MK106P', 'A', 'Senin', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-07 12:15:39', '2026-01-09 22:08:18'),
(37, 'L8007', '0071101', 'MK103', 'B', 'Selasa', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-07 20:04:06', '2026-01-07 20:04:19'),
(38, 'L8008', '0071101', 'MK103', 'C', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 20:06:31', '2026-01-07 20:06:31'),
(39, 'L8005', '0071103', 'MK106T', 'B', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-07 21:28:37', '2026-01-07 21:34:05'),
(40, 'L8005', '0071103', 'MK106P', 'B', 'Rabu', '12:30:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-07 21:28:37', '2026-01-09 22:08:18'),
(41, 'L8007', '0071102', 'MK101T', 'B', 'Selasa', '08:00:00', '09:40:00', '2025/2026 - Ganjil', '2026-01-07 21:31:00', '2026-01-11 20:54:47'),
(42, 'L8007', '0071102', 'MK101P', 'B', 'Selasa', '10:10:00', '12:10:00', '2025/2026 - Ganjil', '2026-01-07 21:31:00', '2026-01-11 20:55:08'),
(43, 'L8010', '0071102', 'MK101T', 'C', 'Senin', '12:00:00', '13:40:00', '2025/2026 - Ganjil', '2026-01-07 21:32:30', '2026-01-07 21:32:48'),
(44, 'L8010', '0071102', 'MK101P', 'C', 'Senin', '14:10:00', '16:10:00', '2025/2026 - Ganjil', '2026-01-07 21:32:30', '2026-01-09 22:08:18'),
(45, 'H04B02', '0073301', 'MK204T', 'A', 'Senin', '08:00:00', '10:30:00', '2025/2026 - Ganjil', '2026-01-08 00:25:59', '2026-01-09 22:30:12'),
(46, 'H04B02', '0073301', 'MK204P', 'A', 'Senin', '11:00:00', '13:00:00', '2025/2026 - Ganjil', '2026-01-08 00:25:59', '2026-01-09 22:30:32'),
(49, 'H04B01', '0073301', 'MK204T', 'C', 'Kamis', '09:00:00', '11:30:00', '2025/2026 - Ganjil', '2026-01-09 22:29:48', '2026-01-09 22:30:46'),
(50, 'H04B01', '0073301', 'MK204P', 'C', 'Kamis', '12:00:00', '14:00:00', '2025/2026 - Ganjil', '2026-01-09 22:29:48', '2026-01-09 22:30:52'),
(53, 'H04B01', '0073301', 'MK204T', 'B', 'Senin', '14:00:00', '16:30:00', '2025/2026 - Ganjil', '2026-01-09 22:33:23', '2026-01-09 22:33:23'),
(54, 'H04B01', '0073301', 'MK204P', 'B', 'Senin', '17:00:00', '19:00:00', '2025/2026 - Ganjil', '2026-01-09 22:33:23', '2026-01-09 22:33:23'),
(55, 'L8009', '0071104', 'MK104', 'A', 'Kamis', '08:00:00', '10:30:00', '2025/2026 - Ganjil', '2026-01-09 22:35:41', '2026-01-11 21:04:03'),
(56, 'L8009', '0071104', 'MK104', 'B', 'Kamis', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-09 22:40:25', '2026-01-11 21:04:14'),
(57, 'L8009', '0071104', 'MK104', 'C', 'Jumat', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-09 22:41:17', '2026-01-11 21:04:31'),
(58, 'L8002', '0071105', 'MK102', 'A', 'Rabu', '08:00:00', '09:40:00', '2025/2026 - Ganjil', '2026-01-09 22:50:32', '2026-01-09 22:50:32'),
(59, 'L8006', '0071105', 'MK102', 'B', 'Rabu', '10:00:00', '11:40:00', '2025/2026 - Ganjil', '2026-01-09 22:51:46', '2026-01-09 22:51:46'),
(60, 'L8009', '0071105', 'MK102', 'C', 'Rabu', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-09 22:52:48', '2026-01-09 22:52:48'),
(61, 'L8001', '0071106', 'MK105', 'A', 'Senin', '08:00:00', '10:30:00', '2025/2026 - Ganjil', '2026-01-09 22:58:45', '2026-01-09 22:58:45'),
(62, 'L8001', '0071106', 'MK105', 'B', 'Senin', '12:30:00', '15:00:00', '2025/2026 - Ganjil', '2026-01-09 22:59:11', '2026-01-09 22:59:11'),
(63, 'L8001', '0071106', 'MK105', 'C', 'Senin', '16:30:00', '19:00:00', '2025/2026 - Ganjil', '2026-01-09 22:59:52', '2026-01-09 22:59:52'),
(64, 'H04C01', '0073303', 'MK203', 'A', 'Selasa', '08:00:00', '10:30:00', '2025/2026 - Ganjil', '2026-01-09 23:32:11', '2026-01-09 23:32:11'),
(65, 'H04C01', '0073303', 'MK203', 'B', 'Selasa', '12:00:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-09 23:38:18', '2026-01-09 23:38:18'),
(66, 'H04C01', '0073303', 'MK203', 'C', 'Selasa', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-11 08:17:12', '2026-01-11 08:17:12'),
(67, 'H03A01', '0073306', 'MK201', 'A', 'Senin', '08:00:00', '11:20:00', '2025/2026 - Ganjil', '2026-01-11 08:18:15', '2026-01-11 08:18:15'),
(68, 'H03A01', '0073306', 'MK201', 'B', 'Senin', '13:00:00', '16:20:00', '2025/2026 - Ganjil', '2026-01-11 08:18:58', '2026-01-11 08:18:58'),
(69, 'H04B01', '0073308', 'MK202', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-11 08:19:36', '2026-01-11 08:19:36'),
(70, 'H04B01', '0073308', 'MK202', 'B', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-11 20:32:46', '2026-01-11 20:32:46'),
(71, 'H04B01', '0073308', 'MK202', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-11 20:35:53', '2026-01-11 20:35:53'),
(72, 'L8001', '0072201', 'MK001T', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(73, 'L8001', '0072201', 'MK001T', 'B', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(74, 'L8001', '0072201', 'MK001T', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(75, 'L8002', '0072201', 'MK001P', 'A', 'Senin', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(76, 'L8002', '0072201', 'MK001P', 'B', 'Selasa', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(77, 'L8002', '0072201', 'MK001P', 'C', 'Selasa', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(78, 'L8003', '0072202', 'MK002T', 'A', 'Selasa', '10:00:00', '11:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(79, 'L8003', '0072202', 'MK002T', 'B', 'Selasa', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(80, 'L8003', '0072202', 'MK002T', 'C', 'Selasa', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(81, 'L8004', '0072202', 'MK002P', 'A', 'Rabu', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(82, 'L8004', '0072202', 'MK002P', 'B', 'Rabu', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(83, 'L8004', '0072202', 'MK002P', 'C', 'Rabu', '10:00:00', '11:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(84, 'L8005', '0072203', 'MK003', 'A', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(85, 'L8005', '0072203', 'MK003', 'B', 'Rabu', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(86, 'L8005', '0072203', 'MK003', 'C', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(87, 'L8006', '0072204', 'MK004', 'A', 'Kamis', '09:30:00', '11:10:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(88, 'L8006', '0072204', 'MK004', 'B', 'Kamis', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(89, 'L8006', '0072204', 'MK004', 'C', 'Kamis', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(90, 'L8007', '0072205', 'MK005', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(91, 'L8007', '0072205', 'MK005', 'B', 'Jumat', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(92, 'L8007', '0072205', 'MK005', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(93, 'L8008', '0072206', 'MK006T', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(94, 'L8008', '0072206', 'MK006T', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(95, 'L8008', '0072206', 'MK006T', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(96, 'L8009', '0072206', 'MK006P', 'A', 'Senin', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(97, 'L8009', '0072206', 'MK006P', 'B', 'Selasa', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(98, 'L8009', '0072206', 'MK006P', 'C', 'Selasa', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(99, 'L8010', '0072207', 'MK007', 'A', 'Selasa', '10:00:00', '11:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(100, 'L8010', '0072207', 'MK007', 'B', 'Selasa', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(101, 'L8010', '0072207', 'MK007', 'C', 'Selasa', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(102, 'L8001', '0071101', 'MK101T', 'A', 'Rabu', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(103, 'L8001', '0071101', 'MK101T', 'B', 'Rabu', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(104, 'L8001', '0071101', 'MK101T', 'C', 'Rabu', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(105, 'L8002', '0071101', 'MK101P', 'A', 'Rabu', '13:00:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(106, 'L8002', '0071101', 'MK101P', 'B', 'Rabu', '14:30:00', '16:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(107, 'L8002', '0071101', 'MK101P', 'C', 'Rabu', '16:00:00', '17:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(108, 'L8003', '0071102', 'MK102', 'A', 'Kamis', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(109, 'L8003', '0071102', 'MK102', 'B', 'Kamis', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(110, 'L8003', '0071102', 'MK102', 'C', 'Kamis', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(111, 'L8004', '0071103', 'MK103', 'A', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(112, 'L8004', '0071103', 'MK103', 'B', 'Kamis', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(113, 'L8004', '0071103', 'MK103', 'C', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(114, 'L8005', '0071104', 'MK104', 'A', 'Jumat', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(115, 'L8005', '0071104', 'MK104', 'B', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(116, 'L8005', '0071104', 'MK104', 'C', 'Jumat', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(117, 'L8006', '0071105', 'MK105', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(118, 'L8006', '0071105', 'MK105', 'B', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(119, 'L8006', '0071105', 'MK105', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(120, 'L8007', '0071106', 'MK106T', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(121, 'L8007', '0071106', 'MK106T', 'B', 'Selasa', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(122, 'L8007', '0071106', 'MK106T', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(123, 'L8008', '0071106', 'MK106P', 'A', 'Selasa', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(124, 'L8008', '0071106', 'MK106P', 'B', 'Rabu', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(125, 'L8008', '0071106', 'MK106P', 'C', 'Rabu', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:40:48', '2026-01-17 13:40:48'),
(171, 'L8011', '0072201', 'MK001T', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(172, 'L8011', '0072201', 'MK001T', 'B', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(173, 'L8011', '0072201', 'MK001T', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(174, 'L8012', '0072201', 'MK001P', 'A', 'Senin', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(175, 'L8012', '0072201', 'MK001P', 'B', 'Selasa', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(176, 'L8012', '0072201', 'MK001P', 'C', 'Selasa', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(177, 'L8013', '0072202', 'MK002T', 'A', 'Selasa', '10:00:00', '11:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(178, 'L8013', '0072202', 'MK002T', 'B', 'Selasa', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(179, 'L8013', '0072202', 'MK002T', 'C', 'Selasa', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(180, 'L8014', '0072202', 'MK002P', 'A', 'Rabu', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(181, 'L8014', '0072202', 'MK002P', 'B', 'Rabu', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(182, 'L8014', '0072202', 'MK002P', 'C', 'Rabu', '10:00:00', '11:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(183, 'L8015', '0072203', 'MK003', 'A', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(184, 'L8015', '0072203', 'MK003', 'B', 'Rabu', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(185, 'L8015', '0072203', 'MK003', 'C', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(186, 'L8026', '0072204', 'MK004', 'A', 'Kamis', '09:30:00', '11:10:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(187, 'L8026', '0072204', 'MK004', 'B', 'Kamis', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(188, 'L8026', '0072204', 'MK004', 'C', 'Kamis', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(189, 'L8021', '0072205', 'MK005', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(190, 'L8021', '0072205', 'MK005', 'B', 'Jumat', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(191, 'L8021', '0072205', 'MK005', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(192, 'L8022', '0072206', 'MK006T', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(193, 'L8022', '0072206', 'MK006T', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(194, 'L8022', '0072206', 'MK006T', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(195, 'L8023', '0072206', 'MK006P', 'A', 'Senin', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(196, 'L8023', '0072206', 'MK006P', 'B', 'Selasa', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(197, 'L8023', '0072206', 'MK006P', 'C', 'Selasa', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(198, 'L8024', '0072207', 'MK007', 'A', 'Selasa', '10:00:00', '11:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(199, 'L8024', '0072207', 'MK007', 'B', 'Selasa', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(200, 'L8024', '0072207', 'MK007', 'C', 'Selasa', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(201, 'L8016', '0071101', 'MK101T', 'A', 'Rabu', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(202, 'L8016', '0071101', 'MK101T', 'B', 'Rabu', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(203, 'L8016', '0071101', 'MK101T', 'C', 'Rabu', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(204, 'L8017', '0071101', 'MK101P', 'A', 'Rabu', '13:00:00', '14:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(205, 'L8017', '0071101', 'MK101P', 'B', 'Rabu', '14:30:00', '16:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(206, 'L8017', '0071101', 'MK101P', 'C', 'Rabu', '16:00:00', '17:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(207, 'L8018', '0071102', 'MK102', 'A', 'Kamis', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(208, 'L8018', '0071102', 'MK102', 'B', 'Kamis', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(209, 'L8018', '0071102', 'MK102', 'C', 'Kamis', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(210, 'L8025', '0071103', 'MK103', 'A', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(211, 'L8025', '0071103', 'MK103', 'B', 'Kamis', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(212, 'L8025', '0071103', 'MK103', 'C', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(213, 'L8019', '0071104', 'MK104', 'A', 'Jumat', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(214, 'L8019', '0071104', 'MK104', 'B', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(215, 'L8019', '0071104', 'MK104', 'C', 'Jumat', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(216, 'L8020', '0071105', 'MK105', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(217, 'L8020', '0071105', 'MK105', 'B', 'Senin', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(218, 'L8020', '0071105', 'MK105', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(219, 'L8001', '0071106', 'MK106T', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(220, 'L8001', '0071106', 'MK106T', 'B', 'Selasa', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(221, 'L8001', '0071106', 'MK106T', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(222, 'L8002', '0071106', 'MK106P', 'A', 'Selasa', '15:30:00', '17:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(223, 'L8002', '0071106', 'MK106P', 'B', 'Rabu', '07:00:00', '08:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(224, 'L8002', '0071106', 'MK106P', 'C', 'Rabu', '08:30:00', '10:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(225, 'H04A02', '0073301', 'MK201', 'A', 'Senin', '07:00:00', '10:20:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(226, 'H04A02', '0073301', 'MK201', 'B', 'Senin', '10:30:00', '13:50:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(227, 'H04A02', '0073301', 'MK201', 'C', 'Senin', '14:00:00', '17:20:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(228, 'H04A03', '0073302', 'MK202', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(229, 'H04A03', '0073302', 'MK202', 'B', 'Selasa', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(230, 'H04A03', '0073302', 'MK202', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(231, 'H04A04', '0073303', 'MK203', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(232, 'H04A04', '0073303', 'MK203', 'B', 'Rabu', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(233, 'H04A04', '0073303', 'MK203', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(234, 'H04B03', '0073304', 'MK204T', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(235, 'H04B03', '0073304', 'MK204T', 'B', 'Kamis', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(236, 'H04B03', '0073304', 'MK204T', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(237, 'H04B04', '0073304', 'MK204P', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(238, 'H04B04', '0073304', 'MK204P', 'B', 'Jumat', '09:30:00', '12:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(239, 'H04B04', '0073304', 'MK204P', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(240, 'L8028', '0072301', 'EED704202222', 'A', 'Senin', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(241, 'L8028', '0072301', 'EED704202222', 'B', 'Senin', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(242, 'L8028', '0072301', 'EED704202222', 'C', 'Senin', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(243, 'L8029', '0072302', 'EED705202222', 'A', 'Senin', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(244, 'L8029', '0072302', 'EED705202222', 'B', 'Senin', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(245, 'L8029', '0072302', 'EED705202222', 'C', 'Senin', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(246, 'L8030', '0072303', 'EED803202222', 'A', 'Selasa', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(247, 'L8030', '0072303', 'EED803202222', 'B', 'Selasa', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(248, 'L8030', '0072303', 'EED803202222', 'C', 'Selasa', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(249, 'L8028', '0072304', 'EED901202222', 'A', 'Selasa', '13:00:00', '13:50:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(250, 'L8028', '0072304', 'EED901202222', 'B', 'Selasa', '14:00:00', '14:50:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(251, 'L8028', '0072304', 'EED901202222', 'C', 'Selasa', '15:00:00', '15:50:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(252, 'L8029', '0072305', 'EED902202222', 'A', 'Rabu', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(253, 'L8029', '0072305', 'EED902202222', 'B', 'Rabu', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(254, 'L8029', '0072305', 'EED902202222', 'C', 'Rabu', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(255, 'L8030', '0072306', 'EED903202222', 'A', 'Rabu', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(256, 'L8030', '0072306', 'EED903202222', 'B', 'Rabu', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(257, 'L8030', '0072306', 'EED903202222', 'C', 'Rabu', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(258, 'L8028', '0072307', 'EED904202222', 'A', 'Kamis', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(259, 'L8028', '0072307', 'EED904202222', 'B', 'Kamis', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(260, 'L8028', '0072307', 'EED904202222', 'C', 'Kamis', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(261, 'L8029', '0072308', 'EED905202222', 'A', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(262, 'L8029', '0072308', 'EED905202222', 'B', 'Kamis', '15:30:00', '18:00:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(263, 'L8029', '0072308', 'EED905202222', 'C', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(264, 'L8030', '0072309', 'CE607', 'A', 'Jumat', '09:30:00', '11:10:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(265, 'L8030', '0072309', 'CE607', 'B', 'Jumat', '11:30:00', '13:10:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(266, 'L8030', '0072309', 'CE607', 'C', 'Jumat', '13:30:00', '15:10:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(267, 'L8003', '0072310', 'BID01', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(268, 'L8003', '0072310', 'BID01', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(269, 'L8003', '0072310', 'BID01', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(270, 'L8004', '0072311', 'BIE03', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(271, 'L8004', '0072311', 'BIE03', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(272, 'L8004', '0072311', 'BIE03', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(273, 'T8006', '0074101', 'IEC001202223', 'A', 'Senin', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(274, 'T8006', '0074101', 'IEC001202223', 'B', 'Senin', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(275, 'T8006', '0074101', 'IEC001202223', 'C', 'Senin', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(276, 'T8007', '0074102', 'IEC002202223', 'A', 'Senin', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(277, 'T8007', '0074102', 'IEC002202223', 'B', 'Senin', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(278, 'T8007', '0074102', 'IEC002202223', 'C', 'Senin', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(279, 'T8008', '0074103', 'IEC003202223', 'A', 'Selasa', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(280, 'T8008', '0074103', 'IEC003202223', 'B', 'Selasa', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(281, 'T8008', '0074103', 'IEC003202223', 'C', 'Selasa', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(282, 'T8009', '0074104', 'IEC004202223', 'A', 'Selasa', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(283, 'T8009', '0074104', 'IEC004202223', 'B', 'Selasa', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(284, 'T8009', '0074104', 'IEC004202223', 'C', 'Selasa', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(285, 'T8010', '0074105', 'IEC005202223', 'A', 'Rabu', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(286, 'T8010', '0074105', 'IEC005202223', 'B', 'Rabu', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(287, 'T8010', '0074105', 'IEC005202223', 'C', 'Rabu', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(288, 'T8006', '0074106', 'IEC006202223', 'A', 'Rabu', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(289, 'T8006', '0074106', 'IEC006202223', 'B', 'Rabu', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(290, 'T8006', '0074106', 'IEC006202223', 'C', 'Rabu', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(291, 'T8007', '0074107', 'IEC007202223', 'A', 'Kamis', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(292, 'T8007', '0074107', 'IEC007202223', 'B', 'Kamis', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(293, 'T8007', '0074107', 'IEC007202223', 'C', 'Kamis', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(294, 'T8008', '0074108', 'CE827', 'A', 'Kamis', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(295, 'T8008', '0074108', 'CE827', 'B', 'Kamis', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(296, 'T8008', '0074108', 'CE827', 'C', 'Kamis', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(297, 'T8009', '0074109', 'CH171', 'A', 'Jumat', '07:00:00', '10:20:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(298, 'T8009', '0074109', 'CH171', 'B', 'Jumat', '10:30:00', '13:50:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(299, 'T8009', '0074109', 'CH171', 'C', 'Jumat', '14:00:00', '17:20:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(300, 'T8010', '0074110', 'MS207', 'A', 'Senin', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(301, 'T8010', '0074110', 'MS207', 'B', 'Senin', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(302, 'T8010', '0074110', 'MS207', 'C', 'Senin', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(303, 'T8001', '0075101', 'DCE100202221', 'A', 'Senin', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(304, 'T8001', '0075101', 'DCE100202221', 'B', 'Senin', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(305, 'T8001', '0075101', 'DCE100202221', 'C', 'Senin', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(306, 'T8002', '0075102', 'DCE1110202221', 'A', 'Senin', '13:00:00', '14:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(307, 'T8002', '0075102', 'DCE1110202221', 'B', 'Senin', '15:00:00', '16:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(308, 'T8002', '0075102', 'DCE1110202221', 'C', 'Senin', '17:00:00', '18:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(309, 'T8003', '0075103', 'GX102202252', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(310, 'T8003', '0075103', 'GX102202252', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(311, 'T8003', '0075103', 'GX102202252', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(312, 'T8004', '0075104', 'GX111202252', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(313, 'T8004', '0075104', 'GX111202252', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(314, 'T8004', '0075104', 'GX111202252', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(315, 'T8005', '0075105', 'GX112202252', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(316, 'T8005', '0075105', 'GX112202252', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(317, 'T8005', '0075105', 'GX112202252', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(318, 'T8001', '0075106', 'GX113202252', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(319, 'T8001', '0075106', 'GX113202252', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(320, 'T8001', '0075106', 'GX113202252', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(321, 'H03A02', '0076101', 'DKVE001202564', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(322, 'H03A02', '0076101', 'DKVE001202564', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(323, 'H03A02', '0076101', 'DKVE001202564', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(324, 'H03A03', '0076102', 'DKVE002202564', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(325, 'H03A03', '0076102', 'DKVE002202564', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(326, 'H03A03', '0076102', 'DKVE002202564', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(327, 'H03A04', '0076103', 'DKVE003202564', 'A', 'Rabu', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(328, 'H03A04', '0076103', 'DKVE003202564', 'B', 'Rabu', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(329, 'H03A04', '0076103', 'DKVE003202564', 'C', 'Rabu', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(330, 'H03A05', '0076104', 'DKVE004202564', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(331, 'H03A05', '0076104', 'DKVE004202564', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(332, 'H03A05', '0076104', 'DKVE004202564', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(333, 'L8026', '0076105', 'DKVI001202564', 'A', 'Jumat', '07:00:00', '08:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(334, 'L8026', '0076105', 'DKVI001202564', 'B', 'Jumat', '09:00:00', '10:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(335, 'L8026', '0076105', 'DKVI001202564', 'C', 'Jumat', '11:00:00', '12:40:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(336, 'H03B01', '0077101', 'AR321', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(337, 'H03B01', '0077101', 'AR321', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(338, 'H03B01', '0077101', 'AR321', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(339, 'H03B02', '0077102', 'AR323', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(340, 'H03B02', '0077102', 'AR323', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(341, 'H03B02', '0077102', 'AR323', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(342, 'H03B03', '0077103', 'AR325', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(343, 'H03B03', '0077103', 'AR325', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(344, 'H03B03', '0077103', 'AR325', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(345, 'H03B04', '0077104', 'AR327', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(346, 'H03B04', '0077104', 'AR327', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(347, 'H03B04', '0077104', 'AR327', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(348, 'H03B05', '0077105', 'AR421', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(349, 'H03B05', '0077105', 'AR421', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(350, 'H03B05', '0077105', 'AR421', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(351, 'H03A01', '0077106', 'AR423', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(352, 'H03A01', '0077106', 'AR423', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(353, 'H03A01', '0077106', 'AR423', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(354, 'H03A02', '0077107', 'AR425', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(355, 'H03A02', '0077107', 'AR425', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(356, 'H03A02', '0077107', 'AR425', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(357, 'H03A03', '0077108', 'AR427', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(358, 'H03A03', '0077108', 'AR427', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(359, 'H03A03', '0077108', 'AR427', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(360, 'H03A04', '0077109', 'AR429', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(361, 'H03A04', '0077109', 'AR429', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(362, 'H03A04', '0077109', 'AR429', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:46:38', '2026-01-17 13:46:38'),
(363, 'M8001', '0078101', 'MN101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(364, 'M8001', '0078101', 'MN101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(365, 'M8001', '0078101', 'MN101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(366, 'M8002', '0078102', 'MN102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(367, 'M8002', '0078102', 'MN102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(368, 'M8002', '0078102', 'MN102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(369, 'M8003', '0078103', 'MN103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(370, 'M8003', '0078103', 'MN103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(371, 'M8003', '0078103', 'MN103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(372, 'M8004', '0078104', 'MN104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(373, 'M8004', '0078104', 'MN104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(374, 'M8004', '0078104', 'MN104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(375, 'M8005', '0078105', 'MN105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(376, 'M8005', '0078105', 'MN105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(377, 'M8005', '0078105', 'MN105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(378, 'M8006', '0079101', 'AK101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(379, 'M8006', '0079101', 'AK101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(380, 'M8006', '0079101', 'AK101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(381, 'M8007', '0079102', 'AK102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(382, 'M8007', '0079102', 'AK102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(383, 'M8007', '0079102', 'AK102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(384, 'M8008', '0079103', 'AK103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(385, 'M8008', '0079103', 'AK103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(386, 'M8008', '0079103', 'AK103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(387, 'M8009', '0079104', 'AK104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(388, 'M8009', '0079104', 'AK104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(389, 'M8009', '0079104', 'AK104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(390, 'M8010', '0079105', 'AK105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(391, 'M8010', '0079105', 'AK105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(392, 'M8010', '0079105', 'AK105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(393, 'S8001', '0080101', 'EN101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(394, 'S8001', '0080101', 'EN101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(395, 'S8001', '0080101', 'EN101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(396, 'S8002', '0080102', 'EN102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(397, 'S8002', '0080102', 'EN102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(398, 'S8002', '0080102', 'EN102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(399, 'S8003', '0080103', 'EN103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(400, 'S8003', '0080103', 'EN103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(401, 'S8003', '0080103', 'EN103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(402, 'S8010', '0080104', 'EN104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25');
INSERT INTO `perkuliahan` (`id_perkuliahan`, `kode_ruangan`, `nip_dosen`, `kode_mk`, `kelas`, `hari`, `jam_mulai`, `jam_berakhir`, `tahun_ajaran`, `created_at`, `updated_at`) VALUES
(403, 'S8010', '0080104', 'EN104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(404, 'S8010', '0080104', 'EN104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(405, 'S8011', '0080105', 'EN105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(406, 'S8011', '0080105', 'EN105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(407, 'S8011', '0080105', 'EN105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(408, 'S8004', '0081101', 'CH101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(409, 'S8004', '0081101', 'CH101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(410, 'S8004', '0081101', 'CH101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(411, 'S8005', '0081102', 'CH102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(412, 'S8005', '0081102', 'CH102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(413, 'S8005', '0081102', 'CH102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(414, 'S8006', '0081103', 'CH103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(415, 'S8006', '0081103', 'CH103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(416, 'S8006', '0081103', 'CH103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(417, 'S8010', '0081104', 'CH104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(418, 'S8010', '0081104', 'CH104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(419, 'S8010', '0081104', 'CH104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(420, 'S8012', '0081105', 'CH105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(421, 'S8012', '0081105', 'CH105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(422, 'S8012', '0081105', 'CH105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(423, 'S8007', '0082101', 'JP101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(424, 'S8007', '0082101', 'JP101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(425, 'S8007', '0082101', 'JP101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(426, 'S8008', '0082102', 'JP102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(427, 'S8008', '0082102', 'JP102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(428, 'S8008', '0082102', 'JP102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(429, 'S8009', '0082103', 'JP103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(430, 'S8009', '0082103', 'JP103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(431, 'S8009', '0082103', 'JP103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(432, 'S8010', '0082104', 'JP104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(433, 'S8010', '0082104', 'JP104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(434, 'S8010', '0082104', 'JP104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(435, 'S8011', '0082105', 'JP105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(436, 'S8011', '0082105', 'JP105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(437, 'S8011', '0082105', 'JP105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(438, 'K8001', '0083101', 'HK101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(439, 'K8001', '0083101', 'HK101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(440, 'K8001', '0083101', 'HK101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(441, 'K8002', '0083102', 'HK102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(442, 'K8002', '0083102', 'HK102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(443, 'K8002', '0083102', 'HK102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(444, 'K8003', '0083103', 'HK103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(445, 'K8003', '0083103', 'HK103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(446, 'K8003', '0083103', 'HK103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(447, 'K8004', '0083104', 'HK104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(448, 'K8004', '0083104', 'HK104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(449, 'K8004', '0083104', 'HK104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(450, 'K8005', '0083105', 'HK105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(451, 'K8005', '0083105', 'HK105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(452, 'K8005', '0083105', 'HK105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(453, 'A8001', '0084101', 'SR101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(454, 'A8001', '0084101', 'SR101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(455, 'A8001', '0084101', 'SR101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(456, 'A8002', '0084102', 'SR102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(457, 'A8002', '0084102', 'SR102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(458, 'A8002', '0084102', 'SR102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(459, 'A8004', '0084103', 'SR103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(460, 'A8004', '0084103', 'SR103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(461, 'A8004', '0084103', 'SR103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(462, 'A8005', '0084104', 'SR104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(463, 'A8005', '0084104', 'SR104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(464, 'A8005', '0084104', 'SR104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(465, 'A8006', '0084105', 'SR105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(466, 'A8006', '0084105', 'SR105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(467, 'A8006', '0084105', 'SR105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(468, 'SI8001', '0085101', 'SI101', 'A', 'Senin', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(469, 'SI8001', '0085101', 'SI101', 'B', 'Senin', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(470, 'SI8001', '0085101', 'SI101', 'C', 'Senin', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(471, 'SI8004', '0085102', 'SI102', 'A', 'Selasa', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(472, 'SI8004', '0085102', 'SI102', 'B', 'Selasa', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(473, 'SI8004', '0085102', 'SI102', 'C', 'Selasa', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(474, 'SI8002', '0085103', 'SI103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(475, 'SI8002', '0085103', 'SI103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(476, 'SI8002', '0085103', 'SI103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(477, 'SI8003', '0085104', 'SI104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(478, 'SI8003', '0085104', 'SI104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(479, 'SI8003', '0085104', 'SI104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(480, 'SI8005', '0085105', 'SI105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(481, 'SI8005', '0085105', 'SI105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(482, 'SI8005', '0085105', 'SI105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(483, 'MED001', '0086101', 'MED101', 'A', 'Senin', '07:00:00', '10:20:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(484, 'MED001', '0086101', 'MED101', 'B', 'Senin', '10:30:00', '13:50:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(485, 'MED001', '0086101', 'MED101', 'C', 'Senin', '14:00:00', '17:20:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(486, 'MED002', '0086102', 'MED102', 'A', 'Selasa', '07:00:00', '10:20:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(487, 'MED002', '0086102', 'MED102', 'B', 'Selasa', '10:30:00', '13:50:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(488, 'MED002', '0086102', 'MED102', 'C', 'Selasa', '14:00:00', '17:20:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(489, 'MED003', '0086103', 'MED103', 'A', 'Rabu', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(490, 'MED003', '0086103', 'MED103', 'B', 'Rabu', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(491, 'MED003', '0086103', 'MED103', 'C', 'Rabu', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(492, 'MED004', '0086104', 'MED104', 'A', 'Kamis', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(493, 'MED004', '0086104', 'MED104', 'B', 'Kamis', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(494, 'MED004', '0086104', 'MED104', 'C', 'Kamis', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(495, 'MED005', '0086105', 'MED105', 'A', 'Jumat', '07:00:00', '09:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(496, 'MED005', '0086105', 'MED105', 'B', 'Jumat', '10:00:00', '12:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25'),
(497, 'MED005', '0086105', 'MED105', 'C', 'Jumat', '13:00:00', '15:30:00', '2025/2026 - Ganjil', '2026-01-17 13:59:25', '2026-01-17 13:59:25');

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
(179, '2472021', 11, '2026-01-03', 'Hadir', NULL, '2026-01-02 23:06:20', '2026-01-02 23:06:20'),
(180, '2472022', 10, '2025-03-20', 'Absen', NULL, '2026-01-12 03:34:21', '2026-01-12 03:34:21');

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
('A8001', 'Studio Lukis 1', 30, 'AC, Proyektor, Whiteboard, Kuda-kuda, Canvas, Cat, Meja Kerja', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('A8002', 'Studio Lukis 2', 30, 'AC, Proyektor, Whiteboard, Kuda-kuda, Canvas, Cat, Meja Kerja', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('A8003', 'Studio Lukis 3', 30, 'AC, Proyektor, Whiteboard, Kuda-kuda, Canvas, Cat, Meja Kerja', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('A8004', 'Studio Patung', 25, 'AC, Proyektor, Whiteboard, Meja Kerja, Tools Patung, Clay', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('A8005', 'Studio Grafis', 30, 'AC, Proyektor, Whiteboard, Mesin Cetak, Meja Kerja, Tools', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('A8006', 'Gallery Seni', 40, 'AC, Proyektor, Whiteboard, Display Panel, Lighting', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03A01', 'Ruang H03A01', 40, NULL, '2026-01-11 08:18:15', '2026-01-11 08:18:15'),
('H03A02', 'Studio Desain 1', 35, 'AC, Proyektor, Whiteboard, Meja Gambar, PC Design, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03A03', 'Studio Desain 2', 35, 'AC, Proyektor, Whiteboard, Meja Gambar, PC Design, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03A04', 'Studio Desain 3', 35, 'AC, Proyektor, Whiteboard, Meja Gambar, PC Design, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03A05', 'Studio Desain 4', 35, 'AC, Proyektor, Whiteboard, Meja Gambar, PC Design, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03B01', 'Studio Arsitektur 1', 35, 'AC, Proyektor, Whiteboard, Meja Gambar Besar, PC CAD, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03B02', 'Studio Arsitektur 2', 35, 'AC, Proyektor, Whiteboard, Meja Gambar Besar, PC CAD, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03B03', 'Studio Arsitektur 3', 35, 'AC, Proyektor, Whiteboard, Meja Gambar Besar, PC CAD, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03B04', 'Studio Arsitektur 4', 35, 'AC, Proyektor, Whiteboard, Meja Gambar Besar, PC CAD, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H03B05', 'Maket Workshop', 30, 'AC, Proyektor, Whiteboard, Meja Kerja, Tools Maket, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04A02', 'Ruang Psikologi 1', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Kursi Lingkaran', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04A03', 'Ruang Psikologi 2', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Kursi Lingkaran', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04A04', 'Ruang Psikologi 3', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Kursi Lingkaran', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04A05', 'Ruang Psikologi 4', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Kursi Lingkaran', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04B01', 'Ruang H04B01', 40, NULL, '2026-01-09 22:30:46', '2026-01-09 22:30:46'),
('H04B02', 'Ruang H04B02', 40, NULL, '2026-01-09 22:28:42', '2026-01-09 22:28:42'),
('H04B03', 'Lab Psikologi 1', 35, 'AC, Proyektor, Whiteboard, Alat Tes Psikologi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04B04', 'Lab Psikologi 2', 35, 'AC, Proyektor, Whiteboard, Alat Tes Psikologi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04B05', 'Lab Psikologi 3', 35, 'AC, Proyektor, Whiteboard, Alat Tes Psikologi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04C01', 'Ruang H04C01', 40, NULL, '2026-01-09 23:32:11', '2026-01-09 23:32:11'),
('H04C02', 'Ruang Konseling 1', 30, 'AC, Proyektor, Whiteboard, Sofa, Kursi Nyaman', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('H04C03', 'Ruang Konseling 2', 30, 'AC, Proyektor, Whiteboard, Sofa, Kursi Nyaman', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8001', 'Ruang Hukum 1', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8002', 'Ruang Hukum 2', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8003', 'Ruang Hukum 3', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8004', 'Ruang Hukum 4', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8005', 'Ruang Hukum 5', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8006', 'Ruang Sidang Praktik', 45, 'AC, Proyektor, Whiteboard, Meja Hakim, Kursi Audiens, Sound System', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('K8007', 'Lab Hukum', 35, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8001', 'ADV 1', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:29'),
('L8002', 'ADV 2', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:33'),
('L8003', 'ADV 3', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:35'),
('L8004', 'ADV 4', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 06:48:37', '2025-12-29 08:03:38'),
('L8005', 'Ruang Multimedia', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8006', 'Ruang Lab Inter', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8007', 'Lab Int 1', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8008', 'Lab Int 2', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8009', 'Lab Int 3', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8010', 'Lab Int 4', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2025-12-29 08:03:13', '2025-12-29 08:03:13'),
('L8011', 'Lab Informatika 1', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8012', 'Lab Informatika 2', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8013', 'Lab Informatika 3', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8014', 'Lab Informatika 4', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8015', 'Lab Informatika 5', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8016', 'Lab Sistem Komputer 1', 28, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi, Hardware Tools', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8017', 'Lab Sistem Komputer 2', 28, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi, Hardware Tools', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8018', 'Lab Sistem Komputer 3', 28, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi, Hardware Tools', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8019', 'Lab Jaringan 1', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Networking Equipment', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8020', 'Lab Jaringan 2', 25, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Networking Equipment', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8021', 'Ruang Kelas Informatika 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8022', 'Ruang Kelas Informatika 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8023', 'Ruang Kelas Informatika 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8024', 'Ruang Kelas Informatika 4', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8025', 'Ruang Kelas Informatika 5', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8026', 'Studio Multimedia 1', 30, 'AC, Proyektor, Whiteboard, PC High-End, Software Desain, Kamera', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8027', 'Studio Multimedia 2', 30, 'AC, Proyektor, Whiteboard, PC High-End, Software Desain, Kamera', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8028', 'Lab Elektro 1', 28, 'AC, Proyektor, Whiteboard, PC Dosen, Peralatan Elektro, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8029', 'Lab Elektro 2', 28, 'AC, Proyektor, Whiteboard, PC Dosen, Peralatan Elektro, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('L8030', 'Lab Elektro 3', 28, 'AC, Proyektor, Whiteboard, PC Dosen, Peralatan Elektro, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8001', 'Ruang Manajemen 1', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8002', 'Ruang Manajemen 2', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8003', 'Ruang Manajemen 3', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8004', 'Ruang Manajemen 4', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8005', 'Ruang Manajemen 5', 40, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8006', 'Lab Akuntansi', 35, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Software Akuntansi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8007', 'Lab Keuangan', 35, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Software Keuangan', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8008', 'Lab Marketing', 35, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8009', 'Lab Entrepreneurship', 35, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('M8010', 'Ruang Seminar Bisnis', 50, 'AC, Proyektor, Whiteboard, Sound System, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED001', 'Ruang Anatomi 1', 40, 'AC, Proyektor, Whiteboard, Model Anatomi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED002', 'Ruang Anatomi 2', 40, 'AC, Proyektor, Whiteboard, Model Anatomi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED003', 'Lab Klinik 1', 30, 'AC, Proyektor, Whiteboard, Meja Pemeriksaan, Alat Medis', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED004', 'Lab Klinik 2', 30, 'AC, Proyektor, Whiteboard, Meja Pemeriksaan, Alat Medis', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED005', 'Ruang Tutorial 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED006', 'Ruang Tutorial 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('MED007', 'Ruang Tutorial 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8001', 'Ruang Sastra Inggris 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8002', 'Ruang Sastra Inggris 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8003', 'Ruang Sastra Inggris 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8004', 'Ruang Sastra China 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8005', 'Ruang Sastra China 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8006', 'Ruang Sastra China 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8007', 'Ruang Sastra Jepang 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8008', 'Ruang Sastra Jepang 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8009', 'Ruang Sastra Jepang 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8010', 'Lab Bahasa 1', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Headset', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8011', 'Lab Bahasa 2', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Headset', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('S8012', 'Lab Bahasa 3', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Headset', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('SI8001', 'Ruang Sistem Informasi 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('SI8002', 'Ruang Sistem Informasi 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('SI8003', 'Ruang Sistem Informasi 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('SI8004', 'Lab Sistem Informasi 1', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Server', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('SI8005', 'Lab Sistem Informasi 2', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Server', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('SI8006', 'Lab Database', 30, 'AC, Proyektor, Whiteboard, PC Dosen, PC Mahasiswa, Database Server', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8001', 'Ruang Teknik Sipil 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja Gambar, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8002', 'Ruang Teknik Sipil 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja Gambar, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8003', 'Ruang Teknik Sipil 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja Gambar, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8004', 'Lab Struktur & Material', 30, 'AC, Proyektor, Whiteboard, Peralatan Uji Material, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8005', 'Lab Mekanika Tanah', 30, 'AC, Proyektor, Whiteboard, Peralatan Geoteknik, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8006', 'Ruang Teknik Industri 1', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8007', 'Ruang Teknik Industri 2', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8008', 'Ruang Teknik Industri 3', 35, 'AC, Proyektor, Whiteboard, PC Dosen, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8009', 'Lab Ergonomi', 28, 'AC, Proyektor, Whiteboard, Peralatan Ergonomi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24'),
('T8010', 'Lab Sistem Produksi', 30, 'AC, Proyektor, Whiteboard, Peralatan Produksi, Meja, Kursi', '2026-01-17 13:46:24', '2026-01-17 13:46:24');

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
('bmj2ZjoSD6RVmefyDTIymg2ol9GAJf8eRK2XJBEG', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNnNOVkdQem5DSnBuRHJscFJ1enprQ1ppTjFRREphRTEzaGZOYll1ZSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6OTA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYXRhLWt1bGlhaD9qdXJ1c2FuPURlc2FpbiUyMEtvbXVuaWthc2klMjBWaXN1YWwmc2lmYXQ9UGlsaWhhbiI7czo1OiJyb3V0ZSI7Tjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjIwOiJyZWFkX25vdGlmaWNhdGlvbl9pZCI7aToxNjt9', 1768401208),
('mFPREQB4ygoWq85expM104F43ebuPbiptWW5SIFM', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiY1RvU3VGdUE2SHBPQko5aGVmZDhDOVNHaWNJbVhPcFpqR0FBRlZFayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wZXJrdWxpYWhhbiI7czo1OiJyb3V0ZSI7czoxNzoicGVya3VsaWFoYW4uaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1768658615),
('toxst0RIDO2aDQi80KnHewK3G37un7TwJ99rGWqj', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibTBwQ1BEWFhHZzBmbnZSSGdFSzNzUXJaUE5UNW5kb2lmSTB3cElpbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Njc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9tYXRhLWt1bGlhaD9qdXJ1c2FuPUlsbXUlMjBIdWt1bSZzaWZhdD0iO3M6NToicm91dGUiO047fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1768405256);

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
(10, '2472021', 'Tagihan Semester', 6300000.00, '2026-01-26', NULL, NULL, 'Belum Lunas', '2026-01-06 09:15:11', '2026-01-11 21:07:24'),
(15, '2472022', 'Tagihan Semester', 2100000.00, '2026-01-26', 3, 1, 'Lunas', '2026-01-14 04:53:38', '2026-01-14 12:24:42'),
(16, '2472022', 'Tagihan Semester (Cicilan 2)', 2100000.00, '2026-02-26', 3, 2, 'Lunas', '2026-01-14 04:53:55', '2026-01-14 12:26:37'),
(17, '2472022', 'Tagihan Semester (Cicilan 3)', 2100000.00, '2026-03-26', 3, 3, 'Belum Lunas', '2026-01-14 04:53:55', '2026-01-14 04:53:55');

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
(24, 'Markus Tanubrata, S.T., M.M., M.T.', '0072213@edutrack.com', '$2y$12$XFyFzFUzRaZ8yleOXFhH6eHrmgGjSU.0.QYknRbwUiZtpCVivRIvK', 'dosen', '2026-01-07 12:23:02', '2026-01-07 12:23:02'),
(25, 'Marissa Chitra Sulastra, S.Psi., M.Psi., Psikolog', '0072214@edutrack.com', '$2y$12$3HFN9NQfI0SjKUVoli3r5OkAuWMeqxMTOJqe3ylKLPHOx9tGxorxS', 'dosen', '2026-01-08 00:24:54', '2026-01-08 00:24:54'),
(26, 'Errvin Junius', '2430001@edutrack.com', '$2y$12$3TW4ejZZei3fTEa.d3KW/eEZzp55FHjx5/CceW6JEjX0IGBU03qfS', 'mahasiswa', '2026-01-08 00:28:17', '2026-01-08 00:28:17'),
(27, 'Hans Maulana', '2430002@edutrack.com', '$2y$12$3TBqRauR8JeRMRdB7M5R.uPZd6U6emD/jp6tAUXuCfxBYC5rq3aea', 'mahasiswa', '2026-01-08 00:29:37', '2026-01-08 00:29:37'),
(28, 'Hendry Wong, S.T., M.Kom.', '0071105@edutrack.com', '$2y$12$Fv5shXkPQx9bRggbnEooYuPzNrq7EYYnE6YPICbBlkMq0mjByCAuK', 'dosen', '2026-01-09 22:48:57', '2026-01-09 22:48:57'),
(29, 'Andrew Sebastian Lehman, S.T., M.Eng.', '0071106@edutrack.com', '$2y$12$iwR.kjPpTdNdN9R65ybOz.wv8gH4c.0SlPvYGoNwUxF3ernZHsFTG', 'dosen', '2026-01-09 22:56:35', '2026-01-09 22:56:35'),
(30, 'Jonathan Chandra, S.T., M.T.', '0071107@edutrack.com', '$2y$12$UtOOhHbL.Ide83kCzNKFq.sq7AcEEi3e3vb6MG1NK.j84idDKt5CC', 'dosen', '2026-01-09 22:57:53', '2026-01-09 22:57:53'),
(31, 'Lisa Imelia Satyawan, M.Psi., Psikolog.', '0073302@edutrack.com', '$2y$12$nzaqE4yn87fS4Zh/AIAFjelq/6FozYbKBhmiC2pMepLr9JLD8fYPG', 'dosen', '2026-01-09 23:07:50', '2026-01-09 23:07:50'),
(32, 'Cindy Maria, M.Psi., Psikolog', '0073303@edutrack.com', '$2y$12$YvExMXEaScuLXvpAGmAwrux98FdJwAHKoJeor6Cm75zcYIeboG0gm', 'dosen', '2026-01-09 23:09:06', '2026-01-09 23:09:06'),
(33, 'Indah Puspitasari, M.Psi., Psikolog.', '0073304@edutrack.com', '$2y$12$20bUq4fK.UtWPK61XL4hQedZVN4Uy4/BrWwBatiDPj7NCFpl/oOSO', 'dosen', '2026-01-09 23:10:21', '2026-01-09 23:10:21'),
(34, 'Meta Dwijayanthy, M.Psi., Psikolog.', '0073305@edutrack.com', '$2y$12$h.dnOhoxGx3MYd2KuT8oHuPx17POVNu7oWFyQacsY/Sz7.X0ltFY2', 'dosen', '2026-01-09 23:12:37', '2026-01-09 23:12:37'),
(35, 'Dr. Tery Setiawan, B.A., S.Psi., M.Si.', '0073306@edutrack.com', '$2y$12$eXA1QUvoJCU/dMB9zqurOewn6DE9IsA4nQ1YKVwuap6yt5VRUT4Bu', 'dosen', '2026-01-09 23:16:11', '2026-01-09 23:16:11'),
(36, 'Dr. Yuspendi, M.Psi., Psikolog, M.Pd.', '0073307@dutrack.com', '$2y$12$U2fXawBEwJi/Wy0ZoZMh6Oq43hHrDUCDaaRe9oX.OW/O3zEXLCQ72', 'dosen', '2026-01-09 23:18:57', '2026-01-09 23:18:57'),
(37, 'Heliany Kiswantomo, S.Psi., M.Si., Psikolog.', '0073308@edutrack.com', '$2y$12$ler7nhRmitKQftDEXkAhfuZC3BrNicnLk/atN7wGS1h6WRZyxyAmK', 'dosen', '2026-01-09 23:21:08', '2026-01-09 23:21:08'),
(38, 'Dr. Ahmad Fauzi, M.Kom', '0072215@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(39, 'Dr. Dewi Kusuma, M.T', '0072216@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(40, 'Dr. Budi Santoso, M.Kom', '0072217@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(41, 'Dr. Rina Widya, M.T', '0072218@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(42, 'Dr. Hendra Kusuma, M.Kom', '0072219@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(43, 'Dr. Lina Marlina, M.Kom', '0071112@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(44, 'Dr. Bambang Suryadi, M.T', '0071113@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(45, 'Dr. Yuni Astuti, M.Kom', '0071114@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(46, 'Dr. Eko Prasetyo, M.T', '0071115@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(47, 'Dr. Nurul Hidayah, M.Psi., Psikolog', '0073309@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(48, 'Dr. Wawan Setiawan, M.Psi., Psikolog', '0073310@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(49, 'Dr. Ir. Faisal Rahman, M.T', '0072301@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(50, 'Dr. Ir. Sinta Dewi, M.T', '0072302@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(51, 'Dr. Ir. Andi Firmansyah, M.T', '0072303@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(52, 'Dr. Ir. Dina Mariana, M.T', '0072304@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(53, 'Dr. Ir. Teguh Santoso, M.T', '0072305@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(54, 'Dr. Ir. Mega Putri, M.T', '0072306@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(55, 'Dr. Ir. Rizki Ramadhan, M.T', '0072307@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(56, 'Dr. Ir. Wulan Dari, M.T', '0072308@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(57, 'Dr. Ir. Yoga Pratama, M.T', '0072309@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(58, 'Dr. Ir. Tari Susanti, M.T', '0072310@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(59, 'Dr. Ir. Bayu Aji, M.T', '0072311@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(60, 'Dr. Ir. Raden Adiputra, M.T', '0074101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(61, 'Dr. Ir. Citra Kusuma, M.T', '0074102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(62, 'Dr. Ir. Andri Wijaya, M.T', '0074103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(63, 'Dr. Ir. Nisa Amelia, M.T', '0074104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(64, 'Dr. Ir. Farhan Maulana, M.T', '0074105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(65, 'Dr. Ir. Sari Wahyuni, M.T', '0074106@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(66, 'Dr. Ir. Irfan Hakim, M.T', '0074107@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(67, 'Dr. Ir. Rina Safitri, M.T', '0074108@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(68, 'Dr. Ir. Zaki Permadi, M.T', '0074109@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(69, 'Dr. Ir. Linda Kartika, M.T', '0074110@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(70, 'Dr. Ir. Gunawan Tri, M.T', '0075101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(71, 'Dr. Ir. Ratih Puspita, M.T', '0075102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(72, 'Dr. Ir. Hendro Susilo, M.T', '0075103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(73, 'Dr. Ir. Diana Sari, M.T', '0075104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(74, 'Dr. Ir. Ridwan Kamil, M.T', '0075105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(75, 'Dr. Ir. Vina Panduwinata, M.T', '0075106@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(76, 'Dr. Arief Budiman, M.Ds', '0076101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(77, 'Dr. Putri Melati, M.Ds', '0076102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(78, 'Dr. Dimas Prasetyo, M.Ds', '0076103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(79, 'Dr. Ayu Lestari, M.Ds', '0076104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(80, 'Dr. Rahman Hakim, M.Ds', '0076105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(81, 'Dr. Ir. Surya Darma, M.Ars', '0077101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(82, 'Dr. Ir. Intan Cahaya, M.Ars', '0077102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(83, 'Dr. Ir. Satria Wijaya, M.Ars', '0077103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(84, 'Dr. Ir. Melinda Sari, M.Ars', '0077104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(85, 'Dr. Ir. Faisal Akbar, M.Ars', '0077105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(86, 'Dr. Ir. Siska Amelia, M.Ars', '0077106@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(87, 'Dr. Ir. Rangga Aditya, M.Ars', '0077107@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(88, 'Dr. Ir. Bella Safira, M.Ars', '0077108@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(89, 'Dr. Ir. Fajar Ramadhan, M.Ars', '0077109@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:37:22', '2026-01-17 13:37:22'),
(90, 'Dr. Rahmat Hidayat, M.M', '0078101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(91, 'Dr. Cindy Permata, M.M', '0078102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(92, 'Dr. Fikri Ramadhan, M.M', '0078103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(93, 'Dr. Nadya Kartika, M.M', '0078104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(94, 'Dr. Reza Pratama, M.M', '0078105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(95, 'Dr. Wahyu Santoso, M.Ak', '0079101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(96, 'Dr. Lia Kurnia, M.Ak', '0079102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(97, 'Dr. Gilang Ramadhan, M.Ak', '0079103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(98, 'Dr. Erna Yulianti, M.Ak', '0079104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(99, 'Dr. Yoga Aditya, M.Ak', '0079105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(100, 'Dr. Rini Novita, M.A', '0080101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(101, 'Dr. David Anderson, M.A', '0080102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(102, 'Dr. Maya Anggraini, M.A', '0080103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(103, 'Dr. John Smith, M.A', '0080104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(104, 'Dr. Ratna Kusuma, M.A', '0080105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(105, 'Dr. Li Wei, M.A', '0081101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(106, 'Dr. Wang Mei, M.A', '0081102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(107, 'Dr. Chen Hao, M.A', '0081103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(108, 'Dr. Liu Ying, M.A', '0081104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(109, 'Dr. Zhang Lei, M.A', '0081105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(110, 'Dr. Tanaka Yuki, M.A', '0082101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(111, 'Dr. Yamamoto Akira, M.A', '0082102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(112, 'Dr. Sato Haruka, M.A', '0082103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(113, 'Dr. Suzuki Ren, M.A', '0082104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(114, 'Dr. Watanabe Mai, M.A', '0082105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(115, 'Dr. H. Suryanto, S.H., M.H', '0083101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(116, 'Dr. Hj. Ratna Dewi, S.H., M.H', '0083102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(117, 'Dr. Arief Hidayat, S.H., M.H', '0083103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(118, 'Dr. Susi Susanti, S.H., M.H', '0083104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(119, 'Dr. Bambang Soepeno, S.H., M.H', '0083105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(120, 'Dr. Agung Kurniawan, M.Sn', '0084101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(121, 'Dr. Retno Maruti, M.Sn', '0084102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(122, 'Dr. Danu Wicaksono, M.Sn', '0084103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(123, 'Dr. Karina Putri, M.Sn', '0084104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(124, 'Dr. Fadhil Akbar, M.Sn', '0084105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(125, 'Dr. Ricky Pratama, M.T', '0085101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(126, 'Dr. Nina Sari, M.T', '0085102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(127, 'Dr. Hadi Wijaya, M.T', '0085103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(128, 'Dr. Diah Ayu, M.T', '0085104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(129, 'Dr. Yusuf Rahman, M.T', '0085105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(130, 'Prof. Dr. dr. Andi Wijaya, Sp.PD', '0086101@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(131, 'Prof. Dr. dr. Siti Rahayu, Sp.OG', '0086102@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(132, 'Dr. dr. Budi Santosa, Sp.B', '0086103@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(133, 'Dr. dr. Rina Kusuma, Sp.A', '0086104@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57'),
(134, 'Dr. dr. Hendra Gunawan, Sp.JP', '0086105@edutrack.com', '$2y$12$LQv3c1yytGi9Lz0JmxXbTuK8Z.Xc4NNwE8C2K.fQzO7bPqDgJEV1m', 'dosen', '2026-01-17 13:58:57', '2026-01-17 13:58:57');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dkbs`
--
ALTER TABLE `dkbs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

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
  MODIFY `id_perkuliahan` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=498;

--
-- AUTO_INCREMENT for table `presensi`
--
ALTER TABLE `presensi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

--
-- AUTO_INCREMENT for table `tagihan`
--
ALTER TABLE `tagihan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

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
