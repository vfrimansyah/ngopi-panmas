<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$isImage = $item['tipe'] === 'image';
$isVideo = $item['tipe'] === 'video';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-pen-to-square me-2 text-gold"></i>Edit Media Galeri
        </h4>
        <p class="text-muted mb-0 small">Perbarui metadata judul, album kegiatan, keterangan, atau file media.</p>
    </div>
    <a href="<?= base_url('admin/gallery') ?>" class="btn btn-sm btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Galeri
    </a>
</div>

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

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-4">
        <form action="<?= base_url('admin/gallery/update/' . $item['id']) ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Media Preview & Replace Section -->
            <div class="p-3 mb-4 rounded border bg-light">
                <div class="row align-items-center g-3">
                    <div class="col-md-3 text-center">
                        <?php if ($isImage): ?>
                            <img src="<?= base_url($item['file_path']) ?>" class="img-fluid rounded shadow-sm" style="max-height: 140px;">
                        <?php elseif ($isVideo && $item['video_type'] === 'youtube'): ?>
                            <img src="<?= $item['thumbnail_path'] ?: "https://img.youtube.com/vi/{$item['video_id']}/hqdefault.jpg" ?>" class="img-fluid rounded shadow-sm" style="max-height: 140px;">
                        <?php else: ?>
                            <div class="bg-dark text-white rounded p-4 text-center">
                                <i class="fa-solid fa-file-video fa-3x text-warning"></i>
                                <div class="small mt-2"><?= esc($item['file_name']) ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-9">
                        <div class="badge <?= $isImage ? 'bg-primary' : 'bg-danger' ?> text-uppercase mb-1">
                            <?= $isImage ? 'Foto / Gambar' : 'Video (' . strtoupper($item['video_type'] ?? 'VIDEO') . ')' ?>
                        </div>
                        <h6 class="fw-bold text-dark mb-2"><?= esc($item['judul']) ?></h6>

                        <?php if ($isImage): ?>
                            <label class="form-label small fw-semibold">Ganti File Foto (Biarkan kosong jika tidak diganti):</label>
                            <input type="file" name="image_replace" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/gif">
                        <?php elseif ($isVideo && $item['video_type'] === 'youtube'): ?>
                            <label class="form-label small fw-semibold">URL / Link Video YouTube:</label>
                            <input type="url" name="video_url" class="form-control form-control-sm" value="<?= esc($item['video_url']) ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Metadata Form -->
            <div class="row mb-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Judul Media <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required
                           value="<?= old('judul', $item['judul']) ?>">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Album Kegiatan</label>
                    <select name="album_id" class="form-select">
                        <option value="">-- Tanpa Album (Umum) --</option>
                        <?php foreach ($albums as $alb): ?>
                        <option value="<?= $alb['id'] ?>" <?= (old('album_id', $item['album_id']) == $alb['id']) ? 'selected' : '' ?>>
                            <?= esc($alb['nama_album']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Kegiatan</label>
                    <input type="date" name="tanggal_media" class="form-control"
                           value="<?= old('tanggal_media', $item['tanggal_media']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Lokasi Acara</label>
                    <input type="text" name="lokasi" class="form-control"
                           value="<?= old('lokasi', $item['lokasi']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kredit / Fotografer / Videografer</label>
                    <input type="text" name="kredit" class="form-control"
                           value="<?= old('kredit', $item['kredit']) ?>">
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Deskripsi / Keterangan</label>
                    <textarea name="deskripsi" class="form-control" rows="2"><?= old('deskripsi', $item['deskripsi']) ?></textarea>
                </div>
                <div class="col-md-4 pt-3">
                    <div class="form-check form-switch p-3 bg-light rounded border">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                               <?= old('is_featured', $item['is_featured']) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_featured">
                            <i class="fa-solid fa-star text-warning me-1"></i>Tandai Media Unggulan
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i>Perbarui Media
                </button>
                <a href="<?= base_url('admin/gallery') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
