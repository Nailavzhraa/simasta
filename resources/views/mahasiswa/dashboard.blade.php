@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa - SIMASTA')

@section('page-title', 'Dashboard Mahasiswa')

@section('sidebar')

    @include('layouts.mahasiswa')

@endsection

@section('user-role')
    Mahasiswa
@endsection

@section('user-avatar')
    M
@endsection

@section(
    'page-description',
    'Selamat datang di Sistem Informasi Manajemen Magang BPS Kota Batu'
)

@section('content')

<div class="welcome">

    <h1>
        Selamat Datang, Mahasiswa 👋
    </h1>

    <p>
        Kelola kegiatan magang kamu melalui SIMASTA.
    </p>

</div>


<!-- STATISTIK -->

<div class="card-grid">

    <div class="card">

        <div class="card-title">
            Status Pengajuan
        </div>

        <div class="card-number">
            Belum Diajukan
        </div>

    </div>


    <div class="card">

        <div class="card-title">
            Status Magang
        </div>

        <div class="card-number">
            -
        </div>

    </div>


    <div class="card">

        <div class="card-title">
            Total Logbook
        </div>

        <div class="card-number">
            0
        </div>

    </div>


    <div class="card">

        <div class="card-title">
            Nilai Akhir
        </div>

        <div class="card-number">
            -
        </div>

    </div>

</div>


<!-- INFORMASI MAGANG -->

<div class="table-card">

    <h2>
        Informasi Magang
    </h2>

    <table>

        <thead>

            <tr>

                <th>
                    Informasi
                </th>

                <th>
                    Detail
                </th>

                <th>
                    Status
                </th>

            </tr>

        </thead>

        <tbody>

            <tr>

                <td>
                    Instansi
                </td>

                <td>
                    -
                </td>

                <td>

                    <span class="status status-warning">
                        Belum Diajukan
                    </span>

                </td>

            </tr>


            <tr>

                <td>
                    Periode Magang
                </td>

                <td>
                    -
                </td>

                <td>

                    <span class="status status-warning">
                        Belum Ada
                    </span>

                </td>

            </tr>


            <tr>

                <td>
                    Pembimbing
                </td>

                <td>
                    -
                </td>

                <td>

                    <span class="status status-warning">
                        Belum Ada
                    </span>

                </td>

            </tr>

        </tbody>

    </table>

</div>

@endsection