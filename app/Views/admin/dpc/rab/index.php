<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-coins me-2 text-gold"></i>Rencana Anggaran Biaya (RAB) Program
        </h4>
        <p class="text-muted mb-0 small">Alokasi anggaran program prioritas per bidang dan pencatatan realisasi pengeluaran.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/dpc/rab/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Excel Export
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/dpc/rab/create') ?>" class="btn btn-sm btn-gold px-4 fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Tambah Item RAB
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

<!-- Summary Cards per Bidang -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #3d1f0f, #63402d); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Total Alokasi RAB</div>
                <div class="h3 fw-bold my-1">Rp <?= number_format($totalAnggaran, 0, ',', '.') ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-calculator me-1"></i> Total rencana pengeluaran</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Realisasi Pengeluaran</div>
                <div class="h3 fw-bold my-1">Rp <?= number_format($totalRealisasi, 0, ',', '.') ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-receipt me-1"></i> <?= $totalAnggaran > 0 ? round(($totalRealisasi / $totalAnggaran) * 100, 1) : 0 ?>% dari total alokasi</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm text-white h-100" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); border-radius: 12px;">
            <div class="card-body p-3">
                <div class="text-white-50 text-uppercase fw-semibold small">Sisa Alokasi Anggaran</div>
                <div class="h3 fw-bold my-1">Rp <?= number_format($totalAnggaran - $totalRealisasi, 0, ',', '.') ?></div>
                <div class="small text-white-50"><i class="fa-solid fa-wallet me-1"></i> Anggaran belum terpakai</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100 p-3" style="border-radius: 12px; background: #fff;">
            <div class="text-muted text-uppercase fw-semibold small mb-1">Rincian per Bidang:</div>
            <div class="d-flex flex-column gap-1" style="font-size: 0.8rem;">
                <?php foreach ($summary as $s): ?>
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold"><?= esc($s['bidang_kode']) ?>:</span>
                        <span>Rp <?= number_format((float)$s['total_anggaran'], 0, ',', '.') ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1">Filter Bidang</label>
                <select name="bidang" class="form-select form-select-sm">
                    <option value="">-- Semua Bidang --</option>
                    <?php foreach ($bidangs as $b): ?>
                    <option value="<?= esc($b['kode']) ?>" <?= ($filters['bidang_kode'] ?? '') === $b['kode'] ? 'selected' : '' ?>>
                        <?= esc($b['kode']) ?> – <?= esc($b['nama_bidang']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-semibold mb-1">Cari Program</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama program / Keterangan" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-gold w-50 fw-semibold">Filter</button>
                <a href="<?= base_url('admin/dpc/rab') ?>" class="btn btn-sm btn-outline-secondary w-50">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table RAB -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: #3e2723 !important; color: #ffffff !important;">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th>Bidang</th>
                        <th>Program Prioritas</th>
                        <th>Waktu / Frekuensi</th>
                        <th class="text-end">Alokasi RAB (Rp)</th>
                        <th class="text-end">Realisasi (Rp)</th>
                        <th class="text-end">Sisa (Rp)</th>
                        <th>Keterangan</th>
                        <th class="text-center pe-4" style="width: 110px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rabList)): ?>
                        <?php
                        $currentBidang = null;
                        $no = 1;
                        foreach ($rabList as $item):
                            $sisa = (float)$item['anggaran'] - (float)$item['realisasi_anggaran'];
                            if ($item['bidang_kode'] !== $currentBidang):
                                $currentBidang = $item['bidang_kode'];
                        ?>
                                <tr style="background: var(--gold)15;">
                                    <td colspan="9" class="ps-4 py-2 fw-bold" style="color: var(--dark-coffee); font-size: 0.9rem;">
                                        <i class="fa-solid fa-layer-group me-2 text-gold"></i>Bidang <?= esc($currentBidang) ?>
                                    </td>
                                </tr>
                        <?php endif; ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td>
                                    <span class="badge px-2 py-1 fw-bold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                        <?= esc($item['bidang_kode']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark"><?= esc($item['nama_program']) ?></td>
                                <td class="small text-muted"><?= esc($item['waktu_frekuensi'] ?: '-') ?></td>
                                <td class="text-end fw-bold text-dark">
                                    <?= number_format((float)$item['anggaran'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    <?= number_format((float)$item['realisasi_anggaran'], 0, ',', '.') ?>
                                </td>
                                <td class="text-end small <?= $sisa < 0 ? 'text-danger fw-bold' : 'text-muted' ?>">
                                    <?= number_format($sisa, 0, ',', '.') ?>
                                </td>
                                <td class="small text-muted"><?= esc($item['keterangan'] ?: '-') ?></td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/dpc/rab/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit / Input Realisasi">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/dpc/rab/delete/' . $item['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                                           onclick="return confirm('Hapus item RAB ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Data RAB tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($rabList)): ?>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="4" class="ps-4 text-uppercase">Total Keseluruhan</td>
                        <td class="text-end text-dark">Rp <?= number_format($totalAnggaran, 0, ',', '.') ?></td>
                        <td class="text-end text-success">Rp <?= number_format($totalRealisasi, 0, ',', '.') ?></td>
                        <td class="text-end text-muted">Rp <?= number_format($totalAnggaran - $totalRealisasi, 0, ',', '.') ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
