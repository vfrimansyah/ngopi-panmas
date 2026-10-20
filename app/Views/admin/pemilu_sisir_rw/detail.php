<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php
$statusConfig = [
    'hijau'  => ['color' => '#22c55e', 'bg' => '#dcfce7', 'label' => '🟢 Hijau — Kondisi Baik'],
    'kuning' => ['color' => '#f59e0b', 'bg' => '#fef3c7', 'label' => '🟡 Kuning — Perlu Perhatian'],
    'merah'  => ['color' => '#ef4444', 'bg' => '#fee2e2', 'label' => '🔴 Merah — Perlu Tindakan'],
];
$sc = $statusConfig[$kegiatan['status']] ?? ['color' => '#6b7280', 'bg' => '#f3f4f6', 'label' => $kegiatan['status']];
?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/pemilu/sisir-rw') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div class="flex-grow-1">
        <h4 class="fw-bold mb-0" style="color:var(--dark-coffee)"><?= esc($kegiatan['nama_kegiatan']) ?></h4>
        <small class="text-muted">Detail Kegiatan Sisir RW</small>
    </div>
    <span class="badge fw-semibold px-3 py-2 fs-6"
          style="background:<?= $sc['bg'] ?>;color:<?= $sc['color'] ?>;border:1px solid <?= $sc['color'] ?>55;">
        <?= $sc['label'] ?>
    </span>
    <?php if ($can_update ?? true): ?>
    <a href="<?= base_url('admin/pemilu/sisir-rw/edit/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-warning">
        <i class="fa-solid fa-pen me-1"></i>Edit
    </a>
    <?php endif; ?>
</div>

<div class="row g-4">
    <!-- Left: Info Utama -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header py-3 border-bottom" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold" style="color:var(--gold);">
                    <i class="fa-solid fa-circle-info me-2"></i>Informasi Kegiatan
                </h6>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted fw-normal small">Nama Kegiatan</dt>
                    <dd class="col-sm-8 fw-semibold"><?= esc($kegiatan['nama_kegiatan']) ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Tanggal</dt>
                    <dd class="col-sm-8"><?= $kegiatan['tanggal'] ? date('d F Y', strtotime($kegiatan['tanggal'])) : '<span class="text-muted">-</span>' ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Bentuk Kegiatan</dt>
                    <dd class="col-sm-8">
                        <?php if ($kegiatan['bentuk_kegiatan']): ?>
                        <span class="badge" style="background:var(--gold);color:var(--dark-coffee);">
                            <?= esc($kegiatan['bentuk_kegiatan']) ?>
                        </span>
                        <?php else: ?>
                        <span class="text-muted">-</span>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Jumlah Peserta</dt>
                    <dd class="col-sm-8">
                        <span class="fw-bold fs-5" style="color:var(--dark-coffee)"><?= number_format($kegiatan['jumlah_peserta']) ?></span>
                        <span class="text-muted small ms-1">orang</span>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Alamat</dt>
                    <dd class="col-sm-8"><?= nl2br(esc($kegiatan['alamat'] ?: '-')) ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Lokasi</dt>
                    <dd class="col-sm-8">
                        <span class="fw-semibold">RW <?= esc($kegiatan['rw'] ?: '-') ?></span>
                        <?php if ($kegiatan['rt']): ?> / RT <?= esc($kegiatan['rt']) ?><?php endif; ?>
                    </dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Kelurahan</dt>
                    <dd class="col-sm-8"><?= esc($kegiatan['kelurahan'] ?: '-') ?></dd>

                    <dt class="col-sm-4 text-muted fw-normal small">Kecamatan</dt>
                    <dd class="col-sm-8"><?= esc($kegiatan['kecamatan'] ?: 'Pancoran Mas') ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Right: Notulensi & Agenda -->
    <div class="col-lg-5">
        <!-- Tokoh Hadir -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-2 border-bottom" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold small" style="color:var(--gold);">
                    <i class="fa-solid fa-users me-2"></i>Tokoh yang Hadir
                </h6>
            </div>
            <div class="card-body p-3">
                <?php if ($kegiatan['tokoh_hadir']): ?>
                <?php foreach (explode(',', $kegiatan['tokoh_hadir']) as $tokoh): ?>
                <span class="badge bg-secondary-subtle text-secondary me-1 mb-1 px-2 py-1">
                    <i class="fa-solid fa-user me-1"></i><?= esc(trim($tokoh)) ?>
                </span>
                <?php endforeach; ?>
                <?php else: ?>
                <span class="text-muted small">Tidak ada data.</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Agenda Lanjutan -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header py-2 border-bottom" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold small" style="color:var(--gold);">
                    <i class="fa-solid fa-calendar-plus me-2"></i>Agenda Lanjutan
                </h6>
            </div>
            <div class="card-body p-3">
                <?= $kegiatan['agenda_lanjutan']
                    ? nl2br(esc($kegiatan['agenda_lanjutan']))
                    : '<span class="text-muted small">Tidak ada agenda lanjutan.</span>' ?>
            </div>
        </div>

        <!-- Keterangan/Notulensi -->
        <div class="card border-0 shadow-sm">
            <div class="card-header py-2 border-bottom" style="background:var(--dark-coffee);">
                <h6 class="mb-0 fw-semibold small" style="color:var(--gold);">
                    <i class="fa-solid fa-file-lines me-2"></i>Keterangan / Notulensi
                </h6>
            </div>
            <div class="card-body p-3" style="max-height:250px;overflow-y:auto;">
                <?= $kegiatan['keterangan']
                    ? nl2br(esc($kegiatan['keterangan']))
                    : '<span class="text-muted small">Belum ada keterangan.</span>' ?>
            </div>
        </div>
    </div>
</div>

<div class="mt-3 text-muted small">
    Dibuat: <?= date('d M Y H:i', strtotime($kegiatan['created_at'])) ?>
    &bull; Diperbarui: <?= date('d M Y H:i', strtotime($kegiatan['updated_at'])) ?>
</div>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
