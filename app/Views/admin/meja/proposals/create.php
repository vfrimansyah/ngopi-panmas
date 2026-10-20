<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('admin/meja/proposals') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-file-signature text-gold me-2"></i>Buat Proposal Baru
                </h4>
                <small class="text-muted">
                    Lengkapi isian form wizard untuk menghasilkan proposal resmi standar Pokir DPRD, CSR, atau Lembaga Sosial.
                </small>
            </div>
        </div>
    </div>

    <!-- Alert Check Lembaga -->
    <?php if (empty($lembagas)): ?>
        <div class="alert bg-warning-subtle text-dark border border-warning-subtle rounded-4 p-4 mb-4">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-2 text-warning"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Anda Belum Mendaftarkan Profil Yayasan / Lembaga</h6>
                    <p class="small mb-2">
                        Agar berkas legalitas (KTP, NPWP, Akta, NIB, & Buku Tabungan) dapat otomatis dibundel ke dalam proposal resmi, disarankan untuk mengisi profil yayasan/lembaga terlebih dahulu.
                    </p>
                    <a href="<?= base_url('admin/meja/lembaga/create') ?>" class="btn btn-dark btn-sm fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i>Daftarkan Lembaga / Yayasan Sekarang
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Error Alerts -->
    <?php if (session('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>Periksa kembali isian form Anda:
            <ul class="mb-0 mt-1">
                <?php foreach (session('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/meja/proposals/store') ?>" method="POST" enctype="multipart/form-data" id="formProposal">
        <?= csrf_field() ?>

        <!-- STEP 1: LEMBAGA & TARGET PENGAJUAN -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-landmark text-gold me-2"></i>Langkah 1: Lembaga Pengusul & Target Pengajuan
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Pilih Yayasan / Lembaga Pengusul <span class="text-danger">*</span></label>
                        <select name="lembaga_id" id="lembaga_id" class="form-select" required onchange="updateLembagaInfo(this.value)">
                            <option value="">-- Pilih Lembaga Anda --</option>
                            <?php foreach ($lembagas as $l): ?>
                                <option value="<?= $l['id'] ?>" <?= ($selectedLembagaId == $l['id'] || old('lembaga_id') == $l['id']) ? 'selected' : '' ?>>
                                    <?= esc($l['nama_lembaga']) ?> (<?= strtoupper(str_replace('_', ' ', esc($l['jenis_lembaga']))) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">
                            <a href="<?= base_url('admin/meja/lembaga/create') ?>" target="_blank" class="text-decoration-none">
                                <i class="fa-solid fa-plus-circle me-1"></i>Tambah lembaga baru
                            </a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Target / Tujuan Pengajuan Proposal <span class="text-danger">*</span></label>
                        <select name="target_pengajuan" id="target_pengajuan" class="form-select" required onchange="applyTemplatePreset(this.value)">
                            <option value="pokir_dprd" <?= old('target_pengajuan', $defaultTarget) === 'pokir_dprd' ? 'selected' : '' ?>>
                                🏛️ Pokir DPRD (Pokok Pikiran / Aspirasi Dewan)
                            </option>
                            <option value="csr_perusahaan" <?= old('target_pengajuan', $defaultTarget) === 'csr_perusahaan' ? 'selected' : '' ?>>
                                🏢 CSR Perusahaan (BUMN / Swasta)
                            </option>
                            <option value="lembaga_sosial" <?= old('target_pengajuan', $defaultTarget) === 'lembaga_sosial' ? 'selected' : '' ?>>
                                🤝 Lembaga Sosial / Baznas / LAZ
                            </option>
                            <option value="hibah_pemda" <?= old('target_pengajuan', $defaultTarget) === 'hibah_pemda' ? 'selected' : '' ?>>
                                🏛️ Hibah / Bansos Pemda (Kesra / Disdik)
                            </option>
                            <option value="sponsor_umum" <?= old('target_pengajuan', $defaultTarget) === 'sponsor_umum' ? 'selected' : '' ?>>
                                📋 Sponsor / Donatur Umum
                            </option>
                        </select>
                    </div>

                    <!-- Lembaga Details Card (Auto-filled via JS) -->
                    <div class="col-12" id="lembagaPreviewBox" style="display: none;">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="small text-muted">Ketua Lembaga:</div>
                                    <div class="fw-bold text-dark" id="txtKetua">-</div>
                                    <div class="small text-muted" id="txtKontak">-</div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="small text-muted">Rekening Bank:</div>
                                    <div class="fw-semibold text-dark" id="txtRekening">-</div>
                                    <div class="small text-muted" id="txtAlamat">-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: ADMINISTRASI SURAT & INSTANSI TUJUAN -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-envelope-open-text text-gold me-2"></i>Langkah 2: Administrasi Surat & Instansi Tujuan
                </h6>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Nomor Surat Proposal</label>
                        <input type="text" name="nomor_surat" id="nomor_surat" class="form-control" 
                               placeholder="Contoh: 012/PROP/YCM/X/2026" 
                               value="<?= old('nomor_surat') ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Tanggal Surat</label>
                        <input type="date" name="tanggal_surat" class="form-control" 
                               value="<?= old('tanggal_surat', date('Y-m-d')) ?>">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small fw-semibold">Perihal Surat</label>
                        <input type="text" name="perihal" class="form-control" 
                               placeholder="Permohonan Bantuan..." 
                               value="<?= old('perihal', 'Permohonan Bantuan Dana / Hibah') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">Judul Usulan / Nama Program <span class="text-danger">*</span></label>
                        <input type="text" name="judul_proposal" id="judul_proposal" class="form-control form-control-lg fw-bold" 
                               placeholder="Contoh: Pengadaan Sarana & Prasarana TPQ Al-Ikhlas RW 05 Pancoran Mas" 
                               value="<?= old('judul_proposal') ?>" required>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Ditujukan Kepada (Nama Pejabat / Tokoh)</label>
                        <input type="text" name="tujuan_nama" id="tujuan_nama" class="form-control" 
                               placeholder="Contoh: Yth. Bpk. H. Anggota DPRD Kota Depok" 
                               value="<?= old('tujuan_nama') ?>">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Instansi / Perusahaan Tujuan</label>
                        <input type="text" name="tujuan_instansi" id="tujuan_instansi" class="form-control" 
                               placeholder="Contoh: DPRD Kota Depok / PT. PLN (Persero)" 
                               value="<?= old('tujuan_instansi') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold">Alamat Tujuan Surat</label>
                        <input type="text" name="tujuan_alamat" id="tujuan_alamat" class="form-control" 
                               placeholder="Contoh: Jl. Margonda Raya No. 54, Kota Depok" 
                               value="<?= old('tujuan_alamat') ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 3: SUBSTANSI PROPOSAL -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-paragraph text-gold me-2"></i>Langkah 3: Narasi & Substansi Usulan
                </h6>
                <button type="button" class="btn btn-outline-warning btn-sm text-gold border-gold" onclick="loadDefaultNarration()">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i>Isi Teks Template Otomatis
                </button>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label small fw-semibold">A. Latar Belakang & Urgensi Masalah</label>
                        <textarea name="latar_belakang" id="latar_belakang" class="form-control" rows="4" 
                                  placeholder="Ceritakan latar belakang perlunya program/kegiatan ini, kondisi fisik saat ini, dan masalah yang dihadapi masyarakat..."><?= old('latar_belakang') ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">B. Maksud & Tujuan Usulan</label>
                        <textarea name="maksud_tujuan" id="maksud_tujuan" class="form-control" rows="3" 
                                  placeholder="1. Memberikan sarana belajar yang layak bagi anak yatim...&#10;2. Meringankan beban operasional..."><?= old('maksud_tujuan') ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">C. Deskripsi Rencana Kegiatan / Spesifikasi Bantuan</label>
                        <textarea name="deskripsi_kegiatan" id="deskripsi_kegiatan" class="form-control" rows="3" 
                                  placeholder="Jelaskan spesifikasi kebutuhan atau bentuk kegiatan yang akan dilaksanakan..."><?= old('deskripsi_kegiatan') ?></textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">D. Sasaran Penerima Manfaat</label>
                        <input type="text" name="sasaran_penerima" class="form-control" 
                               placeholder="Contoh: 120 Santri & Warga Dhuafa di lingkungan RW 05" 
                               value="<?= old('sasaran_penerima') ?>">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">E. Waktu Pelaksanaan / Rencana Realisasi</label>
                        <input type="text" name="waktu_pelaksanaan" class="form-control" 
                               placeholder="Contoh: Triwulan I Tahun Anggaran 2026" 
                               value="<?= old('waktu_pelaksanaan') ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-semibold">F. Lokasi Pelaksanaan / Titik Fasilitas</label>
                        <input type="text" name="lokasi_kegiatan" class="form-control" 
                               placeholder="Contoh: Jl. Cendrawasih RT 03/RW 05, Kelurahan Depok, Kec. Pancoran Mas" 
                               value="<?= old('lokasi_kegiatan') ?>">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">G. Susunan Tim Pelaksana / Kepanitiaan</label>
                        <textarea name="susunan_panitia" class="form-control" rows="3" 
                                  placeholder="Penanggung Jawab: ...&#10;Ketua Pelaksana: ...&#10;Sekretaris: ...&#10;Bendahara: ..."><?= old('susunan_panitia') ?></textarea>
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">H. Kalimat Penutup Proposal</label>
                        <textarea name="penutup" id="penutup" class="form-control" rows="3" 
                                  placeholder="Demikian proposal permohonan ini kami sampaikan, atas perhatian dan dukungan Bapak/Ibu kami haturkan terima kasih."><?= old('penutup') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 4: RENCANA ANGGARAN BIAYA (RAB) DINAMIS -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-calculator text-gold me-2"></i>Langkah 4: Rencana Anggaran Biaya (RAB) Dinamis
                </h6>
                <button type="button" class="btn btn-gold btn-sm fw-semibold" onclick="addRabRow()">
                    <i class="fa-solid fa-plus me-1"></i>Tambah Baris Anggaran
                </button>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="tableRab">
                        <thead class="table-light text-center small">
                            <tr>
                                <th style="width: 140px;">Kategori</th>
                                <th>Uraian Kebutuhan / Spesifikasi Barang</th>
                                <th style="width: 90px;">Volume</th>
                                <th style="width: 100px;">Satuan</th>
                                <th style="width: 170px;">Harga Satuan (Rp)</th>
                                <th style="width: 170px;">Total (Rp)</th>
                                <th style="width: 50px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rabBody">
                            <!-- Rows generated via JavaScript -->
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end fw-bold py-3 fs-6">TOTAL RENCANA ANGGARAN BIAYA:</th>
                                <th class="text-end fw-bold text-success py-3 fs-5" id="grandTotalDisplay">Rp 0</th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="7" class="bg-light-subtle py-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="small fw-bold text-dark-coffee">Terbilang:</span>
                                        <span class="small fst-italic text-primary fw-semibold" id="terbilangDisplay">Nol Rupiah</span>
                                    </div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- STEP 5: CHECKLIST LAMPIRAN LEGALITAS & FOTO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-dark-coffee text-white rounded-top-4 py-3 px-4 border-0">
                <h6 class="fw-bold mb-0">
                    <i class="fa-solid fa-paperclip text-gold me-2"></i>Langkah 5: Checklist Lampiran Dokumen Legalitas & Foto
                </h6>
            </div>
            <div class="card-body p-4">
                <p class="small text-muted mb-3">
                    Pilih dokumen legalitas yang ingin otomatis dilampirkan pada berkas cetak proposal:
                </p>

                <div class="row g-3 mb-4">
                    <?php
                        $legalChecklist = [
                            'ktp_ketua'           => 'Scan KTP Ketua / Pimpinan (Wajib Pokir/Bansos)',
                            'npwp_ketua'          => 'Scan NPWP Pribadi Ketua',
                            'npwp_lembaga'        => 'Scan NPWP Resmi Lembaga / Yayasan',
                            'akta'                => 'Scan Akta Pendirian Notaris',
                            'sk_kemenkumham'      => 'Scan SK Pengesahan Kemenkumham',
                            'nib'                 => 'Scan NIB (Nomor Induk Berusaha)',
                            'buku_rekening'       => 'Scan Buku Rekening Bank Lembaga',
                            'domisili'            => 'Scan Surat Keterangan Domisili / Izin',
                            'susunan_pengurus'    => 'Scan Susunan Kepengurusan',
                        ];
                    ?>
                    <?php foreach ($legalChecklist as $cKey => $cLabel): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="form-check p-3 border rounded-3 bg-light-subtle">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="attachments[]" 
                                       value="<?= $cKey ?>" id="chk_<?= $cKey ?>" checked>
                                <label class="form-check-label small fw-semibold text-dark" for="chk_<?= $cKey ?>">
                                    <?= $cLabel ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold">
                        <i class="fa-solid fa-camera text-gold me-1"></i>Unggah Foto Dokumentasi Lapangan / Kondisi Fasilitas (Opsional)
                    </label>
                    <input type="file" name="foto_kegiatan[]" class="form-control" multiple accept="image/*">
                    <div class="form-text small">
                        Bisa pilih beberapa foto sekaligus (JPG/PNG). Foto akan ditampilkan di halaman lampiran dokumentasi proposal.
                    </div>
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTONS -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-5 bg-white">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="small text-muted">
                    <i class="fa-solid fa-circle-info text-info me-1"></i>
                    Setelah disimpan, Anda dapat langsung melihat pratinjau dokumen dan mengunduh format <strong>PDF Siap Cetak</strong> atau <strong>Microsoft Word</strong>.
                </div>
                <div class="d-flex gap-2 w-100 w-md-auto">
                    <a href="<?= base_url('admin/meja/proposals') ?>" class="btn btn-outline-secondary px-4 py-2">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-gold px-4 py-2 fw-bold flex-fill shadow-sm">
                        <i class="fa-solid fa-file-check me-2"></i>Simpan & Pratinjau Proposal
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Data Lembagas in JS
const lembagasData = <?= json_encode($lembagas) ?>;

function updateLembagaInfo(id) {
    const box = document.getElementById('lembagaPreviewBox');
    if (!id) {
        box.style.display = 'none';
        return;
    }
    const found = lembagasData.find(l => l.id == id);
    if (found) {
        document.getElementById('txtKetua').textContent = found.nama_ketua || '-';
        document.getElementById('txtKontak').textContent = 'HP/WA: ' + (found.hp_ketua || found.no_kontak || '-');
        document.getElementById('txtRekening').textContent = (found.nama_bank || '-') + ' ' + (found.no_rekening || '-') + ' (a.n ' + (found.atas_nama_rekening || '-') + ')';
        document.getElementById('txtAlamat').textContent = found.alamat_lengkap || (found.kelurahan + ', ' + found.kecamatan);
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

function applyTemplatePreset(target) {
    const tujuanNama = document.getElementById('tujuan_nama');
    const tujuanInstansi = document.getElementById('tujuan_instansi');
    const tujuanAlamat = document.getElementById('tujuan_alamat');

    if (target === 'pokir_dprd') {
        tujuanNama.value = 'Yth. Anggota DPRD Kota Depok';
        tujuanInstansi.value = 'DPRD Kota Depok / Bagian Kesra Setda Kota Depok';
        tujuanAlamat.value = 'Jl. Margonda Raya No. 54, Kota Depok';
    } else if (target === 'csr_perusahaan') {
        tujuanNama.value = 'Yth. Pimpinan / Manager CSR & TJSL';
        tujuanInstansi.value = 'PT. PLN (Persero) / PT. Telkom Indonesia';
        tujuanAlamat.value = 'Kantor Pusat / Wilayah Jabodetabek';
    } else if (target === 'lembaga_sosial') {
        tujuanNama.value = 'Yth. Ketua / Direktur Pendistribusian & Pendayagunaan';
        tujuanInstansi.value = 'BAZNAS Kota Depok / Lembaga Amil Zakat';
        tujuanAlamat.value = 'Kota Depok';
    } else if (target === 'hibah_pemda') {
        tujuanNama.value = 'Yth. Wali Kota Depok c.q. Kepala Bagian Kesejahteraan Rakyat (Kesra)';
        tujuanInstansi.value = 'Sekretariat Daerah Kota Depok';
        tujuanAlamat.value = 'Balaikota Depok, Jl. Margonda Raya No. 54';
    }
}

function loadDefaultNarration() {
    const target = document.getElementById('target_pengajuan').value;
    const judul = document.getElementById('judul_proposal').value || 'program yang diajukan';
    
    let latar = '';
    let tujuan = '';
    let deskripsi = '';
    let penutup = '';

    if (target === 'pokir_dprd') {
        latar = `Dalam rangka meningkatkan kualitas sarana dan prasarana masyarakat serta mendukung program pembangunan partisipatif di wilayah Kota Depok, keberadaan fasilitas yang memadai sangat dibutuhkan oleh warga. Saat ini kondisi sarana penunjang kegiatan di lingkungan kami masih menghadapi keterbatasan sarana dan prasarana sehingga memerlukan perbaikan dan dukungan guna optimalisasi manfaat bagi warga masyarakat luas.`;
        tujuan = `1. Terwujudnya sarana dan prasarana yang layak, aman, dan memadai bagi masyarakat.\n2. Mendukung program pemerataan pembangunan daerah melalui aspirasi pokok pikiran dewan.\n3. Meningkatkan kesejahteraan, kapasitas, dan kemaslahatan warga penerima manfaat.`;
        deskripsi = `Pengajuan usulan ini mencakup pengadaan barang, perbaikan fasilitas fisik, dan penyediaan perlengkapan penunjang sebagaimana dirincikan dalam Rencana Anggaran Biaya (RAB) terlampir.`;
        penutup = `Demikian proposal permohonan aspirasi pokok pikiran dewan ini kami sampaikan dengan penuh harapan agar dapat diperjuangkan dan direalisasikan pada Tahun Anggaran berjalan. Atas perhatian, dukungan, dan kebijaksanaan Bapak/Ibu Anggota Dewan, kami haturkan terima kasih.`;
    } else if (target === 'csr_perusahaan') {
        latar = `Sebagai wujud kepedulian terhadap kemajuan sosial dan kesejahteraan masyarakat di sekitar lingkungan operasional, kami berinisiatif menghadirkan program yang berorientasi pada pemberdayaan berkelanjutan. Melalui kemitraan Tanggung Jawab Sosial dan Lingkungan (TJSL/CSR), diharapkan terjalin sinergi yang harmonis antara dunia usaha dengan masyarakat.`;
        tujuan = `1. Menyalurkan program TJSL/CSR perusahaan tepat sasaran kepada masyarakat yang membutuhkan.\n2. Menciptakan nilai tambah sosial (Social Value) dan dampak keberlanjutan bagi penerima manfaat.\n3. Memperkuat citra positif perusahaan sebagai mitra peduli kemajuan bangsa.`;
        deskripsi = `Kegiatan ini akan dilaksanakan secara transparan dan akuntabel, dengan melibatkan partisipasi aktif masyarakat dan didokumentasikan sebagai laporan pertanggungjawaban (LPJ) resmi kepada pihak perusahaan.`;
        penutup = `Besar harapan kami kiranya pihak Manajemen Perusahaan dapat menyetujui dan berpartisipasi dalam program kemitraan ini. Atas sinergi dan perhatian yang diberikan, kami sampaikan terima kasih.`;
    } else {
        latar = `Alhamdulillah puji syukur kita panjatkan ke hadirat Allah SWT. Dalam upaya menebarkan kemanfaatan dan meringankan beban sesama, khususnya kaum dhuafa dan penerima manfaat asnaf yang membutuhkan uluran tangan, kami bermaksud merealisasikan ${judul}.`;
        tujuan = `1. Menyalurkan bantuan dana sosial/infak secara amanah dan tepat sasaran.\n2. Memberikan kebahagiaan dan memenuhi kebutuhan mendesak para mustahik dan santri.\n3. Memakmurkan syiar dakwah dan kepedulian sosial di wilayah Pancoran Mas.`;
        deskripsi = `Bantuan yang dimohonkan akan dialokasikan seutuhnya untuk keperluan mustahik dan operasional sarana dakwah sesuai dengan rincian anggaran yang terlampir.`;
        penutup = `Demikian permohonan bantuan ini kami sampaikan. Semoga Allah SWT senantiasa melimpahkan berkah dan pahala yang berlipat ganda bagi para donatur dan muhsinin. Jazakumullah khairan katsiran.`;
    }

    document.getElementById('latar_belakang').value = latar;
    document.getElementById('maksud_tujuan').value = tujuan;
    document.getElementById('deskripsi_kegiatan').value = deskripsi;
    document.getElementById('penutup').value = penutup;
}

// ── RAB DYNAMIC ROWS ──
let rowCounter = 0;
const defaultCategories = ['Pengadaan Barang / Alat', 'Bahan & Material', 'Jasa & Pengerjaan', 'Operasional & Konsumsi', 'Lain-lain'];

function addRabRow(kategori = 'Pengadaan Barang / Alat', uraian = '', volume = 1, satuan = 'Unit', harga = 0) {
    rowCounter++;
    const tbody = document.getElementById('rabBody');
    const tr = document.createElement('tr');
    tr.id = 'rab_row_' + rowCounter;

    let catOptions = '';
    defaultCategories.forEach(c => {
        catOptions += `<option value="${c}" ${c === kategori ? 'selected' : ''}>${c}</option>`;
    });

    tr.innerHTML = `
        <td>
            <select name="rab_kategori[]" class="form-select form-select-sm">
                ${catOptions}
            </select>
        </td>
        <td>
            <input type="text" name="rab_uraian[]" class="form-control form-control-sm" placeholder="Nama item kebutuhan..." value="${uraian}" required>
        </td>
        <td>
            <input type="number" step="any" name="rab_volume[]" class="form-control form-control-sm text-center rab-vol" value="${volume}" min="0" oninput="calcRow(${rowCounter})">
        </td>
        <td>
            <input type="text" name="rab_satuan[]" class="form-control form-control-sm text-center" value="${satuan}" placeholder="Unit/Pcs">
        </td>
        <td>
            <input type="number" name="rab_harga[]" class="form-control form-control-sm text-end rab-harga" value="${harga}" min="0" oninput="calcRow(${rowCounter})">
        </td>
        <td class="text-end fw-semibold text-dark rab-subtotal" id="subtotal_${rowCounter}">
            Rp 0
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm p-1" onclick="removeRabRow(${rowCounter})">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    calcRow(rowCounter);
}

function removeRabRow(id) {
    const row = document.getElementById('rab_row_' + id);
    if (row) {
        row.remove();
        calcGrandTotal();
    }
}

function calcRow(id) {
    const row = document.getElementById('rab_row_' + id);
    if (!row) return;

    const vol = parseFloat(row.querySelector('.rab-vol').value) || 0;
    const harga = parseFloat(row.querySelector('.rab-harga').value) || 0;
    const subtotal = vol * harga;

    document.getElementById('subtotal_' + id).textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    calcGrandTotal();
}

function calcGrandTotal() {
    let total = 0;
    const rows = document.querySelectorAll('#rabBody tr');
    rows.forEach(r => {
        const vol = parseFloat(r.querySelector('.rab-vol').value) || 0;
        const harga = parseFloat(r.querySelector('.rab-harga').value) || 0;
        total += (vol * harga);
    });

    document.getElementById('grandTotalDisplay').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('terbilangDisplay').textContent = terbilang(total) + ' Rupiah';
}

// Client-side Terbilang
function terbilang(angka) {
    angka = Math.abs(angka);
    const huruf = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    let temp = "";

    if (angka < 12) {
        temp = " " + huruf[Math.floor(angka)];
    } else if (angka < 20) {
        temp = terbilang(angka - 10) + " Belas";
    } else if (angka < 100) {
        temp = terbilang(Math.floor(angka / 10)) + " Puluh" + terbilang(angka % 10);
    } else if (angka < 200) {
        temp = " Seratus" + terbilang(angka - 100);
    } else if (angka < 1000) {
        temp = terbilang(Math.floor(angka / 100)) + " Ratus" + terbilang(angka % 100);
    } else if (angka < 2000) {
        temp = " Seribu" + terbilang(angka - 1000);
    } else if (angka < 1000000) {
        temp = terbilang(Math.floor(angka / 1000)) + " Ribu" + terbilang(angka % 1000);
    } else if (angka < 1000000000) {
        temp = terbilang(Math.floor(angka / 1000000)) + " Juta" + terbilang(angka % 1000000);
    } else if (angka < 1000000000000) {
        temp = terbilang(Math.floor(angka / 1000000000)) + " Milyar" + terbilang(angka % 1000000000);
    }

    return temp.trim();
}

// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    const selLembaga = document.getElementById('lembaga_id');
    if (selLembaga && selLembaga.value) {
        updateLembagaInfo(selLembaga.value);
    }
    const selTarget = document.getElementById('target_pengajuan');
    if (selTarget && selTarget.value) {
        applyTemplatePreset(selTarget.value);
    }

    // Default sample RAB rows
    addRabRow('Pengadaan Barang / Alat', 'Pengadaan Sound System Portable & Mic Wireless', 1, 'Paket', 3500000);
    addRabRow('Pengadaan Barang / Alat', 'Pengadaan Karpet Masjid / TPQ Standar Tebal (1.2m x 6m)', 5, 'Roll', 1200000);
    addRabRow('Bahan & Material', 'Pengecatan dan Perapihan Dinding Fasilitas', 1, 'Paket', 2500000);
});
</script>

<?= $this->endSection() ?>
