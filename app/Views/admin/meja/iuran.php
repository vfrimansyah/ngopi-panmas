<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <!-- Breadcrumb & Header -->
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>" class="text-decoration-none text-muted">Admin</a></li>
                    <li class="breadcrumb-item text-muted">Meja Saya</li>
                    <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Iuran Anggota</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark-coffee mb-0">
                <i class="fa-solid fa-hand-holding-dollar text-gold me-2"></i>Status Iuran Pribadi
            </h3>
        </div>
        <?php if (!empty($isLinked)): ?>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <!-- Year selector form -->
            <form action="<?= base_url('admin/meja/iuran') ?>" method="GET" class="d-flex align-items-center gap-2">
                <label class="small text-muted fw-semibold mb-0">Tahun:</label>
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php
                        $selectedTahun = (int)($tahun ?? date('Y'));
                        $currentYear = (int)date('Y');
                        for ($y = $currentYear + 1; $y >= $currentYear - 3; $y--):
                    ?>
                        <option value="<?= $y ?>" <?= $selectedTahun === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <?php if (!$isLinked): ?>
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <div class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning mb-3 mx-auto">
                <i class="fa-solid fa-user-xmark fa-3x"></i>
            </div>
            <h4 class="fw-bold text-dark-coffee mb-2">Profil Anggota Belum Tertaut</h4>
            <p class="text-muted mx-auto mb-4" style="max-width: 480px;">
                Untuk dapat melihat status pembayaran dan riwayat iuran pribadi Anda, silakan tautkan akun Anda ke data anggota terlebih dahulu.
            </p>
            <div>
                <a href="<?= base_url('admin/meja/profile') ?>" class="btn btn-gold fw-semibold">
                    <i class="fa-solid fa-link me-1"></i>Buka Halaman Profil & Tautkan Akun
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- Member Header Banner -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e140d 0%, #3e2718 100%);">
            <div class="card-body p-4 text-white d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-gold bg-opacity-20 p-3 text-gold border border-gold border-opacity-25 shadow-sm">
                        <i class="fa-solid fa-receipt fa-2x"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-white">Status Iuran Tahun <?= esc($tahun) ?></h5>
                        <small class="text-gold opacity-75">
                            Anggota: <?= esc($member['nama_lengkap']) ?> &bull; UPA: <?= esc($member['kode_upa'] ?? '-') ?> &bull; Jenjang: <?= esc($member['status_anggota'] ?? 'Anggota') ?>
                        </small>
                    </div>
                </div>

                <div class="text-md-end">
                    <span class="badge bg-gold text-dark px-3 py-2 rounded-pill fw-semibold">
                        Kepatuhan: <?= $summary['percentage_completed'] ?>% Lunas
                    </span>
                </div>
            </div>
        </div>

        <!-- 4 KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-coins fa-xl"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Disetor (<?= $tahun ?>)</small>
                            <h5 class="fw-bold text-dark-coffee mb-0">Rp <?= number_format($summary['total_setor'], 0, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i class="fa-solid fa-hand-holding-heart fa-xl"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total ITB (Ta'awun)</small>
                            <h5 class="fw-bold text-dark-coffee mb-0">Rp <?= number_format($summary['total_itb'], 0, ',', '.') ?></h5>
                            <small class="text-success small"><i class="fa-solid fa-check me-1"></i><?= $summary['lunas_itb_months'] ?> / 12 Bulan Lunas</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-file-invoice-dollar fa-xl"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total IWB (Wajib)</small>
                            <h5 class="fw-bold text-dark-coffee mb-0">Rp <?= number_format($summary['total_iwb'], 0, ',', '.') ?></h5>
                            <small class="text-info small"><i class="fa-solid fa-check me-1"></i><?= $summary['lunas_iwb_months'] ?> / 12 Bulan Lunas</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i class="fa-solid fa-gift fa-xl"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block">Total Infaq Tambahan</small>
                            <h5 class="fw-bold text-dark-coffee mb-0">Rp <?= number_format($summary['total_infaq'], 0, ',', '.') ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 12-Month Table -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark-coffee mb-0">
                    <i class="fa-solid fa-calendar-days text-gold me-2"></i>Rincian Status Iuran Per Bulan (Tahun <?= esc($tahun) ?>)
                </h6>
                <span class="badge bg-light text-dark border">
                    12 Bulan
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4" style="width: 50px;">No</th>
                            <th style="width: 140px;">Bulan</th>
                            <th>Status ITB (Ta'awun)</th>
                            <th>Status IWB (Wajib)</th>
                            <th>Infaq</th>
                            <th>Total Setor</th>
                            <th>Tgl Bayar</th>
                            <th class="pe-4">Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $r): 
                            $monthTotal = $r['nominal_itb'] + $r['nominal_iwb'] + $r['nominal_infaq'];
                        ?>
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= $r['bulan'] ?></td>
                                <td class="fw-semibold text-dark-coffee">
                                    <i class="fa-regular fa-calendar me-1 text-gold"></i><?= esc($r['nama_bulan']) ?>
                                </td>
                                <td>
                                    <?php if ($r['status_itb'] === 'lunas'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-check me-1"></i>Lunas (Rp <?= number_format($r['nominal_itb'], 0, ',', '.') ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <i class="fa-solid fa-clock me-1"></i>Belum
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($r['status_iwb'] === 'lunas'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                            <i class="fa-solid fa-check me-1"></i>Lunas (Rp <?= number_format($r['nominal_iwb'], 0, ',', '.') ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary-subtle text-secondary border px-2 py-1">
                                            <i class="fa-solid fa-clock me-1"></i>Belum
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($r['nominal_infaq'] > 0): ?>
                                        <span class="fw-semibold text-primary">Rp <?= number_format($r['nominal_infaq'], 0, ',', '.') ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-bold text-dark-coffee">
                                    Rp <?= number_format($monthTotal, 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?= !empty($r['tanggal_bayar']) ? date('d/m/Y', strtotime($r['tanggal_bayar'])) : '<span class="text-muted">-</span>' ?>
                                </td>
                                <td class="pe-4 text-muted">
                                    <?= !empty($r['catatan']) ? esc($r['catatan']) : '-' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Info & Confirmation Guidance -->
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold text-dark-coffee mb-3">
                        <i class="fa-solid fa-building-columns text-gold me-2"></i>Rekening Resmi Pembayaran Iuran
                    </h6>
                    <p class="text-muted small mb-3">
                        Iuran anggota (ITB, IWB, dan Infaq) dapat disetorkan melalui transfer ke salah satu rekening resmi bendahara berikut:
                    </p>

                    <div class="d-flex flex-column gap-2 mb-3">
                        <?php if (!empty($rekeningList)): ?>
                            <?php foreach ($rekeningList as $rek): ?>
                                <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle p-2 bg-white border text-gold">
                                            <i class="fa-solid fa-money-check-dollar fa-lg"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark-coffee"><?= esc($rek['bank']) ?> - <?= esc($rek['nomor_rekening']) ?></div>
                                            <small class="text-muted">a.n. <?= esc($rek['nama_rekening']) ?></small>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="navigator.clipboard.writeText('<?= esc($rek['nomor_rekening']) ?>'); alert('Nomor rekening berhasil disalin!');">
                                        <i class="fa-regular fa-copy me-1"></i>Salin
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 rounded-3 border bg-light d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle p-2 bg-white border text-gold">
                                        <i class="fa-solid fa-money-check-dollar fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark-coffee">Bank Syariah Indonesia (BSI)</div>
                                        <small class="text-muted">Silakan hubungi Bendahara DPC untuk nomor rekening terbaru</small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white h-100" style="background: linear-gradient(135deg, #2d1e13 0%, #4a3424 100%);">
                    <h6 class="fw-bold text-gold mb-3">
                        <i class="fa-brands fa-whatsapp me-2"></i>Konfirmasi Pembayaran Iuran
                    </h6>
                    <p class="small text-cream opacity-75 mb-3">
                        Setelah melakukan transfer iuran, Anda dapat mengonfirmasi ke Bendahara agar status pembayaran Anda pada sistem segera diperbarui menjadi <strong>Lunas</strong>.
                    </p>

                    <?php
                        $msgText = rawurlencode("Assalamu'alaikum Bendahara, konfirmasi setoran iuran atas nama: " . ($member['nama_lengkap'] ?? '') . " (UPA: " . ($member['kode_upa'] ?? '') . ") untuk tahun " . $tahun . ".");
                    ?>
                    <a href="https://wa.me/?text=<?= $msgText ?>" target="_blank" class="btn btn-gold fw-semibold w-100 mt-auto shadow-sm">
                        <i class="fa-brands fa-whatsapp me-2"></i>Konfirmasi ke Bendahara
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<?= $this->endSection() ?>
