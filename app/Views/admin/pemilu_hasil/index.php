<?= $this->extend('layouts/admin') ?>

<?= $this->section('css') ?>
<style>
    .cursor-pointer {
        cursor: pointer;
    }
    .text-dark-coffee {
        color: #3e2723 !important;
    }
    .text-light-coffee {
        color: #d7ccc8 !important;
    }
    .bg-dark-coffee {
        background-color: #3e2723 !important;
    }
    .bg-gold {
        background-color: #d4af37 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .fs-7 {
        font-size: 0.8rem;
    }
    .table-warning {
        background-color: #fff3e0 !important;
    }
    .party-strength-cell {
        cursor: pointer;
        user-select: none;
        transition: filter 0.15s ease, box-shadow 0.15s ease;
    }
    .party-strength-cell:hover {
        filter: brightness(0.90);
        box-shadow: inset 0 0 0 2px #0f172a;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-square-poll-vertical me-2 text-gold"></i>Dashboard Pemilu <?= esc($selectedTingkat == 'DPRD_PROV' ? 'DPRD Provinsi Jawa Barat' : ($selectedTingkat == 'DPR_RI' ? 'DPR RI' : 'DPRD Kota Depok')) ?>
        </h4>
        <p class="text-secondary small mb-0">Portal Rekapitulasi Suara & Pemenangan PKS - <?= esc($selectedTingkat == 'DPRD_PROV' ? 'Dapil Jabar 8 (Depok - Bekasi)' : ($selectedTingkat == 'DPR_RI' ? 'Dapil Jabar 6 (Depok - Bekasi)' : 'Kota Depok (Dapil 1 Pancoran Mas)')) ?></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('admin/pemilu/heatmap') ?>?tingkat_pemilu=<?= esc($selectedTingkat) ?>" class="btn fw-bold shadow-sm" style="background-color: #3e2723; color: #fff8e1; border-radius: 8px;">
            <i class="fa-solid fa-table-cells me-2 text-warning"></i>Buka Heatmap TPS
        </a>
    </div>
</div>

<!-- FILTER BAR -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #efebe9;">
    <div class="card-body p-4">
        <form action="<?= base_url('admin/pemilu/hasil') ?>" method="GET" class="row g-3 align-items-end" id="filterForm">
            <div class="col-md-3">
                <label class="form-label fw-bold text-dark-coffee small"><i class="fa-solid fa-layer-group me-1"></i>Tingkat Pemilu:</label>
                <select name="tingkat_pemilu" class="form-select border-0 shadow-sm text-dark-coffee fw-bold" style="border-radius: 8px; height: 42px;">
                    <?php foreach ($tingkatOptions as $k => $label): ?>
                        <option value="<?= $k ?>" <?= $selectedTingkat == $k ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold text-dark-coffee small"><i class="fa-solid fa-calendar me-1"></i>Tahun:</label>
                <select name="tahun" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                    <?php foreach ($years as $yr): ?>
                        <option value="<?= $yr ?>" <?= $selectedYear == $yr ? 'selected' : '' ?>>
                            Tahun <?= esc($yr) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label fw-bold text-dark-coffee small"><i class="fa-solid fa-map me-1"></i>Kecamatan:</label>
                <select name="district_id" id="districtSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                    <?php foreach ($districts as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $selectedDistrict == $d['id'] ? 'selected' : '' ?>>
                            Kec. <?= esc($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold text-dark-coffee small"><i class="fa-solid fa-building-flag me-1"></i>Kelurahan:</label>
                <select name="village_id" id="villageSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                    <option value="">-- Semua Kelurahan --</option>
                    <?php foreach ($villages as $v): ?>
                        <option value="<?= $v['id'] ?>" <?= $selectedVillage == $v['id'] ? 'selected' : '' ?>>
                            Kel. <?= esc($v['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn w-100 fw-bold border-0 shadow-sm" style="background-color: #3e2723; color: #fff8e1; border-radius: 8px; height: 42px;">
                    <i class="fa-solid fa-filter me-2"></i>Filter
                </button>
                <a href="<?= base_url('admin/pemilu/hasil') ?>" class="btn w-100 fw-bold shadow-sm" style="background-color: #ffffff; color: #3e2723; border: 2px solid #3e2723; border-radius: 8px; height: 42px; line-height: 26px; text-decoration: none; text-align: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- PKS HERO HIGHLIGHT & CORE STATS -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-lg text-white h-100 overflow-hidden position-relative" style="background: linear-gradient(135deg, #f26522, #b73a00); border-radius: 16px;">
            <div class="position-absolute end-0 top-50 translate-middle-y me-4 text-white" style="font-size: 3.75rem; opacity: 0.25; pointer-events: none; line-height: 1; z-index: 1;">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-between position-relative">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white p-2 rounded" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <img src="<?= base_url('uploads/partai/' . ($pksStats['lambang_partai'] ?? '')) ?>" alt="PKS" style="max-height: 100%; max-width: 100%; object-fit: contain;" onerror="this.src='https://placehold.co/50?text=PKS';">
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-white">Partai Keadilan Sejahtera</h5>
                            <span class="badge bg-white text-dark fw-bold small">Tahun <?= esc($selectedYear) ?></span>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="text-uppercase small fw-bold opacity-75 d-block">Peringkat</span>
                        <h3 class="fw-bold mb-0 text-warning"><i class="fa-solid fa-crown text-warning me-1"></i>#<?= $pksRank ?></h3>
                    </div>
                </div>

                <div class="row g-3 py-3 border-top border-bottom border-white border-opacity-25 my-2">
                    <div class="col-6 border-end border-white border-opacity-25">
                        <span class="small opacity-75 d-block">Total Perolehan Suara</span>
                        <h2 class="fw-bold mb-0 text-warning"><?= number_format($pksStats['total_suara'] ?? 0) ?></h2>
                        <span class="small opacity-75">Suara Sah Partai & Caleg</span>
                    </div>
                    <div class="col-6 ps-4">
                        <span class="small opacity-75 d-block">Persentase Suara Sah</span>
                        <h2 class="fw-bold mb-0 text-warning">
                            <?= $totalValidVotes > 0 ? number_format((($pksStats['total_suara'] ?? 0) / $totalValidVotes) * 100, 2) : 0 ?>%
                        </h2>
                        <span class="small opacity-75">Dari total <?= number_format($totalValidVotes) ?> suara</span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2 small">
                    <span>Suara Partai: <strong><?= number_format($pksStats['suara_partai'] ?? 0) ?></strong></span>
                    <span>Suara Caleg: <strong><?= number_format($pksStats['suara_caleg'] ?? 0) ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- GENERAL SUMMARY CARDS -->
    <div class="col-lg-6">
        <div class="row g-3 h-100">
            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #3e2723 !important;">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="position-absolute end-0 bottom-0 opacity-10 text-secondary me-n2 mb-n2" style="font-size: 5rem;">
                            <i class="fa-solid fa-check-to-slot"></i>
                        </div>
                        <h6 class="text-uppercase small fw-bold text-muted">TPS Terproses</h6>
                        <h3 class="fw-bold mb-0 text-dark-coffee"><?= number_format($summary['tps_count'] ?? 0) ?></h3>
                        <p class="small text-muted mb-0 mt-2">Wilayah Pemilihan Terproses</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #1976d2 !important;">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="position-absolute end-0 bottom-0 opacity-10 text-primary me-n2 mb-n2" style="font-size: 5rem;">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <h6 class="text-uppercase small fw-bold text-muted">Total Suara Masuk</h6>
                        <h3 class="fw-bold mb-0 text-primary"><?= number_format($summary['suara_total'] ?? 0) ?></h3>
                        <p class="small text-muted mb-0 mt-2">Partisipasi kehadiran pemilih</p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #2e7d32 !important;">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="position-absolute end-0 bottom-0 opacity-10 text-success me-n2 mb-n2" style="font-size: 5rem;">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <h6 class="text-uppercase small fw-bold text-muted">Suara Sah</h6>
                        <h3 class="fw-bold mb-0 text-success"><?= number_format($summary['suara_sah'] ?? 0) ?></h3>
                        <p class="small text-muted mb-0 mt-2">
                            <?= ($summary['suara_total'] ?? 0) > 0 ? number_format(($summary['suara_sah'] / $summary['suara_total']) * 100, 2) : 0 ?>% dari total suara
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border-left: 5px solid #c62828 !important;">
                    <div class="card-body p-4 position-relative overflow-hidden">
                        <div class="position-absolute end-0 bottom-0 opacity-10 text-danger me-n2 mb-n2" style="font-size: 5rem;">
                            <i class="fa-solid fa-circle-xmark"></i>
                        </div>
                        <h6 class="text-uppercase small fw-bold text-muted">Suara Tidak Sah</h6>
                        <h3 class="fw-bold mb-0 text-danger"><?= number_format($summary['suara_tidak_sah'] ?? 0) ?></h3>
                        <p class="small text-muted mb-0 mt-2">
                            <?= ($summary['suara_total'] ?? 0) > 0 ? number_format(($summary['suara_tidak_sah'] / $summary['suara_total']) * 100, 2) : 0 ?>% dari total suara
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TOP 10 PARTIES & POSITION HIGHLIGHT -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header p-4 bg-transparent border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-trophy text-gold me-2"></i>10 Besar Perolehan Suara Total Partai
        </h5>
        <span class="badge bg-gold text-dark fs-7 px-3 py-1 fw-bold"><?= esc($selectedTingkat == 'DPRD_PROV' ? 'DPRD Provinsi Jabar (Dapil 8)' : ($selectedTingkat == 'DPR_RI' ? 'DPR RI (Dapil Jabar 6)' : 'DPRD Kota Depok (Dapil 1)')) ?></span>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="row g-4">
            <div class="col-lg-6">
                <h6 class="text-uppercase fw-bold text-muted small mb-3">Grafik Perolehan Suara (Top 10)</h6>
                <?php 
                $limit = 10;
                $count = 0;
                $maxVotes = count($partyResults) > 0 ? $partyResults[0]['total_suara'] : 1;
                foreach ($partyResults as $r): 
                    if ($count++ >= $limit) break;
                    $percent = $totalValidVotes > 0 ? ($r['total_suara'] / $totalValidVotes) * 100 : 0;
                    $barWidth = ($r['total_suara'] / $maxVotes) * 100;
                    $isPKS = ($r['id'] == $pksId);
                ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold small text-dark-coffee">
                                <?= $isPKS ? '<i class="fa-solid fa-star text-gold me-1"></i>' : '' ?>
                                <?= $r['no_urut'] ?>. <?= esc($r['nama_partai']) ?>
                            </span>
                            <span class="small fw-bold text-dark-coffee">
                                <?= number_format($r['total_suara']) ?> suara (<?= number_format($percent, 2) ?>%)
                            </span>
                        </div>
                        <div class="progress" style="height: 14px; border-radius: 6px;">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?= $barWidth ?>%; background: <?= $isPKS ? 'linear-gradient(90deg, #f26522, #ff9100)' : '#b0bec5' ?>; border-radius: 6px;" 
                                 aria-valuenow="<?= $r['total_suara'] ?>" aria-valuemin="0" aria-valuemax="<?= $maxVotes ?>">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-lg-6">
                <h6 class="text-uppercase fw-bold text-muted small mb-3">Detail Klasemen Partai</h6>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3" style="width: 50px;">#</th>
                                <th style="width: 80px;">No Urut</th>
                                <th>Partai Politik</th>
                                <th class="text-end" style="width: 120px;">Total Suara</th>
                                <th class="text-end pe-3" style="width: 100px;">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            $count = 0;
                            foreach ($partyResults as $r): 
                                if ($count++ >= $limit) break;
                                $percent = $totalValidVotes > 0 ? ($r['total_suara'] / $totalValidVotes) * 100 : 0;
                                $isPKS = ($r['id'] == $pksId);
                            ?>
                                <tr class="<?= $isPKS ? 'table-warning border border-2 border-warning' : '' ?>" style="font-weight: <?= $isPKS ? 'bold' : 'normal' ?>;">
                                    <td class="ps-3 text-muted"><?= $rank++ ?></td>
                                    <td class="text-center"><?= esc($r['no_urut']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($r['lambang_partai'])): ?>
                                                <img src="<?= base_url('uploads/partai/' . $r['lambang_partai']) ?>" alt="Logo" class="rounded" style="width: 24px; height: 24px; object-fit: contain;">
                                            <?php endif; ?>
                                            <span><?= esc($r['nama_partai']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end text-dark-coffee"><?= number_format($r['total_suara']) ?></td>
                                    <td class="text-end pe-3 text-secondary"><?= number_format($percent, 2) ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- PETA KEKUATAN PARTAI PER KELURAHAN (HEATMAP) -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header p-4 bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1 text-dark-coffee d-flex align-items-center gap-2">
                <i class="fa-solid fa-map-location-dot text-danger"></i>
                <span>Peta Kekuatan Partai <?= ($selectedTingkat === 'DPRD_PROV' ? '- DPRD Provinsi Jawa Barat' : ($selectedTingkat === 'DPR_RI' ? '- DPR RI' : '- Dapil 1 Pancoran Mas')) ?></span>
            </h5>
            <p class="text-secondary small mb-0">
                Sebaran persentase kekuatan perolehan suara partai politik di 6 Kelurahan se-Kecamatan Pancoran Mas.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Filter Partai: 10 Partai Utama vs Semua -->
            <div class="btn-group btn-group-sm" role="group" id="partyMatrixFilterGroup">
                <button type="button" class="btn btn-secondary active" id="btnPartyTop10" onclick="setPartyFilterMode('top10')">
                    <i class="fa-solid fa-filter me-1"></i>10 Partai Utama
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnPartyAll" onclick="setPartyFilterMode('all')">
                    <i class="fa-solid fa-list me-1"></i>Semua Partai
                </button>
            </div>

            <!-- Switcher Mode Nilai: % vs Suara -->
            <div class="btn-group btn-group-sm" role="group" id="partyMatrixViewModeGroup">
                <button type="button" class="btn btn-primary active" id="btnPartyModePct" onclick="setPartyStrengthViewMode('percent')">
                    <i class="fa-solid fa-percent me-1"></i>% Persentase
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnPartyModeVotes" onclick="setPartyStrengthViewMode('votes')">
                    <i class="fa-solid fa-check-to-slot me-1"></i>Jumlah Suara
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body p-4 pt-0">
        <!-- Legenda Heatmap -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 bg-light rounded-3 small border">
            <span class="fw-bold text-muted me-1"><i class="fa-solid fa-palette me-1"></i>Gradasi Warna Heatmap:</span>
            <span class="px-2 py-1 rounded" style="background-color: #2e7d32; color: #fff; font-size: 0.75rem; font-weight: 700;">&ge; 25%</span>
            <span class="px-2 py-1 rounded" style="background-color: #66bb6a; color: #0d3813; font-size: 0.75rem; font-weight: 700;">15% - 24.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #a5d6a7; color: #1b5e20; font-size: 0.75rem; font-weight: 600;">8% - 14.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #c8e6c9; color: #2e7d32; font-size: 0.75rem; font-weight: 600;">5% - 7.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #fff59d; color: #5d4037; font-size: 0.75rem; font-weight: 600;">3% - 4.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #ffe082; color: #5d4037; font-size: 0.75rem; font-weight: 600;">2% - 2.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #ffccbc; color: #bf360c; font-size: 0.75rem; font-weight: 600;">1% - 1.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #ffcdd2; color: #b71c1c; font-size: 0.75rem; font-weight: 600;">&lt; 1%</span>
            <span class="badge bg-white text-dark border ms-md-auto small py-1 px-2 shadow-xs">
                <i class="fa-solid fa-hand-pointer text-primary me-1"></i>Klik sel untuk rincian Caleg &amp; RW
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-nowrap mb-0 no-datatable" id="tablePartyStrength" style="font-size: 0.86rem;">
                <thead class="text-white text-center align-middle" style="background-color: #1e293b;">
                    <tr>
                        <th style="min-width: 140px; padding: 10px 12px; text-align: left;">PARTAI | KEL</th>
                        <?php 
                        $vShortLabels = [
                            '3276011006' => 'DEPOK',
                            '3276011007' => 'DEPOK JAYA',
                            '3276011009' => 'MAMPANG',
                            '3276011008' => 'PANMAS',
                            '3276011011' => 'RANGK JAYA',
                            '3276011010' => 'RJB',
                        ];
                        foreach ($villagesOrdered as $v): 
                            $shortName = $vShortLabels[$v['id']] ?? strtoupper($v['name']);
                        ?>
                            <th style="min-width: 95px; padding: 10px 8px;"><?= esc($shortName) ?></th>
                        <?php endforeach; ?>
                        <th style="min-width: 95px; padding: 10px 8px; background-color: #0f172a;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($partyStrengthMatrix)): ?>
                        <tr>
                            <td colspan="<?= 2 + count($villagesOrdered) ?>" class="text-center py-4 text-muted">Belum ada data perolehan suara partai.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($partyStrengthMatrix as $pm): 
                            $isTop10 = $pm['is_top10'];
                            $rowClass = $isTop10 ? 'party-row-top10' : 'party-row-other d-none';
                            $rowStyle = $pm['is_pks'] ? 'background-color: #fffde7;' : '';
                        ?>
                            <tr class="<?= $rowClass ?>" style="<?= $rowStyle ?>" data-top10="<?= $isTop10 ? '1' : '0' ?>">
                                <td class="fw-bold <?= $pm['is_pks'] ? 'border-start border-4 border-warning' : '' ?>" style="background-color: <?= $pm['is_pks'] ? '#fff9c4' : '#f8fafc' ?>;">
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($pm['lambang_partai'])): ?>
                                            <img src="<?= base_url('uploads/partai/' . $pm['lambang_partai']) ?>" alt="Logo" class="rounded" style="width: 22px; height: 22px; object-fit: contain;">
                                        <?php endif; ?>
                                        <span class="text-dark"><?= esc($pm['akronim']) ?></span>
                                        <?php if ($pm['is_pks']): ?>
                                            <span class="badge bg-warning text-dark ms-auto" style="font-size: 0.65rem;">PKS</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php foreach ($villagesOrdered as $v): 
                                    $vData = $pm['villages'][$v['id']] ?? ['votes' => 0, 'suara_partai' => 0, 'suara_caleg' => 0, 'percent' => 0];
                                    $pct = $vData['percent'];
                                    $votes = $vData['votes'];

                                    // Heatmap cell styling matching reference image
                                    $cellStyle = '';
                                    if ($pct >= 25.0) {
                                        $cellStyle = 'background-color: #2e7d32; color: #ffffff; font-weight: 700;';
                                    } elseif ($pct >= 15.0) {
                                        $cellStyle = 'background-color: #66bb6a; color: #0d3813; font-weight: 700;';
                                    } elseif ($pct >= 8.0) {
                                        $cellStyle = 'background-color: #a5d6a7; color: #1b5e20; font-weight: 600;';
                                    } elseif ($pct >= 5.0) {
                                        $cellStyle = 'background-color: #c8e6c9; color: #2e7d32; font-weight: 600;';
                                    } elseif ($pct >= 3.0) {
                                        $cellStyle = 'background-color: #fff59d; color: #5d4037; font-weight: 600;';
                                    } elseif ($pct >= 2.0) {
                                        $cellStyle = 'background-color: #ffe082; color: #5d4037; font-weight: 600;';
                                    } elseif ($pct >= 1.0) {
                                        $cellStyle = 'background-color: #ffccbc; color: #bf360c; font-weight: 500;';
                                    } else {
                                        $cellStyle = 'background-color: #ffcdd2; color: #b71c1c; font-weight: 500;';
                                    }
                                    $tooltip = 'Total: ' . number_format($votes) . ' (Partai: ' . number_format($vData['suara_partai'] ?? 0) . ' | Caleg: ' . number_format($vData['suara_caleg'] ?? 0) . ') | Persentase: ' . number_format($pct, 1, ',', '.') . '%';
                                ?>
                                    <td class="text-center party-strength-cell" 
                                        style="<?= $cellStyle ?>" 
                                        title="<?= esc($tooltip) ?>&#10;Klik untuk rincian suara caleg &amp; basis RW"
                                        onclick="openPartyVillageBreakdown(<?= $pm['id'] ?>, '<?= $v['id'] ?>', '<?= esc(addslashes($pm['nama_partai'] ?? $pm['akronim'])) ?>', '<?= esc(addslashes($v['name'])) ?>')">
                                        <div class="party-val-pct fw-bold"><?= number_format($pct, 1, ',', '.') ?>%</div>
                                        <div class="party-val-votes fw-bold d-none"><?= number_format($votes) ?></div>
                                    </td>
                                <?php endforeach; ?>
                                <?php 
                                $totTooltip = 'Total Kecamatan: ' . number_format($pm['total_suara']) . ' (Partai: ' . number_format($pm['total_partai'] ?? 0) . ' | Caleg: ' . number_format($pm['total_caleg'] ?? 0) . ') | Persentase: ' . number_format($pm['total_percent'], 1, ',', '.') . '%';
                                ?>
                                <td class="text-center fw-bold" style="background-color: #f1f5f9; color: #0f172a;" title="<?= esc($totTooltip) ?>">
                                    <div class="party-val-pct fw-bold"><?= number_format($pm['total_percent'], 1, ',', '.') ?>%</div>
                                    <div class="party-val-votes fw-bold d-none"><?= number_format($pm['total_suara']) ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TOP 20 CALEG DENGAN BREAKDOWN SEBARAN KELURAHAN & HEATMAP -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header p-4 bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1 text-dark-coffee d-flex align-items-center gap-2">
                <i class="fa-solid fa-trophy text-gold"></i>
                <span>TOP 20 Caleg <?= ($selectedTingkat === 'DPRD_PROV' ? '- DPRD Provinsi Jawa Barat' : ($selectedTingkat === 'DPR_RI' ? '- DPR RI' : '- Dapil 1 Pancoran Mas')) ?></span>
            </h5>
            <p class="text-secondary small mb-0">
                Peringkat 20 Caleg peraih suara terbanyak dan sebaran persentase perolehan suara di 6 Kelurahan se-Kecamatan Pancoran Mas.
                <span class="badge bg-warning text-dark ms-2"><i class="fa-solid fa-chair me-1"></i>Peringkat 1 - <?= $alokasiKursi ?>: Zona Peraih Kursi</span>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small fw-semibold text-muted me-1">Mode Tampilan:</span>
            <div class="btn-group btn-group-sm" role="group" id="top20ViewModeGroup">
                <button type="button" class="btn btn-primary active" id="btnModePct" onclick="setTop20ViewMode('percent')">
                    <i class="fa-solid fa-percent me-1"></i>% Persentase
                </button>
                <button type="button" class="btn btn-outline-primary" id="btnModeVotes" onclick="setTop20ViewMode('votes')">
                    <i class="fa-solid fa-check-to-slot me-1"></i>Jumlah Suara
                </button>
            </div>
        </div>
    </div>
    
    <div class="card-body p-4 pt-0">
        <!-- Legenda Heatmap -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 bg-light rounded-3 small border">
            <span class="fw-bold text-muted me-1"><i class="fa-solid fa-palette me-1"></i>Legenda Heatmap:</span>
            <span class="px-2 py-1 rounded" style="background-color: #2e7d32; color: #fff; font-size: 0.75rem; font-weight: 700;">&ge; 25%</span>
            <span class="px-2 py-1 rounded" style="background-color: #66bb6a; color: #0d3813; font-size: 0.75rem; font-weight: 700;">15% - 24.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #a5d6a7; color: #1b5e20; font-size: 0.75rem; font-weight: 600;">8% - 14.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #c8e6c9; color: #2e7d32; font-size: 0.75rem; font-weight: 600;">5% - 7.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #fff59d; color: #5d4037; font-size: 0.75rem; font-weight: 600;">3% - 4.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #ffe082; color: #5d4037; font-size: 0.75rem; font-weight: 600;">2% - 2.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #ffccbc; color: #bf360c; font-size: 0.75rem; font-weight: 600;">1% - 1.9%</span>
            <span class="px-2 py-1 rounded" style="background-color: #ffcdd2; color: #b71c1c; font-size: 0.75rem; font-weight: 600;">&lt; 1%</span>
            <span class="ms-auto d-flex align-items-center gap-1">
                <span class="d-inline-block rounded border border-warning" style="width: 14px; height: 14px; background-color: #fff9c4;"></span>
                <span class="text-muted" style="font-size: 0.75rem; font-weight: 600;">Alokasi Kursi (1-<?= $alokasiKursi ?>)</span>
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle text-nowrap mb-0 no-datatable" id="tableTop20Caleg" style="font-size: 0.84rem;">
                <thead class="text-white text-center align-middle" style="background-color: #1e293b;">
                    <tr>
                        <th style="width: 45px; padding: 10px 6px;">NO</th>
                        <th style="min-width: 220px; padding: 10px 12px; text-align: left;">NAMA CALEG</th>
                        <th style="min-width: 85px; padding: 10px 8px;">PARTAI</th>
                        <?php 
                        $vShortLabels = [
                            '3276011006' => 'DEPOK',
                            '3276011007' => 'DEP JAYA',
                            '3276011009' => 'MMPANG',
                            '3276011008' => 'PANMAS',
                            '3276011011' => 'RNG JAYA',
                            '3276011010' => 'RJB',
                        ];
                        foreach ($villagesOrdered as $v): 
                            $shortName = $vShortLabels[$v['id']] ?? strtoupper(substr($v['name'], 0, 8));
                        ?>
                            <th style="min-width: 85px; padding: 10px 6px;"><?= esc($shortName) ?></th>
                        <?php endforeach; ?>
                        <th style="min-width: 95px; padding: 10px 8px; background-color: #0f172a;">TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($top20Caleg)): ?>
                        <tr>
                            <td colspan="<?= 4 + count($villagesOrdered) ?>" class="text-center py-4 text-muted">Belum ada data perolehan suara caleg.</td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $partyShortMap = [
                            'Partai Keadilan Sejahtera' => 'PKS',
                            'Partai Gerakan Indonesia Raya' => 'GERINDRA',
                            'Partai Gerindra' => 'GERINDRA',
                            'Partai Persatuan Pembangunan' => 'PPP',
                            'Partai Golongan Karya' => 'GOLKAR',
                            'Partai Golkar' => 'GOLKAR',
                            'Partai Demokrasi Indonesia Perjuangan' => 'PDIP',
                            'Partai Perindo' => 'PERINDO',
                            'Partai Persatuan Indonesia' => 'PERINDO',
                            'Partai Demokrat' => 'DEMOKRAT',
                            'Partai Amanat Nasional' => 'PAN',
                            'Partai Kebangkitan Bangsa' => 'PKB',
                            'Partai Solidaritas Indonesia' => 'PSI',
                            'Partai NasDem' => 'NASDEM',
                            'Partai Nasdem' => 'NASDEM',
                            'Partai Gelombang Rakyat Indonesia' => 'GELORA',
                            'Partai Gelora' => 'GELORA',
                            'Partai Buruh' => 'BURUH',
                            'Partai Ummat' => 'UMMAT',
                            'Partai Kebangkitan Nusantara' => 'PKN',
                            'Partai Bulan Bintang' => 'PBB',
                            'Partai Garda Republik Indonesia' => 'GARUDA',
                            'Partai Hati Nurani Rakyat' => 'HANURA',
                        ];

                        foreach ($top20Caleg as $c): 
                            $isKursi = $c['is_kursi'];
                            $rankBg = $isKursi ? 'background-color: #fff9c4 !important; font-weight: 700;' : '';
                            $nameBg = $isKursi ? 'background-color: #fff9c4 !important;' : '';
                            
                            $pName = $c['nama_partai'];
                            $pShort = $partyShortMap[$pName] ?? $pName;
                        ?>
                            <tr>
                                <td class="text-center" style="<?= $rankBg ?>">
                                    <?php if ($isKursi): ?>
                                        <i class="fa-solid fa-chair text-warning me-1" style="font-size: 0.72rem;" title="Alokasi Kursi #<?= $c['rank'] ?>"></i>
                                    <?php endif; ?>
                                    <?= $c['rank'] ?>
                                </td>
                                <td style="<?= $nameBg ?>">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span class="text-dark <?= $isKursi ? 'fw-bold' : 'fw-semibold' ?>"><?= esc($c['nama_caleg']) ?></span>
                                        <?php if ($c['is_pks']): ?>
                                            <span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;">PKS</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border fw-bold" style="font-size: 0.75rem;">
                                        <?= esc($pShort) ?>
                                    </span>
                                </td>
                                <?php foreach ($villagesOrdered as $v): 
                                    $vData = $c['villages'][$v['id']] ?? ['votes' => 0, 'percent' => 0];
                                    $pct = $vData['percent'];
                                    $votes = $vData['votes'];

                                    // Heatmap styling based on reference table
                                    $cellStyle = '';
                                    if ($pct >= 25.0) {
                                        $cellStyle = 'background-color: #2e7d32; color: #ffffff; font-weight: 700;';
                                    } elseif ($pct >= 15.0) {
                                        $cellStyle = 'background-color: #66bb6a; color: #0d3813; font-weight: 700;';
                                    } elseif ($pct >= 8.0) {
                                        $cellStyle = 'background-color: #a5d6a7; color: #1b5e20; font-weight: 600;';
                                    } elseif ($pct >= 5.0) {
                                        $cellStyle = 'background-color: #c8e6c9; color: #2e7d32; font-weight: 600;';
                                    } elseif ($pct >= 3.0) {
                                        $cellStyle = 'background-color: #fff59d; color: #5d4037; font-weight: 600;';
                                    } elseif ($pct >= 2.0) {
                                        $cellStyle = 'background-color: #ffe082; color: #5d4037; font-weight: 600;';
                                    } elseif ($pct >= 1.0) {
                                        $cellStyle = 'background-color: #ffccbc; color: #bf360c; font-weight: 500;';
                                    } else {
                                        $cellStyle = 'background-color: #ffcdd2; color: #b71c1c; font-weight: 500;';
                                    }
                                ?>
                                    <td class="text-center top20-val-cell" style="<?= $cellStyle ?>" title="Suara: <?= number_format($votes) ?> | Persentase: <?= number_format($pct, 1, ',', '.') ?>%">
                                        <div class="val-pct fw-bold"><?= number_format($pct, 1, ',', '.') ?>%</div>
                                        <div class="val-votes fw-bold d-none"><?= number_format($votes) ?></div>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-center fw-bold" style="background-color: #f1f5f9; color: #0f172a;" title="Total: <?= number_format($c['total_suara']) ?> suara (<?= number_format($c['total_percent'], 1, ',', '.') ?>%)">
                                    <div class="val-pct fw-bold"><?= number_format($c['total_percent'], 1, ',', '.') ?>%</div>
                                    <div class="val-votes fw-bold d-none"><?= number_format($c['total_suara']) ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 5 BESAR RW TERKUAT PKS DI SETIAP KELURAHAN -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header p-4 bg-transparent border-0">
        <h5 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-map-location-dot text-gold me-2"></i>5 Besar RW Terkuat PKS di Setiap Kelurahan
        </h5>
        <p class="text-secondary small mb-0 mt-1">Daftar RW dengan kontribusi suara PKS tertinggi per wilayah Kelurahan</p>
    </div>
    <div class="card-body p-4 pt-0">
        <div class="row g-4">
            <?php foreach ($topRwsPerKelurahan as $kelName => $rws): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 bg-light-subtle" style="border-radius: 12px; border-top: 4px solid #f26522 !important;">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-dark-coffee mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-building me-2 text-gold"></i>Kel. <?= esc($kelName) ?></span>
                                <span class="badge bg-warning text-dark small font-monospace">Top 5 RW</span>
                            </h6>
                            <?php if (empty($rws)): ?>
                                <p class="text-muted small py-4 text-center">Belum ada data suara.</p>
                            <?php else: ?>
                                <?php 
                                $maxRwVotes = count($rws) > 0 ? $rws[0]['total_suara'] : 1;
                                foreach ($rws as $rw): 
                                    $rwPercent = ($rw['total_suara'] / $maxRwVotes) * 100;
                                ?>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="small fw-semibold text-dark-coffee">RW <?= esc($rw['rw']) ?></span>
                                            <span class="small fw-bold text-dark-coffee"><?= number_format($rw['total_suara']) ?> suara</span>
                                        </div>
                                        <div class="progress" style="height: 8px; border-radius: 4px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= $rwPercent ?>%; background-color: #ff9100; border-radius: 4px;" 
                                                 aria-valuenow="<?= $rw['total_suara'] ?>" aria-valuemin="0" aria-valuemax="<?= $maxRwVotes ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- INTERACTIVE CALEG AND DETAIL LIST -->
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header p-4 bg-transparent border-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">Daftar Semua Partai</h5>
                <p class="text-muted small mb-0 mt-1">Pilih baris untuk memuat suara caleg secara interaktif.</p>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="partyTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4" style="width: 60px;">#</th>
                                <th style="width: 80px;">No Urut</th>
                                <th>Partai Politik</th>
                                <th class="text-end pe-4" style="width: 120px;">Total Suara</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach ($partyResults as $r): 
                                $isPKS = ($r['id'] == $pksId);
                            ?>
                                <tr class="party-row cursor-pointer <?= $isPKS ? 'table-warning' : '' ?>" data-id="<?= $r['id'] ?>" data-name="<?= esc($r['nama_partai']) ?>" style="cursor: pointer;">
                                    <td class="ps-4 text-muted"><?= $rank++ ?></td>
                                    <td class="text-center fw-bold"><?= esc($r['no_urut']) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($r['lambang_partai'])): ?>
                                                <img src="<?= base_url('uploads/partai/' . $r['lambang_partai']) ?>" alt="Logo" class="rounded" style="width: 24px; height: 24px; object-fit: contain;">
                                            <?php endif; ?>
                                            <span><?= esc($r['nama_partai']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-end pe-4 fw-bold text-dark-coffee"><?= number_format($r['total_suara']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
            <div class="card-header p-4 bg-transparent border-0">
                <h5 class="fw-bold mb-0 text-dark-coffee" id="calegTitle">Perolehan Suara Caleg</h5>
                <p class="text-muted small mb-0 mt-1">Daftar perolehan suara caleg dari partai terpilih.</p>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="alert alert-secondary d-flex align-items-center gap-3 border-0 d-none" role="alert" id="welcomeAlert" style="border-radius: 8px;">
                    <i class="fa-solid fa-arrow-left text-secondary fs-4"></i>
                    <div>Pilih partai pada tabel untuk menampilkan perolehan suara Caleg.</div>
                </div>
                <div id="calegLoader" class="text-center py-5 d-none">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2">Memuat suara caleg...</p>
                </div>
                <div id="calegContainer">
                    <ul class="list-group list-group-flush" id="calegList">
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL BREAKDOWN SUARA PARTAI & CALEG PER KELURAHAN -->
<div class="modal fade" id="modalPartyVillageBreakdown" tabindex="-1" aria-labelledby="modalPartyVillageBreakdownLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
            <div class="modal-header text-white" style="background-color: #1e293b;">
                <div class="d-flex align-items-center gap-3">
                    <img id="modalPartyLogo" src="" alt="Logo" class="rounded bg-white p-1" style="width: 40px; height: 40px; object-fit: contain; display: none;">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="modal-title fw-bold mb-0 text-white" id="modalPartyVillageBreakdownLabel">Rincian Perolehan Suara</h5>
                            <span class="badge bg-warning text-dark fw-bold" id="modalPartyNoUrut">No. -</span>
                        </div>
                        <p class="small mb-0 text-light opacity-75 mt-1" id="modalPartyVillageSub">
                            <span id="modalPartyName" class="fw-bold">PKS</span> &bull; <i class="fa-solid fa-location-dot me-1"></i>Kelurahan <span id="modalVillageName" class="fw-bold text-warning">Depok</span>
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4" style="background-color: #f8fafc;">
                <!-- Loading State -->
                <div id="modalBreakdownLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">Mengambil data rincian caleg dan basis RW...</p>
                </div>

                <!-- Content Container -->
                <div id="modalBreakdownContent" class="d-none">
                    <!-- KPI Summary Cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; background-color: #ffffff; border-left: 4px solid #1e293b !important;">
                                <div class="card-body p-3">
                                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem;">Total Suara</div>
                                    <h4 class="fw-bold mb-0 text-dark" id="kpiTotalSuara">0</h4>
                                    <small class="text-muted" style="font-size: 0.72rem;" id="kpiPartyShare">0% suara sah</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; background-color: #ffffff; border-left: 4px solid #f59e0b !important;">
                                <div class="card-body p-3">
                                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem;">Coblos Logo Partai</div>
                                    <h4 class="fw-bold mb-0 text-warning" id="kpiSuaraPartai">0</h4>
                                    <small class="text-muted" style="font-size: 0.72rem;" id="kpiPctPartai">0% dr total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; background-color: #ffffff; border-left: 4px solid #10b981 !important;">
                                <div class="card-body p-3">
                                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem;">Suara Caleg</div>
                                    <h4 class="fw-bold mb-0 text-success" id="kpiSuaraCaleg">0</h4>
                                    <small class="text-muted" style="font-size: 0.72rem;" id="kpiPctCaleg">0% dr total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 10px; background-color: #ffffff; border-left: 4px solid #64748b !important;">
                                <div class="card-body p-3">
                                    <div class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem;">Suara Sah Kelurahan</div>
                                    <h4 class="fw-bold mb-0 text-secondary" id="kpiTotalSahKel">0</h4>
                                    <small class="text-muted" style="font-size: 0.72rem;">Seluruh partai</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nav Tabs: 1. Suara Caleg | 2. Basis Suara RW -->
                    <ul class="nav nav-pills mb-3 gap-2" id="breakdownTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-3 py-2" id="tab-caleg-btn" data-bs-toggle="pill" data-bs-target="#tab-caleg" type="button" role="tab" style="border-radius: 8px;">
                                <i class="fa-solid fa-users me-1"></i> Data Suara Caleg (<span id="countCaleg">0</span>)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-3 py-2" id="tab-rw-btn" data-bs-toggle="pill" data-bs-target="#tab-rw" type="button" role="tab" style="border-radius: 8px;">
                                <i class="fa-solid fa-map-location-dot me-1"></i> Data RW Basis Suara (<span id="countRw">0</span>)
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="breakdownTabContent">
                        <!-- TAB 1: DATA SUARA CALEG -->
                        <div class="tab-pane fade show active" id="tab-caleg" role="tabpanel">
                            <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped align-middle mb-0 no-datatable" style="font-size: 0.88rem;">
                                        <thead class="text-white" style="background-color: #334155;">
                                            <tr>
                                                <th class="text-center" style="width: 55px;">No</th>
                                                <th>Nama Caleg / Rincian</th>
                                                <th class="text-end" style="width: 140px;">Jumlah Suara</th>
                                                <th class="text-end" style="width: 150px;">% Kontribusi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalCalegTableBody">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                        <tfoot class="fw-bold" style="background-color: #f1f5f9;">
                                            <tr>
                                                <td colspan="2" class="text-end text-dark">TOTAL SUARA (PARTAI + CALEG):</td>
                                                <td class="text-end text-dark" id="modalCalegFooterTotal">0</td>
                                                <td class="text-end text-dark">100.0%</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: DATA BASIS SUARA RW -->
                        <div class="tab-pane fade" id="tab-rw" role="tabpanel">
                            <div class="card border-0 shadow-sm" style="border-radius: 10px; overflow: hidden;">
                                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">
                                            <i class="fa-solid fa-ranking-star text-warning me-1"></i> Peringkat Basis Suara per RW
                                        </h6>
                                        <small class="text-muted">Diurutkan dari perolehan suara terbanyak (lumbung suara partai)</small>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0 no-datatable" style="font-size: 0.88rem;">
                                        <thead class="text-white" style="background-color: #334155;">
                                            <tr>
                                                <th class="text-center" style="width: 65px;">Rank</th>
                                                <th style="width: 110px;">RW</th>
                                                <th class="text-center" style="width: 90px;">Jml TPS</th>
                                                <th class="text-end" style="width: 120px;">Coblos Partai</th>
                                                <th class="text-end" style="width: 120px;">Suara Caleg</th>
                                                <th class="text-end" style="width: 130px;">Total Suara</th>
                                                <th style="min-width: 160px;">% Kontribusi RW</th>
                                            </tr>
                                        </thead>
                                        <tbody id="modalRwTableBody">
                                            <!-- Dynamically populated -->
                                        </tbody>
                                        <tfoot class="fw-bold" style="background-color: #f1f5f9;">
                                            <tr>
                                                <td colspan="2" class="text-end text-dark">TOTAL KELURAHAN:</td>
                                                <td class="text-center text-dark" id="modalRwFooterTps">0</td>
                                                <td class="text-end text-dark" id="modalRwFooterPartai">0</td>
                                                <td class="text-end text-dark" id="modalRwFooterCaleg">0</td>
                                                <td class="text-end text-dark" id="modalRwFooterTotal">0</td>
                                                <td class="text-dark">100.0%</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer bg-light border-0 py-2">
                <button type="button" class="btn btn-secondary btn-sm px-3 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function setPartyStrengthViewMode(mode) {
    const btnPct = document.getElementById('btnPartyModePct');
    const btnVotes = document.getElementById('btnPartyModeVotes');
    const pctElements = document.querySelectorAll('#tablePartyStrength .party-val-pct');
    const votesElements = document.querySelectorAll('#tablePartyStrength .party-val-votes');

    if (mode === 'votes') {
        if (btnVotes) {
            btnVotes.classList.add('btn-primary', 'active');
            btnVotes.classList.remove('btn-outline-primary');
        }
        if (btnPct) {
            btnPct.classList.remove('btn-primary', 'active');
            btnPct.classList.add('btn-outline-primary');
        }
        pctElements.forEach(el => el.classList.add('d-none'));
        votesElements.forEach(el => el.classList.remove('d-none'));
    } else {
        if (btnPct) {
            btnPct.classList.add('btn-primary', 'active');
            btnPct.classList.remove('btn-outline-primary');
        }
        if (btnVotes) {
            btnVotes.classList.remove('btn-primary', 'active');
            btnVotes.classList.add('btn-outline-primary');
        }
        pctElements.forEach(el => el.classList.remove('d-none'));
        votesElements.forEach(el => el.classList.add('d-none'));
    }
}

function setPartyFilterMode(mode) {
    const btnTop10 = document.getElementById('btnPartyTop10');
    const btnAll = document.getElementById('btnPartyAll');
    const otherRows = document.querySelectorAll('#tablePartyStrength .party-row-other');

    if (mode === 'all') {
        if (btnAll) {
            btnAll.classList.add('active', 'btn-secondary');
            btnAll.classList.remove('btn-outline-secondary');
        }
        if (btnTop10) {
            btnTop10.classList.remove('active', 'btn-secondary');
            btnTop10.classList.add('btn-outline-secondary');
        }
        otherRows.forEach(r => r.classList.remove('d-none'));
    } else {
        if (btnTop10) {
            btnTop10.classList.add('active', 'btn-secondary');
            btnTop10.classList.remove('btn-outline-secondary');
        }
        if (btnAll) {
            btnAll.classList.remove('active', 'btn-secondary');
            btnAll.classList.add('btn-outline-secondary');
        }
        otherRows.forEach(r => r.classList.add('d-none'));
    }
}

function setTop20ViewMode(mode) {
    const btnPct = document.getElementById('btnModePct');
    const btnVotes = document.getElementById('btnModeVotes');
    const pctElements = document.querySelectorAll('#tableTop20Caleg .val-pct');
    const votesElements = document.querySelectorAll('#tableTop20Caleg .val-votes');

    if (mode === 'votes') {
        if (btnVotes) {
            btnVotes.classList.add('btn-primary', 'active');
            btnVotes.classList.remove('btn-outline-primary');
        }
        if (btnPct) {
            btnPct.classList.remove('btn-primary', 'active');
            btnPct.classList.add('btn-outline-primary');
        }
        pctElements.forEach(el => el.classList.add('d-none'));
        votesElements.forEach(el => el.classList.remove('d-none'));
    } else {
        if (btnPct) {
            btnPct.classList.add('btn-primary', 'active');
            btnPct.classList.remove('btn-outline-primary');
        }
        if (btnVotes) {
            btnVotes.classList.remove('btn-primary', 'active');
            btnVotes.classList.add('btn-outline-primary');
        }
        pctElements.forEach(el => el.classList.remove('d-none'));
        votesElements.forEach(el => el.classList.add('d-none'));
    }
}

function openPartyVillageBreakdown(partaiId, villageId, partyNameHint, villageNameHint) {
    const modalEl = document.getElementById('modalPartyVillageBreakdown');
    if (!modalEl) return;

    let bsModal = bootstrap.Modal.getInstance(modalEl);
    if (!bsModal) {
        bsModal = new bootstrap.Modal(modalEl);
    }

    // Reset and show initial placeholders
    document.getElementById('modalPartyName').textContent = partyNameHint || 'Partai';
    document.getElementById('modalVillageName').textContent = villageNameHint || 'Kelurahan';
    document.getElementById('modalPartyNoUrut').textContent = 'Memuat...';
    document.getElementById('modalPartyLogo').style.display = 'none';

    document.getElementById('modalBreakdownLoading').innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="text-muted small mt-2 mb-0">Mengambil data rincian caleg dan basis RW...</p>
    `;
    document.getElementById('modalBreakdownLoading').classList.remove('d-none');
    document.getElementById('modalBreakdownContent').classList.add('d-none');

    // Reset to first tab (Caleg)
    const tabCalegBtn = document.getElementById('tab-caleg-btn');
    if (tabCalegBtn) {
        const bsTab = bootstrap.Tab.getOrCreateInstance(tabCalegBtn);
        bsTab.show();
    }

    bsModal.show();

    const selectedYear = "<?= $selectedYear ?>";
    const selectedTingkat = "<?= $selectedTingkat ?>";
    const url = `<?= base_url('admin/pemilu/hasil/get_party_village_breakdown') ?>?partai_id=${encodeURIComponent(partaiId)}&village_id=${encodeURIComponent(villageId)}&tahun=${encodeURIComponent(selectedYear)}&tingkat_pemilu=${encodeURIComponent(selectedTingkat)}`;

    fetch(url)
        .then(res => res.json())
        .then(res => {
            if (res.status !== 'success') {
                document.getElementById('modalBreakdownLoading').innerHTML = `
                    <div class="text-danger py-4">
                        <i class="fa-solid fa-circle-exclamation fs-3 mb-2"></i>
                        <p class="mb-0">${res.message || 'Gagal memuat rincian data.'}</p>
                    </div>
                `;
                return;
            }

            const party = res.party;
            const village = res.village;
            const stats = res.stats;

            document.getElementById('modalPartyName').textContent = party.nama_partai;
            document.getElementById('modalVillageName').textContent = village.name;
            document.getElementById('modalPartyNoUrut').textContent = 'No. ' + party.no_urut;

            const logoEl = document.getElementById('modalPartyLogo');
            if (party.lambang_partai) {
                logoEl.src = `<?= base_url('uploads/partai/') ?>/${party.lambang_partai}`;
                logoEl.style.display = 'block';
            } else {
                logoEl.style.display = 'none';
            }

            // KPIs
            document.getElementById('kpiTotalSuara').textContent = Number(stats.total_suara).toLocaleString('id-ID');
            document.getElementById('kpiPartyShare').textContent = stats.party_share_percent + '% suara sah kel.';

            document.getElementById('kpiSuaraPartai').textContent = Number(stats.suara_partai).toLocaleString('id-ID');
            document.getElementById('kpiPctPartai').textContent = stats.pct_suara_partai + '% dr total partai';

            document.getElementById('kpiSuaraCaleg').textContent = Number(stats.suara_caleg).toLocaleString('id-ID');
            document.getElementById('kpiPctCaleg').textContent = stats.pct_suara_caleg + '% dr total partai';

            document.getElementById('kpiTotalSahKel').textContent = Number(stats.total_suara_sah_kelurahan).toLocaleString('id-ID');

            // Tab 1: Caleg Table
            document.getElementById('countCaleg').textContent = res.caleg_list.length;
            const calegTbody = document.getElementById('modalCalegTableBody');
            calegTbody.innerHTML = '';

            // Row Coblos Lambang Partai
            const trParty = document.createElement('tr');
            trParty.className = 'table-warning fw-semibold';
            trParty.innerHTML = `
                <td class="text-center text-muted">-</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-flag text-warning"></i>
                        <span class="text-dark">Suara Coblos Lambang Partai (${party.akronim})</span>
                    </div>
                </td>
                <td class="text-end fw-bold text-dark">${Number(stats.suara_partai).toLocaleString('id-ID')}</td>
                <td class="text-end text-muted fw-bold">${stats.pct_suara_partai}%</td>
            `;
            calegTbody.appendChild(trParty);

            if (res.caleg_list.length === 0) {
                const emptyTr = document.createElement('tr');
                emptyTr.innerHTML = '<td colspan="4" class="text-center text-muted py-3">Tidak ada daftar caleg untuk partai ini.</td>';
                calegTbody.appendChild(emptyTr);
            } else {
                let maxCalegVote = 0;
                res.caleg_list.forEach(c => {
                    if (Number(c.suara_caleg) > maxCalegVote) maxCalegVote = Number(c.suara_caleg);
                });

                res.caleg_list.forEach(c => {
                    const cVotes = Number(c.suara_caleg);
                    const isTop = (cVotes > 0 && cVotes === maxCalegVote);
                    const tr = document.createElement('tr');
                    if (isTop) tr.style.backgroundColor = '#ecfdf5';
                    tr.innerHTML = `
                        <td class="text-center fw-bold text-muted">${c.no_urut}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark-coffee">${c.nama_caleg}</span>
                                ${isTop ? '<span class="badge bg-success" style="font-size:0.65rem;"><i class="fa-solid fa-crown me-1"></i>Tertinggi</span>' : ''}
                            </div>
                        </td>
                        <td class="text-end fw-bold text-dark">${cVotes.toLocaleString('id-ID')}</td>
                        <td class="text-end">
                            <span class="fw-semibold text-dark">${c.pct_of_total}%</span>
                            <div class="small text-muted" style="font-size: 0.72rem;">(${c.pct_of_caleg}% dr total caleg)</div>
                        </td>
                    `;
                    calegTbody.appendChild(tr);
                });
            }
            document.getElementById('modalCalegFooterTotal').textContent = Number(stats.total_suara).toLocaleString('id-ID');

            // Tab 2: RW Table
            document.getElementById('countRw').textContent = res.rw_list.length;
            const rwTbody = document.getElementById('modalRwTableBody');
            rwTbody.innerHTML = '';

            let totRwTps = 0;
            let totRwPartai = 0;
            let totRwCaleg = 0;
            let totRwTotal = 0;

            if (res.rw_list.length === 0) {
                const emptyTr = document.createElement('tr');
                emptyTr.innerHTML = '<td colspan="7" class="text-center text-muted py-3">Belum ada data RW untuk kelurahan ini.</td>';
                rwTbody.appendChild(emptyTr);
            } else {
                res.rw_list.forEach(rw => {
                    const rTot = Number(rw.total_suara);
                    const rSp = Number(rw.suara_partai);
                    const rSc = Number(rw.suara_caleg);
                    const rTps = Number(rw.tps_count);

                    totRwTps += rTps;
                    totRwPartai += rSp;
                    totRwCaleg += rSc;
                    totRwTotal += rTot;

                    const isTop3 = rw.rank <= 3 && rTot > 0;
                    const tr = document.createElement('tr');
                    if (rw.rank === 1 && rTot > 0) {
                        tr.style.backgroundColor = '#fef9c3';
                    } else if (isTop3) {
                        tr.style.backgroundColor = '#f8fafc';
                    }

                    let rankBadge = `<span class="badge bg-secondary text-white">${rw.rank}</span>`;
                    if (rw.rank === 1) {
                        rankBadge = `<span class="badge bg-warning text-dark fw-bold"><i class="fa-solid fa-crown me-1"></i>#1</span>`;
                    } else if (rw.rank === 2) {
                        rankBadge = `<span class="badge bg-info text-dark fw-bold">#2</span>`;
                    } else if (rw.rank === 3) {
                        rankBadge = `<span class="badge bg-primary text-white fw-bold">#3</span>`;
                    }

                    tr.innerHTML = `
                        <td class="text-center">${rankBadge}</td>
                        <td class="fw-bold text-dark">RW ${rw.rw}</td>
                        <td class="text-center"><span class="badge bg-light text-dark border">${rTps} TPS</span></td>
                        <td class="text-end text-muted">${rSp.toLocaleString('id-ID')}</td>
                        <td class="text-end text-muted">${rSc.toLocaleString('id-ID')}</td>
                        <td class="text-end fw-bold text-dark fs-6">${rTot.toLocaleString('id-ID')}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 8px;">
                                    <div class="progress-bar ${rw.rank === 1 ? 'bg-warning' : (rw.rank <= 3 ? 'bg-primary' : 'bg-secondary')}" 
                                         role="progressbar" 
                                         style="width: ${rw.bar_percent}%;"></div>
                                </div>
                                <span class="small fw-bold text-dark" style="min-width: 45px;">${rw.pct_of_party}%</span>
                            </div>
                        </td>
                    `;
                    rwTbody.appendChild(tr);
                });
            }

            document.getElementById('modalRwFooterTps').textContent = totRwTps;
            document.getElementById('modalRwFooterPartai').textContent = totRwPartai.toLocaleString('id-ID');
            document.getElementById('modalRwFooterCaleg').textContent = totRwCaleg.toLocaleString('id-ID');
            document.getElementById('modalRwFooterTotal').textContent = totRwTotal.toLocaleString('id-ID');

            document.getElementById('modalBreakdownLoading').classList.add('d-none');
            document.getElementById('modalBreakdownContent').classList.remove('d-none');
        })
        .catch(err => {
            console.error(err);
            document.getElementById('modalBreakdownLoading').innerHTML = `
                <div class="text-danger py-4">
                    <i class="fa-solid fa-circle-exclamation fs-3 mb-2"></i>
                    <p class="mb-0">Terjadi kesalahan saat memuat data. Silakan coba kembali.</p>
                </div>
            `;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    const selectedYear = "<?= $selectedYear ?>";
    const selectedTingkat = "<?= $selectedTingkat ?>";
    const selectedDistrict = "<?= $selectedDistrict ?>";
    const selectedVillage = "<?= $selectedVillage ?>";

    const rows = document.querySelectorAll('.party-row');
    const calegTitle = document.getElementById('calegTitle');
    const calegList = document.getElementById('calegList');
    const calegContainer = document.getElementById('calegContainer');
    const calegLoader = document.getElementById('calegLoader');
    const welcomeAlert = document.getElementById('welcomeAlert');

    const districtSelect = document.getElementById('districtSelect');
    if (districtSelect) {
        districtSelect.addEventListener('change', function() {
            const vSelect = document.getElementById('villageSelect');
            if (vSelect) vSelect.value = '';
            this.form.submit();
        });
    }

    rows.forEach(row => {
        row.addEventListener('click', function() {
            rows.forEach(r => r.classList.remove('table-active'));
            this.classList.add('table-active');

            const partyId = this.getAttribute('data-id');
            const partyName = this.getAttribute('data-name');

            if (welcomeAlert) welcomeAlert.classList.add('d-none');
            if (calegContainer) calegContainer.classList.add('d-none');
            if (calegLoader) calegLoader.classList.remove('d-none');
            if (calegTitle) calegTitle.innerText = "Suara Caleg " + partyName;

            fetch(`<?= base_url('admin/pemilu/hasil/get_candidates') ?>?partai_id=${partyId}&tahun=${selectedYear}&district_id=${selectedDistrict}&village_id=${selectedVillage}&tingkat_pemilu=${selectedTingkat}`)
                .then(response => response.json())
                .then(data => {
                    if (calegLoader) calegLoader.classList.add('d-none');
                    if (calegContainer) calegContainer.classList.remove('d-none');
                    if (calegList) {
                        calegList.innerHTML = '';
                        if (data.length === 0) {
                            calegList.innerHTML = '<li class="list-group-item text-center text-muted py-4">Tidak ada data caleg untuk partai ini.</li>';
                            return;
                        }

                        data.forEach((c) => {
                            const li = document.createElement('li');
                            li.className = 'list-group-item d-flex justify-content-between align-items-center py-3 border-0 border-bottom';
                            li.innerHTML = `
                                <div class="d-flex align-items-center gap-3">
                                    <span class="fw-bold text-muted" style="width: 24px;">${c.no_urut}.</span>
                                    <div>
                                        <span class="fw-semibold text-dark-coffee">${c.nama_caleg}</span>
                                    </div>
                                </div>
                                <span class="badge bg-gold text-dark fs-7 px-3 py-1 fw-bold">${parseInt(c.total_suara).toLocaleString()} suara</span>
                            `;
                            calegList.appendChild(li);
                        });
                    }
                })
                .catch(err => {
                    if (calegLoader) calegLoader.classList.add('d-none');
                    if (welcomeAlert) welcomeAlert.classList.remove('d-none');
                });
        });
    });

    const pksRow = document.querySelector('.party-row.table-warning');
    if (pksRow) {
        pksRow.click();
    } else if (rows.length > 0) {
        rows[0].click();
    }
});
</script>
<?= $this->endSection() ?>
