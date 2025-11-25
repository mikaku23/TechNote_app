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
    <label class="form-label">Role</label>
    <input type="text" class="form-control" value="{{ $user->role->status ?? '-' }}" disabled>
</div>