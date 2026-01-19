<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routines = [
    // Procedures
    "DROP PROCEDURE IF EXISTS `sp_bayar_tagihan`",
    "CREATE PROCEDURE `sp_bayar_tagihan` (IN `tagihan_id` INT)   BEGIN
                UPDATE tagihan SET status = 'Lunas', updated_at = NOW() WHERE id = tagihan_id;
            END",

    "DROP PROCEDURE IF EXISTS `sp_cek_status_kelas`",
    "CREATE PROCEDURE `sp_cek_status_kelas` (IN `p_ta` VARCHAR(255))   BEGIN
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
            END",

    "DROP PROCEDURE IF EXISTS `sp_get_matkul_by_jurusan`",
    "CREATE PROCEDURE `sp_get_matkul_by_jurusan` (IN `p_jurusan` VARCHAR(255))   BEGIN
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
END",

    "DROP PROCEDURE IF EXISTS `sp_get_perkuliahan_by_ta`",
    "CREATE PROCEDURE `sp_get_perkuliahan_by_ta` (IN `p_ta` VARCHAR(255))   BEGIN
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
END",

    "DROP PROCEDURE IF EXISTS `sp_get_perkuliahan_by_ta_and_jurusan`",
    "CREATE PROCEDURE `sp_get_perkuliahan_by_ta_and_jurusan` (IN `p_ta` VARCHAR(255), IN `p_jurusan` VARCHAR(255))   BEGIN
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
            END",

    "DROP PROCEDURE IF EXISTS `sp_get_va`",
    "CREATE PROCEDURE `sp_get_va` (IN `student_nrp` VARCHAR(50))   BEGIN
                SELECT CONCAT('2911', student_nrp) AS va;
            END",

    "DROP PROCEDURE IF EXISTS `update_nilai_akhir`",
    "CREATE PROCEDURE `update_nilai_akhir` ()   BEGIN
    UPDATE nilai
    SET nilai_akhir = CASE
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 81 THEN 'A'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 71 THEN 'B+'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 61 THEN 'B'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 51 THEN 'C+'
        WHEN (kt * 0.6 + uts * 0.2 + uas * 0.2) >= 41 THEN 'C'
        ELSE 'D'
    END;
END",

    // Functions
    "DROP FUNCTION IF EXISTS `get_ipk`",
    "CREATE FUNCTION `get_ipk` (`p_nrp` VARCHAR(20) COLLATE utf8mb4_unicode_ci) RETURNS DECIMAL(3,2) DETERMINISTIC BEGIN
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
END",

    "DROP FUNCTION IF EXISTS `get_total_sks`",
    "CREATE FUNCTION `get_total_sks` (`p_nrp` VARCHAR(20) COLLATE utf8mb4_unicode_ci, `p_ta` VARCHAR(50) COLLATE utf8mb4_unicode_ci) RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;
    
    SELECT SUM(mk.sks) INTO total
    FROM dkbs d
    JOIN mata_kuliah mk ON d.kode_mk = mk.kode_mk
    WHERE d.nrp = p_nrp AND d.tahun_ajaran = p_ta;
    
    RETURN IFNULL(total, 0);
END",
];

foreach ($routines as $sql) {
    try {
        DB::unprepared($sql);
        echo "Executed: " . substr($sql, 0, 50) . "...\n";
    } catch (\Exception $e) {
        echo "Error executing routine: " . $e->getMessage() . "\n";
    }
}

echo "\nVerifying...\n";
$functions = DB::select("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = 'edutrack'");
foreach ($functions as $f) {
    echo "Found Routine: " . $f->ROUTINE_NAME . "\n";
}
if (empty($functions)) {
    echo "NO ROUTINES FOUND IN database 'edutrack' via information_schema!\n";
    echo "This suggests a MySQL server metadata issue.\n";
}
