@extends('template_admin.layout')
@section('title', 'Tambah Banyak Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/save.css') }}">
@endsection

@section('konten')
<div class="card mt-3">
    <div class="card-header">
        <h4 class="card-title">Tambah Banyak Data Pengguna</h4>
    </div>

    <div class="card-body">
        <p>Isi beberapa entri pengguna lalu klik Simpan Semua. Foto tidak disertakan saat bulk create.</p>

        <form action="{{ route('pengguna.storeMultiple') }}" method="POST" class="needs-validation" data-redirect="{{ route('pengguna.index') }}"
            enctype="multipart/form-data">
            @csrf

            <div id="pengguna-wrapper">
                <div class="pengguna-item border rounded p-3 mb-3 position-relative">
                    <button type="button"
                        class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2 btn-hapus"
                        onclick="hapusBaris(this)"
                        style="display:none;">
                        <i class="fa fa-times"></i>
                    </button>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>NIM</label>
                            <input type="text" name="pengguna[0][nim]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>NIP</label>
                            <input type="text" name="pengguna[0][nip]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label>Nama</label>
                            <input type="text" name="pengguna[0][nama]" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>Username</label>
                            <input type="text" name="pengguna[0][username]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Password</label>
                            <input type="password" name="pengguna[0][password]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label>Nomor HP</label>
                            <input type="text" name="pengguna[0][no_hp]" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label>Security Question</label>
                            <input type="text" name="pengguna[0][security_question]" class="form-control" value="{{ $randomQuestion }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Security Answer</label>
                            <input type="text" name="pengguna[0][security_answer]" class="form-control" required>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label>Role</label>
                            <select name="pengguna[0][role_id]" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                @foreach($roles as $r)
                                <option value="{{ $r->id }}">{{ $r->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <!-- kosongkan kolom tambahan agar rata seperti modul lain -->
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
                <a href="{{ route('pengguna.index') }}" class="btn btn-outline-secondary">
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
        const wrapper = document.getElementById('pengguna-wrapper');

        const html = `
    <div class="pengguna-item border rounded p-3 mb-3 position-relative">
        <button type="button"
            class="btn btn-outline-danger btn-sm position-absolute top-0 end-0 m-2"
            onclick="hapusBaris(this)">
            <i class="fa fa-times"></i>
        </button>

        <div class="row mb-2">
            <div class="col-md-4">
                <label>NIM</label>
                <input type="text" name="pengguna[${index}][nim]" class="form-control">
            </div>
            <div class="col-md-4">
                <label>NIP</label>
                <input type="text" name="pengguna[${index}][nip]" class="form-control">
            </div>
            <div class="col-md-4">
                <label>Nama</label>
                <input type="text" name="pengguna[${index}][nama]" class="form-control" required>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-4">
                <label>Username</label>
                <input type="text" name="pengguna[${index}][username]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Password</label>
                <input type="password" name="pengguna[${index}][password]" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Nomor HP</label>
                <input type="text" name="pengguna[${index}][no_hp]" class="form-control" required>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label>Security Question</label>
                <input type="text" name="pengguna[${index}][security_question]" class="form-control" value="{{ $randomQuestion }}" readonly>
            </div>
            <div class="col-md-6">
                <label>Security Answer</label>
                <input type="text" name="pengguna[${index}][security_answer]" class="form-control" required>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <label>Role</label>
                <select name="pengguna[${index}][role_id]" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Role --</option>
                    @foreach($roles as $r)
                    <option value="{{ $r->id }}">{{ $r->status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6"></div>
        </div>
    </div>`;

        wrapper.insertAdjacentHTML('beforeend', html);
        index++;
    }

    function hapusBaris(btn) {
        const item = btn.closest('.pengguna-item');
        item.remove();
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/save.js') }}"></script>
@endsection