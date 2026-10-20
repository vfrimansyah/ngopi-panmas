<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card admin-card border-0 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="vintage-serif mb-0"><i class="fa-solid fa-calendar me-2 text-gold"></i>Kelola Event & Agenda</h5>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/events/create') ?>" class="btn btn-coffee btn-sm"><i class="fa-solid fa-plus me-2"></i>Tambah Event</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table align-middle no-datatable">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Judul Event</th>
                    <th>Waktu Mulai</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($events)): ?>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td>
                                <div class="rounded border bg-light overflow-hidden" style="width: 55px; height: 55px;">
                                    <?php if ($event['gambar']): ?>
                                        <img src="<?= base_url('uploads/events/' . $event['gambar']) ?>" alt="Event" class="w-100 h-100 object-fit-cover">
                                    <?php else: ?>
                                        <img src="https://images.unsplash.com/photo-1541167760496-1628856ab772?auto=format&fit=crop&q=80&w=100" alt="Default" class="w-100 h-100 object-fit-cover">
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= esc($event['judul']) ?></strong>
                            </td>
                            <td><span class="small text-muted"><?= date('d M Y H:i', strtotime($event['tanggal_mulai'])) ?></span></td>
                            <td><?= esc($event['lokasi']) ?: '-' ?></td>
                            <td>
                                <span class="badge bg-<?= $event['status'] === 'upcoming' ? 'info' : ($event['status'] === 'ongoing' ? 'warning' : ($event['status'] === 'completed' ? 'success' : 'danger')) ?>"><?= esc($event['status']) ?></span>
                            </td>
                            <td>
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/events/edit/' . $event['id']) ?>" class="btn btn-sm btn-outline-primary me-1"><i class="fa-solid fa-pen-to-square"></i></a>
                                <?php endif; ?>
                                <?php if ($can_delete ?? false): ?>
                                <a href="<?= base_url('admin/events/delete/' . $event['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus agenda event ini?')" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada agenda event.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        <?= $pager ? $pager->links() : '' ?>
    </div>
</div>

<?= $this->endSection() ?>
