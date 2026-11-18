@extends('layouts.app')

@section('title', 'Penilaian & Sertifikat - SIMASTA')
@section('page-title', 'Penilaian & Sertifikat')
@section('user-role', 'Mahasiswa')
@section('user-avatar', 'M')

@section('sidebar')
    @include('layouts.mahasiswa')
@endsection

@section('content')

@php
    $tabAktif = request('tab') === 'sertifikat'
        ? 'sertifikat'
        : 'penilaian';

    $modePreview = request()->routeIs('preview.mahasiswa.penilaian');

    $adaTemplate = $berkas
        && ($berkas->template_path || $berkas->template_url);

    $penilaianSelesai = $berkas?->status === 'selesai';

    $sertifikatTerbit = $sertifikat?->status === 'terbit';

    $tanggalTemplate = $berkas?->template_dikirim_at
        ? $berkas->template_dikirim_at->format('d/m/Y H:i')
        : '-';

    $tanggalHasil = $berkas?->hasil_dikirim_at
        ? $berkas->hasil_dikirim_at->format('d/m/Y H:i')
        : '-';
@endphp

<style>
    .sim-penilaian {
        font-family: Arial, sans-serif;
        font-size: 12px;
        color: #334155;
        width: 100%;
    }

    .sim-penilaian * {
        box-sizing: border-box;
    }

    .sim-penilaian .sp-hero {
        padding: 23px;
        margin-bottom: 17px;
        border: 1px solid #dce8f4;
        border-radius: 14px;
        background: linear-gradient(
            110deg,
            #ffffff 0%,
            #edf5ff 65%,
            #e8f7f0 100%
        );
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .sim-penilaian .sp-eyebrow {
        color: #2563eb;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .7px;
        margin-bottom: 9px;
    }

    .sim-penilaian .sp-hero h1 {
        color: #163f71;
        font-size: 23px;
        font-weight: 700;
        margin: 0 0 8px;
    }

    .sim-penilaian .sp-hero p {
        max-width: 650px;
        margin: 0;
        color: #64748b;
        font-size: 12px;
        line-height: 1.7;
    }

    .sim-penilaian .sp-icon {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #e8f0ff;
        color: #2563eb;
    }

    .sim-penilaian .sp-icon.green {
        background: #dcfce7;
        color: #059669;
    }

    .sim-penilaian .sp-icon.orange {
        background: #ffedd5;
        color: #ea580c;
    }

    .sim-penilaian .sp-icon svg,
    .sim-penilaian .sp-btn svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .sim-penilaian .sp-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 13px;
        margin-bottom: 17px;
    }

    .sim-penilaian .sp-stat {
        min-width: 0;
        padding: 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #ffffff;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sim-penilaian .sp-stat-label {
        color: #64748b;
        font-size: 11px;
        margin-bottom: 6px;
    }

    .sim-penilaian .sp-stat-value {
        color: #163f71;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.45;
    }

    .sim-penilaian .sp-info {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
        margin-bottom: 17px;
    }

    .sim-penilaian .sp-info span {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 12px;
        color: #64748b;
        font-size: 11px;
    }

    .sim-penilaian .sp-info strong {
        color: #163f71;
    }

    .sim-penilaian .sp-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 17px;
    }

    .sim-penilaian .sp-tab {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        color: #64748b;
        background: #ffffff;
        text-decoration: none;
        font-weight: 700;
        font-size: 12px;
    }

    .sim-penilaian .sp-tab.active {
        background: #eff6ff;
        color: #1d4ed8;
        border-color: #93c5fd;
    }

    .sim-penilaian .sp-panel {
        padding: 20px;
        margin-bottom: 17px;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: #ffffff;
    }

    .sim-penilaian .sp-panel-head {
        display: flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 18px;
    }

    .sim-penilaian .sp-panel-head h2 {
        color: #163f71;
        font-size: 16px;
        font-weight: 700;
        margin: 0 0 5px;
    }

    .sim-penilaian .sp-panel-head p {
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.6;
        margin: 0;
    }

    .sim-penilaian .sp-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
    }

    .sim-penilaian .sp-badge::before {
        content: "";
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .sim-penilaian .sp-badge.wait {
        background: #fff7ed;
        color: #c2410c;
    }

    .sim-penilaian .sp-badge.done {
        background: #ecfdf5;
        color: #047857;
    }

    .sim-penilaian .sp-badge.empty {
        background: #f1f5f9;
        color: #64748b;
    }

    .sim-penilaian .sp-help {
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.7;
        margin-top: 8px;
    }

    .sim-penilaian .sp-field {
        margin-bottom: 17px;
    }

    .sim-penilaian .sp-field label {
        display: block;
        margin-bottom: 8px;
        color: #334155;
        font-size: 12px;
        font-weight: 700;
    }

    .sim-penilaian .sp-input,
    .sim-penilaian .sp-textarea {
        display: block;
        width: 100%;
        border: 1px solid #dbe3ee;
        border-radius: 9px;
        background: #f8fafc;
        color: #334155;
        outline: none;
        padding: 11px 12px;
        font: 12px Arial, sans-serif;
    }

    .sim-penilaian .sp-textarea {
        min-height: 95px;
        line-height: 1.7;
        resize: vertical;
    }

    .sim-penilaian .sp-input:focus,
    .sim-penilaian .sp-textarea:focus {
        border-color: #2563eb;
        background: white;
        box-shadow: 0 0 0 3px rgba(37,99,235,.08);
    }

    .sim-penilaian .sp-upload {
        border: 1px dashed #93c5fd;
        border-radius: 11px;
        background: #f8fbff;
        padding: 22px 16px;
        text-align: center;
    }

    .sim-penilaian .sp-upload .sp-icon {
        margin: 0 auto 11px;
    }

    .sim-penilaian .sp-upload strong {
        display: block;
        color: #163f71;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .sim-penilaian .sp-upload input {
        max-width: 100%;
        margin-top: 12px;
        font-size: 11px;
    }

    .sim-penilaian .sp-divider {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 18px 0;
        font-size: 10px;
        color: #94a3b8;
    }

    .sim-penilaian .sp-divider::before,
    .sim-penilaian .sp-divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    .sim-penilaian .sp-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 39px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: #2563eb;
        color: #ffffff;
        text-decoration: none;
        font: 700 12px Arial, sans-serif;
        padding: 10px 14px;
        cursor: pointer;
    }

    .sim-penilaian .sp-btn:hover {
        background: #1d4ed8;
        color: #ffffff;
    }

    .sim-penilaian .sp-btn.secondary {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1d4ed8;
    }

    .sim-penilaian .sp-btn.secondary:hover {
        background: #dbeafe;
    }

    .sim-penilaian .sp-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 15px;
        border-top: 1px solid #f1f5f9;
    }

    .sim-penilaian .sp-document {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding: 15px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 12px;
    }

    .sim-penilaian .sp-document-main {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sim-penilaian .sp-document-name {
        font-size: 12px;
        font-weight: 700;
        color: #163f71;
        margin-bottom: 5px;
        overflow-wrap: anywhere;
    }

    .sim-penilaian .sp-document-date {
        font-size: 11px;
        color: #94a3b8;
    }

    .sim-penilaian .sp-empty {
        text-align: center;
        padding: 28px 16px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 10px;
    }

    .sim-penilaian .sp-empty strong {
        display: block;
        margin-bottom: 7px;
        font-size: 13px;
        color: #163f71;
    }

    .sim-penilaian .sp-empty p {
        margin: 0;
        color: #94a3b8;
        font-size: 11px;
        line-height: 1.8;
    }

    .sim-penilaian .sp-note {
        padding: 13px;
        margin-top: 13px;
        background: #eff6ff;
        border: 1px solid #dbeafe;
        border-radius: 9px;
        color: #475569;
        line-height: 1.7;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .sim-penilaian .sp-note strong {
        display: block;
        margin-bottom: 4px;
        color: #1d4ed8;
    }

    .sim-penilaian .sp-alert {
        padding: 12px 15px;
        margin-bottom: 16px;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #047857;
        border-radius: 9px;
    }

    .sim-penilaian .sp-alert.error {
        background: #fef2f2;
        border-color: #fecaca;
        color: #b91c1c;
    }

    .sim-penilaian .sp-certificate {
        text-align: center;
        padding: 35px 20px;
        border: 1px dashed #cbd5e1;
        border-radius: 12px;
        background: linear-gradient(
            120deg,
            #f8fbff,
            #f0fdf4
        );
    }

    .sim-penilaian .sp-certificate .sp-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 15px;
    }

    .sim-penilaian .sp-certificate h3 {
        color: #163f71;
        font-size: 17px;
        margin: 17px 0 9px;
    }

    .sim-penilaian .sp-certificate p {
        color: #64748b;
        line-height: 1.8;
        max-width: 470px;
        margin: 0 auto 14px;
    }

    @media (max-width: 800px) {
        .sim-penilaian .sp-stats {
            grid-template-columns: 1fr;
        }

        .sim-penilaian .sp-hero {
            padding: 18px;
        }

        .sim-penilaian .sp-hero h1 {
            font-size: 20px;
        }

        .sim-penilaian .sp-panel {
            padding: 15px;
        }

        .sim-penilaian .sp-actions .sp-btn {
            width: 100%;
        }
    }
</style>

<div class="sim-penilaian">

    <!-- HEADER -->
    <div class="sp-hero">
        <div>

            <h1>Penilaian & Sertifikat</h1>

            <p>
                Kirim template penilaian dari universitas,
                pantau dokumen yang diproses BPS Kota Batu,
                dan terima sertifikat magang setelah diterbitkan.
            </p>
        </div>

        <div class="sp-icon">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="8" r="5"/>
                <path d="m8 13-2 8 6-3 6 3-2-8"/>
            </svg>
        </div>
    </div>

    <!-- NOTIFIKASI -->
    @if(session('success'))
        <div class="sp-alert">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="sp-alert error">
            <strong>Periksa pengisian dokumen:</strong>
            <ul style="margin:8px 0 0;padding-left:18px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- STATISTIK -->
    <div class="sp-stats">
        <div class="sp-stat">
            <div class="sp-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M4 4h16v16H4z"/>
                    <path d="M8 9h8M8 13h8"/>
                </svg>
            </div>

            <div>
                <div class="sp-stat-label">Template Kampus</div>
                <div class="sp-stat-value">
                    {{ $adaTemplate ? 'Sudah Dikirim' : 'Belum Dikirim' }}
                </div>
            </div>
        </div>

        <div class="sp-stat">
            <div class="sp-icon orange">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"/>
                    <path d="M12 7v5l3 2"/>
                </svg>
            </div>

            <div>
                <div class="sp-stat-label">Hasil Penilaian BPS</div>
                <div class="sp-stat-value">
                    {{ $penilaianSelesai ? 'Selesai' : 'Menunggu' }}
                </div>
            </div>
        </div>

        <div class="sp-stat">
            <div class="sp-icon green">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="5"/>
                    <path d="m8 13-2 8 6-3 6 3-2-8"/>
                </svg>
            </div>

            <div>
                <div class="sp-stat-label">Sertifikat Magang</div>
                <div class="sp-stat-value">
                    {{ $sertifikatTerbit ? 'Sudah Terbit' : 'Belum Terbit' }}
                </div>
            </div>
        </div>
    </div>

    <!-- INFORMASI PENEMPATAN -->
    <div class="sp-info">
        <span>
            Divisi:
            <strong>
                {{ $pengajuan->divisi?->nama ?? 'Belum ditentukan' }}
            </strong>
        </span>

        <span>
            Pembimbing:
            <strong>
                {{ $pengajuan->pembimbingRef?->nama ?? 'Belum ditentukan' }}
            </strong>
        </span>

        <span>
            Status:
            <strong>Diterima Magang</strong>
        </span>
    </div>

    <!-- TAB -->
    <div class="sp-tabs">
        <a
            href="{{ route('mahasiswa.penilaian', ['tab' => 'penilaian']) }}"
            class="sp-tab {{ $tabAktif === 'penilaian' ? 'active' : '' }}"
        >
            Penilaian Magang
        </a>

        <a
            href="{{ route('mahasiswa.penilaian', ['tab' => 'sertifikat']) }}"
            class="sp-tab {{ $tabAktif === 'sertifikat' ? 'active' : '' }}"
        >
            Sertifikat Magang
        </a>
    </div>

    @if($tabAktif === 'penilaian')

        <!-- STATUS PENILAIAN -->
        <div class="sp-panel">
            <div class="sp-panel-head">
                <div class="sp-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v5l3 2"/>
                    </svg>
                </div>

                <div>
                    <h2>Status Penilaian</h2>
                    <p>
                        Status dokumen berdasarkan proses pengisian
                        oleh pihak BPS Kota Batu.
                    </p>
                </div>
            </div>

            @if($penilaianSelesai)
                <span class="sp-badge done">
                    Penilaian Selesai
                </span>
            @elseif($adaTemplate)
                <span class="sp-badge wait">
                    Menunggu Penilaian BPS
                </span>
            @else
                <span class="sp-badge empty">
                    Menunggu Template Kampus
                </span>
            @endif

            <div class="sp-help">
                @if($penilaianSelesai)
                    Dokumen hasil penilaian telah dikirim oleh
                    Admin BPS dan dapat kamu akses di bawah.
                @elseif($adaTemplate)
                    Template telah dikirim.
                    Admin BPS akan mengisi penilaian menggunakan
                    format yang disediakan universitas.
                @else
                    Silakan unggah file atau kirim tautan template
                    penilaian dari universitas terlebih dahulu.
                @endif
            </div>
        </div>

        <!-- FORM TEMPLATE -->
        @if(!$penilaianSelesai)

            <div class="sp-panel">
                <div class="sp-panel-head">
                    <div class="sp-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 16V3M7 8l5-5 5 5"/>
                            <path d="M4 16v4h16v-4"/>
                        </svg>
                    </div>

                    <div>
                        <h2>
                            {{ $adaTemplate
                                ? 'Perbarui Template Penilaian'
                                : 'Kirim Template Penilaian Kampus' }}
                        </h2>
                        <p>
                            Kirim format penilaian resmi yang
                            diberikan oleh universitas.
                        </p>
                    </div>
                </div>

                <form
                   id="sp-template-form"
                   method="POST"
                   action="{{ $modePreview ? '#' : route('mahasiswa.penilaian.template.kirim') }}"
                   enctype="multipart/form-data"
                >
                    @csrf

                    <div class="sp-field">
                        <label for="sp-template-file">
                            Upload File Template
                        </label>

                        <div class="sp-upload">
                            <div class="sp-icon">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 16V3M7 8l5-5 5 5"/>
                                    <path d="M4 16v4h16v-4"/>
                                </svg>
                            </div>

                            <strong>
                                Pilih dokumen penilaian dari kampus
                            </strong>

                            <div class="sp-help">
                                PDF, DOC, DOCX, XLS, XLSX.
                                Ukuran maksimal 10 MB.
                            </div>

                            <input
                                id="sp-template-file"
                                type="file"
                                name="template_file"
                                accept=".pdf,.doc,.docx,.xls,.xlsx"
                            >
                        </div>
                    </div>

                    <div class="sp-divider">
                        ATAU GUNAKAN TAUTAN
                    </div>

                    <div class="sp-field">
                        <label for="sp-template-url">
                            Link Google Drive
                        </label>

                        <input
                            id="sp-template-url"
                            class="sp-input"
                            type="url"
                            name="template_url"
                            maxlength="2000"
                            placeholder="https://drive.google.com/..."
                            value="{{ old('template_url') }}"
                        >

                        <div class="sp-help">
                            Isi file atau tautan. Pastikan admin
                            BPS sudah mendapatkan akses ke dokumen.
                        </div>
                    </div>

                    <div class="sp-field">
                        <label for="sp-note">
                            Catatan untuk Admin
                        </label>

                        <textarea
                            id="sp-note"
                            name="catatan_mahasiswa"
                            class="sp-textarea"
                            maxlength="2000"
                            placeholder="Contoh: Mohon mengisi bagian evaluasi dan tanda tangan pembimbing."
                        >{{ old('catatan_mahasiswa', $berkas?->catatan_mahasiswa) }}</textarea>
                    </div>

                    <div class="sp-actions">
                        <div class="sp-help" style="margin:0">
                            Template akan dikirim ke dashboard
                            Penilaian Admin.
                        </div>

<button
    type="submit"
    class="sp-btn"
    @disabled($modePreview)
>
                            <svg viewBox="0 0 24 24">
                                <path d="M22 2 11 13"/>
                                <path d="m22 2-7 20-4-9-9-4z"/>
                            </svg>

                            {{ $adaTemplate
                                ? 'Kirim Pembaruan'
                                : 'Kirim ke Admin BPS' }}
                        </button>
                    </div>
                </form>
            </div>

        @endif

        <!-- TEMPLATE TERKIRIM -->
        <div class="sp-panel">
            <div class="sp-panel-head">
                <div class="sp-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 3h10l4 4v14H5z"/>
                        <path d="M14 3v5h5M8 12h8M8 16h6"/>
                    </svg>
                </div>

                <div>
                    <h2>Template Kampus Terkirim</h2>
                    <p>Riwayat dokumen template yang dikirim.</p>
                </div>
            </div>

            @if($adaTemplate)
                <div class="sp-document">
                    <div class="sp-document-main">
                        <div class="sp-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 3h10l4 4v14H5z"/>
                                <path d="M14 3v5h5"/>
                            </svg>
                        </div>

                        <div>
                            <div class="sp-document-name">
                                {{ $berkas->template_nama
                                    ?: 'Template Penilaian Kampus' }}
                            </div>

                            <div class="sp-document-date">
                                Dikirim: {{ $tanggalTemplate }}
                            </div>
                        </div>
                    </div>

                    <span class="sp-badge done">
                        Terkirim
                    </span>
                </div>

                @if($berkas->template_url)
                    <a
                        class="sp-btn secondary"
                        href="{{ $berkas->template_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        Buka Template di Google Drive
                    </a>
                @endif

                @if($berkas->template_path)
                    <div class="sp-help">
                        File sudah tersimpan secara privat
                        dan tersedia bagi Admin BPS.
                    </div>
                @endif

            @else
                <div class="sp-empty">
                    <strong>Belum Ada Template</strong>
                    <p>
                        Kamu belum mengirim template penilaian
                        dari universitas.
                    </p>
                </div>
            @endif
        </div>

        <!-- HASIL PENILAIAN -->
        <div class="sp-panel">
            <div class="sp-panel-head">
                <div class="sp-icon green">
                    <svg viewBox="0 0 24 24">
                        <path d="M4 4h16v16H4z"/>
                        <path d="m8 12 3 3 5-6"/>
                    </svg>
                </div>

                <div>
                    <h2>Hasil Penilaian BPS</h2>
                    <p>
                        File yang dikirim setelah pihak
                        BPS menyelesaikan penilaian magang.
                    </p>
                </div>
            </div>

            @if(
                $penilaianSelesai &&
                ($berkas->hasil_path || $berkas->hasil_url)
            )
                <div class="sp-document">
                    <div class="sp-document-main">
                        <div class="sp-icon green">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 3h10l4 4v14H5z"/>
                                <path d="M14 3v5h5M8 12h8"/>
                            </svg>
                        </div>

                        <div>
                            <div class="sp-document-name">
                                {{ $berkas->hasil_nama
                                    ?: 'Hasil Penilaian Magang BPS' }}
                            </div>

                            <div class="sp-document-date">
                                Dikirim: {{ $tanggalHasil }}
                            </div>
                        </div>
                    </div>

                    <span class="sp-badge done">Selesai</span>
                </div>

                <div style="display:flex;gap:9px;flex-wrap:wrap">
                    @if($berkas->hasil_path)
                        <a
                            class="sp-btn"
                            href="{{ route('mahasiswa.penilaian.hasil.unduh', $berkas->id) }}"
                        >
                            Unduh Hasil Penilaian
                        </a>
                    @endif

                    @if($berkas->hasil_url)
                        <a
                            class="sp-btn secondary"
                            href="{{ $berkas->hasil_url }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            Buka Link Hasil BPS
                        </a>
                    @endif
                </div>

                @if($berkas->catatan_admin)
                    <div class="sp-note">
                        <strong>Catatan dari Admin BPS</strong>
                        {{ $berkas->catatan_admin }}
                    </div>
                @endif
            @else
                <div class="sp-empty">
                    <strong>Belum Ada Hasil Penilaian</strong>
                    <p>
                        Dokumen yang sudah diisi oleh pihak BPS
                        akan muncul di sini setelah dikirim admin.
                    </p>
                </div>
            @endif
        </div>

    @else

        <!-- SERTIFIKAT MAGANG -->
        <div class="sp-panel">
            <div class="sp-panel-head">
                <div class="sp-icon green">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="5"/>
                        <path d="m8 13-2 8 6-3 6 3-2-8"/>
                    </svg>
                </div>

                <div>
                    <h2>Sertifikat Magang</h2>
                    <p>
                        Sertifikat resmi dari BPS Kota Batu
                        setelah diterbitkan oleh admin.
                    </p>
                </div>
            </div>

            @if($sertifikatTerbit)
                <div class="sp-certificate">
                    <div class="sp-icon green">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="5"/>
                            <path d="m8 13-2 8 6-3 6 3-2-8"/>
                        </svg>
                    </div>

                    <span class="sp-badge done">
                        Sudah Terbit
                    </span>

                    <h3>Sertifikat Magang Telah Diterbitkan</h3>

                    <p>
                        Sertifikat atas nama
                        <strong>{{ $sertifikat->nama_penerima }}</strong>
                        telah diterbitkan oleh BPS Kota Batu.
                    </p>

                    <div class="sp-document-name">
                        Nomor:
                        {{ $sertifikat->nomor_sertifikat ?? '-' }}
                    </div>

                    <div class="sp-help">
                        Tanggal terbit:
                        {{ $sertifikat->tanggal_terbit?->format('d/m/Y') ?? '-' }}
                    </div>

                    <div class="sp-help">
                        Dokumen PDF dapat diunduh setelah
                        fitur unduh sertifikat dipasang.
                    </div>
                </div>
            @else
                <div class="sp-certificate">
                    <div class="sp-icon orange">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="5"/>
                            <path d="m8 13-2 8 6-3 6 3-2-8"/>
                        </svg>
                    </div>

                    <span class="sp-badge wait">
                        Menunggu Penerbitan
                    </span>

                    <h3>Sertifikat Belum Tersedia</h3>

                    <p>
                        Sertifikat akan muncul di halaman ini
                        setelah Admin BPS Kota Batu
                        menyelesaikan proses penerbitan.
                    </p>
                </div>
            @endif
        </div>

    @endif

</div>

@if($tabAktif === 'penilaian' && !$penilaianSelesai)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('sp-template-form');
    const file = document.getElementById('sp-template-file');
    const url = document.getElementById('sp-template-url');

    if (!form || !file || !url) return;

    form.addEventListener('submit', function (event) {
        if (!file.files.length && !url.value.trim()) {
            event.preventDefault();
            alert(
                'Unggah template penilaian atau isi link Google Drive terlebih dahulu.'
            );
        }
    });
});
</script>
@endif

@endsection
