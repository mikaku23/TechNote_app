@if ($data->hasPages())
<div class="custom-pagination mt-3 d-flex justify-content-center align-items-center gap-2 flex-wrap">

    {{-- Tombol Previous --}}
    @if ($data->onFirstPage())
    <span class="disabled-page">← Back</span>
    @else
    <a href="{{ $data->previousPageUrl() }}" class="page-link">← Back</a>
    @endif

    {{-- Nomor Halaman --}}
    @foreach ($data->getUrlRange(1, $data->lastPage()) as $page => $url)
    @if ($page == $data->currentPage())
    <span class="active-page">{{ $page }}</span>
    @else
    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
    @endif
    @endforeach

    {{-- Tombol Next --}}
    @if ($data->hasMorePages())
    <a href="{{ $data->nextPageUrl() }}" class="page-link">Next →</a>
    @else
    <span class="disabled-page">Next →</span>
    @endif

</div>
@endif