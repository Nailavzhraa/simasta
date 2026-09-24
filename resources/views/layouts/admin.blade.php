<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Admin - SIMASTA')
    </title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
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
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-logo {
            padding: 25px 20px;
            font-size: 28px;
            font-weight: bold;
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .sidebar-menu {
            list-style: none;
            padding: 15px 10px;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: block;
            padding: 13px 15px;
            color: #374151;
            text-decoration: none;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.2s;
        }

        .sidebar-menu a:hover {
            background: #eff6ff;
            color: #2563eb;
        }

        .logout-btn {
            width: 100%;
            border: none;
            background: transparent;
            text-align: left;
            padding: 13px 15px;
            color: #dc2626;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
        }

        .logout-btn:hover {
            background: #fef2f2;
        }

        /* MAIN */

        .main {
            margin-left: 250px;
            min-height: 100vh;
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

        .topbar-title {
            font-size: 20px;
            font-weight: bold;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-role {
            font-size: 14px;
            color: #374151;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
        }

        /* CONTENT */

        .content {
            padding: 30px;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .welcome p {
            color: #6b7280;
        }

        /* CARD */

        .card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
        }

        .card h2 {
            font-size: 21px;
            margin-bottom: 8px;
        }

        /* STATISTICS */

        .card-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .stat-card h3 {
            font-size: 15px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .number {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 8px;
        }

        .stat-card p {
            color: #6b7280;
            font-size: 14px;
        }

        /* TABLE */

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background: #f8fafc;
            text-align: left;
            padding: 14px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }

        table td {
            padding: 14px;
            font-size: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

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
    background-color: #2864e8;
    color: white;
    text-decoration: none;
    border-radius: 11px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    white-space: nowrap;
    box-sizing: border-box;
}

.btn-reset:hover {
    background-color: #d9dce1;
}

.btn-primary:hover {
    background-color: #1f55cc;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

        /* BADGE */

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

        /* INFO */

        .info-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            padding-bottom: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* EMPTY */

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #6b7280;
        }

        .empty-state h3 {
            margin-bottom: 8px;
            color: #374151;
        }

        /* FORM */

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* RESPONSIVE */

        @media (max-width: 900px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
            }

            .card-grid {
                grid-template-columns: repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
            }

            .content {
                padding: 20px;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .topbar {
                padding: 0 20px;
            }

        }

    </style>

</head>


<body>


<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="sidebar-logo">
        SIMASTA
    </div>

    <ul class="sidebar-menu">

        <li>
            <a href="/admin/dashboard">
                Dashboard
            </a>
        </li>

        <li>
            <a href="/admin/mahasiswa">
                Data Mahasiswa
            </a>
        </li>

        <li>
            <a href="/admin/pengajuan">
                Pengajuan Magang
            </a>
        </li>

        <li>
            <a href="/admin/pembimbing">
                Data Pembimbing
            </a>
        </li>

        <li>
            <a href="/admin/penempatan">
                Penempatan
            </a>
        </li>

        <li>
            <a href="/admin/logbook">
                Monitoring Logbook
            </a>
        </li>

        <li>
            <a href="/admin/penilaian">
                Penilaian
            </a>
        </li>

        <li>
            <a href="/admin/laporan">
                Laporan
            </a>
        </li>

        <li>

            <form method="POST" action="/logout">

                @csrf

                <button type="submit" class="logout-btn">
                    Logout
                </button>

            </form>

        </li>

    </ul>

</aside>


<!-- MAIN -->

<main class="main">


    <!-- TOPBAR -->

    <header class="topbar">

        <div class="topbar-title">

            @yield('page-title', 'Dashboard Admin')

        </div>


        <div class="user-info">

            <div class="user-role">

                <strong>
                    @yield('user-role', 'Admin')
                </strong>

            </div>

            <div class="avatar">

                @yield('user-avatar', 'A')

            </div>

        </div>

    </header>


    <!-- CONTENT -->

    <section class="content">

        @yield('content')

    </section>


</main>


</body>

</html>