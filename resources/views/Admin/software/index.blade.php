@extends('template_admin.layout')
@section('title', 'Data Software')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/user.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/hapus.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/show.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/paginate.css') }}">
@endsection
@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Data Software</h4>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-1">

                    <!-- Tombol kiri -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('software.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-1"></i> Tambah Software
                        </a>

                        <a href="{{ route('software.createMultiple') }}" class="btn btn-outline-primary">
                            <i class="fa fa-layer-group me-1"></i> Tambah Banyak
                        </a>
                    </div>


                    <!-- Filter di kanan -->
                    <form action="{{ route('software.index') }}" method="GET"
                        class="d-flex align-items-center gap-2 flex-nowrap">

                        <i class="fa fa-search"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm" placeholder="Search nama software..."
                            style="width: 180px;">

                        <select name="developer" class="form-select form-select-sm" style="width: 150px;">
                            <option value="" disabled selected>Filter Developer</option>

                            @foreach ($developers as $dev)
                            <option value="{{ $dev->developer }}"
                                {{ request('developer') == $dev->developer ? 'selected' : '' }}>
                                {{ $dev->developer }}
                            </option>
                            @endforeach
                        </select>


                        <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                            class="form-control form-control-sm"
                            style="width: 150px;">

                        <button type="submit" class="btn btn-outline-secondary btn-sm px-3 py-1">
                            <i class="fa fa-search me-1"></i> Cari
                        </button>

                        <a href="{{ route('software.index') }}" class="btn btn-outline-danger btn-sm px-3 py-1">
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
                                    <th>Versi</th>
                                    <th>Developer</th>
                                    <th>Tanggal Rilis</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($software as $soft)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $soft->nama ?? ($soft->nama ?? '-') }}</td>
                                    <td>{{ $soft->versi ?? ($soft->versi ?? '-') }}</td>
                                    <td>{{ $soft->developer ?? ($soft->developer ?? '-') }}</td>
                                    <td>{{ optional($soft->tgl_rilis)->format('d F Y') ?? '-' }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm show-btn" data-id="{{ $soft->id }}">
                                            <i class="fa fa-eye mr-1"></i>
                                        </button>
                                        <a href="{{ route('software.edit', $soft->id) }}" class="btn btn-outline-warning btn-sm">
                                            <i class="fa fa-edit mr-1"></i>
                                        </a>
                                        <form id="formHapus{{ $soft->id }}"
                                            action="{{ route('software.destroy', $soft->id) }}"
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
                                    <td colspan="7" class="text-center">Belum ada data software</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-start"><strong>Total Data: @if(method_exists($software, 'total'))
                                            {{ $software->total() }}
                                            @else
                                            {{ $software->count() }}
                                            @endif</strong></td>
                                    <td></td>
                                    <td class="text-center">
                                        <!-- Tombol hapus semua -->
                                        @if($software->count() > 0)
                                        <form id="formHapusSemua" action="{{ route('software.hapusSemua') }}" method="POST" style="display:inline;">
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

                        @include('template_admin.paginate', ['data' => $software])

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
                <h5 class="modal-title" id="showModalLabel">Detail Software</h5>
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