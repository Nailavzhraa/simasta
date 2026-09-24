@extends('layouts.admin')

@section('title', 'Pengajuan Magang - SIMASTA')

@section('page-title', 'Pengajuan Magang')

@section('user-role', 'Admin')

@section('user-avatar', 'A')

@section('sidebar')
    @include('layouts.admin')
@endsection

@section('content')

<div class="card">

    <div style="margin-bottom: 25px;">
        <h2>Pengajuan Magang</h2>
        <p>Kelola dan verifikasi pengajuan magang mahasiswa.</p>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Tempat Magang</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>1</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>
                        <button class="btn-primary">Menunggu</span>
                    </td>
                    <td>
                        <button class="btn-primary">Detail</button>
                    </td>
                </tr>

                <tr>
                    <td colspan="7" style="text-align:center;">
                        Belum ada data pengajuan magang.
                    </td>
                </tr>
            </tbody>

        </table>
    </div>

</div>

@endsection