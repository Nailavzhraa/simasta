@extends('layouts.app')

@section('page-title', 'Profil Mahasiswa')

@section('user-role', 'Mahasiswa')

@section('user-avatar', 'M')

@section('sidebar')

    @include('layouts.mahasiswa')

@endsection

@section('content')

<div class="welcome">

    <h1>
        Profil Mahasiswa
    </h1>

    <p>
        Kelola informasi pribadi dan data akademik kamu.
    </p>

</div>


<!-- PROFIL -->

<div class="card">

    <h2>
        Data Pribadi
    </h2>

    <br>

    <div class="profile-grid">

        <div class="profile-item">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                value="Nama Mahasiswa"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                NIM
            </label>

            <input
                type="text"
                value="202400000000"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                Email
            </label>

            <input
                type="email"
                value="mahasiswa@email.com"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                Nomor Telepon
            </label>

            <input
                type="text"
                value="08xxxxxxxxxx"
                readonly>

        </div>

    </div>

</div>


<br>


<!-- DATA AKADEMIK -->

<div class="card">

    <h2>
        Data Akademik
    </h2>

    <br>

    <div class="profile-grid">

        <div class="profile-item">

            <label>
                Perguruan Tinggi
            </label>

            <input
                type="text"
                value="Universitas / Perguruan Tinggi"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                Program Studi
            </label>

            <input
                type="text"
                value="Program Studi"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                Semester
            </label>

            <input
                type="text"
                value="Semester"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                Tahun Angkatan
            </label>

            <input
                type="text"
                value="2024"
                readonly>

        </div>

    </div>

</div>


<br>


<!-- KONTAK -->

<div class="card">

    <h2>
        Kontak
    </h2>

    <br>

    <div class="profile-grid">

        <div class="profile-item">

            <label>
                Alamat
            </label>

            <input
                type="text"
                value="Alamat mahasiswa"
                readonly>

        </div>


        <div class="profile-item">

            <label>
                Kota
            </label>

            <input
                type="text"
                value="Kota"
                readonly>

        </div>

    </div>

</div>

@endsection