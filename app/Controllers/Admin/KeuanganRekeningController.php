<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinRekeningModel;
use App\Models\FinAccountModel;

class KeuanganRekeningController extends BaseController
{
    protected $model;
    protected $accountModel;

    public function __construct()
    {
        $this->model        = new FinRekeningModel();
        $this->accountModel = new FinAccountModel();
    }

    public function index()
    {
        $rekenings = $this->model->getWithAkun();
        foreach ($rekenings as &$r) {
            $r['saldo_berjalan'] = $this->model->getSaldoBerjalan($r['id']);
        }

        $perms = $this->getPagePermissions('keuangan');
        return view('admin/keuangan/rekening/index', [
            'title'      => 'Rekening & Kas',
            'rekenings'  => $rekenings,
            'totalSaldo' => array_sum(array_column($rekenings, 'saldo_berjalan')),
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);
    }

    public function create()
    {
        return view('admin/keuangan/rekening/form', [
            'title'    => 'Tambah Rekening',
            'rekening' => null,
            'accounts' => $this->accountModel->getKasDanBank(),
        ]);
    }

    public function store()
    {
        if (!$this->validate(['nama_rekening' => 'required|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'nama_rekening'  => $this->request->getPost('nama_rekening'),
            'nomor_rekening' => $this->request->getPost('nomor_rekening'),
            'bank'           => $this->request->getPost('bank'),
            'tipe'           => $this->request->getPost('tipe'),
            'akun_id'        => $this->request->getPost('akun_id') ?: null,
            'saldo_awal'     => (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('saldo_awal') ?? '0'),
            'is_active'      => 1,
        ]);

        return redirect()->to(base_url('admin/keuangan/rekening'))->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $rekening = $this->model->find($id);
        if (!$rekening) return redirect()->to(base_url('admin/keuangan/rekening'))->with('error', 'Rekening tidak ditemukan.');

        return view('admin/keuangan/rekening/form', [
            'title'    => 'Edit Rekening',
            'rekening' => $rekening,
            'accounts' => $this->accountModel->getKasDanBank(),
        ]);
    }

    public function update(int $id)
    {
        if (!$this->validate(['nama_rekening' => 'required|max_length[150]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'nama_rekening'  => $this->request->getPost('nama_rekening'),
            'nomor_rekening' => $this->request->getPost('nomor_rekening'),
            'bank'           => $this->request->getPost('bank'),
            'tipe'           => $this->request->getPost('tipe'),
            'akun_id'        => $this->request->getPost('akun_id') ?: null,
            'saldo_awal'     => (float) str_replace(['.', ','], ['', '.'], $this->request->getPost('saldo_awal') ?? '0'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('admin/keuangan/rekening'))->with('success', 'Rekening berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('admin/keuangan/rekening'))->with('success', 'Rekening dinonaktifkan.');
    }
}
