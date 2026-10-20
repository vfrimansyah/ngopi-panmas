<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProposalModel;
use App\Models\ProposalRabModel;
use App\Models\LembagaProfileModel;
use App\Models\MemberModel;

class ProposalController extends BaseController
{
    protected ProposalModel $proposalModel;
    protected ProposalRabModel $rabModel;
    protected LembagaProfileModel $lembagaModel;
    protected MemberModel $memberModel;

    public function __construct()
    {
        $this->proposalModel = new ProposalModel();
        $this->rabModel      = new ProposalRabModel();
        $this->lembagaModel  = new LembagaProfileModel();
        $this->memberModel   = new MemberModel();
    }

    /**
     * Get member linked to current user
     */
    protected function getLinkedMember(): ?array
    {
        $userId = auth()->id();
        if (!$userId) return null;
        return $this->memberModel->where('user_id', $userId)->first();
    }

    /**
     * Check if user is admin / bph
     */
    protected function isUserAdmin(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return $user->inGroup('admin') || $user->inGroup('superadmin') || $user->inGroup('bph');
    }

    /**
     * Member: List of my proposals (Meja Saya > Proposal Saya)
     */
    public function index()
    {
        $userId  = auth()->id();
        $member  = $this->getLinkedMember();
        $isAdmin = $this->isUserAdmin();

        $filters = [
            'target_pengajuan' => $this->request->getGet('target'),
            'status'           => $this->request->getGet('status'),
            'search'           => $this->request->getGet('search'),
        ];

        $proposals = $this->proposalModel->getList($userId, $member['id'] ?? null, false, $filters);
        $lembagas  = $this->lembagaModel->getByUser($userId, $member['id'] ?? null, false);

        $data = [
            'title'     => 'Proposal Saya',
            'proposals' => $proposals,
            'lembagas'  => $lembagas,
            'filters'   => $filters,
            'member'    => $member,
            'isAdmin'   => $isAdmin,
        ];

        return view('admin/meja/proposals/index', $data);
    }

    /**
     * Admin: Rekapitulasi & Monitoring Semua Proposal (DPC / BPH)
     */
    public function adminIndex()
    {
        if (!$this->isUserAdmin()) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Hanya pengurus DPC / Admin yang dapat mengakses menu rekapitulasi.');
        }

        $filters = [
            'target_pengajuan' => $this->request->getGet('target'),
            'status'           => $this->request->getGet('status'),
            'search'           => $this->request->getGet('search'),
        ];

        $proposals = $this->proposalModel->getList(null, null, true, $filters);

        // Summary Statistics
        $totalDana = 0;
        $countPokir = 0;
        $countCsr = 0;
        $countSosial = 0;
        foreach ($proposals as $p) {
            $totalDana += (float)$p['total_anggaran'];
            if ($p['target_pengajuan'] === 'pokir_dprd') $countPokir++;
            elseif ($p['target_pengajuan'] === 'csr_perusahaan') $countCsr++;
            elseif ($p['target_pengajuan'] === 'lembaga_sosial') $countSosial++;
        }

        $data = [
            'title'       => 'Monitoring & Rekapitulasi Proposal / Pokir',
            'proposals'   => $proposals,
            'filters'     => $filters,
            'totalDana'   => $totalDana,
            'countPokir'  => $countPokir,
            'countCsr'    => $countCsr,
            'countSosial' => $countSosial,
        ];

        return view('admin/proposals/admin_index', $data);
    }

    /**
     * Member: Form Buat Proposal Baru
     */
    public function create()
    {
        $userId  = auth()->id();
        $member  = $this->getLinkedMember();
        $lembagas = $this->lembagaModel->getByUser($userId, $member['id'] ?? null, false);

        $selectedLembagaId = (int)$this->request->getGet('lembaga_id');
        $target            = $this->request->getGet('target') ?: 'pokir_dprd';

        $data = [
            'title'             => 'Buat Proposal Baru',
            'proposal'          => null,
            'rabs'              => [],
            'lembagas'          => $lembagas,
            'selectedLembagaId' => $selectedLembagaId,
            'defaultTarget'     => $target,
            'member'            => $member,
        ];

        return view('admin/meja/proposals/create', $data);
    }

    /**
     * Store new proposal and its RAB
     */
    public function store()
    {
        $userId = auth()->id();
        $member = $this->getLinkedMember();

        $rules = [
            'lembaga_id'       => 'required|numeric',
            'judul_proposal'   => 'required|min_length[5]|max_length[255]',
            'target_pengajuan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $lembagaId = (int)$this->request->getPost('lembaga_id');
        $lembaga   = $this->lembagaModel->find($lembagaId);
        if (!$lembaga) {
            return redirect()->back()->withInput()->with('error', 'Lembaga/Yayasan pengusul tidak valid.');
        }

        // Validasi hak akses anggota pada lembaga
        $isAdmin = $this->isUserAdmin();
        if (!$this->lembagaModel->isAuthorized($lembaga, $userId, $member['id'] ?? null, $isAdmin)) {
            return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki wewenang untuk membuat proposal atas nama yayasan/lembaga ini.');
        }

        // Upload foto dokumentasi (multiple)
        $fotoPaths = [];
        $files = $this->request->getFileMultiple('foto_kegiatan');
        if ($files) {
            $uploadPath = FCPATH . 'uploads/proposals';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $fotoPaths[] = $newName;
                }
            }
        }

        // Selected Attachments Checklist
        $selectedAttachments = $this->request->getPost('attachments') ?: [];

        $proposalData = [
            'lembaga_id'           => $lembagaId,
            'user_id'              => $userId,
            'member_id'            => $member['id'] ?? null,
            'nomor_surat'          => $this->request->getPost('nomor_surat'),
            'tanggal_surat'        => $this->request->getPost('tanggal_surat') ?: date('Y-m-d'),
            'perihal'              => $this->request->getPost('perihal') ?: 'Permohonan Bantuan Dana / Hibah',
            'judul_proposal'       => trim($this->request->getPost('judul_proposal')),
            'target_pengajuan'     => $this->request->getPost('target_pengajuan'),
            'tujuan_nama'          => $this->request->getPost('tujuan_nama'),
            'tujuan_instansi'      => $this->request->getPost('tujuan_instansi'),
            'tujuan_alamat'        => $this->request->getPost('tujuan_alamat'),
            'latar_belakang'       => $this->request->getPost('latar_belakang'),
            'maksud_tujuan'        => $this->request->getPost('maksud_tujuan'),
            'deskripsi_kegiatan'   => $this->request->getPost('deskripsi_kegiatan'),
            'sasaran_penerima'     => $this->request->getPost('sasaran_penerima'),
            'waktu_pelaksanaan'    => $this->request->getPost('waktu_pelaksanaan'),
            'lokasi_kegiatan'      => $this->request->getPost('lokasi_kegiatan'),
            'susunan_panitia'      => $this->request->getPost('susunan_panitia'),
            'penutup'              => $this->request->getPost('penutup'),
            'selected_attachments' => json_encode($selectedAttachments),
            'foto_dokumentasi'     => json_encode($fotoPaths),
            'status'               => $this->request->getPost('status') ?: 'draft',
            'total_anggaran'       => 0,
            'terbilang_anggaran'   => '',
        ];

        $proposalId = $this->proposalModel->insert($proposalData);

        // Process RAB Items
        $rabKategori = $this->request->getPost('rab_kategori') ?: [];
        $rabUraian   = $this->request->getPost('rab_uraian') ?: [];
        $rabVolume   = $this->request->getPost('rab_volume') ?: [];
        $rabSatuan   = $this->request->getPost('rab_satuan') ?: [];
        $rabHarga    = $this->request->getPost('rab_harga') ?: [];
        $rabKet      = $this->request->getPost('rab_keterangan') ?: [];

        $totalAnggaran = 0;
        foreach ($rabUraian as $i => $uraian) {
            $uraian = trim($uraian);
            if (empty($uraian)) continue;

            $vol   = (float)($rabVolume[$i] ?? 1);
            $harga = (float)preg_replace('/[^0-9.]/', '', $rabHarga[$i] ?? '0');
            $tot   = $vol * $harga;
            $totalAnggaran += $tot;

            $this->rabModel->insert([
                'proposal_id'  => $proposalId,
                'kategori'     => $rabKategori[$i] ?? 'Umum',
                'uraian'       => $uraian,
                'volume'       => $vol,
                'satuan'       => $rabSatuan[$i] ?? 'Unit',
                'harga_satuan' => $harga,
                'total_harga'  => $tot,
                'keterangan'   => $rabKet[$i] ?? '',
                'sort_order'   => $i + 1,
            ]);
        }

        // Update total and terbilang on proposal
        $terbilang = ProposalModel::terbilang($totalAnggaran) . ' Rupiah';
        $this->proposalModel->update($proposalId, [
            'total_anggaran'     => $totalAnggaran,
            'terbilang_anggaran' => $terbilang,
        ]);

        return redirect()->to(base_url('admin/meja/proposals/' . $proposalId . '/preview'))
            ->with('success', 'Proposal berhasil dibuat! Silakan tinjau pratinjau dokumen dan unduh berkasnya.');
    }

    /**
     * Member: Form Edit Proposal
     */
    public function edit(int $id)
    {
        $proposal = $this->proposalModel->getProposalWithLembaga($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Proposal tidak ditemukan.');
        }

        $userId  = auth()->id();
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin && $proposal['user_id'] != $userId) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Akses ditolak.');
        }

        $member   = $this->getLinkedMember();
        $lembagas = $this->lembagaModel->getByUser($userId, $member['id'] ?? null, false);
        $rabs     = $this->rabModel->getByProposal($id);

        $data = [
            'title'             => 'Edit Proposal: ' . $proposal['judul_proposal'],
            'proposal'          => $proposal,
            'rabs'              => $rabs,
            'lembagas'          => $lembagas,
            'selectedLembagaId' => $proposal['lembaga_id'],
            'defaultTarget'     => $proposal['target_pengajuan'],
            'member'            => $member,
        ];

        return view('admin/meja/proposals/edit', $data);
    }

    /**
     * Update existing proposal and its RAB
     */
    public function update(int $id)
    {
        $proposal = $this->proposalModel->find($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Proposal tidak ditemukan.');
        }

        $userId  = auth()->id();
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin && $proposal['user_id'] != $userId) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Akses ditolak.');
        }

        $rules = [
            'lembaga_id'       => 'required|numeric',
            'judul_proposal'   => 'required|min_length[5]|max_length[255]',
            'target_pengajuan' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle existing photos + new uploads
        $existingPhotos = json_decode($proposal['foto_dokumentasi'] ?? '[]', true) ?: [];
        $files = $this->request->getFileMultiple('foto_kegiatan');
        if ($files) {
            $uploadPath = FCPATH . 'uploads/proposals';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    $existingPhotos[] = $newName;
                }
            }
        }

        $selectedAttachments = $this->request->getPost('attachments') ?: [];

        $proposalData = [
            'lembaga_id'           => (int)$this->request->getPost('lembaga_id'),
            'nomor_surat'          => $this->request->getPost('nomor_surat'),
            'tanggal_surat'        => $this->request->getPost('tanggal_surat') ?: date('Y-m-d'),
            'perihal'              => $this->request->getPost('perihal') ?: 'Permohonan Bantuan Dana / Hibah',
            'judul_proposal'       => trim($this->request->getPost('judul_proposal')),
            'target_pengajuan'     => $this->request->getPost('target_pengajuan'),
            'tujuan_nama'          => $this->request->getPost('tujuan_nama'),
            'tujuan_instansi'      => $this->request->getPost('tujuan_instansi'),
            'tujuan_alamat'        => $this->request->getPost('tujuan_alamat'),
            'latar_belakang'       => $this->request->getPost('latar_belakang'),
            'maksud_tujuan'        => $this->request->getPost('maksud_tujuan'),
            'deskripsi_kegiatan'   => $this->request->getPost('deskripsi_kegiatan'),
            'sasaran_penerima'     => $this->request->getPost('sasaran_penerima'),
            'waktu_pelaksanaan'    => $this->request->getPost('waktu_pelaksanaan'),
            'lokasi_kegiatan'      => $this->request->getPost('lokasi_kegiatan'),
            'susunan_panitia'      => $this->request->getPost('susunan_panitia'),
            'penutup'              => $this->request->getPost('penutup'),
            'selected_attachments' => json_encode($selectedAttachments),
            'foto_dokumentasi'     => json_encode($existingPhotos),
            'status'               => $this->request->getPost('status') ?: $proposal['status'],
        ];

        // Replace RAB items: delete existing, insert new
        $this->rabModel->where('proposal_id', $id)->delete();

        $rabKategori = $this->request->getPost('rab_kategori') ?: [];
        $rabUraian   = $this->request->getPost('rab_uraian') ?: [];
        $rabVolume   = $this->request->getPost('rab_volume') ?: [];
        $rabSatuan   = $this->request->getPost('rab_satuan') ?: [];
        $rabHarga    = $this->request->getPost('rab_harga') ?: [];
        $rabKet      = $this->request->getPost('rab_keterangan') ?: [];

        $totalAnggaran = 0;
        foreach ($rabUraian as $i => $uraian) {
            $uraian = trim($uraian);
            if (empty($uraian)) continue;

            $vol   = (float)($rabVolume[$i] ?? 1);
            $harga = (float)preg_replace('/[^0-9.]/', '', $rabHarga[$i] ?? '0');
            $tot   = $vol * $harga;
            $totalAnggaran += $tot;

            $this->rabModel->insert([
                'proposal_id'  => $id,
                'kategori'     => $rabKategori[$i] ?? 'Umum',
                'uraian'       => $uraian,
                'volume'       => $vol,
                'satuan'       => $rabSatuan[$i] ?? 'Unit',
                'harga_satuan' => $harga,
                'total_harga'  => $tot,
                'keterangan'   => $rabKet[$i] ?? '',
                'sort_order'   => $i + 1,
            ]);
        }

        $proposalData['total_anggaran']     = $totalAnggaran;
        $proposalData['terbilang_anggaran'] = ProposalModel::terbilang($totalAnggaran) . ' Rupiah';

        $this->proposalModel->update($id, $proposalData);

        return redirect()->to(base_url('admin/meja/proposals/' . $id . '/preview'))
            ->with('success', 'Perubahan proposal berhasil disimpan.');
    }

    /**
     * Delete proposal & its RAB
     */
    public function delete(int $id)
    {
        $proposal = $this->proposalModel->find($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Proposal tidak ditemukan.');
        }

        $userId  = auth()->id();
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin && $proposal['user_id'] != $userId) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Akses ditolak.');
        }

        // Delete photos
        $photos = json_decode($proposal['foto_dokumentasi'] ?? '[]', true) ?: [];
        $uploadPath = FCPATH . 'uploads/proposals/';
        foreach ($photos as $p) {
            if (file_exists($uploadPath . $p)) {
                @unlink($uploadPath . $p);
            }
        }

        $this->rabModel->where('proposal_id', $id)->delete();
        $this->proposalModel->delete($id);

        return redirect()->to(base_url('admin/meja/proposals'))->with('success', 'Proposal berhasil dihapus.');
    }

    /**
     * Interactive HTML preview
     */
    public function preview(int $id)
    {
        $proposal = $this->proposalModel->getProposalWithLembaga($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Proposal tidak ditemukan.');
        }

        $userId  = auth()->id();
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin && $proposal['user_id'] != $userId) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Akses ditolak.');
        }

        $rabs = $this->rabModel->getByProposal($id);
        $selectedAttachments = json_decode($proposal['selected_attachments'] ?? '[]', true) ?: [];
        $photos              = json_decode($proposal['foto_dokumentasi'] ?? '[]', true) ?: [];

        $data = [
            'title'               => 'Pratinjau Proposal: ' . $proposal['judul_proposal'],
            'proposal'            => $proposal,
            'rabs'                => $rabs,
            'selectedAttachments' => $selectedAttachments,
            'photos'              => $photos,
            'isAdmin'             => $isAdmin,
        ];

        return view('admin/meja/proposals/preview', $data);
    }

    /**
     * Official Printable A4 View (Print to PDF)
     */
    public function printPdf(int $id)
    {
        $proposal = $this->proposalModel->getProposalWithLembaga($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Proposal tidak ditemukan.');
        }

        $userId  = auth()->id();
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin && $proposal['user_id'] != $userId) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Akses ditolak.');
        }

        $rabs = $this->rabModel->getByProposal($id);
        $selectedAttachments = json_decode($proposal['selected_attachments'] ?? '[]', true) ?: [];
        $photos              = json_decode($proposal['foto_dokumentasi'] ?? '[]', true) ?: [];

        $data = [
            'title'               => 'Proposal - ' . $proposal['judul_proposal'],
            'proposal'            => $proposal,
            'rabs'                => $rabs,
            'selectedAttachments' => $selectedAttachments,
            'photos'              => $photos,
        ];

        return view('admin/meja/proposals/print_pdf', $data);
    }

    /**
     * Export to Microsoft Word (.doc)
     */
    public function exportWord(int $id)
    {
        $proposal = $this->proposalModel->getProposalWithLembaga($id);
        if (!$proposal) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Proposal tidak ditemukan.');
        }

        $userId  = auth()->id();
        $isAdmin = $this->isUserAdmin();
        if (!$isAdmin && $proposal['user_id'] != $userId) {
            return redirect()->to(base_url('admin/meja/proposals'))->with('error', 'Akses ditolak.');
        }

        $rabs = $this->rabModel->getByProposal($id);
        $selectedAttachments = json_decode($proposal['selected_attachments'] ?? '[]', true) ?: [];
        $photos              = json_decode($proposal['foto_dokumentasi'] ?? '[]', true) ?: [];

        $data = [
            'proposal'            => $proposal,
            'rabs'                => $rabs,
            'selectedAttachments' => $selectedAttachments,
            'photos'              => $photos,
        ];

        $filename = 'Proposal_' . url_title($proposal['judul_proposal'], '_') . '.doc';

        header('Content-Type: application/vnd.ms-word');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        return view('admin/meja/proposals/export_word', $data);
    }

    /**
     * Admin: Update Proposal Status
     */
    public function updateStatus(int $id)
    {
        if (!$this->isUserAdmin()) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $status = $this->request->getPost('status');
        $catatan = $this->request->getPost('catatan_admin');

        $this->proposalModel->update($id, [
            'status'        => $status,
            'catatan_admin' => $catatan,
        ]);

        return redirect()->back()->with('success', 'Status proposal berhasil diperbarui menjadi: ' . strtoupper($status));
    }
}
