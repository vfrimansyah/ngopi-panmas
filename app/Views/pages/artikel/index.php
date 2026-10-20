<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-book-open"></i>
                Wawasan &amp; Edukasi
            </div>
            <h1 class="page-hero-title">Kumpulan Artikel</h1>
            <p class="page-hero-desc">
                Artikel, opini, dan bacaan berkualitas dari para kontributor Ngobrol Pikiran Indonesia.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Artikel</span>
            </div>
        </div>
    </div>
</section>

<!-- Content Section -->
<section class="pub-section section-bg-white" style="padding-top:50px;">
    <div class="container">
        <div class="row g-4">

            <!-- Main Content -->
            <div class="col-lg-8">

                <!-- Filter Bar -->
                <div class="filter-bar">
                    <span class="filter-bar-label"><i class="fa-solid fa-filter me-1"></i>Filter:</span>
                    <div class="filter-pills">
                        <a href="<?= base_url('artikel') ?>"
                           class="filter-pill <?= empty($selectedCategory) ? 'active' : '' ?>"
                           id="filter-artikel-semua">Semua</a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?= base_url('artikel?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                           class="filter-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>"
                           id="filter-art-<?= $cat['id'] ?>">
                            <?= esc($cat['nama_kategori']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="pub-search-wrap ms-auto">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="artikelSearch" placeholder="Cari artikel..." 
                               value="<?= esc(request()->getGet('q') ?? '') ?>">
                    </div>
                </div>

                <!-- Articles Grid -->
                <?php if (!empty($artikels)): ?>
                    <div class="row g-4" id="artikelGrid">
                        <?php foreach ($artikels as $art): ?>
                        <div class="col-12 col-md-6">
                            <div class="content-card">
                                <div class="content-card-img">
                                    <?php if (!empty($art['foto_cover'])): ?>
                                        <img src="<?= base_url('uploads/' . $art['foto_cover']) ?>"
                                             alt="<?= esc($art['judul']) ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="content-card-img-placeholder">
                                            <i class="fa-solid fa-book-open"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="content-card-badge">Artikel</span>
                                </div>
                                <div class="content-card-body">
                                    <div class="content-card-title">
                                        <a href="<?= base_url('artikel/' . $art['slug']) ?>">
                                            <?= esc($art['judul']) ?>
                                        </a>
                                    </div>
                                    <div class="content-card-excerpt">
                                        <?= esc(strip_tags(substr($art['konten'] ?? '', 0, 160))) ?>...
                                    </div>
                                    <div class="content-card-footer">
                                        <div class="content-card-meta">
                                            <span>
                                                <i class="fa-regular fa-calendar"></i>
                                                <?= date('d M Y', strtotime($art['published_at'] ?? $art['created_at'])) ?>
                                            </span>
                                            <?php if (!empty($art['views_count'])): ?>
                                            <span>
                                                <i class="fa-regular fa-eye"></i>
                                                <?= number_format($art['views_count']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= base_url('artikel/' . $art['slug']) ?>" class="content-card-link">
                                            Baca <i class="fa-solid fa-arrow-right fa-xs"></i>
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
                        <a href="<?= $pager->getPageURI($p) ?>" class="page-link-item <?= $p == $currentPage ? 'active' : '' ?>">
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
                        <div class="empty-state-icon"><i class="fa-solid fa-book-open"></i></div>
                        <div class="empty-state-title">Belum Ada Artikel</div>
                        <p class="empty-state-desc">Belum ada artikel dipublikasikan pada kategori ini.</p>
                        <?php if ($selectedCategory): ?>
                        <a href="<?= base_url('artikel') ?>" class="btn-coffee-outline">
                            <i class="fa-solid fa-rotate-left"></i>Reset Filter
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-tags"></i>
                            <h5>Kategori Artikel</h5>
                        </div>
                        <div class="sidebar-widget-body">
                            <div style="display:flex; flex-wrap:wrap;">
                                <a href="<?= base_url('artikel') ?>" class="category-pill <?= empty($selectedCategory) ? 'active' : '' ?>">Semua</a>
                                <?php foreach ($categories as $cat): ?>
                                <a href="<?= base_url('artikel?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                                   class="category-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>">
                                    <?= esc($cat['nama_kategori']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <?php if (!auth()->loggedIn()): ?>
                    <div class="sidebar-widget" style="background:linear-gradient(135deg,var(--coffee-900),var(--coffee-800)); border-color:var(--coffee-700);">
                        <div class="sidebar-widget-body text-center py-4">
                            <i class="fa-solid fa-lightbulb fa-2x mb-3" style="color:var(--gold-400);"></i>
                            <h6 style="color:#fff; font-family:var(--font-serif);">Punya Gagasan?</h6>
                            <p style="color:var(--coffee-200); font-size:0.82rem; margin-bottom:16px;">
                                Daftarkan diri Anda untuk menulis ide dan gagasan di Ngobrol Pikiran Indonesia.
                            </p>
                            <a href="<?= base_url('register') ?>" class="btn-gold btn-full mb-2">Daftar Sekarang</a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->section('js') ?>
<script>
const searchInput = document.getElementById('artikelSearch');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
        const q = this.value.trim();
        if (q.length >= 2 || q.length === 0) {
            const url = new URL(window.location.href);
            if (q) url.searchParams.set('q', q); else url.searchParams.delete('q');
            window.location.href = url.toString();
        }
    }, 600));
}
function debounce(fn, delay) { let t; return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); }; }
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
