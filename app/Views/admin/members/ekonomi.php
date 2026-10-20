<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<?php
    $currentMode = $mode ?? 'keluarga';
?>

<!-- ===== NAV TABS / PILLS UTAMA ===== -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?= base_url('admin/members') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-users me-1"></i>Daftar Anggota
        </a>
        <a href="<?= base_url('admin/members/ekonomi') ?>" class="btn btn-sm fw-semibold px-3 active shadow-sm" style="border-radius:20px; background:var(--dark-coffee); color:#fff; border:none;">
            <i class="fa-solid fa-sack-dollar me-1 text-gold"></i>Data Ekonomi Anggota
        </a>
        <a href="<?= base_url('admin/members/pendidikan') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-graduation-cap me-1 text-gold"></i>Pendidikan Anggota
        </a>
        <a href="<?= base_url('admin/members/dashboard') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-chart-pie me-1"></i>Statistik & Dashboard
        </a>
        <a href="<?= base_url('admin/upa') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-people-group me-1 text-gold"></i>Group UPA
        </a>
    </div>
</div>

<!-- ===== SUB-TOGGLE: BERBASIS KELUARGA (KK) VS INDIVIDU ===== -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div class="btn-group p-1 bg-white rounded-pill shadow-sm border" role="group">
        <a href="<?= base_url('admin/members/ekonomi?mode=keluarga') ?>" 
           class="btn btn-sm rounded-pill px-4 fw-bold <?= $currentMode === 'keluarga' ? 'btn-dark-coffee active shadow-sm' : 'btn-light text-dark-coffee' ?>">
            <i class="fa-solid fa-people-roof me-2 text-gold"></i>Ekonomi per Kepala Keluarga (KK)
        </a>
        <a href="<?= base_url('admin/members/ekonomi?mode=individu') ?>" 
           class="btn btn-sm rounded-pill px-4 fw-bold <?= $currentMode === 'individu' ? 'btn-dark-coffee active shadow-sm' : 'btn-light text-dark-coffee' ?>">
            <i class="fa-solid fa-user me-2 text-gold"></i>Ekonomi per Individu
        </a>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php
            $exportParams = http_build_query($filter ?? []);
            $exportUrl = base_url('admin/members/ekonomi/export') . ($exportParams ? '?' . $exportParams : '');
        ?>
        <a href="<?= $exportUrl ?>" class="btn btn-sm btn-success fw-semibold px-3 shadow-sm">
            <i class="fa-solid fa-file-excel me-1"></i><?= $currentMode === 'keluarga' ? 'Export Excel KK' : 'Export Excel Individu' ?>
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/members/create') ?>" class="btn btn-sm btn-gold fw-semibold px-3 shadow-sm">
            <i class="fa-solid fa-plus me-1"></i>Tambah Anggota
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($currentMode === 'keluarga'): ?>
    <!-- ======================================================== -->
    <!-- TAMPILAN 1: KONDISI EKONOMI PER KEPALA KELUARGA (KK)     -->
    <!-- ======================================================== -->

    <!-- PAGE HEADER KK -->
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #efebe9 0%, #f5f0e8 100%); border-left: 5px solid #d4af37 !important; border-radius: 12px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 style="font-family:'Playfair Display',serif; color:#3e2723;" class="mb-1 fw-bold">
                        <i class="fa-solid fa-people-roof text-gold me-2"></i>Kondisi Ekonomi per Kepala Keluarga (KK)
                    </h4>
                    <p class="text-muted small mb-0">
                        Pemetaan kesejahteraan keluarga berbasis <strong>akumulasi pendapatan (Kepala Keluarga + Pasangan)</strong> dibagi dengan <strong>jumlah tanggungan / anggota keluarga</strong> (Pendapatan per Kapita).
                    </p>
                </div>
                <div class="d-flex align-items-center gap-3 bg-white px-3 py-2 rounded shadow-sm border flex-wrap">
                    <div class="text-center border-end pe-3">
                        <div class="text-muted small">Total KK</div>
                        <div class="fw-bold fs-5 text-dark-coffee"><?= number_format($totalKK ?? 0) ?></div>
                    </div>
                    <div class="text-center border-end pe-3">
                        <div class="text-muted small">Total Jiwa</div>
                        <div class="fw-bold fs-5 text-primary"><?= number_format($totalJiwa ?? 0) ?></div>
                    </div>
                    <div class="text-center border-end pe-3">
                        <div class="text-muted small">Rata-rata Jiwa/KK</div>
                        <div class="fw-bold fs-5 text-dark"><?= number_format($avgFamilySize ?? 0, 1) ?></div>
                    </div>
                    <div class="text-center border-end pe-3">
                        <div class="text-muted small">Rata-rata Pendapatan KK</div>
                        <div class="fw-bold fs-5 text-success">Rp <?= number_format(($avgIncome ?? 0) / 1000000, 1) ?> Jt</div>
                    </div>
                    <div class="text-center">
                        <div class="text-muted small">Rata-rata per Kapita</div>
                        <div class="fw-bold fs-5 text-gold">Rp <?= number_format(($avgPerCapita ?? 0) / 1000, 0, ',', '.') ?> rb</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI CARDS: 4 KATEGORI KESEJAHTERAAN + BELUM MENGISI -->
    <?php
        $baseQsKK = function($key, $val) use ($filter) {
            $q = $filter ?? [];
            $q['mode'] = 'keluarga';
            $q[$key] = $val;
            return base_url('admin/members/ekonomi?' . http_build_query($q));
        };
        $cPra = $welfareCounts['prasejahtera'] ?? 0;
        $cRen = $welfareCounts['rentan'] ?? 0;
        $cMen = $welfareCounts['menengah'] ?? 0;
        $cSej = $welfareCounts['sejahtera'] ?? 0;
        $cBel = $welfareCounts['belum'] ?? 0;
    ?>
    <div class="row g-3 mb-4">
        <!-- Prasejahtera -->
        <div class="col-6 col-md-4 col-xl">
            <a href="<?= $baseQsKK('welfare', ($filter['welfare'] ?? '') === 'prasejahtera' ? '' : 'prasejahtera') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['welfare'] ?? '') === 'prasejahtera' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #dc2626 !important; border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#fef2f2; color:#b91c1c; border:1px solid #fecaca;">Prasejahtera</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#fef2f2;">
                                <i class="fa-solid fa-triangle-exclamation text-danger" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($cPra) ?> <span class="fs-6 fw-normal text-muted">KK</span></div>
                        <div class="fw-semibold text-truncate small text-danger">&lt; Rp 800 rb / jiwa</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalKK ?? 0) > 0 ? round(($cPra / $totalKK) * 100, 1) : 0 ?>% dari total KK</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Rentan -->
        <div class="col-6 col-md-4 col-xl">
            <a href="<?= $baseQsKK('welfare', ($filter['welfare'] ?? '') === 'rentan' ? '' : 'rentan') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['welfare'] ?? '') === 'rentan' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #f97316 !important; border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#fff7ed; color:#c2410c; border:1px solid #ffedd5;">Rentan</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#fff7ed;">
                                <i class="fa-solid fa-hand-holding-heart text-warning" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($cRen) ?> <span class="fs-6 fw-normal text-muted">KK</span></div>
                        <div class="fw-semibold text-truncate small" style="color:#ea580c;">Rp 800 rb – 1,5 jt / jiwa</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalKK ?? 0) > 0 ? round(($cRen / $totalKK) * 100, 1) : 0 ?>% dari total KK</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Menengah -->
        <div class="col-6 col-md-4 col-xl">
            <a href="<?= $baseQsKK('welfare', ($filter['welfare'] ?? '') === 'menengah' ? '' : 'menengah') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['welfare'] ?? '') === 'menengah' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #0284c7 !important; border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#f0f9ff; color:#0369a1; border:1px solid #e0f2fe;">Menengah</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f0f9ff;">
                                <i class="fa-solid fa-shield-halved text-info" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($cMen) ?> <span class="fs-6 fw-normal text-muted">KK</span></div>
                        <div class="fw-semibold text-truncate small" style="color:#0284c7;">Rp 1,5 – 3 jt / jiwa</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalKK ?? 0) > 0 ? round(($cMen / $totalKK) * 100, 1) : 0 ?>% dari total KK</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Sejahtera -->
        <div class="col-6 col-md-4 col-xl">
            <a href="<?= $baseQsKK('welfare', ($filter['welfare'] ?? '') === 'sejahtera' ? '' : 'sejahtera') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['welfare'] ?? '') === 'sejahtera' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #059669 !important; border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#ecfdf5; color:#047857; border:1px solid #d1fae5;">Sejahtera</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#ecfdf5;">
                                <i class="fa-solid fa-circle-check text-success" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($cSej) ?> <span class="fs-6 fw-normal text-muted">KK</span></div>
                        <div class="fw-semibold text-truncate small text-success">&ge; Rp 3 jt / jiwa</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalKK ?? 0) > 0 ? round(($cSej / $totalKK) * 100, 1) : 0 ?>% dari total KK</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Belum Terdata -->
        <div class="col-6 col-md-4 col-xl">
            <a href="<?= $baseQsKK('welfare', ($filter['welfare'] ?? '') === 'belum' ? '' : 'belum') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['welfare'] ?? '') === 'belum' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #9ca3af !important; border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-muted border">Belum Terdata</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f3f4f6;">
                                <i class="fa-regular fa-circle-question text-muted" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($cBel) ?> <span class="fs-6 fw-normal text-muted">KK</span></div>
                        <div class="fw-semibold text-truncate small text-secondary">Pendapatan Kosong</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalKK ?? 0) > 0 ? round(($cBel / $totalKK) * 100, 1) : 0 ?>% dari total KK</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- VISUAL CHART & MATRIKS BEBAN TANGGUNGAN KK -->
    <div class="row g-3 mb-4">
        <!-- Donut Kesejahteraan KK -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header bg-transparent border-0 pt-3 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark-coffee mb-0">
                        <i class="fa-solid fa-chart-pie text-gold me-2"></i>Komposisi Kesejahteraan KK
                    </h6>
                    <small class="text-muted"><?= ($filter['status_anggota'] ?? 'pelopor') === 'pelopor' ? 'Anggota Pelopor' : 'Semua Anggota' ?></small>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center mb-3 mb-md-0">
                            <canvas id="welfarePieChart" style="max-height: 190px; max-width: 190px; margin: 0 auto;"></canvas>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex flex-column gap-2 small">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1 text-success" style="font-size:0.6rem;"></i>Sejahtera (&ge; 3 jt/kapita)</span>
                                    <span class="fw-bold"><?= number_format($cSej) ?> KK <small class="text-muted">(<?= ($totalKK??0)>0?round(($cSej/$totalKK)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1 text-info" style="font-size:0.6rem;"></i>Menengah (1,5 - 3 jt)</span>
                                    <span class="fw-bold"><?= number_format($cMen) ?> KK <small class="text-muted">(<?= ($totalKK??0)>0?round(($cMen/$totalKK)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#ea580c; font-size:0.6rem;"></i>Rentan (800 rb - 1,5 jt)</span>
                                    <span class="fw-bold"><?= number_format($cRen) ?> KK <small class="text-muted">(<?= ($totalKK??0)>0?round(($cRen/$totalKK)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1 text-danger" style="font-size:0.6rem;"></i>Prasejahtera (&lt; 800 rb)</span>
                                    <span class="fw-bold text-danger"><?= number_format($cPra) ?> KK <small class="text-muted">(<?= ($totalKK??0)>0?round(($cPra/$totalKK)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between text-muted border-top pt-1">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#9ca3af;font-size:0.6rem;"></i>Belum Terdata</span>
                                    <span class="fw-semibold"><?= number_format($cBel) ?> KK <small>(<?= ($totalKK??0)>0?round(($cBel/$totalKK)*100,1):0 ?>%)</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matriks Ukuran Keluarga vs Kesejahteraan -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header bg-transparent border-0 pt-3 px-4 pb-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold text-dark-coffee mb-0">
                            <i class="fa-solid fa-table-cells-large text-gold me-2"></i>Matriks Ukuran Keluarga vs Kesejahteraan
                        </h6>
                        <small class="text-muted">Melihat tingkat kerentanan keluarga berdasarkan beban tanggungan</small>
                    </div>
                    <span class="badge bg-light text-dark-coffee border">Beban Tanggungan</span>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle text-center mb-0 no-datatable" style="font-size:0.82rem;">
                            <thead style="background:#efebe9;">
                                <tr class="text-dark-coffee">
                                    <th class="text-start ps-3" style="width:25%;">Jumlah Anggota</th>
                                    <th style="color:#dc2626; width:15%;">Prasejahtera</th>
                                    <th style="color:#ea580c; width:15%;">Rentan</th>
                                    <th style="color:#0284c7; width:15%;">Menengah</th>
                                    <th style="color:#059669; width:15%;">Sejahtera</th>
                                    <th style="color:#6b7280; width:15%;">Belum Terdata</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $sizeLabels = [
                                        1 => '1 Jiwa (Lajang/Sendiri)',
                                        2 => '2 Jiwa (Pasangan tanpa anak)',
                                        3 => '3 Jiwa (1 Anak)',
                                        4 => '4 Jiwa (2 Anak)',
                                        5 => '5 Jiwa (3 Anak)',
                                        6 => '6+ Jiwa (Banyak tanggungan)',
                                    ];
                                    foreach ($sizeLabels as $sz => $lbl):
                                        $rowW = $welfareBySize[$sz] ?? [];
                                        $pCount = $rowW['prasejahtera'] ?? 0;
                                        $rCount = $rowW['rentan'] ?? 0;
                                        $mCount = $rowW['menengah'] ?? 0;
                                        $sCount = $rowW['sejahtera'] ?? 0;
                                        $bCount = $rowW['belum'] ?? 0;
                                ?>
                                <tr>
                                    <td class="text-start ps-3 fw-semibold text-dark-coffee">
                                        <a href="<?= $baseQsKK('family_size', ($filter['family_size'] ?? '') == $sz ? '' : $sz) ?>" class="text-decoration-none text-dark-coffee">
                                            <?= esc($lbl) ?>
                                        </a>
                                    </td>
                                    <td class="<?= $pCount > 0 ? 'bg-danger bg-opacity-10 fw-bold text-danger' : 'text-muted' ?>">
                                        <?= $pCount ?: '-' ?>
                                    </td>
                                    <td class="<?= $rCount > 0 ? 'bg-warning bg-opacity-10 fw-bold text-warning' : 'text-muted' ?>">
                                        <?= $rCount ?: '-' ?>
                                    </td>
                                    <td class="<?= $mCount > 0 ? 'bg-info bg-opacity-10 fw-bold text-primary' : 'text-muted' ?>">
                                        <?= $mCount ?: '-' ?>
                                    </td>
                                    <td class="<?= $sCount > 0 ? 'bg-success bg-opacity-10 fw-bold text-success' : 'text-muted' ?>">
                                        <?= $sCount ?: '-' ?>
                                    </td>
                                    <td class="text-muted">
                                        <?= $bCount ?: '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR KK -->
    <div class="card border-0 shadow-sm mb-3" style="background:#fff; border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" action="<?= base_url('admin/members/ekonomi') ?>" class="row g-2 align-items-end">
                <input type="hidden" name="mode" value="keluarga">

                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label small mb-1 fw-semibold text-muted">Cari KK / Pasangan / Anak</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Ketik nama anggota keluarga..." value="<?= esc($filter['search'] ?? '') ?>">
                    </div>
                </div>

                <!-- Kondisi Kesejahteraan -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Kondisi Kesejahteraan</label>
                    <select name="welfare" class="form-select form-select-sm">
                        <option value="">Semua Kondisi</option>
                        <option value="prasejahtera" <?= ($filter['welfare'] ?? '') === 'prasejahtera' ? 'selected' : '' ?>>Prasejahtera (&lt; 800 rb)</option>
                        <option value="rentan" <?= ($filter['welfare'] ?? '') === 'rentan' ? 'selected' : '' ?>>Rentan (800 rb - 1,5 jt)</option>
                        <option value="menengah" <?= ($filter['welfare'] ?? '') === 'menengah' ? 'selected' : '' ?>>Menengah (1,5 - 3 jt)</option>
                        <option value="sejahtera" <?= ($filter['welfare'] ?? '') === 'sejahtera' ? 'selected' : '' ?>>Sejahtera (&ge; 3 jt)</option>
                        <option value="belum" <?= ($filter['welfare'] ?? '') === 'belum' ? 'selected' : '' ?>>Belum Terdata</option>
                    </select>
                </div>

                <!-- Status Anggota -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Jenjang Kader</label>
                    <select name="status_anggota" class="form-select form-select-sm">
                        <option value="pelopor" <?= ($filter['status_anggota'] ?? '') === 'pelopor' ? 'selected' : '' ?>>Pelopor (Madya/Dewasa/Utama)</option>
                        <option value="all" <?= ($filter['status_anggota'] ?? '') === 'all' ? 'selected' : '' ?>>Semua Jenjang</option>
                        <option value="Utama"  <?= ($filter['status_anggota'] ?? '') === 'Utama'  ? 'selected' : '' ?>>Utama</option>
                        <option value="Dewasa" <?= ($filter['status_anggota'] ?? '') === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                        <option value="Madya"  <?= ($filter['status_anggota'] ?? '') === 'Madya'  ? 'selected' : '' ?>>Madya</option>
                        <option value="Muayyid" <?= ($filter['status_anggota'] ?? '') === 'Muayyid' ? 'selected' : '' ?>>Muayyid</option>
                        <option value="Muhib"   <?= ($filter['status_anggota'] ?? '') === 'Muhib'   ? 'selected' : '' ?>>Muhib</option>
                        <option value="Belum Tarbiyah" <?= ($filter['status_anggota'] ?? '') === 'Belum Tarbiyah' ? 'selected' : '' ?>>Belum Tarbiyah</option>
                        <option value="non_pelopor" <?= ($filter['status_anggota'] ?? '') === 'non_pelopor' ? 'selected' : '' ?>>Non-Pelopor</option>
                    </select>
                </div>

                <!-- Jumlah Anggota Keluarga -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Jumlah Anggota (Jiwa)</label>
                    <select name="family_size" class="form-select form-select-sm">
                        <option value="">Semua Jumlah</option>
                        <option value="1" <?= ($filter['family_size'] ?? '') === '1' ? 'selected' : '' ?>>1 Jiwa (Sendiri)</option>
                        <option value="2" <?= ($filter['family_size'] ?? '') === '2' ? 'selected' : '' ?>>2 Jiwa</option>
                        <option value="3" <?= ($filter['family_size'] ?? '') === '3' ? 'selected' : '' ?>>3 Jiwa</option>
                        <option value="4" <?= ($filter['family_size'] ?? '') === '4' ? 'selected' : '' ?>>4 Jiwa</option>
                        <option value="5" <?= ($filter['family_size'] ?? '') === '5' ? 'selected' : '' ?>>5 Jiwa</option>
                        <option value="6" <?= ($filter['family_size'] ?? '') === '6' ? 'selected' : '' ?>>6+ Jiwa</option>
                    </select>
                </div>

                <!-- Kelurahan -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Kelurahan</label>
                    <select name="kelurahan_id" class="form-select form-select-sm">
                        <option value="">Semua Kelurahan</option>
                        <?php if (!empty($kelurahanOptions)): ?>
                            <?php foreach ($kelurahanOptions as $v): ?>
                            <option value="<?= esc($v['id']) ?>" <?= (string)($filter['kelurahan_id'] ?? '') === (string)$v['id'] ? 'selected' : '' ?>>
                                <?= esc(ucwords(strtolower($v['name']))) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <option value="none" <?= ($filter['kelurahan_id'] ?? '') === 'none' ? 'selected' : '' ?>>Tanpa Kelurahan</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark-coffee w-100 fw-semibold" title="Terapkan Filter">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/members/ekonomi?mode=keluarga') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- DATA TABLE: KEPALA KELUARGA -->
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark-coffee mb-0">
                    <i class="fa-solid fa-list-check text-gold me-2"></i>Daftar Kepala Keluarga (KK) & Rincian Ekonomi
                </h6>
                <small class="text-muted">Menampilkan <?= number_format(count($households ?? [])) ?> KK dari total <?= number_format($totalKK ?? 0) ?> KK</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark-coffee border">
                    <i class="fa-solid fa-user-check text-success me-1"></i>Urutan: Kategori Kesejahteraan Terendah &rarr; Tertinggi
                </span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableKK" style="font-size:0.875rem;">
                    <thead style="background:#efebe9;">
                        <tr class="text-dark-coffee">
                            <th class="text-center" style="width:40px;">No</th>
                            <th style="min-width:210px;">Kepala Keluarga</th>
                            <th style="min-width:180px;">Pasangan</th>
                            <th class="text-center" style="min-width:90px;">Tanggungan</th>
                            <th style="min-width:170px;">Pendapatan KK (Est.)</th>
                            <th style="min-width:160px;">Pendapatan per Kapita</th>
                            <th class="text-center" style="min-width:130px;">Kondisi Ekonomi</th>
                            <th style="min-width:140px;">Wilayah / UPA</th>
                            <th class="text-center" style="width:70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($households)): ?>
                            <?php 
                                $no = 1;
                                foreach ($households as $h): 
                                    $w = $h['welfare'];
                                    $wCat = $welfareCategories[$w] ?? [
                                        'label' => 'Belum Terdata', 
                                        'color' => '#6b7280', 
                                        'bg'    => '#f3f4f6', 
                                        'desc'  => 'Belum ada data pendapatan'
                                    ];

                                    $phoneClean = preg_replace('/[^0-9]/', '', (string)($h['head_phone'] ?? ''));
                                    if (str_starts_with($phoneClean, '0')) {
                                        $phoneClean = '62' . substr($phoneClean, 1);
                                    }
                            ?>
                            <tr>
                                <td class="text-center text-muted small fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                                             style="width:36px; height:36px; background-color:#3e2723; font-size:0.85rem;">
                                            <?= strtoupper(substr(trim($h['head_name']), 0, 2)) ?>
                                        </div>
                                        <div class="lh-sm">
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="<?= base_url('admin/members/edit/' . $h['head_id']) ?>" class="fw-bold text-dark-coffee text-decoration-none hover-gold" title="Lihat profil">
                                                    <?= esc($h['head_name']) ?>
                                                </a>
                                                <?php if (!empty($phoneClean)): ?>
                                                    <a href="https://wa.me/<?= $phoneClean ?>" target="_blank" class="text-success ms-1" title="Hubungi WhatsApp">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small mt-1 d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge bg-light text-dark-coffee border" style="font-size:0.7rem;"><?= esc($h['head_status'] ?: 'Non-Pelopor') ?></span>
                                                <?php if (!empty($h['head_job'])): ?>
                                                    <span class="text-secondary small">&bull; <?= esc($h['head_job']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($h['spouse_id'])): ?>
                                        <div class="lh-sm">
                                            <a href="<?= base_url('admin/members/edit/' . $h['spouse_id']) ?>" class="fw-semibold text-dark text-decoration-none hover-gold">
                                                <?= esc($h['spouse_name']) ?>
                                            </a>
                                            <div class="text-muted small mt-1 d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge bg-light text-muted border" style="font-size:0.7rem;"><?= esc($h['spouse_status'] ?: 'Anggota') ?></span>
                                                <?php if (!empty($h['spouse_job'])): ?>
                                                    <span class="text-secondary small">&bull; <?= esc($h['spouse_job']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted" style="font-size:0.72rem;">
                                                Pendapatan: <span class="fw-semibold"><?= esc($h['spouse_income_range'] ?: 'Belum Mengisi') ?></span>
                                            </div>
                                        </div>
                                    <?php elseif ($h['spouse_unregistered']): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning" style="font-size:0.75rem;">
                                            <i class="fa-solid fa-ring me-1"></i>Pasangan (Belum Terdata)
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">&mdash; Lajang / Mandiri</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark-coffee border px-2 py-1 fs-7 fw-bold" title="<?= $h['children_count'] ?> Anak Terdata">
                                        <i class="fa-solid fa-users text-gold me-1"></i><?= $h['family_size'] ?> Jiwa
                                    </span>
                                    <?php if ($h['children_count'] > 0): ?>
                                        <div class="text-muted mt-1" style="font-size:0.7rem;" title="<?= esc(implode(', ', $h['children'])) ?>">
                                            <?= $h['children_count'] ?> anak terdata
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($h['total_income'] !== null): ?>
                                        <div class="fw-bold text-success fs-7">
                                            Rp <?= number_format($h['total_income'], 0, ',', '.') ?>
                                        </div>
                                        <div class="text-muted" style="font-size:0.7rem;">
                                            <?php if ($h['spouse_id'] && $h['spouse_income'] !== null): ?>
                                                <?= esc($h['head_name']) ?> (<?= esc($h['head_income_range'] ?: '-') ?>) + Pasangan (<?= esc($h['spouse_income_range'] ?: '-') ?>)
                                            <?php else: ?>
                                                <?= esc($h['head_income_range'] ?: 'Belum Lengkap') ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Belum Ada Data</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($h['per_capita'] !== null): ?>
                                        <div class="fw-bold text-dark fs-7">
                                            Rp <?= number_format($h['per_capita'], 0, ',', '.') ?> <small class="text-muted fw-normal">/jiwa</small>
                                        </div>
                                        <div class="text-muted" style="font-size:0.7rem;">
                                            Rp <?= number_format($h['total_income'], 0, ',', '.') ?> &divide; <?= $h['family_size'] ?> jiwa
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge px-2 py-1 fw-bold" style="background-color: <?= $wCat['bg'] ?>; color: <?= $wCat['color'] ?>; border: 1px solid <?= $wCat['color'] ?>40;">
                                        <?= esc($wCat['label']) ?>
                                    </span>
                                    <div class="text-muted mt-1" style="font-size:0.68rem;">
                                        <?= esc($wCat['desc']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark-coffee">
                                        <?= esc($h['kelurahan'] ? ucwords(strtolower($h['kelurahan'])) : 'Tanpa Kelurahan') ?>
                                    </div>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        <?= esc($h['kode_upa'] ? 'UPA: ' . $h['kode_upa'] : 'UPA: -') ?>
                                        <?php if (!empty($h['rw'])): ?> &bull; RW <?= esc($h['rw']) ?><?php endif; ?>
                                        <?php if (!empty($h['rt'])): ?> / RT <?= esc($h['rt']) ?><?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-detail-kk py-1 px-2" 
                                            data-head="<?= esc(htmlspecialchars(json_encode($h), ENT_QUOTES, 'UTF-8')) ?>"
                                            title="Rincian Kartu Keluarga">
                                        <i class="fa-solid fa-eye text-gold"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-users-slash fs-2 mb-2 d-block text-secondary"></i>
                                    Tidak ada data Kepala Keluarga yang cocok dengan filter.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL DETAIL KK -->
    <div class="modal fade" id="modalDetailKK" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 text-white" style="background-color:#3e2723;">
                    <h5 class="modal-title fw-bold" style="font-family:'Playfair Display',serif;">
                        <i class="fa-solid fa-people-roof text-gold me-2"></i>Rincian Kartu Keluarga (KK)
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="modalDetailKKBody">
                    <!-- Diisi via JavaScript -->
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ======================================================== -->
    <!-- TAMPILAN 2: DATA EKONOMI PER INDIVIDU ANGGOTA            -->
    <!-- ======================================================== -->

    <!-- PAGE HEADER INDIVIDU -->
    <div class="card shadow-sm border-0 mb-4" style="background: linear-gradient(135deg, #efebe9 0%, #f5f0e8 100%); border-left: 5px solid #d4af37 !important; border-radius: 12px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h4 style="font-family:'Playfair Display',serif; color:#3e2723;" class="mb-1 fw-bold">
                        <i class="fa-solid fa-user text-gold me-2"></i>Data Ekonomi & Pendapatan Individu Anggota
                    </h4>
                    <p class="text-muted small mb-0">
                        Pemetaan profil ekonomi anggota perorangan berdasarkan rentang pendapatan per bulan, profesi, dan bidang usaha di wilayah DPC Pancoran Mas.
                    </p>
                </div>
                <div class="d-flex align-items-center gap-3 bg-white px-3 py-2 rounded shadow-sm border">
                    <div class="text-center border-end pe-3">
                        <div class="text-muted small">Cakupan Data</div>
                        <div class="fw-bold fs-5 text-dark-coffee"><?= number_format($totalScope ?? 0) ?></div>
                    </div>
                    <div class="text-center border-end pe-3">
                        <div class="text-muted small">Terdata</div>
                        <div class="fw-bold fs-5 text-success"><?= number_format($totalFilled ?? 0) ?></div>
                    </div>
                    <div class="text-center">
                        <div class="text-muted small">Keterisian</div>
                        <div class="fw-bold fs-5 text-primary"><?= $percentFilled ?? 0 ?>%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI CARDS INDIVIDU -->
    <?php
        $baseQsInd = function($rangeVal) use ($filter) {
            $q = $filter ?? [];
            $q['mode'] = 'individu';
            $q['pendapatan_range'] = $rangeVal;
            return base_url('admin/members/ekonomi?' . http_build_query($q));
        };
    ?>
    <div class="row g-3 mb-4">
        <!-- Tier 1: < 1 Juta -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= $baseQsInd('< Rp1.000.000') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['pendapatan_range'] ?? '') === '< Rp1.000.000' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #f97316 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#fff7ed;color:#c2410c;border:1px solid #ffedd5;">Tingkat 1</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#fff7ed;">
                                <i class="fa-solid fa-coins text-warning" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($tierUnder1M ?? 0) ?></div>
                        <div class="fw-semibold text-truncate small" style="color:#c2410c;">&lt; Rp 1 Juta</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalScope ?? 0) > 0 ? round((($tierUnder1M ?? 0) / $totalScope) * 100, 1) : 0 ?>% dari total</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tier 2: 1 - 3 Juta -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= $baseQsInd('Rp1.000.000 - Rp3.000.000') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['pendapatan_range'] ?? '') === 'Rp1.000.000 - Rp3.000.000' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #eab308 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#fefce8;color:#a16207;border:1px solid #fef9c3;">Tingkat 2</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#fefce8;">
                                <i class="fa-solid fa-wallet" style="font-size:0.8rem;color:#ca8a04;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($tier1M3M ?? 0) ?></div>
                        <div class="fw-semibold text-truncate small" style="color:#a16207;">Rp 1 - 3 Juta</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalScope ?? 0) > 0 ? round((($tier1M3M ?? 0) / $totalScope) * 100, 1) : 0 ?>% dari total</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tier 3: 3 - 5 Juta -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= $baseQsInd('Rp3.000.000 - Rp5.000.000') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['pendapatan_range'] ?? '') === 'Rp3.000.000 - Rp5.000.000' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #0284c7 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#f0f9ff;color:#0369a1;border:1px solid #e0f2fe;">Tingkat 3</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f0f9ff;">
                                <i class="fa-solid fa-briefcase" style="font-size:0.8rem;color:#0284c7;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($tier3M5M ?? 0) ?></div>
                        <div class="fw-semibold text-truncate small" style="color:#0369a1;">Rp 3 - 5 Juta</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalScope ?? 0) > 0 ? round((($tier3M5M ?? 0) / $totalScope) * 100, 1) : 0 ?>% dari total</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tier 4: 5 - 10 Juta -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= $baseQsInd('Rp5.000.000 - Rp10.000.000') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['pendapatan_range'] ?? '') === 'Rp5.000.000 - Rp10.000.000' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #6366f1 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#eef2ff;color:#4338ca;border:1px solid #e0e7ff;">Tingkat 4</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#eef2ff;">
                                <i class="fa-solid fa-chart-line" style="font-size:0.8rem;color:#6366f1;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($tier5M10M ?? 0) ?></div>
                        <div class="fw-semibold text-truncate small" style="color:#4338ca;">Rp 5 - 10 Juta</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalScope ?? 0) > 0 ? round((($tier5M10M ?? 0) / $totalScope) * 100, 1) : 0 ?>% dari total</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tier 5: > 10 Juta -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= $baseQsInd('> Rp10.000.000') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['pendapatan_range'] ?? '') === '> Rp10.000.000' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #10b981 !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge" style="background:#ecfdf5;color:#047857;border:1px solid #d1fae5;">Tingkat 5</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#ecfdf5;">
                                <i class="fa-solid fa-gem text-success" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($tierAbove10M ?? 0) ?></div>
                        <div class="fw-semibold text-truncate small" style="color:#047857;">&gt; Rp 10 Juta</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalScope ?? 0) > 0 ? round((($tierAbove10M ?? 0) / $totalScope) * 100, 1) : 0 ?>% dari total</div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Tier 6: Belum Mengisi -->
        <div class="col-6 col-md-4 col-xl-2">
            <a href="<?= $baseQsInd('belum_mengisi') ?>" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 kpi-card-hover <?= ($filter['pendapatan_range'] ?? '') === 'belum_mengisi' ? 'active-kpi' : '' ?>" style="border-top: 4px solid #9ca3af !important;">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-light text-muted border">Belum</span>
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:28px;height:28px;background:#f3f4f6;">
                                <i class="fa-regular fa-circle-question text-muted" style="font-size:0.8rem;"></i>
                            </div>
                        </div>
                        <div class="fw-bold fs-4 text-dark"><?= number_format($tierUnfilled ?? 0) ?></div>
                        <div class="fw-semibold text-truncate small text-secondary">Belum Mengisi</div>
                        <div class="text-muted" style="font-size:0.7rem;"><?= ($totalScope ?? 0) > 0 ? round((($tierUnfilled ?? 0) / $totalScope) * 100, 1) : 0 ?>% dari total</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- VISUAL CHART & PROFESI INDIVIDU -->
    <div class="row g-3 mb-4">
        <!-- Donut Distribusi Pendapatan -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header bg-transparent border-0 pt-3 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark-coffee mb-0">
                        <i class="fa-solid fa-chart-pie text-gold me-2"></i>Komposisi Pendapatan per Bulan
                    </h6>
                    <small class="text-muted">Cakupan: <?= ($filter['status_anggota'] ?? 'pelopor') === 'pelopor' ? 'Anggota Pelopor' : 'Semua Anggota' ?></small>
                </div>
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-5 text-center mb-3 mb-md-0">
                            <canvas id="incomePieChart" style="max-height: 200px; max-width: 200px; margin: 0 auto;"></canvas>
                        </div>
                        <div class="col-md-7">
                            <div class="d-flex flex-column gap-2 small">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#10b981;font-size:0.6rem;"></i>&gt; Rp 10 Juta</span>
                                    <span class="fw-bold"><?= number_format($tierAbove10M ?? 0) ?> org <small class="text-muted">(<?= ($totalScope??0)>0?round((($tierAbove10M??0)/$totalScope)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#6366f1;font-size:0.6rem;"></i>Rp 5 - 10 Juta</span>
                                    <span class="fw-bold"><?= number_format($tier5M10M ?? 0) ?> org <small class="text-muted">(<?= ($totalScope??0)>0?round((($tier5M10M??0)/$totalScope)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#0284c7;font-size:0.6rem;"></i>Rp 3 - 5 Juta</span>
                                    <span class="fw-bold"><?= number_format($tier3M5M ?? 0) ?> org <small class="text-muted">(<?= ($totalScope??0)>0?round((($tier3M5M??0)/$totalScope)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#eab308;font-size:0.6rem;"></i>Rp 1 - 3 Juta</span>
                                    <span class="fw-bold"><?= number_format($tier1M3M ?? 0) ?> org <small class="text-muted">(<?= ($totalScope??0)>0?round((($tier1M3M??0)/$totalScope)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#f97316;font-size:0.6rem;"></i>&lt; Rp 1 Juta</span>
                                    <span class="fw-bold"><?= number_format($tierUnder1M ?? 0) ?> org <small class="text-muted">(<?= ($totalScope??0)>0?round((($tierUnder1M??0)/$totalScope)*100,1):0 ?>%)</small></span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between text-muted border-top pt-1">
                                    <span><i class="fa-solid fa-circle me-1" style="color:#9ca3af;font-size:0.6rem;"></i>Belum Mengisi</span>
                                    <span class="fw-semibold"><?= number_format($tierUnfilled ?? 0) ?> org <small>(<?= ($totalScope??0)>0?round((($tierUnfilled??0)/$totalScope)*100,1):0 ?>%)</small></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profesi Terbanyak -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius:12px;">
                <div class="card-header bg-transparent border-0 pt-3 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <h6 class="fw-bold text-dark-coffee mb-0">
                        <i class="fa-solid fa-briefcase text-gold me-2"></i>Profesi / Pekerjaan Utama Anggota
                    </h6>
                    <small class="text-muted">Top 8 Profesi</small>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0 no-datatable">
                            <tbody>
                                <?php if (!empty($topProfessions)): ?>
                                    <?php $maxCount = max($topProfessions); ?>
                                    <?php foreach ($topProfessions as $pekName => $pekCount): 
                                        $pctBar = $maxCount > 0 ? ($pekCount / $maxCount) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td class="fw-semibold small text-truncate" style="max-width:140px; color:var(--dark-coffee);">
                                            <?= esc($pekName) ?>
                                        </td>
                                        <td style="width: 50%;">
                                            <div class="progress" style="height: 8px; border-radius: 4px; background-color: #f5f0e8;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $pctBar ?>%; background-color: #3e2723; border-radius: 4px;"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold small text-nowrap" style="width: 25%;">
                                            <?= number_format($pekCount) ?> orang
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td class="text-center text-muted py-3">Belum ada data profesi</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER BAR INDIVIDU -->
    <div class="card border-0 shadow-sm mb-3" style="background:#fff; border-radius:12px;">
        <div class="card-body p-3">
            <form method="GET" action="<?= base_url('admin/members/ekonomi') ?>" class="row g-2 align-items-end">
                <input type="hidden" name="mode" value="individu">

                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label small mb-1 fw-semibold text-muted">Cari Nama / Instansi</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Ketik nama atau kantor..." value="<?= esc($filter['search'] ?? '') ?>">
                    </div>
                </div>

                <!-- Rentang Pendapatan -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Rentang Pendapatan</label>
                    <select name="pendapatan_range" class="form-select form-select-sm">
                        <option value="">Semua Rentang</option>
                        <option value="< Rp1.000.000" <?= ($filter['pendapatan_range'] ?? '') === '< Rp1.000.000' ? 'selected' : '' ?>>&lt; Rp 1.000.000</option>
                        <option value="Rp1.000.000 - Rp3.000.000" <?= ($filter['pendapatan_range'] ?? '') === 'Rp1.000.000 - Rp3.000.000' ? 'selected' : '' ?>>Rp 1.000.000 - Rp 3.000.000</option>
                        <option value="Rp3.000.000 - Rp5.000.000" <?= ($filter['pendapatan_range'] ?? '') === 'Rp3.000.000 - Rp5.000.000' ? 'selected' : '' ?>>Rp 3.000.000 - Rp 5.000.000</option>
                        <option value="Rp5.000.000 - Rp10.000.000" <?= ($filter['pendapatan_range'] ?? '') === 'Rp5.000.000 - Rp10.000.000' ? 'selected' : '' ?>>Rp 5.000.000 - Rp 10.000.000</option>
                        <option value="> Rp10.000.000" <?= ($filter['pendapatan_range'] ?? '') === '> Rp10.000.000' ? 'selected' : '' ?>>&gt; Rp 10.000.000</option>
                        <option value="belum_mengisi" <?= ($filter['pendapatan_range'] ?? '') === 'belum_mengisi' ? 'selected' : '' ?>>Belum Mengisi Data</option>
                    </select>
                </div>

                <!-- Status Anggota -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Jenjang Anggota</label>
                    <select name="status_anggota" class="form-select form-select-sm">
                        <option value="pelopor" <?= ($filter['status_anggota'] ?? '') === 'pelopor' ? 'selected' : '' ?>>Pelopor (Madya/Dewasa/Utama)</option>
                        <option value="all" <?= ($filter['status_anggota'] ?? '') === 'all' ? 'selected' : '' ?>>Semua Jenjang</option>
                        <option value="Utama"  <?= ($filter['status_anggota'] ?? '') === 'Utama'  ? 'selected' : '' ?>>Utama</option>
                        <option value="Dewasa" <?= ($filter['status_anggota'] ?? '') === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                        <option value="Madya"  <?= ($filter['status_anggota'] ?? '') === 'Madya'  ? 'selected' : '' ?>>Madya</option>
                        <option value="Muayyid" <?= ($filter['status_anggota'] ?? '') === 'Muayyid' ? 'selected' : '' ?>>Muayyid</option>
                        <option value="Muhib"   <?= ($filter['status_anggota'] ?? '') === 'Muhib'   ? 'selected' : '' ?>>Muhib</option>
                        <option value="Belum Tarbiyah" <?= ($filter['status_anggota'] ?? '') === 'Belum Tarbiyah' ? 'selected' : '' ?>>Belum Tarbiyah</option>
                        <option value="non_pelopor" <?= ($filter['status_anggota'] ?? '') === 'non_pelopor' ? 'selected' : '' ?>>Non-Pelopor</option>
                    </select>
                </div>

                <!-- UPA -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Unit Pembinaan (UPA)</label>
                    <select name="kode_upa" class="form-select form-select-sm">
                        <option value="">Semua UPA</option>
                        <?php if (!empty($regions)): ?>
                            <?php foreach ($regions as $u): ?>
                            <option value="<?= esc($u['kode_upa']) ?>" <?= ($filter['kode_upa'] ?? '') === $u['kode_upa'] ? 'selected' : '' ?>>
                                <?= esc($u['kode_upa']) ?> - <?= esc($u['kelurahan'] ?? '') ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Kelurahan -->
                <div class="col-md-2">
                    <label class="form-label small mb-1 fw-semibold text-muted">Kelurahan</label>
                    <select name="kelurahan_id" class="form-select form-select-sm">
                        <option value="">Semua Kelurahan</option>
                        <?php if (!empty($villages)): ?>
                            <?php foreach ($villages as $v): ?>
                            <option value="<?= esc($v['id']) ?>" <?= (string)($filter['kelurahan_id'] ?? '') === (string)$v['id'] ? 'selected' : '' ?>>
                                <?= esc(ucwords(strtolower($v['name']))) ?>
                            </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-sm btn-dark-coffee w-100 fw-semibold" title="Terapkan Filter">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                    <a href="<?= base_url('admin/members/ekonomi?mode=individu') ?>" class="btn btn-sm btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- DATA TABLE INDIVIDU -->
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h6 class="fw-bold text-dark-coffee mb-0">
                    <i class="fa-solid fa-list-check text-gold me-2"></i>Daftar Profil Ekonomi Anggota
                </h6>
                <small class="text-muted">Menampilkan <?= number_format(count($members ?? [])) ?> anggota dari filter yang dipilih</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-dark-coffee border">
                    <i class="fa-solid fa-arrow-down-wide-short text-gold me-1"></i>Urutan: Pendapatan Terisi &rarr; Tertinggi ke Terendah
                </span>
            </div>
        </div>
        <div class="card-body px-4 pb-4 pt-2">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableIndividu" style="font-size:0.875rem;">
                    <thead style="background:#efebe9;">
                        <tr class="text-dark-coffee">
                            <th class="text-center" style="width:40px;">No</th>
                            <th style="min-width:220px;">Anggota</th>
                            <th style="min-width:110px;">Jenjang</th>
                            <th style="min-width:130px;">UPA / Kelurahan</th>
                            <th style="min-width:180px;">Pekerjaan & Instansi</th>
                            <th style="min-width:150px;">Bidang Usaha</th>
                            <th class="text-center" style="min-width:150px;">Rentang Pendapatan</th>
                            <th class="text-center" style="width:70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($members)): ?>
                            <?php 
                                $no = 1;
                                foreach ($members as $m): 
                                    $rng = trim((string)($m['pendapatan_range'] ?? ''));
                                    $badgeStyle = 'background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb;';
                                    $badgeIcon  = 'fa-circle-question';
                                    $badgeLabel = $rng ?: 'Belum Mengisi';

                                    if ($rng === '< Rp1.000.000') {
                                        $badgeStyle = 'background:#fff7ed; color:#c2410c; border:1px solid #fed7aa;';
                                        $badgeIcon  = 'fa-coins';
                                    } elseif ($rng === 'Rp1.000.000 - Rp3.000.000') {
                                        $badgeStyle = 'background:#fefce8; color:#a16207; border:1px solid #fef08a;';
                                        $badgeIcon  = 'fa-wallet';
                                    } elseif ($rng === 'Rp3.000.000 - Rp5.000.000') {
                                        $badgeStyle = 'background:#f0f9ff; color:#0369a1; border:1px solid #bae6fd;';
                                        $badgeIcon  = 'fa-briefcase';
                                    } elseif ($rng === 'Rp5.000.000 - Rp10.000.000') {
                                        $badgeStyle = 'background:#eef2ff; color:#4338ca; border:1px solid #c7d2fe;';
                                        $badgeIcon  = 'fa-chart-line';
                                    } elseif ($rng === '> Rp10.000.000') {
                                        $badgeStyle = 'background:#ecfdf5; color:#047857; border:1px solid #a7f3d0;';
                                        $badgeIcon  = 'fa-gem';
                                    }

                                    $phoneClean = preg_replace('/[^0-9]/', '', (string)($m['handphone'] ?? ''));
                                    if (str_starts_with($phoneClean, '0')) {
                                        $phoneClean = '62' . substr($phoneClean, 1);
                                    }
                            ?>
                            <tr>
                                <td class="text-center text-muted small fw-semibold"><?= $no++ ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm flex-shrink-0" 
                                             style="width:36px; height:36px; background-color:#3e2723; font-size:0.85rem;">
                                            <?= strtoupper(substr(trim($m['nama_lengkap']), 0, 2)) ?>
                                        </div>
                                        <div class="lh-sm">
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="<?= base_url('admin/members/edit/' . $m['id']) ?>" class="fw-bold text-dark-coffee text-decoration-none hover-gold" title="Lihat profil lengkap">
                                                    <?= esc($m['nama_lengkap']) ?>
                                                </a>
                                                <?php if (!empty($phoneClean)): ?>
                                                    <a href="https://wa.me/<?= $phoneClean ?>" target="_blank" class="text-success ms-1" title="Kirim Pesan WhatsApp">
                                                        <i class="fa-brands fa-whatsapp"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small mt-1">
                                                <?= $m['jenis_kelamin'] === 'L' ? '<i class="fa-solid fa-mars text-primary me-1" title="Laki-laki"></i>' : ($m['jenis_kelamin'] === 'P' ? '<i class="fa-solid fa-venus text-danger me-1" title="Perempuan"></i>' : '') ?>
                                                <span class="text-muted" style="font-size:0.75rem;"><?= esc($m['handphone'] ?: '-') ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php 
                                        $st = $m['status_anggota'] ?? '';
                                        $stClass = in_array($st, ['Madya', 'Dewasa', 'Utama']) ? 'bg-dark-coffee text-cream' : 'bg-light text-secondary border';
                                    ?>
                                    <span class="badge <?= $stClass ?> px-2 py-1 fw-semibold" style="font-size:0.75rem;">
                                        <?= esc($st ?: 'Non-Pelopor') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark-coffee small">
                                        <?= esc($m['kode_upa'] ? 'UPA: ' . $m['kode_upa'] : 'Tanpa UPA') ?>
                                    </div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        <?= esc($m['kelurahan'] ? ucwords(strtolower($m['kelurahan'])) : '-') ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark small">
                                        <?= esc($m['pekerjaan'] ?: '-') ?>
                                    </div>
                                    <?php if (!empty($m['nama_instansi'])): ?>
                                        <div class="text-muted text-truncate" style="max-width:180px; font-size:0.75rem;">
                                            <i class="fa-regular fa-building me-1 text-gold"></i><?= esc($m['nama_instansi']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small text-muted">
                                        <?= esc($m['bidang_pekerjaan'] ?: '-') ?>
                                    </div>
                                    <?php if (!empty($m['keahlian'])): ?>
                                        <div class="text-secondary small fst-italic text-truncate" style="max-width:150px; font-size:0.7rem;" title="<?= esc($m['keahlian']) ?>">
                                            <i class="fa-solid fa-star text-gold me-1"></i><?= esc($m['keahlian']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge px-3 py-2 fw-semibold" style="<?= $badgeStyle ?> font-size:0.75rem;">
                                        <i class="fa-solid <?= $badgeIcon ?> me-1"></i><?= esc($badgeLabel) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($can_update ?? false): ?>
                                    <button type="button" class="btn btn-sm btn-outline-secondary btn-quick-edit py-1 px-2" 
                                            data-id="<?= $m['id'] ?>"
                                            data-nama="<?= esc($m['nama_lengkap']) ?>"
                                            data-range="<?= esc($m['pendapatan_range'] ?? '') ?>"
                                            data-pekerjaan="<?= esc($m['pekerjaan'] ?? '') ?>"
                                            data-instansi="<?= esc($m['nama_instansi'] ?? '') ?>"
                                            data-bidang="<?= esc($m['bidang_pekerjaan'] ?? '') ?>"
                                            title="Quick Edit Pendapatan">
                                        <i class="fa-solid fa-pen text-gold"></i>
                                    </button>
                                    <?php else: ?>
                                    <a href="<?= base_url('admin/members/edit/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Detail">
                                        <i class="fa-solid fa-eye text-gold"></i>
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-circle-exclamation fs-2 mb-2 d-block text-secondary"></i>
                                    Tidak ada data anggota yang cocok dengan filter.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL QUICK EDIT PENDAPATAN INDIVIDU -->
    <div class="modal fade" id="quickEditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 text-white" style="background-color:#3e2723;">
                    <h5 class="modal-title fw-bold" style="font-family:'Playfair Display',serif;">
                        <i class="fa-solid fa-sack-dollar text-gold me-2"></i>Update Data Ekonomi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="quickEditForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="member_id" id="edit_member_id">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Nama Anggota</label>
                            <input type="text" class="form-control fw-bold bg-light" id="edit_nama_lengkap" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-dark-coffee">Rentang Pendapatan per Bulan <span class="text-danger">*</span></label>
                            <select name="pendapatan_range" id="edit_pendapatan_range" class="form-select" required>
                                <option value="">-- Pilih Rentang Pendapatan --</option>
                                <option value="< Rp1.000.000">&lt; Rp 1.000.000</option>
                                <option value="Rp1.000.000 - Rp3.000.000">Rp 1.000.000 - Rp 3.000.000</option>
                                <option value="Rp3.000.000 - Rp5.000.000">Rp 3.000.000 - Rp 5.000.000</option>
                                <option value="Rp5.000.000 - Rp10.000.000">Rp 5.000.000 - Rp 10.000.000</option>
                                <option value="> Rp10.000.000">&gt; Rp 10.000.000</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Profesi / Pekerjaan Utama</label>
                            <input type="text" name="pekerjaan" id="edit_pekerjaan" class="form-control" placeholder="Contoh: Karyawan Swasta, Wiraswasta, Guru">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Nama Instansi / Perusahaan</label>
                            <input type="text" name="nama_instansi" id="edit_nama_instansi" class="form-control" placeholder="Nama kantor atau usaha">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold text-muted">Bidang Pekerjaan / Usaha</label>
                            <input type="text" name="bidang_pekerjaan" id="edit_bidang_pekerjaan" class="form-control" placeholder="Contoh: Teknologi, Pendidikan, Perdagangan">
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-dark-coffee fw-semibold" id="btnSaveQuickEdit">
                            <i class="fa-solid fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php endif; ?>

<style>
.text-dark-coffee { color: #3e2723 !important; }
.bg-dark-coffee { background-color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; border:none; }
.btn-gold:hover { background-color: #c49f27 !important; color: #fff !important; }
.btn-dark-coffee { background-color: #3e2723 !important; color: #fff !important; border:none; }
.btn-dark-coffee:hover, .btn-dark-coffee.active { background-color: #27140f !important; color: #fff !important; }
.hover-gold:hover { color: #d4af37 !important; }

.kpi-card-hover {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}
.kpi-card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
.kpi-card-hover.active-kpi {
    transform: translateY(-2px);
    box-shadow: 0 0 0 2px #d4af37, 0 8px 16px rgba(0,0,0,0.12) !important;
}
.fs-7 { font-size: 0.85rem !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function() {
    <?php if ($currentMode === 'keluarga'): ?>
        // ── KELUARGA (KK) MODE JS ──
        if ($.fn.DataTable && $('#tableKK').length) {
            if (!$.fn.DataTable.isDataTable('#tableKK')) {
                $('#tableKK').DataTable({
                    pageLength: 25,
                    responsive: true,
                    language: {
                        search: "Cari di tabel:",
                        lengthMenu: "Tampilkan _MENU_ KK",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ KK",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 KK",
                        infoFiltered: "(disaring dari _MAX_ total KK)",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "&rarr;",
                            previous: "&larr;"
                        }
                    }
                });
            }
        }

        const ctxW = document.getElementById('welfarePieChart');
        if (ctxW) {
            new Chart(ctxW, {
                type: 'doughnut',
                data: {
                    labels: ['Sejahtera', 'Menengah', 'Rentan', 'Prasejahtera', 'Belum Terdata'],
                    datasets: [{
                        data: [
                            <?= (int)($welfareCounts['sejahtera'] ?? 0) ?>,
                            <?= (int)($welfareCounts['menengah'] ?? 0) ?>,
                            <?= (int)($welfareCounts['rentan'] ?? 0) ?>,
                            <?= (int)($welfareCounts['prasejahtera'] ?? 0) ?>,
                            <?= (int)($welfareCounts['belum'] ?? 0) ?>
                        ],
                        backgroundColor: ['#059669', '#0284c7', '#ea580c', '#dc2626', '#9ca3af'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = <?= (int)($totalKK ?? 0) ?>;
                                    const val = context.parsed;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${val} KK (${pct}%)`;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        $('.btn-detail-kk').on('click', function() {
            const data = $(this).data('head');
            if (!data) return;

            let welfareBadge = '';
            if (data.welfare === 'prasejahtera') welfareBadge = '<span class="badge bg-danger">Prasejahtera (&lt; Rp 800 rb/jiwa)</span>';
            else if (data.welfare === 'rentan') welfareBadge = '<span class="badge bg-warning text-dark">Rentan (Rp 800 rb – 1,5 jt/jiwa)</span>';
            else if (data.welfare === 'menengah') welfareBadge = '<span class="badge bg-info text-white">Menengah (Rp 1,5 – 3 jt/jiwa)</span>';
            else if (data.welfare === 'sejahtera') welfareBadge = '<span class="badge bg-success">Sejahtera (&ge; Rp 3 jt/jiwa)</span>';
            else welfareBadge = '<span class="badge bg-secondary">Belum Terdata</span>';

            let html = `
                <div class="d-flex align-items-center justify-content-between pb-3 border-bottom mb-3 flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold text-dark-coffee mb-0">${data.head_name}</h5>
                        <small class="text-muted">Kelurahan: ${data.kelurahan || '-'} &bull; UPA: ${data.kode_upa || '-'} &bull; RW ${data.rw || '-'} / RT ${data.rt || '-'}</small>
                    </div>
                    <div>${welfareBadge}</div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="text-muted small">Total Jiwa dalam KK</div>
                            <div class="fw-bold fs-4 text-dark-coffee">${data.family_size} Jiwa</div>
                            <small class="text-muted">${data.children_count} anak terdata</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="text-muted small">Total Pendapatan KK</div>
                            <div class="fw-bold fs-4 text-success">${data.total_income ? 'Rp ' + Number(data.total_income).toLocaleString('id-ID') : '-'}</div>
                            <small class="text-muted">Kepala + Pasangan</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <div class="text-muted small">Pendapatan per Kapita</div>
                            <div class="fw-bold fs-4 text-primary">${data.per_capita ? 'Rp ' + Number(Math.round(data.per_capita)).toLocaleString('id-ID') : '-'}</div>
                            <small class="text-muted">per jiwa / bulan</small>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold text-dark-coffee mb-2"><i class="fa-solid fa-users text-gold me-2"></i>Susunan Anggota Keluarga:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered align-middle mb-0">
                        <thead style="background:#efebe9;">
                            <tr>
                                <th>Posisi</th>
                                <th>Nama Lengkap</th>
                                <th>Pekerjaan</th>
                                <th>Rentang Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-bold"><span class="badge bg-primary">Kepala Keluarga</span></td>
                                <td class="fw-semibold">${data.head_name}</td>
                                <td>${data.head_job || '-'}</td>
                                <td class="fw-bold text-success">${data.head_income_range || 'Belum Mengisi'}</td>
                            </tr>
                            ${data.spouse_name ? `
                            <tr>
                                <td class="fw-bold"><span class="badge bg-warning text-dark">Pasangan (Istri/Suami)</span></td>
                                <td class="fw-semibold">${data.spouse_name}</td>
                                <td>${data.spouse_job || '-'}</td>
                                <td class="fw-bold text-success">${data.spouse_income_range || 'Belum Mengisi'}</td>
                            </tr>
                            ` : (data.spouse_unregistered ? `
                            <tr>
                                <td class="fw-bold"><span class="badge bg-secondary">Pasangan</span></td>
                                <td class="text-muted fst-italic">Tercatat Menikah, pasangan belum terdaftar</td>
                                <td>-</td>
                                <td class="text-muted">-</td>
                            </tr>
                            ` : '')}
                            ${data.children && data.children.length > 0 ? data.children.map((c, idx) => `
                            <tr>
                                <td><span class="badge bg-light text-dark-coffee border">Anak ke-${idx + 1}</span></td>
                                <td>${c}</td>
                                <td class="text-muted">Tanggungan</td>
                                <td class="text-muted">-</td>
                            </tr>
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            `;

            $('#modalDetailKKBody').html(html);
            $('#modalDetailKK').modal('show');
        });

    <?php else: ?>
        // ── INDIVIDU MODE JS ──
        if ($.fn.DataTable && $('#tableIndividu').length) {
            if (!$.fn.DataTable.isDataTable('#tableIndividu')) {
                $('#tableIndividu').DataTable({
                    pageLength: 25,
                    responsive: true,
                    language: {
                        search: "Cari di tabel:",
                        lengthMenu: "Tampilkan _MENU_ data",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        paginate: {
                            first: "Awal",
                            last: "Akhir",
                            next: "&rarr;",
                            previous: "&larr;"
                        }
                    }
                });
            }
        }

        const ctxI = document.getElementById('incomePieChart');
        if (ctxI) {
            new Chart(ctxI, {
                type: 'doughnut',
                data: {
                    labels: ['> Rp 10 Juta', 'Rp 5 - 10 Juta', 'Rp 3 - 5 Juta', 'Rp 1 - 3 Juta', '< Rp 1 Juta', 'Belum Mengisi'],
                    datasets: [{
                        data: [
                            <?= (int)($tierAbove10M ?? 0) ?>,
                            <?= (int)($tier5M10M ?? 0) ?>,
                            <?= (int)($tier3M5M ?? 0) ?>,
                            <?= (int)($tier1M3M ?? 0) ?>,
                            <?= (int)($tierUnder1M ?? 0) ?>,
                            <?= (int)($tierUnfilled ?? 0) ?>
                        ],
                        backgroundColor: ['#10b981', '#6366f1', '#0284c7', '#eab308', '#f97316', '#9ca3af'],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const total = <?= (int)($totalScope ?? 0) ?>;
                                    const val = context.parsed;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return `${context.label}: ${val} orang (${pct}%)`;
                                }
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }

        $('.btn-quick-edit').on('click', function() {
            const id       = $(this).data('id');
            const nama     = $(this).data('nama');
            const range    = $(this).data('range');
            const pek      = $(this).data('pekerjaan');
            const instansi = $(this).data('instansi');
            const bidang   = $(this).data('bidang');

            $('#edit_member_id').val(id);
            $('#edit_nama_lengkap').val(nama);
            $('#edit_pendapatan_range').val(range);
            $('#edit_pekerjaan').val(pek);
            $('#edit_nama_instansi').val(instansi);
            $('#edit_bidang_pekerjaan').val(bidang);

            $('#quickEditModal').modal('show');
        });

        $('#quickEditForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $('#btnSaveQuickEdit');
            const origText = btn.html();
            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>Menyimpan...');

            $.ajax({
                url: '<?= base_url('admin/members/ekonomi/quick-update') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    btn.prop('disabled', false).html(origText);
                    if (resp.status === 'success') {
                        $('#quickEditModal').modal('hide');
                        alert(resp.message);
                        location.reload();
                    } else {
                        alert(resp.message || 'Gagal menyimpan perubahan.');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).html(origText);
                    alert('Terjadi kesalahan saat menyimpan data. Silakan coba lagi.');
                }
            });
        });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
