
@extends('layouts.admin')

@section('title', 'Kelola Tim Kerja - SIMASTA')
@section('page-title', 'Kelola Tim Kerja')
@section('user-role', 'Admin')
@section('user-avatar', 'A')

@section('content')
@php
    $tabAktif = request('tab', 'divisi');
    if (!in_array($tabAktif, ['divisi', 'tim'], true)) {
        $tabAktif = 'divisi';
    }
@endphp

<style>
    .kt-wrap {
        width: 100%;
        color: #334155;
        font-family: Arial, sans-serif;
    }

    .kt-hero {
        padding: 25px 28px;
        margin-bottom: 22px;
        border: 1px solid #dbe7f2;
        border-radius: 16px;
        background: linear-gradient(
            110deg, #ffffff, #f1f7ff, #eaf8f0
        );
    }

    .kt-eyebrow {
        font-size: 11px;
        font-weight: 700;
        color: #2563eb;
        letter-spacing: 1px;
        margin-bottom: 9px;
    }

    .kt-hero h1 {
        margin: 0 0 9px;
        font-size: 26px;
        color: #123e70;
    }

    .kt-hero p {
        margin: 0;
        font-size: 13px;
        line-height: 1.7;
        color: #64748b;
    }

    .kt-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }

    .kt-stat {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 13px;
        background: white;
    }

    .kt-stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        border-radius: 11px;
        background: #eaf2ff;
        color: #2563eb;
        font-size: 21px;
    }

    .kt-stat:nth-child(2) .kt-stat-icon {
        background: #fff1df;
        color: #e98a25;
    }

    .kt-stat:nth-child(3) .kt-stat-icon {
        background: #e4f8ed;
        color: #15975e;
    }

    .kt-stat strong {
        display: block;
        font-size: 23px;
        color: #123e70;
    }

    .kt-stat small {
        color: #64748b;
        font-size: 12px;
    }

    .kt-tabs {
        display: flex;
        gap: 8px;
        padding: 6px;
        margin-bottom: 20px;
        background: #eef3f9;
        border-radius: 12px;
    }

    .kt-tab {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 9px;
        padding: 13px 16px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }

    .kt-tab.active {
        background: #fff;
        color: #2563eb;
        box-shadow: 0 2px 8px rgba(20, 53, 91, .07);
    }

    .kt-card {
        padding: 23px;
        margin-bottom: 20px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
    }

    .kt-card h2 {
        font-size: 17px;
        margin: 0 0 6px;
        color: #123e70;
    }

    .kt-desc {
        font-size: 12px;
        color: #64748b;
        margin: 0 0 20px;
        line-height: 1.7;
    }

    .kt-form {
        display: grid;
        grid-template-columns: 1fr 1.5fr auto;
        gap: 12px;
        align-items: end;
    }

    .kt-form.tim {
        grid-template-columns: 1fr 1fr 1.5fr auto;
    }

    .kt-field label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 7px;
    }

    .kt-field input,
    .kt-field select,
    .kt-field textarea,
    .kt-edit input,
    .kt-edit select,
    .kt-edit textarea {
        display: block;
        width: 100%;
        min-height: 40px;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #dbe3ed;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        color: #334155;
        background: #fff;
    }

    .kt-field textarea,
    .kt-edit textarea {
        resize: vertical;
        min-height: 40px;
    }

    .kt-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 10px 14px;
        border: 1px solid transparent;
        border-radius: 8px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        white-space: nowrap;
    }

    .kt-primary {
        background: #2563eb;
        color: white;
    }

    .kt-primary:hover {
        background: #1d4ed8;
    }

    .kt-outline {
        background: white;
        color: #475569;
        border-color: #dbe3ed;
    }

    .kt-danger {
        background: #fff1f2;
        color: #be123c;
        border-color: #fecdd3;
    }

    .kt-alert {
        padding: 13px 16px;
        border-radius: 9px;
        margin-bottom: 18px;
        font-size: 13px;
    }

    .kt-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .kt-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .kt-table-wrap {
        overflow-x: auto;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
    }

    .kt-table {
        width: 100%;
        min-width: 700px;
        border-collapse: collapse;
    }

    .kt-table th {
        padding: 13px 15px;
        background: #f8fafc;
        color: #2563eb;
        text-align: left;
        font-size: 11px;
        text-transform: uppercase;
        border-bottom: 1px solid #e2e8f0;
    }

    .kt-table td {
        padding: 14px 15px;
        border-bottom: 1px solid #edf2f7;
        font-size: 13px;
        vertical-align: top;
    }

    .kt-table tr:last-child td {
        border-bottom: none;
    }

    .kt-actions {
        display: flex;
        gap: 7px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .kt-actions form {
        margin: 0;
    }

    .kt-edit summary {
        list-style: none;
        cursor: pointer;
    }

    .kt-edit summary::-webkit-details-marker {
        display: none;
    }

    .kt-edit form {
        display: grid;
        gap: 9px;
        min-width: 230px;
        padding-top: 12px;
    }

    .kt-empty {
        text-align: center;
        padding: 30px !important;
        color: #94a3b8;
    }

    .kt-badge {
        display: inline-flex;
        padding: 5px 9px;
        background: #eff6ff;
        color: #1d4ed8;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 700;
    }

    .kt-muted {
        color: #94a3b8;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .kt-form,
        .kt-form.tim {
            grid-template-columns: 1fr;
        }

        .kt-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 600px) {
        .kt-hero {
            padding: 20px;
        }

        .kt-hero h1 {
            font-size: 22px;
        }

        .kt-card {
            padding: 16px;
        }
    }
</style>

<div class="kt-wrap">

    <div class="kt-hero">

        <h1>Kelola Tim Kerja</h1>

        <p>
            Kelola struktur divisi dan tim kerja untuk mendukung
            pengelolaan pembimbing serta penempatan mahasiswa magang.
        </p>
    </div>

    <div class="kt-stats">
        <div class="kt-stat">
            <div class="kt-stat-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div>
                <strong>{{ $divisis->count() }}</strong>
                <small>Total Divisi</small>
            </div>
        </div>

        <div class="kt-stat">
            <div class="kt-stat-icon">
                <i class="bi bi-people"></i>
            </div>
            <div>
                <strong>{{ $timKerjas->count() }}</strong>
                <small>Total Tim Kerja</small>
            </div>
        </div>

        <div class="kt-stat">
            <div class="kt-stat-icon">
                <i class="bi bi-person-badge"></i>
            </div>
            <div>
                <strong>{{ $divisis->sum('pembimbings_count') }}</strong>
                <small>Total Pembimbing</small>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="kt-alert kt-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="kt-alert kt-error">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="kt-alert kt-error">
            <strong>Periksa kembali data:</strong>
            <ul style="margin:8px 0 0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="kt-tabs">
        <a
            href="{{ route('admin.divisi.index', ['tab' => 'divisi']) }}"
            class="kt-tab {{ $tabAktif === 'divisi' ? 'active' : '' }}"
        >
            <i class="bi bi-diagram-3"></i>
            Divisi
        </a>

        <a
            href="{{ route('admin.divisi.index', ['tab' => 'tim']) }}"
            class="kt-tab {{ $tabAktif === 'tim' ? 'active' : '' }}"
        >
            <i class="bi bi-people"></i>
            Tim Kerja
        </a>
    </div>

    @if($tabAktif === 'divisi')

        <div class="kt-card">
            <h2>Tambah Divisi</h2>
            <p class="kt-desc">
                Tambahkan divisi beserta jobdesk bawaan yang dapat
                digunakan pada proses penempatan magang.
            </p>

            <form
                class="kt-form"
                method="POST"
                action="{{ route('admin.divisi.simpan') }}"
            >
                @csrf

                <div class="kt-field">
                    <label for="namaDivisi">Nama Divisi</label>
                    <input
                        id="namaDivisi"
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        maxlength="150"
                        placeholder="Masukkan nama divisi"
                        required
                    >
                </div>

                <div class="kt-field">
                    <label for="jobdeskDivisi">Jobdesk Bawaan</label>
                    <textarea
                        id="jobdeskDivisi"
                        name="jobdesk"
                        maxlength="2000"
                        placeholder="Deskripsi tugas divisi"
                    >{{ old('jobdesk') }}</textarea>
                </div>

                <button type="submit" class="kt-btn kt-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Divisi
                </button>
            </form>
        </div>

        <div class="kt-card">
            <h2>Daftar Divisi</h2>
            <p class="kt-desc">
                Data divisi yang digunakan dalam pengelolaan magang.
            </p>

            <div class="kt-table-wrap">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th>Divisi</th>
                            <th>Jobdesk</th>
                            <th>Pembimbing</th>
                            <th>Tim Kerja</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($divisis as $divisi)
                            <tr>
                                <td>
                                    <strong>{{ $divisi->nama }}</strong>
                                </td>

                                <td>
                                    {{ $divisi->jobdesk ?: '-' }}
                                </td>

                                <td>
                                    <span class="kt-badge">
                                        {{ $divisi->pembimbings_count }} orang
                                    </span>
                                </td>

                                <td>
                                    <span class="kt-badge">
                                        {{ $divisi->tim_kerjas_count }} tim
                                    </span>
                                </td>

                                <td>
                                    <div class="kt-actions">
                                        <details class="kt-edit">
                                            <summary class="kt-btn kt-outline">
                                                Edit
                                            </summary>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.divisi.update', $divisi) }}"
                                            >
                                                @csrf
                                                @method('PUT')

                                                <input
                                                    type="text"
                                                    name="nama"
                                                    value="{{ $divisi->nama }}"
                                                    maxlength="150"
                                                    required
                                                >

                                                <textarea
                                                    name="jobdesk"
                                                    maxlength="2000"
                                                    placeholder="Jobdesk divisi"
                                                >{{ $divisi->jobdesk }}</textarea>

                                                <button
                                                    type="submit"
                                                    class="kt-btn kt-primary"
                                                >
                                                    Simpan Perubahan
                                                </button>
                                            </form>
                                        </details>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.divisi.hapus', $divisi) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus divisi ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="kt-btn kt-danger"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="kt-empty">
                                    Belum ada data divisi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @else

        <div class="kt-card">
            <h2>Tambah Tim Kerja</h2>
            <p class="kt-desc">
                Setiap tim kerja berada di bawah satu divisi.
                Pilih divisi induk sebelum menambahkan tim kerja.
            </p>

            <form
                class="kt-form tim"
                method="POST"
                action="{{ route('admin.divisi.tim.simpan') }}"
            >
                @csrf

                <div class="kt-field">
                    <label for="divisiTim">Divisi Induk</label>

                    <select id="divisiTim" name="divisi_id" required>
                        <option value="">Pilih divisi</option>

                        @foreach($divisis as $divisi)
                            <option
                                value="{{ $divisi->id }}"
                                @selected(old('divisi_id') == $divisi->id)
                            >
                                {{ $divisi->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="kt-field">
                    <label for="namaTim">Nama Tim Kerja</label>

                    <input
                        id="namaTim"
                        type="text"
                        name="nama"
                        value="{{ old('nama') }}"
                        maxlength="150"
                        placeholder="Nama tim kerja"
                        required
                    >
                </div>

                <div class="kt-field">
                    <label for="deskripsiTim">Deskripsi Tugas</label>

                    <textarea
                        id="deskripsiTim"
                        name="deskripsi"
                        maxlength="2000"
                        placeholder="Deskripsi tugas tim kerja"
                    >{{ old('deskripsi') }}</textarea>
                </div>

                <button type="submit" class="kt-btn kt-primary">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Tim
                </button>
            </form>
        </div>

        <div class="kt-card">
            <h2>Daftar Tim Kerja</h2>
            <p class="kt-desc">
                Daftar tim kerja yang telah terhubung dengan divisi.
            </p>

            <div class="kt-table-wrap">
                <table class="kt-table">
                    <thead>
                        <tr>
                            <th>Nama Tim Kerja</th>
                            <th>Divisi Induk</th>
                            <th>Deskripsi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($timKerjas as $tim)
                            <tr>
                                <td>
                                    <strong>{{ $tim->nama }}</strong>
                                </td>

                                <td>
                                    <span class="kt-badge">
                                        {{ $tim->divisi?->nama ?? '-' }}
                                    </span>
                                </td>

                                <td>
                                    {{ $tim->deskripsi ?: '-' }}
                                </td>

                                <td>
                                    <div class="kt-actions">
                                        <details class="kt-edit">
                                            <summary class="kt-btn kt-outline">
                                                Edit
                                            </summary>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.divisi.tim.update', $tim) }}"
                                            >
                                                @csrf
                                                @method('PUT')

                                                <select
                                                    name="divisi_id"
                                                    required
                                                >
                                                    @foreach($divisis as $divisi)
                                                        <option
                                                            value="{{ $divisi->id }}"
                                                            @selected($tim->divisi_id == $divisi->id)
                                                        >
                                                            {{ $divisi->nama }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <input
                                                    type="text"
                                                    name="nama"
                                                    value="{{ $tim->nama }}"
                                                    maxlength="150"
                                                    required
                                                >

                                                <textarea
                                                    name="deskripsi"
                                                    maxlength="2000"
                                                >{{ $tim->deskripsi }}</textarea>

                                                <button
                                                    type="submit"
                                                    class="kt-btn kt-primary"
                                                >
                                                    Simpan Perubahan
                                                </button>
                                            </form>
                                        </details>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.divisi.tim.hapus', $tim) }}"
                                            onsubmit="return confirm('Yakin ingin menghapus tim kerja ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="kt-btn kt-danger"
                                            >
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="kt-empty">
                                    Belum ada tim kerja.
                                    Tambahkan melalui formulir di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @endif

</div>
@endsection
