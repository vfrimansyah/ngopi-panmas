<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// 1. Update members table
$pdo->exec("UPDATE members SET kode_upa = 'PM4106' WHERE id = 49");

// 2. Clear histories for member 49
$pdo->exec("DELETE FROM member_upa_histories WHERE member_id = 49");

// 3. Insert Past History: PM3101 (2023 - 2024)
$pdo->exec("
    INSERT INTO member_upa_histories 
    (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, tahun_selesai, periode, status, is_current, catatan, created_at, updated_at)
    VALUES (49, 'PM3101', 'Group PM3101', 53, 'Khairul Wajdi', '2023', '2024', '2023 - 2024', 'pindah', 0, 'Mutasi pembinaan ke kelompok PM4106', '2023-01-01 08:00:00', '2024-01-01 08:00:00')
");

// 4. Insert Current History: PM4106 (2024 - Sekarang)
$pdo->exec("
    INSERT INTO member_upa_histories 
    (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, tahun_selesai, periode, status, is_current, catatan, created_at, updated_at)
    VALUES (49, 'PM4106', 'Group PM4106', 293, 'Tasri', '2024', NULL, '2024 - Sekarang', 'aktif', 1, 'Kelompok aktif saat ini', '2024-01-02 08:00:00', NOW())
");

$v = $pdo->query('SELECT id, nama_lengkap, kode_upa FROM members WHERE id = 49')->fetch();
echo "Updated Verdian: {$v['nama_lengkap']} -> {$v['kode_upa']}\n";

$hist = $pdo->query('SELECT kode_upa, pembimbing_nama, periode, status, is_current FROM member_upa_histories WHERE member_id = 49 ORDER BY is_current DESC, tahun_mulai DESC')->fetchAll();
print_r($hist);
