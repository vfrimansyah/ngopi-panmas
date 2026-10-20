<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background-color: #f8f9fa;
            font-size: 12pt;
            line-height: 1.5;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }
        .page-break {
            page-break-after: always;
            break-after: page;
        }
        .kop-table {
            width: 100%;
            border-bottom: 3px double #000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }
        .text-justify {
            text-align: justify;
        }
        .indent-p {
            text-indent: 1.5cm;
            margin-bottom: 0.8rem;
        }
        table.table-rab {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
            margin-top: 10px;
            margin-bottom: 15px;
        }
        table.table-rab th, table.table-rab td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        table.table-rab th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .signature-box {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 15mm 20mm;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

<!-- Floating Print Button (Hidden when printing) -->
<div class="no-print position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <button onclick="window.print()" class="btn btn-primary btn-lg shadow fw-bold px-4">
        <i class="fa-solid fa-print me-2"></i>Cetak / Simpan ke PDF
    </button>
    <button onclick="window.close()" class="btn btn-secondary btn-lg shadow ms-2">
        Tutup
    </button>
</div>

<!-- ════════════════════════════════════════════════════════════════
     HALAMAN 1: SURAT PENGANTAR PERMOHONAN RESMI
     ════════════════════════════════════════════════════════════════ -->
<div class="page page-break">
    <!-- KOP SURAT -->
    <table class="kop-table">
        <tr>
            <?php if (!empty($proposal['file_logo'])): ?>
                <td style="width: 100px; vertical-align: middle; text-align: center;">
                    <img src="<?= base_url('uploads/lembaga/' . $proposal['file_logo']) ?>" style="max-width: 85px; max-height: 85px; object-fit: contain;">
                </td>
            <?php endif; ?>
            <td style="vertical-align: middle; text-align: center;">
                <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                    <?= esc($proposal['nama_lembaga'] ?: 'YAYASAN / LEMBAGA PEMOHON') ?>
                </div>
                <?php if (!empty($proposal['no_sk_kemenkumham'])): ?>
                    <div style="font-size: 10.5pt; font-weight: bold;">
                        SK KEMENKUMHAM: <?= esc($proposal['no_sk_kemenkumham']) ?>
                    </div>
                <?php endif; ?>
                <div style="font-size: 10pt;">
                    <?= esc($proposal['alamat_lembaga'] ?: ($proposal['kelurahan_lembaga'] . ', ' . $proposal['kecamatan_lembaga'] . ', ' . $proposal['kota_lembaga'])) ?>
                </div>
                <div style="font-size: 9.5pt;">
                    Telepon/WhatsApp: <?= esc($proposal['telp_lembaga'] ?: $proposal['hp_ketua'] ?: '-') ?> | Email: <?= esc($proposal['email_lembaga'] ?: '-') ?>
                </div>
            </td>
        </tr>
    </table>

    <!-- TANGGAL & NOMOR SURAT -->
    <table style="width: 100%; margin-bottom: 25px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
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
            </td>
            <td style="width: 40%; text-align: right; vertical-align: top;">
                <?= esc($proposal['kota_lembaga'] ?: 'Depok') ?>, <?= $proposal['tanggal_surat'] ? date('d F Y', strtotime($proposal['tanggal_surat'])) : date('d F Y') ?>
            </td>
        </tr>
    </table>

    <!-- TUJUAN SURAT -->
    <div style="margin-bottom: 25px;">
        Kepada Yth.<br>
        <strong><?= esc($proposal['tujuan_nama'] ?: 'Bapak/Ibu Pimpinan') ?></strong><br>
        <?= esc($proposal['tujuan_instansi'] ?: '') ?><br>
        di -<br>
        &nbsp;&nbsp;&nbsp;&nbsp;<?= esc($proposal['tujuan_alamat'] ?: 'Tempat') ?>
    </div>

    <!-- ISI SURAT -->
    <div class="text-justify">
        <p class="indent-p">
            Dengan hormat,<br>
            Puji syukur kehadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya. Semoga Bapak/Ibu senantiasa diberikan kelancaran dan kemudahan dalam mengemban amanah masyarakat.
        </p>

        <p class="indent-p">
            Dalam upaya meningkatkan kualitas pelayanan, sarana prasarana, serta keberdayaan warga masyarakat di lingkungan wilayah kami, bersama surat ini kami dari pengurus <strong><?= esc($proposal['nama_lembaga']) ?></strong> bermaksud mengajukan usulan permohonan bantuan dana / hibah untuk program:
        </p>

        <div style="text-align: center; font-weight: bold; font-size: 13pt; margin: 15px 0; padding: 10px; border: 1px solid #ccc; background-color: #fbfbfb;">
            "<?= esc($proposal['judul_proposal']) ?>"
        </div>

        <p class="indent-p">
            Total rencana anggaran biaya yang dibutuhkan untuk pelaksanaan program tersebut sebesar <strong>Rp <?= number_format((float)$proposal['total_anggaran'], 0, ',', '.') ?>,- (<?= esc($proposal['terbilang_anggaran']) ?>)</strong> sebagaimana rincian anggaran yang tertera dalam proposal terlampir.
        </p>

        <p class="indent-p">
            Besar harapan kami kiranya Bapak/Ibu dapat berkenan menyetujui serta merealisasikan usulan permohonan ini. Sebagai bahan telaah dan pertimbangan Bapak/Ibu, kami sertakan berkas naskah proposal, rincian anggaran biaya (RAB), profil lembaga, serta dokumen legalitas resmi.
        </p>

        <p class="indent-p">
            Demikian surat permohonan ini kami sampaikan. Atas perhatian, dukungan, dan perkenan Bapak/Ibu, kami haturkan terima kasih yang sebesar-besarnya.
        </p>
    </div>

    <!-- TANDA TANGAN -->
    <div class="signature-box" style="margin-top: 40px;">
        <table style="width: 100%; text-align: center;">
            <tr>
                <td style="width: 50%;">
                    Sekretaris,
                    <div style="height: 70px;"></div>
                    <strong style="text-decoration: underline;"><?= esc($proposal['nama_sekretaris'] ?: '.......................................') ?></strong>
                </td>
                <td style="width: 50%;">
                    Ketua / Pimpinan Lembaga,
                    <div style="height: 70px;"></div>
                    <strong style="text-decoration: underline;"><?= esc($proposal['nama_ketua'] ?: '.......................................') ?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     HALAMAN 2: NASKAH PROPOSAL & RINCIAN KEGIATAN
     ════════════════════════════════════════════════════════════════ -->
<div class="page page-break">
    <div style="text-align: center; margin-bottom: 30px;">
        <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">PROPOSAL USULAN BANTUAN</div>
        <div style="font-size: 15pt; font-weight: bold; text-transform: uppercase; margin-top: 5px;"><?= esc($proposal['judul_proposal']) ?></div>
        <div style="font-size: 11pt; font-weight: bold; margin-top: 5px; color: #333;">
            OLEH: <?= strtoupper(esc($proposal['nama_lembaga'])) ?>
        </div>
        <hr style="border-top: 2px solid #000; margin-top: 15px;">
    </div>

    <!-- BAB I -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; font-size: 13pt; text-transform: uppercase; margin-bottom: 10px;">BAB I. PENDAHULUAN</div>
        
        <div style="font-weight: bold; margin-bottom: 4px;">1.1 Latar Belakang</div>
        <div class="text-justify indent-p" style="white-space: pre-line;">
            <?= esc($proposal['latar_belakang'] ?: 'Latar belakang kegiatan belum diisi.') ?>
        </div>

        <div style="font-weight: bold; margin-top: 15px; margin-bottom: 4px;">1.2 Maksud dan Tujuan</div>
        <div class="text-justify" style="white-space: pre-line;">
            <?= esc($proposal['maksud_tujuan'] ?: 'Maksud dan tujuan kegiatan belum diisi.') ?>
        </div>
    </div>

    <!-- BAB II -->
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; font-size: 13pt; text-transform: uppercase; margin-top: 25px; margin-bottom: 10px;">BAB II. RENCANA USULAN KEGIATAN</div>

        <div style="font-weight: bold; margin-bottom: 4px;">2.1 Deskripsi Rencana Kegiatan / Spesifikasi Bantuan</div>
        <div class="text-justify indent-p" style="white-space: pre-line;">
            <?= esc($proposal['deskripsi_kegiatan'] ?: 'Deskripsi usulan belum diisi.') ?>
        </div>

        <table style="width: 100%; margin-top: 10px;">
            <tr>
                <td style="width: 230px; font-weight: bold; vertical-align: top;">2.2 Sasaran Penerima Manfaat</td>
                <td style="width: 15px; vertical-align: top;">:</td>
                <td><?= esc($proposal['sasaran_penerima'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">2.3 Waktu Pelaksanaan / Realisasi</td>
                <td style="vertical-align: top;">:</td>
                <td><?= esc($proposal['waktu_pelaksanaan'] ?: '-') ?></td>
            </tr>
            <tr>
                <td style="font-weight: bold; vertical-align: top;">2.4 Lokasi Fasilitas / Kegiatan</td>
                <td style="vertical-align: top;">:</td>
                <td><?= esc($proposal['lokasi_kegiatan'] ?: '-') ?></td>
            </tr>
        </table>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     HALAMAN 3: RENCANA ANGGARAN BIAYA (RAB) & LEMBAR PENGESAHAN
     ════════════════════════════════════════════════════════════════ -->
<div class="page page-break">
    <div style="margin-bottom: 20px;">
        <div style="font-weight: bold; font-size: 13pt; text-transform: uppercase; margin-bottom: 10px;">BAB III. RENCANA ANGGARAN BIAYA (RAB)</div>
        <p class="small" style="margin-bottom: 10px;">Rincian kebutuhan anggaran yang diusulkan adalah sebagai berikut:</p>

        <table class="table-rab">
            <thead>
                <tr>
                    <th style="width: 35px;">No</th>
                    <th>Uraian Kebutuhan / Spesifikasi</th>
                    <th style="width: 60px;">Vol</th>
                    <th style="width: 70px;">Satuan</th>
                    <th style="width: 120px;">Harga Satuan (Rp)</th>
                    <th style="width: 130px;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rabs)): ?>
                    <?php foreach ($rabs as $idx => $r): ?>
                        <tr>
                            <td style="text-align: center;"><?= $idx + 1 ?></td>
                            <td>
                                <strong><?= esc($r['uraian']) ?></strong>
                                <?php if (!empty($r['keterangan'])): ?>
                                    <div style="font-size: 9pt; color: #555;"><?= esc($r['keterangan']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;"><?= rtrim(rtrim($r['volume'], '0'), '.') ?></td>
                            <td style="text-align: center;"><?= esc($r['satuan']) ?></td>
                            <td style="text-align: right;"><?= number_format((float)$r['harga_satuan'], 0, ',', '.') ?></td>
                            <td style="text-align: right; font-weight: bold;"><?= number_format((float)$r['total_harga'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align: center;">- Belum ada rincian anggaran -</td></tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #f2f2f2; font-weight: bold;">
                    <td colspan="5" style="text-align: right;">TOTAL ANGGARAN:</td>
                    <td style="text-align: right;">Rp <?= number_format((float)$proposal['total_anggaran'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td colspan="6" style="font-style: italic; font-size: 10pt;">
                        <strong>Terbilang:</strong> <?= esc($proposal['terbilang_anggaran']) ?>
                    </td>
                </tr>
            </tfoot>
        </table>

        <?php if (!empty($proposal['nama_bank']) && !empty($proposal['no_rekening'])): ?>
            <div style="border: 1px solid #000; padding: 10px; margin-top: 15px; font-size: 10.5pt;">
                <strong>Rekening Penyaluran Bantuan:</strong><br>
                Nama Bank: <strong><?= esc($proposal['nama_bank']) ?></strong><br>
                Nomor Rekening: <strong><?= esc($proposal['no_rekening']) ?></strong><br>
                Atas Nama: <strong><?= esc($proposal['atas_nama_rekening']) ?></strong>
            </div>
        <?php endif; ?>
    </div>

    <!-- BAB IV: SUSUNAN PANITIA -->
    <?php if (!empty($proposal['susunan_panitia'])): ?>
        <div style="margin-top: 25px;">
            <div style="font-weight: bold; font-size: 13pt; text-transform: uppercase; margin-bottom: 8px;">BAB IV. SUSUNAN TIM PELAKSANA</div>
            <div style="white-space: pre-line; padding-left: 15px;">
                <?= esc($proposal['susunan_panitia']) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- BAB V: PENUTUP -->
    <div style="margin-top: 25px;">
        <div style="font-weight: bold; font-size: 13pt; text-transform: uppercase; margin-bottom: 8px;">BAB V. PENUTUP</div>
        <div class="text-justify indent-p" style="white-space: pre-line;">
            <?= esc($proposal['penutup'] ?: 'Demikian proposal ini kami sampaikan, atas perhatian dan kerja samanya kami haturkan terima kasih.') ?>
        </div>
    </div>

    <!-- LEMBAR PENGESAHAN -->
    <div class="signature-box" style="margin-top: 40px;">
        <div style="text-align: center; margin-bottom: 25px;">
            <?= esc($proposal['kota_lembaga'] ?: 'Depok') ?>, <?= $proposal['tanggal_surat'] ? date('d F Y', strtotime($proposal['tanggal_surat'])) : date('d F Y') ?><br>
            <strong>PENGURUS / TIM PELAKSANA <?= strtoupper(esc($proposal['nama_lembaga'])) ?></strong>
        </div>
        <table style="width: 100%; text-align: center;">
            <tr>
                <td style="width: 50%;">
                    Sekretaris,
                    <div style="height: 70px;"></div>
                    <strong style="text-decoration: underline;"><?= esc($proposal['nama_sekretaris'] ?: '.......................................') ?></strong>
                </td>
                <td style="width: 50%;">
                    Ketua / Pimpinan,
                    <div style="height: 70px;"></div>
                    <strong style="text-decoration: underline;"><?= esc($proposal['nama_ketua'] ?: '.......................................') ?></strong>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     HALAMAN LAMPIRAN DOKUMEN LEGALITAS
     ════════════════════════════════════════════════════════════════ -->
<div class="page">
    <div style="text-align: center; font-weight: bold; font-size: 14pt; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 25px;">
        LAMPIRAN DOKUMEN LEGALITAS & BUKTI FISIK
    </div>

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

    <div class="row g-4">
        <?php foreach ($selectedAttachments as $attKey): ?>
            <?php if (isset($attachLabels[$attKey])): ?>
                <?php $item = $attachLabels[$attKey]; ?>
                <div class="col-6 text-center mb-4" style="page-break-inside: avoid;">
                    <div style="border: 1px solid #333; padding: 10px; min-height: 200px;">
                        <div style="font-weight: bold; font-size: 11pt; margin-bottom: 8px; border-bottom: 1px dashed #ccc; padding-bottom: 4px;">
                            <?= $item['label'] ?>
                        </div>
                        <?php if (!empty($item['file'])): ?>
                            <?php $ext = pathinfo($item['file'], PATHINFO_EXTENSION); ?>
                            <?php if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp'])): ?>
                                <img src="<?= base_url('uploads/lembaga/' . $item['file']) ?>" style="max-width: 100%; max-height: 240px; object-fit: contain;">
                            <?php else: ?>
                                <div style="padding: 40px 10px; color: #555;">
                                    <i class="fa-solid fa-file-pdf" style="font-size: 36pt; color: #c00;"></i><br>
                                    <span style="font-size: 10pt;"><?= esc($item['file']) ?></span>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div style="padding: 50px 10px; font-style: italic; color: #888;">
                                Dokumen belum diunggah
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Foto Dokumentasi Fisik -->
    <?php if (!empty($photos)): ?>
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <div style="font-weight: bold; font-size: 11pt; text-transform: uppercase; margin-bottom: 10px;">
                Dokumentasi Kondisi Fisik Lapangan:
            </div>
            <div class="row g-3">
                <?php foreach ($photos as $p): ?>
                    <div class="col-6 text-center">
                        <div style="border: 1px solid #ccc; padding: 5px;">
                            <img src="<?= base_url('uploads/proposals/' . $p) ?>" style="max-width: 100%; max-height: 220px; object-fit: cover;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
