@extends('template_admin.layout')
@section('title', 'Tambah Instalasi')
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
            <h4 class="card-title">Tambah Data penginstalan</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Pastikan semua data terisi dengan benar.</p>

        <form class="needs-validation"
            action="{{ route('penginstalan.store') }}"
            method="POST"
            data-redirect="{{ route('penginstalan.index') }}">
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
                <div class="col-md-6">
                    <label for="user_id" class="form-label">User</label>
                    <select
                        id="user_id"
                        name="user_id"
                        class="form-select {{ $errors->has('user_id') ? 'is-invalid' : (old('user_id') ? 'is-valid' : '') }}"
                        required>
                        <option value="" disabled selected>-- Pilih user --</option>
                        @foreach($users as $j)
                        <option value="{{ $j->id }}" {{ old('user_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="software_id" class="form-label">Software</label>
                    <select
                        id="software_id"
                        name="software_id"
                        class="form-select {{ $errors->has('software_id') ? 'is-invalid' : (old('software_id') ? 'is-valid' : '') }}"
                        required>
                        <option value="" disabled selected>-- Pilih Software --</option>
                        @foreach($softwares as $j)
                        <option value="{{ $j->id }}" {{ old('software_id') == $j->id ? 'selected' : '' }}>
                            {{ $j->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('software_id')
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