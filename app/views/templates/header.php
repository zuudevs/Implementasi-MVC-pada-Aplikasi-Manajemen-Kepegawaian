<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SIMKEP') ?> — <?= APP_NAME ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        /* ── Variables ─────────────────────────────────────────── */
        :root {
            --primary:       #1a56db;
            --primary-dark:  #1e429f;
            --primary-light: #e8f0fe;
            --accent:        #0e9f6e;
            --accent-light:  #d5f5e6;
            --warn:          #e3a008;
            --warn-light:    #fef3c7;
            --danger:        #e02424;
            --danger-light:  #fde8e8;
            --sidebar-w:     260px;
            --sidebar-bg:    #0f172a;
            --sidebar-text:  #94a3b8;
            --sidebar-hover: rgba(255,255,255,.06);
            --sidebar-active:#1a56db;
            --body-bg:       #f1f5f9;
            --card-bg:       #ffffff;
            --border:        #e2e8f0;
            --text-main:     #0f172a;
            --text-muted:    #64748b;
            --radius:        12px;
            --shadow:        0 1px 3px rgba(0,0,0,.07), 0 4px 16px rgba(0,0,0,.06);
            --font:          'Plus Jakarta Sans', sans-serif;
            --font-mono:     'JetBrains Mono', monospace;
        }

        /* ── Reset ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: var(--font);
            background: var(--body-bg);
            color: var(--text-main);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ── Sidebar ───────────────────────────────────────────── */
        #sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            z-index: 200;
            transition: transform .3s ease;
        }

        .sidebar-brand {
            padding: 22px 24px 18px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .brand-icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 9px;
            display: grid; place-items: center;
            color: #fff; font-size: 18px; flex-shrink: 0;
        }
        .brand-text { color: #fff; font-weight: 700; font-size: 16px; letter-spacing: -.2px; }
        .brand-sub  { color: var(--sidebar-text); font-size: 11px; margin-top: 1px; }

        .sidebar-nav { padding: 16px 12px; flex: 1; overflow-y: auto; }
        .nav-section-label {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 1px; color: rgba(255,255,255,.25);
            padding: 14px 12px 6px; display: block;
        }
        .nav-item { margin-bottom: 2px; }
        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px; border-radius: 8px;
            color: var(--sidebar-text); font-weight: 500;
            text-decoration: none; transition: all .15s;
            font-size: 13.5px;
        }
        .nav-link i { font-size: 16px; width: 20px; text-align: center; }
        .nav-link:hover  { background: var(--sidebar-hover); color: #fff; }
        .nav-link.active { background: var(--primary); color: #fff; }
        .nav-badge {
            margin-left: auto;
            background: rgba(255,255,255,.1); color: var(--sidebar-text);
            border-radius: 20px; padding: 1px 8px; font-size: 11px;
        }
        .nav-link.active .nav-badge { background: rgba(255,255,255,.2); color: #fff; }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid rgba(255,255,255,.07);
            color: var(--sidebar-text); font-size: 12px;
        }
        .sidebar-footer .version { color: rgba(255,255,255,.25); }

        /* ── Main Layout ───────────────────────────────────────── */
        #main {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* ── Topbar ────────────────────────────────────────────── */
        .topbar {
            position: sticky; top: 0; z-index: 100;
            background: var(--card-bg);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            height: 60px;
            display: flex; align-items: center;
            box-shadow: 0 1px 0 var(--border);
        }
        .topbar-title {
            font-size: 15px; font-weight: 700;
            color: var(--text-main); flex: 1;
        }
        .topbar-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--primary); color: #fff;
            font-weight: 700; font-size: 13px;
            display: grid; place-items: center;
        }

        /* ── Page Content ──────────────────────────────────────── */
        .page-content { padding: 28px; flex: 1; }

        /* ── Cards ─────────────────────────────────────────────── */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .card-header-custom {
            padding: 18px 24px 0;
            display: flex; align-items: center; gap: 12px;
        }
        .card-body { padding: 20px 24px 24px; }

        /* ── Stat Cards ────────────────────────────────────────── */
        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px 22px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: var(--shadow);
            transition: transform .15s, box-shadow .15s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 24px rgba(0,0,0,.1); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: grid; place-items: center;
            font-size: 22px; flex-shrink: 0;
        }
        .stat-icon.blue   { background: var(--primary-light); color: var(--primary); }
        .stat-icon.green  { background: var(--accent-light);  color: var(--accent); }
        .stat-icon.yellow { background: var(--warn-light);    color: var(--warn); }
        .stat-icon.red    { background: var(--danger-light);  color: var(--danger); }
        .stat-label { font-size: 12px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
        .stat-value { font-size: 26px; font-weight: 800; color: var(--text-main); line-height: 1; }

        /* ── Table ─────────────────────────────────────────────── */
        .table { margin: 0; }
        .table thead th {
            background: #f8fafc;
            color: var(--text-muted);
            font-size: 11px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .6px;
            padding: 11px 16px; border-bottom: 2px solid var(--border);
            white-space: nowrap;
        }
        .table tbody td {
            padding: 13px 16px; border-color: var(--border);
            vertical-align: middle; font-size: 13.5px;
        }
        .table tbody tr { transition: background .1s; }
        .table tbody tr:hover { background: #f8fafc; }
        .table > :not(caption) > * > * { background: transparent; }

        .nik-cell { font-family: var(--font-mono); font-size: 12px; color: var(--text-muted); }

        /* ── Status Badges ─────────────────────────────────────── */
        .status-badge {
            display: inline-flex; align-items: center;
            padding: 3px 10px; border-radius: 20px;
            font-size: 11.5px; font-weight: 600;
        }
        .badge-aktif    { background: var(--accent-light);  color: #065f46; }
        .badge-nonaktif { background: var(--danger-light);  color: #991b1b; }
        .badge-cuti     { background: var(--warn-light);    color: #92400e; }

        /* ── Action Buttons ────────────────────────────────────── */
        .btn-icon {
            width: 30px; height: 30px; padding: 0;
            display: inline-grid; place-items: center;
            border-radius: 7px; border: 1px solid var(--border);
            background: var(--card-bg); color: var(--text-muted);
            transition: all .15s; text-decoration: none;
            font-size: 14px;
        }
        .btn-icon:hover        { color: var(--primary); border-color: var(--primary); background: var(--primary-light); }
        .btn-icon.btn-edit:hover  { color: var(--warn); border-color: var(--warn); background: var(--warn-light); }
        .btn-icon.btn-danger:hover { color: var(--danger); border-color: var(--danger); background: var(--danger-light); }

        /* ── Form Controls ─────────────────────────────────────── */
        .form-label { font-weight: 600; font-size: 13px; margin-bottom: 6px; }
        .form-control, .form-select {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 13.5px; padding: 9px 13px;
            font-family: var(--font);
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(26,86,219,.1);
        }

        /* ── Buttons ───────────────────────────────────────────── */
        .btn { font-weight: 600; font-size: 13.5px; border-radius: 8px; padding: 8px 18px; }
        .btn-primary  { background: var(--primary); border-color: var(--primary); }
        .btn-primary:hover  { background: var(--primary-dark); border-color: var(--primary-dark); }
        .btn-success  { background: var(--accent); border-color: var(--accent); }
        .btn-success:hover  { filter: brightness(.9); }

        /* ── Pagination ────────────────────────────────────────── */
        .page-link { border-radius: 7px !important; margin: 0 2px; font-size: 13px; color: var(--text-main); border-color: var(--border); }
        .page-link:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary); }
        .page-item.active .page-link { background: var(--primary); border-color: var(--primary); }
        .page-item.disabled .page-link { color: #cbd5e1; }

        /* ── Search bar ────────────────────────────────────────── */
        .search-wrap { position: relative; }
        .search-wrap .bi-search {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: var(--text-muted); pointer-events: none;
        }
        .search-wrap input { padding-left: 36px; }

        /* ── Empty state ───────────────────────────────────────── */
        .empty-state {
            text-align: center; padding: 60px 24px;
            color: var(--text-muted);
        }
        .empty-state i { font-size: 48px; opacity: .25; margin-bottom: 16px; display: block; }

        /* ── Scrollbar ─────────────────────────────────────────── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }

        /* ── Animations ────────────────────────────────────────── */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .page-content > * { animation: slideIn .25s ease both; }

        /* ── Mobile ────────────────────────────────────────────── */
        @media (max-width: 768px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            #main { margin-left: 0; }
            .page-content { padding: 16px; }
        }
    </style>
</head>
<body>

<!-- ══ Sidebar ══════════════════════════════════════════════════════════════ -->
<nav id="sidebar">
    <a href="<?= URLROOT ?>/employee" class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-people-fill"></i></div>
        <div>
            <div class="brand-text"><?= APP_NAME ?></div>
            <div class="brand-sub">Manajemen Kepegawaian</div>
        </div>
    </a>

    <div class="sidebar-nav">
        <span class="nav-section-label">Menu Utama</span>
        <div class="nav-item">
            <a href="<?= URLROOT ?>/employee" class="nav-link active">
                <i class="bi bi-people"></i>
                Data Pegawai
            </a>
        </div>
        <div class="nav-item">
            <a href="<?= URLROOT ?>/employee/add" class="nav-link">
                <i class="bi bi-person-plus"></i>
                Tambah Pegawai
            </a>
        </div>
    </div>

    <div class="sidebar-footer">
        <div><?= APP_NAME ?> v<?= APP_VERSION ?></div>
        <div class="version mt-1">Kelompok 14 · <?= date('Y') ?></div>
    </div>
</nav>

<!-- ══ Main ═════════════════════════════════════════════════════════════════ -->
<div id="main">

    <!-- Topbar -->
    <header class="topbar">
        <button class="btn btn-sm d-md-none me-3 border-0" id="sidebarToggle">
            <i class="bi bi-list fs-5"></i>
        </button>
        <div class="topbar-title"><?= htmlspecialchars($title ?? '') ?></div>
        <div class="d-flex align-items-center gap-3">
            <div class="topbar-avatar">A</div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">

        <!-- Flash Messages -->
        <?= flash(FLASH_SUCCESS) ?>
        <?= flash(FLASH_ERROR)   ?>
        <?= flash(FLASH_WARNING) ?>
