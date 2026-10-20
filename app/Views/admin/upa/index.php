<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-0 text-dark-coffee">
            <i class="fa-solid fa-people-group me-2 text-gold"></i>Kelola Group UPA (Unit Pembinaan Anggota)
        </h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>" class="text-secondary text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/members') ?>" class="text-secondary text-decoration-none">Anggota</a></li>
                <li class="breadcrumb-item active text-dark-coffee fw-medium" aria-current="page">Group UPA</li>
            </ol>
        </nav>
    </div>
    <?php if ($hasWriteAccess): ?>
        <a href="<?= base_url('admin/upa/create') ?>" class="btn btn-gold fw-semibold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i>Bentuk Group UPA Baru
        </a>
    <?php endif; ?>
</div>

<!-- Navigation Tabs for Anggota Modules -->
<ul class="nav nav-pills mb-4 p-1 bg-white rounded-pill shadow-sm d-inline-flex border">
    <li class="nav-item">
        <a class="nav-link rounded-pill text-dark-coffee fw-medium px-4" href="<?= base_url('admin/members') ?>">
            <i class="fa-solid fa-users me-2"></i>Daftar Anggota
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill text-dark-coffee fw-medium px-4" href="<?= base_url('admin/members/ekonomi') ?>">
            <i class="fa-solid fa-wallet me-2"></i>Ekonomi Anggota
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill text-dark-coffee fw-medium px-4" href="<?= base_url('admin/members/pendidikan') ?>">
            <i class="fa-solid fa-graduation-cap me-2"></i>Pendidikan Anggota
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link rounded-pill text-dark-coffee fw-medium px-4" href="<?= base_url('admin/members/dashboard') ?>">
            <i class="fa-solid fa-chart-pie me-2"></i>Statistik & Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active rounded-pill bg-dark-coffee text-cream fw-semibold px-4" href="<?= base_url('admin/upa') ?>">
            <i class="fa-solid fa-people-group me-2 text-gold"></i>Group UPA
        </a>
    </li>
</ul>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-check me-2"></i><?= session()->getFlashdata('message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 8px;">
        <i class="fa-solid fa-circle-xmark me-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Stat Cards -->
<?php
    $totalNaqib = count(array_filter($upas, fn($u) => !empty($u['pembimbing_id'])));
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3 bg-white" style="border-radius: 12px; border-left: 4px solid #3e2723 !important;">
            <div class="fw-bold fs-4 text-dark-coffee"><?= $totalUpas ?></div>
            <small class="text-muted"><i class="fa-solid fa-people-group me-1 text-gold"></i>Total Group UPA</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3 bg-white" style="border-radius: 12px; border-left: 4px solid #d4af37 !important;">
            <div class="fw-bold fs-4 text-dark-coffee"><?= $totalMembers ?></div>
            <small class="text-muted"><i class="fa-solid fa-users me-1 text-gold"></i>Total Anggota Terbina</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3 bg-white" style="border-radius: 12px; border-left: 4px solid #198754 !important;">
            <div class="fw-bold fs-4 text-success"><?= $totalNaqib ?></div>
            <small class="text-muted"><i class="fa-solid fa-chalkboard-user me-1 text-success"></i>Pembimbing (Naqib) Terisi</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm text-center py-3 bg-white" style="border-radius: 12px; border-left: 4px solid #dc3545 !important;">
            <div class="fw-bold fs-4 text-danger"><?= $totalUpas - $totalNaqib ?></div>
            <small class="text-muted"><i class="fa-solid fa-user-slash me-1 text-danger"></i>Belum Memiliki Naqib</small>
        </div>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-body p-3">
        <form action="<?= base_url('admin/upa') ?>" method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Cari Kode, Nama Group, Pembimbing..." value="<?= esc($searchQuery ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="kelurahan_id" class="form-select">
                    <option value="">-- Semua Kelurahan --</option>
                    <?php if (!empty($villages)): ?>
                        <?php foreach ($villages as $v): ?>
                            <option value="<?= $v['id'] ?>" <?= ($selectedKel ?? '') == $v['id'] ? 'selected' : '' ?>>
                                Kel. <?= esc($v['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-dark-coffee text-cream w-100 fw-semibold">
                    <i class="fa-solid fa-filter me-1"></i>Filter
                </button>
                <?php if (!empty($searchQuery) || !empty($selectedKel)): ?>
                    <a href="<?= base_url('admin/upa') ?>" class="btn btn-outline-secondary" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table align-middle table-hover mb-0" id="upaTable">
            <thead class="bg-dark-coffee text-cream" style="background-color: #3e2723; color: #fff8e1;">
                <tr>
                    <th class="ps-4" style="width: 140px;">Kode UPA</th>
                    <th>Nama Group & Keterangan</th>
                    <th>Pembimbing (Naqib)</th>
                    <th>Wilayah Pembinaan</th>
                    <th>Jadwal & Lokasi</th>
                    <th class="text-center" style="width: 110px;">Anggota</th>
                    <th class="text-center" style="width: 180px; min-width: 180px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($upas)): ?>
                    <?php foreach ($upas as $u): ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-dark-coffee text-gold font-monospace px-2 py-1 fs-6">
                                    <?= esc($u['kode_upa']) ?>
                                </span>
                            </td>
                            <td>
                                <strong class="text-dark-coffee fs-6"><?= esc($u['nama_group'] ?: ('Group ' . $u['kode_upa'])) ?></strong>
                                <?php if (!empty($u['keterangan'])): ?>
                                    <div class="text-muted small text-truncate" style="max-width: 200px;"><?= esc($u['keterangan']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($u['nama_pembimbing'])): ?>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2 bg-light-cream text-dark-coffee fw-bold">
                                            <?= strtoupper(substr($u['nama_pembimbing'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark-coffee d-block"><?= esc($u['nama_pembimbing']) ?></span>
                                            <?php if (!empty($u['jenjang_pembimbing'])): ?>
                                                <span class="badge bg-gold text-dark" style="font-size: 0.72rem;"><?= esc($u['jenjang_pembimbing']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted fst-italic small"><i class="fa-solid fa-user-slash me-1 opacity-50"></i>Belum ditentukan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">Kel. <?= esc($u['kelurahan']) ?></span>
                                <br><small class="text-muted">Kec. <?= esc($u['kecamatan']) ?></small>
                            </td>
                            <td>
                                <?php if (!empty($u['jadwal_rutin']) || !empty($u['tempat_pertemuan'])): ?>
                                    <small class="d-block text-dark fw-medium"><i class="fa-regular fa-clock me-1 text-gold"></i><?= esc($u['jadwal_rutin'] ?: '-') ?></small>
                                    <small class="d-block text-muted"><i class="fa-solid fa-location-dot me-1 text-secondary"></i><?= esc($u['tempat_pertemuan'] ?: '-') ?></small>
                                <?php else: ?>
                                    <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/upa/detail/' . $u['kode_upa']) ?>" class="text-decoration-none">
                                    <span class="badge <?= $u['jumlah_anggota'] > 0 ? 'bg-primary' : 'bg-secondary' ?> rounded-pill px-3 py-2 fw-bold" style="font-size: 0.85rem;" title="Lihat <?= $u['jumlah_anggota'] ?> anggota">
                                        <?= $u['jumlah_anggota'] ?> Orang
                                    </span>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="<?= base_url('admin/upa/detail/' . $u['kode_upa']) ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Kelola Anggota & Riwayat UPA">
                                        <i class="fa-solid fa-users-gear me-1"></i>Kelola
                                    </a>
                                    <?php if ($hasWriteAccess): ?>
                                        <a href="<?= base_url('admin/upa/edit/' . $u['kode_upa']) ?>" 
                                           class="btn btn-sm btn-outline-warning" title="Edit Data Group">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a href="<?= base_url('admin/upa/delete/' . $u['kode_upa']) ?>" 
                                           onclick="return confirm('Hapus Group UPA ini? Anggota di dalamnya akan tercatat sebagai alumni dan status aktif UPA mereka dihapus.')" 
                                           class="btn btn-sm btn-outline-danger" title="Hapus Group UPA">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-people-group fa-3x d-block mb-3 opacity-25"></i>
                            Tidak ada data Group UPA yang sesuai kriteria.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .bg-dark-coffee {
        background-color: #3e2723 !important;
    }
    .text-dark-coffee {
        color: #3e2723 !important;
    }
    .btn-dark-coffee {
        background-color: #3e2723 !important;
        color: #fff8e1 !important;
    }
    .btn-dark-coffee:hover {
        background-color: #2c1b18 !important;
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
    .text-cream {
        color: #fff8e1 !important;
    }
    .bg-light-cream {
        background-color: #efebe9 !important;
    }
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
    }
</style>
<?= $this->endSection() ?>
