<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" value="{{ $log->user->nama ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Username</label>
        <input type="text" class="form-control" value="{{ $log->user->username ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Role</label>
        <input type="text" class="form-control" value="{{ $log->user->role->status ?? 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-4">
        <label class="form-label">Status</label>
        <input type="text" class="form-control" value="{{ $log->status ?? 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tanggal Login</label>
        <input type="text" class="form-control" value="{{ $log->login_at ? \Carbon\Carbon::parse($log->login_at)->format('d F Y H:i:s') : 'tidak ada data' }}" disabled>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tanggal Logout</label>
        <input type="text" class="form-control" value="{{ $log->logout_at ? \Carbon\Carbon::parse($log->logout_at)->format('d F Y H:i:s') : 'tidak ada data' }}" disabled>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">IP Address</label>
    <input type="text" class="form-control" value="{{ $log->ip_address ?? 'tidak ada data' }}" disabled>
</div>

<div class="mb-3">
    <label class="form-label">Durasi Login</label>
    <input type="text" class="form-control" value="{{ $log->durasi_login ?? 'tidak ada data' }}" disabled>
</div>

<div class="mb-3"> <label class="form-label">Activities</label>
    <ul class="list-group"> @forelse($log->activities as $act) <li class="list-group-item"> {{ $act->created_at->format('H:i:s') }} - {{ $act->activity }} </li> @empty <li class="list-group-item text-muted"> tidak ada data </li> @endforelse </ul>
</div>