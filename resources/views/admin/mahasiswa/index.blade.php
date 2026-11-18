
@extends('layouts.admin')

@section('title', 'Data Mahasiswa - SIMASTA')
@section('page-title', 'Data Mahasiswa')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')

@php
    $daftarMahasiswa = collect($mahasiswa ?? []);
    $totalMahasiswa = $daftarMahasiswa->count();

    // Status pengajuan terakhir per akun mahasiswa.
    $pengajuanTerakhir = \App\Models\Pengajuan::query()
        ->orderByDesc('created_at')->orderByDesc('id')->get()
        ->unique('user_id')->keyBy('user_id');

    $statusMahasiswa = $daftarMahasiswa->mapWithKeys(function ($m) use ($pengajuanTerakhir) {
        $status = $pengajuanTerakhir->get($m->user_id)?->status;
        return [$m->id => match ($status) {
            'diproses', 'diverifikasi' => 'pengajuan',
            'diterima' => 'diterima',
            'ditolak' => 'ditolak',
            default => 'belum',
        }];
    });
    $jumlahPengajuan = $statusMahasiswa->filter(fn ($s) => $s === 'pengajuan')->count();
    $jumlahDiterima = $statusMahasiswa->filter(fn ($s) => $s === 'diterima')->count();
    $jumlahDitolak = $statusMahasiswa->filter(fn ($s) => $s === 'ditolak')->count();
    // Belum ada sumber data terverifikasi untuk status pelaksanaan.
    $jumlahAktif = null;
    $jumlahSelesai = null;
    $labelStatus = [
        'pengajuan' => 'Pengajuan',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
        'belum' => 'Belum Mengajukan',
    ];
@endphp

<style>
    .mhs-admin {
        color: #1e293b;
    }

    .mhs-admin * {
        box-sizing: border-box;
    }

    .mhs-page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 25px;
    }

    .mhs-page-header h1 {
        font-size: 25px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #1e293b;
    }

    .mhs-page-header p {
        font-size: 14px;
        color: #64748b;
        line-height: 1.6;
    }

    .mhs-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 13px 19px;
        background: linear-gradient(120deg, #3b82f6, #10b981);
        border-radius: 10px;
        color: white;
        text-decoration: none;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        box-shadow: 0 6px 15px rgba(59,130,246,.2);
    }

    .mhs-add-btn:hover {
        opacity: .9;
    }

    .mhs-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .mhs-stat {
        background: white;
        border: 1px solid #e5e7eb;
        border-top: 3px solid #3b82f6;
        border-radius: 14px;
        padding: 23px;
        box-shadow: 0 2px 10px rgba(15,23,42,.03);
    }

    .mhs-stat.green {
        border-top-color: #10b981;
    }

    .mhs-stat.orange {
        border-top-color: #f97316;
    }

    .mhs-stat-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    .mhs-stat-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }

    .mhs-stat-icon {
        width: 44px;
        height: 44px;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: #dbeafe;
        color: #3b82f6;
        font-size: 20px;
    }

    .mhs-stat.green .mhs-stat-icon {
        background: #d1fae5;
        color: #10b981;
    }

    .mhs-stat.orange .mhs-stat-icon {
        background: #ffedd5;
        color: #f97316;
    }

    .mhs-stat-value {
        font-size: 32px;
        font-weight: 800;
        color: #1e293b;
        margin-top: 13px;
    }

    .mhs-panel {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(15,23,42,.03);
        overflow: hidden;
    }

    .mhs-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 24px;
    }

    .mhs-panel-head h2 {
        font-size: 19px;
        color: #1e293b;
        margin-bottom: 7px;
    }

    .mhs-panel-head p {
        color: #64748b;
        font-size: 13px;
    }

    .mhs-count-badge {
        background: #dbeafe;
        color: #2563eb;
        padding: 7px 13px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mhs-toolbar {
        display: flex;
        gap: 12px;
        padding: 0 24px 20px;
        flex-wrap: wrap;
    }

    .mhs-search {
        position: relative;
        flex: 1;
        min-width: 220px;
    }

    .mhs-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .mhs-search input {
        width: 100%;
        height: 44px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        padding: 0 15px 0 42px;
        font-family: inherit;
        font-size: 13px;
        outline: none;
        background: #fff;
    }

    .mhs-search input:focus {
        border-color: #3b82f6;
    }

    .mhs-select {
        height: 44px;
        min-width: 170px;
        padding: 0 13px;
        border: 1px solid #d1d5db;
        border-radius: 9px;
        background: white;
        color: #475569;
        font-family: inherit;
        font-size: 13px;
        outline: none;
    }

    .mhs-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .mhs-admin table {
        width: 100%;
        border-collapse: collapse;
    }

    .mhs-admin th {
        text-align: left;
        padding: 15px 18px;
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        color: #64748b;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .mhs-admin td {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13px;
        vertical-align: middle;
    }

    .mhs-admin tbody tr:hover {
        background: #f8fbff;
    }

    .mhs-person {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 190px;
    }

    .mhs-avatar {
        width: 39px;
        height: 39px;
        border-radius: 50%;
        background: #dbeafe;
        color: #2563eb;
        display: grid;
        place-items: center;
        font-weight: 700;
        flex-shrink: 0;
    }

    .mhs-person-info strong {
        display: block;
        color: #1e293b;
        margin-bottom: 5px;
        font-size: 13px;
    }

    .mhs-person-info small {
        color: #94a3b8;
        font-size: 11px;
        overflow-wrap: anywhere;
    }

    .mhs-muted {
        color: #64748b;
    }

    .mhs-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .mhs-action {
        width: 33px;
        height: 33px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background: white;
        cursor: pointer;
        text-decoration: none;
        font-size: 14px;
    }

    .mhs-action.view {
        color: #3b82f6;
        background: #eff6ff;
    }

    .mhs-action.edit {
        color: #10b981;
        background: #ecfdf5;
    }

    .mhs-action.delete {
        color: #ef4444;
        background: #fef2f2;
    }

    .mhs-action:hover {
        filter: brightness(.96);
    }

    .mhs-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .mhs-empty-icon {
        width: 65px;
        height: 65px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 18px;
        display: grid;
        place-items: center;
        font-size: 27px;
        margin: 0 auto 18px;
    }

    .mhs-empty h3 {
        font-size: 16px;
        margin-bottom: 8px;
    }

    .mhs-empty p {
        color: #64748b;
        font-size: 13px;
    }

    .mhs-footer {
        padding: 17px 24px;
        border-top: 1px solid #e5e7eb;
        color: #64748b;
        font-size: 12px;
    }

    .mhs-alert {
        padding: 14px 18px;
        border-radius: 10px;
        background: #d1fae5;
        color: #065f46;
        margin-bottom: 20px;
        font-size: 13px;
    }

    @media (max-width: 1000px) {
        .mhs-stats {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 700px) {
        .mhs-stats {
            grid-template-columns: 1fr;
        }

        .mhs-page-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .mhs-toolbar {
            flex-direction: column;
        }

        .mhs-search,
        .mhs-select {
            width: 100%;
        }
    }

/* Tampilan ringkas dengan aksen warna BPS Kota Batu */
.mhs-admin { --bps-blue:#1764a0; --bps-dark:#173657; }
.mhs-page-header { margin-bottom:18px; }
.mhs-page-header h1 { font-size:23px; color:var(--bps-dark); }
.mhs-stats {
    grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px;
    padding:14px; margin-bottom:14px; border-radius:18px;
    background:linear-gradient(125deg,#e2eef9,#f1f6fb 55%,#e7f4ed);
    border:1px solid #dce8f2;
}
.mhs-stat {
    min-height:108px; padding:15px 18px;
    border:1px solid #e4eaf1; border-left:4px solid var(--accent);
    border-radius:12px; background:#fff;
    box-shadow:0 3px 12px rgba(22,68,108,.045);
    transition:transform .2s,box-shadow .2s;
}
.mhs-stat:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(22,68,108,.09); }
.mhs-stat-label { font-size:12px; color:#526983; line-height:1.4; }
.mhs-stat-icon { width:36px; height:36px; border-radius:10px;
    background:#f1f5f9; color:var(--accent); font-size:17px; }
.mhs-stat-value { margin-top:5px; font-size:27px; line-height:1.15; color:var(--bps-dark); }
.mhs-panel { border-color:#e0e9f2; box-shadow:0 4px 16px rgba(22,68,108,.04); }
.mhs-panel-head { background:linear-gradient(100deg,#fff,#f5f9fd); }
.mhs-panel-head h2 { color:var(--bps-dark); }
.mhs-admin th { background:#f0f5fa; color:#486481; }
.mhs-admin tbody tr:hover { background:#f4f9fd; }
.mhs-tabs { display:flex; flex-wrap:wrap; gap:8px; padding:0 24px 18px; }
.mhs-tab { padding:9px 13px; border:1px solid #e2e8f0; border-radius:9px;
    background:#fff; color:#64748b; font:inherit; font-size:12px; font-weight:600; cursor:pointer; }
.mhs-tab.active { background:#1764a0; color:#fff; border-color:#1764a0; }
.mhs-tab:disabled { opacity:.45; cursor:not-allowed; }
.mhs-status { display:inline-block; padding:6px 10px; border-radius:20px;
    font-size:11px; font-weight:700; background:#f1f5f9; color:#64748b; white-space:nowrap; }
.mhs-status.pengajuan { background:#ffedd5; color:#c2410c; }
.mhs-status.diterima { background:#d1fae5; color:#047857; }
.mhs-status.ditolak { background:#fee2e2; color:#b91c1c; }
.mhs-info-note { display:flex; align-items:center; gap:9px; margin-bottom:20px;
    padding:11px 15px; border-radius:10px; background:#eff6ff;
    border:1px solid #bfdbfe; color:#1e40af; font-size:12px; line-height:1.5; }
@media(max-width:1000px) { .mhs-stats { grid-template-columns:repeat(2,minmax(0,1fr)); } }
@media(max-width:600px) { .mhs-stats { grid-template-columns:1fr; padding:10px; }
    .mhs-stat { min-height:96px; padding:13px 15px; } .mhs-stat-value { font-size:25px; } }

/* FINAL LAYOUT: sejajar dengan gaya Pusat Laporan Magang */
.mhs-admin .mhs-hero {
    display:flex; align-items:center; justify-content:space-between; gap:24px;
    padding:28px 30px; margin-bottom:22px;
    background:linear-gradient(110deg,#ffffff 0%,#eef6ff 57%,#e7f8f0 100%);
    border:1px solid #d8e7f4; border-radius:18px;
}
.mhs-admin .mhs-hero-copy { min-width:0; }
.mhs-admin .mhs-hero h1 {
    font-size:clamp(23px,2.5vw,30px); line-height:1.2;
    margin:0 0 12px; color:#173d68; font-weight:750;
}
.mhs-admin .mhs-hero p {
    max-width:780px; margin:0; color:#5d7796; font-size:14px; line-height:1.7;
}
.mhs-admin .mhs-hero-icon {
    display:grid; place-items:center; flex-shrink:0; width:56px; height:56px;
    border-radius:15px; background:#dbeafe; color:#2674d6; font-size:25px;
}
.mhs-admin .mhs-hero-add {
    display:inline-flex; align-items:center; gap:8px; margin-top:16px;
    padding:10px 14px; border-radius:9px; text-decoration:none;
    color:#fff; background:#1764a0; font-size:12px; font-weight:700;
}
.mhs-admin .mhs-hero-add:hover { background:#104f83; }
.mhs-admin .mhs-stats {
    display:grid; grid-template-columns:repeat(3,minmax(0,1fr));
    gap:14px; margin:0 0 20px; padding:0;
    background:none; border:0; border-radius:0;
}
.mhs-admin .mhs-stat {
    display:flex; flex-direction:row; align-items:center; gap:15px;
    min-height:106px; padding:18px 20px; margin:0;
    background:#fff; border:1px solid #dce6f0; border-radius:15px;
    box-shadow:0 2px 8px rgba(15,43,77,.025);
    transform:none;
}
.mhs-admin .mhs-stat:hover { transform:translateY(-2px); box-shadow:0 6px 16px rgba(15,43,77,.06); }
.mhs-admin .mhs-stat-icon {
    flex:0 0 52px; width:52px; height:52px; border-radius:13px;
    display:grid; place-items:center; font-size:22px;
    color:var(--accent); background:#eff6ff;
}
.mhs-admin .mhs-icon-blue { background:#e0edff; }
.mhs-admin .mhs-icon-orange { background:#fff0db; }
.mhs-admin .mhs-icon-green { background:#daf8e9; }
.mhs-admin .mhs-icon-cyan { background:#dff6fa; }
.mhs-admin .mhs-icon-slate { background:#e9edf4; }
.mhs-admin .mhs-icon-red { background:#ffe7e7; }
.mhs-admin .mhs-stat-content { min-width:0; }
.mhs-admin .mhs-stat-label { color:#5c7390; font-size:12px; font-weight:500; line-height:1.4; }
.mhs-admin .mhs-stat-value {
    margin:5px 0 0; font-size:29px; line-height:1.1;
    color:#173d68; font-weight:800; letter-spacing:-.5px;
}
@media(max-width:1100px) {
    .mhs-admin .mhs-stats { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:650px) {
    .mhs-admin .mhs-hero { padding:22px 18px; align-items:flex-start; }
    .mhs-admin .mhs-hero-icon { width:44px; height:44px; font-size:20px; }
    .mhs-admin .mhs-stats { grid-template-columns:1fr; gap:10px; }
    .mhs-admin .mhs-stat { min-height:88px; padding:14px 16px; }
    .mhs-admin .mhs-stat-icon { width:46px; height:46px; flex-basis:46px; }
}
</style>

<div class="mhs-admin">

    {{-- HERO HEADER - mengikuti Pusat Laporan Magang --}}
    <section class="mhs-hero">
        <div class="mhs-hero-copy">
            <h1>Data Mahasiswa</h1>
            <p>Kelola dan pantau data mahasiswa, pengajuan, serta perkembangan kegiatan magang BPS Kota Batu.</p>
            <a href="{{ route('admin.mahasiswa.create') }}" class="mhs-hero-add">
                <i class="bi bi-plus-lg"></i> Tambah Mahasiswa
            </a>
        </div>
        <div class="mhs-hero-icon" aria-hidden="true"><i class="bi bi-people"></i></div>
    </section>

    {{-- NOTIFIKASI --}}
    @if(session('success'))
        <div class="mhs-alert">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- KARTU STATISTIK - horizontal seperti Pusat Laporan Magang --}}
    <div class="mhs-stats">
        @foreach([
            ['Total Mahasiswa', $totalMahasiswa, '#2878bd', 'people', 'blue'],
            ['Pengajuan', $jumlahPengajuan, '#ed8b2f', 'hourglass-split', 'orange'],
            ['Diterima', $jumlahDiterima, '#10a779', 'check-circle', 'green'],
            ['Aktif Magang', $jumlahAktif, '#0799b9', 'briefcase', 'cyan'],
            ['Selesai Magang', $jumlahSelesai, '#687d99', 'mortarboard', 'slate'],
            ['Ditolak', $jumlahDitolak, '#e45858', 'x-circle', 'red'],
        ] as [$judul, $nilai, $warna, $ikon, $tema])
            <div class="mhs-stat" style="--accent: {{ $warna }}">
                <div class="mhs-stat-icon mhs-icon-{{ $tema }}">
                    <i class="bi bi-{{ $ikon }}"></i>
                </div>
                <div class="mhs-stat-content">
                    <div class="mhs-stat-label">{{ $judul }}</div>
                    <div class="mhs-stat-value">{{ $nilai ?? '—' }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mhs-info-note">
        <i class="bi bi-info-circle"></i>
        Status Aktif dan Selesai Magang belum dihitung karena status pelaksanaan belum terhubung ke database.
    </div>

    {{-- TABEL MAHASISWA --}}
    <div class="mhs-panel">

        <div class="mhs-panel-head">
            <div>
                <h2>Daftar Mahasiswa</h2>
                <p>Informasi mahasiswa yang tersimpan di sistem.</p>
            </div>

            <span class="mhs-count-badge">
                {{ $totalMahasiswa }} Mahasiswa
            </span>
        </div>

        <div class="mhs-tabs" aria-label="Filter status mahasiswa">
            <button type="button" class="mhs-tab active" data-status="semua">Semua</button>
            <button type="button" class="mhs-tab" data-status="pengajuan">Pengajuan</button>
            <button type="button" class="mhs-tab" data-status="diterima">Diterima</button>
            <button type="button" class="mhs-tab" data-status="ditolak">Ditolak</button>
            <button type="button" class="mhs-tab" disabled title="Status pelaksanaan belum tersedia">Aktif</button>
            <button type="button" class="mhs-tab" disabled title="Status pelaksanaan belum tersedia">Selesai</button>
        </div>

        <div class="mhs-toolbar">

            <div class="mhs-search">
                <i class="bi bi-search"></i>
                <input type="search"
                       id="cariMahasiswa"
                       placeholder="Cari nama, NIM, universitas...">
            </div>

            <select id="filterTanggal" class="mhs-select">
                <option value="semua">Semua Tanggal</option>
                <option value="bulan">Bulan Ini</option>
                <option value="tahun">Tahun Ini</option>
            </select>

        </div>

        <div class="mhs-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Perguruan Tinggi</th>
                        <th>Program Studi</th>
                        <th>Status</th>
                        <th>Tanggal Daftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($daftarMahasiswa as $m)
                        @php $status = $statusMahasiswa->get($m->id, 'belum'); @endphp
                        <tr class="mhs-row" data-status="{{ $status }}"
                            data-search="{{ strtolower(
                                ($m->nama ?? '') . ' ' .
                                ($m->nim ?? '') . ' ' .
                                ($m->email ?? '') . ' ' .
                                ($m->perguruan_tinggi ?? '') . ' ' .
                                ($m->program_studi ?? '')
                            ) }}"
                            data-tanggal="{{ $m->created_at?->format('Y-m-d') ?? '' }}">

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <div class="mhs-person">
                                    <div class="mhs-avatar">
                                        {{ strtoupper(mb_substr($m->nama ?? 'M', 0, 1)) }}
                                    </div>

                                    <div class="mhs-person-info">
                                        <strong>
                                            {{ $m->nama ?? '-' }}
                                        </strong>
                                        <small>
                                            {{ $m->email ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $m->nim ?? '-' }}</td>

                            <td class="mhs-muted">
                                {{ $m->perguruan_tinggi ?? '-' }}
                            </td>

                            <td class="mhs-muted">
                                {{ $m->program_studi ?? '-' }}
                            </td>

                            <td><span class="mhs-status {{ $status }}">{{ $labelStatus[$status] ?? 'Belum Mengajukan' }}</span></td>

                            <td class="mhs-muted">
                                {{ $m->created_at?->format('d/m/Y') ?? '-' }}
                            </td>

                            <td>
                                <div class="mhs-actions">

                                    <a href="{{ route('admin.mahasiswa.show', $m) }}"
                                       class="mhs-action view"
                                       title="Detail Mahasiswa">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.mahasiswa.edit', $m) }}"
                                       class="mhs-action edit"
                                       title="Edit Mahasiswa">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.mahasiswa.destroy', $m) }}"
                                          onsubmit="return confirm('Yakin ingin menghapus mahasiswa ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="mhs-action delete"
                                                title="Hapus Mahasiswa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mhs-empty" id="dataKosong"
             style="{{ $daftarMahasiswa->count() ? 'display:none' : '' }}">
            <div class="mhs-empty-icon">
                <i class="bi bi-people"></i>
            </div>

            <h3>Tidak Ada Data Mahasiswa</h3>
            <p>
                Belum ada mahasiswa atau tidak ada
                data yang sesuai dengan pencarian.
            </p>
        </div>

        <div class="mhs-footer" id="infoJumlah">
            Menampilkan {{ $totalMahasiswa }} mahasiswa.
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cari = document.getElementById('cariMahasiswa');
    const filter = document.getElementById('filterTanggal');
    const rows = Array.from(document.querySelectorAll('.mhs-row'));
    const tabs = document.querySelectorAll('.mhs-tab:not(:disabled)');
    const kosong = document.getElementById('dataKosong');
    const jumlah = document.getElementById('hasilPencarian');
    const info = document.getElementById('infoJumlah');
    let statusAktif = 'semua';

    function filterData() {
        const keyword = cari.value.toLocaleLowerCase().trim();
        const now = new Date();
        const tahun = String(now.getFullYear());
        const bulan = String(now.getMonth() + 1).padStart(2, '0');
        let terlihat = 0;
        rows.forEach(function (row) {
            const teks = row.dataset.search || '';
            const tanggal = row.dataset.tanggal || '';
            const cocokStatus = statusAktif === 'semua' || row.dataset.status === statusAktif;
            let sesuaiTanggal = true;
            if (filter.value === 'bulan') sesuaiTanggal = tanggal.startsWith(tahun + '-' + bulan);
            if (filter.value === 'tahun') sesuaiTanggal = tanggal.startsWith(tahun);
            const cocok = teks.includes(keyword) && sesuaiTanggal && cocokStatus;
            row.style.display = cocok ? '' : 'none';
            if (cocok) terlihat++;
        });
        kosong.style.display = terlihat === 0 ? '' : 'none';
        jumlah.textContent = terlihat;
        info.textContent = 'Menampilkan ' + terlihat + ' mahasiswa.';
    }
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            statusAktif = tab.dataset.status;
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            filterData();
        });
    });
    cari.addEventListener('input', filterData);
    filter.addEventListener('change', filterData);
    filterData();
});
</script>

@endsection
