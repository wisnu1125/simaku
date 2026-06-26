<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?? 'SIMAKU' ?> - SIMAKU</title>

    

    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">



    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    

    <style>

        /* --- RESET & BASIC --- */

        :root {

            /* Bluish Teal Palette */

            --primary: #0891b2;       /* Cyan 600 */

            --primary-hover: #0e7490; /* Cyan 700 */

            --primary-soft: #ecfeff;  /* Cyan 50 */

            --primary-light: #cffafe; /* Cyan 100 */

            --text-dark: #0f172a;     /* Slate 900 */

            --text-gray: #64748b;     /* Slate 500 */

            --border: #e2e8f0;

        }



        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Plus Jakarta Sans', 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; color: #334155; }

        

        /* --- SIDEBAR --- */

        .sidebar { position: fixed; top: 0; left: 0; width: 250px; height: 100vh; background: #ffffff; border-right: 1px solid #f1f5f9; overflow-y: auto; z-index: 100; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        .sidebar::-webkit-scrollbar { width: 5px; }

        .sidebar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

        

        .sidebar-header { padding: 24px 20px; text-align: center; border-bottom: 1px dashed #e2e8f0; background: linear-gradient(180deg, #fff 0%, #f8fafc 100%); }

        

        .sidebar-text { display: flex; flex-direction: column; align-items: center; }

        .sidebar-text h1 { font-size: 20px; font-weight: 800; color: var(--primary); letter-spacing: 0.5px; margin-bottom: 2px; }

        .sidebar-text p { font-size: 11px; color: var(--text-gray); font-weight: 600; text-transform: uppercase; letter-spacing: 1.5px; }



        .sidebar-menu { padding: 20px 12px; }

        

        /* Menu Items */

        .menu-item { display: flex; align-items: center; gap: 12px; padding: 11px 16px; color: #475569; text-decoration: none; font-size: 13.5px; border-radius: 10px; transition: all 0.2s; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer; margin-bottom: 4px; font-weight: 500; }

        .menu-item i { width: 20px; text-align: center; font-size: 14px; color: #94a3b8; transition: 0.2s; }

        

        /* Hover State */

        .menu-item:hover { background: var(--primary-soft); color: var(--primary); }

        .menu-item:hover i { color: var(--primary); }

        

        /* Active State */

        .menu-item.active { background: var(--primary-soft); color: var(--primary); font-weight: 700; }

        .menu-item.active i { color: var(--primary); }

        

        /* Dropdown Styling */

        .dropdown-btn { justify-content: space-between; }

        .dropdown-btn .menu-title { display: flex; align-items: center; gap: 12px; }

        .dropdown-btn .arrow { font-size: 10px; width: auto; transition: transform 0.3s; opacity: 0.5; }

        .dropdown-btn.dropdown-active { background: var(--primary-soft); color: var(--primary); }

        .dropdown-btn.dropdown-active .arrow { transform: rotate(90deg); opacity: 1; }

        

        .dropdown-container { display: none; padding-left: 12px; margin-top: 2px; position: relative; }

        .dropdown-container::before {

            content: ''; position: absolute; left: 24px; top: 0; bottom: 0; width: 1px; background: #e2e8f0;

        }

        .dropdown-container .menu-item { font-size: 13px; padding-left: 36px; position: relative; }

        /* Dot indicator for dropdown items */

        .dropdown-container .menu-item::after {

            content: ''; position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 4px; height: 4px; border-radius: 50%; background: #cbd5e1; transition: 0.2s;

        }

        .dropdown-container .menu-item:hover::after,

        .dropdown-container .menu-item.active::after { background: var(--primary); }



        /* --- TOPBAR (ESTETIK & TIPIS) --- */

        .main-content { margin-left: 250px; min-height: 100vh; transition: margin-left 0.3s; }

        

        .topbar { 

            height: 60px; 

            background: rgba(255, 255, 255, 0.9);

            border-bottom: 1px solid #f1f5f9;

            padding: 0 32px;

            display: flex; justify-content: space-between; align-items: center;

            position: sticky; top: 0; z-index: 90;

            backdrop-filter: blur(10px);

        }



        .topbar-left h2 { 

            font-size: 18px; font-weight: 700; color: #1e293b; 

            display: flex; align-items: center; gap: 12px;

        }

        /* Indicator accent */

        .topbar-left h2::before {

            content: ''; display: block; width: 4px; height: 18px; 

            background: var(--primary); border-radius: 4px;

        }



        .topbar-right { display: flex; align-items: center; gap: 24px; }



        /* User Profile Grouping */

        .user-group { display: flex; align-items: center; gap: 12px; padding-right: 24px; border-right: 1px solid #e2e8f0; }

        .user-text { text-align: right; line-height: 1.3; }

        .user-text .name { font-size: 13px; font-weight: 700; color: #334155; display: block; }

        .user-text .role { font-size: 11px; color: #64748b; font-weight: 500; }



        /* Avatar Lingkaran Estetik (Bluish Teal) */

        .user-avatar {

            width: 38px; height: 38px;

            background: linear-gradient(135deg, #cffafe, #a5f3fc); /* Cyan Gradient */

            color: var(--primary-hover);

            border-radius: 50%;

            display: flex; align-items: center; justify-content: center;

            font-size: 16px;

            border: 2px solid #ffffff;

            box-shadow: 0 4px 6px -1px rgba(8, 145, 178, 0.1);

        }



        /* Logout Minimalis */

        .btn-logout-icon {

            width: 34px; height: 34px;

            display: flex; align-items: center; justify-content: center;

            color: #94a3b8; border-radius: 10px;

            text-decoration: none; transition: all 0.2s ease;

            background: transparent; border: 1px solid transparent;

        }

        .btn-logout-icon:hover { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }

        

        /* --- CONTENT & ALERTS --- */

        .content { padding: 32px; }

        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 13.5px; display: flex; align-items: center; gap: 12px; border: 1px solid transparent; font-weight: 500; }

        .alert i { font-size: 16px; }

        

        /* Update Alert Colors to match theme vibe */

        .alert-success { background: #f0fdf4; color: #166534; border-color: #bbf7d0; }

        .alert-error { background: #fef2f2; color: #991b1b; border-color: #fecaca; }

        .alert-warning { background: #fffbeb; color: #92400e; border-color: #fde68a; }

        .alert-info { background: #eff6ff; color: #1e40af; border-color: #bfdbfe; }



        /* Responsive */

        @media (max-width: 768px) {

            .sidebar { transform: translateX(-100%); }

            .sidebar.active { transform: translateX(0); }

            .main-content { margin-left: 0; }

            .topbar { padding: 0 16px; }

            .user-text { display: none; }

            .user-group { border: none; padding-right: 0; }

            .content { padding: 20px; }

        }

    </style>

</head>

<body>

    <div class="sidebar" id="sidebar">

        <div class="sidebar-header">

            <div class="sidebar-text">

                <h1>SIMAKU</h1>

                <p>Management System</p>

            </div>

        </div>

        

        <div class="sidebar-menu">

            <a href="<?= base_url('admin/dashboard') ?>" class="menu-item <?= (uri_string() == 'admin/dashboard') ? 'active' : '' ?>">

                <i class="fa-solid fa-house"></i> Dashboard

            </a>



            <button class="menu-item dropdown-btn">

                <div class="menu-title"><i class="fa-solid fa-database"></i> Master Data</div>

                <i class="fa-solid fa-chevron-right arrow"></i>

            </button>

            <div class="dropdown-container">

                <a href="<?= base_url('admin/tahun-ajaran') ?>" class="menu-item <?= (strpos(uri_string(), 'tahun-ajaran') !== false) ? 'active' : '' ?>">

                    Tahun Ajaran

                </a>

                <a href="<?= base_url('admin/kelas') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/kelas') !== false) ? 'active' : '' ?>">

                    Kelas

                </a>

                <a href="<?= base_url('admin/siswa') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/siswa') !== false) ? 'active' : '' ?>">

                    Siswa

                </a>

                <a href="<?= base_url('admin/jenis-tagihan') ?>" class="menu-item <?= (strpos(uri_string(), 'jenis-tagihan') !== false) ? 'active' : '' ?>">

                    Jenis Tagihan

                </a>

            </div>

            

            <button class="menu-item dropdown-btn">

                <div class="menu-title"><i class="fa-solid fa-file-invoice-dollar"></i> Transaksi</div>

                <i class="fa-solid fa-chevron-right arrow"></i>

            </button>

            <div class="dropdown-container">

                <a href="<?= base_url('admin/skema-tagihan') ?>" class="menu-item <?= (strpos(uri_string(), 'skema-tagihan') !== false) ? 'active' : '' ?>">

                    Skema Tagihan

                </a>

                <a href="<?= base_url('admin/tagihan') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/tagihan') !== false) ? 'active' : '' ?>">

                    Tagihan

                </a>

                <a href="<?= base_url('admin/beasiswa') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/beasiswa') !== false) ? 'active' : '' ?>">

                    Beasiswa

                </a>

                <a href="<?= base_url('admin/pembayaran') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/pembayaran') !== false) ? 'active' : '' ?>">

                    Pembayaran

                </a>

            </div>



            <a href="<?= base_url('admin/laporan') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/laporan') !== false) ? 'active' : '' ?>">

                <i class="fa-solid fa-chart-line"></i> Laporan

            </a>

            

          

            

            <a href="<?= base_url('admin/kenaikan-kelas') ?>" class="menu-item <?= (strpos(uri_string(), 'kenaikan-kelas') !== false) ? 'active' : '' ?>">

                <i class="fa-solid fa-arrow-up-right-dots"></i> Kenaikan Kelas

            </a>

              

            <?php if (session()->get('role') === 'super_admin'): ?>

            <button class="menu-item dropdown-btn">

                <div class="menu-title"><i class="fa-solid fa-gears"></i> Pengaturan</div>

                <i class="fa-solid fa-chevron-right arrow"></i>

            </button>

            <div class="dropdown-container">

                <a href="<?= base_url('admin/users') ?>" class="menu-item <?= (strpos(uri_string(), 'admin/users') !== false) ? 'active' : '' ?>">

                    User Management

                </a>

                <a href="<?= base_url('admin/audit-log') ?>" class="menu-item <?= (strpos(uri_string(), 'audit-log') !== false) ? 'active' : '' ?>">

                    Audit Log

                </a>

            </div>

            <?php endif; ?>

        </div>

    </div>

    

    <div class="main-content">

        <div class="topbar">

            <div class="topbar-left">

                <h2><?= $title ?? 'Dashboard' ?></h2>

            </div>

            

            <div class="topbar-right">

                <div class="user-group">

                    <div class="user-text">

                        <span class="name"><?= session()->get('nama_lengkap') ?></span>

                        <span class="role"><?= ucwords(str_replace('_', ' ', session()->get('role'))) ?></span>

                    </div>

                    <div class="user-avatar">

                        <i class="fa-solid fa-user"></i>

                    </div>

                </div>



                <a href="<?= base_url('admin/logout') ?>" class="btn-logout-icon" title="Logout">

                    <i class="fa-solid fa-power-off"></i>

                </a>

            </div>

        </div>

        

        <div class="content">

            <?php if (session()->getFlashdata('success')): ?>

                <div class="alert alert-success">

                    <i class="fa-solid fa-circle-check"></i>

                    <span><?= session()->getFlashdata('success') ?></span>

                </div>

            <?php endif; ?>

            

            <?php if (session()->getFlashdata('error')): ?>

                <div class="alert alert-error">

                    <i class="fa-solid fa-circle-exclamation"></i>

                    <span><?= session()->getFlashdata('error') ?></span>

                </div>

            <?php endif; ?>



            <?php if (session()->getFlashdata('warning')): ?>

                <div class="alert alert-warning">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <span><?= session()->getFlashdata('warning') ?></span>

                </div>

            <?php endif; ?>



            <script>

                document.addEventListener("DOMContentLoaded", function() {

                    // Logic Dropdown Click

                    var dropdowns = document.getElementsByClassName("dropdown-btn");

                    for (var i = 0; i < dropdowns.length; i++) {

                        dropdowns[i].addEventListener("click", function() {

                            this.classList.toggle("dropdown-active");

                            var dropdownContent = this.nextElementSibling;

                            if (dropdownContent.style.display === "block") {

                                dropdownContent.style.display = "none";

                            } else {

                                dropdownContent.style.display = "block";

                            }

                        });

                    }

                    // Logic Keep Active Dropdown Open

                    var activeLinks = document.querySelectorAll('.dropdown-container .menu-item.active');

                    activeLinks.forEach(function(link) {

                        var container = link.closest('.dropdown-container');

                        if (container) {

                            container.style.display = 'block';

                            var btn = container.previousElementSibling;

                            if (btn) btn.classList.add('dropdown-active');

                        }

                    });

                });

            </script>

</body>

</html>