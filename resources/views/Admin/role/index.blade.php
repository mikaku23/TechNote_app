@extends('template_admin.layout')
@section('title', 'Data Role Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/role.css') }}">
@endsection
@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Data Role Pengguna</h4>
                    </div>
                </div>
                <div class="pt-3 pb-1 px-4">
                    <!-- Tombol Tambah Pengguna -->
                    <a href="{{route('pengguna.index')}}" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left mr-1"></i> Kembali
                    </a>

                    @if(!$roleSudahPenuh)
                    <a href="{{ route('role.create') }}" class="btn btn-outline-success">
                        <i class="fa fa-plus mr-1"></i> Tambah Role
                    </a>
                    @else
                    <button type="button" class="btn btn-outline-success" id="btnRolePenuh">
                        <i class="fa fa-plus mr-1"></i> Tambah Role
                    </button>
                    @endif


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
                                    
                                    <th>Id</th>
                                    <th>Role</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datarole as $role)
                                <tr class="text-center">
                                   
                                    <td>{{ $role->id ?? '-' }}</td>
                                    <td>{{ $role->status ?? '-' }}</td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data pengguna</td>
                                </tr>
                                @endforelse
                            </tbody>

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
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('btnRolePenuh');
        if (btn) {
            btn.addEventListener('click', () => {
                Swal.fire({
                    title: '<strong>Data Role Sudah Terisi Semua</strong>',
                    html: 'Semua status role (admin, dosen, mahasiswa, teknisi) sudah ada.',
                    icon: 'info',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6C63FF',
                    customClass: {
                        popup: 'glass-popup'
                    },
                    backdrop: `
                    rgba(0, 0, 0, 0.4)
                    blur(6px)
                `,
                    showClass: {
                        popup: 'swal2-show'
                    },
                    hideClass: {
                        popup: 'swal2-hide'
                    }
                });
            });
        }
    });
</script>

@endsection