@extends('template_admin.layout')
@section('title', 'Tambah Software')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/save.css') }}">
<style>
    /* Gaya validasi */
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
            <h4 class="card-title">Tambah Data software</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Pastikan semua data terisi dengan benar, dan isilah deskripsi untuk menambahkan keterangan lebih lanjut.</p>

        <form class="needs-validation"
            action="{{ route('software.store') }}"
            method="POST"
            data-redirect="{{ route('software.index') }}">
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger" style="display:none;">
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
                <div class="col-md-4">
                    <label class="form-label" for="nama">Nama</label>
                    <input type="text"
                        class="form-control {{ $errors->has('nama') ? 'is-invalid' : (old('nama') ? 'is-valid' : '') }}"
                        id="nama" name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Masukkan nama" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="versi">Versi</label>
                    <input type="text"
                        class="form-control {{ $errors->has('versi') ? 'is-invalid' : (old('versi') ? 'is-valid' : '') }}"
                        id="versi" name="versi"
                        value="{{ old('versi') }}"
                        placeholder="Masukkan versi" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="kategori">Kategori</label>
                    <input type="text"
                        class="form-control {{ $errors->has('kategori') ? 'is-invalid' : (old('kategori') ? 'is-valid' : '') }}"
                        id="kategori" name="kategori"
                        value="{{ old('kategori') }}"
                        placeholder="Masukkan / Kosongkan jika tidak ada data">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="lisensi">Lisensi</label>
                    <input type="text"
                        class="form-control {{ $errors->has('lisensi') ? 'is-invalid' : (old('lisensi') ? 'is-valid' : '') }}"
                        id="lisensi" name="lisensi"
                        value="{{ old('lisensi') }}"
                        placeholder="Masukkan / Kosongkan jika tidak ada data">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="developer">Developer</label>
                    <input type="text"
                        class="form-control {{ $errors->has('developer') ? 'is-invalid' : (old('developer') ? 'is-valid' : '') }}"
                        id="developer" name="developer"
                        value="{{ old('developer') }}"
                        placeholder="Masukkan / Kosongkan jika tidak ada data">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="tgl_rilis">Tanggal Rilis</label>
                    <input type="date"
                        class="form-control {{ $errors->has('tgl_rilis') ? 'is-invalid' : (old('tgl_rilis') ? 'is-valid' : '') }}"
                        id="tgl_rilis" name="tgl_rilis"
                        value="{{ old('tgl_rilis') }}">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea
                    class="form-control {{ $errors->has('deskripsi') ? 'is-invalid' : (old('deskripsi') ? 'is-valid' : '') }}"
                    id="deskripsi" name="deskripsi"
                    placeholder="Masukkan / Kosongkan jika tidak ada data" rows="4">{{ old('deskripsi') }}</textarea>
            </div>


            <div class="text-start">
                <a href="{{route('software.index')}}" class="btn btn-outline-primary">
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
        const pwd = document.getElementById('password');
        const toggle = document.getElementById('togglePassword');
        if (toggle && pwd) {
            toggle.addEventListener('click', function() {
                const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
                pwd.setAttribute('type', type);
                const icon = this.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                }
            });
        }

        // simple client-side bootstrap-like validation feedback (optional)
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