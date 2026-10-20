<?php
/**
 * Import Script: Kas DPC Panmas Excel -> Database fin_*
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

$pdo = new PDO('mysql:host=localhost;dbname=ngopipanmas;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo "=== STEP 1: CONFIGURE CHART OF ACCOUNTS (fin_accounts) ===\n";

$accounts = [
    // Kode Akun, Nama Akun, Tipe, Normal Balance, Deskripsi
    ['1-001', 'Kas Asep (Tunai)', 'kas', 'debit', 'Kas fisik yang dipegang Bendahara Asep'],
    ['1-002', 'Rek 7212', 'bank', 'debit', 'Rekening Bank operasional DPC Panmas 7212'],
    ['1-003', 'Rek 7137', 'bank', 'debit', 'Rekening Bank DPC Panmas 7137'],
    ['1-004', 'Rek 79100', 'bank', 'debit', 'Rekening Bank operasional rutin DPC Panmas 79100'],
    ['1-005', 'Actionpay', 'kas', 'debit', 'Akun digital e-wallet Actionpay Panmas'],
    ['1-006', 'Piutang & Talangan', 'piutang', 'debit', 'Piutang, pinjaman, dan dana talangan'],
    ['1-099', 'Kliring Transfer Antar Rekening', 'kas', 'debit', 'Akun perantara kliring mutasi internal antar rekening'],
    ['3-001', 'Modal / Saldo Awal', 'modal', 'kredit', 'Saldo awal ekuitas pembukuan'],
    ['4-001', 'Pendapatan IWB', 'pendapatan', 'kredit', 'Penerimaan Iuran Wajib Bulanan (IWB) kader'],
    ['4-002', 'Bantuan / Tunjangan Operasional DPD', 'pendapatan', 'kredit', 'Penerimaan tunjangan operasional dari DPD'],
    ['4-003', 'Infaq & Donasi', 'pendapatan', 'kredit', 'Penerimaan infaq, shodaqoh, dan donasi'],
    ['4-004', 'Penerimaan Taawun & ITB', 'pendapatan', 'kredit', 'Penerimaan program Taawun dan ITB'],
    ['4-005', 'Pendapatan Lainnya', 'pendapatan', 'kredit', 'Penerimaan lain-lain'],
    ['5-001', 'Beban Operasional & Kantor', 'beban', 'debit', 'Beban operasional rutin sekretariat dan kantor'],
    ['5-002', 'Beban Kegiatan & Acara', 'beban', 'debit', 'Biaya pelaksanaan kegiatan, pelatihan, dan acara'],
    ['5-003', 'Beban Konsumsi', 'beban', 'debit', 'Biaya makan, minum, dan konsumsi rapat / kegiatan'],
    ['5-004', 'Beban Sarana & Prasarana (Infra)', 'beban', 'debit', 'Biaya perlengkapan kantor, infrastruktur, dan sarpras'],
    ['5-005', 'Beban Tunjangan DPRa / BPH', 'beban', 'debit', 'Tunjangan operasional DPRa ranting dan BPH DPC'],
    ['5-006', 'Beban Kafalah, Honor & Tunjangan Aleg', 'beban', 'debit', 'Honorarium, kafalah pengurus/ustadz, tunjangan aleg'],
    ['5-007', 'Beban Penyaluran Taawun & ITB', 'beban', 'debit', 'Penyaluran bantuan Taawun sosial dan ITB'],
    ['5-008', 'Beban Administrasi Bank', 'beban', 'debit', 'Biaya transfer, admin bulanan bank / flip'],
    ['5-009', 'Pengembalian / Refund / Reimburse', 'beban', 'debit', 'Kelebihan bayar, reimburse, atau salah kirim'],
    ['5-010', 'Beban Lain-Lain', 'beban', 'debit', 'Beban tak terduga lainnya'],
];

$accountMap = []; // kode_akun => id

foreach ($accounts as $acc) {
    [$kode, $nama, $tipe, $normal, $deskripsi] = $acc;
    $existing = $pdo->prepare("SELECT id FROM fin_accounts WHERE kode_akun = ?");
    $existing->execute([$kode]);
    $id = $existing->fetchColumn();

    if ($id) {
        $upd = $pdo->prepare("UPDATE fin_accounts SET nama_akun = ?, tipe_akun = ?, normal_balance = ?, deskripsi = ?, is_active = 1 WHERE id = ?");
        $upd->execute([$nama, $tipe, $normal, $deskripsi, $id]);
        $accountMap[$kode] = (int)$id;
    } else {
        $ins = $pdo->prepare("INSERT INTO fin_accounts (kode_akun, nama_akun, tipe_akun, normal_balance, deskripsi, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
        $ins->execute([$kode, $nama, $tipe, $normal, $deskripsi]);
        $accountMap[$kode] = (int)$pdo->lastInsertId();
    }
}
echo "Configured " . count($accountMap) . " accounts in fin_accounts.\n\n";

echo "=== STEP 2: CONFIGURE REKENING (fin_rekening) ===\n";

$rekenings = [
    // Nama Rekening, No Rek, Bank, Tipe, Kode Akun, Saldo Awal
    ['Rek 7212', '7212', 'Bank (Rek 7212)', 'bank', '1-002', 67879398.79],
    ['Rek 7137', '7137', 'Bank (Rek 7137)', 'bank', '1-003', 29483261.99],
    ['Rek 79100', '79100', 'Bank (Rek 79100)', 'bank', '1-004', 0.00],
    ['Kas Asep', 'KAS-ASEP', 'Kas Tunai', 'kas', '1-001', 0.00],
    ['Actionpay', 'ACTIONPAY', 'Actionpay Digital', 'kas', '1-005', 0.00],
];

$rekeningMap = []; // nama_rekening => id

// Deactivate or clean unused initial generic accounts if any
foreach ($rekenings as $rek) {
    [$namaRek, $noRek, $bank, $tipe, $kodeAkun, $saldoAwal] = $rek;
    $akunId = $accountMap[$kodeAkun];

    $existing = $pdo->prepare("SELECT id FROM fin_rekening WHERE nama_rekening = ?");
    $existing->execute([$namaRek]);
    $id = $existing->fetchColumn();

    if ($id) {
        $upd = $pdo->prepare("UPDATE fin_rekening SET nomor_rekening = ?, bank = ?, tipe = ?, akun_id = ?, saldo_awal = ?, is_active = 1 WHERE id = ?");
        $upd->execute([$noRek, $bank, $tipe, $akunId, $saldoAwal, $id]);
        $rekeningMap[$namaRek] = (int)$id;
    } else {
        $ins = $pdo->prepare("INSERT INTO fin_rekening (nama_rekening, nomor_rekening, bank, tipe, akun_id, saldo_awal, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
        $ins->execute([$namaRek, $noRek, $bank, $tipe, $akunId, $saldoAwal]);
        $rekeningMap[$namaRek] = (int)$pdo->lastInsertId();
    }
    echo "  Rekening: $namaRek -> ID {$rekeningMap[$namaRek]} (Saldo Awal: Rp " . number_format($saldoAwal, 2, ',', '.') . ")\n";
}

// Clean old generic 'Kas Tunai' id 1 if not used
$pdo->exec("UPDATE fin_rekening SET is_active = 0 WHERE nama_rekening = 'Kas Tunai' AND id NOT IN (" . implode(',', array_values($rekeningMap)) . ")");

$pdo->exec("DELETE FROM fin_jurnal_detail");
$pdo->exec("DELETE FROM fin_jurnal");
$pdo->exec("DELETE FROM fin_transaksi");
$pdo->exec("ALTER TABLE fin_transaksi AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE fin_jurnal AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE fin_jurnal_detail AUTO_INCREMENT = 1");
$pdo->exec("ALTER TABLE fin_jurnal_detail MODIFY COLUMN deskripsi TEXT NULL");
echo "Cleared tables fin_transaksi, fin_jurnal, fin_jurnal_detail and expanded fin_jurnal_detail.deskripsi.\n\n";

echo "=== STEP 4: PARSE EXCEL AND INSERT TRANSACTIONS ===\n";
$filePath = dirname(__DIR__) . '/_document_project/Kas DPC Panmas - baru (1).xlsx';
$spreadsheet = IOFactory::load($filePath);
$sheet = $spreadsheet->getSheetByName('Utama');

$accColMapping = [
    'F' => 'Rek 7212',
    'G' => 'Rek 7137',
    'H' => 'Rek 79100',
    'J' => 'Kas Asep',
    'L' => 'Actionpay',
];

$monthCounters = []; // YYYYMM => counter

function getNextTrxNumber(&$monthCounters, string $ym): string {
    if (!isset($monthCounters[$ym])) {
        $monthCounters[$ym] = 0;
    }
    $monthCounters[$ym]++;
    return 'TRX-' . $ym . '-' . str_pad($monthCounters[$ym], 4, '0', STR_PAD_LEFT);
}

function getNextJrnNumber(&$monthCounters, string $ym): string {
    // Uses same sequential counter for consistency
    return 'JRN-' . $ym . '-' . str_pad($monthCounters[$ym], 4, '0', STR_PAD_LEFT);
}

// Helper to determine Account IDs based on Jenis & Tipe
function getContraAccountId(string $jenis, string $tipe, string $kegiatan, array $accountMap): int {
    $j = strtolower(trim($jenis));
    $k = strtolower(trim($kegiatan));

    if ($tipe === 'pemasukan') {
        if (str_contains($j, 'iwb')) return $accountMap['4-001'];
        if (str_contains($j, 'tunj ops') || str_contains($j, 'tunjangan ops')) return $accountMap['4-002'];
        if (str_contains($j, 'infaq') || str_contains($j, 'donasi')) return $accountMap['4-003'];
        if (str_contains($j, 'taawun') || str_contains($j, 'itb')) return $accountMap['4-004'];
        if (str_contains($j, 'pinjam') || str_contains($j, 'piutang')) return $accountMap['1-006'];
        if (str_contains($j, 'rekonsil') || str_contains($j, 'topup') || str_contains($j, 'actionpay')) return $accountMap['1-099'];
        if (str_contains($j, 'refund') || str_contains($j, 'reimburse') || str_contains($j, 'salkir') || str_contains($j, 'lebih bayar')) return $accountMap['5-009'];
        return $accountMap['4-005']; // Pendapatan Lainnya
    } else {
        // pengeluaran
        if (str_contains($j, 'konsumsi') || str_contains($j, 'makan')) return $accountMap['5-003'];
        if (str_contains($j, 'kegiatan') || str_contains($j, 'giat') || str_contains($j, 'rakercab') || str_contains($j, 'halbil') || str_contains($j, 'pelatihan') || str_contains($j, 'bedah buku') || str_contains($j, 'apresiasi') || str_contains($j, 'hadiah')) return $accountMap['5-002'];
        if (str_contains($j, 'infra')) return $accountMap['5-004'];
        if (str_contains($j, 'tunj ops') || str_contains($j, 'tunjangan ops') || str_contains($j, 'dpra')) return $accountMap['5-005'];
        if (str_contains($j, 'kafalah') || str_contains($j, 'tunj aleg') || str_contains($j, 'ramadhan')) return $accountMap['5-006'];
        if (str_contains($j, 'taawun') || str_contains($j, 'itb')) return $accountMap['5-007'];
        if (str_contains($j, 'admin') || str_contains($j, 'bank')) return $accountMap['5-008'];
        if (str_contains($j, 'pinjam') || str_contains($j, 'piutang') || str_contains($j, 'talangan')) return $accountMap['1-006'];
        if (str_contains($j, 'rekonsil') || str_contains($j, 'topup') || str_contains($j, 'actionpay')) return $accountMap['1-099'];
        if (str_contains($j, 'refund') || str_contains($j, 'reimburse') || str_contains($j, 'salkir') || str_contains($j, 'lebih bayar')) return $accountMap['5-009'];
        if (str_contains($j, 'ops')) return $accountMap['5-001'];
        return $accountMap['5-010']; // Beban Lain-Lain
    }
}

function normalizeCategoryName(string $jenis, string $tipe): string {
    $j = trim($jenis);
    if (empty($j)) return $tipe === 'pemasukan' ? 'Penerimaan' : 'Pengeluaran';
    $jl = strtolower($j);
    if (str_contains($jl, 'iwb')) return 'IWB';
    if (str_contains($jl, 'konsumsi') || str_contains($jl, 'makan')) return 'Konsumsi';
    if (str_contains($jl, 'infra')) return 'Infra';
    if (str_contains($jl, 'tunj ops') || str_contains($jl, 'tunjangan ops')) return 'Tunjangan Ops';
    if (str_contains($jl, 'kegiatan') || str_contains($jl, 'giat')) return 'Kegiatan';
    if (str_contains($jl, 'rekonsil')) return 'Rekonsiliasi';
    if (str_contains($jl, 'topup') || str_contains($jl, 'actionpay')) return 'Topup E-Wallet';
    if (str_contains($jl, 'kafalah')) return 'Kafalah';
    if (str_contains($jl, 'tunj aleg')) return 'Tunjangan Aleg';
    if (str_contains($jl, 'taawun')) return 'Taawun';
    if (str_contains($jl, 'itb')) return 'ITB';
    if (str_contains($jl, 'pinjam') || str_contains($jl, 'talangan')) return 'Pinjaman / Talangan';
    if (str_contains($jl, 'infaq') || str_contains($jl, 'donasi')) return 'Infaq & Donasi';
    if (str_contains($jl, 'admin') || str_contains($jl, 'bank')) return 'Admin Bank';
    return ucfirst($j);
}

// Prepared statements for insertion
$stmtTrx = $pdo->prepare("
    INSERT INTO fin_transaksi (
        nomor_transaksi, tanggal, tipe, kategori, deskripsi, jumlah,
        rekening_id, akun_debit_id, akun_kredit_id, created_by, status,
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'posted', NOW(), NOW())
");

$stmtJrn = $pdo->prepare("
    INSERT INTO fin_jurnal (
        nomor_jurnal, tanggal, deskripsi, transaksi_id, created_by,
        created_at, updated_at
    ) VALUES (?, ?, ?, ?, 1, NOW(), NOW())
");

$stmtJrnDetail = $pdo->prepare("
    INSERT INTO fin_jurnal_detail (
        jurnal_id, akun_id, deskripsi, debit, kredit
    ) VALUES (?, ?, ?, ?, ?)
");

$totalInsertedTrx = 0;
$lastValidDate = '2025-01-31';

$pdo->beginTransaction();

for ($r = 3; $r <= 663; $r++) {
    $tglRaw = $sheet->getCell('A' . $r)->getValue();
    $kegiatan = trim((string)$sheet->getCell('B' . $r)->getValue());
    $grup = trim((string)$sheet->getCell('C' . $r)->getValue());
    $jenis = trim((string)$sheet->getCell('D' . $r)->getValue());
    $beaAdmin = (float)$sheet->getCell('I' . $r)->getCalculatedValue();

    // Parse date
    if (is_numeric($tglRaw) && $tglRaw > 40000) {
        $tanggal = Date::excelToDateTimeObject($tglRaw)->format('Y-m-d');
        $lastValidDate = $tanggal;
    } else {
        $tanggal = $lastValidDate;
    }

    $ym = date('Ym', strtotime($tanggal));

    // Read calculated account amounts
    $rowAccs = [];
    foreach ($accColMapping as $col => $name) {
        $val = $sheet->getCell($col . $r)->getCalculatedValue();
        if (is_numeric($val) && abs((float)$val) > 0.0001) {
            $rowAccs[$name] = (float)$val;
        }
    }

    if (empty($rowAccs)) continue;

    $pos = [];
    $neg = [];
    foreach ($rowAccs as $a => $v) {
        if ($v > 0) $pos[$a] = $v;
        else $neg[$a] = abs($v);
    }

    $baseDeskripsi = $kegiatan !== '' ? $kegiatan : ($jenis !== '' ? $jenis : 'Transaksi Kas');
    if ($grup !== '') $baseDeskripsi .= " [Grup: $grup]";
    $baseDeskripsi .= " (Excel #$r)";

    // Pattern 1: Pure or Fee-adjusted Internal Transfer
    if (count($pos) === 1 && count($neg) === 1) {
        $fromAcc = array_key_first($neg);
        $fromAmt = $neg[$fromAcc];
        $toAcc = array_key_first($pos);
        $toAmt = $pos[$toAcc];

        $fromRekId = $rekeningMap[$fromAcc];
        $toRekId = $rekeningMap[$toAcc];
        $fromAkunId = $accountMap[$rekenings[array_search($fromAcc, array_column($rekenings, 0))][4]];
        $toAkunId = $accountMap[$rekenings[array_search($toAcc, array_column($rekenings, 0))][4]];
        $kliringAkunId = $accountMap['1-099'];
        $adminAkunId = $accountMap['5-008'];

        $kat = normalizeCategoryName($jenis ?: 'Transfer', 'pengeluaran');

        // 1. Outflow transaction on source account
        $nomorTrx1 = getNextTrxNumber($monthCounters, $ym);
        $nomorJrn1 = getNextJrnNumber($monthCounters, $ym);
        $desc1 = "Transfer ke $toAcc: $baseDeskripsi";

        $stmtTrx->execute([
            $nomorTrx1, $tanggal, 'pengeluaran', $kat, $desc1, $fromAmt,
            $fromRekId, $kliringAkunId, $fromAkunId
        ]);
        $trxId1 = $pdo->lastInsertId();

        $stmtJrn->execute([$nomorJrn1, $tanggal, $desc1, $trxId1]);
        $jrnId1 = $pdo->lastInsertId();

        // Journal details for outflow:
        // If there is an admin fee difference (e.g. fromAmt > toAmt)
        $fee = $fromAmt - $toAmt;
        if ($fee > 0.001) {
            $stmtJrnDetail->execute([$jrnId1, $kliringAkunId, $desc1, $toAmt, 0]);
            $stmtJrnDetail->execute([$jrnId1, $adminAkunId, "Biaya admin transfer: $desc1", $fee, 0]);
        } else {
            $stmtJrnDetail->execute([$jrnId1, $kliringAkunId, $desc1, $fromAmt, 0]);
        }
        $stmtJrnDetail->execute([$jrnId1, $fromAkunId, $desc1, 0, $fromAmt]);
        $totalInsertedTrx++;

        // 2. Inflow transaction on destination account
        $nomorTrx2 = getNextTrxNumber($monthCounters, $ym);
        $nomorJrn2 = getNextJrnNumber($monthCounters, $ym);
        $desc2 = "Transfer dari $fromAcc: $baseDeskripsi";

        $stmtTrx->execute([
            $nomorTrx2, $tanggal, 'pemasukan', $kat, $desc2, $toAmt,
            $toRekId, $toAkunId, $kliringAkunId
        ]);
        $trxId2 = $pdo->lastInsertId();

        $stmtJrn->execute([$nomorJrn2, $tanggal, $desc2, $trxId2]);
        $jrnId2 = $pdo->lastInsertId();

        $stmtJrnDetail->execute([$jrnId2, $toAkunId, $desc2, $toAmt, 0]);
        $stmtJrnDetail->execute([$jrnId2, $kliringAkunId, $desc2, 0, $toAmt]);
        $totalInsertedTrx++;

    } else {
        // Pattern 2: Standard Incomes and Expenses (including multi-account rows)
        foreach ($pos as $acc => $amt) {
            $rekId = $rekeningMap[$acc];
            $rekAkunId = $accountMap[$rekenings[array_search($acc, array_column($rekenings, 0))][4]];
            $contraAkunId = getContraAccountId($jenis, 'pemasukan', $kegiatan, $accountMap);
            $kat = normalizeCategoryName($jenis, 'pemasukan');

            $nomorTrx = getNextTrxNumber($monthCounters, $ym);
            $nomorJrn = getNextJrnNumber($monthCounters, $ym);

            $stmtTrx->execute([
                $nomorTrx, $tanggal, 'pemasukan', $kat, $baseDeskripsi, $amt,
                $rekId, $rekAkunId, $contraAkunId
            ]);
            $trxId = $pdo->lastInsertId();

            $stmtJrn->execute([$nomorJrn, $tanggal, $baseDeskripsi, $trxId]);
            $jrnId = $pdo->lastInsertId();

            $stmtJrnDetail->execute([$jrnId, $rekAkunId, $baseDeskripsi, $amt, 0]);
            $stmtJrnDetail->execute([$jrnId, $contraAkunId, $baseDeskripsi, 0, $amt]);
            $totalInsertedTrx++;
        }

        foreach ($neg as $acc => $amt) {
            $rekId = $rekeningMap[$acc];
            $rekAkunId = $accountMap[$rekenings[array_search($acc, array_column($rekenings, 0))][4]];
            $contraAkunId = getContraAccountId($jenis, 'pengeluaran', $kegiatan, $accountMap);
            $kat = normalizeCategoryName($jenis, 'pengeluaran');

            $nomorTrx = getNextTrxNumber($monthCounters, $ym);
            $nomorJrn = getNextJrnNumber($monthCounters, $ym);

            $stmtTrx->execute([
                $nomorTrx, $tanggal, 'pengeluaran', $kat, $baseDeskripsi, $amt,
                $rekId, $contraAkunId, $rekAkunId
            ]);
            $trxId = $pdo->lastInsertId();

            $stmtJrn->execute([$nomorJrn, $tanggal, $baseDeskripsi, $trxId]);
            $jrnId = $pdo->lastInsertId();

            $stmtJrnDetail->execute([$jrnId, $contraAkunId, $baseDeskripsi, $amt, 0]);
            $stmtJrnDetail->execute([$jrnId, $rekAkunId, $baseDeskripsi, 0, $amt]);
            $totalInsertedTrx++;
        }
    }
}

$pdo->commit();

echo "Successfully imported $totalInsertedTrx transactions into fin_transaksi and double-entry journals into fin_jurnal & fin_jurnal_detail!\n\n";

echo "=== STEP 5: VERIFY DATABASE BALANCES & TOTALS ===\n";

$rekeningResults = $pdo->query("
    SELECT r.id, r.nama_rekening, r.saldo_awal,
        COALESCE(SUM(CASE WHEN t.tipe = 'pemasukan' THEN t.jumlah ELSE 0 END), 0) AS total_masuk,
        COALESCE(SUM(CASE WHEN t.tipe = 'pengeluaran' THEN t.jumlah ELSE 0 END), 0) AS total_keluar
    FROM fin_rekening r
    LEFT JOIN fin_transaksi t ON t.rekening_id = r.id AND t.status = 'posted'
    WHERE r.is_active = 1
    GROUP BY r.id, r.nama_rekening, r.saldo_awal
    ORDER BY r.id
")->fetchAll();

$totalGrand = 0;
foreach ($rekeningResults as $res) {
    $saldoAkhir = (float)$res['saldo_awal'] + (float)$res['total_masuk'] - (float)$res['total_keluar'];
    $totalGrand += $saldoAkhir;
    echo "Rekening: {$res['nama_rekening']}\n";
    echo "  Saldo Awal  : Rp " . number_format($res['saldo_awal'], 2, ',', '.') . "\n";
    echo "  Pemasukan   : Rp " . number_format($res['total_masuk'], 2, ',', '.') . "\n";
    echo "  Pengeluaran : Rp " . number_format($res['total_keluar'], 2, ',', '.') . "\n";
    echo "  Saldo Akhir : Rp " . number_format($saldoAkhir, 2, ',', '.') . "\n\n";
}
echo "Grand Total Saldo Semua Rekening: Rp " . number_format($totalGrand, 2, ',', '.') . "\n\n";

// Verify Journal Balance (Debit vs Kredit)
$jrnTotals = $pdo->query("
    SELECT 
        SUM(debit) AS total_debit, 
        SUM(kredit) AS total_kredit,
        SUM(debit) - SUM(kredit) AS selisih
    FROM fin_jurnal_detail
")->fetch();

echo "=== JOURNAL DOUBLE-ENTRY BALANCE VERIFICATION ===\n";
echo "Total Debit  : Rp " . number_format($jrnTotals['total_debit'], 2, ',', '.') . "\n";
echo "Total Kredit : Rp " . number_format($jrnTotals['total_kredit'], 2, ',', '.') . "\n";
echo "Selisih      : Rp " . number_format($jrnTotals['selisih'], 2, ',', '.') . "\n";
if (abs((float)$jrnTotals['selisih']) < 0.001) {
    echo "Status       : PERFECTLY BALANCED (SEIMBANG)\n";
} else {
    echo "Status       : UNBALANCED WARNING!\n";
}
