<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('admin/meja/lembaga') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-landmark text-gold me-2"></i><?= esc($title) ?>
                </h4>
                <small class="text-muted">
                    Lengkapi identitas lembaga dan unggah berkas legalitas resmi pemohon proposal.
                </small>
            </div>
        </div>
    </div>

    <!-- Error Alerts -->
    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>Terdapat kesalahan pada isian form:
            <ul class="mb-0 mt-1">
                <?php foreach (session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= $lembaga ? base_url('admin/meja/lembaga/' . $lembaga['id'] . '/update') : base_url('admin/meja/lembaga/store') ?>" 
          method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Kolom Kiri: Data Identitas & Legalitas -->
            <div class="col-12 col-lg-7">
                <!-- 1. IDENTITAS LEMBAGA -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-building text-gold me-2"></i>1. Identitas Yayasan / Lembaga
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-semibold">Nama Lembaga / Yayasan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lembaga" class="form-control" 
                                       placeholder="Contoh: Yayasan Cahaya Mandiri Pancoran Mas" 
                                       value="<?= old('nama_lembaga', $lembaga['nama_lembaga'] ?? '') ?>" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">Jenis Lembaga <span class="text-danger">*</span></label>
                                <select name="jenis_lembaga" class="form-select" required>
                                    <?php
                                        $jenisOpt = [
                                            'yayasan'             => 'Yayasan',
                                            'dkm'                 => 'DKM / Masjid / Musholla',
                                            'majelis_taklim'      => 'Majelis Taklim',
                                            'lsm'                 => 'LSM / Ormas',
                                            'paud'                => 'PAUD / RA / TPQ',
                                            'kelompok_masyarakat' => 'Pokmas / Forum Warga',
                                            'lainnya'             => 'Lainnya',
                                        ];
                                        $curJenis = old('jenis_lembaga', $lembaga['jenis_lembaga'] ?? 'yayasan');
                                    ?>
                                    <?php foreach ($jenisOpt as $k => $v): ?>
                                        <option value="<?= $k ?>" <?= $curJenis === $k ? 'selected' : '' ?>><?= $v ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Kategori Bidang Gerak</label>
                                <input type="text" name="kategori_bidang" class="form-control" 
                                       placeholder="Contoh: Sosial & Pendidikan / Keagamaan" 
                                       value="<?= old('kategori_bidang', $lembaga['kategori_bidang'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">No. Kontak / WhatsApp Sekretariat</label>
                                <input type="text" name="no_kontak" class="form-control" 
                                       placeholder="0812xxxxxxxx" 
                                       value="<?= old('no_kontak', $lembaga['no_kontak'] ?? '') ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-semibold">Alamat Lengkap Sekretariat</label>
                                <textarea name="alamat_lengkap" class="form-control" rows="2" 
                                          placeholder="Nama Jalan, No. Rumah, RT/RW..."><?= old('alamat_lengkap', $lembaga['alamat_lengkap'] ?? '') ?></textarea>
                            </div>

                            <!-- Wilayah Berjenjang (Provinsi -> Kota/Kab -> Kecamatan -> Kelurahan) -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label small fw-semibold">Provinsi <span class="text-danger">*</span></label>
                                <select name="province_id" id="province_id" class="form-select select2-wilayah" required>
                                    <option value="">-- Pilih Provinsi --</option>
                                    <?php if (!empty($provinces)): ?>
                                        <?php foreach ($provinces as $p): ?>
                                            <option value="<?= $p['id'] ?>" <?= old('province_id', $selectedProv ?? '') == $p['id'] ? 'selected' : '' ?>>
                                                <?= esc($p['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label small fw-semibold">Kota / Kabupaten <span class="text-danger">*</span></label>
                                <select name="regency_id" id="regency_id" class="form-select select2-wilayah" required>
                                    <option value="">-- Pilih Kota/Kab --</option>
                                    <?php if (!empty($regencies)): ?>
                                        <?php foreach ($regencies as $r): ?>
                                            <option value="<?= $r['id'] ?>" <?= old('regency_id', $selectedReg ?? '') == $r['id'] ? 'selected' : '' ?>>
                                                <?= esc($r['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label small fw-semibold">Kecamatan <span class="text-danger">*</span></label>
                                <select name="district_id" id="district_id" class="form-select select2-wilayah" required>
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <?php if (!empty($districts)): ?>
                                        <?php foreach ($districts as $d): ?>
                                            <option value="<?= $d['id'] ?>" <?= old('district_id', $selectedDist ?? '') == $d['id'] ? 'selected' : '' ?>>
                                                <?= esc($d['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-6 col-lg-3">
                                <label class="form-label small fw-semibold">Kelurahan <span class="text-danger">*</span></label>
                                <select name="village_id" id="village_id" class="form-select select2-wilayah" required>
                                    <option value="">-- Pilih Kelurahan --</option>
                                    <?php if (!empty($villages)): ?>
                                        <?php foreach ($villages as $v): ?>
                                            <option value="<?= $v['id'] ?>" <?= old('village_id', $selectedVill ?? '') == $v['id'] ? 'selected' : '' ?>>
                                                <?= esc($v['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Hidden inputs untuk menyimpan nama wilayah untuk template/cetak surat -->
                            <input type="hidden" name="kelurahan" id="nama_kelurahan" value="<?= old('kelurahan', $lembaga['kelurahan'] ?? '') ?>">
                            <input type="hidden" name="kecamatan" id="nama_kecamatan" value="<?= old('kecamatan', $lembaga['kecamatan'] ?? 'Pancoran Mas') ?>">
                            <input type="hidden" name="kota" id="nama_kota" value="<?= old('kota', $lembaga['kota'] ?? 'Kota Depok') ?>">

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Email Lembaga</label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="info@yayasan.org" 
                                       value="<?= old('email', $lembaga['email'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Website / Media Sosial</label>
                                <input type="text" name="website" class="form-control" 
                                       placeholder="https://yayasan.org / @instagram" 
                                       value="<?= old('website', $lembaga['website'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. DATA PIMPINAN / KETUA -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-user-tie text-gold me-2"></i>2. Data Ketua / Pimpinan Lembaga
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Nama Lengkap Ketua</label>
                                <input type="text" name="nama_ketua" class="form-control" 
                                       placeholder="Nama Ketua beserta gelar" 
                                       value="<?= old('nama_ketua', $lembaga['nama_ketua'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">NIK Ketua (16 digit KTP)</label>
                                <input type="text" name="nik_ketua" class="form-control" maxlength="20"
                                       placeholder="3276xxxxxxxxxxxx" 
                                       value="<?= old('nik_ketua', $lembaga['nik_ketua'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">No. HP / WA Ketua</label>
                                <input type="text" name="hp_ketua" class="form-control" 
                                       placeholder="0812xxxxxxxx" 
                                       value="<?= old('hp_ketua', $lembaga['hp_ketua'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">NPWP Pribadi Ketua</label>
                                <input type="text" name="npwp_ketua" class="form-control" 
                                       placeholder="Nomor Pokok Wajib Pajak Ketua" 
                                       value="<?= old('npwp_ketua', $lembaga['npwp_ketua'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Nama Sekretaris</label>
                                <input type="text" name="nama_sekretaris" class="form-control" 
                                       placeholder="Nama Sekretaris Lembaga" 
                                       value="<?= old('nama_sekretaris', $lembaga['nama_sekretaris'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Nama Bendahara</label>
                                <input type="text" name="nama_bendahara" class="form-control" 
                                       placeholder="Nama Bendahara Lembaga" 
                                       value="<?= old('nama_bendahara', $lembaga['nama_bendahara'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. LEGALITAS HUKUM & REKENING BANK -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-scale-balanced text-gold me-2"></i>3. Legalitas Hukum & Rekening Bank
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">No. Akta Notaris Pendirian</label>
                                <input type="text" name="no_akta_notaris" class="form-control" 
                                       placeholder="Contoh: No. 12 Tanggal 10 Januari 2020" 
                                       value="<?= old('no_akta_notaris', $lembaga['no_akta_notaris'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">Nama Notaris</label>
                                <input type="text" name="nama_notaris" class="form-control" 
                                       placeholder="Nama Notaris Pembuat Akta" 
                                       value="<?= old('nama_notaris', $lembaga['nama_notaris'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">Tanggal Akta</label>
                                <input type="date" name="tgl_akta" class="form-control" 
                                       value="<?= old('tgl_akta', $lembaga['tgl_akta'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-8">
                                <label class="form-label small fw-semibold">No. SK Kemenkumham</label>
                                <input type="text" name="no_sk_kemenkumham" class="form-control" 
                                       placeholder="Contoh: AHU-0001234.AH.01.04.Tahun 2020" 
                                       value="<?= old('no_sk_kemenkumham', $lembaga['no_sk_kemenkumham'] ?? '') ?>">
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">NPWP Lembaga / Yayasan</label>
                                <input type="text" name="npwp_lembaga" class="form-control" 
                                       placeholder="NPWP resmi atas nama yayasan" 
                                       value="<?= old('npwp_lembaga', $lembaga['npwp_lembaga'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label small fw-semibold">NIB (Nomor Induk Berusaha)</label>
                                <input type="text" name="nib" class="form-control" 
                                       placeholder="13 Digit NIB OSS" 
                                       value="<?= old('nib', $lembaga['nib'] ?? '') ?>">
                            </div>

                            <div class="col-12"><hr class="my-2 text-muted"></div>

                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">Nama Bank</label>
                                <input type="text" name="nama_bank" class="form-control" 
                                       placeholder="BSI / Mandiri / BJB / BRI / BCA" 
                                       value="<?= old('nama_bank', $lembaga['nama_bank'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">Nomor Rekening</label>
                                <input type="text" name="no_rekening" class="form-control" 
                                       placeholder="Nomor Rekening Bank" 
                                       value="<?= old('no_rekening', $lembaga['no_rekening'] ?? '') ?>">
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-semibold">Atas Nama Rekening</label>
                                <input type="text" name="atas_nama_rekening" class="form-control" 
                                       placeholder="Wajib a.n Yayasan / Lembaga" 
                                       value="<?= old('atas_nama_rekening', $lembaga['atas_nama_rekening'] ?? '') ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. HAK AKSES & PENGURUS BERWENANG MEMBUAT PROPOSAL -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-users-gear text-gold me-2"></i>3. Pengurus Berwenang & Akses Kolaborasi
                        </h6>
                        <span class="badge bg-gold text-dark fw-semibold" style="font-size: 0.72rem;">Multi-User</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert bg-primary-subtle text-dark border-0 rounded-3 p-3 mb-3 small d-flex gap-2">
                            <i class="fa-solid fa-circle-info text-primary fs-5 mt-1"></i>
                            <div>
                                <strong>Tidak Perlu Input Ganda:</strong> Cukup satu orang yang mendaftarkan profil yayasan ini. Pilih pengurus atau anggota di bawah ini agar mereka otomatis dapat memilih yayasan ini saat membuat proposal tanpa harus upload ulang dokumen legalitas.
                            </div>
                        </div>

                        <?php
                            $currentAuth = [];
                            if (!empty($lembaga['authorized_members'])) {
                                $currentAuth = json_decode($lembaga['authorized_members'], true) ?: [];
                                $currentAuth = array_map('strval', $currentAuth);
                            }
                            $isPub = (int)old('is_public', $lembaga['is_public'] ?? 0);
                        ?>

                        <!-- Checkbox Public Sharing -->
                        <div class="form-check form-switch p-3 bg-light rounded-3 mb-3 border">
                            <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="is_public" name="is_public" value="1" <?= $isPub === 1 ? 'checked' : '' ?> style="transform: scale(1.3);">
                            <label class="form-check-label fw-semibold text-dark-coffee" for="is_public">
                                <i class="fa-solid fa-globe text-primary me-1"></i>Buka Akses untuk Seluruh Anggota di Portal
                                <div class="text-muted fw-normal small">
                                    Aktifkan jika yayasan/lembaga ini adalah organisasi bersama atau payung komunitas sehingga semua anggota bebas membuat proposal dengan profil ini.
                                </div>
                            </label>
                        </div>

                        <!-- Multi Select Pengurus Berwenang -->
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">
                                <i class="fa-solid fa-user-plus text-gold me-1"></i>Pilih Anggota / Pengurus Tertentu yang Diberi Wewenang:
                            </label>
                            <select name="authorized_members[]" id="authorized_members" class="form-select select2-members" multiple="multiple" style="width: 100%;">
                                <?php if (!empty($allMembers)): ?>
                                    <?php foreach ($allMembers as $m): ?>
                                        <?php 
                                            $isSelected = in_array((string)$m['id'], $currentAuth, true);
                                        ?>
                                        <option value="<?= $m['id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= esc($m['nama_lengkap']) ?> <?= !empty($m['handphone']) ? '(' . esc($m['handphone']) . ')' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="form-text small">
                                Cari nama anggota pengurus yang berhak (misal: Sekretaris, Bendahara, atau Staf Admin).
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Upload Berkas Legalitas Digital -->
            <div class="col-12 col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 20px; z-index: 10;">
                    <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                        <h6 class="fw-bold mb-0">
                            <i class="fa-solid fa-folder-open text-gold me-2"></i>4. Unggah Berkas Dokumen Legalitas
                        </h6>
                    </div>
                    <div class="card-body p-4" style="max-height: 75vh; overflow-y: auto;">
                        <p class="small text-muted mb-3">
                            Format file didukung: <strong>JPG, PNG, atau PDF</strong> (Maks 5 MB per file). File yang diunggah akan otomatis dibundel ke dalam proposal resmi.
                        </p>

                        <?php
                            $uploadInputs = [
                                'file_logo'             => ['label' => 'Logo Yayasan / Lembaga', 'icon' => 'fa-image', 'desc' => 'Ditampilkan pada Kop Surat'],
                                'file_ktp_ketua'        => ['label' => 'Scan KTP Ketua', 'icon' => 'fa-id-card', 'desc' => 'Syarat mutlak Pokir & Bansos'],
                                'file_npwp_ketua'       => ['label' => 'Scan NPWP Ketua', 'icon' => 'fa-credit-card', 'desc' => 'NPWP pribadi Ketua'],
                                'file_npwp_lembaga'     => ['label' => 'Scan NPWP Lembaga', 'icon' => 'fa-file-invoice', 'desc' => 'NPWP resmi atas nama yayasan'],
                                'file_akta'             => ['label' => 'Scan Akta Notaris Pendirian', 'icon' => 'fa-file-contract', 'desc' => 'Akta notaris pendirian'],
                                'file_sk_kemenkumham'   => ['label' => 'Scan SK Kemenkumham', 'icon' => 'fa-stamp', 'desc' => 'SK pengesahan Menkumham'],
                                'file_nib'              => ['label' => 'Scan NIB (Nomor Induk Berusaha)', 'icon' => 'fa-certificate', 'desc' => 'NIB dari OSS'],
                                'file_buku_rekening'    => ['label' => 'Scan Halaman Depan Buku Tabungan', 'icon' => 'fa-money-check-dollar', 'desc' => 'Buku rekening a.n Yayasan'],
                                'file_domisili'         => ['label' => 'Scan Surat Domisili / Izin', 'icon' => 'fa-file-lines', 'desc' => 'Dari Kelurahan / Kemenag / Disdik'],
                                'file_susunan_pengurus' => ['label' => 'Scan SK Susunan Pengurus', 'icon' => 'fa-users', 'desc' => 'Struktur pengurus lengkap'],
                            ];
                        ?>

                        <?php foreach ($uploadInputs as $fKey => $fInfo): ?>
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <label class="form-label small fw-semibold mb-0">
                                        <i class="fa-solid <?= $fInfo['icon'] ?> text-gold me-1"></i><?= $fInfo['label'] ?>
                                    </label>
                                    <?php if (!empty($lembaga[$fKey])): ?>
                                        <a href="<?= base_url('uploads/lembaga/' . $lembaga[$fKey]) ?>" target="_blank" class="badge bg-success text-decoration-none">
                                            <i class="fa-solid fa-eye me-1"></i>Lihat Berkas
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border">Belum Ada</span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted mb-2" style="font-size: 0.75rem;"><?= $fInfo['desc'] ?></div>
                                <input type="file" name="<?= $fKey ?>" class="form-control form-control-sm" accept=".jpg,.jpeg,.png,.pdf">
                                <?php if (!empty($lembaga[$fKey])): ?>
                                    <div class="text-muted" style="font-size: 0.7rem; margin-top: 3px;">
                                        File saat ini: <code><?= esc($lembaga[$fKey]) ?></code> (Unggah baru untuk mengganti)
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-gold py-2 fw-bold shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Profil & Berkas Legalitas
                            </button>
                            <a href="<?= base_url('admin/meja/lembaga') ?>" class="btn btn-outline-secondary py-2">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        if ($.fn.select2) {
            $('.select2-members').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Ketik nama atau cari pengurus / anggota --',
                allowClear: true,
                width: '100%'
            });
            $('.select2-wilayah').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
        }

        function syncRegionNames() {
            const regName = $('#regency_id option:selected').text().trim();
            const distName = $('#district_id option:selected').text().trim();
            const villName = $('#village_id option:selected').text().trim();

            if ($('#regency_id').val()) $('#nama_kota').val(regName);
            if ($('#district_id').val()) $('#nama_kecamatan').val(distName);
            if ($('#village_id').val()) $('#nama_kelurahan').val(villName);
        }

        // Province changed -> load regencies
        $('#province_id').on('change', function() {
            const provinceId = $(this).val();
            $('#regency_id').html('<option value="">-- Pilih Kota/Kab --</option>').trigger('change');
            
            if (provinceId) {
                $.ajax({
                    url: '<?= base_url('admin/meja/lembaga/get-regencies') ?>',
                    type: 'GET',
                    data: { province_id: provinceId },
                    dataType: 'json',
                    success: function(data) {
                        let html = '<option value="">-- Pilih Kota/Kab --</option>';
                        data.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $('#regency_id').html(html).trigger('change');
                    }
                });
            }
        });

        // Regency changed -> load districts
        $('#regency_id').on('change', function() {
            const regencyId = $(this).val();
            syncRegionNames();
            $('#district_id').html('<option value="">-- Pilih Kecamatan --</option>').trigger('change');
            
            if (regencyId) {
                $.ajax({
                    url: '<?= base_url('admin/meja/lembaga/get-districts') ?>',
                    type: 'GET',
                    data: { regency_id: regencyId },
                    dataType: 'json',
                    success: function(data) {
                        let html = '<option value="">-- Pilih Kecamatan --</option>';
                        data.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $('#district_id').html(html).trigger('change');
                    }
                });
            }
        });

        // District changed -> load villages
        $('#district_id').on('change', function() {
            const districtId = $(this).val();
            syncRegionNames();
            $('#village_id').html('<option value="">-- Pilih Kelurahan --</option>').trigger('change');
            
            if (districtId) {
                $.ajax({
                    url: '<?= base_url('admin/meja/lembaga/get-villages') ?>',
                    type: 'GET',
                    data: { district_id: districtId },
                    dataType: 'json',
                    success: function(data) {
                        let html = '<option value="">-- Pilih Kelurahan --</option>';
                        data.forEach(item => {
                            html += `<option value="${item.id}">${item.name}</option>`;
                        });
                        $('#village_id').html(html).trigger('change');
                    }
                });
            }
        });

        // Village changed -> sync hidden name
        $('#village_id').on('change', function() {
            syncRegionNames();
        });

        // Initial sync on load
        syncRegionNames();
    });
</script>
<?= $this->endSection() ?>
