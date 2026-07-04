<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// =====================================================
// PUBLIC ROUTES
// =====================================================

$routes->get('/', 'Public\HomeController::index');
$routes->get('home/searchSiswa', 'Public\HomeController::searchSiswa');
$routes->post('cek-tagihan', 'Public\HomeController::cekTagihan');
$routes->get('print-kartu/(:num)/(:num)', 'Public\HomeController::printKartu/$1/$2');

// =====================================================
// AUTH ROUTES
// =====================================================

$routes->group('admin', function($routes) {
    $routes->get('login', 'Admin\AuthController::login');
    $routes->post('login', 'Admin\AuthController::doLogin');
    $routes->get('logout', 'Admin\AuthController::logout');
});

// =====================================================
// ADMIN ROUTES (Protected by 'auth' filter)
// =====================================================

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    
    // Dashboard
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // Tahun Ajaran
    $routes->get('tahun-ajaran', 'Admin\TahunAjaranController::index');
    $routes->get('tahun-ajaran/create', 'Admin\TahunAjaranController::create');
    $routes->post('tahun-ajaran/store', 'Admin\TahunAjaranController::store');
    $routes->get('tahun-ajaran/edit/(:num)', 'Admin\TahunAjaranController::edit/$1');
    $routes->post('tahun-ajaran/update/(:num)', 'Admin\TahunAjaranController::update/$1');
    $routes->post('tahun-ajaran/delete/(:num)', 'Admin\TahunAjaranController::delete/$1');
    $routes->post('tahun-ajaran/activate/(:num)', 'Admin\TahunAjaranController::activate/$1', ['filter' => 'role:super_admin']);
    $routes->post('tahun-ajaran/close/(:num)', 'Admin\TahunAjaranController::close/$1', ['filter' => 'role:super_admin']);
    
    // Kelas
    $routes->get('kelas', 'Admin\KelasController::index');
    $routes->get('kelas/create', 'Admin\KelasController::create');
    $routes->post('kelas/store', 'Admin\KelasController::store');
    $routes->get('kelas/edit/(:num)', 'Admin\KelasController::edit/$1');
    $routes->post('kelas/update/(:num)', 'Admin\KelasController::update/$1');
    $routes->post('kelas/delete/(:num)', 'Admin\KelasController::delete/$1');
    
    // Siswa
    $routes->get('siswa', 'Admin\SiswaController::index');
    $routes->get('siswa/create', 'Admin\SiswaController::create');
    $routes->post('siswa/store', 'Admin\SiswaController::store');
    $routes->get('siswa/detail/(:num)', 'Admin\SiswaController::detail/$1');
    $routes->get('siswa/edit/(:num)', 'Admin\SiswaController::edit/$1');
    $routes->post('siswa/update/(:num)', 'Admin\SiswaController::update/$1');
    $routes->post('siswa/delete/(:num)', 'Admin\SiswaController::delete/$1');
    $routes->get('siswa/search', 'Admin\SiswaController::search');
    
    // Jenis Tagihan
    $routes->get('jenis-tagihan', 'Admin\JenisTagihanController::index');
    $routes->get('jenis-tagihan/create', 'Admin\JenisTagihanController::create');
    $routes->post('jenis-tagihan/store', 'Admin\JenisTagihanController::store');
    $routes->get('jenis-tagihan/edit/(:num)', 'Admin\JenisTagihanController::edit/$1');
    $routes->post('jenis-tagihan/update/(:num)', 'Admin\JenisTagihanController::update/$1');
    $routes->post('jenis-tagihan/delete/(:num)', 'Admin\JenisTagihanController::delete/$1');
    
    // Skema Tagihan
    $routes->get('skema-tagihan', 'Admin\SkemaTagihanController::index');
    $routes->get('skema-tagihan/create', 'Admin\SkemaTagihanController::create');
    $routes->get('skema-tagihan/generate-bulk', 'Admin\SkemaTagihanController::generateBulk'); // GET - Tampilkan form
    $routes->post('skema-tagihan/store', 'Admin\SkemaTagihanController::store');
    $routes->post('skema-tagihan/store-bulk', 'Admin\SkemaTagihanController::storeBulk'); // POST - Submit bulk
    $routes->get('skema-tagihan/edit/(:num)', 'Admin\SkemaTagihanController::edit/$1');
    $routes->post('skema-tagihan/update/(:num)', 'Admin\SkemaTagihanController::update/$1');
    $routes->post('skema-tagihan/delete/(:num)', 'Admin\SkemaTagihanController::delete/$1');
    
    // Tagihan
    $routes->get('tagihan', 'Admin\TagihanController::index');
    $routes->get('tagihan/generate', 'Admin\TagihanController::generate');
    $routes->post('tagihan/generate', 'Admin\TagihanController::doGenerate');
    $routes->get('tagihan/detail/(:num)', 'Admin\TagihanController::detail/$1');
    $routes->post('tagihan/delete/(:num)', 'Admin\TagihanController::delete/$1');
    // ROUTE BARU: Bulk Delete Tagihan
    $routes->post('tagihan/bulk-delete', 'Admin\TagihanController::bulkDelete');
    
    // Beasiswa
    $routes->get('beasiswa', 'Admin\BeasiswaController::index');
    $routes->get('beasiswa/create', 'Admin\BeasiswaController::create');
    $routes->post('beasiswa/store', 'Admin\BeasiswaController::store');
    $routes->get('beasiswa/edit/(:num)', 'Admin\BeasiswaController::edit/$1');
    $routes->post('beasiswa/update/(:num)', 'Admin\BeasiswaController::update/$1');
    $routes->post('beasiswa/delete/(:num)', 'Admin\BeasiswaController::delete/$1');
    
    // Pembayaran
    $routes->get('pembayaran', 'Admin\PembayaranController::index');
    $routes->get('pembayaran/create', 'Admin\PembayaranController::create');
    $routes->post('pembayaran/store', 'Admin\PembayaranController::store');
    $routes->get('pembayaran/detail/(:num)', 'Admin\PembayaranController::detail/$1');
    $routes->get('pembayaran/print/(:num)', 'Admin\PembayaranController::printKwitansi/$1');
    $routes->post('pembayaran/batal/(:num)', 'Admin\PembayaranController::batal/$1');
    $routes->get('pembayaran/get-tagihan-by-siswa', 'Admin\PembayaranController::getTagihanBySiswa');
    $routes->post('pembayaran/store-bulk', 'Admin\PembayaranController::storeBulk');
    
    // Kenaikan Kelas
    $routes->get('kenaikan-kelas', 'Admin\KenaikanKelasController::index');
    $routes->get('kenaikan-kelas/form', 'Admin\KenaikanKelasController::form');
    $routes->post('kenaikan-kelas/proses', 'Admin\KenaikanKelasController::proses');
    $routes->get('kenaikan-kelas/kelulusan', 'Admin\KenaikanKelasController::kelulusan');
    $routes->post('kenaikan-kelas/proses-kelulusan', 'Admin\KenaikanKelasController::prosesKelulusan');
    
    // Laporan
    $routes->get('laporan', 'Admin\LaporanController::index');
    $routes->get('laporan/pembayaran', 'Admin\LaporanController::pembayaran');
    $routes->get('laporan/tunggakan', 'Admin\LaporanController::tunggakan');
    $routes->get('laporan/per-kelas', 'Admin\LaporanController::perKelas');
    $routes->get('laporan/export-pembayaran', 'Admin\LaporanController::exportPembayaran');
    $routes->get('laporan/export-tunggakan', 'Admin\LaporanController::exportTunggakan');
    $routes->get('laporan/per-kelas/export', 'Admin\LaporanController::exportPerKelas');
    
    // Operasional (Pengeluaran & Saldo) -- DIHAPUS atas permintaan, tidak diperlukan lagi.
    // Controller & view-nya masih ada di server (tidak dihapus otomatis dari sini),
    // tapi sudah tidak reachable sama sekali karena route-nya dicabut.
    
    // User Management (Super Admin Only)
    $routes->group('', ['filter' => 'role:super_admin'], function($routes) {
        $routes->get('users', 'Admin\UserController::index');
        $routes->get('users/create', 'Admin\UserController::create');
        $routes->post('users/store', 'Admin\UserController::store');
        $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');
        $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
        $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');
    });
    
    // Audit Log (Super Admin Only)
    $routes->group('', ['filter' => 'role:super_admin'], function($routes) {
        $routes->get('audit-log', 'Admin\AuditLogController::index');
        $routes->get('audit-log/detail/(:num)', 'Admin\AuditLogController::detail/$1');
    });
});