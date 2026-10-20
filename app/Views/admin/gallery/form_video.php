<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-brands fa-youtube me-2 text-danger"></i>Tambah Video Dokumentasi
        </h4>
        <p class="text-muted mb-0 small">Tambahkan video kegiatan DPC & DPRa melalui tautan YouTube atau unggah file video langsung.</p>
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
        <form action="<?= base_url('admin/gallery/store-video') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- Video Source Selector -->
            <div class="mb-4">
                <label class="form-label fw-semibold">Sumber Video <span class="text-danger">*</span></label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="card p-3 border-2 h-100 cursor-pointer source-card active" id="cardYoutube" style="cursor: pointer; border-radius: 10px;">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="video_source" value="youtube" class="form-check-input me-3" checked onchange="toggleVideoSource('youtube')">
                                <div>
                                    <h6 class="fw-bold mb-1 text-danger"><i class="fa-brands fa-youtube me-2"></i>Tautan YouTube (Direkomendasikan)</h6>
                                    <small class="text-muted">Cukup masukkan link video YouTube, thumbnail dan ID video akan ditarik otomatis.</small>
                                </div>
                            </div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="card p-3 border-2 h-100 cursor-pointer source-card" id="cardUpload" style="cursor: pointer; border-radius: 10px;">
                            <div class="d-flex align-items-center">
                                <input type="radio" name="video_source" value="upload" class="form-check-input me-3" onchange="toggleVideoSource('upload')">
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary"><i class="fa-solid fa-file-video me-2"></i>Upload File Video MP4</h6>
                                    <small class="text-muted">Unggah berkas video MP4, WebM langsung dari komputer Anda.</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- YouTube Input Section -->
            <div id="sectionYoutube" class="p-3 mb-4 rounded border bg-light">
                <label class="form-label fw-semibold">URL / Link Video YouTube <span class="text-danger">*</span></label>
                <div class="input-group mb-2">
                    <span class="input-group-text bg-white"><i class="fa-brands fa-youtube text-danger"></i></span>
                    <input type="url" name="video_url" id="youtubeUrlInput" class="form-control"
                           value="<?= old('video_url') ?>"
                           placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/..."
                           oninput="previewYouTubeVideo(this.value)">
                </div>
                <small class="text-muted d-block mb-3">Mendukung semua format link YouTube (desktop, mobile, shortlink youtu.be, shorts).</small>

                <!-- Live YouTube Preview -->
                <div id="ytPreviewContainer" class="ratio ratio-16x9 rounded overflow-hidden shadow-sm d-none" style="max-width: 480px; background: #000;">
                    <iframe id="ytPreviewIframe" src="" allowfullscreen></iframe>
                </div>
            </div>

            <!-- Video File Upload Section -->
            <div id="sectionUpload" class="p-3 mb-4 rounded border bg-light d-none">
                <div class="row g-3">
                    <div class="col-md-7">
                        <label class="form-label fw-semibold">Berkas Video MP4 / WebM <span class="text-danger">*</span></label>
                        <input type="file" name="video_file" class="form-control" accept="video/mp4,video/webm,video/ogg">
                        <small class="text-muted">Format: MP4, WebM (Maks: 100MB)</small>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Cover Thumbnail (Opsional)</label>
                        <input type="file" name="custom_thumbnail" class="form-control" accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Gambar cover pratinjau sebelum video diputar.</small>
                    </div>
                </div>
            </div>

            <!-- Metadata Form -->
            <div class="row mb-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Judul Video <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required
                           value="<?= old('judul') ?>"
                           placeholder="Contoh: Orasi Kebangsaan & Konsolidasi Kader PKS Pancoran Mas">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">Album Kegiatan</label>
                    <select name="album_id" class="form-select">
                        <option value="">-- Tanpa Album (Umum) --</option>
                        <?php foreach ($albums as $alb): ?>
                        <option value="<?= $alb['id'] ?>" <?= (old('album_id') == $alb['id'] || ($selectedAlb ?? '') == $alb['id']) ? 'selected' : '' ?>>
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
                           value="<?= old('tanggal_media', date('Y-m-d')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Lokasi Acara</label>
                    <input type="text" name="lokasi" class="form-control"
                           value="<?= old('lokasi') ?>"
                           placeholder="Contoh: Aula DPC Panmas">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Videografer / Kredit</label>
                    <input type="text" name="kredit" class="form-control"
                           value="<?= old('kredit') ?>"
                           placeholder="Contoh: Tim Media Komdigi">
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Deskripsi / Ringkasan Video</label>
                    <textarea name="deskripsi" class="form-control" rows="2"
                              placeholder="Uraian isi rekaman video atau catatan acara..."><?= old('deskripsi') ?></textarea>
                </div>
                <div class="col-md-4 pt-3">
                    <div class="form-check form-switch p-3 bg-light rounded border">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                               <?= old('is_featured') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_featured">
                            <i class="fa-solid fa-star text-warning me-1"></i>Tandai Video Unggulan
                        </label>
                        <div class="text-muted" style="font-size: 0.72rem;">Video unggulan akan diprioritaskan tampil di halaman utama galeri.</div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i>Simpan Video Galeri
                </button>
                <a href="<?= base_url('admin/gallery') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function toggleVideoSource(src) {
    const secYt = document.getElementById('sectionYoutube');
    const secUp = document.getElementById('sectionUpload');
    const cardYt = document.getElementById('cardYoutube');
    const cardUp = document.getElementById('cardUpload');

    if (src === 'youtube') {
        secYt.classList.remove('d-none');
        secUp.classList.add('d-none');
        cardYt.classList.add('border-danger', 'bg-danger-subtle');
        cardUp.classList.remove('border-primary', 'bg-primary-subtle');
    } else {
        secYt.classList.add('d-none');
        secUp.classList.remove('d-none');
        cardUp.classList.add('border-primary', 'bg-primary-subtle');
        cardYt.classList.remove('border-danger', 'bg-danger-subtle');
    }
}

function extractYTId(url) {
    const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
    const match = url.match(regExp);
    return (match && match[2].length === 11) ? match[2] : null;
}

function previewYouTubeVideo(url) {
    const ytId = extractYTId(url);
    const container = document.getElementById('ytPreviewContainer');
    const iframe = document.getElementById('ytPreviewIframe');

    if (ytId) {
        iframe.src = `https://www.youtube.com/embed/${ytId}`;
        container.classList.remove('d-none');
    } else {
        iframe.src = '';
        container.classList.add('d-none');
    }
}

// Initial state trigger
document.addEventListener('DOMContentLoaded', function () {
    const initialUrl = document.getElementById('youtubeUrlInput').value;
    if (initialUrl) previewYouTubeVideo(initialUrl);
});
</script>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
