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
        Lengkapi data berikut untuk mengajukan
        permohonan magang di BPS Kota Batu.
    </p>

</div>


<!-- INFORMASI MAHASISWA -->

<div class="card form-card">

    <h2>
        Data Mahasiswa
    </h2>

    <p class="form-description">
        Pastikan data yang kamu masukkan sudah sesuai
        dengan data akademik.
    </p>

    <div class="form-grid">

        <div class="form-group">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                placeholder="Masukkan nama lengkap">

        </div>


        <div class="form-group">

            <label>
                NIM
            </label>

            <input
                type="text"
                placeholder="Masukkan NIM">

        </div>


        <div class="form-group">

            <label>
                Perguruan Tinggi
            </label>

            <input
                type="text"
                placeholder="Masukkan nama perguruan tinggi">

        </div>


        <div class="form-group">

            <label>
                Program Studi
            </label>

            <input
                type="text"
                placeholder="Masukkan program studi">

        </div>

    </div>

</div>


<br>


<!-- INFORMASI MAGANG -->

<div class="card form-card">

    <h2>
        Informasi Magang
    </h2>

    <p class="form-description">
        Isi informasi mengenai rencana pelaksanaan magang.
    </p>


    <div class="form-grid">

        <div class="form-group">

            <label>
                Unit/Bagian yang Dituju
            </label>

            <select>

                <option value="">
                    Pilih unit/bagian
                </option>

                <option>
                    Subbagian Umum
                </option>

                <option>
                    Statistik Sosial
                </option>

                <option>
                    Statistik Produksi
                </option>

                <option>
                    Statistik Distribusi
                </option>

                <option>
                    Neraca Wilayah dan Analisis Statistik
                </option>

                <option>
                    Lainnya
                </option>

            </select>

        </div>


        <div class="form-group">

            <label>
                Bidang/Posisi Magang
            </label>

            <input
                type="text"
                placeholder="Contoh: Pengembangan Sistem Informasi">

        </div>


        <div class="form-group">

            <label>
                Tanggal Mulai
            </label>

            <input
                type="date">

        </div>


        <div class="form-group">

            <label>
                Tanggal Selesai
            </label>

            <input
                type="date">

        </div>

    </div>


    <div class="form-group">

        <label>
            Tujuan Magang
        </label>

        <textarea
            rows="5"
            placeholder="Jelaskan tujuan pelaksanaan magang..."></textarea>

    </div>


    <div class="form-group">

        <label>
            Keterangan Tambahan
        </label>

        <textarea
            rows="4"
            placeholder="Masukkan keterangan tambahan jika diperlukan..."></textarea>

    </div>

</div>


<br>


<!-- DOKUMEN -->

<div class="card form-card">

    <h2>
        Dokumen Persyaratan
    </h2>

    <p class="form-description">
        Upload dokumen yang diperlukan dalam format PDF.
    </p>


    <div class="form-group">

        <label>
            Surat Pengantar
        </label>

        <input
            type="file"
            accept=".pdf">

        <small>
            Format PDF, maksimal 5 MB.
        </small>

    </div>


    <div class="form-group">

        <label>
            Curriculum Vitae (CV)
        </label>

        <input
            type="file"
            accept=".pdf">

        <small>
            Format PDF, maksimal 5 MB.
        </small>

    </div>


    <div class="form-group">

        <label>
            Proposal Magang
        </label>

        <input
            type="file"
            accept=".pdf">

        <small>
            Format PDF, maksimal 5 MB.
        </small>

    </div>


    <div class="form-group">

        <label>
            Dokumen Pendukung
        </label>

        <input
            type="file"
            accept=".pdf">

        <small>
            Format PDF, maksimal 5 MB.
        </small>

    </div>

</div>


<br>


<!-- PERNYATAAN -->

<div class="card form-card">

    <h2>
        Pernyataan
    </h2>

    <div class="checkbox-group">

        <input
            type="checkbox"
            id="agreement">

        <label for="agreement">

            Saya menyatakan bahwa data dan dokumen
            yang saya berikan adalah benar dan dapat
            dipertanggungjawabkan.

        </label>

    </div>

</div>


<br>


<!-- ACTION -->

<div class="form-actions">

    <button
        type="button"
        class="btn-secondary">

        Simpan Draft

    </button>


    <button
        type="button"
        class="btn-primary">

        Kirim Pengajuan

    </button>

</div>

@endsection