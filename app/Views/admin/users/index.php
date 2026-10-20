<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: var(--dark-coffee)">
            <i class="fa-solid fa-user-gear me-2 text-gold"></i>Manajemen User
        </h4>
        <p class="text-muted mb-0 small">Kelola akun pengguna dan role akses panel admin</p>
    </div>
    <?php if ($can_create ?? false): ?>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-sm px-4 fw-semibold" style="background:#3d1f0f;color:#fff;border:none;">
        <i class="fa-solid fa-user-plus me-2"></i>Tambah User
    </a>
    <?php endif; ?>
</div>

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <?php
    $roleStats = ['admin' => 0, 'bph' => 0, 'kaderisasi' => 0, 'seksi' => 0, 'user' => 0];
    foreach ($users as $u) {
        $grp = $u['groups'] ?? ['user'];
        $userGroups = is_array($grp) ? $grp : explode(',', (string) $grp);
        $firstGroup = $userGroups[0] ?? 'user';
        if (isset($roleStats[$firstGroup])) $roleStats[$firstGroup]++;
    }
    $roleConfig = [
        'admin'      => ['icon' => 'fa-shield-halved', 'label' => 'Super Admin', 'color' => '#ef4444'],
        'bph'        => ['icon' => 'fa-star',          'label' => 'BPH',         'color' => '#f59e0b'],
        'kaderisasi' => ['icon' => 'fa-people-group',  'label' => 'Kaderisasi',  'color' => '#3b82f6'],
        'seksi'      => ['icon' => 'fa-sitemap',       'label' => 'Seksi',       'color' => '#10b981'],
        'user'       => ['icon' => 'fa-user',          'label' => 'User',        'color' => '#6b7280'],
    ];
    ?>
    <?php foreach ($roleConfig as $key => $cfg): ?>
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid <?= $cfg['color'] ?> !important;">
            <div class="card-body py-3 d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:42px; height:42px; background: <?= $cfg['color'] ?>22;">
                    <i class="fa-solid <?= $cfg['icon'] ?>" style="color:<?= $cfg['color'] ?>"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5 lh-1" style="color:<?= $cfg['color'] ?>"><?= $roleStats[$key] ?></div>
                    <div class="text-muted small"><?= $cfg['label'] ?></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Users Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="usersTable">
                <thead style="background: var(--dark-coffee); color: var(--cream);">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Password</th>
                        <th>Terakhir Aktif</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $i => $user): ?>
                    <?php
                        $userGroups = is_array($user['groups']) ? $user['groups'] : (empty($user['groups']) ? ['user'] : explode(',', (string) $user['groups']));
                        $roleBadgeColors = [
                            'admin'      => 'danger',
                            'bph'        => 'warning',
                            'kaderisasi' => 'info',
                            'seksi'      => 'success',
                            'user'       => 'secondary',
                        ];
                        $isMe = auth()->id() == $user['id'];
                    ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:34px; height:34px; font-size:0.75rem;
                                            background: linear-gradient(135deg, var(--dark-coffee), var(--gold));">
                                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="fw-semibold"><?= esc($user['username']) ?></div>
                                    <?php if ($isMe): ?><span class="badge bg-success-subtle text-success" style="font-size:0.65rem">Anda</span><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $userGroups = is_array($user['groups']) ? $user['groups'] : (array)$user['groups'];
                                foreach ($userGroups as $grp):
                                    $badgeColor = $roleBadgeColors[$grp] ?? 'primary';
                                    $badgeLabel = $roles[$grp] ?? ucfirst($grp);
                            ?>
                                <span class="badge bg-<?= $badgeColor ?> me-1 mb-1"><?= esc($badgeLabel) ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td>
                            <?php if ($user['active']): ?>
                                <span class="badge bg-success-subtle text-success"><i class="fa-solid fa-circle-check me-1"></i>Aktif</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['force_password_reset']): ?>
                                <span class="badge bg-warning-subtle text-warning"><i class="fa-solid fa-key me-1"></i>Belum diganti</span>
                            <?php else: ?>
                                <span class="badge bg-light text-muted"><i class="fa-solid fa-check me-1"></i>Sudah diganti</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= $user['last_active'] ? date('d/m/Y H:i', strtotime($user['last_active'])) : '—' ?>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex gap-1 justify-content-center">
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>"
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (! $isMe): ?>
                                <?php if ($can_update ?? false): ?>
                                <a href="<?= base_url('admin/users/reset-password/' . $user['id']) ?>"
                                   class="btn btn-sm btn-outline-warning" title="Reset Password ke Default"
                                   onclick="return confirm('Reset password user ini ke default P4nm4s?')">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($can_delete ?? false): ?>
                                <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>"
                                   class="btn btn-sm btn-outline-danger" title="Hapus"
                                   onclick="return confirm('Hapus user <?= esc($user['username']) ?>? Tindakan ini tidak dapat dibatalkan.')">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
