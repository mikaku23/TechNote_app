@extends('template_admin.layout')
@section('title', 'Tambah Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/save.css') }}">
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
            data-redirect="{{ route('pengguna.index') }}">
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
                        placeholder="Masukkan NIM">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="nip">NIP</label>
                    <input type="number" class="form-control" id="nip" name="nip"
                        placeholder="Masukkan NIP">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        placeholder="Masukkan nama" required maxlength="100">
                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                    placeholder="Masukkan username" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required
                        placeholder="Masukkan password">
                    <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
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
<script src="{{ asset('assets/js/save.js') }}"></script>
@endsection