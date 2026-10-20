<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-people-group me-2 text-gold"></i>Detail Group UPA: <?= esc($upa['kode_upa']) ?>
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-secondary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/upa') ?>" class="text-secondary text-decoration-none">Group UPA</a></li>
                <li class="breadcrumb-item active text-dark-coffee fw-medium" aria-current="page"><?= esc($upa['nama_group'] ?: $upa['kode_upa']) ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/upa') ?>" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i>Daftar Group
        </a>
        <?php if ($hasWriteAccess): ?>
            <a href="<?= base_url('admin/upa/edit/' . $upa['kode_upa']) ?>" class="btn btn-outline-warning">
                <i class="fa-solid fa-pen-to-square me-1"></i>Edit Group
            </a>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-xmark me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- UPA METADATA CARD -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #efebe9 0%, #ffffff 100%);">
    <div class="card-body p-4">
        <div class="row g-4 align-items-center">
            <div class="col-md-3 border-end-md">
                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Group UPA</small>
                <div class="d-flex align-items-baseline gap-2">
                    <h3 class="fw-bold text-dark-coffee font-monospace mb-0"><?= esc($upa['kode_upa']) ?></h3>
                    <?php if (($upa['is_active'] ?? 1) == 1): ?>
                        <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Non-Aktif</span>
                    <?php endif; ?>
                </div>
                <h6 class="fw-bold text-dark-coffee mt-1 mb-0"><?= esc($upa['nama_group'] ?: ('Group ' . $upa['kode_upa'])) ?></h6>
            </div>
            <div class="col-md-3 border-end-md">
                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Pembimbing (Naqib) Saat Ini</small>
                <?php if (!empty($upa['nama_pembimbing'])): ?>
                    <h6 class="fw-bold text-dark-coffee mb-1">
                        <i class="fa-solid fa-chalkboard-user text-gold me-1"></i><?= esc($upa['nama_pembimbing']) ?>
                    </h6>
                    <div>
                        <span class="badge bg-gold text-dark" style="font-size:0.75rem;"><?= esc($upa['jenjang_pembimbing']) ?></span>
                        <?php if (!empty($upa['hp_pembimbing'])): ?>
                            <small class="text-muted ms-2"><i class="fa-solid fa-phone me-1"></i><?= esc($upa['hp_pembimbing']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <h6 class="text-muted mb-0"><i class="fa-solid fa-user-slash me-2 opacity-50"></i>Belum ditentukan</h6>
                <?php endif; ?>
            </div>
            <div class="col-md-3 border-end-md">
                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Wilayah Pembinaan</small>
                <h6 class="fw-bold text-dark-coffee mb-0">
                    <i class="fa-solid fa-map-location-dot text-gold me-1"></i>Kel. <?= esc($upa['kelurahan']) ?>
                </h6>
                <small class="text-muted">Kec. <?= esc($upa['kecamatan']) ?></small>
            </div>
            <div class="col-md-3">
                <small class="text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.75rem;">Jadwal & Tempat</small>
                <small class="d-block text-dark fw-semibold"><i class="fa-regular fa-clock me-1 text-gold"></i><?= esc($upa['jadwal_rutin'] ?: 'Belum diatur') ?></small>
                <small class="d-block text-muted"><i class="fa-solid fa-location-dot me-1 text-secondary"></i><?= esc($upa['tempat_pertemuan'] ?: '-') ?></small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- LEFT PANEL: TABS FOR MEMBERS, ALUMNI, MENTOR HISTORIES -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-header bg-white border-bottom pt-3 px-4 pb-0">
                <ul class="nav nav-tabs card-header-tabs" id="upaTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-dark-coffee" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif" type="button" role="tab" aria-controls="aktif" aria-selected="true">
                            <i class="fa-solid fa-users me-1 text-gold"></i>Anggota Aktif
                            <span class="badge bg-primary rounded-pill ms-1"><?= count($upaMembers) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark-coffee" id="alumni-tab" data-bs-toggle="tab" data-bs-target="#alumni" type="button" role="tab" aria-controls="alumni" aria-selected="false">
                            <i class="fa-solid fa-clock-rotate-left me-1 text-secondary"></i>Riwayat Alumni / Mutasi
                            <span class="badge bg-secondary rounded-pill ms-1"><?= count($upaAlumni) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark-coffee" id="naqib-tab" data-bs-toggle="tab" data-bs-target="#naqib" type="button" role="tab" aria-controls="naqib" aria-selected="false">
                            <i class="fa-solid fa-chalkboard-user me-1 text-gold"></i>Riwayat Naqib
                            <span class="badge bg-dark-coffee text-gold rounded-pill ms-1"><?= count($mentorHistories) ?></span>
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="upaTabContent">
                    <!-- TAB 1: ANGGOTA AKTIF -->
                    <div class="tab-pane fade show active" id="aktif" role="tabpanel" aria-labelledby="aktif-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th class="text-center" style="width: 70px;">Gender</th>
                                        <th>Jenjang</th>
                                        <th>Kontak</th>
                                        <th class="text-center" style="width: 140px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($upaMembers)): ?>
                                        <?php foreach ($upaMembers as $m): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark-coffee"><?= esc($m['nama_lengkap']) ?></span>
                                                    <br><small class="text-muted font-monospace"><?= esc($m['email']) ?: '-' ?></small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?= $m['jenis_kelamin'] === 'L' ? 'bg-primary' : 'bg-danger' ?>" style="font-size: 0.75rem;">
                                                        <?= $m['jenis_kelamin'] ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($m['status_anggota'])): ?>
                                                        <span class="badge bg-gold text-dark"><?= esc($m['status_anggota']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">Non-Pelopor</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="small">
                                                    <i class="fa-brands fa-whatsapp text-success me-1"></i><?= esc($m['handphone']) ?: '-' ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex gap-1 justify-content-center">
                                                        <!-- Lihat Riwayat Button -->
                                                        <button type="button" class="btn btn-sm btn-outline-info btn-view-history" 
                                                                data-member-id="<?= $m['id'] ?>" 
                                                                data-member-name="<?= esc($m['nama_lengkap']) ?>"
                                                                title="Lihat Riwayat Perjalanan UPA">
                                                            <i class="fa-solid fa-clock-rotate-left"></i>
                                                        </button>
                                                        <?php if ($hasWriteAccess): ?>
                                                            <!-- Pindah Group Button -->
                                                            <button type="button" class="btn btn-sm btn-outline-warning btn-transfer-member" 
                                                                    data-member-id="<?= $m['id'] ?>" 
                                                                    data-member-name="<?= esc($m['nama_lengkap']) ?>"
                                                                    title="Pindahkan ke Group Lain">
                                                                <i class="fa-solid fa-right-left"></i>
                                                            </button>
                                                            <!-- Keluarkan Button -->
                                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-member"
                                                                    data-member-id="<?= $m['id'] ?>"
                                                                    data-member-name="<?= esc($m['nama_lengkap']) ?>"
                                                                    title="Keluarkan / Catat Alumni">
                                                                <i class="fa-solid fa-user-minus"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-user-group fa-2x d-block mb-2 opacity-50"></i>
                                                Belum ada anggota aktif di kelompok UPA ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2: RIWAYAT ALUMNI / MUTASI -->
                    <div class="tab-pane fade" id="alumni" role="tabpanel" aria-labelledby="alumni-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th>Jenjang</th>
                                        <th>Periode di Group</th>
                                        <th>Status Mutasi</th>
                                        <th>Catatan</th>
                                        <th class="text-center" style="width: 80px;">Riwayat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($upaAlumni)): ?>
                                        <?php foreach ($upaAlumni as $al): ?>
                                            <tr>
                                                <td>
                                                    <span class="fw-bold text-dark-coffee"><?= esc($al['nama_lengkap']) ?></span>
                                                    <br><small class="text-muted font-monospace"><?= esc($al['email']) ?: '-' ?></small>
                                                </td>
                                                <td>
                                                    <?php if (!empty($al['status_anggota'])): ?>
                                                        <span class="badge bg-light text-dark border"><?= esc($al['status_anggota']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted small">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border font-monospace">
                                                        <?= esc($al['periode'] ?: ($al['tahun_mulai'] . ' - ' . ($al['tahun_selesai'] ?: ''))) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($al['status'] === 'pindah'): ?>
                                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                                                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>Pindah Group
                                                        </span>
                                                    <?php elseif ($al['status'] === 'alumni'): ?>
                                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                                            <i class="fa-solid fa-graduation-cap me-1"></i>Alumni
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle">
                                                            <?= esc($al['status']) ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="small text-secondary"><?= esc($al['catatan']) ?: '-' ?></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-info btn-view-history" 
                                                            data-member-id="<?= $al['member_id'] ?>" 
                                                            data-member-name="<?= esc($al['nama_lengkap']) ?>"
                                                            title="Lihat Perjalanan UPA Anggota">
                                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-clock-rotate-left fa-2x d-block mb-2 opacity-50"></i>
                                                Belum ada riwayat anggota yang pindah atau menjadi alumni di kelompok ini.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: RIWAYAT PEMBIMBING (NAQIB) -->
                    <div class="tab-pane fade" id="naqib" role="tabpanel" aria-labelledby="naqib-tab">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Nama Pembimbing (Naqib)</th>
                                        <th>Jenjang</th>
                                        <th>Periode Membimbing</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($mentorHistories)): ?>
                                        <?php foreach ($mentorHistories as $mh): ?>
                                            <tr>
                                                <td>
                                                    <strong class="text-dark-coffee"><?= esc($mh['pembimbing_nama'] ?: '-') ?></strong>
                                                    <?php if (!empty($mh['handphone'])): ?>
                                                        <br><small class="text-muted"><i class="fa-solid fa-phone me-1"></i><?= esc($mh['handphone']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($mh['jenjang_pembimbing'])): ?>
                                                        <span class="badge bg-gold text-dark"><?= esc($mh['jenjang_pembimbing']) ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border font-monospace">
                                                        <?= esc($mh['periode'] ?: ($mh['tahun_mulai'] . ' - ' . ($mh['is_current'] ? 'Sekarang' : $mh['tahun_selesai']))) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($mh['is_current']): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                            <i class="fa-solid fa-check me-1"></i>Sedang Membimbing
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary-subtle text-muted border">
                                                            Masa Lalu
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="small text-secondary"><?= esc($mh['catatan']) ?: '-' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="fa-solid fa-chalkboard-user fa-2x d-block mb-2 opacity-50"></i>
                                                Belum ada riwayat pembimbing yang tercatat.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: ADD MEMBER TO UPA -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-bold mb-0 text-dark-coffee">
                    <i class="fa-solid fa-user-plus text-gold me-2"></i>Masukkan Anggota Baru
                </h5>
            </div>
            <div class="card-body p-4">
                <?php if ($hasWriteAccess): ?>
                    <form action="<?= base_url('admin/upa/add_member/' . $upa['kode_upa']) ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark-coffee">Pilih Anggota Tanpa UPA</label>
                            <select name="member_id" class="form-select border shadow-sm" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php foreach ($unassignedMembers as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= esc($u['nama_lengkap']) ?> (<?= esc($u['status_anggota'] ?: 'Non-Pelopor') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text mt-1 text-muted">Hanya menampilkan anggota yang saat ini belum terdaftar di group manapun.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark-coffee">Tahun Mulai Dibina</label>
                            <input type="text" name="tahun_mulai" class="form-control" value="<?= date('Y') ?>" placeholder="Contoh: <?= date('Y') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold text-dark-coffee">Catatan Penempatan</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Masuk UPA wilayah Pancoran Mas"></textarea>
                        </div>

                        <button type="submit" class="btn btn-gold w-100 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-user-plus me-2"></i>Masukkan ke Group UPA
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning border-0" role="alert" style="border-radius: 8px;">
                        <i class="fa-solid fa-lock me-2"></i>Anda tidak memiliki otorisasi untuk menambahkan anggota.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- QUICK INFO CARD -->
        <div class="card border-0 shadow-sm bg-dark-coffee text-cream" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h6 class="fw-bold text-gold mb-2"><i class="fa-solid fa-circle-info me-2"></i>Informasi Mutasi UPA</h6>
                <p class="small text-cream mb-0" style="opacity: 0.9; line-height: 1.6;">
                    Setiap pemindahan atau mutasi anggota antar Group UPA akan <strong>otomatis tercatat dalam riwayat perjalanan UPA</strong> anggota tersebut, lengkap dengan nama Naqib pembimbing dan rentang tahun pembinaan.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODAL PINDAH GROUP (TRANSFER) ================= -->
<div class="modal fade" id="modalTransfer" tabindex="-1" aria-labelledby="modalTransferLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/upa/transfer_member/' . $upa['kode_upa']) ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="member_id" id="transferMemberId">
            <div class="modal-header bg-dark-coffee text-cream">
                <h5 class="modal-title fw-bold" id="modalTransferLabel">
                    <i class="fa-solid fa-right-left text-gold me-2"></i>Pindahkan Anggota ke Group Lain
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">
                    Pindahkan <strong id="transferMemberName" class="text-dark-coffee"></strong> dari group 
                    <span class="badge bg-dark-coffee text-gold font-monospace"><?= esc($upa['kode_upa']) ?></span> ke:
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Group UPA Tujuan <span class="text-danger">*</span></label>
                    <select name="target_kode_upa" class="form-select" required>
                        <option value="">-- Pilih Group Tujuan --</option>
                        <?php foreach ($otherUpas as $ou): ?>
                            <option value="<?= $ou['kode_upa'] ?>">
                                <?= esc($ou['kode_upa']) ?> - <?= esc($ou['nama_group'] ?: ('Group ' . $ou['kode_upa'])) ?>
                                <?= !empty($ou['pembimbing_nama']) ? ' (Naqib: ' . esc($ou['pembimbing_nama']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tahun Pemindahan <span class="text-danger">*</span></label>
                    <input type="text" name="tahun_pindah" class="form-control" value="<?= date('Y') ?>" required>
                    <div class="form-text">Tahun kepindahan untuk menutup riwayat lama dan memulai riwayat di group baru.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Alasan / Catatan Pemindahan</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Pindah domisili ke kelurahan lain"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-gold fw-bold">
                    <i class="fa-solid fa-check me-1"></i>Pindahkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL KELUARKAN ANGGOTA (ALUMNI / KELUAR) ================= -->
<div class="modal fade" id="modalRemove" tabindex="-1" aria-labelledby="modalRemoveLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formRemoveMember" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="modalRemoveLabel">
                    <i class="fa-solid fa-user-minus me-2"></i>Keluarkan Anggota dari Group UPA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">
                    Anda akan mengeluarkan <strong id="removeMemberName" class="text-danger"></strong> dari kelompok UPA 
                    <span class="badge bg-dark-coffee text-gold font-monospace"><?= esc($upa['kode_upa']) ?></span>.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status Akhir <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="alumni" selected>Alumni UPA</option>
                        <option value="pindah">Pindah / Mutasi</option>
                        <option value="selesai">Selesai Masa Pembinaan</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Tahun Selesai <span class="text-danger">*</span></label>
                    <input type="text" name="tahun_selesai" class="form-control" value="<?= date('Y') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan / Keterangan</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Alasan dikeluarkan atau catatan pembinaan"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger fw-bold">
                    <i class="fa-solid fa-check me-1"></i>Ya, Keluarkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL LIHAT RIWAYAT ANGGOTA (TIMELINE) ================= -->
<div class="modal fade" id="modalTimelineHistory" tabindex="-1" aria-labelledby="modalTimelineLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark-coffee text-cream">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="modalTimelineLabel">
                        <i class="fa-solid fa-timeline text-gold me-2"></i>Riwayat Perjalanan Group UPA & Naqib
                    </h5>
                    <small class="text-gold" id="timelineMemberSub">Memuat data...</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="timelineContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-gold" role="status"></div>
                    <div class="text-muted mt-2">Memuat riwayat pembinaan...</div>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Transfer Modal Handler
    const modalTransfer = new bootstrap.Modal(document.getElementById('modalTransfer'));
    document.querySelectorAll('.btn-transfer-member').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('transferMemberId').value = this.dataset.memberId;
            document.getElementById('transferMemberName').textContent = this.dataset.memberName;
            modalTransfer.show();
        });
    });

    // 2. Remove Modal Handler
    const modalRemove = new bootstrap.Modal(document.getElementById('modalRemove'));
    const formRemoveMember = document.getElementById('formRemoveMember');
    document.querySelectorAll('.btn-remove-member').forEach(btn => {
        btn.addEventListener('click', function() {
            const memberId = this.dataset.memberId;
            document.getElementById('removeMemberName').textContent = this.dataset.memberName;
            formRemoveMember.action = `<?= base_url('admin/upa/remove_member/' . $upa['kode_upa']) ?>/${memberId}`;
            modalRemove.show();
        });
    });

    // 3. Timeline History Modal Handler
    const modalTimeline = new bootstrap.Modal(document.getElementById('modalTimelineHistory'));
    const timelineContainer = document.getElementById('timelineContainer');
    const timelineMemberSub = document.getElementById('timelineMemberSub');
    const hasWriteAccess = <?= ($hasWriteAccess ?? false) ? 'true' : 'false' ?>;

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function loadMemberHistory(memberId, memberName) {
        if (timelineMemberSub && memberName) {
            timelineMemberSub.textContent = `Anggota: ${memberName}`;
        }
        if (timelineContainer) {
            timelineContainer.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <div class="text-muted mt-2">Memuat riwayat pembinaan...</div>
                </div>
            `;
        }

        fetch(`<?= base_url('admin/upa/member_history') ?>/${memberId}`)
            .then(r => r.json())
            .then(data => {
                const mName = memberName || (data.member ? data.member.nama_lengkap : '');
                if (timelineMemberSub && mName) {
                    timelineMemberSub.textContent = `Anggota: ${mName}`;
                }

                if (!data.histories || data.histories.length === 0) {
                    timelineContainer.innerHTML = `
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-clock-rotate-left fa-3x d-block mb-3 opacity-25"></i>
                            Belum ada catatan riwayat UPA untuk anggota ini.
                        </div>
                    `;
                    return;
                }

                let html = '<div class="timeline-wrapper position-relative ps-4">';
                data.histories.forEach((h, idx) => {
                    const isCurrent = h.is_current == 1;
                    const badgeStatus = isCurrent 
                        ? '<span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Aktif Saat Ini</span>'
                        : (h.status === 'pindah' 
                            ? '<span class="badge bg-warning text-dark"><i class="fa-solid fa-right-left me-1"></i>Pindah Group</span>' 
                            : '<span class="badge bg-secondary"><i class="fa-solid fa-graduation-cap me-1"></i>Alumni</span>');

                    const deleteBtn = hasWriteAccess ? `
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2 btn-delete-history" 
                            data-history-id="${h.id}" 
                            data-member-id="${memberId}" 
                            data-member-name="${escapeHtml(mName)}" 
                            data-kode-upa="${h.kode_upa}" 
                            data-is-current="${isCurrent ? '1' : '0'}"
                            title="Hapus riwayat ini"
                            style="font-size: 0.78rem;">
                            <i class="fa-solid fa-trash-can me-1"></i>Hapus
                        </button>
                    ` : '';

                    html += `
                        <div class="timeline-item position-relative mb-4 pb-2">
                            <div class="timeline-dot ${isCurrent ? 'bg-success border-success' : 'bg-dark-coffee border-gold'}"></div>
                            <div class="card border ${isCurrent ? 'border-success shadow-sm' : 'border-light-subtle'} bg-white" style="border-radius: 10px;">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-dark-coffee text-gold font-monospace fs-6">${h.kode_upa}</span>
                                            <div>${badgeStatus}</div>
                                        </div>
                                        ${deleteBtn}
                                    </div>
                                    <h6 class="fw-bold text-dark-coffee mb-1">${h.nama_group || ('Group ' + h.kode_upa)}</h6>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">
                                            <i class="fa-solid fa-calendar me-1 text-gold"></i>Periode: <strong>${h.periode || (h.tahun_mulai + ' - ' + (isCurrent ? 'Sekarang' : (h.tahun_selesai || '')))}</strong>
                                        </small>
                                        <small class="text-dark d-block mt-1">
                                            <i class="fa-solid fa-chalkboard-user me-1 text-gold"></i>Pembimbing (Naqib): <strong>${h.pembimbing_nama || 'Belum ditentukan'}</strong>
                                            ${h.jenjang_pembimbing ? `<span class="badge bg-light text-dark border ms-1">${h.jenjang_pembimbing}</span>` : ''}
                                        </small>
                                        ${h.nama_kelurahan ? `<small class="text-muted d-block"><i class="fa-solid fa-location-dot me-1 text-secondary"></i>Wilayah: Kel. ${h.nama_kelurahan}</small>` : ''}
                                    </div>
                                    ${h.catatan ? `<div class="bg-light p-2 rounded small text-secondary border-start border-3 border-secondary"><i class="fa-solid fa-quote-left me-1 text-muted"></i>${h.catatan}</div>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                timelineContainer.innerHTML = html;
            })
            .catch(err => {
                timelineContainer.innerHTML = `<div class="alert alert-danger">Gagal memuat data riwayat: ${err.message}</div>`;
            });
    }

    document.querySelectorAll('.btn-view-history').forEach(btn => {
        btn.addEventListener('click', function() {
            const memberId = this.dataset.memberId;
            const memberName = this.dataset.memberName;
            modalTimeline.show();
            loadMemberHistory(memberId, memberName);
        });
    });

    // Delete history event delegation inside modal timeline
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-history');
        if (!btn) return;

        const historyId = btn.getAttribute('data-history-id');
        const memberId  = btn.getAttribute('data-member-id');
        const memberName = btn.getAttribute('data-member-name') || '';
        const kodeUpa   = btn.getAttribute('data-kode-upa') || '';
        const isCurrent = btn.getAttribute('data-is-current') === '1';

        let promptMsg = `Hapus riwayat UPA ${kodeUpa}` + (memberName ? ` untuk ${memberName}?` : '?');
        if (isCurrent) {
            promptMsg += `\n\nPerhatian: Ini adalah kelompok aktif saat ini. Menghapusnya akan mengembalikan ke riwayat UPA sebelumnya secara otomatis.`;
        }

        if (!confirm(promptMsg)) {
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...';

        fetch(`<?= base_url('admin/upa/member_history/delete') ?>/${historyId}?ajax=1`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                if (res.was_current) {
                    alert(res.message);
                    window.location.reload();
                } else {
                    loadMemberHistory(memberId, memberName);
                }
            } else {
                alert('Gagal menghapus: ' + (res.message || 'Terjadi kesalahan'));
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-trash-can me-1"></i>Hapus';
            }
        })
        .catch(err => {
            alert('Terjadi kesalahan koneksi: ' + err.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-trash-can me-1"></i>Hapus';
        });
    });
});
</script>

<style>
    .bg-dark-coffee {
        background-color: #3e2723 !important;
    }
    .text-dark-coffee {
        color: #3e2723 !important;
    }
    .text-cream {
        color: #fff8e1 !important;
    }
    .btn-gold {
        background-color: #d4af37 !important;
        color: #3e2723 !important;
        font-weight: 600;
        border: none;
    }
    .btn-gold:hover {
        background-color: #c5a028 !important;
        color: #3e2723 !important;
    }
    .text-gold {
        color: #d4af37 !important;
    }
    .border-gold {
        border-color: #d4af37 !important;
    }

    /* Timeline styling */
    .timeline-wrapper {
        border-left: 2px solid #d4af37;
    }
    .timeline-dot {
        position: absolute;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        left: -32px;
        top: 15px;
        border: 2px solid #ffffff;
    }
</style>
<?= $this->endSection() ?>
