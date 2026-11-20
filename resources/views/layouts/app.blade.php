<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'SIMASTA')
    </title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #1f2937;
        }

        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            padding: 25px 18px;
        }

        .sidebar-logo {
            font-size: 25px;
            font-weight: bold;
            color: #2563eb;
            padding: 0 12px 30px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 6px;
        }

        .sidebar-menu a {
            display: block;
            text-decoration: none;
            color: #4b5563;
            padding: 12px;
            border-radius: 8px;
            transition: 0.2s;
        }

        .sidebar-menu a:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .sidebar-menu .active {
            background: #2563eb;

            color: white;
        }

        /* MAIN */

       .main {
    margin-left: var(--sb-w, 250px);
    transition: margin-left .3s ease;

    min-height: 100vh;
}

        /* TOPBAR */

        .topbar {
            height: 75px;

            background: white;

            border-bottom: 1px solid #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 35px;
        }

        .topbar-title {
            font-size: 20px;

            font-weight: bold;
        }

        .user-info {
            display: flex;

            align-items: center;

            gap: 10px;
        }

        .avatar {
            width: 38px;
            height: 38px;

            border-radius: 50%;

            background: #dbeafe;

            display: flex;

            align-items: center;

            justify-content: center;

            color: #2563eb;

            font-weight: bold;
        }

        /* CONTENT */

        .content {
            padding: 35px;
        }

        .welcome {
            margin-bottom: 30px;
        }

        .welcome h1 {
            font-size: 28px;

            margin-bottom: 8px;
        }

        .welcome p {
            color: #6b7280;
        }

        /* CARD */

        .card-grid {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;

            margin-bottom: 30px;
        }

        .card {
            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            padding: 22px;
        }

        .card-title {
            color: #6b7280;

            font-size: 14px;

            margin-bottom: 10px;
        }

        .card-number {
            font-size: 28px;

            font-weight: bold;

            color: #2563eb;
        }

        /* TABLE */

        .table-card {
            background: white;

            border: 1px solid #e5e7eb;

            border-radius: 12px;

            padding: 25px;

            overflow-x: auto;
        }

        .table-card h2 {
            margin-bottom: 20px;

            font-size: 20px;
        }

        table {
            width: 100%;

            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;

            text-align: left;

            border-bottom: 1px solid #e5e7eb;

            font-size: 14px;
        }

        th {
            background: #f8fafc;
        }

        .status {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 12px;
        }

        .status-success {
            background: #dcfce7;

            color: #166534;
        }

        .status-warning {
            background: #fef3c7;

            color: #92400e;
        }
        
        /* PROFILE */

        .profile-grid {
            display: grid;

            grid-template-columns:
        repeat(2, 1fr);

            gap: 20px;
        }

        .profile-item label {
            display: block;

            margin-bottom: 8px;

            font-size: 14px;

            font-weight: 600;

            color: #374151;
        }

        .profile-item input {
            width: 100%;

            padding: 12px 14px;

            border: 1px solid #e5e7eb;

            border-radius: 8px;

            background: #f8fafc;

            color: #374151;

            outline: none;
        }

/* FORM */

.form-card {
    margin-bottom: 20px;
}

.form-card h2 {
    font-size: 20px;

    margin-bottom: 8px;
}

.form-description {
    color: #6b7280;

    font-size: 14px;

    margin-bottom: 25px;
}

.form-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-size: 14px;

    font-weight: 600;

    color: #374151;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;

    padding: 12px 14px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    font-family: inherit;

    font-size: 14px;

    background: white;

    outline: none;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #2563eb;
}

.form-group textarea {
    resize: vertical;
}

.form-group small {
    display: block;

    margin-top: 6px;

    color: #6b7280;

    font-size: 12px;
}

.checkbox-group {
    display: flex;

    align-items: flex-start;

    gap: 10px;
}

.checkbox-group input {
    margin-top: 3px;
}

.checkbox-group label {
    font-size: 14px;

    line-height: 1.6;

    color: #4b5563;
}

.form-actions {
    display: flex;

    justify-content: flex-end;

    gap: 12px;

    margin-bottom: 30px;
}

.btn-primary,
.btn-secondary {
    border: none;

    padding: 12px 20px;

    border-radius: 8px;

    font-size: 14px;

    cursor: pointer;
}

.btn-primary {
    background: #2563eb;

    color: white;
}

.btn-secondary {
    background: #e5e7eb;

    color: #374151;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary:hover {
    background: #d1d5db;
}

/* DOCUMENT */

.document-info {
    display: flex;

    align-items: center;

    gap: 20px;

    background: #eff6ff;

    border: 1px solid #dbeafe;
}

.info-icon {
    width: 55px;

    height: 55px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: white;

    border-radius: 12px;

    font-size: 25px;
}

.document-info h3 {
    margin-bottom: 5px;
}

.document-info p {
    color: #6b7280;

    font-size: 14px;
}


.document-list {
    margin-top: 25px;
}


.document-item {
    display: flex;

    align-items: center;

    gap: 20px;

    padding: 20px 0;

    border-bottom: 1px solid #e5e7eb;
}

.document-item:last-child {
    border-bottom: none;
}


.document-icon {
    width: 50px;

    height: 50px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eff6ff;

    border-radius: 10px;

    font-size: 22px;
}


.document-detail {
    flex: 1;
}

.document-detail h3 {
    margin-bottom: 5px;

    font-size: 16px;
}

.document-detail p {
    margin-bottom: 10px;

    color: #6b7280;

    font-size: 13px;
}


.document-action {
    flex-shrink: 0;
}


.document-rules {
    padding-left: 20px;

    color: #4b5563;

    line-height: 2;
}

/* PLACEMENT */

.placement-status {
    display: flex;

    align-items: center;

    gap: 20px;

    padding: 25px;

    background: #f0fdf4;

    border: 1px solid #bbf7d0;

    border-radius: 12px;
}

.placement-status-icon {
    width: 55px;

    height: 55px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #16a34a;

    color: white;

    font-size: 25px;

    font-weight: bold;
}

.placement-status span {
    font-size: 13px;

    color: #6b7280;
}

.placement-status h2 {
    margin: 3px 0;

    font-size: 20px;

    color: #15803d;
}

.placement-status p {
    color: #6b7280;

    font-size: 14px;
}


/* PLACEMENT GRID */

.placement-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 25px;

    margin-top: 25px;
}

.placement-item {
    display: flex;

    flex-direction: column;

    gap: 7px;

    padding: 15px;

    background: #f8fafc;

    border-radius: 8px;
}

.placement-item span {
    font-size: 13px;

    color: #6b7280;
}

.placement-item strong {
    font-size: 15px;

    color: #1f2937;
}

.status-text {
    color: #16a34a !important;
}


/* SUPERVISOR */

.supervisor-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;

    margin-top: 25px;
}

.supervisor-card {
    display: flex;

    align-items: center;

    gap: 15px;

    padding: 20px;

    border: 1px solid #e5e7eb;

    border-radius: 10px;
}

.supervisor-icon {
    width: 50px;

    height: 50px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eff6ff;

    border-radius: 50%;

    font-size: 22px;
}

.supervisor-card span {
    display: block;

    margin-bottom: 5px;

    color: #6b7280;

    font-size: 13px;
}

.supervisor-card h3 {
    margin-bottom: 3px;

    font-size: 16px;
}

.supervisor-card p {
    color: #6b7280;

    font-size: 13px;
}

/* CATATAN */

.placement-note {
    margin-top: 15px;

    color: #6b7280;

    line-height: 1.7;
}

/* LOGBOOK */

.logbook-header {
    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;
}

.logbook-header h2 {
    margin-bottom: 5px;
}

.logbook-header p {
    color: #6b7280;

    font-size: 14px;
}


/* TABLE */

.table-wrapper {
    width: 100%;

    overflow-x: auto;
}

.logbook-table {
    width: 100%;

    border-collapse: collapse;

    min-width: 750px;
}

.logbook-table th {
    padding: 15px;

    text-align: left;

    background: #f8fafc;

    color: #374151;

    font-size: 13px;

    font-weight: 600;

    border-bottom: 1px solid #e5e7eb;
}

.logbook-table td {
    padding: 15px;

    border-bottom: 1px solid #e5e7eb;

    font-size: 14px;

    color: #4b5563;
}


/* EMPTY */

.empty-logbook {
    padding: 50px 20px;

    text-align: center;
}

.empty-icon {
    font-size: 45px;

    margin-bottom: 15px;
}

.empty-logbook h3 {
    margin-bottom: 8px;

    color: #1f2937;
}

.empty-logbook p {
    margin-bottom: 20px;

    color: #6b7280;
}

/* STATUS */

.logbook-status {
    display: inline-block;

    padding: 5px 10px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 600;
}

.status-pending {
    background: #fef3c7;

    color: #92400e;
}

.status-approved {
    background: #dcfce7;

    color: #166534;
}

.status-rejected {
    background: #fee2e2;

    color: #991b1b;
}

/* ADMIN DASHBOARD */

.admin-welcome {

    margin-bottom: 25px;

}

.admin-welcome h2 {

    margin-bottom: 8px;

}

.admin-welcome p {

    color: #6b7280;

    line-height: 1.6;

}

.admin-section-header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 20px;

}


.admin-section-header h2 {

    margin-bottom: 7px;

}

.admin-section-header p {

    color: #6b7280;

    font-size: 14px;

}

.admin-link {

    text-decoration: none;

    color: #2563eb;

    font-weight: 600;

    font-size: 14px;

}

.admin-grid {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 25px;

}


.admin-description {

    color: #6b7280;

    font-size: 14px;

    margin-top: 8px;

    line-height: 1.6;

}


.admin-empty {

    text-align: center;

    padding: 35px 15px;

}


.admin-empty-icon {

    font-size: 35px;

    margin-bottom: 12px;

}


.admin-empty h3 {

    margin-bottom: 7px;

}


.admin-empty p {

    color: #6b7280;

    font-size: 14px;

}



.info-list {

    margin-top: 25px;

}


.info-item {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 15px 0;

    border-bottom: 1px solid #e5e7eb;

}


.info-item:last-child {

    border-bottom: none;

}


.info-item span:first-child {

    color: #6b7280;

}


.info-item strong {

    color: #1f2937;

}



.quick-actions {

    display: grid;

    grid-template-columns: repeat(2, 1fr);

    gap: 15px;

    margin-top: 25px;

}


.quick-action {

    display: flex;

    align-items: center;

    gap: 15px;

    padding: 18px;

    border: 1px solid #e5e7eb;

    border-radius: 12px;

    text-decoration: none;

    color: inherit;

    transition: 0.2s;

}


.quick-action:hover {

    border-color: #2563eb;

    background: #f8fafc;

}


.quick-icon {

    width: 45px;

    height: 45px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #eff6ff;

    border-radius: 10px;

    font-size: 21px;

    flex-shrink: 0;

}


.quick-action h3 {

    margin-bottom: 5px;

    font-size: 15px;

}


.quick-action p {

    color: #6b7280;

    font-size: 13px;

}

/* DATA MAHASISWA */

.table-toolbar {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    margin: 25px 0;

}


.search-box {

    flex: 1;

}


.search-box input {

    width: 100%;

    padding: 12px 15px;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    font-size: 14px;

    outline: none;

}


.search-box input:focus {

    border-color: #2563eb;

}


.filter-box select {

    padding: 12px 15px;

    border: 1px solid #e5e7eb;

    border-radius: 8px;

    background: white;

    font-size: 14px;

    min-width: 160px;

    outline: none;

}


.table-container {

    width: 100%;

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

}


table th {

    text-align: left;

    padding: 15px;

    background: #f8fafc;

    color: #374151;

    font-size: 14px;

    white-space: nowrap;

}


table td {

    padding: 15px;

    border-bottom: 1px solid #e5e7eb;

    font-size: 14px;

    color: #374151;

}


table tbody tr:hover {

    background: #f8fafc;

}


.badge {

    display: inline-block;

    padding: 6px 10px;

    border-radius: 6px;

    font-size: 12px;

    font-weight: 600;

}


.badge-warning {

    background: #fef3c7;

    color: #92400e;

}


.badge-success {

    background: #dcfce7;

    color: #166534;

}


.badge-info {

    background: #dbeafe;

    color: #1e40af;

}


.badge-danger {

    background: #fee2e2;

    color: #991b1b;

}

/* MOBILE */

@media (max-width: 900px) {

    :root { --sb-w: 210px; }
    html.sb-collapsed { --sb-w: 78px; }

    .card-grid {
        grid-template-columns:
        repeat(2, 1fr);
    }

}

@media (max-width: 600px) {

    .mhs-side {
        display: none;
    }

    .main,
    html.sb-collapsed .main {
        margin-left: 0;
    }

    .card-grid {
        grid-template-columns: 1fr;
    }

    .content {
        padding: 20px;
    }

    .form-grid {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

        .btn-primary,
    .btn-secondary {
        width: 100%;
    }

    .document-item {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .document-action {
        width: 100%;
        margin-left: 70px;
    }

    .document-action .btn-primary {
        width: 100%;
    }

    .document-info {
        align-items: flex-start;
    }

    .placement-grid {
        grid-template-columns: 1fr;
    }

    .supervisor-grid {
        grid-template-columns: 1fr;
    }

    .placement-status {
        align-items: flex-start;
    }

    .logbook-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .logbook-header .btn-primary {
        width: 100%;
        text-align: center;
    }

    .admin-grid {
        grid-template-columns: 1fr;
    }

    .quick-actions {
    grid-template-columns: 1fr;
    }

    .section-header {
    flex-direction: column;
    }

    .text-link {
    margin-top: 5px;
    }

    .admin-grid {
    grid-template-columns: 1fr;
    }

    .quick-actions {
    grid-template-columns: 1fr;
    }

    .admin-section-header {
    flex-direction: column;
    }

    .admin-link {
    margin-top: 5px;
    }

    .info-item {
    gap: 15px;
    }

    .table-toolbar {

    flex-direction: column;
    align-items: stretch;
    }

    .filter-box select {
    width: 100%;
    }

}


        /* SIMASTA — shared header improvements (non-destructive) */
        :root { --sim-blue:#1c5b9e; --sim-navy:#173f70; --sim-border:#e2eaf2; --sim-text:#253b56; }
        .topbar { min-height:76px; height:auto; gap:18px; position:relative; z-index:35; }
        .topbar-title { color:#162f4e; font-size:20px; letter-spacing:-.35px; }
        .topbar-actions { display:flex; align-items:center; gap:13px; margin-left:auto; }
        .sim-account { position:relative; }
        .sim-account-toggle { display:flex;align-items:center;gap:11px;padding:6px 9px;border:1px solid transparent;background:transparent;border-radius:12px;cursor:pointer;color:var(--sim-text);font:inherit; }
        .sim-account-toggle:hover,.sim-account-toggle[aria-expanded="true"] {background:#f3f7fc;border-color:#e3edf7;}
        .sim-account-name {text-align:left;display:block;max-width:180px;min-width:0;}
        .sim-account-name strong {font-size:13px;font-weight:750;color:#1b3555;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
        .sim-account-name small {display:block;color:#73879e;font-size:11px;margin-top:3px;}
        .sim-avatar {width:42px;height:42px;border-radius:50%;flex:0 0 42px;background:#dbeafe;color:#1d5fa8;display:grid;place-items:center;font-weight:800;font-size:15px;overflow:hidden;border:2px solid #e9f2ff;}
        .sim-avatar img {width:100%;height:100%;object-fit:cover;display:block;}
        .sim-chevron {color:#8394a8;font-size:13px;}
        .sim-profile-menu {display:none;position:absolute;right:0;top:calc(100% + 10px);width:min(320px,calc(100vw - 28px));background:#fff;border:1px solid #e1eaf4;border-radius:15px;box-shadow:0 18px 44px rgba(24,52,90,.16);padding:8px;z-index:1000;}
        .sim-profile-menu.is-open {display:block;}
        .sim-profile-summary {padding:12px 12px 13px;border-bottom:1px solid #eaf0f6;margin-bottom:5px;}
        .sim-profile-summary strong {display:block;font-size:13px;color:#1d385b;overflow-wrap:anywhere;}
        .sim-profile-summary span {display:block;font-size:11px;color:#7d8fa3;margin-top:5px;overflow-wrap:anywhere;}
        .sim-profile-link {width:100%;display:flex;align-items:center;gap:10px;border:0;background:transparent;color:#425d77;text-align:left;text-decoration:none;font-size:12px;font-weight:650;padding:11px 12px;border-radius:9px;font-family:inherit;cursor:pointer;}
        .sim-profile-link:hover {background:#eef5ff;color:#1b5a9c;text-decoration:none;}
        .sim-profile-link.danger {color:#b94a53;}
        .sim-profile-link.danger:hover {background:#fff2f3;}
        .sim-profile-link i {width:17px;text-align:center;font-size:16px;}
        @media(max-width:650px) { .topbar{padding:12px 16px;min-height:67px;} .topbar-title{font-size:16px;} .topbar-actions{gap:5px;} .sim-account-toggle{gap:5px;padding:3px;} .sim-account-name,.sim-chevron{display:none;} .sim-avatar{width:37px;height:37px;flex-basis:37px;} .content{padding:18px 14px;} }

</style>

</head>

<body>

<!-- SIDEBAR -->

@yield('sidebar')

    <!-- MAIN -->

    <main class="main">


        <!-- TOPBAR: profil konsisten untuk akun Admin dan Mahasiswa -->
        @php
            $simUser = auth()->user();
            $simIsStudent = $simUser && $simUser->role === 'mahasiswa';
            $simDisplayName = $simUser?->name ?: 'Pengguna SIMASTA';
            $simInitial = mb_strtoupper(mb_substr($simDisplayName, 0, 1));
            $simMahasiswa = $simIsStudent ? $simUser->mahasiswa : null;
            // Kolom foto telah diperkenalkan pada profil mahasiswa; hanya dipakai jika filenya tersedia.
            $simPhotoPath = $simIsStudent ? data_get($simMahasiswa, 'foto') : null;
            $simPhotoPath = is_string($simPhotoPath) ? ltrim($simPhotoPath, '/') : null;
            $simPhotoUrl = null;
            if ($simPhotoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($simPhotoPath)) {
                $simPhotoUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($simPhotoPath);
            }
        @endphp
        <header class="topbar">
            <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            <div class="topbar-actions">
                {{-- Notifikasi pengajuan milik mahasiswa yang sedang login. --}}
                @if($simIsStudent && view()->exists('partials.lonceng'))
                    @include('partials.lonceng')
                @endif
                {{-- Lonceng Admin akan diaktifkan setelah rute dan sumber notifikasi Admin diaudit. --}}
                @auth
                    <div class="sim-account" id="simAccount">
                        <button type="button" class="sim-account-toggle" id="simAccountToggle"
                                aria-expanded="false" aria-controls="simProfileMenu"
                                aria-label="Buka menu profil pengguna">
                            <span class="sim-account-name">
                                <strong>{{ $simDisplayName }}</strong>
                                <small>@yield('user-role', 'SIMASTA')</small>
                            </span>
                            <span class="sim-avatar" aria-hidden="true">
                                @if($simPhotoUrl)
                                    <img src="{{ $simPhotoUrl }}" alt="" onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                                    <span style="display:none">{{ $simInitial }}</span>
                                @else
                                    {{ $simInitial }}
                                @endif
                            </span>
                            <i class="bi bi-chevron-down sim-chevron" aria-hidden="true"></i>
                        </button>
                        <div class="sim-profile-menu" id="simProfileMenu" role="menu">
                            <div class="sim-profile-summary">
                                <strong>{{ $simDisplayName }}</strong>
                                <span>{{ $simUser->email }}</span>
                            </div>
                            @if(\Illuminate\Support\Facades\Route::has('mahasiswa.profil') && $simIsStudent)
                                <a class="sim-profile-link" href="{{ route('mahasiswa.profil') }}" role="menuitem"><i class="bi bi-person-circle"></i> Profil saya</a>
                            @elseif(\Illuminate\Support\Facades\Route::has('profile.edit'))
                                <a class="sim-profile-link" href="{{ route('profile.edit') }}" role="menuitem"><i class="bi bi-person-circle"></i> Profil saya</a>
                            @endif
                            @if(\Illuminate\Support\Facades\Route::has('logout'))
                                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                                    @csrf
                                    <button type="submit" class="sim-profile-link danger" role="menuitem"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    <span class="avatar">@yield('user-avatar', 'S')</span>
                @endauth
            </div>
        </header>

        <!-- CONTENT -->

                <section class="content">

            @yield('content')

        </section>


    </main>
    <script>
    (function () {
        const root = document.getElementById('simAccount');
        const toggle = document.getElementById('simAccountToggle');
        const menu = document.getElementById('simProfileMenu');
        if (!root || !toggle || !menu) return;
        function setOpen(open) {
            menu.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        }
        toggle.addEventListener('click', () => setOpen(!menu.classList.contains('is-open')));
        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) setOpen(false);
        });
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && menu.classList.contains('is-open')) {
                setOpen(false);
                toggle.focus();
            }
        });
    })();
    </script>
</body>

</html>
