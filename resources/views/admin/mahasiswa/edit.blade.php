@extends('layouts.admin')

@section('title', 'Edit Mahasiswa - SIMASTA')
@section('page-title', 'Edit Mahasiswa')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')

<div class="card">

    <div style="
        margin-bottom: 25px;
    ">
        <h2>Edit Data Mahasiswa</h2>
        <p>Perbarui informasi mahasiswa yang terdaftar di SIMASTA.</p>
    </div>

    {{-- PESAN ERROR --}}
    @if ($errors->any())
        <div class="alert-danger" style="margin-bottom: 20px;">
            <strong>Data belum dapat diperbarui.</strong>

            <ul style="margin-top: 10px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        action="{{ route('admin.mahasiswa.update', $mahasiswa) }}"
        method="POST"
    >

        @csrf
        @method('PUT')

        {{-- NAMA --}}
        <div class="form-group">
            <label for="nama">Nama Mahasiswa</label>

            <input
                type="text"
                id="nama"
                name="nama"
                value="{{ old('nama', $mahasiswa->nama) }}"
                placeholder="Masukkan nama mahasiswa"
                required
            >
        </div>

        {{-- NIM --}}
        <div class="form-group">
            <label for="nim">NIM</label>

            <input
                type="text"
                id="nim"
                name="nim"
                value="{{ old('nim', $mahasiswa->nim) }}"
                placeholder="Masukkan NIM"
                required
            >
        </div>

        {{-- EMAIL --}}
        <div class="form-group">
            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', $mahasiswa->email) }}"
                placeholder="Masukkan email mahasiswa"
                required
            >
        </div>

        {{-- NO TELEPON --}}
        <div class="form-group">
            <label for="no_telepon">No. Telepon</label>

            <input
                type="text"
                id="no_telepon"
                name="no_telepon"
                value="{{ old('no_telepon', $mahasiswa->no_telepon) }}"
                placeholder="Masukkan nomor telepon"
            >
        </div>

        {{-- PERGURUAN TINGGI --}}
        <div class="form-group">
            <label for="perguruan_tinggi">Perguruan Tinggi</label>

            <input
                type="text"
                id="perguruan_tinggi"
                name="perguruan_tinggi"
                value="{{ old('perguruan_tinggi', $mahasiswa->perguruan_tinggi) }}"
                placeholder="Masukkan nama perguruan tinggi"
                required
            >
        </div>

        {{-- PROGRAM STUDI --}}
        <div class="form-group">
            <label for="program_studi">Program Studi</label>

            <input
                type="text"
                id="program_studi"
                name="program_studi"
                value="{{ old('program_studi', $mahasiswa->program_studi) }}"
                placeholder="Masukkan program studi"
                required
            >
        </div>

        {{-- SEMESTER --}}
        <div class="form-group">
            <label for="semester">Semester</label>

            <input
                type="text"
                id="semester"
                name="semester"
                value="{{ old('semester', $mahasiswa->semester) }}"
                placeholder="Contoh: 5"
            >
        </div>

        {{-- TAHUN ANGKATAN --}}
        <div class="form-group">
            <label for="tahun_angkatan">Tahun Angkatan</label>

            <input
                type="text"
                id="tahun_angkatan"
                name="tahun_angkatan"
                value="{{ old('tahun_angkatan', $mahasiswa->tahun_angkatan) }}"
                placeholder="Contoh: 2024"
            >
        </div>

        {{-- ALAMAT --}}
        <div class="form-group">
            <label for="alamat">Alamat</label>

            <textarea
                id="alamat"
                name="alamat"
                placeholder="Masukkan alamat mahasiswa"
            >{{ old('alamat', $mahasiswa->alamat) }}</textarea>
        </div>

        {{-- KOTA --}}
        <div class="form-group">
            <label for="kota">Kota</label>

            <input
                type="text"
                id="kota"
                name="kota"
                value="{{ old('kota', $mahasiswa->kota) }}"
                placeholder="Contoh: Kota Batu"
            >
        </div>

        {{-- BUTTON --}}
        <div style="
            display: flex;
            gap: 10px;
            margin-top: 25px;
        ">

            <button
                type="submit"
                class="btn-primary"
            >
                Simpan Perubahan
            </button>

            <a
                href="{{ route('admin.mahasiswa.index') }}"
                class="btn-secondary"
            >
                Batal
            </a>

        </div>

    </form>

</div>

@endsection