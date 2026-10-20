<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Panel — Ngobrol Pikiran Indonesia' ?></title>
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/logo.png') ?>">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <!-- Custom Admin CSS -->
    <link href="<?= base_url('assets/css/admin.css') ?>" rel="stylesheet">
    <!-- DataTables Bootstrap 5 CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <?= $this->renderSection('css') ?>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark-coffee border-end" id="sidebar-wrapper">
            <div class="sidebar-heading text-center py-3 border-bottom">
                <a href="<?= base_url() ?>" class="d-inline-block mb-1">
                    <img src="<?= base_url('assets/images/logo.png') ?>" alt="NGOPI" style="height: 38px; width: auto; background: #ffffff; padding: 3px 8px; border-radius: 6px;">
                </a>
                <div><small class="text-gold text-uppercase fw-bold" style="letter-spacing: 1px; font-size: 0.72rem;">Admin Panel</small></div>
            </div>
            <div class="list-group list-group-flush my-3">
                <?php
                $menuModel = new \App\Models\MenuModel();
                $dynamicMenus = $menuModel->getMenusForUser(auth()->user());

                // Cek apakah URL menu cocok dengan halaman saat ini
                $menuUrlMatches = function (string $url): bool {
                    if ($url === '') return false;
                    if ($url === 'admin/dashboard') {
                        return url_is('admin/dashboard*') || url_is('admin');
                    }
                    return url_is($url . '*');
                };

                // Cari URL menu paling spesifik (terpanjang) yang cocok di seluruh tree,
                // agar 'admin/members' tidak ikut aktif di 'admin/members/ekonomi'
                $bestMenuUrl = '';
                $findBest = function (array $items) use (&$findBest, &$bestMenuUrl, $menuUrlMatches) {
                    foreach ($items as $it) {
                        $u = trim((string)($it['url'] ?? ''));
                        if ($u !== '' && $menuUrlMatches($u) && strlen($u) > strlen($bestMenuUrl)) {
                            $bestMenuUrl = $u;
                        }
                        if (!empty($it['children'])) $findBest($it['children']);
                    }
                };
                $findBest($dynamicMenus);

                // Apakah menu ini / salah satu turunannya sedang aktif
                $isBranchActive = function (array $item) use (&$isBranchActive, &$bestMenuUrl): bool {
                    $u = trim((string)($item['url'] ?? ''));
                    if ($u !== '' && $u === $bestMenuUrl) return true;
                    foreach ($item['children'] ?? [] as $c) {
                        if ($isBranchActive($c)) return true;
                    }
                    return false;
                };

                // Render menu secara rekursif (mendukung sub-menu bertingkat)
                $renderMenus = function (array $items, int $level) use (&$renderMenus, $isBranchActive, &$bestMenuUrl) {
                    foreach ($items as $menu) {
                        $hasChildren = !empty($menu['children']);
                        $menuUrl     = trim((string)($menu['url'] ?? ''));
                        $isActive    = $isBranchActive($menu);

                        // Padding & ukuran berdasarkan level
                        $padStyle  = $level > 0 ? 'padding-left: ' . (1.5 + $level * 1.5) . 'rem;' : '';
                        $sizeClass = $level > 0 ? 'py-2 small' : 'py-3';
                        $iconGap   = $level > 0 ? 'me-2' : 'me-3';

                        if ($hasChildren) {
                            $menuId = 'menuCollapse_' . $menu['id'];
                            ?>
                            <!-- Dropdown Menu (level <?= $level ?>) -->
                            <a href="#<?= $menuId ?>" data-bs-toggle="collapse"
                               class="list-group-item list-group-item-action bg-transparent text-cream border-0 <?= $sizeClass ?> d-flex align-items-center justify-content-between <?= ($isActive && $level === 0) ? 'active' : '' ?>"
                               style="<?= $padStyle ?>"
                               aria-expanded="<?= $isActive ? 'true' : 'false' ?>">
                                <span>
                                    <?php if (!empty($menu['icon'])): ?>
                                        <i class="<?= esc($menu['icon']) ?> <?= $iconGap ?> text-gold"></i>
                                    <?php endif; ?>
                                    <?= esc($menu['title']) ?>
                                </span>
                                <i class="fa-solid fa-chevron-down text-gold" style="font-size: <?= $level > 0 ? '0.7rem' : '0.8rem' ?>;"></i>
                            </a>
                            <div class="collapse <?= $isActive ? 'show' : '' ?>" id="<?= $menuId ?>">
                                <?php $renderMenus($menu['children'], $level + 1); ?>
                            </div>
                            <?php
                        } else {
                            // Sub-menu tanpa URL & tanpa anak tidak perlu ditampilkan
                            if ($level > 0 && $menuUrl === '') continue;
                            $isLinkActive = ($menuUrl !== '' && $menuUrl === $bestMenuUrl);
                            ?>
                            <a href="<?= $menuUrl === '' ? base_url() : base_url($menuUrl) ?>"
                               <?= $menuUrl === '' ? 'target="_blank"' : '' ?>
                               class="list-group-item list-group-item-action bg-transparent text-cream border-0 <?= $sizeClass ?> <?= $isLinkActive ? 'active' : '' ?>"
                               style="<?= $padStyle ?>">
                                <?php if (!empty($menu['icon'])): ?>
                                    <i class="<?= esc($menu['icon']) ?> <?= $iconGap ?> text-gold"></i>
                                <?php endif; ?>
                                <?= esc($menu['title']) ?>
                            </a>
                            <?php
                        }
                    }
                };

                $renderMenus($dynamicMenus, 0);
                ?>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper" class="w-100">
            <!-- Topbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom py-3 px-4 shadow-sm">
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-secondary btn-sm me-3" id="menu-toggle"><i class="fa-solid fa-bars"></i></button>
                    <h5 class="mb-0 fw-bold d-none d-md-block text-dark-coffee"><?= $title ?? 'Dashboard' ?></h5>
                </div>
                <div class="ms-auto d-flex align-items-center">
                    <?php
                        $currentUser = auth()->user();
                        if ($currentUser) {
                            $groups = $currentUser->getGroups();
                            $db = \Config\Database::connect();
                            $customRoleTitles = [];
                            if ($db->tableExists('admin_custom_roles')) {
                                $crs = $db->table('admin_custom_roles')->get()->getResultArray();
                                foreach ($crs as $cr) {
                                    $customRoleTitles[$cr['slug']] = $cr['title'];
                                }
                            }
                            $groupLabels = array_merge([
                                'admin'      => 'Super Admin',
                                'bph'        => 'BPH',
                                'kaderisasi' => 'Kaderisasi',
                                'seksi'      => 'Seksi',
                                'user'       => 'User',
                            ], $customRoleTitles);
                            $groupColors = ['admin'=>'danger','bph'=>'warning','kaderisasi'=>'info','seksi'=>'success','user'=>'secondary'];

                            foreach ($groups as $g):
                                if ($g === 'user' && count($groups) > 1) continue;
                    ?>
                                <span class="badge bg-<?= $groupColors[$g] ?? 'primary' ?> me-2" style="font-size:0.7rem;">
                                    <?= esc($groupLabels[$g] ?? ucfirst($g)) ?>
                                </span>
                    <?php
                            endforeach;
                        }
                    ?>
                    <a href="<?= base_url('admin/meja/profile') ?>" class="me-3 text-muted text-decoration-none small" title="Profil Saya">
                        <i class="fa-solid fa-circle-user me-1 text-gold"></i><?= esc(auth()->user()?->username ?? 'User') ?>
                    </a>
                    <a href="<?= base_url('logout') ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                </div>
            </nav>

            <!-- Main Content Container -->
            <div class="container-fluid p-4">
                <!-- Session Alerts -->
                <?php if (session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i><?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i><?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
        </div>
        <!-- /#page-content-wrapper -->
    </div>

    <!-- Bootstrap 5.3 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', event => {
            const menuToggle = document.body.querySelector('#menu-toggle');
            if (menuToggle) {
                menuToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });

        $(document).ready(function() {
            // Disable DataTables alert popups globally
            if ($.fn.DataTable) {
                $.fn.dataTable.ext.errMode = 'none';
            }

            // Auto initialize DataTables on any element with .table class (excluding .no-datatable)
            if ($('table.table:not(.no-datatable)').length > 0) {
                $('table.table:not(.no-datatable)').each(function() {
                    // Prevent double initialization if view does it manually
                    if (!$.fn.DataTable.isDataTable(this)) {
                        $(this).DataTable({
                            pageLength: 10,
                            language: {
                                search: "Cari:",
                                lengthMenu: "Tampilkan _MENU_ entri",
                                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                                infoFiltered: "(disaring dari _MAX_ total entri)",
                                zeroRecords: "Tidak ditemukan data yang sesuai",
                                emptyTable: "Tidak ada data yang tersedia pada tabel ini",
                                paginate: {
                                    first: "Pertama",
                                    previous: "Sebelumnya",
                                    next: "Selanjutnya",
                                    last: "Terakhir"
                                }
                            }
                        });
                    }
                });
            }
        });
    </script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            if ($.fn.select2) {
                $('.select2-basic').select2({
                    theme: 'bootstrap-5',
                    placeholder: '-- Cari atau Pilih Nama Anda --',
                    allowClear: true,
                    width: '100%'
                });
            }
        });
    </script>
    <?= $this->renderSection('js') ?>
</body>
</html>
