@extends('layouts.admin')
@section('title', 'Data Mahasiswa - SIMASTA')
@section('page-title', 'Data Mahasiswa')
@section('user-role', 'Admin')
@section('user-avatar', 'A')
@section('sidebar')
    @include('layouts.admin')
@endsection

@section('content')

<div class="card">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:25px;
    ">
        <div>
            <h2>Data Mahasiswa</h2>
            <p>Kelola data mahasiswa yang terdaftar di SIMASTA.</p>
        </div>
         <a
          href="{{ route('admin.mahasiswa.create') }}">
            <button class="btn-primary">
            + Tambah Mahasiswa
            </button>  
        </a>
    </div>
  
    @if(session('success'))

        <div class="alert-success">
            {{ session('success') }}
        </div>

    @endif

    <div class="table-container">

        <table>

            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIM</th>
                    <th>Email</th>
                    <th>Program Studi</th>
                    <th>Perguruan Tinggi</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

                @forelse($mahasiswa as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->nama }}
                        </td>

                        <td>
                            {{ $item->nim }}
                        </td>

                        <td>
                            {{ $item->email }}
                        </td>

                        <td>
                            {{ $item->program_studi }}
                        </td>

                        <td>
                            {{ $item->perguruan_tinggi }}
                        </td>

                        <td>

                            <a
                                href="{{ route('admin.mahasiswa.show', $item) }}"
                                class="btn-secondary"
                            >
                                Detail
                            </a>

                            <a
                                href="{{ route('admin.mahasiswa.edit', $item) }}"
                                class="btn-primary"
                            >
                                Edit
                            </a>

                            <form
                                action="{{ route('admin.mahasiswa.destroy', $item) }}"
                                method="POST"
                                style="display:inline;"
                            >

                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus mahasiswa ini?')"
                                >
                                    Hapus
                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" style="text-align:center;">
                            Belum ada data mahasiswa.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

@endsection