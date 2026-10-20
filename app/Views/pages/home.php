<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- =================== HERO =================== -->
<section class="pub-hero">
    <div class="container">
        <div class="pub-hero-content">
            <span class="pub-hero-eyebrow">
                <i class="fa-solid fa-comments"></i>
                Ngobrol Pikiran Indonesia (NGOPI)
            </span>
            <h1 class="pub-hero-title">
                Dari Pikiran <em>Menjadi Gerakan</em>
            </h1>
            <p class="pub-hero-desc">
                Portal digital <strong>Ngobrol Pikiran Indonesia</strong> (www.indonesiangopi.com) — wadah bertukar gagasan, 
                berita terkini, agenda kegiatan, katalog produk anggota, dan jaringan kolaborasi masyarakat Indonesia.
            </p>
            <div class="pub-hero-actions">
                <?php if (!auth()->loggedIn()): ?>
                <a href="<?= base_url('register') ?>" class="btn-gold" id="hero-btn-register">
                    <i class="fa-solid fa-user-plus"></i>Gabung Komunitas
                </a>
                <?php endif; ?>
                <a href="<?= base_url('berita') ?>" class="btn-coffee-outline" id="hero-btn-berita"
                   style="border-color:rgba(200,160,64,0.5); color:var(--gold-300);">
                    <i class="fa-solid fa-newspaper"></i>Baca Berita
                </a>
                <a href="<?= base_url('event') ?>" class="btn-coffee-outline" id="hero-btn-event"
                   style="border-color:rgba(200,160,64,0.5); color:var(--gold-300);">
                    <i class="fa-solid fa-calendar-days"></i>Lihat Agenda
                </a>
            </div>
        </div>
    </div>
</section>

<!-- =================== STATS STRIP =================== -->
<div class="container" style="position:relative; z-index:5; margin-top:-10px;">
    <div class="pub-stats-strip">
        <div class="row g-0">
            <div class="col-6 col-md-3">
                <div class="pub-stat-item">
                    <div class="pub-stat-number">
                        <span data-counter data-target="<?= $stats['members'] ?>">0</span>
                    </div>
                    <div class="pub-stat-label"><i class="fa-solid fa-users me-1 text-gold"></i>Anggota Aktif</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="pub-stat-item">
                    <div class="pub-stat-number">
                        <span data-counter data-target="<?= $stats['news'] ?>">0</span>
                    </div>
                    <div class="pub-stat-label"><i class="fa-solid fa-newspaper me-1 text-gold"></i>Berita Terbit</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="pub-stat-item">
                    <div class="pub-stat-number">
                        <span data-counter data-target="<?= $stats['events'] ?>">0</span>
                    </div>
                    <div class="pub-stat-label"><i class="fa-solid fa-calendar-check me-1 text-gold"></i>Event Diselenggarakan</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="pub-stat-item">
                    <div class="pub-stat-number">
                        <span data-counter data-target="<?= $stats['products'] ?>">0</span>
                    </div>
                    <div class="pub-stat-label"><i class="fa-solid fa-store me-1 text-gold"></i>Produk di Katalog</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =================== BERITA TERBARU =================== -->
<section class="pub-section section-bg-white" id="berita">
    <div class="container">
        <div class="pub-section-header d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <span class="pub-section-eyebrow">Informasi Terkini</span>
                <h2 class="pub-section-title">Berita &amp; Artikel</h2>
                <div class="pub-divider"></div>
                <p class="pub-section-desc">Informasi terbaru seputar kegiatan, program kerja, dan perkembangan komunitas PANMAS.</p>
            </div>
            <a href="<?= base_url('berita') ?>" class="view-all-link mb-1">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if (!empty($latestNews)): ?>
            <div class="row g-4">
                <?php
                // First item: featured (wide)
                $featured = $latestNews[0];
                $restNews = array_slice($latestNews, 1);
                ?>

                <!-- Featured card -->
                <div class="col-12 col-lg-6">
                    <div class="content-card-featured h-100">
                        <div class="content-card-img">
                            <?php if (!empty($featured['foto_cover'])): ?>
                                <img src="<?= base_url('uploads/' . $featured['foto_cover']) ?>" alt="<?= esc($featured['judul']) ?>">
                            <?php else: ?>
                                <div class="content-card-img-placeholder h-100">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                            <?php endif; ?>
                            <span class="content-card-badge">Terbaru</span>
                        </div>
                        <div class="content-card-body">
                            <div class="content-card-category">
                                <i class="fa-solid fa-tag me-1"></i>
                                <?= esc($featured['tipe'] ?? 'Berita') ?>
                            </div>
                            <div class="content-card-title">
                                <a href="<?= base_url('berita/' . $featured['slug']) ?>">
                                    <?= esc($featured['judul']) ?>
                                </a>
                            </div>
                            <div class="content-card-excerpt">
                                <?= esc(strip_tags(substr($featured['konten'] ?? '', 0, 200))) ?>...
                            </div>
                            <div class="content-card-footer">
                                <div class="content-card-meta">
                                    <span>
                                        <i class="fa-regular fa-calendar"></i>
                                        <?= date('d M Y', strtotime($featured['published_at'] ?? $featured['created_at'])) ?>
                                    </span>
                                    <?php if (!empty($featured['views_count'])): ?>
                                    <span>
                                        <i class="fa-regular fa-eye"></i>
                                        <?= number_format($featured['views_count']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <a href="<?= base_url('berita/' . $featured['slug']) ?>" class="content-card-link">
                                    Baca <i class="fa-solid fa-arrow-right fa-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rest of news (smaller cards) -->
                <div class="col-12 col-lg-6">
                    <div class="row g-3 h-100">
                        <?php foreach (array_slice($restNews, 0, 4) as $article): ?>
                        <div class="col-12 col-sm-6">
                            <div class="content-card">
                                <div class="content-card-img">
                                    <?php if (!empty($article['foto_cover'])): ?>
                                        <img src="<?= base_url('uploads/' . $article['foto_cover']) ?>" alt="<?= esc($article['judul']) ?>">
                                    <?php else: ?>
                                        <div class="content-card-img-placeholder">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="content-card-body">
                                    <div class="content-card-category"><?= esc($article['tipe'] ?? 'Berita') ?></div>
                                    <div class="content-card-title">
                                        <a href="<?= base_url('berita/' . $article['slug']) ?>">
                                            <?= esc($article['judul']) ?>
                                        </a>
                                    </div>
                                    <div class="content-card-footer">
                                        <span class="content-card-meta">
                                            <span>
                                                <i class="fa-regular fa-calendar"></i>
                                                <?= date('d M', strtotime($article['published_at'] ?? $article['created_at'])) ?>
                                            </span>
                                        </span>
                                        <a href="<?= base_url('berita/' . $article['slug']) ?>" class="content-card-link">
                                            Baca <i class="fa-solid fa-arrow-right fa-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>

                        <?php if (empty($restNews)): ?>
                        <div class="col-12">
                            <div class="empty-state" style="padding:30px 20px;">
                                <p class="text-muted-warm mb-0">Belum ada berita lainnya.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- Empty state -->
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-newspaper"></i></div>
                <div class="empty-state-title">Belum Ada Berita</div>
                <p class="empty-state-desc">Belum ada berita yang dipublikasikan saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =================== UPCOMING EVENTS =================== -->
<section class="pub-section section-bg-cream" id="agenda">
    <div class="container">
        <div class="pub-section-header d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <span class="pub-section-eyebrow">Jadwal Kegiatan</span>
                <h2 class="pub-section-title">Agenda Event</h2>
                <div class="pub-divider"></div>
                <p class="pub-section-desc">Event dan kegiatan mendatang yang diselenggarakan oleh PANMAS dan komunitas anggota.</p>
            </div>
            <a href="<?= base_url('event') ?>" class="view-all-link mb-1">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if (!empty($upcomingEvents)): ?>
            <div class="row g-4">
                <?php foreach ($upcomingEvents as $evt): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="event-card">
                        <div class="event-card-banner">
                            <?php if (!empty($evt['poster_url'])): ?>
                                <img src="<?= base_url('uploads/' . $evt['poster_url']) ?>" alt="<?= esc($evt['judul']) ?>">
                            <?php else: ?>
                                <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--coffee-700),var(--coffee-600));display:flex;align-items:center;justify-content:center;font-size:3rem;color:rgba(255,255,255,0.3);">
                                    <i class="fa-solid fa-calendar-star"></i>
                                </div>
                            <?php endif; ?>
                            <span class="event-card-status upcoming">Akan Datang</span>
                            <div class="event-card-date-badge">
                                <div class="day"><?= date('d', strtotime($evt['tanggal_mulai'])) ?></div>
                                <div class="month"><?= date('M', strtotime($evt['tanggal_mulai'])) ?></div>
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
                                    <i class="fa-regular fa-clock"></i>
                                    <?= date('H:i', strtotime($evt['tanggal_mulai'])) ?> WIB
                                    <?php if (!empty($evt['tanggal_selesai'])): ?>
                                    — <?= date('H:i', strtotime($evt['tanggal_selesai'])) ?> WIB
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
                                <?php if (auth()->loggedIn()): ?>
                                <a href="<?= base_url('event/' . ($evt['slug'] ?? $evt['id'])) ?>"
                                   class="btn-coffee btn-sm-coffee btn-full" id="event-daftar-<?= $evt['id'] ?>">
                                    <i class="fa-solid fa-ticket"></i>Daftar Sekarang
                                </a>
                                <?php else: ?>
                                <a href="<?= base_url('login') ?>"
                                   class="btn-coffee-outline btn-full" style="justify-content:center; font-size:0.85rem; padding:8px;">
                                    <i class="fa-solid fa-right-to-bracket"></i>Login untuk Daftar
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-calendar-xmark"></i></div>
                <div class="empty-state-title">Belum Ada Event Mendatang</div>
                <p class="empty-state-desc">Pantau terus halaman agenda untuk informasi event terbaru.</p>
                <a href="<?= base_url('event') ?>" class="btn-coffee-outline">
                    <i class="fa-solid fa-calendar-days"></i>Lihat Semua Event
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =================== KATALOG PRODUK =================== -->
<section class="pub-section section-bg-white" id="produk">
    <div class="container">
        <div class="pub-section-header d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <span class="pub-section-eyebrow">UMKM Anggota</span>
                <h2 class="pub-section-title">Katalog Produk</h2>
                <div class="pub-divider"></div>
                <p class="pub-section-desc">Dukung produk dan usaha sesama anggota PANMAS. Beli, rekomendasikan, dan perkuat ekonomi komunitas.</p>
            </div>
            <a href="<?= base_url('katalog') ?>" class="view-all-link mb-1">
                Lihat Semua <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <?php if (!empty($featuredProducts)): ?>
            <div class="row g-4">
                <?php foreach ($featuredProducts as $prod): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="product-card">
                        <div class="product-card-img">
                            <?php
                            $fotos = json_decode($prod['foto_urls'] ?? '[]', true);
                            $thumb = !empty($fotos[0]) ? $fotos[0] : null;
                            ?>
                            <?php if ($thumb): ?>
                                <img src="<?= base_url('uploads/' . $thumb) ?>" alt="<?= esc($prod['nama_produk']) ?>">
                            <?php else: ?>
                                <div class="product-card-img-placeholder">
                                    <i class="fa-solid fa-box-open"></i>
                                    <span>Foto belum tersedia</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-card-body">
                            <div class="product-card-name"><?= esc($prod['nama_produk']) ?></div>
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
                               class="btn-coffee btn-sm-coffee btn-full" id="prod-detail-<?= $prod['id'] ?>">
                                <i class="fa-solid fa-eye"></i>Detail
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon"><i class="fa-solid fa-store"></i></div>
                <div class="empty-state-title">Belum Ada Produk</div>
                <p class="empty-state-desc">Belum ada produk yang dipublikasikan. Anggota dapat menambahkan produk melalui panel member.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =================== CTA JOIN =================== -->
<?php if (!auth()->loggedIn()): ?>
<section class="pub-section" style="background: linear-gradient(135deg, var(--coffee-900) 0%, var(--coffee-800) 100%); padding:80px 0;">
    <div class="container text-center">
        <span class="pub-hero-eyebrow d-inline-flex mb-4">
            <i class="fa-solid fa-star"></i>Bergabunglah Bersama Kami
        </span>
        <h2 style="font-family:var(--font-serif); font-size:clamp(1.8rem,4vw,2.8rem); color:#fff; margin-bottom:16px;">
            Jadilah Bagian dari <em style="color:var(--gold-400);">Ngobrol Pikiran Indonesia</em>
        </h2>
        <p style="color:var(--coffee-200); font-size:1rem; max-width:520px; margin:0 auto 36px; line-height:1.75;">
            Daftarkan diri Anda dan dapatkan akses ke seluruh fitur — kelola profil, 
            tambahkan produk UMKM, daftar event, dan terhubung dengan ribuan anggota komunitas.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= base_url('register') ?>" class="btn-gold" id="cta-daftar">
                <i class="fa-solid fa-user-plus"></i>Daftar Sekarang — Gratis
            </a>
            <a href="<?= base_url('login') ?>" class="btn-nav-login" id="cta-login">
                <i class="fa-solid fa-right-to-bracket"></i>Sudah punya akun? Masuk
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<?= $this->endSection() ?>
