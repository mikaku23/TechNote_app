<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        table th,
        table td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }

        h2 {
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <h1>Rekapitulasi Data Perbaikan & Penginstalan</h1>

    <h2>Bagian Perbaikan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>User</th>
                <th>NIP User</th>
                <th>Estimasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($perbaikan as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->perbaikan->tgl_perbaikan ? \Carbon\Carbon::parse($r->perbaikan->tgl_perbaikan)->format('d F Y') : '-' }}</td>
                <td>{{ $r->perbaikan->kategori ?? '-' }}</td>
                <td>{{ $r->perbaikan->nama ?? '-' }}</td>
                <td>{{ $r->perbaikan->user->nama ?? '-' }}</td>
                <td>{{ $r->perbaikan->user->nip ?? '-' }}</td>
                <td>
                    @php
                    $estimasi = $r->perbaikan->estimasi ?? null;

                    if ($estimasi) {
                    $parts = explode(':', $estimasi);
                    $h = (int) $parts[0];
                    $m = (int) $parts[1];
                    $s = (int) $parts[2];

                    $out = [];

                    if ($h > 0) {
                    $out[] = $h . ' jam';
                    }

                    if ($m > 0) {
                    $out[] = $m . ' menit';
                    }

                    if ($s > 0) {
                    $out[] = $s . ' detik';
                    }

                    echo implode(' ', $out);
                    } else {
                    echo '-';
                    }
                    @endphp
                </td>

                <td>{{ $r->perbaikan->status ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Bagian Penginstalan</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Software</th>
                <th>Versi</th>
                <th>Kategori</th>
                <th>User</th>
                <th>NIM User</th>
                <th>Estimasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penginstalan as $r)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $r->penginstalan->tgl_instalasi ? \Carbon\Carbon::parse($r->penginstalan->tgl_instalasi)->format('d F Y') : '-' }}</td>
                <td>{{ $r->penginstalan->software->nama ?? '-' }}</td>
                <td>{{ $r->penginstalan->software->versi ?? '-' }}</td>
                <td>{{ $r->penginstalan->software->kategori ?? '-' }}</td>
                <td>{{ $r->penginstalan->user->nama ?? '-' }}</td>
                <td>{{ $r->penginstalan->user->nim ?? '-' }}</td>
                <td>
                    @php
                    $estimasi = $r->penginstalan->estimasi ?? null;

                    if ($estimasi) {
                    $parts = explode(':', $estimasi);
                    $h = (int) $parts[0];
                    $m = (int) $parts[1];
                    $s = (int) $parts[2];

                    $out = [];

                    if ($h > 0) {
                    $out[] = $h . ' jam';
                    }

                    if ($m > 0) {
                    $out[] = $m . ' menit';
                    }

                    if ($s > 0) {
                    $out[] = $s . ' detik';
                    }

                    echo implode(' ', $out);
                    } else {
                    echo '-';
                    }
                    @endphp
                </td>

                <td>{{ $r->penginstalan->status ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>