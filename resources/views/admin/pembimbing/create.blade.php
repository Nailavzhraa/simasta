@extends('layouts.admin')

@php $edit = isset($pembimbing); @endphp

@section('title', ($edit ? 'Edit' : 'Tambah') . ' Pembimbing - SIMASTA')
@section('page-title', ($edit ? 'Edit' : 'Tambah') . ' Pembimbing')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')

<style>
    .pf-card { max-width: 620px; }
    .pf-card h2 { margin: 0 0 6px; color: #1e293b; font-size: 22px; font-weight: 700; }
    .pf-card > p { margin: 0 0 22px; color: #64748b; font-size: 14px; }
    .pf-err { padding: 12px 16px; margin-bottom: 18px; color: #b91c1c; background: #fef2f2; border: 1px solid #fecaca; border-radius: 9px; font-size: 13px; }
    .pf-field { margin-bottom: 18px; }
    .pf-field label { display: block; margin-bottom: 6px; color: #475569; font-size: 13px; font-weight: 600; }
    .pf-field input, .pf-field select { width: 100%; box-sizing: border-box; padding: 11px 13px; border: 1px solid #e2e8f0; border-radius: 9px; font-family: inherit; font-size: 14px; color: #334155; background: #fff; }
    .pf-hint { margin-top: 5px; color: #94a3b8; font-size: 12px; }
    .pf-aksi { display: flex; gap: 10px; margin-top: 24px; }
    .pf-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 40px; padding: 10px 20px; border: 1px solid transparent; border-radius: 9px; font-family: inherit; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; }
    .pf-simpan { color: #fff; background: #2563eb; }
    .pf-simpan:hover { background: #1d4ed8; }
    .pf-batal { color: #475569; background: #fff; border-color: #e2e8f0; }
</style>

<div class="card pf-card">

    <h2>{{ $edit ? 'Edit Pembimbing' : 'Tambah Pembimbing' }}</h2>
    <p>Pembimbing akan muncul sebagai pilihan di halaman Penempatan.</p>

    @if ($errors->any())
        <div class="pf-err">{{ $errors->first() }}</div>
    @endif

      <form method="POST" action="{{ $edit ? route('admin.pembimbing.update', $pembimbing) : route('admin.pembimbing.simpan') }}">    
        @csrf
        @if ($edit) @method('PUT') @endif

        <div class="pf-field">
            <label for="nama">Nama pembimbing</label>
            <input id="nama" type="text" name="nama" required maxlength="150" value="{{ old('nama', $pembimbing->nama ?? '') }}">
        </div>

        <div class="pf-field">
            <label for="nip">NIP (opsional)</label>
            <input id="nip" type="text" name="nip" maxlength="50" value="{{ old('nip', $pembimbing->nip ?? '') }}">
        </div>

        <div class="pf-field">
            <label for="divisi_id">Divisi</label>
            <select id="divisi_id" name="divisi_id">
                <option value="">Semua divisi</option>
                @foreach ($divisis as $dv)
                    <option value="{{ $dv->id }}" @selected((int) old('divisi_id', $pembimbing->divisi_id ?? 0) === $dv->id)>{{ $dv->nama }}</option>
                @endforeach
            </select>
            <div class="pf-hint">Pembimbing hanya muncul saat divisinya dipilih di Penempatan. Pilih "Semua divisi" kalau bisa membimbing di mana saja.</div>
        </div>

        <div class="pf-aksi">
            <button type="submit" class="pf-btn pf-simpan">{{ $edit ? 'Simpan perubahan' : 'Simpan' }}</button>
            <a href="{{ route('admin.pembimbing.index') }}" class="pf-btn pf-batal">Batal</a>
        </div>
    </form>
</div>

@endsection