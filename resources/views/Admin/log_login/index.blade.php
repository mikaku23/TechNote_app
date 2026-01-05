@extends('template_admin.layout')
@section('title', 'Data log-Login-Pengguna')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/show.css') }}">

@endsection

@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <div class="header-title">
                        <h4 class="card-title">Data Login Pengguna</h4>
                    </div>
                </div>

                <div class="pt-3 pb-1 px-4">
                    <div class="d-flex align-items-center justify-content-between gap-2 flex-nowrap">
                        <div class="d-flex align-items-center gap-2 flex-nowrap w-100">

                            <!-- 🔹 Form Search, Role & Tanggal -->
                            <form action="{{ route('logLogin.index') }}" method="GET"
                                class="d-flex align-items-end gap-2 ms-auto flex-wrap">

                                <!-- Search -->
                                <div>
                                    <label class="form-label mb-1">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="form-control form-control-sm"
                                        placeholder="Nama / Username" style="width: 180px;">
                                </div>

                                <!-- Role -->
                                <div>
                                    <label class="form-label mb-1">Role</label>
                                    <select name="role" class="form-select form-select-sm" style="width: 150px;">
                                        <option value="">Semua</option>
                                        @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ request('role') == $r->id ? 'selected' : '' }}>
                                            {{ $r->status }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div>
                                    <label class="form-label mb-1">Status</label>
                                    <select name="status" class="form-select form-select-sm" style="width: 130px;">
                                        <option value="">Semua</option>
                                        <option value="online" {{ request('status') == 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="offline" {{ request('status') == 'offline' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                </div>

                                <!-- Tanggal -->
                                <div>
                                    <label class="form-label mb-1">Tanggal</label>
                                    <input type="date" name="tanggal" value="{{ request('tanggal') }}"
                                        class="form-control form-control-sm" style="width: 150px;">
                                </div>
                                <!-- IP -->
                                <div>
                                    <label class="form-label mb-1">IP</label>
                                    <select name="ip" class="form-select form-select-sm" style="width: 150px;">
                                        <option value="">Semua</option>
                                        @foreach($ips as $ip)
                                        <option value="{{ $ip }}" {{ request('ip') == $ip ? 'selected' : '' }}>{{ $ip }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Button -->
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-secondary btn-sm px-3">
                                        <i class="fa fa-search"></i> Cari
                                    </button>

                                    <a href="{{ route('logLogin.index') }}"
                                        class="btn btn-outline-danger btn-sm px-3">
                                        <i class="fa fa-undo"></i> Reset
                                    </a>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>




                <div class="card-body">
                    @if(session('message'))
                    <div class="alert alert-{{ session('alert') ?? 'success' }}">
                        {{ session('message') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="custom-datatable-entries">
                        <table id="datatable" class="table table-striped table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Waktu Online</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datauser as $log)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->user->nama ?? '-' }}</td>
                                    <td>{{ $log->user->username ?? '-' }}</td>
                                    <td>
                                        @if($log->status === 'online')
                                        <span class="badge bg-success">online</span>
                                        @else
                                        <span class="badge bg-secondary">offline</span>
                                        @endif

                                    </td>

                                    <td>{{ optional($log->login_at)->format('d F Y') }}</td>
                                    <td>{{ optional($log->login_at)->format('H:i') }}</td>

                                    <td>
                                        <button type="button" class="btn btn-outline-info btn-sm show-btn" data-id="{{ $log->id }}">
                                            <i class="fa fa-eye mr-1"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data login</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-start">
                                        <strong>
                                            Total User:
                                            @if(method_exists($datauser, 'total'))
                                            {{ $datauser->total() }}
                                            @else
                                            {{ $datauser->count() }}
                                            @endif
                                        </strong>
                                    </td>


                                </tr>
                            </tfoot>
                        </table>

                        @include('template_admin.paginate', ['data' => $datauser])



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade show-modal-glass" id="showModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-popup">

            <div class="modal-header">
                <h5 class="modal-title" id="showModalLabel">Detail Software</h5>
                <button type="button" class="btn-close close-modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="modalContent">
                    <!-- Konten AJAX -->
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary close-modal">Tutup</button>
            </div>

        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/admin/pengguna/show.js') }}"></script>

@endsection