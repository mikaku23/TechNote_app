<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" value="{{ $software->nama ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Versi</label>
        <input type="text" class="form-control" value="{{ $software->versi ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <input type="text" class="form-control" value="{{ $software->kategori ?? 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Lisensi</label>
        <input type="text" class="form-control" value="{{ $software->lisensi ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Developer</label>
        <input type="text" class="form-control" value="{{ $software->developer ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tanggal Rilis</label>
        <input type="text" class="form-control" value="{{ $software->tgl_rilis ? \Carbon\Carbon::parse($software->tgl_rilis)->format('d F Y') : 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi</label>
    <textarea class="form-control" rows="5" disabled>{{ $software->deskripsi ?? 'tidak ada data' }}</textarea>
</div>