
@extends('layouts.admin')

@section('title', 'Penilaian & Sertifikat - SIMASTA')
@section('page-title', 'Penilaian & Sertifikat')

@section('content')
@php
    $tabAktif = in_array(
        $tab ?? 'penilaian',
        ['penilaian', 'sertifikat'],
        true
    )
        ? ($tab ?? 'penilaian')
        : 'penilaian';
@endphp

<style>
    .ps-page {
        font-family: Arial, sans-serif;
        color: #1e293b;
        font-size: 12px;
    }

    .ps-page *,
    .ps-modal * {
        box-sizing: border-box;
    }

    .ps-hero {
        padding: 24px;
        margin-bottom: 18px;
        border: 1px solid #dce8f4;
        border-radius: 15px;
        background: linear-gradient(110deg, #fff 0%, #edf5ff 65%, #e9f8f1 100%);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .ps-eyebrow {
        color: #2563eb;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .7px;
        margin-bottom: 8px;
    }

    .ps-hero h1 {
        font-size: 24px;
        color: #163f71;
        margin: 0 0 8px;
    }

    .ps-hero p {
        margin: 0;
        font-size: 12px;
        line-height: 1.7;
        color: #64748b;
    }

    .ps-symbol {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        background: #dbeafe;
        color: #2563eb;
        border-radius: 12px;
    }

    .ps-icon {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .ps-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 17px;
    }

    .ps-tab {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 38px;
        padding: 9px 17px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #fff;
        color: #64748b;
        font-weight: 700;
        font-size: 12px;
        text-decoration: none;
    }

    .ps-tab:hover {
        color: #2563eb;
        border-color: #bfdbfe;
    }

    .ps-tab.active {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #93c5fd;
    }

    .ps-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 18px;
    }

    .ps-stat {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
        padding: 17px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
    }

    .ps-stat-icon {
        display: grid;
        place-items: center;
        width: 42px;
        height: 42px;
        flex-shrink: 0;
        border-radius: 10px;
    }

    .ps-blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .ps-orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .ps-green {
        background: #dcfce7;
        color: #059669;
    }

    .ps-purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .ps-stat-label {
        color: #64748b;
        font-size: 11px;
        line-height: 1.45;
        margin-bottom: 5px;
    }

    .ps-stat-value {
        font-size: 25px;
        font-weight: 700;
        color: #163f71;
    }

    .ps-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px;
        margin-bottom: 18px;
    }

    .ps-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 17px;
    }

    .ps-panel-head h2 {
        color: #163f71;
        font-size: 16px;
        margin: 0 0 5px;
    }

    .ps-panel-head p {
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.6;
        margin: 0;
    }

    .ps-filter {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 175px auto;
        align-items: end;
        gap: 10px;
        padding: 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 17px;
    }

    .ps-field label {
        display: block;
        color: #475569;
        font-size: 11px;
        font-weight: 700;
        margin-bottom: 7px;
    }

    .ps-input,
    .ps-select,
    .ps-textarea {
        width: 100%;
        min-height: 40px;
        padding: 10px 12px;
        border-radius: 9px;
        border: 1px solid #dbe3ee;
        outline: none;
        background: #fff;
        color: #334155;
        font: 12px Arial, sans-serif;
    }

    .ps-textarea {
        min-height: 90px;
        resize: vertical;
        line-height: 1.7;
    }

    .ps-input:focus,
    .ps-select:focus,
    .ps-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
    }

    .ps-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .ps-btn {
        min-height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 9px 13px;
        border-radius: 8px;
        border: 1px solid transparent;
        background: #2563eb;
        color: #fff;
        text-decoration: none;
        cursor: pointer;
        font: 700 11px Arial, sans-serif;
        white-space: nowrap;
    }

    .ps-btn:hover {
        background: #1d4ed8;
        color: #fff;
    }

    .ps-btn-light {
        background: #fff;
        color: #475569;
        border-color: #dbe3ee;
    }

    .ps-btn-light:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .ps-btn-outline {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #bfdbfe;
    }

    .ps-btn-outline:hover {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .ps-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .ps-table-wrap {
        overflow-x: auto;
        width: 100%;
    }

    .ps-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .ps-table th {
        padding: 13px 11px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 10px;
        letter-spacing: .25px;
        white-space: nowrap;
    }

    .ps-table td {
        padding: 14px 11px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 12px;
        color: #475569;
    }

    .ps-table tbody tr:hover {
        background: #fafcff;
    }

    .ps-student {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 160px;
    }

    .ps-avatar {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #e8f0ff;
        color: #2563eb;
        font-weight: 700;
    }

    .ps-name {
        color: #163f71;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .ps-sub {
        font-size: 11px;
        color: #94a3b8;
        overflow-wrap: anywhere;
    }

    .ps-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 9px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ps-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        background: currentColor;
        border-radius: 50%;
    }

    .ps-wait {
        color: #b45309;
        background: #fff7ed;
    }

    .ps-done {
        color: #047857;
        background: #ecfdf5;
    }

    .ps-empty-status {
        color: #64748b;
        background: #f1f5f9;
    }

    .ps-link {
        display: inline-block;
        color: #2563eb;
        text-decoration: none;
        font-size: 11px;
        font-weight: 700;
    }

    .ps-link:hover {
        text-decoration: underline;
    }

    .ps-date {
        margin-top: 6px;
        color: #94a3b8;
        font-size: 10px;
    }

    .ps-empty {
        padding: 45px 16px;
        text-align: center;
        color: #94a3b8;
        line-height: 1.8;
    }

    .ps-empty strong {
        display: block;
        color: #334155;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .ps-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 15px;
        font-size: 11px;
        color: #94a3b8;
    }

    .ps-alert {
        padding: 12px 14px;
        margin-bottom: 16px;
        border: 1px solid #a7f3d0;
        background: #ecfdf5;
        color: #047857;
        border-radius: 9px;
    }

    .ps-alert-error {
        border-color: #fecaca;
        background: #fef2f2;
        color: #b91c1c;
    }

    /* MODAL PENILAIAN */
    .ps-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, .58);
        font-family: Arial, sans-serif;
    }

    .ps-modal.open {
        display: flex;
    }

    .ps-modal-card {
        width: 100%;
        max-width: 590px;
        max-height: calc(100vh - 36px);
        overflow-y: auto;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 20px 65px rgba(15, 23, 42, .2);
    }

    .ps-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .ps-modal-head h3 {
        margin: 0 0 5px;
        font-size: 18px;
        color: #163f71;
    }

    .ps-modal-head p {
        margin: 0;
        font-size: 12px;
        color: #64748b;
    }

    .ps-close {
        flex-shrink: 0;
        width: 34px;
        height: 34px;
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 9px;
        cursor: pointer;
        color: #64748b;
        font-size: 22px;
    }

    .ps-modal-body {
        padding: 20px;
    }

    .ps-modal .ps-field {
        margin-bottom: 17px;
    }

    .ps-hint {
        font-size: 11px;
        line-height: 1.6;
        color: #94a3b8;
        margin-top: 7px;
    }

    .ps-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 9px;
        padding-top: 15px;
        border-top: 1px solid #e2e8f0;
    }

    @media (max-width: 1100px) {
        .ps-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .ps-hero {
            padding: 18px;
        }

        .ps-hero h1 {
            font-size: 21px;
        }

        .ps-stats,
        .ps-filter {
            grid-template-columns: 1fr;
        }

        .ps-panel {
            padding: 14px;
        }

        .ps-stat {
            padding: 14px;
        }
    }
</style>

<div class="ps-page">

    <!-- HEADER -->
    <div class="ps-hero">
        <div>

            <h1>Penilaian & Sertifikat</h1>
            <p>
                Kelola dokumen penilaian dari universitas,
                kirim hasil pengisian BPS, dan pantau penerbitan
                sertifikat mahasiswa magang.
            </p>
        </div>

        <div class="ps-symbol">
            <svg class="ps-icon" viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="5"/>
                <path d="m8 13-2 8 6-3 6 3-2-8"/>
            </svg>
        </div>
    </div>

    <!-- TABS -->
    <div class="ps-tabs">
        <a
            class="ps-tab {{ $tabAktif === 'penilaian' ? 'active' : '' }}"
            href="{{ route('admin.penilaian.index', array_merge(request()->except('page'), ['tab' => 'penilaian'])) }}"
        >
            Penilaian
        </a>

        <a
            class="ps-tab {{ $tabAktif === 'sertifikat' ? 'active' : '' }}"
            href="{{ route('admin.penilaian.index', array_merge(request()->except('page'), ['tab' => 'sertifikat'])) }}"
        >
            Sertifikat
        </a>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="ps-alert">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="ps-alert ps-alert-error">
            <strong>Periksa data berikut:</strong>
            <ul style="margin:7px 0 0; padding-left:18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- STATISTIK -->
    <div class="ps-stats">
        <div class="ps-stat">
            <div class="ps-stat-icon ps-blue">
                <svg class="ps-icon" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                </svg>
            </div>
            <div>
                <div class="ps-stat-label">Mahasiswa Diterima</div>
                <div class="ps-stat-value">
                    {{ number_format($statistik['mahasiswa'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="ps-stat">
            <div class="ps-stat-icon ps-orange">
                <svg class="ps-icon" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>
            <div>
                <div class="ps-stat-label">Menunggu Penilaian BPS</div>
                <div class="ps-stat-value">
                    {{ number_format($statistik['menunggu'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="ps-stat">
            <div class="ps-stat-icon ps-green">
                <svg class="ps-icon" viewBox="0 0 24 24">
                    <path d="M4 4h16v16H4z"/>
                    <path d="m8 12 3 3 5-6"/>
                </svg>
            </div>
            <div>
                <div class="ps-stat-label">Penilaian Selesai</div>
                <div class="ps-stat-value">
                    {{ number_format($statistik['selesai'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="ps-stat">
            <div class="ps-stat-icon ps-purple">
                <svg class="ps-icon" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="5"/>
                    <path d="m8 13-2 8 6-3 6 3-2-8"/>
                </svg>
            </div>
            <div>
                <div class="ps-stat-label">Sertifikat Terbit</div>
                <div class="ps-stat-value">
                    {{ number_format($statistik['sertifikat'] ?? 0, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

    <!-- DAFTAR -->
    <div class="ps-panel">
        <div class="ps-panel-head">
            <div>
                <h2>
                    {{ $tabAktif === 'penilaian'
                        ? 'Daftar Penilaian Mahasiswa'
                        : 'Daftar Sertifikat Mahasiswa' }}
                </h2>
                <p>
                    {{ $tabAktif === 'penilaian'
                        ? 'Pantau template kampus dan pengiriman hasil penilaian BPS.'
                        : 'Pantau sertifikat yang sudah diterbitkan dan yang masih menunggu.' }}
                </p>
            </div>

            @if($tabAktif === 'sertifikat')
                <span class="ps-badge ps-empty-status">
                    Penerbitan melalui modul sertifikat
                </span>
            @else
                <span class="ps-badge ps-done">
                    Terhubung Database
                </span>
            @endif
        </div>

        <!-- FILTER -->
        <form
            method="GET"
            action="{{ route('admin.penilaian.index') }}"
            class="ps-filter"
        >
            <input type="hidden" name="tab" value="{{ $tabAktif }}">

            <div class="ps-field">
                <label for="ps-search">Cari Mahasiswa</label>
                <input
                    id="ps-search"
                    type="search"
                    name="q"
                    class="ps-input"
                    placeholder="Nama, email, atau NIM mahasiswa..."
                    value="{{ request('q') }}"
                >
            </div>

            <div class="ps-field">
                <label for="ps-status">Status Penilaian</label>
                <select
                    id="ps-status"
                    name="status"
                    class="ps-select"
                >
                    <option value="">Semua Status</option>
                    <option value="belum" @selected(request('status') === 'belum')>
                        Belum Kirim Template
                    </option>
                    <option value="menunggu" @selected(request('status') === 'menunggu')>
                        Menunggu BPS
                    </option>
                    <option value="selesai" @selected(request('status') === 'selesai')>
                        Selesai
                    </option>
                </select>
            </div>

            <div class="ps-buttons">
                <button type="submit" class="ps-btn">Cari / Filter</button>
                <a
                    class="ps-btn ps-btn-light"
                    href="{{ route('admin.penilaian.index', ['tab' => $tabAktif]) }}"
                >
                    Reset
                </a>
            </div>
        </form>

        <!-- TABEL -->
        <div class="ps-table-wrap">
            <table class="ps-table">
                @if($tabAktif === 'penilaian')
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>MAHASISWA</th>
                            <th>DIVISI</th>
                            <th>TEMPLATE KAMPUS</th>
                            <th>HASIL PENILAIAN BPS</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                @else
                    <thead>
                        <tr>
                            <th>NO.</th>
                            <th>MAHASISWA</th>
                            <th>DIVISI</th>
                            <th>NOMOR SERTIFIKAT</th>
                            <th>TANGGAL TERBIT</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                @endif

                <tbody>
                    @forelse($daftar as $pengajuan)
                        @php
                            $mahasiswa = $pengajuan->user;
                            $berkas = $pengajuan->penilaianBerkas;
                            $sertifikat = $pengajuan->sertifikat;
                            $nama = $mahasiswa?->name ?? 'Mahasiswa tidak ditemukan';

                            $inisial = collect(preg_split('/\s+/', trim($nama)))
                                ->filter()
                                ->take(2)
                                ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))
                                ->implode('');

                            $adaTemplate = $berkas &&
                                ($berkas->template_path || $berkas->template_url);
                        @endphp

                        <tr>
                            <td>
                                {{ ($daftar->firstItem() ?? 1) + $loop->index }}
                            </td>

                            <td>
                                <div class="ps-student">
                                    <div class="ps-avatar">
                                        {{ $inisial ?: 'M' }}
                                    </div>
                                    <div>
                                        <div class="ps-name">{{ $nama }}</div>
                                        <div class="ps-sub">
                                            {{ $mahasiswa?->mahasiswa?->nim
                                                ?: ($mahasiswa?->email ?? '-') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                {{ $pengajuan->divisi?->nama ?? 'Belum ditentukan' }}
                            </td>

                            @if($tabAktif === 'penilaian')
                                <!-- TEMPLATE KAMPUS -->
                                <td>
                                    @if($berkas?->template_path)
                                        <a
                                            class="ps-link"
                                            href="{{ route('admin.penilaian.template.unduh', $berkas->id) }}"
                                        >
                                            Unduh Template
                                        </a>
                                    @elseif($berkas?->template_url)
                                        <a
                                            class="ps-link"
                                            href="{{ $berkas->template_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            Buka Link Template
                                        </a>
                                    @else
                                        <span class="ps-sub">Belum dikirim</span>
                                    @endif

                                    @if($berkas?->template_dikirim_at)
                                        <div class="ps-date">
                                            {{ $berkas->template_dikirim_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- HASIL BPS -->
                                <td>
                                    @if($berkas?->hasil_path)
                                        <span class="ps-badge ps-done">
                                            File telah dikirim
                                        </span>
                                    @elseif($berkas?->hasil_url)
                                        <a
                                            class="ps-link"
                                            href="{{ $berkas->hasil_url }}"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            Buka Hasil BPS
                                        </a>
                                    @else
                                        <span class="ps-sub">Belum tersedia</span>
                                    @endif

                                    @if($berkas?->hasil_dikirim_at)
                                        <div class="ps-date">
                                            {{ $berkas->hasil_dikirim_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </td>

                                <!-- STATUS PENILAIAN -->
                                <td>
                                    @if($berkas?->status === 'selesai')
                                        <span class="ps-badge ps-done">
                                            Selesai
                                        </span>
                                    @elseif($adaTemplate)
                                        <span class="ps-badge ps-wait">
                                            Menunggu BPS
                                        </span>
                                    @else
                                        <span class="ps-badge ps-empty-status">
                                            Menunggu Mahasiswa
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI -->
                                <td>
                                    <button
                                        type="button"
                                        class="ps-btn ps-btn-outline ps-open-modal"
                                        data-action="{{ route('admin.penilaian.hasil.kirim', $pengajuan->id) }}"
                                        data-mahasiswa="{{ $nama }}"
                                        @disabled(!$adaTemplate)
                                    >
                                        {{ $berkas?->status === 'selesai'
                                            ? 'Kirim Ulang'
                                            : 'Kirim Hasil' }}
                                    </button>
                                </td>
                            @else
                                <!-- NOMOR SERTIFIKAT -->
                                <td>
                                    {{ $sertifikat?->nomor_sertifikat ?? '-' }}
                                </td>

                                <!-- TANGGAL TERBIT -->
                                <td>
                                    {{ $sertifikat?->tanggal_terbit?->format('d/m/Y') ?? '-' }}
                                </td>

                                <!-- STATUS -->
                                <td>
                                    @if($sertifikat?->status === 'terbit')
                                        <span class="ps-badge ps-done">
                                            Terbit
                                        </span>
                                    @elseif($sertifikat)
                                        <span class="ps-badge ps-wait">
                                            Draft
                                        </span>
                                    @else
                                        <span class="ps-badge ps-empty-status">
                                            Belum Terbit
                                        </span>
                                    @endif
                                </td>

                                <!-- AKSI SERTIFIKAT -->
                                <td>
                                    <span class="ps-sub">
                                        {{ $sertifikat?->status === 'terbit'
                                            ? 'Dokumen diterbitkan'
                                            : 'Menunggu penerbitan' }}
                                    </span>
                                </td>
                            @endif
                        </tr>

                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="ps-empty">
                                    <strong>Belum Ada Data</strong>
                                    Belum ada mahasiswa diterima yang sesuai
                                    dengan pencarian atau filter.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($daftar->total() > 0)
            <div class="ps-footer">
                <span>
                    Menampilkan {{ $daftar->firstItem() }}
                    – {{ $daftar->lastItem() }}
                    dari {{ $daftar->total() }} data
                </span>

                @if($daftar->hasPages())
                    <div>
                        {{ $daftar->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- INFORMASI -->
        @if($tabAktif === 'penilaian')
        @else
            <div class="ps-p
            anel-head" style="margin-bottom:0">
                <div>
                    <h2>Penerbitan Sertifikat BPS</h2>
                    <p>
                        Sertifikat akan diterbitkan menggunakan template
                        BPS Kota Batu dan dikirim ke akun mahasiswa.
                        Editor template dan penerbitan sertifikat
                        akan diaktifkan pada tahap berikutnya.
                    </p>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- MODAL PENGIRIMAN HASIL -->
<div
    id="ps-result-modal"
    class="ps-modal"
    role="dialog"
    aria-modal="true"
    aria-labelledby="ps-modal-title"
    aria-hidden="true"
>
    <div class="ps-modal-card">
        <div class="ps-modal-head">
            <div>
                <h3 id="ps-modal-title">Kirim Hasil Penilaian</h3>
                <p id="ps-modal-student">Mahasiswa</p>
            </div>

            <button
                type="button"
                class="ps-close ps-close-modal"
                aria-label="Tutup modal"
            >
                &times;
            </button>
        </div>

        <form
            id="ps-result-form"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="ps-modal-body">
                <div class="ps-field">
                    <label for="ps-hasil-file">
                        Upload Hasil Penilaian BPS
                    </label>
                    <input
                        id="ps-hasil-file"
                        class="ps-input"
                        type="file"
                        name="hasil_file"
                        accept=".pdf,.doc,.docx,.xls,.xlsx"
                    >
                    <div class="ps-hint">
                        PDF, Word, atau Excel. Maksimal 10 MB.
                    </div>
                </div>

                <div class="ps-field">
                    <label for="ps-hasil-url">
                        Tautan Google Drive Hasil Penilaian
                    </label>
                    <input
                        id="ps-hasil-url"
                        class="ps-input"
                        type="url"
                        name="hasil_url"
                        placeholder="https://drive.google.com/..."
                    >
                    <div class="ps-hint">
                        Isi minimal salah satu: upload file atau tautan.
                        Pastikan mahasiswa memiliki akses ke tautan Drive.
                    </div>
                </div>

                <div class="ps-field">
                    <label for="ps-catatan-admin">
                        Catatan Admin (Opsional)
                    </label>
                    <textarea
                        id="ps-catatan-admin"
                        class="ps-textarea"
                        name="catatan_admin"
                        maxlength="2000"
                        placeholder="Contoh: Form penilaian sudah diisi dan ditandatangani pihak BPS."
                    ></textarea>
                </div>

                <div class="ps-modal-actions">
                    <button
                        type="button"
                        class="ps-btn ps-btn-light ps-close-modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="ps-btn"
                    >
                        Kirim ke Mahasiswa
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('ps-result-modal');
    const form = document.getElementById('ps-result-form');
    const student = document.getElementById('ps-modal-student');
    const fileInput = document.getElementById('ps-hasil-file');
    const urlInput = document.getElementById('ps-hasil-url');

    if (!modal || !form || !student) {
        return;
    }

    let previousFocus = null;

    function closeModal() {
        modal.classList.remove('open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';

        if (previousFocus) {
            previousFocus.focus();
        }
    }

    document.querySelectorAll('.ps-open-modal').forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.disabled) return;

            previousFocus = button;
            form.reset();

            form.action = button.dataset.action;
            student.textContent = button.dataset.mahasiswa;

            modal.classList.add('open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            fileInput.focus();
        });
    });

    document.querySelectorAll('.ps-close-modal').forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && modal.classList.contains('open')) {
            closeModal();
        }
    });

    form.addEventListener('submit', function (event) {
        if (!fileInput.files.length && !urlInput.value.trim()) {
            event.preventDefault();
            alert('Unggah file hasil penilaian atau masukkan tautan Google Drive.');
            return;
        }
    });
});
</script>
@endsection
