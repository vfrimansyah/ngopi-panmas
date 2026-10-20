<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-lightbulb"></i>
                Gagasan &amp; Inovasi
            </div>
            <h1 class="page-hero-title">Ide &amp; Gagasan</h1>
            <p class="page-hero-desc">
                Ruang apresiasi gagasan, inisiatif, dan konsep perubahan dari para anggota Ngobrol Pikiran Indonesia.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Ide &amp; Gagasan</span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="pub-section section-bg-white" style="padding-top:50px;">
    <div class="container">

        <!-- Top Header Actions -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div class="filter-bar mb-0" style="flex:1;">
                <span class="filter-bar-label"><i class="fa-solid fa-filter me-1"></i>Kategori:</span>
                <div class="filter-pills">
                    <a href="<?= base_url('ide') ?>" class="filter-pill <?= empty($selectedCategory) ? 'active' : '' ?>">Semua</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="<?= base_url('ide?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                       class="filter-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>">
                        <?= esc($cat['nama_kategori']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit button -->
            <a href="<?= base_url('ide/create') ?>" class="btn-gold" id="btn-tulis-ide">
                <i class="fa-solid fa-plus me-1"></i>Tulis Ide Baru
            </a>
        </div>

        <!-- Ide Grid -->
        <?php if (!empty($ides)): ?>
            <div class="row g-4">
                <?php foreach ($ides as $ide): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="content-card h-100" style="border-top:3px solid var(--gold-500);">
                        <div class="content-card-body" style="padding:22px;">
                            <div class="content-card-category" style="color:var(--gold-500); font-weight:700;">
                                <i class="fa-solid fa-lightbulb me-1"></i>Gagasan
                            </div>
                            <div class="content-card-title mb-2">
                                <a href="<?= base_url('ide/' . $ide['slug']) ?>">
                                    <?= esc($ide['judul']) ?>
                                </a>
                            </div>
                            <div class="content-card-excerpt">
                                <?= esc(strip_tags(substr($ide['konten'] ?? '', 0, 160))) ?>...
                            </div>
                            <div class="content-card-footer mt-auto pt-3" style="border-top:1px solid var(--coffee-100);">
                                <span class="content-card-meta">
                                    <span>
                                        <i class="fa-regular fa-calendar"></i>
                                        <?= date('d M Y', strtotime($ide['published_at'] ?? $ide['created_at'])) ?>
                                    </span>
                                </span>
                                <a href="<?= base_url('ide/' . $ide['slug']) ?>" class="content-card-link">
                                    Baca Gagasan <i class="fa-solid fa-arrow-right fa-xs"></i>
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
                <?php $currentPage = $pager->getCurrentPage(); $totalPages = $pager->getPageCount(); ?>
                <?php if ($currentPage > 1): ?>
                <a href="<?= $pager->getPreviousPageURI() ?>" class="page-link-item"><i class="fa-solid fa-chevron-left fa-xs"></i></a>
                <?php endif; ?>
                <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                <a href="<?= $pager->getPageURI($p) ?>" class="page-link-item <?= $p == $currentPage ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $pager->getNextPageURI() ?>" class="page-link-item"><i class="fa-solid fa-chevron-right fa-xs"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-lightbulb"></i></div>
                <div class="empty-state-title">Belum Ada Gagasan</div>
                <p class="empty-state-desc">Jadilah yang pertama menyampaikan ide dan gagasan inspiratif Anda.</p>
                <a href="<?= base_url('ide/create') ?>" class="btn-gold"><i class="fa-solid fa-plus me-1"></i>Tulis Ide Sekarang</a>
            </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->endSection() ?>
