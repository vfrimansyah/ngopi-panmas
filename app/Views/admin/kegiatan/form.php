<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($kegiatan) && !empty($kegiatan); ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex align-items-center gap-3 mb-4">
            <a href="<?= base_url('admin/kegiatan/list') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0" style="color: var(--dark-coffee)"><?= $title ?></h4>
                <small class="text-muted">Masukkan detail data kegiatan organisasi secara rinci.</small>
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
            <div class="card-header bg-dark text-white py-3 border-0" style="border-radius: 12px 12px 0 0;">
                <h6 class="mb-0 fw-semibold"><i class="fa-solid fa-pen-to-square me-2"></i>Form Kegiatan</h6>
            </div>
            <div class="card-body p-4">
                <form action="<?= $isEdit ? base_url('admin/kegiatan/update/' . $kegiatan['id']) : base_url('admin/kegiatan/store') ?>" 
                      method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" value="<?= old('nama_kegiatan', $isEdit ? $kegiatan['nama_kegiatan'] : '') ?>" required placeholder="Contoh: Fogging RW 03">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jenis Kegiatan</label>
                            <select name="jenis_kegiatan_id" class="form-select">
                                <option value="">-- Pilih Jenis --</option>
                                <?php foreach ($jenisList as $j): ?>
                                    <option value="<?= $j['id'] ?>" <?= old('jenis_kegiatan_id', $isEdit ? $kegiatan['jenis_kegiatan_id'] : '') == $j['id'] ? 'selected' : '' ?>>
                                        <?= esc($j['nama_kegiatan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tanggal Kegiatan</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= old('tanggal', $isEdit ? $kegiatan['tanggal'] : date('Y-m-d')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="<?= old('jam_mulai', $isEdit ? $kegiatan['jam_mulai'] : '08:00') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="<?= old('jam_selesai', $isEdit ? $kegiatan['jam_selesai'] : '11:30') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Penyelenggara</label>
                            <select name="penyelenggara" class="form-select">
                                <option value="">-- Pilih Penyelenggara --</option>
                                <?php
                                $penyelenggaraOptions = [];
                                if (!empty($dpraList)) {
                                    foreach ($dpraList as $d) {
                                        $penyelenggaraOptions[$d['nama_dpra']] = $d['nama_dpra'];
                                    }
                                } else {
                                    $penyelenggaraOptions = [
                                        'DPRa Depok'               => 'DPRa Depok',
                                        'DPRa Depok Jaya'          => 'DPRa Depok Jaya',
                                        'DPRa Pancoran Mas'        => 'DPRa Pancoran Mas',
                                        'DPRa Mampang'             => 'DPRa Mampang',
                                        'DPRa Rangkapan Jaya'      => 'DPRa Rangkapan Jaya',
                                        'DPRa Rangkapan Jaya Baru' => 'DPRa Rangkapan Jaya Baru',
                                    ];
                                }
                                $penyelenggaraOptions = array_merge($penyelenggaraOptions, [
                                    'DPC'            => 'DPC PKS Pancoran Mas',
                                    'BPH'            => 'BPH',
                                    'Bipeka'         => 'Bipeka',
                                    'Bid Kepemudaan' => 'Bid Kepemudaan',
                                    'BKO'            => 'BKO',
                                    'Bid Kessos'     => 'Bid Kessos',
                                    'Grup UPA'       => 'Grup UPA',
                                ]);
                                foreach ($penyelenggaraOptions as $val => $lbl):
                                ?>
                                <option value="<?= $val ?>" <?= old('penyelenggara', $isEdit ? $kegiatan['penyelenggara'] : '') == $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">PIC (Penanggung Jawab)</label>
                            <input type="text" name="pic" class="form-control" value="<?= old('pic', $isEdit ? $kegiatan['pic'] : '') ?>" placeholder="Contoh: Pranadi">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jumlah Peserta</label>
                            <input type="number" name="jumlah_peserta" class="form-control" value="<?= old('jumlah_peserta', $isEdit ? $kegiatan['jumlah_peserta'] : '0') ?>">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Lokasi Spesifik Kegiatan</label>
                            <input type="text" name="lokasi_kegiatan" class="form-control" value="<?= old('lokasi_kegiatan', $isEdit ? $kegiatan['lokasi_kegiatan'] : '') ?>" placeholder="Contoh: Rumah Pak Sayuti">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">DPRa (Kelurahan)</label>
                            <select name="dpra" class="form-select">
                                <option value="">-- Pilih DPRa --</option>
                                <?php foreach ($penyelenggaraOptions as $val => $lbl): if (strpos($val, 'DPRa') === 0): ?>
                                <option value="<?= $val ?>" <?= old('dpra', $isEdit ? $kegiatan['dpra'] : '') == $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endif; endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label fw-semibold">RW</label>
                            <input type="text" name="rw" class="form-control" value="<?= old('rw', $isEdit ? $kegiatan['rw'] : '') ?>" placeholder="03">
                        </div>
                        <div class="col-md-2 col-6">
                            <label class="form-label fw-semibold">RT</label>
                            <input type="text" name="rt" class="form-control" value="<?= old('rt', $isEdit ? $kegiatan['rt'] : '') ?>" placeholder="01">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Status Wilayah</label>
                            <select name="status_kondisi" class="form-select">
                                <?php
                                $statusVal = old('status_kondisi', $isEdit ? ($kegiatan['status_kondisi'] ?? 'hijau') : 'hijau');
                                ?>
                                <option value="hijau" <?= $statusVal === 'hijau' ? 'selected' : '' ?>>🟢 Hijau (Aman)</option>
                                <option value="kuning" <?= $statusVal === 'kuning' ? 'selected' : '' ?>>🟡 Kuning (Perhatian)</option>
                                <option value="merah" <?= $statusVal === 'merah' ? 'selected' : '' ?>>🔴 Merah (Rawan)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tokoh Hadir</label>
                            <input type="text" name="tokoh_hadir" class="form-control" value="<?= old('tokoh_hadir', $isEdit ? $kegiatan['tokoh_hadir'] : '') ?>" placeholder="Contoh: Pak RT dan Tomas">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tokoh PKS</label>
                            <input type="text" name="tokoh_pks" class="form-control" value="<?= old('tokoh_pks', $isEdit ? $kegiatan['tokoh_pks'] : '') ?>" placeholder="Contoh: Ketua DPRa">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Catatan / Keterangan Kegiatan</label>
                        <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Kegiatan Memberantas Demam Berdarah"><?= old('catatan', $isEdit ? $kegiatan['catatan'] : '') ?></textarea>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Link Dokumen (URL/Drive)</label>
                            <input type="url" name="link_doc" class="form-control" value="<?= old('link_doc', $isEdit ? $kegiatan['link_doc'] : '') ?>" placeholder="https://drive.google.com/...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Upload Flyer / Foto (Optional)</label>
                            <input type="file" name="flyer" class="form-control" accept="image/*">
                            <?php if ($isEdit && !empty($kegiatan['flyer'])): ?>
                                <small class="text-muted">Flyer saat ini: <?= esc($kegiatan['flyer']) ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn px-5 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
                            <i class="fa-solid fa-save me-2"></i><?= $isEdit ? 'Perbarui' : 'Simpan' ?>
                        </button>
                        <a href="<?= base_url('admin/kegiatan/list') ?>" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
