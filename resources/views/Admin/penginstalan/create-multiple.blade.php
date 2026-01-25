@extends('template_admin.layout')
@section('title', 'Tambah Banyak Instalasi')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/save.css') }}">
@endsection

@section('konten')
<div class="card mt-3">
    <div class="card-header d-flex justify-content-between">
        <div class="header-title">
            <h4 class="card-title">Tambah Banyak Data Penginstalan</h4>
        </div>
    </div>
    <div class="card-body">
        <p>Pastikan semua data terisi dengan benar. Gunakan tombol "Tambah Baris" untuk menambah entri.</p>

        <form class="needs-validation" action="{{ route('penginstalan.storeMultiple') }}" method="POST" data-redirect="{{ route('penginstalan.index') }}">
            @csrf

            <div id="penginstalan-wrapper">
                <div class="penginstalan-item border rounded p-3 mb-3 position-relative">
                    <button type="button"
                        class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus"
                        onclick="hapusBaris(this)"
                        style="display:none;">
                        <i class="fa fa-times"></i>
                    </button>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="user_id_0" class="form-label">User</label>
                            <select id="user_id_0" name="penginstalan[0][user_id]" class="form-select" required>
                                <option value="" disabled selected>-- Pilih user --</option>
                                @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="software_id_0" class="form-label">Software</label>
                            <select id="software_id_0" name="penginstalan[0][software_id]" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Software --</option>
                                @foreach($softwares as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="estimasi_0" class="form-label">Estimasi Selesai (HH:MM)</label>
                            <input type="time" id="estimasi_0" name="penginstalan[0][estimasi]" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <button type="button" class="btn btn-outline-primary" onclick="tambahBaris()">
                    <i class="fa fa-plus"></i> Tambah Baris
                </button>
            </div>

            <div class="text-start">
                <a href="{{ route('penginstalan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                <button type="submit" class="btn btn-outline-success">Simpan Semua</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    let index = 1;

    // pre-generate option HTML dari server supaya mudah cloning
    const userOptions = `@foreach($users as $u)<option value="{{ $u->id }}">{{ addslashes($u->nama) }}</option>@endforeach`;
    const softwareOptions = `@foreach($softwares as $s)<option value="{{ $s->id }}">{{ addslashes($s->nama) }}</option>@endforeach`;

    function tambahBaris() {
        const wrapper = document.getElementById('penginstalan-wrapper');

        const html = `
    <div class="penginstalan-item border rounded p-3 mb-3 position-relative">
        <button type="button"
            class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2"
            onclick="hapusBaris(this)">
            <i class="fa fa-times"></i>
        </button>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="user_id_${index}" class="form-label">User</label>
                <select id="user_id_${index}" name="penginstalan[${index}][user_id]" class="form-select" required>
                    <option value="" disabled selected>-- Pilih user --</option>
                    ${userOptions}
                </select>
            </div>

            <div class="col-md-6">
                <label for="software_id_${index}" class="form-label">Software</label>
                <select id="software_id_${index}" name="penginstalan[${index}][software_id]" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Software --</option>
                    ${softwareOptions}
                </select>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label for="estimasi_${index}" class="form-label">Estimasi Selesai (HH:MM)</label>
                <input type="time" id="estimasi_${index}" name="penginstalan[${index}][estimasi]" class="form-control">
            </div>
        </div>
    </div>
    `;

        wrapper.insertAdjacentHTML('beforeend', html);
        index++;
    }

    function hapusBaris(btn) {
        const item = btn.closest('.penginstalan-item');
        item.remove();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/save.js') }}"></script>
@endsection