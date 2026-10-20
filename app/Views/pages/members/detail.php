<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="page-hero-content">
            <div class="page-hero-eyebrow">
                <i class="fa-solid fa-id-card"></i>
                <?= ($isOwnProfile ?? false) ? 'Profil Saya' : 'Profil Anggota' ?>
            </div>
            <h1 class="page-hero-title"><?= esc($member['nama_lengkap']) ?></h1>
            <div class="breadcrumb-pub">
                <a href="<?= base_url() ?>">Beranda</a>
                <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                <?php if ($isOwnProfile ?? false): ?>
                    <span>Profil Saya</span>
                <?php else: ?>
                    <a href="<?= base_url('anggota') ?>">Direktori Anggota</a>
                    <span class="sep"><i class="fa-solid fa-chevron-right fa-2xs"></i></span>
                    <span><?= esc($member['nama_lengkap']) ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section style="padding:50px 0 70px; background:#fff;">
    <div class="container">
        <div class="row g-5">

            <!-- Left: Profile Card -->
            <div class="col-lg-4">
                <div class="sidebar-widget text-center" style="padding:0; overflow:visible;">
                    <div style="background:linear-gradient(135deg,var(--coffee-900),var(--coffee-800)); padding:30px 20px; border-radius:var(--radius-md) var(--radius-md) 0 0; position:relative;">
                        <div class="member-avatar" style="width:96px; height:96px; font-size:2.5rem; margin:0 auto 14px; border:4px solid var(--gold-400);">
                            <?php if (!empty($member['foto_profil'])): ?>
                                <img src="<?= base_url('uploads/' . $member['foto_profil']) ?>"
                                     alt="<?= esc($member['nama_lengkap']) ?>">
                            <?php else: ?>
                                <?= mb_strtoupper(mb_substr($member['nama_lengkap'] ?? 'A', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <h4 style="font-family:var(--font-serif); color:#fff; margin-bottom:4px;">
                            <?= esc($member['nama_lengkap']) ?>
                        </h4>
                        <?php if (!empty($member['status_anggota'])): ?>
                        <span class="reputation-badge" style="background:rgba(200,160,64,0.2); border-color:rgba(200,160,64,0.3); color:var(--gold-300);">
                            <i class="fa-solid fa-star"></i>
                            <?= esc($member['status_anggota']) ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div style="padding:20px 18px;">
                        <ul class="list-unstyled footer-contact mb-0" style="text-align:left;">
                            <?php if (!empty($member['kode_upa'])): ?>
                            <li>
                                <i class="fa-solid fa-location-dot text-gold"></i>
                                <span>UPA <?= esc($member['kode_upa']) ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($member['kecamatan'])): ?>
                            <li>
                                <i class="fa-solid fa-map-marker-alt text-gold"></i>
                                <span><?= esc($member['kecamatan']) ?><?= !empty($member['kota_kab']) ? ', ' . esc($member['kota_kab']) : '' ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($member['pekerjaan'])): ?>
                            <li>
                                <i class="fa-solid fa-briefcase text-gold"></i>
                                <span><?= esc($member['pekerjaan']) ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($member['pendidikan_terakhir'])): ?>
                            <li>
                                <i class="fa-solid fa-graduation-cap text-gold"></i>
                                <span><?= esc($member['pendidikan_terakhir']) ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($member['status_perkawinan'])): ?>
                            <li>
                                <i class="fa-solid fa-heart text-gold"></i>
                                <span><?= esc($member['status_perkawinan']) ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($member['email'])): ?>
                            <li>
                                <i class="fa-solid fa-envelope text-gold"></i>
                                <span><?= esc($member['email']) ?></span>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($member['handphone'])): ?>
                            <li>
                                <i class="fa-solid fa-phone text-gold"></i>
                                <span><?= esc($member['handphone']) ?></span>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- Stat Card: Kegiatan Count -->
                <?php if (!empty($kegiatanHistory)): ?>
                <div class="sidebar-widget mt-3" style="padding:16px 18px;">
                    <div class="d-flex align-items-center gap-3">
                        <div style="width:48px; height:48px; background:linear-gradient(135deg,var(--coffee-900),var(--coffee-700)); border-radius:12px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-clipboard-check" style="color:var(--gold-400); font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <div style="font-size:1.5rem; font-weight:700; color:var(--coffee-900); line-height:1;">
                                <?php
                                    $hadirCount = count(array_filter($kegiatanHistory, fn($k) => ($k['status'] ?? '') === 'hadir'));
                                ?>
                                <?= $hadirCount ?>
                            </div>
                            <div style="font-size:0.8rem; color:var(--warm-gray);">Kegiatan Diikuti</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Back Button -->
                <div class="mt-3">
                    <?php if ($isOwnProfile ?? false): ?>
                    <a href="<?= base_url() ?>" class="btn-coffee-outline btn-full">
                        <i class="fa-solid fa-home"></i>Kembali ke Beranda
                    </a>
                    <?php else: ?>
                    <a href="<?= base_url('anggota') ?>" class="btn-coffee-outline btn-full">
                        <i class="fa-solid fa-arrow-left"></i>Kembali ke Direktori
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Details -->
            <div class="col-lg-8">

                <!-- Keahlian / Bidang -->
                <?php if (!empty($member['keahlian'])): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:12px;">
                        <i class="fa-solid fa-star me-2 text-gold"></i>Keahlian
                    </h5>
                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                        <?php foreach (explode(',', $member['keahlian']) as $skill): ?>
                        <span class="tag-chip"><?= esc(trim($skill)) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Skills (from member_skills table) -->
                <?php if (!empty($skills)): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:12px;">
                        <i class="fa-solid fa-tools me-2 text-gold"></i>Keterampilan
                    </h5>
                    <div style="display:flex; flex-wrap:wrap; gap:6px;">
                        <?php foreach ($skills as $sk): ?>
                        <span class="tag-chip"><?= esc($sk['nama_skill'] ?? $sk['skill_name'] ?? '-') ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- RIWAYAT KEGIATAN YANG DIIKUTI                          -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <?php if (!empty($kegiatanHistory)): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:16px;">
                        <i class="fa-solid fa-clipboard-check me-2 text-gold"></i>Riwayat Kegiatan
                        <span class="badge bg-secondary ms-2" style="font-size:0.7rem; vertical-align:middle;"><?= count($kegiatanHistory) ?></span>
                    </h5>
                    <div class="table-responsive" style="border:1px solid var(--border-warm); border-radius:var(--radius-md); overflow:hidden;">
                        <table class="table table-hover mb-0 no-datatable" style="font-size:0.88rem;">
                            <thead style="background:var(--coffee-50);">
                                <tr>
                                    <th class="ps-3" style="width:40px;">No</th>
                                    <th>Kegiatan</th>
                                    <th style="width:110px;">Tanggal</th>
                                    <th style="width:90px;" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($kegiatanHistory as $kg): ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-semibold" style="color:var(--coffee-900);"><?= esc($kg['nama_kegiatan'] ?? '-') ?></div>
                                        <?php if (!empty($kg['jenis_kegiatan_nama'])): ?>
                                        <div class="small text-muted"><?= esc($kg['jenis_kegiatan_nama']) ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($kg['lokasi_kegiatan'])): ?>
                                        <div class="small text-muted"><i class="fa-solid fa-location-dot me-1"></i><?= esc($kg['lokasi_kegiatan']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($kg['tanggal'])): ?>
                                        <div class="small fw-semibold"><?= date('d M Y', strtotime($kg['tanggal'])) ?></div>
                                        <div class="small text-muted"><?= esc($kg['jam_mulai'] ?? '') ?></div>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                            $statusBadge = match($kg['status'] ?? 'alfa') {
                                                'hadir' => '<span class="badge bg-success">Hadir</span>',
                                                'izin'  => '<span class="badge bg-warning text-dark">Izin</span>',
                                                'sakit' => '<span class="badge bg-primary">Sakit</span>',
                                                default => '<span class="badge bg-danger">Alfa</span>',
                                            };
                                        ?>
                                        <?= $statusBadge ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="mb-4" style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:28px; text-align:center;">
                    <i class="fa-solid fa-clipboard-check fa-2x mb-2" style="color:var(--coffee-200);"></i>
                    <p style="color:var(--warm-gray); margin:0; font-size:0.9rem;">
                        Belum ada riwayat kegiatan yang tercatat.
                    </p>
                </div>
                <?php endif; ?>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- RIWAYAT PENDIDIKAN                                      -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <?php if (!empty($educations)): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:16px;">
                        <i class="fa-solid fa-graduation-cap me-2 text-gold"></i>Riwayat Pendidikan
                    </h5>
                    <div class="row g-3">
                        <?php foreach ($educations as $edu): ?>
                        <div class="col-md-6">
                            <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; height:100%;">
                                <div class="fw-bold" style="color:var(--coffee-900); font-size:0.95rem;">
                                    <?= esc($edu['nama_institusi'] ?? '-') ?>
                                </div>
                                <?php if (!empty($edu['jenjang'])): ?>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-award me-1 text-gold"></i><?= esc($edu['jenjang']) ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($edu['rentang_tahun'])): ?>
                                <div class="small text-muted">
                                    <i class="fa-regular fa-calendar me-1"></i><?= esc($edu['rentang_tahun']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- RIWAYAT PEKERJAAN                                       -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <?php if (!empty($workExperiences)): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:16px;">
                        <i class="fa-solid fa-briefcase me-2 text-gold"></i>Riwayat Pekerjaan
                    </h5>
                    <div class="row g-3">
                        <?php foreach ($workExperiences as $work): ?>
                        <div class="col-md-6">
                            <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; height:100%;">
                                <div class="fw-bold" style="color:var(--coffee-900); font-size:0.95rem;">
                                    <?= esc($work['jabatan'] ?? '-') ?>
                                </div>
                                <?php if (!empty($work['nama_perusahaan'])): ?>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-building me-1 text-gold"></i><?= esc($work['nama_perusahaan']) ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($work['rentang_tahun'])): ?>
                                <div class="small text-muted">
                                    <i class="fa-regular fa-calendar me-1"></i><?= esc($work['rentang_tahun']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- ═══════════════════════════════════════════════════════ -->
                <!-- RIWAYAT ORGANISASI                                      -->
                <!-- ═══════════════════════════════════════════════════════ -->
                <?php if (!empty($organizations)): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:16px;">
                        <i class="fa-solid fa-people-group me-2 text-gold"></i>Riwayat Organisasi
                    </h5>
                    <div class="row g-3">
                        <?php foreach ($organizations as $org): ?>
                        <div class="col-md-6">
                            <div style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:16px; height:100%;">
                                <div class="fw-bold" style="color:var(--coffee-900); font-size:0.95rem;">
                                    <?= esc($org['nama_organisasi'] ?? '-') ?>
                                </div>
                                <?php if (!empty($org['jabatan'])): ?>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-user-tie me-1 text-gold"></i><?= esc($org['jabatan']) ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($org['rentang_tahun'])): ?>
                                <div class="small text-muted">
                                    <i class="fa-regular fa-calendar me-1"></i><?= esc($org['rentang_tahun']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Produk Anggota -->
                <?php if (!empty($products)): ?>
                <div class="mb-4">
                    <h5 style="font-family:var(--font-serif); color:var(--coffee-900); margin-bottom:16px;">
                        <i class="fa-solid fa-store me-2 text-gold"></i>Produk UMKM
                    </h5>
                    <div class="row g-3">
                        <?php foreach ($products as $prod): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="product-card">
                                <div class="product-card-img" style="aspect-ratio:4/3;">
                                    <?php
                                    $fotos = json_decode($prod['foto_urls'] ?? '[]', true);
                                    $thumb = !empty($fotos[0]) ? $fotos[0] : null;
                                    ?>
                                    <?php if ($thumb): ?>
                                        <img src="<?= base_url('uploads/' . $thumb) ?>"
                                             alt="<?= esc($prod['nama_produk']) ?>">
                                    <?php else: ?>
                                        <div class="product-card-img-placeholder">
                                            <i class="fa-solid fa-box-open"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-card-body">
                                    <div class="product-card-name"><?= esc($prod['nama_produk']) ?></div>
                                    <?php if (!empty($prod['harga']) && $prod['harga'] > 0): ?>
                                        <div class="product-card-price" style="font-size:0.9rem;">
                                            Rp <?= number_format($prod['harga'], 0, ',', '.') ?>
                                        </div>
                                    <?php endif; ?>
                                    <a href="<?= base_url('katalog/' . $prod['id']) ?>"
                                       class="btn-coffee btn-sm-coffee btn-full" id="member-prod-<?= $prod['id'] ?>">
                                        <i class="fa-solid fa-eye"></i>Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php else: ?>
                <div class="mb-4" style="background:var(--coffee-50); border:1px solid var(--border-warm); border-radius:var(--radius-md); padding:28px; text-align:center;">
                    <i class="fa-solid fa-store fa-2x mb-2" style="color:var(--coffee-200);"></i>
                    <p style="color:var(--warm-gray); margin:0; font-size:0.9rem;">
                        Anggota ini belum menambahkan produk ke katalog.
                    </p>
                </div>
                <?php endif; ?>

                <!-- Join CTA (for guest) -->
                <?php if (!auth()->loggedIn()): ?>
                <div style="background:linear-gradient(135deg,var(--coffee-900),var(--coffee-800)); border-radius:var(--radius-md); padding:24px; text-align:center;">
                    <h6 style="color:#fff; font-family:var(--font-serif); margin-bottom:8px;">Bergabung dengan PANMAS</h6>
                    <p style="color:var(--coffee-200); font-size:0.875rem; margin-bottom:16px;">
                        Daftarkan diri dan terhubung dengan sesama anggota komunitas.
                    </p>
                    <a href="<?= base_url('register') ?>" class="btn-gold me-2" id="member-cta-register">
                        <i class="fa-solid fa-user-plus"></i>Daftar Sekarang
                    </a>
                    <a href="<?= base_url('login') ?>" class="btn-nav-login" id="member-cta-login">
                        Sudah punya akun? Masuk
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
