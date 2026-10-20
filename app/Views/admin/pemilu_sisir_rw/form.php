<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($kegiatan) && $kegiatan !== null; ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Header -->
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/pemilu/sisir-rw') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><?= $title ?></h4>
                <small class="text-muted">Isi detail kegiatan Sisir RW dengan lengkap.</small>
            </div>
        </div>

        <!-- Error alert -->
        <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <ul class="mb-0 ps-3">
                <?php foreach (session('errors') as $err): ?>
                <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-header py-3 border-bottom" style="background: var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);">
                    <i class="fa-solid fa-map-location-dot me-2"></i>Form Kegiatan Sisir RW
                </h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= $isEdit
                    ? base_url('admin/pemilu/sisir-rw/update/' . $kegiatan['id'])
                    : base_url('admin/pemilu/sisir-rw/store') ?>"
                    method="POST">
                    <?= csrf_field() ?>

                    <!-- ── Row 1: Nama & Tanggal ── -->
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control"
                                   value="<?= old('nama_kegiatan', $kegiatan['nama_kegiatan'] ?? '') ?>"
                                   placeholder="Contoh: Silaturahim RW 03 Rangkapan Jaya" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Kegiatan</label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="<?= old('tanggal', $kegiatan['tanggal'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- ── Row 2: Alamat ── -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat</label>
                        <input type="text" name="alamat" class="form-control"
                               value="<?= old('alamat', $kegiatan['alamat'] ?? '') ?>"
                               placeholder="Contoh: Jl. Raya Depok No. 10, RT 03 RW 05">
                    </div>

                    <!-- ── Row 3: RT, RW, Kecamatan ── -->
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">RT</label>
                            <input type="text" name="rt" class="form-control"
                                   value="<?= old('rt', $kegiatan['rt'] ?? '') ?>"
                                   placeholder="Contoh: 03">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">RW</label>
                            <input type="text" name="rw" class="form-control"
                                   value="<?= old('rw', $kegiatan['rw'] ?? '') ?>"
                                   placeholder="Contoh: 05">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kecamatan</label>
                            <select name="district_id" id="district_id" class="form-select">
                                <?php
                                $savedKec = $kegiatan['kecamatan'] ?? 'Pancoran Mas';
                                foreach ($kecamatan as $kec):
                                    // Try matching by name
                                    $isSelected = ($isEdit && strtolower($kec['name']) === strtolower($savedKec));
                                    // Default to Pancoran Mas if creating new
                                    if (!$isEdit && $kec['id'] === '327601') $isSelected = true;
                                ?>
                                <option value="<?= $kec['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                    <?= esc($kec['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" name="kecamatan_nama" id="kecamatan_nama"
                                   value="<?= old('kecamatan_nama', $savedKec) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Kelurahan</label>
                            <select name="village_id" id="village_id" class="form-select">
                                <option value="">-- Pilih Kelurahan --</option>
                                <?php foreach ($kelurahan as $kel): ?>
                                <option value="<?= $kel['id'] ?>"
                                    <?= old('village_id', $kegiatan['village_id'] ?? '') == $kel['id'] ? 'selected' : '' ?>>
                                    <?= esc($kel['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ── Row 4: Bentuk, Jumlah Peserta, Status ── -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Bentuk Kegiatan</label>
                            <select name="bentuk_kegiatan_id" class="form-select">
                                <option value="">-- Pilih Bentuk --</option>
                                <?php foreach ($bentukList as $b): ?>
                                <option value="<?= $b['id'] ?>"
                                    <?= old('bentuk_kegiatan_id', $kegiatan['bentuk_kegiatan_id'] ?? '') == $b['id'] ? 'selected' : '' ?>>
                                    <?= esc($b['nama']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Peserta</label>
                            <input type="number" name="jumlah_peserta" class="form-control" min="0"
                                   value="<?= old('jumlah_peserta', $kegiatan['jumlah_peserta'] ?? 0) ?>"
                                   placeholder="Contoh: 35">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <?php
                                $selectedStatus = old('status', $kegiatan['status'] ?? 'hijau');
                                $statuses = [
                                    'hijau'  => '🟢 Hijau — Kondisi Baik',
                                    'kuning' => '🟡 Kuning — Perlu Perhatian',
                                    'merah'  => '🔴 Merah — Perlu Tindakan',
                                ];
                                foreach ($statuses as $val => $label):
                                ?>
                                <option value="<?= $val ?>" <?= $selectedStatus === $val ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- ── Row 5: Tokoh Hadir ── -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tokoh yang Hadir</label>
                        <input type="text" name="tokoh_hadir" class="form-control"
                               value="<?= old('tokoh_hadir', $kegiatan['tokoh_hadir'] ?? '') ?>"
                               placeholder="Contoh: H. Ahmad (Ketua RW), Ibu Siti (Ketua PKK), ..." >
                        <div class="form-text">Pisahkan dengan koma jika lebih dari satu.</div>
                    </div>

                    <!-- ── Row 6: Agenda Lanjutan ── -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Agenda Lanjutan</label>
                        <input type="text" name="agenda_lanjutan" class="form-control"
                               value="<?= old('agenda_lanjutan', $kegiatan['agenda_lanjutan'] ?? '') ?>"
                               placeholder="Contoh: Pembentukan relawan RT 04, Rapat koordinasi minggu depan">
                    </div>

                    <!-- ── Row 7: Keterangan / Notulensi ── -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Keterangan / Notulensi</label>
                        <textarea name="keterangan" rows="5" class="form-control"
                                  placeholder="Tuliskan ringkasan hasil kegiatan, poin-poin penting, masukan dari warga, dll."><?= old('keterangan', $kegiatan['keterangan'] ?? '') ?></textarea>
                    </div>

                    <!-- ── Actions ── -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                        </button>
                        <a href="<?= base_url('admin/pemilu/sisir-rw') ?>" class="btn btn-outline-secondary px-4">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function () {
    // When kecamatan changes, reload kelurahan
    $('#district_id').on('change', function () {
        const districtId = $(this).val();
        const $vil = $('#village_id');
        $vil.html('<option value="">-- Pilih Kelurahan --</option>');
        if (!districtId) return;
        $.getJSON('<?= base_url('admin/pemilu/sisir-rw/get-villages') ?>', { district_id: districtId }, function (data) {
            data.forEach(function (v) {
                $vil.append(`<option value="${v.id}">${v.name}</option>`);
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
