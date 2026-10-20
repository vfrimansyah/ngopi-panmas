<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-3">
    <!-- Action Bar -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="<?= base_url('admin/meja/proposals') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-eye text-gold me-2"></i>Pratinjau Proposal Resmi
                </h4>
                <small class="text-muted">
                    <?= esc($proposal['judul_proposal']) ?>
                </small>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= base_url('admin/meja/proposals/' . $proposal['id'] . '/edit') ?>" class="btn btn-outline-secondary btn-sm shadow-sm">
                <i class="fa-solid fa-pen me-1"></i>Edit Isian
            </a>
            <a href="<?= base_url('admin/meja/proposals/' . $proposal['id'] . '/word') ?>" class="btn btn-outline-success btn-sm shadow-sm">
                <i class="fa-solid fa-file-word me-1"></i>Unduh Word (.doc)
            </a>
            <a href="<?= base_url('admin/meja/proposals/' . $proposal['id'] . '/print') ?>" target="_blank" class="btn btn-gold btn-sm fw-bold shadow-sm">
                <i class="fa-solid fa-print me-1"></i>Cetak / Simpan PDF (A4)
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Status & Admin Controls -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="small fw-semibold text-muted">Status Pengajuan:</span>
                <?php
                    $statusBadge = match($proposal['status']) {
                        'diajukan'   => ['bg' => 'info',    'label' => 'Diajukan'],
                        'disetujui'  => ['bg' => 'success', 'label' => 'Disetujui'],
                        'ditolak'    => ['bg' => 'danger',  'label' => 'Ditolak'],
                        'selesai'    => ['bg' => 'dark',    'label' => 'Selesai'],
                        default      => ['bg' => 'secondary', 'label' => 'Draft'],
                    };
                ?>
                <span class="badge bg-<?= $statusBadge['bg'] ?> px-3 py-2 fs-6">
                    <?= $statusBadge['label'] ?>
                </span>
                <span class="badge bg-light text-dark border">
                    Target: <?= strtoupper(str_replace('_', ' ', $proposal['target_pengajuan'])) ?>
                </span>
            </div>

            <?php if (!empty($isAdmin)): ?>
                <form action="<?= base_url('admin/proposals/' . $proposal['id'] . '/status') ?>" method="POST" class="d-flex align-items-center gap-2">
                    <?= csrf_field() ?>
                    <span class="small fw-semibold text-muted">Ubah Status (Admin):</span>
                    <select name="status" class="form-select form-select-sm" style="width: 140px;">
                        <option value="draft" <?= $proposal['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="diajukan" <?= $proposal['status'] === 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
                        <option value="disetujui" <?= $proposal['status'] === 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="ditolak" <?= $proposal['status'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        <option value="selesai" <?= $proposal['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-dark">Update</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paper Container Simulation (A4 Look) -->
    <div class="mx-auto bg-white shadow rounded-3 p-5 my-3" style="max-width: 850px; min-height: 1100px; font-family: 'Times New Roman', Times, serif; color: #111; line-height: 1.6;">
        
        <!-- KOP SURAT RESMI -->
        <div class="border-bottom border-dark border-3 pb-3 mb-4 d-flex align-items-center gap-4">
            <?php if (!empty($proposal['file_logo'])): ?>
                <img src="<?= base_url('uploads/lembaga/' . $proposal['file_logo']) ?>" style="width: 90px; height: 90px; object-fit: contain;">
            <?php endif; ?>
            <div class="flex-fill text-center">
                <h4 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; font-size: 1.3rem;">
                    <?= esc($proposal['nama_lembaga'] ?: 'YAYASAN / LEMBAGA MASYARAKAT') ?>
                </h4>
                <?php if (!empty($proposal['no_sk_kemenkumham'])): ?>
                    <div style="font-size: 0.85rem; font-weight: bold;">
                        SK KEMENKUMHAM: <?= esc($proposal['no_sk_kemenkumham']) ?>
                    </div>
                <?php endif; ?>
                <div style="font-size: 0.82rem;">
                    <?= esc($proposal['alamat_lembaga'] ?: ($proposal['kelurahan_lembaga'] . ', ' . $proposal['kecamatan_lembaga'] . ', ' . $proposal['kota_lembaga'])) ?>
                </div>
                <div style="font-size: 0.8rem;">
                    Telepon/WA: <?= esc($proposal['telp_lembaga'] ?: $proposal['hp_ketua'] ?: '-') ?> | Email: <?= esc($proposal['email_lembaga'] ?: '-') ?>
                </div>
            </div>
        </div>

        <!-- SURAT PENGANTAR PERMOHONAN -->
        <div class="mb-5">
            <div class="d-flex justify-content-between mb-3" style="font-size: 0.95rem;">
                <div>
                    <table>
                        <tr>
                            <td style="width: 80px;">Nomor</td>
                            <td style="width: 15px;">:</td>
                            <td><strong><?= esc($proposal['nomor_surat'] ?: '-') ?></strong></td>
                        </tr>
                        <tr>
                            <td>Lampiran</td>
                            <td>:</td>
                            <td>1 (Satu) Berkas Proposal & Legalitas</td>
                        </tr>
                        <tr>
                            <td>Perihal</td>
                            <td>:</td>
                            <td><strong><?= esc($proposal['perihal']) ?></strong></td>
                        </tr>
                    </table>
                </div>
                <div class="text-end">
                    <?= esc($proposal['kota_lembaga'] ?: 'Depok') ?>, <?= $proposal['tanggal_surat'] ? date('d F Y', strtotime($proposal['tanggal_surat'])) : date('d F Y') ?>
                </div>
            </div>

            <div class="my-4" style="font-size: 0.95rem;">
                Kepada Yth.<br>
                <strong><?= esc($proposal['tujuan_nama'] ?: 'Bapak/Ibu Pimpinan') ?></strong><br>
                <?= esc($proposal['tujuan_instansi'] ?: '') ?><br>
                di -<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<?= esc($proposal['tujuan_alamat'] ?: 'Tempat') ?>
            </div>

            <p style="text-align: justify; text-indent: 2rem;">
                Dengan hormat,<br>
                Teriring salam dan doa semoga Bapak/Ibu senantiasa berada dalam lindungan Tuhan Yang Maha Esa serta dimudahkan dalam menjalankan amanah dan aktivitas sehari-hari.
            </p>

            <p style="text-align: justify; text-indent: 2rem;">
                Dalam rangka peningkatan sarana, prasarana, serta keberdayaan masyarakat, melalui surat ini kami dari <strong><?= esc($proposal['nama_lembaga']) ?></strong> bermaksud mengajukan usulan permohonan bantuan dana / hibah untuk program:
            </p>

            <div class="p-3 my-3 bg-light border text-center fw-bold" style="font-size: 1.05rem;">
                "<?= esc($proposal['judul_proposal']) ?>"
            </div>

            <p style="text-align: justify; text-indent: 2rem;">
                Adapun total rencana anggaran biaya yang dibutuhkan sebesar <strong>Rp <?= number_format((float)$proposal['total_anggaran'], 0, ',', '.') ?> (<?= esc($proposal['terbilang_anggaran']) ?>)</strong> sebagaimana rincian anggaran terlampir pada proposal ini.
            </p>

            <p style="text-align: justify; text-indent: 2rem;">
                Besar harapan kami kiranya Bapak/Ibu berkenan mengabulkan permohonan ini. Sebagai bahan pertimbangan, bersama surat ini kami lampirkan naskah proposal, rincian RAB, profil lembaga, serta dokumen legalitas lengkap.
            </p>

            <p style="text-align: justify; text-indent: 2rem;">
                Demikian surat permohonan ini kami sampaikan. Atas perhatian, dukungan, dan perkenan Bapak/Ibu, kami haturkan terima kasih.
            </p>

            <!-- Tanda Tangan Surat Pengantar -->
            <div class="row mt-5" style="page-break-inside: avoid;">
                <div class="col-6 text-center">
                    <div>Sekretaris,</div>
                    <div style="height: 60px;"></div>
                    <div class="fw-bold text-decoration-underline"><?= esc($proposal['nama_sekretaris'] ?: '....................................') ?></div>
                </div>
                <div class="col-6 text-center">
                    <div>Ketua / Pimpinan,</div>
                    <div style="height: 60px;"></div>
                    <div class="fw-bold text-decoration-underline"><?= esc($proposal['nama_ketua'] ?: '....................................') ?></div>
                </div>
            </div>
        </div>

        <hr class="my-5 border-2 border-dark" style="page-break-after: always;">

        <!-- NASKAH PROPOSAL UTAMA -->
        <div class="text-center mb-5">
            <h5 class="fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">PROPOSAL USULAN BANTUAN</h5>
            <h4 class="fw-bold text-uppercase mb-2"><?= esc($proposal['judul_proposal']) ?></h4>
            <div class="small fw-semibold text-muted text-uppercase">
                DIAJUKAN OLEH: <?= esc($proposal['nama_lembaga']) ?>
            </div>
        </div>

        <!-- BAB I -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase border-bottom pb-1">BAB I. PENDAHULUAN</h6>
            
            <div class="fw-bold mt-3 mb-1">1.1 Latar Belakang</div>
            <p style="text-align: justify; text-indent: 2rem; white-space: pre-line;">
                <?= esc($proposal['latar_belakang'] ?: 'Latar belakang kegiatan belum diisi.') ?>
            </p>

            <div class="fw-bold mt-3 mb-1">1.2 Maksud dan Tujuan</div>
            <div style="text-align: justify; white-space: pre-line;">
                <?= esc($proposal['maksud_tujuan'] ?: 'Maksud dan tujuan kegiatan belum diisi.') ?>
            </div>
        </div>

        <!-- BAB II -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase border-bottom pb-1">BAB II. RENCANA USULAN KEGIATAN & SASARAN</h6>
            
            <div class="fw-bold mt-3 mb-1">2.1 Deskripsi Rencana Kegiatan / Pengadaan</div>
            <p style="text-align: justify; text-indent: 2rem; white-space: pre-line;">
                <?= esc($proposal['deskripsi_kegiatan'] ?: 'Deskripsi kegiatan belum diisi.') ?>
            </p>

            <table class="table table-borderless table-sm my-2">
                <tr>
                    <td style="width: 220px;" class="fw-bold">2.2 Sasaran Penerima Manfaat</td>
                    <td style="width: 15px;">:</td>
                    <td><?= esc($proposal['sasaran_penerima'] ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">2.3 Waktu Realisasi Rencana</td>
                    <td>:</td>
                    <td><?= esc($proposal['waktu_pelaksanaan'] ?: '-') ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">2.4 Lokasi Fasilitas / Kegiatan</td>
                    <td>:</td>
                    <td><?= esc($proposal['lokasi_kegiatan'] ?: '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- BAB III: RAB -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase border-bottom pb-1">BAB III. RENCANA ANGGARAN BIAYA (RAB)</h6>
            <p class="small text-muted mb-2">Berikut rincian kebutuhan biaya yang diajukan secara terperinci:</p>

            <table class="table table-bordered table-sm align-middle" style="font-size: 0.9rem;">
                <thead class="table-light text-center">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Kategori & Uraian Kebutuhan</th>
                        <th style="width: 70px;">Vol</th>
                        <th style="width: 80px;">Satuan</th>
                        <th style="width: 120px;">Harga Satuan (Rp)</th>
                        <th style="width: 130px;">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($rabs)): ?>
                        <?php foreach ($rabs as $idx => $r): ?>
                            <tr>
                                <td class="text-center"><?= $idx + 1 ?></td>
                                <td>
                                    <div class="fw-bold"><?= esc($r['uraian']) ?></div>
                                    <small class="text-muted"><?= esc($r['kategori']) ?> <?= !empty($r['keterangan']) ? ' - ' . esc($r['keterangan']) : '' ?></small>
                                </td>
                                <td class="text-center"><?= rtrim(rtrim($r['volume'], '0'), '.') ?></td>
                                <td class="text-center"><?= esc($r['satuan']) ?></td>
                                <td class="text-end"><?= number_format((float)$r['harga_satuan'], 0, ',', '.') ?></td>
                                <td class="text-end fw-semibold"><?= number_format((float)$r['total_harga'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">- Belum ada rincian anggaran -</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th colspan="5" class="text-end fw-bold">TOTAL ANGGARAN BIAYA:</th>
                        <th class="text-end fw-bold text-dark">Rp <?= number_format((float)$proposal['total_anggaran'], 0, ',', '.') ?></th>
                    </tr>
                    <tr>
                        <td colspan="6" class="fst-italic py-2" style="font-size: 0.85rem;">
                            <strong>Terbilang:</strong> <?= esc($proposal['terbilang_anggaran']) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <?php if (!empty($proposal['nama_bank']) && !empty($proposal['no_rekening'])): ?>
                <div class="p-3 bg-light border rounded small mt-3">
                    <strong>Penyaluran Dana Bantuan dapat ditransfer melalui:</strong><br>
                    Nama Bank: <strong><?= esc($proposal['nama_bank']) ?></strong><br>
                    Nomor Rekening: <strong><?= esc($proposal['no_rekening']) ?></strong><br>
                    Atas Nama: <strong><?= esc($proposal['atas_nama_rekening']) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- BAB IV: SUSUNAN PANITIA -->
        <?php if (!empty($proposal['susunan_panitia'])): ?>
            <div class="mb-4">
                <h6 class="fw-bold text-uppercase border-bottom pb-1">BAB IV. SUSUNAN TIM PELAKSANA</h6>
                <div style="white-space: pre-line;" class="ps-3">
                    <?= esc($proposal['susunan_panitia']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- BAB V: PENUTUP -->
        <div class="mb-4">
            <h6 class="fw-bold text-uppercase border-bottom pb-1">BAB V. PENUTUP</h6>
            <p style="text-align: justify; text-indent: 2rem; white-space: pre-line;">
                <?= esc($proposal['penutup'] ?: 'Demikian proposal ini kami sampaikan, atas perhatian dan dukungannya kami haturkan terima kasih.') ?>
            </p>
        </div>

        <!-- LEMBAR PENGESAHAN -->
        <div class="mt-5" style="page-break-inside: avoid;">
            <div class="text-center mb-4">
                <?= esc($proposal['kota_lembaga'] ?: 'Depok') ?>, <?= $proposal['tanggal_surat'] ? date('d F Y', strtotime($proposal['tanggal_surat'])) : date('d F Y') ?><br>
                <strong>TIM PELAKSANA / PENGURUS <?= strtoupper(esc($proposal['nama_lembaga'])) ?></strong>
            </div>
            <div class="row text-center">
                <div class="col-6">
                    <div>Sekretaris,</div>
                    <div style="height: 65px;"></div>
                    <div class="fw-bold text-decoration-underline"><?= esc($proposal['nama_sekretaris'] ?: '....................................') ?></div>
                </div>
                <div class="col-6">
                    <div>Ketua / Pimpinan,</div>
                    <div style="height: 65px;"></div>
                    <div class="fw-bold text-decoration-underline"><?= esc($proposal['nama_ketua'] ?: '....................................') ?></div>
                </div>
            </div>
        </div>

        <!-- LAMPIRAN BERKAS LEGALITAS & FOTO -->
        <div class="mt-5 pt-4 border-top border-2 border-dark" style="page-break-before: always;">
            <h6 class="fw-bold text-uppercase text-center mb-4">LAMPIRAN DOKUMEN LEGALITAS & DOKUMENTASI</h6>

            <div class="row g-3">
                <?php
                    $attachLabels = [
                        'ktp_ketua'        => ['label' => 'Scan KTP Ketua', 'file' => $proposal['file_ktp_ketua'] ?? null],
                        'npwp_ketua'       => ['label' => 'Scan NPWP Ketua', 'file' => $proposal['file_npwp_ketua'] ?? null],
                        'npwp_lembaga'     => ['label' => 'Scan NPWP Lembaga', 'file' => $proposal['file_npwp_lembaga'] ?? null],
                        'akta'             => ['label' => 'Scan Akta Notaris', 'file' => $proposal['file_akta'] ?? null],
                        'sk_kemenkumham'   => ['label' => 'Scan SK Kemenkumham', 'file' => $proposal['file_sk_kemenkumham'] ?? null],
                        'nib'              => ['label' => 'Scan NIB', 'file' => $proposal['file_nib'] ?? null],
                        'buku_rekening'    => ['label' => 'Scan Buku Rekening Bank', 'file' => $proposal['file_buku_rekening'] ?? null],
                        'domisili'         => ['label' => 'Scan Surat Domisili / Izin', 'file' => $proposal['file_domisili'] ?? null],
                        'susunan_pengurus' => ['label' => 'Scan SK Pengurus', 'file' => $proposal['file_susunan_pengurus'] ?? null],
                    ];
                ?>

                <?php foreach ($selectedAttachments as $attKey): ?>
                    <?php if (isset($attachLabels[$attKey])): ?>
                        <?php $item = $attachLabels[$attKey]; ?>
                        <div class="col-6 col-md-4 text-center mb-3">
                            <div class="p-3 border rounded-3 bg-light h-100">
                                <div class="fw-bold small mb-2"><?= $item['label'] ?></div>
                                <?php if (!empty($item['file'])): ?>
                                    <?php $ext = pathinfo($item['file'], PATHINFO_EXTENSION); ?>
                                    <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])): ?>
                                        <img src="<?= base_url('uploads/lembaga/' . $item['file']) ?>" class="img-fluid rounded border mb-2" style="max-height: 150px;">
                                    <?php else: ?>
                                        <div class="py-4"><i class="fa-solid fa-file-pdf text-danger fs-1"></i></div>
                                    <?php endif; ?>
                                    <div class="small"><a href="<?= base_url('uploads/lembaga/' . $item['file']) ?>" target="_blank" class="text-decoration-none"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Buka Berkas</a></div>
                                <?php else: ?>
                                    <div class="text-muted small py-4 fst-italic">Berkas belum diunggah</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Foto Lapangan -->
            <?php if (!empty($photos)): ?>
                <div class="mt-4">
                    <h6 class="fw-bold small text-uppercase mb-3">Dokumentasi Kondisi Lapangan / Rencana Fasilitas:</h6>
                    <div class="row g-2">
                        <?php foreach ($photos as $p): ?>
                            <div class="col-6 col-md-4 text-center">
                                <img src="<?= base_url('uploads/proposals/' . $p) ?>" class="img-fluid rounded border shadow-sm" style="max-height: 180px; object-fit: cover;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
