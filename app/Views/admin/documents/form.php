<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($doc); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-<?= $isEdit ? 'pen' : 'upload' ?> me-2 text-gold"></i><?= $isEdit ? 'Edit Metadata Dokumen' : 'Upload Dokumen Baru' ?>
        </h4>
        <p class="text-muted mb-0 small"><?= $isEdit ? 'Perbarui informasi dokumen atau ganti file lampiran arsip.' : 'Unggah berkas dokumen ke pusat arsip digital DPC PKS Panmas.' ?></p>
    </div>
    <a href="<?= base_url('admin/documents') ?>" class="btn btn-sm btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali ke Daftar
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
        <form action="<?= base_url($isEdit ? 'admin/documents/update/' . $doc['id'] : 'admin/documents/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <!-- File Upload Dropzone -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Berkas Dokumen <?= $isEdit ? '<span class="text-muted fw-normal">(Biarkan kosong jika tidak ingin mengganti file)</span>' : '<span class="text-danger">*</span>' ?>
                </label>
                
                <div class="border border-2 border-dashed rounded-3 p-4 text-center" id="dropZone" 
                     style="background: #fafafa; border-color: #cbd5e1 !important; cursor: pointer;">
                    <i class="fa-solid fa-cloud-arrow-up fa-3x mb-2 text-gold"></i>
                    <h6 class="fw-bold mb-1" id="fileLabel">Pilih atau Tarik Berkas Dokumen ke Sini</h6>
                    <p class="text-muted small mb-2">Mendukung format: <strong>PDF, Word (DOCX), Excel (XLSX), PPTX, JPG, PNG, WEBP, TXT, CSV, ZIP</strong> (Maks: 50MB)</p>
                    <button type="button" class="btn btn-sm btn-outline-secondary px-3" onclick="document.getElementById('dokumenFile').click()">
                        <i class="fa-solid fa-folder-open me-1"></i>Jelajahi Berkas Komputer
                    </button>
                    <input type="file" name="dokumen_file" id="dokumenFile" class="d-none" <?= $isEdit ? '' : 'required' ?>
                           accept=".pdf,.docx,.doc,.xlsx,.xls,.pptx,.ppt,.jpg,.jpeg,.png,.webp,.txt,.csv,.zip,.rar"
                           onchange="handleFileSelect(this)">
                </div>

                <?php if ($isEdit && !empty($doc['file_name'])): ?>
                <div class="mt-2 p-2 bg-light rounded d-flex align-items-center justify-content-between border">
                    <div class="small">
                        <i class="fa-solid fa-file me-2 text-primary"></i>File saat ini: <strong><?= esc($doc['file_name']) ?></strong> (<?= round($doc['file_size'] / 1024, 1) ?> KB)
                    </div>
                    <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">
                        <i class="fa-solid fa-download me-1"></i>Unduh
                    </a>
                </div>
                <?php endif; ?>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Judul Dokumen <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control" required
                           value="<?= old('judul', $isEdit ? $doc['judul'] : '') ?>"
                           placeholder="Contoh: SK Pengurus DPC PKS Pancoran Mas 2025-2030">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Nomor Dokumen / Surat</label>
                    <input type="text" name="nomor_dokumen" class="form-control"
                           value="<?= old('nomor_dokumen', $isEdit ? $doc['nomor_dokumen'] : '') ?>"
                           placeholder="Contoh: 001/SK/DPC-PANMAS/2025">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kategori Dokumen</label>
                    <?php $catVal = old('kategori_id', $isEdit ? $doc['kategori_id'] : ($selectedCat ?? '')); ?>
                    <select name="kategori_id" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $catVal == $cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['nama_kategori']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sifat / Kerahasiaan Dokumen <span class="text-danger">*</span></label>
                    <?php $sifatVal = old('sifat_dokumen', $isEdit ? $doc['sifat_dokumen'] : 'internal'); ?>
                    <select name="sifat_dokumen" class="form-select" required>
                        <option value="internal" <?= $sifatVal === 'internal' ? 'selected' : '' ?>>🛡️ Internal Pengurus</option>
                        <option value="publik" <?= $sifatVal === 'publik' ? 'selected' : '' ?>>🌐 Publik (Bisa Diakses Terbuka)</option>
                        <option value="rahasia" <?= $sifatVal === 'rahasia' ? 'selected' : '' ?>>🔒 Rahasia / Khusus BPH</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Dokumen</label>
                    <input type="date" name="tanggal_dokumen" class="form-control"
                           value="<?= old('tanggal_dokumen', $isEdit ? $doc['tanggal_dokumen'] : date('Y-m-d')) ?>">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Bidang / Unit Terkait</label>
                    <?php $bidVal = old('bidang_kode', $isEdit ? $doc['bidang_kode'] : ''); ?>
                    <select name="bidang_kode" class="form-select">
                        <option value="">-- Umum / Lintas Bidang --</option>
                        <?php foreach ($bidangs as $b): ?>
                        <option value="<?= esc($b['kode']) ?>" <?= $bidVal === $b['kode'] ? 'selected' : '' ?>>
                            <?= esc($b['kode']) ?> – <?= esc($b['nama_bidang']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Institusi / Wilayah</label>
                    <?php $instVal = old('institusi', $isEdit ? $doc['institusi'] : 'DPC'); ?>
                    <select name="institusi" class="form-select">
                        <option value="DPC" <?= $instVal === 'DPC' ? 'selected' : '' ?>>DPC Pancoran Mas</option>
                        <option value="DPRa Depok" <?= $instVal === 'DPRa Depok' ? 'selected' : '' ?>>DPRa Depok</option>
                        <option value="DPRa Depok Jaya" <?= $instVal === 'DPRa Depok Jaya' ? 'selected' : '' ?>>DPRa Depok Jaya</option>
                        <option value="DPRa Pancoran Mas" <?= $instVal === 'DPRa Pancoran Mas' ? 'selected' : '' ?>>DPRa Pancoran Mas</option>
                        <option value="DPRa Mampang" <?= $instVal === 'DPRa Mampang' ? 'selected' : '' ?>>DPRa Mampang</option>
                        <option value="DPRa Rangkapan Jaya" <?= $instVal === 'DPRa Rangkapan Jaya' ? 'selected' : '' ?>>DPRa Rangkapan Jaya</option>
                        <option value="DPRa Rangkapan Jaya Baru" <?= $instVal === 'DPRa Rangkapan Jaya Baru' ? 'selected' : '' ?>>DPRa Rangkapan Jaya Baru</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1"
                               <?= old('is_pinned', $isEdit ? $doc['is_pinned'] : 0) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="is_pinned">
                            <i class="fa-solid fa-thumbtack text-warning me-1"></i>Pin / Dokumen Utama
                        </label>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Deskripsi / Keterangan Dokumen</label>
                <textarea name="deskripsi" class="form-control" rows="3"
                          placeholder="Uraian singkat isi berkas, catatan pengesahan, atau instruksi tindak lanjut..."><?= old('deskripsi', $isEdit ? $doc['deskripsi'] : '') ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui Metadata' : 'Simpan & Upload Dokumen' ?>
                </button>
                <a href="<?= base_url('admin/documents') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function handleFileSelect(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const sizeKB = (file.size / 1024).toFixed(1);
        document.getElementById('fileLabel').innerHTML = `<span class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Terpilih: <strong>${file.name}</strong> (${sizeKB} KB)</span>`;
    }
}

// Drag & drop support
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('dokumenFile');

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.style.background = '#f0fdf4';
    dropZone.style.borderColor = '#22c55e';
});

dropZone.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropZone.style.background = '#fafafa';
    dropZone.style.borderColor = '#cbd5e1';
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.style.background = '#fafafa';
    dropZone.style.borderColor = '#cbd5e1';
    if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(fileInput);
    }
});
</script>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
