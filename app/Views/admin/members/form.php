<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 bg-light-cream">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="vintage-serif text-dark-coffee mb-0"><i class="fa-solid fa-user me-3 text-gold"></i><?= $member ? 'Edit Profil Anggota' : 'Tambah Anggota Baru' ?></h3>
                    <a href="<?= base_url('admin/members') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-2"></i>Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <?php if (session('errors')): ?>
            <div class="alert alert-danger shadow-sm">
                <?php foreach (session('errors') as $error): ?>
                    <i class="fa-solid fa-circle-exclamation me-2"></i><?= $error ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= $member ? base_url('admin/members/update/' . $member['id']) : base_url('admin/members/store') ?>" method="post">
            <?= csrf_field() ?>

            <!-- Section: Data Diri Utama -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-id-card me-2 text-gold"></i>Data Identitas Diri</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Row 1 -->
                        <div class="col-md-4">
                            <label for="nama_lengkap" class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', $member['nama_lengkap'] ?? '') ?>" placeholder="Nama lengkap sesuai KTP" required>
                        </div>
                        <div class="col-md-4">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $member['email'] ?? '') ?>" placeholder="Alamat Email">
                        </div>
                        <div class="col-md-4">
                            <label for="handphone" class="form-label fw-bold">Handphone</label>
                            <input type="text" class="form-control" id="handphone" name="handphone" value="<?= old('handphone', $member['handphone'] ?? '') ?>" placeholder="No. Handphone">
                        </div>

                        <!-- Row 2 -->
                        <div class="col-md-4">
                            <label for="jenis_kelamin" class="form-label fw-bold">Jenis Kelamin</label>
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                                <option value="">-- Pilih --</option>
                                <option value="L" <?= old('jenis_kelamin', $member['jenis_kelamin'] ?? '') === 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= old('jenis_kelamin', $member['jenis_kelamin'] ?? '') === 'P' ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tempat_lahir" class="form-label fw-bold">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= old('tempat_lahir', $member['tempat_lahir'] ?? '') ?>" placeholder="Kota Tempat Lahir">
                        </div>
                        <div class="col-md-4">
                            <label for="tanggal_lahir" class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= old('tanggal_lahir', $member['tanggal_lahir'] ?? '') ?>">
                        </div>

                        <!-- Row 3 -->
                        <div class="col-md-6">
                            <label for="status_anggota" class="form-label fw-bold">Jenjang</label>
                            <select class="form-select" id="status_anggota" name="status_anggota">
                                <option value="">-- Pilih Jenjang --</option>
                                <option value="Utama" <?= old('status_anggota', $member['status_anggota'] ?? '') === 'Utama' ? 'selected' : '' ?>>Utama</option>
                                <option value="Dewasa" <?= old('status_anggota', $member['status_anggota'] ?? '') === 'Dewasa' ? 'selected' : '' ?>>Dewasa</option>
                                <option value="Madya" <?= old('status_anggota', $member['status_anggota'] ?? '') === 'Madya' ? 'selected' : '' ?>>Madya</option>
                                <option value="Muayyid" <?= old('status_anggota', $member['status_anggota'] ?? '') === 'Muayyid' ? 'selected' : '' ?>>Muayyid</option>
                                <option value="Muhib" <?= old('status_anggota', $member['status_anggota'] ?? '') === 'Muhib' ? 'selected' : '' ?>>Muhib</option>
                                <option value="Belum Tarbiyah" <?= old('status_anggota', $member['status_anggota'] ?? '') === 'Belum Tarbiyah' ? 'selected' : '' ?>>Belum Tarbiyah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="kode_upa" class="form-label fw-bold">UPA</label>
                            <select class="form-select" id="kode_upa" name="kode_upa">
                                <option value="">-- Pilih UPA --</option>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?= $r['kode_upa'] ?>" <?= old('kode_upa', $member['kode_upa'] ?? '') === $r['kode_upa'] ? 'selected' : '' ?>><?= $r['kode_upa'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Row 4 -->
                        <div class="col-md-6">
                            <label for="status_perkawinan" class="form-label fw-bold">Status Perkawinan</label>
                            <select class="form-select" id="status_perkawinan" name="status_perkawinan">
                                <option value="Lajang" <?= old('status_perkawinan', $member['status_perkawinan'] ?? '') === 'Lajang' ? 'selected' : '' ?>>Lajang</option>
                                <option value="Menikah" <?= old('status_perkawinan', $member['status_perkawinan'] ?? '') === 'Menikah' ? 'selected' : '' ?>>Menikah</option>
                                <option value="Duda/Janda" <?= old('status_perkawinan', $member['status_perkawinan'] ?? '') === 'Duda/Janda' ? 'selected' : '' ?>>Duda/Janda</option>
                            </select>
                        </div>
                        <!-- Spouse Connection Field -->
                        <div class="col-md-6" id="spouse_container" style="display: none;">
                            <label for="spouse_member_id" class="form-label fw-bold">Nama Pasangan</label>
                            <select class="form-select select2" id="spouse_member_id" name="spouse_member_id">
                                <option value="">-- Pilih Pasangan --</option>
                                <?php foreach ($potentialSpouses as $ps): ?>
                                    <option value="<?= $ps['id'] ?>" <?= old('spouse_member_id', $member['spouse_member_id'] ?? '') == $ps['id'] ? 'selected' : '' ?>>
                                        <?= esc($ps['nama_lengkap']) ?> (<?= $ps['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($spouse)): ?>
                                <small class="text-muted d-block mt-1">Saat ini terhubung dengan: <a href="<?= base_url('admin/members/edit/' . $spouse['id']) ?>" target="_blank" class="fw-bold text-decoration-none text-dark-coffee"><?= esc($spouse['nama_lengkap']) ?> <i class="fa-solid fa-external-link ms-1 small"></i></a></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: Group UPA & Riwayat Pembina (Naqib) -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-people-group me-2 text-gold"></i>Riwayat Group UPA & Pembimbing (Naqib)</h5>
                    <?php if (!empty($member)): ?>
                        <button type="button" class="btn btn-sm btn-gold" data-bs-toggle="modal" data-bs-target="#modalAddUpaHistory">
                            <i class="fa-solid fa-plus me-1"></i>Catat Riwayat Masa Lalu
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($upaHistories)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Periode Pembinaan</th>
                                        <th>Group UPA</th>
                                        <th>Pembimbing (Naqib)</th>
                                        <th>Status Keanggotaan</th>
                                        <th>Catatan</th>
                                        <?php if (!empty($member)): ?>
                                            <th class="text-center" style="width: 70px;">Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upaHistories as $uh): ?>
                                        <tr class="<?= $uh['is_current'] == 1 ? 'table-success-subtle bg-success-subtle' : '' ?>">
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace fs-6">
                                                    <?= esc($uh['periode'] ?: ($uh['tahun_mulai'] . ' - ' . ($uh['is_current'] ? 'Sekarang' : $uh['tahun_selesai']))) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <strong class="text-dark-coffee"><?= esc($uh['nama_group'] ?: ('Group ' . $uh['kode_upa'])) ?></strong>
                                                <br><span class="badge bg-dark-coffee text-gold font-monospace"><?= esc($uh['kode_upa']) ?></span>
                                                <?php if (!empty($uh['nama_kelurahan'])): ?>
                                                    <small class="text-muted ms-1">Kel. <?= esc($uh['nama_kelurahan']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($uh['pembimbing_nama'])): ?>
                                                    <div class="fw-bold text-dark-coffee">
                                                        <i class="fa-solid fa-chalkboard-user me-1 text-gold"></i><?= esc($uh['pembimbing_nama']) ?>
                                                    </div>
                                                    <?php if (!empty($uh['jenjang_pembimbing'])): ?>
                                                        <span class="badge bg-gold text-dark" style="font-size: 0.72rem;"><?= esc($uh['jenjang_pembimbing']) ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted fst-italic small">Belum ditentukan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($uh['is_current'] == 1): ?>
                                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Aktif Saat Ini</span>
                                                <?php elseif ($uh['status'] === 'pindah'): ?>
                                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-right-left me-1"></i>Pindah Group</span>
                                                <?php elseif ($uh['status'] === 'alumni'): ?>
                                                    <span class="badge bg-secondary"><i class="fa-solid fa-graduation-cap me-1"></i>Alumni</span>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-dark"><?= esc($uh['status']) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="small text-secondary"><?= esc($uh['catatan']) ?: '-' ?></td>
                                            <?php if (!empty($member)): ?>
                                                <td class="text-center">
                                                    <a href="<?= base_url('admin/upa/member_history/delete/' . $uh['id']) ?>" 
                                                       onclick="return confirm('Hapus riwayat UPA <?= esc($uh['kode_upa']) ?> ini?<?= $uh['is_current'] == 1 ? '\n\nPerhatian: Ini adalah kelompok aktif saat ini. Menghapusnya akan mengembalikan status aktif ke riwayat UPA sebelumnya.' : '' ?>')"
                                                       class="btn btn-sm btn-outline-danger" title="Hapus Riwayat">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </a>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fa-solid fa-people-group fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="mb-0">Belum ada riwayat pembinaan UPA untuk anggota ini.</p>
                            <small class="text-muted">Pilih UPA pada bagian Data Identitas Diri atau klik tombol "Catat Riwayat Masa Lalu" di atas.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section: Hubungan Keluarga -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-people-roof me-2 text-gold"></i>Hubungan Keluarga</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- Parent Connection Fields -->
                        <div class="col-md-6">
                            <label for="father_member_id" class="form-label fw-bold">Ayah (Father)</label>
                            <select class="form-select select2" id="father_member_id" name="father_member_id">
                                <option value="">-- Pilih Ayah --</option>
                                <?php foreach ($potentialFathers as $pf): ?>
                                    <option value="<?= $pf['id'] ?>" <?= old('father_member_id', $member['father_member_id'] ?? $prefilledFatherId ?? '') == $pf['id'] ? 'selected' : '' ?>>
                                        <?= esc($pf['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="mother_member_id" class="form-label fw-bold">Ibu (Mother)</label>
                            <select class="form-select select2" id="mother_member_id" name="mother_member_id">
                                <option value="">-- Pilih Ibu --</option>
                                <?php foreach ($potentialMothers as $pm): ?>
                                    <option value="<?= $pm['id'] ?>" <?= old('mother_member_id', $member['mother_member_id'] ?? $prefilledMotherId ?? '') == $pm['id'] ? 'selected' : '' ?>>
                                        <?= esc($pm['nama_lengkap']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Section: Data Anak -->
                        <?php if ($member): ?>
                        <div class="col-12 mt-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 border-top pt-3">
                                <h6 class="fw-bold text-dark-coffee mb-0"><i class="fa-solid fa-children me-2 text-gold"></i>Data Anak</h6>
                                <a href="<?= base_url('admin/members/create?parent_id=' . $member['id']) ?>" class="btn btn-sm btn-gold"><i class="fa-solid fa-plus me-1"></i>Tambah Anak Baru</a>
                            </div>
                            <?php if (empty($children)): ?>
                                <div class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-baby-carriage fa-3x mb-3 text-light-cream"></i>
                                    <p class="mb-0">Belum ada data anak terhubung dengan anggota ini.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
                                        <thead class="table-light-cream text-dark-coffee">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Lengkap</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Usia / Tgl Lahir</th>
                                                <th>Pendidikan & Lembaga</th>
                                                <th>Status Mentoring</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($children as $c): ?>
                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td>
                                                        <span class="fw-bold text-dark-coffee"><?= esc($c['nama_lengkap']) ?></span>
                                                        <?php if ($c['account_status'] === 'no_account'): ?>
                                                            <span class="badge bg-secondary ms-1 small">Shadow</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $c['jenis_kelamin'] === 'L' ? 'Laki-laki' : ($c['jenis_kelamin'] === 'P' ? 'Perempuan' : '-') ?></td>
                                                    <td>
                                                        <?= $c['tanggal_lahir'] ? date('d-m-Y', strtotime($c['tanggal_lahir'])) : '-' ?>
                                                        <?php if ($c['usia_range']): ?>
                                                            <span class="text-muted text-xs d-block">Rentang: <?= esc($c['usia_range']) ?></span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($c['pendidikan_saat_ini'] || $c['sekolah_universitas']): ?>
                                                            <span class="fw-semibold text-xs"><?= esc($c['pendidikan_saat_ini'] ?: '-') ?></span>
                                                            <span class="text-muted text-xs d-block"><?= esc($c['sekolah_universitas'] ?: '-') ?></span>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if (strtolower(trim($c['mengikuti_mentoring'])) === 'ya'): ?>
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Mengikuti Mentoring</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-light text-muted">Tidak</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="<?= base_url('admin/members/edit/' . $c['id']) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit Profil Anak"><i class="fa-solid fa-pencil"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Section: Alamat Rumah -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-map-location-dot me-2 text-gold"></i>Data Alamat Tinggal</h5>
                    <button type="button" class="btn btn-sm btn-gold animate-hover" id="btn-add-address"><i class="fa-solid fa-plus me-1"></i>Tambah Alamat Lain</button>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="alamat_lengkap" class="form-label fw-bold">Alamat Lengkap Utama</label>
                            <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="2" placeholder="Jalan, No. Rumah, Blok, dll"><?= old('alamat_lengkap', $member['alamat_lengkap'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-3">
                            <label for="rt" class="form-label fw-bold">RT</label>
                            <input type="text" class="form-control" id="rt" name="rt" value="<?= old('rt', $member['rt'] ?? '') ?>" placeholder="00">
                        </div>
                        <div class="col-md-3">
                            <label for="rw" class="form-label fw-bold">RW</label>
                            <input type="text" class="form-control" id="rw" name="rw" value="<?= old('rw', $member['rw'] ?? '') ?>" placeholder="00">
                        </div>
                        <div class="col-md-4">
                            <label for="kota_kabupaten_id" class="form-label fw-bold">Kota / Kabupaten</label>
                            <select class="form-select" id="kota_kabupaten_id" name="kota_kabupaten_id" data-selected="<?= old('kota_kabupaten_id', $member['kota_kabupaten_id'] ?? '') ?>">
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="kecamatan_id" class="form-label fw-bold">Kecamatan</label>
                            <select class="form-select" id="kecamatan_id" name="kecamatan_id" data-selected="<?= old('kecamatan_id', $member['kecamatan_id'] ?? '') ?>" disabled>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="kelurahan_id" class="form-label fw-bold">Kelurahan</label>
                            <select class="form-select" id="kelurahan_id" name="kelurahan_id" data-selected="<?= old('kelurahan_id', $member['kelurahan_id'] ?? '') ?>" disabled>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                        </div>

                        <!-- Additional Addresses Container -->
                        <div id="additional-addresses-container" class="col-12 mt-4">
                            <?php if (!empty($addresses)): ?>
                                <h6 class="fw-bold text-dark-coffee mb-3 border-top pt-3"><i class="fa-solid fa-map-pin me-2 text-gold"></i>Alamat Lainnya</h6>
                            <?php endif; ?>
                            <?php foreach ($addresses as $index => $addr): ?>
                                <div class="address-row card bg-light-cream-subtle border p-3 mb-3 position-relative" style="background-color: #fdfbf7;">
                                    <input type="hidden" name="addresses[id][]" value="<?= $addr['id'] ?>">
                                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-address"><i class="fa-solid fa-trash"></i> Hapus</button>
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-muted">Label Alamat</label>
                                            <input type="text" name="addresses[label][]" class="form-control form-control-sm" value="<?= esc($addr['label']) ?>" placeholder="KTP / Domisili / Kantor" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small text-muted">Alamat Lengkap</label>
                                            <input type="text" name="addresses[alamat_lengkap][]" class="form-control form-control-sm" value="<?= esc($addr['alamat_lengkap']) ?>" required>
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-bold small text-muted">RT</label>
                                            <input type="text" name="addresses[rt][]" class="form-control form-control-sm" value="<?= esc($addr['rt']) ?>">
                                        </div>
                                        <div class="col-md-1">
                                            <label class="form-label fw-bold small text-muted">RW</label>
                                            <input type="text" name="addresses[rw][]" class="form-control form-control-sm" value="<?= esc($addr['rw']) ?>">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-muted">Kota / Kabupaten</label>
                                            <select name="addresses[kota_kabupaten_id][]" class="form-select form-select-sm city-select" data-selected="<?= $addr['kota_kabupaten_id'] ?>">
                                                <option value="">-- Pilih Kota/Kabupaten --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-muted">Kecamatan</label>
                                            <select name="addresses[kecamatan_id][]" class="form-select form-select-sm district-select" data-selected="<?= $addr['kecamatan_id'] ?>" disabled>
                                                <option value="">-- Pilih Kecamatan --</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-muted">Kelurahan</label>
                                            <select name="addresses[kelurahan_id][]" class="form-select form-select-sm village-select" data-selected="<?= $addr['kelurahan_id'] ?>" disabled>
                                                <option value="">-- Pilih Kelurahan --</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>





            <!-- Section: Riwayat Pendidikan -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-graduation-cap me-2 text-gold"></i>Riwayat Pendidikan</h5>
                    <button type="button" class="btn btn-sm btn-gold animate-hover" id="btn-add-education"><i class="fa-solid fa-plus me-1"></i>Tambah Pendidikan</button>
                </div>
                <div class="card-body p-4">
                    <div id="education-container">
                        <?php foreach ($educations as $edu): ?>
                            <div class="education-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative" style="background-color: #fdfbf7;">
                                <input type="hidden" name="educations[id][]" value="<?= $edu['id'] ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-education"><i class="fa-solid fa-trash"></i></button>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-muted">Jenjang Pendidikan</label>
                                        <select name="educations[jenjang][]" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih --</option>
                                            <option value="SD" <?= ($edu['jenjang'] ?? '') === 'SD' ? 'selected' : '' ?>>SD</option>
                                            <option value="SMP" <?= ($edu['jenjang'] ?? '') === 'SMP' ? 'selected' : '' ?>>SMP</option>
                                            <option value="SMA/SMK" <?= ($edu['jenjang'] ?? '') === 'SMA/SMK' ? 'selected' : '' ?>>SMA / SMK</option>
                                            <option value="D3" <?= ($edu['jenjang'] ?? '') === 'D3' ? 'selected' : '' ?>>D3</option>
                                            <option value="S1" <?= ($edu['jenjang'] ?? '') === 'S1' ? 'selected' : '' ?>>S1</option>
                                            <option value="S2" <?= ($edu['jenjang'] ?? '') === 'S2' ? 'selected' : '' ?>>S2</option>
                                            <option value="S3" <?= ($edu['jenjang'] ?? '') === 'S3' ? 'selected' : '' ?>>S3</option>
                                            <option value="Lainnya" <?= ($edu['jenjang'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">Institusi / Nama Sekolah</label>
                                        <input type="text" name="educations[nama_institusi][]" class="form-control form-control-sm" value="<?= esc($edu['nama_institusi']) ?>" placeholder="Contoh: Universitas Negeri Rimberio" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold small text-muted">Tahun / Rentang Tahun</label>
                                        <input type="text" name="educations[rentang_tahun][]" class="form-control form-control-sm" value="<?= esc($edu['rentang_tahun']) ?>" placeholder="Contoh: 2017-2021">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section: Pengalaman Organisasi -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-users me-2 text-gold"></i>Pengalaman Organisasi</h5>
                    <button type="button" class="btn btn-sm btn-gold animate-hover" id="btn-add-organization"><i class="fa-solid fa-plus me-1"></i>Tambah Organisasi</button>
                </div>
                <div class="card-body p-4">
                    <div id="organization-container">
                        <?php foreach ($organizations as $org): ?>
                            <div class="organization-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative" style="background-color: #fdfbf7;">
                                <input type="hidden" name="organizations[id][]" value="<?= $org['id'] ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-organization"><i class="fa-solid fa-trash"></i></button>
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold small text-muted">Jabatan</label>
                                        <input type="text" name="organizations[jabatan][]" class="form-control form-control-sm" value="<?= esc($org['jabatan']) ?>" placeholder="Contoh: Ketua Himpunan Mahasiswa" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold small text-muted">Nama Organisasi / Instansi</label>
                                        <input type="text" name="organizations[nama_organisasi][]" class="form-control form-control-sm" value="<?= esc($org['nama_organisasi']) ?>" placeholder="Contoh: Universitas Negeri Rimberio" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small text-muted">Tahun / Rentang Tahun</label>
                                        <input type="text" name="organizations[rentang_tahun][]" class="form-control form-control-sm" value="<?= esc($org['rentang_tahun'] ?? '') ?>" placeholder="Contoh: 2017-2021">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section: Pengalaman Kerja -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-briefcase me-2 text-gold"></i>Pengalaman Kerja</h5>
                    <button type="button" class="btn btn-sm btn-gold animate-hover" id="btn-add-work"><i class="fa-solid fa-plus me-1"></i>Tambah Pengalaman Kerja</button>
                </div>
                <div class="card-body p-4">
                    <div id="work-container">
                        <?php foreach ($workExperiences as $work): ?>
                            <div class="work-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative" style="background-color: #fdfbf7;">
                                <input type="hidden" name="work_experiences[id][]" value="<?= $work['id'] ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-work"><i class="fa-solid fa-trash"></i></button>
                                <div class="row g-3">
                                    <!-- Row 1 -->
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold small text-muted">Jabatan / Posisi</label>
                                        <input type="text" name="work_experiences[jabatan][]" class="form-control form-control-sm" value="<?= esc($work['jabatan']) ?>" placeholder="Contoh: Karyawan Magang" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold small text-muted">Nama Perusahaan / Tempat Kerja</label>
                                        <input type="text" name="work_experiences[nama_perusahaan][]" class="form-control form-control-sm" value="<?= esc($work['nama_perusahaan']) ?>" placeholder="Contoh: Perusahaan Liceria" required>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold small text-muted">Rentang Tahun</label>
                                        <input type="text" name="work_experiences[rentang_tahun][]" class="form-control form-control-sm" value="<?= esc($work['rentang_tahun']) ?>" placeholder="Contoh: 2021-2022">
                                    </div>
                                    
                                    <!-- Row 2 -->
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">Bidang Pekerjaan</label>
                                        <select name="work_experiences[bidang_pekerjaan_id][]" class="form-select form-select-sm">
                                            <option value="">-- Pilih Bidang --</option>
                                            <?php foreach ($jobFields as $jf): ?>
                                                <option value="<?= $jf['id'] ?>" <?= ($work['bidang_pekerjaan_id'] ?? '') == $jf['id'] ? 'selected' : '' ?>><?= esc($jf['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">Rentang Pendapatan per Bulan</label>
                                        <select name="work_experiences[pendapatan_range][]" class="form-select form-select-sm">
                                            <option value="">-- Pilih --</option>
                                            <option value="< Rp1.000.000" <?= ($work['pendapatan_range'] ?? '') === '< Rp1.000.000' ? 'selected' : '' ?>>&lt; Rp1.000.000</option>
                                            <option value="Rp1.000.000 - Rp3.000.000" <?= ($work['pendapatan_range'] ?? '') === 'Rp1.000.000 - Rp3.000.000' ? 'selected' : '' ?>>Rp1.000.000 - Rp3.000.000</option>
                                            <option value="Rp3.000.000 - Rp5.000.000" <?= ($work['pendapatan_range'] ?? '') === 'Rp3.000.000 - Rp5.000.000' ? 'selected' : '' ?>>Rp3.000.000 - Rp5.000.000</option>
                                            <option value="Rp5.000.000 - Rp10.000.000" <?= ($work['pendapatan_range'] ?? '') === 'Rp5.000.000 - Rp10.000.000' ? 'selected' : '' ?>>Rp5.000.000 - Rp10.000.000</option>
                                            <option value="> Rp10.000.000" <?= ($work['pendapatan_range'] ?? '') === '> Rp10.000.000' ? 'selected' : '' ?>>&gt; Rp10.000.000</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold small text-muted">Alamat Tempat Kerja / Usaha</label>
                                        <input type="text" name="work_experiences[alamat_kerja][]" class="form-control form-control-sm" value="<?= esc($work['alamat_kerja'] ?? '') ?>" placeholder="Contoh: Cipayung, Depok">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Section: Kemampuan / Keahlian -->
            <div class="card shadow-sm border-0 mb-4 bg-white">
                <div class="card-header bg-dark-coffee text-cream border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold"><i class="fa-solid fa-brain me-2 text-gold"></i>Kemampuan / Keahlian</h5>
                    <button type="button" class="btn btn-sm btn-gold animate-hover" id="btn-add-skill"><i class="fa-solid fa-plus me-1"></i>Tambah Kemampuan</button>
                </div>
                <div class="card-body p-4">
                    <div id="skill-container">
                        <?php foreach ($skills as $skill): ?>
                            <div class="skill-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative" style="background-color: #fdfbf7;">
                                <input type="hidden" name="skills[id][]" value="<?= $skill['id'] ?>">
                                <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-skill"><i class="fa-solid fa-trash"></i></button>
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-bold small text-muted">Nama Kemampuan / Keahlian</label>
                                        <input type="text" name="skills[nama_keahlian][]" class="form-control form-control-sm" value="<?= esc($skill['nama_keahlian']) ?>" placeholder="Contoh: Mampu mengoperasikan perangkat lunak" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold small text-muted">Keterangan / Tingkat (Bahasa/Software/Lainnya)</label>
                                        <input type="text" name="skills[keterangan][]" class="form-control form-control-sm" value="<?= esc($skill['keterangan']) ?>" placeholder="Contoh: Indonesia (Aktif) / Inggris (Pasif)">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex justify-content-end mb-5">
                <button type="submit" class="btn btn-gold btn-lg px-5 shadow-sm"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Anggota</button>
            </div>
        </form>
    </div>
</div>

<style>
    .bg-dark-coffee {
        background-color: #3e2723 !important;
    }
    .text-cream {
        color: #efebe9 !important;
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
    .vintage-serif {
        font-family: 'Playfair Display', Georgia, serif;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Spouse Toggle
        const statusPerkawinan = document.getElementById('status_perkawinan');
        const spouseContainer = document.getElementById('spouse_container');
        const spouseSelect = document.getElementById('spouse_member_id');

        function toggleSpouse() {
            if (statusPerkawinan.value === 'Menikah') {
                spouseContainer.style.display = 'block';
            } else {
                spouseContainer.style.display = 'none';
                if (spouseSelect) {
                    spouseSelect.value = '';
                }
            }
        }

        if (statusPerkawinan) {
            statusPerkawinan.addEventListener('change', toggleSpouse);
            toggleSpouse(); // Run initial check on page load
        }

        // Region Cascading Dropdowns for Primary Address
        const kotaSelect = document.getElementById('kota_kabupaten_id');
        const kecSelect = document.getElementById('kecamatan_id');
        const kelSelect = document.getElementById('kelurahan_id');

        if (kotaSelect) {
            function loadCities(selectedId = '') {
                fetch('<?= base_url("admin/regions/cities") ?>')
                    .then(r => r.json())
                    .then(data => {
                        kotaSelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                        data.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.name;
                            if (c.id == selectedId) {
                                opt.selected = true;
                            }
                            kotaSelect.appendChild(opt);
                        });
                        if (selectedId) {
                            kotaSelect.dispatchEvent(new Event('change'));
                        }
                    });
            }

            kotaSelect.addEventListener('change', function() {
                const cityId = this.value;
                kecSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                kecSelect.disabled = true;
                kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                kelSelect.disabled = true;

                if (!cityId) return;

                const selectedKecId = kecSelect.getAttribute('data-selected');
                fetch('<?= base_url("admin/regions/districts") ?>?city_id=' + cityId)
                    .then(r => r.json())
                    .then(data => {
                        data.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = d.name;
                            if (d.id == selectedKecId) {
                                opt.selected = true;
                            }
                            kecSelect.appendChild(opt);
                        });
                        kecSelect.disabled = false;
                        if (selectedKecId) {
                            kecSelect.dispatchEvent(new Event('change'));
                            kecSelect.removeAttribute('data-selected');
                        }
                    });
            });

            kecSelect.addEventListener('change', function() {
                const districtId = this.value;
                kelSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                kelSelect.disabled = true;

                if (!districtId) return;

                const selectedKelId = kelSelect.getAttribute('data-selected');
                fetch('<?= base_url("admin/regions/villages") ?>?district_id=' + districtId)
                    .then(r => r.json())
                    .then(data => {
                        data.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.textContent = v.name;
                            if (v.id == selectedKelId) {
                                opt.selected = true;
                            }
                            kelSelect.appendChild(opt);
                        });
                        kelSelect.disabled = false;
                        if (selectedKelId) {
                            kelSelect.removeAttribute('data-selected');
                        }
                    });
            });

            // Trigger initial load
            const initialCityId = kotaSelect.getAttribute('data-selected');
            loadCities(initialCityId);
        }

        // --- DYNAMIC SECTIONS MANAGEMENT ---

        // Helper to initialize Cascading Dropdowns for a specific address row element
        function initAddressRowCascade(row) {
            const citySelect = row.querySelector('.city-select');
            const districtSelect = row.querySelector('.district-select');
            const villageSelect = row.querySelector('.village-select');

            if (!citySelect) return;

            function loadRowCities(selectedId = '') {
                fetch('<?= base_url("admin/regions/cities") ?>')
                    .then(r => r.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">-- Pilih Kota/Kabupaten --</option>';
                        data.forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.name;
                            if (c.id == selectedId) {
                                opt.selected = true;
                            }
                            citySelect.appendChild(opt);
                        });
                        if (selectedId) {
                            citySelect.dispatchEvent(new Event('change'));
                        }
                    });
            }

            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                districtSelect.innerHTML = '<option value="">-- Pilih Kecamatan --</option>';
                districtSelect.disabled = true;
                villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                villageSelect.disabled = true;

                if (!cityId) return;

                const selectedKecId = districtSelect.getAttribute('data-selected');
                fetch('<?= base_url("admin/regions/districts") ?>?city_id=' + cityId)
                    .then(r => r.json())
                    .then(data => {
                        data.forEach(d => {
                            const opt = document.createElement('option');
                            opt.value = d.id;
                            opt.textContent = d.name;
                            if (d.id == selectedKecId) {
                                opt.selected = true;
                            }
                            districtSelect.appendChild(opt);
                        });
                        districtSelect.disabled = false;
                        if (selectedKecId) {
                            districtSelect.dispatchEvent(new Event('change'));
                            districtSelect.removeAttribute('data-selected');
                        }
                    });
            });

            districtSelect.addEventListener('change', function() {
                const districtId = this.value;
                villageSelect.innerHTML = '<option value="">-- Pilih Kelurahan --</option>';
                villageSelect.disabled = true;

                if (!districtId) return;

                const selectedKelId = villageSelect.getAttribute('data-selected');
                fetch('<?= base_url("admin/regions/villages") ?>?district_id=' + districtId)
                    .then(r => r.json())
                    .then(data => {
                        data.forEach(v => {
                            const opt = document.createElement('option');
                            opt.value = v.id;
                            opt.textContent = v.name;
                            if (v.id == selectedKelId) {
                                opt.selected = true;
                            }
                            villageSelect.appendChild(opt);
                        });
                        villageSelect.disabled = false;
                        if (selectedKelId) {
                            villageSelect.removeAttribute('data-selected');
                        }
                    });
            });

            const initialCityId = citySelect.getAttribute('data-selected');
            loadRowCities(initialCityId);
        }

        // Initialize existing additional address rows
        document.querySelectorAll('.address-row').forEach(row => {
            initAddressRowCascade(row);
        });

        // Add Address Row
        const btnAddAddress = document.getElementById('btn-add-address');
        const addressContainer = document.getElementById('additional-addresses-container');
        if (btnAddAddress && addressContainer) {
            btnAddAddress.addEventListener('click', function() {
                // Check if the "Alamat Lainnya" header exists, if not add it
                if (!addressContainer.querySelector('h6')) {
                    const header = document.createElement('h6');
                    header.className = 'fw-bold text-dark-coffee mb-3 border-top pt-3';
                    header.innerHTML = '<i class="fa-solid fa-map-pin me-2 text-gold"></i>Alamat Lainnya';
                    addressContainer.insertBefore(header, addressContainer.firstChild);
                }

                const tempDiv = document.createElement('div');
                tempDiv.className = 'address-row card bg-light-cream-subtle border p-3 mb-3 position-relative';
                tempDiv.style.backgroundColor = '#fdfbf7';
                tempDiv.innerHTML = `
                    <input type="hidden" name="addresses[id][]" value="">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-address"><i class="fa-solid fa-trash"></i> Hapus</button>
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Label Alamat</label>
                            <input type="text" name="addresses[label][]" class="form-control form-control-sm" placeholder="KTP / Domisili / Kantor" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Alamat Lengkap</label>
                            <input type="text" name="addresses[alamat_lengkap][]" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-bold small text-muted">RT</label>
                            <input type="text" name="addresses[rt][]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label fw-bold small text-muted">RW</label>
                            <input type="text" name="addresses[rw][]" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Kota / Kabupaten</label>
                            <select name="addresses[kota_kabupaten_id][]" class="form-select form-select-sm city-select" required>
                                <option value="">-- Pilih Kota/Kabupaten --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Kecamatan</label>
                            <select name="addresses[kecamatan_id][]" class="form-select form-select-sm district-select" disabled required>
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Kelurahan</label>
                            <select name="addresses[kelurahan_id][]" class="form-select form-select-sm village-select" disabled required>
                                <option value="">-- Pilih Kelurahan --</option>
                            </select>
                        </div>
                    </div>
                `;
                addressContainer.appendChild(tempDiv);
                initAddressRowCascade(tempDiv);
            });

            // Delegate remove address click event
            addressContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-address') || e.target.closest('.btn-remove-address')) {
                    const row = e.target.closest('.address-row');
                    if (row) {
                        row.remove();
                        // If no more address rows, remove the header too
                        if (addressContainer.querySelectorAll('.address-row').length === 0) {
                            const header = addressContainer.querySelector('h6');
                            if (header) header.remove();
                        }
                    }
                }
            });
        }

        // Education Dynamic Input
        const btnAddEdu = document.getElementById('btn-add-education');
        const eduContainer = document.getElementById('education-container');
        if (btnAddEdu && eduContainer) {
            btnAddEdu.addEventListener('click', function() {
                const tempDiv = document.createElement('div');
                tempDiv.className = 'education-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative';
                tempDiv.style.backgroundColor = '#fdfbf7';
                tempDiv.innerHTML = `
                    <input type="hidden" name="educations[id][]" value="">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-education"><i class="fa-solid fa-trash"></i></button>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Jenjang Pendidikan</label>
                            <select name="educations[jenjang][]" class="form-select form-select-sm" required>
                                <option value="">-- Pilih --</option>
                                <option value="SD">SD</option>
                                <option value="SMP">SMP</option>
                                <option value="SMA/SMK">SMA / SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Institusi / Nama Sekolah</label>
                            <input type="text" name="educations[nama_institusi][]" class="form-control form-control-sm" placeholder="Contoh: Universitas Negeri Rimberio" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Tahun / Rentang Tahun</label>
                            <input type="text" name="educations[rentang_tahun][]" class="form-control form-control-sm" placeholder="Contoh: 2017-2021">
                        </div>
                    </div>
                `;
                eduContainer.appendChild(tempDiv);
            });

            eduContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-education') || e.target.closest('.btn-remove-education')) {
                    const row = e.target.closest('.education-row');
                    if (row) row.remove();
                }
            });
        }

        // Organization Dynamic Input
        const btnAddOrg = document.getElementById('btn-add-organization');
        const orgContainer = document.getElementById('organization-container');
        if (btnAddOrg && orgContainer) {
            btnAddOrg.addEventListener('click', function() {
                const tempDiv = document.createElement('div');
                tempDiv.className = 'organization-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative';
                tempDiv.style.backgroundColor = '#fdfbf7';
                tempDiv.innerHTML = `
                    <input type="hidden" name="organizations[id][]" value="">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-organization"><i class="fa-solid fa-trash"></i></button>
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Jabatan</label>
                            <input type="text" name="organizations[jabatan][]" class="form-control form-control-sm" placeholder="Contoh: Ketua Himpunan Mahasiswa" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Nama Organisasi / Instansi</label>
                            <input type="text" name="organizations[nama_organisasi][]" class="form-control form-control-sm" placeholder="Contoh: Universitas Negeri Rimberio" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Tahun / Rentang Tahun</label>
                            <input type="text" name="organizations[rentang_tahun][]" class="form-control form-control-sm" placeholder="Contoh: 2017-2021">
                        </div>
                    </div>
                `;
                orgContainer.appendChild(tempDiv);
            });

            orgContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-organization') || e.target.closest('.btn-remove-organization')) {
                    const row = e.target.closest('.organization-row');
                    if (row) row.remove();
                }
            });
        }

        // Work Experience Dynamic Input
        const btnAddWork = document.getElementById('btn-add-work');
        const workContainer = document.getElementById('work-container');
        if (btnAddWork && workContainer) {
            btnAddWork.addEventListener('click', function() {
                const tempDiv = document.createElement('div');
                tempDiv.className = 'work-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative';
                tempDiv.style.backgroundColor = '#fdfbf7';
                tempDiv.innerHTML = `
                    <input type="hidden" name="work_experiences[id][]" value="">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-work"><i class="fa-solid fa-trash"></i></button>
                    <div class="row g-3">
                        <!-- Row 1 -->
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Jabatan / Posisi</label>
                            <input type="text" name="work_experiences[jabatan][]" class="form-control form-control-sm" placeholder="Contoh: Karyawan Magang" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Nama Perusahaan / Tempat Kerja</label>
                            <input type="text" name="work_experiences[nama_perusahaan][]" class="form-control form-control-sm" placeholder="Contoh: Perusahaan Liceria" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Rentang Tahun</label>
                            <input type="text" name="work_experiences[rentang_tahun][]" class="form-control form-control-sm" placeholder="Contoh: 2021-2022">
                        </div>
                        
                        <!-- Row 2 -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Bidang Pekerjaan</label>
                            <select name="work_experiences[bidang_pekerjaan_id][]" class="form-select form-select-sm">
                                <option value="">-- Pilih Bidang --</option>
                                <?php foreach ($jobFields as $jf): ?>
                                    <option value="<?= $jf['id'] ?>"><?= esc($jf['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Rentang Pendapatan per Bulan</label>
                            <select name="work_experiences[pendapatan_range][]" class="form-select form-select-sm">
                                <option value="">-- Pilih --</option>
                                <option value="< Rp1.000.000">&lt; Rp1.000.000</option>
                                <option value="Rp1.000.000 - Rp3.000.000">Rp1.000.000 - Rp3.000.000</option>
                                <option value="Rp3.000.000 - Rp5.000.000">Rp3.000.000 - Rp5.000.000</option>
                                <option value="Rp5.000.000 - Rp10.000.000">Rp5.000.000 - Rp10.000.000</option>
                                <option value="> Rp10.000.000">&gt; Rp10.000.000</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Alamat Tempat Kerja / Usaha</label>
                            <input type="text" name="work_experiences[alamat_kerja][]" class="form-control form-control-sm" placeholder="Contoh: Cipayung, Depok">
                        </div>
                    </div>
                `;
                workContainer.appendChild(tempDiv);
            });

            workContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-work') || e.target.closest('.btn-remove-work')) {
                    const row = e.target.closest('.work-row');
                    if (row) row.remove();
                }
            });
        }

        // Skills Dynamic Input
        const btnAddSkill = document.getElementById('btn-add-skill');
        const skillContainer = document.getElementById('skill-container');
        if (btnAddSkill && skillContainer) {
            btnAddSkill.addEventListener('click', function() {
                const tempDiv = document.createElement('div');
                tempDiv.className = 'skill-row mb-3 p-3 border rounded bg-light-cream-subtle position-relative';
                tempDiv.style.backgroundColor = '#fdfbf7';
                tempDiv.innerHTML = `
                    <input type="hidden" name="skills[id][]" value="">
                    <button type="button" class="btn btn-sm btn-outline-danger position-absolute end-0 top-0 m-2 btn-remove-skill"><i class="fa-solid fa-trash"></i></button>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small text-muted">Nama Kemampuan / Keahlian</label>
                            <input type="text" name="skills[nama_keahlian][]" class="form-control form-control-sm" placeholder="Contoh: Mampu mengoperasikan perangkat lunak" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small text-muted">Keterangan / Tingkat (Bahasa/Software/Lainnya)</label>
                            <input type="text" name="skills[keterangan][]" class="form-control form-control-sm" placeholder="Contoh: Indonesia (Aktif) / Inggris (Pasif)">
                        </div>
                    </div>
                `;
                skillContainer.appendChild(tempDiv);
            });

            skillContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-skill') || e.target.closest('.btn-remove-skill')) {
                    const row = e.target.closest('.skill-row');
                    if (row) row.remove();
                }
            });
        }
    });
</script>

<?php if (!empty($member)): ?>
<!-- Modal Catat Riwayat Masa Lalu UPA -->
<div class="modal fade" id="modalAddUpaHistory" tabindex="-1" aria-labelledby="modalAddUpaHistoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="<?= base_url('admin/upa/member_history/save') ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <input type="hidden" name="member_id" value="<?= $member['id'] ?>">
            
            <div class="modal-header bg-dark-coffee text-cream">
                <h5 class="modal-title fw-bold" id="modalAddUpaHistoryLabel">
                    <i class="fa-solid fa-clock-rotate-left text-gold me-2"></i>Catat Riwayat Pembinaan UPA Masa Lalu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">Kode Group UPA <span class="text-danger">*</span></label>
                    <input type="text" name="kode_upa" class="form-control font-monospace" placeholder="Contoh: PM3101" required>
                    <div class="form-text">Kode unik kelompok UPA saat itu (contoh: PM3101).</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Group / Kelompok</label>
                    <input type="text" name="nama_group" class="form-control" placeholder="Contoh: Group PM3101">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nama Pembimbing (Naqib)</label>
                    <input type="text" name="pembimbing_nama" class="form-control" placeholder="Contoh: Khairul Wajdi">
                    <div class="form-text">Nama naqib/pembimbing yang membina selama periode tersebut.</div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold">Tahun Mulai <span class="text-danger">*</span></label>
                        <input type="text" name="tahun_mulai" class="form-control" placeholder="Contoh: 2023" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold">Tahun Selesai</label>
                        <input type="text" name="tahun_selesai" class="form-control" placeholder="Contoh: 2024">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Status Akhir di Group Tersebut <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="pindah" selected>Pindah / Mutasi ke Group Lain</option>
                        <option value="alumni">Alumni</option>
                        <option value="selesai">Selesai Masa Pembinaan</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Catatan / Keterangan</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Mutasi pembinaan ke kelompok PM4106"></textarea>
                </div>
            </div>
            <div class="modal-footer border-top">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-gold fw-bold">
                    <i class="fa-solid fa-floppy-disk me-1"></i>Simpan Riwayat
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
