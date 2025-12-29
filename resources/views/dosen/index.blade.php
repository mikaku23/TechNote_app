@extends('template_dosen.layout')
@section('title', 'Riwayat Perbaikan')
@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/typing.css') }}">

@endsection
@section('konten')
<section class="card-hero">
    <div class="card-hero-container">
        <div class="typing-container">
            <span id="typingText"></span><span class="cursor">|</span>
        </div>
    </div>
    <h4>Riwayat Perbaikan Anda</h4>
    <div class="card glass-table">
        <div class="table-responsive">
            <table class="data-table" aria-describedby="riwayat-perbaikan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                      
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Estimasi Selesai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($perbaikans as $item)
                    @php
                    $st = strtolower($item->status ?? '');
                    $badgeClass = 'badge-primary';
                    if (str_contains($st, 'selesai') || str_contains($st, 'bagus')) $badgeClass = 'badge-success';
                    elseif (str_contains($st, 'sedang') || str_contains($st, 'proses')) $badgeClass = 'badge-warning';
                    elseif (str_contains($st, 'rusak')) $badgeClass = 'badge-danger';
                    @endphp
                    <tr>
                        <td data-label="No">{{ $loop->iteration + ($perbaikans->currentPage() - 1) * $perbaikans->perPage() }}</td>
                        <td data-label="Tanggal">{{ $item->tgl_perbaikan ? $item->tgl_perbaikan->format('d M Y') : 'tidak ada data' }}</td>
                        <td data-label="Nama">{{ $item->nama ?? 'tidak ada data' }}</td>
                        <td data-label="Kategori">{{ $item->kategori ?? 'tidak ada data' }}</td>
                       
                        <td data-label="Status">
                            <span class="badge {{ $badgeClass }}">{{ $item->status ?? '-' }}</span>
                        </td>
                        <td data-label="Keterangan">{{ \Illuminate\Support\Str::limit($item->keterangan ?? '-', 60) }}</td>
                        <td data-label="Estimasi Selesai">
                            {{ $item->estimasi_hitung ?? 'tidak ada estimasi' }}
                            @if(!empty($item->estimasi_selesai) && ($item->estimasi_hitung ?? '') !== 'perbaikan selesai')
                            @php
                            $target = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $item->estimasi_selesai, 'Asia/Jakarta');
                            $sekarang = \Carbon\Carbon::now('Asia/Jakarta');
                            @endphp

                            @if($target->isToday())
                            <br>Selesai pada: {{ $target->format('H:i:s') }}
                            @else
                            <br>Selesai pada: {{ $target->format('d-m-Y H:i:s') }}
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data perbaikan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="table-footer">
            {{ $perbaikans->links() }}
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    const texts = [{
            text: 'Selamat Datang, {{ auth()->user()->nama }} 👋',
            class: 'text-welcome'
        },
        {
            text: 'Silahkan cek status perbaikan anda di sini 🛠️',
            class: 'text-status'
        },
        {
            text: 'Tim teknisi akan mengupdate status sesuai perkembangan',
            class: 'text-help'
        }
    ];

    let count = 0;
    let index = 0;
    let currentText = '';
    let typingTextEl = document.getElementById('typingText');

    (function type() {
        if (count === texts.length) count = 0;
        currentText = texts[count];

        // ambil substring sesuai index
        let letter = currentText.text.slice(0, ++index);

        // update innerHTML + cursor span
        typingTextEl.innerHTML = `<span class="${currentText.class}">${letter}</span><span class="cursor"></span>`;

        if (index === currentText.text.length) {
            count++;
            index = 0;
            return setTimeout(type, 1600); // jeda sebelum berganti teks
        }

        setTimeout(type, 60); // kecepatan ketik
    })();
</script>
@endsection