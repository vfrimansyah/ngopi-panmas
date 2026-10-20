<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-calendar-days"></i>
                Jadwal Kegiatan
            </div>
            <h1 class="page-hero-title">Agenda &amp; Event</h1>
            <p class="page-hero-desc">
                Semua event dan kegiatan yang diselenggarakan oleh PANMAS dan komunitas anggota.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Agenda</span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="pub-section section-bg-white" style="padding-top:50px;">
    <div class="container">

        <!-- Filter Bar -->
        <div class="filter-bar mb-4">
            <span class="filter-bar-label"><i class="fa-solid fa-filter me-1"></i>Status:</span>
            <div class="filter-pills">
                <a href="<?= base_url('event') ?>"
                   class="filter-pill <?= $selectedFilter == 'all' ? 'active' : '' ?>"
                   id="filter-all">Semua</a>
                <a href="<?= base_url('event?status=upcoming') ?>"
                   class="filter-pill <?= $selectedFilter == 'upcoming' ? 'active' : '' ?>"
                   id="filter-upcoming">Akan Datang</a>
                <a href="<?= base_url('event?status=ongoing') ?>"
                   class="filter-pill <?= $selectedFilter == 'ongoing' ? 'active' : '' ?>"
                   id="filter-ongoing">Berlangsung</a>
                <a href="<?= base_url('event?status=selesai') ?>"
                   class="filter-pill <?= $selectedFilter == 'selesai' ? 'active' : '' ?>"
                   id="filter-selesai">Selesai</a>
            </div>
        </div>

        <!-- Events Grid -->
        <?php if (!empty($events)): ?>
            <div class="row g-4">
                <?php foreach ($events as $evt): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="event-card">
                        <div class="event-card-banner">
                            <?php if (!empty($evt['poster_url'])): ?>
                                <img src="<?= base_url('uploads/' . $evt['poster_url']) ?>"
                                     alt="<?= esc($evt['judul']) ?>" loading="lazy">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--coffee-700),var(--coffee-600));display:flex;align-items:center;justify-content:center;font-size:3rem;color:rgba(255,255,255,0.25);">
                                    <i class="fa-solid fa-calendar-star"></i>
                                </div>
                            <?php endif; ?>
                            <?php
                            $status      = $evt['status'] ?? 'upcoming';
                            $statusLabel = ['upcoming'=>'Akan Datang','ongoing'=>'Berlangsung','selesai'=>'Selesai'][$status] ?? $status;
                            ?>
                            <span class="event-card-status <?= $status ?>"><?= $statusLabel ?></span>
                            <div class="event-card-date-badge">
                                <div class="day"><?= date('d', strtotime($evt['tanggal_mulai'])) ?></div>
                                <div class="month"><?= date('M Y', strtotime($evt['tanggal_mulai'])) ?></div>
                            </div>
                        </div>
                        <div class="event-card-body">
                            <div class="event-card-title">
                                <a href="<?= base_url('event/' . ($evt['slug'] ?? $evt['id'])) ?>">
                                    <?= esc($evt['judul']) ?>
                                </a>
                            </div>
                            <div class="event-card-meta">
                                <span>
                                    <i class="fa-solid fa-calendar"></i>
                                    <?= date('d M Y', strtotime($evt['tanggal_mulai'])) ?>
                                    <?php if (!empty($evt['tanggal_selesai']) && $evt['tanggal_selesai'] != $evt['tanggal_mulai']): ?>
                                    — <?= date('d M Y', strtotime($evt['tanggal_selesai'])) ?>
                                    <?php endif; ?>
                                </span>
                                <?php if (!empty($evt['lokasi'])): ?>
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    <?= esc($evt['lokasi']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($evt['kuota'])): ?>
                                <span>
                                    <i class="fa-solid fa-users"></i>
                                    Kuota <?= number_format($evt['kuota']) ?> peserta
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="event-card-footer">
                                <a href="<?= base_url('event/' . ($evt['slug'] ?? $evt['id'])) ?>"
                                   class="btn-coffee btn-sm-coffee btn-full" id="evt-detail-<?= $evt['id'] ?>">
                                    <i class="fa-solid fa-info-circle"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($pager): ?>
            <div class="pub-pagination mt-5">
                <?php
                $currentPage = $pager->getCurrentPage();
                $totalPages  = $pager->getPageCount();
                ?>
                <?php if ($currentPage > 1): ?>
                <a href="<?= $pager->getPreviousPageURI() ?>" class="page-link-item">
                    <i class="fa-solid fa-chevron-left fa-xs"></i>
                </a>
                <?php endif; ?>

                <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                <a href="<?= $pager->getPageURI($p) ?>"
                   class="page-link-item <?= $p == $currentPage ? 'active' : '' ?>">
                    <?= $p ?>
                </a>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $pager->getNextPageURI() ?>" class="page-link-item">
                    <i class="fa-solid fa-chevron-right fa-xs"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                <div class="empty-state-title">Belum Ada Event</div>
                <p class="empty-state-desc">
                    <?php if ($selectedFilter != 'all'): ?>
                        Tidak ada event dengan status <strong><?= esc($selectedFilter) ?></strong>.
                    <?php else: ?>
                        Belum ada event yang tersedia saat ini.
                    <?php endif; ?>
                </p>
                <?php if ($selectedFilter != 'all'): ?>
                <a href="<?= base_url('event') ?>" class="btn-coffee-outline">
                    <i class="fa-solid fa-rotate-left"></i>Lihat Semua Event
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
