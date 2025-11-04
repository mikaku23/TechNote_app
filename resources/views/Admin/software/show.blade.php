@extends('template_admin.layout')
@section('title', 'Show Pengguna')
@section('konten')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <div class="header-title">
            <h4 class="card-title">Show Data Pengguna</h4>
        </div>
    </div>
    <div class="card-body">
        <form class="needs-validation">
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="nim">NIM</label>
                    <input type="text" class="form-control" id="nim" name="nim"
                        value="{{ $user->nim ?? 'tidak ada data' }}" disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="nip">NIP</label>
                    <input type="text" class="form-control" id="nip" name="nip"
                        value="{{ $user->nip ?? 'tidak ada data' }}" disabled>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        value="{{ $user->nama ?? '' }}" disabled>
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="{{ $user->username ?? '' }}" disabled>
            </div>


            <div class="form-group mb-3">
                <label class="form-label" for="role_id">Role</label>
                <select class="form-control" id="role_id" name="role_id" disabled>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    @foreach($roles as $j)
                    <option value="{{$j['id']}}" {{ $j['id'] == $user->role_id ? 'selected' : '' }}>{{$j['status']}}</option>
                    @endforeach

                </select>
            </div>


            <div class="text-start">
                <a href="{{route('pengguna.index')}}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection