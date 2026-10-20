<?php
/**
 * Migration Script for UPA Groups and Member UPA Histories
 */

try {
    $pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    echo "=== 1. UPDATE TABEL regions_upa ===\n";
    // Check existing columns in regions_upa
    $cols = $pdo->query("SHOW COLUMNS FROM regions_upa")->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('nama_group', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN nama_group VARCHAR(150) NULL DEFAULT NULL AFTER kode_upa");
        echo "- Kolom nama_group ditambahkan ke regions_upa\n";
    }
    if (!in_array('pembimbing_nama', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN pembimbing_nama VARCHAR(255) NULL DEFAULT NULL AFTER pembimbing_id");
        echo "- Kolom pembimbing_nama ditambahkan ke regions_upa\n";
    }
    if (!in_array('jadwal_rutin', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN jadwal_rutin VARCHAR(255) NULL DEFAULT NULL");
        echo "- Kolom jadwal_rutin ditambahkan ke regions_upa\n";
    }
    if (!in_array('tempat_pertemuan', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN tempat_pertemuan VARCHAR(255) NULL DEFAULT NULL");
        echo "- Kolom tempat_pertemuan ditambahkan ke regions_upa\n";
    }
    if (!in_array('is_active', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN is_active TINYINT(1) NOT NULL DEFAULT 1");
        echo "- Kolom is_active ditambahkan ke regions_upa\n";
    }
    if (!in_array('keterangan', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN keterangan TEXT NULL DEFAULT NULL");
        echo "- Kolom keterangan ditambahkan ke regions_upa\n";
    }
    if (!in_array('created_at', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP");
        echo "- Kolom created_at ditambahkan ke regions_upa\n";
    }
    if (!in_array('updated_at', $cols)) {
        $pdo->exec("ALTER TABLE regions_upa ADD COLUMN updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "- Kolom updated_at ditambahkan ke regions_upa\n";
    }

    // Set default nama_group jika masih null/kosong
    $pdo->exec("UPDATE regions_upa SET nama_group = CONCAT('Group ', kode_upa) WHERE nama_group IS NULL OR nama_group = ''");
    echo "- Set default nama_group berhasil\n";

    // Isi pembimbing_nama dari tabel members untuk UPA yang sudah punya pembimbing_id
    $pdo->exec("UPDATE regions_upa r 
                JOIN members m ON r.pembimbing_id = m.id 
                SET r.pembimbing_nama = m.nama_lengkap 
                WHERE r.pembimbing_id IS NOT NULL AND (r.pembimbing_nama IS NULL OR r.pembimbing_nama = '')");
    echo "- Sinkronisasi pembimbing_nama di regions_upa selesai\n";


    echo "\n=== 2. MEMBUAT TABEL member_upa_histories ===\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS member_upa_histories (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        member_id BIGINT UNSIGNED NOT NULL,
        kode_upa VARCHAR(50) NOT NULL,
        nama_group VARCHAR(150) NULL DEFAULT NULL,
        pembimbing_id BIGINT UNSIGNED NULL DEFAULT NULL,
        pembimbing_nama VARCHAR(255) NULL DEFAULT NULL,
        tahun_mulai VARCHAR(10) NULL DEFAULT NULL,
        tahun_selesai VARCHAR(10) NULL DEFAULT NULL,
        periode VARCHAR(50) NULL DEFAULT NULL,
        status ENUM('aktif', 'selesai', 'pindah', 'alumni') NOT NULL DEFAULT 'aktif',
        is_current TINYINT(1) NOT NULL DEFAULT 1,
        catatan TEXT NULL DEFAULT NULL,
        created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_member_id (member_id),
        INDEX idx_kode_upa (kode_upa),
        INDEX idx_member_current (member_id, is_current),
        INDEX idx_pembimbing (pembimbing_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "- Tabel member_upa_histories siap\n";


    echo "\n=== 3. MEMBUAT TABEL upa_mentor_histories ===\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS upa_mentor_histories (
        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        kode_upa VARCHAR(50) NOT NULL,
        pembimbing_id BIGINT UNSIGNED NULL DEFAULT NULL,
        pembimbing_nama VARCHAR(255) NULL DEFAULT NULL,
        tahun_mulai VARCHAR(10) NULL DEFAULT NULL,
        tahun_selesai VARCHAR(10) NULL DEFAULT NULL,
        periode VARCHAR(50) NULL DEFAULT NULL,
        is_current TINYINT(1) NOT NULL DEFAULT 1,
        catatan TEXT NULL DEFAULT NULL,
        created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_kode_upa (kode_upa),
        INDEX idx_pembimbing_id (pembimbing_id),
        INDEX idx_current (kode_upa, is_current)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "- Tabel upa_mentor_histories siap\n";


    echo "\n=== 4. SINKRONISASI DATA EKSISTING ANGGOTA KE member_upa_histories ===\n";
    // Cek apakah member_upa_histories sudah ada isinya
    $countHist = (int)$pdo->query("SELECT COUNT(*) FROM member_upa_histories")->fetchColumn();
    if ($countHist === 0) {
        // Ambil semua member yang punya kode_upa
        $stmtMembers = $pdo->query("
            SELECT m.id, m.nama_lengkap, m.kode_upa, r.nama_group, r.pembimbing_id, r.pembimbing_nama
            FROM members m
            LEFT JOIN regions_upa r ON m.kode_upa = r.kode_upa
            WHERE m.kode_upa IS NOT NULL AND m.kode_upa != ''
        ");
        $membersWithUpa = $stmtMembers->fetchAll();
        
        $insertStmt = $pdo->prepare("
            INSERT INTO member_upa_histories 
            (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, periode, status, is_current, catatan)
            VALUES (?, ?, ?, ?, ?, '2024', '2024 - Sekarang', 'aktif', 1, 'Data awal anggota UPA')
        ");

        $inserted = 0;
        foreach ($membersWithUpa as $row) {
            $insertStmt->execute([
                $row['id'],
                $row['kode_upa'],
                $row['nama_group'] ?: ('Group ' . $row['kode_upa']),
                $row['pembimbing_id'] ?: null,
                $row['pembimbing_nama'] ?: null,
            ]);
            $inserted++;
        }
        echo "- Berhasil menyalin {$inserted} anggota dengan UPA ke member_upa_histories.\n";
    } else {
        echo "- member_upa_histories sudah memiliki {$countHist} data, lewati initial bulk copy.\n";
    }


    echo "\n=== 5. SINKRONISASI PEMBIMBING KE upa_mentor_histories ===\n";
    $countMentorHist = (int)$pdo->query("SELECT COUNT(*) FROM upa_mentor_histories")->fetchColumn();
    if ($countMentorHist === 0) {
        $stmtUpas = $pdo->query("SELECT kode_upa, pembimbing_id, pembimbing_nama FROM regions_upa WHERE pembimbing_id IS NOT NULL");
        $upas = $stmtUpas->fetchAll();
        $insMentor = $pdo->prepare("
            INSERT INTO upa_mentor_histories 
            (kode_upa, pembimbing_id, pembimbing_nama, tahun_mulai, periode, is_current, catatan)
            VALUES (?, ?, ?, '2024', '2024 - Sekarang', 1, 'Naqib Pembimbing Saat Ini')
        ");
        $mCount = 0;
        foreach ($upas as $u) {
            $insMentor->execute([
                $u['kode_upa'],
                $u['pembimbing_id'],
                $u['pembimbing_nama']
            ]);
            $mCount++;
        }
        echo "- Berhasil menyalin {$mCount} pembimbing UPA ke upa_mentor_histories.\n";
    } else {
        echo "- upa_mentor_histories sudah memiliki {$countMentorHist} data.\n";
    }


    echo "\n=== 6. MENYUNTIKKAN DATA CONTOH SPESIFIK USER (VERDIAN FRIMANSYAH) ===\n";
    // Cari Verdian Frimansyah, Tasri, Khairul Wajdi
    $verdian = $pdo->query("SELECT id, nama_lengkap FROM members WHERE nama_lengkap LIKE '%Verdian%' LIMIT 1")->fetch();
    $tasri = $pdo->query("SELECT id, nama_lengkap FROM members WHERE nama_lengkap LIKE '%Tasri%' LIMIT 1")->fetch();
    $khairul = $pdo->query("SELECT id, nama_lengkap FROM members WHERE nama_lengkap LIKE '%Khairul Wajdi%' LIMIT 1")->fetch();

    if ($verdian && $tasri && $khairul) {
        echo "Ditemukan:\n";
        echo "- Verdian ID: {$verdian['id']} ({$verdian['nama_lengkap']})\n";
        echo "- Tasri ID: {$tasri['id']} ({$tasri['nama_lengkap']})\n";
        echo "- Khairul Wajdi ID: {$khairul['id']} ({$khairul['nama_lengkap']})\n";

        // 1. Pastikan UPA PM4106 naqibnya Tasri
        $pdo->prepare("UPDATE regions_upa SET pembimbing_id = ?, pembimbing_nama = ?, nama_group = 'Group PM4106' WHERE kode_upa = 'PM4106'")
            ->execute([$tasri['id'], $tasri['nama_lengkap']]);
        echo "- UPA PM4106 pembimbing diset: {$tasri['nama_lengkap']}\n";

        // 2. Pastikan UPA PM3101 naqibnya Khairul Wajdi
        $pdo->prepare("UPDATE regions_upa SET pembimbing_id = ?, pembimbing_nama = ?, nama_group = 'Group PM3101' WHERE kode_upa = 'PM3101'")
            ->execute([$khairul['id'], $khairul['nama_lengkap']]);
        echo "- UPA PM3101 pembimbing diset: {$khairul['nama_lengkap']}\n";

        // 3. Update member Verdian ke UPA PM4106
        $pdo->prepare("UPDATE members SET kode_upa = 'PM4106' WHERE id = ?")
            ->execute([$verdian['id']]);
        echo "- Member Verdian kode_upa diset ke PM4106\n";

        // 4. Reset & insert histori UPA untuk Verdian sesuai skenario user
        $pdo->prepare("DELETE FROM member_upa_histories WHERE member_id = ?")->execute([$verdian['id']]);
        
        // Histori 1: Masa Lalu (2023 - 2024, Group PM3101, Naqib Khairul Wajdi)
        $pdo->prepare("
            INSERT INTO member_upa_histories 
            (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, tahun_selesai, periode, status, is_current, catatan)
            VALUES (?, 'PM3101', 'Group PM3101', ?, ?, '2023', '2024', '2023 - 2024', 'pindah', 0, 'Mutasi pembinaan ke kelompok PM4106')
        ")->execute([$verdian['id'], $khairul['id'], $khairul['nama_lengkap']]);

        // Histori 2: Saat Ini (2024 - Sekarang, Group PM4106, Naqib Tasri)
        $pdo->prepare("
            INSERT INTO member_upa_histories 
            (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, tahun_selesai, periode, status, is_current, catatan)
            VALUES (?, 'PM4106', 'Group PM4106', ?, ?, '2024', NULL, '2024 - Sekarang', 'aktif', 1, 'Kelompok aktif saat ini')
        ")->execute([$verdian['id'], $tasri['id'], $tasri['nama_lengkap']]);

        echo "- Histori UPA Verdian berhasil disuntikkan: 2023-2024 di PM3101 (Khairul Wajdi) & 2024-Sekarang di PM4106 (Tasri).\n";
    }


    echo "\n=== 7. MENAMBAHKAN MENU GROUP UPA KE settings_menus JIKA BELUM ADA ===\n";
    $menuUpa = $pdo->query("SELECT id FROM settings_menus WHERE url = 'admin/upa'")->fetch();
    if (!$menuUpa) {
        // Ambil ID menu parent Anggota (id = 2)
        $pdo->exec("INSERT INTO settings_menus (parent_id, title, url, sort_order, icon, created_at, updated_at) 
                    VALUES (2, 'Group UPA', 'admin/upa', 4, 'fa-solid fa-people-group', NOW(), NOW())");
        $newMenuId = $pdo->lastInsertId();
        echo "- Menu 'Group UPA' ditambahkan ke settings_menus dengan id: {$newMenuId}\n";

        // Berikan izin ke group yang sama dengan Anggota
        $rolesToInsert = ['admin', 'bph', 'kaderisasi', 'seksi_copy_1784722410', 'seksi_copy_1784722410_copy_1785019369'];
        $insRole = $pdo->prepare("INSERT INTO settings_menu_roles (menu_id, group_name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        foreach ($rolesToInsert as $grp) {
            $insRole->execute([$newMenuId, $grp]);
        }
        echo "- Izin roles untuk menu Group UPA berhasil didaftarkan.\n";
    } else {
        echo "- Menu Group UPA sudah ada di settings_menus (ID: {$menuUpa['id']}).\n";
    }

    echo "\n=== SELESAI DENGAN SUKSES! ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
