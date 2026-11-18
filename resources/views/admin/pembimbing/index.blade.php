
@extends('layouts.admin')

@section('title', 'Data Pembimbing - SIMASTA')
@section('page-title', 'Data Pembimbing')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')

@php
    $stat = $statistik ?? [
        'total' => $pembimbings->total(),
        'aktif' => 0,
        'divisi' => 0,
        'mahasiswa' => 0,
    ];

    $daftarDivisi = $divisis
        ?? \App\Models\Divisi::orderBy('nama')->get();
@endphp

<style>
    .pb-page {
        font-family: Arial, sans-serif;
        color: #1e293b;
    }

    .pb-page * {
        box-sizing: border-box;
    }

    .pb-hero {
        position: relative;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        padding: 28px;
        margin-bottom: 20px;
        background: linear-gradient(
            115deg,
            #ffffff 0%,
            #eef6ff 65%,
            #eaf8f0 100%
        );
        border: 1px solid #dce8f4;
        border-radius: 16px;
    }

    .pb-eyebrow {
        color: #1d5fa8;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .8px;
        margin-bottom: 10px;
    }

    .pb-hero h1 {
        color: #163f71;
        font-size: 26px;
        font-weight: 700;
        margin: 0 0 9px;
    }

    .pb-hero p {
        max-width: 600px;
        margin: 0;
        color: #64748b;
        font-size: 13px;
        line-height: 1.7;
    }

    .pb-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        border: 1px solid transparent;
        border-radius: 9px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        transition: .2s ease;
    }

    .pb-button svg {
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .pb-button-primary {
        background: #2563eb;
        color: #ffffff;
    }

    .pb-button-primary:hover {
        background: #1d4ed8;
        color: #ffffff;
    }

    .pb-button-light {
        background: #ffffff;
        color: #475569;
        border-color: #e2e8f0;
    }

    .pb-button-light:hover {
        background: #f8fafc;
    }

    .pb-button-edit {
        color: #2563eb;
        background: #eff6ff;
        border-color: #dbeafe;
    }

    .pb-button-edit:hover {
        background: #dbeafe;
    }

    .pb-button-delete {
        color: #dc2626;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .pb-button-delete:hover {
        background: #fee2e2;
    }

    .pb-alert {
        padding: 14px 17px;
        border-radius: 10px;
        margin-bottom: 18px;
        font-size: 13px;
        line-height: 1.6;
    }

    .pb-alert-success {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
    }

    .pb-alert-error {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #b91c1c;
    }

    /* STATISTIK */

    .pb-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 15px;
        margin-bottom: 22px;
    }

    .pb-stat-card {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 21px 18px;
        min-width: 0;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        transition: .2s ease;
    }

    .pb-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(15,23,42,.06);
    }

    .pb-stat-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border-radius: 12px;
    }

    .pb-stat-icon svg {
        width: 23px;
        height: 23px;
        stroke-width: 1.8;
    }

    .pb-stat-blue {
        background: #e8f0ff;
        color: #2563eb;
    }

    .pb-stat-green {
        background: #dff8eb;
        color: #059669;
    }

    .pb-stat-orange {
        background: #fff1e1;
        color: #ea580c;
    }

    .pb-stat-purple {
        background: #f0eaff;
        color: #7c3aed;
    }

    .pb-stat-label {
        font-size: 12px;
        color: #64748b;
        margin-bottom: 6px;
    }

    .pb-stat-number {
        color: #1e293b;
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
    }

    /* PANEL DAFTAR */

    .pb-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        overflow: hidden;
    }

    .pb-panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        padding: 24px 24px 18px;
    }

    .pb-panel-title {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .pb-panel-title > svg {
        width: 20px;
        height: 20px;
        color: #2563eb;
    }

    .pb-panel-head h2 {
        color: #163f71;
        font-size: 17px;
        margin: 0 0 6px;
    }

    .pb-panel-head p {
        color: #94a3b8;
        font-size: 12px;
        margin: 0;
    }

    .pb-count {
        padding: 7px 12px;
        border-radius: 20px;
        background: #eff6ff;
        color: #2563eb;
        font-size: 11px;
        font-weight: 700;
    }

    /* FILTER */

    .pb-filter {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        align-items: center;
        padding: 0 24px 20px;
    }

    .pb-search {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
        min-width: 200px;
        height: 41px;
        padding: 0 13px;
        border: 1px solid #dbe3ee;
        border-radius: 9px;
        background: #f8fafc;
    }

    .pb-search svg {
        width: 17px;
        height: 17px;
        color: #94a3b8;
        flex-shrink: 0;
    }

    .pb-search input {
        width: 100%;
        min-width: 0;
        border: none;
        outline: none;
        background: transparent;
        font-family: inherit;
        font-size: 12px;
        color: #334155;
    }

    .pb-filter select {
        height: 41px;
        min-width: 175px;
        padding: 0 13px;
        border: 1px solid #dbe3ee;
        border-radius: 9px;
        background: #f8fafc;
        color: #334155;
        font-family: inherit;
        font-size: 12px;
        outline: none;
    }

    .pb-search:focus-within,
    .pb-filter select:focus {
        border-color: #3b82f6;
        background: #ffffff;
    }

    /* TABLE */

    .pb-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .pb-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
    }

    .pb-table th {
        padding: 15px 21px;
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .pb-table td {
        padding: 18px 21px;
        color: #475569;
        font-size: 12px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .pb-table tbody tr:hover {
        background: #f8fbff;
    }

    .pb-table tbody tr:last-child td {
        border-bottom: none;
    }

    .pb-person {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .pb-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 41px;
        height: 41px;
        flex-shrink: 0;
        border-radius: 11px;
        color: #1d5fa8;
        background: #e8f0ff;
        font-size: 13px;
        font-weight: 700;
    }

    .pb-name {
        margin-bottom: 5px;
        color: #1e293b;
        font-size: 13px;
        font-weight: 700;
    }

    .pb-sub {
        font-size: 11px;
        color: #94a3b8;
    }

    .pb-divisi {
        display: inline-block;
        padding: 6px 10px;
        border-radius: 7px;
        background: #ecfdf5;
        color: #047857;
        font-size: 11px;
        font-weight: 600;
    }

    .pb-divisi-empty {
        background: #f1f5f9;
        color: #64748b;
    }

    .pb-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 7px;
    }

    .pb-actions form {
        margin: 0;
    }

    .pb-empty {
        padding: 45px 20px !important;
        text-align: center;
        color: #94a3b8;
    }

    .pb-empty strong {
        display: block;
        margin-bottom: 6px;
        color: #334155;
        font-size: 14px;
    }

    .pb-pagination {
        padding: 18px 24px;
        border-top: 1px solid #f1f5f9;
    }

    @media(max-width: 1100px) {
        .pb-stats {
            grid-template-columns: repeat(2, minmax(0,1fr));
        }
    }

    @media(max-width: 650px) {
        .pb-hero {
            padding: 20px;
        }

        .pb-hero h1 {
            font-size: 22px;
        }

        .pb-stats {
            grid-template-columns: 1fr;
        }

        .pb-filter {
            flex-direction: column;
            align-items: stretch;
        }

        .pb-filter select,
        .pb-filter .pb-button {
            width: 100%;
        }
    }
</style>

<div class="pb-page">

    <!-- HEADER -->
    <div class="pb-hero">

        <div>

            <h1>Data Pembimbing</h1>

            <p>
                Kelola pembimbing lapangan, atur divisi,
                dan pantau penugasan mahasiswa magang
                di BPS Kota Batu.
            </p>
        </div>

        <a
            href="{{ route('admin.pembimbing.create') }}"
            class="pb-button pb-button-primary"
        >
            <svg xmlns="http://www.w3.org/2000/svg"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Tambah Pembimbing
        </a>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="pb-alert pb-alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="pb-alert pb-alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="pb-alert pb-alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- STATISTIK -->
    <div class="pb-stats">

        <!-- TOTAL -->
        <div class="pb-stat-card">
            <div class="pb-stat-icon pb-stat-blue">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>

            <div>
                <div class="pb-stat-label">
                    Total Pembimbing
                </div>
                <div class="pb-stat-number">
                    {{ $stat['total'] ?? 0 }}
                </div>
            </div>
        </div>

        <!-- AKTIF -->
        <div class="pb-stat-card">
            <div class="pb-stat-icon pb-stat-green">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="m16 11 2 2 4-4"/>
                </svg>
            </div>

            <div>
                <div class="pb-stat-label">
                    Pembimbing Aktif
                </div>
                <div class="pb-stat-number">
                    {{ $stat['aktif'] ?? 0 }}
                </div>
            </div>
        </div>

        <!-- DIVISI -->
        <div class="pb-stat-card">
            <div class="pb-stat-icon pb-stat-orange">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <path d="M9 21V9h6v12M7 6h.01M17 6h.01"/>
                </svg>
            </div>

            <div>
                <div class="pb-stat-label">
                    Divisi Terisi
                </div>
                <div class="pb-stat-number">
                    {{ $stat['divisi'] ?? 0 }}
                </div>
            </div>
        </div>

        <!-- MAHASISWA -->
        <div class="pb-stat-card">
            <div class="pb-stat-icon pb-stat-purple">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <path d="m2 10 10-6 10 6-10 6-10-6Z"/>
                    <path d="M6 12.5V17c3.8 3 8.2 3 12 0v-4.5M22 10v6"/>
                </svg>
            </div>

            <div>
                <div class="pb-stat-label">
                    Mahasiswa Dibimbing
                </div>
                <div class="pb-stat-number">
                    {{ $stat['mahasiswa'] ?? 0 }}
                </div>
            </div>
        </div>

    </div>

    <!-- DAFTAR -->
    <div class="pb-panel">

        <div class="pb-panel-head">
            <div class="pb-panel-title">

                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 9h10M7 13h10M7 17h6"/>
                </svg>

                <div>
                    <h2>Daftar Pembimbing Lapangan</h2>
                    <p>
                        Data pegawai pembimbing
                        dan divisi penempatan magang.
                    </p>
                </div>
            </div>

            <span class="pb-count">
                {{ $pembimbings->total() }} pembimbing ditemukan
            </span>
        </div>

        <!-- FILTER -->
        <form
            action="{{ route('admin.pembimbing.index') }}"
            method="GET"
            class="pb-filter"
        >
            <div class="pb-search">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.35-4.35"/>
                </svg>

                <input
                    type="search"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Cari nama, NIP, atau divisi..."
                >
            </div>

            <select name="divisi_id">
                <option value="">Semua Divisi</option>

                @foreach($daftarDivisi as $divisi)
                    <option
                        value="{{ $divisi->id }}"
                        @selected(
                            (string) request('divisi_id')
                            === (string) $divisi->id
                        )
                    >
                        {{ $divisi->nama }}
                    </option>
                @endforeach

                <option
                    value="tanpa_divisi"
                    @selected(request('divisi_id') === 'tanpa_divisi')
                >
                    Tanpa Divisi
                </option>
            </select>

            <button
                type="submit"
                class="pb-button pb-button-primary"
            >
                Cari Data
            </button>

            <a
                href="{{ route('admin.pembimbing.index') }}"
                class="pb-button pb-button-light"
            >
                Reset
            </a>
        </form>

        <!-- TABLE -->
        <div class="pb-table-wrap">
            <table class="pb-table">

                <thead>
                    <tr>
                        <th>Pembimbing</th>
                        <th>NIP</th>
                        <th>Divisi / Bagian</th>
                        <th style="text-align:right">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pembimbings as $pb)

                        @php
                            $inisial = collect(
                                preg_split('/\s+/', trim($pb->nama))
                            )
                                ->filter()
                                ->take(2)
                                ->map(fn ($kata) =>
                                    mb_strtoupper(
                                        mb_substr($kata, 0, 1)
                                    )
                                )
                                ->implode('');
                        @endphp

                        <tr>
                            <td>
                                <div class="pb-person">

                                    <div class="pb-avatar">
                                        {{ $inisial }}
                                    </div>

                                    <div>
                                        <div class="pb-name">
                                            {{ $pb->nama }}
                                        </div>

                                        <div class="pb-sub">
                                            Pembimbing Lapangan
                                        </div>
                                    </div>

                                </div>
                            </td>

                            <td>
                                {{ $pb->nip ?: '-' }}
                            </td>

                            <td>
                                <span
                                    class="pb-divisi {{ !$pb->divisi ? 'pb-divisi-empty' : '' }}"
                                >
                                    {{ $pb->divisi?->nama ?? 'Semua divisi' }}
                                </span>
                            </td>

                            <td>
                                <div class="pb-actions">

                                    <a
                                        href="{{ route('admin.pembimbing.edit', $pb) }}"
                                        class="pb-button pb-button-edit"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             viewBox="0 0 24 24"
                                             fill="none"
                                             stroke="currentColor"
                                             stroke-width="2"
                                             stroke-linecap="round"
                                             stroke-linejoin="round">
                                            <path d="M12 20h9"/>
                                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L9 17l-4 1 1-4Z"/>
                                        </svg>
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.pembimbing.hapus', $pb) }}"
                                        onsubmit="return confirm('Yakin ingin menghapus pembimbing ini?');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="pb-button pb-button-delete"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 24 24"
                                                 fill="none"
                                                 stroke="currentColor"
                                                 stroke-width="2"
                                                 stroke-linecap="round"
                                                 stroke-linejoin="round">
                                                <path d="M3 6h18M8 6V4h8v2"/>
                                                <path d="M5 6l1 14h12l1-14M10 10v7M14 10v7"/>
                                            </svg>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="4" class="pb-empty">
                                <strong>
                                    Belum ada data pembimbing
                                </strong>

                                Tambahkan pembimbing baru
                                atau ubah filter pencarian.
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        @if($pembimbings->hasPages())
            <div class="pb-pagination">
                {{ $pembimbings->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
