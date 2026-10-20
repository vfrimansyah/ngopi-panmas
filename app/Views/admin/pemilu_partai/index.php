<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-flag me-2 text-gold"></i>Daftar Partai Politik
        </h4>
        <p class="text-muted mb-0 small">Kelola data dasar partai politik yang terdaftar.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/pemilu/partai/create') ?>" class="btn btn-sm btn-gold px-4">
        <i class="fa-solid fa-plus me-2"></i>Tambah Partai
    </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="partaiTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Logo</th>
                        <th>Nama Partai</th>
                        <th>Tahun Berdiri</th>
                        <th>Azas / Ideologi</th>
                        <th>Cakupan</th>
                        <th>Alamat</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($partai)): ?>
                        <?php foreach ($partai as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <?php if (!empty($p['lambang_partai'])): ?>
                                        <img src="<?= base_url('uploads/partai/' . $p['lambang_partai']) ?>" alt="Logo" class="img-thumbnail rounded" style="max-height: 50px; max-width: 50px;">
                                    <?php else: ?>
                                        <div class="bg-light rounded text-center py-2 text-muted" style="height: 50px; width: 50px;">
                                            <i class="fa-solid fa-flag fa-lg"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark-coffee"><?= esc($p['nama_partai']) ?></span>
                                </td>
                                <td><?= esc($p['tahun_berdiri']) ?: '-' ?></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= esc($p['azas_ideologi']) ?: '-' ?></span>
                                </td>
                                <td>
                                    <?php if ($p['cakupan'] === 'Nasional'): ?>
                                        <span class="badge bg-success-subtle text-success">Nasional</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary">Lokal</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.8rem;" class="text-muted"><?= esc($p['alamat']) ?: '-' ?></td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/pemilu/partai/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/pemilu/partai/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus data Partai <?= esc($p['nama_partai']) ?>?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-gold {
        background-color: #d4af37 !important;
        color: #3e2723 !important;
        font-weight: 600;
        border: none;
    }
    .btn-gold:hover {
        background-color: #c5a028 !important;
        color: #3e2723 !important;
    }
    .text-dark-coffee {
        color: #3e2723 !important;
    }
</style>

<?= $this->endSection() ?>
