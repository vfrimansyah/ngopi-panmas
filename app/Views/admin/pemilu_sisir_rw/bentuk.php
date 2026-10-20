<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('admin/pemilu/sisir-rw') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)">
                <i class="fa-solid fa-list-check me-2 text-gold"></i>Master Bentuk Kegiatan
            </h4>
            <small class="text-muted">Kelola kategori bentuk kegiatan Sisir RW.</small>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row g-4">
    <!-- Add Form -->
    <?php if ($can_create ?? false): ?>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                    <i class="fa-solid fa-plus me-2"></i>Tambah Bentuk Kegiatan
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if (session('errors')): ?>
                <div class="alert alert-danger py-2 small">
                    <?php foreach (session('errors') as $e): ?>
                    <div><?= esc($e) ?></div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <form action="<?= base_url('admin/pemilu/sisir-rw/bentuk/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Bentuk Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control"
                               value="<?= old('nama') ?>" required
                               placeholder="Contoh: Pengajian, Gotong Royong...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan Tampil</label>
                        <input type="number" name="urutan" class="form-control" min="0"
                               value="<?= old('urutan', 0) ?>">
                    </div>
                    <button type="submit" class="btn w-100 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                        <i class="fa-solid fa-save me-2"></i>Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- List -->
    <div class="col-md-<?= ($can_create ?? false) ? '8' : '12' ?>">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: var(--dark-coffee); color: var(--cream);">
                        <tr>
                            <th class="ps-4" style="width:60px;">Urutan</th>
                            <th>Nama Bentuk Kegiatan</th>
                            <?php if ($can_delete ?? false): ?>
                            <th class="text-center pe-4" style="width:100px;">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($bentukList)): ?>
                        <?php foreach ($bentukList as $b): ?>
                        <tr>
                            <td class="ps-4 text-center fw-bold text-muted"><?= $b['urutan'] ?></td>
                            <td>
                                <span class="badge px-3 py-2 fw-semibold fs-7"
                                      style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)33;">
                                    <?= esc($b['nama']) ?>
                                </span>
                            </td>
                            <?php if ($can_delete ?? false): ?>
                            <td class="text-center pe-4">
                                <a href="<?= base_url('admin/pemilu/sisir-rw/bentuk/delete/' . $b['id']) ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Hapus bentuk kegiatan <?= esc($b['nama']) ?>?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada bentuk kegiatan.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
