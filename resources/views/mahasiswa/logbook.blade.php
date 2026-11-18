@extends('layouts.app')

@section('title', 'Logbook Magang - SIMASTA')
@section('page-title', 'Logbook Magang')
@section('user-role', 'Mahasiswa')
@section('user-avatar', mb_strtoupper(mb_substr(auth()->user()->name ?? 'M', 0, 1)))

@section('sidebar')
    @include('layouts.mahasiswa')
@endsection

@section('content')
@php
    $awal = $pengajuan->tanggal_mulai_aktual ?: $pengajuan->periode_mulai;
    $akhir = $pengajuan->tanggal_selesai_aktual ?: $pengajuan->periode_selesai;
    $batasAwal = $awal ? \Carbon\Carbon::parse($awal)->toDateString() : null;
    $batasAkhir = $akhir ? \Carbon\Carbon::parse($akhir)->toDateString() : null;
    $hariIni = now()->toDateString();
    $maksTanggal = $batasAkhir && $batasAkhir < $hariIni ? $batasAkhir : $hariIni;
    $awalGrid = $awalBulan->copy()->startOfWeek(\Carbon\Carbon::MONDAY);
    $akhirGrid = $awalBulan->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SUNDAY);
    $jumlahHariGrid = (int) $awalGrid->diffInDays($akhirGrid) + 1;
    $namaHari = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    $namaDivisi = $pengajuan->divisi?->nama ?: ($pengajuan->bidang ?: 'Belum ditentukan');
    $namaPembimbing = $pengajuan->pembimbingRef?->nama ?: ($pengajuan->pembimbing ?: 'Belum ditentukan');
    $bisaInput = !$batasAwal || $batasAwal <= $maksTanggal;
@endphp

<style>
.lkc{--lkc-blue:#245fa6;--lkc-navy:#193c63;--lkc-line:#e1e9f2;--lkc-muted:#71839b;--lkc-green:#18966b;--lkc-purple:#7146b7;color:#263e59;font-family:inherit;max-width:1440px;margin:0 auto}
.lkc *{box-sizing:border-box}.lkc button,.lkc input,.lkc textarea{font-family:inherit}
.lkc-hero{background:linear-gradient(110deg,#fff 0%,#edf5ff 65%,#e9f8ee 100%);padding:24px 26px;border:1px solid #d9e6f0;border-radius:15px;display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:17px}
.lkc-eyebrow{font-size:10px;font-weight:800;color:#2467a9;letter-spacing:.09em;text-transform:uppercase}.lkc h1{font-size:24px;margin:7px 0;color:#173e6c}.lkc-hero p{font-size:12px;color:#6a7e95;margin:0;line-height:1.65}.lkc-hero-icon{font-size:30px;color:#477bb5}
.lkc-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-bottom:17px}.lkc-stat{background:#fff;border:1px solid var(--lkc-line);padding:16px;border-radius:13px;display:flex;align-items:center;gap:12px}.lkc-stat i{font-size:20px;color:#2369b4;background:#ebf3ff;padding:11px;border-radius:11px}.lkc-stat:nth-child(2) i{color:#c77a19;background:#fff4e3}.lkc-stat:nth-child(3) i{color:#0e9866;background:#e7f8ef}.lkc-stat span{font-size:11px;color:var(--lkc-muted);display:block}.lkc-stat strong{font-size:24px;color:var(--lkc-navy)}
.lkc-panel{border:1px solid var(--lkc-line);border-radius:14px;background:#fff;margin-bottom:17px;overflow:hidden}.lkc-head{padding:17px 20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;border-bottom:1px solid #e9eff5}.lkc-head h2{font-size:16px;color:var(--lkc-navy);margin:0 0 5px}.lkc-head p{font-size:11px;color:var(--lkc-muted);margin:0;line-height:1.5}
.lkc-toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap}.lkc-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:10px 13px;border-radius:9px;border:1px solid transparent;background:var(--lkc-blue);color:#fff;text-decoration:none;font-size:12px;font-weight:700;cursor:pointer}.lkc-btn:hover{background:#1a4d88;color:#fff}.lkc-btn.light{background:#f8fafc;color:#375572;border-color:#dee7f0}.lkc-btn.light:hover{background:#edf3fa}.lkc-btn:disabled{opacity:.5;cursor:not-allowed}
.lkc-month{display:flex;align-items:center;gap:11px}.lkc-month strong{font-size:14px;color:var(--lkc-navy);min-width:115px;text-align:center}.lkc-nav{border:1px solid #dce5ee;background:#fff;color:#3f6080;width:32px;height:32px;border-radius:9px;display:grid;place-items:center;text-decoration:none}.lkc-nav:hover{background:#eef5ff}
.lkc-calendar-scroll{overflow-x:auto}.lkc-calendar{min-width:660px}.lkc-weekdays,.lkc-week{display:grid;grid-template-columns:repeat(7,minmax(0,1fr))}.lkc-weekdays span{text-align:center;background:#f7faff;font-size:11px;font-weight:750;color:#647b94;padding:12px 0;border-bottom:1px solid #e8edf4}.lkc-day{display:flex;flex-direction:column;align-items:stretch;text-align:left;width:100%;min-height:106px;padding:10px;border:0;border-right:1px solid #e8edf4;border-bottom:1px solid #e8edf4;background:#fff;color:#35516d;position:relative}.lkc-day:nth-child(7n){border-right:0}.lkc-day.other{background:#fafbfe;color:#acb9c8}.lkc-day.clickable{cursor:pointer}.lkc-day.clickable:hover{background:#eef7ff}.lkc-day.unavailable{cursor:not-allowed;background:#fbfcfe;color:#b1bdca}.lkc-day.today .lkc-daynum{outline:2px solid #2e77c4;outline-offset:2px}.lkc-daynum{font-size:12px;font-weight:700;width:24px;height:24px;border-radius:7px;display:inline-flex;align-items:center;justify-content:center}.lkc-day.other .lkc-daynum{opacity:.6}.lkc-pill{display:block;max-width:100%;font-size:10px;font-weight:700;border-radius:6px;padding:5px 6px;margin-top:8px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;background:#fff2dc;color:#ab6712}.lkc-pill.done{background:#e5f8ed;color:#08794d}.lkc-addhint{font-size:10px;color:#6897b9;margin-top:auto;padding-top:8px}.lkc-legend{padding:13px 17px;display:flex;gap:17px;flex-wrap:wrap;font-size:11px;color:#698199}.lkc-dot{display:inline-block;width:9px;height:9px;border-radius:50%;margin-right:5px;background:#f7b653}.lkc-dot.done{background:#3abb83}.lkc-dot.empty{background:#b9c9db}
.lkc-info{margin-bottom:15px;padding:12px 16px;background:#f4f9fe;border:1px solid #e4eef8;border-radius:10px;display:flex;gap:12px 20px;flex-wrap:wrap;font-size:11px;color:#607891}.lkc-info b{color:#345779}
.lkc-alert{padding:12px 14px;margin-bottom:14px;background:#f0f7ff;border:1px solid #d4e8fa;border-radius:10px;font-size:12px;color:#315f86}.lkc-alert.success{background:#effaf3;border-color:#c6ebd5;color:#166f48}.lkc-alert.error{background:#fff2f2;border-color:#ffd0d0;color:#a83840}
.lkc-table-scroll{overflow-x:auto}.lkc-table{width:100%;min-width:630px;border-collapse:collapse}.lkc-table th{padding:12px 15px;text-align:left;font-size:11px;color:#698199;background:#f7faff}.lkc-table td{padding:14px 15px;font-size:12px;border-top:1px solid #edf2f8;color:#445f79;vertical-align:top;line-height:1.6}.lkc-table td.activity{white-space:pre-wrap;overflow-wrap:anywhere;max-width:440px}.lkc-badge{font-size:10px;font-weight:750;display:inline-flex;border-radius:30px;padding:5px 8px;color:#a96510;background:#fff3df}.lkc-badge.done{background:#e5f8ed;color:#0a7b50}.lkc-footer{padding:14px 18px;border-top:1px solid #edf2f8;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;color:#8394a8;font-size:11px}
.lkc-overlay{position:fixed;inset:0;z-index:1200;background:rgba(17,33,54,.58);padding:20px;display:none;align-items:center;justify-content:center}.lkc-overlay.open{display:flex}.lkc-dialog{background:#fff;width:min(100%,560px);max-height:90vh;overflow:auto;border-radius:16px;box-shadow:0 20px 60px rgba(18,35,59,.28)}.lkc-dialog-head{background:var(--lkc-purple);color:#fff;padding:17px 21px;display:flex;align-items:center;justify-content:space-between}.lkc-dialog-head h2{color:#fff;font-size:16px;margin:0}.lkc-close{color:#fff;border:0;background:transparent;font-size:24px;cursor:pointer}.lkc-dialog-body{padding:21px}.lkc-field{margin-bottom:17px}.lkc-field label{display:block;margin:0 0 7px;font-size:12px;font-weight:750;color:#435c75}.lkc-field input,.lkc-field textarea{width:100%;padding:12px;border:1px solid #d8e3ef;border-radius:9px;color:#294562;font-size:13px;background:#fff}.lkc-field textarea{min-height:150px;resize:vertical;line-height:1.6}.lkc-field input:focus,.lkc-field textarea:focus{outline:0;border-color:#3674ae;box-shadow:0 0 0 3px #e4f0fb}.lkc-help{color:#7b8da2;font-size:11px;line-height:1.6;margin:7px 0 0}.lkc-dialog-actions{display:flex;justify-content:flex-end;gap:9px;border-top:1px solid #edf1f6;padding-top:16px}.lkc-readonly{white-space:pre-wrap;overflow-wrap:anywhere;background:#f6f9fc;padding:13px;border:1px solid #e2eaf2;border-radius:9px;font-size:12px;line-height:1.6}.lkc-view-row{margin-bottom:13px}.lkc-view-row strong{font-size:11px;color:#5e7590;display:block;margin-bottom:6px}.lkc-view-row p{margin:0}
 .lkc-evidence{display:flex;align-items:center;gap:9px;margin:6px 0 10px}.lkc-evidence-icon{display:inline-flex;align-items:center;gap:6px;background:#eee8fb;border:1px solid #d7caef;color:#6240a7;border-radius:7px;padding:7px 12px;font-size:12px;font-weight:750}.lkc-photo-tip{font-size:11px;line-height:1.6;color:#7b8da2;margin-bottom:10px}.lkc-proof-link{color:#225eaa;font-weight:700;text-decoration:none;font-size:12px}.lkc-proof-link:hover{text-decoration:underline}
body.lkc-modal-open{overflow:hidden}
@media(max-width:780px){.lkc-stats{grid-template-columns:1fr}.lkc-hero{padding:20px}.lkc-hero-icon{display:none}.lkc-head{padding:15px}.lkc-dialog-body{padding:16px}}

/* PERBAIKAN FINAL: tombol Simpan selalu tampil di popup tambah kegiatan */
#lkcModal #lkcForm .lkc-dialog-actions {
    display: flex !important;
    width: 100%;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 20px;
    padding: 16px 0 0;
    border-top: 1px solid #edf1f6;
    background: #fff;
}
#lkcModal #lkcForm #lkcSimpan {
    display: inline-flex !important;
    visibility: visible !important;
    opacity: 1;
    position: static !important;
    flex: 0 0 auto;
    align-items: center;
    justify-content: center;
    min-width: 156px;
    min-height: 43px;
    padding: 10px 16px;
    border: 0;
    border-radius: 9px;
    background: #245fa6;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
}
#lkcModal #lkcForm #lkcSimpan:hover { background: #194d89; }
#lkcModal #lkcForm #lkcSimpan:disabled { opacity: .6; cursor: wait; }
#lkcModal #lkcForm .lkc-dialog-actions [data-close] {
    display: inline-flex !important;
    visibility: visible !important;
    position: static !important;
}
@media (max-width: 480px) {
    #lkcModal #lkcForm .lkc-dialog-actions > button { flex: 1 1 130px; }
}
</style>

<div class="lkc">
    <header class="lkc-hero">
        <div>
            <div class="lkc-eyebrow">SIMASTA · BPS Kota Batu</div>
            <h1>Kalender Logbook Magang</h1>
            <p>Klik tanggal kegiatan untuk mengisi logbook, termasuk tanggal sebelumnya yang terlewat selama periode magang.</p>
        </div>
        <i class="bi bi-calendar3 lkc-hero-icon" aria-hidden="true"></i>
    </header>

    @if(session('success'))<div class="lkc-alert success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="lkc-alert error" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="lkc-alert error" role="alert"><strong>Periksa isian:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="lkc-stats">
        <div class="lkc-stat"><i class="bi bi-journals"></i><div><span>Total Kegiatan</span><strong>{{ $statistik['total'] ?? 0 }}</strong></div></div>
        <div class="lkc-stat"><i class="bi bi-hourglass-split"></i><div><span>Menunggu Tinjauan</span><strong>{{ $statistik['menunggu'] ?? 0 }}</strong></div></div>
        <div class="lkc-stat"><i class="bi bi-check-circle"></i><div><span>Sudah Ditinjau</span><strong>{{ $statistik['ditinjau'] ?? 0 }}</strong></div></div>
    </div>

    <div class="lkc-info">
        <span><b>Divisi:</b> {{ $namaDivisi }}</span>
        <span><b>Pembimbing:</b> {{ $namaPembimbing }}</span>
        <span><b>Periode:</b> {{ $batasAwal ? \Carbon\Carbon::parse($batasAwal)->format('d/m/Y') : '—' }} – {{ $batasAkhir ? \Carbon\Carbon::parse($batasAkhir)->format('d/m/Y') : '—' }}</span>
    </div>

    <section class="lkc-panel" aria-label="Kalender logbook">
        <div class="lkc-head">
            <div><h2>Kalender Kegiatan</h2><p>Pilih hari yang kosong untuk mengisi; klik hari dengan label untuk melihat catatannya.</p></div>
            <div class="lkc-toolbar">
                <div class="lkc-month">
                    <a class="lkc-nav" href="{{ route('mahasiswa.logbook', ['bulan' => $awalBulan->copy()->subMonth()->format('Y-m')]) }}" aria-label="Bulan sebelumnya"><i class="bi bi-chevron-left"></i></a>
                    <strong>{{ $awalBulan->translatedFormat('F Y') }}</strong>
                    <a class="lkc-nav" href="{{ route('mahasiswa.logbook', ['bulan' => $awalBulan->copy()->addMonth()->format('Y-m')]) }}" aria-label="Bulan berikutnya"><i class="bi bi-chevron-right"></i></a>
                </div>
                <button class="lkc-btn" type="button" id="lkcTambah" @disabled(!$bisaInput)><i class="bi bi-plus-lg"></i> Tambah Kegiatan</button>
            </div>
        </div>
        <div class="lkc-calendar-scroll">
            <div class="lkc-calendar">
                <div class="lkc-weekdays">@foreach($namaHari as $hari)<span>{{ $hari }}</span>@endforeach</div>
                @for($minggu = 0; $minggu < $jumlahHariGrid / 7; $minggu++)
                    <div class="lkc-week">
                        @for($hari = 0; $hari < 7; $hari++)
                            @php
                                $tgl = $awalGrid->copy()->addDays($minggu * 7 + $hari);
                                $tanggalIso = $tgl->toDateString();
                                $diBulan = $tgl->month === $awalBulan->month && $tgl->year === $awalBulan->year;
                                $diPeriode = (!$batasAwal || $tanggalIso >= $batasAwal) && (!$batasAkhir || $tanggalIso <= $batasAkhir);
                                $bolehIsi = $diBulan && $diPeriode && $tanggalIso <= $hariIni;
                                $laporanHari = $kalenderLogbooks->get($tanggalIso);
                            @endphp
                            <button type="button"
                                class="lkc-day {{ !$diBulan ? 'other' : '' }} {{ ($bolehIsi || $laporanHari) ? 'clickable' : 'unavailable' }} {{ $tanggalIso === $hariIni ? 'today' : '' }}"
                                data-date="{{ $tanggalIso }}"
                                data-available="{{ $bolehIsi ? '1' : '0' }}"
                                @disabled(!$bolehIsi && !$laporanHari)
                                aria-label="{{ $tgl->translatedFormat('d F Y') }}{{ $laporanHari ? ', sudah ada logbook' : ($bolehIsi ? ', bisa diisi' : ', tidak tersedia') }}">
                                <span class="lkc-daynum">{{ $tgl->format('d') }}</span>
                                @if($laporanHari)
                                    <span class="lkc-pill {{ $laporanHari['status'] === 'ditinjau' ? 'done' : '' }}">{{ $laporanHari['status'] === 'ditinjau' ? '✓ Ditinjau' : '● Menunggu' }}</span>
                                @elseif($bolehIsi)
                                    <span class="lkc-addhint">+ Isi kegiatan</span>
                                @endif
                            </button>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>
        <div class="lkc-legend"><span><i class="lkc-dot"></i> Menunggu tinjauan</span><span><i class="lkc-dot done"></i> Sudah ditinjau</span><span><i class="lkc-dot empty"></i> Belum ada laporan</span></div>
    </section>

    <section class="lkc-panel" aria-label="Riwayat logbook">
        <div class="lkc-head"><div><h2>Riwayat Kegiatan</h2><p>Daftar kegiatan yang telah tersimpan, termasuk yang diisi untuk tanggal sebelumnya.</p></div></div>
        <div class="lkc-table-scroll">
            <table class="lkc-table">
                <thead><tr><th>Tanggal</th><th>Deskripsi Aktivitas</th><th>Capaian</th><th>Bukti Foto</th><th>Status</th><th>Catatan Admin</th></tr></thead>
                <tbody>
                    @forelse($logbooks as $logbook)
                        <tr>
                            <td>{{ $logbook->tanggal?->translatedFormat('d M Y') }}</td>
                            <td class="activity">{{ $logbook->kegiatan }}</td>
                            <td class="activity">{{ $logbook->capaian ?: '—' }}</td>
                            <td>
                                @php $buktiHost = strtolower((string) parse_url($logbook->link_bukti ?? '', PHP_URL_HOST)); @endphp
                                @if(in_array($buktiHost, ['drive.google.com', 'docs.google.com'], true) && str_starts_with($logbook->link_bukti, 'https://'))
                                    <a class="lkc-proof-link" href="{{ $logbook->link_bukti }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Buka Bukti</a>
                                @else — @endif
                            </td>
                            <td><span class="lkc-badge {{ $logbook->status === 'ditinjau' ? 'done' : '' }}">{{ $logbook->status === 'ditinjau' ? 'Ditinjau' : 'Menunggu' }}</span></td>
                            <td>{{ $logbook->catatan_admin ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:35px;color:#8193a8">Belum ada logbook. Klik tanggal di kalender untuk mulai mencatat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logbooks->hasPages())<div class="lkc-footer"><span>Menampilkan {{ $logbooks->firstItem() }}–{{ $logbooks->lastItem() }} dari {{ $logbooks->total() }} kegiatan</span>{{ $logbooks->links() }}</div>@endif
    </section>
</div>

{{-- POPUP FORM TAMBAH KEGIATAN --}}
<div id="lkcModal" class="lkc-overlay" role="dialog" aria-modal="true" aria-labelledby="lkcModalTitle" aria-hidden="true">
    <div class="lkc-dialog">
        <div class="lkc-dialog-head"><h2 id="lkcModalTitle">+ Tambah Kegiatan</h2><button type="button" class="lkc-close" data-close aria-label="Tutup">&times;</button></div>
        <div class="lkc-dialog-body">
            <form action="{{ route('mahasiswa.logbook.simpan') }}" method="POST" id="lkcForm">
                @csrf
                <div class="lkc-field"><label for="lkcTanggal">Tanggal Kegiatan *</label><input id="lkcTanggal" type="date" name="tanggal" min="{{ $batasAwal }}" max="{{ $maksTanggal }}" value="{{ old('tanggal') }}" required readonly><p class="lkc-help">Tanggal otomatis mengikuti hari yang kamu klik pada kalender. Untuk mengganti tanggal, tutup popup lalu klik tanggal lain.</p></div>
                <div class="lkc-field"><label for="lkcKegiatan">Deskripsi Aktivitas *</label><textarea id="lkcKegiatan" name="kegiatan" maxlength="5000" required placeholder="Jelaskan kegiatan magang yang kamu lakukan pada tanggal ini...">{{ old('kegiatan') }}</textarea></div>
                <div class="lkc-field"><label for="lkcCapaian">Capaian</label><textarea id="lkcCapaian" name="capaian" maxlength="3000" placeholder="Apa hasil atau output dari kegiatan hari ini?">{{ old('capaian') }}</textarea></div>
                <div class="lkc-field">
                    <label for="lkcBukti">Bukti Kegiatan (Foto via Google Drive) *</label>
                    <p class="lkc-photo-tip">Unggah foto kegiatan ke Google Drive milikmu, atur akses menjadi <b>Siapa saja yang memiliki link — Pelihat</b> (atau bagikan kepada akun Admin), lalu tempel tautannya di sini. SIMASTA tidak mengunggah foto ke server.</p>
                    <div class="lkc-evidence"><span class="lkc-evidence-icon"><i class="bi bi-link-45deg"></i> Link Google Drive</span></div>
                    <input id="lkcBukti" type="url" name="link_bukti" maxlength="2048" required placeholder="https://drive.google.com/file/d/..." value="{{ old('link_bukti') }}">
                    <p class="lkc-help">Gunakan alamat https://drive.google.com atau https://docs.google.com.</p>
                </div>
                <div class="lkc-dialog-actions">
                    <button type="button" class="lkc-btn light" data-close>Batal</button>
                    <button type="submit" class="lkc-btn" id="lkcSimpan" style="display:inline-flex;visibility:visible;align-items:center;justify-content:center;min-height:43px;background:#245fa6;color:#fff;">
                        <i class="bi bi-send"></i> Simpan Logbook
                    </button>
                </div>
            </form>
            <div id="lkcExisting" hidden>
                <div class="lkc-view-row"><strong>Tanggal</strong><div id="lkcExistingDate"></div></div>
                <div class="lkc-view-row"><strong>Status</strong><div id="lkcExistingStatus"></div></div>
                <div class="lkc-view-row"><strong>Deskripsi Aktivitas</strong><div class="lkc-readonly" id="lkcExistingText"></div></div>
                <div class="lkc-view-row" id="lkcExistingCapaianRow"><strong>Capaian</strong><div class="lkc-readonly" id="lkcExistingCapaian"></div></div>
                <div class="lkc-view-row" id="lkcExistingBuktiRow"><strong>Bukti Foto Google Drive</strong><a id="lkcExistingBukti" class="lkc-proof-link" target="_blank" rel="noopener noreferrer">Buka bukti kegiatan di Google Drive</a></div>
                <div class="lkc-view-row" id="lkcExistingNoteRow"><strong>Catatan Admin</strong><div class="lkc-readonly" id="lkcExistingNote"></div></div>
                <div class="lkc-dialog-actions"><button type="button" class="lkc-btn light" data-close>Tutup</button></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('lkcModal');
    const form = document.getElementById('lkcForm');
    const existing = document.getElementById('lkcExisting');
    const dateInput = document.getElementById('lkcTanggal');
    const textarea = document.getElementById('lkcKegiatan');
    const capaianInput = document.getElementById('lkcCapaian');
    const buktiInput = document.getElementById('lkcBukti');
    const saveButton = document.getElementById('lkcSimpan');
    const title = document.getElementById('lkcModalTitle');
    if (!modal || !form) return;
    const today = @json($hariIni);
    const maxDate = @json($maksTanggal);
    const minDate = @json($batasAwal);
    const entries = @json($kalenderLogbooks);
    const fmt = iso => new Intl.DateTimeFormat('id-ID', {day:'numeric',month:'long',year:'numeric',timeZone:'UTC'}).format(new Date(iso + 'T12:00:00Z'));
    const open = () => { modal.classList.add('open'); modal.setAttribute('aria-hidden', 'false'); document.body.classList.add('lkc-modal-open'); modal.querySelector('[data-close]').focus(); };
    const close = () => { modal.classList.remove('open'); modal.setAttribute('aria-hidden', 'true'); document.body.classList.remove('lkc-modal-open'); };
    let lastTrigger = null;
    const show = (date, entry) => {
        if (entry) {
            title.textContent = 'Detail Kegiatan'; form.hidden = true; existing.hidden = false;
            document.getElementById('lkcExistingDate').textContent = fmt(date);
            document.getElementById('lkcExistingStatus').textContent = entry.status === 'ditinjau' ? 'Sudah ditinjau' : 'Menunggu tinjauan';
            document.getElementById('lkcExistingText').textContent = entry.kegiatan || '—';
            document.getElementById('lkcExistingCapaian').textContent = entry.capaian || '—';
            const link = document.getElementById('lkcExistingBukti');
            const validLink = (() => { try { const url = new URL(entry.link_bukti); return url.protocol === 'https:' && ['drive.google.com', 'docs.google.com'].includes(url.hostname); } catch (_) { return false; } })();
            document.getElementById('lkcExistingBuktiRow').hidden = !validLink;
            if (validLink) link.href = entry.link_bukti; else link.removeAttribute('href');
            document.getElementById('lkcExistingNote').textContent = entry.catatan_admin || '—';
        } else {
            if (date > today || date > maxDate || (minDate && date < minDate)) return;
            title.textContent = '+ Tambah Kegiatan'; existing.hidden = true; form.hidden = false;
            dateInput.value = date; textarea.value = ''; capaianInput.value = ''; buktiInput.value = ''; saveButton.disabled = false;
            saveButton.innerHTML = '<i class="bi bi-send"></i> Simpan Logbook';
        }
        open();
    };
    document.querySelectorAll('.lkc-day[data-date]').forEach(btn => btn.addEventListener('click', () => {
        if (btn.disabled) return;
        lastTrigger = btn;
        const date = btn.dataset.date;
        show(date, entries[date] || null);
    }));
    document.getElementById('lkcTambah')?.addEventListener('click', () => {
        const eligible = Array.from(document.querySelectorAll('.lkc-day[data-available="1"]')).filter(b => !entries[b.dataset.date]);
        if (eligible.length) { lastTrigger = document.getElementById('lkcTambah'); show((eligible.find(b => b.dataset.date === today) || eligible[eligible.length-1]).dataset.date, null); }
        else alert('Tidak ada tanggal kosong yang dapat diisi pada bulan ini. Gunakan navigasi bulan untuk memilih bulan lain.');
    });
    modal.querySelectorAll('[data-close]').forEach(button => button.addEventListener('click', () => { close(); lastTrigger?.focus(); }));
    modal.addEventListener('mousedown', e => {if(e.target===modal) { close(); lastTrigger?.focus(); }});
    document.addEventListener('keydown', e => {if(e.key==='Escape' && modal.classList.contains('open')) { close(); lastTrigger?.focus(); }});
    form.addEventListener('submit', e => {
        if (!form.checkValidity()) return;
        saveButton.disabled = true; saveButton.textContent = 'Menyimpan...';
    });
    @if($errors->any() && old('tanggal'))
        const oldDate = @json(old('tanggal'));
        if (oldDate && !entries[oldDate] && oldDate <= maxDate && (!minDate || oldDate >= minDate)) {
            show(oldDate, null);
            textarea.value = @json(old('kegiatan', ''));
            capaianInput.value = @json(old('capaian', ''));
            buktiInput.value = @json(old('link_bukti', ''));
        }
    @endif
})();
</script>
@endsection
