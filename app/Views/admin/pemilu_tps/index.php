<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-check-to-slot me-2 text-gold"></i>Data TPS (Tempat Pemungutan Suara)
        </h4>
        <p class="text-muted mb-0 small">Kelola data TPS, Wilayah Administratif, RT/RW, dan Daerah Pemilihan (Dapil).</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('admin/pemilu/tps/export-excel?' . http_build_query(['tahun' => $selectedTahun, 'village_id' => $selectedKelurahan])) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Export Excel
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/pemilu/tps/create') ?>" class="btn btn-sm btn-gold px-4 fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Tambah TPS
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

<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ===== FILTER TAHUN & KELURAHAN ===== -->
<div class="card shadow-sm border-0 mb-4" style="background:#efebe9;">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1 text-dark-coffee">Tahun Pemilu:</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">-- Semua Tahun --</option>
                    <?php foreach ($listTahun as $th): ?>
                        <option value="<?= $th ?>" <?= $selectedTahun == $th ? 'selected' : '' ?>><?= $th ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1 text-dark-coffee">Kelurahan:</label>
                <select name="village_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kelurahan --</option>
                    <?php foreach ($listKelurahan as $kl): ?>
                        <option value="<?= $kl['village_id'] ?>" <?= $selectedKelurahan == $kl['village_id'] ? 'selected' : '' ?>><?= esc($kl['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-self-end">
                <button type="submit" class="btn btn-sm btn-gold px-3 fw-semibold"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                <a href="<?= base_url('admin/pemilu/tps') ?>" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tpsTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 80px;">Tahun</th>
                        <th>Provinsi</th>
                        <th>Kota/Kab</th>
                        <th>Dapil DPRD Kota</th>
                        <th>Dapil DPRD Prov</th>
                        <th>Dapil DPR RI</th>
                        <th>Kecamatan</th>
                        <th>Kelurahan</th>
                        <th>RW</th>
                        <th>RT</th>
                        <th>TPS</th>
                        <th>DPT</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tps)): ?>
                        <?php foreach ($tps as $t): ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-dark-coffee"><?= esc($t['tahun']) ?></td>
                                <td style="font-size: 0.85rem;" class="text-secondary"><?= esc($t['provinsi']) ?></td>
                                <td style="font-size: 0.85rem;" class="text-secondary"><?= esc($t['kabupaten']) ?></td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= esc($t['dapil_kota']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= esc($t['dapil_prov']) ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= esc($t['dapil_ri']) ?></span>
                                </td>
                                <td style="font-size: 0.85rem;" class="text-secondary"><?= esc($t['kecamatan']) ?></td>
                                <td>
                                    <span class="fw-semibold text-dark-coffee"><?= esc($t['kelurahan']) ?></span>
                                </td>
                                <td class="fw-medium text-secondary"><?= esc($t['rw']) ?: '-' ?></td>
                                <td class="fw-medium text-secondary"><?= esc($t['rt']) ?: '-' ?></td>
                                <td>
                                    <span class="badge bg-gold text-dark fs-7 px-3 py-1 fw-bold" style="border: 1px solid var(--dark-coffee);">
                                        <?= esc($t['tps']) ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-secondary">
                                    <?= esc($t['dpt']) ?: '-' ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/pemilu/tps/edit/' . $t['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/pemilu/tps/delete/' . $t['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus data <?= esc($t['tps']) ?> di Kelurahan <?= esc($t['kelurahan']) ?>?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .btn-gold {
        background-color: #d4af37 !important;
        color: #3e2723 !important;
        font-weight: 600;
        border: none;
    }
    .btn-gold:hover {
        background-color: #c5a028 !important;
        color: #3e2723 !important;
    }
    .text-dark-coffee {
        color: #3e2723 !important;
    }
</style>

<?= $this->endSection() ?>
