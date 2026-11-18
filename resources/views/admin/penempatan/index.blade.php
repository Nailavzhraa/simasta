

@extends('layouts.admin')

@section('title', 'Penempatan Magang - SIMASTA')

@section('page-title', 'Penempatan Magang')

@section('user-role', 'Admin')

@section('user-avatar', 'A')

@section('content')

@php

    $rows = $penempatans ?? $daftar ?? null;

    $angka = $statistik ?? [

        'total' => $hitung['semua'] ?? 0,

        'sudah' => $hitung['sudah'] ?? 0,

        'belum' => $hitung['belum'] ?? 0,

        'divisi' => 0,

    ];

    $divisiList = $divisis ?? collect();

    $timKerjaList = $timKerjas ?? collect();

    $pembimbingList = $pembimbings ?? collect();

    $formatTanggal = fn ($date) => $date

        ? \Carbon\Carbon::parse($date)->translatedFormat('d M Y')

        : '—';

@endphp

<style>

    .pn-page, .pn-page \\* { box-sizing: border-box; }

    .pn-page { font-family: Arial, sans-serif; color: #172b4d; }

    .pn-hero {

        display: flex; align-items: center; justify-content: space-between;

        gap: 18px; flex-wrap: wrap; padding: 28px 35px;

        margin-bottom: 24px;

        background: linear-gradient(115deg,#fff 0%,#eff6ff 62%,#e9f8ef 100%);

        border: 1px solid #dae7f4; border-radius: 19px;

    }

    .pn-hero h1 {

        margin: 0 0 12px; color: #143f74;

        font-size: 25px; font-weight: 700; letter-spacing: -.5px;

    }

    .pn-hero p {

        margin: 0; max-width: 790px; color: #5f7794;

        font-size: 13px; line-height: 1.65;

    }

    .pn-icon {

        width: 18px; height: 18px; display: block;

        flex-shrink: 0; stroke: currentColor; fill: none;

        stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;

    }

    .pn-btn {

        display: inline-flex; align-items: center;

        justify-content: center; gap: 8px;

        border: 1px solid transparent; padding: 10px 15px;

        border-radius: 9px; font: 700 12px Arial,sans-serif;

        text-decoration: none; cursor: pointer; white-space: nowrap;

        transition: background .15s;

    }

    .pn-btn-primary { background: #2563eb; color: #fff; }

    .pn-btn-primary:hover { background: #1d4ed8; color: #fff; }

    .pn-btn-light {

        background: #fff; color: #475569; border-color: #d4deed;

    }

    .pn-btn-light:hover { background: #f6f9ff; }

    .pn-btn-outline {

        background: #fff; color: #2563eb;

        border-color: #a9c2fa; padding: 7px 11px;

    }

    .pn-btn-outline:hover { background: #eff6ff; }

    .pn-btn:disabled { opacity: .5; cursor: not-allowed; }

    .pn-stats {

        display: grid; grid-template-columns: repeat(4,minmax(0,1fr));

        gap: 18px; margin-bottom: 26px;

    }

    .pn-stat {

        min-width: 0; display: flex; align-items: center;

        gap: 15px; padding: 15px 17px;

        background: #fff; border: 1px solid #dfe7f1;

        border-radius: 16px;

    }

    .pn-stat-icon {

        width: 46px; height: 46px; border-radius: 12px;

        display: grid; place-items: center; flex-shrink: 0;

    }

    .pn-stat-icon .pn-icon { width: 24px; height: 24px; }

    .pn-i-blue { color: #2563eb; background: #e8f0ff; }

    .pn-i-green { color: #059669; background: #ddf8ec; }

    .pn-i-orange { color: #ea580c; background: #fff0df; }

    .pn-i-purple { color: #7c3aed; background: #f0e9ff; }

    .pn-stat-title {

        margin-bottom: 6px; color: #5f7794;

        font-size: 12px; line-height: 1.4;

    }

    .pn-stat-value {

        color: #102d54; font-size: 27px;

        line-height: 1; font-weight: 700;

    }

    .pn-alert {

        padding: 12px 15px; border-radius: 10px;

        margin-bottom: 18px; font-size: 13px;

    }

    .pn-alert-ok {

        background: #ecfdf5; border: 1px solid #a7f3d0;

        color: #047857;

    }

    .pn-alert-error {

        background: #fef2f2; border: 1px solid #fecaca;

        color: #b91c1c;

    }

    .pn-panel {

        background: #fff; border: 1px solid #dfe7f1;

        border-radius: 17px; overflow: hidden;

    }

    .pn-panel-head {

        display: flex; align-items: center;

        justify-content: space-between; gap: 12px;

        flex-wrap: wrap; padding: 24px 25px 18px;

    }

    .pn-panel h2 {

        margin: 0 0 6px; font-size: 21px; color: #163f73;

    }

    .pn-panel-sub {

        margin: 0; color: #64748b; font-size: 12px;

    }

    .pn-count {

        border-radius: 30px; padding: 8px 12px;

        font-size: 12px; font-weight: 700;

        color: #2563eb; background: #e8f0ff;

    }

    .pn-filters {

        display: grid;

        grid-template-columns: minmax(200px,1.8fr) minmax(135px,.85fr)

                               minmax(135px,.85fr) auto auto;

        gap: 10px; align-items: center; padding: 0 25px 21px;

    }

    .pn-search {

        display: flex; align-items: center; gap: 9px;

        padding: 0 12px; min-width: 0;

        border: 1px solid #d7e1ef; border-radius: 9px; height: 42px;

    }

    .pn-search .pn-icon { color: #64748b; }

    .pn-search input {

        border: 0; background: transparent; outline: none;

        width: 100%; font: 12px Arial,sans-serif; color: #334155;

    }

    .pn-filters select {

        width: 100%; height: 42px;

        border: 1px solid #d7e1ef; padding: 0 10px;

        border-radius: 9px; background: #fff;

        color: #334155; font: 12px Arial,sans-serif;

    }

    .pn-table-wrap {

        margin: 0 25px; overflow-x: auto;

        border: 1px solid #e2e8f0; border-radius: 11px;

    }

    .pn-table {

        border-collapse: collapse; width: 100%; min-width: 1120px;

    }

    .pn-table th {

        padding: 10px 10px; background: #f2f6fb;

        text-align: left; font-size: 12px;

        color: #173c70; font-weight: 700;

    }

    .pn-table td {

        padding: 10px; border-top: 1px solid #e9eef5;

        font-size: 12px; color: #243b5b; vertical-align: middle;

    }

    .pn-table tbody tr:hover { background: #f9fbff; }

    .pn-student-name {

        font-size: 12px; font-weight: 700; color: #183253;

    }

    .pn-muted {

        margin-top: 5px; font-size: 11px;

        color: #7689a5; line-height: 1.45;

    }

    .pn-status {

        display: inline-flex; align-items: center;

        gap: 6px; padding: 7px 11px; border-radius: 999px;

        font-size: 11px; font-weight: 700; white-space: nowrap;

    }

    .pn-status .pn-icon { width: 14px; height: 14px; }

    .pn-status-yes { background: #e0f8ec; color: #047857; }

    .pn-status-no { background: #fff0db; color: #c45b06; }

    .pn-empty {

        padding: 34px 18px !important;

        color: #64748b; text-align: center;

    }

    .pn-footer {

        padding: 17px 25px; display: flex;

        justify-content: space-between; align-items: center;

        gap: 16px; flex-wrap: wrap;

        font-size: 12px; color: #64748b;

    }

    .pn-modal {

        position: fixed; inset: 0;

        background: rgba(15,23,42,.5);

        z-index: 1200; padding: 18px;

        display: none; align-items: center; justify-content: center;

    }

    .pn-modal.is-open { display: flex; }

    .pn-dialog {

        width: 100%; max-width: 520px;

        max-height: 90vh; overflow-y: auto;

        background: #fff; border-radius: 16px;

        box-shadow: 0 25px 70px rgba(15,23,42,.2);

    }

    .pn-dialog-head {

        padding: 20px 23px 16px;

        display: flex; align-items: flex-start;

        justify-content: space-between; gap: 10px;

        border-bottom: 1px solid #edf2f7;

    }

    .pn-dialog h3 {

        font-size: 18px; margin: 0 0 5px; color: #173c70;

    }

    .pn-dialog p {

        margin: 0; color: #64748b; font-size: 12px;

    }

    .pn-icon-close {

        border: 0; cursor: pointer; background: #f1f5f9;

        color: #475569; border-radius: 8px; padding: 6px;

    }

    .pn-dialog-body { padding: 21px 23px 7px; }

    .pn-field { margin-bottom: 17px; }

    .pn-field label {

        display: block; margin-bottom: 7px;

        font-size: 12px; font-weight: 700; color: #334155;

    }

    .pn-field select, .pn-field textarea, .pn-field input[type="date"] {

        display: block; width: 100%; padding: 11px 12px;

        border-radius: 9px; border: 1px solid #d7e1ed;

        background: #fff; color: #334155;

        font: 12px Arial,sans-serif;

    }

    .pn-field textarea {

        min-height: 112px; line-height: 1.55; resize: vertical;

    }

    .pn-field-hint {

        margin-top: 6px; color: #8190a8;

        font-size: 11px; line-height: 1.5;

    }

    .pn-action-group { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }

    .pn-btn-compact { padding: 7px 9px; font-size: 11px; }

    .pn-btn-green { background: #047857; color: #fff; }

    .pn-btn-green:hover { background: #065f46; color: #fff; }

    .pn-btn-orange { background: #fff7ed; color: #9a3412; border-color: #fed7aa; }

    .pn-btn-orange:hover { background: #ffedd5; }

    .pn-status-progress { background: #dbeafe; color: #1d4ed8; }

    .pn-status-done { background: #f1f5f9; color: #475569; }

    .pn-dialog-foot {

        display: flex; justify-content: flex-end;

        gap: 9px; padding: 16px 23px 21px;

        border-top: 1px solid #edf2f7;

    }

    .pn-info {

        margin-bottom: 15px; padding: 12px;

        color: #92400e; background: #fffbeb;

        border: 1px solid #fde68a; border-radius: 9px;

        font-size: 12px;

    }

    body.pn-modal-active { overflow: hidden; }

    @media(max-width:1150px) {

        .pn-stats { grid-template-columns: repeat(2,minmax(0,1fr)); }

        .pn-filters { grid-template-columns: repeat(2,minmax(0,1fr)); }

        .pn-search { grid-column: span 2; }

    }

    @media(max-width:650px) {

        .pn-hero { padding: 21px; }

        .pn-hero h1 { font-size: 23px; }

        .pn-hero .pn-btn { width: 100%; }

        .pn-stats { gap: 10px; }

        .pn-stat { padding: 12px; gap: 9px; }

        .pn-stat-icon { width: 36px; height: 36px; }

        .pn-stat-icon .pn-icon { width: 19px; height: 19px; }

        .pn-stat-title { font-size: 11px; }

        .pn-stat-value { font-size: 22px; }

        .pn-panel-head { padding: 18px; }

        .pn-panel h2 { font-size: 18px; }

        .pn-filters { padding: 0 18px 16px; }

        .pn-filters .pn-btn { width: 100%; }

        .pn-table-wrap { margin: 0 12px; }

        .pn-footer { padding: 16px; }

    }

</style>

<div class="pn-page">

    <section class="pn-hero">

        <div>

            <h1>Penempatan Magang</h1>

            <p>

                Kelola divisi, tim kerja, pembimbing, dan jobdesk

                mahasiswa yang telah diterima magang di BPS Kota Batu.

            </p>

        </div>

        <button

            type="button"

            class="pn-btn pn-btn-primary"

            id="pn-open-first"

            @disabled(!$rows || !$rows->contains(fn ($p) => $p->status_pelaksanaan === 'belum_mulai'))

        >

            <svg class="pn-icon" viewBox="0 0 24 24">

                <path d="M12 5v14M5 12h14"/>

            </svg>

            Atur Penempatan

        </button>

    </section>

    @if(session('success'))

        <div class="pn-alert pn-alert-ok">

            {{ session('success') }}

        </div>

    @endif

    @if(session('error'))

        <div class="pn-alert pn-alert-error">

            {{ session('error') }}

        </div>

    @endif

    @if($errors->any())

        <div class="pn-alert pn-alert-error">

            {{ $errors->first() }}

        </div>

    @endif

    <section class="pn-stats" aria-label="Ringkasan penempatan">

        <div class="pn-stat">

            <div class="pn-stat-icon pn-i-blue">

                <svg class="pn-icon" viewBox="0 0 24 24">

                    <circle cx="9" cy="7" r="4"/>

                    <path d="M2 21v-2a5 5 0 0 1 5-5h4a5 5 0 0 1 5 5v2M16 11l2 2 4-4"/>

                </svg>

            </div>

            <div>

                <div class="pn-stat-title">Pengajuan Diterima</div>

                <div class="pn-stat-value">{{ $angka['total'] ?? 0 }}</div>

            </div>

        </div>

        <div class="pn-stat">

            <div class="pn-stat-icon pn-i-green">

                <svg class="pn-icon" viewBox="0 0 24 24">

                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>

                    <circle cx="9" cy="7" r="4"/>

                    <path d="M20 8v6m-3-3h6"/>

                </svg>

            </div>

            <div>

                <div class="pn-stat-title">Sudah Ditempatkan</div>

                <div class="pn-stat-value">{{ $angka['sudah'] ?? 0 }}</div>

            </div>

        </div>

        <div class="pn-stat">

            <div class="pn-stat-icon pn-i-orange">

                <svg class="pn-icon" viewBox="0 0 24 24">

                    <circle cx="9" cy="7" r="4"/>

                    <path d="M2 21v-2a5 5 0 0 1 5-5h4"/>

                    <circle cx="18" cy="17" r="5"/>

                    <path d="M18 14v3l2 1"/>

                </svg>

            </div>

            <div>

                <div class="pn-stat-title">Belum Ditempatkan</div>

                <div class="pn-stat-value">{{ $angka['belum'] ?? 0 }}</div>

            </div>

        </div>

        <div class="pn-stat">

            <div class="pn-stat-icon pn-i-purple">

                <svg class="pn-icon" viewBox="0 0 24 24">

                    <rect x="4" y="8" width="16" height="13" rx="1"/>

                    <path d="M9 21V4h6v17M7 12h1M7 16h1M12 8v1M12 13v1M17 12h1M17 16h1"/>

                </svg>

            </div>

            <div>

                <div class="pn-stat-title">Divisi Terisi</div>

                <div class="pn-stat-value">{{ $angka['divisi'] ?? 0 }}</div>

            </div>

        </div>

    </section>

    <section class="pn-panel">

        <div class="pn-panel-head">

            <div>

                <h2>Daftar Penempatan Mahasiswa</h2>

                <p class="pn-panel-sub">

                    Atur divisi, tim kerja, pembimbing, dan jobdesk mahasiswa magang.

                </p>

            </div>

            <span class="pn-count">

                {{ $rows ? $rows->total() : 0 }} data

            </span>

        </div>

        <form

            class="pn-filters"

            method="GET"

            action="{{ route('admin.penempatan.index') }}"

        >

            <label class="pn-search">

                <svg class="pn-icon" viewBox="0 0 24 24">

                    <circle cx="11" cy="11" r="8"/>

                    <path d="m21 21-4.35-4.35"/>

                </svg>

                <input

                    type="search"

                    name="q"

                    value="{{ request('q') }}"

                    placeholder="Cari nama, email, atau instansi..."

                    aria-label="Cari penempatan"

                >

            </label>

            <select name="status" aria-label="Filter status">

                <option value="">Semua Status</option>

                <option value="sudah" @selected(request('status') === 'sudah')>

                    Sudah Ditempatkan

                </option>

                <option value="belum" @selected(request('status') === 'belum')>

                    Belum Ditempatkan

                </option>

            </select>

            <select name="divisi_id" aria-label="Filter divisi">

                <option value="">Semua Divisi</option>

                @foreach($divisiList as $divisi)

                    <option

                        value="{{ $divisi->id }}"

                        @selected((string) request('divisi_id') === (string) $divisi->id)

                    >

                        {{ $divisi->nama }}

                    </option>

                @endforeach

            </select>

            <button type="submit" class="pn-btn pn-btn-primary">

                Cari Data

            </button>

            <a

                href="{{ route('admin.penempatan.index') }}"

                class="pn-btn pn-btn-light"

            >

                Reset

            </a>

        </form>

        <div class="pn-table-wrap">

            <table class="pn-table">

                <thead>

                    <tr>

                        <th>Mahasiswa</th>

                        <th>Instansi</th>

                        <th>Divisi</th>

                        <th>Tim Kerja</th>

                        <th>Pembimbing</th>

                        <th>Periode</th>

                        <th>Penempatan</th>

                        <th>Pelaksanaan</th>

                        <th>Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($rows ?? [] as $p)

                        @php

                            $mhs = $p->user?->mahasiswa;

                            $instansi = data_get($mhs, 'universitas')

                                ?: (data_get($mhs, 'perguruan_tinggi') ?: $p->instansi);

                            $isPlaced = filled($p->divisi_id)

                                && filled($p->pembimbing_id);

                        @endphp

                        <tr>

                            <td>

                                <div class="pn-student-name">

                                    {{ $p->user?->name ?? 'Mahasiswa' }}

                                </div>

                                <div class="pn-muted">

                                    {{ $p->user?->email ?? '—' }}

                                </div>

                                <div class="pn-muted">

                                    {{ data_get($mhs, 'nim') ?: '' }}

                                </div>

                            </td>

                            <td>{{ $instansi ?: '—' }}</td>

                            <td>

                                {{ $p->divisi?->nama ?? ($p->bidang ?: '—') }}

                            </td>

                            <td>

                                {{ $p->timKerja?->nama ?? '—' }}

                                @if($isPlaced && !$p->tim_kerja_id)

                                    <div class="pn-muted">Belum diatur</div>

                                @endif

                            </td>

                            <td>

                                {{ $p->pembimbingRef?->nama ?? ($p->pembimbing ?: '—') }}

                            </td>

                            <td>

                                {{ $formatTanggal($p->periode_mulai) }}

                                <div class="pn-muted">

                                    s/d {{ $formatTanggal($p->periode_selesai) }}

                                </div>

                            </td>

                            <td>

                                @if($isPlaced)

                                    <span class="pn-status pn-status-yes">

                                        Sudah Ditempatkan

                                    </span>

                                @else

                                    <span class="pn-status pn-status-no">

                                        Belum Ditempatkan

                                    </span>

                                @endif

                            </td>

                            <td>

                                @if($p->status_pelaksanaan === 'selesai')

                                    <span class="pn-status pn-status-done">Selesai Magang</span>

                                @elseif($p->status_pelaksanaan === 'berlangsung')

                                    <span class="pn-status pn-status-progress">Sedang Magang</span>

                                @else

                                    <span class="pn-status pn-status-no">Belum Mulai</span>

                                @endif

                            </td>

                            <td>

                                <div class="pn-action-group">

                                    @if($p->status_pelaksanaan === 'belum_mulai')

                                        <button type="button" class="pn-btn pn-btn-outline pn-btn-compact"

                                            data-pn-open="pn-modal-{{ $p->id }}">

                                            {{ $isPlaced ? 'Ubah' : 'Atur' }}

                                        </button>

                                        @if($isPlaced)

                                            <button type="button" class="pn-btn pn-btn-green pn-btn-compact"

                                                data-pn-open="pn-mulai-{{ $p->id }}">Mulai Magang</button>

                                        @endif

                                    @elseif($p->status_pelaksanaan === 'berlangsung')

                                        <button type="button" class="pn-btn pn-btn-orange pn-btn-compact"

                                            data-pn-open="pn-selesai-{{ $p->id }}">Selesai Magang</button>

                                    @else

                                        <span class="pn-muted">Tidak ada aksi</span>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9" class="pn-empty">

                                Belum ada mahasiswa yang diterima

                                atau cocok dengan filter pencarian.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pn-footer">

            <span>

                Menampilkan {{ $rows ? $rows->count() : 0 }}

                dari {{ $rows ? $rows->total() : 0 }} pengajuan diterima

            </span>

            @if($rows && $rows->hasPages())

                <div>{{ $rows->links() }}</div>

            @endif

        </div>

    </section>

</div>

{{-- Modal penempatan untuk setiap mahasiswa --}}

@foreach($rows ?? [] as $p)

    @if($p->status_pelaksanaan === 'belum_mulai')

    <div

        class="pn-modal"

        id="pn-modal-{{ $p->id }}"

        role="dialog"

        aria-modal="true"

        aria-hidden="true"

        aria-label="Atur penempatan mahasiswa"

    >

        <div class="pn-dialog">

            <form

                method="POST"

                action="{{ route('admin.penempatan.simpan', $p) }}"

            >

                @csrf

                <div class="pn-dialog-head">

                    <div>

                        <h3>

                            {{ $p->divisi_id && $p->pembimbing_id

                                ? 'Ubah Penempatan'

                                : 'Atur Penempatan' }}

                        </h3>

                        <p>{{ $p->user?->name ?? 'Mahasiswa' }}</p>

                    </div>

                    <button

                        type="button"

                        class="pn-icon-close"

                        data-pn-close

                        aria-label="Tutup"

                    >

                        <svg class="pn-icon" viewBox="0 0 24 24">

                            <path d="M18 6 6 18M6 6l12 12"/>

                        </svg>

                    </button>

                </div>

                <div class="pn-dialog-body">

                    @if($divisiList->isEmpty())

                        <div class="pn-info">

                            Belum ada divisi.

                            <a href="{{ route('admin.divisi.index') }}">

                                Tambahkan divisi

                            </a>

                            terlebih dahulu.

                        </div>

                    @endif

                    @if($pembimbingList->isEmpty())

                        <div class="pn-info">

                            Belum ada pembimbing.

                            <a href="{{ route('admin.pembimbing.index') }}">

                                Tambahkan pembimbing

                            </a>

                            terlebih dahulu.

                        </div>

                    @endif

                    <div class="pn-field">

                        <label for="pn-divisi-{{ $p->id }}">

                            Divisi / Bagian \\*

                        </label>

                        <select

                            name="divisi_id"

                            id="pn-divisi-{{ $p->id }}"

                            data-pn-divisi

                            data-pn-jobdesk="pn-jobdesk-{{ $p->id }}"

                            data-pn-pembimbing="pn-pembimbing-{{ $p->id }}"

                            data-pn-tim="pn-tim-{{ $p->id }}"

                            required

                        >

                            <option value="">Pilih divisi</option>

                            @foreach($divisiList as $dv)

                                <option

                                    value="{{ $dv->id }}"

                                    data-jobdesk="{{ $dv->jobdesk }}"

                                    @selected((int)$p->divisi_id === (int)$dv->id)

                                >

                                    {{ $dv->nama }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="pn-field">

                        <label for="pn-tim-{{ $p->id }}">

                            Tim Kerja

                        </label>

                        <select

                            name="tim_kerja_id"

                            id="pn-tim-{{ $p->id }}"

                        >

                            <option value="">Pilih tim kerja</option>

                            @foreach($timKerjaList as $tim)

                                <option

                                    value="{{ $tim->id }}"

                                    data-divisi="{{ $tim->divisi_id }}"

                                    @selected((int)$p->tim_kerja_id === (int)$tim->id)

                                >

                                    {{ $tim->nama }} ({{ $divisiList->firstWhere('id', $tim->divisi_id)?->nama ?? 'Tanpa divisi' }})

                                </option>

                            @endforeach

                        </select>

                        <div class="pn-field-hint">

                            Semua Tim Kerja ditampilkan. Pilih yang sesuai dengan divisi.

                            Kosongkan jika belum ditentukan.

                        </div>

                    </div>

                    <div class="pn-field">

                        <label for="pn-pembimbing-{{ $p->id }}">

                            Pembimbing Lapangan \\*

                        </label>

                        <select

                            name="pembimbing_id"

                            id="pn-pembimbing-{{ $p->id }}"

                            required

                        >

                            <option value="">Pilih pembimbing</option>

                            @foreach($pembimbingList as $pb)

                                <option

                                    value="{{ $pb->id }}"

                                    data-divisi="{{ $pb->divisi_id }}"

                                    @selected((int)$p->pembimbing_id === (int)$pb->id)

                                >

                                    {{ $pb->nama }} ({{ $divisiList->firstWhere('id', $pb->divisi_id)?->nama ?? 'Semua divisi' }})

                                </option>

                            @endforeach

                        </select>

                        <div class="pn-field-hint">

                            Semua pembimbing ditampilkan. Pilih yang sesuai dengan divisi.

                            Pembimbing tanpa divisi dapat dipilih

                            untuk semua divisi.

                        </div>

                    </div>

                    <div class="pn-field">

                        <label for="pn-jobdesk-{{ $p->id }}">

                            Jobdesk Magang \\*

                        </label>

                        <textarea

                            name="jobdesk"

                            id="pn-jobdesk-{{ $p->id }}"

                            maxlength="2000"

                            required

                            placeholder="Tuliskan tugas mahasiswa selama magang..."

                        >{{ $p->jobdesk }}</textarea>

                        <div class="pn-field-hint">

                            Saat divisi diubah, jobdesk mengikuti

                            template divisi dan tetap bisa diedit.

                        </div>

                    </div>

                </div>

                <div class="pn-dialog-foot">

                    <button

                        type="button"

                        class="pn-btn pn-btn-light"

                        data-pn-close

                    >

                        Batal

                    </button>

                    <button

                        type="submit"

                        class="pn-btn pn-btn-primary"

                        @disabled($divisiList->isEmpty() || $pembimbingList->isEmpty())

                    >

                        Simpan Penempatan

                    </button>

                </div>

            </form>

        </div>

    </div>

    @endif

    @if($p->status === 'diterima' && $p->divisi_id && $p->pembimbing_id && $p->status_pelaksanaan === 'belum_mulai')

    <div class="pn-modal" id="pn-mulai-{{ $p->id }}" role="dialog" aria-modal="true"

        aria-hidden="true" aria-label="Konfirmasi mulai magang">

        <div class="pn-dialog">

            <form method="POST" action="{{ route('admin.penempatan.mulai', $p) }}">

                @csrf

                @method('PATCH')

                <div class="pn-dialog-head">

                    <div><h3>Konfirmasi Mulai Magang</h3><p>{{ $p->user?->name ?? 'Mahasiswa' }}</p></div>

                    <button type="button" class="pn-icon-close" data-pn-close aria-label="Tutup">&times;</button>

                </div>

                <div class="pn-dialog-body">

                    <div class="pn-field">

                        <label for="pn-mulai-tanggal-{{ $p->id }}">Tanggal mulai aktual *</label>

                        <input type="date" id="pn-mulai-tanggal-{{ $p->id }}"

                            name="tanggal_mulai_aktual" value="{{ now()->toDateString() }}"

                            max="{{ now()->toDateString() }}" required>

                        <div class="pn-field-hint">Pilih tanggal mahasiswa benar-benar mulai magang.</div>

                    </div>

                </div>

                <div class="pn-dialog-foot">

                    <button type="button" class="pn-btn pn-btn-light" data-pn-close>Batal</button>

                    <button type="submit" class="pn-btn pn-btn-green">Konfirmasi Mulai</button>

                </div>

            </form>

        </div>

    </div>

    @endif

    @if($p->status_pelaksanaan === 'berlangsung' && $p->tanggal_mulai_aktual)

    <div class="pn-modal" id="pn-selesai-{{ $p->id }}" role="dialog" aria-modal="true"

        aria-hidden="true" aria-label="Konfirmasi selesai magang">

        <div class="pn-dialog">

            <form method="POST" action="{{ route('admin.penempatan.selesai', $p) }}">

                @csrf

                @method('PATCH')

                <div class="pn-dialog-head">

                    <div><h3>Konfirmasi Selesai Magang</h3><p>{{ $p->user?->name ?? 'Mahasiswa' }}</p></div>

                    <button type="button" class="pn-icon-close" data-pn-close aria-label="Tutup">&times;</button>

                </div>

                <div class="pn-dialog-body">

                    <div class="pn-field">

                        <label for="pn-selesai-tanggal-{{ $p->id }}">Tanggal selesai aktual *</label>

                        <input type="date" id="pn-selesai-tanggal-{{ $p->id }}"

                            name="tanggal_selesai_aktual"

                            min="{{ $p->tanggal_mulai_aktual->format('Y-m-d') }}"

                            max="{{ now()->toDateString() }}" value="{{ now()->toDateString() }}" required>

                        <div class="pn-field-hint">Tanggal selesai tidak boleh sebelum tanggal mulai atau setelah hari ini.</div>

                    </div>

                </div>

                <div class="pn-dialog-foot">

                    <button type="button" class="pn-btn pn-btn-light" data-pn-close>Batal</button>

                    <button type="submit" class="pn-btn pn-btn-primary">Konfirmasi Selesai</button>

                </div>

            </form>

        </div>

    </div>

    @endif

@endforeach

<script>

(function () {

    const modals = document.querySelectorAll('.pn-modal');

    function closeModals() {

        modals.forEach(modal => {

            modal.classList.remove('is-open');

            modal.setAttribute('aria-hidden', 'true');

        });

        document.body.classList.remove('pn-modal-active');

    }

    function openModal(id) {

        const modal = document.getElementById(id);

        if (!modal) return;

        closeModals();

        modal.classList.add('is-open');

        modal.setAttribute('aria-hidden', 'false');

        document.body.classList.add('pn-modal-active');

        modal.querySelector('[data-pn-close]')?.focus();

    }

    document.querySelectorAll('[data-pn-open]').forEach(button => {

        button.addEventListener('click', () => {

            openModal(button.dataset.pnOpen);

        });

    });

    document.getElementById('pn-open-first')?.addEventListener('click', () => {

        const target = document.querySelector('.pn-status-no')

            ?.closest('tr')

            ?.querySelector('[data-pn-open]')

            || document.querySelector('[data-pn-open]');

        target?.click();

    });

    document.querySelectorAll('[data-pn-close]').forEach(button => {

        button.addEventListener('click', closeModals);

    });

    modals.forEach(modal => {

        modal.addEventListener('mousedown', event => {

            if (event.target === modal) closeModals();

        });

    });

    document.addEventListener('keydown', event => {

        if (event.key === 'Escape') closeModals();

    });

    document.querySelectorAll('[data-pn-divisi]').forEach(select => {

        const jobdesk = document.getElementById(

            select.dataset.pnJobdesk

        );

        const pembimbing = document.getElementById(

            select.dataset.pnPembimbing

        );

        const timKerja = document.getElementById(

            select.dataset.pnTim

        );

        if (!jobdesk || !pembimbing || !timKerja) return;

        // Seluruh opsi tetap terlihat seperti dropdown Divisi.
        // Controller tetap memvalidasi kecocokan divisi saat menyimpan.
        function showAllOptions(element) {
            Array.from(element.options).forEach(option => {
                option.hidden = false;
                option.disabled = false;
            });
        }

        function update(fillJobdesk) {
            const selected = select.selectedOptions[0];

            if (fillJobdesk) {
                jobdesk.value = selected?.dataset.jobdesk || '';
            }

            showAllOptions(timKerja);
            showAllOptions(pembimbing);
        }

        select.addEventListener('change', () => update(true));

        // Saat modal dibuka, pertahankan nilai lama

        update(false);

    });

})();

</script>

@endsection
