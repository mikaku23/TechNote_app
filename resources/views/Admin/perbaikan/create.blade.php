@extends('template_admin.layout')
@section('title', 'Tambah Perbaikan')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/save.css') }}">
<style>
    .form-control.is-valid {
        border-color: #198754 !important;
        padding-right: 2.5rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23198754' viewBox='0 0 16 16'%3E%3Cpath d='M16 2L6 12l-4-4 1.5-1.5L6 9l8.5-8.5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem 1rem;
    }

    .form-control.is-invalid {
        border-color: #dc3545 !important;
        padding-right: 2.5rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23dc3545' viewBox='0 0 16 16'%3E%3Cpath d='M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem 1rem;
    }
</style>
@endsection

@section('konten')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <div class="header-title">
            <h4 class="card-title">Tambah Data Perbaikan</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Pastikan semua data terisi dengan benar.</p>

        <form class="needs-validation"
            action="{{ route('perbaikan.store') }}"
            method="POST"
            data-redirect="{{ route('perbaikan.index') }}">
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

            @if (session('success'))
            <div data-success="{{ session('success') }}"></div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nama" class="form-label">Nama Barang</label>
                    <input
                        id="nama"
                        name="nama"
                        type="text"
                        placeholder="Masukkan nama barang"
                        class="form-control {{ $errors->has('nama') ? 'is-invalid' : (old('nama') ? 'is-valid' : '') }}"
                        value="{{ old('nama') }}"
                        required>
                    @error('nama')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="kategori" class="form-label">Kategori / Merek</label>
                    <input
                        id="kategori"
                        name="kategori"
                        type="text"
                        placeholder="Masukkan kategori / merek"
                        class="form-control {{ $errors->has('kategori') ? 'is-invalid' : (old('kategori') ? 'is-valid' : '') }}"
                        value="{{ old('kategori') }}"
                        required>
                    @error('kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="lokasi" class="form-label">Letak / Lokasi Barang</label>
                    <input
                        id="lokasi"
                        name="lokasi"
                        type="text"
                        placeholder="Masukkan letak / lokasi barang"
                        class="form-control {{ $errors->has('lokasi') ? 'is-invalid' : (old('lokasi') ? 'is-valid' : '') }}"
                        value="{{ old('lokasi') }}"
                        required>
                    @error('lokasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="user_id" class="form-label">User</label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="form-select {{ $errors->has('user_id') ? 'is-invalid' : (old('user_id') ? 'is-valid' : '') }}"
                        required>
                        <option value="" disabled {{ old('user_id') ? '' : 'selected' }}>-- Pilih user --</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea
                        id="keterangan"
                        name="keterangan"
                        rows="3"
                        placeholder="Masukkan keterangan"
                        class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : (old('keterangan') ? 'is-valid' : '') }}">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label for="estimasi" class="form-label">Estimasi Selesai</label>
                    <input
                        type="time"
                        id="estimasi"
                        name="estimasi"
                        class="form-control {{ $errors->has('estimasi') ? 'is-invalid' : (old('estimasi') ? 'is-valid' : '') }}"
                        value="{{ old('estimasi') }}" required>
                    @error('estimasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.needs-validation');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/save.js') }}"></script>
@endsection