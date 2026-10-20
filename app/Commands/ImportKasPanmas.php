<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportKasPanmas extends BaseCommand
{
    protected $group       = 'Keuangan';
    protected $name        = 'keuangan:import-kas';
    protected $description = 'Import data pembukuan kas DPC Panmas dari Excel ke modul keuangan';

    public function run(array $params)
    {
        $db = \Config\Database::connect();

        $defaultPath = ROOTPATH . '_document_project/Kas DPC Panmas - baru (1).xlsx';
        $filePath    = $params[0] ?? $defaultPath;

        if (!file_exists($filePath)) {
            CLI::error("File tidak ditemukan: $filePath");
            return;
        }

        CLI::write("Memulai import kas dari: $filePath", 'yellow');

        // 1. Chart of Accounts
        $accounts = [
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

        $accountMap = [];
        foreach ($accounts as $acc) {
            [$kode, $nama, $tipe, $normal, $deskripsi] = $acc;
            $existing = $db->table('fin_accounts')->where('kode_akun', $kode)->get()->getRowArray();
            if ($existing) {
                $db->table('fin_accounts')->where('id', $existing['id'])->update([
                    'nama_akun'      => $nama,
                    'tipe_akun'      => $tipe,
                    'normal_balance' => $normal,
                    'deskripsi'      => $deskripsi,
                    'is_active'      => 1,
                ]);
                $accountMap[$kode] = (int)$existing['id'];
            } else {
                $db->table('fin_accounts')->insert([
                    'kode_akun'      => $kode,
                    'nama_akun'      => $nama,
                    'tipe_akun'      => $tipe,
                    'normal_balance' => $normal,
                    'deskripsi'      => $deskripsi,
                    'is_active'      => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
                $accountMap[$kode] = (int)$db->insertID();
            }
        }

        // 2. Rekening Setup
        $rekenings = [
            ['Rek 7212', '7212', 'Bank (Rek 7212)', 'bank', '1-002', 67879398.79],
            ['Rek 7137', '7137', 'Bank (Rek 7137)', 'bank', '1-003', 29483261.99],
            ['Rek 79100', '79100', 'Bank (Rek 79100)', 'bank', '1-004', 0.00],
            ['Kas Asep', 'KAS-ASEP', 'Kas Tunai', 'kas', '1-001', 0.00],
            ['Actionpay', 'ACTIONPAY', 'Actionpay Digital', 'kas', '1-005', 0.00],
        ];

        $rekeningMap = [];
        foreach ($rekenings as $rek) {
            [$namaRek, $noRek, $bank, $tipe, $kodeAkun, $saldoAwal] = $rek;
            $akunId = $accountMap[$kodeAkun];
            $existing = $db->table('fin_rekening')->where('nama_rekening', $namaRek)->get()->getRowArray();
            if ($existing) {
                $db->table('fin_rekening')->where('id', $existing['id'])->update([
                    'nomor_rekening' => $noRek,
                    'bank'           => $bank,
                    'tipe'           => $tipe,
                    'akun_id'        => $akunId,
                    'saldo_awal'     => $saldoAwal,
                    'is_active'      => 1,
                ]);
                $rekeningMap[$namaRek] = (int)$existing['id'];
            } else {
                $db->table('fin_rekening')->insert([
                    'nama_rekening'  => $namaRek,
                    'nomor_rekening' => $noRek,
                    'bank'           => $bank,
                    'tipe'           => $tipe,
                    'akun_id'        => $akunId,
                    'saldo_awal'     => $saldoAwal,
                    'is_active'      => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
                $rekeningMap[$namaRek] = (int)$db->insertID();
            }
        }

        // Deactivate old generic Kas Tunai if present
        $db->table('fin_rekening')
            ->where('nama_rekening', 'Kas Tunai')
            ->whereNotIn('id', array_values($rekeningMap))
            ->update(['is_active' => 0]);

        // Reset transactions & journals
        $db->query("DELETE FROM fin_jurnal_detail");
        $db->query("DELETE FROM fin_jurnal");
        $db->query("DELETE FROM fin_transaksi");
        $db->query("ALTER TABLE fin_transaksi AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE fin_jurnal AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE fin_jurnal_detail AUTO_INCREMENT = 1");
        $db->query("ALTER TABLE fin_jurnal_detail MODIFY COLUMN deskripsi TEXT NULL");

        // Parse Excel
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getSheetByName('Utama');

        $accColMapping = [
            'F' => 'Rek 7212',
            'G' => 'Rek 7137',
            'H' => 'Rek 79100',
            'J' => 'Kas Asep',
            'L' => 'Actionpay',
        ];

        $monthCounters = [];
        $totalInserted = 0;
        $lastValidDate = '2025-01-31';

        $db->transStart();

        for ($r = 3; $r <= 663; $r++) {
            $tglRaw   = $sheet->getCell('A' . $r)->getValue();
            $kegiatan = trim((string)$sheet->getCell('B' . $r)->getValue());
            $grup     = trim((string)$sheet->getCell('C' . $r)->getValue());
            $jenis    = trim((string)$sheet->getCell('D' . $r)->getValue());

            if (is_numeric($tglRaw) && $tglRaw > 40000) {
                $tanggal = Date::excelToDateTimeObject($tglRaw)->format('Y-m-d');
                $lastValidDate = $tanggal;
            } else {
                $tanggal = $lastValidDate;
            }

            $ym = date('Ym', strtotime($tanggal));

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

            if (count($pos) === 1 && count($neg) === 1) {
                $fromAcc = array_key_first($neg);
                $fromAmt = $neg[$fromAcc];
                $toAcc   = array_key_first($pos);
                $toAmt   = $pos[$toAcc];

                $fromRekId     = $rekeningMap[$fromAcc];
                $toRekId       = $rekeningMap[$toAcc];
                $fromAkunId    = $accountMap[$rekenings[array_search($fromAcc, array_column($rekenings, 0))][4]];
                $toAkunId      = $accountMap[$rekenings[array_search($toAcc, array_column($rekenings, 0))][4]];
                $kliringAkunId = $accountMap['1-099'];
                $adminAkunId   = $accountMap['5-008'];

                $kat = $this->normalizeCategoryName($jenis ?: 'Transfer', 'pengeluaran');

                // Outflow
                $nomorTrx1 = $this->getNextTrxNumber($monthCounters, $ym);
                $nomorJrn1 = $this->getNextJrnNumber($monthCounters, $ym);
                $desc1     = "Transfer ke $toAcc: $baseDeskripsi";

                $db->table('fin_transaksi')->insert([
                    'nomor_transaksi' => $nomorTrx1,
                    'tanggal'         => $tanggal,
                    'tipe'            => 'pengeluaran',
                    'kategori'        => $kat,
                    'deskripsi'       => $desc1,
                    'jumlah'          => $fromAmt,
                    'rekening_id'     => $fromRekId,
                    'akun_debit_id'   => $kliringAkunId,
                    'akun_kredit_id'  => $fromAkunId,
                    'created_by'      => 1,
                    'status'          => 'posted',
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $trxId1 = $db->insertID();

                $db->table('fin_jurnal')->insert([
                    'nomor_jurnal' => $nomorJrn1,
                    'tanggal'      => $tanggal,
                    'deskripsi'    => $desc1,
                    'transaksi_id' => $trxId1,
                    'created_by'   => 1,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
                $jrnId1 = $db->insertID();

                $fee = $fromAmt - $toAmt;
                if ($fee > 0.001) {
                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId1, 'akun_id' => $kliringAkunId, 'deskripsi' => $desc1, 'debit' => $toAmt, 'kredit' => 0]);
                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId1, 'akun_id' => $adminAkunId, 'deskripsi' => "Biaya admin transfer: $desc1", 'debit' => $fee, 'kredit' => 0]);
                } else {
                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId1, 'akun_id' => $kliringAkunId, 'deskripsi' => $desc1, 'debit' => $fromAmt, 'kredit' => 0]);
                }
                $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId1, 'akun_id' => $fromAkunId, 'deskripsi' => $desc1, 'debit' => 0, 'kredit' => $fromAmt]);
                $totalInserted++;

                // Inflow
                $nomorTrx2 = $this->getNextTrxNumber($monthCounters, $ym);
                $nomorJrn2 = $this->getNextJrnNumber($monthCounters, $ym);
                $desc2     = "Transfer dari $fromAcc: $baseDeskripsi";

                $db->table('fin_transaksi')->insert([
                    'nomor_transaksi' => $nomorTrx2,
                    'tanggal'         => $tanggal,
                    'tipe'            => 'pemasukan',
                    'kategori'        => $kat,
                    'deskripsi'       => $desc2,
                    'jumlah'          => $toAmt,
                    'rekening_id'     => $toRekId,
                    'akun_debit_id'   => $toAkunId,
                    'akun_kredit_id'  => $kliringAkunId,
                    'created_by'      => 1,
                    'status'          => 'posted',
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ]);
                $trxId2 = $db->insertID();

                $db->table('fin_jurnal')->insert([
                    'nomor_jurnal' => $nomorJrn2,
                    'tanggal'      => $tanggal,
                    'deskripsi'    => $desc2,
                    'transaksi_id' => $trxId2,
                    'created_by'   => 1,
                    'created_at'   => date('Y-m-d H:i:s'),
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);
                $jrnId2 = $db->insertID();

                $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId2, 'akun_id' => $toAkunId, 'deskripsi' => $desc2, 'debit' => $toAmt, 'kredit' => 0]);
                $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId2, 'akun_id' => $kliringAkunId, 'deskripsi' => $desc2, 'debit' => 0, 'kredit' => $toAmt]);
                $totalInserted++;
            } else {
                foreach ($pos as $acc => $amt) {
                    $rekId        = $rekeningMap[$acc];
                    $rekAkunId    = $accountMap[$rekenings[array_search($acc, array_column($rekenings, 0))][4]];
                    $contraAkunId = $this->getContraAccountId($jenis, 'pemasukan', $kegiatan, $accountMap);
                    $kat          = $this->normalizeCategoryName($jenis, 'pemasukan');

                    $nomorTrx = $this->getNextTrxNumber($monthCounters, $ym);
                    $nomorJrn = $this->getNextJrnNumber($monthCounters, $ym);

                    $db->table('fin_transaksi')->insert([
                        'nomor_transaksi' => $nomorTrx,
                        'tanggal'         => $tanggal,
                        'tipe'            => 'pemasukan',
                        'kategori'        => $kat,
                        'deskripsi'       => $baseDeskripsi,
                        'jumlah'          => $amt,
                        'rekening_id'     => $rekId,
                        'akun_debit_id'   => $rekAkunId,
                        'akun_kredit_id'  => $contraAkunId,
                        'created_by'      => 1,
                        'status'          => 'posted',
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]);
                    $trxId = $db->insertID();

                    $db->table('fin_jurnal')->insert([
                        'nomor_jurnal' => $nomorJrn,
                        'tanggal'      => $tanggal,
                        'deskripsi'    => $baseDeskripsi,
                        'transaksi_id' => $trxId,
                        'created_by'   => 1,
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);
                    $jrnId = $db->insertID();

                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId, 'akun_id' => $rekAkunId, 'deskripsi' => $baseDeskripsi, 'debit' => $amt, 'kredit' => 0]);
                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId, 'akun_id' => $contraAkunId, 'deskripsi' => $baseDeskripsi, 'debit' => 0, 'kredit' => $amt]);
                    $totalInserted++;
                }

                foreach ($neg as $acc => $amt) {
                    $rekId        = $rekeningMap[$acc];
                    $rekAkunId    = $accountMap[$rekenings[array_search($acc, array_column($rekenings, 0))][4]];
                    $contraAkunId = $this->getContraAccountId($jenis, 'pengeluaran', $kegiatan, $accountMap);
                    $kat          = $this->normalizeCategoryName($jenis, 'pengeluaran');

                    $nomorTrx = $this->getNextTrxNumber($monthCounters, $ym);
                    $nomorJrn = $this->getNextJrnNumber($monthCounters, $ym);

                    $db->table('fin_transaksi')->insert([
                        'nomor_transaksi' => $nomorTrx,
                        'tanggal'         => $tanggal,
                        'tipe'            => 'pengeluaran',
                        'kategori'        => $kat,
                        'deskripsi'       => $baseDeskripsi,
                        'jumlah'          => $amt,
                        'rekening_id'     => $rekId,
                        'akun_debit_id'   => $contraAkunId,
                        'akun_kredit_id'  => $rekAkunId,
                        'created_by'      => 1,
                        'status'          => 'posted',
                        'created_at'      => date('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ]);
                    $trxId = $db->insertID();

                    $db->table('fin_jurnal')->insert([
                        'nomor_jurnal' => $nomorJrn,
                        'tanggal'      => $tanggal,
                        'deskripsi'    => $baseDeskripsi,
                        'transaksi_id' => $trxId,
                        'created_by'   => 1,
                        'created_at'   => date('Y-m-d H:i:s'),
                        'updated_at'   => date('Y-m-d H:i:s'),
                    ]);
                    $jrnId = $db->insertID();

                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId, 'akun_id' => $contraAkunId, 'deskripsi' => $baseDeskripsi, 'debit' => $amt, 'kredit' => 0]);
                    $db->table('fin_jurnal_detail')->insert(['jurnal_id' => $jrnId, 'akun_id' => $rekAkunId, 'deskripsi' => $baseDeskripsi, 'debit' => 0, 'kredit' => $amt]);
                    $totalInserted++;
                }
            }
        }

        $db->transComplete();

        CLI::write("Berhasil mengimpor $totalInserted transaksi dan jurnal!", 'green');
    }

    private function getNextTrxNumber(&$monthCounters, string $ym): string
    {
        if (!isset($monthCounters[$ym])) $monthCounters[$ym] = 0;
        $monthCounters[$ym]++;
        return 'TRX-' . $ym . '-' . str_pad($monthCounters[$ym], 4, '0', STR_PAD_LEFT);
    }

    private function getNextJrnNumber(&$monthCounters, string $ym): string
    {
        return 'JRN-' . $ym . '-' . str_pad($monthCounters[$ym], 4, '0', STR_PAD_LEFT);
    }

    private function getContraAccountId(string $jenis, string $tipe, string $kegiatan, array $accountMap): int
    {
        $j = strtolower(trim($jenis));
        if ($tipe === 'pemasukan') {
            if (str_contains($j, 'iwb')) return $accountMap['4-001'];
            if (str_contains($j, 'tunj ops') || str_contains($j, 'tunjangan ops')) return $accountMap['4-002'];
            if (str_contains($j, 'infaq') || str_contains($j, 'donasi')) return $accountMap['4-003'];
            if (str_contains($j, 'taawun') || str_contains($j, 'itb')) return $accountMap['4-004'];
            if (str_contains($j, 'pinjam') || str_contains($j, 'piutang')) return $accountMap['1-006'];
            if (str_contains($j, 'rekonsil') || str_contains($j, 'topup') || str_contains($j, 'actionpay')) return $accountMap['1-099'];
            if (str_contains($j, 'refund') || str_contains($j, 'reimburse') || str_contains($j, 'salkir') || str_contains($j, 'lebih bayar')) return $accountMap['5-009'];
            return $accountMap['4-005'];
        } else {
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
            return $accountMap['5-010'];
        }
    }

    private function normalizeCategoryName(string $jenis, string $tipe): string
    {
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
}
