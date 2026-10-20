<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-users"></i>
                Direktori Keanggotaan
            </div>
            <h1 class="page-hero-title">Direktori Anggota</h1>
            <p class="page-hero-desc">
                Kenali sesama anggota PANMAS — temukan profil, wilayah UPA, dan bidang kontribusi mereka.
            </p>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <span>Direktori Anggota</span>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="pub-section section-bg-white" style="padding-top:50px;">
    <div class="container">

        <!-- Search Bar -->
        <div class="filter-bar mb-4">
            <span class="filter-bar-label"><i class="fa-solid fa-magnifying-glass me-1"></i>Cari Anggota:</span>
            <div class="pub-search-wrap" style="flex:1;">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="memberSearch" placeholder="Nama anggota, wilayah UPA..."
                       value="<?= esc(request()->getGet('q') ?? '') ?>">
            </div>
            <span style="font-size:0.8rem; color:var(--warm-gray);">
                <?= number_format(count($members)) ?> anggota ditemukan
            </span>
        </div>

        <!-- Members Grid -->
        <?php if (!empty($members)): ?>
            <div class="row g-4" id="membersGrid">
                <?php foreach ($members as $m): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="member-card">
                        <div class="member-avatar">
                            <?php if (!empty($m['foto_profil'])): ?>
                                <img src="<?= base_url('uploads/' . $m['foto_profil']) ?>"
                                     alt="<?= esc($m['nama_lengkap']) ?>">
                            <?php else: ?>
                                <?= mb_strtoupper(mb_substr($m['nama_lengkap'] ?? 'A', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="member-name"><?= esc($m['nama_lengkap']) ?></div>
                        <?php if (!empty($m['kode_upa'])): ?>
                        <div class="member-upa">
                            <i class="fa-solid fa-location-dot text-gold"></i>
                            UPA <?= esc($m['kode_upa']) ?>
                        </div>
                        <?php endif; ?>
                        <?php
                        $statusLabel = [
                            'Utama'          => 'Anggota Utama',
                            'Dewasa'         => 'Anggota Dewasa',
                            'Madya'          => 'Anggota Madya',
                            'Muayyid'        => 'Anggota Muayyid',
                            'Muhib'          => 'Anggota Muhib',
                            'Belum Tarbiyah' => 'Belum Tarbiyah',
                        ][$m['status_anggota'] ?? ''] ?? ($m['status_anggota'] ?? 'Anggota');
                        ?>
                        <div class="reputation-badge">
                            <i class="fa-solid fa-id-badge"></i>
                            <?= esc($statusLabel) ?>
                        </div>
                        <div class="mt-3">
                            <a href="<?= base_url('anggota/' . $m['id']) ?>"
                               class="btn-coffee-outline" style="width:100%; justify-content:center; padding:7px 12px; font-size:0.8rem;"
                               id="member-detail-<?= $m['id'] ?>">
                                <i class="fa-solid fa-eye"></i>Lihat Profil
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
                <div class="empty-state-icon"><i class="fa-solid fa-users"></i></div>
                <div class="empty-state-title">Belum Ada Anggota</div>
                <p class="empty-state-desc">Belum ada data anggota aktif yang dapat ditampilkan.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->section('js') ?>
<script>
const memberSearch = document.getElementById('memberSearch');
if (memberSearch) {
    memberSearch.addEventListener('input', debounce(function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#membersGrid .col-6').forEach(card => {
            const name = card.querySelector('.member-name')?.textContent?.toLowerCase() || '';
            const upa  = card.querySelector('.member-upa')?.textContent?.toLowerCase() || '';
            card.style.display = (name.includes(q) || upa.includes(q)) ? '' : 'none';
        });
    }, 200));
}

function debounce(fn, delay) {
    let t;
    return function(...args) { clearTimeout(t); t = setTimeout(() => fn.apply(this, args), delay); };
}
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
