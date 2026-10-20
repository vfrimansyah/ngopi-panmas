<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>

<div class="card admin-card border-0 p-4 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
        <h5 class="vintage-serif mb-0"><i class="fa-solid fa-newspaper me-2 text-gold"></i>Kelola Postingan Berita / Artikel</h5>
        <?php if ($can_create ?? false): ?>
        <a href="<?= base_url('admin/news/create') ?>" class="btn btn-coffee btn-sm"><i class="fa-solid fa-plus me-2"></i>Tambah Berita</a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table align-middle no-datatable">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Judul Berita</th>
                    <th>Status</th>
                    <th>Diposting Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($news)): ?>
                    <?php foreach ($news as $item): ?>
                        <tr>
                            <td>
                                <div class="rounded border bg-light overflow-hidden" style="width: 50px; height: 50px;">
                                    <?php 
                                    $cover = $item['foto_cover'] ?? null;
                                    $imgSrc = null;
                                    if ($cover) {
                                        if (file_exists('uploads/news/' . $cover)) {
                                            $imgSrc = base_url('uploads/news/' . $cover);
                                        } elseif (file_exists('uploads/' . $cover)) {
                                            $imgSrc = base_url('uploads/' . $cover);
                                        }
                                    }
                                    ?>
                                    <?php if ($imgSrc): ?>
                                        <img src="<?= $imgSrc ?>" alt="Gambar" class="w-100 h-100 object-fit-cover">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light text-muted">
                                            <i class="fa-solid fa-newspaper"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= esc($item['judul']) ?></strong><br>
                                <small class="text-muted">Slug: <?= esc($item['slug']) ?></small>
                            </td>
                            <td>
                                <span class="badge bg-<?= $item['status'] === 'published' ? 'success' : 'secondary' ?>"><?= esc($item['status']) ?></span>
                            </td>
                            <td><span class="small text-muted"><?= !empty($item['published_at']) ? date('d M Y H:i', strtotime($item['published_at'])) : '-' ?></span></td>
                            <td>
                                <?php 
                                $isOwner = !empty($is_full_admin) || ((int)($item['created_by'] ?? 0) === (int)($current_user_id ?? 0));
                                ?>
                                <?php if (($can_update ?? false) && $isOwner): ?>
                                <a href="<?= base_url('admin/news/edit/' . $item['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                <?php endif; ?>
                                <?php if (($can_delete ?? false) && $isOwner): ?>
                                <a href="<?= base_url('admin/news/delete/' . $item['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus berita ini?')" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada postingan berita.</td>
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
