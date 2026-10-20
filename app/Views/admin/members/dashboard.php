<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<!-- ===== NAV TABS / PILLS ===== -->
<div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="<?= base_url('admin/members') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-users me-1 text-gold"></i>Daftar Anggota
        </a>
        <a href="<?= base_url('admin/members/ekonomi') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-sack-dollar me-1 text-gold"></i>Data Ekonomi Anggota
        </a>
        <a href="<?= base_url('admin/members/pendidikan') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-graduation-cap me-1 text-gold"></i>Pendidikan Anggota
        </a>
        <a href="<?= base_url('admin/members/dashboard') ?>" class="btn btn-sm fw-semibold px-3 active shadow-sm" style="border-radius:20px; background:var(--dark-coffee); color:#fff; border:none;">
            <i class="fa-solid fa-chart-pie me-1 text-gold"></i>Statistik & Dashboard
        </a>
        <a href="<?= base_url('admin/upa') ?>" class="btn btn-sm btn-outline-secondary fw-semibold px-3" style="border-radius:20px;">
            <i class="fa-solid fa-people-group me-1 text-gold"></i>Group UPA
        </a>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-chart-line me-2 text-gold"></i>Dashboard Analisis Anggota
        </h4>
        <p class="text-secondary small mb-0">Statistik dan analisis data demografi, profesi, pendidikan, dan pendapatan anggota komunitas</p>
    </div>
    
    <!-- Filter Buttons with Counts -->
    <?php
        $kelQs = $kelurahanId !== '' ? '&kelurahan_id=' . urlencode($kelurahanId) : '';
    ?>
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <form method="get" action="<?= base_url('admin/members/dashboard') ?>" class="d-flex align-items-center gap-2 m-0">
            <input type="hidden" name="type" value="<?= esc($type) ?>">
            <div class="input-group shadow-sm" style="min-width: 240px;">
                <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-map-location-dot text-gold"></i></span>
                <select name="kelurahan_id" class="form-select border-start-0 fw-semibold text-dark-coffee" onchange="this.form.submit()">
                    <option value="">Semua Kelurahan</option>
                    <?php foreach ($kelurahanOptions as $opt): ?>
                        <option value="<?= $opt['id'] ?>" <?= (string)$opt['id'] === $kelurahanId ? 'selected' : '' ?>>
                            <?= esc(ucwords(strtolower($opt['name']))) ?> (<?= $opt['total'] ?>)
                        </option>
                    <?php endforeach; ?>
                    <?php if ($noKelurahanCount > 0): ?>
                        <option value="none" <?= $kelurahanId === 'none' ? 'selected' : '' ?>>Tanpa Kelurahan (<?= $noKelurahanCount ?>)</option>
                    <?php endif; ?>
                </select>
            </div>
        </form>
        <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
            <a href="<?= base_url('admin/members/dashboard?type=all' . $kelQs) ?>" class="btn <?= $type === 'all' ? 'btn-dark-coffee active' : 'btn-light text-dark-coffee border' ?> fw-semibold py-2 px-3 text-decoration-none">
                Semua (<?= $totalMembersRaw ?>)
            </a>
            <a href="<?= base_url('admin/members/dashboard?type=pelopor' . $kelQs) ?>" class="btn <?= $type === 'pelopor' ? 'btn-dark-coffee active' : 'btn-light text-dark-coffee border' ?> fw-semibold py-2 px-3 text-decoration-none">
                Pelopor (<?= $totalPelopor ?>)
            </a>
            <a href="<?= base_url('admin/members/dashboard?type=non_pelopor' . $kelQs) ?>" class="btn <?= $type === 'non_pelopor' ? 'btn-dark-coffee active' : 'btn-light text-dark-coffee border' ?> fw-semibold py-2 px-3 text-decoration-none">
                Non-Pelopor (<?= $totalNonPelopor ?>)
            </a>
        </div>
        <a href="<?= base_url('admin/members') ?>" class="btn btn-outline-secondary py-2">
            <i class="fa-solid fa-users me-2"></i>Kelola
        </a>
    </div>
</div>

<?php if ($selectedKelurahanName !== ''): ?>
<div class="alert d-flex align-items-center justify-content-between py-2 px-3 mb-4 shadow-sm" style="background:#fff8e1; border:1px solid #d4af37; border-radius:10px;">
    <div class="text-dark-coffee">
        <i class="fa-solid fa-filter text-gold me-2"></i>Menampilkan statistik untuk <strong>Kelurahan <?= esc($selectedKelurahanName) ?></strong>
    </div>
    <a href="<?= base_url('admin/members/dashboard?type=' . urlencode($type)) ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-xmark me-1"></i>Reset Kelurahan
    </a>
</div>
<?php endif; ?>

<!-- BREAKDOWN PER KELURAHAN -->
<?php
    $grandTotal = array_sum(array_column($kelurahanBreakdown, 'total'));
?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-map-location-dot text-gold me-2"></i>Breakdown Anggota per Kelurahan
            <span class="text-muted small fw-normal">(klik baris untuk filter)</span>
        </h6>
        <span class="badge bg-light text-dark-coffee border"><?= count($kelurahanBreakdown) ?> wilayah · <?= number_format($grandTotal) ?> anggota</span>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 no-datatable small">
                <thead style="background:#efebe9;">
                    <tr class="text-dark-coffee">
                        <th>Kelurahan</th>
                        <th class="text-center">Total</th>
                        <th style="min-width:140px;">Proporsi</th>
                        <th class="text-center">Pelopor</th>
                        <th class="text-center">L / P</th>
                        <th class="text-center">Akun Aktif</th>
                        <th class="text-center">Mentoring</th>
                        <th class="text-center">Pendapatan Terisi</th>
                        <th class="text-center">Pendapatan &ge; 5jt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kelurahanBreakdown as $row):
                        $rowKelId  = empty($row['kelurahan_id']) ? 'none' : (string)$row['kelurahan_id'];
                        $rowName   = empty($row['kelurahan']) ? 'Tanpa Kelurahan' : ucwords(strtolower($row['kelurahan']));
                        $pct       = $grandTotal > 0 ? ($row['total'] / $grandTotal) * 100 : 0;
                        $isActive  = $rowKelId === $kelurahanId;
                        $rowUrl    = base_url('admin/members/dashboard?type=' . urlencode($type) . ($isActive ? '' : '&kelurahan_id=' . urlencode($rowKelId)));
                        $incPct    = $row['total'] > 0 ? round(($row['pendapatan_terisi'] / $row['total']) * 100) : 0;
                    ?>
                    <tr onclick="window.location='<?= $rowUrl ?>'" style="cursor:pointer; <?= $isActive ? 'background:#fff8e1;' : '' ?>">
                        <td class="fw-semibold text-dark-coffee">
                            <?php if ($isActive): ?><i class="fa-solid fa-circle-check text-gold me-1"></i><?php endif; ?>
                            <?= esc($rowName) ?>
                        </td>
                        <td class="text-center fw-bold"><?= number_format($row['total']) ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;">
                                    <div class="progress-bar" style="width: <?= $pct ?>%; background-color:#3e2723;"></div>
                                </div>
                                <span class="text-muted" style="font-size:.75rem;"><?= number_format($pct, 1) ?>%</span>
                            </div>
                        </td>
                        <td class="text-center"><?= number_format($row['pelopor']) ?></td>
                        <td class="text-center"><span class="text-primary"><?= $row['laki'] ?></span> / <span class="text-danger"><?= $row['perempuan'] ?></span></td>
                        <td class="text-center"><?= number_format($row['aktif']) ?></td>
                        <td class="text-center"><?= number_format($row['mentoring']) ?></td>
                        <td class="text-center"><?= number_format($row['pendapatan_terisi']) ?> <span class="text-muted">(<?= $incPct ?>%)</span></td>
                        <td class="text-center"><?= number_format($row['pendapatan_atas_5jt']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ROW 1: SUMMARY KPI CARDS -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Members -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #3e2723 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Total Anggota</span>
                        <h3 class="fw-bold text-dark-coffee mb-0"><?= number_format($totalMembers) ?></h3>
                    </div>
                    <div class="p-3 bg-light rounded-circle text-dark-coffee">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Active Accounts -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #198754 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Akun Aktif</span>
                        <h3 class="fw-bold text-success mb-0"><?= number_format($activeCount) ?></h3>
                    </div>
                    <div class="p-3 bg-light rounded-circle text-success">
                        <i class="fa-solid fa-user-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Invited Accounts -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #fd7e14 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Diundang (Belum Aktivasi)</span>
                        <h3 class="fw-bold text-warning mb-0"><?= number_format($invitedCount) ?></h3>
                    </div>
                    <div class="p-3 bg-light rounded-circle text-warning">
                        <i class="fa-solid fa-paper-plane fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: No Account -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #dc3545 !important;">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small fw-semibold d-block mb-1">Belum Punya Akun</span>
                        <h3 class="fw-bold text-danger mb-0"><?= number_format($noAccountCount) ?></h3>
                    </div>
                    <div class="p-3 bg-light rounded-circle text-danger">
                        <i class="fa-solid fa-user-xmark fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2: PROFESSIONS & EDUCATION -->
<div class="row g-4 mb-4">
    <!-- 10 Profesi Terbanyak -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-briefcase text-gold me-2"></i>10 Profesi Terbanyak
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 no-datatable">
                        <tbody>
                            <?php if (!empty($professions)): ?>
                                <?php foreach ($professions as $idx => $p): 
                                    $percent = $totalMembers > 0 ? ($p['count'] / $totalMembers) * 100 : 0;
                                ?>
                                    <tr>
                                        <td class="text-muted fw-bold" style="width: 30px;"><?= $idx + 1 ?></td>
                                        <td class="fw-semibold text-dark-coffee" style="width: 180px;"><?= esc($p['pekerjaan']) ?></td>
                                        <td>
                                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #efebe9;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background-color: #3e2723; border-radius: 5px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold" style="width: 90px;">
                                            <?= number_format($p['count']) ?> <small class="text-muted" style="font-size: 0.7rem; font-weight: normal;"><?= number_format($percent, 1) ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center py-4 text-muted">Tidak ada data profesi anggota.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- 10 Pendidikan Terakhir -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-graduation-cap text-gold me-2"></i>10 Pendidikan Terbanyak
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 no-datatable">
                        <tbody>
                            <?php if (!empty($educations)): ?>
                                <?php foreach ($educations as $idx => $e): 
                                    $percent = $totalMembers > 0 ? ($e['count'] / $totalMembers) * 100 : 0;
                                ?>
                                    <tr>
                                        <td class="text-muted fw-bold" style="width: 30px;"><?= $idx + 1 ?></td>
                                        <td class="fw-semibold text-dark-coffee" style="width: 180px;"><?= esc($e['pendidikan_terakhir']) ?></td>
                                        <td>
                                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #efebe9;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background-color: #d4af37; border-radius: 5px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold" style="width: 90px;">
                                            <?= number_format($e['count']) ?> <small class="text-muted" style="font-size: 0.7rem; font-weight: normal;"><?= number_format($percent, 1) ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center py-4 text-muted">Tidak ada data pendidikan anggota.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 3: INCOME RANGE & AGE RANGE -->
<div class="row g-4 mb-4">
    <!-- Rentang Pendapatan -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-money-bill-wave text-gold me-2"></i>Rentang Pendapatan Anggota (Grafik)
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 no-datatable">
                        <tbody>
                            <?php if (!empty($incomes)): ?>
                                <?php foreach ($incomes as $idx => $i): 
                                    $percent = $totalMembers > 0 ? ($i['count'] / $totalMembers) * 100 : 0;
                                ?>
                                    <tr>
                                        <td class="text-muted fw-bold" style="width: 30px;"><?= $idx + 1 ?></td>
                                        <td class="fw-semibold text-dark-coffee" style="width: 220px;"><?= esc($i['pendapatan_range']) ?></td>
                                        <td>
                                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #efebe9;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background-color: #8d6e63; border-radius: 5px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold" style="width: 90px;">
                                            <?= number_format($i['count']) ?> <small class="text-muted" style="font-size: 0.7rem; font-weight: normal;"><?= number_format($percent, 1) ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center py-4 text-muted">Tidak ada data pendapatan anggota.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Rentang Usia -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-calendar-days text-gold me-2"></i>Distribusi Usia Anggota
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 no-datatable">
                        <tbody>
                            <?php if (!empty($ages)): ?>
                                <?php foreach ($ages as $idx => $a): 
                                    $percent = $totalMembers > 0 ? ($a['count'] / $totalMembers) * 100 : 0;
                                ?>
                                    <tr>
                                        <td class="text-muted fw-bold" style="width: 30px;"><?= $idx + 1 ?></td>
                                        <td class="fw-semibold text-dark-coffee" style="width: 180px;"><?= esc($a['usia_range']) ?> Tahun</td>
                                        <td>
                                            <div class="progress" style="height: 10px; border-radius: 5px; background-color: #efebe9;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $percent ?>%; background-color: #3e2723; border-radius: 5px;" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </td>
                                        <td class="text-end fw-bold" style="width: 90px;">
                                            <?= number_format($a['count']) ?> <small class="text-muted" style="font-size: 0.7rem; font-weight: normal;"><?= number_format($percent, 1) ?>%</small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="text-center py-4 text-muted">Tidak ada data rentang usia anggota.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 4: DEMOGRAPHICS (GENDER & MENTORING) -->
<div class="row g-4">
    <!-- Jenis Kelamin -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-venus-mars text-gold me-2"></i>Komposisi Jenis Kelamin
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-around align-items-center py-3">
                    <!-- Male Count -->
                    <div class="text-center">
                        <span class="badge bg-primary p-3 rounded-circle mb-2" style="font-size: 1.5rem;"><i class="fa-solid fa-mars"></i></span>
                        <h4 class="fw-bold text-dark-coffee mb-0"><?= number_format($maleCount) ?></h4>
                        <span class="text-muted small">Laki-Laki (<?= $totalMembers > 0 ? number_format(($maleCount / $totalMembers) * 100, 1) : 0 ?>%)</span>
                    </div>

                    <!-- Divider -->
                    <div style="width: 1px; height: 80px; background-color: #dee2e6;"></div>

                    <!-- Female Count -->
                    <div class="text-center">
                        <span class="badge bg-danger p-3 rounded-circle mb-2" style="font-size: 1.5rem;" style="background-color: #e91e63 !important;"><i class="fa-solid fa-venus"></i></span>
                        <h4 class="fw-bold text-dark-coffee mb-0"><?= number_format($femaleCount) ?></h4>
                        <span class="text-muted small">Perempuan (<?= $totalMembers > 0 ? number_format(($femaleCount / $totalMembers) * 100, 1) : 0 ?>%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keikutsertaan Mentoring -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-user-graduate text-gold me-2"></i>Partisipasi Mentoring / UPA
                </h5>
            </div>
            <div class="card-body p-4">
                <?php 
                    $mentoringPercent = $totalMembers > 0 ? ($mentoringCount / $totalMembers) * 100 : 0;
                    $notMentoringCount = $totalMembers - $mentoringCount;
                    $notMentoringPercent = 100 - $mentoringPercent;
                ?>
                <div class="d-flex justify-content-around align-items-center py-3">
                    <div class="text-center">
                        <span class="badge bg-success p-3 rounded-circle mb-2" style="font-size: 1.5rem;"><i class="fa-solid fa-graduation-cap"></i></span>
                        <h4 class="fw-bold text-dark-coffee mb-0"><?= number_format($mentoringCount) ?></h4>
                        <span class="text-muted small">Ikut Mentoring (<?= number_format($mentoringPercent, 1) ?>%)</span>
                    </div>

                    <div style="width: 1px; height: 80px; background-color: #dee2e6;"></div>

                    <div class="text-center">
                        <span class="badge bg-secondary p-3 rounded-circle mb-2" style="font-size: 1.5rem;"><i class="fa-solid fa-xmark"></i></span>
                        <h4 class="fw-bold text-dark-coffee mb-0"><?= number_format($notMentoringCount) ?></h4>
                        <span class="text-muted small">Belum Mentoring (<?= number_format($notMentoringPercent, 1) ?>%)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-dark-coffee {
        color: #3e2723 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .btn-dark-coffee {
        background-color: #3e2723 !important;
        color: #fff8e1 !important;
        border: none;
    }
    .btn-dark-coffee.active, .btn-dark-coffee:active, .btn-dark-coffee:hover {
        background-color: #27140f !important;
        color: #fff8e1 !important;
    }
</style>
<?= $this->endSection() ?>
