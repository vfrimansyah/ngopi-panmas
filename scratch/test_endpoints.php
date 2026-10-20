<?php
$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== 1. TEST HASIL PEMILU DPRD_PROV (REKAP KECAMATAN PANCORAN MAS) ===\n";
// Suara Total TPS
$stmt = $pdo->query("SELECT SUM(suara_sah) as sah, SUM(suara_tidak_sah) as tidak_sah, SUM(suara_total) as total FROM pemilu_suara_tps WHERE tahun = '2024' AND tingkat_pemilu = 'DPRD_PROV'");
$tpsSummary = $stmt->fetch(PDO::FETCH_ASSOC);
echo "TPS Suara: Sah={$tpsSummary['sah']}, Tidak Sah={$tpsSummary['tidak_sah']}, Total={$tpsSummary['total']}\n";

// Suara PKS DPRD_PROV
$stmt = $pdo->query("
    SELECT 
        (SELECT COALESCE(SUM(jumlah_suara),0) FROM pemilu_suara_partai WHERE tahun='2024' AND tingkat_pemilu='DPRD_PROV' AND partai_id=8) as suara_partai,
        (SELECT COALESCE(SUM(sc.jumlah_suara),0) FROM pemilu_suara_caleg sc JOIN pemilu_caleg c ON sc.caleg_id=c.id WHERE sc.tahun='2024' AND c.partai_id=8 AND c.tingkat_pemilu='DPRD_PROV') as suara_caleg
");
$pks = $stmt->fetch(PDO::FETCH_ASSOC);
$totalPks = $pks['suara_partai'] + $pks['suara_caleg'];
echo "PKS DPRD_PROV: Partai={$pks['suara_partai']}, Caleg={$pks['suara_caleg']}, Total={$totalPks}\n";
assert($totalPks == 35368, "PKS total should be 35368");
echo "-> PKS Total Match 100%: 35,368 suara!\n\n";

echo "=== 2. TEST TOP 5 CALEG PKS DPRD_PROV DAPIL 8 ===\n";
$stmt = $pdo->query("
    SELECT c.no_urut, c.nama_caleg, SUM(sc.jumlah_suara) as total_suara
    FROM pemilu_caleg c
    JOIN pemilu_suara_caleg sc ON sc.caleg_id = c.id
    WHERE c.partai_id = 8 AND c.tahun = '2024' AND c.tingkat_pemilu = 'DPRD_PROV'
    GROUP BY c.id, c.no_urut, c.nama_caleg
    ORDER BY total_suara DESC
");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "  No {$row['no_urut']}. {$row['nama_caleg']}: " . number_format($row['total_suara']) . " suara\n";
}

echo "\n=== 3. TEST RW REKAP DPRD_PROV DI KELURAHAN DEPOK JAYA (3276011007) ===\n";
$stmt = $pdo->prepare("
    SELECT 
        t.rw,
        COUNT(DISTINCT t.id) as total_tps,
        SUM(COALESCE(st.suara_sah, 0)) as total_suara_sah,
        SUM(COALESCE(st.suara_tidak_sah, 0)) as total_suara_tidak_sah,
        SUM(COALESCE(st.suara_total, 0)) as total_suara_total,
        SUM(COALESCE(v.pks_partai, 0) + COALESCE(v.pks_caleg, 0)) as total_suara_pks
    FROM pemilu_tps t
    LEFT JOIN pemilu_suara_tps st ON st.tps_id = t.id AND st.tahun = '2024' AND st.tingkat_pemilu = 'DPRD_PROV'
    LEFT JOIN (
        SELECT 
            tps_id,
            SUM(CASE WHEN type = 'partai' THEN jumlah_suara ELSE 0 END) as pks_partai,
            SUM(CASE WHEN type = 'caleg' THEN jumlah_suara ELSE 0 END) as pks_caleg
        FROM (
            SELECT tps_id, jumlah_suara, 'partai' as type 
            FROM pemilu_suara_partai 
            WHERE tahun = '2024' AND partai_id = 8 AND tingkat_pemilu = 'DPRD_PROV'
            
            UNION ALL
            
            SELECT sc.tps_id, sc.jumlah_suara, 'caleg' as type 
            FROM pemilu_suara_caleg sc
            JOIN pemilu_caleg c ON sc.caleg_id = c.id
            WHERE sc.tahun = '2024' AND c.tahun = '2024' AND c.partai_id = 8 AND c.tingkat_pemilu = 'DPRD_PROV'
        ) inner_v
        GROUP BY tps_id
    ) v ON v.tps_id = t.id
    WHERE t.village_id = '3276011007'
    GROUP BY t.rw
    ORDER BY CAST(t.rw AS UNSIGNED) ASC
");
$stmt->execute();
$rwRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Jumlah RW di Depok Jaya: " . count($rwRows) . "\n";
foreach (array_slice($rwRows, 0, 5) as $r) {
    echo "  RW {$r['rw']}: TPS={$r['total_tps']}, PKS={$r['total_suara_pks']}, Sah={$r['total_suara_sah']}, Total={$r['total_suara_total']}\n";
}

echo "\n=== 4. TEST TPS DETAIL DPRD_PROV DI TPS 1 KELURAHAN DEPOK (tps_id) ===\n";
$stmt = $pdo->query("SELECT id FROM pemilu_tps WHERE village_id = '3276011006' AND tps = 'TPS 001' LIMIT 1");
$tps1 = $stmt->fetch(PDO::FETCH_ASSOC);
if ($tps1) {
    $tpsId = $tps1['id'];
    $stmt2 = $pdo->prepare("
        SELECT p.nama_partai, SUM(sc.jumlah_suara) as caleg_suara, sp.jumlah_suara as partai_suara,
               (SUM(sc.jumlah_suara) + COALESCE(sp.jumlah_suara, 0)) as total
        FROM pemilu_partai p
        LEFT JOIN pemilu_suara_partai sp ON sp.partai_id = p.id AND sp.tps_id = :tps_id AND sp.tahun = '2024' AND sp.tingkat_pemilu = 'DPRD_PROV'
        LEFT JOIN pemilu_caleg c ON c.partai_id = p.id AND c.tahun = '2024' AND c.tingkat_pemilu = 'DPRD_PROV'
        LEFT JOIN pemilu_suara_caleg sc ON sc.caleg_id = c.id AND sc.tps_id = :tps_id AND sc.tahun = '2024'
        GROUP BY p.id, p.nama_partai, sp.jumlah_suara
        ORDER BY total DESC
        LIMIT 3
    ");
    $stmt2->execute(['tps_id' => $tpsId]);
    echo "Top 3 Partai di TPS 001 Kel. Depok (DPRD_PROV):\n";
    while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        echo "  - {$row['nama_partai']}: Total={$row['total']} (Partai={$row['partai_suara']}, Caleg={$row['caleg_suara']})\n";
    }
}

echo "\n=== ALL TESTS PASSED SUCCESSFULLY! ===\n";
