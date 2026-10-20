<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-newspaper"></i>
                <?= esc($news['tipe'] ?? 'Berita') ?>
            </div>
            <h1 class="page-hero-title" style="max-width:700px;">
                <?= esc($news['judul']) ?>
            </h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <a href="<?= base_url('berita') ?>">Berita</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span><?= esc(mb_strimwidth($news['judul'], 0, 40, '...')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row g-5">

            <!-- Main Article -->
            <div class="col-lg-8">

                <!-- Author & Meta -->
                <div class="article-author-box mb-4">
                    <div class="article-author-avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; color:var(--coffee-900); font-size:0.95rem;">
                            Redaksi PANMAS
                        </div>
                        <div style="font-size:0.8rem; color:var(--warm-gray); display:flex; gap:14px; margin-top:4px; flex-wrap:wrap;">
                            <span><i class="fa-regular fa-calendar me-1"></i>
                                <?= date('d F Y', strtotime($news['published_at'] ?? $news['created_at'])) ?>
                            </span>
                            <?php if (!empty($news['views_count'])): ?>
                            <span><i class="fa-regular fa-eye me-1"></i>
                                <?= number_format($news['views_count']) ?> kali dibaca
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Cover Image -->
                <?php if (!empty($news['foto_cover'])): ?>
                <div class="mb-4 img-zoom-wrap" style="border-radius:var(--radius-md); overflow:hidden; box-shadow:var(--shadow-md);">
                    <img src="<?= base_url('uploads/' . $news['foto_cover']) ?>"
                         alt="<?= esc($news['judul']) ?>"
                         style="width:100%; height:auto; display:block;">
                </div>
                <?php endif; ?>

                <!-- Article Body -->
                <div class="article-body">
                    <?= $news['konten'] ?>
                </div>

                <!-- Share Buttons -->
                <div class="article-share">
                    <span style="font-size:0.82rem; font-weight:700; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px;">
                        <i class="fa-solid fa-share-nodes me-1"></i>Bagikan:
                    </span>
                    <?php $shareUrl = current_url(); $shareTitle = urlencode($news['judul']); ?>
                    <a href="https://api.whatsapp.com/send?text=<?= $shareTitle . ' ' . urlencode($shareUrl) ?>"
                       target="_blank" class="share-btn share-btn-wa">
                        <i class="fa-brands fa-whatsapp"></i>WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>"
                       target="_blank" class="share-btn share-btn-fb">
                        <i class="fa-brands fa-facebook-f"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= $shareTitle ?>"
                       target="_blank" class="share-btn share-btn-tw">
                        <i class="fa-brands fa-x-twitter"></i>Twitter / X
                    </a>
                </div>

                <!-- Tags -->
                <div class="mb-4" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <span style="font-size:0.78rem; font-weight:700; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px;">
                        <i class="fa-solid fa-tags me-1"></i>Tag:
                    </span>
                    <span class="tag-chip"><?= esc($news['tipe'] ?? 'Berita') ?></span>
                    <a href="<?= base_url('berita') ?>" class="tag-chip">PANMAS</a>
                    <a href="<?= base_url('berita') ?>" class="tag-chip">Komunitas</a>
                </div>

                <!-- Back -->
                <div class="mt-4">
                    <a href="<?= base_url('berita') ?>" class="btn-coffee-outline">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Daftar Berita
                    </a>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">

                    <!-- Berita Terbaru -->
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                            <h5>Berita Terkait</h5>
                        </div>
                        <div class="sidebar-widget-body" style="padding:8px 18px;">
                            <?php if (!empty($recentNews)): ?>
                                <?php foreach ($recentNews as $rn): ?>
                                <div class="sidebar-news-item">
                                    <?php if (!empty($rn['foto_cover'])): ?>
                                        <img class="sidebar-news-img"
                                             src="<?= base_url('uploads/' . $rn['foto_cover']) ?>"
                                             alt="<?= esc($rn['judul']) ?>">
                                    <?php else: ?>
                                        <div class="sidebar-news-img d-flex align-items-center justify-content-center"
                                             style="background:var(--coffee-100); color:var(--coffee-400);">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="sidebar-news-title">
                                            <a href="<?= base_url('berita/' . $rn['slug']) ?>">
                                                <?= esc($rn['judul']) ?>
                                            </a>
                                        </div>
                                        <div class="sidebar-news-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?= date('d M Y', strtotime($rn['published_at'] ?? $rn['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted-warm small mb-0 py-2">Tidak ada berita terkait.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Kategori -->
                    <?php if (!empty($categories)): ?>
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-tags"></i>
                            <h5>Kategori</h5>
                        </div>
                        <div class="sidebar-widget-body">
                            <div style="display:flex; flex-wrap:wrap;">
                                <?php foreach ($categories as $cat): ?>
                                <a href="<?= base_url('berita?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                                   class="category-pill">
                                    <?= esc($cat['nama_kategori']) ?>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Login CTA -->
                    <?php if (!auth()->loggedIn()): ?>
                    <div class="sidebar-widget"
                         style="background:linear-gradient(135deg,var(--coffee-900),var(--coffee-800)); border-color:var(--coffee-700);">
                        <div class="sidebar-widget-body text-center py-4">
                            <i class="fa-solid fa-shield-halved fa-2x mb-3" style="color:var(--gold-400);"></i>
                            <h6 style="color:#fff; font-family:var(--font-serif);">Area Anggota</h6>
                            <p style="color:var(--coffee-300); font-size:0.82rem; margin-bottom:16px;">
                                Login untuk berpartisipasi dan mengakses fitur lengkap.
                            </p>
                            <a href="<?= base_url('login') ?>" class="btn-gold btn-full" id="sidebar-detail-login">
                                <i class="fa-solid fa-right-to-bracket"></i>Masuk
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
