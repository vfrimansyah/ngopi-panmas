<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FinAccountModel;

class KeuanganAccountController extends BaseController
{
    protected $model;
    public function __construct() { $this->model = new FinAccountModel(); }

    public function index()
    {
        $perms = $this->getPagePermissions('keuangan');
        return view('admin/keuangan/accounts/index', [
            'title'      => 'Chart of Accounts',
            'accounts'   => $this->model->orderBy('kode_akun')->findAll(),
            'can_create' => $perms['canCreate'],
            'can_update' => $perms['canUpdate'],
            'can_delete' => $perms['canDelete'],
        ]);
    }

    public function create()
    {
        return view('admin/keuangan/accounts/form', [
            'title'   => 'Tambah Akun',
            'account' => null,
        ]);
    }

    public function store()
    {
        if (!$this->validate([
            'kode_akun' => 'required|max_length[20]|is_unique[fin_accounts.kode_akun]',
            'nama_akun' => 'required|max_length[200]',
            'tipe_akun' => 'required|in_list[kas,bank,piutang,pendapatan,beban,modal,hutang]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->save([
            'kode_akun'      => $this->request->getPost('kode_akun'),
            'nama_akun'      => $this->request->getPost('nama_akun'),
            'tipe_akun'      => $this->request->getPost('tipe_akun'),
            'normal_balance' => $this->request->getPost('normal_balance') ?? 'debit',
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'is_active'      => 1,
        ]);

        return redirect()->to(base_url('admin/keuangan/accounts'))->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $account = $this->model->find($id);
        if (!$account) return redirect()->to(base_url('admin/keuangan/accounts'))->with('error', 'Akun tidak ditemukan.');

        return view('admin/keuangan/accounts/form', [
            'title'   => 'Edit Akun: ' . $account['nama_akun'],
            'account' => $account,
        ]);
    }

    public function update(int $id)
    {
        if (!$this->validate([
            'nama_akun' => 'required|max_length[200]',
            'tipe_akun' => 'required|in_list[kas,bank,piutang,pendapatan,beban,modal,hutang]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'nama_akun'      => $this->request->getPost('nama_akun'),
            'tipe_akun'      => $this->request->getPost('tipe_akun'),
            'normal_balance' => $this->request->getPost('normal_balance') ?? 'debit',
            'deskripsi'      => $this->request->getPost('deskripsi'),
            'is_active'      => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to(base_url('admin/keuangan/accounts'))->with('success', 'Akun berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $this->model->update($id, ['is_active' => 0]);
        return redirect()->to(base_url('admin/keuangan/accounts'))->with('success', 'Akun berhasil dinonaktifkan.');
    }
}
