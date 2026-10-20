<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '');
$stmt = $pdo->query('SHOW TABLES LIKE "%upa%"');
echo "TABLES:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $pdo->query('DESCRIBE regions_upa');
echo "DESCRIBE regions_upa:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));

$stmt3 = $pdo->query("SELECT id, nama_lengkap, kode_upa, status_anggota FROM members WHERE nama_lengkap LIKE '%Verdian%' OR nama_lengkap LIKE '%Tasri%' OR nama_lengkap LIKE '%Khairul%'");
echo "MEMBERS FOUND:\n";
print_r($stmt3->fetchAll(PDO::FETCH_ASSOC));

$stmt4 = $pdo->query("SELECT kode_upa, nama_group, pembimbing_id, pembimbing_nama FROM regions_upa WHERE kode_upa IN ('PM4106', 'PM3101')");
if ($stmt4) {
    echo "UPA FOUND:\n";
    print_r($stmt4->fetchAll(PDO::FETCH_ASSOC));
} else {
    echo "Query regions_upa failed (maybe columns missing?)\n";
    $stmt5 = $pdo->query("SELECT * FROM regions_upa WHERE kode_upa IN ('PM4106', 'PM3101')");
    print_r($stmt5->fetchAll(PDO::FETCH_ASSOC));
}
