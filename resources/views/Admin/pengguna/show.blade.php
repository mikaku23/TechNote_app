<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">NIM</label>
        <input type="text" class="form-control" value="{{ $user->nim ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">NIP</label>
        <input type="text" class="form-control" value="{{ $user->nip ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" value="{{ $user->nama ?? '-' }}" disabled>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" class="form-control" value="{{ $user->username ?? '-' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Nomor HP</label>
    <input type="text" class="form-control" value="{{ $user->no_hp ?? '-' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Role</label>
    <input type="text" class="form-control" value="{{ $user->role->status ?? '-' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Security Question</label>
    <input type="text" class="form-control" value="{{ $user->security_question ?? '-' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Foto</label>
    @if(!empty($user->foto))
    <div>
        <img src="{{ asset('foto/' . $user->foto) }}" alt="Foto {{ $user->nama }}" class="img-thumbnail" style="max-height:200px;">
        <div class="form-text mt-1">{{ $user->foto }}</div>
    </div>
    @else
    <input type="text" class="form-control" value="Tidak ada foto" disabled>
    @endif
</div>
<div class="mb-3">
    <label class="form-label">QR Code</label>

    @if(!empty($user->qr_url))
    <div class="mt-2">
        <img
            src="{{ $user->qr_url }}"
            alt="QR Code"
            style="background:#fff; padding:8px">

        <div class="mt-2 text-muted">
            {{ $user->qr_code }}
        </div>
    </div>
    @else
    <div class="text-muted mt-2">
        QR Code belum tersedia
    </div>
    @endif
</div>