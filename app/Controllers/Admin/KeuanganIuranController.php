<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MemberIuranModel;
use App\Models\RegionUpaModel;

class KeuanganIuranController extends BaseController
{
    protected $iuranModel;
    protected $upaModel;

    public function __construct()
    {
        $this->iuranModel = new MemberIuranModel();
        $this->upaModel   = new RegionUpaModel();
    }

    public function index()
    {
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getGet('bulan') ?? date('n'));

        $filters = [
            'search'            => trim((string) $this->request->getGet('search')),
            'kode_upa'          => trim((string) $this->request->getGet('kode_upa')),
            'status_anggota'    => trim((string) $this->request->getGet('status_anggota')),
            'status_pembayaran' => trim((string) $this->request->getGet('status_pembayaran')),
        ];

        $report  = $this->iuranModel->getMonthlyReport($tahun, $bulan, $filters);
        $summary = $this->iuranModel->getSummary($tahun, $bulan, $filters);

        // Fetch list of UPAs for dropdown filter
        $upaList = $this->upaModel->orderBy('kode_upa', 'ASC')->findAll();

        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('admin/keuangan/iuran/index', [
            'title'      => 'Status Iuran Anggota',
            'tahun'      => $tahun,
            'bulan'      => $bulan,
            'bulanNames' => $bulanNames,
            'filters'    => $filters,
            'report'     => $report,
            'summary'    => $summary,
            'upaList'    => $upaList,
        ]);
    }

    public function update()
    {
        $memberId = (int) $this->request->getPost('member_id');
        $tahun    = (int) $this->request->getPost('tahun');
        $bulan    = (int) $this->request->getPost('bulan');

        if (!$memberId || !$tahun || !$bulan) {
            return redirect()->back()->with('error', 'Data tidak lengkap.');
        }

        $data = [
            'status_itb'    => $this->request->getPost('status_itb') === 'lunas' ? 'lunas' : 'belum',
            'nominal_itb'   => (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('nominal_itb') ?? 0),
            'status_iwb'    => $this->request->getPost('status_iwb') === 'lunas' ? 'lunas' : 'belum',
            'nominal_iwb'   => (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('nominal_iwb') ?? 0),
            'status_infaq'  => $this->request->getPost('status_infaq') === 'lunas' ? 'lunas' : 'belum',
            'nominal_infaq' => (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('nominal_infaq') ?? 0),
            'tanggal_bayar' => $this->request->getPost('tanggal_bayar') ?: date('Y-m-d'),
            'catatan'       => trim((string) $this->request->getPost('catatan')),
        ];

        $success = $this->iuranModel->saveOrUpdatePayment($memberId, $tahun, $bulan, $data);

        if ($success) {
            return redirect()->back()->with('success', 'Status iuran berhasil diperbarui.');
        }

        return redirect()->back()->with('error', 'Gagal memperbarui status iuran.');
    }

    public function bulkUpdate()
    {
        $memberIds = $this->request->getPost('member_ids');
        $tahun     = (int) $this->request->getPost('tahun');
        $bulan     = (int) $this->request->getPost('bulan');
        $action    = $this->request->getPost('bulk_action');

        if (empty($memberIds) || !is_array($memberIds) || !$tahun || !$bulan || !$action) {
            return redirect()->back()->with('error', 'Pilih minimal satu anggota dan aksi bulk yang valid.');
        }

        $updatedCount = 0;
        foreach ($memberIds as $id) {
            $memberId = (int) $id;
            if (!$memberId) continue;

            $existing = $this->iuranModel->where('member_id', $memberId)
                                          ->where('tahun', $tahun)
                                          ->where('bulan', $bulan)
                                          ->first();

            $data = [
                'status_itb'    => $existing['status_itb'] ?? 'belum',
                'nominal_itb'   => $existing['nominal_itb'] ?? 0,
                'status_iwb'    => $existing['status_iwb'] ?? 'belum',
                'nominal_iwb'   => $existing['nominal_iwb'] ?? 0,
                'status_infaq'  => $existing['status_infaq'] ?? 'belum',
                'nominal_infaq' => $existing['nominal_infaq'] ?? 0,
                'tanggal_bayar' => date('Y-m-d'),
                'catatan'       => $existing['catatan'] ?? null,
            ];

            if ($action === 'itb_lunas') {
                $data['status_itb'] = 'lunas';
            } elseif ($action === 'iwb_lunas') {
                $data['status_iwb'] = 'lunas';
            } elseif ($action === 'infaq_lunas') {
                $data['status_infaq'] = 'lunas';
            } elseif ($action === 'semua_lunas') {
                $data['status_itb']   = 'lunas';
                $data['status_iwb']   = 'lunas';
                $data['status_infaq'] = 'lunas';
            }

            if ($this->iuranModel->saveOrUpdatePayment($memberId, $tahun, $bulan, $data)) {
                $updatedCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil memperbarui $updatedCount data anggota.");
    }

    public function export()
    {
        $tahun = (int) ($this->request->getGet('tahun') ?? date('Y'));
        $bulan = (int) ($this->request->getGet('bulan') ?? date('n'));

        $filters = [
            'search'            => trim((string) $this->request->getGet('search')),
            'kode_upa'          => trim((string) $this->request->getGet('kode_upa')),
            'status_anggota'    => trim((string) $this->request->getGet('status_anggota')),
            'status_pembayaran' => trim((string) $this->request->getGet('status_pembayaran')),
        ];

        $report = $this->iuranModel->getMonthlyReport($tahun, $bulan, $filters);

        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $filename = "Laporan_Iuran_Anggota_{$bulanNames[$bulan]}_{$tahun}.csv";

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fputs($output, $bom = chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($output, [
            'No',
            'Nama Anggota',
            'Jenjang',
            'UPA',
            'Status ITB',
            'Nominal ITB',
            'Status IWB',
            'Nominal IWB',
            'Status Infaq DPC',
            'Nominal Infaq DPC',
            'Total Bayar',
            'Tanggal Bayar',
            'Catatan'
        ]);

        $no = 1;
        foreach ($report as $row) {
            $total = (float)$row['nominal_itb'] + (float)$row['nominal_iwb'] + (float)$row['nominal_infaq'];
            fputcsv($output, [
                $no++,
                $row['nama_lengkap'],
                $row['status_anggota'] ?? '-',
                $row['kode_upa'] ?? '-',
                strtoupper($row['status_itb']),
                $row['nominal_itb'],
                strtoupper($row['status_iwb']),
                $row['nominal_iwb'],
                strtoupper($row['status_infaq']),
                $row['nominal_infaq'],
                $total,
                $row['tanggal_bayar'] ?? '-',
                $row['catatan'] ?? '-'
            ]);
        }

        fclose($output);
        exit;
    }
}
