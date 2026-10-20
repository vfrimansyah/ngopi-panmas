<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-cloud-arrow-up me-2 text-gold"></i>Upload Foto Galeri (Bisa Multiple File)
        </h4>
        <p class="text-muted mb-0 small">Unggah satu atau banyak foto dokumentasi kegiatan DPC & DPRa sekaligus.</p>
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
        <form action="<?= base_url('admin/gallery/store-image') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- MULTIPLE FILE UPLOAD DROPZONE -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Pilih Berkas Foto <span class="text-danger">* (Bisa pilih / drag banyak foto sekaligus)</span>
                </label>
                
                <div class="border border-2 border-dashed rounded-3 p-4 text-center" id="dropZone" 
                     style="background: #f8fafc; border-color: #cbd5e1 !important; cursor: pointer;">
                    <i class="fa-solid fa-images fa-3x mb-2 text-gold"></i>
                    <h6 class="fw-bold mb-1" id="fileLabel">Tarik Banyak Foto ke Sini atau Klik untuk Memilih</h6>
                    <p class="text-muted small mb-2">Mendukung format: <strong>JPG, JPEG, PNG, WEBP, GIF</strong> (Maks: 20MB per foto)</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary px-4" onclick="document.getElementById('imageFiles').click()">
                        <i class="fa-solid fa-folder-open me-1"></i>Jelajahi Berkas Foto
                    </button>
                    <input type="file" name="images[]" id="imageFiles" class="d-none" multiple required
                           accept="image/jpeg,image/png,image/webp,image/gif,image/jpg"
                           onchange="handleMultipleFiles(this)">
                </div>

                <!-- Preview Selected Files Grid -->
                <div id="previewContainer" class="row g-2 mt-3 d-none">
                    <div class="col-12">
                        <div class="small fw-bold text-muted mb-1" id="selectedCountText">Pratinjau Foto Terpilih:</div>
                    </div>
                </div>
            </div>

            <!-- METADATA FORM -->
            <div class="row mb-3">
                <div class="col-12 mb-2">
                    <label class="form-label fw-bold mb-1">
                        <i class="fa-solid fa-signature text-gold me-1"></i>Pilihan Judul / Nama Dokumentasi Foto
                    </label>
                    <div class="card bg-light border p-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check p-2 rounded bg-white border h-100">
                                    <input class="form-check-input ms-1 me-2" type="radio" name="title_mode" id="mode_filename" value="filename" 
                                           <?= old('title_mode', 'filename') === 'filename' ? 'checked' : '' ?> onchange="toggleTitleMode()">
                                    <label class="form-check-label fw-semibold" for="mode_filename" style="cursor: pointer;">
                                        <i class="fa-solid fa-file-image text-primary me-1"></i>Ambil dari Nama File Foto (Rekomendasi)
                                    </label>
                                    <div class="text-muted ps-4" style="font-size: 0.75rem;">
                                        Judul otomatis diambil dari nama file masing-masing (misal: <em>"Bendahara 1"</em>, <em>"Ketua DPC"</em>, <em>"DPRa Panmas"</em>).
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check p-2 rounded bg-white border h-100">
                                    <input class="form-check-input ms-1 me-2" type="radio" name="title_mode" id="mode_custom" value="custom" 
                                           <?= old('title_mode') === 'custom' ? 'checked' : '' ?> onchange="toggleTitleMode()">
                                    <label class="form-check-label fw-semibold" for="mode_custom" style="cursor: pointer;">
                                        <i class="fa-solid fa-pen-to-square text-success me-1"></i>Satu Judul Seragam (+ Nomor Urut)
                                    </label>
                                    <div class="text-muted ps-4" style="font-size: 0.75rem;">
                                        Semua foto memakai judul yang sama & dinomori urut (misal: <em>"Pelantikan DPC - Foto 1"</em>, <em>"Pelantikan DPC - Foto 2"</em>).
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7" id="customTitleWrapper">
                    <label class="form-label fw-semibold" id="judulLabel">Awalan / Judul Dokumentasi</label>
                    <input type="text" name="judul" id="inputJudul" class="form-control"
                           value="<?= old('judul') ?>"
                           placeholder="Opsional: Tambahkan awalan (contoh: Dokumentasi Rakerda)">
                    <small class="text-muted" id="judulHelpText" style="font-size: 0.75rem;">
                        Bisa dikosongkan jika ingin murni memakai nama file asli.
                    </small>
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
                           placeholder="Contoh: Lapangan RW 05 Depok Jaya">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fotografer / Kredit</label>
                    <input type="text" name="kredit" class="form-control"
                           value="<?= old('kredit') ?>"
                           placeholder="Contoh: Tim Media Komdigi">
                </div>
            </div>

            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Deskripsi / Keterangan Dokumentasi</label>
                    <textarea name="deskripsi" class="form-control" rows="2"
                              placeholder="Uraian singkat momen kegiatan atau catatan dokumentasi..."><?= old('deskripsi') ?></textarea>
                </div>
                <div class="col-md-4 pt-3">
                    <div class="form-check form-switch p-3 bg-light rounded border">
                        <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" value="1"
                               <?= old('is_featured') ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_featured">
                            <i class="fa-solid fa-star text-warning me-1"></i>Tandai Foto Unggulan
                        </label>
                        <div class="text-muted" style="font-size: 0.72rem;">Foto unggulan akan diprioritaskan tampil di slider & halaman utama galeri.</div>
                    </div>
                </div>
            </div>

            <!-- Warning Banner for Large Batch -->
            <div id="sizeWarning" class="alert alert-warning border-0 shadow-sm d-none mb-3">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                <span id="sizeWarningText"></span>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <button type="submit" id="submitBtn" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-cloud-arrow-up me-2"></i>Mulai Upload Foto
                </button>
                <div id="uploadSpinner" class="d-none align-items-center text-muted small ms-2">
                    <div class="spinner-border spinner-border-sm text-warning me-2" role="status"></div>
                    <span id="spinnerText">Sedang mengunggah foto... Mohon tunggu.</span>
                </div>
                <a href="<?= base_url('admin/gallery') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function handleMultipleFiles(input) {
    const container = document.getElementById('previewContainer');
    const label = document.getElementById('fileLabel');
    const countText = document.getElementById('selectedCountText');
    const sizeWarning = document.getElementById('sizeWarning');
    const sizeWarningText = document.getElementById('sizeWarningText');
    const submitBtn = document.getElementById('submitBtn');

    if (input.files && input.files.length > 0) {
        const files = Array.from(input.files);
        let totalBytes = 0;
        files.forEach(f => totalBytes += f.size);
        const totalMb = (totalBytes / (1024 * 1024)).toFixed(1);

        label.innerHTML = `<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-1"></i>${files.length} Foto Siap Diupload (Total: ${totalMb} MB)</span>`;
        countText.innerText = `${files.length} Foto Terpilih (Total: ${totalMb} MB):`;
        container.classList.remove('d-none');

        // Check if total size exceeds 120MB
        if (totalBytes > 120 * 1024 * 1024) {
            sizeWarningText.innerText = `Ukuran total foto (${totalMb} MB) cukup besar. Disarankan mengunggah bertahap (10–20 foto per upload) agar proses transfer berjalan lancar.`;
            sizeWarning.classList.remove('d-none');
        } else {
            sizeWarning.classList.add('d-none');
        }

        // Clear existing previews
        container.querySelectorAll('.preview-col').forEach(el => el.remove());

        files.slice(0, 24).forEach((file, idx) => {
            const fileMb = (file.size / (1024 * 1024)).toFixed(1);
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-3 col-lg-2 preview-col';
                col.innerHTML = `
                    <div class="card border shadow-sm overflow-hidden h-100 position-relative">
                        <img src="${e.target.result}" class="card-img-top object-fit-cover" style="height: 90px;">
                        <div class="p-1 bg-white text-truncate text-center" style="font-size: 0.68rem;" title="${file.name}">
                            ${file.name}
                            <div class="text-muted" style="font-size: 0.62rem;">${fileMb} MB</div>
                        </div>
                        <div class="position-absolute top-0 start-0 m-1">
                            <span class="badge bg-dark bg-opacity-75" style="font-size: 0.6rem;">#${idx + 1}</span>
                        </div>
                    </div>
                `;
                container.appendChild(col);
            };
            reader.readAsDataURL(file);
        });

        if (files.length > 24) {
            const extra = document.createElement('div');
            extra.className = 'col-12 preview-col small text-muted text-center mt-2';
            extra.innerText = `...dan ${files.length - 24} foto lainnya.`;
            container.appendChild(extra);
        }
    }
}

// Drag & drop support
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('imageFiles');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.background = '#f0fdf4';
    dropZone.style.borderColor = '#22c55e';
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.style.background = '#f8fafc';
    dropZone.style.borderColor = '#cbd5e1';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.background = '#f8fafc';
    dropZone.style.borderColor = '#cbd5e1';
    if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        fileInput.files = e.dataTransfer.files;
        handleMultipleFiles(fileInput);
    }
});

// Toggle Title Naming Mode
function toggleTitleMode() {
    const isCustom = document.getElementById('mode_custom') && document.getElementById('mode_custom').checked;
    const label = document.getElementById('judulLabel');
    const input = document.getElementById('inputJudul');
    const help = document.getElementById('judulHelpText');

    if (!label || !input || !help) return;

    if (isCustom) {
        label.innerHTML = 'Judul Seragam Dokumentasi <span class="text-danger">*</span>';
        input.placeholder = 'Contoh: Senam Sehat DPC & Pembagian Sayur Gratis';
        input.required = true;
        help.innerHTML = 'Semua foto akan diberi judul ini dan otomatis dinomori urut: <em>"Judul - Foto 1"</em>, <em>"Judul - Foto 2"</em>, dst.';
    } else {
        label.innerHTML = 'Awalan Judul (Opsional)';
        input.placeholder = 'Opsional: Tambahkan awalan (contoh: Rakerda 2026)';
        input.required = false;
        help.innerHTML = 'Judul foto diambil dari nama file aslinya (misal: <em>"Bendahara 1"</em>, <em>"Ketua DPC"</em>). Jika awalan diisi, menjadi <em>"Awalan - Nama File"</em>.';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleTitleMode();
});
</script>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
