<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<?php
$isMasuk  = ($tipeHint === 'pemasukan');
$isBaru   = $transaksi === null;
$accentColor = $isMasuk ? '#10b981' : '#ef4444';
$accentBadge = $isMasuk ? 'success' : 'danger';
$accentLabel = $isMasuk ? 'Pemasukan' : 'Pengeluaran';
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/keuangan/transaksi') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i>Kembali
    </a>
    <div>
        <h4 class="fw-bold mb-0" style="color:var(--dark-coffee)">
            <?= $title ?>
            <span class="badge bg-<?= $accentBadge ?> ms-2 fs-7"><?= $accentLabel ?></span>
        </h4>
        <p class="text-muted mb-0 small">Jurnal akan dibuat otomatis setelah transaksi disimpan.</p>
    </div>
</div>

<?php if (session('errors')): ?>
<div class="alert alert-danger border-0 shadow-sm alert-dismissible">
    <?php foreach (session('errors') as $e): ?><div><i class="fa-solid fa-triangle-exclamation me-1"></i><?= esc($e) ?></div><?php endforeach; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST" action="<?= $transaksi ? base_url('admin/keuangan/transaksi/update/' . $transaksi['id']) : base_url('admin/keuangan/transaksi/store') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Kiri -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" style="border-top:4px solid <?= $accentColor ?> !important;">
                <div class="card-header py-3" style="background:var(--dark-coffee);">
                    <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-right-left me-2"></i>Detail Transaksi</h6>
                </div>
                <div class="card-body p-4">
                    <!-- Tipe: tampilkan saja, tidak bisa diubah saat edit -->
                    <?php if ($isBaru): ?>
                    <input type="hidden" name="tipe" value="<?= $tipeHint ?>">
                    <?php else: ?>
                    <input type="hidden" name="tipe" value="<?= $transaksi['tipe'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control"
                                   value="<?= old('tanggal', $transaksi['tanggal'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Rekening <span class="text-danger">*</span></label>
                            <select name="rekening_id" class="form-select" required>
                                <option value="">— Pilih Rekening —</option>
                                <?php foreach ($rekenings as $r): ?>
                                <option value="<?= $r['id'] ?>" <?= old('rekening_id', $transaksi['rekening_id'] ?? '') == $r['id'] ? 'selected' : '' ?>>
                                    <?= esc($r['nama_rekening']) ?> <?= $r['tipe']==='bank'?'('.$r['bank'].')':'' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3 mb-3">
                        <label class="form-label fw-semibold small">Jumlah (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="text" name="jumlah" id="jumlahInput" class="form-control fs-5 fw-bold"
                                   placeholder="0" value="<?= old('jumlah', $transaksi ? number_format($transaksi['jumlah'],0,',','.') : '') ?>"
                                   required style="color:<?= $accentColor ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kategori</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Iuran Bulanan, Konsumsi, dll"
                               value="<?= old('kategori', $transaksi['kategori'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan</label>
                        <textarea name="deskripsi" class="form-control" rows="3"
                                  placeholder="Uraian transaksi..."><?= old('deskripsi', $transaksi['deskripsi'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kanan -->
        <div class="col-lg-5">
            <!-- Akun Jurnal -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header py-3" style="background:var(--dark-coffee);">
                    <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-book-open me-2"></i>Akun Jurnal</h6>
                </div>
                <div class="card-body p-4">
                    <small class="text-muted d-block mb-3">
                        <?php if ($isMasuk): ?>
                        <i class="fa-solid fa-info-circle me-1 text-info"></i>
                        Pemasukan: Debit = Rekening Kas/Bank, Kredit = Akun Pendapatan
                        <?php else: ?>
                        <i class="fa-solid fa-info-circle me-1 text-info"></i>
                        Pengeluaran: Debit = Akun Beban, Kredit = Rekening Kas/Bank
                        <?php endif; ?>
                    </small>

                    <?php if ($isMasuk): ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Kredit (Akun Pendapatan) <span class="text-danger">*</span></label>
                        <select name="akun_kredit_id" class="form-select">
                            <option value="">— Pilih Akun Pendapatan —</option>
                            <?php foreach ($accounts as $a):
                                if (!in_array($a['tipe_akun'], ['pendapatan','modal'])) continue;
                            ?>
                            <option value="<?= $a['id'] ?>" <?= old('akun_kredit_id', $transaksi['akun_kredit_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                                <?= esc($a['kode_akun'] . ' — ' . $a['nama_akun']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Debit (Akun Beban) <span class="text-danger">*</span></label>
                        <select name="akun_debit_id" class="form-select">
                            <option value="">— Pilih Akun Beban —</option>
                            <?php foreach ($accounts as $a):
                                if ($a['tipe_akun'] !== 'beban') continue;
                            ?>
                            <option value="<?= $a['id'] ?>" <?= old('akun_debit_id', $transaksi['akun_debit_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                                <?= esc($a['kode_akun'] . ' — ' . $a['nama_akun']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upload Bukti -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header py-3" style="background:var(--dark-coffee);">
                    <h6 class="mb-0 fw-semibold" style="color:var(--gold);"><i class="fa-solid fa-paperclip me-2"></i>Bukti Transaksi</h6>
                </div>
                <div class="card-body p-3">
                    <?php if ($transaksi && $transaksi['bukti_file']): ?>
                    <div class="mb-2">
                        <a href="<?= base_url('uploads/keuangan/' . $transaksi['bukti_file']) ?>" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="fa-solid fa-file me-1"></i>Lihat Bukti Lama
                        </a>
                    </div>
                    <?php endif; ?>
                    <input type="file" name="bukti_file" class="form-control form-control-sm" accept="image/*,.pdf">
                    <small class="text-muted">Format: JPG/PNG/PDF. Maks 5MB.</small>
                </div>
            </div>

            <button type="submit" class="btn w-100 fw-semibold py-3" style="background:<?= $accentColor ?>;color:#fff;">
                <i class="fa-solid fa-save me-2"></i>Simpan Transaksi
            </button>
        </div>
    </div>
</form>

<script>
// Format input jumlah
document.getElementById('jumlahInput').addEventListener('input', function() {
    const raw = this.value.replace(/[^\d]/g, '');
    this.value = raw ? parseInt(raw).toLocaleString('id-ID') : '';
});
</script>
<?= $this->endSection() ?>
