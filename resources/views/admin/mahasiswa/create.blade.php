@extends('layouts.admin')

@section('title', 'Tambah Mahasiswa - SIMASTA')

@section('page-title', 'Tambah Mahasiswa')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')

<div class="card">

    <div style="margin-bottom: 25px;">
        <h2>Tambah Mahasiswa</h2>
        <p>Tambahkan data mahasiswa yang akan terdaftar di SIMASTA.</p>
    </div>

    @if ($errors->any())
        <div class="alert-danger" style="margin-bottom: 20px;">
            <strong>Data belum dapat disimpan.</strong>

            <ul style="margin-top: 10px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.mahasiswa.store') }}" method="POST">

        @csrf

        <div class="form-group">
            <label for="nama">Nama Mahasiswa</label>
            <input
                type="text"
                id="nama"
                name="nama"
                value="{{ old('nama') }}"
                placeholder="Masukkan nama mahasiswa"
                required
            >
        </div>

        <div class="form-group">
            <label for="nim">NIM</label>
            <input
                type="text"
                id="nim"
                name="nim"
                value="{{ old('nim') }}"
                placeholder="Masukkan NIM"
                required
            >
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="Masukkan email"
                required
            >
        </div>

        <div class="form-group">
            <label for="no_telepon">No. Telepon</label>
            <input
                type="text"
                id="no_telepon"
                name="no_telepon"
                value="{{ old('no_telepon') }}"
                placeholder="Masukkan nomor telepon"
            >
        </div>

        <div class="form-group">
            <label for="perguruan_tinggi">Perguruan Tinggi</label>
            <input
                type="text"
                id="perguruan_tinggi"
                name="perguruan_tinggi"
                value="{{ old('perguruan_tinggi') }}"
                placeholder="Masukkan nama perguruan tinggi"
                required
            >
        </div>

        <div class="form-group">
            <label for="program_studi">Program Studi</label>
            <input
                type="text"
                id="program_studi"
                name="program_studi"
                value="{{ old('program_studi') }}"
                placeholder="Masukkan program studi"
                required
            >
        </div>

        <div class="form-group">
            <label for="semester">Semester</label>
            <input
                type="text"
                id="semester"
                name="semester"
                value="{{ old('semester') }}"
                placeholder="Contoh: 5"
            >
        </div>

        <div class="form-group">
            <label for="tahun_angkatan">Tahun Angkatan</label>
            <input
                type="text"
                id="tahun_angkatan"
                name="tahun_angkatan"
                value="{{ old('tahun_angkatan') }}"
                placeholder="Contoh: 2024"
            >
        </div>

        <div class="form-group">
            <label for="alamat">Alamat</label>
            <textarea
                id="alamat"
                name="alamat"
                rows="4"
                placeholder="Masukkan alamat mahasiswa"
            >{{ old('alamat') }}</textarea>
        </div>

        <div class="form-group">
            <label for="kota">Kota</label>
            <input
                type="text"
                id="kota"
                name="kota"
                value="{{ old('kota') }}"
                placeholder="Contoh: Malang"
            >
        </div>

        <div style="
            display: flex;
            gap: 10px;
            margin-top: 25px;">

            <button type="submit" class="btn-primary">
                Simpan Mahasiswa
            </button>

            <a href="{{ route('admin.mahasiswa.index') }}"
                class="btn-primary">
                    Batal
            </button>
            </a>

        </div>

    </form>

</div>

@endsection