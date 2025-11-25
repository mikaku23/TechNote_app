<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">

    <!-- Aos Animation Css -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/aos/dist/aos.css') }}">

    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=5.0.0') }}">

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=5.0.0') }}">

    <!-- Customizer Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css?v=5.0.0') }}">

    <!-- RTL Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css?v=5.0.0') }}">

    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Bootstrap Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        .alert-warning {
            background-color: #fff3cd !important;
            color: #856404 !important;
            border-color: #ffeeba !important;
        }

        .input-group-text {
            background-color: #e9ecef;
            border-left: none;
            border-radius: 0 0.375rem 0.375rem 0;
        }

        .input-group .form-control {
            border-right: none;
            border-radius: 0.375rem 0 0 0.375rem;
            background-color: #f5f5f5;
        }

        .form-control {
            background-color: #f5f5f5;
        }


        /* Fullscreen loader */
        .loader-fullscreen {
            position: fixed;
            inset: 0;
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;

            /* Efek kaca transparan */
            background: rgba(255, 255, 255, 0.28);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.15);

            /* Fade-in cepat dan halus */
            animation: fadeInLoaderBlur 0.000001s ease-out;
        }

        @keyframes fadeInLoaderBlur {
            from {
                opacity: 0;
                filter: blur(6px);
                /* langsung mulai dari blur */
                transform: scale(1.015);
            }

            to {
                opacity: 1;
                filter: blur(0px);
                /* blur menghilang halus */
                transform: scale(1);
            }
        }


        /* titik loader */
        .loader-dots {
            display: flex;
            gap: 8px;
        }

        .loader-dots .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--bs-primary);
            animation: pulse 0.28s ease-in-out 2 alternate;
        }

        .loader-dots .dot:nth-child(1) {
            animation-delay: 0s;
        }

        .loader-dots .dot:nth-child(2) {
            animation-delay: 0.05s;
        }

        .loader-dots .dot:nth-child(3) {
            animation-delay: 0.1s;
        }

        @keyframes pulse {
            from {
                transform: scale(0.75);
                opacity: 0.3;
            }

            to {
                transform: scale(1.1);
                opacity: 1;
            }
        }


        /* efek fade-out halus dengan blur */
        .loader-fullscreen.fade-out {
            animation: fadeOutLoaderBlur 0.38s ease-in forwards;
        }

        @keyframes fadeOutLoaderBlur {
            from {
                opacity: 1;
                transform: scale(1);
                filter: blur(0px);
            }

            to {
                opacity: 0;
                transform: scale(1.03);
                filter: blur(4px);
                visibility: hidden;
            }
        }
    </style>

    @yield('css')

</head>

<body class="  ">
    <!-- loader Start -->
    <div id="loaderWrapper" class="loader-fullscreen">
        <div class="loader-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
    <!-- loader END -->

    <aside class="sidebar sidebar-default sidebar-white sidebar-base navs-rounded-all ">
        <div class="sidebar-header d-flex align-items-center justify-content-start">
            <a href="../dashboard/index.html" class="navbar-brand">

                <!--Logo start-->
                <div class="logo-main">
                    <div class="logo-normal">
                        <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                            <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                            <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                            <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                        </svg>
                    </div>
                    <div class="logo-mini">
                        <svg class=" icon-30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="-0.757324" y="19.2427" width="28" height="4" rx="2" transform="rotate(-45 -0.757324 19.2427)" fill="currentColor" />
                            <rect x="7.72803" y="27.728" width="28" height="4" rx="2" transform="rotate(-45 7.72803 27.728)" fill="currentColor" />
                            <rect x="10.5366" y="16.3945" width="16" height="4" rx="2" transform="rotate(45 10.5366 16.3945)" fill="currentColor" />
                            <rect x="10.5562" y="-0.556152" width="28" height="4" rx="2" transform="rotate(45 10.5562 -0.556152)" fill="currentColor" />
                        </svg>
                    </div>
                </div>
                <!--logo End-->




                <h4 class="logo-title">Hope UI</h4>
            </a>
            <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
                <i class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </i>
            </div>
        </div>
        <div class="sidebar-body pt-0 data-scrollbar">
            <div class="sidebar-list">
                <!-- Sidebar Menu Start -->
                @include('template_admin.sidebar')
                <!-- Sidebar Menu End -->
            </div>
        </div>
        <div class="sidebar-footer"></div>
    </aside>
    <main class="main-content">
        <div class="position-relative iq-banner">
            <!--Nav Start-->
            <nav class="nav navbar navbar-expand-xl navbar-light iq-navbar">
                @include('template_admin.navbar')
            </nav> <!-- Nav Header Component Start -->
            <div class="iq-navbar-header" style="height: 50px;">

            </div> <!-- Nav Header Component End -->
            <!--Nav End-->
        </div>
        <div class="container-fluid content-inner mt-n5 py-0">
            <div class="row">
                @yield('konten')
            </div>
        </div>

        <!-- Footer Section Start -->
        <footer class="footer">
            <div class="footer-body">
                @include('template_admin.footer')
            </div>
        </footer>
        <!-- Footer Section End -->
    </main>

    <!-- Library Bundle Script -->
    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>

    <!-- External Library Bundle Script -->
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>

    <!-- Widgetchart Script -->
    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>

    <!-- mapchart Script -->
    <script src="{{ asset('assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('assets/js/charts/dashboard.js') }}"></script>

    <!-- fslightbox Script -->
    <script src="{{ asset('assets/js/plugins/fslightbox.js') }}"></script>

    <!-- Settings Script -->
    <script src="{{ asset('assets/js/plugins/setting.js') }}"></script>

    <!-- Slider-tab Script -->
    <script src="{{ asset('assets/js/plugins/slider-tabs.js') }}"></script>

    <!-- Form Wizard Script -->
    <script src="{{ asset('assets/js/plugins/form-wizard.js') }}"></script>

    <!-- AOS Animation Plugin-->
    <script src="{{ asset('assets/vendor/aos/dist/aos.js') }}"></script>

    <!-- App Script -->
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // pastikan loader terhapus setelah semua asset selesai load
        window.addEventListener('load', function() {
            const loader = document.getElementById('loaderWrapper');
            if (!loader) return;

            // tampilkan minimal 300ms agar animasi terlihat singkat tapi tidak blinking
            const MIN_SHOW = 300;
            const shownAt = performance.now();

            // berikan sedikit delay agar terlihat halus
            const hide = () => {
                loader.style.transition = 'opacity 0.25s ease, filter 0.25s ease';
                loader.style.filter = 'blur(2px)'; // efek kabur ringan saat memudar
                loader.style.opacity = '0'; // tetap mempertahankan fade-out
                setTimeout(() => {
                    loader.style.display = 'none';
                }, 260);
            };

            const elapsed = performance.now() - shownAt;
            const remaining = Math.max(0, MIN_SHOW - elapsed);
            setTimeout(hide, remaining);
        });
    </script>



    @yield('js')


</body>
@section('js')
<script>
    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().destroy();
        }

        $('#datatable').DataTable({
            "lengthChange": false,
            "searching": true,
            "paging": true,
            "info": true,
            "errorMode": "none" // ⬅️ ini penting!

        });
    });
</script>
@endsection

</html>