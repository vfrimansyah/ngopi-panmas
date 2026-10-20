<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$tbl1 = $pdo->query("SHOW TABLE STATUS WHERE Name = 'regions_upa'")->fetch();
$tbl2 = $pdo->query("SHOW TABLE STATUS WHERE Name = 'members'")->fetch();
echo "regions_upa collation: " . $tbl1['Collation'] . "\n";
echo "members collation: " . $tbl2['Collation'] . "\n";

// Convert member_upa_histories and upa_mentor_histories to match
$collation = $tbl1['Collation'];
echo "Converting member_upa_histories and upa_mentor_histories to {$collation}...\n";

$pdo->exec("ALTER TABLE member_upa_histories CONVERT TO CHARACTER SET utf8mb4 COLLATE {$collation}");
$pdo->exec("ALTER TABLE upa_mentor_histories CONVERT TO CHARACTER SET utf8mb4 COLLATE {$collation}");
echo "Conversion done!\n";

// Now test join query again
$stmt = $pdo->prepare("
    SELECT h.*, r.nama_group as upa_group_name, r.kelurahan_id, m.nama_lengkap as nama_pembimbing
    FROM member_upa_histories h
    LEFT JOIN regions_upa r ON r.kode_upa = h.kode_upa
    LEFT JOIN members m ON m.id = h.pembimbing_id
    WHERE h.member_id = ?
    ORDER BY h.is_current DESC, h.tahun_mulai DESC, h.id DESC
");
$stmt->execute([49]);
$histories = $stmt->fetchAll();

echo "HISTORIES FOR VERDIAN (ID 49):\n";
print_r($histories);
