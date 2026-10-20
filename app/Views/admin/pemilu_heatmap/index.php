<?= $this->extend('layouts/admin') ?>

<?= $this->section('css') ?>
<!-- MapLibre GL CSS (100% Free & Open-Source) -->
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.css" />
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

    /* Tab switcher navigation */
    .custom-heatmap-tabs {
        background-color: #efebe9;
        padding: 5px;
        border-radius: 12px;
        display: inline-flex;
    }
    .custom-heatmap-tabs .nav-link {
        color: #5d4037;
        border-radius: 8px;
        padding: 8px 18px;
        font-weight: 700;
        font-size: 0.88rem;
        transition: all 0.2s ease-in-out;
        border: none;
    }
    .custom-heatmap-tabs .nav-link.active {
        background-color: #3e2723;
        color: #fff8e1;
        box-shadow: 0 4px 10px rgba(62, 39, 35, 0.25);
    }
    .custom-heatmap-tabs .nav-link:hover:not(.active) {
        background-color: #d7ccc8;
        color: #3e2723;
    }

    /* ========================================================= */
    /* HEATMAP MATRIX TABLE STYLING                              */
    /* ========================================================= */
    .heatmap-table-container {
        position: relative;
        max-height: 720px;
        overflow: auto;
        border: 1px solid #d7ccc8;
        border-radius: 14px;
        background-color: #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }
    .heatmap-table {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 11.5px;
        width: max-content;
        min-width: 100%;
        margin-bottom: 0;
    }
    /* Sticky Top Header */
    .heatmap-table thead th {
        position: sticky;
        top: 0;
        z-index: 20;
        background-color: #3e2723;
        color: #fff8e1;
        text-align: center;
        padding: 9px 6px;
        border-bottom: 2px solid #271410;
        border-right: 1px solid rgba(255, 255, 255, 0.12);
        font-weight: 600;
        white-space: nowrap;
    }
    .heatmap-table thead tr:first-child th.sticky-col {
        position: sticky;
        left: 0;
        z-index: 30;
        background-color: #271410 !important;
        text-align: left;
        padding-left: 14px;
        box-shadow: 4px 0 8px rgba(0,0,0,0.15);
    }
    /* Sticky Left Candidate / Party Column */
    .heatmap-table th.sticky-col,
    .heatmap-table td.sticky-col {
        position: sticky;
        left: 0;
        z-index: 10;
        background-color: #ffffff;
        min-width: 280px;
        max-width: 330px;
        box-shadow: 4px 0 8px rgba(0,0,0,0.06);
        border-right: 2px solid #bcaaa4 !important;
        padding: 7px 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .heatmap-table tbody tr.party-header-row td.sticky-col {
        background-color: #f7ede2 !important;
        color: #3e2723;
        font-weight: 700;
        z-index: 12;
    }
    .heatmap-table tbody tr.party-header-row {
        background-color: #fdfaf6;
        border-top: 2px solid #d7ccc8;
    }
    .heatmap-table tbody tr.party-subtotal-row td {
        background-color: #fcf6ef !important;
        font-weight: 700;
        border-top: 1px dashed #d7ccc8;
        border-bottom: 2px solid #bcaaa4;
    }
    .heatmap-table tfoot td {
        position: sticky;
        bottom: 0;
        z-index: 25;
        background-color: #3e2723;
        color: #fff8e1;
        font-weight: 700;
        border-top: 3px solid #d4af37;
        padding: 8px 6px;
        text-align: center;
    }
    .heatmap-table tfoot td.sticky-col {
        z-index: 35;
        background-color: #271410 !important;
        text-align: left;
        padding-left: 14px;
    }

    /* Heatmap Cells */
    .heat-cell {
        text-align: center;
        padding: 6px 4px !important;
        min-width: 52px;
        max-width: 60px;
        font-size: 11px;
        font-family: 'Poppins', monospace, sans-serif;
        border-right: 1px solid #f0e6e2;
        border-bottom: 1px solid #f0e6e2;
        transition: transform 0.08s ease, outline 0.08s ease;
        user-select: none;
    }
    .heat-cell:hover {
        outline: 2px solid #3e2723 !important;
        outline-offset: -2px;
        transform: scale(1.12);
        z-index: 15;
        position: relative;
        cursor: pointer;
    }
    .total-col {
        min-width: 90px;
        text-align: end;
        padding-right: 12px !important;
        font-weight: 700;
        background-color: #fff8e1;
        border-left: 2px solid #d4af37 !important;
    }
    .heatmap-legend-strip {
        display: inline-flex;
        align-items: center;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #d7ccc8;
    }
    .heatmap-legend-item {
        padding: 4px 10px;
        font-size: 10.5px;
        font-weight: 600;
        text-align: center;
    }

    /* Breakdown click triggers & modal table styling */
    .tps-header-clickable {
        cursor: pointer !important;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .tps-header-clickable:hover {
        background-color: #4e2e28 !important;
        color: #ffd54f !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.4);
    }
    .cell-breakdown-trigger {
        cursor: pointer !important;
    }
    .cell-breakdown-trigger:hover {
        outline: 2px solid #3e2723 !important;
        outline-offset: -2px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.18);
    }
    .leaderboard-highlight-row {
        background-color: #fff9db !important;
        border-left: 5px solid #d4af37 !important;
    }
    .leaderboard-highlight-row td {
        background-color: #fff9db !important;
        font-weight: 600;
    }

    /* MapLibre map styles */
    #electionHeatMap {
        height: 620px;
        width: 100%;
        border-radius: 12px;
        box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e0e0e0;
        overflow: hidden;
    }
    .maplibregl-popup-content {
        padding: 10px 14px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0,0,0,0.08);
    }
    .kelurahan-label-pin {
        background: rgba(255, 255, 255, 0.96);
        border: 1.5px solid rgba(62, 39, 35, 0.3);
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.18);
        white-space: nowrap;
        pointer-events: none;
        user-select: none;
    }
    .kelurahan-label-pin .pin-votes {
        font-weight: 700;
        color: #c62828;
        font-size: 12px;
    }
    .heatmap-legend-box {
        background: rgba(255, 255, 255, 0.95);
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        font-size: 12px;
        line-height: 18px;
        min-width: 185px;
    }
    .legend-gradient-bar {
        height: 12px;
        border-radius: 6px;
        margin-top: 5px;
        margin-bottom: 4px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- TOP BAR: TITLE & SWITCHER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-table-cells me-2 text-danger"></i>Heatmap Hasil Pemilu (Matriks Caleg &times; TPS)
        </h4>
        <p class="text-secondary small mb-0">Sebaran perolehan suara Caleg seluruh partai per TPS se-Kecamatan Pancoran Mas (<?= esc($selectedTingkat == 'DPRD_PROV' ? 'Dapil Jabar 8: Depok - Bekasi' : ($selectedTingkat == 'DPR_RI' ? 'Dapil Jabar 6: Depok - Bekasi' : 'Dapil 1 Kota Depok')) ?>)</p>
    </div>
    
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <!-- TAB SWITCHER -->
        <ul class="nav nav-pills custom-heatmap-tabs shadow-sm mb-0" id="heatmapTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-matrix-btn" data-bs-toggle="pill" data-bs-target="#tab-matrix" type="button" role="tab" aria-controls="tab-matrix" aria-selected="true">
                    <i class="fa-solid fa-table-cells me-1 text-danger"></i>Tabel Matriks TPS
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-spatial-btn" data-bs-toggle="pill" data-bs-target="#tab-spatial" type="button" role="tab" aria-controls="tab-spatial" aria-selected="false">
                    <i class="fa-solid fa-map-location-dot me-1 text-primary"></i>Peta Spasial
                </button>
            </li>
        </ul>

        <a href="<?= base_url('admin/pemilu/hasil') ?>?tingkat_pemilu=<?= esc($selectedTingkat) ?>" class="btn btn-outline-secondary fw-semibold btn-sm px-3 py-2 shadow-sm" style="border-radius: 8px;">
            <i class="fa-solid fa-chart-column me-1 text-gold"></i>Dashboard Ringkasan
        </a>
    </div>
</div>

<div class="tab-content" id="heatmapTabContent">

    <!-- ============================================================== -->
    <!-- TAB 1: TABEL HEATMAP TPS (VERTIKAL CALEG, HORIZONTAL TPS)     -->
    <!-- ============================================================== -->
    <div class="tab-pane fade show active" id="tab-matrix" role="tabpanel" aria-labelledby="tab-matrix-btn">
        
        <!-- FILTER BAR TABEL HEATMAP -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #fdfbf9, #f7f3f0); border: 1px solid #ebdcd5 !important;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 pb-3 border-bottom border-secondary border-opacity-10 gap-2">
                    <div>
                        <h5 class="fw-bold text-dark-coffee mb-1">
                            <i class="fa-solid fa-sliders text-danger me-2"></i>Filter & Pengaturan Matriks Heatmap
                        </h5>
                        <p class="text-secondary small mb-0">Sesuaikan wilayah Kelurahan, RW, Partai Politik, serta opsi kalkulasi perolehan suara TPS.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-success fw-bold shadow-sm" id="btnExportMatrixCsv">
                            <i class="fa-solid fa-file-excel me-1"></i>Ekspor CSV / Excel
                        </button>
                    </div>
                </div>

                <!-- BARIS 1: HIERARKI WILAYAH & TAHUN PEMILU -->
                <div class="row g-3 align-items-end mb-3">
                    <!-- 1. Tingkat Pemilu -->
                    <div class="col-12 col-md-4 col-lg-3">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-layer-group text-danger me-1"></i>Tingkat Pemilu:
                        </label>
                        <select id="matrixTingkatSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-bold" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <?php foreach ($tingkatOptions as $k => $label): ?>
                                <option value="<?= $k ?>" <?= $selectedTingkat == $k ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 2. Tahun Pemilu -->
                    <div class="col-6 col-sm-6 col-md-2 col-lg-2">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-calendar-days text-gold me-1"></i>Tahun:
                        </label>
                        <select id="matrixYearSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-bold" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <?php foreach ($years as $yr): ?>
                                <option value="<?= $yr ?>" <?= $selectedYear == $yr ? 'selected' : '' ?>><?= $yr ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 3. Kota / Kab -->
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-city text-gold me-1"></i>Kota / Kab:
                        </label>
                        <select id="matrixRegencySelect" class="form-select border-0 shadow-sm text-dark-coffee fw-semibold" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <?php foreach ($regencies as $reg): ?>
                                <option value="<?= $reg['id'] ?>" <?= $selectedRegency == $reg['id'] ? 'selected' : '' ?>>
                                    <?= esc($reg['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 4. Kecamatan -->
                    <div class="col-6 col-sm-6 col-md-3 col-lg-2">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-map-location-dot text-gold me-1"></i>Kecamatan:
                        </label>
                        <select id="matrixDistrictSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-semibold" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <?php foreach ($districts as $d): ?>
                                <option value="<?= $d['id'] ?>" <?= $selectedDistrict == $d['id'] ? 'selected' : '' ?>>
                                    Kec. <?= esc($d['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 5. Kelurahan -->
                    <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-building-flag text-danger me-1"></i>Kelurahan: <span class="text-danger">*</span>
                        </label>
                        <select id="matrixVillageSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-bold" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <?php foreach ($villages as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= $selectedVillage == $v['id'] ? 'selected' : '' ?>>
                                    Kel. <?= esc($v['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- BARIS 2: FILTER RW, PARTAI & CALEG -->
                <div class="row g-3 align-items-end">
                    <!-- 5. Filter RW -->
                    <div class="col-12 col-sm-4 col-md-2 col-lg-2">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-map-pin text-gold me-1"></i>Filter RW:
                        </label>
                        <select id="matrixRwSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <option value="all">-- Semua RW --</option>
                        </select>
                    </div>

                    <!-- 6. Filter Partai Politik -->
                    <div class="col-12 col-sm-8 col-md-5 col-lg-5">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-flag text-gold me-1"></i>Filter Partai:
                        </label>
                        <select id="matrixPartySelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px; background-color: #ffffff;">
                            <option value="all">-- Seluruh Partai Politik (18 Partai) --</option>
                            <?php foreach ($allPartiesWithCaleg as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= esc($p['no_urut']) ?>. <?= esc($p['nama_partai']) ?> (<?= $p['caleg_count'] ?> Caleg)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- 7. Filter Caleg -->
                    <div class="col-12 col-md-5 col-lg-5">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-user-tie text-danger me-1"></i>Filter Caleg:
                        </label>
                        <div class="input-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                            <select id="matrixCalegSelect" class="form-select border-0 text-dark-coffee fw-medium" style="height: 42px; background-color: #ffffff;">
                                <option value="all">-- Seluruh Caleg (Semua Partai) --</option>
                                <?php 
                                $curParty = null;
                                foreach ($allCalegList as $c): 
                                    if ($c['nama_partai'] !== $curParty):
                                        if ($curParty !== null) echo '</optgroup>';
                                        $curParty = $c['nama_partai'];
                                        echo '<optgroup label="' . esc($curParty) . '">';
                                    endif;
                                ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= $c['no_urut'] ?>. <?= esc($c['nama_caleg']) ?>
                                    </option>
                                <?php endforeach; 
                                if ($curParty !== null) echo '</optgroup>';
                                ?>
                            </select>
                            <button class="btn btn-white border-0 bg-white text-secondary px-3" type="button" id="btnResetFilters" title="Reset Filter ke Default">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Opsi Tampilan & Switchers -->
                <div class="d-flex flex-wrap align-items-center justify-content-between pt-3 mt-3 border-top border-secondary border-opacity-10 gap-3">
                    <div class="d-flex align-items-center gap-4 flex-wrap">
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="toggleShowPartyVotes" checked>
                            <label class="form-check-label small fw-semibold text-dark-coffee" for="toggleShowPartyVotes">
                                Tampilkan Baris Coblos Partai
                            </label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" id="toggleShowSubtotals" checked>
                            <label class="form-check-label small fw-semibold text-dark-coffee" for="toggleShowSubtotals">
                                Tampilkan Subtotal Per Partai
                            </label>
                        </div>
                    </div>

                    <!-- Legend Seaborn Reds -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="small fw-bold text-dark-coffee">Intensitas Suara:</span>
                        <div class="heatmap-legend-strip shadow-sm">
                            <div class="heatmap-legend-item" style="background-color: #ffffff; color: #6c757d;">0</div>
                            <div class="heatmap-legend-item" style="background-color: #fee5d9; color: #495057;">1-4</div>
                            <div class="heatmap-legend-item" style="background-color: #fcae91; color: #212529;">5-11</div>
                            <div class="heatmap-legend-item" style="background-color: #fb6a4a; color: #ffffff;">12-24</div>
                            <div class="heatmap-legend-item" style="background-color: #de2d26; color: #ffffff;">25-49</div>
                            <div class="heatmap-legend-item" style="background-color: #a50f15; color: #ffffff;">50-74</div>
                            <div class="heatmap-legend-item" style="background-color: #67000d; color: #ffffff;">75+</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- STATS STRIP FOR SELECTED VILLAGE -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #3e2723 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block">Kelurahan</span>
                            <h6 class="fw-bold text-dark-coffee mb-0" id="matrixVillageBadge">Kel. Depok Jaya</h6>
                        </div>
                        <i class="fa-solid fa-building-flag text-secondary fs-3 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #1976d2 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block">Jumlah Kolom TPS</span>
                            <h5 class="fw-bold text-primary mb-0" id="matrixTpsCountBadge">0 TPS</h5>
                        </div>
                        <i class="fa-solid fa-check-to-slot text-primary fs-3 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #2e7d32 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block">Total Suara Sah</span>
                            <h5 class="fw-bold text-success mb-0" id="matrixTotalVotesBadge">0</h5>
                        </div>
                        <i class="fa-solid fa-circle-check text-success fs-3 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #c62828 !important;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-muted small fw-bold d-block">Suara Tertinggi di TPS</span>
                            <h5 class="fw-bold text-danger mb-0" id="matrixMaxVoteBadge">0 suara</h5>
                        </div>
                        <i class="fa-solid fa-fire text-danger fs-3 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- MATRIX HEATMAP TABLE CONTAINER -->
        <div class="position-relative">
            <!-- Loading Indicator -->
            <div id="matrixLoading" class="position-absolute top-50 start-50 translate-middle bg-white bg-opacity-90 p-4 rounded-3 shadow text-center d-none" style="z-index: 100;">
                <div class="spinner-border text-danger mb-2" role="status"></div>
                <h6 class="fw-bold text-dark-coffee mb-0">Memuat Matriks Heatmap TPS...</h6>
                <small class="text-muted">Menghitung seluruh perolehan caleg & TPS</small>
            </div>

            <div class="heatmap-table-container">
                <table class="heatmap-table" id="heatmapMatrixTable">
                    <thead id="matrixTableHead">
                        <!-- Populated by JavaScript -->
                    </thead>
                    <tbody id="matrixTableBody">
                        <!-- Populated by JavaScript -->
                    </tbody>
                    <tfoot id="matrixTableFoot">
                        <!-- Populated by JavaScript -->
                    </tfoot>
                </table>
            </div>
        </div>
        <p class="text-muted small mt-2">
            <i class="fa-solid fa-circle-info text-primary me-1"></i> Kolom pertama dan header atas terkunci (sticky). <strong>Klik salah satu sel suara (misal caleg di nomor TPS tertentu) atau header kolom TPS</strong> untuk membuka rincian breakdown TPS: suara terbesar di TPS, urutan peringkat caleg, klasemen partai, serta leaderboard lengkap.
        </p>

    </div>

    <!-- ============================================================== -->
    <!-- TAB 2: PETA SPASIAL KELURAHAN (MAPLIBRE GL JS INTERAKTIF)      -->
    <!-- ============================================================== -->
    <div class="tab-pane fade" id="tab-spatial" role="tabpanel" aria-labelledby="tab-spatial-btn">
        
        <!-- HEATMAP CONTROLS CARD -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: linear-gradient(135deg, #fcfbf9, #f7f3f0); border: 1px solid #ebdcd5 !important;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 pb-3 border-bottom border-secondary border-opacity-10 gap-2">
                    <div>
                        <h5 class="fw-bold text-dark-coffee mb-1">
                            <i class="fa-solid fa-fire text-danger me-2"></i>Filter Peta Heatmap Sebaran Suara Caleg
                        </h5>
                        <p class="text-secondary small mb-0">Analisis visual sebaran intensitas suara caleg dan partai di seluruh kelurahan dan RW se-Kecamatan Pancoran Mas.</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold" id="btnFitPancoranMas">
                            <i class="fa-solid fa-compress me-1"></i>Fokus Pancoran Mas
                        </button>
                    </div>
                </div>

                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-layer-group me-1 text-danger"></i>Tingkat Pemilu:
                        </label>
                        <select id="spatialTingkatSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-bold" style="border-radius: 8px; height: 42px;">
                            <?php foreach ($tingkatOptions as $k => $label): ?>
                                <option value="<?= $k ?>" <?= $selectedTingkat == $k ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-flag me-1 text-gold"></i>Pilih Partai Politik:
                        </label>
                        <select id="heatmapPartaiSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                            <option value="all">-- Seluruh Partai Politik (18 Partai) --</option>
                            <?php foreach ($allPartiesWithCaleg as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= esc($p['no_urut']) ?>. <?= esc($p['nama_partai']) ?> (<?= $p['caleg_count'] ?> Caleg)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-user-tie me-1 text-gold"></i>Pilih Caleg / Suara:
                        </label>
                        <select id="heatmapCalegSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                            <option value="all">-- Seluruh Caleg & Semua Partai --</option>
                            <?php 
                            $curParty = null;
                            foreach ($allCalegList as $c): 
                                if ($c['nama_partai'] !== $curParty):
                                    if ($curParty !== null) echo '</optgroup>';
                                    $curParty = $c['nama_partai'];
                                    echo '<optgroup label="' . esc($curParty) . '">';
                                endif;
                            ?>
                                <option value="<?= $c['id'] ?>">
                                    <?= $c['no_urut'] ?>. <?= esc($c['nama_caleg']) ?>
                                </option>
                            <?php endforeach; 
                            if ($curParty !== null) echo '</optgroup>';
                            ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark-coffee small mb-1">
                            <i class="fa-solid fa-palette me-1 text-gold"></i>Palet Warna:
                        </label>
                        <select id="heatmapPaletteSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                            <option value="orange-red">Oranye - Merah Panas</option>
                            <option value="emerald">Hijau Pemenangan (PKS)</option>
                            <option value="blue">Biru Elektoral</option>
                            <option value="purple">Ungu - Magenta</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- CALEG SPOTLIGHT BANNER -->
        <div class="card border-0 shadow-sm mb-4 text-white overflow-hidden position-relative" id="calegBannerCard" style="background: linear-gradient(135deg, #3e2723, #5d4037); border-radius: 14px;">
            <div class="card-body p-4 position-relative">
                <div class="row align-items-center g-3">
                    <div class="col-md-7 d-flex align-items-center gap-3">
                        <div class="bg-white p-2 rounded shadow-sm" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <img id="bannerPartyLogo" src="<?= base_url('uploads/partai/pks.png') ?>" alt="Logo" style="max-height: 100%; max-width: 100%; object-fit: contain;" onerror="this.src='https://placehold.co/56?text=LOGO';">
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <span class="badge bg-gold text-dark fw-bold px-2 py-1" id="bannerNoBadge">No. 1</span>
                                <span class="badge bg-white bg-opacity-25 text-white fw-medium" id="bannerPartyName">Partai Keadilan Sejahtera (PKS)</span>
                                <span class="badge bg-light text-dark fw-semibold" id="bannerDistrictName">Kec. Pancoran Mas</span>
                            </div>
                            <h4 class="fw-bold text-white mb-0" id="bannerCalegName">H. MOH. HAFID NASIR, Dipl.Inf.</h4>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="row g-2 text-center text-md-end">
                            <div class="col-6">
                                <span class="small opacity-75 d-block">Total Suara Kecamatan</span>
                                <h3 class="fw-bold text-warning mb-0" id="bannerTotalSuara">0</h3>
                                <small class="text-white-50" style="font-size: 0.72rem;">Suara Terhitung</small>
                            </div>
                            <div class="col-6 border-start border-white border-opacity-25 ps-3 text-start">
                                <span class="small opacity-75 d-block">Lumbung Suara Utama</span>
                                <h5 class="fw-bold text-warning mb-0" id="bannerTopVillage">-</h5>
                                <small class="text-white-50" id="bannerTopVillageStats" style="font-size: 0.72rem;">-</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FULL-WIDTH HEATMAP MAP CARD -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
            <div class="card-header p-3 px-4 bg-transparent border-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-dark-coffee mb-0">
                        <i class="fa-solid fa-map-marked-alt text-danger me-2"></i>Peta Spasial Sebaran Suara (Pancoran Mas)
                    </h6>
                    <small class="text-muted">Klik pada wilayah kelurahan untuk melihat detail breakdown suara per RW.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary fw-semibold" onclick="document.getElementById('btnFitPancoranMas')?.click()">
                        <i class="fa-solid fa-compress me-1"></i>Fokus Pancoran Mas
                    </button>
                    <div class="form-check form-switch mb-0 ms-2">
                        <input class="form-check-input" type="checkbox" id="toggleMapLabels" checked>
                        <label class="form-check-label small fw-semibold text-dark-coffee" for="toggleMapLabels">Label Suara</label>
                    </div>
                </div>
            </div>
            <div class="card-body p-3 pt-0 position-relative">
                <div id="mapLoadingOverlay" class="position-absolute top-50 start-50 translate-middle bg-white bg-opacity-75 p-3 rounded-3 shadow text-center d-none" style="z-index: 1000;">
                    <div class="spinner-border text-danger spinner-border-sm me-2" role="status"></div>
                    <span class="small fw-bold text-dark-coffee">Memperbarui Heatmap...</span>
                </div>
                <div id="electionHeatMap"></div>
            </div>
        </div>

        <!-- BREAKDOWN CARDS BELOW FULL-WIDTH MAP (2 COLUMNS) -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header p-3 px-4 bg-transparent border-0 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark-coffee mb-0">
                                <i class="fa-solid fa-ranking-star text-gold me-2"></i>Peringkat Kelurahan
                            </h6>
                            <span class="badge bg-light text-muted small" id="totalVillagesBadge">6 Kelurahan</span>
                        </div>
                        <small class="text-muted">Diurutkan dari lumbung suara tertinggi</small>
                    </div>
                    <div class="card-body p-3 pt-0">
                        <div class="list-group list-group-flush" id="villageRankingList">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 14px;">
                    <div class="card-header p-3 px-4 bg-transparent border-0 pb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark-coffee mb-0">
                                <i class="fa-solid fa-trophy text-warning me-2"></i>Top 5 RW Terkuat
                            </h6>
                            <span class="badge bg-warning text-dark small">Basis Suara</span>
                        </div>
                        <small class="text-muted">RW dengan perolehan tertinggi se-Pancoran Mas</small>
                    </div>
                    <div class="card-body p-3 pt-0">
                        <div class="list-group list-group-flush" id="topRwsList">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: DETAIL BREAKDOWN SEBARAN SUARA PER RW (DARI PETA)        -->
<!-- ============================================================== -->
<div class="modal fade" id="rwBreakdownModal" tabindex="-1" aria-labelledby="rwBreakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0" style="background-color: #3e2723; color: #fff8e1; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="rwBreakdownModalLabel">
                        <i class="fa-solid fa-sitemap text-gold me-2"></i>Detail Sebaran Suara RW — <span id="modalVillageName" class="text-warning">Kel. Depok Jaya</span>
                    </h5>
                    <p class="small text-white-50 mb-3" id="modalCalegSub">Caleg: H. MOH. HAFID NASIR, Dipl.Inf. (PKS)</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted d-block">Total Suara Kelurahan</small>
                            <h4 class="fw-bold text-dark-coffee mb-0" id="modalTotalSuara">0</h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted d-block">Jumlah RW</small>
                            <h4 class="fw-bold text-primary mb-0" id="modalTotalRw">0 RW</h4>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted d-block">Rata-rata Suara / RW</small>
                            <h4 class="fw-bold text-success mb-0" id="modalAvgRw">0</h4>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-bold text-dark-coffee mb-3">
                        <i class="fa-solid fa-chart-simple text-danger me-2"></i>Grafik Distribusi Suara per RW
                    </h6>
                    <div style="height: 220px;">
                        <canvas id="rwVotesBarChart"></canvas>
                    </div>
                </div>

                <h6 class="fw-bold text-dark-coffee mb-2">
                    <i class="fa-solid fa-table-list text-gold me-2"></i>Tabel Rincian Suara Seluruh RW
                </h6>
                <div class="table-responsive" style="max-height: 260px;">
                    <table class="table table-sm table-hover align-middle mb-0" id="modalRwTable">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3" style="width: 80px;">RW</th>
                                <th class="text-center" style="width: 100px;">Jumlah TPS</th>
                                <th class="text-end" style="width: 120px;">Jumlah Suara</th>
                                <th class="pe-3" style="width: 180px;">Kontribusi</th>
                            </tr>
                        </thead>
                        <tbody id="modalRwTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: DETAIL BREAKDOWN TPS & POSISI CALEG (DARI MATRIKS)       -->
<!-- ============================================================== -->
<div class="modal fade" id="tpsBreakdownModal" tabindex="-1" aria-labelledby="tpsBreakdownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 pb-3" style="background: linear-gradient(135deg, #271410 0%, #3e2723 100%); color: #fff8e1;">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(212, 175, 55, 0.2);">
                        <i class="fa-solid fa-person-booth fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h4 class="modal-title fw-bold mb-0 text-white" id="tpsModalTitle">Rincian TPS</h4>
                            <span class="badge bg-warning text-dark fw-bold px-2 py-1" id="tpsModalBadgeRw">RW -</span>
                            <span class="badge bg-light text-dark fw-bold px-2 py-1" id="tpsModalBadgeKel">Kelurahan -</span>
                        </div>
                        <p class="small text-white-50 mb-0 mt-1" id="tpsModalSubtitle">
                            Kecamatan Pancoran Mas • Kota Depok • Pemilu Tahun 2024
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4" style="background-color: #fcfaf8;">
                <!-- Loading State inside Modal -->
                <div id="tpsModalLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-danger mb-3" style="width: 3rem; height: 3rem;" role="status"></div>
                    <h5 class="fw-bold text-dark-coffee">Memuat Data Rincian TPS...</h5>
                    <p class="text-muted small">Menghitung peringkat caleg, suara terbesar, dan klasemen partai di TPS ini</p>
                </div>

                <div id="tpsModalContent">
                    <!-- 1. HERO CARD: CALEG YANG DIKLIK -->
                    <div id="tpsSelectedCalegSection" class="card border-0 shadow-sm mb-4" style="border-radius: 14px; background: #ffffff; border-left: 6px solid #d4af37 !important;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2 bg-light rounded-3 border text-center d-flex align-items-center justify-content-center" style="width: 58px; height: 58px; min-width: 58px;">
                                        <img id="tpsCalegPartyLogo" src="" alt="Partai" class="img-fluid" style="max-height: 44px; max-width: 44px; object-fit: contain;">
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="badge bg-dark-coffee text-white fw-bold" id="tpsCalegPartyNumber">No. -</span>
                                            <h5 class="fw-bold text-dark-coffee mb-0" id="tpsCalegName">-</h5>
                                        </div>
                                        <div class="text-muted small mt-1" id="tpsCalegPartyName">-</div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3 flex-wrap ms-auto">
                                    <!-- Suara Caleg -->
                                    <div class="text-end">
                                        <span class="text-muted small d-block">Perolehan di TPS ini:</span>
                                        <div class="fw-bolder fs-4 text-success" id="tpsCalegVoteDisplay">0 suara</div>
                                        <small class="text-muted" id="tpsCalegPctDisplay">(0.0% dari suara sah)</small>
                                    </div>
                                    <!-- Rank Overall Badge -->
                                    <div class="p-2 px-3 rounded-3 shadow-sm text-center" style="background-color: #3e2723; color: #fff8e1; min-width: 140px;">
                                        <small class="d-block text-white-50" style="font-size: 10px; letter-spacing: 0.5px;">PERINGKAT DI TPS</small>
                                        <span class="fw-bold fs-5 text-warning" id="tpsCalegRankDisplay">Urutan #-</span>
                                    </div>
                                    <!-- Rank In Party Badge -->
                                    <div class="p-2 px-3 rounded-3 shadow-sm bg-white border text-center" style="min-width: 130px;">
                                        <small class="d-block text-muted" style="font-size: 10px; letter-spacing: 0.5px;">INTERNAL PARTAI</small>
                                        <span class="fw-bold fs-5 text-primary" id="tpsCalegPartyRankDisplay">Juara #-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. SUMMARY 4 STAT CARDS -->
                    <div class="row g-3 mb-4">
                        <!-- Top Caleg Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #c62828 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-bold">Suara Terbesar (Caleg)</span>
                                    <i class="fa-solid fa-crown text-warning fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-dark-coffee text-truncate mb-1" id="tpsCardTopCalegName">-</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger text-white fw-bold" id="tpsCardTopCalegVotes">0 suara</span>
                                    <span class="small text-muted text-truncate" id="tpsCardTopCalegParty">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Caleg Rank Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #1976d2 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-bold">Posisi Caleg Terpilih</span>
                                    <i class="fa-solid fa-ranking-star text-primary fs-5"></i>
                                </div>
                                <h4 class="fw-bold text-primary mb-1" id="tpsCardCalegRank">#-</h4>
                                <span class="small text-muted" id="tpsCardCalegRankDesc">-</span>
                            </div>
                        </div>

                        <!-- Top Party Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #f57c00 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-bold">Partai Terbanyak</span>
                                    <i class="fa-solid fa-trophy text-gold fs-5"></i>
                                </div>
                                <h6 class="fw-bold text-dark-coffee text-truncate mb-1" id="tpsCardTopPartyName">-</h6>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-warning-subtle text-dark fw-bold" id="tpsCardTopPartyVotes">0 suara</span>
                                    <span class="small text-muted" id="tpsCardTopPartyPct">0%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Turnout / Total Suara Card -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card border-0 shadow-sm h-100 p-3 bg-white" style="border-radius: 12px; border-left: 4px solid #2e7d32 !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="text-muted small fw-bold">Total Suara Sah & DPT</span>
                                    <i class="fa-solid fa-check-to-slot text-success fs-5"></i>
                                </div>
                                <h5 class="fw-bold text-success mb-1" id="tpsCardTotalVotes">0 suara</h5>
                                <span class="small text-muted" id="tpsCardTurnout">DPT: 0 • Partisipasi: 0%</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. RW CONTEXT STRIP: ANGGOTA & KEGIATAN LAPANGAN DI RW INI -->
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%); border-left: 5px solid #0288d1 !important;">
                        <div class="card-body p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary" style="width: 44px; height: 44px; min-width: 44px;">
                                        <i class="fa-solid fa-map-location-dot fs-5"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold text-dark-coffee mb-0">Basis Wilayah <span id="tpsRwContextTitle">RW - Kelurahan -</span></h6>
                                        <small class="text-muted">Data terhubung dengan catatan kegiatan lapangan & direktori anggota partai</small>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-1 fw-bold" id="btnQuickTabKegiatan" title="Lihat rincian kegiatan di RW ini">
                                        <i class="fa-solid fa-calendar-check me-1"></i> <span id="tpsRwKegiatanBadge">0 Kegiatan</span>
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 fw-bold" id="btnQuickTabAnggota" title="Lihat rincian anggota di RW ini">
                                        <i class="fa-solid fa-users me-1"></i> <span id="tpsRwAnggotaBadge">0 Anggota</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. TABS: LEADERBOARD CALEG, KLASEMEN PARTAI, KEGIATAN RW, & ANGGOTA RW -->
                    <div class="card border-0 shadow-sm bg-white" style="border-radius: 14px;">
                        <div class="card-header bg-white border-bottom p-3">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <ul class="nav nav-pills" id="tpsModalTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fw-bold px-3 py-2" id="tab-leaderboard-btn" data-bs-toggle="pill" data-bs-target="#tab-leaderboard" type="button" role="tab">
                                            <i class="fa-solid fa-list-ol me-2"></i>Leaderboard Caleg (<span id="tpsLeaderboardCount">0</span>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-bold px-3 py-2" id="tab-partai-btn" data-bs-toggle="pill" data-bs-target="#tab-partai" type="button" role="tab">
                                            <i class="fa-solid fa-landmark me-2"></i>Klasemen Suara Partai (<span id="tpsPartaiCount">0</span>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-bold px-3 py-2" id="tab-kegiatan-btn" data-bs-toggle="pill" data-bs-target="#tab-kegiatan" type="button" role="tab">
                                            <i class="fa-solid fa-calendar-check me-2 text-success"></i>Kegiatan di RW (<span id="tpsKegiatanCount">0</span>)
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fw-bold px-3 py-2" id="tab-anggota-btn" data-bs-toggle="pill" data-bs-target="#tab-anggota" type="button" role="tab">
                                            <i class="fa-solid fa-users me-2 text-primary"></i>Anggota di RW (<span id="tpsAnggotaCount">0</span>)
                                        </button>
                                    </li>
                                </ul>

                                <!-- Search in modal table -->
                                <div class="d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm" style="width: 270px;">
                                        <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                                        <input type="text" id="tpsModalSearchInput" class="form-control bg-light border-0" placeholder="Cari caleg, partai, kegiatan, anggota...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="tab-content" id="tpsModalTabContent">
                                <!-- TAB 1: LEADERBOARD CALEG -->
                                <div class="tab-pane fade show active" id="tab-leaderboard" role="tabpanel">
                                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;" id="tpsLeaderboardContainer">
                                        <table class="table table-hover align-middle mb-0" id="tpsLeaderboardTable">
                                            <thead class="table-light sticky-top" style="z-index: 5;">
                                                <tr>
                                                    <th class="text-center" style="width: 60px;">#</th>
                                                    <th style="min-width: 240px;">Nama Caleg</th>
                                                    <th style="min-width: 180px;">Partai Politik</th>
                                                    <th class="text-end" style="width: 110px;">Suara TPS</th>
                                                    <th class="text-end" style="width: 100px;">% Suara</th>
                                                    <th style="min-width: 150px;">Proporsi Terhadap Suara Sah</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tpsLeaderboardTableBody">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 2: STANDINGS PARTAI -->
                                <div class="tab-pane fade" id="tab-partai" role="tabpanel">
                                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="tpsPartaiTable">
                                            <thead class="table-light sticky-top" style="z-index: 5;">
                                                <tr>
                                                    <th class="text-center" style="width: 60px;">#</th>
                                                    <th style="min-width: 220px;">Partai Politik</th>
                                                    <th class="text-end" style="width: 120px;">Coblos Logo</th>
                                                    <th class="text-end" style="width: 120px;">Suara Caleg</th>
                                                    <th class="text-end" style="width: 130px;">Total Suara</th>
                                                    <th class="text-end" style="width: 100px;">% Suara</th>
                                                    <th style="min-width: 150px;">Proporsi Terhadap Suara Sah</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tpsPartaiTableBody">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 3: KEGIATAN DI RW -->
                                <div class="tab-pane fade" id="tab-kegiatan" role="tabpanel">
                                    <div class="p-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div>
                                            <span class="small fw-bold text-dark-coffee d-block">Catatan Kegiatan & Sisir RW di <span id="tpsKegiatanRwLabel">RW -</span></span>
                                            <small class="text-muted" id="tpsKegiatanSummaryText">Total 0 kegiatan dengan 0 partisipan</small>
                                        </div>
                                        <a href="<?= base_url('admin/pemilu/sisir-rw') ?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Modul Sisir RW
                                        </a>
                                    </div>
                                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="tpsKegiatanTable">
                                            <thead class="table-light sticky-top" style="z-index: 5;">
                                                <tr>
                                                    <th style="width: 110px;">Tanggal</th>
                                                    <th style="min-width: 220px;">Nama Kegiatan</th>
                                                    <th style="min-width: 140px;">Bentuk / Kategori</th>
                                                    <th style="min-width: 160px;">Lokasi / RT</th>
                                                    <th class="text-center" style="width: 90px;">Peserta</th>
                                                    <th style="min-width: 180px;">Tokoh Hadir</th>
                                                    <th class="text-center" style="width: 100px;">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tpsKegiatanTableBody">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- TAB 4: ANGGOTA DI RW -->
                                <div class="tab-pane fade" id="tab-anggota" role="tabpanel">
                                    <div class="p-3 bg-light border-bottom d-flex flex-wrap align-items-center justify-content-between gap-2">
                                        <div>
                                            <span class="small fw-bold text-dark-coffee d-block">Daftar Anggota / Kader di <span id="tpsAnggotaRwLabel">RW -</span></span>
                                            <small class="text-muted" id="tpsAnggotaSummaryText">Total 0 anggota (0 Laki-laki, 0 Perempuan)</small>
                                        </div>
                                        <a href="<?= base_url('admin/members') ?>" target="_blank" class="btn btn-outline-secondary btn-sm" style="border-radius: 8px;">
                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Buka Direktori Anggota
                                        </a>
                                    </div>
                                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                                        <table class="table table-hover align-middle mb-0" id="tpsAnggotaTable">
                                            <thead class="table-light sticky-top" style="z-index: 5;">
                                                <tr>
                                                    <th class="text-center" style="width: 50px;">#</th>
                                                    <th style="min-width: 200px;">Nama Anggota</th>
                                                    <th class="text-center" style="width: 70px;">RT</th>
                                                    <th style="min-width: 120px;">Jenis Kelamin</th>
                                                    <th style="min-width: 140px;">Status Anggota</th>
                                                    <th style="min-width: 160px;">Pekerjaan</th>
                                                    <th style="min-width: 120px;">Pendidikan</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tpsAnggotaTableBody">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-3 bg-white" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <div class="me-auto text-muted small">
                    <i class="fa-solid fa-circle-info text-primary me-1"></i> Data dihitung dari formulir rekapitulasi C1 / Form Hasil Pemilu <?= esc($selectedTingkat == 'DPRD_PROV' ? 'DPRD Provinsi Jabar Dapil 8' : ($selectedTingkat == 'DPR_RI' ? 'DPR RI Dapil Jabar 6' : 'DPRD Kota Depok Dapil 1')) ?>
                </div>
                <button type="button" class="btn btn-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- MapLibre GL JS (100% Free & Open-Source) -->
<script src="https://unpkg.com/maplibre-gl@4.7.1/dist/maplibre-gl.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const defaultYear = "<?= $selectedYear ?>";
    const defaultTingkat = "<?= $selectedTingkat ?>";
    const defaultDistrict = "<?= $selectedDistrict ?>";
    const defaultVillage = "<?= $selectedVillage ?>";

    // =========================================================
    // PART A: MATRIX HEATMAP TABLE (8 FILTERS)
    // 1. Tingkat Pemilu | 2. Tahun Pemilu | 3. Kota | 4. Kecamatan 
    // 5. Kelurahan | 6. RW | 7. Partai | 8. Caleg
    // =========================================================
    const matrixTingkatSelect = document.getElementById('matrixTingkatSelect');
    const spatialTingkatSelect = document.getElementById('spatialTingkatSelect');
    const matrixYearSelect = document.getElementById('matrixYearSelect');
    const matrixRegencySelect = document.getElementById('matrixRegencySelect');
    const matrixDistrictSelect = document.getElementById('matrixDistrictSelect');
    const matrixVillageSelect = document.getElementById('matrixVillageSelect');
    const matrixRwSelect = document.getElementById('matrixRwSelect');
    const matrixPartySelect = document.getElementById('matrixPartySelect');
    const matrixCalegSelect = document.getElementById('matrixCalegSelect');
    const btnResetFilters = document.getElementById('btnResetFilters');
    const toggleShowPartyVotes = document.getElementById('toggleShowPartyVotes');
    const toggleShowSubtotals = document.getElementById('toggleShowSubtotals');
    const btnExportMatrixCsv = document.getElementById('btnExportMatrixCsv');

    const matrixTableHead = document.getElementById('matrixTableHead');
    const matrixTableBody = document.getElementById('matrixTableBody');
    const matrixTableFoot = document.getElementById('matrixTableFoot');
    const matrixLoading = document.getElementById('matrixLoading');

    let currentMatrixData = null;

    // Seaborn Reds Heatmap Palette calculation
    function getSeabornHeatStyle(val, maxVal) {
        if (!val || val === 0 || isNaN(val)) {
            return {
                bg: '#ffffff',
                color: '#adb5bd',
                bold: false
            };
        }
        if (!maxVal || maxVal <= 0) maxVal = 1;
        const ratio = val / maxVal;

        // Gradient from soft peach to deep maroon
        if (ratio >= 0.75) {
            return { bg: '#67000d', color: '#ffffff', bold: true };
        } else if (ratio >= 0.50) {
            return { bg: '#a50f15', color: '#ffffff', bold: true };
        } else if (ratio >= 0.30) {
            return { bg: '#de2d26', color: '#ffffff', bold: true };
        } else if (ratio >= 0.18) {
            return { bg: '#fb6a4a', color: '#ffffff', bold: true };
        } else if (ratio >= 0.08) {
            return { bg: '#fcae91', color: '#212529', bold: true };
        } else {
            return { bg: '#fee5d9', color: '#495057', bold: false };
        }
    }

    function loadTpsHeatmapMatrix() {
        const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
        const tingkat = matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat;
        const villageId = matrixVillageSelect ? matrixVillageSelect.value : defaultVillage;
        const partyId = matrixPartySelect ? matrixPartySelect.value : 'all';
        const rw = matrixRwSelect ? matrixRwSelect.value : 'all';

        if (matrixLoading) matrixLoading.classList.remove('d-none');

        const url = `<?= base_url('admin/pemilu/heatmap/get_matrix') ?>?village_id=${villageId}&partai_id=${partyId}&rw=${rw}&tahun=${year}&tingkat_pemilu=${tingkat}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (matrixLoading) matrixLoading.classList.add('d-none');
                if (data.status === 'success') {
                    currentMatrixData = data;
                    updateRwFilterDropdown(data.rw_list, rw);
                    renderMatrixTable(data);
                } else {
                    console.error('API Error:', data);
                }
            })
            .catch(err => {
                if (matrixLoading) matrixLoading.classList.add('d-none');
                console.error('Error fetching heatmap matrix:', err);
            });
    }

    function updateRwFilterDropdown(rwList, currentSelectedRw) {
        if (!matrixRwSelect) return;
        const prev = currentSelectedRw || matrixRwSelect.value || 'all';
        let html = '<option value="all">-- Semua RW --</option>';
        if (Array.isArray(rwList)) {
            rwList.forEach(rw => {
                if (rw) {
                    const sel = (rw === prev) ? 'selected' : '';
                    html += `<option value="${rw}" ${sel}>RW ${rw}</option>`;
                }
            });
        }
        matrixRwSelect.innerHTML = html;
    }

    function updateCalegDropdown(partyId) {
        if (!matrixCalegSelect) return;
        const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
        const tingkat = matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat;
        const url = `<?= base_url('admin/pemilu/heatmap/get_caleg_by_party') ?>?partai_id=${partyId}&tahun=${year}&tingkat_pemilu=${tingkat}`;

        fetch(url)
            .then(res => res.json())
            .then(calegs => {
                let html = (partyId === 'all') 
                    ? '<option value="all">-- Seluruh Caleg (Semua Partai) --</option>' 
                    : '<option value="all">-- Seluruh Caleg Partai Ini --</option>';

                if (Array.isArray(calegs)) {
                    if (partyId === 'all') {
                        let curParty = '';
                        let groupOpen = false;
                        calegs.forEach(c => {
                            if (c.nama_partai && c.nama_partai !== curParty) {
                                if (groupOpen) html += '</optgroup>';
                                curParty = c.nama_partai;
                                html += `<optgroup label="${curParty}">`;
                                groupOpen = true;
                            }
                            const noUrut = c.no_urut ? `${c.no_urut}. ` : '';
                            html += `<option value="${c.id}">${noUrut}${c.nama_caleg}</option>`;
                        });
                        if (groupOpen) html += '</optgroup>';
                    } else {
                        calegs.forEach(c => {
                            const noUrut = c.no_urut ? `${c.no_urut}. ` : '';
                            html += `<option value="${c.id}">${noUrut}${c.nama_caleg}</option>`;
                        });
                    }
                }
                matrixCalegSelect.innerHTML = html;
                matrixCalegSelect.value = 'all';
            })
            .catch(err => {
                console.error('Error fetching calegs:', err);
            });
    }

    function loadVillagesForDistrict(districtId) {
        fetch(`<?= base_url('admin/pemilu/heatmap/get_villages') ?>?district_id=${districtId}`)
            .then(res => res.json())
            .then(villages => {
                let vHtml = '';
                villages.forEach(v => {
                    vHtml += `<option value="${v.id}">Kel. ${v.name}</option>`;
                });
                if (matrixVillageSelect) {
                    matrixVillageSelect.innerHTML = vHtml;
                    if (villages.length > 0) {
                        matrixVillageSelect.value = villages[0].id;
                    }
                }
                if (matrixRwSelect) matrixRwSelect.value = 'all';
                loadTpsHeatmapMatrix();
            })
            .catch(err => console.error('Error fetching villages:', err));
    }

    function renderMatrixTable(data) {
        const village = data.village || {};
        const tpsList = data.tps_list || [];
        const parties = data.parties || [];
        const maxVote = data.max_vote || 1;
        const totalAllVotes = data.total_all_votes || 0;
        const tpsColTotals = data.tps_col_totals || {};

        // Update Stats Bar
        const vBadge = document.getElementById('matrixVillageBadge');
        if (vBadge) vBadge.innerText = 'Kel. ' + (village.name || '-');
        const tCount = document.getElementById('matrixTpsCountBadge');
        if (tCount) tCount.innerText = tpsList.length + ' TPS';
        const totBadge = document.getElementById('matrixTotalVotesBadge');
        if (totBadge) totBadge.innerText = totalAllVotes.toLocaleString() + ' suara';
        const maxBadge = document.getElementById('matrixMaxVoteBadge');
        if (maxBadge) maxBadge.innerText = maxVote.toLocaleString() + ' suara';

        const showPartyVotes = toggleShowPartyVotes ? toggleShowPartyVotes.checked : true;
        const showSubtotals = toggleShowSubtotals ? toggleShowSubtotals.checked : true;
        const selectedCalegId = matrixCalegSelect ? matrixCalegSelect.value : 'all';

        // Check if no TPS found for this kelurahan
        if (tpsList.length === 0) {
            matrixTableHead.innerHTML = `<tr><th class="text-center py-3">DATA TPS BELUM TERSEDIA</th></tr>`;
            matrixTableBody.innerHTML = `<tr>
                <td class="text-center py-5 text-muted">
                    <i class="fa-solid fa-folder-open fa-3x mb-3 text-secondary opacity-50 d-block"></i>
                    Belum ada data TPS atau hasil pemilu untuk Kelurahan ini.
                </td>
            </tr>`;
            matrixTableFoot.innerHTML = '';
            return;
        }

        // 1. BUILD THEAD
        let theadHtml = '<tr>';
        theadHtml += '<th class="sticky-col" style="min-width: 280px; text-align: left;">PARTAI POLITIK / NAMA CALEG</th>';
        tpsList.forEach(t => {
            const rwInfo = t.rw ? `RW ${t.rw}` : '';
            const rtInfo = t.rt ? `RT ${t.rt}` : '';
            const sub = [rwInfo, rtInfo].filter(Boolean).join(' • ');
            theadHtml += `<th class="tps-header-clickable" data-tps-id="${t.id}" title="${t.tps} - ${sub}&#10;Klik untuk melihat rincian breakdown TPS...">
                <div class="fw-bold">${t.tps}</div>
                <div style="font-size: 9.5px; opacity: 0.8; font-weight: normal;">${rwInfo || '-'}</div>
            </th>`;
        });
        theadHtml += '<th class="total-col sticky-top" style="text-align: end; background-color: #271410; color: #d4af37;">TOTAL</th>';
        theadHtml += '</tr>';
        matrixTableHead.innerHTML = theadHtml;

        // 2. BUILD TBODY
        let tbodyHtml = '';
        let visibleRowsCount = 0;

        parties.forEach(p => {
            const pId = p.id;
            const pName = p.nama_partai;
            const pNo = p.no_urut;
            const pLogo = p.lambang_partai ? `<?= base_url('uploads/partai/') ?>/${p.lambang_partai}` : '';
            const calegs = p.calegs || [];
            const pVotes = p.suara_partai_tps || {};

            // Filter calegs by selectedCalegId
            let matchedCalegs = calegs;
            if (selectedCalegId && selectedCalegId !== 'all') {
                matchedCalegs = calegs.filter(c => String(c.id) === String(selectedCalegId));
                if (matchedCalegs.length === 0) {
                    return; // Skip party if this party does not contain the selected caleg
                }
            }

            // A. PARTY HEADER ROW
            tbodyHtml += `<tr class="party-header-row">`;
            tbodyHtml += `<td class="sticky-col">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark-coffee text-white fw-bold px-2 py-1" style="font-size: 10px;">${pNo}</span>
                    ${pLogo ? `<img src="${pLogo}" alt="Logo" class="rounded" style="width: 20px; height: 20px; object-fit: contain;">` : ''}
                    <strong class="text-dark-coffee">${pName}</strong>
                </div>
            </td>`;
            tpsList.forEach(t => {
                tbodyHtml += `<td class="text-center text-muted" style="background-color: #f7ede2;">-</td>`;
            });
            tbodyHtml += `<td class="total-col" style="background-color: #f4e4cf; color: #3e2723;">
                ${(p.total_suara_gabungan || 0).toLocaleString()}
            </td>`;
            tbodyHtml += `</tr>`;
            visibleRowsCount++;

            // B. COBLOS PARTAI ROW (Optional toggle)
            if (showPartyVotes) {
                tbodyHtml += `<tr class="party-coblos-row" style="background-color: #fcfcfc;">`;
                tbodyHtml += `<td class="sticky-col ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-stamp text-muted fs-7"></i>
                        <span class="fst-italic text-secondary">Coblos Tanda Gambar Partai</span>
                    </div>
                </td>`;
                let partyRowTotal = 0;
                tpsList.forEach(t => {
                    const vote = pVotes[t.id] || 0;
                    partyRowTotal += vote;
                    const heat = getSeabornHeatStyle(vote, maxVote);
                    tbodyHtml += `<td class="heat-cell cell-breakdown-trigger" data-tps-id="${t.id}" data-partai-id="${pId}" data-caleg-id="" style="background-color: ${heat.bg}; color: ${heat.color}; font-weight: ${heat.bold ? '700' : 'normal'};"
                        title="${pName} (Coblos Partai)&#10;${t.tps} (${t.rw ? 'RW ' + t.rw : ''}): ${vote} suara&#10;Klik untuk rincian TPS...">
                        ${vote > 0 ? vote : '-'}
                    </td>`;
                });
                tbodyHtml += `<td class="total-col text-secondary fw-bold">${partyRowTotal.toLocaleString()}</td>`;
                tbodyHtml += `</tr>`;
                visibleRowsCount++;
            }

            // C. CALEGS ROWS
            matchedCalegs.forEach(c => {
                const cVotes = c.votes || {};
                tbodyHtml += `<tr class="caleg-row">`;
                tbodyHtml += `<td class="sticky-col ps-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-bold font-monospace" style="width: 22px; font-size: 11px;">${c.no_urut}.</span>
                        <span class="fw-medium text-dark-coffee">${c.nama_caleg}</span>
                    </div>
                </td>`;
                let calegTotal = 0;
                tpsList.forEach(t => {
                    const vote = cVotes[t.id] || 0;
                    calegTotal += vote;
                    const heat = getSeabornHeatStyle(vote, maxVote);
                    tbodyHtml += `<td class="heat-cell cell-breakdown-trigger" data-tps-id="${t.id}" data-caleg-id="${c.id}" data-partai-id="${pId}" style="background-color: ${heat.bg}; color: ${heat.color}; font-weight: ${heat.bold ? '700' : 'normal'};"
                        title="${c.nama_caleg} (${pName})&#10;${t.tps} (${t.rw ? 'RW ' + t.rw : ''}): ${vote} suara&#10;Klik untuk rincian TPS & peringkat caleg...">
                        ${vote > 0 ? vote : '-'}
                    </td>`;
                });
                tbodyHtml += `<td class="total-col text-dark-coffee fw-bold">${calegTotal.toLocaleString()}</td>`;
                tbodyHtml += `</tr>`;
                visibleRowsCount++;
            });

            // D. PARTY SUBTOTAL ROW (Optional toggle)
            if (showSubtotals) {
                tbodyHtml += `<tr class="party-subtotal-row">`;
                tbodyHtml += `<td class="sticky-col ps-3 text-dark-coffee fw-bold">
                    <i class="fa-solid fa-calculator me-1 text-gold"></i>Subtotal ${pName}
                </td>`;
                tpsList.forEach(t => {
                    let subtotalTps = (pVotes[t.id] || 0);
                    calegs.forEach(c => {
                        subtotalTps += ((c.votes && c.votes[t.id]) || 0);
                    });
                    const heat = getSeabornHeatStyle(subtotalTps, maxVote * 2);
                    tbodyHtml += `<td class="heat-cell cell-breakdown-trigger fw-bold" data-tps-id="${t.id}" data-partai-id="${pId}" data-caleg-id="" style="background-color: #f7ede2; color: #3e2723; border-top: 1px dashed #d7ccc8;"
                        title="Subtotal ${pName} di ${t.tps}: ${subtotalTps} suara&#10;Klik untuk rincian TPS...">
                        ${subtotalTps > 0 ? subtotalTps : '-'}
                    </td>`;
                });
                tbodyHtml += `<td class="total-col fw-bold text-dark-coffee" style="background-color: #fcefdc;">
                    ${(p.total_suara_gabungan || 0).toLocaleString()}
                </td>`;
                tbodyHtml += `</tr>`;
                visibleRowsCount++;
            }
        });

        if (visibleRowsCount === 0) {
            tbodyHtml = `<tr>
                <td colspan="${tpsList.length + 2}" class="text-center py-5 text-muted">
                    <i class="fa-solid fa-user-slash fa-2x mb-3 text-secondary opacity-50 d-block"></i>
                    Tidak ada data caleg yang sesuai dengan filter yang dipilih.
                </td>
            </tr>`;
        }

        matrixTableBody.innerHTML = tbodyHtml;

        // 3. BUILD TFOOT (TOTAL PER TPS)
        let tfootHtml = '<tr>';
        tfootHtml += '<td class="sticky-col">TOTAL SUARA SELURUH PARTAI</td>';
        let grandTotal = 0;
        tpsList.forEach(t => {
            const colTotal = tpsColTotals[t.id] || 0;
            grandTotal += colTotal;
            tfootHtml += `<td class="text-center tps-header-clickable fw-bold" data-tps-id="${t.id}" title="Total Suara di ${t.tps}: ${colTotal.toLocaleString()}&#10;Klik untuk rincian TPS...">${colTotal.toLocaleString()}</td>`;
        });
        tfootHtml += `<td class="total-col text-warning" style="background-color: #271410;">${grandTotal.toLocaleString()}</td>`;
        tfootHtml += '</tr>';
        matrixTableFoot.innerHTML = tfootHtml;
    }

    // Client-side & Server export CSV / Excel
    function exportMatrixToCsv() {
        const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
        const tingkat = matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat;
        const villageId = matrixVillageSelect ? matrixVillageSelect.value : defaultVillage;
        const partyId = matrixPartySelect ? matrixPartySelect.value : 'all';
        const rw = matrixRwSelect ? matrixRwSelect.value : 'all';

        const exportUrl = `<?= base_url('admin/pemilu/heatmap/export_matrix') ?>?village_id=${villageId}&partai_id=${partyId}&rw=${rw}&tahun=${year}&tingkat_pemilu=${tingkat}`;
        window.location.href = exportUrl;
    }

    // Event listeners
    // 0. Tingkat Pemilu (Matrix Tab & Spatial Tab)
    if (matrixTingkatSelect) {
        matrixTingkatSelect.addEventListener('change', function() {
            const y = matrixYearSelect ? matrixYearSelect.value : defaultYear;
            const v = matrixVillageSelect ? matrixVillageSelect.value : defaultVillage;
            window.location.href = `<?= base_url('admin/pemilu/heatmap') ?>?tingkat_pemilu=${this.value}&tahun=${y}&village_id=${v}`;
        });
    }
    if (spatialTingkatSelect) {
        spatialTingkatSelect.addEventListener('change', function() {
            const y = matrixYearSelect ? matrixYearSelect.value : defaultYear;
            const v = matrixVillageSelect ? matrixVillageSelect.value : defaultVillage;
            window.location.href = `<?= base_url('admin/pemilu/heatmap') ?>?tingkat_pemilu=${this.value}&tahun=${y}&village_id=${v}`;
        });
    }

    // 1. Tahun Pemilu
    if (matrixYearSelect) {
        matrixYearSelect.addEventListener('change', function() {
            const partyId = matrixPartySelect ? matrixPartySelect.value : 'all';
            updateCalegDropdown(partyId);
            loadTpsHeatmapMatrix();
        });
    }

    // 2. Kota / Kab (Regency) -> loads Districts
    if (matrixRegencySelect) {
        matrixRegencySelect.addEventListener('change', function() {
            const regencyId = this.value;
            fetch(`<?= base_url('admin/pemilu/heatmap/get_districts') ?>?regency_id=${regencyId}`)
                .then(res => res.json())
                .then(districts => {
                    let dHtml = '';
                    districts.forEach(d => {
                        dHtml += `<option value="${d.id}">Kec. ${d.name}</option>`;
                    });
                    if (matrixDistrictSelect) {
                        matrixDistrictSelect.innerHTML = dHtml;
                        if (districts.length > 0) {
                            matrixDistrictSelect.value = districts[0].id;
                            loadVillagesForDistrict(districts[0].id);
                        } else {
                            if (matrixVillageSelect) matrixVillageSelect.innerHTML = '<option value="">-- Tidak ada kelurahan --</option>';
                            loadTpsHeatmapMatrix();
                        }
                    }
                })
                .catch(err => console.error('Error fetching districts:', err));
        });
    }

    // 3. Kecamatan (District)
    if (matrixDistrictSelect) {
        matrixDistrictSelect.addEventListener('change', function() {
            loadVillagesForDistrict(this.value);
        });
    }

    // 4. Kelurahan (Village)
    if (matrixVillageSelect) {
        matrixVillageSelect.addEventListener('change', function() {
            if (matrixRwSelect) matrixRwSelect.value = 'all';
            loadTpsHeatmapMatrix();
        });
    }

    // 5. RW
    if (matrixRwSelect) {
        matrixRwSelect.addEventListener('change', function() {
            loadTpsHeatmapMatrix();
        });
    }

    // 6. Partai
    if (matrixPartySelect) {
        matrixPartySelect.addEventListener('change', function() {
            updateCalegDropdown(this.value);
            loadTpsHeatmapMatrix();
        });
    }

    // 7. Caleg
    if (matrixCalegSelect) {
        matrixCalegSelect.addEventListener('change', function() {
            if (currentMatrixData) renderMatrixTable(currentMatrixData);
        });
    }

    // Reset button
    if (btnResetFilters) {
        btnResetFilters.addEventListener('click', function() {
            if (matrixRwSelect) matrixRwSelect.value = 'all';
            if (matrixPartySelect) matrixPartySelect.value = 'all';
            if (matrixCalegSelect) matrixCalegSelect.value = 'all';
            updateCalegDropdown('all');
            loadTpsHeatmapMatrix();
        });
    }

    if (toggleShowPartyVotes) {
        toggleShowPartyVotes.addEventListener('change', function() {
            if (currentMatrixData) renderMatrixTable(currentMatrixData);
        });
    }

    if (toggleShowSubtotals) {
        toggleShowSubtotals.addEventListener('change', function() {
            if (currentMatrixData) renderMatrixTable(currentMatrixData);
        });
    }

    if (btnExportMatrixCsv) {
        btnExportMatrixCsv.addEventListener('click', function() {
            exportMatrixToCsv();
        });
    }

    // Initial Load of Matrix Heatmap on page load
    loadTpsHeatmapMatrix();

    // =========================================================
    // PART A.2: CLICK BREAKDOWN MODAL (CALEG & TPS MATRIX CELL)
    // =========================================================
    const heatmapTable = document.getElementById('heatmapMatrixTable');
    if (heatmapTable) {
        heatmapTable.addEventListener('click', function(e) {
            // 1. Check if a heat cell or party coblos cell is clicked
            const cell = e.target.closest('.cell-breakdown-trigger');
            if (cell) {
                const tpsId = cell.getAttribute('data-tps-id');
                const calegId = cell.getAttribute('data-caleg-id');
                const partaiId = cell.getAttribute('data-partai-id');
                if (tpsId) {
                    openTpsBreakdownModal(tpsId, calegId, partaiId);
                }
                return;
            }

            // 2. Check if a TPS column header or total cell is clicked
            const header = e.target.closest('.tps-header-clickable');
            if (header) {
                const tpsId = header.getAttribute('data-tps-id');
                if (tpsId) {
                    openTpsBreakdownModal(tpsId, null, null);
                }
            }
        });
    }

    window.openTpsBreakdownModal = function(tpsId, calegId, partaiId) {
        const modalEl = document.getElementById('tpsBreakdownModal');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // Switch to leaderboard tab by default
        const firstTabBtn = document.getElementById('tab-leaderboard-btn');
        if (firstTabBtn) {
            bootstrap.Tab.getOrCreateInstance(firstTabBtn).show();
        }

        const loadingEl = document.getElementById('tpsModalLoading');
        const contentEl = document.getElementById('tpsModalContent');
        if (loadingEl) loadingEl.classList.remove('d-none');
        if (contentEl) contentEl.classList.add('d-none');

        const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
        const tingkat = matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat;
        let url = `<?= base_url('admin/pemilu/heatmap/get_tps_breakdown') ?>?tps_id=${tpsId}&tahun=${year}&tingkat_pemilu=${tingkat}`;
        if (calegId && calegId !== 'all') {
            url += `&caleg_id=${calegId}`;
        }
        if (partaiId && partaiId !== 'all') {
            url += `&partai_id=${partaiId}`;
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (loadingEl) loadingEl.classList.add('d-none');
                if (contentEl) contentEl.classList.remove('d-none');

                if (data.status === 'success') {
                    renderTpsBreakdownModal(data, calegId);
                } else {
                    alert(data.message || 'Gagal memuat rincian TPS.');
                }
            })
            .catch(err => {
                if (loadingEl) loadingEl.classList.add('d-none');
                console.error('Error fetching TPS breakdown:', err);
                alert('Terjadi kesalahan saat memuat rincian TPS.');
            });
    };

    function renderTpsBreakdownModal(data, targetCalegId) {
        const tps = data.tps || {};
        const selectedCaleg = data.selected_caleg || null;
        const topCaleg = data.top_caleg || {};
        const topParty = data.top_party || {};
        const stats = data.stats || {};
        const leaderboard = data.leaderboard || [];
        const partyStandings = data.party_standings || [];

        // 1. Header Info
        const modalTitle = document.getElementById('tpsModalTitle');
        if (modalTitle) modalTitle.innerText = `Rincian ${tps.tps || 'TPS'}`;
        const badgeRw = document.getElementById('tpsModalBadgeRw');
        if (badgeRw) badgeRw.innerText = `RW ${tps.rw || '-'}${tps.rt ? ' / RT ' + tps.rt : ''}`;
        const badgeKel = document.getElementById('tpsModalBadgeKel');
        if (badgeKel) badgeKel.innerText = `Kel. ${tps.village_name || '-'}`;
        const subtitle = document.getElementById('tpsModalSubtitle');
        if (subtitle) {
            subtitle.innerText = `Kecamatan ${tps.district_name || 'Pancoran Mas'} • ${tps.regency_name || 'Kota Depok'} • DPT: ${(stats.dpt || 0).toLocaleString()} Pemilih`;
        }

        // 2. Selected Caleg Hero Card
        const heroSection = document.getElementById('tpsSelectedCalegSection');
        if (selectedCaleg && selectedCaleg.nama_caleg) {
            if (heroSection) heroSection.classList.remove('d-none');
            const nameEl = document.getElementById('tpsCalegName');
            if (nameEl) nameEl.innerText = selectedCaleg.nama_caleg;
            const partyNumEl = document.getElementById('tpsCalegPartyNumber');
            if (partyNumEl) partyNumEl.innerText = `No. Urut ${selectedCaleg.no_urut}`;
            const partyNameEl = document.getElementById('tpsCalegPartyName');
            if (partyNameEl) partyNameEl.innerText = `${selectedCaleg.nama_partai} (Partai No. ${selectedCaleg.partai_no_urut || '-'})`;

            const logoEl = document.getElementById('tpsCalegPartyLogo');
            if (logoEl) {
                if (selectedCaleg.lambang_partai) {
                    logoEl.src = `<?= base_url('uploads/partai/') ?>/${selectedCaleg.lambang_partai}`;
                    logoEl.classList.remove('d-none');
                } else {
                    logoEl.classList.add('d-none');
                }
            }

            const voteVal = parseInt(selectedCaleg.jumlah_suara) || 0;
            const voteDisplay = document.getElementById('tpsCalegVoteDisplay');
            if (voteDisplay) voteDisplay.innerText = `${voteVal.toLocaleString()} suara`;
            const pctDisplay = document.getElementById('tpsCalegPctDisplay');
            if (pctDisplay) pctDisplay.innerText = `(${selectedCaleg.persentase || 0}% dari suara sah)`;
            const rankDisplay = document.getElementById('tpsCalegRankDisplay');
            if (rankDisplay) rankDisplay.innerText = `Urutan #${selectedCaleg.rank_overall || '-'}`;
            const partyRankDisplay = document.getElementById('tpsCalegPartyRankDisplay');
            if (partyRankDisplay) partyRankDisplay.innerText = `Peringkat #${selectedCaleg.rank_in_party || '-'} di ${selectedCaleg.nama_partai}`;

            // Stat card 2
            const cardCalegRank = document.getElementById('tpsCardCalegRank');
            if (cardCalegRank) cardCalegRank.innerText = `#${selectedCaleg.rank_overall || '-'}`;
            const cardCalegRankDesc = document.getElementById('tpsCardCalegRankDesc');
            if (cardCalegRankDesc) cardCalegRankDesc.innerText = `Peringkat #${selectedCaleg.rank_in_party || '-'} dari ${selectedCaleg.total_in_party || '-'} Caleg ${selectedCaleg.nama_partai}`;
        } else {
            // If no specific candidate was clicked (e.g. TPS header clicked)
            if (heroSection) heroSection.classList.add('d-none');
            const cardCalegRank = document.getElementById('tpsCardCalegRank');
            if (cardCalegRank) cardCalegRank.innerText = `#1`;
            const cardCalegRankDesc = document.getElementById('tpsCardCalegRankDesc');
            if (cardCalegRankDesc) cardCalegRankDesc.innerText = `Peraih Tertinggi: ${topCaleg.nama_caleg || '-'}`;
        }

        // 3. Stat Cards
        // Top Caleg
        const cardTopCalegName = document.getElementById('tpsCardTopCalegName');
        if (cardTopCalegName) cardTopCalegName.innerText = topCaleg.nama_caleg || '-';
        const cardTopCalegVotes = document.getElementById('tpsCardTopCalegVotes');
        if (cardTopCalegVotes) cardTopCalegVotes.innerText = `${(topCaleg.jumlah_suara || 0).toLocaleString()} suara`;
        const cardTopCalegParty = document.getElementById('tpsCardTopCalegParty');
        if (cardTopCalegParty) cardTopCalegParty.innerText = `${topCaleg.nama_partai || '-'} (${topCaleg.persentase || 0}%)`;

        // Top Party
        const cardTopPartyName = document.getElementById('tpsCardTopPartyName');
        if (cardTopPartyName) cardTopPartyName.innerText = topParty.nama_partai || '-';
        const cardTopPartyVotes = document.getElementById('tpsCardTopPartyVotes');
        if (cardTopPartyVotes) cardTopPartyVotes.innerText = `${(topParty.total_suara || 0).toLocaleString()} suara`;
        const cardTopPartyPct = document.getElementById('tpsCardTopPartyPct');
        if (cardTopPartyPct) cardTopPartyPct.innerText = `${topParty.persentase || 0}% suara sah`;

        // Turnout & DPT
        const cardTotalVotes = document.getElementById('tpsCardTotalVotes');
        if (cardTotalVotes) cardTotalVotes.innerText = `${(stats.total_suara_sah || 0).toLocaleString()} suara`;
        const cardTurnout = document.getElementById('tpsCardTurnout');
        if (cardTurnout) cardTurnout.innerText = `DPT: ${(stats.dpt || 0).toLocaleString()} • Partisipasi: ${stats.partisipasi || 0}%`;

        // Counts
        const lCount = document.getElementById('tpsLeaderboardCount');
        if (lCount) lCount.innerText = leaderboard.length;
        const pCount = document.getElementById('tpsPartaiCount');
        if (pCount) pCount.innerText = partyStandings.length;

        // 4. Render Leaderboard Caleg Table
        const lBody = document.getElementById('tpsLeaderboardTableBody');
        if (lBody) {
            lBody.innerHTML = '';
            let targetRowEl = null;

            leaderboard.forEach(c => {
                const isTarget = targetCalegId && String(c.id) === String(targetCalegId);
                const tr = document.createElement('tr');
                if (isTarget) {
                    tr.className = 'leaderboard-highlight-row';
                    targetRowEl = tr;
                }

                let rankBadge = `<span class="fw-bold text-muted">${c.rank}</span>`;
                if (c.rank === 1) {
                    rankBadge = `<span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-trophy text-danger me-1"></i>#1</span>`;
                } else if (c.rank === 2) {
                    rankBadge = `<span class="badge bg-secondary-subtle text-dark fw-bold px-2 py-1"><i class="fa-solid fa-medal text-secondary me-1"></i>#2</span>`;
                } else if (c.rank === 3) {
                    rankBadge = `<span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1"><i class="fa-solid fa-award text-danger me-1"></i>#3</span>`;
                }

                const pLogo = c.lambang_partai ? `<img src="<?= base_url('uploads/partai/') ?>/${c.lambang_partai}" alt="Logo" class="rounded me-1" style="width: 18px; height: 18px; object-fit: contain;">` : '';
                const barWidth = stats.total_suara_sah > 0 ? ((c.jumlah_suara / stats.total_suara_sah) * 100).toFixed(1) : 0;

                tr.innerHTML = `
                    <td class="text-center">${rankBadge}</td>
                    <td>
                        <div class="d-flex align-items-center gap-1 flex-wrap">
                            <span class="font-monospace text-muted small me-1">${c.no_urut}.</span>
                            <strong class="${isTarget ? 'text-primary' : 'text-dark-coffee'}">${c.nama_caleg}</strong>
                            ${isTarget ? '<span class="badge bg-success text-white ms-1" style="font-size: 10px;">Caleg Yang Diklik</span>' : ''}
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${pLogo}
                            <span class="small text-secondary text-truncate" style="max-width: 170px;">${c.nama_partai}</span>
                        </div>
                    </td>
                    <td class="text-end fw-bold ${c.jumlah_suara > 0 ? 'text-dark-coffee' : 'text-muted'}">
                        ${c.jumlah_suara > 0 ? c.jumlah_suara.toLocaleString() : '-'}
                    </td>
                    <td class="text-end small ${c.jumlah_suara > 0 ? 'fw-bold text-success' : 'text-muted'}">
                        ${c.persentase}%
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px; background-color: #eee;">
                                <div class="progress-bar ${isTarget ? 'bg-primary' : (c.rank === 1 ? 'bg-danger' : 'bg-warning')}" role="progressbar" style="width: ${Math.min(100, barWidth * 2.5)}%"></div>
                            </div>
                            <span class="small text-muted" style="width: 38px; text-align: right;">${barWidth}%</span>
                        </div>
                    </td>
                `;
                lBody.appendChild(tr);
            });

            // Auto-scroll into view for selected candidate
            if (targetRowEl) {
                setTimeout(() => {
                    targetRowEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 250);
            }
        }

        // 5. Render Klasemen Partai Table
        const pBody = document.getElementById('tpsPartaiTableBody');
        if (pBody) {
            pBody.innerHTML = '';

            partyStandings.forEach(p => {
                const tr = document.createElement('tr');
                let pRankBadge = `<span class="fw-bold text-muted">${p.rank}</span>`;
                if (p.rank === 1) {
                    pRankBadge = `<span class="badge bg-warning text-dark fw-bold px-2 py-1"><i class="fa-solid fa-crown text-danger me-1"></i>#1</span>`;
                } else if (p.rank === 2) {
                    pRankBadge = `<span class="badge bg-secondary-subtle text-dark fw-bold px-2 py-1">#2</span>`;
                } else if (p.rank === 3) {
                    pRankBadge = `<span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1">#3</span>`;
                }

                const pLogo = p.lambang_partai ? `<img src="<?= base_url('uploads/partai/') ?>/${p.lambang_partai}" alt="Logo" class="rounded me-2" style="width: 22px; height: 22px; object-fit: contain;">` : '';
                const barWidth = stats.total_suara_sah > 0 ? ((p.total_suara / stats.total_suara_sah) * 100).toFixed(1) : 0;

                tr.innerHTML = `
                    <td class="text-center">${pRankBadge}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            ${pLogo}
                            <div>
                                <strong class="text-dark-coffee">${p.nama_partai}</strong>
                                <small class="text-muted d-block" style="font-size: 11px;">Nomor Urut Partai: ${p.partai_no_urut || '-'}</small>
                            </div>
                        </div>
                    </td>
                    <td class="text-end text-secondary font-monospace">${(p.suara_partai || 0).toLocaleString()}</td>
                    <td class="text-end text-secondary font-monospace">${(p.suara_caleg || 0).toLocaleString()}</td>
                    <td class="text-end fw-bold text-dark-coffee font-monospace">${(p.total_suara || 0).toLocaleString()}</td>
                    <td class="text-end fw-bold text-success">${p.persentase}%</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px; background-color: #eee;">
                                <div class="progress-bar ${p.rank === 1 ? 'bg-danger' : 'bg-warning'}" role="progressbar" style="width: ${barWidth}%"></div>
                            </div>
                            <span class="small text-muted" style="width: 38px; text-align: right;">${barWidth}%</span>
                        </div>
                    </td>
                `;
                pBody.appendChild(tr);
            });
        }

        // 6. Render Data Kegiatan di RW
        const rwData = data.rw_data || {};
        const rwTitle = document.getElementById('tpsRwContextTitle');
        if (rwTitle) rwTitle.innerText = `RW ${rwData.rw || '-'} Kel. ${rwData.kelurahan || '-'}`;
        const rwAnggotaBadge = document.getElementById('tpsRwAnggotaBadge');
        if (rwAnggotaBadge) rwAnggotaBadge.innerText = `${rwData.total_anggota || 0} Anggota`;
        const rwKegiatanBadge = document.getElementById('tpsRwKegiatanBadge');
        if (rwKegiatanBadge) rwKegiatanBadge.innerText = `${rwData.total_kegiatan || 0} Kegiatan`;

        const kCount = document.getElementById('tpsKegiatanCount');
        if (kCount) kCount.innerText = rwData.total_kegiatan || 0;
        const aCount = document.getElementById('tpsAnggotaCount');
        if (aCount) aCount.innerText = rwData.total_anggota || 0;

        const kRwLabel = document.getElementById('tpsKegiatanRwLabel');
        if (kRwLabel) kRwLabel.innerText = `RW ${rwData.rw || '-'} Kel. ${rwData.kelurahan || '-'}`;
        const kSummary = document.getElementById('tpsKegiatanSummaryText');
        if (kSummary) kSummary.innerText = `Total ${rwData.total_kegiatan || 0} kegiatan tercatat dengan estimasi ${(rwData.total_peserta || 0).toLocaleString()} peserta/warga`;

        const aRwLabel = document.getElementById('tpsAnggotaRwLabel');
        if (aRwLabel) aRwLabel.innerText = `RW ${rwData.rw || '-'} Kel. ${rwData.kelurahan || '-'}`;
        const aSummary = document.getElementById('tpsAnggotaSummaryText');
        if (aSummary) aSummary.innerText = `Total ${rwData.total_anggota || 0} anggota (${rwData.gender_l || 0} Laki-laki, ${rwData.gender_p || 0} Perempuan)`;

        const kBody = document.getElementById('tpsKegiatanTableBody');
        if (kBody) {
            kBody.innerHTML = '';
            const kegiatan = rwData.kegiatan || [];
            if (kegiatan.length === 0) {
                kBody.innerHTML = `<tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-calendar-xmark fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                        Belum ada catatan kegiatan di RW ${rwData.rw || '-'} Kel. ${rwData.kelurahan || '-'}.
                        <div class="mt-2">
                            <a href="<?= base_url('admin/pemilu/sisir-rw/create') ?>" target="_blank" class="btn btn-outline-success btn-sm">
                                <i class="fa-solid fa-plus me-1"></i> Tambah Kegiatan Sisir RW
                            </a>
                        </div>
                    </td>
                </tr>`;
            } else {
                kegiatan.forEach(k => {
                    const tr = document.createElement('tr');
                    let statusBadge = `<span class="badge bg-secondary-subtle text-secondary px-2 py-1">-</span>`;
                    if (k.status === 'hijau') {
                        statusBadge = `<span class="badge bg-success-subtle text-success border border-success fw-bold px-2 py-1"><i class="fa-solid fa-circle-check me-1"></i>Hijau</span>`;
                    } else if (k.status === 'kuning') {
                        statusBadge = `<span class="badge bg-warning-subtle text-warning-emphasis border border-warning fw-bold px-2 py-1"><i class="fa-solid fa-triangle-exclamation me-1"></i>Kuning</span>`;
                    } else if (k.status === 'merah') {
                        statusBadge = `<span class="badge bg-danger-subtle text-danger border border-danger fw-bold px-2 py-1"><i class="fa-solid fa-circle-exclamation me-1"></i>Merah</span>`;
                    }

                    tr.innerHTML = `
                        <td class="font-monospace text-secondary small">${k.tanggal || '-'}</td>
                        <td>
                            <strong class="text-dark-coffee d-block">${k.nama_kegiatan || '-'}</strong>
                            <small class="text-muted">${k.keterangan || '-'}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border px-2 py-1">${k.bentuk_kegiatan || 'Kegiatan'}</span>
                        </td>
                        <td>
                            <span class="small text-secondary">${k.alamat || (k.rt ? 'RT ' + k.rt : '-')}</span>
                        </td>
                        <td class="text-center fw-bold text-dark-coffee font-monospace">${(k.jumlah_peserta || 0).toLocaleString()}</td>
                        <td><span class="small text-muted">${k.tokoh_hadir || '-'}</span></td>
                        <td class="text-center">${statusBadge}</td>
                    `;
                    kBody.appendChild(tr);
                });
            }
        }

        // 7. Render Data Anggota di RW
        const aBody = document.getElementById('tpsAnggotaTableBody');
        if (aBody) {
            aBody.innerHTML = '';
            const members = rwData.members || [];
            if (members.length === 0) {
                aBody.innerHTML = `<tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <i class="fa-solid fa-user-slash fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                        Belum ada anggota yang tercatat di RW ${rwData.rw || '-'} Kel. ${rwData.kelurahan || '-'}.
                    </td>
                </tr>`;
            } else {
                members.forEach((m, idx) => {
                    const tr = document.createElement('tr');
                    const jk = (m.jenis_kelamin || '').toUpperCase().trim();
                    let jkBadge = `<span class="text-muted small">-</span>`;
                    if (jk === 'L') {
                        jkBadge = `<span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1"><i class="fa-solid fa-mars me-1"></i>Laki-laki</span>`;
                    } else if (jk === 'P') {
                        jkBadge = `<span class="badge bg-danger-subtle text-danger fw-bold px-2 py-1"><i class="fa-solid fa-venus me-1"></i>Perempuan</span>`;
                    }

                    tr.innerHTML = `
                        <td class="text-center text-muted fw-bold">${idx + 1}</td>
                        <td>
                            <strong class="text-dark-coffee d-block">${m.nama_lengkap || '-'}</strong>
                            ${m.kode_upa ? `<small class="text-muted font-monospace">UPA: ${m.kode_upa}</small>` : ''}
                        </td>
                        <td class="text-center font-monospace">${m.rt ? 'RT ' + m.rt : '-'}</td>
                        <td>${jkBadge}</td>
                        <td><span class="badge bg-light text-secondary border px-2 py-1">${m.status_anggota || 'Anggota'}</span></td>
                        <td><span class="small text-secondary">${m.pekerjaan || '-'}</span></td>
                        <td><span class="small text-muted">${m.pendidikan_terakhir || '-'}</span></td>
                    `;
                    aBody.appendChild(tr);
                });
            }
        }

        // 8. Quick switch buttons on RW context strip
        const btnQAnggota = document.getElementById('btnQuickTabAnggota');
        if (btnQAnggota) {
            btnQAnggota.onclick = function() {
                const aBtn = document.getElementById('tab-anggota-btn');
                if (aBtn) bootstrap.Tab.getOrCreateInstance(aBtn).show();
            };
        }
        const btnQKegiatan = document.getElementById('btnQuickTabKegiatan');
        if (btnQKegiatan) {
            btnQKegiatan.onclick = function() {
                const kBtn = document.getElementById('tab-kegiatan-btn');
                if (kBtn) bootstrap.Tab.getOrCreateInstance(kBtn).show();
            };
        }

        // 9. Setup Modal Search across all 4 tables
        const searchInput = document.getElementById('tpsModalSearchInput');
        if (searchInput) {
            searchInput.value = '';
            searchInput.oninput = function() {
                const query = this.value.toLowerCase().trim();
                const filterTable = (tbody) => {
                    if (!tbody) return;
                    tbody.querySelectorAll('tr').forEach(r => {
                        const text = r.innerText.toLowerCase();
                        r.style.display = text.includes(query) ? '' : 'none';
                    });
                };
                filterTable(lBody);
                filterTable(pBody);
                filterTable(kBody);
                filterTable(aBody);
            };
        }
    }

    // =========================================================
    // PART B: TAB 2 - MAPLIBRE GL JS PETA SPASIAL (100% GRATIS & KEYLESS)
    // =========================================================
    let map = null;
    let mapLoaded = false;
    let geojsonData = null;
    let pinMarkers = [];
    let legendContainer = null;
    let mapPopup = null;
    let rwChartInstance = null;
    let currentHeatmapData = null;

    const heatmapPartySelect = document.getElementById('heatmapPartaiSelect');
    const heatmapCalegSelect = document.getElementById('heatmapCalegSelect');
    const heatmapPaletteSelect = document.getElementById('heatmapPaletteSelect');
    const toggleMapLabels = document.getElementById('toggleMapLabels');
    const btnFitPancoranMas = document.getElementById('btnFitPancoranMas');
    const mapLoadingOverlay = document.getElementById('mapLoadingOverlay');

    const PALETTES = {
        'orange-red': {
            name: 'Oranye - Merah Panas',
            colors: ['#fff8e1', '#ffe082', '#ffb74d', '#ff7043', '#e65100', '#b71c1c'],
            stroke: '#7f0000',
            barGradient: 'linear-gradient(90deg, #fff8e1, #ffe082, #ffb74d, #ff7043, #e65100, #b71c1c)'
        },
        'emerald': {
            name: 'Hijau Pemenangan (PKS)',
            colors: ['#e8f5e9', '#c8e6c9', '#81c784', '#4caf50', '#2e7d32', '#1b5e20'],
            stroke: '#0d3813',
            barGradient: 'linear-gradient(90deg, #e8f5e9, #a5d6a7, #4caf50, #2e7d32, #1b5e20)'
        },
        'blue': {
            name: 'Biru Elektoral',
            colors: ['#e3f2fd', '#bbdefb', '#64b5f6', '#2196f3', '#1565c0', '#0d47a1'],
            stroke: '#082b61',
            barGradient: 'linear-gradient(90deg, #e3f2fd, #90caf9, #2196f3, #1565c0, #0d47a1)'
        },
        'purple': {
            name: 'Ungu - Magenta',
            colors: ['#f3e5f5', '#e1bee7', '#ba68c8', '#8e24aa', '#6a1b9a', '#4a148c'],
            stroke: '#2c0c53',
            barGradient: 'linear-gradient(90deg, #f3e5f5, #ce93d8, #ab47bc, #7b1fa2, #4a148c)'
        }
    };

    // Free OpenStreetMap Raster Basemap Style (No API key, No token required)
    const freeOsmStyle = {
        version: 8,
        sources: {
            'osm-tiles': {
                type: 'raster',
                tiles: [
                    'https://tile.openstreetmap.org/{z}/{x}/{y}.png'
                ],
                tileSize: 256,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'
            }
        },
        layers: [
            {
                id: 'osm-tiles-layer',
                type: 'raster',
                source: 'osm-tiles',
                minzoom: 0,
                maxzoom: 19
            }
        ]
    };

    function getHeatColor(val, maxVal, paletteKey) {
        if (!maxVal || maxVal <= 0 || val <= 0) return '#f5f5f5';
        const pal = PALETTES[paletteKey] || PALETTES['orange-red'];
        const ratio = Math.min(1, Math.max(0, val / maxVal));

        if (ratio >= 0.85) return pal.colors[5];
        if (ratio >= 0.65) return pal.colors[4];
        if (ratio >= 0.45) return pal.colors[3];
        if (ratio >= 0.25) return pal.colors[2];
        if (ratio >= 0.10) return pal.colors[1];
        return pal.colors[0];
    }

    // Helper: calculate bounds from GeoJSON features
    function getGeoJsonBounds(data) {
        const bounds = new maplibregl.LngLatBounds();
        if (!data || !data.features) return bounds;
        data.features.forEach(f => {
            const geom = f.geometry;
            if (!geom) return;
            if (geom.type === 'Polygon') {
                geom.coordinates.forEach(ring => {
                    ring.forEach(c => bounds.extend(c));
                });
            } else if (geom.type === 'MultiPolygon') {
                geom.coordinates.forEach(poly => {
                    poly.forEach(ring => {
                        ring.forEach(c => bounds.extend(c));
                    });
                });
            }
        });
        return bounds;
    }

    function initMap() {
        if (map !== null) return;

        map = new maplibregl.Map({
            container: 'electionHeatMap',
            style: freeOsmStyle,
            center: [106.805, -6.398],
            zoom: 12.8,
            attributionControl: true
        });

        // Add standard navigation controls (zoom in/out, compass)
        map.addControl(new maplibregl.NavigationControl({
            showCompass: true,
            showZoom: true
        }), 'top-left');

        // Custom Legend Control in bottom-right
        class MapLibreLegendControl {
            onAdd(map) {
                this._container = document.createElement('div');
                this._container.className = 'maplibregl-ctrl heatmap-legend-box shadow-sm';
                legendContainer = this._container;
                return this._container;
            }
            onRemove() {
                if (this._container && this._container.parentNode) {
                    this._container.parentNode.removeChild(this._container);
                }
                legendContainer = null;
            }
        }
        map.addControl(new MapLibreLegendControl(), 'bottom-right');

        // Popup for kelurahan hover
        mapPopup = new maplibregl.Popup({
            closeButton: false,
            closeOnClick: false,
            offset: 14
        });

        map.on('load', function() {
            mapLoaded = true;

            // Load Pancoran Mas GeoJSON (with cache-buster)
            fetch('<?= base_url('assets/geojson/pancoran_mas_kelurahan.json?v=' . filemtime(FCPATH . 'assets/geojson/pancoran_mas_kelurahan.json')) ?>')
                .then(res => res.json())
                .then(data => {
                    geojsonData = data;

                    // Add GeoJSON source
                    map.addSource('pancoran_mas', {
                        type: 'geojson',
                        data: geojsonData
                    });

                    // Polygon fill layer
                    map.addLayer({
                        id: 'kelurahan-fill',
                        type: 'fill',
                        source: 'pancoran_mas',
                        paint: {
                            'fill-color': ['coalesce', ['get', 'heat_color'], '#ffe082'],
                            'fill-opacity': 0.80
                        }
                    });

                    // Boundary line layer
                    map.addLayer({
                        id: 'kelurahan-outline',
                        type: 'line',
                        source: 'pancoran_mas',
                        paint: {
                            'line-color': ['coalesce', ['get', 'stroke_color'], '#7f0000'],
                            'line-width': 2.5
                        }
                    });

                    // Hover highlight line layer
                    map.addLayer({
                        id: 'kelurahan-highlight',
                        type: 'line',
                        source: 'pancoran_mas',
                        filter: ['==', 'id', ''],
                        paint: {
                            'line-color': '#ffffff',
                            'line-width': 4.5
                        }
                    });

                    // Pointer on hover
                    map.on('mouseenter', 'kelurahan-fill', function() {
                        map.getCanvas().style.cursor = 'pointer';
                    });

                    map.on('mousemove', 'kelurahan-fill', function(e) {
                        if (e.features && e.features.length > 0) {
                            const feat = e.features[0];
                            const props = feat.properties;
                            const vId = props.id || props.village_id;
                            const vName = props.village_name || props.name || '-';
                            const suara = parseInt(props.suara || 0);
                            const pct = props.percentage || 0;
                            const tpsCount = props.tps_count || 0;

                            map.setFilter('kelurahan-highlight', ['==', 'id', vId]);

                            const tooltipHtml = `
                                <div class="text-center p-1" style="min-width: 145px; font-family: system-ui, -apple-system, sans-serif;">
                                    <strong class="d-block text-dark-coffee mb-1" style="font-size: 13px;">Kel. ${vName}</strong>
                                    <div class="text-danger fw-bold fs-6 my-1">${suara.toLocaleString()} suara</div>
                                    <span class="badge bg-secondary mb-1">${pct}% dari total</span>
                                    ${tpsCount > 0 ? `<div class="small text-muted">${tpsCount} TPS terdata</div>` : ''}
                                    <div class="mt-2 text-primary small fw-semibold"><i class="fa-solid fa-hand-pointer me-1"></i>Klik detail RW</div>
                                </div>
                            `;
                            mapPopup.setLngLat(e.lngLat).setHTML(tooltipHtml).addTo(map);
                        }
                    });

                    map.on('mouseleave', 'kelurahan-fill', function() {
                        map.getCanvas().style.cursor = '';
                        map.setFilter('kelurahan-highlight', ['==', 'id', '']);
                        mapPopup.remove();
                    });

                    // Click event to open RW Breakdown Modal
                    map.on('click', 'kelurahan-fill', function(e) {
                        if (e.features && e.features.length > 0) {
                            const props = e.features[0].properties;
                            const vId = props.id || props.village_id;
                            const vName = props.village_name || props.name;
                            openRwModal(vId, vName);
                        }
                    });

                    // Fit to bounds
                    const bounds = getGeoJsonBounds(geojsonData);
                    if (!bounds.isEmpty()) {
                        map.fitBounds(bounds, { padding: 30, duration: 600 });
                    }

                    // Load vote data
                    loadHeatmapData();
                })
                .catch(err => {
                    console.error('Error loading GeoJSON for MapLibre:', err);
                });
        });
    }

    function loadHeatmapData() {
        const partaiId = heatmapPartySelect ? heatmapPartySelect.value : 'all';
        const calegId = heatmapCalegSelect ? heatmapCalegSelect.value : 'all';
        const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
        const tingkat = spatialTingkatSelect ? spatialTingkatSelect.value : (matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat);
        const districtId = defaultDistrict || '327601';

        if (mapLoadingOverlay) mapLoadingOverlay.classList.remove('d-none');

        const url = `<?= base_url('admin/pemilu/heatmap/get_spatial') ?>?partai_id=${partaiId}&caleg_id=${calegId}&tahun=${year}&district_id=${districtId}&tingkat_pemilu=${tingkat}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (mapLoadingOverlay) mapLoadingOverlay.classList.add('d-none');
                if (data.status === 'success') {
                    currentHeatmapData = data;
                    updateCalegBanner(data);
                    renderSpatialHeatmap(data);
                    renderRankingsAndTopRws(data);
                }
            })
            .catch(err => {
                if (mapLoadingOverlay) mapLoadingOverlay.classList.add('d-none');
                console.error('Error fetching spatial heatmap data:', err);
            });
    }

    function updateCalegBanner(data) {
        const caleg = data.caleg_info || data.caleg || {};
        const party = data.party_info || data.party || {};
        const totalDistrictVotes = data.total_suara !== undefined ? data.total_suara : ((data.stats && data.stats.total_district_votes) || 0);

        const bannerPartyName = document.getElementById('bannerPartyName');
        const bannerPartyLogo = document.getElementById('bannerPartyLogo');
        const bannerCalegName = document.getElementById('bannerCalegName');
        const bannerNoBadge = document.getElementById('bannerNoBadge');
        const bannerTotalSuara = document.getElementById('bannerTotalSuara');
        const bannerTopVillage = document.getElementById('bannerTopVillage');
        const bannerTopVillageStats = document.getElementById('bannerTopVillageStats');

        if (bannerPartyName) {
            bannerPartyName.innerText = party.nama_partai ? `${party.nama_partai}` : 'Seluruh Partai Politik';
        }
        if (bannerPartyLogo) {
            if (party.lambang_partai) {
                bannerPartyLogo.src = `<?= base_url('uploads/partai/') ?>/${party.lambang_partai}`;
            } else {
                bannerPartyLogo.src = 'https://placehold.co/56?text=ALL';
            }
        }
        if (bannerCalegName) {
            if (caleg.nama_caleg) {
                bannerCalegName.innerText = caleg.nama_caleg;
            } else if (party.nama_partai) {
                bannerCalegName.innerText = `Seluruh Caleg ${party.nama_partai}`;
            } else {
                bannerCalegName.innerText = 'Seluruh Caleg (Semua Partai)';
            }
        }
        if (bannerNoBadge) {
            bannerNoBadge.innerText = caleg.no_urut ? `No. ${caleg.no_urut}` : (party.no_urut ? `Partai No. ${party.no_urut}` : 'Semua');
        }
        if (bannerTotalSuara) bannerTotalSuara.innerText = parseInt(totalDistrictVotes || 0).toLocaleString();

        const topVillage = (data.village_data && data.village_data.length > 0) ? data.village_data[0] : null;
        if (bannerTopVillage && topVillage && topVillage.suara > 0) {
            bannerTopVillage.innerText = 'Kel. ' + (topVillage.village_name || topVillage.name);
            const pct = totalDistrictVotes > 0 ? ((topVillage.suara / totalDistrictVotes) * 100).toFixed(1) : 0;
            if (bannerTopVillageStats) bannerTopVillageStats.innerText = `${parseInt(topVillage.suara).toLocaleString()} suara (${pct}%)`;
        } else if (bannerTopVillage) {
            bannerTopVillage.innerText = '-';
            if (bannerTopVillageStats) bannerTopVillageStats.innerText = '-';
        }
    }

    function renderSpatialHeatmap(data) {
        if (!map || !mapLoaded || !geojsonData) return;

        const villageMap = data.village_map || {};
        const villageVotes = data.village_votes || {};
        const maxVotes = data.max_suara || (data.stats ? data.stats.max_village_votes : 1) || 1;
        const currentPalette = heatmapPaletteSelect ? heatmapPaletteSelect.value : 'orange-red';
        const pal = PALETTES[currentPalette] || PALETTES['orange-red'];

        // Remove old HTML pin markers
        pinMarkers.forEach(m => m.remove());
        pinMarkers = [];

        // Update GeoJSON properties
        geojsonData.features.forEach(f => {
            const vId = f.properties.id || f.properties.village_id;
            const vData = villageMap[vId] || villageVotes[vId] || { suara: 0, percentage: 0, tps_count: 0 };
            const suara = parseInt(vData.suara || 0);
            const vName = f.properties.name || f.properties.village_name || vData.village_name || '-';

            f.properties.id = vId;
            f.properties.village_name = vName;
            f.properties.suara = suara;
            f.properties.percentage = vData.percentage !== undefined ? vData.percentage : 0;
            f.properties.tps_count = vData.tps_count || 0;
            f.properties.heat_color = getHeatColor(suara, maxVotes, currentPalette);
            f.properties.stroke_color = pal.stroke;

            // Pin label marker
            if (toggleMapLabels && toggleMapLabels.checked) {
                const lat = f.properties.lat;
                const lon = f.properties.lon;
                if (lat && lon) {
                    const el = document.createElement('div');
                    el.className = 'kelurahan-label-pin';
                    const displayName = vName.startsWith('Kel.') ? vName : 'Kel. ' + vName;
                    el.innerHTML = `<div>${displayName}</div><div class="pin-votes">${suara.toLocaleString()}</div>`;

                    const marker = new maplibregl.Marker({
                        element: el,
                        anchor: 'center'
                    })
                    .setLngLat([lon, lat])
                    .addTo(map);

                    pinMarkers.push(marker);
                }
            }
        });

        // Update GeoJSON source data
        const src = map.getSource('pancoran_mas');
        if (src) {
            src.setData(geojsonData);
        }

        // Update stroke color
        if (map.getLayer('kelurahan-outline')) {
            map.setPaintProperty('kelurahan-outline', 'line-color', pal.stroke);
        }

        renderLegend(maxVotes, currentPalette);
    }

    function renderLegend(maxVotes, currentPalette) {
        if (!legendContainer) return;
        const pal = PALETTES[currentPalette || 'orange-red'] || PALETTES['orange-red'];
        const max = maxVotes || 0;

        legendContainer.innerHTML = `
            <div class="fw-bold text-dark-coffee mb-1"><i class="fa-solid fa-fire text-danger me-1"></i>Kepadatan Suara</div>
            <div class="legend-gradient-bar" style="background: ${pal.barGradient};"></div>
            <div class="d-flex justify-content-between small text-muted font-monospace" style="font-size: 11px;">
                <span>0</span>
                <span>${Math.round(max * 0.5).toLocaleString()}</span>
                <span>${parseInt(max).toLocaleString()}</span>
            </div>
        `;
    }

    function renderRankingsAndTopRws(data) {
        const villageMap = data.village_map || {};
        const villageList = (data.village_data && data.village_data.length > 0) ? [...data.village_data] : Object.values(villageMap);
        villageList.sort((a, b) => (parseInt(b.suara) || 0) - (parseInt(a.suara) || 0));

        const rankingListEl = document.getElementById('villageRankingList');
        if (rankingListEl) {
            rankingListEl.innerHTML = '';
            const max = villageList.length > 0 ? (parseInt(villageList[0].suara) || 1) : 1;

            villageList.forEach((v, idx) => {
                const suara = parseInt(v.suara) || 0;
                const pct = max > 0 ? ((suara / max) * 100).toFixed(0) : 0;
                const vId = v.village_id || v.id;
                const vName = v.village_name || v.name;
                const item = document.createElement('a');
                item.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-1 border-0 border-bottom cursor-pointer';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge ${idx === 0 ? 'bg-warning text-dark' : 'bg-light text-muted'} fw-bold" style="width: 24px;">${idx + 1}</span>
                        <div>
                            <div class="fw-semibold text-dark-coffee small">Kel. ${vName}</div>
                            <div class="progress" style="height: 4px; width: 100px; border-radius: 2px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: ${pct}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <span class="fw-bold text-dark-coffee small">${suara.toLocaleString()}</span>
                        <small class="text-muted d-block" style="font-size: 0.7rem;">suara</small>
                    </div>
                `;
                item.addEventListener('click', () => {
                    openRwModal(vId, vName);
                });
                rankingListEl.appendChild(item);
            });
        }

        const topRwsListEl = document.getElementById('topRwsList');
        if (topRwsListEl) {
            topRwsListEl.innerHTML = '';
            const topRws = data.top_rws || (data.stats && data.stats.top_rws) || [];
            if (topRws.length === 0) {
                topRwsListEl.innerHTML = '<div class="text-muted small py-3 text-center">Belum ada data RW.</div>';
                return;
            }

            topRws.forEach((rw, idx) => {
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex justify-content-between align-items-center py-2 px-1 border-0 border-bottom';
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-trophy ${idx === 0 ? 'text-warning' : 'text-secondary opacity-50'}"></i>
                        <div>
                            <div class="fw-bold text-dark-coffee small">RW ${rw.rw}</div>
                            <div class="small text-muted" style="font-size: 0.72rem;">Kel. ${rw.village_name}</div>
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark fw-bold px-2 py-1">${parseInt(rw.suara).toLocaleString()} suara</span>
                `;
                topRwsListEl.appendChild(item);
            });
        }
    }

    if (heatmapPartySelect) {
        heatmapPartySelect.addEventListener('change', function() {
            const partyId = this.value;
            const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
            const tingkat = spatialTingkatSelect ? spatialTingkatSelect.value : (matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat);
            fetch(`<?= base_url('admin/pemilu/heatmap/get_caleg_by_party') ?>?partai_id=${partyId}&tahun=${year}&tingkat_pemilu=${tingkat}`)
                .then(res => res.json())
                .then(calegs => {
                    if (heatmapCalegSelect) {
                        let html = '';
                        if (partyId === 'all') {
                            html += '<option value="all">-- Seluruh Caleg & Semua Partai --</option>';
                            let curParty = '';
                            let groupOpen = false;
                            calegs.forEach(c => {
                                if (c.nama_partai && c.nama_partai !== curParty) {
                                    if (groupOpen) html += '</optgroup>';
                                    curParty = c.nama_partai;
                                    html += `<optgroup label="${curParty}">`;
                                    groupOpen = true;
                                }
                                const noUrut = c.no_urut ? `${c.no_urut}. ` : '';
                                html += `<option value="${c.id}">${noUrut}${c.nama_caleg}</option>`;
                            });
                            if (groupOpen) html += '</optgroup>';
                        } else {
                            html += '<option value="all">-- Seluruh Caleg & Coblos Partai Ini --</option>';
                            html += '<option value="party_only">-- Hanya Suara Coblos Partai --</option>';
                            calegs.forEach(c => {
                                const noUrut = c.no_urut ? `${c.no_urut}. ` : '';
                                html += `<option value="${c.id}">${noUrut}${c.nama_caleg}</option>`;
                            });
                        }
                        heatmapCalegSelect.innerHTML = html;
                    }
                    loadHeatmapData();
                })
                .catch(err => {
                    console.error('Error fetching caleg list:', err);
                    loadHeatmapData();
                });
        });
    }

    if (heatmapCalegSelect) {
        heatmapCalegSelect.addEventListener('change', function() {
            loadHeatmapData();
        });
    }

    if (heatmapPaletteSelect) {
        heatmapPaletteSelect.addEventListener('change', function() {
            if (currentHeatmapData) {
                renderSpatialHeatmap(currentHeatmapData);
            }
        });
    }

    if (toggleMapLabels) {
        toggleMapLabels.addEventListener('change', function() {
            if (currentHeatmapData) {
                renderSpatialHeatmap(currentHeatmapData);
            }
        });
    }

    if (btnFitPancoranMas) {
        btnFitPancoranMas.addEventListener('click', function() {
            if (map && geojsonData) {
                const bounds = getGeoJsonBounds(geojsonData);
                if (!bounds.isEmpty()) {
                    map.fitBounds(bounds, { padding: 35, duration: 700 });
                }
            }
        });
    }

    document.querySelectorAll('.quick-caleg-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const calegId = this.getAttribute('data-caleg-id');
            if (heatmapPartySelect) {
                heatmapPartySelect.value = "<?= $pksId ?>";
            }
            if (heatmapCalegSelect) {
                heatmapCalegSelect.value = calegId;
            }
            loadHeatmapData();
        });
    });

    const tabSpatialBtn = document.getElementById('tab-spatial-btn');
    if (tabSpatialBtn) {
        tabSpatialBtn.addEventListener('shown.bs.tab', function() {
            if (!map) {
                initMap();
            } else {
                setTimeout(function() {
                    map.resize();
                    if (geojsonData) {
                        const bounds = getGeoJsonBounds(geojsonData);
                        if (!bounds.isEmpty()) {
                            map.fitBounds(bounds, { padding: 30, duration: 400 });
                        }
                    }
                }, 200);
            }
        });
    }

    window.openRwModal = function(villageId, villageName) {
        const partaiId = heatmapPartySelect ? heatmapPartySelect.value : 'all';
        const calegId = heatmapCalegSelect ? heatmapCalegSelect.value : 'all';
        const year = matrixYearSelect ? matrixYearSelect.value : defaultYear;
        const tingkat = spatialTingkatSelect ? spatialTingkatSelect.value : (matrixTingkatSelect ? matrixTingkatSelect.value : defaultTingkat);

        document.getElementById('modalVillageName').innerText = 'Kel. ' + villageName;
        const modal = new bootstrap.Modal(document.getElementById('rwBreakdownModal'));
        modal.show();

        const url = `<?= base_url('admin/pemilu/heatmap/get_rw_breakdown') ?>?village_id=${villageId}&partai_id=${partaiId}&caleg_id=${calegId}&tahun=${year}&tingkat_pemilu=${tingkat}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    renderRwModalData(data);
                }
            })
            .catch(err => {
                console.error('Error fetching RW breakdown:', err);
            });
    };

    function renderRwModalData(data) {
        const caleg = data.caleg || {};
        const village = data.village || {};
        const rows = data.rw_list || [];
        const totalSuara = data.total_suara || 0;
        const maxSuara = data.max_suara || 1;

        document.getElementById('modalCalegSub').innerText = `Caleg: ${caleg.nama_caleg || '-'}`;
        document.getElementById('modalTotalSuara').innerText = parseInt(totalSuara).toLocaleString();
        document.getElementById('modalTotalRw').innerText = rows.length + ' RW';
        const avg = rows.length > 0 ? Math.round(totalSuara / rows.length) : 0;
        document.getElementById('modalAvgRw').innerText = avg.toLocaleString();

        const labels = rows.map(r => 'RW ' + r.rw);
        const votes = rows.map(r => parseInt(r.suara) || 0);

        const ctx = document.getElementById('rwVotesBarChart').getContext('2d');
        if (rwChartInstance) {
            rwChartInstance.destroy();
        }

        rwChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Suara',
                    data: votes,
                    backgroundColor: '#f26522',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        const tbody = document.getElementById('modalRwTableBody');
        tbody.innerHTML = '';

        if (rows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data RW.</td></tr>';
            return;
        }

        rows.forEach(r => {
            const suara = parseInt(r.suara) || 0;
            const pct = totalSuara > 0 ? ((suara / totalSuara) * 100).toFixed(1) : 0;
            const barWidth = maxSuara > 0 ? ((suara / maxSuara) * 100).toFixed(1) : 0;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="ps-3 fw-bold text-dark-coffee">RW ${r.rw}</td>
                <td class="text-center text-muted">${r.tps_count || '-'} TPS</td>
                <td class="text-end fw-bold text-dark-coffee">${suara.toLocaleString()}</td>
                <td class="pe-3">
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 6px; border-radius: 3px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: ${barWidth}%"></div>
                        </div>
                        <span class="small text-muted" style="width: 42px; text-align: right;">${pct}%</span>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
});
</script>
<?= $this->endSection() ?>
