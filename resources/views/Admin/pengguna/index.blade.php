@extends('template_admin.layout')
@section('title', 'Data Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/user.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/hapus.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/show.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/search.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/paginate.css') }}">
@endsection

@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Data Pengguna</h4>
                    </div>
                </div>

                <div class="pt-3 pb-1 px-4">
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                        <div class="d-flex align-items-center gap-2 flex-nowrap w-100">

                            <!-- 🔹 Tombol Tambah & Role (ukuran normal) -->
                            <button type="button" class="btn btn-outline-success"
                                data-bs-toggle="modal" data-bs-target="#tambahPenggunaModal">
                                <i class="fa fa-plus me-1"></i> Tambah Pengguna
                            </button>

                            <a href="{{ route('pengguna.createMultiple') }}" class="btn btn-outline-primary">
                                <i class="fa fa-layer-group me-1"></i> Tambah Banyak
                            </a>


                            <a href="{{ route('role.index') }}" class="btn btn-outline-primary">
                                <i class="fa fa-user-shield me-1"></i> Role
                            </a>

                            @include('template_admin.popup-user')

                            <!-- 🔹 Form Search, Role & Tanggal -->
                            <form action="{{ route('pengguna.index') }}" method="GET"
                                class="d-flex align-items-center gap-2 ms-auto flex-nowrap">

                                <!-- Kolom Search -->
                                <i class="fa fa-search"></i>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    class="form-control form-control-sm" placeholder="nama/username/QR"
                                    style="width: 180px;">

                                <!-- Kolom Role + Tanggal -->
                                <div class="d-flex flex-column align-items-start">
                                    <select name="role" class="form-select form-select-sm" style="width: 150px;">
                                        <option value="" disabled selected>Filter Role</option>
                                        <option value="">Semua Role</option>
                                        @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>
                                            {{ $r->status }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <!-- 🔽 Filter tanggal di bawah role -->
                                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                                        class="form-control form-control-sm mt-1" style="width: 150px;">
                                </div>

                                <!-- Tombol Cari & Reset -->
                                <button type="submit" class="btn btn-outline-secondary btn-sm px-3 py-1">
                                    <i class="fa fa-search me-1"></i> Cari
                                </button>

                                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-danger btn-sm px-3 py-1">
                                    <i class="fa fa-undo me-1"></i> Reset
                                </a>
                            </form>
                        </div>
                    </div>
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
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Tanggal Gabung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datauser as $user)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->nama ?? ($user->name ?? '-') }}</td>
                                    <td>{{ $user->username ?? ($user->username ?? '-') }}</td>
                                    <td>{{ $user->role->role ?? ($user->role->status ?? '-') ?? '-' }}</td>
                                    <td>{{ optional($user->created_at)->format('d F Y') ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm show-btn" data-id="{{ $user->id }}">
                                            <i class="fa fa-eye mr-1"></i>
                                        </button>
                                        <a href="{{ route('pengguna.edit', $user->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fa fa-edit mr-1"></i>
                                        </a>
                                        <form id="formHapus{{ $user->id }}" action="{{ route('pengguna.destroy', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="konfirmasiHapus('{{ $user->id }}')">
                                                <i class="fa fa-trash mr-1"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data pengguna</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-start">
                                        <strong>
                                            Total User:
                                            @if(method_exists($datauser, 'total'))
                                            {{ $datauser->total() }}
                                            @else
                                            {{ $datauser->count() }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td></td>
                                    <td class="text-center">
                                        @if($datauser->count() > 0)
                                        <form id="formHapusSemua" action="{{ route('pengguna.hapusSemua') }}" method="POST" style="display:inline;">
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

                        @include('template_admin.paginate', ['data' => $datauser])



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
                <h5 class="modal-title" id="showModalLabel">Detail Pengguna</h5>
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