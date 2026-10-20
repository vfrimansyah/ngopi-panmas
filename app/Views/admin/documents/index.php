<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-folder-open me-2 text-gold"></i>Manajemen Dokumen & Arsip Digital
        </h4>
        <p class="text-muted mb-0 small">Pusat penyimpanan, pengarsipan berkas resmi DPC & DPRa, dan penampil dokumen interaktif (Document Viewer).</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('admin/documents/categories') ?>" class="btn btn-sm btn-outline-secondary px-3 fw-semibold">
            <i class="fa-solid fa-tags me-1"></i>Kategori Dokumen
        </a>
        <a href="<?= base_url('admin/documents/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Excel Export
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/documents/create') ?>" class="btn btn-sm btn-gold px-4 fw-semibold">
            <i class="fa-solid fa-upload me-1"></i>Upload Dokumen
        </a>
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

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #3d1f0f, #63402d); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Arsip Dokumen</div>
                <div class="h3 fw-bold my-1"><?= number_format($stats['total_docs']) ?> <span class="fs-6 fw-normal text-white-50">File</span></div>
                <div class="small text-white-50"><i class="fa-solid fa-hard-drive me-1"></i> Total Ukuran: <?= round($stats['total_size'] / (1024 * 1024), 2) ?> MB</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Sifat Dokumen</div>
                <div class="h3 fw-bold my-1"><?= $stats['total_internal'] ?> <span class="fs-6 fw-normal text-white-50">Internal</span> / <?= $stats['total_publik'] ?> <span class="fs-6 fw-normal text-white-50">Publik</span></div>
                <div class="small text-white-50"><i class="fa-solid fa-lock me-1"></i> Rahasia/BPH: <?= $stats['total_rahasia'] ?> File</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #059669, #10b981); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Interaksi</div>
                <div class="h3 fw-bold my-1"><?= number_format($stats['total_views']) ?> <span class="fs-6 fw-normal text-white-50">Views</span></div>
                <div class="small text-white-50"><i class="fa-solid fa-download me-1"></i> <?= number_format($stats['total_downloads']) ?>x Diunduh</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #d97706, #b45309); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Kategori & Klasifikasi</div>
                <div class="h3 fw-bold my-1"><?= count($categories) ?> <span class="fs-6 fw-normal text-white-50">Kategori</span></div>
                <div class="small text-white-50"><i class="fa-solid fa-folder-tree me-1"></i> Pengarsipan Terstruktur</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar & View Mode Toggle -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <input type="hidden" name="mode" value="<?= esc($viewMode) ?>">

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Kategori</label>
                <select name="kategori" class="form-select form-select-sm">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ($filters['kategori_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                        <?= esc($cat['nama_kategori']) ?> (<?= $cat['total_documents'] ?>)
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Sifat Dokumen</label>
                <select name="sifat" class="form-select form-select-sm">
                    <option value="">-- Semua Sifat --</option>
                    <option value="publik" <?= ($filters['sifat_dokumen'] ?? '') === 'publik' ? 'selected' : '' ?>>Publik</option>
                    <option value="internal" <?= ($filters['sifat_dokumen'] ?? '') === 'internal' ? 'selected' : '' ?>>Internal Pengurus</option>
                    <option value="rahasia" <?= ($filters['sifat_dokumen'] ?? '') === 'rahasia' ? 'selected' : '' ?>>Rahasia / BPH</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Bidang / Unit</label>
                <select name="bidang" class="form-select form-select-sm">
                    <option value="">-- Semua Unit --</option>
                    <?php foreach ($bidangs as $b): ?>
                    <option value="<?= esc($b['kode']) ?>" <?= ($filters['bidang_kode'] ?? '') === $b['kode'] ? 'selected' : '' ?>>
                        <?= esc($b['kode']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Cari Judul / No. Dokumen</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik kata kunci..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">Filter</button>
                <a href="<?= base_url('admin/documents?mode=' . $viewMode) ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- View Switcher & Header Controls -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="text-muted small">
        Menampilkan <strong><?= count($documents) ?></strong> berkas dokumen
    </div>
    <div class="btn-group btn-group-sm" role="group">
        <a href="<?= base_url('admin/documents?' . http_build_query(array_merge($filters, ['mode' => 'table']))) ?>" 
           class="btn <?= $viewMode === 'table' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <i class="fa-solid fa-list me-1"></i>Tabel List
        </a>
        <a href="<?= base_url('admin/documents?' . http_build_query(array_merge($filters, ['mode' => 'grid']))) ?>" 
           class="btn <?= $viewMode === 'grid' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <i class="fa-solid fa-grip me-1"></i>Grid Card
        </a>
    </div>
</div>

<?php if ($viewMode === 'grid'): ?>
<!-- GRID CARD VIEW -->
<div class="row g-3 mb-4">
    <?php if (!empty($documents)): ?>
        <?php foreach ($documents as $doc): ?>
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
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm hover-elevate" style="border-radius: 12px; transition: transform 0.2s;">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <i class="fa-solid <?= $iconClass ?> fs-1"></i>
                            <div>
                                <span class="badge <?= $badgeBg ?> text-uppercase me-1" style="font-size: 0.7rem;"><?= $ext ?></span>
                                <?php if ($doc['sifat_dokumen'] === 'rahasia'): ?>
                                    <span class="badge bg-danger" style="font-size: 0.65rem;"><i class="fa-solid fa-lock"></i></span>
                                <?php elseif ($doc['sifat_dokumen'] === 'internal'): ?>
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Internal</span>
                                <?php else: ?>
                                    <span class="badge bg-success" style="font-size: 0.65rem;">Publik</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-1 text-truncate-2" style="font-size: 0.95rem; min-height: 2.8em;" title="<?= esc($doc['judul']) ?>">
                            <?= esc($doc['judul']) ?>
                        </h6>

                        <div class="small text-muted mb-2">
                            <?php if ($doc['nomor_dokumen']): ?>
                                <div class="text-truncate" style="font-size: 0.78rem;"><strong>No:</strong> <?= esc($doc['nomor_dokumen']) ?></div>
                            <?php endif; ?>
                            <span class="badge bg-light text-dark border mt-1" style="font-size: 0.72rem;">
                                <i class="fa-solid <?= esc($doc['kategori_icon'] ?: 'fa-folder') ?> me-1" style="color: <?= esc($doc['kategori_warna'] ?: '#3b82f6') ?>;"></i>
                                <?= esc($doc['nama_kategori'] ?: 'Umum') ?>
                            </span>
                        </div>

                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                            <span><?= round($doc['file_size'] / 1024, 1) ?> KB</span>
                            <span><i class="fa-regular fa-eye me-1"></i><?= $doc['views_count'] ?></span>
                            <span><i class="fa-solid fa-download me-1"></i><?= $doc['downloads_count'] ?></span>
                        </div>

                        <div class="d-flex gap-1 mt-3">
                            <a href="<?= base_url('admin/documents/view/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary flex-grow-1 fw-semibold">
                                <i class="fa-solid fa-eye me-1"></i>Viewer
                            </a>
                            <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-sm btn-outline-success" title="Download">
                                <i class="fa-solid fa-download"></i>
                            </a>
                            <?php 
                            $isOwner = !empty($is_full_admin) || ((int)($doc['uploader_id'] ?? 0) === (int)($current_user_id ?? 0));
                            ?>
                            <?php if (($can_update ?? false) && $isOwner): ?>
                            <a href="<?= base_url('admin/documents/edit/' . $doc['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <?php endif; ?>
                            <?php if (($can_delete ?? false) && $isOwner): ?>
                            <a href="<?= base_url('admin/documents/delete/' . $doc['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus dokumen ini?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center text-muted py-5 bg-white rounded-3 shadow-sm">
            <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted opacity-50"></i>
            <h6>Tidak ada arsip dokumen yang sesuai dengan filter pencarian.</h6>
        </div>
    <?php endif; ?>
</div>

<?php else: ?>
<!-- TABLE LIST VIEW -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #3e2723 !important; color: #ffffff !important;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th style="width: 50px;">Tipe</th>
                        <th>Judul Dokumen & No. Surat</th>
                        <th>Kategori</th>
                        <th>Sifat</th>
                        <th>Unit / Bidang</th>
                        <th class="text-end">Ukuran</th>
                        <th class="text-center">Statistik</th>
                        <th class="text-center pe-4" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $idx => $doc): ?>
                            <?php
                            $ext = strtolower($doc['file_type']);
                            $iconClass = 'fa-file-lines text-secondary';
                            if ($ext === 'pdf') $iconClass = 'fa-file-pdf text-danger';
                            elseif (in_array($ext, ['doc', 'docx'])) $iconClass = 'fa-file-word text-primary';
                            elseif (in_array($ext, ['xls', 'xlsx'])) $iconClass = 'fa-file-excel text-success';
                            elseif (in_array($ext, ['ppt', 'pptx'])) $iconClass = 'fa-file-powerpoint text-warning';
                            elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) $iconClass = 'fa-file-image text-info';
                            ?>
                            <tr>
                                <td class="ps-4 text-muted fw-medium"><?= $idx + 1 ?></td>
                                <td class="text-center">
                                    <i class="fa-solid <?= $iconClass ?> fs-4"></i>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 0.95rem;">
                                        <a href="<?= base_url('admin/documents/view/' . $doc['id']) ?>" class="text-decoration-none text-dark hover-primary">
                                            <?= esc($doc['judul']) ?>
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1" style="font-size: 0.8rem;">
                                        <?php if ($doc['nomor_dokumen']): ?>
                                            <span class="text-muted"><i class="fa-solid fa-hashtag me-1"></i><?= esc($doc['nomor_dokumen']) ?></span>
                                        <?php endif; ?>
                                        <?php if ($doc['tanggal_dokumen']): ?>
                                            <span class="text-muted"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y', strtotime($doc['tanggal_dokumen'])) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-size: 0.82rem;">
                                        <i class="fa-solid <?= esc($doc['kategori_icon'] ?: 'fa-folder') ?> me-1" style="color: <?= esc($doc['kategori_warna'] ?: '#3b82f6') ?>;"></i>
                                        <?= esc($doc['nama_kategori'] ?: 'Umum') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($doc['sifat_dokumen'] === 'rahasia'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-solid fa-lock me-1"></i>Rahasia</span>
                                    <?php elseif ($doc['sifat_dokumen'] === 'internal'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="fa-solid fa-shield-halved me-1"></i>Internal</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="fa-solid fa-globe me-1"></i>Publik</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 fw-bold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                        <?= esc($doc['bidang_kode'] ?: $doc['institusi']) ?>
                                    </span>
                                </td>
                                <td class="text-end fw-medium text-muted" style="font-size: 0.85rem;">
                                    <?= round($doc['file_size'] / 1024, 1) ?> KB
                                </td>
                                <td class="text-center small text-muted">
                                    <span class="me-2" title="Views"><i class="fa-regular fa-eye me-1 text-primary"></i><?= $doc['views_count'] ?></span>
                                    <span title="Downloads"><i class="fa-solid fa-download me-1 text-success"></i><?= $doc['downloads_count'] ?></span>
                                </td>
                                <td class="text-center pe-4">
                                    <?php 
                                    $isOwner = !empty($is_full_admin) || ((int)($doc['uploader_id'] ?? 0) === (int)($current_user_id ?? 0));
                                    ?>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <!-- Quick Modal Viewer Button -->
                                        <button type="button" class="btn btn-sm btn-outline-info" title="Quick Preview"
                                                data-bs-toggle="modal" data-bs-target="#quickViewModal<?= $doc['id'] ?>">
                                            <i class="fa-solid fa-magnifying-glass"></i>
                                        </button>
                                        <!-- Dedicated Fullscreen Viewer -->
                                        <a href="<?= base_url('admin/documents/view/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary" title="Document Viewer">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                        <!-- Download -->
                                        <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-sm btn-outline-success" title="Download">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                        <?php if (($can_update ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/documents/edit/' . $doc['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (($can_delete ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/documents/delete/' . $doc['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus dokumen ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Quick Preview Modal -->
                                    <div class="modal fade text-start" id="quickViewModal<?= $doc['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 90vw;">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header text-white" style="background: #3e2723;">
                                                    <h6 class="modal-title fw-bold text-white">
                                                        <i class="fa-solid <?= $iconClass ?> me-2"></i><?= esc($doc['judul']) ?>
                                                    </h6>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <a href="<?= base_url('admin/documents/view/' . $doc['id']) ?>" class="btn btn-sm btn-warning fw-semibold">
                                                            <i class="fa-solid fa-expand me-1"></i>Buka Layar Penuh
                                                        </a>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                </div>
                                                <div class="modal-body p-0" style="height: 75vh; background: #525659;">
                                                    <?php if ($ext === 'pdf'): ?>
                                                        <iframe src="<?= base_url('admin/documents/stream/' . $doc['id']) ?>" width="100%" height="100%" frameborder="0"></iframe>
                                                    <?php elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])): ?>
                                                        <div class="d-flex align-items-center justify-content-center h-100 p-3 bg-dark">
                                                            <img src="<?= base_url('admin/documents/stream/' . $doc['id']) ?>" class="img-fluid rounded" style="max-height: 100%;">
                                                        </div>
                                                    <?php elseif (in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'])): ?>
                                                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white p-4">
                                                            <i class="fa-solid <?= $iconClass ?> fa-4x mb-3 text-warning"></i>
                                                            <h5><?= esc($doc['file_name']) ?></h5>
                                                            <p class="text-white-50 text-center" style="max-width: 500px;">
                                                                File Office Document dapat dilihat menggunakan Document Viewer atau langsung diunduh ke komputer.
                                                            </p>
                                                            <div class="d-flex gap-2 mt-2">
                                                                <a href="<?= base_url('admin/documents/view/' . $doc['id']) ?>" class="btn btn-warning fw-semibold">
                                                                    <i class="fa-solid fa-eye me-1"></i>Buka di Document Viewer
                                                                </a>
                                                                <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" class="btn btn-success fw-semibold">
                                                                    <i class="fa-solid fa-download me-1"></i>Download File
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <iframe src="<?= base_url('admin/documents/stream/' . $doc['id']) ?>" width="100%" height="100%" frameborder="0"></iframe>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Modal -->
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fa-solid fa-folder-open fa-3x mb-2 text-muted opacity-50 d-block"></i>
                                Belum ada arsip dokumen yang terdaftar.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
.hover-primary:hover { color: #2563eb !important; }
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?= $this->endSection() ?>
