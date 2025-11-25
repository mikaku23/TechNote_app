@extends('template_admin.layout')
@section('title', 'Data Terhapus Perbaikan')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/admin/pengguna/user.css') }}">
<style>
    tr.deleted-row td {
        background-color: rgba(255, 0, 0, 0.15) !important;
    }

    /* Card utama soft */
    .card {
        background-color: rgba(255, 245, 246, 0.8);
        /* merah pastel sangat lembut */
        border: 1px solid rgba(220, 120, 120, 0.15);
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(220, 120, 120, 0.08);
        color: black;
        /* bayangan lembut */
    }

    /* Card header soft, tidak mencolok */
    .card-header {
        background-color: rgba(255, 228, 230, 0.7);
        /* pink/rose pastel */
        color: #464646ff;
        /* abu elegan, tidak terlalu kontras */
        border-bottom: 1px solid rgba(220, 120, 120, 0.15);
        border-radius: 10px 10px 0 0;
        font-weight: 500;
    }

    /* Baris data terhapus tetap lembut */
    tr.deleted-row td {
        background-color: rgba(255, 225, 227, 0.35) !important;
        color: #464646ff !important;

        /* red/pink fade */
    }

    /* Tombol pulihkan tetap jelas tapi tidak dominan */
    .btn-outline-success {
        border-color: #4caf50;
        color: #4caf50;
    }

    .btn-outline-success:hover {
        background-color: #4caf50;
        color: white;
    }
</style>
@endsection

@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Data Terhapus Perbaikan</h4>
                    <a href="{{ route('perbaikan.index') }}" class="btn btn-outline-primary">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Data Aktif
                    </a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Tanggal Terhapus</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perbaikan as $soft)
                            <tr class="text-center deleted-row">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $soft->nama ?? '-' }}</td>
                                <td>{{ $soft->lokasi ?? '-' }}</td>
                                <td>{{ $soft->status ?? '-' }}</td>
                                <td>{{ $soft->deleted_at ? $soft->deleted_at->format('d F Y') : '-' }}</td>
                                <td>
                                    <form action="{{ route('perbaikan.pulihkan', $soft->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-outline-success btn-sm">
                                            Pulihkan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data terhapus.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    @include('template_admin.paginate', ['data' => $perbaikan])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection