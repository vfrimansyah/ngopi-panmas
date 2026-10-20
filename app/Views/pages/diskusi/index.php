<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-comments"></i>
                Forum Komunitas
            </div>
            <h1 class="page-hero-title">Forum Diskusi</h1>
            <p class="page-hero-desc">
                Ruang diskusi publik — sampaikan pandangan, ajukan pertanyaan, dan temukan solusi bersama anggota Ngobrol Pikiran Indonesia.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Forum Diskusi</span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="pub-section section-bg-white" style="padding-top:50px;">
    <div class="container">

        <!-- Header Actions -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <!-- Filter & Sort Bar -->
            <div class="filter-bar mb-0" style="flex:1;">
                <span class="filter-bar-label"><i class="fa-solid fa-filter me-1"></i>Kategori:</span>
                <div class="filter-pills">
                    <a href="<?= base_url('diskusi') ?>" class="filter-pill <?= empty($selectedCategory) ? 'active' : '' ?>">Semua</a>
                    <?php foreach ($categories as $cat): ?>
                    <a href="<?= base_url('diskusi?kategori=' . urlencode($cat['nama_kategori'])) ?>"
                       class="filter-pill <?= $selectedCategory == $cat['nama_kategori'] ? 'active' : '' ?>">
                        <?= esc($cat['nama_kategori']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <a href="<?= base_url('diskusi?sort=terbaru' . ($selectedCategory ? '&kategori=' . urlencode($selectedCategory) : '')) ?>"
                       class="filter-pill <?= $selectedSort == 'terbaru' ? 'active' : '' ?>">Terbaru</a>
                    <a href="<?= base_url('diskusi?sort=terpopuler' . ($selectedCategory ? '&kategori=' . urlencode($selectedCategory) : '')) ?>"
                       class="filter-pill <?= $selectedSort == 'terpopuler' ? 'active' : '' ?>">Terpopuler</a>
                </div>
            </div>

            <!-- Start Discussion Button -->
            <a href="<?= base_url('diskusi/create') ?>" class="btn-gold" id="btn-mulai-diskusi">
                <i class="fa-solid fa-comment-dots me-1"></i>Mulai Diskusi Baru
            </a>
        </div>

        <!-- Discussions List -->
        <?php if (!empty($discussions)): ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($discussions as $disc): ?>
                <div class="card p-3 shadow-sm border-0" style="border-radius:var(--radius-md); transition:var(--transition); background:var(--warm-white);">
                    <div class="d-flex align-items-start gap-3">
                        
                        <!-- Vote Widget Box -->
                        <div class="text-center p-2 rounded" style="background:var(--coffee-50); border:1px solid var(--border-warm); min-width:64px;">
                            <button class="btn btn-sm p-0 text-coffee btn-vote" data-id="<?= $disc['id'] ?>" style="font-size:1.1rem;">
                                <i class="fa-solid fa-circle-chevron-up"></i>
                            </button>
                            <div class="fw-bold fs-6 text-dark-coffee vote-count-<?= $disc['id'] ?>">
                                <?= number_format($disc['upvotes_count'] ?? 0) ?>
                            </div>
                            <div style="font-size:0.65rem; color:var(--warm-gray); text-transform:uppercase; font-weight:700;">Dukungan</div>
                        </div>

                        <!-- Main Info -->
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <?php if (!empty($disc['is_pinned'])): ?>
                                <span class="badge bg-danger" style="font-size:0.7rem;"><i class="fa-solid fa-thumbtack me-1"></i>Sematkan</span>
                                <?php endif; ?>
                                <span class="badge" style="background:var(--gold-100); color:var(--coffee-700); font-size:0.72rem;">
                                    Diskusi Publik
                                </span>
                            </div>
                            <h5 style="font-family:var(--font-serif); margin-bottom:8px; font-size:1.1rem; font-weight:700;">
                                <a href="<?= base_url('diskusi/' . $disc['slug']) ?>" style="color:var(--coffee-900);">
                                    <?= esc($disc['judul']) ?>
                                </a>
                            </h5>
                            <p style="font-size:0.875rem; color:var(--warm-gray); margin-bottom:12px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                <?= esc(strip_tags($disc['konten'])) ?>
                            </p>
                            <div class="d-flex align-items-center gap-3 text-muted-warm" style="font-size:0.8rem;">
                                <span><i class="fa-regular fa-calendar me-1"></i><?= date('d M Y', strtotime($disc['created_at'])) ?></span>
                                <span><i class="fa-regular fa-comments me-1"></i><?= number_format($disc['replies_count'] ?? 0) ?> Balasan</span>
                                <span><i class="fa-regular fa-eye me-1"></i><?= number_format($disc['views_count'] ?? 0) ?> Dilihat</span>
                            </div>
                        </div>

                        <a href="<?= base_url('diskusi/' . $disc['slug']) ?>" class="btn-coffee-outline btn-sm align-self-center d-none d-md-inline-flex">
                            Lihat <i class="fa-solid fa-arrow-right ms-1 fa-xs"></i>
                        </a>
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
                <div class="empty-state-icon"><i class="fa-solid fa-comments"></i></div>
                <div class="empty-state-title">Belum Ada Diskusi</div>
                <p class="empty-state-desc">Belum ada topik diskusi yang dibuat pada kategori ini.</p>
                <a href="<?= base_url('diskusi/create') ?>" class="btn-gold"><i class="fa-solid fa-comment-dots me-1"></i>Mulai Diskusi Pertama</a>
            </div>
        <?php endif; ?>

    </div>
</section>

<?= $this->section('js') ?>
<script>
document.querySelectorAll('.btn-vote').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.dataset.id;
        fetch('<?= base_url('diskusi/vote/') ?>' + id, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.querySelector('.vote-count-' + id).textContent = data.upvotes;
            } else if (data.message) {
                alert(data.message);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
