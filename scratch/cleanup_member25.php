<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$pdo->exec("UPDATE members SET kode_upa = 'PM4107' WHERE id = 25");
$pdo->exec("DELETE FROM member_upa_histories WHERE member_id = 25");
$pdo->exec("
    INSERT INTO member_upa_histories 
    (member_id, kode_upa, nama_group, pembimbing_id, pembimbing_nama, tahun_mulai, periode, status, is_current, catatan)
    VALUES (25, 'PM4107', 'Group PM4107', NULL, NULL, '2024', '2024 - Sekarang', 'aktif', 1, 'Data awal anggota UPA')
");
echo "Member 25 restored.\n";
