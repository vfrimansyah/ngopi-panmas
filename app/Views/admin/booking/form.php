<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($booking) && !empty($booking); ?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/booking/list') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><?= $title ?></h4>
                <small class="text-muted">Formulir pemesanan/booking ruangan kantor dan fasilitas organisasi.</small>
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

        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header style-header text-white py-3 border-0" style="background: var(--dark-coffee); border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold" style="color: var(--gold);"><i class="fa-solid fa-door-open me-2"></i>Booking Ruangan</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= $isEdit ? base_url('admin/booking/update/' . $booking['id']) : base_url('admin/booking/store') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-semibold">Tgl Kegiatan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" name="tgl_kegiatan" class="form-control" value="<?= old('tgl_kegiatan', $isEdit ? $booking['tgl_kegiatan'] : date('Y-m-d')) ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-semibold">Jam Mulai <span class="text-danger">*</span></label>
                        <div class="col-sm-4">
                            <input type="time" name="jam_mulai" class="form-control" value="<?= old('jam_mulai', $isEdit ? $booking['jam_mulai'] : '08:30') ?>" required>
                        </div>
                        <label class="col-sm-1 text-center col-form-label fw-semibold">s.d</label>
                        <div class="col-sm-4">
                            <input type="time" name="jam_selesai" class="form-control" value="<?= old('jam_selesai', $isEdit ? $booking['jam_selesai'] : '11:30') ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="nama_kegiatan" class="form-control" value="<?= old('nama_kegiatan', $isEdit ? $booking['nama_kegiatan'] : '') ?>" required placeholder="Contoh: Rapat Bipeka Monev Reguler">
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-semibold">Penyelenggara</label>
                        <div class="col-sm-9">
                            <select name="penyelenggara" class="form-select">
                                <option value="">-- Pilih Penyelenggara --</option>
                                <?php
                                $penyelenggaraOptions = [
                                    'DPRa Depok'               => 'DPRa Depok',
                                    'DPRa Depok Jaya'          => 'DPRa Depok Jaya',
                                    'DPRa Pancoran Mas'        => 'DPRa Pancoran Mas',
                                    'DPRa Mampang'             => 'DPRa Mampang',
                                    'DPRa Rangkapan Jaya'      => 'DPRa Rangkapan Jaya',
                                    'DPRa Rangkapan Jaya Baru' => 'DPRa Rangkapan Jaya Baru',
                                    'DPC'                      => 'DPC PKS Pancoran Mas',
                                    'BPH'                      => 'BPH',
                                    'Bipeka'                   => 'Bipeka',
                                    'BKAP'                     => 'BKAP',
                                    'Bid Kepemudaan'           => 'Bid Kepemudaan',
                                    'BPUD'                     => 'BPUD',
                                    'Bid Komdigi'              => 'Bid Komdigi',
                                    'BKO'                      => 'BKO',
                                    'Bid Kessos'               => 'Bid Kessos',
                                    'DPC - SPKK'               => 'DPC - SPKK',
                                    'DPC - Humas'              => 'DPC - Humas',
                                    'Grup UPA'                 => 'Grup UPA',
                                ];
                                foreach ($penyelenggaraOptions as $val => $lbl):
                                ?>
                                <option value="<?= $val ?>" <?= old('penyelenggara', $isEdit ? $booking['penyelenggara'] : '') == $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-semibold">Ruangan Dipakai <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="ruangan_id" class="form-select" required>
                                <option value="">-- Pilih Ruangan --</option>
                                <?php foreach ($ruanganList as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= old('ruangan_id', $isEdit ? $booking['ruangan_id'] : '') == $r['id'] ? 'selected' : '' ?>>
                                        <?= esc($r['nama_ruangan']) ?> (Kapasitas: <?= $r['kapasitas'] ?> org)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row align-items-center">
                        <label class="col-sm-3 col-form-label fw-semibold">PJ Kegiatan</label>
                        <div class="col-sm-9">
                            <input type="text" name="pj_kegiatan" class="form-control" value="<?= old('pj_kegiatan', $isEdit ? $booking['pj_kegiatan'] : '') ?>" placeholder="Nama Orangnya (Contoh: Wanda Fitriyah)">
                        </div>
                    </div>

                    <div class="mb-4 row">
                        <label class="col-sm-3 col-form-label fw-semibold">Catatan</label>
                        <div class="col-sm-9">
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan / keperluan fasilitas"><?= old('catatan', $isEdit ? $booking['catatan'] : '') ?></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 offset-sm-3 d-flex gap-2">
                            <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                                <?= $isEdit ? 'PERBARUI' : 'SAVE' ?>
                            </button>
                            <a href="<?= base_url('admin/booking/list') ?>" class="btn btn-outline-secondary px-4">
                                CANCEL
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.text-gold { color: #d4af37 !important; }
</style>

<?= $this->endSection() ?>
