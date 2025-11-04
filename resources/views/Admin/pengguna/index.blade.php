@extends('template_admin.layout')
@section('title', 'Data Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/user.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/hapus.css') }}">
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
                    <!-- Tombol Tambah Pengguna -->
                    <button type="button" class="btn btn-outline-success " data-bs-toggle="modal" data-bs-target="#tambahPenggunaModal">
                        <i class="fa fa-plus mr-1"></i> Tambah Pengguna
                    </button>

                    <a href="{{route('role.index')}}" class="btn btn-outline-primary">
                        <i class="fa fa-user-shield mr-1"></i> Role
                    </a>

                    @include('template_admin.popup-user')

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
                                    <td>{{ optional($user->created_at)->format('d-m-Y') ?? '-' }}</td>
                                    <td>
                                        <a href="{{ url('pengguna/'.$user->id) }}" class="btn btn-outline-info btn-sm"><i class="fa fa-eye mr-1"></i></a>
                                        <a href="{{Route('pengguna.edit', $user->id)}}" class="btn btn-outline-warning btn-sm"><i class="fa fa-edit mr-1"></i></a>

                                        <form id="formHapus{{ $user->id }}"
                                            action="{{ route('pengguna.destroy', $user->id) }}"
                                            method="POST"
                                            style="display:inline;">
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
                                    <td colspan="7" class="text-center">Belum ada data pengguna</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>


                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/hapus.js') }}"></script>
@endsection