@extends('template_admin.layout')
@section('title', 'Dashboard Admin')
@section('css')
<style>
    .circle-progress-wrapper {
        position: relative;
    }

    .circle-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>
@endsection
@section('konten')
<div class="col-md-12 col-lg-12 pt-3">
    <div class="row row-cols-1"></div>
    <div class="overflow-hidden d-slider1 swiper-container-initialized swiper-container-horizontal swiper-container-pointer-events">
        <ul class="p-0 m-0 mb-2 swiper-wrapper list-inline" id="swiper-wrapper-87636f10519107f681" aria-live="polite">
            <li class="swiper-slide card card-slide aos-init aos-animate" data-aos="fade-up" data-aos-delay="700" style="width: 215.5px; margin-right: 32px;">
                <div class="card-body">
                    <div class="progress-widget text-center">
                        <div class="circle-progress-wrapper position-relative d-inline-block" style="width:100px; height:100px;">
                            <!-- Lingkaran background -->
                            <svg width="100" height="100" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                <circle class="circle-progress-value" cx="50" cy="50" r="46" fill="none" stroke="#00E699" stroke-width="8"
                                    stroke-dasharray="289" stroke-dashoffset="289"
                                    data-value="{{ $persentaseUser }}" transform="rotate(-90 50 50)"></circle>
                            </svg>

                            <!-- Icon di tengah lingkaran -->
                            <div class="circle-icon position-absolute top-50 start-50 translate-middle">
                                <svg class="icon-24" width="40" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 
                        1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="progress-detail mt-2">
                            <p class="mb-1">Pengguna Aktif</p>
                            <h4 class="counter">{{ $totalUser }}</h4>
                        </div>
                    </div>
                </div>
            </li>


            <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="800" style="width: 215.5px; margin-right: 32px;">
                <div class="card-body">
                    <div class="progress-widget text-center">
                        <div class="circle-progress-wrapper position-relative d-inline-block" style="width:100px; height:100px;">
                            <!-- Lingkaran background -->
                            <svg width="100" height="100" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                <circle class="circle-progress-value" cx="50" cy="50" r="46" fill="none" stroke="#007bff" stroke-width="8"
                                    stroke-dasharray="289" stroke-dashoffset="289"
                                    data-value="{{ $persentaseSoftware }}" transform="rotate(-90 50 50)"></circle>
                            </svg>
                            <!-- Icon di tengah -->
                            <div class="circle-icon position-absolute top-50 start-50 translate-middle">
                                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                                    <rect x="2" y="4" width="28" height="20" rx="1.5" opacity="0.4" fill="currentColor" />
                                    <path d="M2 18h20v1.5c0 .828-.672 1.5-1.5 1.5h-17C2.672 21 2 20.328 2 19.5V18z" fill="currentColor" />
                                    <path d="M9.5 10.5L8 12l1.5 1.5M14.5 10.5L16 12l-1.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="progress-detail mt-2">
                            <p class="mb-1">Total Software</p>
                            <h4 class="counter">{{ $totalSoftware }}</h4>
                        </div>
                    </div>
                </div>
            </li>
            <!-- Card rekap -->
            <li class="swiper-slide card card-slide" data-aos="fade-up" data-aos-delay="900" style="width: 215.5px; margin-right: 32px;">
                <div class="card-body">
                    <div class="progress-widget text-center">
                        <div class="circle-progress-wrapper position-relative d-inline-block" style="width:100px; height:100px;">
                            <svg width="100" height="100" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="46" fill="none" stroke="#ddd" stroke-width="8"></circle>
                                <circle class="circle-progress-value" cx="50" cy="50" r="46" fill="none" stroke="#FF9900" stroke-width="8"
                                    stroke-dasharray="289" stroke-dashoffset="289"
                                    data-value="{{ $persentaserekap }}" transform="rotate(-90 50 50)"></circle>
                            </svg>
                            <div class="circle-icon position-absolute top-50 start-50 translate-middle">
                                <!-- Ukuran ikon diperbesar -->
                                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-24">
                                    <path opacity="0.4" d="M7 2h6l2 2h3v17a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" fill="currentColor" />
                                    <path d="M12 7v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.5 11h7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.5 14h7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                        <div class="progress-detail mt-2">
                            <p class="mb-1">Total rekap</p>
                            <h4 class="counter">{{ $totalrekap }}</h4>
                        </div>
                    </div>
                </div>
            </li>
        </ul>

        <div class="col-md-12">
            <div class="card">
                <div class="flex-wrap card-header d-flex justify-content-between align-items-center">
                    <div class="header-title">
                        <h4 class="card-title">Data Rekapitulasi</h4>
                        <p class="mb-0">Grafik Instalasi & Perbaikan</p>
                    </div>

                    <div class="dropdown">
                        <a id="dropdownMenu" href="#" class="text-gray dropdown-toggle" data-bs-toggle="dropdown">
                            Bulan Ini
                        </a>
                        <ul id="dropdownFilter" class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-period="week">Minggu Ini</a></li>
                            <li><a class="dropdown-item" href="#" data-period="month">Bulan Ini</a></li>
                            <li><a class="dropdown-item" href="#" data-period="year">Tahun Ini</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body">
                    <div id="chart-dashboard" style="min-height: 260px;"></div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
@endsection
@section('js')
<script>
    document.querySelectorAll('.circle-progress-value').forEach(function(circle) {
        const radius = circle.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;

        // Ambil persentase dari data-value (dari controller)
        const value = parseInt(circle.dataset.value) || 0;

        circle.style.strokeDasharray = `${circumference}`;
        circle.style.strokeDashoffset = circumference;

        // animasi
        setTimeout(() => {
            const offset = circumference - (value / 1000) * circumference;
            circle.style.transition = 'stroke-dashoffset 1.5s ease';
            circle.style.strokeDashoffset = offset;
        }, 100);
    });
    document.querySelectorAll('.circle-progress-value').forEach(function(circle) {
        const radius = circle.r.baseVal.value;
        const circumference = 2 * Math.PI * radius;

        const value = parseInt(circle.dataset.value) || 0;

        circle.style.strokeDasharray = `${circumference}`;
        circle.style.strokeDashoffset = circumference;

        setTimeout(() => {
            const offset = circumference - (value / 100) * circumference;
            circle.style.transition = 'stroke-dashoffset 1.5s ease';
            circle.style.strokeDashoffset = offset;
        }, 100);
    });
</script>
<div id="dashboardData"
    data-label-hari='@json($labelHari)'
    data-instalasi-hari='@json($dataInstalasiHari)'
    data-perbaikan-hari='@json($dataPerbaikanHari)'
    data-label-tanggal='@json($labelTanggal)'
    data-instalasi-tanggal='@json($dataInstalasiTanggal)'
    data-perbaikan-tanggal='@json($dataPerbaikanTanggal)'
    data-label-bulan='@json($labelBulan)'
    data-instalasi-bulan='@json($dataInstalasiBulan)'
    data-perbaikan-bulan='@json($dataPerbaikanBulan)'
    data-label-tahun='@json($labelTahun)'
    data-semua-tahun='@json($dataSemuaTahun)'>
</div>

<script src="{{ asset('assets/js/dashboard.js') }}"></script>
@endsection