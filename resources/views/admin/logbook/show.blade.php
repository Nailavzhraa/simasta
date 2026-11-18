@extends('layouts.admin')

@section('title', 'Detail Logbook - SIMASTA')
@section('page-title', 'Detail Logbook')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')
@php
    $profil = $mahasiswa->mahasiswa;
    $totalLogbook = (int) ($total ?? 0);
    $totalMenunggu = (int) ($menunggu ?? 0);
    $totalDitinjau = (int) ($ditinjau ?? max(0, $totalLogbook - $totalMenunggu));
    $namaMahasiswa = $profil?->nama ?: $mahasiswa->name;
    $inisial = mb_strtoupper(mb_substr(trim($namaMahasiswa), 0, 1));
    $mulai = $pengajuan->periode_mulai;
    $selesai = $pengajuan->periode_selesai;
@endphp

<style>
    .ld-page { font-family:Arial,Helvetica,sans-serif; color:#24364e; }
    .ld-page * { box-sizing:border-box; }
    .ld-page a { text-decoration:none; }
    .ld-hero { padding:23px 25px; margin-bottom:17px; border:1px solid #dce8f4; border-radius:15px; background:linear-gradient(115deg,#fff 0%,#eef6ff 64%,#e8f7f0 100%); }
    .ld-back { display:inline-flex; align-items:center; gap:7px; color:#2563eb; font-size:12px; font-weight:700; margin-bottom:18px; }
    .ld-back:hover { color:#174ea6; }
    .ld-hero-row { display:flex; align-items:center; gap:14px; }
    .ld-avatar { width:53px; height:53px; display:grid; place-items:center; flex-shrink:0; border-radius:15px; background:#dbeafe; color:#1d4ed8; font-size:22px; font-weight:800; }
    .ld-eyebrow { margin:0 0 5px; font-size:10px; font-weight:800; letter-spacing:.09em; text-transform:uppercase; color:#2563eb; }
    .ld-hero h1 { margin:0; color:#163f71; font-size:24px; line-height:1.25; overflow-wrap:anywhere; }
    .ld-hero p { margin:7px 0 0; font-size:12px; color:#5d7592; }
    .ld-metadata { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:11px; margin-top:21px; }
    .ld-meta { padding:12px 13px; border:1px solid #dde9f5; border-radius:10px; background:rgba(255,255,255,.8); min-width:0; }
    .ld-meta-label { font-size:10px; color:#7e90a6; margin-bottom:6px; }
    .ld-meta-value { font-size:12px; color:#27466a; font-weight:700; line-height:1.5; overflow-wrap:anywhere; }
    .ld-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:13px; margin-bottom:17px; }
    .ld-stat { padding:17px; display:flex; align-items:center; gap:12px; background:#fff; border:1px solid #e2e8f0; border-radius:13px; }
    .ld-stat-icon { width:39px; height:39px; flex-shrink:0; display:grid; place-items:center; border-radius:11px; font-size:19px; background:#eaf2ff; color:#2563eb; }
    .ld-stat-icon.orange { background:#fff1df; color:#c47708; }
    .ld-stat-icon.green { background:#e4f9ed; color:#09855c; }
    .ld-stat-label { font-size:11px; color:#64748b; margin:0 0 5px; }
    .ld-stat-number { font-weight:800; font-size:24px; line-height:1.1; color:#17375d; }
    .ld-panel { padding:21px; border:1px solid #e2e8f0; border-radius:14px; background:#fff; }
    .ld-panel-top { display:flex; align-items:flex-start; justify-content:space-between; gap:15px; flex-wrap:wrap; margin-bottom:17px; }
    .ld-panel h2 { font-size:16px; color:#163f71; margin:0 0 6px; }
    .ld-panel-sub { margin:0; font-size:11px; color:#8a9bb0; line-height:1.6; }
    .ld-filter { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .ld-select { height:38px; min-width:160px; padding:0 11px; border:1px solid #dce5ef; background:#fbfdff; color:#334155; border-radius:9px; font:12px Arial,sans-serif; }
    .ld-btn { display:inline-flex; align-items:center; justify-content:center; min-height:38px; padding:9px 14px; border:1px solid transparent; border-radius:9px; background:#2563eb; color:#fff!important; font:700 12px Arial,sans-serif; cursor:pointer; }
    .ld-btn:hover { background:#1d4ed8; }
    .ld-btn-outline { background:#fff; border-color:#dce5ef; color:#52667e!important; }
    .ld-btn-outline:hover { background:#f3f7fb; }
    .ld-alert { padding:12px 15px; margin-bottom:15px; border-radius:10px; font-size:12px; line-height:1.6; }
    .ld-alert-success { background:#ecfdf3; border:1px solid #bdebd0; color:#08734a; }
    .ld-alert-error { background:#fff1f2; border:1px solid #fecdd3; color:#a42e40; }
    .ld-entry { padding:18px; border:1px solid #e2e9f1; border-radius:12px; margin-bottom:13px; background:#fff; }
    .ld-entry:last-of-type { margin-bottom:0; }
    .ld-entry-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; padding-bottom:14px; margin-bottom:15px; border-bottom:1px solid #eef2f7; }
    .ld-date { font-size:14px; font-weight:800; color:#163f71; }
    .ld-date-sub { margin-top:5px; font-size:11px; color:#8b9aab; }
    .ld-badge { display:inline-flex; align-items:center; gap:6px; padding:6px 11px; border-radius:99px; font-size:10px; font-weight:800; white-space:nowrap; background:#fff4e7; color:#ae6a0d; }
    .ld-badge::before { content:''; background:currentColor; width:6px; height:6px; border-radius:50%; }
    .ld-badge-ok { background:#e4f9ed; color:#0a845a; }
    .ld-fields { display:grid; grid-template-columns:1fr 1fr; gap:13px 18px; }
    .ld-content-main { grid-column:1/-1; }
    .ld-content strong { display:block; margin-bottom:6px; color:#315172; font-size:11px; }
    .ld-content p { margin:0; color:#586b81; font-size:12px; line-height:1.75; white-space:pre-wrap; overflow-wrap:anywhere; }
    .ld-review { margin-top:17px; padding-top:16px; border-top:1px solid #edf2f7; }
    .ld-review-top { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:10px; }
    .ld-review-title { font-size:12px; color:#163f71; font-weight:800; }
    .ld-review-date { color:#889aaf; font-size:10px; }
    .ld-review label { display:block; color:#485f79; font-size:11px; margin-bottom:7px; font-weight:700; }
    .ld-review textarea { display:block; width:100%; min-height:90px; border:1px solid #dce5ef; border-radius:9px; padding:11px 12px; font:12px/1.6 Arial,sans-serif; resize:vertical; color:#334155; background:#fcfdff; }
    .ld-review textarea:focus, .ld-select:focus { border-color:#2563eb; outline:2px solid rgba(37,99,235,.12); }
    .ld-review-foot { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-top:10px; }
    .ld-hint { color:#8b9aae; font-size:11px; line-height:1.5; }
    .ld-empty { text-align:center; padding:40px 15px; color:#91a0b0; font-size:12px; line-height:1.7; }
    .ld-empty strong { display:block; color:#315172; font-size:14px; margin-bottom:6px; }
    .ld-pagination { display:flex; justify-content:space-between; align-items:center; gap:15px; flex-wrap:wrap; padding-top:17px; font-size:11px; color:#889aaf; }
    @media(max-width:1100px) { .ld-metadata { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media(max-width:700px) { .ld-hero { padding:18px; } .ld-hero h1 { font-size:20px; } .ld-stats { grid-template-columns:1fr; gap:9px; } .ld-stat { padding:12px; } .ld-metadata { grid-template-columns:1fr 1fr; } .ld-panel { padding:15px; } .ld-fields { grid-template-columns:1fr; } .ld-content-main { grid-column:auto; } .ld-filter { width:100%; } .ld-select { flex:1; min-width:0; } .ld-review-foot .ld-btn { width:100%; } }
    @media(max-width:440px) { .ld-metadata { grid-template-columns:1fr; } .ld-hero-row { align-items:flex-start; } .ld-avatar { width:44px; height:44px; font-size:17px; } }
</style>

<div class="ld-page">
    <header class="ld-hero">
        <a class="ld-back" href="{{ route('admin.logbook.index') }}">&larr; Kembali ke Monitoring Logbook</a>
        <div class="ld-hero-row">
            <div class="ld-avatar" aria-hidden="true">{{ $inisial ?: 'M' }}</div>
            <div>
                <p class="ld-eyebrow">Monitoring Logbook Mahasiswa</p>
                <h1>{{ $namaMahasiswa }}</h1>
                <p>{{ $profil?->nim ?: $mahasiswa->email }} &middot; {{ $profil?->perguruan_tinggi ?: 'Data perguruan tinggi belum tersedia' }}</p>
            </div>
        </div>
        <div class="ld-metadata">
            <div class="ld-meta"><div class="ld-meta-label">Program Studi</div><div class="ld-meta-value">{{ $profil?->program_studi ?: '—' }}</div></div>
            <div class="ld-meta"><div class="ld-meta-label">Divisi / Tim Kerja</div><div class="ld-meta-value">{{ $pengajuan->divisi?->nama ?: '—' }}@if($pengajuan->timKerja?->nama)<br>{{ $pengajuan->timKerja->nama }}@endif</div></div>
            <div class="ld-meta"><div class="ld-meta-label">Pembimbing Lapangan</div><div class="ld-meta-value">{{ $pengajuan->pembimbingRef?->nama ?: '—' }}</div></div>
            <div class="ld-meta"><div class="ld-meta-label">Periode Magang</div><div class="ld-meta-value">{{ $mulai ? \Carbon\Carbon::parse($mulai)->format('d M Y') : '—' }} – {{ $selesai ? \Carbon\Carbon::parse($selesai)->format('d M Y') : '—' }}</div></div>
        </div>
    </header>

    @if(session('success'))
        <div class="ld-alert ld-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="ld-alert ld-alert-error" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="ld-alert ld-alert-error" role="alert">{{ $errors->first() }}</div>
    @endif

    <section class="ld-stats" aria-label="Statistik logbook mahasiswa">
        <div class="ld-stat"><div class="ld-stat-icon" aria-hidden="true">▤</div><div><p class="ld-stat-label">Total Logbook</p><div class="ld-stat-number">{{ number_format($totalLogbook, 0, ',', '.') }}</div></div></div>
        <div class="ld-stat"><div class="ld-stat-icon orange" aria-hidden="true">◷</div><div><p class="ld-stat-label">Menunggu Tinjauan</p><div class="ld-stat-number">{{ number_format($totalMenunggu, 0, ',', '.') }}</div></div></div>
        <div class="ld-stat"><div class="ld-stat-icon green" aria-hidden="true">✓</div><div><p class="ld-stat-label">Sudah Ditinjau</p><div class="ld-stat-number">{{ number_format($totalDitinjau, 0, ',', '.') }}</div></div></div>
    </section>

    <section class="ld-panel">
        <div class="ld-panel-top">
            <div><h2>Riwayat Kegiatan Harian</h2><p class="ld-panel-sub">Periksa kegiatan harian mahasiswa dan berikan catatan tinjauan yang dapat dibaca mahasiswa.</p></div>
            <form class="ld-filter" method="GET" action="{{ route('admin.logbook.show', $mahasiswa) }}">
                <label for="ld-status" class="ld-hint">Status</label>
                <select id="ld-status" class="ld-select" name="status">
                    <option value="">Semua status</option>
                    <option value="menunggu" @selected(request('status') === 'menunggu')>Menunggu tinjauan</option>
                    <option value="ditinjau" @selected(request('status') === 'ditinjau')>Sudah ditinjau</option>
                </select>
                <button class="ld-btn" type="submit">Filter</button>
                @if(request()->filled('status'))
                    <a class="ld-btn ld-btn-outline" href="{{ route('admin.logbook.show', $mahasiswa) }}">Reset</a>
                @endif
            </form>
        </div>

        @forelse($logbooks as $logbook)
            <article class="ld-entry">
                <div class="ld-entry-head">
                    <div>
                        <div class="ld-date">{{ $logbook->tanggal ? $logbook->tanggal->translatedFormat('l, d F Y') : 'Tanggal tidak tersedia' }}</div>
                        <div class="ld-date-sub">Logbook #{{ $logbook->id }}@if($logbook->created_at) &middot; Dicatat {{ $logbook->created_at->format('d/m/Y H:i') }}@endif</div>
                    </div>
                    <span class="ld-badge {{ $logbook->status === 'ditinjau' ? 'ld-badge-ok' : '' }}">{{ $logbook->status === 'ditinjau' ? 'Sudah Ditinjau' : 'Menunggu Tinjauan' }}</span>
                </div>
                <div class="ld-fields">
                    <div class="ld-content ld-content-main"><strong>Deskripsi Aktivitas</strong><p>{{ $logbook->kegiatan }}</p></div>
                    <div class="ld-content ld-content-main"><strong>Capaian</strong><p>{{ $logbook->capaian ?: '—' }}</p></div>
                    <div class="ld-content ld-content-main"><strong>Bukti Foto Google Drive</strong>
                        @php $hostBukti = strtolower((string) parse_url($logbook->link_bukti ?? '', PHP_URL_HOST)); @endphp
                        @if(in_array($hostBukti, ['drive.google.com', 'docs.google.com'], true) && str_starts_with($logbook->link_bukti, 'https://'))
                            <p><a href="{{ $logbook->link_bukti }}" target="_blank" rel="noopener noreferrer" style="color:#1d5fa8;font-weight:700">Buka Bukti Kegiatan di Google Drive ↗</a></p>
                        @else
                            <p>— (logbook lama tanpa link bukti)</p>
                        @endif
                    </div>
                </div>
                <form class="ld-review" method="POST" action="{{ route('admin.logbook.tinjau', $logbook) }}">
                    @csrf
                    <div class="ld-review-top">
                        <span class="ld-review-title">Peninjauan Admin</span>
                        @if($logbook->ditinjau_at)
                            <span class="ld-review-date">Ditinjau pada {{ $logbook->ditinjau_at->translatedFormat('d M Y, H:i') }}</span>
                        @endif
                    </div>
                    <label for="ld-note-{{ $logbook->id }}">Catatan atau masukan untuk mahasiswa (opsional)</label>
                    <textarea id="ld-note-{{ $logbook->id }}" name="catatan_admin" maxlength="2000" placeholder="Contoh: Kegiatan sudah sesuai. Harap tambahkan detail hasil pekerjaan pada hari berikutnya.">{{ $logbook->catatan_admin }}</textarea>
                    <div class="ld-review-foot">
                        <span class="ld-hint">Maksimal 2.000 karakter. Menyimpan akan menandai kegiatan sebagai ditinjau.</span>
                        <button class="ld-btn" type="submit">{{ $logbook->status === 'ditinjau' ? 'Perbarui Catatan' : 'Tandai Sudah Ditinjau' }}</button>
                    </div>
                </form>
            </article>
        @empty
            <div class="ld-empty"><strong>Belum ada logbook</strong>Tidak ada kegiatan yang sesuai dengan filter saat ini.</div>
        @endforelse

        @if($logbooks->total() > 0)
            <div class="ld-pagination"><span>Menampilkan {{ $logbooks->firstItem() }}–{{ $logbooks->lastItem() }} dari {{ $logbooks->total() }} kegiatan</span>@if($logbooks->hasPages())<div>{{ $logbooks->links() }}</div>@endif</div>
        @endif
    </section>
</div>
@endsection
