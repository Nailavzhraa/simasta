@extends('layouts.admin')

@section('title', 'Dashboard Admin - SIMASTA')
@section('page-title', 'Dashboard ')
@section('user-role', 'Admin')

@section('user-avatar', 'A')

@section('content')

<div class="welcome">

    <h1>Dashboard Admin</h1>
    <p>Selamat datang di Sistem Informasi Manajemen Magang BPS Kota Batu</p>

</div>

<!-- STATISTIK -->

<div class="card-grid">

    <div class="card stat-card">

        <h3>
            Total Mahasiswa
        </h3>

        <div class="number">
             {{ \App\Models\Mahasiswa::count() }}
        </div>

        <p>
            Mahasiswa terdaftar
        </p>

    </div>


    <div class="card stat-card">

        <h3>
            Pengajuan Magang
        </h3>

        <div class="number">
            0
        </div>

        <p>
            Total pengajuan
        </p>

    </div>


    <div class="card stat-card">

        <h3>
            Menunggu Verifikasi
        </h3>

        <div class="number">
            0
        </div>

        <p>
            Pengajuan perlu diperiksa
        </p>

    </div>


    <div class="card stat-card">

        <h3>
            Pembimbing
        </h3>

        <div class="number">
            0
        </div>

        <p>
            Pembimbing lapangan
        </p>

    </div>

</div>


<br>


<!-- PENGAJUAN TERBARU -->

<div class="card">

    <h2>
        Pengajuan Terbaru
    </h2>

    <p>
        Daftar pengajuan magang mahasiswa terbaru.
    </p>

    <br>

    <div class="table-container">

        <table>

            <thead>

                <tr>

                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>Kampus</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>

            </thead>

            <tbody>

                <tr>

                    <td>-</td>

                    <td>
                        Belum ada data
                    </td>

                    <td>-</td>

                    <td>-</td>

                    <td>

                        <span class="badge badge-warning">
                            Menunggu
                        </span>

                    </td>

                    <td>

                        <button
                            class="btn btn-secondary"
                            disabled>

                            Detail

                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>


<br>


<!-- INFORMASI -->

<div class="card">

    <h2>
        Informasi Sistem
    </h2>

    <br>

    <div class="info-list">

        <div class="info-item">

            <span>
                Sistem
            </span>

            <strong>
                SIMASTA
            </strong>

        </div>


        <div class="info-item">

            <span>
                Instansi
            </span>

            <strong>
                BPS Kota Batu
            </strong>

        </div>


        <div class="info-item">

            <span>
                Status Sistem
            </span>

            <span class="badge badge-success">
                Aktif
            </span>

        </div>

    </div>

</div>


@endsection