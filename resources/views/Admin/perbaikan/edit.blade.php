@extends('template_admin.layout')
@section('title', 'Edit Perbaikan')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/edit.css') }}">
@endsection
@section('konten')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <div class="header-title">
            <h4 class="card-title">Edit Data Perbaikan</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Disarankan untuk tidak mengubah data yang sudah ada termasuk Password.</p>

        <form class="needs-validation"
            action="{{ route('perbaikan.update', $perbaikan->id) }}"
            method="POST"
            data-redirect="{{ route('perbaikan.index') }}">
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
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" id="nama" name="nama" class="form-control"
                        value="{{ old('nama', $perbaikan->nama) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="kategori" class="form-label">Kategori</label>
                    <input type="text" id="kategori" name="kategori" class="form-control"
                        value="{{ old('kategori', $perbaikan->kategori) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <input type="text" id="lokasi" name="lokasi" class="form-control"
                        value="{{ old('lokasi', $perbaikan->lokasi) }}" required>
                </div>

                <div class="col-md-6">
                    <label for="user_id" class="form-label">Pengguna</label>
                    <select id="user_id" name="user_id" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Pengguna --</option>
                        @foreach($users as $j)
                        <option value="{{ $j->id }}" {{ $perbaikan->user_id == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    @php
                    $statusOptions = ['sedang diperbaiki', 'rusak', 'selesai', 'bagus'];
                    @endphp
                    @foreach($statusOptions as $item)
                    <option value="{{ $item }}" {{ old('status', $perbaikan->status) == $item ? 'selected' : '' }}>
                        {{ ucfirst($item) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control" rows="4" required>{{ old('keterangan', $perbaikan->keterangan) }}</textarea>
            </div>

            <div class="text-start">
                <a href="{{route('perbaikan.index')}}" class="btn btn-outline-primary">
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