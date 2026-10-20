<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-list-check me-2 text-gold"></i>Master Kegiatan
        </h4>
        <p class="text-muted mb-0 small">Pengelolaan data dasar jenis kegiatan organisasi.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <button type="button" class="btn btn-sm btn-gold px-4 fw-semibold" data-bs-toggle="modal" data-bs-target="#addMasterModal">
        <i class="fa-solid fa-plus me-2"></i>Tambah Master
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
            <table class="table table-hover align-middle mb-0" id="masterKegiatanTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4" style="width: 80px;">No</th>
                        <th>Nama Kegiatan</th>
                        <th style="width: 150px;">Nomor Urut</th>
                        <th class="text-center pe-4" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($jenisList)): ?>
                        <?php foreach ($jenisList as $idx => $item): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                <td class="fw-semibold text-dark-coffee"><?= esc($item['nama_kegiatan']) ?></td>
                                <td>
                                    <span class="badge px-3 py-2 fw-semibold" style="background:var(--gold)22;color:var(--dark-coffee);border:1px solid var(--dark-coffee)44;font-size:0.85rem;">
                                        <?= $item['nomor_urut'] ?>
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if ($can_update ?? false): ?>
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-btn" 
                                                data-id="<?= $item['id'] ?>"
                                                data-nama="<?= esc($item['nama_kegiatan']) ?>"
                                                data-urut="<?= $item['nomor_urut'] ?>"
                                                title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <?php endif; ?>
                                        <?php if ($can_delete ?? false): ?>
                                        <a href="<?= base_url('admin/kegiatan/master/delete/' . $item['id']) ?>" 
                                           class="btn btn-sm btn-outline-danger"
                                           title="Hapus"
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus jenis kegiatan ini?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data master kegiatan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Add -->
<div class="modal fade" id="addMasterModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/kegiatan/master/store') ?>" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header style-header text-white" style="background: var(--dark-coffee);">
                    <h5 class="modal-title" style="color: var(--gold);"><i class="fa-solid fa-plus me-2"></i>Tambah Master Jenis Kegiatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kegiatan" class="form-control" required placeholder="Contoh: Silaturahim Tokoh">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Urut</label>
                        <input type="number" name="nomor_urut" class="form-control" value="0">
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
<div class="modal fade" id="editMasterModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST">
            <?= csrf_field() ?>
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header style-header text-white" style="background: var(--dark-coffee);">
                    <h5 class="modal-title" style="color: var(--gold);"><i class="fa-solid fa-pen me-2"></i>Edit Master Jenis Kegiatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="nama_kegiatan" id="editNama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomor Urut</label>
                        <input type="number" name="nomor_urut" id="editUrut" class="form-control">
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

        $('#editNama').val(nama);
        $('#editUrut').val(urut);
        $('#editForm').attr('action', '<?= base_url('admin/kegiatan/master/update/') ?>' + id);

        $('#editMasterModal').modal('show');
    });
});
</script>
<?= $this->endSection() ?>
