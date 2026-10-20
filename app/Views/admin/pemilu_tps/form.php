<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/pemilu/tps') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><?= $title ?></h4>
                <small class="text-muted">Masukkan data detail TPS dengan penentuan wilayah berjenjang secara dinamis.</small>
            </div>
        </div>

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
                    <i class="fa-solid fa-check-to-slot me-2"></i>Form TPS
                </h6>
            </div>
            <div class="card-body p-4">
                <?php $isEdit = isset($tps); ?>
                <form action="<?= $isEdit ? base_url('admin/pemilu/tps/update/' . $tps['id']) : base_url('admin/pemilu/tps/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tahun Pemilu <span class="text-danger">*</span></label>
                            <input type="number" name="tahun" class="form-control" value="<?= old('tahun', $isEdit ? $tps['tahun'] : '2024') ?>" required placeholder="Contoh: 2024">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dapil DPRD Kota <span class="text-danger">*</span></label>
                            <input type="text" name="dapil_kota" class="form-control" value="<?= old('dapil_kota', $isEdit ? $tps['dapil_kota'] : '1') ?>" required placeholder="Contoh: 1">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dapil DPRD Prov <span class="text-danger">*</span></label>
                            <input type="text" name="dapil_prov" class="form-control" value="<?= old('dapil_prov', $isEdit ? $tps['dapil_prov'] : 'VIII') ?>" required placeholder="Contoh: VIII">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Dapil DPR RI <span class="text-danger">*</span></label>
                            <input type="text" name="dapil_ri" class="form-control" value="<?= old('dapil_ri', $isEdit ? $tps['dapil_ri'] : 'Jawa Barat VI') ?>" required placeholder="Contoh: Jawa Barat VI">
                        </div>
                    </div>

                    <!-- GEOGRAPHIC DROPDOWN LAYERS -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Provinsi <span class="text-danger">*</span></label>
                            <select name="province_id" id="province_id" class="form-select" required>
                                <option value="">-- Pilih Provinsi --</option>
                                <?php foreach ($provinces as $p): ?>
                                    <option value="<?= $p['id'] ?>" <?= old('province_id', $isEdit ? $tps['province_id'] : '') == $p['id'] ? 'selected' : '' ?>>
                                        <?= esc($p['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kota / Kabupaten <span class="text-danger">*</span></label>
                            <select name="regency_id" id="regency_id" class="form-select" required>
                                <option value="">-- Pilih Kota/Kab --</option>
                                <?php if ($isEdit && isset($regencies)): ?>
                                    <?php foreach ($regencies as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= old('regency_id', $tps['regency_id']) == $r['id'] ? 'selected' : '' ?>>
                                            <?= esc($r['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                            <select name="district_id" id="district_id" class="form-select" required>
                                <option value="">-- Pilih Kecamatan --</option>
                                <?php if ($isEdit && isset($districts)): ?>
                                    <?php foreach ($districts as $d): ?>
                                        <option value="<?= $d['id'] ?>" <?= old('district_id', $tps['district_id']) == $d['id'] ? 'selected' : '' ?>>
                                            <?= esc($d['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kelurahan <span class="text-danger">*</span></label>
                            <select name="village_id" id="village_id" class="form-select" required>
                                <option value="">-- Pilih Kelurahan --</option>
                                <?php if ($isEdit && isset($villages)): ?>
                                    <?php foreach ($villages as $v): ?>
                                        <option value="<?= $v['id'] ?>" <?= old('village_id', $tps['village_id']) == $v['id'] ? 'selected' : '' ?>>
                                            <?= esc($v['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- RT / RW / TPS / DPT -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Nama TPS <span class="text-danger">*</span></label>
                            <input type="text" name="tps" class="form-control" value="<?= old('tps', $isEdit ? $tps['tps'] : '') ?>" required placeholder="Contoh: TPS 001">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">RW</label>
                            <input type="text" name="rw" class="form-control" value="<?= old('rw', $isEdit ? $tps['rw'] : '') ?>" placeholder="Contoh: 02">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">RT</label>
                            <input type="text" name="rt" class="form-control" value="<?= old('rt', $isEdit ? $tps['rt'] : '') ?>" placeholder="Contoh: 05">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">DPT (Jumlah Pemilih)</label>
                            <input type="number" name="dpt" class="form-control" value="<?= old('dpt', $isEdit ? $tps['dpt'] : '') ?>" placeholder="Contoh: 200">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?= base_url('admin/pemilu/tps') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    // Province changed -> load regencies
    $('#province_id').change(function() {
        const provinceId = $(this).val();
        $('#regency_id').html('<option value="">-- Pilih Kota/Kab --</option>').trigger('change');
        
        if (provinceId) {
            $.ajax({
                url: '<?= base_url('admin/pemilu/tps/get-regencies') ?>',
                type: 'GET',
                data: { province_id: provinceId },
                dataType: 'json',
                success: function(data) {
                    let html = '<option value="">-- Pilih Kota/Kab --</option>';
                    data.forEach(item => {
                        html += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#regency_id').html(html);
                }
            });
        }
    });

    // Regency changed -> load districts
    $('#regency_id').change(function() {
        const regencyId = $(this).val();
        $('#district_id').html('<option value="">-- Pilih Kecamatan --</option>').trigger('change');
        
        if (regencyId) {
            $.ajax({
                url: '<?= base_url('admin/pemilu/tps/get-districts') ?>',
                type: 'GET',
                data: { regency_id: regencyId },
                dataType: 'json',
                success: function(data) {
                    let html = '<option value="">-- Pilih Kecamatan --</option>';
                    data.forEach(item => {
                        html += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#district_id').html(html);
                }
            });
        }
    });

    // District changed -> load villages
    $('#district_id').change(function() {
        const districtId = $(this).val();
        $('#village_id').html('<option value="">-- Pilih Kelurahan --</option>');
        
        if (districtId) {
            $.ajax({
                url: '<?= base_url('admin/pemilu/tps/get-villages') ?>',
                type: 'GET',
                data: { district_id: districtId },
                dataType: 'json',
                success: function(data) {
                    let html = '<option value="">-- Pilih Kelurahan --</option>';
                    data.forEach(item => {
                        html += `<option value="${item.id}">${item.name}</option>`;
                    });
                    $('#village_id').html(html);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
