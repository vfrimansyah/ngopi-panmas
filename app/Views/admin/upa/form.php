<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-people-group me-2 text-gold"></i><?= isset($upa) ? 'Edit Group UPA: ' . esc($upa['kode_upa']) : 'Bentuk Group UPA Baru' ?>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-secondary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/upa') ?>" class="text-secondary text-decoration-none">Group UPA</a></li>
                <li class="breadcrumb-item active text-dark-coffee fw-medium" aria-current="page"><?= isset($upa) ? 'Edit' : 'Bentuk Baru' ?></li>
            </ol>
        </nav>
    </div>
    <a href="<?= base_url('admin/upa') ?>" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<!-- ERROR VALIDASI -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <h6 class="fw-bold"><i class="fa-solid fa-circle-xmark me-2"></i>Perbaiki Kesalahan Berikut:</h6>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: 12px; background-color: #ffffff;">
    <div class="card-body p-4">
        <form action="<?= isset($upa) ? base_url('admin/upa/update/' . $upa['kode_upa']) : base_url('admin/upa/store') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="row g-3">
                <!-- 1. Kode UPA -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Kode UPA <span class="text-danger">*</span></label>
                    <input type="text" name="kode_upa" class="form-control font-monospace" placeholder="Contoh: PM4106" value="<?= old('kode_upa', $upa['kode_upa'] ?? '') ?>" <?= isset($upa) ? 'readonly style="background-color:#e9ecef;"' : '' ?> required>
                    <div class="form-text">Kode unik kelompok UPA (contoh: PM4106, PM3101). Tidak dapat diubah setelah disimpan.</div>
                </div>

                <!-- 2. Nama Group -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Nama Group / Kelompok</label>
                    <input type="text" name="nama_group" class="form-control" placeholder="Contoh: Group PM4106 / Sahabat Al-Fatih" value="<?= old('nama_group', $upa['nama_group'] ?? '') ?>">
                    <div class="form-text">Nama atau identitas kelompok UPA (opsional, default: Group [Kode UPA]).</div>
                </div>

                <!-- 3. Pembimbing UPA (Naqib) -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Pembimbing UPA (Naqib)</label>
                    <select name="pembimbing_id" class="form-select">
                        <option value="">-- Pilih Pembimbing (Kader Madya / Dewasa / Utama) --</option>
                        <?php foreach ($mentors as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= old('pembimbing_id', $upa['pembimbing_id'] ?? '') == $m['id'] ? 'selected' : '' ?>>
                                <?= esc($m['nama_lengkap']) ?> (<?= esc($m['status_anggota']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Pembimbing terpilih (Naqib) yang akan membina anggota di kelompok ini.</div>
                </div>

                <!-- 4. Status Aktif UPA -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Status Kelompok</label>
                    <select name="is_active" class="form-select">
                        <option value="1" <?= old('is_active', $upa['is_active'] ?? 1) == 1 ? 'selected' : '' ?>>Aktif Berjalan</option>
                        <option value="0" <?= old('is_active', $upa['is_active'] ?? 1) == 0 ? 'selected' : '' ?>>Non-Aktif / Ditangguhkan</option>
                    </select>
                    <div class="form-text">Status keaktifan penyelenggaraan kegiatan UPA.</div>
                </div>

                <!-- 5. Kecamatan -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Kecamatan Wilayah UPA <span class="text-danger">*</span></label>
                    <select name="kecamatan_id" id="kecamatanSelect" class="form-select" required>
                        <option value="">-- Pilih Kecamatan --</option>
                        <?php foreach ($districts as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= old('kecamatan_id', $upa['kecamatan_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
                                <?= esc($d['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 6. Kelurahan -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Kelurahan Wilayah UPA <span class="text-danger">*</span></label>
                    <select name="kelurahan_id" id="kelurahanSelect" class="form-select" required>
                        <option value="">-- Pilih Kelurahan --</option>
                        <?php if (isset($villages)): ?>
                            <?php foreach ($villages as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= old('kelurahan_id', $upa['kelurahan_id'] ?? '') == $v['id'] ? 'selected' : '' ?>>
                                    <?= esc($v['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 7. Jadwal Rutin -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Jadwal Pertemuan Rutin</label>
                    <input type="text" name="jadwal_rutin" class="form-control" placeholder="Contoh: Setiap Ahad Pagi / 06.00 WIB" value="<?= old('jadwal_rutin', $upa['jadwal_rutin'] ?? '') ?>">
                </div>

                <!-- 8. Tempat Pertemuan -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark-coffee">Tempat Pertemuan</label>
                    <input type="text" name="tempat_pertemuan" class="form-control" placeholder="Contoh: Rumah Naqib / Masjid Al-Ikhlas" value="<?= old('tempat_pertemuan', $upa['tempat_pertemuan'] ?? '') ?>">
                </div>

                <!-- 9. Keterangan -->
                <div class="col-12">
                    <label class="form-label fw-bold text-dark-coffee">Keterangan / Catatan Group</label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan khusus kelompok pembinaan, profil anggota, dsb."><?= old('keterangan', $upa['keterangan'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="mt-4 pt-3 border-top d-flex gap-2">
                <button type="submit" class="btn btn-gold px-4 py-2">
                    <i class="fa-solid fa-floppy-disk me-2"></i><?= isset($upa) ? 'Simpan Perubahan' : 'Bentuk Group UPA' ?>
                </button>
                <a href="<?= base_url('admin/upa') ?>" class="btn btn-outline-secondary px-4 py-2">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kecamatanSelect = document.getElementById('kecamatanSelect');
    const kelurahanSelect = document.getElementById('kelurahanSelect');

    kecamatanSelect.addEventListener('change', function() {
        const districtId = this.value;
        kelurahanSelect.innerHTML = '<option value="">-- Loading Kelurahan... --</option>';

        if (!districtId) {
            kelurahanSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
            return;
        }

        fetch(`<?= base_url('admin/upa/get-villages') ?>?district_id=${districtId}`)
            .then(res => res.json())
            .then(data => {
                let html = '<option value="">-- Pilih Kelurahan --</option>';
                data.forEach(v => {
                    html += `<option value="${v.id}">${v.name}</option>`;
                });
                kelurahanSelect.innerHTML = html;
            })
            .catch(err => {
                kelurahanSelect.innerHTML = '<option value="">-- Gagal memuat kelurahan --</option>';
            });
    });
});
</script>

<style>
    .text-dark-coffee {
        color: #3e2723 !important;
    }
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
</style>
<?= $this->endSection() ?>
