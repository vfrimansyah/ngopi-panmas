<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-images me-2 text-gold"></i>Manajemen Album Kegiatan Galeri
        </h4>
        <p class="text-muted mb-0 small">Kelola folder/album pengelompokan dokumentasi foto dan video acara DPC & DPRa.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/gallery') ?>" class="btn btn-sm btn-outline-secondary px-3">
            <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Galeri Media
        </a>
        <?php if ($can_create ?? false): ?>
        <button type="button" class="btn btn-sm btn-gold px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#addAlbumModal">
            <i class="fa-solid fa-plus me-1"></i>Tambah Album Baru
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

<!-- Album Cards Grid -->
<div class="row g-3 mb-4">
    <?php if (!empty($albums)): ?>
        <?php foreach ($albums as $alb): ?>
            <?php 
            $isOwner = !empty($is_full_admin) || ((int)($alb['created_by'] ?? 0) === (int)($current_user_id ?? 0));
            $coverUrl = !empty($alb['cover_image']) ? base_url($alb['cover_image']) : base_url('assets/images/album_placeholder.jpg');
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm hover-elevate album-card" style="border-radius: 12px; overflow: hidden;">
                    <!-- Cover Thumbnail -->
                    <div class="position-relative" style="height: 160px; background: #262626;">
                        <img src="<?= $coverUrl ?>" alt="<?= esc($alb['nama_album']) ?>" class="w-100 h-100 object-fit-cover" onerror="this.src='https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&q=80'">
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-dark bg-opacity-75">
                                <?= $alb['total_items'] ?? 0 ?> Media
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="fw-bold text-dark mb-1 text-truncate-2" title="<?= esc($alb['nama_album']) ?>" style="min-height: 2.7em;">
                            <?= esc($alb['nama_album']) ?>
                        </h6>
                        <p class="small text-muted mb-2 text-truncate-2" style="font-size: 0.8rem;"><?= esc($alb['deskripsi'] ?: 'Album dokumentasi kegiatan.') ?></p>

                        <div class="small text-muted mb-3 d-flex justify-content-between" style="font-size: 0.75rem;">
                            <span><i class="fa-solid fa-camera text-primary me-1"></i><?= $alb['total_images'] ?? 0 ?> Foto</span>
                            <span><i class="fa-brands fa-youtube text-danger me-1"></i><?= $alb['total_videos'] ?? 0 ?> Video</span>
                            <span><i class="fa-regular fa-calendar me-1"></i><?= $alb['tanggal_kegiatan'] ? date('d/m/y', strtotime($alb['tanggal_kegiatan'])) : '-' ?></span>
                        </div>

                        <div class="mt-auto pt-2 border-top d-flex gap-1">
                            <a href="<?= base_url('admin/gallery?album_id=' . $alb['id']) ?>" class="btn btn-sm btn-outline-primary flex-grow-1" title="Lihat Media">
                                <i class="fa-solid fa-folder-open me-1"></i>Buka Album
                            </a>
                            <?php if (($can_update ?? false) && $isOwner): ?>
                            <button type="button" class="btn btn-sm btn-outline-warning" title="Edit Album"
                                    data-bs-toggle="modal" data-bs-target="#editAlbumModal<?= $alb['id'] ?>">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <?php endif; ?>
                            <?php if (($can_delete ?? false) && $isOwner): ?>
                            <a href="<?= base_url('admin/gallery/albums/delete/' . $alb['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus Album"
                               onclick="return confirm('Hapus album ini? Foto & video di dalamnya tidak akan terhapus, hanya status albumnya menjadi umum.')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Modal Edit Album -->
                <div class="modal fade" id="editAlbumModal<?= $alb['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header text-white" style="background: #3e2723;">
                                <h6 class="modal-title fw-bold text-white"><i class="fa-solid fa-pen me-2 text-gold"></i>Edit Album Kegiatan</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="<?= base_url('admin/gallery/albums/update/' . $alb['id']) ?>" method="POST" enctype="multipart/form-data">
                                <?= csrf_field() ?>
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Nama Album <span class="text-danger">*</span></label>
                                        <input type="text" name="nama_album" class="form-control" required value="<?= esc($alb['nama_album']) ?>">
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Tanggal Acara</label>
                                            <input type="date" name="tanggal_kegiatan" class="form-control" value="<?= esc($alb['tanggal_kegiatan']) ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Lokasi</label>
                                            <input type="text" name="lokasi" class="form-control" value="<?= esc($alb['lokasi']) ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Foto Cover Album (Opsional)</label>
                                        <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Urutan Tampilan</label>
                                        <input type="number" name="sort_order" class="form-control" value="<?= $alb['sort_order'] ?>">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold">Deskripsi Album</label>
                                        <textarea name="deskripsi" class="form-control" rows="2"><?= esc($alb['deskripsi']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-gold fw-semibold px-4">Perbarui Album</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /Modal Edit -->
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center text-muted py-5 bg-white rounded-3 shadow-sm">
            <i class="fa-solid fa-images fa-3x mb-3 text-muted opacity-50"></i>
            <h6>Belum ada album kegiatan yang dibuat.</h6>
        </div>
    <?php endif; ?>
</div>

<!-- Modal Add Album -->
<div class="modal fade" id="addAlbumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: #3e2723;">
                <h6 class="modal-title fw-bold text-white"><i class="fa-solid fa-plus me-2 text-gold"></i>Tambah Album Kegiatan Baru</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/gallery/albums/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Album Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_album" class="form-control" required placeholder="Contoh: Musyawarah Cabang DPC Panmas 2025">
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Acara</label>
                            <input type="date" name="tanggal_kegiatan" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lokasi Acara</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Contoh: Gedung Serbaguna Panmas">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Foto Cover Album (Opsional)</label>
                        <input type="file" name="cover_image" class="form-control" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Urutan Tampilan</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= count($albums) + 1 ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Deskripsi Album</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Catatan singkat tentang acara atau kegiatan ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold fw-semibold px-4">Simpan Album</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
.album-card:hover {
    transform: translateY(-4px);
    transition: transform 0.2s ease-in-out;
}
</style>

<?= $this->endSection() ?>
