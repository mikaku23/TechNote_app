<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="tambahPenggunaModal" tabindex="-1" aria-labelledby="tambahPenggunaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal">
            <div class="modal-header border-0 d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0" id="tambahPenggunaModalLabel">
                    Anda ingin menambahkan data user sebagai?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center">
                <a href="{{ route('pengguna.createMahasiswa') }}" class="btn btn-glass btn-primary m-2 px-4 py-2">Mahasiswa</a>
                <a href="{{ route('pengguna.createDosen') }}" class="btn btn-glass btn-success m-2 px-4 py-2">Dosen</a>
                <a href="{{ route('pengguna.create') }}" class="btn btn-glass btn-secondary m-2 px-4 py-2">Lainnya</a>
            </div>

            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-outline-danger px-4" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>