@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIMASTA')
@section('page-title', 'Dashboard Mahasiswa')

@section('sidebar')
    @include('layouts.mahasiswa')
@endsection

@section('user-role')
    Mahasiswa
@endsection

@section('user-avatar')
    {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
@endsection

@section('page-description', 'Selamat datang di Sistem Informasi Manajemen Magang BPS Kota Batu')

@section('content')

@php
    $pengajuan = auth()->user()->pengajuan;
    $status    = $pengajuan->status ?? 'belum_diajukan';

    $labelStatus = [
        'belum_diajukan' => 'Belum Diajukan',
        'diproses'       => 'Sedang Diproses',
        'diverifikasi'   => 'Berkas Terverifikasi',
        'diterima'       => 'Diterima',
        'ditolak'        => 'Ditolak',
    ][$status] ?? 'Belum Diajukan';

    $catatanStatus = [
        'belum_diajukan' => 'Silakan isi form Pengajuan Magang terlebih dahulu.',
        'diproses'       => 'Pengajuan kamu sedang menunggu pemeriksaan admin.',
        'diverifikasi'   => 'Berkas sudah diperiksa, menunggu keputusan seleksi.',
        'diterima'       => 'Selamat, pengajuan magang kamu diterima.',
        'ditolak'        => 'Pengajuan belum dapat diterima.',
    ][$status] ?? '';

    // warna badge status
    $badgeClass = match ($status) {
        'diterima' => 'ok',
        'ditolak'  => 'bad',
        default    => 'pending',
    };

    $statusMagang = $status === 'diterima' ? 'Aktif' : 'Belum Aktif';

    // Data detail (aman kalau kolom kosong)
    $instansi = $pengajuan->instansi ?? null;
    $periode  = '-';
    if (!empty($pengajuan->periode_mulai) && !empty($pengajuan->periode_selesai)) {
        $periode = \Carbon\Carbon::parse($pengajuan->periode_mulai)->translatedFormat('d M Y')
                 . ' - ' .
                   \Carbon\Carbon::parse($pengajuan->periode_selesai)->translatedFormat('d M Y');
    }

    // Placeholder sampai modul logbook & penilaian dibuat
    $totalLogbook = 0;
    $nilaiAkhir   = null;

    // Stepper: index tahap yang sedang berjalan
    $aktif = match ($status) {
        'belum_diajukan'      => 0,
        'diproses'            => 1,
        'diverifikasi'        => 2,
        'diterima', 'ditolak' => 3,
        default               => 0,
    };

    $steps = [
        ['Pengajuan',         'Isi form & upload dokumen'],
        ['Verifikasi Berkas', 'Admin memeriksa berkas'],
        ['Seleksi',           'Penentuan oleh BPS'],
        ['Keputusan',         $status === 'ditolak' ? 'Ditolak' : ($status === 'diterima' ? 'Diterima' : 'Diterima / Ditolak')],
    ];
@endphp

<style>
    .student-dashboard{display:flex; flex-direction:column; gap:24px}

    /* sapaan */
    .student-welcome{
        position:relative; overflow:hidden; padding:30px; border-radius:18px;
        color:#16324f; border:1px solid #dbeafe;
        background:linear-gradient(120deg,#d9f7eb 0%,#e8f1ff 60%,#dce9ff 100%);
    }
    .student-welcome::after{
        content:""; position:absolute; width:180px; height:180px; right:-45px; top:-70px;
        border-radius:50%; background:rgba(245,166,91,.18);
    }
    .welcome-label{
        display:inline-block; padding:6px 12px; margin-bottom:14px; border-radius:20px;
        color:#18794e; background:rgba(255,255,255,.8);
        font-size:12px; font-weight:700; letter-spacing:.5px;
    }
    .student-welcome h1{margin:0 0 10px; font-size:clamp(23px,3vw,30px); line-height:1.35; color:#183153}
    .student-welcome p{margin:0; color:#52677f; line-height:1.7}

    .student-section-title{margin:0 0 5px; color:#1e293b; font-size:20px}
    .student-section-description{margin:0 0 20px; color:#64748b; font-size:14px; line-height:1.6}

    /* stepper */
    .stepper{display:flex; background:#fff; border:1px solid #e5eaf1; border-radius:16px;
        padding:26px 20px; box-shadow:0 4px 14px rgba(15,23,42,.035)}
    .step{flex:1; text-align:center; position:relative; min-width:0; padding:0 6px}
    .step:not(:last-child)::after{
        content:''; position:absolute; top:20px; left:50%; width:100%; height:4px;
        background:#e3e8ef; z-index:0;
    }
    .step.done:not(:last-child)::after{background:#2fa266}
    .step-dot{
        position:relative; z-index:1; width:42px; height:42px; margin:0 auto 10px;
        border-radius:50%; display:grid; place-items:center; font-weight:700;
        background:#e3e8ef; color:#8794a6;
    }
    .step.done .step-dot{background:#2fa266; color:#fff}
    .step.current .step-dot{background:#f5a65b; color:#fff; box-shadow:0 0 0 6px rgba(245,166,91,.25)}
    .step.rejected .step-dot{background:#d64545; color:#fff}
    .step b{display:block; font-size:14px; color:#1d5fa8}
    .step small{font-size:12px; color:#7a8696}

    /* banner hasil */
    .student-alert{padding:16px 20px; border-radius:14px; background:#fff; border:1px solid #e5eaf1;
        border-left-width:5px; color:#334155; line-height:1.6}
    .student-alert.ok{border-left-color:#2fa266}
    .student-alert.bad{border-left-color:#d64545}
    .student-alert.info{border-left-color:#f5a65b}

    /* statistik */
    .student-stat-grid{display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px}
    .student-stat-card{
        min-width:0; padding:22px; background:#fff; border:1px solid #e5eaf1;
        border-radius:16px; box-shadow:0 4px 14px rgba(15,23,42,.035);
    }
    .student-stat-top{display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:18px}
    .student-stat-icon{display:grid; place-items:center; width:44px; height:44px; border-radius:13px; font-size:21px}
    .student-stat-icon.blue{color:#1d5fa8; background:#eaf2fc}
    .student-stat-icon.green{color:#168653; background:#e9f9ef}
    .student-stat-icon.orange{color:#c77716; background:#fff4df}
    .student-stat-icon.purple{color:#7c3aed; background:#f3e8ff}
    .student-stat-label{color:#64748b; font-size:13px; font-weight:600}
    .student-stat-value{margin:0 0 8px; color:#1e293b; font-size:21px; font-weight:700; overflow-wrap:anywhere}
    .student-stat-note{margin:0; color:#94a3b8; font-size:12px; line-height:1.5}

    /* tabel */
    .student-panel{
        min-width:0; padding:25px; background:#fff; border:1px solid #e5eaf1;
        border-radius:16px; box-shadow:0 4px 14px rgba(15,23,42,.035);
    }
    .student-table-wrap{width:100%; overflow-x:auto}
    .student-table{width:100%; border-collapse:collapse; min-width:520px}
    .student-table th{
        padding:14px 16px; background:#f8fafc; color:#64748b; text-align:left;
        font-size:12px; text-transform:uppercase; letter-spacing:.4px; border-bottom:1px solid #e2e8f0;
    }
    .student-table td{padding:16px; color:#334155; font-size:14px; border-bottom:1px solid #edf2f7}
    .student-table tr:last-child td{border-bottom:none}

    .student-status{display:inline-flex; align-items:center; padding:6px 10px; border-radius:20px;
        font-size:12px; font-weight:650; white-space:nowrap}
    .student-status.pending{color:#a16207; background:#fef3c7}
    .student-status.neutral{color:#475569; background:#f1f5f9}
    .student-status.ok{color:#166534; background:#dcfce7}
    .student-status.bad{color:#991b1b; background:#fee2e2}

    /* akses cepat */
    .student-actions{display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px}
    .student-action{
        display:flex; align-items:flex-start; gap:13px; padding:18px; color:inherit;
        text-decoration:none; background:#fff; border:1px solid #e5eaf1; border-radius:14px;
        transition:transform .2s, box-shadow .2s, border-color .2s;
    }
    .student-action:hover{transform:translateY(-3px); border-color:#b7d4fa; box-shadow:0 8px 20px rgba(29,95,168,.1)}
    .student-action-icon{
        display:grid; place-items:center; flex:0 0 42px; width:42px; height:42px;
        border-radius:12px; color:#1d5fa8; background:#eaf2fc; font-size:20px;
    }
    .student-action strong{display:block; margin:2px 0 6px; color:#1e293b; font-size:14px}
    .student-action span{display:block; color:#64748b; font-size:12px; line-height:1.6}

    @media (max-width:1100px){
        .student-stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
        .student-actions{grid-template-columns:repeat(2,minmax(0,1fr))}
    }
    @media (max-width:640px){
        .student-welcome,.student-panel{padding:20px}
        .student-stat-grid,.student-actions{grid-template-columns:1fr}
        .student-stat-card{padding:18px}
        .stepper{flex-direction:column; gap:18px}
        .step{display:flex; align-items:center; gap:14px; text-align:left}
        .step:not(:last-child)::after{display:none}
        .step-dot{margin:0; flex-shrink:0}
    }
</style>

<div class="student-dashboard">

    {{-- SAPAAN --}}
    <section class="student-welcome">
        <span class="welcome-label">SIMASTA · BPS KOTA BATU</span>
        <h1>Selamat Datang, {{ auth()->user()->name ?? 'Mahasiswa' }} 👋</h1>
        <p>Pantau pengajuan dan kelola kegiatan magangmu melalui satu dashboard SIMASTA.</p>
    </section>

    {{-- TAHAPAN PENGAJUAN --}}
    <section>
        <h2 class="student-section-title">Tahapan Pengajuan</h2>
        <p class="student-section-description">Pantau sampai tahap mana pengajuan magang kamu berjalan.</p>

        <div class="stepper">
            @foreach($steps as $i => $s)
                @php
                    $cls = '';
                    if ($status === 'diterima' && $i === 3)       $cls = 'done';
                    elseif ($status === 'ditolak' && $i === 3)    $cls = 'rejected';
                    elseif ($i < $aktif)                          $cls = 'done';
                    elseif ($i === $aktif)                        $cls = 'current';
                @endphp
                <div class="step {{ $cls }}">
                    <div class="step-dot">
                        @if($cls === 'done') <i class="bi bi-check-lg"></i>
                        @elseif($cls === 'rejected') <i class="bi bi-x-lg"></i>
                        @else {{ $i + 1 }}
                        @endif
                    </div>
                    <div>
                        <b>{{ $s[0] }}</b>
                        <small>{{ $s[1] }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- BANNER STATUS --}}
    @if($status === 'diterima')
        <div class="student-alert ok">
            🎉 Selamat! Pengajuan magang kamu <b>diterima</b>. Menu Presensi, Logbook, dan Penilaian sudah terbuka.
        </div>
    @elseif($status === 'ditolak')
        <div class="student-alert bad">
            Mohon maaf, pengajuan magang kamu belum dapat kami terima.
            @if(!empty($pengajuan->catatan_admin))
                <br><b>Catatan admin:</b> {{ $pengajuan->catatan_admin }}
            @endif
        </div>
    @elseif($status === 'belum_diajukan')
        <div class="student-alert info">
            Kamu belum mengajukan magang. Mulai dari menu <b>Pengajuan Magang</b>.
        </div>
    @endif

    {{-- STATISTIK --}}
    <section>
        <h2 class="student-section-title">Ringkasan Magang</h2>
        <p class="student-section-description">Informasi singkat mengenai proses magang kamu saat ini.</p>

        <div class="student-stat-grid">

            <article class="student-stat-card">
                <div class="student-stat-top">
                    <span class="student-stat-label">Status Pengajuan</span>
                    <div class="student-stat-icon blue"><i class="bi bi-send"></i></div>
                </div>
                <h3 class="student-stat-value">{{ $labelStatus }}</h3>
                <p class="student-stat-note">{{ $catatanStatus }}</p>
            </article>

            <article class="student-stat-card">
                <div class="student-stat-top">
                    <span class="student-stat-label">Status Magang</span>
                    <div class="student-stat-icon green"><i class="bi bi-check2-circle"></i></div>
                </div>
                <h3 class="student-stat-value">{{ $statusMagang }}</h3>
                <p class="student-stat-note">
                    {{ $status === 'diterima' ? 'Kamu sudah terdaftar sebagai peserta magang.' : 'Informasi aktif setelah pengajuan disetujui.' }}
                </p>
            </article>

            <article class="student-stat-card">
                <div class="student-stat-top">
                    <span class="student-stat-label">Total Logbook</span>
                    <div class="student-stat-icon orange"><i class="bi bi-journal-text"></i></div>
                </div>
                <h3 class="student-stat-value">{{ $totalLogbook }} Entri</h3>
                <p class="student-stat-note">Jumlah catatan kegiatan magang yang tersimpan.</p>
            </article>

            <article class="student-stat-card">
                <div class="student-stat-top">
                    <span class="student-stat-label">Nilai Akhir</span>
                    <div class="student-stat-icon purple"><i class="bi bi-star"></i></div>
                </div>
                <h3 class="student-stat-value">{{ $nilaiAkhir ?? 'Belum Ada' }}</h3>
                <p class="student-stat-note">Nilai ditampilkan setelah penilaian tersedia.</p>
            </article>

        </div>
    </section>

    {{-- INFORMASI MAGANG --}}
    <section class="student-panel">
        <h2 class="student-section-title">Informasi Magang</h2>
        <p class="student-section-description">Ringkasan instansi, periode, dan pembimbing magang.</p>

        <div class="student-table-wrap">
            <table class="student-table">
                <thead>
                    <tr>
                        <th>Informasi</th>
                        <th>Detail</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Instansi</td>
                        <td>{{ $instansi ?: 'BPS Kota Batu' }}</td>
                        <td>
                            <span class="student-status {{ $badgeClass }}">{{ $labelStatus }}</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Periode Magang</td>
                        <td>{{ $periode }}</td>
                        <td>
                            <span class="student-status {{ $periode !== '-' ? 'ok' : 'neutral' }}">
                                {{ $periode !== '-' ? 'Tersedia' : 'Belum tersedia' }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td>Pembimbing Lapangan</td>
                        <td>Belum ditentukan</td>
                        <td>
                            <span class="student-status neutral">Belum ditetapkan</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- AKSES CEPAT --}}
    <section>
        <h2 class="student-section-title">Akses Cepat</h2>
        <p class="student-section-description">Pilih menu untuk melanjutkan proses magang.</p>

        <div class="student-actions">

            <a class="student-action" href="{{ url('/mahasiswa/pengajuan') }}">
                <div class="student-action-icon"><i class="bi bi-send-plus"></i></div>
                <div>
                    <strong>Pengajuan Magang</strong>
                    <span>Isi dan pantau pengajuan magang.</span>
                </div>
            </a>

            <a class="student-action" href="{{ url('/mahasiswa/dokumen') }}">
                <div class="student-action-icon"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <strong>Dokumen Magang</strong>
                    <span>Periksa dokumen persyaratan magang.</span>
                </div>
            </a>

            <a class="student-action" href="{{ url('/mahasiswa/profil') }}">
                <div class="student-action-icon"><i class="bi bi-person"></i></div>
                <div>
                    <strong>Profil Mahasiswa</strong>
                    <span>Periksa dan lengkapi informasi diri.</span>
                </div>
            </a>

        </div>
    </section>

</div>

@endsection