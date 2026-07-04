<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= $title ?? 'Dashboard' ?> — SIMAKU</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/favicon.png') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Roboto+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
    /* =====================================================================
       SIMAKU DESIGN SYSTEM
       Satu sumber gaya untuk seluruh halaman admin — supaya font, warna,
       spacing, dan komponen (tombol/badge/modal/drawer) konsisten di mana-mana.
       ===================================================================== */

    :root {
        /* Warna inti — teal, dipertahankan dari identitas SIMAKU sebelumnya, disatukan jadi satu skala */
        --ink: #0f172a;
        --body: #334155;
        --muted: #64748b;
        --faint: #94a3b8;
        --border: #e2e8f0;
        --border-soft: #f1f5f9;
        --surface: #ffffff;
        --bg: #f8fafc;

        --brand: #0d9488;
        --brand-dark: #0f766e;
        --brand-darker: #115e59;
        --brand-light: #ccfbf1;
        --brand-bg: #f0fdfa;

        --success: #16a34a;
        --success-bg: #f0fdf4;
        --success-border: #bbf7d0;
        --warning: #d97706;
        --warning-bg: #fffbeb;
        --warning-border: #fde68a;
        --danger: #dc2626;
        --danger-bg: #fef2f2;
        --danger-border: #fecaca;
        --info: #2563eb;
        --info-bg: #eff6ff;
        --info-border: #bfdbfe;

        --r-sm: 8px;
        --r-md: 12px;
        --r-lg: 16px;
        --r-xl: 20px;

        --shadow-sm: 0 1px 2px rgba(15,23,42,.06);
        --shadow-md: 0 6px 16px rgba(15,23,42,.08);
        --shadow-lg: 0 20px 40px rgba(15,23,42,.16);

        --sidebar-w: 248px;
        --topbar-h: 64px;
        --mobile-topbar-h: 56px;
        --bottomnav-h: 62px;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    html { -webkit-text-size-adjust: 100%; }

    body {
        font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background: var(--bg);
        color: var(--body);
        font-size: 14px;
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
    }

    h1, h2, h3, h4 { color: var(--ink); font-weight: 700; line-height: 1.25; }

    a { color: var(--brand); }

    .mono, .tabular, .money { font-family: 'Roboto Mono', ui-monospace, monospace; font-variant-numeric: tabular-nums; }

    button, input, select, textarea { font-family: inherit; font-size: inherit; color: inherit; }

    :focus-visible {
        outline: 2px solid var(--brand);
        outline-offset: 2px;
    }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
    }

    /* ============================== LAYOUT SHELL ============================== */

    .app-shell { min-height: 100vh; }

    /* ---- Sidebar (desktop/tablet) ---- */
    .sidebar {
        position: fixed; top: 0; left: 0; bottom: 0; width: var(--sidebar-w);
        background: var(--surface); border-right: 1px solid var(--border-soft);
        display: flex; flex-direction: column; z-index: 40;
        transition: transform .25s ease;
    }
    .sidebar-brand {
        display: flex; align-items: center; gap: 10px;
        padding: 20px 22px; border-bottom: 1px dashed var(--border);
    }
    .sidebar-brand-mark {
        width: 36px; height: 36px; border-radius: 10px;
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 900; font-size: 15px; flex-shrink: 0;
    }
    .sidebar-brand-text h1 { font-size: 16px; font-weight: 900; letter-spacing: .2px; }
    .sidebar-brand-text p { font-size: 10.5px; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: 1.2px; }

    .sidebar-nav { flex: 1; overflow-y: auto; padding: 14px 12px; }
    .nav-group-label {
        font-size: 10.5px; font-weight: 700; color: var(--faint); text-transform: uppercase;
        letter-spacing: 1px; padding: 14px 12px 6px;
    }
    .nav-item {
        display: flex; align-items: center; gap: 11px; width: 100%;
        padding: 10px 12px; border-radius: var(--r-sm); text-decoration: none;
        color: var(--body); font-size: 13.5px; font-weight: 500;
        border: none; background: transparent; cursor: pointer; text-align: left;
        transition: background .15s, color .15s; margin-bottom: 2px;
    }
    .nav-item i { width: 18px; text-align: center; font-size: 14px; color: var(--faint); transition: color .15s; flex-shrink: 0; }
    .nav-item:hover { background: var(--brand-bg); color: var(--brand-darker); }
    .nav-item:hover i { color: var(--brand); }
    .nav-item.active { background: var(--brand-bg); color: var(--brand-darker); font-weight: 700; }
    .nav-item.active i { color: var(--brand); }
    .nav-item .chev { margin-left: auto; font-size: 11px; transition: transform .2s; color: var(--faint); }
    .nav-item.expanded .chev { transform: rotate(90deg); }
    .nav-subgroup { padding-left: 14px; max-height: 0; overflow: hidden; transition: max-height .2s ease; }
    .nav-subgroup.open { max-height: 400px; }
    .nav-subgroup .nav-item { font-size: 13px; padding-left: 26px; }

    .sidebar-user {
        border-top: 1px solid var(--border-soft); padding: 14px 16px;
        display: flex; align-items: center; gap: 10px;
    }
    .sidebar-user-avatar {
        width: 34px; height: 34px; border-radius: 50%; background: var(--brand-bg); color: var(--brand-darker);
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;
    }
    .sidebar-user-info { flex: 1; min-width: 0; }
    .sidebar-user-info .name { font-size: 12.5px; font-weight: 700; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .sidebar-user-info .role { font-size: 11px; color: var(--muted); }
    .sidebar-logout { color: var(--faint); font-size: 15px; padding: 8px; border-radius: var(--r-sm); flex-shrink: 0; }
    .sidebar-logout:hover { color: var(--danger); background: var(--danger-bg); }

    /* ---- Main content area ---- */
    .main-content { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }

    .topbar {
        height: var(--topbar-h); display: flex; align-items: center; justify-content: space-between;
        padding: 0 28px; border-bottom: 1px solid var(--border-soft); background: rgba(248,250,252,.85);
        backdrop-filter: blur(6px); position: sticky; top: 0; z-index: 20;
    }
    .topbar h2 { font-size: 19px; font-weight: 800; letter-spacing: -.2px; }
    .topbar-actions { display: flex; align-items: center; gap: 10px; }

    .mobile-topbar { display: none; }
    .content { padding: 24px 28px 40px; flex: 1; }

    /* ============================== TYPOGRAPHY HELPERS ============================== */
    .page-title { font-size: 22px; font-weight: 800; color: var(--ink); letter-spacing: -.3px; margin-bottom: 4px; }
    .page-subtitle { font-size: 13px; color: var(--muted); }
    .eyebrow { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: var(--brand); }

    /* ============================== BUTTONS ============================== */
    .btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 8px;
        padding: 10px 18px; border-radius: var(--r-sm); border: none; cursor: pointer;
        font-size: 13.5px; font-weight: 700; text-decoration: none; white-space: nowrap;
        transition: background .15s, transform .1s, box-shadow .15s; line-height: 1.2;
    }
    .btn:active { transform: scale(.98); }
    .btn-primary { background: var(--brand); color: #fff; box-shadow: 0 2px 8px rgba(13,148,136,.28); }
    .btn-primary:hover { background: var(--brand-dark); }
    .btn-secondary { background: var(--surface); color: var(--body); border: 1.5px solid var(--border); }
    .btn-secondary:hover { background: var(--border-soft); }
    .btn-danger { background: var(--danger-bg); color: var(--danger); border: 1.5px solid var(--danger-border); }
    .btn-danger:hover { background: var(--danger); color: #fff; }
    .btn-ghost { background: transparent; color: var(--muted); }
    .btn-ghost:hover { background: var(--border-soft); color: var(--ink); }
    .btn-sm { padding: 7px 13px; font-size: 12.5px; }
    .btn-block { width: 100%; }
    .btn-icon { width: 36px; height: 36px; padding: 0; border-radius: var(--r-sm); }
    .btn[disabled] { opacity: .5; cursor: not-allowed; }

    .icon-action {
        width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: var(--r-sm); border: 1.5px solid var(--border); background: var(--surface);
        color: var(--muted); cursor: pointer; transition: .15s; text-decoration: none;
    }
    .icon-action:hover { border-color: var(--brand); color: var(--brand); background: var(--brand-bg); }
    .icon-action.danger:hover { border-color: var(--danger-border); color: var(--danger); background: var(--danger-bg); }

    /* ============================== CARDS / SURFACES ============================== */
    .card { background: var(--surface); border: 1px solid var(--border-soft); border-radius: var(--r-lg); box-shadow: var(--shadow-sm); }
    .card-pad { padding: 22px; }

    /* ============================== BADGES ============================== */
    .badge {
        display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px;
        border-radius: 999px; font-size: 11px; font-weight: 700; border: 1px solid transparent;
        white-space: nowrap;
    }
    .badge-success { background: var(--success-bg); color: var(--success); border-color: var(--success-border); }
    .badge-warning { background: var(--warning-bg); color: var(--warning); border-color: var(--warning-border); }
    .badge-danger { background: var(--danger-bg); color: var(--danger); border-color: var(--danger-border); }
    .badge-info { background: var(--info-bg); color: var(--info); border-color: var(--info-border); }
    .badge-neutral { background: var(--border-soft); color: var(--muted); }
    .badge-brand { background: var(--brand-bg); color: var(--brand-darker); border-color: var(--brand-light); }

    /* ============================== FORMS ============================== */
    .field { margin-bottom: 16px; }
    .field label {
        display: block; margin-bottom: 6px; font-size: 12.5px; font-weight: 700; color: var(--body);
    }
    .field label.required::after { content: " *"; color: var(--danger); }
    .field-hint { font-size: 11.5px; color: var(--muted); margin-top: 5px; }
    .field-error { font-size: 11.5px; color: var(--danger); margin-top: 5px; font-weight: 500; }

    .input, select.input, textarea.input {
        width: 100%; padding: 11px 14px; border: 1.5px solid var(--border); border-radius: var(--r-sm);
        background: var(--bg); font-size: 14px; color: var(--ink); transition: border-color .15s, background .15s, box-shadow .15s;
        appearance: none;
    }
    select.input {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%2364748b' stroke-width='1.5' fill='none' fill-rule='evenodd'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center; padding-right: 34px;
    }
    .input:focus, select.input:focus, textarea.input:focus {
        outline: none; border-color: var(--brand); background: var(--surface); box-shadow: 0 0 0 4px var(--brand-bg);
    }
    .input.is-invalid { border-color: var(--danger); background: var(--danger-bg); }
    .input::placeholder { color: var(--faint); }

    .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    .segmented { display: flex; gap: 8px; flex-wrap: wrap; }
    .segmented label {
        display: flex; align-items: center; gap: 8px; padding: 10px 16px; border: 1.5px solid var(--border);
        border-radius: var(--r-sm); cursor: pointer; font-size: 13.5px; font-weight: 500; color: var(--body);
        transition: .15s;
    }
    .segmented input { accent-color: var(--brand); width: 16px; height: 16px; }
    .segmented label:has(input:checked) { border-color: var(--brand); background: var(--brand-bg); color: var(--brand-darker); font-weight: 700; }

    /* ============================== ALERTS / TOASTS ============================== */
    .toast-stack {
        position: fixed; top: 16px; right: 16px; z-index: 2000;
        display: flex; flex-direction: column; gap: 10px; width: min(380px, calc(100vw - 32px));
    }
    .toast {
        display: flex; align-items: flex-start; gap: 10px; padding: 14px 16px; border-radius: var(--r-md);
        border: 1px solid; box-shadow: var(--shadow-md); font-size: 13.5px; font-weight: 500;
        background: var(--surface); animation: toast-in .25s ease;
    }
    @keyframes toast-in { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
    .toast.success { border-color: var(--success-border); color: #14532d; background: var(--success-bg); }
    .toast.error { border-color: var(--danger-border); color: #7f1d1d; background: var(--danger-bg); }
    .toast.warning { border-color: var(--warning-border); color: #78350f; background: var(--warning-bg); }
    .toast i.icon { font-size: 16px; margin-top: 1px; }
    .toast .close { margin-left: auto; cursor: pointer; opacity: .5; }
    .toast .close:hover { opacity: 1; }

    /* ============================== TABLE (desktop) / CARD LIST (mobile) ============================== */
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table thead th {
        text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px;
        color: var(--muted); padding: 12px 14px; border-bottom: 1.5px solid var(--border-soft); white-space: nowrap;
    }
    .data-table tbody td { padding: 14px; border-bottom: 1px solid var(--border-soft); font-size: 13.5px; vertical-align: middle; }
    .data-table tbody tr:hover { background: var(--brand-bg); }
    .data-table tbody tr:last-child td { border-bottom: none; }

    .row-list { display: none; }

    /* ============================== MODAL (Tambah/Edit — bottom sheet di mobile) ============================== */
    .overlay {
        position: fixed; inset: 0; background: rgba(15,23,42,.5); z-index: 1000;
        opacity: 0; pointer-events: none; transition: opacity .2s ease;
    }
    .overlay.open { opacity: 1; pointer-events: auto; }

    .modal {
        position: fixed; left: 0; right: 0; bottom: 0; z-index: 1001;
        background: var(--surface); border-radius: var(--r-xl) var(--r-xl) 0 0;
        max-height: 92vh; display: flex; flex-direction: column;
        transform: translateY(100%); transition: transform .3s cubic-bezier(.32,.72,0,1);
        box-shadow: var(--shadow-lg);
    }
    .modal.open { transform: translateY(0); }
    .modal-drag { width: 40px; height: 4px; border-radius: 999px; background: var(--border); margin: 10px auto 0; flex-shrink: 0; }
    .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid var(--border-soft); flex-shrink: 0; }
    .modal-header h3 { font-size: 16px; font-weight: 800; }
    .modal-close { width: 32px; height: 32px; border-radius: 50%; border: none; background: var(--border-soft); color: var(--muted); cursor: pointer; flex-shrink: 0; }
    .modal-close:hover { background: var(--border); color: var(--ink); }
    .modal-body { padding: 20px; overflow-y: auto; flex: 1; }
    .modal-footer { display: flex; gap: 10px; padding: 16px 20px; border-top: 1px solid var(--border-soft); flex-shrink: 0; }
    .modal-footer .btn { flex: 1; }

    @media (min-width: 640px) {
        .modal {
            left: 50%; right: auto; bottom: auto; top: 50%; width: 92vw; max-width: 640px;
            border-radius: var(--r-xl); max-height: 86vh;
            transform: translate(-50%, -48%) scale(.97); opacity: 0;
            transition: transform .2s ease, opacity .2s ease;
        }
        .modal.open { transform: translate(-50%, -50%) scale(1); opacity: 1; }
        .modal-drag { display: none; }
        .modal-footer .btn { flex: none; }
        .modal-footer { justify-content: flex-end; }
    }
    .modal.modal-wide { max-width: 880px; }

    /* ============================== DRAWER (Detail — dari kanan) ============================== */
    .drawer {
        position: fixed; top: 0; right: 0; bottom: 0; z-index: 1001; width: 100%;
        background: var(--surface); box-shadow: var(--shadow-lg);
        transform: translateX(100%); transition: transform .3s cubic-bezier(.32,.72,0,1);
        display: flex; flex-direction: column;
    }
    .drawer.open { transform: translateX(0); }
    @media (min-width: 640px) { .drawer { width: 440px; } }
    .drawer-header { display: flex; align-items: center; gap: 12px; padding: 16px 20px; border-bottom: 1px solid var(--border-soft); flex-shrink: 0; }
    .drawer-header h3 { font-size: 16px; font-weight: 800; flex: 1; }
    .drawer-body { padding: 20px; overflow-y: auto; flex: 1; }
    .drawer-skeleton { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 200px; color: var(--faint); gap: 10px; }

    .info-row { display: flex; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--border-soft); font-size: 13px; }
    .info-row:last-child { border-bottom: none; }
    .info-row .k { color: var(--muted); font-weight: 500; }
    .info-row .v { color: var(--ink); font-weight: 700; text-align: right; }

    /* ============================== EMPTY STATE ============================== */
    .empty-state { text-align: center; padding: 56px 20px; color: var(--muted); }
    .empty-state i { font-size: 38px; color: var(--border); margin-bottom: 14px; }
    .empty-state p { font-size: 13.5px; }

    /* ============================== SKELETON LOADING ============================== */
    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .4; } }
    .skeleton-row { padding: 14px 20px; }
    .skeleton-bar { height: 14px; background: var(--border-soft); border-radius: 6px; margin-bottom: 8px; animation: pulse 1.2s ease-in-out infinite; }

    /* ============================== MOBILE NAV ============================== */
    .bottom-nav { display: none; }
    .mobile-nav-sheet { display: none; }

    /* ============================== RESPONSIVE ============================== */
    @media (max-width: 1023px) {
        .sidebar { transform: translateX(-100%); box-shadow: var(--shadow-lg); }
        .sidebar.open { transform: translateX(0); }
        .main-content { margin-left: 0; }

        .topbar { display: none; }
        .mobile-topbar {
            display: flex; align-items: center; gap: 12px; height: var(--mobile-topbar-h);
            padding: 0 16px; background: var(--surface); border-bottom: 1px solid var(--border-soft);
            position: sticky; top: 0; z-index: 30;
        }
        .mobile-topbar .hamburger {
            width: 36px; height: 36px; border-radius: var(--r-sm); border: none; background: var(--border-soft);
            color: var(--ink); display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0;
        }
        .mobile-topbar h2 { font-size: 16px; font-weight: 800; flex: 1; }

        .content { padding: 16px 14px calc(var(--bottomnav-h) + 24px); }

        .bottom-nav {
            display: flex; position: fixed; left: 0; right: 0; bottom: 0; height: var(--bottomnav-h);
            background: var(--surface); border-top: 1px solid var(--border-soft); z-index: 25;
            padding-bottom: env(safe-area-inset-bottom, 0);
            box-shadow: 0 -4px 16px rgba(15,23,42,.05);
        }
        .bottom-nav-item {
            flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 3px;
            text-decoration: none; color: var(--faint); font-size: 10.5px; font-weight: 600; border: none; background: none; cursor: pointer;
        }
        .bottom-nav-item i { font-size: 17px; }
        .bottom-nav-item.active { color: var(--brand); }

        .mobile-nav-sheet {
            display: block; position: fixed; left: 0; right: 0; bottom: 0; z-index: 1001;
            background: var(--surface); border-radius: var(--r-xl) var(--r-xl) 0 0;
            max-height: 82vh; overflow-y: auto; transform: translateY(100%);
            transition: transform .3s cubic-bezier(.32,.72,0,1); padding-bottom: env(safe-area-inset-bottom, 0);
        }
        .mobile-nav-sheet.open { transform: translateY(0); }

        .field-row { grid-template-columns: 1fr; }

        .data-table { display: none; }
        .row-list { display: flex; flex-direction: column; gap: 10px; }

        .toast-stack { top: auto; bottom: calc(var(--bottomnav-h) + 12px); right: 12px; left: 12px; width: auto; }
    }

    @media (min-width: 1024px) {
        .hamburger, .mobile-topbar, .bottom-nav, .mobile-nav-sheet, .overlay#mobileNavOverlay { display: none !important; }
    }
    </style>
</head>
<body>
<div class="app-shell">

    <!-- ================= SIDEBAR (desktop & tablet) ================= -->
    <?php
    $__role = session()->get('role');
    $__uri  = uri_string();
    if (!function_exists('simaku_active')) {
        function simaku_active($needle, $exact = false) {
            $uri = uri_string();
            return $exact ? ($uri === $needle) : (strpos($uri, $needle) !== false);
        }
    }
    ?>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-mark">S</div>
            <div class="sidebar-brand-text">
                <h1>SIMAKU</h1>
                <p>Management System</p>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="<?= base_url('admin/dashboard') ?>" class="nav-item <?= simaku_active('admin/dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>

            <div class="nav-group-label">Data Master</div>
            <a href="<?= base_url('admin/siswa') ?>" class="nav-item <?= simaku_active('admin/siswa') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-graduate"></i> Siswa
            </a>
            <a href="<?= base_url('admin/kelas') ?>" class="nav-item <?= simaku_active('admin/kelas') ? 'active' : '' ?>">
                <i class="fa-solid fa-chalkboard"></i> Kelas
            </a>
            <a href="<?= base_url('admin/tahun-ajaran') ?>" class="nav-item <?= simaku_active('tahun-ajaran') ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-days"></i> Tahun Ajaran
            </a>
            <a href="<?= base_url('admin/jenis-tagihan') ?>" class="nav-item <?= simaku_active('jenis-tagihan') ? 'active' : '' ?>">
                <i class="fa-solid fa-tags"></i> Jenis Tagihan
            </a>

            <div class="nav-group-label">Keuangan</div>
            <a href="<?= base_url('admin/pembayaran') ?>" class="nav-item <?= simaku_active('admin/pembayaran') ? 'active' : '' ?>">
                <i class="fa-solid fa-wallet"></i> Pembayaran
            </a>
            <a href="<?= base_url('admin/tagihan') ?>" class="nav-item <?= simaku_active('admin/tagihan') ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan
            </a>
            <a href="<?= base_url('admin/skema-tagihan') ?>" class="nav-item <?= simaku_active('skema-tagihan') ? 'active' : '' ?>">
                <i class="fa-solid fa-sitemap"></i> Skema Tagihan
            </a>
            <a href="<?= base_url('admin/beasiswa') ?>" class="nav-item <?= simaku_active('admin/beasiswa') ? 'active' : '' ?>">
                <i class="fa-solid fa-award"></i> Beasiswa
            </a>
            <a href="<?= base_url('admin/laporan') ?>" class="nav-item <?= simaku_active('admin/laporan') ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-line"></i> Laporan
            </a>

            <div class="nav-group-label">Akademik</div>
            <a href="<?= base_url('admin/kenaikan-kelas') ?>" class="nav-item <?= simaku_active('kenaikan-kelas') ? 'active' : '' ?>">
                <i class="fa-solid fa-arrow-up-right-dots"></i> Kenaikan Kelas
            </a>

            <?php if ($__role === 'super_admin'): ?>
            <div class="nav-group-label">Pengaturan</div>
            <a href="<?= base_url('admin/users') ?>" class="nav-item <?= simaku_active('admin/users') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-shield"></i> User Management
            </a>
            <a href="<?= base_url('admin/audit-log') ?>" class="nav-item <?= simaku_active('audit-log') ? 'active' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> Audit Log
            </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-user">
            <div class="sidebar-user-avatar"><?= esc(strtoupper(substr(session()->get('nama_lengkap') ?? 'U', 0, 1))) ?></div>
            <div class="sidebar-user-info">
                <div class="name"><?= esc(session()->get('nama_lengkap')) ?></div>
                <div class="role"><?= esc(ucwords(str_replace('_', ' ', $__role ?? ''))) ?></div>
            </div>
            <a href="<?= base_url('admin/logout') ?>" class="sidebar-logout" title="Logout"><i class="fa-solid fa-power-off"></i></a>
        </div>
    </aside>

    <!-- ================= MOBILE NAV SHEET (isi sama dengan sidebar, dipakai lewat hamburger) ================= -->
    <div class="overlay" id="mobileNavOverlay" onclick="toggleMobileNav(false)"></div>
    <div class="mobile-nav-sheet" id="mobileNavSheet">
        <div class="modal-drag"></div>
        <div class="modal-header">
            <h3>Menu</h3>
            <button class="modal-close" onclick="toggleMobileNav(false)"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" style="padding-top:8px;">
            <a href="<?= base_url('admin/kelas') ?>" class="nav-item <?= simaku_active('admin/kelas') ? 'active' : '' ?>"><i class="fa-solid fa-chalkboard"></i> Kelas</a>
            <a href="<?= base_url('admin/tahun-ajaran') ?>" class="nav-item <?= simaku_active('tahun-ajaran') ? 'active' : '' ?>"><i class="fa-solid fa-calendar-days"></i> Tahun Ajaran</a>
            <a href="<?= base_url('admin/jenis-tagihan') ?>" class="nav-item <?= simaku_active('jenis-tagihan') ? 'active' : '' ?>"><i class="fa-solid fa-tags"></i> Jenis Tagihan</a>
            <a href="<?= base_url('admin/skema-tagihan') ?>" class="nav-item <?= simaku_active('skema-tagihan') ? 'active' : '' ?>"><i class="fa-solid fa-sitemap"></i> Skema Tagihan</a>
            <a href="<?= base_url('admin/beasiswa') ?>" class="nav-item <?= simaku_active('admin/beasiswa') ? 'active' : '' ?>"><i class="fa-solid fa-award"></i> Beasiswa</a>
            <a href="<?= base_url('admin/laporan') ?>" class="nav-item <?= simaku_active('admin/laporan') ? 'active' : '' ?>"><i class="fa-solid fa-chart-line"></i> Laporan</a>
            <a href="<?= base_url('admin/kenaikan-kelas') ?>" class="nav-item <?= simaku_active('kenaikan-kelas') ? 'active' : '' ?>"><i class="fa-solid fa-arrow-up-right-dots"></i> Kenaikan Kelas</a>
            <?php if ($__role === 'super_admin'): ?>
            <div class="nav-group-label">Pengaturan</div>
            <a href="<?= base_url('admin/users') ?>" class="nav-item <?= simaku_active('admin/users') ? 'active' : '' ?>"><i class="fa-solid fa-user-shield"></i> User Management</a>
            <a href="<?= base_url('admin/audit-log') ?>" class="nav-item <?= simaku_active('audit-log') ? 'active' : '' ?>"><i class="fa-solid fa-clock-rotate-left"></i> Audit Log</a>
            <?php endif; ?>
            <a href="<?= base_url('admin/logout') ?>" class="nav-item" style="color:var(--danger); margin-top:8px;"><i class="fa-solid fa-power-off"></i> Logout</a>
        </div>
    </div>

    <!-- ================= MAIN ================= -->
    <div class="main-content">

        <div class="mobile-topbar">
            <button class="hamburger" onclick="toggleMobileNav(true)"><i class="fa-solid fa-bars"></i></button>
            <h2><?= $title ?? 'Dashboard' ?></h2>
        </div>

        <div class="topbar">
            <h2><?= $title ?? 'Dashboard' ?></h2>
            <div class="topbar-actions" id="topbarActions"></div>
        </div>

        <div class="content">

            <div class="toast-stack" id="toastStack">
                <?php if (session()->getFlashdata('success')): ?>
                <div class="toast success"><i class="fa-solid fa-circle-check icon"></i><span><?= session()->getFlashdata('success') ?></span><i class="fa-solid fa-xmark close" onclick="this.parentElement.remove()"></i></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')): ?>
                <div class="toast error"><i class="fa-solid fa-circle-exclamation icon"></i><span><?= session()->getFlashdata('error') ?></span><i class="fa-solid fa-xmark close" onclick="this.parentElement.remove()"></i></div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('warning')): ?>
                <div class="toast warning"><i class="fa-solid fa-triangle-exclamation icon"></i><span><?= session()->getFlashdata('warning') ?></span><i class="fa-solid fa-xmark close" onclick="this.parentElement.remove()"></i></div>
                <?php endif; ?>
            </div>
