@extends('layouts.app')

@section('title', 'Pengajuan Magang - SIMASTA')
@section('page-title', 'Pengajuan Magang')
@section('user-role', 'Mahasiswa')
@section('user-avatar', strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)))

@section('sidebar')
    @include('layouts.mahasiswa')
@endsection

@section('content')
@php
    $mahasiswa = $mahasiswa ?? auth()->user()->mahasiswa;
    $status = $pengajuan?->status;
    $aktif = in_array($status, ['diproses', 'diverifikasi', 'diterima'], true);
    $statusLabels = [
        'diproses' => 'Menunggu Pemeriksaan',
        'diverifikasi' => 'Sudah Diverifikasi',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $draftData = $draft?->form_data ?? [];
    $draftStep = max(1, min(5, (int) ($draft?->current_step ?? 1)));
    $prefill = function ($key, $default = '') use ($draftData) {
        return old($key, data_get($draftData, $key, $default));
    };
@endphp

<style>
    .pmw, .pmw *{box-sizing:border-box}
    .pmw{--blue:#1d5fa8;--deep:#12457f;--green:#169a68;--orange:#ee943b;--ink:#18334f;--muted:#6c8199;--line:#dbe6f1;color:var(--ink);max-width:1200px;margin:0 auto;font-family:inherit}
    .pmw-hero{background:linear-gradient(110deg,#fff 0%,#f2f8ff 58%,#e8f8f0 100%);border:1px solid #d9e8f4;border-radius:18px;padding:24px 28px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:20px}
    .pmw-eyebrow{display:block;font-size:11px;font-weight:800;letter-spacing:1.1px;color:var(--blue);margin-bottom:9px}
    .pmw-hero h1{font-size:27px;line-height:1.25;color:#103e74;margin:0 0 7px}
    .pmw-hero p{font-size:13px;line-height:1.65;color:#597593;margin:0;max-width:760px}
    .pmw-hero-icon{width:55px;height:55px;display:grid;place-items:center;background:#dbeeff;color:var(--blue);border-radius:14px;font-size:25px;flex-shrink:0}
    .pmw-alert{padding:13px 16px;border-radius:11px;margin-bottom:16px;font-size:13px;line-height:1.6}
    .pmw-alert-success{background:#e7f8ed;color:#17653e;border:1px solid #b4e2c5}
    .pmw-alert-danger{background:#fff0f1;color:#9d2635;border:1px solid #ffcbd1}
    .pmw-alert-info{background:#eef6ff;color:#164f88;border:1px solid #caddf5}
    .pmw-alert-warning{background:#fff8e9;color:#8d5b13;border:1px solid #f4db9a}
    .pmw-alert ul{margin:5px 0 0;padding-left:20px}
    .pmw-card{background:#fff;border:1px solid var(--line);border-radius:18px;overflow:hidden;box-shadow:0 5px 24px rgba(25,62,104,.035)}
    .pmw-card-header{display:flex;align-items:center;justify-content:space-between;gap:15px;padding:19px 24px;border-bottom:1px solid #edf1f6}
    .pmw-card-header h2{margin:0 0 4px;font-size:19px;color:#123e70}
    .pmw-card-header p{margin:0;color:var(--muted);font-size:12px}
    .pmw-pill{font-size:11px;font-weight:800;border:1px solid #cee1f4;color:var(--blue);background:#f0f7ff;border-radius:100px;padding:7px 12px;white-space:nowrap}
    .pmw-steps{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:8px;padding:23px 25px 17px}
    .pmw-step{border:0;background:transparent;padding:0 3px;cursor:pointer;min-width:0;color:#8494a8;font:inherit;text-align:center}
    .pmw-step-line{height:5px;border-radius:20px;background:#dce4ed;margin-bottom:13px}
    .pmw-step-dot{width:34px;height:34px;margin:0 auto 8px;border-radius:50%;background:#e7ecf3;color:#73859a;display:grid;place-items:center;font-size:13px;font-weight:800;transition:.2s}
    .pmw-step-label{font-size:11px;font-weight:700;line-height:1.35;display:block;min-height:28px}
    .pmw-step.is-current .pmw-step-line,.pmw-step.is-current .pmw-step-dot{background:var(--orange);color:#fff}
    .pmw-step.is-complete .pmw-step-line,.pmw-step.is-complete .pmw-step-dot{background:var(--green);color:#fff}
    .pmw-step.is-current .pmw-step-label{color:#b97325}
    .pmw-step.is-complete .pmw-step-label{color:#16875b}
    .pmw-legend{display:flex;gap:17px;flex-wrap:wrap;padding:0 25px 19px;font-size:11px;color:#74869a}
    .pmw-legend span{display:inline-flex;align-items:center;gap:6px}
    .pmw-legend i{width:8px;height:8px;border-radius:50%;background:#d4dee9}
    .pmw-legend .done i{background:var(--green)}.pmw-legend .current i{background:var(--orange)}
    .pmw-panel{padding:25px;border-top:1px solid #edf1f6;min-height:335px}
    .pmw-panel[hidden]{display:none!important}
    .pmw-panel-title{display:flex;align-items:center;gap:12px;margin-bottom:22px}
    .pmw-panel-icon{width:43px;height:43px;border-radius:12px;display:grid;place-items:center;background:#e9f3ff;color:var(--blue);font-size:20px;flex-shrink:0}
    .pmw-panel-title h3{font-size:18px;margin:0 0 4px;color:#183e68}
    .pmw-panel-title p{font-size:12px;color:var(--muted);margin:0;line-height:1.5}
    .pmw-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:19px 22px}
    .pmw-field{min-width:0}.pmw-full{grid-column:1/-1}
    .pmw-field label{display:block;font-size:12px;font-weight:750;color:#344c66;margin-bottom:8px}
    .pmw-req{color:#e58b32}
    .pmw-field input,.pmw-field select,.pmw-field textarea{display:block;width:100%;border:1px solid #d4e0ec;border-radius:10px;padding:12px 13px;min-height:46px;font:inherit;font-size:13px;background:#fff;color:#233c54}
    .pmw-field textarea{resize:vertical;min-height:135px;line-height:1.6}
    .pmw-field input:focus,.pmw-field select:focus,.pmw-field textarea:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 3px rgba(29,95,168,.1)}
    .pmw-field input[readonly]{background:#f3f6fa;color:#5d748b}
    .pmw-help{font-size:11px;color:#8191a3;line-height:1.55;margin:7px 0 0}
    .pmw-period-result{padding:18px 20px;background:linear-gradient(110deg,#f0f7ff,#f0fbf5);border:1px solid #d7e9ef;border-radius:12px;grid-column:1/-1}
    .pmw-period-result small{display:block;font-size:11px;color:#67839c;margin-bottom:7px}
    .pmw-period-result strong{font-size:22px;color:#145487}
    .pmw-drive-note{padding:13px 16px;background:#eef6ff;color:#375b7e;font-size:12px;border-radius:11px;line-height:1.6;margin-bottom:18px}
    .pmw-doc-list{display:grid;gap:15px}
    .pmw-doc{display:flex;align-items:flex-start;gap:14px;padding:17px;background:#fafcff;border:1px solid #e0e9f3;border-radius:13px}
    .pmw-doc-icon{width:40px;height:40px;border-radius:10px;background:#fff0df;color:#d98a36;display:grid;place-items:center;font-size:20px;flex-shrink:0}
    .pmw-doc-main{flex:1;min-width:0}
    .pmw-doc-main strong{display:block;font-size:13px;margin-bottom:9px}
    .pmw-doc-main input{width:100%;font-size:12px}
    .pmw-doc-main a{font-size:11px;color:var(--blue);display:inline-block;margin-top:8px;text-decoration:none}
    .pmw-footer{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:19px 25px;border-top:1px solid #edf1f6;background:#fbfdff}
    .pmw-save{font-size:12px;color:#6c8199;display:flex;align-items:center;gap:7px;line-height:1.4}
    .pmw-save.is-good{color:#16875b}.pmw-save.is-bad{color:#aa3e36}
    .pmw-btns{display:flex;gap:10px;align-items:center}
    .pmw-btn{border:1px solid #d2e0ed;background:#fff;color:var(--blue);border-radius:10px;padding:12px 17px;font-size:12px;font-weight:800;cursor:pointer}
    .pmw-btn.primary{background:var(--blue);border-color:var(--blue);color:#fff}
    .pmw-btn.primary:hover{background:var(--deep)}.pmw-btn:disabled{opacity:.5;cursor:not-allowed}
    .pmw-status-box{padding:25px}.pmw-status-tag{display:inline-block;background:#eaf3ff;color:#1d5fa8;border-radius:50px;font-size:12px;font-weight:800;padding:8px 13px;margin:12px 0}
    .pmw-status-tag.accepted{background:#ddf8e8;color:#14794e}.pmw-status-tag.rejected{background:#fff0ef;color:#b03434}
    .pmw-status-box h3{font-size:18px;color:#193f6b;margin:0 0 6px}
    .pmw-status-box p{font-size:13px;color:#657b91;line-height:1.65}
    .pmw-review{background:#f6f9fc;padding:15px;border-radius:12px;border:1px solid #e1eaf3;font-size:12px;line-height:1.8;margin-top:15px}
    .pmw-review dt{font-weight:800;color:#183c66}.pmw-review dd{margin:0 0 8px;color:#556a80;overflow-wrap:anywhere}
    @media(max-width:750px){.pmw-hero{padding:20px}.pmw-hero-icon{display:none}.pmw-hero h1{font-size:23px}.pmw-card-header{padding:17px}.pmw-steps{padding:20px 11px 15px;gap:3px}.pmw-step-dot{width:29px;height:29px}.pmw-step-label{font-size:9px}.pmw-legend{padding:0 14px 16px}.pmw-panel{padding:19px 16px}.pmw-grid{grid-template-columns:1fr}.pmw-full,.pmw-period-result{grid-column:auto}.pmw-doc{padding:12px}.pmw-footer{flex-wrap:wrap;padding:16px}.pmw-save{width:100%}.pmw-btns{width:100%;justify-content:flex-end}}
</style>

<div class="pmw">
    <header class="pmw-hero">
        <div>
            <h1>Pengajuan Magang</h1>
            <p>Lengkapi pengajuan dalam lima langkah. Progres pengisian disimpan otomatis, sehingga kamu dapat melanjutkannya nanti.</p>
        </div>
        <div class="pmw-hero-icon"><i class="bi bi-file-earmark-text"></i></div>
    </header>

    @if(session('success'))
        <div class="pmw-alert pmw-alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="pmw-alert pmw-alert-info" role="status">{{ session('info') }}</div>
    @endif
    @if($errors->any())
        <div class="pmw-alert pmw-alert-danger" role="alert">
            <strong>Periksa kembali formulir:</strong>
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if($aktif)
        <section class="pmw-card">
            <div class="pmw-card-header"><div><h2>Pengajuan Telah Tercatat</h2><p>Pengajuan aktif tidak dapat dikirim ulang.</p></div><span class="pmw-pill">Status Pengajuan</span></div>
            <div class="pmw-status-box">
                <h3>Pengajuan magangmu sedang ditangani</h3>
                <span class="pmw-status-tag {{ $status === 'diterima' ? 'accepted' : '' }}">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                <p>Periode: {{ $pengajuan->periode_mulai?->format('d/m/Y') ?? '-' }} — {{ $pengajuan->periode_selesai?->format('d/m/Y') ?? '-' }}</p>
                <p>{{ $status === 'diterima' ? 'Selamat! Pengajuan kamu diterima. Silakan lihat informasi penempatan dan layanan magang yang tersedia.' : 'Silakan menunggu pemeriksaan Admin BPS Kota Batu. Status pengajuan akan diperbarui melalui sistem.' }}</p>
            </div>
        </section>
    @else
        @if($status === 'ditolak')
            <div class="pmw-alert pmw-alert-warning"><strong>Pengajuan sebelumnya ditolak.</strong> Kamu dapat mengajukan kembali setelah memperbaiki persyaratan.
                @if($pengajuan?->catatan_admin)<div style="margin-top:7px">Catatan Admin: {{ $pengajuan->catatan_admin }}</div>@endif
            </div>
        @endif

        <form id="formPengajuan" method="POST" action="{{ route('mahasiswa.dokumen.store') }}" novalidate>
            @csrf
            <section class="pmw-card">
                <div class="pmw-card-header">
                    <div><h2>Formulir Pengajuan Magang</h2><p>Isi data per tahap, lalu periksa kembali sebelum mengirim.</p></div>
                    <span class="pmw-pill" id="stepBadge">Tahap 1 dari 5</span>
                </div>
                <nav class="pmw-steps" aria-label="Tahapan pengisian">
                    @foreach(['Data Mahasiswa', 'Informasi Akademik', 'Periode Magang', 'Dokumen Persyaratan', 'Tujuan & Keterangan'] as $i => $label)
                        <button type="button" class="pmw-step {{ $i === 0 ? 'is-current' : '' }}" data-step-nav="{{ $i + 1 }}" aria-label="Tahap {{ $i + 1 }}: {{ $label }}">
                            <div class="pmw-step-line"></div>
                            <div class="pmw-step-dot">{{ $i + 1 }}</div>
                            <span class="pmw-step-label">{{ $label }}</span>
                        </button>
                    @endforeach
                </nav>
                <div class="pmw-legend"><span class="done"><i></i> Selesai</span><span class="current"><i></i> Sedang diisi</span><span><i></i> Belum diisi</span></div>

                <section class="pmw-panel" data-panel="1">
                    <div class="pmw-panel-title"><div class="pmw-panel-icon"><i class="bi bi-person-vcard"></i></div><div><h3>Data Mahasiswa</h3><p>Lengkapi identitas sesuai data mahasiswa.</p></div></div>
                    <div class="pmw-grid">
                        <div class="pmw-field"><label for="nama">Nama lengkap <span class="pmw-req">*</span></label><input id="nama" name="nama" type="text" maxlength="255" required value="{{ $prefill('nama', $mahasiswa?->nama ?? auth()->user()->name ?? '') }}" autocomplete="name"></div>
                        <div class="pmw-field"><label for="nim">NIM <span class="pmw-req">*</span></label><input id="nim" name="nim" type="text" maxlength="255" required value="{{ $prefill('nim', $mahasiswa?->nim ?? '') }}"></div>
                        <div class="pmw-field"><label for="email">Email akun</label><input id="email" type="email" readonly value="{{ auth()->user()->email }}"><p class="pmw-help">Email mengikuti akun login dan tidak diubah melalui formulir ini.</p></div>
                        <div class="pmw-field"><label for="no_wa">Nomor HP / WhatsApp</label><input id="no_wa" name="no_wa" type="tel" maxlength="20" value="{{ $prefill('no_wa', $mahasiswa?->no_wa ?: ($mahasiswa?->no_telepon ?? '')) }}" placeholder="08xxxxxxxxxx"><p class="pmw-help">Gunakan satu nomor aktif yang bisa dihubungi Admin BPS Kota Batu.</p></div>
                        {{-- Kompatibilitas data lama: kolom no_telepon diisi otomatis dari satu input yang terlihat. --}}
                        <input id="no_telepon" name="no_telepon" type="hidden" value="{{ $prefill('no_wa', $mahasiswa?->no_wa ?: ($mahasiswa?->no_telepon ?? '')) }}">
                        <div class="pmw-field"><label>Instansi tujuan</label><input type="text" value="BPS Kota Batu" readonly></div>
                    </div>
                </section>

                <section class="pmw-panel" data-panel="2" hidden>
                    <div class="pmw-panel-title"><div class="pmw-panel-icon"><i class="bi bi-mortarboard"></i></div><div><h3>Informasi Akademik</h3><p>Isikan kampus, jurusan, angkatan, dan semester.</p></div></div>
                    <div class="pmw-grid">
                        <div class="pmw-field"><label for="perguruan_tinggi">Universitas / Perguruan Tinggi <span class="pmw-req">*</span></label><input id="perguruan_tinggi" name="perguruan_tinggi" type="text" maxlength="255" required value="{{ $prefill('perguruan_tinggi', $mahasiswa?->perguruan_tinggi ?? '') }}"></div>
                        <div class="pmw-field"><label for="program_studi">Program Studi / Jurusan <span class="pmw-req">*</span></label><input id="program_studi" name="program_studi" type="text" maxlength="255" required value="{{ $prefill('program_studi', $mahasiswa?->program_studi ?? '') }}"></div>
                        <div class="pmw-field"><label for="tahun_angkatan">Tahun Angkatan <span class="pmw-req">*</span></label><input id="tahun_angkatan" name="tahun_angkatan" type="text" inputmode="numeric" pattern="[0-9]{4}" maxlength="4" required value="{{ $prefill('tahun_angkatan', $mahasiswa?->tahun_angkatan ?? '') }}" placeholder="2023"></div>
                        <div class="pmw-field"><label for="semester">Semester <span class="pmw-req">*</span></label><select id="semester" name="semester" required><option value="">Pilih semester</option>@for($s = 1; $s <= 14; $s++)<option value="{{ $s }}" {{ (string) $prefill('semester', $mahasiswa?->semester ?? '') === (string) $s ? 'selected' : '' }}>{{ $s }}</option>@endfor</select></div>
                    </div>
                </section>

                <section class="pmw-panel" data-panel="3" hidden>
                    <div class="pmw-panel-title"><div class="pmw-panel-icon"><i class="bi bi-calendar3"></i></div><div><h3>Periode Magang</h3><p>Boleh memilih tanggal lampau untuk pendaftaran susulan.</p></div></div>
                    <div class="pmw-grid">
                        <div class="pmw-field"><label for="tanggal_mulai">Tanggal mulai <span class="pmw-req">*</span></label><input id="tanggal_mulai" name="tanggal_mulai" type="date" required value="{{ $prefill('tanggal_mulai') }}"><p class="pmw-help">Pilih tanggal sesuai awal magang, termasuk tanggal sebelumnya.</p></div>
                        <div class="pmw-field"><label for="durasi_bulan">Durasi magang <span class="pmw-req">*</span></label><select id="durasi_bulan" name="durasi_bulan" required><option value="">Pilih durasi</option>@for($m = 1; $m <= 12; $m++)<option value="{{ $m }}" {{ (string) $prefill('durasi_bulan', '3') === (string) $m ? 'selected' : '' }}>{{ $m }} bulan</option>@endfor</select></div>
                        <div class="pmw-field pmw-full"><label for="jenis_pendaftaran">Jenis pendaftaran <span class="pmw-req">*</span></label><select id="jenis_pendaftaran" name="jenis_pendaftaran" required><option value="reguler" {{ $prefill('jenis_pendaftaran', 'reguler') === 'reguler' ? 'selected' : '' }}>Reguler (pengajuan baru)</option><option value="susulan" {{ $prefill('jenis_pendaftaran', 'reguler') === 'susulan' ? 'selected' : '' }}>Susulan (sebelumnya mendaftar secara manual)</option></select></div>
                        <div class="pmw-period-result" aria-live="polite"><small>Estimasi tanggal selesai (dihitung otomatis)</small><strong id="tanggalSelesaiHasil">Pilih tanggal mulai dan durasi</strong><p class="pmw-help">Tanggal selesai aktual ditetapkan terpisah oleh Admin jika masa magang berubah.</p></div>
                    </div>
                </section>

                <section class="pmw-panel" data-panel="4" hidden>
                    <div class="pmw-panel-title"><div class="pmw-panel-icon"><i class="bi bi-link-45deg"></i></div><div><h3>Dokumen Persyaratan</h3><p>Gunakan tautan Google Drive, bukan upload file ke server SIMASTA.</p></div></div>
                    <div class="pmw-drive-note"><i class="bi bi-info-circle"></i> Upload PDF ke Google Drive, atur izin <strong>Viewer</strong> untuk akun Admin BPS yang dituju, lalu salin tautan. Pastikan Admin bisa membukanya tanpa meminta akses tambahan.</div>
                    <div class="pmw-doc-list">
                        @foreach([['name' => 'link_cv', 'title' => 'Curriculum Vitae (CV)'], ['name' => 'link_surat', 'title' => 'Surat Pengantar'], ['name' => 'link_proposal', 'title' => 'Proposal Magang']] as $doc)
                            <div class="pmw-doc"><div class="pmw-doc-icon"><i class="bi bi-file-earmark-link"></i></div><div class="pmw-doc-main"><strong>{{ $doc['title'] }} <span class="pmw-req">*</span></strong><div class="pmw-field"><input id="{{ $doc['name'] }}" name="{{ $doc['name'] }}" type="url" maxlength="2048" required placeholder="https://drive.google.com/file/d/..." value="{{ $prefill($doc['name']) }}"></div><a href="#" data-open-link="{{ $doc['name'] }}" target="_blank" rel="noopener noreferrer" hidden><i class="bi bi-box-arrow-up-right"></i> Cek tautan dokumen</a></div></div>
                        @endforeach
                    </div>
                </section>

                <section class="pmw-panel" data-panel="5" hidden>
                    <div class="pmw-panel-title"><div class="pmw-panel-icon"><i class="bi bi-pencil-square"></i></div><div><h3>Tujuan & Keterangan</h3><p>Langkah terakhir sebelum pengajuan dikirim ke Admin.</p></div></div>
                    <div class="pmw-grid">
                        <div class="pmw-field pmw-full"><label for="tujuan_magang">Tujuan magang <span class="pmw-req">*</span></label><textarea id="tujuan_magang" name="tujuan_magang" minlength="10" maxlength="3000" required placeholder="Jelaskan tujuan, motivasi, atau kompetensi yang ingin dikembangkan...">{{ $prefill('tujuan_magang') }}</textarea><p class="pmw-help">Minimal 10 karakter, maksimal 3.000 karakter.</p></div>
                        <div class="pmw-field pmw-full"><label for="keterangan_tambahan">Keterangan tambahan (opsional)</label><textarea id="keterangan_tambahan" name="keterangan_tambahan" maxlength="3000" placeholder="Informasi yang perlu diketahui Admin...">{{ $prefill('keterangan_tambahan') }}</textarea></div>
                        <div class="pmw-field pmw-full"><div class="pmw-review" id="reviewPengajuan"><strong>Ringkasan pengajuan</strong><p>Lengkapi data untuk melihat ringkasan sebelum dikirim.</p></div></div>
                    </div>
                </section>

                <footer class="pmw-footer">
                    <div class="pmw-save" id="autosaveStatus" role="status" aria-live="polite"><i class="bi bi-cloud"></i> Draft belum diubah</div>
                    <div class="pmw-btns">
                        <button class="pmw-btn" type="button" id="btnKembali" disabled><i class="bi bi-arrow-left"></i> Kembali</button>
                        <button class="pmw-btn primary" type="button" id="btnLanjut">Selanjutnya <i class="bi bi-arrow-right"></i></button>
                        <button class="pmw-btn primary" type="submit" id="btnKirimPengajuan" hidden><i class="bi bi-send"></i> Kirim Pengajuan</button>
                    </div>
                </footer>
            </section>
        </form>
    @endif
</div>

@if(!$aktif)
<script>
(function () {
    'use strict';
    const form = document.getElementById('formPengajuan');
    if (!form) return;
    const panels = [...form.querySelectorAll('[data-panel]')];
    const nav = [...form.querySelectorAll('[data-step-nav]')];
    const prevBtn = document.getElementById('btnKembali');
    const nextBtn = document.getElementById('btnLanjut');
    const submitBtn = document.getElementById('btnKirimPengajuan');
    const badge = document.getElementById('stepBadge');
    const saveStatus = document.getElementById('autosaveStatus');
    // Satu input nomor HP saja; duplikasi internal untuk menjaga kompatibilitas Admin lama.
    const nomorUtama = document.getElementById('no_wa');
    const nomorLama = document.getElementById('no_telepon');
    if (nomorUtama && nomorLama) {
        const syncNomor = () => { nomorLama.value = nomorUtama.value; };
        syncNomor();
        nomorUtama.addEventListener('input', syncNomor);
        nomorUtama.addEventListener('change', syncNomor);
    }
    const fields = [...form.querySelectorAll('[name]')].filter(el => el.name !== '_token');
    const keys = fields.map(el => el.name);
    const dateInput = document.getElementById('tanggal_mulai');
    const durationInput = document.getElementById('durasi_bulan');
    let currentStep = {{ $draftStep }};
    let saveTimer = null;
    let saveSequence = Promise.resolve();
    let suppressSave = false;
    let sending = false;
    const draftUrl = @json(route('mahasiswa.pengajuan.draft.show'));
    const csrf = form.querySelector('[name="_token"]').value;

    function activePanel(n) { return panels[n - 1]; }
    function fieldStep(el) { return Number(el.closest('[data-panel]')?.dataset.panel || 1); }
    function validateStep(step, announce = true) {
        const inputs = [...activePanel(step).querySelectorAll('input[name],select[name],textarea[name]')];
        for (const input of inputs) {
            if (!input.checkValidity()) {
                if (announce) { setStep(step); input.reportValidity(); input.focus(); }
                return false;
            }
            if (input.type === 'url' && input.value.trim() && !isGoogleDocLink(input.value)) {
                if (announce) { setStep(step); input.setCustomValidity('Gunakan link HTTPS Google Drive atau Google Docs.'); input.reportValidity(); input.setCustomValidity(''); input.focus(); }
                return false;
            }
        }
        return true;
    }
    function isGoogleDocLink(value) {
        try { const u = new URL(value); return u.protocol === 'https:' && ['drive.google.com','docs.google.com'].includes(u.hostname.toLowerCase()); }
        catch (_) { return false; }
    }
    function setSaveMessage(message, state = '') {
        saveStatus.className = 'pmw-save' + (state ? ' is-' + state : '');
        saveStatus.innerHTML = '<i class="bi bi-cloud-check"></i> ' + message;
    }
    function dateResult() {
        const match = /^(\d{4})-(\d{2})-(\d{2})$/.exec(dateInput.value);
        const months = Number(durationInput.value);
        const target = document.getElementById('tanggalSelesaiHasil');
        if (!match || !Number.isInteger(months) || months < 1 || months > 12) { target.textContent = 'Pilih tanggal mulai dan durasi'; return; }
        const year = Number(match[1]), month = Number(match[2]), day = Number(match[3]);
        const first = new Date(Date.UTC(year, month - 1 + months, 1));
        const last = new Date(Date.UTC(first.getUTCFullYear(), first.getUTCMonth() + 1, 0)).getUTCDate();
        const finish = new Date(Date.UTC(first.getUTCFullYear(), first.getUTCMonth(), Math.min(day, last)));
        finish.setUTCDate(finish.getUTCDate() - 1);
        target.textContent = new Intl.DateTimeFormat('id-ID', {day:'numeric',month:'long',year:'numeric',timeZone:'UTC'}).format(finish);
    }
    function review() {
        const get = name => (form.elements[name]?.value || '').trim();
        const values = [
            ['Nama / NIM', [get('nama'), get('nim')].filter(Boolean).join(' · ')],
            ['Kampus / Prodi', [get('perguruan_tinggi'), get('program_studi')].filter(Boolean).join(' · ')],
            ['Angkatan / Semester', [get('tahun_angkatan'), get('semester')].filter(Boolean).join(' · ')],
            ['Tanggal mulai / durasi', [get('tanggal_mulai'), get('durasi_bulan') ? get('durasi_bulan') + ' bulan' : ''].filter(Boolean).join(' · ')],
            ['Jenis pendaftaran', get('jenis_pendaftaran')],
            ['Dokumen', ['link_cv','link_surat','link_proposal'].filter(k => get(k)).length + ' dari 3 tautan diisi'],
        ];
        const root = document.getElementById('reviewPengajuan');
        root.replaceChildren();
        const title = document.createElement('strong'); title.textContent = 'Ringkasan pengajuan'; root.appendChild(title);
        const dl = document.createElement('dl');
        values.forEach(([label, value]) => { const dt = document.createElement('dt'); dt.textContent = label; const dd = document.createElement('dd'); dd.textContent = value || 'Belum diisi'; dl.append(dt,dd); });
        root.appendChild(dl);
    }
    function completeStep(step) { return validateStep(step, false); }
    function draw() {
        panels.forEach((panel, index) => { panel.hidden = index + 1 !== currentStep; });
        nav.forEach((item, index) => {
            const n = index + 1;
            item.classList.toggle('is-current', n === currentStep);
            item.classList.toggle('is-complete', n !== currentStep && completeStep(n));
            item.querySelector('.pmw-step-dot').textContent = n !== currentStep && completeStep(n) ? '✓' : String(n);
            item.setAttribute('aria-current', n === currentStep ? 'step' : 'false');
        });
        badge.textContent = 'Tahap ' + currentStep + ' dari 5';
        prevBtn.disabled = currentStep === 1;
        nextBtn.hidden = currentStep === 5;
        submitBtn.hidden = currentStep !== 5;
        dateResult();
        if (currentStep === 5) review();
    }
    function setStep(n) {
        currentStep = Math.max(1, Math.min(5, Number(n) || 1));
        draw();
        scheduleSave();
    }
    function payload() {
        const form_data = {};
        keys.forEach(k => { form_data[k] = form.elements[k].value; });
        return { current_step: currentStep, form_data };
    }
    async function persistSnapshot(data) {
        const response = await fetch(draftUrl, {
            method:'POST',
            headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':csrf},
            credentials:'same-origin',
            body: JSON.stringify(data)
        });
        if (!response.ok) throw new Error('Gagal menyimpan draft (' + response.status + ')');
    }
    function saveNow() {
        if (suppressSave || sending) return saveSequence;
        clearTimeout(saveTimer);
        const snapshot = payload();
        setSaveMessage('Menyimpan draft...');
        saveSequence = saveSequence.catch(() => {}).then(() => persistSnapshot(snapshot)).then(() => {
            if (!saveTimer && !sending) setSaveMessage('Draft tersimpan otomatis', 'good');
        }).catch(() => { setSaveMessage('Draft belum tersimpan. Periksa koneksi internet.', 'bad'); });
        return saveSequence;
    }
    function scheduleSave() {
        if (suppressSave || sending) return;
        clearTimeout(saveTimer);
        setSaveMessage('Ada perubahan belum tersimpan...');
        saveTimer = setTimeout(() => { saveTimer = null; saveNow(); }, 1200);
    }
    nextBtn.addEventListener('click', () => { if (validateStep(currentStep)) { setStep(currentStep + 1); } });
    prevBtn.addEventListener('click', () => setStep(currentStep - 1));
    nav.forEach(button => button.addEventListener('click', () => {
        const target = Number(button.dataset.stepNav);
        if (target > currentStep) {
            for (let s = 1; s < target; s++) {
                if (!validateStep(s)) return;
            }
        }
        setStep(target);
    }));
    fields.forEach(el => {
        el.addEventListener('input', () => { draw(); scheduleSave(); });
        el.addEventListener('change', () => { draw(); scheduleSave(); });
    });
    document.querySelectorAll('[data-open-link]').forEach(anchor => {
        const input = document.getElementById(anchor.dataset.openLink);
        function refresh() {
            anchor.hidden = !isGoogleDocLink(input.value);
            if (!anchor.hidden) anchor.href = input.value;
            else anchor.removeAttribute('href');
        }
        input.addEventListener('input', refresh); refresh();
    });
    form.addEventListener('submit', event => {
        if (sending) { event.preventDefault(); return; }
        for (let step = 1; step <= 5; step++) {
            if (!validateStep(step)) { event.preventDefault(); return; }
        }
        sending = true;
        clearTimeout(saveTimer);
        submitBtn.disabled = true;
        submitBtn.textContent = 'Mengirim pengajuan...';
    });
    draw();
    // Pengisian awal: server memuat data draft melalui $prefill; GET endpoint tersedia
    // untuk integrasi lanjutan, tetapi tidak dibutuhkan untuk mengisi ulang form ini.
})();
</script>
@endif
@endsection
