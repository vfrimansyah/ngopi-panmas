<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = !empty($asset); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-<?= $isEdit ? 'pen' : 'plus' ?> me-2 text-gold"></i><?= $isEdit ? 'Edit' : 'Tambah' ?> Data Aset
        </h4>
        <p class="text-muted mb-0 small"><?= $isEdit ? 'Perbarui informasi aset organisasi.' : 'Tambahkan aset baru ke inventaris organisasi.' ?></p>
    </div>
    <a href="<?= base_url('admin/aset/list') ?>" class="btn btn-sm btn-outline-secondary px-4">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
</div>

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
    <div class="card-body p-4">
        <form action="<?= base_url($isEdit ? 'admin/aset/update/' . $asset['id'] : 'admin/aset/store') ?>" method="POST" enctype="multipart/form-data">
            <?= csrf_field() ?>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" class="form-control" required
                           value="<?= old('nama_barang', $isEdit ? $asset['nama_barang'] : '') ?>"
                           placeholder="Contoh: AC 2PK">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Merk</label>
                    <input type="text" name="merk" class="form-control"
                           value="<?= old('merk', $isEdit ? ($asset['merk'] ?? '') : '') ?>"
                           placeholder="Contoh: Daikin, Panasonic, dll.">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Model / Tipe</label>
                    <input type="text" name="model" class="form-control"
                           value="<?= old('model', $isEdit ? ($asset['model'] ?? '') : '') ?>"
                           placeholder="Contoh: FTKC25, Inverter, dll.">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" name="jumlah" class="form-control" min="1" required
                           value="<?= old('jumlah', $isEdit ? $asset['jumlah'] : '1') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Kondisi</label>
                    <select name="kondisi" class="form-select">
                        <?php
                        $kondisiVal = old('kondisi', $isEdit ? $asset['kondisi'] : 'baik');
                        ?>
                        <option value="baik" <?= $kondisiVal === 'baik' ? 'selected' : '' ?>>🟢 Baik</option>
                        <option value="kurang_baik" <?= $kondisiVal === 'kurang_baik' ? 'selected' : '' ?>>🟡 Kurang Baik</option>
                        <option value="rusak" <?= $kondisiVal === 'rusak' ? 'selected' : '' ?>>🔴 Rusak</option>
                        <option value="hilang" <?= $kondisiVal === 'hilang' ? 'selected' : '' ?>>⚫ Hilang</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" name="lokasi" class="form-control" list="lokasiList"
                           value="<?= old('lokasi', $isEdit ? $asset['lokasi'] : '') ?>"
                           placeholder="Contoh: Kantor DPC Lantai Atas">
                    <datalist id="lokasiList">
                        <?php foreach ($lokasiOptions as $loc): ?>
                        <option value="<?= esc($loc) ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Penanggung Jawab / Pemegang Barang</label>
                    <input type="text" name="penanggung_jawab" class="form-control" list="memberList"
                           value="<?= old('penanggung_jawab', $isEdit ? ($asset['penanggung_jawab'] ?? '') : '') ?>"
                           placeholder="Nama PIC / Pemegang Barang (bisa pilih anggota)">
                    <datalist id="memberList">
                        <?php if (!empty($memberList)): ?>
                            <?php foreach ($memberList as $mName): ?>
                            <option value="<?= esc($mName) ?>">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Keterangan / Asal-usul</label>
                    <input type="text" name="keterangan" class="form-control"
                           value="<?= old('keterangan', $isEdit ? $asset['keterangan'] : '') ?>"
                           placeholder="Contoh: Beli saat kepengurusan Pa Ilham / Hibah dari DPD">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Foto Aset</label>
                    <div class="d-flex flex-wrap align-items-center gap-3 p-3 bg-light rounded-3 border">
                        <?php if ($isEdit && !empty($asset['foto']) && file_exists(FCPATH . 'uploads/aset/' . $asset['foto'])): ?>
                            <div class="position-relative text-center border rounded bg-white p-2 shadow-xs" id="currentFotoWrapper">
                                <a href="<?= base_url('uploads/aset/' . $asset['foto']) ?>" target="_blank" title="Lihat Foto Penuh">
                                    <img src="<?= base_url('uploads/aset/' . $asset['foto']) ?>" alt="Foto Aset" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                </a>
                                <div class="form-check mt-2 small text-start">
                                    <input class="form-check-input" type="checkbox" name="hapus_foto" value="1" id="hapusFoto">
                                    <label class="form-check-label text-danger small fw-semibold" for="hapusFoto">Hapus foto ini</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <input type="file" name="foto" class="form-control" accept="image/jpeg,image/png,image/webp,image/jpg" id="inputFoto" onchange="previewAsetFoto(this)">
                            <div class="form-text small text-muted mt-1">
                                <i class="fa-solid fa-circle-info me-1"></i>Format yang didukung: JPG, PNG, WEBP (maks. 5MB). <?= $isEdit && !empty($asset['foto']) ? 'Pilih file baru jika ingin mengganti foto.' : '' ?>
                            </div>
                            <div id="newFotoPreviewWrapper" class="mt-2 d-none">
                                <span class="badge bg-secondary mb-1">Preview Foto Baru:</span><br>
                                <img id="newFotoPreview" src="" alt="Preview" class="rounded border shadow-xs" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                    <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                </button>
                <a href="<?= base_url('admin/aset/list') ?>" class="btn btn-outline-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewAsetFoto(input) {
    const previewWrapper = document.getElementById('newFotoPreviewWrapper');
    const previewImg = document.getElementById('newFotoPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewWrapper.classList.remove('d-none');
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        previewImg.src = '';
        previewWrapper.classList.add('d-none');
    }
}
</script>

<?= $this->endSection() ?>
