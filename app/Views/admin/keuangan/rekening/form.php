<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/keuangan/rekening') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--dark-coffee)"><?= $title ?></h4>
        <p class="text-muted mb-0 small">Data rekening bank atau kas untuk pencatatan transaksi.</p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-building-columns me-2"></i>Data Rekening</h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $rekening ? base_url('admin/keuangan/rekening/update/' . $rekening['id']) : base_url('admin/keuangan/rekening/store') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tipe <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe" id="tipeKas" value="kas"
                                       <?= old('tipe', $rekening['tipe'] ?? 'kas') === 'kas' ? 'checked' : '' ?> onchange="toggleBank(this)">
                                <label class="form-check-label" for="tipeKas"><i class="fa-solid fa-coins me-1 text-success"></i>Kas</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipe" id="tipeBank" value="bank"
                                       <?= old('tipe', $rekening['tipe'] ?? '') === 'bank' ? 'checked' : '' ?> onchange="toggleBank(this)">
                                <label class="form-check-label" for="tipeBank"><i class="fa-solid fa-building-columns me-1 text-info"></i>Bank</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="nama_rekening" class="form-control"
                               placeholder="Contoh: Kas Tunai / BRI Cabang Utama"
                               value="<?= old('nama_rekening', $rekening['nama_rekening'] ?? '') ?>" required>
                    </div>

                    <div id="bankFields" style="<?= old('tipe', $rekening['tipe'] ?? 'kas') === 'kas' ? 'display:none' : '' ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nama Bank</label>
                            <input type="text" name="bank" class="form-control" placeholder="BRI, BNI, Mandiri, dll"
                                   value="<?= old('bank', $rekening['bank'] ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" class="form-control font-monospace" placeholder="1234567890"
                                   value="<?= old('nomor_rekening', $rekening['nomor_rekening'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Akun Terkait (Chart of Accounts)</label>
                        <select name="akun_id" class="form-select">
                            <option value="">— Pilih Akun —</option>
                            <?php foreach ($accounts as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= old('akun_id', $rekening['akun_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                                <?= esc($a['kode_akun'] . ' — ' . $a['nama_akun']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Saldo Awal (Rp)</label>
                        <input type="text" name="saldo_awal" class="form-control" placeholder="0"
                               value="<?= old('saldo_awal', number_format($rekening['saldo_awal'] ?? 0, 0, ',', '.')) ?>">
                    </div>

                    <?php if ($rekening): ?>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" <?= ($rekening['is_active']??1)?'checked':'' ?>>
                            <label class="form-check-label small fw-semibold" for="isActive">Rekening Aktif</label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?= base_url('admin/keuangan/rekening') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBank(el) { document.getElementById('bankFields').style.display = el.value === 'bank' ? '' : 'none'; }
</script>
<?= $this->endSection() ?>
