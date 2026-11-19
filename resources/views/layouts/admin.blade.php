<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Admin - SIMASTA')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --neon: #34D399;
            --neon-dark: #10B981;
            --neon-soft: #D1FAE5;
            --orange: #FB923C;
            --orange-dark: #F97316;
            --orange-soft: #FFEDD5;
            --blue: #60A5FA;
            --blue-dark: #3B82F6;
            --blue-soft: #DBEAFE;
            --navy: #1E293B;
            --navy-soft: #334155;
            --footer-bg: #1E3A5F;

            /* lebar sidebar (ikut berubah saat menu ditutup) */
            --sb-w: 250px;
        }

        html.sb-collapsed { --sb-w: 78px; }

        body {
            font-family: Arial, sans-serif;
            background: #F1F5F9;
            color: #1f2937;
        }

        /* ================= SIDEBAR ================= */

        .sidebar {
            --sb-blue: #1d5fa8;
            --sb-blue-dark: #12457f;
            --sb-green: #2fa266;
            --sb-orange: #f5a65b;
            --sb-orange-dark: #ee8a3a;

            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sb-w);
            height: 100vh;
            z-index: 1000;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: #fff;
            transition: width .3s ease;
            background:
                radial-gradient(circle at 100% 0%, rgba(47,162,102,.35) 0, transparent 40%),
                radial-gradient(circle at 0% 100%, rgba(245,166,91,.25) 0, transparent 35%),
                linear-gradient(180deg, var(--sb-blue) 0%, var(--sb-blue-dark) 100%);
        }

        /* logo tanpa latar putih */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 18px;
            border-bottom: 1px solid rgba(255,255,255,.15);
            flex-shrink: 0;
            white-space: nowrap;
        }

        .sidebar-logo img {
            width: 42px; height: 42px;
            object-fit: contain;
            flex-shrink: 0;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,.25));
        }

        .sidebar-logo .sidebar-logo-text { line-height: 1.2; }
        .sidebar-logo .sidebar-logo-text .name { font-size: 16px; font-weight: 700; color: #fff; }
        .sidebar-logo .sidebar-logo-text .sub { font-size: 10px; letter-spacing: 1px; color: rgba(255,255,255,.8); }

        /* menu */
        .sidebar-menu {
            list-style: none;
            padding: 12px;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar-menu li { margin-bottom: 3px; }

        .sidebar-menu::-webkit-scrollbar { width: 5px; }
        .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,.28); border-radius: 10px; }
        .sidebar-menu { scrollbar-width: thin; scrollbar-color: rgba(255,255,255,.28) transparent; }

        .sidebar-menu a,
        .sb-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: #fff;
            text-decoration: none;
            white-space: nowrap;
            transition: .2s;
            border: 0;
            background: transparent;
            font-family: inherit;
            cursor: pointer;
            text-align: left;
        }

        .sidebar-menu i { font-size: 17px; width: 20px; text-align: center; flex-shrink: 0; }

        .sidebar-menu a:hover,
        .sb-toggle:hover { background: rgba(255,255,255,.14); }

        /* menu aktif = oranye */
        .sidebar-menu a.active {
            background: linear-gradient(120deg, var(--sb-orange), var(--sb-orange-dark));
            color: #fff;
            font-weight: 600;
            box-shadow: 0 6px 16px rgba(238,138,58,.4);
        }

        /* tombol tutup / buka menu */
        .sb-toggle {
            margin-bottom: 6px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            border-radius: 12px 12px 0 0;
        }
        .sb-toggle i { transition: transform .3s; }

        /* kartu user + keluar */
        .sb-user {
            margin: 12px;
            padding: 14px;
            border-radius: 16px;
            background: rgba(255,255,255,.12);
            flex-shrink: 0;
            overflow: hidden;
        }

        .sb-user-top { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }

        .sb-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: var(--sb-green);
            display: grid; place-items: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sb-user b { font-size: 13px; display: block; }
        .sb-user small { font-size: 11px; opacity: .85; word-break: break-all; }

        .sb-badge {
            display: inline-block;
            margin-top: 4px;
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            background: var(--sb-orange);
            color: #5a3200;
        }

        .logout-btn {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px;
            border: 0;
            border-radius: 12px;
            background: rgba(255,255,255,.18);
            color: #fff;
            font-weight: 600;
            font-size: 13px;
            font-family: inherit;
            cursor: pointer;
            transition: .2s;
        }

        .logout-btn:hover { background: var(--sb-orange); color: #5a3200; }

        /* ===== mode menu ditutup (hanya ikon) ===== */
        html.sb-collapsed .sidebar-logo { justify-content: center; padding: 18px 0; }

        html.sb-collapsed .sidebar-logo .sidebar-logo-text,
        html.sb-collapsed .sidebar-menu span,
        html.sb-collapsed .sb-user-info,
        html.sb-collapsed .logout-btn span { display: none; }

        html.sb-collapsed .sidebar-menu { padding: 12px 10px; }

        html.sb-collapsed .sidebar-menu a,
        html.sb-collapsed .sb-toggle { justify-content: center; padding: 11px 0; }

        html.sb-collapsed .sb-toggle i { transform: rotate(180deg); }

        html.sb-collapsed .sb-user { margin: 10px; padding: 10px; }
        html.sb-collapsed .sb-user-top { justify-content: center; margin-bottom: 10px; }

        /* ================= MAIN ================= */

        .main {
            margin-left: var(--sb-w);
            min-height: 100vh;
            transition: margin-left .3s ease;
        }

        /* TOPBAR */

        .topbar {
            height: 75px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .topbar-title { font-size: 20px; font-weight: bold; color: var(--navy); }

        .user-info { display: flex; align-items: center; gap: 12px; }
        .user-role { font-size: 14px; color: #374151; }

        .avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(120deg, var(--blue-dark), var(--neon-dark));
            color: white;
            display: flex; justify-content: center; align-items: center;
            font-weight: bold;
        }

        /* CONTENT */

        .content { padding: 30px; }

        .welcome { margin-bottom: 25px; }
        .welcome h1 { font-size: 26px; margin-bottom: 8px; color: var(--navy); }
        .welcome p { color: #64748b; }

        /* CARD */

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.03);
        }

        .card h2 { font-size: 19px; margin-bottom: 8px; color: var(--navy); }

        /* GRAFIK TREN & DISTRIBUSI */

        .grid-2 { display: grid; grid-template-columns: 1fr; gap: 20px; }

        @media (min-width: 900px) {
            .grid-2 { grid-template-columns: 1.3fr 1fr; }
        }

        .panel-head {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .panel-head .total { font-size: 20px; font-weight: 800; color: var(--blue-dark); }

        .trend-chart {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            height: 120px;
            padding-top: 22px;
        }

        .trend-bar-wrap {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .trend-bar {
            width: 100%;
            max-width: 36px;
            border-radius: 8px 8px 0 0;
            background: linear-gradient(180deg, var(--blue), var(--blue-dark));
            position: relative;
        }

        .trend-bar.is-current { background: linear-gradient(180deg, var(--neon), var(--neon-dark)); }

        .trend-bar .val {
            position: absolute;
            top: -20px; left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: 700;
            color: var(--navy);
            white-space: nowrap;
        }

        .trend-year { font-size: 11px; color: #94a3b8; }

        .campus-row {
            display: grid;
            grid-template-columns: 130px 1fr 32px;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 12.5px;
        }

        .campus-row .campus-name {
            color: var(--navy-soft);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .campus-bar-track { background: #f1f5f9; border-radius: 20px; height: 10px; overflow: hidden; }

        .campus-bar-fill {
            height: 100%;
            border-radius: 20px;
            background: linear-gradient(90deg, var(--neon-dark), var(--blue-dark));
        }

        .campus-row .campus-count { text-align: right; font-weight: 700; color: var(--navy); }

        /* AKTIVITAS TERBARU */

        .activity-list { display: flex; flex-direction: column; gap: 14px; }
        .activity-item { display: flex; gap: 12px; }

        .activity-icon {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
        }

        .activity-icon.info { background: var(--blue-soft); color: var(--blue-dark); }
        .activity-icon.success { background: var(--neon-soft); color: var(--neon-dark); }
        .activity-icon.warning { background: var(--orange-soft); color: var(--orange-dark); }

        .activity-text p { font-size: 13.5px; color: var(--navy); }
        .activity-text span { font-size: 11.5px; color: #94a3b8; }

        /* STATISTICS */

        .card-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }

        .stat-card { border-top: 3px solid var(--blue-dark); }

        .card-grid .stat-card:nth-child(1) { border-top-color: var(--blue-dark); }
        .card-grid .stat-card:nth-child(2) { border-top-color: var(--neon-dark); }
        .card-grid .stat-card:nth-child(3) { border-top-color: var(--orange-dark); }
        .card-grid .stat-card:nth-child(4) { border-top-color: var(--blue-dark); }

        .stat-card h3 { font-size: 13px; color: #64748b; margin-bottom: 14px; font-weight: 600; }

        .number { font-size: 30px; font-weight: 800; color: var(--navy); margin-bottom: 8px; }

        .card-grid .stat-card:nth-child(1) .number { color: var(--blue-dark); }
        .card-grid .stat-card:nth-child(2) .number { color: var(--neon-dark); }
        .card-grid .stat-card:nth-child(3) .number { color: var(--orange-dark); }
        .card-grid .stat-card:nth-child(4) .number { color: var(--blue-dark); }

        .stat-card p { color: #94a3b8; font-size: 12.5px; }

        /* TABLE */

        .table-container { width: 100%; overflow-x: auto; }

        table { width: 100%; border-collapse: collapse; }

        table th {
            background: #F8FAFC;
            text-align: left;
            padding: 13px 14px;
            font-size: 11.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #64748b;
            border-bottom: 1px solid #e5e7eb;
        }

        table td { padding: 14px; font-size: 14px; border-bottom: 1px solid #f1f5f9; }

        /* BUTTON */

        .btn {
            display: inline-block;
            padding: 9px 15px;
            border-radius: 7px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            background: linear-gradient(120deg, var(--blue-dark), var(--neon-dark));
            color: white;
            text-decoration: none;
            border-radius: 11px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            box-sizing: border-box;
            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.2);
        }

        .btn-reset:hover { background-color: #d9dce1; }
        .btn-primary:hover { opacity: 0.92; }

        .btn-secondary { background: #e5e7eb; color: #374151; }
        .btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

        .btn-danger { background: #dc2626; color: white; }

        /* BADGE */

        .badge {
            display: inline-block;
            padding: 5px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-warning { background: var(--orange-soft); color: var(--orange-dark); }
        .badge-success { background: var(--neon-soft); color: var(--neon-dark); }
        .badge-info { background: var(--blue-soft); color: var(--blue-dark); }
        .badge-danger { background: #fee2e2; color: #991b1b; }

        /* INFO */

        .info-list { display: flex; flex-direction: column; gap: 15px; }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* EMPTY */

        .empty-state { text-align: center; padding: 50px 20px; color: #6b7280; }
        .empty-state h3 { margin-bottom: 8px; color: #374151; }

        /* FORM */

        .form-group { margin-bottom: 18px; }

        .form-group label { display: block; margin-bottom: 7px; font-weight: 600; color: var(--navy); }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
            background: #f8fafc;
            transition: 0.2s;
        }

        .form-group textarea { min-height: 120px; resize: vertical; }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--blue);
            background: white;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.15);
        }

        .form-actions { display: flex; gap: 10px; margin-top: 20px; }

        /* RESPONSIVE */

        @media (max-width: 900px) {
            :root { --sb-w: 210px; }
            html.sb-collapsed { --sb-w: 78px; }

            .card-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 600px) {
            .sidebar { display: none; }

            .main,
            html.sb-collapsed .main { margin-left: 0; }

            .content { padding: 20px; }
            .card-grid { grid-template-columns: 1fr; }
            .topbar { padding: 0 20px; }
        }


        /* Submenu Data Pembimbing: mengikuti desain sidebar lama */
        .sidebar-menu .sb-submenu-toggle {
            display: flex; align-items: center; gap: 12px; width: 100%;
            padding: 10px 14px; border: 0; border-radius: 12px;
            background: transparent; color: #fff; font: 500 14px Arial, sans-serif;
            cursor: pointer; text-align: left; white-space: nowrap;
        }
        .sidebar-menu .sb-submenu-toggle:hover { background: rgba(255,255,255,.14); }
        .sidebar-menu .sb-submenu-toggle.active {
            background: linear-gradient(120deg, var(--sb-orange), var(--sb-orange-dark));
            box-shadow: 0 6px 16px rgba(238,138,58,.4); font-weight: 600;
        }
        .sb-submenu-chevron { margin-left: auto; font-size: 12px !important; }
        .sb-submenu[hidden] { display: none !important; }
        .sidebar-menu .sb-submenu { padding: 4px 0 4px 18px; list-style: none; }
        .sidebar-menu .sb-submenu a { font-size: 12.5px; padding: 9px 10px; }
        .sidebar-menu .sb-submenu a i { font-size: 15px; }
        html.sb-collapsed .sidebar-menu .sb-submenu-toggle {
            justify-content: center; padding: 11px 0;
        }
        html.sb-collapsed .sb-submenu-chevron,
        html.sb-collapsed .sb-submenu { display: none !important; }


        /* PENYEMPURNAAN HEADER, FOTO, NOTIFIKASI DAN MOBILE */
        .sb-avatar,.avatar{overflow:hidden}
        .sb-avatar img,.admin-top-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}
        .sb-avatar span[hidden],.admin-top-avatar span[hidden]{display:none}
        .admin-top-controls{gap:14px}
        .admin-dropdown-container{position:relative}
        .admin-icon-btn{width:43px;height:43px;border:1px solid #e2e8f0;border-radius:50%;background:#f8fafc;color:#1d5fa8;display:grid;place-items:center;cursor:pointer;position:relative;font-size:19px}
        .admin-icon-btn:hover,.admin-profile-button:hover{background:#eff6ff}
        .admin-notif-count{position:absolute;right:-5px;top:-5px;min-width:19px;height:19px;border-radius:12px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;display:grid;place-items:center;padding:0 4px}
        .admin-profile-button{display:flex;align-items:center;gap:11px;border:0;border-radius:12px;padding:5px 7px;background:transparent;cursor:pointer;color:#193c63;font-family:inherit}
        .admin-profile-name{font-weight:750;font-size:13px;max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;text-align:right}
        .admin-profile-name small{display:block;font-size:10px;color:#8494a6;font-weight:500;margin-top:3px}
        .admin-top-avatar{flex-shrink:0;width:42px;height:42px;background:#dbeafe;color:#1e5fa8}
        .admin-chevron{font-size:12px;color:#71839b}
        .admin-dropdown[hidden]{display:none!important}
        .admin-dropdown{position:absolute;right:0;top:calc(100% + 13px);width:min(355px,calc(100vw - 30px));background:white;border:1px solid #e2e8f0;border-radius:14px;box-shadow:0 17px 50px rgba(26,52,84,.16);z-index:1200;overflow:hidden}
        .admin-dropdown-title{padding:16px;border-bottom:1px solid #edf2f6;display:flex;flex-direction:column;gap:5px}
        .admin-dropdown-title strong{font-size:13px;color:#193c63}.admin-dropdown-title small{font-size:11px;color:#71839b;overflow-wrap:anywhere}
        .admin-notif-list{max-height:310px;overflow:auto}
        .admin-notif-item{display:flex;align-items:flex-start;gap:11px;padding:12px 15px;border-bottom:1px solid #f1f5f9;text-decoration:none;color:#334155}
        .admin-notif-item:hover{background:#f4f9ff}
        .admin-notif-mark{width:32px;height:32px;flex-shrink:0;display:grid;place-items:center;border-radius:10px;background:#eff6ff;color:#245fa6}
        .admin-notif-detail{display:flex;flex-direction:column;gap:4px;min-width:0}.admin-notif-detail strong{font-size:12px;color:#193c63}.admin-notif-detail small{font-size:11px;color:#8193a5}
        .admin-notif-empty{padding:22px 15px;text-align:center;font-size:12px;color:#71839b}
        .admin-notif-form{margin:0}
        .admin-notif-action{width:100%;border:0;background:#fff;text-align:left;font-family:inherit;cursor:pointer}
        .admin-notif-action.is-unread{background:#eff6ff}
        .admin-notif-action:focus-visible{outline:2px solid #245fa6;outline-offset:-2px}
        .admin-notif-detail small{line-height:1.5;overflow-wrap:anywhere}
        .admin-notif-unread-dot{background:#2563eb;width:8px;height:8px;border-radius:50%;flex-shrink:0;margin-top:5px}
        .admin-notif-bottom{display:flex;align-items:center;justify-content:space-around;flex-wrap:wrap;gap:6px;border-top:1px solid #edf2f6;padding:6px}
        .admin-notif-bottom form{margin:0}
        .admin-notif-all{border:0;background:transparent;color:#245fa6;font-family:inherit;font-size:11px;font-weight:700;cursor:pointer;padding:12px}
        .admin-notif-all:hover{text-decoration:underline}

        .admin-dropdown-footer{display:flex;justify-content:center;align-items:center;gap:8px;padding:13px;font-size:12px;font-weight:750;color:#245fa6;text-decoration:none;background:#f8fbff}
        .admin-profile-link{display:flex;align-items:center;gap:10px;padding:13px 16px;width:100%;border:0;background:white;text-align:left;color:#334155;text-decoration:none;cursor:pointer;font:600 12px Arial,sans-serif}
        .admin-profile-link:hover{background:#f1f7ff}.admin-profile-logout{color:#b91c1c}
        .admin-mobile-trigger{display:none;width:41px;height:41px;border:1px solid #dbe5ef;border-radius:11px;background:#fff;color:#245fa6;place-items:center;font-size:21px;cursor:pointer}
        .admin-mobile-scrim{display:none}
        .admin-dropdown-container button:focus-visible,.admin-mobile-trigger:focus-visible{outline:2px solid #245fa6;outline-offset:2px}
        @media(max-width:600px){
            .sidebar{display:flex!important;width:260px!important;transform:translateX(-100%);transition:transform .25s ease;z-index:1300}
            .sidebar.admin-mobile-visible{transform:translateX(0)}
            html.sb-collapsed .sidebar-logo .sidebar-logo-text,html.sb-collapsed .sidebar-menu span,html.sb-collapsed .sb-user-info,html.sb-collapsed .logout-btn span{display:initial}
            html.sb-collapsed .sidebar-menu a,html.sb-collapsed .sb-toggle{justify-content:flex-start;padding:10px 14px}
            html.sb-collapsed .sb-submenu{display:block!important}
            .admin-mobile-trigger{display:grid}
            .admin-mobile-scrim:not([hidden]){display:block;position:fixed;inset:0;background:rgba(15,23,42,.48);z-index:1250}
            .topbar{gap:12px;padding:0 14px}.topbar-title{font-size:16px;flex:1}
            .admin-profile-name,.admin-chevron{display:none}
            .content{padding:15px}
            html.sb-collapsed .sb-submenu-chevron{display:inline!important}
        }

    </style>

    {{-- pulihkan pilihan menu tertutup/terbuka sebelum halaman tampil (tanpa kedip) --}}
    <script>
        try {
            if (localStorage.getItem('simasta_sidebar_collapsed') === '1') {
                document.documentElement.classList.add('sb-collapsed');
            }
        } catch (e) {}
    </script>
</head>


<body>
@php
    $adminAccount = auth()->user();
    $adminName = $adminAccount?->name ?? 'Administrator';
    $adminEmail = $adminAccount?->email ?? '';
    $adminInitial = mb_strtoupper(mb_substr($adminName, 0, 1));

    // Foto admin menggunakan kolom akun yang sudah tersedia (jika ada).
    $adminPhotoPath = data_get($adminAccount, 'foto') ?: data_get($adminAccount, 'foto_profil');
    $adminPhotoUrl = null;
    if (filled($adminPhotoPath)) {
        $path = ltrim(str_replace('\\', '/', (string) $adminPhotoPath), '/');
        if (str_starts_with($path, 'storage/')) $path = substr($path, 8);
        if (!str_contains($path, '..') && \Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            $adminPhotoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        }
    }

    // Lonceng Admin menampilkan notifikasi database akun yang login.
    $adminUnreadCount = $adminAccount->unreadNotifications()->count();
    $adminNotifications = $adminAccount->notifications()
        ->latest('created_at')->limit(8)->get();
@endphp

@php
    // Urutan menu utama; pertahankan URL modul lama.
    $adminMenus = [
        ['Dashboard', 'admin/dashboard', 'bi-grid-1x2'],
        ['Verifikasi Magang', 'admin/pengajuan', 'bi-inbox'],
        ['Data Mahasiswa', 'admin/mahasiswa', 'bi-people'],
        ['Monitoring Logbook', 'admin/logbook', 'bi-journal-text'],
        ['Penilaian', 'admin/penilaian', 'bi-award'],
    ];

    $pembimbingMenus = [
        ['Kelola Tim Kerja', 'admin/divisi', 'bi-diagram-3'],
        ['Kelola Data Pembimbing', 'admin/pembimbing', 'bi-person-badge'],
        ['Penempatan Magang', 'admin/penempatan', 'bi-geo-alt'],
    ];
    $pembimbingAktif = request()->is('admin/divisi*')
        || request()->is('admin/pembimbing*')
        || request()->is('admin/penempatan*');
@endphp

<!-- SIDEBAR -->

<div class="admin-mobile-scrim" id="adminMobileScrim" hidden></div>
<aside class="sidebar" id="adminSidebar">

    <div class="sidebar-logo">
        <img src="{{ asset('images/logo-bps.png') }}" alt="Logo BPS">
        <div class="sidebar-logo-text">
            <div class="name">SIMASTA</div>
            <div class="sub">ADMIN · BPS KOTA BATU</div>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li>
            <button type="button" class="sb-toggle" id="sbToggle" title="Tutup / buka menu" aria-label="Tutup atau buka sidebar">
                <i class="bi bi-chevron-double-left"></i>
            </button>
        </li>

        @foreach(array_slice($adminMenus, 0, 3) as [$label, $path, $icon])
            <li>
                <a href="/{{ $path }}" title="{{ $label }}"
                   class="{{ request()->is($path.'*') ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i>
                    <span>{{ $label }}</span>
                </a>
            </li>
        @endforeach

        <li>
            <button type="button" class="sb-submenu-toggle {{ $pembimbingAktif ? 'active' : '' }}"
                    id="pembimbingToggle" aria-expanded="true"
                    aria-controls="pembimbingSubmenu" title="Manajemen Magang">
                <i class="bi bi-briefcase"></i>
                <span>Manajemen Magang</span>
                <i class="bi bi-chevron-down sb-submenu-chevron" aria-hidden="true"></i>
            </button>
            <ul id="pembimbingSubmenu" class="sb-submenu">
                @foreach($pembimbingMenus as [$label, $path, $icon])
                    <li>
                        <a href="/{{ $path }}" title="{{ $label }}"
                           class="{{ request()->is($path.'*') ? 'active' : '' }}">
                            <i class="bi {{ $icon }}"></i>
                            <span>{{ $label }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

        @foreach(array_slice($adminMenus, 3) as [$label, $path, $icon])
            <li>
                <a href="/{{ $path }}" title="{{ $label }}"
                   class="{{ request()->is($path.'*') ? 'active' : '' }}">
                    <i class="bi {{ $icon }}"></i>
                    <span>{{ $label }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <div class="sb-user">
        <div class="sb-user-top">
            <div class="sb-avatar" title="{{ $adminName }}">
                @if($adminPhotoUrl)
                    <img src="{{ $adminPhotoUrl }}" alt="Foto profil Admin" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                    <span hidden>{{ $adminInitial }}</span>
                @else
                    <span>{{ $adminInitial }}</span>
                @endif
            </div>
            <div class="sb-user-info">
                <b>{{ $adminName }}</b>
                <small>{{ $adminEmail }}</small><br>
                <span class="sb-badge">Administrator</span>
            </div>
        </div>

        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="logout-btn" title="Keluar">
                <i class="bi bi-box-arrow-right"></i> <span>Keluar</span>
            </button>
        </form>
    </div>

</aside>


<!-- MAIN -->

<main class="main">

    <!-- TOPBAR -->

    <header class="topbar">
        <button type="button" id="adminMobileOpen" class="admin-mobile-trigger" aria-label="Buka menu Admin" aria-expanded="false" aria-controls="adminSidebar"><i class="bi bi-list"></i></button>
        <div class="topbar-title">
            @yield('page-title', 'Dashboard Admin')
        </div>

        <div class="user-info admin-top-controls">
            <div class="admin-dropdown-container" id="adminNotifRoot">
                <button type="button" class="admin-icon-btn" id="adminNotifButton"
                    aria-label="Notifikasi Admin, {{ $adminUnreadCount }} belum dibaca"
                    aria-haspopup="true" aria-expanded="false" aria-controls="adminNotifMenu">
                    <i class="bi bi-bell" aria-hidden="true"></i>
                    @if($adminUnreadCount > 0)
                        <span class="admin-notif-count">{{ $adminUnreadCount > 99 ? '99+' : $adminUnreadCount }}</span>
                    @endif
                </button>
                <div class="admin-dropdown admin-notif-menu" id="adminNotifMenu" hidden>
                    <div class="admin-dropdown-title">
                        <strong>Notifikasi SIMASTA</strong>
                        <small>{{ $adminUnreadCount }} notifikasi belum dibaca</small>
                    </div>
                    <div class="admin-notif-list">
                        @forelse($adminNotifications as $notif)
                            @php
                                $isiNotif = is_array($notif->data) ? $notif->data : [];
                                $notifTitle = $isiNotif['title'] ?? $isiNotif['judul'] ?? 'Informasi SIMASTA';
                                $notifMessage = $isiNotif['message'] ?? $isiNotif['pesan'] ?? 'Ada pembaruan pengajuan magang.';
                            @endphp
                            <form method="POST" action="{{ route('admin.notifikasi.buka', $notif->id) }}" class="admin-notif-form">
                                @csrf
                                <button type="submit" class="admin-notif-item admin-notif-action {{ is_null($notif->read_at) ? 'is-unread' : '' }}">
                                    <span class="admin-notif-mark"><i class="bi bi-file-earmark-text"></i></span>
                                    <span class="admin-notif-detail">
                                        <strong>{{ $notifTitle }}</strong>
                                        <small>{{ $notifMessage }}</small>
                                        <small>{{ $notif->created_at?->diffForHumans() }}</small>
                                    </span>
                                    @if(is_null($notif->read_at))
                                        <span class="admin-notif-unread-dot" aria-label="Belum dibaca"></span>
                                    @endif
                                </button>
                            </form>
                        @empty
                            <div class="admin-notif-empty">Belum ada notifikasi untuk akun Admin ini.</div>
                        @endforelse
                    </div>
                    <div class="admin-notif-bottom">
                        <a class="admin-dropdown-footer" href="{{ route('admin.pengajuan.index') }}">Lihat pengajuan <i class="bi bi-arrow-right"></i></a>
                        @if($adminUnreadCount > 0)
                            <form method="POST" action="{{ route('admin.notifikasi.baca-semua') }}">
                                @csrf
                                <button class="admin-notif-all" type="submit"><i class="bi bi-check2-all"></i> Tandai semua dibaca</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="admin-dropdown-container" id="adminProfileRoot">
                <button type="button" class="admin-profile-button" id="adminProfileButton"
                    aria-haspopup="true" aria-expanded="false" aria-controls="adminProfileMenu">
                    <span class="admin-profile-name">{{ $adminName }}<small>Administrator</small></span>
                    <span class="avatar admin-top-avatar">
                        @if($adminPhotoUrl)
                            <img src="{{ $adminPhotoUrl }}" alt="Foto profil Admin" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                            <span hidden>{{ $adminInitial }}</span>
                        @else
                            <span>{{ $adminInitial }}</span>
                        @endif
                    </span>
                    <i class="bi bi-chevron-down admin-chevron"></i>
                </button>
                <div class="admin-dropdown admin-profile-menu" id="adminProfileMenu" hidden>
                    <div class="admin-dropdown-title admin-profile-heading">
                        <strong>{{ $adminName }}</strong>
                        <small>{{ $adminEmail }}</small>
                        <small>Administrator · SIMASTA</small>
                    </div>
                    @if(\Illuminate\Support\Facades\Route::has('admin.profil'))
                        <a href="{{ route('admin.profil') }}" class="admin-profile-link"><i class="bi bi-person"></i> Profil Saya</a>
                    @elseif(\Illuminate\Support\Facades\Route::has('profile.edit'))
                        <a href="{{ route('profile.edit') }}" class="admin-profile-link"><i class="bi bi-person"></i> Profil Saya</a>
                    @endif
                    <form method="POST" action="{{ url('logout') }}">
                        @csrf
                        <button type="submit" class="admin-profile-link admin-profile-logout"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                    </form>
                </div>
            </div>
        </div>

    </header>

    <!-- CONTENT -->

    <section class="content">
        @yield('content')
    </section>

</main>


<script>
    document.getElementById('sbToggle').addEventListener('click', function () {
        var closed = document.documentElement.classList.toggle('sb-collapsed');
        try { localStorage.setItem('simasta_sidebar_collapsed', closed ? '1' : '0'); } catch (e) {}
    });

    const pembimbingToggle = document.getElementById('pembimbingToggle');
    const pembimbingSubmenu = document.getElementById('pembimbingSubmenu');
    pembimbingToggle.addEventListener('click', function () {
        // Saat sidebar ikon-only, perluas terlebih dahulu agar submenu terlihat.
        if (document.documentElement.classList.contains('sb-collapsed')) {
            document.documentElement.classList.remove('sb-collapsed');
            try { localStorage.setItem('simasta_sidebar_collapsed', '0'); } catch (e) {}
        }
        const membuka = pembimbingSubmenu.hidden;
        pembimbingSubmenu.hidden = !membuka;
        pembimbingToggle.setAttribute('aria-expanded', String(membuka));
    });

    // Dropdown notifikasi dan profil.
    (function () {
        const pairs = [
            [document.getElementById('adminNotifButton'), document.getElementById('adminNotifMenu')],
            [document.getElementById('adminProfileButton'), document.getElementById('adminProfileMenu')]
        ];
        function closeMenus() {
            pairs.forEach(([b,m]) => { if (m) m.hidden = true; if (b) b.setAttribute('aria-expanded','false'); });
        }
        pairs.forEach(([button,menu]) => {
            if (!button || !menu) return;
            button.addEventListener('click', e => {
                e.stopPropagation();
                const opening = menu.hidden;
                closeMenus();
                menu.hidden = !opening;
                button.setAttribute('aria-expanded', String(opening));
            });
            menu.addEventListener('click', e => e.stopPropagation());
        });
        document.addEventListener('click', closeMenus);
        const sidebar = document.getElementById('adminSidebar');
        const trigger = document.getElementById('adminMobileOpen');
        const scrim = document.getElementById('adminMobileScrim');
        function mobile(open) {
            sidebar?.classList.toggle('admin-mobile-visible',open);
            if (scrim) scrim.hidden = !open;
            trigger?.setAttribute('aria-expanded', String(open));
            document.body.style.overflow = open ? 'hidden' : '';
        }
        trigger?.addEventListener('click', () => mobile(!sidebar?.classList.contains('admin-mobile-visible')));
        scrim?.addEventListener('click', () => mobile(false));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') { closeMenus(); mobile(false); }
        });
        window.addEventListener('resize', () => { if (window.innerWidth > 600) mobile(false); });
    })();
</script>

</body>

</html>