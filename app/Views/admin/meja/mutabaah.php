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
                    <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Mutabaah Yaumiyah</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark-coffee mb-0">
                <i class="fa-solid fa-book-quran text-gold me-2"></i>Mutabaah Yaumiyah
            </h3>
        </div>
        <?php if (!empty($isLinked)): ?>
        <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
            <!-- Date Quick Selector Form -->
            <form action="<?= base_url('admin/meja/mutabaah') ?>" method="GET" class="d-flex align-items-center gap-2">
                <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= esc($tanggal ?? date('Y-m-d')) ?>" max="<?= date('Y-m-d') ?>" onchange="this.form.submit()">
                <button type="submit" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </form>
            <a href="<?= base_url('admin/meja/mutabaah?tanggal=' . date('Y-m-d')) ?>" class="btn btn-gold btn-sm fw-semibold shadow-sm">
                <i class="fa-solid fa-calendar-day me-1"></i>Hari Ini
            </a>
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
                Untuk dapat mengisi dan mencatat Mutaba'ah Yaumiyah pribadi, silakan tautkan akun Anda ke data profil anggota terlebih dahulu.
            </p>
            <div>
                <a href="<?= base_url('admin/meja/profile') ?>" class="btn btn-gold fw-semibold">
                    <i class="fa-solid fa-link me-1"></i>Buka Halaman Profil & Tautkan Akun
                </a>
            </div>
        </div>
    <?php else: ?>

        <!-- Date Header Banner -->
        <?php
            $hariNames = [
                'Sunday' => 'Ahad', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 
                'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
            ];
            $dayName = $hariNames[date('l', strtotime($tanggal))] ?? date('l', strtotime($tanggal));
            $formattedDate = $dayName . ', ' . date('d', strtotime($tanggal)) . ' ' . $namaBulan . ' ' . date('Y', strtotime($tanggal));
            $isToday = ($tanggal === date('Y-m-d'));
        ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e140d 0%, #3e2718 100%);">
            <div class="card-body p-4 text-white d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-gold bg-opacity-20 p-3 text-gold border border-gold border-opacity-25 shadow-sm">
                        <i class="fa-solid fa-calendar-check fa-2x"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <h5 class="fw-bold mb-0 text-white"><?= $formattedDate ?></h5>
                            <?php if ($isToday): ?>
                                <span class="badge bg-gold text-dark px-2 py-1 small fw-semibold">Hari Ini</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-gold opacity-75">
                            Lembar evaluasi ibadah harian pribadi &bull; Anggota: <?= esc($member['nama_lengkap']) ?>
                        </small>
                    </div>
                </div>

                <div class="text-md-end">
                    <span class="badge bg-white bg-opacity-10 text-cream px-3 py-2 border border-white border-opacity-10">
                        <i class="fa-solid fa-user-shield me-1 text-gold"></i>Data Privat (Hanya Anda yang Dapat Melihat)
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Left Column: Daily Mutabaah Checklist Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark-coffee mb-0">
                            <i class="fa-solid fa-list-check text-gold me-2"></i>Checklist Amalan Harian
                        </h6>
                        <span class="small text-muted">Tanggal: <?= date('d/m/Y', strtotime($tanggal)) ?></span>
                    </div>

                    <form action="<?= base_url('admin/meja/mutabaah/save') ?>" method="POST">
                        <?= csrf_field() ?>
                        <input type="hidden" name="tanggal" value="<?= esc($tanggal) ?>">

                        <div class="card-body p-4">
                            <?php
                                $logItemValues = $currentLog['items'] ?? [];
                            ?>

                            <div class="d-flex flex-column gap-3">
                                <?php if (!empty($items)): ?>
                                    <?php foreach ($items as $idx => $item): 
                                        $val = $logItemValues[$item['id']] ?? '';
                                    ?>
                                        <div class="p-3 rounded-3 border bg-light bg-opacity-50 hover-shadow transition-all">
                                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                                <div class="d-flex align-items-center gap-3">
                                                    <span class="badge bg-gold text-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                        <?= $idx + 1 ?>
                                                    </span>
                                                    <div>
                                                        <div class="fw-semibold text-dark-coffee"><?= esc($item['nama_item']) ?></div>
                                                        <small class="text-muted"><i class="fa-solid fa-tag me-1 text-gold"></i><?= esc($item['kategori'] ?? 'Amalan') ?></small>
                                                    </div>
                                                </div>

                                                <div class="ms-auto">
                                                    <?php if ($item['tipe_input'] === 'boolean'): ?>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <input type="radio" class="btn-check" name="items[<?= $item['id'] ?>]" id="item_<?= $item['id'] ?>_yes" value="1" <?= ($val === '1' || strtolower($val) === 'ya') ? 'checked' : '' ?>>
                                                            <label class="btn btn-outline-success px-3 fw-semibold" for="item_<?= $item['id'] ?>_yes">
                                                                <i class="fa-solid fa-check me-1"></i>Ya
                                                            </label>

                                                            <input type="radio" class="btn-check" name="items[<?= $item['id'] ?>]" id="item_<?= $item['id'] ?>_no" value="0" <?= ($val === '0' || $val === '' || strtolower($val) === 'tidak') ? 'checked' : '' ?>>
                                                            <label class="btn btn-outline-secondary px-3" for="item_<?= $item['id'] ?>_no">
                                                                <i class="fa-solid fa-xmark me-1"></i>Belum
                                                            </label>
                                                        </div>
                                                    <?php elseif ($item['tipe_input'] === 'angka'): ?>
                                                        <div class="input-group input-group-sm" style="width: 140px;">
                                                            <input type="number" step="any" min="0" name="items[<?= $item['id'] ?>]" class="form-control text-center fw-bold" value="<?= esc($val !== '' ? $val : 0) ?>">
                                                            <span class="input-group-text bg-white text-muted small">
                                                                <?= str_contains(strtolower($item['nama_item']), 'tilawah') ? 'Hlm' : (str_contains(strtolower($item['nama_item']), 'olahraga') ? 'Mnt' : 'Qty') ?>
                                                            </span>
                                                        </div>
                                                    <?php else: ?>
                                                        <input type="text" name="items[<?= $item['id'] ?>]" class="form-control form-control-sm" value="<?= esc($val) ?>" placeholder="Nilai">
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info py-2 small mb-0">Belum ada item amalan mutabaah yang aktif.</div>
                                <?php endif; ?>

                                <!-- Catatan / Refleksi Diri -->
                                <div class="mt-2">
                                    <label class="form-label small fw-semibold text-dark-coffee">
                                        <i class="fa-solid fa-pen-nib text-gold me-1"></i>Catatan & Evaluasi Diri (Opsional)
                                    </label>
                                    <textarea name="catatan" rows="3" class="form-control form-control-sm" placeholder="Catatan renungan harian, dzikir khusus, kendala atau rasa syukur..."><?= esc($currentLog['catatan'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
                            <span class="small text-muted">
                                <?php if (!empty($currentLog['updated_at'])): ?>
                                    <i class="fa-solid fa-clock me-1 text-gold"></i>Terakhir diperbarui: <?= date('d/m/Y H:i', strtotime($currentLog['updated_at'])) ?>
                                <?php else: ?>
                                    <i class="fa-solid fa-circle-info me-1 text-muted"></i>Belum disimpan untuk tanggal ini
                                <?php endif; ?>
                            </span>

                            <button type="submit" class="btn btn-gold btn-sm fw-semibold px-4 shadow-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Mutabaah
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Monthly Recap & Consistency Stats -->
            <div class="col-lg-5">
                <!-- Summary Card Month -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-dark-coffee mb-3">
                        <i class="fa-solid fa-chart-pie text-gold me-2"></i>Rekap Mutabaah Bulan <?= esc($namaBulan) ?> <?= esc($tahun) ?>
                    </h6>

                    <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-light mb-3">
                        <div>
                            <span class="text-muted small d-block">Hari Terisi</span>
                            <span class="fs-4 fw-bold text-dark-coffee"><?= $monthlyRecap['days_filled_count'] ?> / <?= $monthlyRecap['days_in_month'] ?> Hari</span>
                        </div>
                        <div class="text-end">
                            <span class="fs-3 fw-bold text-gold"><?= $monthlyRecap['percentage_filled'] ?>%</span>
                            <small class="text-muted d-block">Konsistensi</small>
                        </div>
                    </div>

                    <div class="progress mb-4" style="height: 10px;">
                        <div class="progress-bar bg-gold" role="progressbar" style="width: <?= $monthlyRecap['percentage_filled'] ?>%;" aria-valuenow="<?= $monthlyRecap['percentage_filled'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <h6 class="fw-bold text-dark-coffee text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-list-check text-gold me-1"></i>Ketercapaian Per Amalan (Bulan Ini)
                    </h6>

                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($monthlyRecap['items'] as $item): 
                            $stat = $monthlyRecap['item_stats'][$item['id']] ?? null;
                            $count = $stat['total_terisi'] ?? 0;
                            $totalDays = $monthlyRecap['days_in_month'];
                            $pct = round(($count / $totalDays) * 100, 1);
                        ?>
                            <div>
                                <div class="d-flex justify-content-between align-items-center mb-1 small">
                                    <span class="fw-semibold text-dark"><?= esc($item['nama_item']) ?></span>
                                    <span class="text-muted fw-semibold">
                                        <?php if ($item['tipe_input'] === 'boolean'): ?>
                                            <?= $count ?> hari (<?= $pct ?>%)
                                        <?php else: ?>
                                            <?= $stat['total_nilai'] ?? 0 ?> akumulasi (<?= $count ?> hari)
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-gold" role="progressbar" style="width: <?= min(100, $pct) ?>%;"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Motivation / Hadith Quote Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 text-white" style="background: linear-gradient(135deg, #2d1e13 0%, #4a3424 100%);">
                    <div class="d-flex gap-3">
                        <i class="fa-solid fa-quote-left text-gold fa-2x opacity-50"></i>
                        <div>
                            <p class="fst-italic mb-2 small text-cream">
                                "Amalan yang paling dicintai oleh Allah adalah amalan yang berkelanjutan (konsisten) walaupun sedikit."
                            </p>
                            <small class="text-gold fw-semibold">&mdash; HR. Muslim No. 782</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
