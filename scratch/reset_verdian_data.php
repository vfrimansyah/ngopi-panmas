<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// 1. Set member 49 kode_upa to PM4106
$pdo->exec("UPDATE members SET kode_upa = 'PM4106' WHERE id = 49");

// 2. Clear histories for member 49
$pdo->exec("DELETE FROM member_upa_histories WHERE member_id = 49");

// 3. Insert Past History (2023 - 2024 di PM3101 dengan Khairul Wajdi)
$pdo->exec("
    INSERT INTO member_upa_histories 
    (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, tahun_selesai, periode, status, is_current, catatan, created_at, updated_at)
    VALUES (49, 'PM3101', 'Group PM3101', 53, 'Khairul Wajdi', '2023', '2024', '2023 - 2024', 'pindah', 0, 'Mutasi pembinaan ke kelompok PM4106', '2023-01-01 08:00:00', '2024-01-01 08:00:00')
");

// 4. Insert Current History (2024 - Sekarang di PM4106 dengan Tasri)
$pdo->exec("
    INSERT INTO member_upa_histories 
    (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, tahun_selesai, periode, status, is_current, catatan, created_at, updated_at)
    VALUES (49, 'PM4106', 'Group PM4106', 293, 'Tasri', '2024', NULL, '2024 - Sekarang', 'aktif', 1, 'Kelompok aktif saat ini', '2024-01-02 08:00:00', NOW())
");

// Query back and show
$stmt = $pdo->prepare("
    SELECT h.*, r.nama_group as upa_group_name, r.kelurahan_id, m.nama_lengkap as nama_pembimbing
    FROM member_upa_histories h
    LEFT JOIN regions_upa r ON r.kode_upa = h.kode_upa
    LEFT JOIN members m ON m.id = h.pembimbing_id
    WHERE h.member_id = 49
    ORDER BY h.is_current DESC, h.tahun_mulai DESC, h.id DESC
");
$stmt->execute();
print_r($stmt->fetchAll());
echo "Verdian UPA data set successfully!\n";
