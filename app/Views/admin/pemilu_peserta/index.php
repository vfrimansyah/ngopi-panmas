<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-list-ol me-2 text-gold"></i>Peserta Pemilu
        </h4>
        <p class="text-muted mb-0 small">Daftar nomor urut partai politik dan ketua umum per tahun pemilu.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/pemilu/peserta/create') ?>" class="btn btn-sm btn-gold px-4">
        <i class="fa-solid fa-plus me-2"></i>Tambah Peserta
    </a>
    <?php endif; ?>
</div>

<!-- ===== FILTER TAHUN PEMILU ===== -->
<div class="card shadow-sm border-0 mb-4" style="background:#efebe9;">
    <div class="card-body p-3">
        <form action="" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="form-label small fw-semibold mb-1 text-dark-coffee">Saring Berdasarkan Tahun Pemilu:</label>
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">-- Semua Tahun Pemilu --</option>
                    <?php foreach ($listTahun as $th): ?>
                        <option value="<?= $th ?>" <?= $selectedTahun == $th ? 'selected' : '' ?>><?= $th ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-self-end">
                <button type="submit" class="btn btn-sm btn-gold px-3 fw-semibold"><i class="fa-solid fa-filter me-1"></i>Filter</button>
                <a href="<?= base_url('admin/pemilu/peserta') ?>" class="btn btn-sm btn-outline-secondary px-3">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="pesertaTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 100px;">Tahun Pemilu</th>
                        <th style="width: 100px;">No. Urut</th>
                        <th style="width: 80px;">Logo</th>
                        <th>Nama Partai</th>
                        <th>Ketua Umum</th>
                        <th>Cakupan</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($peserta)): ?>
                        <?php foreach ($peserta as $p): ?>
                            <tr>
                                <td class="ps-4 fw-semibold text-dark-coffee"><?= esc($p['tahun']) ?></td>
                                <td>
                                    <span class="badge bg-gold text-dark fs-6 px-3 py-2 rounded-circle" style="border: 2px solid var(--dark-coffee);">
                                        <?= esc($p['no_urut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($p['lambang_partai'])): ?>
                                        <img src="<?= base_url('uploads/partai/' . $p['lambang_partai']) ?>" alt="Logo" class="img-thumbnail rounded" style="max-height: 45px; max-width: 45px;">
                                    <?php else: ?>
                                        <div class="bg-light rounded text-center py-2 text-muted" style="height: 45px; width: 45px;">
                                            <i class="fa-solid fa-flag"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark-coffee"><?= esc($p['nama_partai']) ?></span>
                                </td>
                                <td>
                                    <span class="fw-medium text-secondary"><?= esc($p['ketua_partai']) ?></span>
                                </td>
                                <td>
                                    <?php if ($p['cakupan'] === 'Nasional'): ?>
                                        <span class="badge bg-success-subtle text-success">Nasional</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary">Lokal</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <a href="<?= base_url('admin/pemilu/peserta/edit/' . $p['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/pemilu/peserta/delete/' . $p['id']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Hapus keikutsertaan Partai ini dari Pemilu <?= esc($p['tahun']) ?>?')">
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
    .bg-gold {
        background-color: #d4af37 !important;
    }
</style>

<?= $this->endSection() ?>
