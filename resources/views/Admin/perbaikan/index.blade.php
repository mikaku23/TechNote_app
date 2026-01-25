@extends('template_admin.layout')
@section('title', 'Data Instalasi perbaikan')
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

    /* Merah */
    .status-badge.rusak {
        background-color: #dc3545;
        color: white;
    }

    /* Kuning */
    .status-badge.sedang-diperbaiki {
        background-color: #ffc107;
        color: #333;
    }

    /* Hijau */
    .status-badge.selesai {
        background-color: #28a745;
        color: white;
    }

    /* Biru (jika nanti pakai 'bagus') */
    .status-badge.bagus {
        background-color: #007bff;
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
                        <h4 class="card-title">Data perbaikan</h4>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-1">

                    <div class="d-flex gap-2">
                        <a href="{{ route('perbaikan.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-1"></i>Tambah Data
                        </a>

                        <a href="{{ route('perbaikan.createMultiple') }}" class="btn btn-outline-primary">
                            <i class="fa fa-layer-group me-1"></i>Tambah Banyak
                        </a>

                        @if ($jumlahTerhapus > 0)
                        <a href="{{ route('perbaikan.arsip') }}" class="btn btn-outline-danger">
                            <i class="fa fa-trash me-1"></i>({{ $jumlahTerhapus }})
                        </a>
                        @endif
                    </div>


                    <!-- Filter di kanan -->
                    <form method="GET" action="{{ route('perbaikan.index') }}" class="d-flex gap-2 align-items-center">

                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm" placeholder="Cari nama atau lokasi..." style="width: 200px;">

                        <select name="status" class="form-select form-select-sm" style="width: 150px;">
                            <option value="" disabled selected>Status</option>
                            @foreach (['rusak','sedang diperbaiki','selesai','bagus'] as $item)
                            <option value="{{ $item }}" {{ request('status') == $item ? 'selected' : '' }}>
                                {{ ucwords($item) }}
                            </option>
                            @endforeach
                        </select>

                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="form-control form-control-sm" style="width: 150px;">

                        <button type="submit" class="btn btn-outline-secondary btn-sm px-3 py-1">
                            <i class="fa fa-search me-1"></i> Cari
                        </button>

                        <a href="{{ route('perbaikan.index') }}" class="btn btn-outline-danger btn-sm px-3 py-1">
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
                                    <th>Nama</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Kondisi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($perbaikan as $soft)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $soft->nama ?? ($soft->nama ?? '-') }}</td>
                                    <td>{{ $soft->lokasi ?? ($soft->lokasi ?? '-') }}</td>
                                    <td> <span class="status-badge {{ Str::slug($soft->status) }}">
                                            {{ $soft->status ?? '-' }}
                                        </span></td>

                                    <td>

                                        <div class="mt-2">
                                            <form action="{{ route('perbaikan.updateStatus', $soft->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rusak">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Rusak</button>
                                            </form>

                                            <form action="{{ route('perbaikan.updateStatus', $soft->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="selesai">
                                                <button type="submit" class="btn btn-sm btn-outline-success">Selesai</button>
                                            </form>
                                        </div>
                                    </td>


                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm show-btn" data-id="{{ $soft->id }}">
                                            <i class="fa fa-eye mr-1"></i>
                                        </button>
                                        <a href="{{ route('perbaikan.edit', $soft->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fa fa-edit mr-1"></i>
                                        </a>
                                        <form id="formHapus{{ $soft->id }}"
                                            action="{{ route('perbaikan.destroy', $soft->id) }}"
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
                                    <td colspan="7" class="text-center">Belum ada data perbaikan</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-start"><strong>Total Data: @if(method_exists($perbaikan, 'total'))
                                            {{ $perbaikan->total() }}
                                            @else
                                            {{ $perbaikan->count() }}
                                            @endif</strong></td>
                                    <td></td>
                                    <td class="text-center">
                                        <!-- Tombol hapus semua -->
                                        @if($perbaikan->count() > 0)
                                        <form id="formHapusSemua" action="{{ route('perbaikan.hapusSemua') }}" method="POST" style="display:inline;">
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

                        @include('template_admin.paginate', ['data' => $perbaikan])

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
                <h5 class="modal-title" id="showModalLabel">Detail perbaikan</h5>
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