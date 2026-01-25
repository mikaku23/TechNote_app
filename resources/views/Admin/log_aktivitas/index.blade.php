@extends('template_admin.layout')
@section('title', 'Log Aktivitas Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/paginate.css') }}">
@endsection
@section('konten')
<div class="container-fluid">
    <div class="card mt-3">
        <div class="card-header">
            <h4 class="card-title">Log Aktivitas Pengguna</h4>
        </div>

        <div class="card-body">

            <!-- FILTER -->
            <form method="GET" class="row g-2 mb-4">

                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="sistem" {{ request('type') == 'sistem' ? 'selected' : '' }}>Sistem</option>
                        <option value="nonsistem" {{ request('type') == 'nonsistem' ? 'selected' : '' }}>Non Sistem</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua</option>
                        <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Tanggal</label>
                    <select name="tanggal_filter" class="form-select form-select-sm">
                        <option value="hari_ini" {{ request('tanggal_filter') == 'hari_ini' ? 'selected' : '' }}>Hari ini</option>
                        <option value="kemarin" {{ request('tanggal_filter') == 'kemarin' ? 'selected' : '' }}>Kemarin</option>
                        <option value="custom" {{ request('tanggal_filter') == 'custom' ? 'selected' : '' }}>Custom</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Pilih</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm">
                </div>

                <div class="col-md-12 d-flex gap-2 mt-2">
                    <button class="btn btn-outline-secondary btn-sm">Filter</button>
                    <a href="{{ route('logAktif.index') }}" class="btn btn-outline-danger btn-sm">Reset</a>
                </div>
            </form>

            <!-- LIST AKTIVITAS -->
            <!-- LIST AKTIVITAS -->
            @forelse($activities as $item)
            <div class="border rounded p-3 mb-2 bg-light">
                <div class="d-flex justify-content-between">
                    <div>
                        <div>
                            {{ $item->created_at->format('H:i:s') }} -
                            {{ $item->activity }}
                        </div>

                        <small class="text-muted">
                            {{ $item->user->nama ?? '-' }} |
                            {{ $item->created_at->format('d F Y') }}
                        </small>
                    </div>

                    <div class="text-end">
                        <div class="mt-1">
                            <span class="badge bg-info">
                                {{ $item->type }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted">
                Belum ada data aktivitas
            </div>
            @endforelse

            <!-- PAGINATE -->
            @include('template_admin.paginate', ['data' => $activities])


        </div>
    </div>
</div>
@endsection