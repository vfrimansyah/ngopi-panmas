<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-boxes-stacked me-2 text-gold"></i>Daftar Aset & Inventaris
        </h4>
        <p class="text-muted mb-0 small">Kelola data barang / aset organisasi.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/aset/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3 fw-semibold">
            <i class="fa-solid fa-file-excel me-1"></i>Excel Export
        </a>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/aset/create') ?>" class="btn btn-sm btn-gold px-4 fw-semibold">
            <i class="fa-solid fa-plus me-2"></i>Tambah Aset
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

<!-- Filter Bar -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Lokasi</label>
                <select name="lokasi" class="form-select form-select-sm">
                    <option value="">-- Semua Lokasi --</option>
                    <?php foreach ($lokasiOptions as $loc): ?>
                    <option value="<?= esc($loc) ?>" <?= ($filters['lokasi'] ?? '') === $loc ? 'selected' : '' ?>><?= esc($loc) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold mb-1">Kondisi</label>
                <select name="kondisi" class="form-select form-select-sm">
                    <option value="">-- Semua --</option>
                    <option value="baik" <?= ($filters['kondisi'] ?? '') === 'baik' ? 'selected' : '' ?>>🟢 Baik</option>
                    <option value="kurang_baik" <?= ($filters['kondisi'] ?? '') === 'kurang_baik' ? 'selected' : '' ?>>🟡 Kurang Baik</option>
                    <option value="rusak" <?= ($filters['kondisi'] ?? '') === 'rusak' ? 'selected' : '' ?>>🔴 Rusak</option>
                    <option value="hilang" <?= ($filters['kondisi'] ?? '') === 'hilang' ? 'selected' : '' ?>>⚫ Hilang</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Cari</label>
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama / Kode / Keterangan" value="<?= esc($filters['search'] ?? '') ?>">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">GO</button>
                <a href="<?= base_url('admin/aset/list') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 50px;">No</th>
                        <th style="width: 70px;">Foto</th>
                        <th style="width: 100px;">Kode</th>
                        <th>Nama Barang &amp; Spesifikasi</th>
                        <th>Lokasi</th>
                        <th>Penanggung Jawab</th>
                        <th class="text-center" style="width: 80px;">Jumlah</th>
                        <th class="text-center" style="width: 130px;">Kondisi</th>
                        <th>Keterangan</th>
                        <th class="text-center pe-4" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($assets)): ?>
                        <?php
                        $currentLokasi = null;
                        $no = 1;
                        foreach ($assets as $item):
                            // Location separator row
                            if ($item['lokasi'] !== $currentLokasi):
                                $currentLokasi = $item['lokasi'];
                        ?>
                                <tr style="background: var(--gold)15;">
                                    <td colspan="10" class="ps-4 py-2 fw-bold" style="color: var(--dark-coffee); font-size: 0.9rem;">
                                        <i class="fa-solid fa-location-dot me-2 text-gold"></i><?= esc($currentLokasi ?: 'Lokasi Lain') ?>
                                    </td>
                                </tr>
                        <?php endif; ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td>
                                    <?php if (!empty($item['foto']) && file_exists(FCPATH . 'uploads/aset/' . $item['foto'])): ?>
                                        <a href="javascript:void(0)" onclick="showFotoModal('<?= base_url('uploads/aset/' . $item['foto']) ?>', '<?= esc(addslashes($item['nama_barang'])) ?>', '<?= esc(addslashes(($item['merk'] ?? '') . ' ' . ($item['model'] ?? ''))) ?>')">
                                            <img src="<?= base_url('uploads/aset/' . $item['foto']) ?>" alt="Foto" class="rounded border shadow-xs" style="width: 44px; height: 44px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    <?php else: ?>
                                        <div class="rounded border bg-light text-muted d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                            <i class="fa-solid fa-box text-secondary opacity-50"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge px-2 py-1 fw-semibold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                        <?= esc($item['kode_aset'] ?: '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><?= esc($item['nama_barang']) ?></div>
                                    <?php if (!empty($item['merk']) || !empty($item['model'])): ?>
                                        <div class="small text-muted mt-1 d-flex flex-wrap align-items-center gap-1">
                                            <?php if (!empty($item['merk'])): ?>
                                                <span class="badge bg-light text-dark border"><i class="fa-solid fa-tag me-1 text-gold"></i><?= esc($item['merk']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($item['model'])): ?>
                                                <span class="badge bg-secondary-subtle text-secondary"><?= esc($item['model']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-muted"><?= esc($item['lokasi'] ?: '-') ?></td>
                                <td>
                                    <?php if (!empty($item['penanggung_jawab'])): ?>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <i class="fa-solid fa-user-check me-1 text-gold"></i><?= esc($item['penanggung_jawab']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center fw-bold"><?= $item['jumlah'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $k = $item['kondisi'];
                                    if ($k === 'baik') echo '<span class="badge bg-success-subtle text-success px-2 py-1">🟢 Baik</span>';
                                    elseif ($k === 'kurang_baik') echo '<span class="badge bg-warning text-dark px-2 py-1">🟡 Kurang Baik</span>';
                                    elseif ($k === 'rusak') echo '<span class="badge bg-danger text-white px-2 py-1">🔴 Rusak</span>';
                                    else echo '<span class="badge bg-secondary text-white px-2 py-1">⚫ Hilang</span>';
                                    ?>
                                </td>
                                <td class="small text-muted"><?= esc($item['keterangan'] ?: '-') ?></td>
                                <td class="text-center pe-4">
                                    <?php 
                                    $isOwner = !empty($is_full_admin) || ((int)($item['created_by'] ?? 0) === (int)($current_user_id ?? 0));
                                    ?>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if (($can_update ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/aset/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (($can_delete ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/aset/delete/' . $item['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus data aset ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">Belum ada data aset.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Foto Aset -->
<div class="modal fade" id="modalFotoAset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header py-2" style="background: var(--dark-coffee); color: var(--cream);">
                <div>
                    <h6 class="modal-title fw-bold mb-0" id="modalFotoTitle">Foto Aset</h6>
                    <small class="text-light opacity-75" id="modalFotoSub"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center bg-dark">
                <img id="modalFotoImg" src="" alt="Foto Aset" class="img-fluid" style="max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
function showFotoModal(src, title, sub) {
    document.getElementById('modalFotoImg').src = src;
    document.getElementById('modalFotoTitle').textContent = title || 'Foto Aset';
    document.getElementById('modalFotoSub').textContent = sub ? sub.trim() : '';
    const modal = new bootstrap.Modal(document.getElementById('modalFotoAset'));
    modal.show();
}
</script>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
