@extends('template_admin.layout')
@section('title', 'Edit Instalasi')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/edit.css') }}">
@endsection
@section('konten')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <div class="header-title">
            <h4 class="card-title">Edit Data Instalasi</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Disarankan untuk tidak mengubah data yang sudah ada termasuk Password.</p>

        <form class="needs-validation"
            action="{{ route('penginstalan.update', $penginstalan->id) }}"
            method="POST"
            data-redirect="{{ route('penginstalan.index') }}">
            @csrf
            @method('PUT')


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
                    <label class="form-label" for="user_id">Pengguna</label>
                    <select class="form-select" id="user_id" name="user_id" required>
                        <option value="">-- Pilih Pengguna --</option>
                        @foreach($users as $j)
                        <option value="{{$j['id']}}" {{ $penginstalan->user_id == $j['id'] ? 'selected' : '' }}>{{$j['nama']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="software_id">Software</label>
                    <select class="form-select" id="software_id" name="software_id" required>
                        <option value="">-- Pilih Software --</option>
                        @foreach($softwares as $j)
                        <option value="{{$j['id']}}" {{ $penginstalan->software_id == $j['id'] ? 'selected' : '' }}>{{$j['nama']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="" disabled selected>-- Pilih Status --</option>
                        <option value="berhasil" {{ (old('status', $penginstalan->status ?? '') == 'berhasil') ? 'selected' : '' }}>Berhasil</option>
                        <option value="gagal" {{ (old('status', $penginstalan->status ?? '') == 'gagal') ? 'selected' : '' }}>Gagal</option>
                    </select>
                </div>
            </div>
            <div class="text-start">
                <a href="{{route('penginstalan.index')}}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="reset" class="btn btn-outline-warning">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </button>
                <button type="submit" class="btn btn-outline-success">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/edit.js') }}"></script>
@endsection