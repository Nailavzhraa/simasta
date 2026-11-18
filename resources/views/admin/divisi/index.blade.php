@extends('layouts.admin')

@section('title', 'Kelola Tim Kerja - SIMASTA')
@section('page-title', 'Kelola Tim Kerja')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')
@php
    $tabAktif = in_array(request('tab', 'divisi'), ['divisi', 'tim'], true) ? request('tab', 'divisi') : 'divisi';
    $divisiList = $divisis ?? collect();
    $timKerjaList = $timKerjas ?? collect();
@endphp
<style>
.kt-wrap,.kt-wrap *{box-sizing:border-box}
.kt-wrap{font-family:Arial,sans-serif;color:#334155;width:100%}
.kt-hero{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:28px 34px;border:1px solid #dbe7f2;border-radius:17px;background:linear-gradient(110deg,#fff,#f1f7ff,#eaf8f0);margin-bottom:20px}
.kt-hero h1{font-size:27px;color:#123e70;margin:0 0 10px;font-weight:750}
.kt-hero p{font-size:13px;color:#64748b;line-height:1.7;margin:0}
.kt-eyebrow{font-size:11px;font-weight:700;color:#2563eb;letter-spacing:1px;margin-bottom:8px}
.kt-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:40px;padding:10px 15px;border:1px solid transparent;border-radius:9px;font:700 12px Arial,sans-serif;cursor:pointer;text-decoration:none;white-space:nowrap;transition:background .15s}
.kt-primary{background:#2563eb;color:white}.kt-primary:hover{background:#1d4ed8;color:white}
.kt-outline{background:white;color:#475569;border-color:#dbe3ed}.kt-outline:hover{background:#f8fafc}
.kt-danger{background:#fff1f2;color:#be123c;border-color:#fecdd3}.kt-danger:hover{background:#ffe4e6}
.kt-hero .kt-btn{min-height:47px;padding:12px 20px;font-size:13px}
.kt-stats{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:20px}
.kt-stat{display:flex;align-items:center;gap:14px;padding:20px;border:1px solid #e2e8f0;border-radius:13px;background:white}
.kt-stat-icon{width:46px;height:46px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:11px;background:#eaf2ff;color:#2563eb;font-size:21px}
.kt-stat:nth-child(2) .kt-stat-icon{background:#fff1df;color:#e98a25}.kt-stat:nth-child(3) .kt-stat-icon{background:#e4f8ed;color:#15975e}
.kt-stat strong{display:block;font-size:23px;color:#123e70}.kt-stat small{font-size:12px;color:#64748b}
.kt-tabs{display:flex;gap:8px;padding:6px;background:#eef3f9;border-radius:12px;margin-bottom:20px}
.kt-tab{flex:1;display:flex;align-items:center;justify-content:center;gap:9px;padding:13px 16px;border-radius:9px;color:#64748b;font-size:13px;font-weight:700;text-decoration:none}
.kt-tab.active{background:white;color:#2563eb;box-shadow:0 2px 8px rgba(20,53,91,.07)}
.kt-card{padding:23px;margin-bottom:20px;background:white;border:1px solid #e2e8f0;border-radius:14px}
.kt-add-heading{display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap}.kt-add-heading .kt-desc{margin-bottom:0}.kt-inline-form{margin-top:18px;border-top:1px solid #edf2f7}.kt-inline-form[hidden]{display:none!important}.kt-inline-form .kt-form-body{padding:20px 0}.kt-inline-form .kt-form-actions{padding:17px 0 0}.kt-card h2{font-size:17px;margin:0 0 6px;color:#123e70}.kt-desc{font-size:12px;color:#64748b;margin:0 0 20px;line-height:1.7}
.kt-alert{padding:13px 16px;border-radius:9px;margin-bottom:18px;font-size:13px}.kt-success{background:#ecfdf5;color:#047857;border:1px solid #a7f3d0}.kt-error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
.kt-table-wrap{overflow-x:auto;border:1px solid #e2e8f0;border-radius:10px}.kt-table{width:100%;min-width:700px;border-collapse:collapse}
.kt-table th{padding:13px 15px;background:#f8fafc;color:#2563eb;text-align:left;font-size:11px;text-transform:uppercase;border-bottom:1px solid #e2e8f0}
.kt-table td{padding:14px 15px;border-bottom:1px solid #edf2f7;font-size:13px;vertical-align:middle}.kt-table tr:last-child td{border-bottom:0}
.kt-actions{display:flex;align-items:center;gap:7px;flex-wrap:wrap}.kt-actions form{margin:0}
.kt-badge{display:inline-flex;padding:5px 9px;background:#eff6ff;color:#1d4ed8;border-radius:7px;font-size:11px;font-weight:700}
.kt-empty{text-align:center;padding:30px!important;color:#94a3b8}
.kt-modal{display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;padding:20px;background:rgba(15,23,42,.54)}
.kt-modal.is-open{display:flex}.kt-dialog{width:100%;max-width:550px;max-height:calc(100vh - 40px);overflow-y:auto;background:white;border-radius:16px;box-shadow:0 22px 65px rgba(15,23,42,.25)}
.kt-modal-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;padding:22px 24px;border-bottom:1px solid #e2e8f0}
.kt-modal-head h2{margin:0 0 6px;color:#123e70;font-size:20px}.kt-modal-head p{margin:0;color:#64748b;font-size:12px;line-height:1.6}
.kt-close{background:#f1f5f9;color:#64748b;border:0;border-radius:8px;width:34px;height:34px;font-size:20px;cursor:pointer;flex-shrink:0}
.kt-form-body{display:grid;gap:18px;padding:23px 24px}.kt-field label{display:block;font-size:12px;font-weight:700;color:#475569;margin-bottom:8px}
.kt-field input,.kt-field select,.kt-field textarea{display:block;width:100%;min-height:43px;padding:11px 12px;border:1px solid #dbe3ed;border-radius:8px;font:13px Arial,sans-serif;color:#334155;background:white;outline:none}
.kt-field input:focus,.kt-field select:focus,.kt-field textarea:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1)}
.kt-field textarea{min-height:100px;resize:vertical;line-height:1.6}.kt-hint{margin-top:7px;font-size:11px;color:#94a3b8;line-height:1.5}
.kt-form-actions{display:flex;justify-content:flex-end;gap:10px;padding:17px 24px;border-top:1px solid #edf2f7}
body.kt-modal-open{overflow:hidden}
@media(max-width:900px){.kt-stats{grid-template-columns:1fr}}
@media(max-width:600px){.kt-hero{padding:20px;align-items:stretch;flex-direction:column}.kt-hero h1{font-size:23px}.kt-hero .kt-btn{width:100%}.kt-card{padding:16px}.kt-modal{padding:10px}.kt-dialog{max-height:calc(100vh - 20px)}.kt-modal-head,.kt-form-body{padding:18px}.kt-form-actions{padding:15px 18px}.kt-form-actions .kt-btn{flex:1}}
</style>

<div class="kt-wrap">
    <div class="kt-hero">
        <div>
            <h1>Kelola Tim Kerja</h1>
            <p>Kelola struktur divisi dan tim kerja untuk mendukung pengelolaan pembimbing serta penempatan mahasiswa magang.</p>
        </div>

    </div>

    <div class="kt-stats">
        <div class="kt-stat"><div class="kt-stat-icon"><i class="bi bi-diagram-3"></i></div><div><strong>{{ $divisiList->count() }}</strong><small>Total Divisi</small></div></div>
        <div class="kt-stat"><div class="kt-stat-icon"><i class="bi bi-people"></i></div><div><strong>{{ $timKerjaList->count() }}</strong><small>Total Tim Kerja</small></div></div>
        <div class="kt-stat"><div class="kt-stat-icon"><i class="bi bi-person-badge"></i></div><div><strong>{{ $divisiList->sum('pembimbings_count') }}</strong><small>Total Pembimbing</small></div></div>
    </div>

    @if(session('success'))<div class="kt-alert kt-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="kt-alert kt-error">{{ session('error') }}</div>@endif
    @if($errors->any())
        <div class="kt-alert kt-error"><strong>Periksa kembali data:</strong><ul style="margin:8px 0 0;padding-left:20px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="kt-tabs">
        <a class="kt-tab {{ $tabAktif === 'divisi' ? 'active' : '' }}" href="{{ route('admin.divisi.index', ['tab' => 'divisi']) }}"><i class="bi bi-diagram-3"></i> Divisi</a>
        <a class="kt-tab {{ $tabAktif === 'tim' ? 'active' : '' }}" href="{{ route('admin.divisi.index', ['tab' => 'tim']) }}"><i class="bi bi-people"></i> Tim Kerja</a>
    </div>

    <div class="kt-card kt-add-card" id="kt-add-section">
        <div class="kt-add-heading">
            <div>
                <h2>Tambah {{ $tabAktif === 'divisi' ? 'Divisi' : 'Tim Kerja' }}</h2>
                <p class="kt-desc">{{ $tabAktif === 'divisi' ? 'Tambahkan divisi beserta jobdesk bawaan untuk penempatan magang.' : 'Tambahkan tim kerja di bawah divisi yang sesuai.' }}</p>
            </div>
            <button type="button" class="kt-btn kt-primary" id="kt-toggle-add" aria-expanded="false" aria-controls="kt-add-form">
                <i class="bi bi-plus-lg"></i> Tambah {{ $tabAktif === 'divisi' ? 'Divisi' : 'Tim Kerja' }}
            </button>
        </div>
        <div id="kt-add-form" class="kt-inline-form" hidden>
            @if($tabAktif === 'divisi')
                <form method="POST" action="{{ route('admin.divisi.simpan') }}">
                    @csrf
                    <div class="kt-form-body">
                        <div class="kt-field"><label for="kt-nama-divisi">Nama Divisi *</label><input id="kt-nama-divisi" name="nama" type="text" maxlength="150" value="{{ old('nama') }}" placeholder="Masukkan nama divisi" required></div>
                        <div class="kt-field"><label for="kt-jobdesk-divisi">Jobdesk Bawaan</label><textarea id="kt-jobdesk-divisi" name="jobdesk" maxlength="2000" placeholder="Tuliskan tugas atau tanggung jawab divisi...">{{ old('jobdesk') }}</textarea><div class="kt-hint">Digunakan sebagai template jobdesk pada penempatan mahasiswa.</div></div>
                    </div>
                    <div class="kt-form-actions"><button type="button" class="kt-btn kt-outline" id="kt-cancel-add">Batal</button><button type="submit" class="kt-btn kt-primary"><i class="bi bi-check-lg"></i> Simpan Divisi</button></div>
                </form>
            @else
                <form method="POST" action="{{ route('admin.divisi.tim.simpan') }}">
                    @csrf
                    <div class="kt-form-body">
                        @if($divisiList->isEmpty())<div class="kt-alert kt-error" style="margin:0">Belum ada divisi. Tambahkan divisi terlebih dahulu.</div>@endif
                        <div class="kt-field"><label for="kt-divisi-tim">Divisi Induk *</label><select id="kt-divisi-tim" name="divisi_id" required><option value="">Pilih divisi induk</option>@foreach($divisiList as $divisi)<option value="{{ $divisi->id }}" @selected((string) old('divisi_id') === (string) $divisi->id)>{{ $divisi->nama }}</option>@endforeach</select></div>
                        <div class="kt-field"><label for="kt-nama-tim">Nama Tim Kerja *</label><input id="kt-nama-tim" type="text" name="nama" maxlength="150" value="{{ old('nama') }}" placeholder="Masukkan nama tim kerja" required></div>
                        <div class="kt-field"><label for="kt-deskripsi-tim">Deskripsi Tugas</label><textarea id="kt-deskripsi-tim" name="deskripsi" maxlength="2000" placeholder="Tuliskan tugas dan tanggung jawab tim...">{{ old('deskripsi') }}</textarea></div>
                    </div>
                    <div class="kt-form-actions"><button type="button" class="kt-btn kt-outline" id="kt-cancel-add">Batal</button><button type="submit" class="kt-btn kt-primary" @disabled($divisiList->isEmpty())><i class="bi bi-check-lg"></i> Simpan Tim Kerja</button></div>
                </form>
            @endif
        </div>
    </div>

    @if($tabAktif === 'divisi')
        <div class="kt-card">
            <h2>Daftar Divisi</h2><p class="kt-desc">Data divisi yang digunakan dalam pengelolaan magang.</p>
            <div class="kt-table-wrap"><table class="kt-table"><thead><tr><th>Divisi</th><th>Jobdesk</th><th>Pembimbing</th><th>Tim Kerja</th><th>Aksi</th></tr></thead><tbody>
                @forelse($divisiList as $divisi)
                    <tr>
                        <td><strong>{{ $divisi->nama }}</strong></td>
                        <td>{{ $divisi->jobdesk ?: '-' }}</td>
                        <td><span class="kt-badge">{{ $divisi->pembimbings_count ?? 0 }} orang</span></td>
                        <td><span class="kt-badge">{{ $divisi->tim_kerjas_count ?? 0 }} tim</span></td>
                        <td><div class="kt-actions">
                            <button type="button" class="kt-btn kt-outline" data-kt-open="kt-edit-divisi-{{ $divisi->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                            <form method="POST" action="{{ route('admin.divisi.hapus', $divisi) }}" onsubmit="return confirm('Yakin ingin menghapus divisi ini?')">@csrf @method('DELETE')<button class="kt-btn kt-danger" type="submit"><i class="bi bi-trash"></i> Hapus</button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="kt-empty">Belum ada data divisi. Klik Tambah Divisi di kanan atas.</td></tr>
                @endforelse
            </tbody></table></div>
        </div>
    @else
        <div class="kt-card">
            <h2>Daftar Tim Kerja</h2><p class="kt-desc">Daftar tim kerja yang terhubung dengan divisi.</p>
            <div class="kt-table-wrap"><table class="kt-table"><thead><tr><th>Nama Tim Kerja</th><th>Divisi Induk</th><th>Deskripsi</th><th>Aksi</th></tr></thead><tbody>
                @forelse($timKerjaList as $tim)
                    <tr>
                        <td><strong>{{ $tim->nama }}</strong></td>
                        <td><span class="kt-badge">{{ $tim->divisi?->nama ?? '-' }}</span></td>
                        <td>{{ $tim->deskripsi ?: '-' }}</td>
                        <td><div class="kt-actions">
                            <button type="button" class="kt-btn kt-outline" data-kt-open="kt-edit-tim-{{ $tim->id }}"><i class="bi bi-pencil-square"></i> Edit</button>
                            <form method="POST" action="{{ route('admin.divisi.tim.hapus', $tim) }}" onsubmit="return confirm('Yakin ingin menghapus tim kerja ini?')">@csrf @method('DELETE')<button class="kt-btn kt-danger" type="submit"><i class="bi bi-trash"></i> Hapus</button></form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="kt-empty">Belum ada tim kerja. Klik Tambah Tim Kerja di kanan atas.</td></tr>
                @endforelse
            </tbody></table></div>
        </div>
    @endif
</div>

{{-- MODAL EDIT DIVISI / TIM KERJA --}}
@if($tabAktif === 'divisi')
@foreach($divisiList as $divisi)
<div class="kt-modal" id="kt-edit-divisi-{{ $divisi->id }}" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Edit Divisi">
    <div class="kt-dialog"><form method="POST" action="{{ route('admin.divisi.update', $divisi) }}">@csrf @method('PUT')
        <div class="kt-modal-head"><div><h2>Edit Divisi</h2><p>Perbarui informasi {{ $divisi->nama }}.</p></div><button type="button" class="kt-close" data-kt-close aria-label="Tutup">&times;</button></div>
        <div class="kt-form-body"><div class="kt-field"><label>Nama Divisi *</label><input type="text" name="nama" value="{{ $divisi->nama }}" maxlength="150" required></div><div class="kt-field"><label>Jobdesk Bawaan</label><textarea name="jobdesk" maxlength="2000">{{ $divisi->jobdesk }}</textarea></div></div>
        <div class="kt-form-actions"><button type="button" class="kt-btn kt-outline" data-kt-close>Batal</button><button type="submit" class="kt-btn kt-primary">Simpan Perubahan</button></div>
    </form></div>
</div>
@endforeach
@else
@foreach($timKerjaList as $tim)
<div class="kt-modal" id="kt-edit-tim-{{ $tim->id }}" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Edit Tim Kerja">
    <div class="kt-dialog"><form method="POST" action="{{ route('admin.divisi.tim.update', $tim) }}">@csrf @method('PUT')
        <div class="kt-modal-head"><div><h2>Edit Tim Kerja</h2><p>Perbarui informasi {{ $tim->nama }}.</p></div><button type="button" class="kt-close" data-kt-close aria-label="Tutup">&times;</button></div>
        <div class="kt-form-body">
            <div class="kt-field"><label>Divisi Induk *</label><select name="divisi_id" required>@foreach($divisiList as $divisi)<option value="{{ $divisi->id }}" @selected((int) $tim->divisi_id === (int) $divisi->id)>{{ $divisi->nama }}</option>@endforeach</select></div>
            <div class="kt-field"><label>Nama Tim Kerja *</label><input type="text" name="nama" value="{{ $tim->nama }}" maxlength="150" required></div>
            <div class="kt-field"><label>Deskripsi Tugas</label><textarea name="deskripsi" maxlength="2000">{{ $tim->deskripsi }}</textarea></div>
        </div>
        <div class="kt-form-actions"><button type="button" class="kt-btn kt-outline" data-kt-close>Batal</button><button type="submit" class="kt-btn kt-primary">Simpan Perubahan</button></div>
    </form></div>
</div>
@endforeach
@endif

<script>
(function () {
    const addPanel = document.getElementById('kt-add-form');
    const toggleAdd = document.getElementById('kt-toggle-add');
    const cancelAdd = document.getElementById('kt-cancel-add');
    function setAddOpen(open) {
        if (!addPanel || !toggleAdd) return;
        addPanel.hidden = !open;
        toggleAdd.setAttribute('aria-expanded', String(open));
        toggleAdd.innerHTML = open
            ? '<i class="bi bi-dash-lg"></i> Tutup Form'
            : '<i class="bi bi-plus-lg"></i> Tambah {{ $tabAktif === 'divisi' ? 'Divisi' : 'Tim Kerja' }}';
        if (open) addPanel.querySelector('input,select,textarea')?.focus();
    }
    toggleAdd?.addEventListener('click', () => setAddOpen(addPanel.hidden));
    cancelAdd?.addEventListener('click', () => setAddOpen(false));
    let lastTrigger = null;
    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (!document.querySelector('.kt-modal.is-open')) document.body.classList.remove('kt-modal-open');
        if (lastTrigger) lastTrigger.focus();
    }
    function openModal(id, trigger) {
        const modal = document.getElementById(id);
        if (!modal) return;
        lastTrigger = trigger || null;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('kt-modal-open');
        const first = modal.querySelector('input:not([type="hidden"]),select,textarea,button');
        if (first) first.focus();
    }
    document.querySelectorAll('[data-kt-open]').forEach(btn => btn.addEventListener('click', () => openModal(btn.dataset.ktOpen, btn)));
    document.querySelectorAll('[data-kt-close]').forEach(btn => btn.addEventListener('click', () => closeModal(btn.closest('.kt-modal'))));
    document.querySelectorAll('.kt-modal').forEach(modal => modal.addEventListener('click', e => { if (e.target === modal) closeModal(modal); }));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(document.querySelector('.kt-modal.is-open')); });
    @if($errors->any())
        setAddOpen(true);
    @endif
})();
</script>
@endsection
