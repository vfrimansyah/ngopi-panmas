<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-map-location-dot me-2 text-gold"></i>Kegiatan Sisir RW
        </h4>
        <p class="text-muted mb-0 small">Kelola data kegiatan pemenangan berbasis wilayah RT/RW.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/pemilu/sisir-rw/bentuk') ?>" class="btn btn-sm btn-outline-secondary px-3">
            <i class="fa-solid fa-list-check me-1"></i>Master Bentuk
        </a>
        <a href="<?= base_url('admin/pemilu/sisir-rw/create') ?>" class="btn btn-sm btn-gold px-4">
            <i class="fa-solid fa-plus me-2"></i>Tambah Kegiatan
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
    <i class="fa-solid fa-triangle-exclamation me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ===== SUMMARY CARDS ===== -->
<?php
$total  = count($kegiatans);
$hijau  = count(array_filter($kegiatans, fn($k) => $k['status'] === 'hijau'));
$kuning = count(array_filter($kegiatans, fn($k) => $k['status'] === 'kuning'));
$merah  = count(array_filter($kegiatans, fn($k) => $k['status'] === 'merah'));
$totalPeserta = array_sum(array_column($kegiatans, 'jumlah_peserta'));
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid var(--dark-coffee) !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#3e272322;">
                    <i class="fa-solid fa-map-location-dot" style="color:var(--dark-coffee)"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1" style="color:var(--dark-coffee)"><?= $total ?></div>
                    <div class="text-muted small">Total Kegiatan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #22c55e !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#22c55e22;">
                    <i class="fa-solid fa-circle-check text-success"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1 text-success"><?= $hijau ?></div>
                    <div class="text-muted small">Hijau</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #f59e0b !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#f59e0b22;">
                    <i class="fa-solid fa-circle-half-stroke text-warning"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1 text-warning"><?= $kuning ?></div>
                    <div class="text-muted small">Kuning</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #ef4444 !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:42px;height:42px;background:#ef444422;">
                    <i class="fa-solid fa-circle-xmark text-danger"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1 text-danger"><?= $merah ?></div>
                    <div class="text-muted small">Merah</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== FILTER ===== -->
<div class="card shadow-sm border-0 mb-4" style="background:#efebe9;">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Status:</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">-- Semua Status --</option>
                    <option value="hijau"  <?= ($filters['status'] ?? '') === 'hijau'  ? 'selected' : '' ?>>🟢 Hijau</option>
                    <option value="kuning" <?= ($filters['status'] ?? '') === 'kuning' ? 'selected' : '' ?>>🟡 Kuning</option>
                    <option value="merah"  <?= ($filters['status'] ?? '') === 'merah'  ? 'selected' : '' ?>>🔴 Merah</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Kelurahan:</label>
                <select name="village_id" class="form-select form-select-sm">
                    <option value="">-- Semua Kelurahan --</option>
                    <?php foreach ($kelurahan as $kel): ?>
                    <option value="<?= $kel['id'] ?>" <?= ($filters['village_id'] ?? '') == $kel['id'] ? 'selected' : '' ?>>
                        <?= esc($kel['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Bentuk Kegiatan:</label>
                <select name="bentuk_kegiatan_id" class="form-select form-select-sm">
                    <option value="">-- Semua Bentuk --</option>
                    <?php foreach ($bentukList as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= ($filters['bentuk_kegiatan_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                        <?= esc($b['nama']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-self-end">
                <button type="submit" class="btn btn-sm btn-gold px-3 fw-semibold"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                <a href="<?= base_url('admin/pemilu/sisir-rw') ?>" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- ===== TABEL ===== -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="sisirRwTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width:50px;">#</th>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Lokasi (RW/RT)</th>
                        <th>Kelurahan</th>
                        <th>Bentuk</th>
                        <th class="text-center">Peserta</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4" style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($kegiatans)): ?>
                    <?php foreach ($kegiatans as $i => $k): ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold" style="color:var(--dark-coffee)"><?= esc($k['nama_kegiatan']) ?></div>
                            <?php if ($k['alamat']): ?>
                            <small class="text-muted"><?= esc(substr($k['alamat'], 0, 60)) ?><?= strlen($k['alamat']) > 60 ? '…' : '' ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?= $k['tanggal'] ? date('d M Y', strtotime($k['tanggal'])) : '-' ?>
                        </td>
                        <td>
                            <span class="fw-semibold text-dark-coffee">RW <?= esc($k['rw'] ?: '-') ?></span>
                            <?php if ($k['rt']): ?> / RT <?= esc($k['rt']) ?><?php endif; ?>
                            <div class="small text-muted"><?= esc($k['kecamatan']) ?></div>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                <?= esc($k['kelurahan'] ?: '-') ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($k['bentuk_kegiatan']): ?>
                            <span class="badge" style="background:var(--gold);color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                <?= esc($k['bentuk_kegiatan']) ?>
                            </span>
                            <?php else: ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center fw-semibold"><?= number_format($k['jumlah_peserta']) ?></td>
                        <td class="text-center">
                            <?php
                            $statusConfig = [
                                'hijau'  => ['color' => '#22c55e', 'bg' => '#dcfce7', 'label' => '🟢 Hijau'],
                                'kuning' => ['color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => '🟡 Kuning'],
                                'merah'  => ['color' => '#ef4444', 'bg' => '#fee2e2', 'label' => '🔴 Merah'],
                            ];
                            $sc = $statusConfig[$k['status']] ?? ['color' => '#6b7280', 'bg' => '#f3f4f6', 'label' => $k['status']];
                            ?>
                            <span class="badge fw-semibold px-2 py-1"
                                  style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;border:1px solid <?= $sc['color'] ?>55;">
                                <?= $sc['label'] ?>
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="<?= base_url('admin/pemilu/sisir-rw/detail/' . $k['id']) ?>"
                                   class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/pemilu/sisir-rw/edit/' . $k['id']) ?>"
                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($can_delete ?? false): ?>
                                <a href="<?= base_url('admin/pemilu/sisir-rw/delete/' . $k['id']) ?>"
                                   class="btn btn-sm btn-outline-danger" title="Hapus"
                                   onclick="return confirm('Hapus kegiatan <?= esc($k['nama_kegiatan']) ?>?')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fa-solid fa-map-location-dot fa-2x d-block mb-2 opacity-25"></i>
                            Belum ada data kegiatan Sisir RW.
                            <?php if ($can_create ?? false): ?>
                            <a href="<?= base_url('admin/pemilu/sisir-rw/create') ?>" class="d-block mt-2 text-decoration-none">
                                <i class="fa-solid fa-plus me-1"></i>Tambah Kegiatan Pertama
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (!empty($kegiatans)): ?>
<div class="mt-3 text-muted small text-end">
    Menampilkan <?= count($kegiatans) ?> kegiatan &bull; Total <?= number_format($totalPeserta) ?> peserta
</div>
<?php endif; ?>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-dark-coffee { color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
