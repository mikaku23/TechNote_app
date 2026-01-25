@extends('template_mahasiswa.layout')

@section('title', 'Top 10 Ranking')

@section('css')
<link rel="stylesheet" href="{{ asset('assets/css/rank-user.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/typing.css') }}">
@endsection

@section('konten')
<section class="card-hero">
    <div class="card-hero-container mb-3">
        <div class="typing-container">
            <span id="typingText"></span><span class="cursor">|</span>
        </div>
    </div>

    <h4 class="mb-4">Top 10 Mahasiswa Teraktif Semester Ini</h4>

    <div class="card glass-table mb-4 p-4 text-center podium-wrapper">
        @if($myRank)
        <div
            id="myPodium"
            class="my-podium"
            data-rank="{{ $myRank }}"
            data-badge="{{ $myBadge ?? '' }}"
            data-progress="{{ (int)($progressWidth ?? 0) }}">
            {{-- hanya tampilkan badge bila ada kelas (gold/silver/bronze) --}}
            @if($myBadge)
            <div class="podium-badge {{ $myBadge }}"></div>
            @endif

            <p class="mb-1 text-muted small">Selamat Anda menduduki peringkat</p>

            <div class="podium-number-wrap" aria-live="polite" aria-atomic="true">
                <div id="podiumOld" class="podium-number podium-number--old" aria-hidden="true"></div>
                <div id="podiumNow" class="podium-number podium-number--now">#{{ $myRank }}</div>
            </div>

            <div class="podium-label">
                @if($myBadge === 'gold') 🥇 Gold
                @elseif($myBadge === 'silver') 🥈 Silver
                @elseif($myBadge === 'bronze') 🥉 Bronze
                @else Peringkat Anda
                @endif
            </div>

            <div class="progress mt-3" style="height:8px">
                <div class="progress-bar"></div>
            </div>

            <!-- gunakan kelas meta, tidak ada mt-2 atau mb-? -->
            <p class="meta">
                dengan {{ $progressWidth ?? 0 }}% dari total penginstalan.
            </p>

            <p class="meta mb-0">
                Terus pertahankan performa penginstalan anda
            </p>


        </div>
        @else
        <div class="text-muted">Maaf — belum masuk peringkat semester ini.</div>
        @endif
    </div>


</section>
@endsection

@section('js')
<script src="{{ asset('assets/js/rank-user.js') }}"></script>

{{-- typing script tetap inline --}}
<script>
    const texts = [{
            text: "Halo, {{ auth()->user()->nama ?? 'Mahasiswa' }} 👋",
            class: "text-welcome"
        },
        {
            text: "Top 10 Ranking Semester Ini 🏆",
            class: "text-status"
        },
        {
            text: "Terus tingkatkan aktivitasmu 🚀",
            class: "text-help"
        },
    ];

    (function typing() {
        let count = 0,
            index = 0;
        const el = document.getElementById('typingText');
        (function type() {
            if (count === texts.length) count = 0;
            const current = texts[count];
            const letter = current.text.slice(0, ++index);
            el.innerHTML = `<span class="${current.class}">${letter}</span><span class="cursor"></span>`;
            if (index === current.text.length) {
                count++;
                index = 0;
                return setTimeout(type, 1500);
            }
            setTimeout(type, 60);
        })();
    })();
</script>
@endsection