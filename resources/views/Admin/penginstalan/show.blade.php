<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">User</label>
        <input type="text" class="form-control" value="{{ $penginstalan->user->nama ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Software</label>
        <input type="text" class="form-control" value="{{ $penginstalan->software->nama ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Status</label>
        <input type="text" class="form-control" value="{{ $penginstalan->status ?? 'tidak ada data' }}" disabled>
    </div>
</div>
<div class="mb-3">
    <label class="form-label">Estimasi Selesai</label>
    <input type="text" class="form-control"
        value="{{ $penginstalan->estimasi ? \Carbon\Carbon::parse($penginstalan->estimasi)->format('G \J\a\m i \M\e\n\i\t') : 'tidak ada data' }}"
        disabled>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal Instalasi</label>
    <input type="text" class="form-control" value="{{ $penginstalan->tgl_instalasi ? \Carbon\Carbon::parse($penginstalan->tgl_instalasi)->format('d F Y') : 'tidak ada data' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal Hapus</label>
    <input type="text" class="form-control" value="{{ $penginstalan->deleted_at ? \Carbon\Carbon::parse($penginstalan->tgl_hapus)->format('d F Y') : 'tidak ada data' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal Edit</label>
    <input type="text" class="form-control" value="{{ $penginstalan->updated_at ? \Carbon\Carbon::parse($penginstalan->updated_at)->format('d F Y') : 'tidak ada data' }}" disabled>
</div>
<div class="mb-3">
    <label class="form-label">QR Code</label>

    @if(!empty($penginstalan->qr_url))
    <div class="mt-2">
        <img
            src="{{ $penginstalan->qr_url }}"
            alt="QR Code"
            style="background:#fff; padding:8px">

        <div class="mt-2 text-muted">
            {{ $penginstalan->qr_code }}
        </div>
    </div>
    @else
    <div class="text-muted mt-2">
        QR Code belum tersedia
    </div>
    @endif
</div>