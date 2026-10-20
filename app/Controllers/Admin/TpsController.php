<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PemiluTpsModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

class TpsController extends BaseController
{
    protected $tpsModel;

    public function __construct()
    {
        $this->tpsModel = new PemiluTpsModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        
        $selectedTahun = $this->request->getVar('tahun');
        $selectedKelurahan = $this->request->getVar('village_id');

        // Ambil daftar tahun unik
        $listTahun = $db->table('pemilu_tps')
            ->select('tahun')
            ->distinct()
            ->orderBy('tahun', 'DESC')
            ->get()
            ->getResultArray();

        // Ambil list kelurahan yang terdaftar di TPS
        $listKelurahan = $db->table('pemilu_tps t')
            ->select('t.village_id, v.name')
            ->join('reg_villages v', 'v.id = t.village_id')
            ->distinct()
            ->orderBy('v.name', 'ASC')
            ->get()
            ->getResultArray();

        $query = $db->table('pemilu_tps t')
            ->select('t.*, p.name AS provinsi, r.name AS kabupaten, d.name AS kecamatan, v.name AS kelurahan')
            ->join('reg_provinces p', 'p.id = t.province_id')
            ->join('reg_regencies r', 'r.id = t.regency_id')
            ->join('reg_districts d', 'd.id = t.district_id')
            ->join('reg_villages v', 'v.id = t.village_id');

        if (!empty($selectedTahun)) {
            $query = $query->where('t.tahun', $selectedTahun);
        }
        if (!empty($selectedKelurahan)) {
            $query = $query->where('t.village_id', $selectedKelurahan);
        }

        $tps = $query->orderBy('t.tahun', 'DESC')
            ->orderBy('v.name', 'ASC')
            ->orderBy('t.tps', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'title'             => 'Daftar TPS',
            'tps'               => $tps,
            'listTahun'         => array_column($listTahun, 'tahun'),
            'listKelurahan'     => $listKelurahan,
            'selectedTahun'     => $selectedTahun,
            'selectedKelurahan' => $selectedKelurahan,
            'pager'             => null
        ];

        $perms = $this->getPagePermissions('pemilu');
        $data = array_merge($data, [
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);

        return view('admin/pemilu_tps/index', $data);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        
        $data = [
            'title'     => 'Tambah Data TPS',
            'provinces' => $db->table('reg_provinces')->orderBy('name', 'ASC')->get()->getResultArray()
        ];

        return view('admin/pemilu_tps/form', $data);
    }

    public function store()
    {
        $rules = $this->tpsModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tpsModel->save([
            'tahun'       => $this->request->getPost('tahun'),
            'province_id' => $this->request->getPost('province_id'),
            'regency_id'  => $this->request->getPost('regency_id'),
            'district_id' => $this->request->getPost('district_id'),
            'village_id'  => $this->request->getPost('village_id'),
            'rw'          => $this->request->getPost('rw'),
            'rt'          => $this->request->getPost('rt'),
            'tps'         => $this->request->getPost('tps'),
            'dapil_kota'  => $this->request->getPost('dapil_kota'),
            'dapil_prov'  => $this->request->getPost('dapil_prov'),
            'dapil_ri'    => $this->request->getPost('dapil_ri'),
            'dpt'         => $this->request->getPost('dpt') ?: null
        ]);

        return redirect()->to('admin/pemilu/tps')->with('success', 'Data TPS berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tps = $this->tpsModel->find($id);

        if (!$tps) {
            return redirect()->to('admin/pemilu/tps')->with('error', 'Data TPS tidak ditemukan.');
        }

        $db = \Config\Database::connect();

        $data = [
            'title'     => 'Edit Data TPS',
            'tps'       => $tps,
            'provinces' => $db->table('reg_provinces')->orderBy('name', 'ASC')->get()->getResultArray(),
            'regencies' => $db->table('reg_regencies')->where('province_id', $tps['province_id'])->orderBy('name', 'ASC')->get()->getResultArray(),
            'districts' => $db->table('reg_districts')->where('regency_id', $tps['regency_id'])->orderBy('name', 'ASC')->get()->getResultArray(),
            'villages'  => $db->table('reg_villages')->where('district_id', $tps['district_id'])->orderBy('name', 'ASC')->get()->getResultArray()
        ];

        return view('admin/pemilu_tps/form', $data);
    }

    public function update($id)
    {
        $tps = $this->tpsModel->find($id);

        if (!$tps) {
            return redirect()->to('admin/pemilu/tps')->with('error', 'Data TPS tidak ditemukan.');
        }

        $rules = $this->tpsModel->getValidationRules();

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tpsModel->update($id, [
            'tahun'       => $this->request->getPost('tahun'),
            'province_id' => $this->request->getPost('province_id'),
            'regency_id'  => $this->request->getPost('regency_id'),
            'district_id' => $this->request->getPost('district_id'),
            'village_id'  => $this->request->getPost('village_id'),
            'rw'          => $this->request->getPost('rw'),
            'rt'          => $this->request->getPost('rt'),
            'tps'         => $this->request->getPost('tps'),
            'dapil_kota'  => $this->request->getPost('dapil_kota'),
            'dapil_prov'  => $this->request->getPost('dapil_prov'),
            'dapil_ri'    => $this->request->getPost('dapil_ri'),
            'dpt'         => $this->request->getPost('dpt') ?: null
        ]);

        return redirect()->to('admin/pemilu/tps')->with('success', 'Data TPS berhasil diperbarui.');
    }

    public function delete($id)
    {
        $tps = $this->tpsModel->find($id);

        if ($tps) {
            $this->tpsModel->delete($id);
            return redirect()->to('admin/pemilu/tps')->with('success', 'Data TPS berhasil dihapus.');
        }

        return redirect()->to('admin/pemilu/tps')->with('error', 'Data TPS tidak ditemukan.');
    }

    public function exportExcel()
    {
        $db = \Config\Database::connect();

        $selectedTahun = $this->request->getVar('tahun');
        $selectedKelurahan = $this->request->getVar('village_id');

        $query = $db->table('pemilu_tps t')
            ->select('t.*, p.name AS provinsi, r.name AS kabupaten, d.name AS kecamatan, v.name AS kelurahan')
            ->join('reg_provinces p', 'p.id = t.province_id')
            ->join('reg_regencies r', 'r.id = t.regency_id')
            ->join('reg_districts d', 'd.id = t.district_id')
            ->join('reg_villages v', 'v.id = t.village_id');

        $kelurahanName = '';
        if (!empty($selectedTahun)) {
            $query = $query->where('t.tahun', $selectedTahun);
        }
        if (!empty($selectedKelurahan)) {
            $query = $query->where('t.village_id', $selectedKelurahan);
            $kelRow = $db->table('reg_villages')->select('name')->where('id', $selectedKelurahan)->get()->getRowArray();
            if ($kelRow) {
                $kelurahanName = $kelRow['name'];
            }
        }

        $tps = $query->orderBy('t.tahun', 'DESC')
            ->orderBy('v.name', 'ASC')
            ->orderBy('t.tps', 'ASC')
            ->get()
            ->getResultArray();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data TPS');

        // Header Judul
        $sheet->mergeCells('A1:M1');
        $sheet->setCellValue('A1', 'DATA TEMPAT PEMUNGUTAN SUARA (TPS)');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('3E2723'));
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Subtitle Info Filter
        $subtitleInfo = [];
        $subtitleInfo[] = 'Tahun: ' . (!empty($selectedTahun) ? $selectedTahun : 'Semua Tahun');
        $subtitleInfo[] = 'Kelurahan: ' . (!empty($kelurahanName) ? $kelurahanName : 'Semua Kelurahan');
        $subtitleInfo[] = 'Total TPS: ' . count($tps);
        $subtitleInfo[] = 'Diexport pada: ' . date('d/m/Y H:i') . ' WIB';

        $sheet->mergeCells('A2:M2');
        $sheet->setCellValue('A2', implode(' | ', $subtitleInfo));
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Headers
        $headers = [
            'No',
            'Tahun',
            'Provinsi',
            'Kota/Kabupaten',
            'Dapil DPRD Kota',
            'Dapil DPRD Prov',
            'Dapil DPR RI',
            'Kecamatan',
            'Kelurahan',
            'RW',
            'RT',
            'TPS',
            'DPT (Jiwa)'
        ];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M'];

        $row = 4;
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . $row, $h);
        }

        // Style Table Header
        $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3E2723']],
        ]);
        $sheet->getRowDimension($row)->setRowHeight(26);

        // Table Data
        $dataRow = 5;
        foreach ($tps as $idx => $t) {
            $sheet->setCellValue('A' . $dataRow, $idx + 1);
            $sheet->setCellValue('B' . $dataRow, $t['tahun']);
            $sheet->setCellValue('C' . $dataRow, $t['provinsi']);
            $sheet->setCellValue('D' . $dataRow, $t['kabupaten']);
            $sheet->setCellValue('E' . $dataRow, $t['dapil_kota'] ?: '-');
            $sheet->setCellValue('F' . $dataRow, $t['dapil_prov'] ?: '-');
            $sheet->setCellValue('G' . $dataRow, $t['dapil_ri'] ?: '-');
            $sheet->setCellValue('H' . $dataRow, $t['kecamatan']);
            $sheet->setCellValue('I' . $dataRow, $t['kelurahan']);
            $sheet->setCellValueExplicit('J' . $dataRow, (string)($t['rw'] ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('K' . $dataRow, (string)($t['rt'] ?? '-'), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit('L' . $dataRow, (string)($t['tps'] ?? '-'), DataType::TYPE_STRING);

            if ($t['dpt'] !== null && $t['dpt'] !== '') {
                $dptVal = (int) $t['dpt'];
                $sheet->setCellValue('M' . $dataRow, $dptVal);
            } else {
                $sheet->setCellValue('M' . $dataRow, 0);
            }

            // Alignments
            $sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$dataRow}:G{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J{$dataRow}:L{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("M{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("M{$dataRow}")->getNumberFormat()->setFormatCode('#,##0');

            $dataRow++;
        }

        $lastDataRow = $dataRow - 1;

        // Total Row
        if (count($tps) > 0) {
            $sheet->mergeCells("A{$dataRow}:L{$dataRow}");
            $sheet->setCellValue("A{$dataRow}", 'TOTAL DPT');
            $sheet->setCellValue("M{$dataRow}", "=SUM(M5:M{$lastDataRow})");

            $sheet->getStyle("A{$dataRow}:M{$dataRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '3E2723']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5EBE6']],
            ]);
            $sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("M{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("M{$dataRow}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getRowDimension($dataRow)->setRowHeight(22);

            $borderEndRow = $dataRow;
        } else {
            $borderEndRow = 4;
        }

        // Borders
        $sheet->getStyle("A4:M{$borderEndRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        // Auto-fit column dimensions
        foreach ($cols as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // Generate Filename
        $filenameParts = ['Data_TPS'];
        if (!empty($selectedTahun)) {
            $filenameParts[] = 'Tahun_' . $selectedTahun;
        }
        if (!empty($kelurahanName)) {
            $filenameParts[] = preg_replace('/[^a-zA-Z0-9_-]/', '_', $kelurahanName);
        }
        $filenameParts[] = date('Ymd_His');
        $fileName = implode('_', $filenameParts) . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // --- Dynamic Dropdown AJAX APIs ---
    public function getRegencies()
    {
        $provinceId = $this->request->getGet('province_id');
        $db = \Config\Database::connect();
        $data = $db->table('reg_regencies')
            ->where('province_id', $provinceId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($data);
    }

    public function getDistricts()
    {
        $regencyId = $this->request->getGet('regency_id');
        $db = \Config\Database::connect();
        $data = $db->table('reg_districts')
            ->where('regency_id', $regencyId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($data);
    }

    public function getVillages()
    {
        $districtId = $this->request->getGet('district_id');
        $db = \Config\Database::connect();
        $data = $db->table('reg_villages')
            ->where('district_id', $districtId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
        return $this->response->setJSON($data);
    }
}
