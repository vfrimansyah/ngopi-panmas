<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// =============================================
// Public Routes
// =============================================
$routes->get('/', 'Home::index');

// Anggota
$routes->get('profil', 'MemberController::profile', ['filter' => 'session']);
$routes->get('anggota', 'MemberController::index', ['filter' => 'session']);
$routes->get('anggota/(:num)', 'MemberController::show/$1', ['filter' => 'session']);

// Katalog Produk
$routes->get('katalog', 'ProductController::index');
$routes->get('katalog/(:any)', 'ProductController::show/$1');

// Artikel
$routes->get('artikel', 'ArtikelController::index');
$routes->get('artikel/(:any)', 'ArtikelController::show/$1');

// Ide
$routes->get('ide', 'IdeController::index');
$routes->get('ide/create', 'IdeController::create');
$routes->post('ide/store', 'IdeController::store');
$routes->get('ide/(:any)', 'IdeController::show/$1');

// Diskusi
$routes->get('diskusi', 'DiskusiController::index');
$routes->get('diskusi/create', 'DiskusiController::create');
$routes->post('diskusi/store', 'DiskusiController::store');
$routes->post('diskusi/reply/(:num)', 'DiskusiController::reply/$1');
$routes->post('diskusi/vote/(:num)', 'DiskusiController::vote/$1');
$routes->get('diskusi/(:any)', 'DiskusiController::show/$1');

// Berita
$routes->get('berita', 'NewsController::index');
$routes->get('berita/(:any)', 'NewsController::show/$1');

// Event
$routes->get('event', 'EventController::index');
$routes->get('event/(:any)', 'EventController::show/$1');

// Galeri Media Publik
$routes->get('galeri', 'GaleriController::index');

// ── Custom Dynamic Forms (Public) ──────────────────────
$routes->get('form/(:segment)', 'PublicFormController::show/$1');
$routes->post('form/(:segment)', 'PublicFormController::submit/$1');
$routes->get('form/(:segment)/success', 'PublicFormController::success/$1');

// =============================================
// Admin Routes (protected by AdminFilter — login + role check)
// =============================================
$routes->group('admin', ['namespace' => 'App\Controllers\Admin', 'filter' => 'admin_access'], function ($routes) {
    $routes->get('/', 'DashboardController::index');
    $routes->get('dashboard', 'DashboardController::index');

    // ── Meja Saya (Personal Desk: Profile, Mutabaah, Iuran) ───────────────
    $routes->group('meja', function ($routes) {
        $routes->get('/',               'MejaSayaController::index');
        $routes->get('profile',         'MejaSayaController::profile');
        $routes->post('profile/update', 'MejaSayaController::updateProfile');
        $routes->post('profile/link',   'MejaSayaController::linkMember');
        $routes->get('mutabaah',        'MejaSayaController::mutabaah');
        $routes->post('mutabaah/save',  'MejaSayaController::saveMutabaah');
        $routes->get('iuran',           'MejaSayaController::iuran');

        // Lembaga & Legalitas Yayasan
        $routes->get('lembaga',                  'LembagaController::index');
        $routes->get('lembaga/create',           'LembagaController::create');
        $routes->post('lembaga/store',           'LembagaController::store');
        $routes->get('lembaga/(:num)/edit',      'LembagaController::edit/$1');
        $routes->post('lembaga/(:num)/update',   'LembagaController::update/$1');
        $routes->post('lembaga/(:num)/delete',   'LembagaController::delete/$1');
        $routes->get('lembaga/ajax/(:num)',      'LembagaController::getAjax/$1');
        $routes->get('lembaga/get-regencies',    'LembagaController::getRegencies');
        $routes->get('lembaga/get-districts',    'LembagaController::getDistricts');
        $routes->get('lembaga/get-villages',     'LembagaController::getVillages');

        // Proposal Saya
        $routes->get('proposals',                'ProposalController::index');
        $routes->get('proposals/create',         'ProposalController::create');
        $routes->post('proposals/store',         'ProposalController::store');
        $routes->get('proposals/(:num)/edit',    'ProposalController::edit/$1');
        $routes->post('proposals/(:num)/update', 'ProposalController::update/$1');
        $routes->post('proposals/(:num)/delete', 'ProposalController::delete/$1');
        $routes->get('proposals/(:num)/preview', 'ProposalController::preview/$1');
        $routes->get('proposals/(:num)/print',   'ProposalController::printPdf/$1');
        $routes->get('proposals/(:num)/word',    'ProposalController::exportWord/$1');
    });

    // ── Rekapitulasi Proposal & Pokir (Admin / BPH) ────────────────────────
    $routes->get('proposals',                    'ProposalController::adminIndex');
    $routes->post('proposals/(:num)/status',     'ProposalController::updateStatus/$1');

    // ── Form & Survei (Dynamic Form Builder & Responses) ───
    $routes->group('forms', function ($routes) {
        $routes->get('/',                           'CustomFormController::index');
        $routes->get('check-slug',                  'CustomFormController::checkSlug');
        $routes->get('create',                      'CustomFormController::create');
        $routes->post('store',                      'CustomFormController::store');
        $routes->get('(:num)/edit',                 'CustomFormController::edit/$1');
        $routes->post('(:num)/update',              'CustomFormController::update/$1');
        $routes->post('(:num)/delete',              'CustomFormController::delete/$1');
        $routes->post('(:num)/toggle',              'CustomFormController::toggleStatus/$1');
        $routes->get('(:num)/builder',              'CustomFormController::builder/$1');
        $routes->post('(:num)/fields/save',         'CustomFormController::saveField/$1');
        $routes->get('(:num)/fields/(:num)/delete', 'CustomFormController::deleteField/$1/$2');
        $routes->get('(:num)/responses',            'CustomFormController::responses/$1');
        $routes->get('responses/detail/(:num)',     'CustomFormController::responseDetail/$1');
        $routes->post('responses/link',             'CustomFormController::linkResponse');
        $routes->post('(:num)/responses/(:num)/delete', 'CustomFormController::deleteResponse/$1/$2');
        $routes->get('(:num)/responses/(:num)/delete',  'CustomFormController::deleteResponse/$1/$2');
        $routes->get('(:num)/export',               'CustomFormController::export/$1');
    });

    // Members CRUD
    $routes->get('members', 'MemberController::index');
    $routes->get('members/ekonomi', 'MemberController::ekonomi');
    $routes->post('members/ekonomi/quick-update', 'MemberController::quickUpdateEkonomi');
    $routes->get('members/ekonomi/export', 'MemberController::exportEkonomi');
    $routes->get('members/ekonomi/keluarga', 'MemberController::ekonomiKeluarga');
    $routes->get('members/ekonomi/keluarga/export', 'MemberController::exportEkonomiKeluarga');
    $routes->get('members/dashboard', 'MemberController::dashboard');
    $routes->get('members/pendidikan', 'MemberController::pendidikan');
    $routes->get('members/pendidikan/export', 'MemberController::exportPendidikan');
    $routes->post('members/pendidikan/quick-update', 'MemberController::quickUpdatePendidikan');
    $routes->get('members/create', 'MemberController::create');
    $routes->post('members/store', 'MemberController::store');
    $routes->get('members/edit/(:num)', 'MemberController::edit/$1');
    $routes->post('members/update/(:num)', 'MemberController::update/$1');

    // Kelola UPA
    $routes->get('upa', 'UpaController::index');
    $routes->get('upa/create', 'UpaController::create');
    $routes->post('upa/store', 'UpaController::store');
    $routes->get('upa/edit/(:any)', 'UpaController::edit/$1');
    $routes->post('upa/update/(:any)', 'UpaController::update/$1');
    $routes->get('upa/delete/(:any)', 'UpaController::delete/$1');
    $routes->get('upa/detail/(:any)', 'UpaController::detail/$1');
    $routes->post('upa/add_member/(:any)', 'UpaController::addMember/$1');
    $routes->post('upa/transfer_member/(:any)', 'UpaController::transferMember/$1');
    $routes->match(['get', 'post'], 'upa/remove_member/(:any)/(:num)', 'UpaController::removeMember/$1/$2');
    $routes->get('upa/member_history/(:num)', 'UpaController::memberHistory/$1');
    $routes->post('upa/member_history/save', 'UpaController::saveMemberHistory');
    $routes->match(['get', 'post'], 'upa/member_history/delete/(:num)', 'UpaController::deleteMemberHistory/$1');
    $routes->get('upa/get-villages', 'UpaController::getVillages');
    $routes->get('members/delete/(:num)', 'MemberController::delete/$1');
    // Promote to User Portal
    $routes->match(['get', 'post'], 'members/promote/(:num)', 'MemberController::promoteToUser/$1');
    $routes->post('members/bulk-promote', 'MemberController::bulkPromote');

    // Products CRUD
    $routes->get('products', 'ProductController::index');
    $routes->get('products/create', 'ProductController::create');
    $routes->post('products/store', 'ProductController::store');
    $routes->get('products/edit/(:num)', 'ProductController::edit/$1');
    $routes->post('products/update/(:num)', 'ProductController::update/$1');
    $routes->get('products/delete/(:num)', 'ProductController::delete/$1');
    $routes->get('products/categories', 'ProductController::categories');
    $routes->post('products/categories/store', 'ProductController::storeCategory');
    $routes->get('products/categories/delete/(:num)', 'ProductController::deleteCategory/$1');

    // News CRUD
    $routes->get('news', 'NewsController::index');
    $routes->get('news/create', 'NewsController::create');
    $routes->post('news/store', 'NewsController::store');
    $routes->get('news/edit/(:num)', 'NewsController::edit/$1');
    $routes->post('news/update/(:num)', 'NewsController::update/$1');
    $routes->get('news/delete/(:num)', 'NewsController::delete/$1');
    $routes->get('news/categories', 'NewsController::categories');
    $routes->post('news/categories/store', 'NewsController::storeCategory');
    $routes->get('news/categories/delete/(:num)', 'NewsController::deleteCategory/$1');

    // Events CRUD
    $routes->get('events', 'EventController::index');
    $routes->get('events/create', 'EventController::create');
    $routes->post('events/store', 'EventController::store');
    $routes->get('events/edit/(:num)', 'EventController::edit/$1');
    $routes->post('events/update/(:num)', 'EventController::update/$1');
    $routes->get('events/delete/(:num)', 'EventController::delete/$1');

    // Matching Review
    $routes->get('match/spouse', 'MatchController::spouse');
    $routes->post('match/spouse/confirm/(:num)', 'MatchController::confirmSpouse/$1');
    $routes->post('match/spouse/reject/(:num)', 'MatchController::rejectSpouse/$1');
    $routes->get('match/child', 'MatchController::child');
    $routes->post('match/child/confirm/(:num)', 'MatchController::confirmChild/$1');
    $routes->post('match/child/separate/(:num)', 'MatchController::separateChild/$1');

    // Regions AJAX API
    $routes->get('regions/cities', 'RegionController::getCities');
    $routes->get('regions/districts', 'RegionController::getDistricts');
    $routes->get('regions/villages', 'RegionController::getVillages');

    // User Management (Admin only)
    $routes->get('users', 'UserManagementController::index');
    $routes->get('users/create', 'UserManagementController::create');
    $routes->post('users/store', 'UserManagementController::store');
    $routes->get('users/edit/(:num)', 'UserManagementController::edit/$1');
    $routes->post('users/update/(:num)', 'UserManagementController::update/$1');
    $routes->get('users/delete/(:num)', 'UserManagementController::delete/$1');
    $routes->get('users/reset-password/(:num)', 'UserManagementController::resetPassword/$1');

    // Page Role — CRUD
    $routes->get('roles', 'RoleController::index');
    $routes->get('roles/create', 'RoleController::create');
    $routes->post('roles/store', 'RoleController::store');
    $routes->get('roles/edit/(:segment)', 'RoleController::edit/$1');
    $routes->post('roles/update/(:segment)', 'RoleController::update/$1');
    $routes->get('roles/copy/(:segment)', 'RoleController::copy/$1');
    $routes->get('roles/delete/(:segment)', 'RoleController::delete/$1');

    // Menu Management (Admin only)
    $routes->get('menus', 'MenuController::index');
    $routes->get('menus/create', 'MenuController::create');
    $routes->post('menus/store', 'MenuController::store');
    $routes->get('menus/edit/(:num)', 'MenuController::edit/$1');
    $routes->post('menus/update/(:num)', 'MenuController::update/$1');
    $routes->get('menus/delete/(:num)', 'MenuController::delete/$1');

    // Website Settings CMS (Admin only)
    $routes->get('settings', 'SettingController::index');
    $routes->post('settings/update', 'SettingController::update');

    // Pemilu Routes
    $routes->group('pemilu', function ($routes) {
        // Partai
        $routes->get('partai', 'PartaiController::index');
        $routes->get('partai/create', 'PartaiController::create');
        $routes->post('partai/store', 'PartaiController::store');
        $routes->get('partai/edit/(:num)', 'PartaiController::edit/$1');
        $routes->post('partai/update/(:num)', 'PartaiController::update/$1');
        $routes->get('partai/delete/(:num)', 'PartaiController::delete/$1');

        // Peserta Pemilu
        $routes->get('peserta', 'PesertaPemiluController::index');
        $routes->get('peserta/create', 'PesertaPemiluController::create');
        $routes->post('peserta/store', 'PesertaPemiluController::store');
        $routes->get('peserta/edit/(:num)', 'PesertaPemiluController::edit/$1');
        $routes->post('peserta/update/(:num)', 'PesertaPemiluController::update/$1');
        $routes->get('peserta/delete/(:num)', 'PesertaPemiluController::delete/$1');

        // TPS
        $routes->get('tps', 'TpsController::index');
        $routes->get('tps/export-excel', 'TpsController::exportExcel');
        $routes->get('tps/create', 'TpsController::create');
        $routes->post('tps/store', 'TpsController::store');
        $routes->get('tps/edit/(:num)', 'TpsController::edit/$1');
        $routes->post('tps/update/(:num)', 'TpsController::update/$1');
        $routes->get('tps/delete/(:num)', 'TpsController::delete/$1');
        
        // AJAX Region API lookups for TPS Form
        $routes->get('tps/get-regencies', 'TpsController::getRegencies');
        $routes->get('tps/get-districts', 'TpsController::getDistricts');
        $routes->get('tps/get-villages', 'TpsController::getVillages');

        // Hasil Pemilu
        $routes->get('hasil', 'HasilPemiluController::index');
        $routes->get('hasil/get_candidates', 'HasilPemiluController::getCandidates');
        $routes->get('hasil/get_heatmap_data', 'HasilPemiluController::getHeatmapData');
        $routes->get('hasil/get_caleg_by_party', 'HasilPemiluController::getCalegByParty');
        $routes->get('hasil/get_rw_breakdown', 'HasilPemiluController::getRwBreakdown');
        $routes->get('hasil/get_tps_heatmap_matrix', 'HasilPemiluController::getTpsHeatmapMatrix');
        $routes->get('hasil/export_tps_heatmap', 'HasilPemiluController::exportTpsHeatmap');
        $routes->get('hasil/get_tps_breakdown', 'HasilPemiluController::getTpsCalegBreakdown');
        $routes->get('hasil/get_party_village_breakdown', 'HasilPemiluController::getPartyVillageBreakdown');

        // Dedicated Heatmap Hasil Pemilu (Matriks Caleg x TPS & Peta Spasial)
        $routes->get('heatmap', 'HasilPemiluController::heatmap');
        $routes->get('heatmap/get_matrix', 'HasilPemiluController::getTpsHeatmapMatrix');
        $routes->get('heatmap/export_matrix', 'HasilPemiluController::exportTpsHeatmap');
        $routes->get('heatmap/get_spatial', 'HasilPemiluController::getHeatmapData');
        $routes->get('heatmap/get_rw_breakdown', 'HasilPemiluController::getRwBreakdown');
        $routes->get('heatmap/get_caleg_by_party', 'HasilPemiluController::getCalegByParty');
        $routes->get('heatmap/get_districts', 'HasilPemiluController::getDistricts');
        $routes->get('heatmap/get_villages', 'HasilPemiluController::getVillages');
        $routes->get('heatmap/get_tps_breakdown', 'HasilPemiluController::getTpsCalegBreakdown');

        // Detail Suara TPS & RW
        $routes->get('tps-suara', 'TpsSuaraController::index');
        $routes->get('tps-suara/get_rw_detail', 'TpsSuaraController::getRwDetail');
        $routes->get('tps-suara/get_tps_detail', 'TpsSuaraController::getTpsDetail');

        // Kegiatan Sisir RW
        $routes->get('sisir-rw',                    'SisirRwController::index');
        $routes->get('sisir-rw/create',             'SisirRwController::create');
        $routes->post('sisir-rw/store',             'SisirRwController::store');
        $routes->get('sisir-rw/detail/(:num)',      'SisirRwController::detail/$1');
        $routes->get('sisir-rw/edit/(:num)',        'SisirRwController::edit/$1');
        $routes->post('sisir-rw/update/(:num)',     'SisirRwController::update/$1');
        $routes->get('sisir-rw/delete/(:num)',      'SisirRwController::delete/$1');
        $routes->get('sisir-rw/get-villages',       'SisirRwController::getVillages');

        // Bentuk Kegiatan master data
        $routes->get('sisir-rw/bentuk',             'SisirRwController::bentuk');
        $routes->post('sisir-rw/bentuk/store',      'SisirRwController::bentukStore');
        $routes->get('sisir-rw/bentuk/delete/(:num)', 'SisirRwController::bentukDelete/$1');
    });

    // Change Password (force reset — no extra role check needed beyond admin_access)
    $routes->get('change-password', 'ChangePasswordController::index');
    $routes->post('change-password', 'ChangePasswordController::update');

    // ── Keuangan ──────────────────────────────────────────────────────────
    $routes->group('keuangan', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',       'KeuanganDashboardController::index');
        $routes->get('dashboard', 'KeuanganDashboardController::index');

        // Chart of Accounts
        $routes->get('accounts',              'KeuanganAccountController::index');
        $routes->get('accounts/create',       'KeuanganAccountController::create');
        $routes->post('accounts/store',       'KeuanganAccountController::store');
        $routes->get('accounts/edit/(:num)',  'KeuanganAccountController::edit/$1');
        $routes->post('accounts/update/(:num)','KeuanganAccountController::update/$1');
        $routes->get('accounts/delete/(:num)','KeuanganAccountController::delete/$1');

        // Rekening & Kas
        $routes->get('rekening',              'KeuanganRekeningController::index');
        $routes->get('rekening/create',       'KeuanganRekeningController::create');
        $routes->post('rekening/store',       'KeuanganRekeningController::store');
        $routes->get('rekening/edit/(:num)',  'KeuanganRekeningController::edit/$1');
        $routes->post('rekening/update/(:num)','KeuanganRekeningController::update/$1');
        $routes->get('rekening/delete/(:num)','KeuanganRekeningController::delete/$1');

        // Transaksi
        $routes->get('transaksi',              'KeuanganTransaksiController::index');
        $routes->get('transaksi/create',       'KeuanganTransaksiController::create');
        $routes->post('transaksi/store',       'KeuanganTransaksiController::store');
        $routes->get('transaksi/(:num)',       'KeuanganTransaksiController::show/$1');
        $routes->get('transaksi/edit/(:num)',  'KeuanganTransaksiController::edit/$1');
        $routes->post('transaksi/update/(:num)','KeuanganTransaksiController::update/$1');
        $routes->get('transaksi/delete/(:num)','KeuanganTransaksiController::delete/$1');

        // Jurnal Umum
        $routes->get('jurnal',           'KeuanganJurnalController::index');
        $routes->get('jurnal/(:num)',    'KeuanganJurnalController::show/$1');

        // Laporan
        $routes->get('laporan',          'KeuanganLaporanController::index');

        // Status Iuran Anggota
        $routes->get('iuran',             'KeuanganIuranController::index');
        $routes->post('iuran/update',     'KeuanganIuranController::update');
        $routes->post('iuran/bulk-update','KeuanganIuranController::bulkUpdate');
        $routes->get('iuran/export',      'KeuanganIuranController::export');
    });

    // ── Master DPRa PKS ──────────────────────────────────────────────────
    $routes->group('dpra', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                      'DpraMasterController::index');
        $routes->post('store',                 'DpraMasterController::store');
        $routes->post('update/(:num)',        'DpraMasterController::update/$1');
        $routes->get('delete/(:num)',         'DpraMasterController::delete/$1');
    });

    // ── Modul DPC PKS (Struktur, Proker, RAB & Monev) ───────────────────
    $routes->group('dpc', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                      'DpcDashboardController::index');
        $routes->get('dashboard',              'DpcDashboardController::index');

        // Struktur Pengurus
        $routes->get('struktur',               'DpcStrukturController::index');
        $routes->get('struktur/create',        'DpcStrukturController::create');
        $routes->post('struktur/store',        'DpcStrukturController::store');
        $routes->get('struktur/edit/(:num)',   'DpcStrukturController::edit/$1');
        $routes->post('struktur/update/(:num)','DpcStrukturController::update/$1');
        $routes->get('struktur/delete/(:num)', 'DpcStrukturController::delete/$1');
        $routes->get('struktur/export-excel',  'DpcStrukturController::exportExcel');

        // Program Kerja & Matriks
        $routes->get('proker',                 'DpcProkerController::index');
        $routes->get('proker/create',          'DpcProkerController::create');
        $routes->post('proker/store',          'DpcProkerController::store');
        $routes->get('proker/edit/(:num)',     'DpcProkerController::edit/$1');
        $routes->post('proker/update/(:num)',  'DpcProkerController::update/$1');
        $routes->get('proker/delete/(:num)',   'DpcProkerController::delete/$1');
        $routes->get('proker/export-excel',    'DpcProkerController::exportExcel');

        // RAB Program
        $routes->get('rab',                    'DpcRabController::index');
        $routes->get('rab/create',             'DpcRabController::create');
        $routes->post('rab/store',             'DpcRabController::store');
        $routes->get('rab/edit/(:num)',        'DpcRabController::edit/$1');
        $routes->post('rab/update/(:num)',     'DpcRabController::update/$1');
        $routes->get('rab/delete/(:num)',      'DpcRabController::delete/$1');
        $routes->get('rab/export-excel',       'DpcRabController::exportExcel');

        // Tools Monev & Evaluasi
        $routes->get('monev',                  'DpcMonevController::index');
        $routes->get('monev/log/(:num)',       'DpcMonevController::log/$1');
        $routes->post('monev/save-log',        'DpcMonevController::saveLog');
        $routes->get('monev/delete-log/(:num)','DpcMonevController::deleteLog/$1');
        $routes->get('monev/export-excel',     'DpcMonevController::exportExcel');
    });

    // ── Modul Aset & Inventaris ─────────────────────────────────────────
    $routes->group('aset', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                      'AssetDashboardController::index');
        $routes->get('dashboard',              'AssetDashboardController::index');
        $routes->get('list',                   'AssetListController::index');
        $routes->get('create',                 'AssetListController::create');
        $routes->post('store',                 'AssetListController::store');
        $routes->get('edit/(:num)',            'AssetListController::edit/$1');
        $routes->post('update/(:num)',         'AssetListController::update/$1');
        $routes->get('delete/(:num)',          'AssetListController::delete/$1');
        $routes->get('export-excel',           'AssetListController::exportExcel');
    });

    // ── Modul Kegiatan ───────────────────────────────────────────────────
    $routes->group('kegiatan', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                      'KegiatanDashboardController::index');
        $routes->get('dashboard',              'KegiatanDashboardController::index');

        // Master Kegiatan
        $routes->get('master',                 'KegiatanMasterController::index');
        $routes->post('master/store',          'KegiatanMasterController::store');
        $routes->post('master/update/(:num)',   'KegiatanMasterController::update/$1');
        $routes->get('master/delete/(:num)',   'KegiatanMasterController::delete/$1');

        // List & Input Kegiatan
        $routes->get('list',                   'KegiatanListController::index');
        $routes->get('create',                 'KegiatanListController::create');
        $routes->post('store',                  'KegiatanListController::store');
        $routes->get('edit/(:num)',             'KegiatanListController::edit/$1');
        $routes->post('update/(:num)',          'KegiatanListController::update/$1');
        $routes->get('delete/(:num)',          'KegiatanListController::delete/$1');
        $routes->get('export-excel',           'KegiatanListController::exportExcel');

        // Rekap
        $routes->get('rekap-dpra',             'KegiatanRekapController::rekapDpra');
        $routes->get('rekap-dpra/export',      'KegiatanRekapController::exportDpra');
        $routes->get('rekap-rw',               'KegiatanRekapController::rekapRw');
        $routes->get('rekap-rw/export',        'KegiatanRekapController::exportRw');

        // Absensi Kegiatan
        $routes->get('absensi/(:num)',         'KegiatanAbsensiController::index/$1');
        $routes->post('absensi/(:num)/save',   'KegiatanAbsensiController::save/$1');
        $routes->get('absensi/(:num)/export',  'KegiatanAbsensiController::export/$1');
    });

    // ── Modul Booking Ruangan ──────────────────────────────────────────────
    $routes->group('booking', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                      'BookingListController::index');
        $routes->get('list',                   'BookingListController::index');

        // Master Ruangan
        $routes->get('master',                 'BookingMasterController::index');
        $routes->post('master/store',          'BookingMasterController::store');
        $routes->post('master/update/(:num)',   'BookingMasterController::update/$1');
        $routes->get('master/delete/(:num)',   'BookingMasterController::delete/$1');

        // Booking Form
        $routes->get('form',                   'BookingFormController::index');
        $routes->post('store',                 'BookingFormController::store');

        // Edit & Delete
        $routes->get('edit/(:num)',            'BookingListController::edit/$1');
        $routes->post('update/(:num)',         'BookingListController::update/$1');
        $routes->get('delete/(:num)',         'BookingListController::delete/$1');
        $routes->get('export-excel',          'BookingListController::exportExcel');
    });

    // ── Modul Document Management & Viewer ──────────────────────────────
    $routes->group('documents', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                      'DocumentController::index');
        $routes->get('create',                 'DocumentController::create');
        $routes->post('store',                 'DocumentController::store');
        $routes->get('edit/(:num)',            'DocumentController::edit/$1');
        $routes->post('update/(:num)',         'DocumentController::update/$1');
        $routes->get('delete/(:num)',          'DocumentController::delete/$1');
        $routes->get('view/(:num)',            'DocumentController::viewDocument/$1');
        $routes->get('stream/(:num)',          'DocumentController::stream/$1');
        $routes->get('download/(:num)',        'DocumentController::download/$1');
        $routes->get('export-excel',           'DocumentController::exportExcel');

        // Categories
        $routes->get('categories',             'DocumentCategoryController::index');
        $routes->post('categories/store',      'DocumentCategoryController::store');
        $routes->post('categories/update/(:num)', 'DocumentCategoryController::update/$1');
        $routes->get('categories/delete/(:num)',  'DocumentCategoryController::delete/$1');
    });

    // ── Modul Galeri & Dokumentasi Media (Foto & Video) ──────────────────
    $routes->group('gallery', ['namespace' => 'App\Controllers\Admin'], function ($routes) {
        $routes->get('/',                           'GalleryController::index');
        $routes->get('create-image',                'GalleryController::createImage');
        $routes->post('store-image',                'GalleryController::storeImage');
        $routes->get('create-video',                'GalleryController::createVideo');
        $routes->post('store-video',                'GalleryController::storeVideo');
        $routes->get('edit/(:num)',                 'GalleryController::edit/$1');
        $routes->post('update/(:num)',              'GalleryController::update/$1');
        $routes->get('delete/(:num)',               'GalleryController::delete/$1');
        $routes->post('bulk-delete',                'GalleryController::bulkDelete');
        $routes->get('toggle-featured/(:num)',      'GalleryController::toggleFeatured/$1');

        // Albums
        $routes->get('albums',                      'GalleryAlbumController::index');
        $routes->post('albums/store',               'GalleryAlbumController::store');
        $routes->post('albums/update/(:num)',       'GalleryAlbumController::update/$1');
        $routes->get('albums/delete/(:num)',        'GalleryAlbumController::delete/$1');
    });
});

// Override Shield Login Routes to prevent LogicException on duplicate login attempts
$routes->get('login', '\App\Controllers\Auth\LoginController::loginView', ['as' => 'login']);
$routes->post('login', '\App\Controllers\Auth\LoginController::loginAction');

// Shield Auth Routes
service('auth')->routes($routes);
