<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-clipboard-list me-2 text-gold"></i>List Booking Ruangan
        </h4>
        <p class="text-muted mb-0 small">Daftar seluruh jadwal pemesanan dan penggunaan ruangan kantor/sekretariat.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/booking/form') ?>" class="btn btn-sm btn-gold px-4">
        <i class="fa-solid fa-calendar-plus me-2"></i>Tambah Booking
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Filter Form -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: #f8fafc;">
    <div class="card-body p-3">
        <form method="GET" action="" class="row g-2 align-items-end">
            <div class="col-md-3 col-6">
                <label class="form-label small fw-semibold mb-1">Range Tanggal (Dari)</label>
                <input type="date" name="from_date" class="form-control form-control-sm" value="<?= esc($filters['from_date'] ?? '') ?>">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small fw-semibold mb-1">Sampai Tanggal</label>
                <input type="date" name="to_date" class="form-control form-control-sm" value="<?= esc($filters['to_date'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold mb-1">Ruangan</label>
                <select name="ruangan_id" class="form-select form-select-sm">
                    <option value="">-- Semua Ruangan --</option>
                    <?php foreach ($ruanganList as $r): ?>
                        <option value="<?= $r['id'] ?>" <?= ($filters['ruangan_id'] ?? '') == $r['id'] ? 'selected' : '' ?>><?= esc($r['nama_ruangan']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-gold w-100 fw-semibold">GO</button>
                <a href="<?= base_url('admin/booking/list') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
        <div class="mt-3 pt-2 border-top">
            <a href="<?= base_url('admin/booking/export-excel?' . http_build_query($filters)) ?>" class="btn btn-sm btn-success px-3">
                <i class="fa-solid fa-file-excel me-1"></i> Excel Export
            </a>
        </div>
    </div>
</div>

<!-- Table List Booking -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 200px;">Tanggal</th>
                        <th>Nama Kegiatan</th>
                        <th>Ruangan</th>
                        <th>Keterangan</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $item): ?>
                            <tr>
                                <td class="ps-4 align-top py-3">
                                    <div class="fw-bold text-dark">
                                        <?= date('d M Y', strtotime($item['tgl_kegiatan'])) ?>
                                        <span class="small text-muted">(<?= date('l', strtotime($item['tgl_kegiatan'])) ?>)</span>
                                    </div>
                                    <div class="small text-secondary mt-1">
                                        <?= esc($item['jam_mulai']) ?>-<?= esc($item['jam_selesai']) ?>
                                    </div>
                                </td>
                                <td class="align-top py-3">
                                    <div class="fw-bold text-dark-coffee fs-6"><?= esc($item['nama_kegiatan']) ?></div>
                                    <div class="small text-muted mt-1">Oleh : <span class="badge bg-secondary-subtle text-secondary"><?= esc($item['penyelenggara'] ?: '-') ?></span></div>
                                    <div class="small text-muted">PIC : <span class="fw-semibold"><?= esc($item['pj_kegiatan'] ?: '-') ?></span></div>
                                </td>
                                <td class="align-top py-3">
                                    <span class="badge bg-info-subtle text-info fs-6 fw-bold px-3 py-2">
                                        <i class="fa-solid fa-door-open me-1"></i><?= esc($item['nama_ruangan']) ?>
                                    </span>
                                </td>
                                <td class="align-top py-3">
                                    <div class="small text-dark"><?= esc($item['catatan'] ?: '-') ?></div>
                                </td>
                                <td class="text-center align-top py-3 pe-4">
                                    <?php 
                                    $isOwner = !empty($is_full_admin) || ((int)($item['created_by'] ?? 0) === (int)($current_user_id ?? 0));
                                    ?>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if (($can_update ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/booking/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (($can_delete ?? false) && $isOwner): ?>
                                        <a href="<?= base_url('admin/booking/delete/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus booking ruangan ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fa-solid fa-calendar-xmark fa-2x mb-2 d-block opacity-50"></i>
                                Belum ada data booking ruangan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-dark-coffee { color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
