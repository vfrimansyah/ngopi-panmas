<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card admin-card border-0 p-4 bg-white" style="max-width: 750px;">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="vintage-serif mb-0"><i class="fa-solid fa-calendar me-2 text-gold"></i><?= $event ? 'Edit Agenda Event' : 'Buat Agenda Event Baru' ?></h5>
        <a href="<?= base_url('admin/events') ?>" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-2"></i>Kembali</a>
    </div>

    <?php if (session('errors')): ?>
        <div class="alert alert-danger">
            <?php foreach (session('errors') as $error): ?>
                <?= $error ?><br>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="<?= $event ? base_url('admin/events/update/' . $event['id']) : base_url('admin/events/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="judul" class="form-label fw-bold">Judul Event</label>
            <input type="text" class="form-control" id="judul" name="judul" value="<?= old('judul', $event['judul'] ?? '') ?>" placeholder="Masukkan judul event" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="tanggal_mulai" class="form-label fw-bold">Tanggal Mulai</label>
                <input type="datetime-local" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= old('tanggal_mulai', $event['tanggal_mulai'] ? date('Y-m-d\TH:i', strtotime($event['tanggal_mulai'])) : date('Y-m-d\T09:00')) ?>" required>
            </div>
            <div class="col-md-6">
                <label for="tanggal_selesai" class="form-label fw-bold">Tanggal Selesai (Opsional)</label>
                <input type="datetime-local" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= old('tanggal_selesai', $event['tanggal_selesai'] ? date('Y-m-d\TH:i', strtotime($event['tanggal_selesai'])) : '') ?>">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="lokasi" class="form-label fw-bold">Lokasi Pelaksanaan</label>
                <input type="text" class="form-control" id="lokasi" name="lokasi" value="<?= old('lokasi', $event['lokasi'] ?? '') ?>" placeholder="Masukkan tempat/lokasi event">
            </div>
            <div class="col-md-6">
                <label for="status" class="form-label fw-bold">Status Event</label>
                <select class="form-select" id="status" name="status">
                    <option value="upcoming" <?= old('status', $event['status'] ?? 'upcoming') === 'upcoming' ? 'selected' : '' ?>>Upcoming (Akan Datang)</option>
                    <option value="ongoing" <?= old('status', $event['status'] ?? '') === 'ongoing' ? 'selected' : '' ?>>Ongoing (Sedang Berjalan)</option>
                    <option value="completed" <?= old('status', $event['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed (Selesai)</option>
                    <option value="cancelled" <?= old('status', $event['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled (Batal)</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label fw-bold">Deskripsi Event / Keterangan</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi" rows="6" placeholder="Tuliskan deskripsi lengkap event ini..." required><?= old('deskripsi', $event['deskripsi'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <label for="gambar" class="form-label fw-bold">Gambar Cover / Pamflet</label>
            <input class="form-control" type="file" id="gambar" name="gambar">
            <small class="text-muted">Format: JPG, JPEG, PNG. Max: 2MB.</small>
        </div>

        <?php if ($event && $event['gambar']): ?>
            <div class="mb-4">
                <p class="fw-bold mb-2">Pamflet Saat Ini:</p>
                <img src="<?= base_url('uploads/events/' . $event['gambar']) ?>" alt="Pamflet event saat ini" class="rounded img-thumbnail" style="max-height: 150px;">
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-coffee px-4 py-2"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Event</button>
    </form>
</div>

<?= $this->endSection() ?>
