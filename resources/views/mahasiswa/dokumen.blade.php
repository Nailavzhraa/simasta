@extends('layouts.app')

@section('title', 'Dokumen Magang - SIMASTA')

@section('page-title', 'Dokumen Magang')

@section('user-role', 'Mahasiswa')

@section('user-avatar', 'M')

@section('sidebar')

    @include('layouts.mahasiswa')

@endsection

@section('content')

<div class="welcome">

    <h1>
        Dokumen Magang
    </h1>

    <p>
        Kelola dokumen persyaratan magang dan pantau
        status verifikasinya.
    </p>

</div>


<!-- INFORMASI -->

<div class="card document-info">

    <div class="info-icon">
        📄
    </div>

    <div>

        <h3>
            Kelengkapan Dokumen
        </h3>

        <p>
            Pastikan seluruh dokumen yang diperlukan
            telah diunggah dan sesuai dengan persyaratan.
        </p>

    </div>

</div>


<br>


<!-- DAFTAR DOKUMEN -->

<div class="card">

    <h2>
        Daftar Dokumen
    </h2>

    <p class="form-description">
        Berikut merupakan dokumen yang diperlukan
        dalam proses pengajuan magang.
    </p>


    <div class="document-list">


        <!-- SURAT PENGANTAR -->

        <div class="document-item">

            <div class="document-icon">
                📄
            </div>

            <div class="document-detail">

                <h3>
                    Surat Pengantar
                </h3>

                <p>
                    Surat pengantar resmi dari perguruan tinggi.
                </p>

                <span class="status status-warning">
                    Belum Diunggah
                </span>

            </div>

            <div class="document-action">

                <button class="btn-primary">
                    Upload
                </button>

            </div>

        </div>


        <!-- CV -->

        <div class="document-item">

            <div class="document-icon">
                📄
            </div>

            <div class="document-detail">

                <h3>
                    Curriculum Vitae (CV)
                </h3>

                <p>
                    Curriculum Vitae mahasiswa.
                </p>

                <span class="status status-warning">
                    Belum Diunggah
                </span>

            </div>

            <div class="document-action">

                <button class="btn-primary">
                    Upload
                </button>

            </div>

        </div>


        <!-- PROPOSAL -->

        <div class="document-item">

            <div class="document-icon">
                📄
            </div>

            <div class="document-detail">

                <h3>
                    Proposal Magang
                </h3>

                <p>
                    Proposal kegiatan magang mahasiswa.
                </p>

                <span class="status status-warning">
                    Belum Diunggah
                </span>

            </div>

            <div class="document-action">

                <button class="btn-primary">
                    Upload
                </button>

            </div>

        </div>


        <!-- KHS -->

        <div class="document-item">

            <div class="document-icon">
                📄
            </div>

            <div class="document-detail">

                <h3>
                    KHS / Transkrip Nilai
                </h3>

                <p>
                    Dokumen akademik terbaru mahasiswa.
                </p>

                <span class="status status-warning">
                    Belum Diunggah
                </span>

            </div>

            <div class="document-action">

                <button class="btn-primary">
                    Upload
                </button>

            </div>

        </div>


        <!-- DOKUMEN PENDUKUNG -->

        <div class="document-item">

            <div class="document-icon">
                📄
            </div>

            <div class="document-detail">

                <h3>
                    Dokumen Pendukung
                </h3>

                <p>
                    Dokumen tambahan apabila diperlukan.
                </p>

                <span class="status status-warning">
                    Belum Diunggah
                </span>

            </div>

            <div class="document-action">

                <button class="btn-primary">
                    Upload
                </button>

            </div>

        </div>


    </div>

</div>


<br>


<!-- KETERANGAN -->

<div class="card">

    <h2>
        Ketentuan Dokumen
    </h2>

    <ul class="document-rules">

        <li>
            Dokumen harus dalam format PDF.
        </li>

        <li>
            Dokumen harus dapat dibaca dengan jelas.
        </li>

        <li>
            Pastikan dokumen yang diunggah merupakan
            dokumen terbaru.
        </li>

        <li>
            Ukuran file maksimal mengikuti ketentuan
            yang ditetapkan oleh BPS Kota Batu.
        </li>

        <li>
            Dokumen yang ditolak dapat diperbaiki dan
            diunggah kembali.
        </li>

    </ul>

</div>

@endsection