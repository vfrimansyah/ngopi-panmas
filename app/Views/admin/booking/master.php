<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-key me-2 text-gold"></i>Master Ruangan
        </h4>
        <p class="text-muted mb-0 small">Pengelolaan data master fasilitas ruangan yang tersedia untuk pemesanan/booking.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <button type="button" class="btn btn-sm btn-gold px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#addRuanganModal">
        <i class="fa-solid fa-plus me-2"></i>Tambah Ruangan
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
            <table class="table table-hover align-middle mb-0" id="masterRuanganTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 80px;">No</th>
                        <th>Nama Ruangan</th>
                        <th style="width: 150px;">Nomor Urut</th>
                        <th style="width: 150px;">Kapasitas</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ruanganList)): ?>
                        <?php foreach ($ruanganList as $idx => $item): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-semibold text-dark-coffee"><?= esc($item['nama_ruangan']) ?></td>
                                <td>
                                    <span class="badge px-3 py-2 fw-semibold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;font-size:0.85rem;">
                                        <?= $item['nomor_urut'] ?>
                                    </span>
                                </td>
                                <td><span class="badge bg-info-subtle text-info px-3 py-2 fs-6 fw-bold"><i class="fa-solid fa-users me-1"></i><?= $item['kapasitas'] ?> Org</span></td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-btn" 
                                                data-id="<?= $item['id'] ?>"
                                                data-nama="<?= esc($item['nama_ruangan']) ?>"
                                                data-urut="<?= $item['nomor_urut'] ?>"
                                                data-kapasitas="<?= $item['kapasitas'] ?>"
                                                data-keterangan="<?= esc($item['keterangan'] ?? '') ?>"
                                                title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/booking/master/delete/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data ruangan ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data master ruangan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addRuanganModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/booking/master/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header style-header text-white" style="background: var(--dark-coffee);">
                    <h5 class="modal-title" style="color: var(--gold);"><i class="fa-solid fa-plus me-2"></i>Tambah Master Ruangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" class="form-control" required placeholder="Contoh: R. Meeting (Lt 1)">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nomor Urut</label>
                            <input type="number" name="nomor_urut" class="form-control" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" class="form-control" value="20">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Fasilitas</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: AC, Proyektor, Sound System"></textarea>
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
<div class="modal fade" id="editRuanganModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header style-header text-white" style="background: var(--dark-coffee);">
                    <h5 class="modal-title" style="color: var(--gold);"><i class="fa-solid fa-pen me-2"></i>Edit Master Ruangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Ruangan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_ruangan" id="editNama" class="form-control" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Nomor Urut</label>
                            <input type="number" name="nomor_urut" id="editUrut" class="form-control">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Kapasitas (Orang)</label>
                            <input type="number" name="kapasitas" id="editKapasitas" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / Fasilitas</label>
                        <textarea name="keterangan" id="editKeterangan" class="form-control" rows="2"></textarea>
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
    $('.edit-btn').click(function() {
        const id   = $(this).data('id');
        const nama = $(this).data('nama');
        const urut = $(this).data('urut');
        const kap  = $(this).data('kapasitas');
        const ket  = $(this).data('keterangan');

        $('#editNama').val(nama);
        $('#editUrut').val(urut);
        $('#editKapasitas').val(kap);
        $('#editKeterangan').val(ket);
        $('#editForm').attr('action', '<?= base_url('admin/booking/master/update/') ?>' + id);

        $('#editRuanganModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>
