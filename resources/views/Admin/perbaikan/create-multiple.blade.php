@extends('template_admin.layout')
@section('title', 'Tambah Banyak Perbaikan')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/save.css') }}">
@endsection

@section('konten')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Tambah Banyak Data Perbaikan</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('perbaikan.storeMultiple') }}" method="POST" class="needs-validation" data-redirect="{{ route('perbaikan.index') }}">
            @csrf

            <div id="perbaikan-wrapper">
                <div class="perbaikan-item border rounded p-3 mb-3 position-relative">
                    <button type="button" class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus" onclick="hapusBaris(this)" style="display:none;">
                        <i class="fa fa-times"></i>
                    </button>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>Nama Barang</label>
                            <input type="text" name="perbaikan[0][nama]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Kategori / Merek</label>
                            <input type="text" name="perbaikan[0][kategori]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Letak / Lokasi Barang</label>
                            <input type="text" name="perbaikan[0][lokasi]" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label>User</label>
                            <select name="perbaikan[0][user_id]" class="form-select" required>
                                <option value="" disabled selected>-- Pilih user --</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Estimasi Selesai (HH:MM)</label>
                            <input type="time" name="perbaikan[0][estimasi]" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label>Keterangan</label>
                        <textarea name="perbaikan[0][keterangan]" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-outline-primary" onclick="tambahBaris()">
                    <i class="fa fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="text-start">
                <a href="{{ route('perbaikan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                <button type="submit" class="btn btn-outline-success">Simpan Semua</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    let index = 1;

    function tambahBaris() {
        const wrapper = document.getElementById('perbaikan-wrapper');

        const html = `
    <div class="perbaikan-item border rounded p-3 mb-3 position-relative">
        <button type="button" class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2" onclick="hapusBaris(this)">
            <i class="fa fa-times"></i>
        </button>

        <div class="row mb-2">
            <div class="col-md-4">
                <label>Nama Barang</label>
                <input type="text" name="perbaikan[${index}][nama]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Kategori / Merek</label>
                <input type="text" name="perbaikan[${index}][kategori]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Letak / Lokasi Barang</label>
                <input type="text" name="perbaikan[${index}][lokasi]" class="form-control" required>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label>User</label>
                <select name="perbaikan[${index}][user_id]" class="form-select" required>
                    <option value="" disabled selected>-- Pilih user --</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label>Estimasi Selesai (HH:MM)</label>
                <input type="time" name="perbaikan[${index}][estimasi]" class="form-control" required>
            </div>
        </div>

        <div class="mb-2">
            <label>Keterangan</label>
            <textarea name="perbaikan[${index}][keterangan]" class="form-control" rows="2" required></textarea>
        </div>
    </div>
        `;

        wrapper.insertAdjacentHTML('beforeend', html);
        index++;
    }

    function hapusBaris(btn) {
        const item = btn.closest('.perbaikan-item');
        item.remove();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/save.js') }}"></script>
@endsection