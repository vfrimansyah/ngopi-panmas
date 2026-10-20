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
                    <li class="breadcrumb-item active text-gold fw-semibold" aria-current="page">Profil Saya</li>
                </ol>
            </nav>
            <h3 class="fw-bold text-dark-coffee mb-0">
                <i class="fa-solid fa-id-card text-gold me-2"></i>Profil Saya
            </h3>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <?php if ($isLinked && $member): ?>
                <a href="<?= base_url('profil') ?>" target="_blank" class="btn btn-outline-secondary btn-sm shadow-sm">
                    <i class="fa-solid fa-external-link me-1"></i>Lihat Tampilan Publik
                </a>
                <button type="button" class="btn btn-gold btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalEditProfile">
                    <i class="fa-solid fa-user-pen me-1"></i>Perbarui Profil
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isLinked): ?>
        <!-- UNLINKED ACCOUNT NOTICE & CLAIM CARD -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-body p-4 p-md-5 text-center">
                        <div class="mb-4">
                            <div class="d-inline-flex p-3 rounded-circle bg-warning bg-opacity-10 text-warning mb-3">
                                <i class="fa-solid fa-link-slash fa-3x"></i>
                            </div>
                            <h4 class="fw-bold text-dark-coffee mb-2">Akun Belum Tertaut ke Data Anggota</h4>
                            <p class="text-muted mx-auto" style="max-width: 520px;">
                                Akun login Anda (<strong><?= esc($user->username ?? '') ?></strong>) saat ini belum terhubung dengan salah satu profil anggota di database komunitas PANMAS.
                            </p>
                        </div>

                        <div class="card bg-light border-0 rounded-3 p-4 text-start mx-auto" style="max-width: 600px;">
                            <h6 class="fw-bold text-dark-coffee mb-3">
                                <i class="fa-solid fa-link text-gold me-2"></i>Tautkan ke Profil Anggota Anda:
                            </h6>
                            <form action="<?= base_url('admin/meja/profile/link') ?>" method="POST">
                                <?= csrf_field() ?>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Pilih Nama Anggota</label>
                                    <select name="member_id" class="form-select select2-basic" required>
                                        <option value="">-- Cari atau Pilih Nama Anda --</option>
                                        <?php if (!empty($unlinkedMembers)): ?>
                                            <?php foreach ($unlinkedMembers as $um): ?>
                                                <option value="<?= $um['id'] ?>">
                                                    <?= esc($um['nama_lengkap']) ?> (UPA: <?= esc($um['kode_upa'] ?? '-') ?> | <?= esc($um['status_anggota'] ?? 'Anggota') ?>)<?= !empty($um['linked_username']) ? ' — [Tertaut: ' . esc($um['linked_username']) . ']' : '' ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <div class="form-text small text-muted">
                                        Setelah akun Anda ditautkan, data profil, rekapitulasi mutaba'ah harian, serta riwayat iuran Anda akan otomatis aktif.
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-gold fw-semibold">
                                        <i class="fa-solid fa-check me-2"></i>Konfirmasi Penautan Akun
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- LINKED PROFILE DETAIL -->
        <div class="row g-4">
            <!-- Left Column: Identity & Card Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #1e140d 0%, #3e2718 100%);">
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle border border-3 border-gold d-flex align-items-center justify-content-center shadow" 
                                 style="width: 100px; height: 100px; background: rgba(255,255,255,0.1); margin: 0 auto; overflow: hidden;">
                                <?php if (!empty($member['foto_profil'])): ?>
                                    <img src="<?= base_url('uploads/' . $member['foto_profil']) ?>" alt="<?= esc($member['nama_lengkap']) ?>" class="w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <span class="fs-1 fw-bold text-gold">
                                        <?= mb_strtoupper(mb_substr($member['nama_lengkap'] ?? 'A', 0, 1)) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <h5 class="fw-bold text-white mb-1"><?= esc($member['nama_lengkap']) ?></h5>
                        <p class="text-gold mb-2 small fw-medium">
                            <i class="fa-solid fa-location-dot me-1"></i>UPA <?= esc($member['kode_upa'] ?? '-') ?>
                        </p>

                        <div class="d-flex justify-content-center gap-2 mt-2">
                            <span class="badge bg-gold text-dark px-3 py-2 rounded-pill small fw-semibold">
                                <i class="fa-solid fa-star me-1"></i><?= esc($member['status_anggota'] ?? 'Anggota') ?>
                            </span>
                            <?php if (!empty($member['jenis_kelamin'])): ?>
                                <span class="badge bg-white bg-opacity-25 text-white px-3 py-2 rounded-pill small">
                                    <?= $member['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <h6 class="fw-bold text-dark-coffee text-uppercase mb-3" style="letter-spacing: 0.5px; font-size: 0.78rem;">
                            <i class="fa-solid fa-address-book text-gold me-2"></i>Informasi Kontak & Domisili
                        </h6>

                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small text-muted">
                            <li class="d-flex align-items-start">
                                <i class="fa-solid fa-envelope text-gold mt-1 me-3" style="width: 16px;"></i>
                                <div>
                                    <div class="fw-semibold text-dark">Email</div>
                                    <div><?= !empty($member['email']) ? esc($member['email']) : '<span class="text-muted fst-italic">Belum diisi</span>' ?></div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fa-brands fa-whatsapp text-gold mt-1 me-3" style="width: 16px;"></i>
                                <div>
                                    <div class="fw-semibold text-dark">No. Handphone / WA</div>
                                    <div>
                                        <?php if (!empty($member['handphone'])): ?>
                                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $member['handphone']) ?>" target="_blank" class="text-decoration-none text-primary">
                                                <?= esc($member['handphone']) ?> <i class="fa-solid fa-arrow-up-right-from-square fa-2xs"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Belum diisi</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fa-solid fa-map-location-dot text-gold mt-1 me-3" style="width: 16px;"></i>
                                <div>
                                    <div class="fw-semibold text-dark">Alamat Lengkap</div>
                                    <div>
                                        <?= esc($member['alamat_lengkap'] ?? '-') ?>
                                        <?php if (!empty($member['rt']) || !empty($member['rw'])): ?>
                                            <br><small class="text-muted">RT <?= esc($member['rt'] ?? '-') ?> / RW <?= esc($member['rw'] ?? '-') ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                            <li class="d-flex align-items-start">
                                <i class="fa-solid fa-heart text-gold mt-1 me-3" style="width: 16px;"></i>
                                <div>
                                    <div class="fw-semibold text-dark">Status Pernikahan</div>
                                    <div><?= esc($member['status_perkawinan'] ?? '-') ?></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Keaktifan Kegiatan Card -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 p-3 bg-gold bg-opacity-10 text-gold">
                            <i class="fa-solid fa-calendar-check fa-2x"></i>
                        </div>
                        <div>
                            <?php
                                $hadirCount = !empty($kegiatanHistory) ? count(array_filter($kegiatanHistory, fn($k) => ($k['status'] ?? '') === 'hadir')) : 0;
                            ?>
                            <h3 class="fw-bold text-dark-coffee mb-0"><?= $hadirCount ?></h3>
                            <small class="text-muted">Kegiatan Dihadiri</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Tabs (Pendidikan, Pekerjaan, Organisasi, Kegiatan) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white border-bottom p-3">
                        <ul class="nav nav-pills card-header-pills" id="profileTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-semibold" id="pekerjaan-tab" data-bs-toggle="tab" data-bs-target="#pekerjaan" type="button" role="tab">
                                    <i class="fa-solid fa-briefcase me-2"></i>Pekerjaan & Pendidikan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="organisasi-tab" data-bs-toggle="tab" data-bs-target="#organisasi" type="button" role="tab">
                                    <i class="fa-solid fa-sitemap me-2"></i>Organisasi & Keahlian
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="kegiatan-tab" data-bs-toggle="tab" data-bs-target="#kegiatan" type="button" role="tab">
                                    <i class="fa-solid fa-list-check me-2"></i>Riwayat Kegiatan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-semibold" id="survei-tab" data-bs-toggle="tab" data-bs-target="#survei" type="button" role="tab">
                                    <i class="fa-solid fa-heart-pulse me-2"></i>Skrining & Survei
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="profileTabContent">
                            <!-- TAB 1: PEKERJAAN & PENDIDIKAN -->
                            <div class="tab-pane fade show active" id="pekerjaan" role="tabpanel">
                                <h6 class="fw-bold text-dark-coffee mb-3">Informasi Pekerjaan Saat Ini</h6>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Pekerjaan</small>
                                            <span class="fw-semibold text-dark"><?= esc($member['pekerjaan'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Nama Instansi / Perusahaan</small>
                                            <span class="fw-semibold text-dark"><?= esc($member['nama_instansi'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Pendidikan Terakhir</small>
                                            <span class="fw-semibold text-dark"><?= esc($member['pendidikan_terakhir'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Bidang Pekerjaan</small>
                                            <span class="fw-semibold text-dark"><?= esc($member['bidang_pekerjaan'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold text-dark-coffee mb-3">Riwayat Pendidikan</h6>
                                <?php if (!empty($educations)): ?>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-sm align-middle small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 140px;">Jenjang</th>
                                                    <th>Nama Institusi / Sekolah</th>
                                                    <th style="width: 160px;">Periode / Tahun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($educations as $edu): ?>
                                                    <tr>
                                                        <td><span class="badge bg-secondary"><?= esc($edu['jenjang'] ?? '-') ?></span></td>
                                                        <td class="fw-semibold text-dark-coffee"><?= esc($edu['nama_institusi'] ?? '-') ?></td>
                                                        <td><?= esc($edu['rentang_tahun'] ?? '-') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small fst-italic mb-4">Belum ada riwayat pendidikan yang dicatat.</p>
                                <?php endif; ?>

                                <h6 class="fw-bold text-dark-coffee mb-3">Pengalaman Kerja</h6>
                                <?php if (!empty($workExperiences)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Perusahaan / Instansi</th>
                                                    <th>Posisi / Jabatan</th>
                                                    <th style="width: 160px;">Periode / Rentang Tahun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($workExperiences as $work): ?>
                                                    <tr>
                                                        <td class="fw-semibold text-dark-coffee"><?= esc($work['nama_perusahaan'] ?? '-') ?></td>
                                                        <td><?= esc($work['jabatan'] ?? '-') ?></td>
                                                        <td><?= esc($work['rentang_tahun'] ?? '-') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small fst-italic">Belum ada pengalaman kerja yang dicatat.</p>
                                <?php endif; ?>
                            </div>

                            <!-- TAB 2: ORGANISASI & KEAHLIAN -->
                            <div class="tab-pane fade" id="organisasi" role="tabpanel">
                                <h6 class="fw-bold text-dark-coffee mb-3">Riwayat Keaktifan Organisasi</h6>
                                <?php if (!empty($organizations)): ?>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-sm align-middle small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Organisasi</th>
                                                    <th>Jabatan / Peran</th>
                                                    <th style="width: 160px;">Periode / Rentang Tahun</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($organizations as $org): ?>
                                                    <tr>
                                                        <td class="fw-semibold text-dark-coffee"><?= esc($org['nama_organisasi'] ?? '-') ?></td>
                                                        <td><?= esc($org['jabatan'] ?? '-') ?></td>
                                                        <td><?= esc($org['rentang_tahun'] ?? '-') ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted small fst-italic mb-4">Belum ada data organisasi yang dicatat.</p>
                                <?php endif; ?>

                                <h6 class="fw-bold text-dark-coffee mb-3">Keahlian & Hobi</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Keahlian / Skill</small>
                                            <span class="fw-semibold text-dark"><?= esc($member['keahlian'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-3">
                                            <small class="text-muted d-block mb-1">Hobi / Minat</small>
                                            <span class="fw-semibold text-dark"><?= esc($member['hobi'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: RIWAYAT KEGIATAN -->
                            <div class="tab-pane fade" id="kegiatan" role="tabpanel">
                                <h6 class="fw-bold text-dark-coffee mb-3">Histori Kehadiran Kegiatan PANMAS</h6>
                                <?php if (!empty($kegiatanHistory)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-sm align-middle small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Kegiatan</th>
                                                    <th>Tanggal</th>
                                                    <th>Tempat</th>
                                                    <th>Status Kehadiran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($kegiatanHistory as $k): ?>
                                                    <tr>
                                                        <td class="fw-semibold"><?= esc($k['nama_kegiatan'] ?? '-') ?></td>
                                                        <td><?= !empty($k['tanggal']) ? date('d/m/Y', strtotime($k['tanggal'])) : '-' ?></td>
                                                        <td><?= esc($k['lokasi_kegiatan'] ?? $k['tempat'] ?? '-') ?></td>
                                                        <td>
                                                            <?php if (($k['status'] ?? '') === 'hadir'): ?>
                                                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Hadir</span>
                                                            <?php elseif (($k['status'] ?? '') === 'izin'): ?>
                                                                <span class="badge bg-warning text-dark">Izin</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Tidak Hadir</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="p-4 text-center text-muted bg-light rounded-3">
                                        <i class="fa-solid fa-calendar-xmark fa-2x mb-2 text-muted"></i>
                                        <p class="mb-0 small">Belum ada riwayat absensi kegiatan yang tercatat untuk akun Anda.</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- TAB 4: SKRINING & SURVEI -->
                            <div class="tab-pane fade" id="survei" role="tabpanel">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="fw-bold text-dark-coffee mb-0">Riwayat Skrining Kesehatan & Formulir Terisi</h6>
                                    <a href="<?= base_url('form/skrining-kesehatan') ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="fa-solid fa-plus me-1"></i>Isi Skrining Baru
                                    </a>
                                </div>

                                <?php if (!empty($surveyHistory)): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered table-sm align-middle small">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Formulir</th>
                                                    <th>Waktu Pengisian</th>
                                                    <th>Status Tautan</th>
                                                    <th class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($surveyHistory as $sh): ?>
                                                    <tr>
                                                        <td class="fw-semibold text-dark-coffee"><?= esc($sh['form_title'] ?? 'Formulir') ?></td>
                                                        <td><?= date('d M Y, H:i', strtotime($sh['created_at'])) ?> WIB</td>
                                                        <td>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                                <i class="fa-solid fa-check me-1"></i><?= $sh['link_status'] ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url('form/' . ($sh['form_slug'] ?? 'skrining-kesehatan')) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-0">
                                                                <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Form
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="p-4 text-center text-muted bg-light rounded-3">
                                        <i class="fa-solid fa-heart-pulse fa-2x mb-2 text-warning opacity-75"></i>
                                        <p class="mb-2 small">Anda belum pernah mengisi skrining kesehatan berkala.</p>
                                        <a href="<?= base_url('form/skrining-kesehatan') ?>" target="_blank" class="btn btn-sm btn-gold">
                                            <i class="fa-solid fa-heart-pulse me-1"></i>Isi Skrining Kesehatan Sekarang
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT PROFILE -->
        <div class="modal fade" id="modalEditProfile" tabindex="-1" aria-labelledby="modalEditProfileLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow rounded-4">
                    <div class="modal-header bg-dark-coffee text-white border-0 py-3 px-4">
                        <h5 class="modal-title fw-bold" id="modalEditProfileLabel">
                            <i class="fa-solid fa-user-pen text-gold me-2"></i>Perbarui Data Profil Pribadi
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="<?= base_url('admin/meja/profile/update') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <!-- Foto Profil -->
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Foto Profil Baru (Opsional)</label>
                                    <input type="file" name="foto_profil" class="form-control" accept="image/*">
                                    <div class="form-text small">Format JPG, PNG, atau WEBP. Maks 2MB.</div>
                                </div>

                                <!-- Email & Phone -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Alamat Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($member['email'] ?? '') ?>" placeholder="contoh@email.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nomor Handphone / WhatsApp</label>
                                    <input type="text" name="handphone" class="form-control" value="<?= esc($member['handphone'] ?? '') ?>" placeholder="0812xxxxxxxx">
                                </div>

                                <!-- Alamat, RT, RW -->
                                <div class="col-md-8">
                                    <label class="form-label small fw-semibold">Alamat Lengkap</label>
                                    <input type="text" name="alamat_lengkap" class="form-control" value="<?= esc($member['alamat_lengkap'] ?? '') ?>" placeholder="Nama jalan, nomor rumah">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold">RT</label>
                                    <input type="text" name="rt" class="form-control" value="<?= esc($member['rt'] ?? '') ?>" placeholder="01">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-semibold">RW</label>
                                    <input type="text" name="rw" class="form-control" value="<?= esc($member['rw'] ?? '') ?>" placeholder="02">
                                </div>

                                <!-- Pekerjaan & Instansi -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Pekerjaan</label>
                                    <input type="text" name="pekerjaan" class="form-control" value="<?= esc($member['pekerjaan'] ?? '') ?>" placeholder="Pekerjaan">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nama Instansi / Perusahaan</label>
                                    <input type="text" name="nama_instansi" class="form-control" value="<?= esc($member['nama_instansi'] ?? '') ?>" placeholder="Instansi / Kantor">
                                </div>

                                <!-- Pendidikan Terakhir -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Pendidikan Terakhir</label>
                                    <select name="pendidikan_terakhir" class="form-select">
                                        <option value="">-- Pilih Pendidikan --</option>
                                        <?php
                                            $eduOptions = ['SMP / Sederajat', 'SMA / SMK / Sederajat', 'D3 / Diploma', 'S1 / Sarjana', 'S2 / Magister', 'S3 / Doktor'];
                                            foreach ($eduOptions as $eo):
                                        ?>
                                            <option value="<?= $eo ?>" <?= ($member['pendidikan_terakhir'] ?? '') === $eo ? 'selected' : '' ?>><?= $eo ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Keahlian & Hobi -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Keahlian Utama</label>
                                    <input type="text" name="keahlian" class="form-control" value="<?= esc($member['keahlian'] ?? '') ?>" placeholder="Keahlian / Skill">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-semibold">Hobi / Minat</label>
                                    <input type="text" name="hobi" class="form-control" value="<?= esc($member['hobi'] ?? '') ?>" placeholder="Hobi atau minat khusus">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 py-3 px-4">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-gold btn-sm fw-semibold">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
