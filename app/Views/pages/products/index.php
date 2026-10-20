<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-store"></i>
                Katalog UMKM Anggota
            </div>
            <h1 class="page-hero-title">Katalog Produk</h1>
            <p class="page-hero-desc">
                Temukan produk dan jasa dari anggota PANMAS. Dukung ekonomi komunitas dengan berbelanja dari sesama anggota.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Katalog Produk</span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="pub-section section-bg-white" style="padding-top:50px;">
    <div class="container">

        <!-- Filter Bar -->
        <div class="filter-bar mb-4">
            <span class="filter-bar-label"><i class="fa-solid fa-filter me-1"></i>Kategori:</span>
            <div class="filter-pills">
                <a href="<?= base_url('katalog') ?>"
                   class="filter-pill <?= empty($selectedCategory) ? 'active' : '' ?>"
                   id="filter-semua-produk">Semua</a>
                <?php foreach ($categories as $cat): ?>
                <a href="<?= base_url('katalog?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                   class="filter-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>"
                   id="filter-cat-<?= $cat['id'] ?>">
                    <?= esc($cat['nama_kategori']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (!empty($products)): ?>
            <div class="row g-4" id="productsGrid">
                <?php foreach ($products as $prod): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <div class="product-card-img">
                            <?php
                            $fotos = json_decode($prod['foto_urls'] ?? '[]', true);
                            $thumb = !empty($fotos[0]) ? $fotos[0] : null;
                            ?>
                            <?php if ($thumb): ?>
                                <img src="<?= base_url('uploads/' . $thumb) ?>"
                                     alt="<?= esc($prod['nama_produk']) ?>"
                                     loading="lazy">
                            <?php else: ?>
                                <div class="product-card-img-placeholder">
                                    <i class="fa-solid fa-box-open"></i>
                                    <span>Foto belum tersedia</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-name"><?= esc($prod['nama_produk']) ?></div>
                            <?php if (!empty($prod['deskripsi'])): ?>
                            <div class="product-card-seller" style="font-size:0.8rem; color:var(--warm-gray); margin-bottom:10px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= esc(strip_tags($prod['deskripsi'])) ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($prod['harga']) && $prod['harga'] > 0): ?>
                                <div class="product-card-price">
                                    Rp <?= number_format($prod['harga'], 0, ',', '.') ?>
                                    <?php if (!empty($prod['satuan'])): ?>
                                    <small style="font-size:0.7rem; font-weight:500; color:var(--warm-gray);">/<?= esc($prod['satuan']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="product-card-price negotiable">Harga Nego</div>
                            <?php endif; ?>
                            <a href="<?= base_url('katalog/' . $prod['id']) ?>"
                               class="btn-coffee btn-sm-coffee btn-full" id="produk-detail-<?= $prod['id'] ?>">
                                <i class="fa-solid fa-eye"></i>Lihat Detail
                            </a>
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
                <div class="empty-state-icon"><i class="fa-solid fa-store"></i></div>
                <div class="empty-state-title">Belum Ada Produk</div>
                <p class="empty-state-desc">
                    <?php if ($selectedCategory): ?>
                        Tidak ada produk pada kategori "<strong><?= esc($selectedCategory) ?></strong>".
                    <?php else: ?>
                        Belum ada produk yang dipublikasikan.
                    <?php endif; ?>
                </p>
                <?php if ($selectedCategory): ?>
                <a href="<?= base_url('katalog') ?>" class="btn-coffee-outline">
                    <i class="fa-solid fa-rotate-left"></i>Lihat Semua Produk
                </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
