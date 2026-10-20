<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '');
echo "=== cms_region04_villages sample ===\n";
print_r($pdo->query("SELECT id, name, district_id FROM cms_region04_villages WHERE name LIKE '%Pancoran%' OR name LIKE '%Mampang%' OR name LIKE '%Depok%' LIMIT 6")->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== Distinct pekerjaan sample ===\n";
print_r($pdo->query("SELECT DISTINCT pekerjaan FROM members WHERE pekerjaan IS NOT NULL AND pekerjaan != '' AND pekerjaan != '-' ORDER BY pekerjaan ASC LIMIT 10")->fetchAll(PDO::FETCH_COLUMN));
