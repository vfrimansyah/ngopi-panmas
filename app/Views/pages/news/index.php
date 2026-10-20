<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-newspaper"></i>
                Informasi Terkini
            </div>
            <h1 class="page-hero-title">Berita &amp; Artikel</h1>
            <p class="page-hero-desc">
                Kumpulan berita, informasi program kerja, dan artikel dari komunitas PANMAS.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Berita</span>
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
                        <a href="<?= base_url('berita') ?>"
                           class="filter-pill <?= empty($selectedCategory) ? 'active' : '' ?>"
                           id="filter-semua">Semua</a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?= base_url('berita?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                           class="filter-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>"
                           id="filter-<?= $cat['id'] ?>">
                            <?= esc($cat['nama_kategori']) ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="pub-search-wrap ms-auto">
                        <i class="fa-solid fa-magnifying-glass search-icon"></i>
                        <input type="text" id="newsSearch" placeholder="Cari berita..." 
                               value="<?= esc(request()->getGet('q') ?? '') ?>">
                    </div>
                </div>

                <!-- News Grid -->
                <?php if (!empty($news)): ?>
                    <div class="row g-4" id="newsGrid">
                        <?php foreach ($news as $article): ?>
                        <div class="col-12 col-md-6 col-lg-6">
                            <div class="content-card">
                                <div class="content-card-img">
                                    <?php if (!empty($article['foto_cover'])): ?>
                                        <img src="<?= base_url('uploads/' . $article['foto_cover']) ?>"
                                             alt="<?= esc($article['judul']) ?>"
                                             loading="lazy">
                                    <?php else: ?>
                                        <div class="content-card-img-placeholder">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="content-card-body">
                                    <div class="content-card-category">
                                        <i class="fa-solid fa-circle-dot me-1 fa-2xs"></i>
                                        <?= esc($article['tipe'] ?? 'Umum') ?>
                                    </div>
                                    <div class="content-card-title">
                                        <a href="<?= base_url('berita/' . $article['slug']) ?>">
                                            <?= esc($article['judul']) ?>
                                        </a>
                                    </div>
                                    <div class="content-card-excerpt">
                                        <?= esc(strip_tags(substr($article['konten'] ?? '', 0, 180))) ?>...
                                    </div>
                                    <div class="content-card-footer">
                                        <div class="content-card-meta">
                                            <span>
                                                <i class="fa-regular fa-calendar"></i>
                                                <?= date('d M Y', strtotime($article['published_at'] ?? $article['created_at'])) ?>
                                            </span>
                                            <?php if (!empty($article['views_count'])): ?>
                                            <span>
                                                <i class="fa-regular fa-eye"></i>
                                                <?= number_format($article['views_count']) ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?= base_url('berita/' . $article['slug']) ?>"
                                           class="content-card-link">
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
                        <?php else: ?>
                        <span class="page-link-item disabled"><i class="fa-solid fa-chevron-left fa-xs"></i></span>
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
                        <?php else: ?>
                        <span class="page-link-item disabled"><i class="fa-solid fa-chevron-right fa-xs"></i></span>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-state-icon"><i class="fa-solid fa-newspaper"></i></div>
                        <div class="empty-state-title">Belum Ada Berita</div>
                        <p class="empty-state-desc">
                            <?php if ($selectedCategory): ?>
                                Tidak ada berita untuk kategori "<strong><?= esc($selectedCategory) ?></strong>".
                            <?php else: ?>
                                Belum ada berita yang dipublikasikan.
                            <?php endif; ?>
                        </p>
                        <?php if ($selectedCategory): ?>
                        <a href="<?= base_url('berita') ?>" class="btn-coffee-outline">
                            <i class="fa-solid fa-rotate-left"></i>Reset Filter
                        </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">

                    <!-- Kategori Widget -->
                    <?php if (!empty($categories)): ?>
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-tags"></i>
                            <h5>Kategori</h5>
                        </div>
                        <div class="sidebar-widget-body">
                            <div style="display:flex; flex-wrap:wrap;">
                                <a href="<?= base_url('berita') ?>"
                                   class="category-pill <?= empty($selectedCategory) ? 'active' : '' ?>">
                                    Semua
                                </a>
                                <?php foreach ($categories as $cat): ?>
                                <a href="<?= base_url('berita?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                                   class="category-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>">
                                    <?= esc($cat['nama_kategori']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Berita Terbaru Widget -->
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                            <h5>Berita Terbaru</h5>
                        </div>
                        <div class="sidebar-widget-body" style="padding: 8px 18px;">
                            <?php if (!empty($news)): ?>
                                <?php foreach (array_slice($news, 0, 5) as $sideNews): ?>
                                <div class="sidebar-news-item">
                                    <?php if (!empty($sideNews['foto_cover'])): ?>
                                        <img class="sidebar-news-img"
                                             src="<?= base_url('uploads/' . $sideNews['foto_cover']) ?>"
                                             alt="<?= esc($sideNews['judul']) ?>">
                                    <?php else: ?>
                                        <div class="sidebar-news-img d-flex align-items-center justify-content-center"
                                             style="background:var(--coffee-100); color:var(--coffee-400);">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="sidebar-news-title">
                                            <a href="<?= base_url('berita/' . $sideNews['slug']) ?>">
                                                <?= esc($sideNews['judul']) ?>
                                            </a>
                                        </div>
                                        <div class="sidebar-news-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?= date('d M Y', strtotime($sideNews['published_at'] ?? $sideNews['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted-warm small mb-0 py-2">Belum ada berita.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- CTA Login -->
                    <?php if (!auth()->loggedIn()): ?>
                    <div class="sidebar-widget" style="background:linear-gradient(135deg,var(--coffee-900),var(--coffee-800)); border-color:var(--coffee-700);">
                        <div class="sidebar-widget-body text-center py-4">
                            <i class="fa-solid fa-shield-halved fa-2x mb-3" style="color:var(--gold-400);"></i>
                            <h6 style="color:#fff; font-family:var(--font-serif);">Area Anggota</h6>
                            <p style="color:var(--coffee-300); font-size:0.82rem; margin-bottom:16px;">
                                Login untuk mendapatkan akses penuh ke seluruh fitur PANMAS.
                            </p>
                            <a href="<?= base_url('login') ?>" class="btn-gold btn-full mb-2" id="sidebar-btn-login">
                                <i class="fa-solid fa-right-to-bracket"></i>Masuk
                            </a>
                            <a href="<?= base_url('register') ?>" class="btn-nav-login btn-full" id="sidebar-btn-register"
                               style="justify-content:center;">
                                Belum punya akun? Daftar
                            </a>
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
// Live search filter
const searchInput = document.getElementById('newsSearch');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function() {
        const q = this.value.trim();
        if (q.length >= 2) {
            const url = new URL(window.location.href);
            url.searchParams.set('q', q);
            window.location.href = url.toString();
        }
    }, 600));
}

function debounce(fn, delay) {
    let t;
    return function(...args) {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
