<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-lightbulb"></i>
                Detail Gagasan
            </div>
            <h1 class="page-hero-title" style="max-width:750px;">
                <?= esc($ide['judul']) ?>
            </h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <a href="<?= base_url('ide') ?>">Ide &amp; Gagasan</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span><?= esc(mb_strimwidth($ide['judul'], 0, 40, '...')) ?></span>
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
                    <div class="article-author-avatar" style="background:var(--gold-100); color:var(--coffee-700);">
                        <i class="fa-solid fa-user-gear"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; color:var(--coffee-900); font-size:0.95rem;">
                            Kontributor NGOPI
                        </div>
                        <div style="font-size:0.8rem; color:var(--warm-gray); display:flex; gap:14px; margin-top:4px; flex-wrap:wrap;">
                            <span><i class="fa-regular fa-calendar me-1"></i>
                                <?= date('d F Y', strtotime($ide['published_at'] ?? $ide['created_at'])) ?>
                            </span>
                            <?php if (!empty($ide['views_count'])): ?>
                            <span><i class="fa-regular fa-eye me-1"></i>
                                <?= number_format($ide['views_count']) ?> kali dibaca
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="article-body">
                    <?= $ide['konten'] ?>
                </div>

                <!-- Share -->
                <div class="article-share mt-4">
                    <span style="font-size:0.82rem; font-weight:700; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px;">
                        <i class="fa-solid fa-share-nodes me-1"></i>Bagikan Gagasan:
                    </span>
                    <?php $shareUrl = current_url(); $shareTitle = urlencode($ide['judul']); ?>
                    <a href="https://api.whatsapp.com/send?text=<?= $shareTitle . ' ' . urlencode($shareUrl) ?>" target="_blank" class="share-btn share-btn-wa">
                        <i class="fa-brands fa-whatsapp"></i>WhatsApp
                    </a>
                </div>

                <!-- Back -->
                <div class="mt-4">
                    <a href="<?= base_url('ide') ?>" class="btn-coffee-outline">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Daftar Ide
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-lightbulb"></i>
                            <h5>Gagasan Lainnya</h5>
                        </div>
                        <div class="sidebar-widget-body" style="padding:8px 18px;">
                            <?php if (!empty($recentIdes)): ?>
                                <?php foreach ($recentIdes as $ri): ?>
                                <div class="sidebar-news-item">
                                    <div>
                                        <div class="sidebar-news-title">
                                            <a href="<?= base_url('ide/' . $ri['slug']) ?>">
                                                <?= esc($ri['judul']) ?>
                                            </a>
                                        </div>
                                        <div class="sidebar-news-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?= date('d M Y', strtotime($ri['published_at'] ?? $ri['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted-warm small mb-0 py-2">Belum ada gagasan lain.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
