<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- ===== NAV TABS / PILLS UTAMA ===== -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?= base_url('admin/members') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-users me-1"></i>Daftar Anggota
        </a>
        <a href="<?= base_url('admin/members/ekonomi') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-sack-dollar me-1 text-gold"></i>Data Ekonomi Anggota
        </a>
        <a href="<?= base_url('admin/members/pendidikan') ?>" class="btn btn-sm fw-semibold px-3 active shadow-sm" style="border-radius:20px; background:var(--dark-coffee); color:#fff; border:none;">
            <i class="fa-solid fa-graduation-cap me-1 text-gold"></i>Pendidikan Anggota
        </a>
        <a href="<?= base_url('admin/members/dashboard') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-chart-pie me-1"></i>Statistik & Dashboard
        </a>
        <a href="<?= base_url('admin/upa') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-people-group me-1 text-gold"></i>Group UPA
        </a>
    </div>

    <div class="d-flex align-items-center gap-2">
        <?php
            $exportParams = http_build_query($filter ?? []);
            $exportUrl = base_url('admin/members/pendidikan/export') . ($exportParams ? '?' . $exportParams : '');
        ?>
        <a href="<?= $exportUrl ?>" class="btn btn-sm btn-success fw-semibold px-3 shadow-sm">
            <i class="fa-solid fa-file-excel me-1"></i>Export Excel Pendidikan
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/members/create') ?>" class="btn btn-sm btn-gold fw-semibold px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i>Tambah Anggota
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- ===== PAGE HEADER BANNER ===== -->
<div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #efebe9 0%, #f5f0e8 100%); border-left: 5px solid #d4af37 !important; border-radius: 12px;">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h4 style="font-family:'Playfair Display',serif; color:#3e2723;" class="mb-1 fw-bold">
                    <i class="fa-solid fa-graduation-cap text-gold me-2"></i>Data Pendidikan & Institusi Anggota
                </h4>
                <p class="text-muted small mb-0">
                    Pemetaan kualifikasi akademik, jenjang pendidikan, nama sekolah / universitas, dan program studi anggota komunitas DPC PKS Pancoran Mas.
                </p>
            </div>
            <div class="d-flex align-items-center gap-3 bg-white px-3 py-2 rounded shadow-sm border flex-wrap">
                <div class="text-center border-end pe-3">
                    <div class="text-muted small">Total Anggota</div>
                    <div class="fw-bold fs-5 text-dark-coffee"><?= number_format($kpi['total'] ?? 0) ?></div>
                </div>
                <div class="text-center border-end pe-3">
                    <div class="text-muted small">Perguruan Tinggi (PT)</div>
                    <div class="fw-bold fs-5 text-primary"><?= number_format($kpi['pt_total'] ?? 0) ?></div>
                </div>
                <div class="text-center border-end pe-3">
                    <div class="text-muted small">Rasio Sarjana / PT</div>
                    <div class="fw-bold fs-5 text-success">
                        <?= ($kpi['total'] ?? 0) > 0 ? round((($kpi['pt_total'] ?? 0) / $kpi['total']) * 100, 1) : 0 ?>%
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-muted small">Nama Sekolah Terisi</div>
                    <div class="fw-bold fs-5 text-gold"><?= number_format($kpi['school_filled'] ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== INTERACTIVE KPI CARDS ===== -->
<?php
    $baseQs = function($key, $val) use ($filter) {
        $q = $filter ?? [];
        $q[$key] = $val;
        return base_url('admin/members/pendidikan?' . http_build_query($q));
    };
    $curJenjang = $filter['jenjang'] ?? '';
    $totalKpi = max(1, $kpi['total'] ?? 1);
?>
<div class="row g-3 mb-4">
    <!-- PT Total -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 'pt' ? '' : 'pt') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= $curJenjang === 'pt' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #0d6efd !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe;">Semua PT</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#eff6ff;">
                            <i class="fa-solid fa-building-columns text-primary" style="font-size:0.8rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['pt_total']) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small text-primary">S1, S2, S3 & Diploma</div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round(($kpi['pt_total'] / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>

    <!-- S3 (Doktor) -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 's3' ? '' : 's3') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= $curJenjang === 's3' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #4a148c !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#f3e8ff; color:#4a148c; border:1px solid #d8b4fe;">S3 (Doktor)</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f3e8ff;">
                            <i class="fa-solid fa-award" style="font-size:0.8rem; color:#4a148c;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['s3'] ?? 0) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small" style="color:#4a148c;">Doktor (S3)</div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round((($kpi['s3'] ?? 0) / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>

    <!-- S2 (Magister) -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 's2' ? '' : 's2') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= $curJenjang === 's2' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #6f42c1 !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff;">S2 (Magister)</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f3e8ff;">
                            <i class="fa-solid fa-graduation-cap" style="font-size:0.8rem; color:#6f42c1;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['s2']) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small" style="color:#6f42c1;">Magister (S2)</div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round(($kpi['s2'] / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Sarjana (S1 / D4) -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 's1_d4' ? '' : 's1_d4') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= $curJenjang === 's1_d4' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #0284c7 !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#e0f2fe; color:#0369a1; border:1px solid #bae6fd;">S1 / D4</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#e0f2fe;">
                            <i class="fa-solid fa-user-graduate text-info" style="font-size:0.8rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['s1_d4']) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small text-info">Sarjana (S1 / D4)</div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round(($kpi['s1_d4'] / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Diploma (D1 - D3) -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 'diploma' ? '' : 'diploma') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= $curJenjang === 'diploma' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #0d9488 !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#f0fdfa; color:#0f766e; border:1px solid #ccfbf1;">Diploma</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f0fdfa;">
                            <i class="fa-solid fa-certificate text-teal" style="font-size:0.8rem; color:#0d9488;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['diploma']) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small" style="color:#0d9488;">D1 – D3 (Ahli Madya)</div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round(($kpi['diploma'] / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>

    <!-- SMA / SMK -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 'sma_smk' ? '' : 'sma_smk') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= $curJenjang === 'sma_smk' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #16a34a !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0;">SMA / SMK</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f0fdf4;">
                            <i class="fa-solid fa-school text-success" style="font-size:0.8rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['sma_smk']) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small text-success">SMA, SMK & MA</div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round(($kpi['sma_smk'] / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>

    <!-- SMP & SD -->
    <div class="col-6 col-md-4 col-xl">
        <a href="<?= $baseQs('jenjang', $curJenjang === 'smp' ? '' : 'smp') ?>" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= in_array($curJenjang, ['smp', 'sd']) ? 'active-kpi' : '' ?>" style="border-top: 4px solid #f59e0b !important; border-radius: 10px;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge" style="background:#fffbeb; color:#b45309; border:1px solid #fde68a;">SMP & SD</span>
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#fffbeb;">
                            <i class="fa-solid fa-book-open text-warning" style="font-size:0.8rem;"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark"><?= number_format($kpi['smp'] + $kpi['sd']) ?> <span class="fs-6 fw-normal text-muted">Orang</span></div>
                    <div class="fw-semibold text-truncate small text-warning">SMP: <?= $kpi['smp'] ?> &middot; SD: <?= $kpi['sd'] ?></div>
                    <div class="text-muted" style="font-size:0.7rem;"><?= round((($kpi['smp'] + $kpi['sd']) / $totalKpi) * 100, 1) ?>% dari total</div>
                </div>
            </div>
        </a>
    </div>
</div>

<!-- ===== TOP KAMPUS / SEKOLAH POPULER CHIPS ===== -->
<?php if (!empty($topSchools)): ?>
<div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius:12px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-dark-coffee fw-bold small me-1">
                <i class="fa-solid fa-fire text-danger me-1"></i>Institusi Terbanyak:
            </span>
            <?php foreach ($topSchools as $schName => $schCount): ?>
                <?php $isSelected = (strtolower(trim($filter['sekolah'] ?? '')) === strtolower(trim($schName))); ?>
                <a href="<?= $baseQs('sekolah', $isSelected ? '' : $schName) ?>" 
                   class="badge text-decoration-none p-2 <?= $isSelected ? 'bg-dark-coffee text-gold border border-warning' : 'bg-light text-dark border hover-school-badge' ?>"
                   style="font-size:0.78rem; border-radius: 20px;">
                    <i class="fa-solid fa-building-columns me-1 <?= $isSelected ? 'text-gold' : 'text-secondary' ?>"></i>
                    <?= esc($schName) ?>
                    <span class="badge <?= $isSelected ? 'bg-gold text-dark' : 'bg-secondary' ?> ms-1 rounded-pill"><?= $schCount ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ===== FILTER CARD ===== -->
<div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius:12px;">
    <div class="card-body p-4">
        <form method="GET" action="<?= base_url('admin/members/pendidikan') ?>" id="formFilterPendidikan">
            <div class="row g-3 align-items-end">
                <!-- 1. Search Box -->
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted mb-1">Cari Anggota / Sekolah / Jurusan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" 
                               placeholder="Nama, sekolah, prodi..." 
                               value="<?= esc($filter['search'] ?? '') ?>">
                    </div>
                </div>

                <!-- 2. Jenjang Pendidikan -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Jenjang Pendidikan</label>
                    <select name="jenjang" class="form-select form-select-sm">
                        <option value="">-- Semua Jenjang --</option>
                        <option value="pt" <?= ($filter['jenjang'] ?? '') === 'pt' ? 'selected' : '' ?>>🎓 Perguruan Tinggi (Semua PT)</option>
                        <option value="s3" <?= ($filter['jenjang'] ?? '') === 's3' ? 'selected' : '' ?>>S3 (Doktor)</option>
                        <option value="s2" <?= ($filter['jenjang'] ?? '') === 's2' ? 'selected' : '' ?>>S2 (Magister)</option>
                        <option value="s1_d4" <?= ($filter['jenjang'] ?? '') === 's1_d4' ? 'selected' : '' ?>>S1 / D4 (Sarjana)</option>
                        <option value="diploma" <?= ($filter['jenjang'] ?? '') === 'diploma' ? 'selected' : '' ?>>D1 – D3 (Diploma)</option>
                        <option value="sma_smk" <?= ($filter['jenjang'] ?? '') === 'sma_smk' ? 'selected' : '' ?>>SMA / SMK / MA</option>
                        <option value="smp" <?= ($filter['jenjang'] ?? '') === 'smp' ? 'selected' : '' ?>>SMP / MTs</option>
                        <option value="sd" <?= ($filter['jenjang'] ?? '') === 'sd' ? 'selected' : '' ?>>SD / MI</option>
                        <option value="dini" <?= ($filter['jenjang'] ?? '') === 'dini' ? 'selected' : '' ?>>Pendidikan Dini (TK/Playgroup)</option>
                        <option value="unfilled" <?= ($filter['jenjang'] ?? '') === 'unfilled' ? 'selected' : '' ?>>Belum Ada Data</option>
                    </select>
                </div>

                <!-- 3. Gender -->
                <div class="col-md-1">
                    <label class="form-label small fw-bold text-muted mb-1">Gender</label>
                    <select name="gender" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="L" <?= ($filter['gender'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($filter['gender'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <!-- 4. Status Nama Sekolah -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Status Nama Sekolah</label>
                    <select name="has_school" class="form-select form-select-sm">
                        <option value="">-- Semua --</option>
                        <option value="1" <?= ($filter['has_school'] ?? '') === '1' ? 'selected' : '' ?>>✅ Sudah Ada Nama Sekolah</option>
                        <option value="0" <?= ($filter['has_school'] ?? '') === '0' ? 'selected' : '' ?>>⚠️ Belum Ada Nama Sekolah</option>
                    </select>
                </div>

                <!-- 4. Scope Status Anggota -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Kategori Anggota</label>
                    <select name="status_anggota" class="form-select form-select-sm">
                        <option value="pelopor" <?= ($filter['status_anggota'] ?? '') === 'pelopor' ? 'selected' : '' ?>>Pelopor (Madya/Dewasa/Utama)</option>
                        <option value="all" <?= ($filter['status_anggota'] ?? '') === 'all' ? 'selected' : '' ?>>Semua Jenjang</option>
                        <option value="Utama" <?= ($filter['status_anggota'] ?? '') === 'Utama' ? 'selected' : '' ?>>Utama</option>
                        <option value="Dewasa" <?= ($filter['status_anggota'] ?? '') === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                        <option value="Madya" <?= ($filter['status_anggota'] ?? '') === 'Madya' ? 'selected' : '' ?>>Madya</option>
                        <option value="Muayyid" <?= ($filter['status_anggota'] ?? '') === 'Muayyid' ? 'selected' : '' ?>>Muayyid</option>
                        <option value="Muhib" <?= ($filter['status_anggota'] ?? '') === 'Muhib' ? 'selected' : '' ?>>Muhib</option>
                        <option value="Belum Tarbiyah" <?= ($filter['status_anggota'] ?? '') === 'Belum Tarbiyah' ? 'selected' : '' ?>>Belum Tarbiyah</option>
                        <option value="non_pelopor" <?= ($filter['status_anggota'] ?? '') === 'non_pelopor' ? 'selected' : '' ?>>Non-Pelopor / Keluarga</option>
                    </select>
                </div>

                <!-- 5. Kelurahan -->
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted mb-1">Kelurahan</label>
                    <select name="kelurahan_id" class="form-select form-select-sm">
                        <option value="">-- Semua Kelurahan --</option>
                        <?php foreach ($villages as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= ($filter['kelurahan_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                                <?= esc($v['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 6. Group UPA & Tombol Action -->
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-gold fw-bold w-100" title="Terapkan Filter">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/members/pendidikan') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ===== TABEL DAFTAR ANGGOTA & PENDIDIKAN ===== -->
<div class="card border-0 shadow-sm" style="border-radius:12px; overflow:hidden;">
    <div class="card-header bg-dark-coffee text-cream py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-list-check text-gold fs-5"></i>
            <h5 class="mb-0 fw-bold">Daftar Anggota Berdasarkan Pendidikan & Sekolah</h5>
            <span class="badge bg-gold text-dark ms-2"><?= count($members) ?> Anggota Ditampilkan</span>
        </div>
        <small class="text-cream opacity-75">
            Klik tombol <span class="badge bg-warning text-dark"><i class="fa-solid fa-pen-to-square"></i></span> untuk update cepat nama sekolah / jenjang
        </small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablePendidikan" style="font-size:0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 45px;">No</th>
                        <th style="min-width: 220px;">Nama Anggota</th>
                        <th style="min-width: 140px;">Jenjang Pendidikan</th>
                        <th style="min-width: 240px;">Nama Sekolah / Universitas</th>
                        <th style="min-width: 180px;">Jurusan / Program Studi</th>
                        <th style="min-width: 150px;">Domisili / Kelurahan</th>
                        <th class="text-center" style="min-width: 90px;">Group UPA</th>
                        <th class="text-center" style="min-width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-graduation-cap fa-3x mb-3 text-secondary opacity-25 d-block"></i>
                                Tidak ditemukan data anggota sesuai filter yang dipilih.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($members as $m): ?>
                            <?php $edu = $m['edu']; ?>
                            <tr>
                                <!-- No -->
                                <td class="text-center text-muted fw-semibold"><?= $no++ ?></td>

                                <!-- Nama Anggota -->
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm"
                                             style="width: 34px; height: 34px; min-width: 34px; font-size: 0.8rem; background-color: <?= $m['jenis_kelamin'] === 'P' ? '#ec4899' : '#0284c7' ?>;">
                                            <?= esc(substr($m['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('admin/members/edit/' . $m['id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary">
                                                <?= esc($m['nama_lengkap']) ?>
                                            </a>
                                            <div class="small text-muted d-flex align-items-center gap-2 flex-wrap mt-1">
                                                <span class="badge <?= $m['jenis_kelamin'] === 'P' ? 'bg-pink-subtle text-pink' : 'bg-blue-subtle text-blue' ?>" style="font-size:0.7rem;">
                                                    <?= $m['jenis_kelamin'] === 'P' ? 'Perempuan' : 'Laki-laki' ?>
                                                </span>
                                                <?php if (!empty($m['status_anggota'])): ?>
                                                    <span class="badge bg-gold text-dark" style="font-size:0.7rem;">
                                                        <?= esc($m['status_anggota']) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php if (!empty($m['pekerjaan'])): ?>
                                                    <span>&middot; <i class="fa-solid fa-briefcase me-1 opacity-75"></i><?= esc($m['pekerjaan']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Jenjang Pendidikan -->
                                <td>
                                    <div>
                                        <span class="badge <?= $edu['cat_badge'] ?> px-2 py-1 shadow-sm" 
                                              style="background-color: <?= $edu['cat_bg'] ?>; font-size: 0.78rem; font-weight: 600;">
                                            <i class="fa-solid fa-graduation-cap me-1"></i>
                                            <?= esc($edu['jenjang_raw'] ?: $edu['cat_short']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                        <?= esc($edu['cat_label']) ?>
                                    </small>
                                </td>

                                <!-- Nama Sekolah / Universitas -->
                                <td>
                                    <?php if ($edu['has_real_school']): ?>
                                        <div class="fw-bold text-dark-coffee d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-building-columns text-gold me-1"></i>
                                            <span><?= esc($edu['sekolah']) ?></span>
                                        </div>
                                        <?php if (!empty($edu['educations']) && count($edu['educations']) > 1): ?>
                                            <div class="mt-1">
                                                <span class="badge bg-light text-secondary border cursor-pointer btn-show-histories" 
                                                      data-bs-toggle="modal" 
                                                      data-bs-target="#modalDetailEdu_<?= $m['id'] ?>"
                                                      title="Lihat <?= count($edu['educations']) ?> riwayat pendidikan">
                                                    <i class="fa-solid fa-layer-group me-1 text-gold"></i><?= count($edu['educations']) ?> Gelar / Riwayat
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-danger border">
                                                <i class="fa-solid fa-circle-exclamation me-1"></i>Belum Diisi
                                            </span>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-warning py-0 px-2 btn-quick-edit"
                                                    data-id="<?= $m['id'] ?>"
                                                    data-nama="<?= esc($m['nama_lengkap']) ?>"
                                                    data-jenjang="<?= esc($edu['jenjang_raw']) ?>"
                                                    data-sekolah="<?= esc($edu['sekolah']) ?>"
                                                    data-jurusan="<?= esc($edu['jurusan']) ?>"
                                                    style="font-size: 0.72rem;"
                                                    title="Isi Nama Sekolah Cepat">
                                                <i class="fa-solid fa-plus me-1"></i>Isi
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Jurusan / Program Studi -->
                                <td>
                                    <?php if (!empty($edu['jurusan'])): ?>
                                        <div class="fw-semibold text-dark">
                                            <i class="fa-solid fa-book-bookmark text-secondary me-1"></i>
                                            <?= esc($edu['jurusan']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic small">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Domisili -->
                                <td>
                                    <div class="text-dark small">
                                        <?= esc($m['kelurahan'] ?: '-') ?>
                                    </div>
                                    <small class="text-muted" style="font-size: 0.73rem;">
                                        <?= ($m['rt'] ? 'RT ' . esc($m['rt']) : '') . ($m['rw'] ? ' / RW ' . esc($m['rw']) : '') ?>
                                    </small>
                                </td>

                                <!-- Group UPA -->
                                <td class="text-center">
                                    <?php if (!empty($m['kode_upa'])): ?>
                                        <span class="badge font-monospace" style="background-color: #3e2723; color: #fff8e1;">
                                            <?= esc($m['kode_upa']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic small">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <!-- Quick Edit Button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning py-1 px-2 btn-quick-edit"
                                                data-id="<?= $m['id'] ?>"
                                                data-nama="<?= esc($m['nama_lengkap']) ?>"
                                                data-jenjang="<?= esc($edu['jenjang_raw']) ?>"
                                                data-sekolah="<?= esc($edu['sekolah']) ?>"
                                                data-jurusan="<?= esc($edu['jurusan']) ?>"
                                                title="Update Cepat Pendidikan">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>

                                        <!-- Full Edit Button -->
                                        <a href="<?= base_url('admin/members/edit/' . $m['id']) ?>" 
                                           class="btn btn-sm btn-outline-secondary py-1 px-2" 
                                           title="Buka Form Lengkap">
                                            <i class="fa-solid fa-user-pen"></i>
                                        </a>

                                        <!-- WA Button -->
                                        <?php if (!empty($m['handphone'])): ?>
                                            <?php 
                                                $wa = preg_replace('/[^0-9]/', '', $m['handphone']);
                                                if (str_starts_with($wa, '0')) $wa = '62' . substr($wa, 1);
                                            ?>
                                            <a href="https://wa.me/<?= $wa ?>" target="_blank" 
                                               class="btn btn-sm btn-outline-success py-1 px-2" 
                                               title="Chat WhatsApp">
                                                <i class="fa-brands fa-whatsapp"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>

                                    <!-- MODAL DETAIL RIWAYAT MULTI GELAR (jika ada lebih dari 1) -->
                                    <?php if (!empty($edu['educations']) && count($edu['educations']) > 1): ?>
                                        <div class="modal fade" id="modalDetailEdu_<?= $m['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-dark-coffee text-cream">
                                                        <h6 class="modal-title fw-bold">
                                                            <i class="fa-solid fa-graduation-cap text-gold me-2"></i>Riwayat Pendidikan: <?= esc($m['nama_lengkap']) ?>
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-start">
                                                        <ul class="list-group list-group-flush">
                                                            <?php foreach ($edu['educations'] as $he): ?>
                                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                                    <div>
                                                                        <span class="badge bg-gold text-dark font-monospace"><?= esc($he['jenjang']) ?></span>
                                                                        <strong class="text-dark ms-2"><?= esc($he['nama_institusi'] ?: '-') ?></strong>
                                                                    </div>
                                                                    <small class="text-muted"><?= esc($he['rentang_tahun'] ?: '-') ?></small>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    </div>
                                                    <div class="modal-footer border-top p-2">
                                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODAL QUICK UPDATE PENDIDIKAN ================= -->
<div class="modal fade" id="modalQuickEditEdu" tabindex="-1" aria-labelledby="modalQuickEditEduLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form id="formQuickEditEdu" method="POST" action="<?= base_url('admin/members/pendidikan/quick-update') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="member_id" id="quickEduMemberId" value="">
                <input type="hidden" name="ajax" value="1">

                <div class="modal-header bg-dark-coffee text-cream">
                    <h5 class="modal-title fw-bold" id="modalQuickEditEduLabel">
                        <i class="fa-solid fa-graduation-cap text-gold me-2"></i>Update Pendidikan Anggota
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3 p-2 rounded bg-light border">
                        <small class="text-muted d-block">Nama Anggota:</small>
                        <strong id="quickEduMemberName" class="text-dark-coffee fs-6"></strong>
                    </div>

                    <!-- Jenjang Pendidikan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Jenjang Pendidikan <span class="text-danger">*</span></label>
                        <select name="jenjang" id="quickEduJenjang" class="form-select" required>
                            <option value="">-- Pilih Jenjang --</option>
                            <option value="S3">S3 (Doktor)</option>
                            <option value="S2">S2 (Magister)</option>
                            <option value="S1">S1 (Sarjana)</option>
                            <option value="D4">D4 (Diploma 4)</option>
                            <option value="D3">D3 (Diploma 3)</option>
                            <option value="D2">D2 (Diploma 2)</option>
                            <option value="D1">D1 (Diploma 1)</option>
                            <option value="SMA/SMK">SMA / SMK / MA</option>
                            <option value="SMP">SMP / MTs</option>
                            <option value="SD">SD / MI</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <!-- Nama Sekolah / Universitas -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Nama Sekolah / Universitas / Institut <span class="text-danger">*</span></label>
                        <input type="text" name="sekolah_universitas" id="quickEduSekolah" class="form-control" 
                               placeholder="Contoh: Universitas Indonesia, IPB, SMA 1 Depok..." required>
                    </div>

                    <!-- Jurusan / Program Studi -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Jurusan / Program Studi / Peminatan</label>
                        <input type="text" name="jurusan" id="quickEduJurusan" class="form-control" 
                               placeholder="Contoh: Teknik Informatika, Akuntansi, IPA...">
                    </div>

                    <!-- Rentang Tahun -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Tahun / Rentang Tahun</label>
                        <input type="text" name="rentang_tahun" id="quickEduTahun" class="form-control" 
                               placeholder="Contoh: 2016 - 2020 atau 2021">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gold fw-bold" id="btnSubmitQuickEdu">
                        <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-dark-coffee {
        background-color: var(--dark-coffee, #3e2723) !important;
    }
    .text-dark-coffee {
        color: var(--dark-coffee, #3e2723) !important;
    }
    .btn-dark-coffee {
        background-color: var(--dark-coffee, #3e2723) !important;
        color: #fff8e1 !important;
        border: none;
    }
    .btn-gold {
        background-color: #d4af37 !important;
        color: #212529 !important;
        border: none;
    }
    .btn-gold:hover {
        background-color: #b89628 !important;
        color: #000 !important;
    }
    .bg-pink-subtle {
        background-color: #fdf2f8 !important;
    }
    .text-pink {
        color: #db2777 !important;
    }
    .bg-blue-subtle {
        background-color: #f0f9ff !important;
    }
    .text-blue {
        color: #0284c7 !important;
    }
    .kpi-card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .kpi-card-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.08) !important;
    }
    .active-kpi {
        background-color: #fff8e1 !important;
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.3) !important;
        transform: scale(1.02);
    }
    .hover-school-badge:hover {
        background-color: #efebe9 !important;
        color: #3e2723 !important;
        border-color: #d4af37 !important;
    }
    .hover-primary:hover {
        color: #0284c7 !important;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick Edit Education Modal
    const modalQuickEdit = new bootstrap.Modal(document.getElementById('modalQuickEditEdu'));
    const formQuickEdit = document.getElementById('formQuickEditEdu');
    const inputMemberId = document.getElementById('quickEduMemberId');
    const labelMemberName = document.getElementById('quickEduMemberName');
    const inputJenjang = document.getElementById('quickEduJenjang');
    const inputSekolah = document.getElementById('quickEduSekolah');
    const inputJurusan = document.getElementById('quickEduJurusan');
    const btnSubmit = document.getElementById('btnSubmitQuickEdu');

    document.querySelectorAll('.btn-quick-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const mId = this.dataset.id;
            const mNama = this.dataset.nama || '';
            const mJenjang = this.dataset.jenjang || '';
            const mSekolah = this.dataset.sekolah || '';
            const mJurusan = this.dataset.jurusan || '';

            inputMemberId.value = mId;
            labelMemberName.textContent = mNama;
            inputJenjang.value = mJenjang;
            inputSekolah.value = mSekolah;
            inputJurusan.value = mJurusan;

            modalQuickEdit.show();
        });
    });

    if (formQuickEdit) {
        formQuickEdit.addEventListener('submit', function(e) {
            e.preventDefault();
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Menyimpan...';

            const formData = new FormData(formQuickEdit);

            fetch(formQuickEdit.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        alert(res.message);
                        window.location.reload();
                    }
                } else {
                    alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan';
                }
            })
            .catch(err => {
                alert('Terjadi kesalahan: ' + err.message);
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan';
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
