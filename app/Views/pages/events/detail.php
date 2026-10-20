<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$status      = $event['status'] ?? 'upcoming';
$statusLabel = ['upcoming'=>'Akan Datang','ongoing'=>'Berlangsung','selesai'=>'Selesai'][$status] ?? $status;
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-calendar-star"></i>
                <?= esc($statusLabel) ?>
            </div>
            <h1 class="page-hero-title" style="max-width:650px;">
                <?= esc($event['judul']) ?>
            </h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <a href="<?= base_url('event') ?>">Agenda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span><?= esc(mb_strimwidth($event['judul'], 0, 40, '...')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Event Detail -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row g-5">

            <!-- Main -->
            <div class="col-lg-8">

                <!-- Poster -->
                <?php if (!empty($event['poster_url'])): ?>
                <div class="mb-4 img-zoom-wrap"
                     style="border-radius:var(--radius-md); overflow:hidden; box-shadow:var(--shadow-md);">
                    <img src="<?= base_url('uploads/' . $event['poster_url']) ?>"
                         alt="<?= esc($event['judul']) ?>"
                         style="width:100%; height:auto; display:block;">
                </div>
                <?php endif; ?>

                <!-- Info Cards Row -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; text-align:center;">
                            <i class="fa-solid fa-calendar fa-lg text-gold mb-2"></i>
                            <div style="font-size:0.72rem; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px; font-weight:700;">Tanggal</div>
                            <div style="font-weight:700; color:var(--coffee-900); font-size:0.9rem; margin-top:4px;">
                                <?= date('d M Y', strtotime($event['tanggal_mulai'])) ?>
                                <?php if (!empty($event['tanggal_selesai']) && $event['tanggal_selesai'] != $event['tanggal_mulai']): ?>
                                <br><small style="color:var(--warm-gray);">s/d <?= date('d M Y', strtotime($event['tanggal_selesai'])) ?></small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; text-align:center;">
                            <i class="fa-regular fa-clock fa-lg text-gold mb-2"></i>
                            <div style="font-size:0.72rem; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px; font-weight:700;">Waktu</div>
                            <div style="font-weight:700; color:var(--coffee-900); font-size:0.9rem; margin-top:4px;">
                                <?= date('H:i', strtotime($event['tanggal_mulai'])) ?> WIB
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($event['lokasi'])): ?>
                    <div class="col-sm-6 col-md-3">
                        <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; text-align:center;">
                            <i class="fa-solid fa-location-dot fa-lg text-gold mb-2"></i>
                            <div style="font-size:0.72rem; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px; font-weight:700;">Lokasi</div>
                            <div style="font-weight:700; color:var(--coffee-900); font-size:0.9rem; margin-top:4px; line-height:1.3;">
                                <?= esc($event['lokasi']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($event['kuota'])): ?>
                    <div class="col-sm-6 col-md-3">
                        <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; text-align:center;">
                            <i class="fa-solid fa-users fa-lg text-gold mb-2"></i>
                            <div style="font-size:0.72rem; color:var(--warm-gray); text-transform:uppercase; letter-spacing:1px; font-weight:700;">Kuota</div>
                            <div style="font-weight:700; color:var(--coffee-900); font-size:0.9rem; margin-top:4px;">
                                <?= number_format($event['kuota']) ?> peserta
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h4 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:14px; font-size:1.3rem;">
                        <i class="fa-solid fa-circle-info me-2 text-gold"></i>Tentang Event
                    </h4>
                    <div class="article-body">
                        <?= nl2br(esc($event['deskripsi'])) ?>
                    </div>
                </div>

                <!-- Registration CTA -->
                <?php if ($status === 'upcoming'): ?>
                <div style="background:linear-gradient(135deg,var(--coffee-900),var(--coffee-800)); border-radius:var(--radius-md); padding:28px; text-align:center; margin-top:30px;">
                    <h5 style="color:#fff; font-family:var(--font-serif); margin-bottom:8px;">Daftarkan Diri Anda</h5>
                    <p style="color:var(--coffee-200); font-size:0.9rem; margin-bottom:20px;">
                        Jangan lewatkan kesempatan ini. Segera daftarkan diri untuk mengikuti event ini.
                    </p>
                    <?php if (auth()->loggedIn()): ?>
                    <a href="#" class="btn-gold" id="btn-daftar-event-<?= $event['id'] ?>">
                        <i class="fa-solid fa-ticket"></i>Daftar Sekarang
                    </a>
                    <?php else: ?>
                    <a href="<?= base_url('login') ?>" class="btn-gold">
                        <i class="fa-solid fa-right-to-bracket"></i>Login untuk Mendaftar
                    </a>
                    <p style="color:var(--coffee-400); font-size:0.78rem; margin-top:12px; margin-bottom:0;">
                        Belum punya akun? <a href="<?= base_url('register') ?>" style="color:var(--gold-400);">Daftar gratis</a>
                    </p>
                    <?php endif; ?>
                </div>
                <?php elseif ($status === 'selesai'): ?>
                <div style="background:var(--coffee-100); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:20px; text-align:center; margin-top:20px;">
                    <i class="fa-solid fa-circle-check fa-2x" style="color:var(--success); margin-bottom:10px;"></i>
                    <p style="color:var(--warm-gray); margin:0;">Event ini telah selesai diselenggarakan. Terima kasih kepada seluruh peserta!</p>
                </div>
                <?php endif; ?>

                <!-- Back link -->
                <div class="mt-4">
                    <a href="<?= base_url('event') ?>" class="btn-coffee-outline">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Agenda
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">

                    <!-- Event Info Card -->
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-circle-info"></i>
                            <h5>Info Event</h5>
                        </div>
                        <div class="sidebar-widget-body">
                            <ul class="list-unstyled footer-contact mb-0">
                                <li>
                                    <i class="fa-solid fa-calendar text-gold"></i>
                                    <span><?= date('d F Y', strtotime($event['tanggal_mulai'])) ?></span>
                                </li>
                                <li>
                                    <i class="fa-regular fa-clock text-gold"></i>
                                    <span><?= date('H:i', strtotime($event['tanggal_mulai'])) ?> WIB</span>
                                </li>
                                <?php if (!empty($event['lokasi'])): ?>
                                <li>
                                    <i class="fa-solid fa-location-dot text-gold"></i>
                                    <span><?= esc($event['lokasi']) ?></span>
                                </li>
                                <?php endif; ?>
                                <?php if (!empty($event['kuota'])): ?>
                                <li>
                                    <i class="fa-solid fa-users text-gold"></i>
                                    <span>Kuota: <?= number_format($event['kuota']) ?> peserta</span>
                                </li>
                                <?php endif; ?>
                                <li>
                                    <i class="fa-solid fa-circle text-gold" style="font-size:0.5rem; margin-top:6px;"></i>
                                    <span>
                                        <span class="badge-status <?= $status ?>">
                                            <?= $statusLabel ?>
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Event Lainnya -->
                    <?php if (!empty($upcomingEvents)): ?>
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-calendar-days"></i>
                            <h5>Event Lainnya</h5>
                        </div>
                        <div class="sidebar-widget-body" style="padding:8px 18px;">
                            <?php foreach ($upcomingEvents as $ue): ?>
                            <div class="sidebar-news-item">
                                <div style="flex-shrink:0; width:46px; height:46px; background:var(--coffee-900); border-radius:8px; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#fff;">
                                    <div style="font-family:var(--font-serif); font-size:1rem; font-weight:800; color:var(--gold-400); line-height:1;">
                                        <?= date('d', strtotime($ue['tanggal_mulai'])) ?>
                                    </div>
                                    <div style="font-size:0.55rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--coffee-300);">
                                        <?= date('M', strtotime($ue['tanggal_mulai'])) ?>
                                    </div>
                                </div>
                                <div>
                                    <div class="sidebar-news-title">
                                        <a href="<?= base_url('event/' . ($ue['slug'] ?? $ue['id'])) ?>">
                                            <?= esc($ue['judul']) ?>
                                        </a>
                                    </div>
                                    <div class="sidebar-news-date">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <?= esc($ue['lokasi'] ?? 'Lokasi TBA') ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
