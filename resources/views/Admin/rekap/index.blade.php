@extends('template_admin.layout')
@section('title', 'Data Rekap')
@section('css')
<style>
    .status-badge {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-badge.dihapus {
        background-color: #dc3545;
        color: white;
    }

    .status-badge.tersedia {
        background-color: #28a745;
        color: white;
    }


    .status-badge.gabungan {
        background-color: orange;
        color: white;
    }

    .bg-danger-light {
        background-color: #f8d7da !important;
        /* merah muda soft */
    }

    .bg-success-light {
        background-color: #d4edda !important;
        /* hijau muda */
        color: #000;
    }
</style>
@endsection
@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card mt-3">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Data Rekap</h4>
                </div>

                <div class="d-flex justify-content-between align-items-center px-4 pt-3 pb-1">
                    <form method="GET" action="{{ route('rekap.index') }}" class="d-flex gap-2 align-items-center">

                        <select name="jenis" class="form-select form-select-sm" style="width: 150px;">
                            <option value="" {{ request('jenis') == '' ? 'selected' : '' }}>Semua Data</option>
                            <option value="perbaikan" {{ request('jenis') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="penginstalan" {{ request('jenis') == 'penginstalan' ? 'selected' : '' }}>Penginstalan</option>
                        </select>

                        <select name="status" class="form-select form-select-sm" style="width: 150px;">
                            <option value="" {{ request('status') == '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="dihapus" {{ request('status') == 'dihapus' ? 'selected' : '' }}>Dihapus</option>
                        </select>

                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" class="form-control form-control-sm" style="width: 150px;">

                        <select name="waktu" class="form-select form-select-sm" style="width: 150px;">
                            <option value="" {{ request('waktu') == '' ? 'selected' : '' }}>Semua Waktu</option>
                            <option value="minggu" {{ request('waktu') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="bulan" {{ request('waktu') == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="tahun" {{ request('waktu') == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                            @foreach($tahunLain as $tahun)
                            <option value="{{ $tahun }}" {{ request('waktu') == $tahun ? 'selected' : '' }}>
                                Tahun {{ $tahun }}
                            </option>
                            @endforeach
                        </select>

                        <button type="submit" class="btn btn-outline-secondary btn-sm px-3 py-1">Filter</button>
                        <a href="{{ route('rekap.index') }}" class="btn btn-outline-danger btn-sm px-3 py-1">Reset</a>
                    </form>
                </div>

                <div class="card-body">
                    <table class="table table-bordered table-striped text-center">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Data Perbaikan</th>
                                <th>Data Penginstalan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rekap as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item['tanggal'] ? \Carbon\Carbon::parse($item['tanggal'])->format('d F Y') : '-' }}</td>
                                <td class="{{ $item['status_perbaikan'] === 'dihapus' ? 'bg-danger-light' : 'bg-success-light' }}">
                                    {{ $item['nama_perbaikan'] }}
                                </td>
                                <td class="{{ $item['status_instalasi'] === 'dihapus' ? 'bg-danger-light' : 'bg-success-light' }}">
                                    {{ $item['nama_instalasi'] }}
                                </td>
                                <td>
                                    <span class="status-badge {{ $item['status'] }}">
                                        {{ $item['status'] }}
                                    </span>
                                </td>


                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="text-start"><strong>Total Data: {{ $totalData }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    @include('template_admin.paginate', ['data' => $rekap])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection