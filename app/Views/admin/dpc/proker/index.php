<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-list-check me-2 text-gold"></i>Matriks & Program Kerja Bidang DPC
        </h4>
        <p class="text-muted mb-0 small">Perencanaan program kerja 10 bidang DPC PKS Panmas, link & match wilayah, target KPI, dan skema reward.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/dpc/proker/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Excel Export
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/dpc/proker/create') ?>" class="btn btn-sm btn-gold px-4 fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Tambah Proker
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Tabs Navigation -->
<ul class="nav nav-pills mb-4 bg-white p-2 shadow-sm rounded-3">
    <li class="nav-item">
        <a class="nav-link <?= $viewMode === 'matriks' ? 'active' : '' ?>" href="<?= base_url('admin/dpc/proker?mode=matriks') ?>" style="<?= $viewMode === 'matriks' ? 'background-color: #3e2723 !important; color: #ffffff !important;' : 'color: #212529;' ?>">
            <i class="fa-solid fa-table-cells-large me-1 text-gold"></i>Matriks 10 Bidang
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $viewMode === 'prioritas' ? 'active' : '' ?>" href="<?= base_url('admin/dpc/proker?mode=prioritas') ?>" style="<?= $viewMode === 'prioritas' ? 'background-color: #3e2723 !important; color: #ffffff !important;' : 'color: #212529;' ?>">
            <i class="fa-solid fa-star me-1 text-warning"></i>Prioritas Pembinaan & Rekrutmen (7)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $viewMode === 'list' ? 'active' : '' ?>" href="<?= base_url('admin/dpc/proker?mode=list') ?>" style="<?= $viewMode === 'list' ? 'background-color: #3e2723 !important; color: #ffffff !important;' : 'color: #212529;' ?>">
            <i class="fa-solid fa-rectangle-list me-1"></i>Tabel Daftar Proker
        </a>
    </li>
</ul>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <input type="hidden" name="mode" value="<?= esc($viewMode) ?>">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Bidang DPC</label>
                <select name="bidang" class="form-select form-select-sm">
                    <option value="">-- Semua Bidang --</option>
                    <?php foreach ($bidangs as $b): ?>
                    <option value="<?= esc($b['kode']) ?>" <?= ($filters['bidang_kode'] ?? '') === $b['kode'] ? 'selected' : '' ?>>
                        <?= esc($b['kode']) ?> – <?= esc($b['nama_bidang']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status Monev</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="belum" <?= ($filters['status_monev'] ?? '') === 'belum' ? 'selected' : '' ?>>Belum Mulai</option>
                    <option value="berjalan" <?= ($filters['status_monev'] ?? '') === 'berjalan' ? 'selected' : '' ?>>Sedang Berjalan</option>
                    <option value="selesai" <?= ($filters['status_monev'] ?? '') === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="terkendala" <?= ($filters['status_monev'] ?? '') === 'terkendala' ? 'selected' : '' ?>>Terkendala</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Cari Program</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama program / KPI / PIC" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">Filter</button>
                <a href="<?= base_url('admin/dpc/proker?mode=' . $viewMode) ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<?php if ($viewMode === 'matriks'): ?>
<!-- Matriks Per Bidang Cards -->
<div class="row g-4">
    <?php foreach ($bidangs as $b): ?>
        <?php
        if (!empty($filters['bidang_kode']) && $filters['bidang_kode'] !== $b['kode']) continue;
        $bProkers = $prokerByBidang[$b['kode']] ?? [];
        ?>
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header py-3 border-0 d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #3e2723 !important; color: #ffffff !important; border-radius: 12px 12px 0 0;">
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-2" style="font-size: 0.95rem;"><?= esc($b['kode']) ?></span>
                        <span class="fw-bold text-white" style="font-size: 1.15rem; letter-spacing: 0.3px;"><?= esc($b['nama_bidang']) ?></span>
                        <span class="badge bg-light text-dark px-3 py-2 fw-semibold" style="font-size: 0.92rem;">
                            <i class="fa-solid fa-user-tie me-1 text-gold"></i> PIC: <?= esc($b['pic_nama'] ?: '-') ?>
                        </span>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark fw-medium px-3 py-2 text-wrap" style="font-size: 0.92rem; line-height: 1.4;">
                            <strong class="text-secondary me-1">Fokus:</strong> <?= esc($b['fokus']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4 bg-light p-3 rounded-3 g-3 border">
                        <div class="col-md-4">
                            <small class="fw-bold text-muted d-block text-uppercase mb-1" style="font-size: 0.82rem; letter-spacing: 0.5px;">Link & Match Wilayah</small>
                            <div class="fw-medium text-dark" style="font-size: 0.92rem; line-height: 1.55;"><?= esc($b['link_match'] ?: '-') ?></div>
                        </div>
                        <div class="col-md-4">
                            <small class="fw-bold text-muted d-block text-uppercase mb-1" style="font-size: 0.82rem; letter-spacing: 0.5px;">Skema Monev</small>
                            <div class="fw-medium text-dark" style="font-size: 0.92rem; line-height: 1.55;"><?= esc($b['monev_keterangan'] ?: '-') ?></div>
                        </div>
                        <div class="col-md-4">
                            <small class="fw-bold text-muted d-block text-uppercase mb-1" style="font-size: 0.82rem; letter-spacing: 0.5px;">Reward & Apresiasi</small>
                            <div class="fw-bold" style="font-size: 0.92rem; line-height: 1.55; color: #b45309;">
                                <i class="fa-solid fa-trophy me-1 text-warning"></i><?= esc($b['reward_keterangan'] ?: '-') ?>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 small text-uppercase text-muted" style="letter-spacing: 0.5px;">Daftar Program Kerja Bidang:</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Nama Program</th>
                                    <th style="width: 170px;">Frekuensi / Waktu</th>
                                    <th>Target KPI</th>
                                    <th class="text-center" style="width: 130px;">Progres Monev</th>
                                    <th class="text-center" style="width: 100px;">Status</th>
                                    <th class="text-center" style="width: 110px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($bProkers)): ?>
                                    <?php foreach ($bProkers as $idx => $pr): ?>
                                        <tr>
                                            <td class="text-muted fw-medium"><?= $idx + 1 ?></td>
                                            <td>
                                                <div class="fw-semibold text-dark" style="font-size: 0.95rem;">
                                                    <?= esc($pr['nama_program']) ?>
                                                </div>
                                                <?php if ($pr['is_prioritas']): ?>
                                                    <span class="badge bg-warning text-dark mt-1" style="font-size: 0.72rem;">★ Prioritas</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="fw-medium text-dark" style="font-size: 0.92rem;"><?= esc($pr['waktu_frekuensi'] ?: '-') ?></span>
                                            </td>
                                            <td>
                                                <div class="text-dark" style="font-size: 0.92rem; line-height: 1.6;"><?= nl2br(esc($pr['kpi_target'] ?: '-')) ?></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex align-items-center justify-content-center gap-1">
                                                    <div class="progress flex-grow-1" style="height: 14px;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pr['persentase_progres'] ?>%"></div>
                                                    </div>
                                                    <small class="fw-bold" style="font-size: 0.78rem; width: 35px;"><?= $pr['persentase_progres'] ?>%</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <?php
                                                $st = $pr['status_monev'];
                                                if ($st === 'selesai') echo '<span class="badge bg-success">Selesai</span>';
                                                elseif ($st === 'berjalan') echo '<span class="badge bg-primary">Berjalan</span>';
                                                elseif ($st === 'terkendala') echo '<span class="badge bg-danger">Terkendala</span>';
                                                else echo '<span class="badge bg-secondary">Belum</span>';
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="<?= base_url('admin/dpc/monev/log/' . $pr['id']) ?>" class="btn btn-sm btn-outline-info" title="Tools Monev / Evaluasi">
                                                        <i class="fa-solid fa-chart-line"></i>
                                                    </a>
                                                    <?php if ($can_update ?? false): ?>
                                                    <a href="<?= base_url('admin/dpc/proker/edit/' . $pr['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fa-solid fa-pen"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    <?php if ($can_delete ?? false): ?>
                                                    <a href="<?= base_url('admin/dpc/proker/delete/' . $pr['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                       onclick="return confirm('Hapus program kerja ini?')">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fa-solid fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                                            <div>Belum ada rincian program kerja di bidang ini.</div>
                                            <?php if ($can_create ?? false): ?>
                                                <div class="mt-2">
                                                    <a href="<?= base_url('admin/dpc/proker/create?bidang=' . urlencode($b['kode'])) ?>" class="btn btn-sm btn-outline-primary fw-medium">
                                                        <i class="fa-solid fa-plus me-1"></i>Tambah Program Kerja <?= esc($b['kode']) ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php else: ?>
<!-- Table List View (For List or Prioritas tab) -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #3e2723 !important; color: #ffffff !important;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Bidang</th>
                        <th>Nama Program</th>
                        <th>Frekuensi / Waktu</th>
                        <th>PIC</th>
                        <th>Target KPI</th>
                        <th class="text-center" style="width: 130px;">Progres Monev</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-center pe-4" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($prokers)): ?>
                        <?php foreach ($prokers as $idx => $pr): ?>
                            <tr>
                                <td class="ps-4 text-muted fw-medium"><?= $idx + 1 ?></td>
                                <td>
                                    <span class="badge px-2 py-1 fw-bold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44; font-size: 0.85rem;">
                                        <?= esc($pr['bidang_kode']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark" style="font-size: 0.95rem;">
                                        <?= esc($pr['nama_program']) ?>
                                    </div>
                                    <?php if ($pr['is_prioritas']): ?>
                                        <span class="badge bg-warning text-dark mt-1" style="font-size: 0.72rem;">★ Prioritas</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark" style="font-size: 0.92rem;"><?= esc($pr['waktu_frekuensi'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <span class="fw-medium text-dark" style="font-size: 0.92rem;"><?= esc($pr['pic_nama'] ?: '-') ?></span>
                                </td>
                                <td>
                                    <div class="text-dark" style="font-size: 0.92rem; line-height: 1.6;"><?= nl2br(esc($pr['kpi_target'] ?: '-')) ?></div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center gap-1">
                                        <div class="progress flex-grow-1" style="height: 12px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pr['persentase_progres'] ?>%"></div>
                                        </div>
                                        <small class="fw-bold" style="font-size: 0.7rem; width: 30px;"><?= $pr['persentase_progres'] ?>%</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $st = $pr['status_monev'];
                                    if ($st === 'selesai') echo '<span class="badge bg-success">Selesai</span>';
                                    elseif ($st === 'berjalan') echo '<span class="badge bg-primary">Berjalan</span>';
                                    elseif ($st === 'terkendala') echo '<span class="badge bg-danger">Terkendala</span>';
                                    else echo '<span class="badge bg-secondary">Belum</span>';
                                    ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= base_url('admin/dpc/monev/log/' . $pr['id']) ?>" class="btn btn-sm btn-outline-info" title="Tools Monev / Evaluasi">
                                            <i class="fa-solid fa-chart-line"></i>
                                        </a>
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/dpc/proker/edit/' . $pr['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/dpc/proker/delete/' . $pr['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                                           onclick="return confirm('Hapus program kerja ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Data program kerja tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
