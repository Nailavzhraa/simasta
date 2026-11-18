@extends('layouts.admin')

@section('title', 'Verifikasi Pengajuan Magang - SIMASTA')
@section('page-title', 'Verifikasi Pengajuan Magang')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')
@php
    $statusLabels = [
        'diproses' => 'Menunggu Verifikasi',
        'diverifikasi' => 'Diverifikasi',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $tanggal = fn ($nilai) => $nilai ? \Illuminate\Support\Carbon::parse($nilai)->translatedFormat('d M Y') : '—';
    $totalMenunggu = ($hitung['diproses'] ?? 0) + ($hitung['diverifikasi'] ?? 0);
    $adaRouteDokumen = \Illuminate\Support\Facades\Route::has('admin.pengajuan.dokumen');
@endphp

<style>
    .pg-page{font-family:inherit;color:#172b4d}
    .pg-page *{box-sizing:border-box}
    .pg-hero{padding:22px 24px;margin-bottom:16px;border:1px solid #dce8f4;border-radius:14px;background:linear-gradient(115deg,#fff 0%,#eef6ff 60%,#e8f7f0 100%)}
    .pg-hero small{display:block;color:#1d5fa8;font-size:10px;font-weight:800;letter-spacing:.1em;margin-bottom:7px}
    .pg-hero h1{font-size:23px;color:#163f71;margin:0 0 6px;line-height:1.3}
    .pg-hero p{color:#5d7592;font-size:12px;line-height:1.6;margin:0}
    .pg-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:11px;margin-bottom:16px}
    .pg-stat{display:flex;align-items:center;gap:11px;min-width:0;min-height:82px;padding:14px;background:#fff;border:1px solid #e2e8f0;border-radius:14px}
    .pg-stat-icon{display:grid;place-items:center;width:37px;height:37px;flex:none;border-radius:11px;font-size:18px}
    .pg-blue-icon{color:#2563eb;background:#e8f0ff}.pg-orange-icon{color:#d97706;background:#fff1e1}.pg-green-icon{color:#059669;background:#dff8eb}.pg-red-icon{color:#d54454;background:#fff0f1}
    .pg-stat-label{margin:0 0 5px;color:#64748b;font-size:11px}.pg-stat-number{font-size:22px;font-weight:800;color:#1e293b}
    .pg-panel{background:#fff;border:1px solid #e2e8f0;border-radius:15px;overflow:hidden}
    .pg-panel-head{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:18px}
    .pg-panel-head h2{font-size:15px;margin:0 0 5px;color:#163f71}.pg-panel-head p{margin:0;font-size:12px;color:#94a3b8}
    .pg-filter{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
    .pg-filter input,.pg-filter select{height:39px;min-width:0;background:#fafcff;color:#334155;border:1px solid #dfe7f1;border-radius:9px;padding:0 11px;font:12px inherit}
    .pg-filter input{width:230px}.pg-filter select{width:158px}
    .pg-btn{border:1px solid transparent;border-radius:9px;padding:9px 12px;font:700 12px inherit;display:inline-flex;justify-content:center;align-items:center;gap:7px;text-decoration:none;white-space:nowrap;cursor:pointer;line-height:1.4}
    .pg-btn-blue{background:#2563eb;color:#fff}.pg-btn-blue:hover{background:#1d4ed8;color:#fff}
    .pg-btn-light{background:#f8fafc;border-color:#e2e8f0;color:#40536b}.pg-btn-light:hover{background:#edf3fa;color:#40536b}
    .pg-btn-green{background:#e7f9ee;border-color:#b9efd2;color:#0b8352}.pg-btn-green:hover{background:#d2f4e1;color:#0b8352}
    .pg-btn-red{background:#fff0f2;border-color:#ffd2d8;color:#bf394b}.pg-btn-red:hover{background:#ffe3e8;color:#bf394b}
    .pg-btn:disabled{opacity:.5;cursor:not-allowed}
    .pg-alert{padding:13px 16px;border-radius:10px;margin-bottom:15px;font-size:12px;line-height:1.6}.pg-success{background:#ecfdf3;border:1px solid #bef1d4;color:#086d43}.pg-error{background:#fff1f2;border:1px solid #fecdd3;color:#aa3040}
    .pg-table-wrap{width:100%;overflow-x:auto}
    .pg-table{width:100%;min-width:890px;border-collapse:collapse}
    .pg-table th{padding:12px 14px;text-align:left;background:#f8faff;border-block:1px solid #edf2f7;font-size:10px;color:#7890aa;letter-spacing:.05em;text-transform:uppercase;white-space:nowrap}
    .pg-table td{padding:13px 14px;border-bottom:1px solid #f0f4f8;font-size:12px;color:#40536b;vertical-align:middle}.pg-table tbody tr:hover{background:#fbfdff}
    .pg-student{display:flex;gap:10px;align-items:center;min-width:210px}.pg-avatar{width:37px;height:37px;flex:none;display:grid;place-items:center;background:#eaf2ff;color:#2563eb;border-radius:11px;font-weight:800}
    .pg-main{font-weight:700;color:#1b3457;font-size:12px;line-height:1.5}.pg-muted{font-size:11px;color:#8394a8;line-height:1.55;margin-top:3px}
    .pg-badge{display:inline-flex;gap:6px;align-items:center;padding:6px 9px;border-radius:99px;font-size:10px;font-weight:800;white-space:nowrap}
    .pg-badge:before{content:'';width:6px;height:6px;background:currentColor;border-radius:50%}
    .pg-s-diproses{background:#fff5e5;color:#b87609}.pg-s-diverifikasi{background:#eaf2ff;color:#2563eb}.pg-s-diterima{background:#e6faef;color:#0b9362}.pg-s-ditolak{background:#fff0f1;color:#d54454}
    .pg-actions{display:flex;flex-wrap:wrap;gap:6px}.pg-empty{text-align:center;padding:45px 16px!important;color:#94a3b8}.pg-empty strong{display:block;color:#34506d;margin-bottom:6px}
    .pg-footer{border-top:1px solid #eef2f7;padding:14px 18px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;font-size:12px;color:#94a3b8}
    .pg-modal{position:fixed;inset:0;z-index:1100;display:none;align-items:center;justify-content:center;padding:16px;background:rgba(12,29,51,.60)}
    .pg-modal.is-open{display:flex}.pg-dialog{width:100%;max-width:530px;max-height:92vh;overflow-y:auto;background:#fff;border-radius:16px;box-shadow:0 22px 65px rgba(8,26,49,.25)}.pg-dialog-wide{max-width:700px}
    .pg-dialog-head{display:flex;align-items:start;justify-content:space-between;gap:10px;padding:20px 22px 16px;border-bottom:1px solid #eff3f7}
    .pg-dialog-head h3{margin:0 0 5px;font-size:17px;color:#183759}.pg-dialog-head p{margin:0;font-size:12px;color:#94a3b8}
    .pg-close{width:30px;height:30px;border:0;border-radius:8px;background:#f1f5f9;color:#475569;font-size:19px;cursor:pointer}
    .pg-dialog-body{padding:19px 22px}.pg-dialog-foot{padding:15px 22px;border-top:1px solid #eff3f7;display:flex;justify-content:flex-end;flex-wrap:wrap;gap:8px}
    .pg-field{margin-bottom:15px}.pg-field label{display:block;margin-bottom:7px;color:#40536b;font-weight:700;font-size:12px}.pg-field select,.pg-field textarea{display:block;width:100%;padding:10px 12px;border:1px solid #dbe5ef;border-radius:9px;color:#334155;background:#fff;font:12px inherit}.pg-field textarea{resize:vertical;min-height:105px;line-height:1.5}.pg-hint{margin-top:6px;color:#94a3b8;font-size:11px;line-height:1.5}
    .pg-doc{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;padding:14px;border:1px solid #e5edf5;border-radius:11px;margin-bottom:10px}
    .pg-doc-name{font-size:12px;color:#183759;font-weight:700}.pg-doc-sub{font-size:11px;color:#94a3b8;margin-top:5px;overflow-wrap:anywhere}.pg-doc-actions{display:flex;gap:7px;flex-wrap:wrap}
    .pg-note{padding:12px 14px;border-radius:9px;border:1px solid #e9f0f9;background:#f7faff;color:#475569;font-size:12px;line-height:1.6;margin-top:13px;overflow-wrap:anywhere;white-space:pre-wrap}.pg-note strong{display:block;color:#163357;margin-bottom:5px}
    .pg-preview{margin-top:14px;border:1px solid #e2e8f0;border-radius:10px;overflow:hidden}.pg-preview-head{padding:10px 13px;background:#f8fafc;color:#334155;font-size:12px;font-weight:700;display:flex;justify-content:space-between}.pg-preview iframe{display:block;width:100%;height:420px;border:0}
    body.pg-modal-open{overflow:hidden}
    @media(max-width:1000px){.pg-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:650px){.pg-stats{gap:8px}.pg-stat{padding:11px}.pg-stat-number{font-size:20px}.pg-panel-head{padding:15px}.pg-filter{width:100%}.pg-filter input{width:100%;flex:1 1 100%}.pg-filter select{flex:1}.pg-dialog-head,.pg-dialog-body,.pg-dialog-foot{padding:15px}}
</style>

<div class="pg-page">
    <header class="pg-hero">
        <h1>Verifikasi Pengajuan Magang</h1>
        <p>Pantau pengajuan mahasiswa, periksa dokumen, dan tentukan keputusan magang di BPS Kota Batu.</p>
    </header>

    @if(session('success'))<div class="pg-alert pg-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="pg-alert pg-error" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="pg-alert pg-error" role="alert">{{ $errors->first() }}</div>@endif

    <div class="pg-stats">
        <div class="pg-stat"><div class="pg-stat-icon pg-blue-icon"><i class="bi bi-files"></i></div><div><p class="pg-stat-label">Total Pengajuan</p><div class="pg-stat-number">{{ $hitung['semua'] ?? 0 }}</div></div></div>
        <div class="pg-stat"><div class="pg-stat-icon pg-orange-icon"><i class="bi bi-hourglass-split"></i></div><div><p class="pg-stat-label">Menunggu Keputusan</p><div class="pg-stat-number">{{ $totalMenunggu }}</div></div></div>
        <div class="pg-stat"><div class="pg-stat-icon pg-green-icon"><i class="bi bi-check-circle"></i></div><div><p class="pg-stat-label">Diterima</p><div class="pg-stat-number">{{ $hitung['diterima'] ?? 0 }}</div></div></div>
        <div class="pg-stat"><div class="pg-stat-icon pg-red-icon"><i class="bi bi-x-circle"></i></div><div><p class="pg-stat-label">Ditolak</p><div class="pg-stat-number">{{ $hitung['ditolak'] ?? 0 }}</div></div></div>
    </div>

    <section class="pg-panel">
        <div class="pg-panel-head">
            <div><h2>Daftar Pengajuan Mahasiswa</h2><p>Pengajuan resmi yang sudah dikirim; draft mahasiswa tidak muncul di sini.</p></div>
            <form class="pg-filter" method="GET" action="{{ route('admin.pengajuan.index') }}">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, NIM, universitas..." aria-label="Cari mahasiswa">
                <select name="status" aria-label="Filter status">
                    <option value="">Semua status</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="pg-btn pg-btn-blue" type="submit">Cari</button>
                @if(request()->filled('q') || request()->filled('status'))
                    <a class="pg-btn pg-btn-light" href="{{ route('admin.pengajuan.index') }}">Reset</a>
                @endif
            </form>
        </div>
        <div class="pg-table-wrap">
            <table class="pg-table">
                <thead><tr><th>Mahasiswa</th><th>Periode</th><th>Dokumen</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($daftar as $p)
                        @php
                            $mhs = $p->user?->mahasiswa;
                            $nama = $mhs?->nama ?: ($p->user?->name ?? 'Mahasiswa');
                            $inisial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($nama, 0, 1));
                            $kampus = $mhs?->perguruan_tinggi;
                            $prodi = $mhs?->program_studi;
                            $nim = $mhs?->nim;
                            $jumlahDokumen = collect([
                                filled($p->link_cv),
                                filled($p->link_surat),
                                filled($p->link_proposal),
                            ])->filter()->count();
                        @endphp
                        <tr>
                            <td>
                                <div class="pg-student"><span class="pg-avatar">{{ $inisial }}</span><div>
                                    <div class="pg-main">{{ $nama }}</div>
                                    <div class="pg-muted">NIM: {{ $nim ?: '—' }} · {{ $p->user?->email ?? '—' }}</div>
                                    <div class="pg-muted">{{ $kampus ?: '—' }}{{ $prodi ? ' / '.$prodi : '' }}</div>
                                </div></div>
                            </td>
                            <td>
                                <div class="pg-main">{{ $tanggal($p->periode_mulai) }}</div>
                                <div class="pg-muted">s/d {{ $tanggal($p->periode_selesai) }}</div>
                                @if($p->durasi_bulan)<div class="pg-muted">{{ $p->durasi_bulan }} bulan · {{ ucfirst($p->jenis_pendaftaran ?? 'reguler') }}</div>@endif
                            </td>
                            <td><span class="pg-badge {{ $jumlahDokumen === 3 ? 'pg-s-diterima' : 'pg-s-diproses' }}">{{ $jumlahDokumen }}/3 Link Drive</span></td>
                            <td><span class="pg-badge pg-s-{{ in_array($p->status, array_keys($statusLabels), true) ? $p->status : 'diproses' }}">{{ $statusLabels[$p->status] ?? ucfirst($p->status) }}</span></td>
                            <td><div class="pg-actions">
                                <button type="button" class="pg-btn pg-btn-blue" data-modal-open="pg-dok-{{ $p->id }}">Periksa Berkas</button>
                                <a class="pg-btn pg-btn-light" href="{{ route('admin.pengajuan.show', $p) }}">Detail</a>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="pg-empty"><strong>Belum ada pengajuan yang ditemukan</strong>Coba ubah pencarian atau tunggu mahasiswa mengirim pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pg-footer"><span>Menampilkan {{ $daftar->count() }} dari {{ $daftar->total() }} pengajuan sesuai filter</span><div>{{ $daftar->links() }}</div></div>
    </section>
</div>

@foreach($daftar as $p)
    @php
        $dokumen = [
            ['jenis'=>'cv','judul'=>'Curriculum Vitae (CV)','link'=>$p->link_cv],
            ['jenis'=>'surat','judul'=>'Surat Pengantar','link'=>$p->link_surat],
            ['jenis'=>'proposal','judul'=>'Proposal Magang','link'=>$p->link_proposal],
        ];
        $bisaDiproses = in_array($p->status, ['diproses', 'diverifikasi'], true);
    @endphp
    <div class="pg-modal" id="pg-dok-{{ $p->id }}" role="dialog" aria-modal="true" aria-label="Dokumen pengajuan {{ $p->user?->name }}" aria-hidden="true">
        <div class="pg-dialog pg-dialog-wide">
            <div class="pg-dialog-head">
                <div><h3>Dokumen Pengajuan</h3><p>{{ $p->user?->name ?? 'Mahasiswa' }} · {{ $tanggal($p->created_at) }}</p></div>
                <button type="button" class="pg-close" data-modal-close aria-label="Tutup">×</button>
            </div>
            <div class="pg-dialog-body">
                <div class="pg-hint" style="margin-bottom:14px">Mahasiswa mengumpulkan dokumen melalui tautan Google Drive atau Google Docs. Pastikan izin berbagi memungkinkan Admin membukanya.</div>
                @foreach($dokumen as $d)
                    <div class="pg-doc">
                        <div>
                            <div class="pg-doc-name"><i class="bi bi-link-45deg"></i> {{ $d['judul'] }}</div>
                            <div class="pg-doc-sub">{{ filled($d['link']) ? 'Tautan Google Drive / Docs tersedia' : 'Tautan belum dikirim' }}</div>
                        </div>
                        <div class="pg-doc-actions">
                            @if(filled($d['link']) && $adaRouteDokumen)
                                <a class="pg-btn pg-btn-blue" href="{{ route('admin.pengajuan.dokumen', [$p, $d['jenis']]) }}" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Buka Google Drive</a>
                            @elseif(filled($d['link']))
                                <span class="pg-muted">Route dokumen Admin belum terdaftar</span>
                            @else
                                <span class="pg-muted">Belum tersedia</span>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($p->tujuan_magang || $p->motivasi)<div class="pg-note"><strong>Maksud dan Tujuan Magang</strong>{{ $p->tujuan_magang ?: $p->motivasi }}</div>@endif
                @if($p->keterangan_tambahan)<div class="pg-note"><strong>Keterangan Tambahan</strong>{{ $p->keterangan_tambahan }}</div>@endif
                @if($p->catatan_admin)<div class="pg-note"><strong>Catatan Admin</strong>{{ $p->catatan_admin }}</div>@endif

            </div>
            <div class="pg-dialog-foot">
                @if($bisaDiproses)
                    @if($p->status === 'diproses')
                        <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $p) }}" style="margin:0">@csrf
                            <button class="pg-btn pg-btn-green" type="submit">Verifikasi</button>
                        </form>
                    @endif
                    <button type="button" class="pg-btn pg-btn-blue" data-modal-open="pg-terima-{{ $p->id }}">Terima</button>
                    <button type="button" class="pg-btn pg-btn-red" data-modal-open="pg-tolak-{{ $p->id }}">Tolak</button>
                @endif
                <button type="button" class="pg-btn pg-btn-light" data-modal-close>Tutup</button>
            </div>
        </div>
    </div>

    @if($bisaDiproses)
        <div class="pg-modal" id="pg-terima-{{ $p->id }}" role="dialog" aria-modal="true" aria-label="Terima pengajuan" aria-hidden="true">
            <div class="pg-dialog">
                <form method="POST" action="{{ route('admin.pengajuan.terima', $p) }}">
                    @csrf
                    <div class="pg-dialog-head"><div><h3>Terima Pengajuan</h3><p>{{ $p->user?->name }} · Tentukan penempatan mahasiswa</p></div><button class="pg-close" type="button" data-modal-close aria-label="Tutup">×</button></div>
                    <div class="pg-dialog-body">
                        @if($divisis->isEmpty())<div class="pg-note">Belum ada divisi. Silakan kelola divisi terlebih dahulu.</div>@endif
                        <div class="pg-field"><label for="pg-divisi-{{ $p->id }}">Divisi Penempatan *</label>
                            <select id="pg-divisi-{{ $p->id }}" name="divisi_id" data-pg-divisi data-jobdesk="pg-jobdesk-{{ $p->id }}" data-pembimbing="pg-pembimbing-{{ $p->id }}" required>
                                <option value="">Pilih divisi</option>
                                @foreach($divisis as $dv)<option value="{{ $dv->id }}" data-jobdesk="{{ $dv->jobdesk }}" @selected((string) old('divisi_id', $p->divisi_id) === (string) $dv->id)>{{ $dv->nama }}</option>@endforeach
                            </select>
                        </div>
                        <div class="pg-field"><label for="pg-pembimbing-{{ $p->id }}">Pembimbing *</label>
                            <select id="pg-pembimbing-{{ $p->id }}" name="pembimbing_id" required>
                                <option value="">Pilih pembimbing</option>
                                @foreach($pembimbings as $pb)<option value="{{ $pb->id }}" data-divisi="{{ $pb->divisi_id }}" @selected((string) old('pembimbing_id', $p->pembimbing_id) === (string) $pb->id)>{{ $pb->nama }}</option>@endforeach
                            </select>
                            <div class="pg-hint">Pembimbing ditampilkan sesuai divisi yang dipilih.</div>
                        </div>
                        <div class="pg-field"><label for="pg-jobdesk-{{ $p->id }}">Jobdesk *</label><textarea id="pg-jobdesk-{{ $p->id }}" name="jobdesk" maxlength="2000" required placeholder="Jelaskan tugas mahasiswa selama magang...">{{ old('jobdesk', $p->jobdesk) }}</textarea><div class="pg-hint">Dapat terisi otomatis berdasarkan divisi dan disesuaikan kembali.</div></div>
                    </div>
                    <div class="pg-dialog-foot"><button type="button" class="pg-btn pg-btn-light" data-modal-close>Batal</button><button type="submit" class="pg-btn pg-btn-blue" @disabled($divisis->isEmpty() || $pembimbings->isEmpty())>Terima Pengajuan</button></div>
                </form>
            </div>
        </div>
        <div class="pg-modal" id="pg-tolak-{{ $p->id }}" role="dialog" aria-modal="true" aria-label="Tolak pengajuan" aria-hidden="true">
            <div class="pg-dialog">
                <form method="POST" action="{{ route('admin.pengajuan.tolak', $p) }}">
                    @csrf
                    <div class="pg-dialog-head"><div><h3>Tolak Pengajuan</h3><p>{{ $p->user?->name }} · Sertakan alasan yang jelas</p></div><button class="pg-close" type="button" data-modal-close aria-label="Tutup">×</button></div>
                    <div class="pg-dialog-body"><div class="pg-field"><label for="pg-alasan-{{ $p->id }}">Alasan Penolakan *</label><textarea id="pg-alasan-{{ $p->id }}" name="catatan_admin" minlength="5" maxlength="1000" required placeholder="Tuliskan alasan penolakan...">{{ old('catatan_admin') }}</textarea><div class="pg-hint">Minimal 5 karakter. Catatan akan terlihat oleh mahasiswa.</div></div></div>
                    <div class="pg-dialog-foot"><button type="button" class="pg-btn pg-btn-light" data-modal-close>Batal</button><button type="submit" class="pg-btn pg-btn-red">Tolak Pengajuan</button></div>
                </form>
            </div>
        </div>
    @endif
@endforeach

<script>
(function(){
    const modals = document.querySelectorAll('.pg-modal');
    const closeAll = () => {
        modals.forEach(modal => {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');

        });
        document.body.classList.remove('pg-modal-open');
    };
    document.querySelectorAll('[data-modal-open]').forEach(button => button.addEventListener('click', () => {
        const modal = document.getElementById(button.dataset.modalOpen);
        if (!modal) return;
        closeAll();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('pg-modal-open');
        modal.querySelector('[data-modal-close]')?.focus();
    }));
    document.querySelectorAll('[data-modal-close]').forEach(button => button.addEventListener('click', closeAll));
    modals.forEach(modal => modal.addEventListener('mousedown', event => { if(event.target === modal) closeAll(); }));
    document.addEventListener('keydown', event => { if(event.key === 'Escape') closeAll(); });
    document.querySelectorAll('[data-pg-divisi]').forEach(select => {
        const jobdesk = document.getElementById(select.dataset.jobdesk);
        const pembimbing = document.getElementById(select.dataset.pembimbing);
        if (!jobdesk || !pembimbing) return;
        const update = fillJobdesk => {
            const selected = select.options[select.selectedIndex];
            if (fillJobdesk) jobdesk.value = selected?.dataset.jobdesk || '';
            [...pembimbing.options].forEach(option => {
                if (!option.value) return;
                option.hidden = Boolean(select.value && option.dataset.divisi && option.dataset.divisi !== select.value);
            });
            if (pembimbing.selectedOptions[0]?.hidden) pembimbing.value = '';
        };
        select.addEventListener('change', () => update(true));
        update(false);
    });
})();
</script>
@endsection
