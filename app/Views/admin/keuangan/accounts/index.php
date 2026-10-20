<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<?php
$tipeMap = [
    'kas'        => ['label'=>'Kas',        'color'=>'#10b981','badge'=>'success'],
    'bank'       => ['label'=>'Bank',       'color'=>'#3b82f6','badge'=>'info'],
    'piutang'    => ['label'=>'Piutang',    'color'=>'#f59e0b','badge'=>'warning'],
    'pendapatan' => ['label'=>'Pendapatan', 'color'=>'#6366f1','badge'=>'primary'],
    'beban'      => ['label'=>'Beban',      'color'=>'#ef4444','badge'=>'danger'],
    'modal'      => ['label'=>'Modal',      'color'=>'#0ea5e9','badge'=>'info'],
    'hutang'     => ['label'=>'Hutang',     'color'=>'#f97316','badge'=>'warning'],
];
?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)">
            <i class="fa-solid fa-sitemap me-2 text-gold"></i>Chart of Accounts
        </h4>
        <p class="text-muted mb-0 small">Daftar akun keuangan yang digunakan dalam pencatatan transaksi.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/keuangan/accounts/create') ?>" class="btn btn-sm px-4 fw-semibold"
       style="background:#3d1f0f;color:#fff;border:none;">
        <i class="fa-solid fa-plus me-2"></i>Tambah Akun
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 no-datatable" id="accountsTable">
                <thead style="background:var(--dark-coffee);color:var(--cream);">
                    <tr>
                        <th class="ps-4">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th>Tipe</th>
                        <th>Normal Balance</th>
                        <th>Status</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($accounts as $acc):
                        $tm = $tipeMap[$acc['tipe_akun']] ?? ['label'=>$acc['tipe_akun'],'color'=>'#6b7280','badge'=>'secondary'];
                    ?>
                    <tr class="<?= !$acc['is_active'] ? 'opacity-50' : '' ?>">
                        <td class="ps-4 fw-bold font-monospace" style="color:var(--dark-coffee)"><?= esc($acc['kode_akun']) ?></td>
                        <td>
                            <div class="fw-semibold"><?= esc($acc['nama_akun']) ?></div>
                            <?php if ($acc['deskripsi']): ?>
                            <small class="text-muted"><?= esc($acc['deskripsi']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td><span class="badge bg-<?= $tm['badge'] ?>"><?= $tm['label'] ?></span></td>
                        <td>
                            <span class="badge <?= $acc['normal_balance']==='debit'?'bg-info':'bg-warning text-dark' ?>">
                                <?= ucfirst($acc['normal_balance']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($acc['is_active']): ?>
                            <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-circle-check me-1"></i>Aktif</span>
                            <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/keuangan/accounts/edit/' . $acc['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                <?php endif; ?>
                                <?php if ($can_delete ?? false): ?>
                                <a href="<?= base_url('admin/keuangan/accounts/delete/' . $acc['id']) ?>"
                                   class="btn btn-sm btn-outline-warning" title="Nonaktifkan"
                                   onclick="return confirm('Nonaktifkan akun <?= esc($acc['nama_akun']) ?>?')">
                                    <i class="fa-solid fa-eye-slash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
