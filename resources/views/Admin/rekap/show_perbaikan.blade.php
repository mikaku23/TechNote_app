<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control"
            value="{{ $data->nama ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <input type="text" class="form-control"
            value="{{ $data->kategori ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Lokasi</label>
        <input type="text" class="form-control"
            value="{{ $data->lokasi ?? 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <input type="text" class="form-control"
            value="{{ $data->status ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tanggal Perbaikan</label>
        <input type="text" class="form-control"
            value="{{ $data->tgl_perbaikan ? \Carbon\Carbon::parse($data->tgl_perbaikan)->format('d F Y') : 'tidak ada data' }}"
            disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">User</label>
        <input type="text" class="form-control"
            value="{{ $data->user->nama ?? 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Keterangan</label>
    <textarea class="form-control" rows="4" disabled>{{ $data->keterangan ?? 'tidak ada data' }}</textarea>
</div>