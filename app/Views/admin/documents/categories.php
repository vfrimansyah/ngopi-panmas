<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-tags me-2 text-gold"></i>Kategori & Klasifikasi Dokumen
        </h4>
        <p class="text-muted mb-0 small">Kelola klasifikasi folder pengarsipan dokumen resmi DPC & DPRa.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/documents') ?>" class="btn btn-sm btn-outline-secondary px-3">
            <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Dokumen
        </a>
        <?php if ($can_create ?? false): ?>
        <button type="button" class="btn btn-sm btn-gold px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fa-solid fa-plus me-1"></i>Tambah Kategori
        </button>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <ul class="mb-0 ps-3">
        <?php foreach (session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Category Cards Grid -->
<div class="row g-3 mb-4">
    <?php foreach ($categories as $cat): ?>
        <div class="col-md-6 col-lg-3">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 12px; border-top: 4px solid <?= esc($cat['warna'] ?: '#3b82f6') ?> !important;">
                <div class="card-body p-3 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="avatar d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 44px; height: 44px; background: <?= esc($cat['warna'] ?: '#3b82f6') ?>18; color: <?= esc($cat['warna'] ?: '#3b82f6') ?>;">
                            <i class="fa-solid <?= esc($cat['icon'] ?: 'fa-folder') ?> fs-5"></i>
                        </div>
                        <span class="badge bg-light text-dark border">
                            <?= $cat['total_documents'] ?> Dokumen
                        </span>
                    </div>

                    <h6 class="fw-bold text-dark mb-1"><?= esc($cat['nama_kategori']) ?></h6>
                    <p class="small text-muted mb-3 flex-grow-1" style="font-size: 0.8rem;"><?= esc($cat['deskripsi'] ?: 'Klasifikasi arsip dokumen.') ?></p>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <small class="text-muted" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-hard-drive me-1"></i><?= round($cat['total_size'] / 1024, 1) ?> KB
                        </small>
                        <div class="d-flex gap-1">
                            <a href="<?= base_url('admin/documents?kategori=' . $cat['id']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" title="Lihat Dokumen">
                                <i class="fa-solid fa-folder-open"></i>
                            </a>
                            <?php if ($can_update ?? false): ?>
                            <button type="button" class="btn btn-sm btn-outline-warning py-0 px-2" title="Edit"
                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal<?= $cat['id'] ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <?php endif; ?>
                            <?php if ($can_delete ?? false): ?>
                            <a href="<?= base_url('admin/documents/categories/delete/' . $cat['id']) ?>" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus"
                               onclick="return confirm('Hapus kategori ini?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Edit Category -->
            <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header text-white" style="background: #3e2723;">
                            <h6 class="modal-title fw-bold text-white"><i class="fa-solid fa-pen me-2 text-gold"></i>Edit Kategori</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="<?= base_url('admin/documents/categories/update/' . $cat['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_kategori" class="form-control" required value="<?= esc($cat['nama_kategori']) ?>">
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Ikon FontAwesome</label>
                                        <input type="text" name="icon" class="form-control" value="<?= esc($cat['icon']) ?>" placeholder="Contoh: fa-stamp">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Warna Aksen</label>
                                        <input type="color" name="warna" class="form-control form-control-color w-100" value="<?= esc($cat['warna'] ?: '#3b82f6') ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Urutan Tampilan</label>
                                    <input type="number" name="sort_order" class="form-control" value="<?= $cat['sort_order'] ?>">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="2"><?= esc($cat['deskripsi']) ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-gold fw-semibold px-4">Perbarui</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Modal Edit -->
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal Add Category -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: #3e2723;">
                <h6 class="modal-title fw-bold text-white"><i class="fa-solid fa-plus me-2 text-gold"></i>Tambah Kategori Dokumen</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/documents/categories/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kategori" class="form-control" required placeholder="Contoh: Surat Edaran & Instruksi">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ikon FontAwesome</label>
                            <input type="text" name="icon" class="form-control" value="fa-folder" placeholder="fa-folder">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Warna Aksen</label>
                            <input type="color" name="warna" class="form-control form-control-color w-100" value="#3b82f6">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan Tampilan</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= count($categories) + 1 ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Uraian jenis berkas yang masuk dalam kategori ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold fw-semibold px-4">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
