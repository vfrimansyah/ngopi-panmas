<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$ext = strtolower($doc['file_type']);
$iconClass = 'fa-file-lines text-secondary';
$badgeBg = 'bg-secondary';
if ($ext === 'pdf') {
    $iconClass = 'fa-file-pdf text-danger';
    $badgeBg = 'bg-danger';
} elseif (in_array($ext, ['doc', 'docx'])) {
    $iconClass = 'fa-file-word text-primary';
    $badgeBg = 'bg-primary';
} elseif (in_array($ext, ['xls', 'xlsx'])) {
    $iconClass = 'fa-file-excel text-success';
    $badgeBg = 'bg-success';
} elseif (in_array($ext, ['ppt', 'pptx'])) {
    $iconClass = 'fa-file-powerpoint text-warning';
    $badgeBg = 'bg-warning text-dark';
} elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
    $iconClass = 'fa-file-image text-info';
    $badgeBg = 'bg-info text-dark';
}
$streamUrl = base_url('admin/documents/stream/' . $doc['id']);
?>

<!-- Top Header Navigation -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="<?= base_url('admin/documents') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>Kembali
            </a>
            <span class="badge <?= $badgeBg ?> text-uppercase px-2 py-1"><?= $ext ?></span>
            <?php if ($doc['sifat_dokumen'] === 'rahasia'): ?>
                <span class="badge bg-danger"><i class="fa-solid fa-lock me-1"></i>Dokumen Rahasia</span>
            <?php elseif ($doc['sifat_dokumen'] === 'internal'): ?>
                <span class="badge bg-warning text-dark"><i class="fa-solid fa-shield-halved me-1"></i>Internal Pengurus</span>
            <?php else: ?>
                <span class="badge bg-success"><i class="fa-solid fa-globe me-1"></i>Dokumen Publik</span>
            <?php endif; ?>
        </div>
        <h5 class="fw-bold text-dark mb-0"><?= esc($doc['judul']) ?></h5>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-dark" onclick="toggleFullscreen()">
            <i class="fa-solid fa-expand me-1"></i>Layar Penuh
        </button>
        <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-download me-1"></i>Download File
        </a>
        <a href="<?= base_url('admin/documents/edit/' . $doc['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit Metadata">
            <i class="fa-solid fa-pen"></i>
        </a>
    </div>
</div>

<div class="row g-3">
    <!-- Main Document Viewer Container (Left / Center) -->
    <div class="col-lg-8 col-xl-9">
        <div class="card border-0 shadow-sm" id="viewerCard" style="border-radius: 12px; height: 80vh; min-height: 550px; background: #262626; overflow: hidden;">
            <div class="card-header py-2 px-3 d-flex justify-content-between align-items-center text-white border-0" style="background: #1e1e1e;">
                <div class="small text-truncate" style="max-width: 60%;">
                    <i class="fa-solid <?= $iconClass ?> me-2"></i><?= esc($doc['file_name']) ?>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-secondary small"><?= round($doc['file_size'] / 1024, 1) ?> KB</span>
                    <button type="button" class="btn btn-sm btn-dark text-white p-1" onclick="reloadViewer()" title="Reload Viewer">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-0 d-flex align-items-center justify-content-center position-relative" id="viewerBody" style="height: calc(100% - 41px); background: #333333;">
                <?php if ($ext === 'pdf'): ?>
                    <!-- PDF Interactive Viewer -->
                    <iframe id="docFrame" src="<?= $streamUrl ?>#toolbar=1&navpanes=1" width="100%" height="100%" style="border: none;"></iframe>

                <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])): ?>
                    <!-- High-Res Image Viewer -->
                    <div class="p-3 text-center w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                        <div class="mb-2 btn-group btn-group-sm">
                            <button class="btn btn-dark" onclick="zoomImage(1.2)"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
                            <button class="btn btn-dark" onclick="zoomImage(0.8)"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
                            <button class="btn btn-dark" onclick="resetImage()"><i class="fa-solid fa-arrows-rotate"></i> Reset</button>
                        </div>
                        <div style="overflow: auto; max-height: 90%; max-width: 100%;">
                            <img id="previewImage" src="<?= $streamUrl ?>" class="img-fluid rounded shadow" style="max-height: 65vh; transition: transform 0.2s;">
                        </div>
                    </div>

                <?php elseif (in_array($ext, ['txt', 'csv', 'md', 'json', 'sql']) && $textContent !== null): ?>
                    <!-- Text / Code / Markdown Viewer -->
                    <div class="w-100 h-100 p-4" style="overflow: auto; background: #1e1e1e; color: #d4d4d4; font-family: monospace; font-size: 0.9rem; line-height: 1.6;">
                        <pre class="mb-0 text-wrap"><code><?= htmlspecialchars($textContent) ?></code></pre>
                    </div>

                <?php elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
                    <!-- Office Document Viewer Container -->
                    <div class="d-flex flex-column align-items-center justify-content-center text-center p-4 text-white">
                        <div class="mb-3">
                            <i class="fa-solid <?= $iconClass ?> fa-5x text-warning"></i>
                        </div>
                        <h4 class="fw-bold text-white mb-2"><?= esc($doc['judul']) ?></h4>
                        <p class="text-white-50 small mb-4" style="max-width: 500px;">
                            Berkas ini bertipe <strong><?= strtoupper($ext) ?></strong> (<?= esc($doc['file_name']) ?>). Anda dapat membuka berkas secara langsung di komputer Anda atau menggunakan Google Docs Viewer.
                        </p>
                        <div class="d-flex gap-2 flex-wrap justify-content-center">
                            <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-success px-4 py-2 fw-semibold">
                                <i class="fa-solid fa-download me-2"></i>Download File (<?= round($doc['file_size'] / 1024, 1) ?> KB)
                            </a>
                            <a href="https://docs.google.com/viewer?url=<?= urlencode(base_url($doc['file_path'])) ?>&embedded=true" 
                               target="_blank" class="btn btn-primary px-4 py-2 fw-semibold">
                                <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Buka via Google Docs Viewer
                            </a>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- General File Fallback -->
                    <div class="text-center text-white p-4">
                        <i class="fa-solid fa-file fa-4x mb-3 text-white-50"></i>
                        <h5><?= esc($doc['file_name']) ?></h5>
                        <p class="text-white-50 small">Format file ini belum mendukung pratinjau langsung di dalam browser.</p>
                        <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-success fw-semibold">
                            <i class="fa-solid fa-download me-1"></i>Unduh Berkas
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Metadata & Details Sidebar (Right) -->
    <div class="col-lg-4 col-xl-3">
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px;">
            <div class="card-header py-3 border-0" style="background: #3e2723; color: #fff; border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold"><i class="fa-solid fa-circle-info me-2 text-gold"></i>Informasi & Metadata Dokumen</h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Nomor Dokumen / Surat</small>
                    <span class="fw-semibold text-dark"><?= esc($doc['nomor_dokumen'] ?: '-') ?></span>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Kategori Arsip</small>
                    <span class="badge bg-light text-dark border px-2 py-1 mt-1">
                        <i class="fa-solid <?= esc($doc['kategori_icon'] ?: 'fa-folder') ?> me-1" style="color: <?= esc($doc['kategori_warna'] ?: '#3b82f6') ?>;"></i>
                        <?= esc($doc['nama_kategori'] ?: 'Umum') ?>
                    </span>
                </div>

                <div class="row mb-3 g-2">
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Tanggal Dokumen</small>
                        <span class="text-dark small fw-medium"><?= $doc['tanggal_dokumen'] ? date('d M Y', strtotime($doc['tanggal_dokumen'])) : '-' ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Tahun Arsip</small>
                        <span class="text-dark small fw-medium"><?= esc($doc['tahun'] ?: '-') ?></span>
                    </div>
                </div>

                <div class="row mb-3 g-2">
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Unit / Bidang</small>
                        <span class="badge px-2 py-1 fw-bold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                            <?= esc($doc['bidang_kode'] ?: $doc['institusi']) ?>
                        </span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Ukuran File</small>
                        <span class="text-dark small fw-medium"><?= round($doc['file_size'] / 1024, 1) ?> KB</span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted text-uppercase fw-bold d-block" style="font-size: 0.7rem;">Deskripsi / Ringkasan</small>
                    <p class="small text-dark mb-0 bg-light p-2 rounded"><?= nl2br(esc($doc['deskripsi'] ?: 'Tidak ada keterangan tambahan.')) ?></p>
                </div>

                <div class="border-top pt-2 mt-3">
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Pengunggah:</span>
                        <strong class="text-dark"><?= esc($doc['uploader_nama'] ?: 'Admin') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between text-muted small mb-1">
                        <span>Diupload pada:</span>
                        <span><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between text-muted small">
                        <span>Statistik:</span>
                        <span><strong><?= $doc['views_count'] ?></strong> views • <strong><?= $doc['downloads_count'] ?></strong> downloads</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Documents in same Category -->
        <?php if (!empty($relatedDocs)): ?>
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header py-2 px-3 bg-light border-0">
                <small class="fw-bold text-uppercase text-muted">Dokumen Kategori Serupa</small>
            </div>
            <div class="list-group list-group-flush" style="font-size: 0.85rem;">
                <?php foreach ($relatedDocs as $rel): ?>
                <a href="<?= base_url('admin/documents/view/' . $rel['id']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2">
                    <div class="text-truncate me-2" title="<?= esc($rel['judul']) ?>">
                        <i class="fa-solid fa-file me-1 text-muted"></i><?= esc($rel['judul']) ?>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary small"><?= strtoupper($rel['file_type']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
let currentScale = 1.0;

function zoomImage(factor) {
    const img = document.getElementById('previewImage');
    if (img) {
        currentScale *= factor;
        img.style.transform = `scale(${currentScale})`;
    }
}

function resetImage() {
    const img = document.getElementById('previewImage');
    if (img) {
        currentScale = 1.0;
        img.style.transform = 'scale(1)';
    }
}

function reloadViewer() {
    const frame = document.getElementById('docFrame');
    if (frame) {
        frame.src = frame.src;
    }
}

function toggleFullscreen() {
    const card = document.getElementById('viewerCard');
    if (!document.fullscreenElement) {
        if (card.requestFullscreen) {
            card.requestFullscreen();
        } else if (card.webkitRequestFullscreen) {
            card.webkitRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}
</script>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
