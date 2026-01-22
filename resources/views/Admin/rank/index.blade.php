@extends('template_admin.layout')
@section('title', 'Ranking Top 10')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/rank.css') }}">
@endsection

@section('konten')
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mt-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Ranking Top 10</h4>

                    <form method="GET" action="{{ route('rank.index') }}" class="d-flex gap-2 align-items-center">
                        <label class="mb-0">Tipe:</label>
                        <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="mahasiswa" {{ $type === 'mahasiswa' ? 'selected' : '' }}>
                                Mahasiswa (penginstalan)
                            </option>
                            <option value="dosen" {{ $type === 'dosen' ? 'selected' : '' }}>
                                Dosen (perbaikan)
                            </option>
                        </select>

                        <label class="mb-0">Semester:</label>
                        <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="ini" {{ $semester === 'ini' ? 'selected' : '' }}>
                                Semester ini
                            </option>
                            @if(now()->month > 6)
                            <option value="kemarin" {{ $semester === 'kemarin' ? 'selected' : '' }}>
                                Semester kemarin
                            </option>
                            @endif
                        </select>
                    </form>

                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="card chart-card p-3">
                                <canvas
                                    id="rankChart"
                                    data-labels='@json($labels)'
                                    data-values='@json($data)'
                                    data-type="{{ $type }}"
                                    height="220">
                                </canvas>

                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="card p-2 h-100">
                                <div class="card-body">
                                    <h5 class="mb-3">Top {{ count($top) }} - {{ $type === 'dosen' ? 'Dosen' : 'Mahasiswa' }}</h5>

                                    <div class="list-group">
                                        @forelse($top as $index => $u)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-3 align-items-center">
                                                @php
                                                $rank = $index + 1;
                                                @endphp

                                                <div class="rank-badge
    @if($rank === 1) rank-gold
    @elseif($rank === 2) rank-silver
    @elseif($rank === 3) rank-bronze
    @else bg-light text-dark
    @endif
">
                                                    {{ $rank }}
                                                </div>

                                                <div>
                                                    <div class="fw-semibold truncate">{{ $u->nama }}</div>
                                                    <div class="text-muted" style="font-size: .85rem;">
                                                        {{ $u->username ?? '-' }} •
                                                        <span class="role-badge badge bg-secondary">{{ $u->role->status ?? '-' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="text-end">
                                                <div style="font-weight:700;">
                                                    {{ $type === 'dosen' ? ($u->perbaikans_count ?? 0) : ($u->penginstalans_count ?? 0) }}
                                                </div>
                                                <div class="text-muted" style="font-size: .8rem;">
                                                    {{ $type === 'dosen' ? 'Perbaikan' : 'Penginstalan' }}
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="list-group-item text-center">Belum ada data</div>
                                        @endforelse
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- tabel detail (opsional) -->
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead class="text-center">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($top as $i => $u)
                                    <tr class="text-center">
                                        <td>{{ $i + 1 }}</td>
                                        <td class="text-start">{{ $u->nama }}</td>
                                        <td>{{ $u->username ?? '-' }}</td>
                                        <td>{{ $u->role->status ?? '-' }}</td>
                                        <td>
                                            {{ $type === 'dosen' ? ($u->perbaikans_count ?? 0) : ($u->penginstalans_count ?? 0) }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> <!-- card-body -->
            </div> <!-- card -->
        </div> <!-- col -->
    </div> <!-- row -->
</div> <!-- container -->
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('assets/js/rank.js') }}"></script>
@endsection