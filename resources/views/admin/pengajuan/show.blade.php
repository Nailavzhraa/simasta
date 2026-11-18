{{-- resources/views/admin/pengajuan/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detail Pengajuan Magang - SIMASTA')
@section('page-title', 'Detail Pengajuan Magang')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')
@php
    $mhs = $pengajuan->user?->mahasiswa;
    $statusLabels = [
        'diproses' => 'Menunggu Verifikasi',
        'diverifikasi' => 'Diverifikasi',
        'perlu_revisi' => 'Perlu Revisi',
        'diterima' => 'Diterima',
        'ditolak' => 'Ditolak',
    ];
    $formatTanggal = fn ($tanggal) => $tanggal
        ? \Illuminate\Support\Carbon::parse($tanggal)->translatedFormat('d M Y')
        : '—';
    $bisaDiproses = in_array($pengajuan->status, ['diproses', 'diverifikasi'], true);
    $dokumen = [
        ['jenis' => 'cv', 'label' => 'Curriculum Vitae (CV)', 'link' => $pengajuan->link_cv],
        ['jenis' => 'surat', 'label' => 'Surat Pengantar', 'link' => $pengajuan->link_surat],
        ['jenis' => 'proposal', 'label' => 'Proposal Magang', 'link' => $pengajuan->link_proposal],
    ];
    $jumlahDokumen = collect($dokumen)->filter(fn ($d) => filled($d['link']))->count();
    $namaMahasiswa = $mhs?->nama ?: ($pengajuan->user?->name ?? 'Mahasiswa');
    $namaDivisi = $pengajuan->divisi?->nama ?: $pengajuan->bidang;
    $namaPembimbing = $pengajuan->pembimbingRef?->nama ?: $pengajuan->pembimbing;
    $namaTimKerja = $pengajuan->timKerja?->nama;
@endphp

<style>
.pd-page{--pd-blue:#1d5fa8;--pd-green:#169b71;--pd-orange:#ee963b;--pd-muted:#6b7e95;color:#223a56;font-family:inherit}
.pd-page *{box-sizing:border-box}
.pd-top{display:flex;justify-content:space-between;align-items:center;gap:18px;flex-wrap:wrap;margin-bottom:18px;padding:24px 26px;border-radius:16px;border:1px solid #dbe8f2;background:linear-gradient(115deg,#fff 0%,#f0f7ff 65%,#e9f9f0 100%)}
.pd-top-label{color:#2270b0;font-size:11px;letter-spacing:.7px;font-weight:800;margin-bottom:7px}
.pd-top h1{font-size:24px;line-height:1.3;color:#143c6a;margin:0 0 8px}.pd-top p{margin:0;color:#637c95;font-size:13px;line-height:1.6}
.pd-grid{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(280px,1fr);gap:17px;align-items:start}
.pd-card{background:#fff;border:1px solid #e1e8f1;border-radius:15px;padding:21px 23px;margin-bottom:17px}
.pd-card h2{font-size:16px;color:#163f71;margin:0 0 17px;display:flex;align-items:center;gap:9px}.pd-card h2 i{color:#2674b9}
.pd-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px 15px}
.pd-field{min-width:0}.pd-field small{display:block;color:#7d90a5;font-size:11px;margin-bottom:5px}.pd-field strong{display:block;color:#2b425f;font-size:13px;font-weight:650;overflow-wrap:anywhere;line-height:1.55}
.pd-note{white-space:pre-wrap;overflow-wrap:anywhere;background:#f7faff;border:1px solid #e7eff8;border-radius:10px;padding:13px;font-size:12px;line-height:1.7;color:#4a6079}
.pd-separator{border:0;border-top:1px solid #edf1f6;margin:19px 0}.pd-label{display:block;margin:0 0 8px;font-size:12px;font-weight:750;color:#425d7a}
.pd-doc{border:1px solid #e4edf6;border-radius:12px;padding:13px 14px;display:flex;justify-content:space-between;align-items:center;gap:13px;flex-wrap:wrap;margin-bottom:10px}
.pd-doc-main{display:flex;align-items:center;gap:11px;min-width:0;flex:1}.pd-doc-symbol{width:37px;height:37px;display:grid;place-items:center;background:#edf5ff;color:#2878bf;border-radius:10px;flex-shrink:0}.pd-doc b{font-size:12px;color:#1f3d5d}.pd-doc small{display:block;color:#8393a7;margin-top:5px;font-size:11px;overflow-wrap:anywhere}
.pd-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.pd-btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid transparent;border-radius:9px;padding:10px 13px;font-size:12px;font-weight:700;font-family:inherit;cursor:pointer;text-decoration:none;line-height:1.3}
.pd-btn:hover{text-decoration:none}.pd-blue{background:#2563eb;color:#fff}.pd-blue:hover{background:#1d4ed8;color:#fff}.pd-light{background:#f8fafc;border-color:#dce5ef;color:#40536b}.pd-light:hover{background:#edf3f8;color:#233d5b}.pd-green{background:#e7f9ee;color:#0b8352;border-color:#b9efd2}.pd-green:hover{background:#d9f3e6}.pd-red{background:#fff0f2;color:#bf394b;border-color:#ffd2d8}.pd-red:hover{background:#ffe5e9}
.pd-badge{display:inline-flex;align-items:center;padding:7px 11px;border-radius:30px;background:#eaf2ff;color:#2563eb;font-size:11px;font-weight:750}.pd-badge.diterima{background:#e4f8ed;color:#087747}.pd-badge.ditolak{background:#feecef;color:#b52942}.pd-badge.diverifikasi{background:#e2f5f9;color:#117c90}
.pd-alert{padding:12px 14px;border-radius:10px;margin-bottom:15px;font-size:12px;line-height:1.6}.pd-success{background:#ecfdf3;color:#086d43}.pd-error{background:#fff1f2;color:#aa3040}.pd-info{background:#eff6ff;color:#315f94}
.pd-form{margin-top:17px;border-top:1px solid #e9eef5;padding-top:17px}.pd-form h2{font-size:15px;margin:0 0 14px}.pd-form label{display:block;margin:0 0 7px;font-size:12px;font-weight:700;color:#40536b}
.pd-form select,.pd-form textarea{display:block;width:100%;margin:0 0 13px;border:1px solid #dbe5ef;border-radius:9px;padding:10px 12px;font-size:13px;font-family:inherit;color:#334155;background:#fff}.pd-form textarea{resize:vertical;min-height:100px;line-height:1.5}
.pd-form select:focus,.pd-form textarea:focus{outline:none;border-color:#3480bd;box-shadow:0 0 0 3px rgba(29,95,168,.1)}
.pd-preview-section[hidden]{display:none}.pd-preview{display:block;width:100%;height:480px;border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;margin-top:12px}
.pd-hint{font-size:11px;color:#7488a1;line-height:1.6;margin:12px 0 0}.pd-actions form{margin:0}
@media(max-width:980px){.pd-grid{grid-template-columns:1fr}}
@media(max-width:600px){.pd-fields{grid-template-columns:1fr}.pd-top{padding:20px 17px}.pd-card{padding:18px 16px}.pd-top h1{font-size:21px}.pd-btn{max-width:100%}}
</style>

<div class="pd-page">
    <header class="pd-top">
        <div>
            <div class="pd-top-label"><i class="bi bi-building"></i> SIMASTA · BPS KOTA BATU</div>
            <h1>Detail Pengajuan Magang</h1>
            <p>Periksa identitas, periode, dokumen persyaratan, serta keputusan pengajuan mahasiswa.</p>
        </div>
        <a href="{{ route('admin.pengajuan.index') }}" class="pd-btn pd-light"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
    </header>

    @if(session('success'))<div class="pd-alert pd-success" role="status">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="pd-alert pd-error" role="alert">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="pd-alert pd-error" role="alert"><strong>Periksa kembali isian:</strong>
            <ul style="margin:7px 0 0;padding-left:19px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="pd-grid">
        <main>
            <section class="pd-card">
                <h2><i class="bi bi-person-vcard"></i> Identitas Mahasiswa</h2>
                <div class="pd-fields">
                    <div class="pd-field"><small>Nama lengkap</small><strong>{{ $namaMahasiswa }}</strong></div>
                    <div class="pd-field"><small>NIM</small><strong>{{ $mhs?->nim ?: '—' }}</strong></div>
                    <div class="pd-field"><small>Email</small><strong>{{ $mhs?->email ?: ($pengajuan->user?->email ?? '—') }}</strong></div>
                    <div class="pd-field"><small>Nomor HP / WhatsApp</small><strong>{{ $mhs?->no_wa ?: ($mhs?->no_telepon ?: '—') }}</strong></div>
                    <div class="pd-field"><small>Perguruan tinggi</small><strong>{{ $mhs?->perguruan_tinggi ?: '—' }}</strong></div>
                    <div class="pd-field"><small>Program studi</small><strong>{{ $mhs?->program_studi ?: '—' }}</strong></div>
                    <div class="pd-field"><small>Angkatan</small><strong>{{ $mhs?->tahun_angkatan ?: '—' }}</strong></div>
                    <div class="pd-field"><small>Semester</small><strong>{{ $mhs?->semester ?: '—' }}</strong></div>
                    <div class="pd-field"><small>Dosen pembimbing kampus</small><strong>{{ $mhs?->dosen_pembimbing ?: '—' }}</strong></div>
                </div>
            </section>

            <section class="pd-card">
                <h2><i class="bi bi-calendar3"></i> Informasi Pengajuan</h2>
                <div class="pd-fields">
                    <div class="pd-field"><small>Instansi tujuan</small><strong>{{ $pengajuan->instansi ?: 'BPS Kota Batu' }}</strong></div>
                    <div class="pd-field"><small>Tanggal pengajuan</small><strong>{{ $formatTanggal($pengajuan->created_at) }}</strong></div>
                    <div class="pd-field"><small>Mulai magang (rencana)</small><strong>{{ $formatTanggal($pengajuan->periode_mulai) }}</strong></div>
                    <div class="pd-field"><small>Selesai magang (rencana)</small><strong>{{ $formatTanggal($pengajuan->periode_selesai) }}</strong></div>
                    <div class="pd-field"><small>Durasi magang</small><strong>{{ $pengajuan->durasi_bulan ? $pengajuan->durasi_bulan.' bulan' : '— (data lama)' }}</strong></div>
                    <div class="pd-field"><small>Jenis pendaftaran</small><strong>{{ ucfirst($pengajuan->jenis_pendaftaran ?: 'reguler') }}</strong></div>
                    <div class="pd-field"><small>Status pelaksanaan</small><strong>{{ str_replace('_', ' ', ucfirst($pengajuan->status_pelaksanaan ?: 'belum_mulai')) }}</strong></div>
                    <div class="pd-field"><small>Mulai aktual</small><strong>{{ $formatTanggal($pengajuan->tanggal_mulai_aktual) }}</strong></div>
                    <div class="pd-field"><small>Selesai aktual</small><strong>{{ $formatTanggal($pengajuan->tanggal_selesai_aktual) }}</strong></div>
                </div>
                <hr class="pd-separator">
                <span class="pd-label">Maksud dan tujuan magang</span>
                <div class="pd-note">{{ $pengajuan->tujuan_magang ?: ($pengajuan->motivasi ?: '—') }}</div>
                @if(filled($pengajuan->keterangan_tambahan))
                    <div style="margin-top:15px"><span class="pd-label">Keterangan tambahan</span><div class="pd-note">{{ $pengajuan->keterangan_tambahan }}</div></div>
                @endif
            </section>

            <section class="pd-card">
                <h2><i class="bi bi-folder2-open"></i> Dokumen Persyaratan</h2>
                <div class="pd-alert pd-info" style="margin-bottom:13px">{{ $jumlahDokumen }} dari 3 dokumen tersedia. Semua dokumen dikumpulkan melalui tautan Google Drive / Google Docs.</div>

                @foreach($dokumen as $d)
                    <div class="pd-doc">
                        <div class="pd-doc-main">
                            <div class="pd-doc-symbol"><i class="bi bi-link-45deg"></i></div>
                            <div>
                                <b>{{ $d['label'] }}</b>
                                <small>{{ filled($d['link']) ? 'Tautan Google Drive / Docs tersedia' : 'Tautan belum dikirim' }}</small>
                            </div>
                        </div>
                        <div class="pd-actions">
                            @if(filled($d['link']))
                                <a href="{{ route('admin.pengajuan.dokumen', [$pengajuan, $d['jenis']]) }}" target="_blank" rel="noopener noreferrer" class="pd-btn pd-blue"><i class="bi bi-box-arrow-up-right"></i> Buka Google Drive</a>
                            @else
                                <span style="font-size:11px;color:#94a3b8">Belum tersedia</span>
                            @endif
                        </div>
                    </div>
                @endforeach
                <p class="pd-hint"><i class="bi bi-info-circle"></i> Jika Google meminta izin akses, mahasiswa perlu membagikan dokumen kepada akun Google Admin BPS Kota Batu.</p>
            </section>
        </main>

        <div>
            <section class="pd-card">
                <h2><i class="bi bi-clipboard-check"></i> Status & Keputusan</h2>
                <span class="pd-badge {{ $pengajuan->status }}">{{ $statusLabels[$pengajuan->status] ?? ucfirst($pengajuan->status) }}</span>
                @if(filled($pengajuan->catatan_admin))
                    <div style="margin-top:15px"><span class="pd-label">Catatan Admin</span><div class="pd-note">{{ $pengajuan->catatan_admin }}</div></div>
                @endif

                @if($bisaDiproses)
                    @if($pengajuan->status === 'diproses')
                        <div class="pd-actions" style="margin-top:17px">
                            <form method="POST" action="{{ route('admin.pengajuan.verifikasi', $pengajuan) }}">
                                @csrf
                                <button class="pd-btn pd-green" type="submit"><i class="bi bi-check2-circle"></i> Verifikasi Berkas</button>
                            </form>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.pengajuan.terima', $pengajuan) }}" class="pd-form" id="pd-form-terima">
                        @csrf
                        <h2>Terima & Tentukan Penempatan</h2>
                        <label for="pd-divisi">Divisi Penempatan</label>
                        <select id="pd-divisi" name="divisi_id" required>
                            <option value="">Pilih divisi</option>
                            @foreach($divisis as $dv)
                                <option value="{{ $dv->id }}" data-jobdesk="{{ $dv->jobdesk }}" @selected(old('divisi_id') == $dv->id)>{{ $dv->nama }}</option>
                            @endforeach
                        </select>
                        <label for="pd-pembimbing">Pembimbing</label>
                        <select id="pd-pembimbing" name="pembimbing_id" required>
                            <option value="">Pilih pembimbing</option>
                            @foreach($pembimbings as $pb)
                                <option value="{{ $pb->id }}" data-divisi="{{ $pb->divisi_id }}" @selected(old('pembimbing_id') == $pb->id)>{{ $pb->nama }}</option>
                            @endforeach
                        </select>
                        <label for="pd-jobdesk">Jobdesk</label>
                        <textarea id="pd-jobdesk" name="jobdesk" maxlength="2000" required placeholder="Jelaskan tugas mahasiswa selama magang">{{ old('jobdesk') }}</textarea>
                        <button class="pd-btn pd-blue" type="submit"><i class="bi bi-check-circle"></i> Terima Pengajuan</button>
                    </form>

                    <form method="POST" action="{{ route('admin.pengajuan.tolak', $pengajuan) }}" class="pd-form" id="pd-form-tolak">
                        @csrf
                        <h2>Tolak Pengajuan</h2>
                        <label for="pd-catatan">Alasan Penolakan</label>
                        <textarea id="pd-catatan" name="catatan_admin" minlength="5" maxlength="1000" required placeholder="Jelaskan alasan penolakan kepada mahasiswa">{{ old('catatan_admin') }}</textarea>
                        <button type="submit" class="pd-btn pd-red"><i class="bi bi-x-circle"></i> Tolak Pengajuan</button>
                    </form>
                @endif
            </section>

            @if($pengajuan->status === 'diterima')
                <section class="pd-card">
                    <h2><i class="bi bi-diagram-3"></i> Informasi Penempatan</h2>
                    <div class="pd-fields">
                        <div class="pd-field"><small>Divisi / Bagian</small><strong>{{ $namaDivisi ?: '—' }}</strong></div>
                        <div class="pd-field"><small>Tim Kerja</small><strong>{{ $namaTimKerja ?: 'Belum ditentukan' }}</strong></div>
                        <div class="pd-field"><small>Pembimbing Lapangan</small><strong>{{ $namaPembimbing ?: '—' }}</strong></div>
                    </div>
                    <hr class="pd-separator">
                    <span class="pd-label">Jobdesk</span>
                    <div class="pd-note">{{ $pengajuan->jobdesk ?: '—' }}</div>
                </section>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    const divisi = document.getElementById('pd-divisi');
    const pembimbing = document.getElementById('pd-pembimbing');
    const jobdesk = document.getElementById('pd-jobdesk');
    if (divisi && pembimbing && jobdesk) {
        function update(fillJobdesk) {
            const selected = divisi.options[divisi.selectedIndex];
            if (fillJobdesk) jobdesk.value = selected?.dataset.jobdesk || '';
            [...pembimbing.options].forEach(opt => {
                if (!opt.value) return;
                opt.hidden = !!(divisi.value && opt.dataset.divisi && opt.dataset.divisi !== divisi.value);
            });
            if (pembimbing.selectedOptions[0]?.hidden) pembimbing.value = '';
        }
        divisi.addEventListener('change', () => update(true));
        update(false);
    }
})();
</script>
@endsection
