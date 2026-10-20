<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/keuangan/accounts') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--dark-coffee)"><?= $title ?></h4>
        <p class="text-muted mb-0 small">Isi informasi akun untuk dicatat dalam Chart of Accounts.</p>
    </div>
</div>

<?php if (session()->getFlashdata('errors') || session('errors')): ?>
<div class="alert alert-danger border-0 shadow-sm">
    <?php foreach ((session('errors') ?? []) as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header py-3" style="background:#3d1f0f;">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);">
                    <i class="fa-solid fa-sitemap me-2"></i>Data Akun
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= $account ? base_url('admin/keuangan/accounts/update/' . $account['id']) : base_url('admin/keuangan/accounts/store') ?>">
                    <?= csrf_field() ?>

                    <?php if (!$account): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kode Akun <span class="text-danger">*</span></label>
                        <input type="text" name="kode_akun" class="form-control font-monospace"
                               placeholder="Contoh: 4-005" value="<?= old('kode_akun') ?>" required>
                        <small class="text-muted">Format: [kelompok]-[urutan] (misal: 1-001, 4-002, 5-001)</small>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Akun <span class="text-danger">*</span></label>
                        <input type="text" name="nama_akun" class="form-control"
                               placeholder="Nama akun" value="<?= old('nama_akun', $account['nama_akun'] ?? '') ?>" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tipe Akun <span class="text-danger">*</span></label>
                            <select name="tipe_akun" class="form-select" id="tipeAkun" required>
                                <?php
                                $tipes = ['kas'=>'Kas','bank'=>'Bank','piutang'=>'Piutang','pendapatan'=>'Pendapatan','beban'=>'Beban','modal'=>'Modal','hutang'=>'Hutang'];
                                foreach ($tipes as $v=>$l):
                                    $sel = old('tipe_akun', $account['tipe_akun'] ?? '') === $v ? 'selected' : '';
                                ?>
                                <option value="<?= $v ?>" <?= $sel ?>><?= $l ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Normal Balance</label>
                            <select name="normal_balance" class="form-select" id="normalBalance">
                                <option value="debit"  <?= old('normal_balance', $account['normal_balance'] ?? '') === 'debit'  ? 'selected' : '' ?>>Debit</option>
                                <option value="kredit" <?= old('normal_balance', $account['normal_balance'] ?? '') === 'kredit' ? 'selected' : '' ?>>Kredit</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" placeholder="Keterangan singkat"><?= old('deskripsi', $account['deskripsi'] ?? '') ?></textarea>
                    </div>

                    <?php if ($account): ?>
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                   <?= ($account['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label fw-semibold small" for="isActive">Akun Aktif</label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-4 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i>Simpan
                        </button>
                        <a href="<?= base_url('admin/keuangan/accounts') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-set normal balance berdasarkan tipe
document.getElementById('tipeAkun').addEventListener('change', function() {
    const nb = document.getElementById('normalBalance');
    const kreditTypes = ['pendapatan','modal','hutang'];
    nb.value = kreditTypes.includes(this.value) ? 'kredit' : 'debit';
});
</script>
<?= $this->endSection() ?>
