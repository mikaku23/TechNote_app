@extends('template_admin.layout')
@section('title', 'Tambah Banyak Software')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/save.css') }}">
@endsection

@section('konten')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Tambah Banyak Data Software</h4>
    </div>

    <div class="card-body">
        <form action="{{ route('software.storeMultiple') }}" method="POST" class="needs-validation" data-redirect="{{ route('software.index') }}">
            @csrf

            <div id="software-wrapper">
                <!-- BARIS PERTAMA -->
                <div class="software-item border rounded p-3 mb-3 position-relative">
                    <button type="button"
                        class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus"
                        onclick="hapusBaris(this)"
                        style="display:none;">
                        <i class="fa fa-times"></i>
                    </button>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>Nama</label>
                            <input type="text" name="software[0][nama]" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>Versi</label>
                            <input type="text" name="software[0][versi]" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label>Kategori</label>
                            <input type="text" name="software[0][kategori]" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>Lisensi</label>
                            <input type="text" name="software[0][lisensi]" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Developer</label>
                            <input type="text" name="software[0][developer]" class="form-control">
                        </div>

                        <div class="col-md-4">
                            <label>Tanggal Rilis</label>
                            <input type="date" name="software[0][tgl_rilis]" class="form-control">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label>Deskripsi</label>
                        <textarea name="software[0][deskripsi]" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-outline-primary" onclick="tambahBaris()">
                    <i class="fa fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="text-start">
                <a href="{{ route('software.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button type="submit" class="btn btn-outline-success">
                    <i class="fa fa-save"></i> Simpan Semua
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script>
    let index = 1;

    function tambahBaris() {
        const wrapper = document.getElementById('software-wrapper');

        const html = `
    <div class="software-item border rounded p-3 mb-3 position-relative">
        <button type="button"
            class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2"
            onclick="hapusBaris(this)">
            <i class="fa fa-times"></i>
        </button>

        <div class="row mb-2">
            <div class="col-md-4">
                <label>Nama</label>
                <input type="text" name="software[${index}][nama]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Versi</label>
                <input type="text" name="software[${index}][versi]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Kategori</label>
                <input type="text" name="software[${index}][kategori]" class="form-control">
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-4">
                <label>Lisensi</label>
                <input type="text" name="software[${index}][lisensi]" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Developer</label>
                <input type="text" name="software[${index}][developer]" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Tanggal Rilis</label>
                <input type="date" name="software[${index}][tgl_rilis]" class="form-control">
            </div>
        </div>

        <div>
            <label>Deskripsi</label>
            <textarea name="software[${index}][deskripsi]" class="form-control" rows="3"></textarea>
        </div>
    </div>`;

        wrapper.insertAdjacentHTML('beforeend', html);
        index++;
    }

    function hapusBaris(btn) {
        const item = btn.closest('.software-item');
        item.remove();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/save.js') }}"></script>
@endsection