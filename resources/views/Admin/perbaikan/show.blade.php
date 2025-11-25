<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control"
            value="{{ $perbaikan->nama ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Kategori</label>
        <input type="text" class="form-control"
            value="{{ $perbaikan->kategori ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Lokasi</label>
        <input type="text" class="form-control"
            value="{{ $perbaikan->lokasi ?? 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <input type="text" class="form-control"
            value="{{ $perbaikan->status ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tanggal Perbaikan</label>
        <input type="text" class="form-control"
            value="{{ $perbaikan->tgl_perbaikan ? \Carbon\Carbon::parse($perbaikan->tgl_perbaikan)->format('d F Y') : 'tidak ada data' }}"
            disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">User</label>
        <input type="text" class="form-control"
            value="{{ $perbaikan->user->nama ?? 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Keterangan</label>
    <textarea class="form-control" rows="4" disabled>{{ $perbaikan->keterangan ?? 'tidak ada data' }}</textarea>
</div>
