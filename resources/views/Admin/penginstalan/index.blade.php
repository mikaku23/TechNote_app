@extends('template_admin.layout')
@section('title', 'Data Instalasi Software')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/user.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/hapus.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/show.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/paginate.css') }}">

<style>
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }

    /* Hijau sukses */
    .status-badge.berhasil,
    .status-badge.dipulihkan,
    .status-badge.diupdate {
        background-color: #28a745;
        color: white;
    }

    /* Merah gagal atau permanen dihapus */
    .status-badge.gagal,
    .status-badge.dihapus-permanen {
        background-color: #dc3545;
        color: white;
    }

    /* Abu atau netral */
    .status-badge.dihapus {
        background-color: #6c757d;
        color: white;
    }
</style>

@endsection
@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Data Instalasi Software</h4>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-1">

                    <div class="d-flex gap-2">
                        <a href="{{ route('penginstalan.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-1"></i>Data Instalasi
                        </a>
                        @if ($jumlahTerhapus > 0)
                        <a href="{{ route('penginstalan.terhapus') }}" class="btn btn-outline-danger">
                            <i class="fa fa-trash me-1"></i>({{ $jumlahTerhapus }})
                        </a>
                        @endif

                    </div>

                    <!-- Filter di kanan -->
                    <form action="{{ route('penginstalan.index') }}" method="GET" class="d-flex gap-2 align-items-center">

                        <!-- Search Nama Pengguna -->
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm" placeholder="Cari nama pengguna..." style="width: 180px;">

                        <!-- Filter Status -->
                        <select name="status" class="form-select form-select-sm" style="width: 130px;">
                            <option value="" disabled selected>Status</option>
                            <option value="berhasil" {{ request('status')=='berhasil' ? 'selected' : '' }}>Berhasil</option>
                            <option value="gagal" {{ request('status')=='gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>

                        <!-- Filter Tanggal Instalasi -->
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="form-control form-control-sm" style="width: 150px;">

                        <!-- Tombol Cari dan Reset -->
                        <button type="submit" class="btn btn-outline-secondary btn-sm px-3 py-1">
                            <i class="fa fa-search me-1"></i> Cari
                        </button>

                        <a href="{{ route('penginstalan.index') }}" class="btn btn-outline-danger btn-sm px-3 py-1">
                            <i class="fa fa-undo me-1"></i> Reset
                        </a>
                    </form>


                </div>


                <div class="card-body">
                    @if(session('message'))
                    <div class="alert alert-{{ session('alert') ?? 'success' }}">
                        {{ session('message') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama Pengguna</th>
                                    <th>Software</th>
                                    <th>Status</th>
                                    <th>Tanggal Instalasi</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($penginstalan as $soft)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $soft->user->nama ?? ($soft->user_id ?? '-') }}</td>
                                    <td>{{ $soft->software->nama ?? ($soft->software_id ?? '-') }}</td>
                                    <td>
                                        <span class="status-badge {{ Str::slug($soft->status) }}">
                                            {{ $soft->status ?? '-' }}
                                        </span>
                                    </td>

                                    <td>{{ $soft->tgl_instalasi ? $soft->tgl_instalasi->format('d F Y') : '-' }}</td>


                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm show-btn" data-id="{{ $soft->id }}">
                                            <i class="fa fa-eye mr-1"></i>
                                        </button>
                                        <a href="{{ route('penginstalan.edit', $soft->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fa fa-edit mr-1"></i>
                                        </a>
                                        <form id="formHapus{{ $soft->id }}"
                                            action="{{ route('penginstalan.destroy', $soft->id) }}"
                                            method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="konfirmasiHapus('{{ $soft->id }}')">
                                                <i class="fa fa-trash mr-1"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data penginstalan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-start"><strong>Total Data: @if(method_exists($penginstalan, 'total'))
                                            {{ $penginstalan->total() }}
                                            @else
                                            {{ $penginstalan->count() }}
                                            @endif</strong></td>
                                    <td></td>
                                    <td class="text-center">
                                        <!-- Tombol hapus semua -->
                                        @if($penginstalan->count() > 0)
                                        <form id="formHapusSemua" action="{{ route('penginstalan.hapusSemua') }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="konfirmasiHapusSemua()">
                                                <i class="fa fa-trash mr-1"></i> Hapus Semua
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>

                            </tfoot>
                        </table>

                        @include('template_admin.paginate', ['data' => $penginstalan])

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade show-modal-glass" id="showModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-popup">

            <div class="modal-header">
                <h5 class="modal-title" id="showModalLabel">Detail penginstalan</h5>
                <button type="button" class="btn-close close-modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="modalContent">
                    <!-- Konten AJAX -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary close-modal">Tutup</button>
            </div>

        </div>
    </div>
</div>








@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/hapus.js') }}"></script>
<script src="{{ asset('assets/js/admin/pengguna/show.js') }}"></script>
@endsection