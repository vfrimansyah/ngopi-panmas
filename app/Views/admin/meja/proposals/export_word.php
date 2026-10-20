<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title><?= esc($proposal['judul_proposal']) ?></title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page {
            size: 21.0cm 29.7cm;
            margin: 2.5cm 2.5cm 2.5cm 2.5cm;
            mso-page-orientation: portrait;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }
        p {
            margin-bottom: 8pt;
            text-align: justify;
        }
        .indent {
            text-indent: 1.5cm;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table.table-rab td, table.table-rab th {
            border: 1px solid #000;
            padding: 5pt 7pt;
            font-size: 11pt;
        }
        table.table-rab th {
            background-color: #eee;
            text-align: center;
        }
        .page-break {
            page-break-before: always;
            mso-break-type: page-break;
        }
        .kop {
            text-align: center;
            border-bottom: 2pt solid #000;
            padding-bottom: 10pt;
            margin-bottom: 20pt;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

<!-- KOP SURAT -->
<div class="kop">
    <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase;">
        <?= esc($proposal['nama_lembaga']) ?>
    </div>
    <?php if (!empty($proposal['no_sk_kemenkumham'])): ?>
        <div style="font-size: 10pt; font-weight: bold;">
            SK KEMENKUMHAM: <?= esc($proposal['no_sk_kemenkumham']) ?>
        </div>
    <?php endif; ?>
    <div style="font-size: 10pt;">
        <?= esc($proposal['alamat_lembaga'] ?: ($proposal['kelurahan_lembaga'] . ', ' . $proposal['kecamatan_lembaga'] . ', ' . $proposal['kota_lembaga'])) ?>
    </div>
    <div style="font-size: 9.5pt;">
        Telp/WA: <?= esc($proposal['telp_lembaga'] ?: $proposal['hp_ketua'] ?: '-') ?> | Email: <?= esc($proposal['email_lembaga'] ?: '-') ?>
    </div>
</div>

<!-- SURAT PENGANTAR -->
<table style="width: 100%; margin-bottom: 15pt;">
    <tr>
        <td style="width: 60%; vertical-align: top;">
            <table>
                <tr>
                    <td style="width: 80pt;">Nomor</td>
                    <td style="width: 10pt;">:</td>
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

<div style="margin-bottom: 20pt;">
    Kepada Yth.<br>
    <strong><?= esc($proposal['tujuan_nama'] ?: 'Bapak/Ibu Pimpinan') ?></strong><br>
    <?= esc($proposal['tujuan_instansi'] ?: '') ?><br>
    di -<br>
    &nbsp;&nbsp;&nbsp;&nbsp;<?= esc($proposal['tujuan_alamat'] ?: 'Tempat') ?>
</div>

<p class="indent">
    Dengan hormat,<br>
    Puji syukur kita panjatkan ke hadirat Tuhan Yang Maha Esa. Semoga Bapak/Ibu senantiasa dalam keadaan sehat walafiat serta sukses dalam menjalankan tugas sehari-hari.
</p>

<p class="indent">
    Sehubungan dengan upaya peningkatan sarana, prasarana, serta kegiatan kemasyarakatan di wilayah kami, melalui surat ini kami dari <strong><?= esc($proposal['nama_lembaga']) ?></strong> bermaksud mengajukan usulan permohonan bantuan dana / hibah untuk program:
</p>

<div style="text-align: center; font-weight: bold; font-size: 13pt; margin: 15pt 0; padding: 10pt; border: 1pt solid #aaa;">
    "<?= esc($proposal['judul_proposal']) ?>"
</div>

<p class="indent">
    Total rencana anggaran biaya yang dibutuhkan sebesar <strong>Rp <?= number_format((float)$proposal['total_anggaran'], 0, ',', '.') ?>,- (<?= esc($proposal['terbilang_anggaran']) ?>)</strong> sebagaimana rincian anggaran yang tertera pada proposal terlampir.
</p>

<p class="indent">
    Besar harapan kami kiranya Bapak/Ibu dapat berkenan menyetujui dan merealisasikan usulan permohonan ini. Sebagai bahan pertimbangan, bersama surat ini kami lampirkan naskah proposal, rincian anggaran biaya (RAB), dan dokumen legalitas pendukung.
</p>

<p class="indent">
    Demikian surat permohonan ini kami sampaikan. Atas perhatian, dukungan, dan perkenan Bapak/Ibu, kami haturkan terima kasih.
</p>

<table style="width: 100%; text-align: center; margin-top: 30pt;">
    <tr>
        <td style="width: 50%;">
            Sekretaris,
            <br><br><br><br>
            <strong><u><?= esc($proposal['nama_sekretaris'] ?: '.......................................') ?></u></strong>
        </td>
        <td style="width: 50%;">
            Ketua / Pimpinan Lembaga,
            <br><br><br><br>
            <strong><u><?= esc($proposal['nama_ketua'] ?: '.......................................') ?></u></strong>
        </td>
    </tr>
</table>

<div class="page-break"></div>

<!-- NASKAH PROPOSAL -->
<div class="title">PROPOSAL USULAN BANTUAN</div>
<div class="title" style="font-size: 15pt; margin-top: 5pt;"><?= esc($proposal['judul_proposal']) ?></div>
<div style="text-align: center; font-weight: bold; margin-bottom: 25pt;">
    DIAJUKAN OLEH: <?= strtoupper(esc($proposal['nama_lembaga'])) ?>
</div>

<div style="font-weight: bold; font-size: 13pt;">BAB I. PENDAHULUAN</div>
<div style="font-weight: bold; margin-top: 6pt;">1.1 Latar Belakang</div>
<p class="indent" style="white-space: pre-line;">
    <?= esc($proposal['latar_belakang']) ?>
</p>

<div style="font-weight: bold; margin-top: 10pt;">1.2 Maksud dan Tujuan</div>
<p style="white-space: pre-line;">
    <?= esc($proposal['maksud_tujuan']) ?>
</p>

<div style="font-weight: bold; font-size: 13pt; margin-top: 20pt;">BAB II. RENCANA USULAN KEGIATAN</div>
<div style="font-weight: bold; margin-top: 6pt;">2.1 Deskripsi Rencana Kegiatan / Pengadaan</div>
<p class="indent" style="white-space: pre-line;">
    <?= esc($proposal['deskripsi_kegiatan']) ?>
</p>

<table style="width: 100%; margin-top: 10pt;">
    <tr>
        <td style="width: 220pt; font-weight: bold;">2.2 Sasaran Penerima Manfaat</td>
        <td style="width: 10pt;">:</td>
        <td><?= esc($proposal['sasaran_penerima'] ?: '-') ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">2.3 Waktu Realisasi Rencana</td>
        <td>:</td>
        <td><?= esc($proposal['waktu_pelaksanaan'] ?: '-') ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">2.4 Lokasi Fasilitas / Kegiatan</td>
        <td>:</td>
        <td><?= esc($proposal['lokasi_kegiatan'] ?: '-') ?></td>
    </tr>
</table>

<div class="page-break"></div>

<!-- BAB III: RAB -->
<div style="font-weight: bold; font-size: 13pt;">BAB III. RENCANA ANGGARAN BIAYA (RAB)</div>
<p>Rincian kebutuhan anggaran yang diusulkan adalah sebagai berikut:</p>

<table class="table-rab">
    <thead>
        <tr>
            <th style="width: 30pt;">No</th>
            <th>Uraian Kebutuhan / Spesifikasi</th>
            <th style="width: 50pt;">Vol</th>
            <th style="width: 60pt;">Satuan</th>
            <th style="width: 90pt;">Harga Satuan (Rp)</th>
            <th style="width: 100pt;">Jumlah (Rp)</th>
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
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #eee; font-weight: bold;">
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
    <div style="border: 1pt solid #000; padding: 10pt; margin-top: 15pt; font-size: 10.5pt;">
        <strong>Rekening Penyaluran Bantuan:</strong><br>
        Nama Bank: <strong><?= esc($proposal['nama_bank']) ?></strong><br>
        Nomor Rekening: <strong><?= esc($proposal['no_rekening']) ?></strong><br>
        Atas Nama: <strong><?= esc($proposal['atas_nama_rekening']) ?></strong>
    </div>
<?php endif; ?>

<?php if (!empty($proposal['susunan_panitia'])): ?>
    <div style="font-weight: bold; font-size: 13pt; margin-top: 20pt;">BAB IV. SUSUNAN TIM PELAKSANA</div>
    <div style="white-space: pre-line; padding-left: 15pt;">
        <?= esc($proposal['susunan_panitia']) ?>
    </div>
<?php endif; ?>

<div style="font-weight: bold; font-size: 13pt; margin-top: 20pt;">BAB V. PENUTUP</div>
<p class="indent" style="white-space: pre-line;">
    <?= esc($proposal['penutup']) ?>
</p>

<table style="width: 100%; text-align: center; margin-top: 40pt;">
    <tr>
        <td colspan="2" style="padding-bottom: 20pt;">
            <?= esc($proposal['kota_lembaga'] ?: 'Depok') ?>, <?= $proposal['tanggal_surat'] ? date('d F Y', strtotime($proposal['tanggal_surat'])) : date('d F Y') ?><br>
            <strong>PENGURUS / TIM PELAKSANA <?= strtoupper(esc($proposal['nama_lembaga'])) ?></strong>
        </td>
    </tr>
    <tr>
        <td style="width: 50%;">
            Sekretaris,
            <br><br><br><br>
            <strong><u><?= esc($proposal['nama_sekretaris'] ?: '.......................................') ?></u></strong>
        </td>
        <td style="width: 50%;">
            Ketua / Pimpinan,
            <br><br><br><br>
            <strong><u><?= esc($proposal['nama_ketua'] ?: '.......................................') ?></u></strong>
        </td>
    </tr>
</table>

</body>
</html>
