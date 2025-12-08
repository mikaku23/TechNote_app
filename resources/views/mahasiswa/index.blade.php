@extends('template_mahasiswa.layout')
@section('title', 'Riwayat Penginstalan')
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
    <h4>Riwayat Penginstalan Software Anda</h4>
    <div class="card glass-table">
        <div class="table-responsive">
            <table class="data-table" aria-describedby="riwayat-penginstalan">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Instalasi</th>
                        <th>Software</th>
                        <th>Status</th>
                        <th>Estimasi Selesai</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($penginstalans as $item)
                    @php
                    $st = strtolower($item->status ?? '');
                    $badgeClass = 'badge-primary';
                    if (str_contains($st, 'selesai') || str_contains($st, 'sukses')) $badgeClass = 'badge-success';
                    elseif (str_contains($st, 'pending') || str_contains($st, 'tunggu')) $badgeClass = 'badge-warning';
                    elseif (str_contains($st, 'gagal') || str_contains($st, 'error')) $badgeClass = 'badge-danger';
                    @endphp
                    <tr>
                        <td data-label="No">{{ $loop->iteration + ($penginstalans->currentPage() - 1) * $penginstalans->perPage() }}</td>
                        <td data-label="Tanggal Instalasi">{{ $item->tgl_instalasi ? $item->tgl_instalasi->format('d-m-Y') : 'tidak ada data' }}</td>
                        <td data-label="Software">{{ $item->software->nama ?? 'tidak ada data' }}</td>
                        <td data-label="Status">
                            <span class="badge {{ $badgeClass }}">{{ $item->status }}</span>
                        </td>
                        <td>
                            {{ $item->estimasi_hitung }}

                            @if($item->estimasi_selesai && $item->estimasi_hitung !== 'penginstalan selesai')
                            @php
                            $target = \Carbon\Carbon::parse($item->estimasi_selesai);
                            $sekarang = \Carbon\Carbon::now('Asia/Jakarta');
                            @endphp

                            @if($target->isToday())
                            {{-- Jika masih hari ini → tampilkan jam menit detik --}}
                            <br>Selesai pada: {{ $target->format('H:i:s') }}
                            @else
                            {{-- Jika sudah melewati hari ini → tampilkan tanggal lengkap --}}
                            <br>Selesai pada: {{ $target->format('d-m-Y H:i:s') }}
                            @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data penginstalan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="table-footer">
            {{ $penginstalans->links() }}
        </div>
    </div>
</section>

@endsection
@section('js')
<script>
    const texts = [{
            text: "Selamat Datang, {{ auth()->user()->nama }} 👋",
            class: "text-welcome",
        },
        {
            text: "Silahkan cek status penginstalan anda di sini 💻📄",
            class: "text-status",
        },
        {
            text: "Senang membantu 😊",
            class: "text-help",
        },
    ];

    let count = 0;
    let index = 0;
    let currentText = "";
    let typingTextEl = document.getElementById("typingText");

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
            return setTimeout(type, 1800); // jeda sebelum berganti teks
        }

        setTimeout(type, 70); // kecepatan ketik
    })();
</script>
@endsection