<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-square-poll-horizontal me-2 text-gold"></i>Laporan Perolehan Suara TPS & RW (<?= esc($selectedTingkat == 'DPRD_PROV' ? 'DPRD Provinsi Jabar' : ($selectedTingkat == 'DPR_RI' ? 'DPR RI' : 'DPRD Kota Depok')) ?>)
        </h4>
        <p class="text-secondary small mb-0">Rincian perolehan suara Partai Keadilan Sejahtera (PKS) di tingkat RW dan TPS - <?= esc($selectedTingkat == 'DPRD_PROV' ? 'Dapil Jabar 8 (Depok - Bekasi)' : ($selectedTingkat == 'DPR_RI' ? 'Dapil Jabar 6 (Depok - Bekasi)' : 'Kota Depok (Dapil 1 Pancoran Mas)')) ?></p>
    </div>
</div>

<!-- FILTER BAR -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background-color: #efebe9;">
    <div class="card-body p-4">
        <form action="<?= base_url('admin/pemilu/tps-suara') ?>" method="GET" class="row g-3 align-items-end" id="filterForm">
            <!-- 1. Tingkat Pemilu Filter -->
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

            <!-- 2. Tahun Filter -->
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

            <!-- 3. Kecamatan Filter -->
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

            <!-- 4. Kelurahan Filter -->
            <div class="col-md-3">
                <label class="form-label fw-bold text-dark-coffee small"><i class="fa-solid fa-building-flag me-1"></i>Kelurahan:</label>
                <select name="village_id" id="villageSelect" class="form-select border-0 shadow-sm text-dark-coffee fw-medium" style="border-radius: 8px; height: 42px;">
                    <?php if (empty($villages)): ?>
                        <option value="">-- Tidak ada Kelurahan --</option>
                    <?php else: ?>
                        <?php foreach ($villages as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= $selectedVillage == $v['id'] ? 'selected' : '' ?>>
                                Kel. <?= esc($v['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- 5. Action Buttons -->
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn w-100 fw-bold border-0 shadow-sm" style="background-color: #3e2723; color: #fff8e1; border-radius: 8px; height: 42px;">
                    <i class="fa-solid fa-filter me-2"></i>Filter
                </button>
                <a href="<?= base_url('admin/pemilu/tps-suara') ?>" class="btn w-100 fw-bold shadow-sm" style="background-color: #ffffff; color: #3e2723; border: 2px solid #3e2723; border-radius: 8px; height: 42px; line-height: 26px; text-decoration: none; text-align: center;">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (empty($selectedVillage)): ?>
    <div class="alert alert-warning border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-triangle-exclamation me-2"></i>Silakan pilih Kelurahan terlebih dahulu untuk memuat data.
    </div>
<?php else: ?>

    <!-- SECTION 1: KALKULASI SUARA PER RW -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-header p-4 bg-transparent border-0">
            <h5 class="fw-bold mb-0 text-dark-coffee">
                <i class="fa-solid fa-calculator text-gold me-2"></i>Kalkulasi Perolehan Suara PKS per RW
            </h5>
            <p class="text-secondary small mb-0 mt-1">Akumulasi perolehan suara PKS di setiap RW pada kelurahan terpilih</p>
        </div>
        <div class="card-body p-4 pt-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3" style="width: 120px;">No RW</th>
                            <th class="text-center" style="width: 120px;">Jumlah TPS</th>
                            <th class="text-end" style="width: 160px;">Total Suara PKS</th>
                            <th class="text-end" style="width: 160px;">Total Suara Sah</th>
                            <th class="text-end" style="width: 160px;">Suara Tidak Sah</th>
                            <th class="text-end" style="width: 160px;">Total Suara Masuk</th>
                            <th class="text-end pe-3" style="width: 180px;">Persentase PKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($rwResults)): ?>
                            <?php foreach ($rwResults as $rw): 
                                $percent = $rw['total_suara_sah'] > 0 ? ($rw['total_suara_pks'] / $rw['total_suara_sah']) * 100 : 0;
                            ?>
                                <tr class="fw-semibold clickable-rw-row" data-rw="<?= esc($rw['rw']) ?>" style="cursor: pointer;">
                                    <td class="ps-3 text-dark-coffee">RW <?= esc($rw['rw']) ?></td>
                                    <td class="text-center text-secondary"><?= number_format($rw['total_tps']) ?> TPS</td>
                                    <td class="text-end text-success"><?= number_format($rw['total_suara_pks']) ?></td>
                                    <td class="text-end text-dark-coffee"><?= number_format($rw['total_suara_sah']) ?></td>
                                    <td class="text-end text-muted"><?= number_format($rw['total_suara_tidak_sah']) ?></td>
                                    <td class="text-end text-primary"><?= number_format($rw['total_suara_total']) ?></td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-gold text-dark p-2" style="font-size: 0.85rem;">
                                            <?= number_format($percent, 2) ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data rekapitulasi RW untuk kelurahan ini.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- SECTION 2: DATA SUARA DETAIL PER TPS -->
    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-header p-4 bg-transparent border-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-list text-gold me-2"></i>Data Rincian Suara per TPS
                </h5>
                <p class="text-secondary small mb-0 mt-1">Rincian perolehan suara PKS, suara sah, dan tidak sah untuk seluruh TPS</p>
            </div>
            <span class="badge bg-gold text-dark fs-7 px-3 py-2 fw-bold">Total: <?= count($tpsResults) ?> TPS</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tpsDetailsTable">
                    <thead class="bg-dark-coffee text-cream" style="background-color: #3e2723; color: #fff8e1;">
                        <tr>
                            <th class="ps-4">No TPS</th>
                            <th>RW / RT</th>
                            <th class="text-end">Suara Partai PKS</th>
                            <th class="text-end">Suara Caleg PKS</th>
                            <th class="text-end text-warning">Total Suara PKS</th>
                            <th class="text-end">Total Suara Sah</th>
                            <th class="text-end text-danger">Suara Tidak Sah</th>
                            <th class="text-end text-primary">Total Suara Masuk</th>
                            <th class="text-end pe-4">Persentase PKS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tpsResults)): ?>
                            <?php foreach ($tpsResults as $tps): 
                                $percent = $tps['suara_sah'] > 0 ? ($tps['pks_total'] / $tps['suara_sah']) * 100 : 0;
                            ?>
                                <tr class="clickable-tps-row" data-tps-id="<?= $tps['id'] ?>" data-tps-name="<?= esc($tps['tps']) ?>" style="cursor: pointer;">
                                    <td class="ps-4 fw-bold text-dark-coffee"><?= esc($tps['tps']) ?></td>
                                    <td>
                                        <span class="badge bg-light text-dark border">RW <?= esc($tps['rw']) ?></span>
                                        <span class="badge bg-light text-dark border">RT <?= esc($tps['rt']) ?></span>
                                    </td>
                                    <td class="text-end text-secondary"><?= number_format($tps['pks_partai']) ?></td>
                                    <td class="text-end text-secondary"><?= number_format($tps['pks_caleg']) ?></td>
                                    <td class="text-end fw-bold text-success"><?= number_format($tps['pks_total']) ?></td>
                                    <td class="text-end text-dark-coffee"><?= number_format($tps['suara_sah']) ?></td>
                                    <td class="text-end text-danger"><?= number_format($tps['suara_tidak_sah']) ?></td>
                                    <td class="text-end text-primary"><?= number_format($tps['suara_total']) ?></td>
                                    <td class="text-end pe-4 fw-bold text-gold">
                                        <?= number_format($percent, 2) ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-square-poll-horizontal fa-2x d-block mb-2 opacity-50"></i>
                                    Tidak ada data suara TPS untuk kelurahan ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-cream py-3" style="background-color: #3e2723; color: #fff8e1; border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="modal-title fw-bold" id="detailModalLabel">Rincian Perolehan Suara</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Pills for details -->
                    <ul class="nav nav-pills mb-4 justify-content-center" id="detailTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-4" id="parties-tab" data-bs-toggle="pill" data-bs-target="#parties-panel" type="button" role="tab">
                                <i class="fa-solid fa-flag me-2"></i>Perolehan Suara Partai
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4" id="candidates-tab" data-bs-toggle="pill" data-bs-target="#candidates-panel" type="button" role="tab">
                                <i class="fa-solid fa-trophy me-2 text-gold"></i>Top 5 Caleg Terkuat
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="detailTabContent">
                        <!-- PARTIES PANEL -->
                        <div class="tab-pane fade show active" id="parties-panel" role="tabpanel">
                            <div class="table-responsive" style="max-height: 400px;">
                                <table class="table table-hover no-datatable align-middle mb-0">
                                    <thead class="bg-light sticky-top">
                                        <tr>
                                            <th style="width: 80px;" class="text-center">No Urut</th>
                                            <th>Partai Politik</th>
                                            <th class="text-end" style="width: 140px;">Suara Partai</th>
                                            <th class="text-end" style="width: 140px;">Suara Caleg</th>
                                            <th class="text-end" style="width: 150px;">Total Perolehan</th>
                                        </tr>
                                    </thead>
                                    <tbody id="partiesDetailBody">
                                        <!-- AJAX Content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- CANDIDATES PANEL -->
                        <div class="tab-pane fade" id="candidates-panel" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-hover no-datatable align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="width: 80px;" class="text-center">Rank</th>
                                            <th>Nama Calon Legislatif (Caleg)</th>
                                            <th>Partai Politik</th>
                                            <th class="text-end" style="width: 160px;">Jumlah Suara</th>
                                        </tr>
                                    </thead>
                                    <tbody id="candidatesDetailBody">
                                        <!-- AJAX Content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic village dropdown update on district change
    document.getElementById('districtSelect').addEventListener('change', function() {
        document.getElementById('villageSelect').value = '';
        this.form.submit();
    });

    const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
    const modalLabel = document.getElementById('detailModalLabel');
    const partiesBody = document.getElementById('partiesDetailBody');
    const candidatesBody = document.getElementById('candidatesDetailBody');

    const selectedYear = '<?= $selectedYear ?>';
    const selectedTingkat = '<?= $selectedTingkat ?>';
    const selectedVillage = '<?= $selectedVillage ?>';
    const villageName = '<?= !empty($villages) ? array_column($villages, 'name', 'id')[$selectedVillage] ?? '' : '' ?>';

    // Click listener for RW rows
    document.querySelectorAll('.clickable-rw-row').forEach(row => {
        row.addEventListener('click', function() {
            const rw = this.getAttribute('data-rw');
            modalLabel.innerHTML = `<i class="fa-solid fa-calculator text-gold me-2"></i>Rincian Suara RW ${rw} - Kel. ${villageName} (${selectedYear})`;
            
            partiesBody.innerHTML = `<tr><td colspan="5" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`;
            candidatesBody.innerHTML = `<tr><td colspan="4" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`;
            
            // Activate first tab
            const triggerEl = document.querySelector('#detailTab button[data-bs-target="#parties-panel"]');
            bootstrap.Tab.getInstance(triggerEl)?.show() || new bootstrap.Tab(triggerEl).show();
            
            detailModal.show();

            fetch(`<?= base_url('admin/pemilu/tps-suara/get_rw_detail') ?>?rw=${rw}&village_id=${selectedVillage}&tahun=${selectedYear}&tingkat_pemilu=${selectedTingkat}`)
                .then(res => res.json())
                .then(data => {
                    renderDetails(data);
                })
                .catch(err => {
                    partiesBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
                });
        });
    });

    // Click listener for TPS rows
    document.querySelectorAll('.clickable-tps-row').forEach(row => {
        row.addEventListener('click', function() {
            const tpsId = this.getAttribute('data-tps-id');
            const tpsName = this.getAttribute('data-tps-name');
            modalLabel.innerHTML = `<i class="fa-solid fa-square-poll-horizontal text-gold me-2"></i>Rincian Suara ${tpsName} - Kel. ${villageName} (${selectedYear})`;

            partiesBody.innerHTML = `<tr><td colspan="5" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`;
            candidatesBody.innerHTML = `<tr><td colspan="4" class="text-center py-4"><i class="fa-solid fa-spinner fa-spin me-2"></i>Memuat data...</td></tr>`;
            
            // Activate first tab
            const triggerEl = document.querySelector('#detailTab button[data-bs-target="#parties-panel"]');
            bootstrap.Tab.getInstance(triggerEl)?.show() || new bootstrap.Tab(triggerEl).show();

            detailModal.show();

            fetch(`<?= base_url('admin/pemilu/tps-suara/get_tps_detail') ?>?tps_id=${tpsId}&tahun=${selectedYear}&tingkat_pemilu=${selectedTingkat}`)
                .then(res => res.json())
                .then(data => {
                    renderDetails(data);
                })
                .catch(err => {
                    partiesBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Gagal memuat data.</td></tr>`;
                });
        });
    });

    function renderDetails(data) {
        // Render Parties
        if (!data.parties || data.parties.length === 0) {
            partiesBody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data.</td></tr>`;
        } else {
            let partyHtml = '';
            data.parties.forEach(p => {
                const logo = p.lambang_partai 
                    ? `<img src="<?= base_url('uploads/partai/') ?>/${p.lambang_partai}" alt="${p.nama_partai}" style="height: 24px; width: 24px; object-fit: contain; margin-right: 8px;">`
                    : '<span class="badge bg-secondary me-2" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-flag fs-8"></i></span>';
                
                const isPks = p.nama_partai.toLowerCase().includes('keadilan sejahtera') || p.nama_partai.toLowerCase().includes('pks');
                const rowStyle = isPks ? 'style="background-color: #fffde7; font-weight: bold; border-left: 4px solid #d4af37;"' : '';
                
                partyHtml += `
                    <tr ${rowStyle}>
                        <td class="text-center font-monospace">${p.no_urut}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                ${logo}
                                <span>${p.nama_partai}</span>
                            </div>
                        </td>
                        <td class="text-end">${Number(p.suara_partai).toLocaleString()}</td>
                        <td class="text-end">${Number(p.suara_caleg).toLocaleString()}</td>
                        <td class="text-end text-success">${Number(p.total_suara).toLocaleString()}</td>
                    </tr>
                `;
            });
            partiesBody.innerHTML = partyHtml;
        }

        // Render Candidates
        if (!data.candidates || data.candidates.length === 0) {
            candidatesBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-4">Tidak ada data caleg di wilayah/TPS ini.</td></tr>`;
        } else {
            let candidateHtml = '';
            let rank = 1;
            data.candidates.forEach(c => {
                const logo = c.lambang_partai 
                    ? `<img src="<?= base_url('uploads/partai/') ?>/${c.lambang_partai}" alt="${c.nama_partai}" style="height: 20px; width: 20px; object-fit: contain; margin-right: 6px;">`
                    : '';
                candidateHtml += `
                    <tr>
                        <td class="text-center fw-bold text-muted">${rank++}</td>
                        <td class="fw-semibold text-dark-coffee">${c.nama_caleg} <small class="text-muted">(No ${c.no_urut})</small></td>
                        <td>
                            <div class="d-flex align-items-center small text-muted">
                                ${logo}
                                <span>${c.nama_partai}</span>
                            </div>
                        </td>
                        <td class="text-end fw-bold text-success">${Number(c.total_suara).toLocaleString()} suara</td>
                    </tr>
                `;
            });
            candidatesBody.innerHTML = candidateHtml;
        }
    }
});
</script>

<style>
    .text-dark-coffee {
        color: #3e2723 !important;
    }
    .text-light-coffee {
        color: #d7ccc8 !important;
    }
    .bg-gold {
        background-color: #d4af37 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .text-cream {
        color: #fff8e1 !important;
    }
    .fs-7 {
        font-size: 0.8rem;
    }
    tr.clickable-rw-row:hover, tr.clickable-tps-row:hover {
        background-color: #f7f5f4 !important;
        transition: background-color 0.15s ease-in-out;
    }
    .nav-pills .nav-link.active {
        background-color: #3e2723 !important;
        color: #fff8e1 !important;
    }
    .nav-pills .nav-link {
        color: #3e2723;
        border: 1px solid #3e2723;
        margin: 0 4px;
    }
</style>
<?= $this->endSection() ?>
