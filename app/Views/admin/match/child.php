<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 mb-4 bg-light-cream">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h3 class="vintage-serif text-dark-coffee mb-0"><i class="fa-solid fa-children me-3 text-gold"></i>Tinjau Data Anak (Bipartite Matching)</h3>
                    <span class="badge bg-gold text-dark fs-6"><?= count($candidates) ?> Anak Menunggu Review</span>
                </div>
                <p class="text-muted mb-0">Halaman ini menampilkan anak-anak yang diinput ganda oleh ayah & ibu pada masing-masing form. Anda bisa menggabungkannya ke dalam satu profil anak bayangan (shadow profile) terhubung ke kedua orang tua, atau memisahkan/mengabaikan jika mereka adalah anak yang berbeda.</p>
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
                    <p class="text-muted">Tidak ada rekomendasi anak ganda yang perlu diverifikasi saat ini.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="d-flex flex-column gap-4">
                <?php foreach ($candidates as $c): ?>
                    <div class="card shadow-sm border-0 overflow-hidden match-card bg-white">
                        <div class="card-header bg-dark-coffee text-cream d-flex align-items-center justify-content-between py-3 px-4 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($c['score'] >= 85): ?>
                                    <span class="badge bg-success text-white px-3 py-2"><i class="fa-solid fa-check-double me-1"></i> Sangat mirip</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark px-3 py-2"><i class="fa-solid fa-triangle-exclamation me-1"></i> Perlu tinjauan</span>
                                <?php endif; ?>
                                <span class="fw-bold">Skor Kemiripan: <?= $c['score'] ?>%</span>
                            </div>
                            <div class="d-flex gap-2">
                                <form action="<?= base_url('admin/match/child/confirm/' . $c['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-gold px-3"><i class="fa-solid fa-code-merge me-1"></i> Gabungkan Data</button>
                                </form>
                                <form action="<?= base_url('admin/match/child/separate/' . $c['id']) ?>" method="POST" class="d-inline">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm btn-outline-light px-3"><i class="fa-solid fa-arrows-split-up-and-left me-1"></i> Pisahkan</button>
                                </form>
                            </div>
                        </div>
                        <div class="card-body p-4 bg-light-cream-gradient">
                            <div class="row g-4">
                                <?php if ($c['type'] === 'shadow_merge'): ?>
                                    <!-- Child A (Father's side) -->
                                    <div class="col-md-6 border-end border-gold-light">
                                        <div class="p-3 bg-white rounded shadow-sm h-100">
                                            <h5 class="fw-bold text-dark-coffee border-bottom pb-2 mb-3">
                                                <i class="fa-solid fa-mars me-2 text-primary"></i>Versi Data Ayah
                                                <small class="text-muted fs-7 d-block">Ayah: <?= $c['father']['nama_lengkap'] ?? 'Tidak Terbaca' ?></small>
                                            </h5>
                                            <table class="table table-sm table-borderless fs-7 mb-0">
                                                <tr>
                                                    <th width="35%" class="text-muted">Nama Lengkap</th>
                                                    <td class="fw-semibold">: <?= $c['member_a']['nama_lengkap'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Jenis Kelamin</th>
                                                    <td>: <?= $c['member_a']['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Tanggal Lahir</th>
                                                    <td>: <?= $c['member_a']['tanggal_lahir'] ?: 'Tidak diisi' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Sekolah / Univ</th>
                                                    <td>: <?= $c['member_a']['sekolah_universitas'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Jurusan</th>
                                                    <td>: <?= $c['member_a']['jurusan'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Pekerjaan</th>
                                                    <td>: <?= $c['member_a']['pekerjaan'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Hobi</th>
                                                    <td>: <?= $c['member_a']['hobi'] ?: '-' ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Child B (Mother's side) -->
                                    <div class="col-md-6">
                                        <div class="p-3 bg-white rounded shadow-sm h-100">
                                            <h5 class="fw-bold text-dark-coffee border-bottom pb-2 mb-3">
                                                <i class="fa-solid fa-venus me-2 text-danger"></i>Versi Data Ibu
                                                <small class="text-muted fs-7 d-block">Ibu: <?= $c['mother']['nama_lengkap'] ?? 'Tidak Terbaca' ?></small>
                                            </h5>
                                            <table class="table table-sm table-borderless fs-7 mb-0">
                                                <tr>
                                                    <th width="35%" class="text-muted">Nama Lengkap</th>
                                                    <td class="fw-semibold">: <?= $c['member_b']['nama_lengkap'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Jenis Kelamin</th>
                                                    <td>: <?= $c['member_b']['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Tanggal Lahir</th>
                                                    <td>: <?= $c['member_b']['tanggal_lahir'] ?: 'Tidak diisi' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Sekolah / Univ</th>
                                                    <td>: <?= $c['member_b']['sekolah_universitas'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Jurusan</th>
                                                    <td>: <?= $c['member_b']['jurusan'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Pekerjaan</th>
                                                    <td>: <?= $c['member_b']['pekerjaan'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Hobi</th>
                                                    <td>: <?= $c['member_b']['hobi'] ?: '-' ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Primary Child vs Raw data -->
                                    <div class="col-md-6 border-end border-gold-light">
                                        <div class="p-3 bg-white rounded shadow-sm h-100">
                                            <h5 class="fw-bold text-dark-coffee border-bottom pb-2 mb-3">
                                                <i class="fa-solid fa-id-card me-2 text-gold"></i>Profil Responden Aktif (A)
                                            </h5>
                                            <table class="table table-sm table-borderless fs-7 mb-0">
                                                <tr>
                                                    <th width="35%" class="text-muted">Nama Lengkap</th>
                                                    <td class="fw-semibold">: <?= $c['child']['nama_lengkap'] ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Jenis Kelamin</th>
                                                    <td>: <?= $c['child']['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Pekerjaan</th>
                                                    <td>: <?= $c['child']['pekerjaan'] ?: '-' ?></td>
                                                </tr>
                                                <tr>
                                                    <th class="text-muted">Pendidikan</th>
                                                    <td>: <?= $c['child']['pendidikan_terakhir'] ?: '-' ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-white rounded shadow-sm h-100">
                                            <h5 class="fw-bold text-dark-coffee border-bottom pb-2 mb-3">
                                                <i class="fa-solid fa-users-rectangle me-2 text-gold"></i>Data Orang Tua (B)
                                                <small class="text-muted fs-7 d-block">Orang Tua: <?= $c['parent']['nama_lengkap'] ?></small>
                                            </h5>
                                            <p class="fs-7 text-muted">Orang tua melaporkan memiliki anak dengan kemiripan nama sangat tinggi dengan responden aktif tersebut. Konfirmasi akan menghubungkan responden aktif ini sebagai anak dari orang tua tersebut.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
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
