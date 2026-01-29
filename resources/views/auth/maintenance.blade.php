@extends('template_admin.layout')

@section('title','Maintenance Sistem')

@section('css')
<!-- kalau perlu tambahkan CSS khusus -->
@endsection

@section('konten')
<div class="container mt-4">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between">
            <div class="header-title">
                <h4 class="card-title">⚙ Pengaturan Maintenance Sistem</h4>
            </div>
        </div>
        <div class="card-body">

            @if($active)
            <div class="alert alert-warning">
                <h5>Maintenance Sedang Aktif</h5>
                <p>Berakhir pada: <strong>{{ $active->ends_at }}</strong></p>
                <p>Alasan: {{ $active->reason ?? '-' }}</p>
                <h4>
                    Sisa waktu:
                    <span id="countdown"
                        data-seconds="{{ $active ? $active->remainingSeconds() : 0 }}">
                    </span>
                </h4>


                <form method="POST" action="{{ route('admin.maintenance.stop') }}">
                    @csrf
                    <button class="btn btn-danger mt-2">Hentikan Maintenance</button>
                </form>
            </div>
            @else
            <form method="POST" action="{{ route('admin.maintenance.start') }}">
                @csrf
                <div class="mb-3">
                    <label>Durasi (menit)</label>
                    <input type="number" name="duration_minutes" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Alasan</label>
                    <textarea name="reason" class="form-control"></textarea>
                </div>
                <button class="btn btn-primary">Mulai Maintenance</button>
            </form>
            @endif

        </div>
    </div>
</div>
@endsection
@section('js')
@if($active)
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const el = document.getElementById('countdown');
        if (!el) return;

        let seconds = parseInt(el.dataset.seconds);

        function formatTime(s) {
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const sec = s % 60;
            return h + " jam " + m + " menit " + sec + " detik";
        }

        el.innerText = formatTime(seconds);

        const timer = setInterval(function() {
            if (seconds > 0) {
                seconds--;
                el.innerText = formatTime(seconds);
            } else {
                clearInterval(timer);
            }
        }, 1000);

    });
</script>
@endif
@endsection