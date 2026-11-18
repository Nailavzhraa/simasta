{{-- resources/views/mahasiswa/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIMASTA')
@section('page-title', 'Dashboard Mahasiswa')
@section('sidebar')
    @include('layouts.mahasiswa')
@endsection
@section('user-role', 'Mahasiswa')
@section('user-avatar')
    {{ mb_strtoupper(mb_substr(auth()->user()->name ?? 'M', 0, 1)) }}
@endsection
@section('page-description', 'Sistem Informasi Manajemen Magang BPS Kota Batu')

@section('content')
@php
    // Ambil pengajuan terbaru milik akun login, bukan pengajuan akun lain.
    $user = auth()->user();
    $pengajuan = \App\Models\Pengajuan::query()
        ->where('user_id', $user->id)
        ->latest('id')
        ->first();

    // Draft dan pengajuan resmi disimpan pada tabel berbeda.
    $draft = \App\Models\PengajuanDraft::query()
        ->where('user_id', $user->id)
        ->first();
    $draftData = is_array($draft?->form_data) ? $draft->form_data : [];

    if ($pengajuan) {
        $pengajuan->loadMissing(['divisi', 'pembimbingRef', 'timKerja']);
    }

    $status = $pengajuan?->status ?? 'belum_diajukan';
    $diterima = $status === 'diterima';
    $ditolak = $status === 'ditolak';
    $pengajuanAktif = in_array($status, ['diproses', 'diverifikasi', 'diterima'], true);
    $punyaDraft = !$pengajuanAktif && (bool) $draft;

    $labelStatus = [
        'belum_diajukan' => $punyaDraft ? 'Draft Tersimpan' : 'Belum Diajukan',
        'diproses' => 'Menunggu Verifikasi',
        'diverifikasi' => 'Berkas Terverifikasi',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ][$status] ?? 'Dalam Proses';

    $keteranganStatus = [
        'belum_diajukan' => $punyaDraft
            ? 'Isian sementara sudah tersimpan. Lanjutkan formulir dan kirim pengajuan saat lengkap.'
            : 'Isi formulir serta tautan Google Drive dokumen persyaratan.',
        'diproses' => 'Pengajuan terkirim dan menunggu pemeriksaan Admin.',
        'diverifikasi' => 'Berkas telah diverifikasi dan menunggu keputusan BPS Kota Batu.',
        'diterima' => 'Pengajuan telah diterima oleh BPS Kota Batu.',
        'ditolak' => 'Pengajuan ditolak. Baca catatan Admin dan ajukan kembali bila diperbolehkan.',
    ][$status] ?? 'Pantau perkembangan pengajuan melalui dashboard.';

    $tahapan = [
        ['judul' => 'Isi Form Pengajuan', 'detail' => 'Identitas, akademik, dan periode', 'ikon' => 'bi-pencil-square'],
        ['judul' => 'Dokumen Persyaratan', 'detail' => 'Tautan dokumen Google Drive', 'ikon' => 'bi-folder2-open'],
        ['judul' => 'Verifikasi Berkas', 'detail' => 'Pemeriksaan oleh Admin BPS', 'ikon' => 'bi-patch-check'],
        ['judul' => 'Peninjauan Pengajuan', 'detail' => 'Menunggu keputusan BPS', 'ikon' => 'bi-clipboard-check'],
        ['judul' => 'Keputusan Pengajuan', 'detail' => $diterima ? 'Pengajuan diterima' : ($ditolak ? 'Pengajuan ditolak' : 'Diterima atau ditolak'), 'ikon' => 'bi-megaphone'],
    ];

    // Dua tahap pengisian hanya dinilai lengkap jika field draft benar-benar ada.
    $isianDasar = ['nama', 'nim', 'perguruan_tinggi', 'program_studi', 'tahun_angkatan', 'semester', 'tanggal_mulai', 'durasi_bulan', 'tujuan_magang'];
    $draftFormLengkap = collect($isianDasar)->every(fn ($field) => filled($draftData[$field] ?? null));
    $draftDokumenLengkap = collect(['link_cv', 'link_surat', 'link_proposal'])
        ->every(fn ($field) => filled($draftData[$field] ?? null));

    // 0=belum selesai; 1-5=jumlah tahap yang tuntas. Tahap 3 tidak menyala
    // sebelum submit resmi meskipun form dan dokumen draft sudah lengkap.
    $tahapSelesai = 0;
    $tahapBerlangsung = 1;
    if ($pengajuanAktif || $ditolak) {
        $tahapSelesai = match ($status) {
            'diproses' => 2,
            'diverifikasi' => 3,
            'diterima', 'ditolak' => 5,
            default => 0,
        };
        $tahapBerlangsung = match ($status) {
            'diproses' => 3,
            'diverifikasi' => 4,
            default => 0,
        };
    } elseif ($punyaDraft) {
        $tahapSelesai = $draftFormLengkap ? 1 : 0;
        if ($draftFormLengkap && $draftDokumenLengkap) {
            $tahapSelesai = 2;
            $tahapBerlangsung = 0; // Menunggu mahasiswa klik Kirim Pengajuan
        } else {
            $tahapBerlangsung = $draftFormLengkap ? 2 : 1;
        }
    }

    $namaDivisi = $pengajuan?->divisi?->nama ?: $pengajuan?->bidang;
    $namaTimKerja = $pengajuan?->timKerja?->nama;
    $namaPembimbing = $pengajuan?->pembimbingRef?->nama ?: $pengajuan?->pembimbing;
    $jobdeskPenempatan = $pengajuan?->jobdesk;
    $sudahDitempatkan = filled($namaDivisi) || filled($namaTimKerja) || filled($namaPembimbing);

    $statusPelaksanaan = $pengajuan?->status_pelaksanaan ?? 'belum_mulai';
    $statusMagang = !$diterima ? 'Belum Aktif' : match ($statusPelaksanaan) {
        'berlangsung' => 'Sedang Berlangsung',
        'selesai' => 'Selesai Magang',
        default => 'Diterima — Menunggu Mulai',
    };
    $totalLogbook = $diterima ? $pengajuan->logbooks()->count() : null;

    // Kompatibilitas dokumen: link Google Drive baru ATAU PDF lokal lama.
    $dokumen = [
        ['nama' => 'Surat Pengantar', 'ada' => filled($pengajuan?->link_surat) || filled($pengajuan?->file_surat)],
        ['nama' => 'Curriculum Vitae (CV)', 'ada' => filled($pengajuan?->link_cv) || filled($pengajuan?->file_cv)],
        ['nama' => 'Proposal Magang', 'ada' => filled($pengajuan?->link_proposal) || filled($pengajuan?->file_proposal)],
    ];
    $dokumenTerisi = collect($dokumen)->where('ada', true)->count();
@endphp

<style>
    .sd-page{--sd-blue:#1e5e9e;--sd-navy:#173b61;--sd-green:#159b76;--sd-orange:#f39a3f;--sd-border:#e1e9f1;--sd-muted:#64748b;display:flex;flex-direction:column;gap:20px;color:#20354c}
    .sd-page *{box-sizing:border-box}
    .sd-welcome{position:relative;overflow:hidden;border:1px solid #d4e5f1;border-radius:17px;background:linear-gradient(110deg,#dcf6e9 0%,#e5f1fb 58%,#dfe9ff 100%);padding:24px 27px}
    .sd-welcome:after{content:"";position:absolute;width:160px;height:160px;border-radius:50%;right:-45px;top:-78px;background:rgba(242,151,60,.13);pointer-events:none}
    .sd-eyebrow{display:inline-block;background:rgba(255,255,255,.85);color:#147a59;font-weight:750;letter-spacing:.35px;font-size:10px;border-radius:99px;padding:6px 11px;margin-bottom:10px}
    .sd-welcome h1{font-size:clamp(21px,2.5vw,27px);line-height:1.35;margin:0 0 7px;color:#163b61}
    .sd-welcome p{font-size:13px;color:#5b7086;line-height:1.6;margin:0}
    .sd-section-head{margin:0 0 12px}.sd-section-head h2{font-size:18px;color:#173b61;margin:0 0 5px}.sd-section-head p{font-size:12px;color:#718096;line-height:1.6;margin:0}
    .sd-apply{border-radius:15px;padding:20px 23px;color:#fff;display:flex;justify-content:space-between;align-items:center;gap:18px;background:linear-gradient(110deg,#1d609f,#397fcb);position:relative;overflow:hidden}
    .sd-apply:after{content:"";position:absolute;width:170px;height:170px;border:1px solid rgba(255,255,255,.17);border-radius:50%;right:-45px;bottom:-115px;pointer-events:none}
    .sd-apply.is-submitted{background:linear-gradient(110deg,#168269,#2a9e8b)}
    .sd-apply-content{position:relative;z-index:1}.sd-apply-kicker{font-size:10px;font-weight:750;letter-spacing:.8px;opacity:.85;text-transform:uppercase}.sd-apply h2{color:#fff;font-size:18px;margin:6px 0}.sd-apply p{font-size:12px;opacity:.91;line-height:1.65;margin:0;max-width:600px}
    .sd-apply-link{position:relative;z-index:1;flex-shrink:0;display:inline-flex;align-items:center;gap:7px;padding:11px 15px;border-radius:9px;background:#fff;color:#1e5e9e;font-size:12px;font-weight:750;text-decoration:none}.sd-apply-link:hover{background:#edf6ff}
    .sd-panel{background:#fff;border:1px solid var(--sd-border);border-radius:16px;padding:20px 22px;box-shadow:0 3px 12px rgba(25,67,102,.025)}
    .sd-steps{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:0;padding:13px 0 5px}
    .sd-step{position:relative;min-width:0;text-align:center;padding:0 8px}
    .sd-step:not(:last-child):before{content:"";position:absolute;top:21px;left:50%;width:100%;height:4px;background:#e7edf3;z-index:0}
    .sd-step.is-done:not(:last-child):before{background:#21a479}
    .sd-light{position:relative;z-index:1;margin:0 auto 12px;display:grid;place-items:center;width:45px;height:45px;border-radius:50%;font-size:16px;font-weight:750;background:#e8edf3;color:#8190a2;border:4px solid #fff;box-shadow:0 0 0 1px #e4eaf1}
    .sd-step.is-done .sd-light{background:#16a477;color:#fff;box-shadow:0 0 0 3px rgba(22,164,119,.13),0 0 15px rgba(22,164,119,.19)}
    .sd-step.is-current .sd-light{background:#f39235;color:#fff;box-shadow:0 0 0 4px rgba(243,146,53,.18),0 0 18px rgba(243,146,53,.23)}
    .sd-step.is-rejected .sd-light{background:#df5656;color:#fff;box-shadow:0 0 0 4px rgba(223,86,86,.16)}
    .sd-step h3{font-size:12px;line-height:1.4;margin:0 0 5px;color:#2c4868;font-weight:750}.sd-step p{font-size:10px;line-height:1.5;margin:0;color:#8190a1}
    .sd-step.is-current h3{color:#c97924}.sd-step.is-done h3{color:#147c5f}.sd-step.is-rejected h3{color:#bf4545}
    .sd-legend{border-top:1px solid #edf1f5;margin-top:15px;padding-top:13px;display:flex;flex-wrap:wrap;gap:15px;color:#738398;font-size:11px}.sd-legend span{display:inline-flex;align-items:center;gap:6px}.sd-legend i{width:9px;height:9px;border-radius:50%;display:inline-block}
    .sd-alert{border-radius:12px;padding:13px 16px;font-size:12px;line-height:1.65;border:1px solid #e4eaf1;border-left:4px solid #f39235;background:#fff}.sd-alert.ok{border-left-color:#159b76;background:#f1fbf7}.sd-alert.bad{border-left-color:#d95c5c;background:#fff7f7}.sd-alert.info{border-left-color:#2a79bc;background:#f4f9fe}.sd-alert strong{color:#193c5e}.sd-alert .sd-note{margin-top:7px;padding-top:7px;border-top:1px solid rgba(100,116,139,.16)}
    .sd-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
    .sd-stat{min-width:0;border:1px solid #e3eaf1;background:#fff;border-radius:13px;padding:16px;min-height:135px}.sd-stat.is-locked{background:#f8fafc}.sd-stat-top{display:flex;align-items:center;justify-content:space-between;gap:6px;margin-bottom:13px}.sd-stat-label{font-size:11px;color:#63758b;font-weight:650}.sd-stat-icon{width:33px;height:33px;border-radius:10px;display:grid;place-items:center;font-size:15px;background:#e8f2fd;color:#2878bb}.sd-stat-icon.green{background:#e4f7ed;color:#159b76}.sd-stat-icon.orange{background:#fff0df;color:#df8b27}.sd-stat-icon.gray{background:#eef1f5;color:#98a6b6}.sd-stat-value{font-size:16px;line-height:1.35;color:#173b61;font-weight:750;margin:0 0 7px;overflow-wrap:anywhere}.sd-stat.is-locked .sd-stat-value{color:#95a2b2}.sd-stat-desc{font-size:11px;line-height:1.55;color:#8a99aa;margin:0}
    .sd-bottom{display:grid;grid-template-columns:1fr 1fr;gap:14px}.sd-bottom .sd-panel{height:100%}.sd-bottom h3{font-size:15px;color:#173b61;margin:0 0 7px}.sd-bottom p{font-size:12px;color:#728298;line-height:1.6;margin:0}
    .sd-docs{margin-top:13px;display:grid;gap:8px}.sd-doc{display:flex;align-items:center;gap:9px;padding:10px 11px;border-radius:9px;border:1px solid #e8edf3;font-size:12px;color:#4e6178}.sd-doc i{font-size:15px;color:#b4c0cc}.sd-doc.is-ready i{color:#159b76}.sd-doc small{margin-left:auto;color:#8a99aa;font-size:10px;text-align:right}
    .sd-next{background:linear-gradient(135deg,#eef5fc,#f0f9f5);border-color:#dce9f2}.sd-next-icon{width:37px;height:37px;border-radius:11px;background:#dcebf9;color:#2169a6;display:grid;place-items:center;font-size:18px;margin-bottom:13px}.sd-next p{margin-top:8px}
    .sd-placement-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .sd-placement-item{border:1px solid #e3eaf1;border-radius:11px;padding:14px 16px;background:#f9fbfe;min-width:0}
    .sd-placement-label{font-size:11px;color:#64748b;margin-bottom:7px;display:flex;align-items:center;gap:7px}
    .sd-placement-value{font-size:14px;font-weight:750;color:#173b61;overflow-wrap:anywhere;line-height:1.55}
    .sd-placement-full{grid-column:1/-1}
    .sd-placement-desc{font-size:12px;color:#718096;line-height:1.65;margin:0 0 15px}
    .sd-placement-jobdesk{white-space:pre-line;font-weight:500}

    /* Pesan status berada di dalam kartu pengajuan, bukan pop-up terpisah. */
    .sd-apply{align-items:stretch;flex-wrap:wrap}
    .sd-apply-content{flex:1 1 500px}
    .sd-apply-action{position:relative;z-index:1;display:flex;align-items:center}
    /* Notifikasi ringkas di dalam card pengajuan, tanpa kotak besar */
    .sd-apply-inline{position:relative;z-index:1;width:100%;display:flex;align-items:center;gap:8px;font-size:11px;line-height:1.5;color:#f3f9ff;border-top:1px solid rgba(255,255,255,.22);padding-top:11px}
    .sd-apply-inline i{font-size:14px;flex-shrink:0}
    .sd-apply-inline strong{font-weight:750}
    .sd-apply-admin-note{position:relative;z-index:1;width:100%;font-size:11px;line-height:1.55;padding:9px 12px;background:rgba(255,255,255,.12);border-radius:8px}
    .sd-apply.is-rejected{background:linear-gradient(110deg,#b45353,#d16a60)}
    .sd-apply.is-waiting{background:linear-gradient(110deg,#206399,#367dc2)}
    .sd-step.is-rejected:not(:last-child):before{background:#16a477}
    @media(max-width:600px){.sd-placement-grid{grid-template-columns:1fr}}
    @media(max-width:1050px){.sd-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:760px){.sd-steps{grid-template-columns:1fr;gap:10px}.sd-step{text-align:left;display:grid;grid-template-columns:45px 1fr;column-gap:12px;align-items:center;padding:0}.sd-light{grid-row:1/3;margin:0}.sd-step:not(:last-child):before{top:36px;left:21px;width:4px;height:calc(100% + 10px)}.sd-step h3{margin:0}.sd-step p{grid-column:2}.sd-bottom{grid-template-columns:1fr}.sd-apply{align-items:flex-start;flex-direction:column}}
    @media(max-width:520px){.sd-page{gap:16px}.sd-welcome{padding:20px}.sd-panel{padding:17px}.sd-stats{grid-template-columns:1fr}.sd-stat{min-height:unset}.sd-legend{gap:10px}}
</style>

<div class="sd-page">
    {{-- 1. SAMBUTAN --}}
    <section class="sd-welcome">
        <span class="sd-eyebrow"><i class="bi bi-building"></i>SIMASTA · BPS KOTA BATU</span>
        <h1>Selamat Datang, {{ $user->name ?? 'Mahasiswa' }} 👋</h1>
        <p>Pantau proses pengajuan dan kelola informasi magang melalui dashboard SIMASTA.</p>
    </section>

    {{-- 2. AKSI PENGAJUAN + NOTIFIKASI STATUS DALAM SATU KARTU --}}
    <section class="sd-apply {{ $ditolak ? 'is-rejected' : ($diterima ? 'is-submitted' : ($pengajuanAktif ? 'is-waiting' : '')) }}">
        <div class="sd-apply-content">
            <div class="sd-apply-kicker">
                {{ $diterima ? 'Pengajuan diterima' : ($ditolak ? 'Keputusan pengajuan' : ($pengajuanAktif ? 'Pengajuan tersimpan' : ($punyaDraft ? 'Draft tersimpan otomatis' : 'Langkah pertama'))) }}
            </div>
            <h2>
                {{ $diterima ? 'Selamat, Pengajuan Diterima!' : ($ditolak ? 'Pengajuan Memerlukan Tindak Lanjut' : ($pengajuanAktif ? 'Pengajuan Magang Sudah Dikirim' : ($punyaDraft ? 'Lanjutkan Form Pengajuan Magang' : 'Isi Form Pengajuan Magang'))) }}
            </h2>
            <p>
                {{ $diterima ? 'Informasi penempatan dan pelaksanaan magang dapat kamu pantau di dashboard ini.' : ($ditolak ? 'Periksa catatan Admin dan perbaiki pengajuan sebelum mencoba kembali.' : ($pengajuanAktif ? 'Pengajuan telah tercatat dan sedang melalui proses pemeriksaan BPS Kota Batu.' : 'Isi formulir lima tahap dan tautan dokumen Google Drive. Isian akan tersimpan otomatis sebagai draft.')) }}
            </p>
        </div>
        <div class="sd-apply-action">
            <a href="{{ route('mahasiswa.dokumen') }}" class="sd-apply-link">
                <i class="bi {{ $pengajuanAktif ? 'bi-folder2-open' : 'bi-arrow-right-circle' }}"></i>
                {{ $ditolak ? 'Ajukan Kembali' : ($pengajuanAktif ? 'Lihat Pengajuan' : ($punyaDraft ? 'Lanjutkan Pengisian' : 'Mulai Pengajuan')) }}
            </a>
        </div>
        <div class="sd-apply-inline" role="status">
            <i class="bi {{ $diterima ? 'bi-check-circle-fill' : ($ditolak ? 'bi-exclamation-circle-fill' : ($pengajuanAktif ? 'bi-clock-history' : ($punyaDraft ? 'bi-cloud-check' : 'bi-info-circle'))) }}"></i>
            <span>
                @if($diterima)
                    <strong>Diterima.</strong> Pengajuan magang sudah disetujui.
                @elseif($ditolak)
                    <strong>Ditolak.</strong> Periksa catatan Admin sebelum mengajukan kembali.
                @elseif($status === 'diverifikasi')
                    <strong>Terverifikasi.</strong> Menunggu keputusan Admin.
                @elseif($status === 'diproses')
                    <strong>Terkirim.</strong> Pengajuan sedang diperiksa Admin.
                @elseif($punyaDraft)
                    <strong>Draft tersimpan.</strong> Kamu bisa melanjutkan formulir kapan saja.
                @else
                    <strong>Info:</strong> Isian formulir akan tersimpan otomatis sebagai draft.
                @endif
            </span>
        </div>
        @if($ditolak && filled($pengajuan?->catatan_admin))
            <div class="sd-apply-admin-note"><strong>Catatan Admin:</strong> {{ $pengajuan->catatan_admin }}</div>
        @endif
    </section>

    {{-- 3. TAHAPAN PENGAJUAN --}}
    <section>
        <div class="sd-section-head">
            <h2>Tahapan Pengajuan Magang</h2>
            <p>Indikator akan menyala mengikuti perkembangan pengajuan yang tercatat di sistem.</p>
        </div>
        <div class="sd-panel">
            <div class="sd-steps" aria-label="Progres pengajuan magang">
                @foreach($tahapan as $index => $tahap)
                    @php
                        $nomor = $index + 1;
                        $kelas = $nomor <= $tahapSelesai ? 'is-done' : ($nomor === $tahapBerlangsung ? 'is-current' : '');
                        if ($nomor === 5 && $ditolak) $kelas = 'is-rejected';
                    @endphp
                    <div class="sd-step {{ $kelas }}" aria-label="Tahap {{ $nomor }}: {{ $tahap['judul'] }}">
                        <div class="sd-light">
                            @if($kelas === 'is-done')
                                <i class="bi bi-check-lg"></i>
                            @elseif($kelas === 'is-rejected')
                                <i class="bi bi-x-lg"></i>
                            @elseif($kelas === 'is-current')
                                <i class="bi {{ $tahap['ikon'] }}"></i>
                            @else
                                {{ $nomor }}
                            @endif
                        </div>
                        <h3>{{ $tahap['judul'] }}</h3>
                        <p>{{ $tahap['detail'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="sd-legend">
                <span><i style="background:#16a477"></i> Selesai</span>
                <span><i style="background:#f39235"></i> Sedang berlangsung</span>
                <span><i style="background:#d4dce5"></i> Belum dimulai</span>
                @if($ditolak)<span><i style="background:#df5656"></i> Ditolak</span>@endif
            </div>
        </div>
    </section>

    {{-- 5. RINGKASAN MAGANG --}}
    <section>
        <div class="sd-section-head">
            <h2>Ringkasan Magang</h2>
            <p>Informasi terbaru terkait pengajuan dan kegiatan magang kamu.</p>
        </div>
        <div class="sd-stats">
            <article class="sd-stat">
                <div class="sd-stat-top"><span class="sd-stat-label">Status Pengajuan</span><span class="sd-stat-icon"><i class="bi bi-send"></i></span></div>
                <h3 class="sd-stat-value">{{ $labelStatus }}</h3>
                <p class="sd-stat-desc">{{ $keteranganStatus }}</p>
            </article>
            <article class="sd-stat">
                <div class="sd-stat-top"><span class="sd-stat-label">Status Magang</span><span class="sd-stat-icon green"><i class="bi bi-briefcase"></i></span></div>
                <h3 class="sd-stat-value">{{ $statusMagang }}</h3>
                <p class="sd-stat-desc">{{ !$diterima ? 'Pelaksanaan magang belum dimulai.' : ($statusPelaksanaan === 'selesai' ? 'Pelaksanaan magang telah selesai.' : ($statusPelaksanaan === 'berlangsung' ? 'Magang sedang berjalan.' : 'Menunggu konfirmasi tanggal mulai dari Admin.')) }}</p>
            </article>
            <article class="sd-stat {{ !$diterima ? 'is-locked' : '' }}">
                <div class="sd-stat-top"><span class="sd-stat-label">Total Logbook</span><span class="sd-stat-icon {{ $diterima ? 'orange' : 'gray' }}"><i class="bi {{ $diterima ? 'bi-journal-text' : 'bi-lock' }}"></i></span></div>
                <h3 class="sd-stat-value">{{ $diterima ? ($totalLogbook ?? '—') : 'Terkunci' }}</h3>
                <p class="sd-stat-desc">{{ $diterima ? 'Jumlah catatan kegiatan yang tersimpan.' : 'Tersedia setelah pengajuan diterima.' }}</p>
            </article>
            <article class="sd-stat {{ !$diterima ? 'is-locked' : '' }}">
                <div class="sd-stat-top"><span class="sd-stat-label">Nilai Akhir</span><span class="sd-stat-icon gray"><i class="bi bi-lock"></i></span></div>
                <h3 class="sd-stat-value">{{ $diterima ? 'Lihat Menu Penilaian' : 'Terkunci' }}</h3>
                <p class="sd-stat-desc">{{ $diterima ? 'Periksa hasil penilaian dan sertifikat pada menu Penilaian.' : 'Tersedia setelah pengajuan diterima.' }}</p>
            </article>
        </div>
    </section>

    {{-- 6. INFORMASI PENEMPATAN MAGANG --}}
    <section id="informasi-penempatan">
        <div class="sd-section-head">
            <h2>Informasi Penempatan Magang</h2>
            <p>Divisi, tim kerja, dan pembimbing yang ditetapkan oleh Admin BPS Kota Batu.</p>
        </div>
        <div class="sd-panel">
            @if(!$diterima)
                <div class="sd-alert info">
                    Informasi penempatan akan tersedia setelah pengajuan magang diterima dan Admin menetapkan penempatan.
                </div>
            @elseif(!$sudahDitempatkan)
                <div class="sd-alert info">
                    Pengajuan kamu sudah diterima. Penempatan divisi dan pembimbing masih menunggu pengaturan dari Admin.
                </div>
            @else
                <p class="sd-placement-desc">Informasi ini mengikuti data penempatan terbaru yang disimpan Admin.</p>
                <div class="sd-placement-grid">
                    <div class="sd-placement-item">
                        <div class="sd-placement-label"><i class="bi bi-diagram-3"></i> Divisi / Bagian</div>
                        <div class="sd-placement-value">{{ $namaDivisi ?: 'Belum ditentukan' }}</div>
                    </div>
                    <div class="sd-placement-item">
                        <div class="sd-placement-label"><i class="bi bi-people"></i> Tim Kerja</div>
                        <div class="sd-placement-value">{{ $namaTimKerja ?: 'Belum ditentukan' }}</div>
                    </div>
                    <div class="sd-placement-item">
                        <div class="sd-placement-label"><i class="bi bi-person-badge"></i> Pembimbing Lapangan</div>
                        <div class="sd-placement-value">{{ $namaPembimbing ?: 'Belum ditentukan' }}</div>
                    </div>
                    <div class="sd-placement-item">
                        <div class="sd-placement-label"><i class="bi bi-calendar-event"></i> Periode Magang</div>
                        <div class="sd-placement-value">
                            {{ filled($pengajuan?->periode_mulai) ? \Illuminate\Support\Carbon::parse($pengajuan->periode_mulai)->format('d/m/Y') : '—' }}
                            –
                            {{ filled($pengajuan?->periode_selesai) ? \Illuminate\Support\Carbon::parse($pengajuan->periode_selesai)->format('d/m/Y') : '—' }}
                        </div>
                    </div>
                    <div class="sd-placement-item sd-placement-full">
                        <div class="sd-placement-label"><i class="bi bi-list-task"></i> Jobdesk Magang</div>
                        <div class="sd-placement-value sd-placement-jobdesk">{{ $jobdeskPenempatan ?: 'Belum ditentukan' }}</div>
                    </div>
                </div>
            @endif
        </div>
    </section>

@endsection
