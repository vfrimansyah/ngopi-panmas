<?php $this->extend('layouts/admin') ?>
<?php $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-1" style="color:var(--dark-coffee)"><i class="fa-solid fa-building-columns me-2 text-gold"></i>Rekening & Kas</h4>
        <p class="text-muted mb-0 small">Daftar rekening bank dan kas beserta saldo berjalan.</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/keuangan/rekening/create') ?>" class="btn btn-sm px-4 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
        <i class="fa-solid fa-plus me-2"></i>Tambah Rekening
    </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
    <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Total Saldo Card -->
<div class="card border-0 shadow-sm mb-4" style="border-left:4px solid #6366f1 !important;">
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;background:#6366f122;">
                <i class="fa-solid fa-wallet fs-5" style="color:#6366f1"></i>
            </div>
            <div>
                <div class="text-muted small">Total Saldo Semua Rekening</div>
                <div class="fw-bold fs-4" style="color:#6366f1">Rp <?= number_format($totalSaldo, 0, ',', '.') ?></div>
            </div>
        </div>
        <small class="text-muted"><?= count($rekenings) ?> rekening aktif</small>
    </div>
</div>

<!-- Rekening Cards -->
<div class="row g-3">
    <?php foreach ($rekenings as $r):
        $isBank = $r['tipe'] === 'bank';
        $color  = $isBank ? '#3b82f6' : '#10b981';
        $icon   = $isBank ? 'fa-building-columns' : 'fa-coins';
    ?>
    <div class="col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100" style="border-top:4px solid <?= $color ?> !important;">
            <div class="card-body p-4">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:44px;height:44px;background:<?= $color ?>22;">
                            <i class="fa-solid <?= $icon ?> fs-5" style="color:<?= $color ?>"></i>
                        </div>
                        <div>
                            <div class="fw-bold"><?= esc($r['nama_rekening']) ?></div>
                            <small class="text-muted"><?= $isBank ? esc($r['bank'] ?? 'Bank') : 'Kas Tunai' ?></small>
                        </div>
                    </div>
                    <span class="badge" style="background:<?= $color ?>22;color:<?= $color ?>;"><?= $isBank ? 'Bank' : 'Kas' ?></span>
                </div>
                <?php if ($r['nomor_rekening']): ?>
                <p class="text-muted small mb-2"><i class="fa-solid fa-hashtag me-1"></i><?= esc($r['nomor_rekening']) ?></p>
                <?php endif; ?>
                <div class="border-top pt-3 mt-2">
                    <div class="text-muted small mb-1">Saldo Berjalan</div>
                    <div class="fw-bold fs-5 <?= $r['saldo_berjalan']>=0?'text-success':'text-danger' ?>">
                        Rp <?= number_format($r['saldo_berjalan'], 0, ',', '.') ?>
                    </div>
                    <small class="text-muted">Saldo awal: Rp <?= number_format($r['saldo_awal'],0,',','.') ?></small>
                </div>
                <div class="d-flex gap-2 mt-3">
                    <?php if ($can_update ?? false): ?>
                    <a href="<?= base_url('admin/keuangan/rekening/edit/' . $r['id']) ?>" class="btn btn-sm btn-outline-secondary flex-grow-1">
                        <i class="fa-solid fa-pen me-1"></i>Edit
                    </a>
                    <?php endif; ?>
                    <a href="<?= base_url('admin/keuangan/transaksi?rekening_id=' . $r['id']) ?>" class="btn btn-sm" style="background:#6366f122;color:#6366f1;border:1px solid #6366f144;">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($rekenings)): ?>
    <div class="col-12"><div class="text-center text-muted py-5"><i class="fa-solid fa-building-columns fa-3x mb-3 opacity-25"></i><p>Belum ada rekening. <a href="<?= base_url('admin/keuangan/rekening/create') ?>">Tambah sekarang</a></p></div></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
