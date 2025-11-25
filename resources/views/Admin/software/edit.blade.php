@extends('template_admin.layout')
@section('title', 'Edit Software')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/edit.css') }}">
@endsection
@section('konten')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <div class="header-title">
            <h4 class="card-title">Edit Data software</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Disarankan untuk tidak mengubah data yang sudah ada termasuk Password.</p>

        <form class="needs-validation"
            action="{{ route('software.update', $software->id) }}"
            method="POST"
            data-redirect="{{ route('software.index') }}">
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
                    <label class="form-label" for="nama">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama"
                        value="{{ $software->nama ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="versi">Versi</label>
                    <input type="text" class="form-control" id="versi" name="versi"
                        value="{{ $software->versi ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="kategori">Kategori</label>
                    <input type="text" class="form-control" id="kategori" name="kategori"
                        value="{{ $software->kategori ?? '' }}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label" for="lisensi">Lisensi</label>
                    <input type="text" class="form-control" id="lisensi" name="lisensi"
                        value="{{ $software->lisensi ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="developer">Developer</label>
                    <input type="text" class="form-control" id="developer" name="developer"
                        value="{{ $software->developer ?? '' }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="tgl_rilis">Tanggal Rilis</label>
                    <input type="date" class="form-control" id="tgl_rilis" name="tgl_rilis"
                        value="{{ $software->tgl_rilis ? \Carbon\Carbon::parse($software->tgl_rilis)->format('Y-m-d') : '' }}">

                </div>
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="deskripsi">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"
                    placeholder="Masukkan deskripsi lengkap">{{ $software->deskripsi ?? '' }}</textarea>
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
<script src="{{ asset('assets/js/admin/pengguna/edit.js') }}"></script>
@endsection