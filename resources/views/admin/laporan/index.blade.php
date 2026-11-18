
@extends('layouts.admin')

@section('title', 'Laporan Magang - SIMASTA')
@section('page-title', 'Laporan')

@section('content')

@php
    $s = $statistik ?? [];

    $jumlahMahasiswa = $s['mahasiswa'] ?? 0;
    $jumlahPengajuan = $s['pengajuan'] ?? 0;
    $jumlahDiterima = $s['diterima'] ?? 0;
    $jumlahLogbook = $s['logbook'] ?? 0;
    $jumlahPenilaian = $s['penilaian'] ?? 0;
    $jumlahLaporan = $s['laporan_total'] ?? 0;
    $jumlahMenunggu = $s['laporan_menunggu'] ?? 0;
    $jumlahRevisi = $s['laporan_revisi'] ?? 0;
    $jumlahDisetujui = $s['laporan_disetujui'] ?? 0;
@endphp

<style>
    .lr-page,
    .lr-page button,
    .lr-page input,
    .lr-page select {
        font-family: Arial, sans-serif;
    }

    .lr-page {
        width: 100%;
        color: #1e293b;
        font-size: 12px;
    }

    .lr-page * {
        box-sizing: border-box;
    }

    .lr-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 24px;
        margin-bottom: 18px;
        border-radius: 15px;
        border: 1px solid #dbe7f3;
        background: linear-gradient(
            110deg,
            #ffffff 0%,
            #eef5ff 66%,
            #e8f7ef 100%
        );
    }

    .lr-eyebrow {
        color: #2563eb;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .7px;
        margin-bottom: 9px;
    }

    .lr-hero h1 {
        color: #163f71;
        font-size: 24px;
        margin: 0 0 8px;
    }

    .lr-hero p {
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
        max-width: 680px;
        margin: 0;
    }

    .lr-icon {
        flex-shrink: 0;
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        border-radius: 11px;
        background: #dbeafe;
        color: #2563eb;
    }

    .lr-icon svg,
    .lr-btn svg,
    .lr-icon-small svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .lr-icon.green {
        background: #dcfce7;
        color: #059669;
    }

    .lr-icon.orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .lr-icon.purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .lr-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 18px;
    }

    .lr-stat {
        display: flex;
        align-items: center;
        gap: 13px;
        min-width: 0;
        padding: 17px;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #fff;
    }

    .lr-stat-label {
        font-size: 11px;
        color: #64748b;
        margin-bottom: 6px;
        line-height: 1.5;
    }

    .lr-stat-value {
        font-size: 26px;
        font-weight: 700;
        color: #163f71;
    }

    .lr-section {
        padding: 21px;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
    }

    .lr-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 19px;
    }

    .lr-section-title h2 {
        margin: 0 0 6px;
        font-size: 16px;
        color: #163f71;
    }

    .lr-section-title p {
        margin: 0;
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.7;
    }

    .lr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 39px;
        padding: 10px 14px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        cursor: pointer;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .lr-btn:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .lr-btn.outline {
        border-color: #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .lr-btn.outline:hover {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .lr-btn.gray {
        border-color: #e2e8f0;
        background: white;
        color: #475569;
    }

    .lr-btn.gray:hover {
        background: #f1f5f9;
    }

    .lr-report-cards {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
    }

    .lr-report-card {
        padding: 16px;
        min-width: 0;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #fdfefe;
    }

    .lr-report-card h3 {
        color: #163f71;
        margin: 13px 0 7px;
        font-size: 13px;
    }

    .lr-report-card p {
        color: #64748b;
        margin: 0 0 13px;
        font-size: 11px;
        line-height: 1.65;
        min-height: 37px;
    }

    .lr-report-count {
        font-size: 20px;
        color: #163f71;
        font-weight: 700;
        margin-bottom: 12px;
    }

    .lr-report-count span {
        font-size: 10px;
        color: #94a3b8;
        font-weight: 400;
    }

    .lr-filter {
        display: grid;
        grid-template-columns: minmax(190px, 1fr) 160px 160px auto;
        align-items: end;
        gap: 10px;
        padding: 14px;
        margin-bottom: 18px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #f8fafc;
    }

    .lr-field label {
        display: block;
        font-size: 11px;
        color: #475569;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .lr-input,
    .lr-select {
        display: block;
        width: 100%;
        min-height: 39px;
        padding: 10px 11px;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        background: white;
        color: #334155;
        outline: none;
        font-size: 11px;
    }

    .lr-input:focus,
    .lr-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    }

    .lr-filter-actions {
        display: flex;
        gap: 7px;
        flex-wrap: wrap;
    }

    .lr-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .lr-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .lr-table th {
        padding: 13px 10px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 10px;
        letter-spacing: .3px;
        white-space: nowrap;
    }

    .lr-table td {
        padding: 15px 10px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 11px;
        color: #475569;
    }

    .lr-table tbody tr:hover {
        background: #fafcff;
    }

    .lr-student {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 170px;
    }

    .lr-avatar {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e8f0ff;
        color: #2563eb;
        font-weight: 700;
        font-size: 12px;
    }

    .lr-name {
        color: #163f71;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .lr-muted {
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .lr-report-title {
        color: #334155;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 5px;
        max-width: 190px;
        overflow-wrap: anywhere;
    }

    .lr-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .lr-badge::before {
        content: "";
        display: inline-block;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .lr-badge.wait {
        color: #b45309;
        background: #fff7ed;
    }

    .lr-badge.review {
        color: #1d4ed8;
        background: #eff6ff;
    }

    .lr-badge.revision {
        color: #dc2626;
        background: #fef2f2;
    }

    .lr-badge.done {
        color: #047857;
        background: #ecfdf5;
    }

    .lr-badge.empty {
        color: #64748b;
        background: #f1f5f9;
    }

    .lr-empty {
        text-align: center;
        padding: 42px 15px;
        color: #94a3b8;
        line-height: 1.8;
    }

    .lr-empty strong {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: 14px;
    }

    .lr-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 17px;
        color: #94a3b8;
        font-size: 11px;
    }

    .lr-alert {
        padding: 13px 15px;
        margin-bottom: 17px;
        border: 1px solid #a7f3d0;
        border-radius: 9px;
        background: #ecfdf5;
        color: #047857;
    }

    .lr-alert.error {
        border-color: #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    @media(max-width:1150px) {
        .lr-stats,
        .lr-report-cards {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .lr-filter {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width:650px) {
        .lr-stats,
        .lr-report-cards,
        .lr-filter {
            grid-template-columns: 1fr;
        }

        .lr-hero {
            padding: 17px;
        }

        .lr-hero h1 {
            font-size: 21px;
        }

        .lr-section {
            padding: 14px;
        }
    }
</style>

<div class="lr-page">

    <!-- HEADER -->
    <div class="lr-hero">
        <div>

            <h1>Pusat Laporan Magang</h1>

            <p>
                Monitoring dan rekapitulasi data mahasiswa,
                pengajuan, logbook, penilaian, serta
                laporan akhir kegiatan magang BPS Kota Batu.
            </p>
        </div>

        <div class="lr-icon">
            <svg viewBox="0 0 24 24">
                <path d="M3 3v18h18"/>
                <path d="m7 16 4-5 3 3 6-8"/>
            </svg>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="lr-alert">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="lr-alert error">
            <strong>Terjadi masalah:</strong>

            <ul style="margin:8px 0 0;padding-left:18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- STATISTIK -->
    <div class="lr-stats">

        <div class="lr-stat">
            <div class="lr-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                </svg>
            </div>

            <div>
                <div class="lr-stat-label">Total Mahasiswa</div>
                <div class="lr-stat-value">
                    {{ number_format($jumlahMahasiswa, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="lr-stat">
            <div class="lr-icon orange">
                <svg viewBox="0 0 24 24">
                    <rect x="4" y="3" width="16" height="18" rx="2"/>
                    <path d="M8 8h8M8 12h8M8 16h5"/>
                </svg>
            </div>

            <div>
                <div class="lr-stat-label">Total Pengajuan Magang</div>
                <div class="lr-stat-value">
                    {{ number_format($jumlahPengajuan, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="lr-stat">
            <div class="lr-icon green">
                <svg viewBox="0 0 24 24">
                    <path d="M5 4h14v16H5z"/>
                    <path d="M9 8h6M9 12h6M9 16h4"/>
                </svg>
            </div>

            <div>
                <div class="lr-stat-label">Total Logbook</div>
                <div class="lr-stat-value">
                    {{ number_format($jumlahLogbook, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="lr-stat">
            <div class="lr-icon purple">
                <svg viewBox="0 0 24 24">
                    <path d="M4 4h16v16H4z"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </div>

            <div>
                <div class="lr-stat-label">Laporan Akhir Disetujui</div>
                <div class="lr-stat-value">
                    {{ number_format($jumlahDisetujui, 0, ',', '.') }}
                </div>
            </div>
        </div>

    </div>

    <!-- REKAP MODUL -->
    <div class="lr-section">
        <div class="lr-section-head">
            <div class="lr-section-title">
                <h2>Ringkasan Sistem Magang</h2>
                <p>
                    Rekap otomatis dari modul-modul SIMASTA yang
                    terhubung dengan database.
                </p>
            </div>
        </div>

        <div class="lr-report-cards">

            <!-- DATA MAHASISWA -->
            <div class="lr-report-card">
                <div class="lr-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M2 21v-2a6 6 0 0 1 12 0v2"/>
                        <path d="M19 9v6M16 12h6"/>
                    </svg>
                </div>

                <h3>Data Mahasiswa</h3>

                <p>
                    Rekap akun mahasiswa yang terdaftar
                    pada sistem SIMASTA.
                </p>

                <div class="lr-report-count">
                    {{ number_format($jumlahMahasiswa, 0, ',', '.') }}
                    <span>Mahasiswa</span>
                </div>

                <a
                    href="{{ route('admin.mahasiswa.index') }}"
                    class="lr-btn outline"
                >
                    Lihat Data
                </a>
            </div>

            <!-- PENGAJUAN -->
            <div class="lr-report-card">
                <div class="lr-icon orange">
                    <svg viewBox="0 0 24 24">
                        <rect x="4" y="3" width="16" height="18" rx="2"/>
                        <path d="M8 8h8M8 12h8M8 16h5"/>
                    </svg>
                </div>

                <h3>Pengajuan Magang</h3>

                <p>
                    Rekap seluruh pengajuan dan mahasiswa
                    yang telah diterima magang.
                </p>

                <div class="lr-report-count">
                    {{ number_format($jumlahDiterima, 0, ',', '.') }}
                    <span>Mahasiswa Diterima</span>
                </div>

                <a
                    href="{{ route('admin.pengajuan.index') }}"
                    class="lr-btn outline"
                >
                    Lihat Pengajuan
                </a>
            </div>

            <!-- LOGBOOK -->
            <div class="lr-report-card">
                <div class="lr-icon green">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 4h14v16H5z"/>
                        <path d="M9 8h6M9 12h6M9 16h4"/>
                    </svg>
                </div>

                <h3>Logbook Mahasiswa</h3>

                <p>
                    Rekap kegiatan harian yang dicatat
                    mahasiswa selama pelaksanaan magang.
                </p>

                <div class="lr-report-count">
                    {{ number_format($jumlahLogbook, 0, ',', '.') }}
                    <span>Catatan Harian</span>
                </div>

                <a
                    href="{{ route('admin.logbook.index') }}"
                    class="lr-btn outline"
                >
                    Lihat Logbook
                </a>
            </div>

            <!-- PENILAIAN -->
            <div class="lr-report-card">
                <div class="lr-icon purple">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="5"/>
                        <path d="m8 13-2 8 6-3 6 3-2-8"/>
                    </svg>
                </div>

                <h3>Penilaian Magang</h3>

                <p>
                    Rekap dokumen penilaian universitas
                    yang sudah diselesaikan pihak BPS.
                </p>

                <div class="lr-report-count">
                    {{ number_format($jumlahPenilaian, 0, ',', '.') }}
                    <span>Penilaian Selesai</span>
                </div>

                <a
                    href="{{ route('admin.penilaian.index') }}"
                    class="lr-btn outline"
                >
                    Lihat Penilaian
                </a>
            </div>

        </div>
    </div>

    <!-- STATUS LAPORAN AKHIR -->
    <div class="lr-section">
        <div class="lr-section-head">
            <div class="lr-section-title">
                <h2>Monitoring Laporan Akhir</h2>
                <p>
                    Pantau laporan yang dikirim mahasiswa
                    dan progres pemeriksaan Admin BPS.
                </p>
            </div>

            <a
                href="{{ route('admin.laporan.export') }}"
                class="lr-btn"
            >
                <svg viewBox="0 0 24 24">
                    <path d="M12 3v12"/>
                    <path d="m7 10 5 5 5-5"/>
                    <path d="M4 17v4h16v-4"/>
                </svg>
                Ekspor CSV
            </a>
        </div>

        <div class="lr-stats">
            <div class="lr-stat">
                <div>
                    <div class="lr-stat-label">Laporan Masuk</div>
                    <div class="lr-stat-value">
                        {{ number_format($jumlahLaporan, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="lr-stat">
                <div>
                    <div class="lr-stat-label">Menunggu Pemeriksaan</div>
                    <div class="lr-stat-value">
                        {{ number_format($jumlahMenunggu, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="lr-stat">
                <div>
                    <div class="lr-stat-label">Perlu Revisi</div>
                    <div class="lr-stat-value">
                        {{ number_format($jumlahRevisi, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="lr-stat">
                <div>
                    <div class="lr-stat-label">Disetujui</div>
                    <div class="lr-stat-value">
                        {{ number_format($jumlahDisetujui, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- PENCARIAN DAN FILTER -->
        <form
            method="GET"
            action="{{ route('admin.laporan.index') }}"
            class="lr-filter"
        >
            <div class="lr-field">
                <label for="lr-search">Cari Mahasiswa</label>
                <input
                    id="lr-search"
                    class="lr-input"
                    type="search"
                    name="q"
                    placeholder="Nama, email, atau NIM..."
                    value="{{ request('q') }}"
                >
            </div>

            <div class="lr-field">
                <label for="lr-divisi">Divisi</label>
                <select
                    id="lr-divisi"
                    name="divisi_id"
                    class="lr-select"
                >
                    <option value="">Semua Divisi</option>

                    @foreach(($divisis ?? []) as $divisi)
                        <option
                            value="{{ $divisi->id }}"
                            @selected((string) request('divisi_id') === (string) $divisi->id)
                        >
                            {{ $divisi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lr-field">
                <label for="lr-status">Status Laporan</label>
                <select
                    id="lr-status"
                    name="status"
                    class="lr-select"
                >
                    <option value="">Semua Status</option>

                    <option
                        value="belum"
                        @selected(request('status') === 'belum')
                    >
                        Belum Kirim
                    </option>

                    <option
                        value="menunggu"
                        @selected(request('status') === 'menunggu')
                    >
                        Menunggu
                    </option>

                    <option
                        value="ditinjau"
                        @selected(request('status') === 'ditinjau')
                    >
                        Ditinjau
                    </option>

                    <option
                        value="revisi"
                        @selected(request('status') === 'revisi')
                    >
                        Perlu Revisi
                    </option>

                    <option
                        value="disetujui"
                        @selected(request('status') === 'disetujui')
                    >
                        Disetujui
                    </option>
                </select>
            </div>

            <div class="lr-filter-actions">
                <button type="submit" class="lr-btn">
                    Filter
                </button>

                <a
                    href="{{ route('admin.laporan.index') }}"
                    class="lr-btn gray"
                >
                    Reset
                </a>
            </div>
        </form>

        <!-- TABEL LAPORAN -->
        <div class="lr-table-wrap">
            <table class="lr-table">
                <thead>
                    <tr>
                        <th>NO.</th>
                        <th>MAHASISWA</th>
                        <th>DIVISI</th>
                        <th>JUDUL LAPORAN</th>
                        <th>TANGGAL KIRIM</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($daftar as $pengajuan)

                        @php
                            $mahasiswa = $pengajuan->user;
                            $laporan = $pengajuan->laporanMagang;

                            $nama = $mahasiswa?->name
                                ?? 'Mahasiswa tidak ditemukan';

                            $inisial = collect(
                                preg_split('/\s+/', trim($nama))
                            )
                                ->filter()
                                ->take(2)
                                ->map(
                                    fn ($kata) =>
                                    mb_strtoupper(mb_substr($kata, 0, 1))
                                )
                                ->implode('');
                        @endphp

                        <tr>
                            <td>
                                {{ ($daftar->firstItem() ?? 1) + $loop->index }}
                            </td>

                            <!-- MAHASISWA -->
                            <td>
                                <div class="lr-student">
                                    <div class="lr-avatar">
                                        {{ $inisial ?: 'M' }}
                                    </div>

                                    <div>
                                        <div class="lr-name">
                                            {{ $nama }}
                                        </div>

                                        <div class="lr-muted">
                                            {{ $mahasiswa?->mahasiswa?->nim
                                                ?: ($mahasiswa?->email ?? '-') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- DIVISI -->
                            <td>
                                {{ $pengajuan->divisi?->nama ?? '-' }}
                            </td>

                            <!-- JUDUL LAPORAN -->
                            <td>
                                @if($laporan)
                                    <div class="lr-report-title">
                                        {{ $laporan->judul }}
                                    </div>

                                    <div class="lr-muted">
                                        {{ $laporan->file_nama
                                            ?: ($laporan->file_url
                                                ? 'Tautan Google Drive'
                                                : 'Tanpa lampiran') }}
                                    </div>
                                @else
                                    <span class="lr-muted">
                                        Belum ada laporan
                                    </span>
                                @endif
                            </td>

                            <!-- TANGGAL -->
                            <td>
                                {{ $laporan?->dikirim_at?->format('d/m/Y') ?? '-' }}

                                @if($laporan?->dikirim_at)
                                    <div class="lr-muted">
                                        {{ $laporan->dikirim_at->format('H:i') }}
                                    </div>
                                @endif
                            </td>

                            <!-- STATUS -->
                            <td>
                                @if(!$laporan)
                                    <span class="lr-badge empty">
                                        Belum Dikirim
                                    </span>
                                @elseif($laporan->status === 'menunggu')
                                    <span class="lr-badge wait">
                                        Menunggu
                                    </span>
                                @elseif($laporan->status === 'ditinjau')
                                    <span class="lr-badge review">
                                        Ditinjau
                                    </span>
                                @elseif($laporan->status === 'revisi')
                                    <span class="lr-badge revision">
                                        Perlu Revisi
                                    </span>
                                @elseif($laporan->status === 'disetujui')
                                    <span class="lr-badge done">
                                        Disetujui
                                    </span>
                                @else
                                    <span class="lr-badge empty">
                                        {{ $laporan->status }}
                                    </span>
                                @endif
                            </td>

                            <!-- AKSI -->
                            <td>
                                @if($laporan)
                                    <a
                                        href="{{ route('admin.laporan.show', $laporan->id) }}"
                                        class="lr-btn outline"
                                    >
                                        Periksa
                                    </a>
                                @else
                                    <span class="lr-muted">
                                        Menunggu mahasiswa
                                    </span>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="lr-empty">
                                    <strong>Belum Ada Data Laporan</strong>

                                    Tidak ada mahasiswa dengan
                                    pengajuan diterima yang sesuai
                                    dengan filter.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        @if($daftar->total() > 0)
            <div class="lr-footer">
                <span>
                    Menampilkan {{ $daftar->firstItem() }}
                    - {{ $daftar->lastItem() }}
                    dari {{ $daftar->total() }} data
                </span>

                @if($daftar->hasPages())
                    {{ $daftar->links() }}
                @endif
            </div>
        @endif
    </div>

    <!-- CATATAN SISTEM -->
    <div class="lr-section">
        <div class="lr-section-head" style="margin-bottom:0">
            <div class="lr-section-title">
                <h2>Informasi Pengelolaan Laporan</h2>
                <p>
                    Laporan akhir berasal dari dokumen yang
                    dikirim mahasiswa melalui akun SIMASTA.
                    Admin dapat memeriksa, meminta revisi,
                    atau menyetujui laporan.

                    Rekap Penilaian dihitung dari jumlah dokumen
                    penilaian yang sudah selesai diproses BPS,
                    bukan dari skor numerik.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection
