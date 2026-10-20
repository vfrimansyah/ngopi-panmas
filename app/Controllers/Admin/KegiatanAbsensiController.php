<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KegiatanAbsensiModel;
use App\Models\KegiatanListModel;
use App\Models\MemberModel;

class KegiatanAbsensiController extends BaseController
{
    protected $absensiModel;
    protected $kegiatanModel;
    protected $memberModel;

    public function __construct()
    {
        $this->absensiModel  = new KegiatanAbsensiModel();
        $this->kegiatanModel = new KegiatanListModel();
        $this->memberModel   = new MemberModel();
    }

    /**
     * Halaman absensi kegiatan — menampilkan daftar anggota dengan status kehadiran.
     */
    public function index($kegiatanId = null)
    {
        $kegiatan = $this->kegiatanModel->find($kegiatanId);
        if (!$kegiatan) {
            return redirect()->to(base_url('admin/kegiatan/list'))->with('error', 'Data kegiatan tidak ditemukan.');
        }

        // Get jenis kegiatan name
        $jenisModel = new \App\Models\KegiatanJenisModel();
        $jenis = $jenisModel->find($kegiatan['jenis_kegiatan_id'] ?? 0);

        // All active members
        $members = $this->memberModel
            ->where('is_active', 1)
            ->orderBy('nama_lengkap', 'ASC')
            ->findAll();

        // Existing attendance records for this kegiatan
        $existingAbsensi = $this->absensiModel->getByKegiatan($kegiatanId);
        $absensiMap = [];
        foreach ($existingAbsensi as $a) {
            $absensiMap[$a['member_id']] = $a;
        }

        // Attendance stats
        $stats = $this->absensiModel->getStats($kegiatanId);

        $perms = $this->getPagePermissions('kegiatan');

        $data = [
            'title'       => 'Absensi Kegiatan',
            'kegiatan'    => $kegiatan,
            'jenisNama'   => $jenis['nama_kegiatan'] ?? '-',
            'members'     => $members,
            'absensiMap'  => $absensiMap,
            'stats'       => $stats,
            'can_update'  => $perms['canUpdate'],
        ];

        return view('admin/kegiatan/absensi', $data);
    }

    /**
     * Simpan data absensi (batch save).
     */
    public function save($kegiatanId = null)
    {
        $kegiatan = $this->kegiatanModel->find($kegiatanId);
        if (!$kegiatan) {
            return redirect()->to(base_url('admin/kegiatan/list'))->with('error', 'Data kegiatan tidak ditemukan.');
        }

        $memberIds = $this->request->getPost('member_id') ?? [];
        $statuses  = $this->request->getPost('status') ?? [];
        $catatans  = $this->request->getPost('catatan') ?? [];

        $attendanceData = [];
        foreach ($memberIds as $i => $memberId) {
            $status = $statuses[$i] ?? 'alfa';
            // Only save records that are not 'alfa' (unmarked/absent)
            // Actually, save ALL marked records so we have a complete picture
            $attendanceData[] = [
                'member_id' => (int) $memberId,
                'status'    => $status,
                'catatan'   => $catatans[$i] ?? null,
            ];
        }

        $userId = auth()->user() ? auth()->user()->id : null;
        $result = $this->absensiModel->saveAbsensi($kegiatanId, $attendanceData, $userId);

        if ($result) {
            return redirect()->to(base_url('admin/kegiatan/absensi/' . $kegiatanId))
                ->with('success', 'Data absensi berhasil disimpan.');
        }

        return redirect()->back()->with('error', 'Gagal menyimpan data absensi.');
    }

    /**
     * Export daftar hadir ke Excel (CSV).
     */
    public function export($kegiatanId = null)
    {
        $kegiatan = $this->kegiatanModel->find($kegiatanId);
        if (!$kegiatan) {
            return redirect()->to(base_url('admin/kegiatan/list'))->with('error', 'Data kegiatan tidak ditemukan.');
        }

        $absensiList = $this->absensiModel->getByKegiatan($kegiatanId);

        $filename = 'absensi_' . url_title($kegiatan['nama_kegiatan'], '-', true) . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header info
        fputcsv($output, ['Absensi Kegiatan: ' . $kegiatan['nama_kegiatan']]);
        fputcsv($output, ['Tanggal: ' . ($kegiatan['tanggal'] ?? '-')]);
        fputcsv($output, ['Lokasi: ' . ($kegiatan['lokasi_kegiatan'] ?? '-')]);
        fputcsv($output, []);

        // Table header
        fputcsv($output, ['No', 'Nama Lengkap', 'UPA', 'Jenis Kelamin', 'Status', 'Catatan']);

        $no = 1;
        foreach ($absensiList as $row) {
            $jk = ($row['jenis_kelamin'] ?? '') === 'L' ? 'Laki-laki' : (($row['jenis_kelamin'] ?? '') === 'P' ? 'Perempuan' : '-');
            $statusLabel = match($row['status']) {
                'hadir' => 'Hadir',
                'izin'  => 'Izin',
                'sakit' => 'Sakit',
                'alfa'  => 'Alfa',
                default => '-',
            };
            fputcsv($output, [
                $no++,
                $row['nama_lengkap'] ?? '-',
                $row['kode_upa'] ?? '-',
                $jk,
                $statusLabel,
                $row['catatan'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }
}
