<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: #3e2723;">
            <i class="fa-solid fa-photo-film me-2 text-gold"></i>Galeri & Dokumentasi Media (Foto & Video)
        </h4>
        <p class="text-muted mb-0 small">Pusat dokumentasi visual, galeri foto kegiatan, dan arsip video resmi DPC & DPRa PKS Pancoran Mas.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('galeri') ?>" target="_blank" class="btn btn-sm btn-outline-secondary px-3 fw-semibold">
            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Lihat Galeri Publik
        </a>
        <a href="<?= base_url('admin/gallery/albums') ?>" class="btn btn-sm btn-outline-dark px-3 fw-semibold">
            <i class="fa-solid fa-images me-1"></i>Album Kegiatan
        </a>
        <?php if ($can_create ?? false): ?>
        <div class="btn-group">
            <a href="<?= base_url('admin/gallery/create-image') ?>" class="btn btn-sm btn-gold px-3 fw-semibold">
                <i class="fa-solid fa-cloud-arrow-up me-1"></i>Upload Foto
            </a>
            <a href="<?= base_url('admin/gallery/create-video') ?>" class="btn btn-sm btn-dark px-3 fw-semibold">
                <i class="fa-brands fa-youtube me-1 text-danger"></i>Tambah Video
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    text: <?= json_encode(session()->getFlashdata('success')) ?>,
    confirmButtonColor: '#d4af37',
    timer: 3000,
    timerProgressBar: true,
});
</script>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Terjadi Kesalahan',
    html: <?= json_encode('<ul class="text-start mb-0">' . implode('', array_map(fn($e) => '<li>' . esc($e) . '</li>', session()->getFlashdata('errors'))) . '</ul>') ?>,
    confirmButtonColor: '#dc3545',
});
</script>
<?php endif; ?>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #3d1f0f, #63402d); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Arsip Media</div>
                <div class="h3 fw-bold my-1"><?= number_format($stats['total_media']) ?> <span class="fs-6 fw-normal text-white-50">Media</span></div>
                <div class="small text-white-50"><i class="fa-solid fa-hard-drive me-1"></i> Storage: <?= round($stats['total_storage_size'] / (1024 * 1024), 1) ?> MB</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #0284c7, #0369a1); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Koleksi Foto</div>
                <div class="h3 fw-bold my-1"><?= number_format($stats['total_images']) ?> <span class="fs-6 fw-normal text-white-50">Foto</span></div>
                <div class="small text-white-50"><i class="fa-solid fa-camera me-1"></i> Dokumentasi Visual</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #dc2626, #991b1b); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Koleksi Video</div>
                <div class="h3 fw-bold my-1"><?= number_format($stats['total_videos']) ?> <span class="fs-6 fw-normal text-white-50">Video</span></div>
                <div class="small text-white-50"><i class="fa-brands fa-youtube me-1"></i> YouTube & Upload MP4</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #d97706, #b45309); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Album & Interaksi</div>
                <div class="h3 fw-bold my-1"><?= count($albums) ?> <span class="fs-6 fw-normal text-white-50">Album</span></div>
                <div class="small text-white-50"><i class="fa-regular fa-eye me-1"></i> <?= number_format($stats['total_views']) ?>x Dilihat</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <input type="hidden" name="mode" value="<?= esc($viewMode) ?>">

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Tipe Media</label>
                <select name="tipe" class="form-select form-select-sm">
                    <option value="">-- Semua Tipe --</option>
                    <option value="image" <?= ($filters['tipe'] ?? '') === 'image' ? 'selected' : '' ?>>📷 Foto / Gambar</option>
                    <option value="video" <?= ($filters['tipe'] ?? '') === 'video' ? 'selected' : '' ?>>🎬 Video</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Album Kegiatan</label>
                <select name="album_id" class="form-select form-select-sm">
                    <option value="">-- Semua Album --</option>
                    <?php foreach ($albums as $alb): ?>
                    <option value="<?= $alb['id'] ?>" <?= ($filters['album_id'] ?? '') == $alb['id'] ? 'selected' : '' ?>>
                        <?= esc($alb['nama_album']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Unggulan</label>
                <select name="is_featured" class="form-select form-select-sm">
                    <option value="">-- Status --</option>
                    <option value="1" <?= ($filters['is_featured'] ?? '') === '1' ? 'selected' : '' ?>>⭐ Unggulan Saja</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Cari Judul / Lokasi / Kredit</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Ketik kata kunci..." value="<?= esc($filters['search'] ?? '') ?>">
            </div>

            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">Filter</button>
                <a href="<?= base_url('admin/gallery?mode=' . $viewMode) ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Hidden Bulk Delete Form -->
<?php if ($can_delete ?? false): ?>
<form id="bulkDeleteForm" method="POST" action="<?= base_url('admin/gallery/bulk-delete') ?>" class="d-none">
    <?= csrf_field() ?>
    <div id="bulkDeleteInputs"></div>
</form>
<?php endif; ?>

<!-- Header Controls & Switcher -->
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-3">
        <?php if ($can_delete ?? false): ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAllMedia" style="width:1.2em;height:1.2em;cursor:pointer;">
            <label class="form-check-label small fw-semibold text-muted" for="selectAllMedia" style="cursor:pointer;">Pilih Semua</label>
        </div>
        <?php endif; ?>
        <div class="text-muted small">
            Menampilkan <strong><?= count($items) ?></strong> media dokumentasi
        </div>
    </div>
    <div class="btn-group btn-group-sm" role="group">
        <a href="<?= base_url('admin/gallery?' . http_build_query(array_merge($filters, ['mode' => 'grid']))) ?>" 
           class="btn <?= $viewMode === 'grid' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <i class="fa-solid fa-grip me-1"></i>Grid Card
        </a>
        <a href="<?= base_url('admin/gallery?' . http_build_query(array_merge($filters, ['mode' => 'table']))) ?>" 
           class="btn <?= $viewMode === 'table' ? 'btn-dark' : 'btn-outline-secondary' ?>">
            <i class="fa-solid fa-list me-1"></i>Tabel List
        </a>
    </div>
</div>

<!-- Floating Bulk Action Bar -->
<?php if ($can_delete ?? false): ?>
<div id="bulkActionBar" class="alert alert-dark border-0 shadow-lg mb-3 d-none" 
     style="position:sticky;top:10px;z-index:1050;border-radius:12px;background:linear-gradient(135deg,#3d1f0f,#63402d);">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="text-white d-flex align-items-center gap-2">
            <i class="fa-solid fa-check-double fs-5 text-warning"></i>
            <span><strong id="selectedCount">0</strong> media terpilih</span>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-light px-3" onclick="deselectAll()">
                <i class="fa-solid fa-xmark me-1"></i>Batal Pilih
            </button>
            <button type="button" class="btn btn-sm btn-danger px-3 fw-semibold" id="btnBulkDelete">
                <i class="fa-solid fa-trash-can me-1"></i>Hapus Terpilih
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($viewMode === 'grid'): ?>
<!-- GRID CARD / MASONRY VIEW -->
<div class="row g-3 mb-4" id="galleryGrid">
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
            <?php
            $isImage = $item['tipe'] === 'image';
            $isVideo = $item['tipe'] === 'video';
            $thumbUrl = '';
            if ($isImage) {
                $thumbUrl = base_url($item['file_path'] ?: $item['thumbnail_path']);
            } elseif ($isVideo) {
                if ($item['video_type'] === 'youtube') {
                    $thumbUrl = $item['thumbnail_path'] ?: "https://img.youtube.com/vi/{$item['video_id']}/hqdefault.jpg";
                } else {
                    $thumbUrl = !empty($item['thumbnail_path']) ? base_url($item['thumbnail_path']) : base_url('assets/images/video_placeholder.png');
                }
            }
            $isOwner = !empty($is_full_admin) || ((int)($item['created_by'] ?? 0) === (int)($current_user_id ?? 0));
            ?>
            <div class="col-md-4 col-lg-3">
                <div class="card h-100 border-0 shadow-sm hover-elevate gallery-card" style="border-radius: 12px; overflow: hidden;">
                    <!-- Thumbnail Container with Media Type Badge -->
                    <div class="position-relative media-thumb-wrap" style="height: 190px; background: #1e1e1e; cursor: pointer;"
                         <?php if ($isImage): ?>
                         onclick="openLightbox('<?= $thumbUrl ?>', '<?= addslashes(esc($item['judul'])) ?>', '<?= addslashes(esc($item['deskripsi'] ?: '')) ?>', '<?= addslashes(esc($item['kredit'] ?: '')) ?>', '<?= $item['tanggal_media'] ? date('d/m/Y', strtotime($item['tanggal_media'])) : '' ?>', '<?= addslashes(esc($item['lokasi'] ?: '')) ?>')"
                         <?php else: ?>
                         onclick="openVideoPlayer('<?= esc($item['video_type']) ?>', '<?= esc($item['video_id']) ?>', '<?= !empty($item['file_path']) ? base_url($item['file_path']) : '' ?>', '<?= addslashes(esc($item['judul'])) ?>')"
                         <?php endif; ?>>

                        <img src="<?= $thumbUrl ?>" alt="<?= esc($item['judul']) ?>" class="w-100 h-100 object-fit-cover">

                        <!-- Overlay Badges -->
                        <div class="position-absolute top-0 start-0 m-2">
                            <?php if ($isImage): ?>
                                <span class="badge bg-dark bg-opacity-75"><i class="fa-solid fa-camera me-1"></i>Foto</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fa-solid fa-play me-1"></i>Video</span>
                            <?php endif; ?>
                            <?php if ($item['is_featured']): ?>
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-star"></i></span>
                            <?php endif; ?>
                        </div>

                        <?php if (($can_delete ?? false) && $isOwner): ?>
                        <div class="position-absolute top-0 end-0 m-2" style="z-index:5;" onclick="event.stopPropagation();">
                            <input type="checkbox" class="form-check-input media-checkbox" value="<?= $item['id'] ?>"
                                   data-title="<?= esc($item['judul']) ?>"
                                   style="width:1.3em;height:1.3em;cursor:pointer;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.5);">
                        </div>
                        <?php endif; ?>

                        <?php if ($isVideo): ?>
                        <div class="position-absolute top-50 start-50 translate-middle text-white play-btn-overlay">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger shadow" style="width: 48px; height: 48px;">
                                <i class="fa-solid fa-play fs-5 ms-1"></i>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="position-absolute bottom-0 end-0 m-2">
                            <span class="badge bg-dark bg-opacity-75 small">
                                <i class="fa-regular fa-eye me-1"></i><?= $item['views_count'] ?>
                            </span>
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-3 d-flex flex-column">
                        <h6 class="fw-bold text-dark mb-1 text-truncate-2" title="<?= esc($item['judul']) ?>" style="font-size: 0.9rem; min-height: 2.7em;">
                            <?= esc($item['judul']) ?>
                        </h6>

                        <div class="small text-muted mb-2">
                            <?php if (!empty($item['nama_album'])): ?>
                                <span class="badge bg-light text-dark border text-truncate mb-1" style="max-width: 100%;">
                                    <i class="fa-solid fa-images me-1 text-gold"></i><?= esc($item['nama_album']) ?>
                                </span>
                            <?php endif; ?>
                            <div class="d-flex justify-content-between text-muted" style="font-size: 0.75rem;">
                                <span><i class="fa-regular fa-calendar me-1"></i><?= $item['tanggal_media'] ? date('d M Y', strtotime($item['tanggal_media'])) : '-' ?></span>
                                <?php if ($item['kredit']): ?>
                                    <span class="text-truncate" style="max-width: 50%;"><i class="fa-solid fa-user me-1"></i><?= esc($item['kredit']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Card Footer Controls -->
                        <div class="mt-auto pt-2 border-top d-flex gap-1">
                            <?php if ($isImage): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary flex-grow-1"
                                    onclick="openLightbox('<?= $thumbUrl ?>', '<?= addslashes(esc($item['judul'])) ?>', '<?= addslashes(esc($item['deskripsi'] ?: '')) ?>', '<?= addslashes(esc($item['kredit'] ?: '')) ?>', '<?= $item['tanggal_media'] ? date('d/m/Y', strtotime($item['tanggal_media'])) : '' ?>', '<?= addslashes(esc($item['lokasi'] ?: '')) ?>')">
                                <i class="fa-solid fa-expand me-1"></i>Lihat
                            </button>
                            <?php else: ?>
                            <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1"
                                    onclick="openVideoPlayer('<?= esc($item['video_type']) ?>', '<?= esc($item['video_id']) ?>', '<?= !empty($item['file_path']) ? base_url($item['file_path']) : '' ?>', '<?= addslashes(esc($item['judul'])) ?>')">
                                <i class="fa-solid fa-play me-1"></i>Putar
                            </button>
                            <?php endif; ?>

                            <?php if (($can_update ?? false) && $isOwner): ?>
                            <a href="<?= base_url('admin/gallery/toggle-featured/' . $item['id']) ?>" 
                               class="btn btn-sm <?= $item['is_featured'] ? 'btn-warning text-dark' : 'btn-outline-secondary' ?>" title="Toggle Unggulan">
                                <i class="fa-solid fa-star"></i>
                            </a>
                            <a href="<?= base_url('admin/gallery/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit Metadata">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <?php endif; ?>

                            <?php if (($can_delete ?? false) && $isOwner): ?>
                            <a href="<?= base_url('admin/gallery/delete/' . $item['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete-media" title="Hapus"
                               data-title="<?= esc($item['judul']) ?>">
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
            <i class="fa-solid fa-photo-film fa-3x mb-3 text-muted opacity-50"></i>
            <h6>Belum ada foto atau video yang sesuai dengan filter pencarian.</h6>
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
                        <?php if ($can_delete ?? false): ?>
                        <th class="ps-3" style="width:40px;">
                            <input type="checkbox" class="form-check-input" id="selectAllTable" style="width:1.1em;height:1.1em;cursor:pointer;">
                        </th>
                        <?php endif; ?>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th style="width: 80px;">Pratinjau</th>
                        <th>Judul Media & Keterangan</th>
                        <th>Tipe</th>
                        <th>Album Kegiatan</th>
                        <th>Tanggal & Lokasi</th>
                        <th class="text-center">Views</th>
                        <th class="text-center pe-4" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $idx => $item): ?>
                            <?php
                            $isImage = $item['tipe'] === 'image';
                            $isVideo = $item['tipe'] === 'video';
                            $thumbUrl = '';
                            if ($isImage) {
                                $thumbUrl = base_url($item['file_path'] ?: $item['thumbnail_path']);
                            } elseif ($isVideo) {
                                if ($item['video_type'] === 'youtube') {
                                    $thumbUrl = $item['thumbnail_path'] ?: "https://img.youtube.com/vi/{$item['video_id']}/hqdefault.jpg";
                                } else {
                                    $thumbUrl = !empty($item['thumbnail_path']) ? base_url($item['thumbnail_path']) : base_url('assets/images/video_placeholder.png');
                                }
                            }
                            $isOwner = !empty($is_full_admin) || ((int)($item['created_by'] ?? 0) === (int)($current_user_id ?? 0));
                            ?>
                            <tr>
                                <?php if ($can_delete ?? false): ?>
                                <td class="ps-3">
                                    <?php if ($isOwner): ?>
                                    <input type="checkbox" class="form-check-input media-checkbox" value="<?= $item['id'] ?>"
                                           data-title="<?= esc($item['judul']) ?>"
                                           style="width:1.1em;height:1.1em;cursor:pointer;">
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td class="ps-4 text-muted fw-medium"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="rounded shadow-sm overflow-hidden" style="width: 70px; height: 50px; background: #000; cursor: pointer;"
                                         <?php if ($isImage): ?>
                                         onclick="openLightbox('<?= $thumbUrl ?>', '<?= addslashes(esc($item['judul'])) ?>', '<?= addslashes(esc($item['deskripsi'] ?: '')) ?>', '<?= addslashes(esc($item['kredit'] ?: '')) ?>', '<?= $item['tanggal_media'] ? date('d/m/Y', strtotime($item['tanggal_media'])) : '' ?>', '<?= addslashes(esc($item['lokasi'] ?: '')) ?>')"
                                         <?php else: ?>
                                         onclick="openVideoPlayer('<?= esc($item['video_type']) ?>', '<?= esc($item['video_id']) ?>', '<?= !empty($item['file_path']) ? base_url($item['file_path']) : '' ?>', '<?= addslashes(esc($item['judul'])) ?>')"
                                         <?php endif; ?>>
                                        <img src="<?= $thumbUrl ?>" class="w-100 h-100 object-fit-cover">
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($item['judul']) ?></div>
                                    <?php if ($item['deskripsi']): ?>
                                        <div class="text-muted small text-truncate" style="max-width: 300px;"><?= esc($item['deskripsi']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($isImage): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1"><i class="fa-solid fa-camera me-1"></i>Foto</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="fa-brands fa-youtube me-1"></i>Video</span>
                                    <?php endif; ?>
                                    <?php if ($item['is_featured']): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-star"></i></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <?= esc($item['nama_album'] ?: 'Umum') ?>
                                    </span>
                                </td>
                                <td class="small text-muted">
                                    <div><?= $item['tanggal_media'] ? date('d/m/Y', strtotime($item['tanggal_media'])) : '-' ?></div>
                                    <div class="text-truncate" style="max-width: 140px;"><?= esc($item['lokasi'] ?: '-') ?></div>
                                </td>
                                <td class="text-center small text-muted">
                                    <i class="fa-regular fa-eye me-1"></i><?= $item['views_count'] ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($isImage): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Lihat Foto"
                                                onclick="openLightbox('<?= $thumbUrl ?>', '<?= addslashes(esc($item['judul'])) ?>', '<?= addslashes(esc($item['deskripsi'] ?: '')) ?>', '<?= addslashes(esc($item['kredit'] ?: '')) ?>', '<?= $item['tanggal_media'] ? date('d/m/Y', strtotime($item['tanggal_media'])) : '' ?>', '<?= addslashes(esc($item['lokasi'] ?: '')) ?>')">
                                            <i class="fa-solid fa-expand"></i>
                                        </button>
                                        <?php else: ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Putar Video"
                                                onclick="openVideoPlayer('<?= esc($item['video_type']) ?>', '<?= esc($item['video_id']) ?>', '<?= !empty($item['file_path']) ? base_url($item['file_path']) : '' ?>', '<?= addslashes(esc($item['judul'])) ?>')">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                        <?php endif; ?>

                                        <?php if (($can_update ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/gallery/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>

                                        <?php if (($can_delete ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/gallery/delete/' . $item['id']) ?>" class="btn btn-sm btn-outline-danger btn-delete-media" title="Hapus"
                                           data-title="<?= esc($item['judul']) ?>">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">Belum ada dokumentasi media yang terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ======================================================== -->
<!-- MODAL 1: INTERACTIVE IMAGE LIGHTBOX -->
<!-- ======================================================== -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: #121212; color: #fff;">
            <div class="modal-header border-0 py-2 px-3" style="background: #1e1e1e;">
                <h6 class="modal-title fw-bold text-white text-truncate" id="lightboxTitle">Pratinjau Foto</h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="lightboxDownload" target="_blank" class="btn btn-sm btn-outline-light py-1 px-2" title="Unduh Foto">
                        <i class="fa-solid fa-download"></i>
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height: 500px; max-height: 75vh; overflow: hidden; background: #000;">
                <img id="lightboxImg" src="" class="img-fluid rounded" style="max-height: 75vh; max-width: 100%; object-fit: contain;">
            </div>
            <div class="modal-footer border-0 py-2 px-3 d-flex justify-content-between" style="background: #1e1e1e; font-size: 0.85rem;">
                <div class="text-white-50" id="lightboxMeta"></div>
                <div class="text-gold fw-semibold" id="lightboxKredit"></div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- MODAL 2: INTERACTIVE VIDEO PLAYER POPUP -->
<!-- ======================================================== -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="background: #000; color: #fff;">
            <div class="modal-header border-0 py-2 px-3" style="background: #1e1e1e;">
                <h6 class="modal-title fw-bold text-white text-truncate" id="videoModalTitle">Pemutar Video</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopVideo()"></button>
            </div>
            <div class="modal-body p-0 ratio ratio-16x9 bg-black" id="videoContainer">
                <!-- Video iframe / tag injected here via JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function openLightbox(url, title, desc, kredit, tgl, lokasi) {
    document.getElementById('lightboxImg').src = url;
    document.getElementById('lightboxTitle').innerText = title;
    document.getElementById('lightboxDownload').href = url;
    
    let metaText = '';
    if (tgl) metaText += `<i class="fa-regular fa-calendar me-1"></i>${tgl} `;
    if (lokasi) metaText += `• <i class="fa-solid fa-location-dot me-1"></i>${lokasi} `;
    if (desc) metaText += `<div class="mt-1 text-white">${desc}</div>`;
    document.getElementById('lightboxMeta').innerHTML = metaText;

    document.getElementById('lightboxKredit').innerHTML = kredit ? `<i class="fa-solid fa-camera me-1"></i>Kredit: ${kredit}` : '';

    const modal = new bootstrap.Modal(document.getElementById('lightboxModal'));
    modal.show();
}

function openVideoPlayer(type, videoId, videoFile, title) {
    document.getElementById('videoModalTitle').innerText = title;
    const container = document.getElementById('videoContainer');
    
    if (type === 'youtube' && videoId) {
        container.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
    } else if (videoFile) {
        container.innerHTML = `<video controls autoplay class="w-100 h-100"><source src="${videoFile}" type="video/mp4">Browser Anda tidak mendukung tag video.</video>`;
    } else {
        container.innerHTML = `<div class="d-flex align-items-center justify-content-center h-100 text-white">Video tidak dapat dimuat.</div>`;
    }

    const modal = new bootstrap.Modal(document.getElementById('videoModal'));
    modal.show();
}

function stopVideo() {
    document.getElementById('videoContainer').innerHTML = '';
}

document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    stopVideo();
});

// SweetAlert2 Delete Confirmation Handler
document.addEventListener('click', function (e) {
    const btn = e.target.closest('.btn-delete-media');
    if (!btn) return;
    
    e.preventDefault();
    const deleteUrl = btn.getAttribute('href');
    const mediaTitle = btn.getAttribute('data-title') || 'media ini';
    
    Swal.fire({
        title: 'Hapus Media?',
        html: `<p class="mb-1">Apakah Anda yakin ingin menghapus:</p><p class="fw-bold text-danger">"${mediaTitle}"</p><p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Ya, Hapus!',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        focusCancel: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = deleteUrl;
        }
    });
});

// =============================================
// BULK SELECTION & DELETE LOGIC
// =============================================
function updateBulkUI() {
    const checked = document.querySelectorAll('.media-checkbox:checked');
    const bar = document.getElementById('bulkActionBar');
    const countEl = document.getElementById('selectedCount');
    const selectAllGrid = document.getElementById('selectAllMedia');
    const selectAllTable = document.getElementById('selectAllTable');
    const allCheckboxes = document.querySelectorAll('.media-checkbox');
    
    if (!bar) return;
    
    if (checked.length > 0) {
        bar.classList.remove('d-none');
        countEl.textContent = checked.length;
    } else {
        bar.classList.add('d-none');
    }
    
    // Sync "select all" checkbox state
    const allChecked = allCheckboxes.length > 0 && checked.length === allCheckboxes.length;
    if (selectAllGrid) selectAllGrid.checked = allChecked;
    if (selectAllTable) selectAllTable.checked = allChecked;
    
    // Highlight selected cards
    allCheckboxes.forEach(cb => {
        const card = cb.closest('.gallery-card') || cb.closest('tr');
        if (card) {
            if (cb.checked) {
                card.style.outline = '3px solid #d4af37';
                card.style.outlineOffset = '-3px';
                if (card.tagName === 'TR') card.style.background = '#fff8e1';
            } else {
                card.style.outline = '';
                card.style.outlineOffset = '';
                if (card.tagName === 'TR') card.style.background = '';
            }
        }
    });
}

function deselectAll() {
    document.querySelectorAll('.media-checkbox:checked').forEach(cb => cb.checked = false);
    const selectAllGrid = document.getElementById('selectAllMedia');
    const selectAllTable = document.getElementById('selectAllTable');
    if (selectAllGrid) selectAllGrid.checked = false;
    if (selectAllTable) selectAllTable.checked = false;
    updateBulkUI();
}

// Listen for checkbox changes
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('media-checkbox')) {
        updateBulkUI();
    }
});

// Select All (grid header)
const selectAllGrid = document.getElementById('selectAllMedia');
if (selectAllGrid) {
    selectAllGrid.addEventListener('change', function () {
        document.querySelectorAll('.media-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkUI();
    });
}

// Select All (table header)
const selectAllTable = document.getElementById('selectAllTable');
if (selectAllTable) {
    selectAllTable.addEventListener('change', function () {
        document.querySelectorAll('.media-checkbox').forEach(cb => cb.checked = this.checked);
        updateBulkUI();
    });
}

// Bulk Delete Button
const btnBulkDelete = document.getElementById('btnBulkDelete');
if (btnBulkDelete) {
    btnBulkDelete.addEventListener('click', function () {
        const checked = document.querySelectorAll('.media-checkbox:checked');
        if (checked.length === 0) return;
        
        // Build title list (max 5 shown)
        const titles = Array.from(checked).map(cb => cb.getAttribute('data-title') || 'Untitled');
        let titleList = titles.slice(0, 5).map(t => `<li class="text-start">${t}</li>`).join('');
        if (titles.length > 5) {
            titleList += `<li class="text-start text-muted">...dan ${titles.length - 5} media lainnya</li>`;
        }
        
        Swal.fire({
            title: `Hapus ${checked.length} Media?`,
            html: `<p class="mb-2">Media berikut akan dihapus permanen:</p>
                   <ul class="small mb-2" style="max-height:200px;overflow-y:auto;">${titleList}</ul>
                   <p class="text-danger small fw-bold mb-0"><i class="fa-solid fa-triangle-exclamation me-1"></i>Tindakan ini tidak dapat dibatalkan!</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `<i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus ${checked.length} Media!`,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                // Populate hidden form and submit
                const inputsDiv = document.getElementById('bulkDeleteInputs');
                inputsDiv.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    inputsDiv.appendChild(input);
                });
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    });
}
</script>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.media-thumb-wrap:hover .play-btn-overlay {
    transform: translate(-50%, -50%) scale(1.1);
    transition: transform 0.2s;
}
.gallery-card:hover {
    transform: translateY(-4px);
    transition: transform 0.2s ease-in-out;
}
.media-checkbox:checked {
    background-color: #d4af37 !important;
    border-color: #d4af37 !important;
}
</style>

<?= $this->endSection() ?>
