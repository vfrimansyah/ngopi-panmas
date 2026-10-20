<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check the import_staging table structure
    echo "=== IMPORT_STAGING COLUMNS ===\n";
    $stmt = $pdo->query("DESCRIBE import_staging");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['Field']} | {$c['Type']} | {$c['Null']}\n";
    }

    // Check the members table structure (key fields)
    echo "\n=== MEMBERS TABLE KEY COLUMNS ===\n";
    $stmt = $pdo->query("DESCRIBE members");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        if (in_array($c['Field'], ['id','nama_lengkap','jenis_kelamin','status_perkawinan','spouse_member_id','spouse_pairing_status','father_member_id','mother_member_id','account_status','tanggal_lahir','status_anggota'])) {
            echo "{$c['Field']} | {$c['Type']} | Default: {$c['Default']}\n";
        }
    }

    // Check what col_d maps to (it should be gender of the PRIMARY respondent based on mapping)
    echo "\n=== STAGING ROW SAMPLE: col_a to col_j ===\n";
    $stmt = $pdo->query("SELECT col_a, col_b, col_c, col_d, col_e, col_f, col_g, col_h, col_i, col_j FROM import_staging LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        print_r($r);
    }

    // Check which col stores the gender of primary respondent
    echo "\n=== CHECKING GENDER COLUMN (col_d or col_e or col_f) ===\n";
    $stmt = $pdo->query("SELECT DISTINCT col_d FROM import_staging WHERE col_d IS NOT NULL AND col_d != '' LIMIT 10");
    echo "col_d values: ";
    $vals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(', ', $vals) . "\n";

    $stmt = $pdo->query("SELECT DISTINCT col_e FROM import_staging WHERE col_e IS NOT NULL AND col_e != '' LIMIT 10");
    echo "col_e values: ";
    $vals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(', ', $vals) . "\n";

    $stmt = $pdo->query("SELECT DISTINCT col_f FROM import_staging WHERE col_f IS NOT NULL AND col_f != '' LIMIT 10");
    echo "col_f values: ";
    $vals = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo implode(', ', $vals) . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
