<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4 bg-light-cream">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="vintage-serif text-dark-coffee mb-0"><i class="fa-solid fa-user-friends me-3 text-gold"></i>Tinjau Pasangan (Fuzzy Matching)</h3>
                    <span class="badge bg-gold text-dark fs-6"><?= count($candidates) ?> Kandidat Menunggu Review</span>
                </div>
                <p class="text-muted mb-0">Halaman ini menampilkan rekomendasi pencocokan pasangan suami-istri yang diinput dari kuesioner Excel responden yang terpisah. Silakan lakukan verifikasi manual di bawah ini.</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?php if (empty($candidates)): ?>
            <div class="card shadow-sm border-0 text-center p-5 bg-white">
                <div class="card-body">
                    <i class="fa-solid fa-circle-check text-gold fa-4x mb-3"></i>
                    <h4 class="fw-bold text-dark-coffee">Semua Bersih!</h4>
                    <p class="text-muted">Tidak ada kandidat pasangan yang membutuhkan tinjauan saat ini.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($candidates as $c): ?>
                    <div class="card shadow-sm border-0 overflow-hidden match-card bg-white">
                        <div class="card-header bg-dark-coffee text-cream d-flex align-items-center justify-content-between py-3 px-4 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($c['score'] >= 90): ?>
                                    <span class="badge bg-success text-white px-3 py-2"><i class="fa-solid fa-check-double me-1"></i> Kemungkinan besar cocok</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark px-3 py-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Perlu ditinjau</span>
                                <?php endif; ?>
                                <span class="fw-bold">Skor Kecocokan: <?= $c['score'] ?>%</span>
                            </div>
                            <div class="d-flex gap-2">
                                <form action="<?= base_url('admin/match/spouse/confirm/' . $c['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-gold px-3"><i class="fa-solid fa-link me-1"></i> Konfirmasi Tautan</button>
                                </form>
                                <form action="<?= base_url('admin/match/spouse/reject/' . $c['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-light px-3"><i class="fa-solid fa-unlink me-1"></i> Abaikan</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-light-cream-gradient">
                            <div class="row g-4">
                                <!-- Member A side -->
                                <div class="col-md-6 border-end border-gold-light">
                                    <div class="p-3 bg-white rounded shadow-sm h-100">
                                        <h5 class="fw-bold text-dark-coffee border-bottom pb-2 mb-3"><i class="fa-solid fa-user me-2 text-gold"></i>Responden Utama (A)</h5>
                                        <table class="table table-sm table-borderless fs-7 mb-0">
                                            <tr>
                                                <th width="35%" class="text-muted">Nama Lengkap</th>
                                                <td class="fw-semibold">: <?= $c['member_a']['nama_lengkap'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Jenis Kelamin</th>
                                                <td>: <?= $c['member_a']['jenis_kelamin'] === 'L' ? 'Laki-laki (Suami)' : 'Perempuan (Istri)' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Alamat</th>
                                                <td>: <?= $c['member_a']['alamat_lengkap'] ?: '-' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">RT / RW</th>
                                                <td>: RT <?= $c['member_a']['rt'] ?: '-' ?> / RW <?= $c['member_a']['rw'] ?: '-' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Kelurahan</th>
                                                <td>: <?= $c['member_a']['kelurahan'] ?: '-' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Kecamatan</th>
                                                <td>: <?= $c['member_a']['kecamatan'] ?: '-' ?></td>
                                            </tr>
                                            <tr class="bg-light-cream-alpha mt-2">
                                                <th class="text-gold fw-bold">Nama Pasangan Raw</th>
                                                <td class="text-gold fw-bold">: <?= $c['raw_spouse_name'] ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <!-- Member B side -->
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded shadow-sm h-100">
                                        <h5 class="fw-bold text-dark-coffee border-bottom pb-2 mb-3"><i class="fa-solid fa-user me-2 text-gold"></i>Kandidat Pasangan Terdeteksi (B)</h5>
                                        <table class="table table-sm table-borderless fs-7 mb-0">
                                            <tr>
                                                <th width="35%" class="text-muted">Nama Lengkap</th>
                                                <td class="fw-semibold">: <?= $c['member_b']['nama_lengkap'] ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Jenis Kelamin</th>
                                                <td>: <?= $c['member_b']['jenis_kelamin'] === 'L' ? 'Laki-laki (Suami)' : 'Perempuan (Istri)' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Alamat</th>
                                                <td>: <?= $c['member_b']['alamat_lengkap'] ?: '-' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">RT / RW</th>
                                                <td>: RT <?= $c['member_b']['rt'] ?: '-' ?> / RW <?= $c['member_b']['rw'] ?: '-' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Kelurahan</th>
                                                <td>: <?= $c['member_b']['kelurahan'] ?: '-' ?></td>
                                            </tr>
                                            <tr>
                                                <th class="text-muted">Kecamatan</th>
                                                <td>: <?= $c['member_b']['kecamatan'] ?: '-' ?></td>
                                            </tr>
                                            <tr class="bg-light-cream-alpha mt-2">
                                                <th class="text-gold fw-bold">Nama Pasangan (B)</th>
                                                <td class="text-gold fw-bold">: <?= $c['member_b']['spouse_member_id'] ? 'Sudah Tertaut' : 'Belum Tertaut' ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .bg-dark-coffee {
        background-color: #3e2723 !important;
    }
    .text-cream {
        color: #efebe9 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .bg-gold {
        background-color: #d4af37 !important;
    }
    .btn-gold {
        background-color: #d4af37;
        color: #3e2723;
        font-weight: 600;
        border: none;
    }
    .btn-gold:hover {
        background-color: #c5a028;
        color: #3e2723;
    }
    .bg-light-cream {
        background-color: #efebe9 !important;
    }
    .bg-light-cream-gradient {
        background: linear-gradient(135deg, #efebe9 0%, #d7ccc8 100%);
    }
    .border-gold-light {
        border-color: rgba(212, 175, 55, 0.3) !important;
    }
    .fs-7 {
        font-size: 0.875rem;
    }
    .vintage-serif {
        font-family: 'Playfair Display', Georgia, serif;
    }
    .match-card {
        border-radius: 8px;
        transition: transform 0.2s;
    }
    .match-card:hover {
        transform: translateY(-2px);
    }
</style>
<?= $this->endSection() ?>
