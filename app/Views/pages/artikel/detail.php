<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-book-open"></i>
                Artikel Pilihan
            </div>
            <h1 class="page-hero-title" style="max-width:750px;">
                <?= esc($artikel['judul']) ?>
            </h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <a href="<?= base_url('artikel') ?>">Artikel</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span><?= esc(mb_strimwidth($artikel['judul'], 0, 40, '...')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-8">

                <!-- Meta Box -->
                <div class="article-author-box mb-4">
                    <div class="article-author-avatar">
                        <i class="fa-solid fa-pen-nib"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; color:var(--coffee-900); font-size:0.95rem;">
                            Redaksi Ngobrol Pikiran Indonesia
                        </div>
                        <div style="font-size:0.8rem; color:var(--warm-gray); display:flex; gap:14px; margin-top:4px; flex-wrap:wrap;">
                            <span><i class="fa-regular fa-calendar me-1"></i>
                                <?= date('d F Y', strtotime($artikel['published_at'] ?? $artikel['created_at'])) ?>
                            </span>
                            <?php if (!empty($artikel['views_count'])): ?>
                            <span><i class="fa-regular fa-eye me-1"></i>
                                <?= number_format($artikel['views_count']) ?> dibaca
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Cover Image -->
                <?php if (!empty($artikel['foto_cover'])): ?>
                <div class="mb-4 img-zoom-wrap" style="border-radius:var(--radius-md); overflow:hidden; box-shadow:var(--shadow-md);">
                    <img src="<?= base_url('uploads/' . $artikel['foto_cover']) ?>" alt="<?= esc($artikel['judul']) ?>" style="width:100%; height:auto; display:block;">
                </div>
                <?php endif; ?>

                <!-- Body -->
                <div class="article-body">
                    <?= $artikel['konten'] ?>
                </div>

                <!-- Share Buttons -->
                <div class="article-share">
                    <span style="font-size:0.82rem; font-weight:700; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px;">
                        <i class="fa-solid fa-share-nodes me-1"></i>Bagikan:
                    </span>
                    <?php $shareUrl = current_url(); $shareTitle = urlencode($artikel['judul']); ?>
                    <a href="https://api.whatsapp.com/send?text=<?= $shareTitle . ' ' . urlencode($shareUrl) ?>" target="_blank" class="share-btn share-btn-wa">
                        <i class="fa-brands fa-whatsapp"></i>WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" class="share-btn share-btn-fb">
                        <i class="fa-brands fa-facebook-f"></i>Facebook
                    </a>
                </div>

                <!-- Back Link -->
                <div class="mt-4">
                    <a href="<?= base_url('artikel') ?>" class="btn-coffee-outline">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Daftar Artikel
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-fire-flame-curved"></i>
                            <h5>Artikel Terkait</h5>
                        </div>
                        <div class="sidebar-widget-body" style="padding:8px 18px;">
                            <?php if (!empty($recentArtikels)): ?>
                                <?php foreach ($recentArtikels as $ra): ?>
                                <div class="sidebar-news-item">
                                    <div>
                                        <div class="sidebar-news-title">
                                            <a href="<?= base_url('artikel/' . $ra['slug']) ?>">
                                                <?= esc($ra['judul']) ?>
                                            </a>
                                        </div>
                                        <div class="sidebar-news-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?= date('d M Y', strtotime($ra['published_at'] ?? $ra['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted-warm small mb-0 py-2">Belum ada artikel terkait.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
