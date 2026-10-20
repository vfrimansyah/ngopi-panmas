<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinTransaksiModel;
use App\Models\FinJurnalModel;
use App\Models\FinJurnalDetailModel;
use App\Models\FinRekeningModel;
use App\Models\FinAccountModel;

class KeuanganTransaksiController extends BaseController
{
    protected $model;
    protected $jurnalModel;
    protected $jurnalDetailModel;
    protected $rekeningModel;
    protected $accountModel;

    public function __construct()
    {
        $this->model             = new FinTransaksiModel();
        $this->jurnalModel       = new FinJurnalModel();
        $this->jurnalDetailModel = new FinJurnalDetailModel();
        $this->rekeningModel     = new FinRekeningModel();
        $this->accountModel      = new FinAccountModel();
    }

    public function index()
    {
        $filter = [
            'tipe'        => $this->request->getGet('tipe'),
            'rekening_id' => $this->request->getGet('rekening_id'),
            'dari'        => $this->request->getGet('dari'),
            'sampai'      => $this->request->getGet('sampai'),
            'bulan'       => $this->request->getGet('bulan'),
            'tahun'       => $this->request->getGet('tahun'),
        ];

        $transaksis = $this->model->getList($filter);

        $totalMasuk  = 0;
        $totalKeluar = 0;
        foreach ($transaksis as $t) {
            if ($t['tipe'] === 'pemasukan')   $totalMasuk  += $t['jumlah'];
            if ($t['tipe'] === 'pengeluaran') $totalKeluar += $t['jumlah'];
        }

        $perms = $this->getPagePermissions('keuangan');
        return view('admin/keuangan/transaksi/index', [
            'title'       => 'Transaksi Keuangan',
            'transaksis'  => $transaksis,
            'filter'      => $filter,
            'totalMasuk'  => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'rekenings'   => $this->rekeningModel->where('is_active', 1)->findAll(),
            'can_create'  => $perms['canCreate'],
            'can_update'  => $perms['canUpdate'],
            'can_delete'  => $perms['canDelete'],
        ]);
    }

    public function create()
    {
        return view('admin/keuangan/transaksi/form', [
            'title'      => 'Tambah Transaksi',
            'transaksi'  => null,
            'rekenings'  => $this->rekeningModel->where('is_active', 1)->findAll(),
            'accounts'   => $this->accountModel->getActive(),
            'tipeHint'   => $this->request->getGet('tipe') ?? 'pemasukan',
        ]);
    }

    public function store()
    {
        if (!$this->validate([
            'tanggal'     => 'required|valid_date',
            'tipe'        => 'required|in_list[pemasukan,pengeluaran]',
            'jumlah'      => 'required',
            'rekening_id' => 'required|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $jumlah     = (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('jumlah'));
        $tipe       = $this->request->getPost('tipe');
        $rekeningId = (int) $this->request->getPost('rekening_id');
        $tanggal    = $this->request->getPost('tanggal');
        $deskripsi  = $this->request->getPost('deskripsi');

        // Tentukan akun debit/kredit otomatis
        $rekening = $this->rekeningModel->find($rekeningId);
        $akunRek  = $rekening ? $this->accountModel->find($rekening['akun_id']) : null;

        $akunDebitId  = null;
        $akunKreditId = null;

        if ($tipe === 'pemasukan') {
            // Debit: Kas/Bank (rekening), Kredit: Akun pendapatan
            $akunDebitId  = $akunRek ? $akunRek['id'] : $this->request->getPost('akun_debit_id');
            $akunKreditId = $this->request->getPost('akun_kredit_id');
        } else {
            // Debit: Akun beban, Kredit: Kas/Bank (rekening)
            $akunDebitId  = $this->request->getPost('akun_debit_id');
            $akunKreditId = $akunRek ? $akunRek['id'] : $this->request->getPost('akun_kredit_id');
        }

        // Upload bukti
        $buktiFIle = null;
        $file = $this->request->getFile('bukti_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $buktiFIle = $file->getRandomName();
            $file->move('uploads/keuangan', $buktiFIle);
        }

        $nomor = $this->model->generateNomor();

        $transaksiId = $this->model->insert([
            'nomor_transaksi' => $nomor,
            'tanggal'         => $tanggal,
            'tipe'            => $tipe,
            'kategori'        => $this->request->getPost('kategori'),
            'deskripsi'       => $deskripsi,
            'jumlah'          => $jumlah,
            'rekening_id'     => $rekeningId,
            'akun_debit_id'   => $akunDebitId,
            'akun_kredit_id'  => $akunKreditId,
            'bukti_file'      => $buktiFIle,
            'created_by'      => auth()->id(),
            'status'          => 'posted',
        ]);

        // Auto-buat jurnal double-entry
        $this->buatJurnal($transaksiId, $tanggal, $deskripsi . " [{$nomor}]", $jumlah, $akunDebitId, $akunKreditId);

        return redirect()->to(base_url('admin/keuangan/transaksi'))->with('success', "Transaksi {$nomor} berhasil disimpan.");
    }

    public function show(int $id)
    {
        $transaksi = $this->model->getDetail($id);
        if (!$transaksi) return redirect()->to(base_url('admin/keuangan/transaksi'))->with('error', 'Transaksi tidak ditemukan.');

        $jurnal = $this->jurnalModel->where('transaksi_id', $id)->first();
        if ($jurnal) {
            $jurnal = $this->jurnalModel->getWithDetail($jurnal['id']);
        }

        return view('admin/keuangan/transaksi/show', [
            'title'     => 'Detail Transaksi — ' . $transaksi['nomor_transaksi'],
            'transaksi' => $transaksi,
            'jurnal'    => $jurnal,
        ]);
    }

    public function edit(int $id)
    {
        $transaksi = $this->model->find($id);
        if (!$transaksi) return redirect()->to(base_url('admin/keuangan/transaksi'))->with('error', 'Transaksi tidak ditemukan.');

        return view('admin/keuangan/transaksi/form', [
            'title'     => 'Edit Transaksi',
            'transaksi' => $transaksi,
            'rekenings' => $this->rekeningModel->where('is_active', 1)->findAll(),
            'accounts'  => $this->accountModel->getActive(),
            'tipeHint'  => $transaksi['tipe'],
        ]);
    }

    public function update(int $id)
    {
        if (!$this->validate([
            'tanggal'     => 'required|valid_date',
            'jumlah'      => 'required',
            'rekening_id' => 'required|is_natural_no_zero',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $transaksi  = $this->model->find($id);
        $jumlah     = (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('jumlah'));
        $tipe       = $transaksi['tipe']; // tipe tidak bisa diubah
        $rekeningId = (int) $this->request->getPost('rekening_id');
        $tanggal    = $this->request->getPost('tanggal');

        $rekening = $this->rekeningModel->find($rekeningId);
        $akunRek  = $rekening ? $this->accountModel->find($rekening['akun_id']) : null;

        $akunDebitId  = $this->request->getPost('akun_debit_id');
        $akunKreditId = $this->request->getPost('akun_kredit_id');
        if ($tipe === 'pemasukan' && $akunRek) $akunDebitId  = $akunRek['id'];
        if ($tipe === 'pengeluaran' && $akunRek) $akunKreditId = $akunRek['id'];

        $buktiFile = $transaksi['bukti_file'];
        $file = $this->request->getFile('bukti_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            if ($buktiFile && file_exists('uploads/keuangan/' . $buktiFile)) unlink('uploads/keuangan/' . $buktiFile);
            $buktiFile = $file->getRandomName();
            $file->move('uploads/keuangan', $buktiFile);
        }

        $this->model->update($id, [
            'tanggal'        => $tanggal,
            'kategori'       => $this->request->getPost('kategori'),
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'jumlah'         => $jumlah,
            'rekening_id'    => $rekeningId,
            'akun_debit_id'  => $akunDebitId,
            'akun_kredit_id' => $akunKreditId,
            'bukti_file'     => $buktiFile,
        ]);

        // Update jurnal terkait
        $jurnal = $this->jurnalModel->where('transaksi_id', $id)->first();
        if ($jurnal) {
            $this->jurnalDetailModel->where('jurnal_id', $jurnal['id'])->delete();
            $this->jurnalModel->update($jurnal['id'], ['tanggal' => $tanggal, 'deskripsi' => $this->request->getPost('deskripsi')]);
            $this->insertJurnalDetail($jurnal['id'], $transaksi['nomor_transaksi'], $jumlah, $akunDebitId, $akunKreditId);
        }

        return redirect()->to(base_url('admin/keuangan/transaksi'))->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $transaksi = $this->model->find($id);
        if (!$transaksi) return redirect()->to(base_url('admin/keuangan/transaksi'))->with('error', 'Transaksi tidak ditemukan.');

        // Hapus jurnal terkait
        $jurnal = $this->jurnalModel->where('transaksi_id', $id)->first();
        if ($jurnal) {
            $this->jurnalDetailModel->where('jurnal_id', $jurnal['id'])->delete();
            $this->jurnalModel->delete($jurnal['id']);
        }

        if ($transaksi['bukti_file'] && file_exists('uploads/keuangan/' . $transaksi['bukti_file'])) {
            unlink('uploads/keuangan/' . $transaksi['bukti_file']);
        }

        $this->model->delete($id);
        return redirect()->to(base_url('admin/keuangan/transaksi'))->with('success', 'Transaksi berhasil dihapus.');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function buatJurnal(int $transaksiId, string $tanggal, string $deskripsi, float $jumlah, ?int $debitId, ?int $kreditId): void
    {
        $nomor    = $this->jurnalModel->generateNomor();
        $jurnalId = $this->jurnalModel->insert([
            'nomor_jurnal' => $nomor,
            'tanggal'      => $tanggal,
            'deskripsi'    => $deskripsi,
            'transaksi_id' => $transaksiId,
            'created_by'   => auth()->id(),
        ]);

        $this->insertJurnalDetail($jurnalId, $deskripsi, $jumlah, $debitId, $kreditId);
    }

    private function insertJurnalDetail(int $jurnalId, string $deskripsi, float $jumlah, ?int $debitId, ?int $kreditId): void
    {
        if ($debitId) {
            $this->jurnalDetailModel->insert([
                'jurnal_id' => $jurnalId, 'akun_id' => $debitId,
                'deskripsi' => $deskripsi, 'debit' => $jumlah, 'kredit' => 0,
            ]);
        }
        if ($kreditId) {
            $this->jurnalDetailModel->insert([
                'jurnal_id' => $jurnalId, 'akun_id' => $kreditId,
                'deskripsi' => $deskripsi, 'debit' => 0, 'kredit' => $jumlah,
            ]);
        }
    }
}
