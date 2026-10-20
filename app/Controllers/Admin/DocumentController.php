<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Models\DocumentCategoryModel;
use App\Models\DpcBidangModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DocumentController extends BaseController
{
    protected DocumentModel $docModel;
    protected DocumentCategoryModel $catModel;
    protected DpcBidangModel $bidangModel;

    public function __construct()
    {
        $this->docModel    = new DocumentModel();
        $this->catModel    = new DocumentCategoryModel();
        $this->bidangModel = new DpcBidangModel();
    }



    public function index()
    {
        $viewMode = $this->request->getGet('mode') ?: 'table'; // 'table' or 'grid'

        $filters = [
            'kategori_id'   => $this->request->getGet('kategori'),
            'sifat_dokumen' => $this->request->getGet('sifat'),
            'bidang_kode'   => $this->request->getGet('bidang'),
            'tahun'         => $this->request->getGet('tahun'),
            'file_type'     => $this->request->getGet('type'),
            'search'        => $this->request->getGet('search'),
        ];

        $documents  = $this->docModel->getList($filters);
        $categories = $this->catModel->getCategoriesWithStats();
        $bidangs    = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();
        $stats      = $this->docModel->getStats();

        // Get distinct years
        $db = \Config\Database::connect();
        $yearsList = $db->table('documents')->select('tahun')->where('tahun IS NOT NULL')->groupBy('tahun')->orderBy('tahun', 'DESC')->get()->getResultArray();
        $years = array_filter(array_column($yearsList, 'tahun'));

        $perms = $this->getPagePermissions('documents');

        $user = auth()->user();
        $userGroups = $user ? $user->getGroups() : [];

        $data = [
            'title'           => 'Manajemen Dokumen & Arsip Digital (DMS)',
            'documents'       => $documents,
            'categories'      => $categories,
            'bidangs'         => $bidangs,
            'stats'           => $stats,
            'years'           => $years,
            'filters'         => $filters,
            'viewMode'        => $viewMode,
            'can_create'      => $perms['canCreate'],
            'can_update'      => $perms['canUpdate'],
            'can_delete'      => $perms['canDelete'],
            'is_full_admin'   => in_array('admin', $userGroups) || in_array('bph', $userGroups),
            'current_user_id' => $user ? $user->id : 0,
        ];

        return view('admin/documents/index', $data);
    }

    public function create()
    {
        $categories = $this->catModel->orderBy('sort_order', 'ASC')->findAll();
        $bidangs    = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();

        $selectedCat = $this->request->getGet('kategori');

        $data = [
            'title'       => 'Upload Dokumen Baru',
            'doc'         => null,
            'categories'  => $categories,
            'bidangs'     => $bidangs,
            'selectedCat' => $selectedCat,
        ];

        return view('admin/documents/form', $data);
    }

    public function store()
    {
        $rules = [
            'judul'          => 'required|min_length[3]|max_length[255]',
            'sifat_dokumen'  => 'required|in_list[publik,internal,rahasia]',
            'dokumen_file'   => 'uploaded[dokumen_file]|max_size[dokumen_file,51200]|ext_in[dokumen_file,pdf,docx,doc,xlsx,xls,pptx,ppt,jpg,jpeg,png,webp,txt,csv,zip,rar]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('dokumen_file');
        if (!$file->isValid()) {
            return redirect()->back()->withInput()->with('errors', ['dokumen_file' => 'File tidak valid atau gagal diupload.']);
        }

        $originalName = $file->getClientName();
        $ext          = strtolower($file->getClientExtension());
        $fileSize     = $file->getSize();

        $uploadDir = FCPATH . 'uploads/documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
        $file->move($uploadDir, $newName);

        $judul = $this->request->getPost('judul');
        $slug  = url_title($judul . '-' . time(), '-', true);

        $currentUser = auth()->user();
        $uploaderName = $currentUser ? ($currentUser->username ?? 'Admin') : 'Admin';
        $uploaderId   = $currentUser ? $currentUser->id : null;

        $tglDokumen = $this->request->getPost('tanggal_dokumen');
        $tahun = $tglDokumen ? date('Y', strtotime($tglDokumen)) : ($this->request->getPost('tahun') ?: date('Y'));

        $this->docModel->insert([
            'kategori_id'     => $this->request->getPost('kategori_id') ?: null,
            'nomor_dokumen'   => $this->request->getPost('nomor_dokumen'),
            'judul'           => $judul,
            'slug'            => $slug,
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'file_name'       => $originalName,
            'file_path'       => 'uploads/documents/' . $newName,
            'file_type'       => $ext,
            'file_size'       => $fileSize,
            'tahun'           => $tahun,
            'tanggal_dokumen' => $tglDokumen ?: date('Y-m-d'),
            'sifat_dokumen'   => $this->request->getPost('sifat_dokumen') ?: 'internal',
            'bidang_kode'     => $this->request->getPost('bidang_kode') ?: null,
            'institusi'       => $this->request->getPost('institusi') ?: 'DPC',
            'uploader_id'     => $uploaderId,
            'uploader_nama'   => $uploaderName,
            'views_count'     => 0,
            'downloads_count' => 0,
            'is_pinned'       => $this->request->getPost('is_pinned') ? 1 : 0,
        ]);

        return redirect()->to(base_url('admin/documents'))->with('success', 'Dokumen berhasil diupload.');
    }

    public function edit(int $id)
    {
        $doc = $this->docModel->find($id);
        if (!$doc) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Dokumen tidak ditemukan.']);
        }

        if (!$this->canModifyResource($doc['uploader_id'] ?? null, 'update', 'documents')) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Anda hanya dapat mengubah dokumen yang Anda upload sendiri.']);
        }

        $categories = $this->catModel->orderBy('sort_order', 'ASC')->findAll();
        $bidangs    = $this->bidangModel->orderBy('sort_order', 'ASC')->findAll();

        $data = [
            'title'      => 'Edit Dokumen: ' . $doc['judul'],
            'doc'        => $doc,
            'categories' => $categories,
            'bidangs'    => $bidangs,
        ];

        return view('admin/documents/form', $data);
    }

    public function update(int $id)
    {
        $doc = $this->docModel->find($id);
        if (!$doc) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Dokumen tidak ditemukan.']);
        }

        if (!$this->canModifyResource($doc['uploader_id'] ?? null, 'update', 'documents')) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Anda hanya dapat mengubah dokumen yang Anda upload sendiri.']);
        }

        $rules = [
            'judul'         => 'required|min_length[3]|max_length[255]',
            'sifat_dokumen' => 'required|in_list[publik,internal,rahasia]',
        ];

        $file = $this->request->getFile('dokumen_file');
        if ($file && $file->isValid()) {
            $rules['dokumen_file'] = 'max_size[dokumen_file,51200]|ext_in[dokumen_file,pdf,docx,doc,xlsx,xls,pptx,ppt,jpg,jpeg,png,webp,txt,csv,zip,rar]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tglDokumen = $this->request->getPost('tanggal_dokumen');
        $tahun = $tglDokumen ? date('Y', strtotime($tglDokumen)) : ($this->request->getPost('tahun') ?: date('Y'));

        $updateData = [
            'kategori_id'     => $this->request->getPost('kategori_id') ?: null,
            'nomor_dokumen'   => $this->request->getPost('nomor_dokumen'),
            'judul'           => $this->request->getPost('judul'),
            'deskripsi'       => $this->request->getPost('deskripsi'),
            'tahun'           => $tahun,
            'tanggal_dokumen' => $tglDokumen ?: $doc['tanggal_dokumen'],
            'sifat_dokumen'   => $this->request->getPost('sifat_dokumen') ?: 'internal',
            'bidang_kode'     => $this->request->getPost('bidang_kode') ?: null,
            'institusi'       => $this->request->getPost('institusi') ?: 'DPC',
            'is_pinned'       => $this->request->getPost('is_pinned') ? 1 : 0,
        ];

        // If replace file
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old file
            $oldPath = FCPATH . $doc['file_path'];
            if (file_exists($oldPath) && is_file($oldPath)) {
                unlink($oldPath);
            }

            $originalName = $file->getClientName();
            $ext          = strtolower($file->getClientExtension());
            $fileSize     = $file->getSize();

            $uploadDir = FCPATH . 'uploads/documents';
            $newName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
            $file->move($uploadDir, $newName);

            $updateData['file_name'] = $originalName;
            $updateData['file_path'] = 'uploads/documents/' . $newName;
            $updateData['file_type'] = $ext;
            $updateData['file_size'] = $fileSize;
        }

        $this->docModel->update($id, $updateData);

        return redirect()->to(base_url('admin/documents'))->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $doc = $this->docModel->find($id);
        if (!$doc) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Dokumen tidak ditemukan.']);
        }

        if (!$this->canModifyResource($doc['uploader_id'] ?? null, 'delete', 'documents')) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Anda hanya dapat menghapus dokumen yang Anda upload sendiri.']);
        }

        // Delete physical file
        $filePath = FCPATH . $doc['file_path'];
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }

        $this->docModel->delete($id);

        return redirect()->to(base_url('admin/documents'))->with('success', 'Dokumen dan arsip file berhasil dihapus.');
    }

    /**
     * Dedicated Interactive Document Viewer Page
     */
    public function viewDocument(int $id)
    {
        $doc = $this->docModel->getDetail($id);
        if (!$doc) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Dokumen tidak ditemukan.']);
        }

        // Increment view count
        $this->docModel->incrementViews($id);

        // Related documents in same category
        $relatedDocs = [];
        if (!empty($doc['kategori_id'])) {
            $relatedDocs = $this->docModel->where('kategori_id', $doc['kategori_id'])
                ->where('id !=', $id)
                ->orderBy('id', 'DESC')
                ->limit(5)
                ->findAll();
        }

        // Read text content if it's markdown or text
        $textContent = null;
        if (in_array($doc['file_type'], ['txt', 'csv', 'md', 'json', 'sql'])) {
            $fullPath = FCPATH . $doc['file_path'];
            if (file_exists($fullPath) && is_file($fullPath)) {
                $textContent = file_get_contents($fullPath);
            }
        }

        $data = [
            'title'       => 'Document Viewer — ' . $doc['judul'],
            'doc'         => $doc,
            'relatedDocs' => $relatedDocs,
            'textContent' => $textContent,
        ];

        return view('admin/documents/view', $data);
    }

    /**
     * Stream file securely for inline iframe/embed viewer
     */
    public function stream(int $id)
    {
        $doc = $this->docModel->find($id);
        if (!$doc) {
            return $this->response->setStatusCode(404, 'Document Not Found');
        }

        $filePath = FCPATH . $doc['file_path'];
        if (!file_exists($filePath) || !is_file($filePath)) {
            return $this->response->setStatusCode(404, 'File Not Found on Server');
        }

        $mimeTypes = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'txt'  => 'text/plain',
            'csv'  => 'text/csv',
            'md'   => 'text/markdown',
            'json' => 'application/json',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        $ext = strtolower($doc['file_type']);
        $mime = $mimeTypes[$ext] ?? 'application/octet-stream';

        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . basename($doc['file_name']) . '"')
            ->setHeader('Content-Length', (string)filesize($filePath))
            ->setBody(file_get_contents($filePath));
    }

    /**
     * Download document file with counter tracking
     */
    public function download(int $id)
    {
        $doc = $this->docModel->find($id);
        if (!$doc) {
            return redirect()->to(base_url('admin/documents'))->with('errors', ['Dokumen tidak ditemukan.']);
        }

        $filePath = FCPATH . $doc['file_path'];
        if (!file_exists($filePath) || !is_file($filePath)) {
            return redirect()->back()->with('errors', ['File fisik tidak ditemukan di server.']);
        }

        $this->docModel->incrementDownloads($id);

        return $this->response->download($filePath, null)->setFileName($doc['file_name']);
    }

    /**
     * Export document archive metadata to Excel
     */
    public function exportExcel()
    {
        $filters = [
            'kategori_id'   => $this->request->getGet('kategori'),
            'sifat_dokumen' => $this->request->getGet('sifat'),
            'bidang_kode'   => $this->request->getGet('bidang'),
            'tahun'         => $this->request->getGet('tahun'),
            'search'        => $this->request->getGet('search'),
        ];

        $documents = $this->docModel->getList($filters);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arsip Dokumen');

        // Header Title
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'DAFTAR ARSIP & DOKUMEN DIGITAL DPC PKS PANCORAN MAS');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Diexport pada: ' . date('d F Y H:i') . ' WIB');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Table Headers
        $headers = ['No', 'Nomor Dokumen', 'Judul Dokumen', 'Kategori', 'Sifat', 'Bidang/Institusi', 'Tipe File', 'Ukuran', 'Tgl Dokumen', 'Pengunggah'];
        $cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];

        $row = 4;
        foreach ($headers as $idx => $h) {
            $sheet->setCellValue($cols[$idx] . $row, $h);
        }

        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '3E2723']],
        ]);

        $sheet->getRowDimension($row)->setRowHeight(25);

        // Table Data
        $dataRow = 5;
        foreach ($documents as $idx => $d) {
            $sheet->setCellValue('A' . $dataRow, $idx + 1);
            $sheet->setCellValue('B' . $dataRow, $d['nomor_dokumen'] ?: '-');
            $sheet->setCellValue('C' . $dataRow, $d['judul']);
            $sheet->setCellValue('D' . $dataRow, $d['nama_kategori'] ?: 'Umum');
            $sheet->setCellValue('E' . $dataRow, ucfirst($d['sifat_dokumen']));
            $sheet->setCellValue('F' . $dataRow, ($d['bidang_kode'] ?: $d['institusi']));
            $sheet->setCellValue('G' . $dataRow, strtoupper($d['file_type']));
            $sheet->setCellValue('H' . $dataRow, round($d['file_size'] / 1024, 1) . ' KB');
            $sheet->setCellValue('I' . $dataRow, $d['tanggal_dokumen'] ? date('d/m/Y', strtotime($d['tanggal_dokumen'])) : '-');
            $sheet->setCellValue('J' . $dataRow, $d['uploader_nama'] ?: '-');

            $sheet->getStyle("A{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("I{$dataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $dataRow++;
        }

        $lastRow = $dataRow - 1;
        $sheet->getStyle("A4:J{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ]);

        foreach ($cols as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'Daftar_Dokumen_DPC_Panmas_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
