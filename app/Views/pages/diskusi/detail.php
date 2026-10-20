<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-comments"></i>
                Thread Diskusi
            </div>
            <h1 class="page-hero-title" style="max-width:750px;">
                <?= esc($discussion['judul']) ?>
            </h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <a href="<?= base_url('diskusi') ?>">Diskusi</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span><?= esc(mb_strimwidth($discussion['judul'], 0, 40, '...')) ?></span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row g-5">

            <!-- Main Thread -->
            <div class="col-lg-8">

                <!-- Thread Header Box -->
                <div class="card p-4 shadow-sm border-0 mb-4" style="background:var(--warm-white); border-radius:var(--radius-md);">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="article-author-avatar" style="width:48px; height:48px; font-size:1.1rem;">
                            <?= mb_strtoupper(mb_substr($author['nama_lengkap'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div>
                            <div class="fw-bold text-coffee" style="font-size:0.95rem;">
                                <?= esc($author['nama_lengkap'] ?? 'Anggota Komunitas') ?>
                            </div>
                            <div class="text-muted-warm" style="font-size:0.78rem;">
                                Dibuat pada <?= date('d F Y, H:i', strtotime($discussion['created_at'])) ?> WIB
                            </div>
                        </div>
                    </div>

                    <div class="article-body" style="font-size:1rem;">
                        <?= nl2br(esc($discussion['konten'])) ?>
                    </div>

                    <div class="d-flex align-items-center justify-content-between pt-3 mt-3 border-top" style="font-size:0.85rem; color:var(--warm-gray);">
                        <div class="d-flex align-items-center gap-3">
                            <span><i class="fa-solid fa-circle-up text-gold me-1"></i><?= number_format($discussion['upvotes_count'] ?? 0) ?> Dukungan</span>
                            <span><i class="fa-regular fa-comments me-1"></i><?= number_format(count($replies)) ?> Balasan</span>
                        </div>
                        <a href="#form-reply" class="btn-coffee btn-sm-coffee">
                            <i class="fa-solid fa-reply me-1"></i>Balas Thread
                        </a>
                    </div>
                </div>

                <!-- Replies Section -->
                <h4 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:20px; font-size:1.25rem;">
                    <i class="fa-solid fa-comments me-2 text-gold"></i><?= count($replies) ?> Balasan &amp; Tanggapan
                </h4>

                <?php if (!empty($replies)): ?>
                    <div class="d-flex flex-column gap-3 mb-5">
                        <?php foreach ($replies as $reply): ?>
                        <div class="card p-3 border-0 shadow-sm" style="border-radius:var(--radius-md); background: <?= !empty($reply['is_best_answer']) ? '#fef9e7' : '#fff' ?>; border-left: <?= !empty($reply['is_best_answer']) ? '4px solid var(--gold-500)' : '1px solid var(--border-warm)' ?> !important;">
                            
                            <?php if (!empty($reply['is_best_answer'])): ?>
                            <div class="mb-2">
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-award me-1"></i>Solusi Terbaik</span>
                            </div>
                            <?php endif; ?>

                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="member-avatar" style="width:36px; height:36px; font-size:0.9rem; margin:0;">
                                    <?= mb_strtoupper(mb_substr($reply['nama_lengkap'] ?? 'A', 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-coffee" style="font-size:0.88rem;">
                                        <?= esc($reply['nama_lengkap'] ?? 'Anggota') ?>
                                    </div>
                                    <div style="font-size:0.72rem; color:var(--warm-gray);">
                                        <?= date('d M Y, H:i', strtotime($reply['created_at'])) ?> WIB
                                    </div>
                                </div>
                            </div>
                            <div style="font-size:0.92rem; color:var(--coffee-800); line-height:1.6;">
                                <?= nl2br(esc($reply['konten'])) ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state py-4 mb-4" style="background:var(--coffee-50); border-radius:var(--radius-md);">
                        <p class="text-muted-warm mb-0">Belum ada balasan. Jadilah yang pertama memberikan tanggapan!</p>
                    </div>
                <?php endif; ?>

                <!-- Reply Form Box -->
                <div id="form-reply" class="card p-4 border-0 shadow-sm" style="background:var(--warm-white); border-radius:var(--radius-md);">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:16px;">
                        <i class="fa-solid fa-reply text-gold me-2"></i>Tulis Balasan
                    </h5>

                    <?php if (auth()->loggedIn()): ?>
                        <form action="<?= base_url('diskusi/reply/' . $discussion['id']) ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <textarea name="konten" class="form-control" rows="4" placeholder="Tuliskan tanggapan atau solusi Anda secara sopan dan konstruktif..." required></textarea>
                            </div>
                            <button type="submit" class="btn-gold">
                                <i class="fa-solid fa-paper-plane me-1"></i>Kirim Balasan
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <p style="color:var(--warm-gray); font-size:0.9rem; margin-bottom:14px;">
                                Anda harus login terlebih dahulu untuk dapat membalas diskusi.
                            </p>
                            <a href="<?= base_url('login') ?>" class="btn-gold">
                                <i class="fa-solid fa-right-to-bracket me-1"></i>Login Sekarang
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Back -->
                <div class="mt-4">
                    <a href="<?= base_url('diskusi') ?>" class="btn-coffee-outline">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Daftar Diskusi
                    </a>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-header">
                            <i class="fa-solid fa-comments"></i>
                            <h5>Diskusi Lainnya</h5>
                        </div>
                        <div class="sidebar-widget-body" style="padding:8px 18px;">
                            <?php if (!empty($recentDiscussions)): ?>
                                <?php foreach ($recentDiscussions as $rd): ?>
                                <div class="sidebar-news-item">
                                    <div>
                                        <div class="sidebar-news-title">
                                            <a href="<?= base_url('diskusi/' . $rd['slug']) ?>">
                                                <?= esc($rd['judul']) ?>
                                            </a>
                                        </div>
                                        <div class="sidebar-news-date">
                                            <i class="fa-regular fa-comments"></i>
                                            <?= number_format($rd['replies_count'] ?? 0) ?> Balasan
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted-warm small mb-0 py-2">Belum ada diskusi lain.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
