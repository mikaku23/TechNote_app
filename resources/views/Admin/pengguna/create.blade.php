@extends('template_admin.layout')
@section('title', 'Tambah Pengguna')
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
            <h4 class="card-title">Tambah Data Pengguna</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Isi data pengguna pada form di bawah. Pastikan username, nim, nik bersifat unik dan password minimal 4 karakter.</p>

        <form class="needs-validation"
            action="{{ route('pengguna.store') }}"
            method="POST"
            data-redirect="{{ route('pengguna.index') }}"
            enctype="multipart/form-data">
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
                    <label class="form-label" for="nim">NIM</label>
                    <input type="text"
                        class="form-control {{ $errors->has('nim') ? 'is-invalid' : (old('nim') ? 'is-valid' : '') }}"
                        id="nim" name="nim"
                        value="{{ old('nim') }}"
                        placeholder="Masukkan NIM">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="nip">NIP</label>
                    <input type="number"
                        class="form-control {{ $errors->has('nip') ? 'is-invalid' : (old('nip') ? 'is-valid' : '') }}"
                        id="nip" name="nip"
                        value="{{ old('nip') }}"
                        placeholder="Masukkan NIP">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="nama">Nama</label>
                    <input type="text"
                        class="form-control {{ $errors->has('nama') ? 'is-invalid' : (old('nama') ? 'is-valid' : '') }}"
                        id="nama" name="nama"
                        value="{{ old('nama') }}"
                        placeholder="Masukkan nama">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="username">Username</label>
                <input type="text"
                    class="form-control {{ $errors->has('username') ? 'is-invalid' : (old('username') ? 'is-valid' : '') }}"
                    id="username" name="username"
                    value="{{ old('username') }}"
                    placeholder="Masukkan username">
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <input type="password"
                        class="form-control {{ $errors->has('password') ? 'is-invalid' : (old('password') ? 'is-valid' : '') }}"
                        id="password" name="password"
                        placeholder="Masukkan password">
                    <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="no_hp">Nomor HP</label>
                <input type="text"
                    class="form-control {{ $errors->has('no_hp') ? 'is-invalid' : (old('no_hp') ? 'is-valid' : '') }}"
                    id="no_hp" name="no_hp"
                    value="{{ old('no_hp') }}"
                    placeholder="Masukkan nomor HP">
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="security_question">Security Question</label>
                <input type="text"
                    class="form-control"
                    id="security_question"
                    name="security_question"
                    value="{{ $randomQuestion }}"
                    readonly>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="security_answer">Security Answer</label>
                <input type="text"
                    class="form-control {{ $errors->has('security_answer') ? 'is-invalid' : (old('security_answer') ? 'is-valid' : '') }}"
                    id="security_answer" name="security_answer"
                    value="{{ old('security_answer') }}"
                    placeholder="Masukkan jawaban keamanan  (berfungsi untuk pemulihan akun)">
            </div>

            <!-- Foto upload (keep Bootstrap styling). 
                 NOTE: make sure the <form> has enctype="multipart/form-data" -->
            <div class="form-group mb-3">
                <div class="row">
                    <div class="col-12">
                        <label for="foto" class="form-label">Foto</label>
                        <input type="file"
                            class="form-control {{ $errors->has('foto') ? 'is-invalid' : (old('foto') ? 'is-valid' : '') }}"
                            id="foto" name="foto" accept="image/*" required>
                        @if ($errors->has('foto'))
                        <div class="invalid-feedback">
                            {{ $errors->first('foto') }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group mb-3">
                <label class="form-label" for="role_id">Role</label>
                <select class="form-control" id="role_id" name="role_id" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    @foreach($roles as $j)
                    <option value="{{$j['id']}}">{{$j['status']}}</option>
                    @endforeach
                </select>
            </div>

            <div class="text-start">
                <a href="{{route('pengguna.index')}}" class="btn btn-outline-primary">
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