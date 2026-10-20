<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container py-4 my-2">
    <!-- Header Banner -->
    <div class="p-4 p-md-5 mb-4 rounded-4 text-white position-relative overflow-hidden shadow-sm" style="background: linear-gradient(135deg, #2c1a0e, #5a351c);">
        <div class="position-relative z-1" style="max-width: 700px;">
            <span class="badge px-3 py-2 text-uppercase mb-2" style="background: #d4af37; color: #3e2723; font-weight: 700; letter-spacing: 1px;">
                <i class="fa-solid fa-photo-film me-1"></i>Dokumentasi Resmi
            </span>
            <h1 class="display-5 fw-bold mb-2">Galeri & Media PKS Panmas</h1>
            <p class="lead mb-0 text-white-50">
                Arsip foto kegiatan, aksi sosial kemasyarakatan, serta video kompilasi perjuangan kader PKS se-Kecamatan Pancoran Mas.
            </p>
        </div>
        <div class="position-absolute end-0 bottom-0 opacity-10 p-4 d-none d-md-block">
            <i class="fa-solid fa-camera-retro" style="font-size: 14rem;"></i>
        </div>
    </div>

    <!-- Filter & Tabs Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #faf8f5;">
        <div class="card-body p-3">
            <div class="row g-3 align-items-center">
                <!-- Tipe Media Pills -->
                <div class="col-md-6">
                    <div class="nav nav-pills gap-2" role="tablist">
                        <a href="<?= base_url('galeri?' . http_build_query(array_filter(['album' => $filters['album_id'] ?? null, 'q' => $filters['search'] ?? null]))) ?>" 
                           class="nav-link rounded-pill px-3 py-2 <?= empty($filters['tipe']) ? 'active bg-dark' : 'bg-white text-dark border' ?>">
                            <i class="fa-solid fa-border-all me-1"></i>Semua (<?= $stats['total_media'] ?>)
                        </a>
                        <a href="<?= base_url('galeri?' . http_build_query(array_filter(['tipe' => 'image', 'album' => $filters['album_id'] ?? null, 'q' => $filters['search'] ?? null]))) ?>" 
                           class="nav-link rounded-pill px-3 py-2 <?= ($filters['tipe'] ?? '') === 'image' ? 'active bg-primary' : 'bg-white text-dark border' ?>">
                            <i class="fa-solid fa-camera me-1"></i>Foto (<?= $stats['total_images'] ?>)
                        </a>
                        <a href="<?= base_url('galeri?' . http_build_query(array_filter(['tipe' => 'video', 'album' => $filters['album_id'] ?? null, 'q' => $filters['search'] ?? null]))) ?>" 
                           class="nav-link rounded-pill px-3 py-2 <?= ($filters['tipe'] ?? '') === 'video' ? 'active bg-danger' : 'bg-white text-dark border' ?>">
                            <i class="fa-brands fa-youtube me-1"></i>Video (<?= $stats['total_videos'] ?>)
                        </a>
                    </div>
                </div>

                <!-- Search & Album Filter -->
                <div class="col-md-6">
                    <form method="GET" action="" class="d-flex gap-2">
                        <?php if (!empty($filters['tipe'])): ?>
                            <input type="hidden" name="tipe" value="<?= esc($filters['tipe']) ?>">
                        <?php endif; ?>
                        <select name="album" class="form-select form-select-sm rounded-pill" onchange="this.form.submit()">
                            <option value="">-- Semua Album Kegiatan --</option>
                            <?php foreach ($albums as $alb): ?>
                            <option value="<?= $alb['id'] ?>" <?= ($filters['album_id'] ?? '') == $alb['id'] ? 'selected' : '' ?>>
                                <?= esc($alb['nama_album']) ?> (<?= $alb['total_items'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="q" class="form-control form-control-sm rounded-pill" placeholder="Cari foto/video..." value="<?= esc($filters['search'] ?? '') ?>">
                        <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3"><i class="fa-solid fa-search"></i></button>
                    </form>
                </div>
            </div>

            <!-- Album Filter Pills if any -->
            <?php if (!empty($albums)): ?>
            <div class="d-flex gap-2 flex-wrap pt-3 mt-3 border-top">
                <small class="text-muted fw-semibold align-self-center me-1">Album Populer:</small>
                <?php foreach ($albums as $alb): ?>
                    <a href="<?= base_url('galeri?' . http_build_query(array_filter(['album' => $alb['id'], 'tipe' => $filters['tipe'] ?? null]))) ?>" 
                       class="badge <?= ($filters['album_id'] ?? '') == $alb['id'] ? 'bg-warning text-dark' : 'bg-white text-secondary border' ?> text-decoration-none p-2 rounded-pill font-weight-normal">
                        <i class="fa-solid fa-images me-1"></i><?= esc($alb['nama_album']) ?> (<?= $alb['total_items'] ?>)
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Album Banner (if filtered) -->
    <?php if ($activeAlbum): ?>
    <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1"><i class="fa-solid fa-images me-2 text-warning"></i>Album: <?= esc($activeAlbum['nama_album']) ?></h5>
            <small class="text-muted"><?= esc($activeAlbum['deskripsi'] ?: 'Dokumentasi kegiatan DPC & DPRa PKS Pancoran Mas.') ?></small>
        </div>
        <a href="<?= base_url('galeri') ?>" class="btn btn-sm btn-outline-dark rounded-pill">Lihat Semua Media</a>
    </div>
    <?php endif; ?>

    <!-- Media Grid -->
    <div class="row g-4 mb-5">
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
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden gallery-card hover-elevate">
                        <!-- Thumbnail / Media Wrap -->
                        <div class="position-relative media-thumb-wrap" style="height: 210px; background: #1e1e1e; cursor: pointer;"
                             <?php if ($isImage): ?>
                             onclick="openPublicLightbox('<?= $thumbUrl ?>', '<?= addslashes(esc($item['judul'])) ?>', '<?= addslashes(esc($item['deskripsi'] ?: '')) ?>', '<?= addslashes(esc($item['kredit'] ?: '')) ?>', '<?= $item['tanggal_media'] ? date('d/m/Y', strtotime($item['tanggal_media'])) : '' ?>', '<?= addslashes(esc($item['lokasi'] ?: '')) ?>')"
                             <?php else: ?>
                             onclick="openPublicVideo('<?= esc($item['video_type']) ?>', '<?= esc($item['video_id']) ?>', '<?= !empty($item['file_path']) ? base_url($item['file_path']) : '' ?>', '<?= addslashes(esc($item['judul'])) ?>')"
                             <?php endif; ?>>
                            
                            <img src="<?= $thumbUrl ?>" alt="<?= esc($item['judul']) ?>" class="w-100 h-100 object-fit-cover">

                            <!-- Badges -->
                            <div class="position-absolute top-0 start-0 m-2">
                                <?php if ($isImage): ?>
                                    <span class="badge bg-dark bg-opacity-75 rounded-pill"><i class="fa-solid fa-camera me-1"></i>Foto</span>
                                <?php else: ?>
                                    <span class="badge bg-danger rounded-pill"><i class="fa-brands fa-youtube me-1"></i>Video</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($isVideo): ?>
                            <div class="position-absolute top-50 start-50 translate-middle text-white play-btn-overlay">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger shadow-lg" style="width: 52px; height: 52px;">
                                    <i class="fa-solid fa-play fs-5 ms-1"></i>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body p-3 d-flex flex-column">
                            <h6 class="fw-bold text-dark mb-1 text-truncate-2" title="<?= esc($item['judul']) ?>" style="min-height: 2.7em; font-size: 0.95rem;">
                                <?= esc($item['judul']) ?>
                            </h6>

                            <?php if ($item['nama_album']): ?>
                                <small class="text-muted text-truncate mb-2">
                                    <i class="fa-solid fa-images me-1 text-warning"></i><?= esc($item['nama_album']) ?>
                                </small>
                            <?php endif; ?>

                            <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center text-muted" style="font-size: 0.75rem;">
                                <span><i class="fa-regular fa-calendar me-1"></i><?= $item['tanggal_media'] ? date('d M Y', strtotime($item['tanggal_media'])) : '-' ?></span>
                                <?php if ($item['lokasi']): ?>
                                    <span class="text-truncate" style="max-width: 50%;"><i class="fa-solid fa-location-dot me-1"></i><?= esc($item['lokasi']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center text-muted py-5 bg-white rounded-4 shadow-sm my-4">
                <i class="fa-solid fa-photo-film fa-4x mb-3 text-muted opacity-50"></i>
                <h5 class="fw-bold">Belum Ada Dokumentasi Media</h5>
                <p class="text-muted">Tidak ada foto atau video yang sesuai dengan kriteria filter.</p>
                <a href="<?= base_url('galeri') ?>" class="btn btn-outline-dark rounded-pill px-4">Reset Filter</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ======================================================== -->
<!-- PUBLIC LIGHTBOX MODAL -->
<!-- ======================================================== -->
<div class="modal fade" id="pubLightboxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: #121212; color: #fff;">
            <div class="modal-header border-0 py-3 px-4" style="background: #1e1e1e;">
                <h6 class="modal-title fw-bold text-white text-truncate" id="pubLightboxTitle">Foto Dokumentasi</h6>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="pubLightboxDownload" target="_blank" class="btn btn-sm btn-outline-light rounded-pill py-1 px-3" title="Unduh Foto Resolusi Penuh">
                        <i class="fa-solid fa-download me-1"></i>Unduh Foto
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body p-0 d-flex align-items-center justify-content-center" style="min-height: 520px; max-height: 80vh; overflow: hidden; background: #000;">
                <img id="pubLightboxImg" src="" class="img-fluid" style="max-height: 80vh; max-width: 100%; object-fit: contain;">
            </div>
            <div class="modal-footer border-0 py-3 px-4 d-flex justify-content-between" style="background: #1e1e1e; font-size: 0.9rem;">
                <div class="text-white-50" id="pubLightboxMeta"></div>
                <div class="text-warning fw-semibold" id="pubLightboxKredit"></div>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- PUBLIC VIDEO PLAYER MODAL -->
<!-- ======================================================== -->
<div class="modal fade" id="pubVideoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden" style="background: #000; color: #fff;">
            <div class="modal-header border-0 py-3 px-4" style="background: #1e1e1e;">
                <h6 class="modal-title fw-bold text-white text-truncate" id="pubVideoTitle">Pemutar Video</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" onclick="stopPubVideo()"></button>
            </div>
            <div class="modal-body p-0 ratio ratio-16x9 bg-black" id="pubVideoContainer"></div>
        </div>
    </div>
</div>

<script>
function openPublicLightbox(url, title, desc, kredit, tgl, lokasi) {
    document.getElementById('pubLightboxImg').src = url;
    document.getElementById('pubLightboxTitle').innerText = title;
    document.getElementById('pubLightboxDownload').href = url;
    
    let meta = '';
    if (tgl) meta += `<i class="fa-regular fa-calendar me-1"></i>${tgl} `;
    if (lokasi) meta += `• <i class="fa-solid fa-location-dot me-1"></i>${lokasi} `;
    if (desc) meta += `<div class="mt-1 text-white">${desc}</div>`;
    document.getElementById('pubLightboxMeta').innerHTML = meta;

    document.getElementById('pubLightboxKredit').innerHTML = kredit ? `<i class="fa-solid fa-camera me-1"></i>Dokumentasi: ${kredit}` : '';

    const modal = new bootstrap.Modal(document.getElementById('pubLightboxModal'));
    modal.show();
}

function openPublicVideo(type, videoId, videoFile, title) {
    document.getElementById('pubVideoTitle').innerText = title;
    const container = document.getElementById('pubVideoContainer');
    
    if (type === 'youtube' && videoId) {
        container.innerHTML = `<iframe src="https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>`;
    } else if (videoFile) {
        container.innerHTML = `<video controls autoplay class="w-100 h-100"><source src="${videoFile}" type="video/mp4">Browser Anda tidak mendukung pemutar video.</video>`;
    } else {
        container.innerHTML = `<div class="d-flex align-items-center justify-content-center h-100 text-white">Video tidak dapat diputar.</div>`;
    }

    const modal = new bootstrap.Modal(document.getElementById('pubVideoModal'));
    modal.show();
}

function stopPubVideo() {
    document.getElementById('pubVideoContainer').innerHTML = '';
}

document.getElementById('pubVideoModal').addEventListener('hidden.bs.modal', function () {
    stopPubVideo();
});
</script>

<style>
.gallery-card {
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.gallery-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.12) !important;
}
.media-thumb-wrap:hover .play-btn-overlay {
    transform: translate(-50%, -50%) scale(1.15);
    transition: transform 0.2s;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
<?= $this->endSection() ?>
