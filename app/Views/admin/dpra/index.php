<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-sitemap me-2 text-gold"></i>Master DPRa PKS
        </h4>
        <p class="text-muted mb-0 small">Pengelolaan unit DPRa PKS resmi yang terhubung ke data wilayah Kelurahan (reg_villages).</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <button type="button" class="btn btn-sm btn-gold px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#addDpraModal">
        <i class="fa-solid fa-plus me-2"></i>Tambah DPRa
    </button>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('errors')): ?>
<div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <ul class="mb-0 ps-3">
        <?php foreach (session()->getFlashdata('errors') as $e): ?>
            <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="dpraTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 60px;">No</th>
                        <th>Nama DPRa PKS</th>
                        <th>Kode</th>
                        <th>Kelurahan (reg_villages)</th>
                        <th>Kecamatan</th>
                        <th class="text-center">Status</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dpraList)): ?>
                        <?php foreach ($dpraList as $idx => $item): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-bold text-dark-coffee"><?= esc($item['nama_dpra']) ?></td>
                                <td>
                                    <span class="badge px-3 py-2 fw-semibold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;">
                                        <?= esc($item['kode_dpra'] ?: '-') ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark"><?= esc($item['nama_kelurahan'] ?: '-') ?></td>
                                <td class="small text-muted"><?= esc($item['nama_kecamatan'] ?: 'Pancoran Mas') ?></td>
                                <td class="text-center">
                                    <?php if ($item['status'] === 'aktif'): ?>
                                        <span class="badge bg-success-subtle text-success px-3 py-1">🟢 Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger px-3 py-1">🔴 Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-dpra-btn" 
                                                data-id="<?= $item['id'] ?>"
                                                data-nama="<?= esc($item['nama_dpra']) ?>"
                                                data-kode="<?= esc($item['kode_dpra'] ?? '') ?>"
                                                data-village="<?= $item['village_id'] ?>"
                                                data-status="<?= $item['status'] ?>"
                                                data-keterangan="<?= esc($item['keterangan'] ?? '') ?>"
                                                title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/dpra/delete/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data DPRa PKS ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data Master DPRa PKS.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addDpraModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/dpra/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header text-white" style="background: var(--dark-coffee);">
                    <h5 class="modal-title" style="color: var(--gold);"><i class="fa-solid fa-plus me-2"></i>Tambah Master DPRa PKS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama DPRa PKS <span class="text-danger">*</span></label>
                        <input type="text" name="nama_dpra" class="form-control" required placeholder="Contoh: DPRa Depok">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Relasi Kelurahan (reg_villages) <span class="text-danger">*</span></label>
                        <select name="village_id" class="form-select" required>
                            <option value="">-- Pilih Kelurahan --</option>
                            <?php foreach ($villages as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= esc($v['name']) ?> (ID: <?= $v['id'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode DPRa</label>
                        <input type="text" name="kode_dpra" class="form-control" placeholder="Contoh: DPRA-DPK">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif">🟢 Aktif</option>
                            <option value="nonaktif">🔴 Nonaktif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan struktur DPRa"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn px-4 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editDpraModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editDpraForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header text-white" style="background: var(--dark-coffee);">
                    <h5 class="modal-title" style="color: var(--gold);"><i class="fa-solid fa-pen me-2"></i>Edit Master DPRa PKS</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama DPRa PKS <span class="text-danger">*</span></label>
                        <input type="text" name="nama_dpra" id="editNamaDpra" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Relasi Kelurahan (reg_villages) <span class="text-danger">*</span></label>
                        <select name="village_id" id="editVillageId" class="form-select" required>
                            <option value="">-- Pilih Kelurahan --</option>
                            <?php foreach ($villages as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= esc($v['name']) ?> (ID: <?= $v['id'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode DPRa</label>
                        <input type="text" name="kode_dpra" id="editKodeDpra" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" id="editStatusDpra" class="form-select">
                            <option value="aktif">🟢 Aktif</option>
                            <option value="nonaktif">🔴 Nonaktif</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" id="editKeteranganDpra" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn px-4 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">Perbarui</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.btn-gold { background-color: #d4af37 !important; color: #3e2723 !important; font-weight: 600; border: none; }
.btn-gold:hover { background-color: #c5a028 !important; color: #3e2723 !important; }
.text-dark-coffee { color: #3e2723 !important; }
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('.edit-dpra-btn').click(function() {
        const id      = $(this).data('id');
        const nama    = $(this).data('nama');
        const kode    = $(this).data('kode');
        const village = $(this).data('village');
        const status  = $(this).data('status');
        const ket     = $(this).data('keterangan');

        $('#editNamaDpra').val(nama);
        $('#editKodeDpra').val(kode);
        $('#editVillageId').val(village);
        $('#editStatusDpra').val(status);
        $('#editKeteranganDpra').val(ket);
        $('#editDpraForm').attr('action', '<?= base_url('admin/dpra/update/') ?>' + id);

        $('#editDpraModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>
