@extends('layouts.admin')

@section('title', 'Monitoring Logbook - SIMASTA')
@section('page-title', 'Monitoring Logbook')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')
@php
    $statistik = $statistik ?? [];
    $jumlahLogbook = $jumlahLogbook ?? collect();
    $divisis = $divisis ?? collect();
    $formatTanggal = fn ($tanggal) => $tanggal ? \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('d M Y') : '—';
@endphp

<style>
    .ml-page{--blue:#1d5fa8;--navy:#153b64;--green:#14946b;--orange:#e78e30;--border:#e0e8f0;color:#263a51;font-family:inherit}
    .ml-page *{box-sizing:border-box}
    .ml-hero{background:linear-gradient(110deg,#fff 0%,#edf6ff 62%,#e8f8ef 100%);border:1px solid #dae7f3;border-radius:15px;padding:25px 27px;margin-bottom:19px;display:flex;align-items:center;justify-content:space-between;gap:15px}
    .ml-eyebrow{font-size:10px;font-weight:800;letter-spacing:1px;color:var(--blue)}
    .ml-hero h1{font-size:25px;line-height:1.3;color:var(--navy);margin:8px 0}
    .ml-hero p{font-size:12px;line-height:1.7;color:#66809b;margin:0;max-width:730px}
    .ml-hero-icon{width:54px;height:54px;display:grid;place-items:center;background:#dcecff;color:var(--blue);border-radius:15px;font-size:24px;flex-shrink:0}
    .ml-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:13px;margin-bottom:19px}
    .ml-stat{padding:17px;border:1px solid var(--border);background:#fff;border-radius:14px;display:flex;align-items:center;gap:12px;min-width:0}
    .ml-stat-icon{width:43px;height:43px;border-radius:12px;background:#e8f2fe;color:var(--blue);display:grid;place-items:center;flex-shrink:0;font-size:19px}
    .ml-stat:nth-child(2) .ml-stat-icon{background:#e8f8ee;color:#11915c}
    .ml-stat:nth-child(3) .ml-stat-icon{background:#fff1df;color:#d78623}
    .ml-stat:nth-child(4) .ml-stat-icon{background:#e8f4ef;color:#158769}
    .ml-stat span{display:block;font-size:11px;color:#71849a;line-height:1.4}
    .ml-stat strong{display:block;margin-top:6px;color:var(--navy);font-size:25px;line-height:1.2}
    .ml-panel{background:#fff;border:1px solid var(--border);border-radius:15px;overflow:hidden}
    .ml-panel-head{padding:21px 21px 17px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap}
    .ml-panel-head h2{font-size:17px;margin:0 0 5px;color:var(--navy)}
    .ml-panel-head p{font-size:12px;color:#8190a4;margin:0;line-height:1.5}
    .ml-filter{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .ml-input,.ml-select{height:39px;border:1px solid #dbe5ee;border-radius:9px;padding:0 11px;background:#fafcfe;color:#344a62;font-family:inherit;font-size:12px;min-width:0}
    .ml-input{width:205px}
    .ml-select{max-width:160px}
    .ml-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;min-height:37px;padding:9px 12px;border:1px solid transparent;border-radius:9px;background:var(--blue);color:#fff;text-decoration:none;cursor:pointer;font-family:inherit;font-size:12px;font-weight:700;white-space:nowrap}
    .ml-btn:hover{background:#144a87;color:#fff}
    .ml-btn-light{background:#f7f9fc;border-color:#dce5ee;color:#405772}
    .ml-btn-light:hover{background:#eaf2fb;color:var(--blue)}
    .ml-tablewrap{overflow-x:auto;width:100%}
    .ml-table{width:100%;min-width:930px;border-collapse:collapse}
    .ml-table th{text-align:left;background:#f7faff;color:#7890a8;border-top:1px solid #eef2f6;border-bottom:1px solid #e7edf3;font-size:10px;text-transform:uppercase;letter-spacing:.6px;padding:12px 14px;white-space:nowrap}
    .ml-table td{padding:14px;border-bottom:1px solid #edf1f6;vertical-align:middle;font-size:12px;color:#52657c}
    .ml-table tr:last-child td{border-bottom:0}
    .ml-table tbody tr:hover{background:#fbfdff}
    .ml-student{display:flex;align-items:center;gap:10px;min-width:190px}
    .ml-avatar{flex-shrink:0;display:grid;place-items:center;width:39px;height:39px;background:#eaf3ff;color:var(--blue);border-radius:11px;font-size:15px;font-weight:800}
    .ml-name{color:#1b3c60;font-weight:750;font-size:12px;margin-bottom:4px}
    .ml-sub{font-size:11px;color:#8999ad;line-height:1.5;overflow-wrap:anywhere}
    .ml-badge{display:inline-flex;align-items:center;border-radius:100px;padding:6px 9px;font-size:11px;font-weight:750;white-space:nowrap}
    .ml-badge-ok{background:#e6f8ef;color:#117d55}
    .ml-badge-wait{background:#fff2dc;color:#a86412}
    .ml-badge-neutral{background:#eef2f6;color:#67778b}
    .ml-count{font-size:15px;font-weight:800;color:var(--navy)}
    .ml-empty{text-align:center;padding:47px 16px!important;color:#8b9bb0;font-size:12px}
    .ml-empty i{font-size:28px;color:#a9bed1;display:block;margin-bottom:10px}
    .ml-footer{border-top:1px solid #edf2f6;padding:16px 20px;display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;font-size:12px;color:#8393a5}
    .ml-alert{padding:12px 15px;background:#e9f9ef;color:#176744;border:1px solid #b9e8cd;border-radius:10px;margin-bottom:15px;font-size:12px}
    @media(max-width:1100px){.ml-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:680px){.ml-hero{padding:20px}.ml-hero-icon{display:none}.ml-hero h1{font-size:22px}.ml-stats{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.ml-stat{padding:12px;gap:8px}.ml-stat-icon{width:35px;height:35px;font-size:16px}.ml-stat strong{font-size:22px}.ml-panel-head{padding:17px}.ml-filter{width:100%}.ml-input{flex:1 1 100%;width:100%}.ml-select{flex:1 1 140px;max-width:none}.ml-footer{padding:14px}}
</style>

<div class="ml-page">
    <header class="ml-hero">
        <div>
            <h1>Monitoring Logbook Mahasiswa</h1>
            <p>Pantau catatan kegiatan harian mahasiswa magang, tinjau laporan yang masuk, dan lihat penempatan mahasiswa berdasarkan divisi, tim kerja, serta pembimbing.</p>
        </div>
        <div class="ml-hero-icon"><i class="bi bi-journal-check"></i></div>
    </header>

    @if(session('success'))
        <div class="ml-alert" role="status">{{ session('success') }}</div>
    @endif

    <section class="ml-stats" aria-label="Statistik logbook">
        <article class="ml-stat"><div class="ml-stat-icon"><i class="bi bi-people"></i></div><div><span>Mahasiswa Diterima</span><strong>{{ number_format($statistik['mahasiswa'] ?? 0, 0, ',', '.') }}</strong></div></article>
        <article class="ml-stat"><div class="ml-stat-icon"><i class="bi bi-journals"></i></div><div><span>Total Logbook</span><strong>{{ number_format($statistik['total'] ?? 0, 0, ',', '.') }}</strong></div></article>
        <article class="ml-stat"><div class="ml-stat-icon"><i class="bi bi-clock-history"></i></div><div><span>Menunggu Tinjauan</span><strong>{{ number_format($statistik['menunggu'] ?? 0, 0, ',', '.') }}</strong></div></article>
        <article class="ml-stat"><div class="ml-stat-icon"><i class="bi bi-check-circle"></i></div><div><span>Sudah Ditinjau</span><strong>{{ number_format($statistik['ditinjau'] ?? 0, 0, ',', '.') }}</strong></div></article>
    </section>

    <section class="ml-panel">
        <div class="ml-panel-head">
            <div>
                <h2>Daftar Mahasiswa Magang</h2>
                <p>Mahasiswa yang pengajuannya diterima dan dapat mengisi logbook.</p>
            </div>
            <form action="{{ route('admin.logbook.index') }}" method="GET" class="ml-filter">
                <input class="ml-input" type="search" name="q" aria-label="Cari mahasiswa" placeholder="Nama, NIM, kampus..." value="{{ request('q') }}">
                <select class="ml-select" name="divisi_id" aria-label="Filter divisi">
                    <option value="">Semua Divisi</option>
                    @foreach($divisis as $divisi)
                        <option value="{{ $divisi->id }}" @selected((string)request('divisi_id') === (string)$divisi->id)>{{ $divisi->nama }}</option>
                    @endforeach
                </select>
                <select class="ml-select" name="pengisian" aria-label="Filter pengisian">
                    <option value="">Semua Pengisian</option>
                    <option value="sudah" @selected(request('pengisian') === 'sudah')>Sudah Mengisi</option>
                    <option value="belum" @selected(request('pengisian') === 'belum')>Belum Mengisi</option>
                </select>
                <button class="ml-btn" type="submit"><i class="bi bi-search"></i> Cari</button>
                @if(request()->filled('q') || request()->filled('divisi_id') || request()->filled('pengisian'))
                    <a class="ml-btn ml-btn-light" href="{{ route('admin.logbook.index') }}">Reset</a>
                @endif
            </form>
        </div>
        <div class="ml-tablewrap">
            <table class="ml-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Mahasiswa</th>
                        <th>Divisi / Tim Kerja</th>
                        <th>Pembimbing</th>
                        <th>Total Kegiatan</th>
                        <th>Menunggu</th>
                        <th>Pengisian</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daftar as $pengajuan)
                        @php
                            $mhs = $pengajuan->user?->mahasiswa;
                            $nama = $mhs?->nama ?: ($pengajuan->user?->name ?? 'Mahasiswa');
                            $jumlah = $jumlahLogbook->get($pengajuan->id);
                            $totalKegiatan = (int) ($jumlah?->total ?? 0);
                            $menungguKegiatan = (int) ($jumlah?->menunggu ?? 0);
                        @endphp
                        <tr>
                            <td>{{ ($daftar->firstItem() ?? 1) + $loop->index }}</td>
                            <td>
                                <div class="ml-student">
                                    <div class="ml-avatar">{{ mb_strtoupper(mb_substr($nama, 0, 1)) }}</div>
                                    <div>
                                        <div class="ml-name">{{ $nama }}</div>
                                        <div class="ml-sub">NIM: {{ $mhs?->nim ?: '—' }}</div>
                                        <div class="ml-sub">{{ $mhs?->perguruan_tinggi ?: 'Kampus belum diisi' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="ml-name">{{ $pengajuan->divisi?->nama ?: ($pengajuan->bidang ?: 'Belum ditentukan') }}</div>
                                <div class="ml-sub">{{ $pengajuan->timKerja?->nama ?: 'Tim kerja belum ditentukan' }}</div>
                            </td>
                            <td>{{ $pengajuan->pembimbingRef?->nama ?: ($pengajuan->pembimbing ?: '—') }}</td>
                            <td><span class="ml-count">{{ $totalKegiatan }}</span></td>
                            <td>
                                @if($menungguKegiatan > 0)
                                    <span class="ml-badge ml-badge-wait">{{ $menungguKegiatan }} perlu ditinjau</span>
                                @else
                                    <span class="ml-badge ml-badge-neutral">0</span>
                                @endif
                            </td>
                            <td>
                                <span class="ml-badge {{ $totalKegiatan > 0 ? 'ml-badge-ok' : 'ml-badge-neutral' }}">
                                    {{ $totalKegiatan > 0 ? 'Sudah Mengisi' : 'Belum Mengisi' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.logbook.show', $pengajuan->user_id) }}" class="ml-btn ml-btn-light">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="ml-empty" colspan="8"><i class="bi bi-inbox"></i>Belum ada mahasiswa magang yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="ml-footer">
            <span>Menampilkan {{ $daftar->count() }} dari {{ $daftar->total() }} data mahasiswa magang.</span>
            @if($daftar->hasPages())<div>{{ $daftar->links() }}</div>@endif
        </div>
    </section>
</div>
@endsection
