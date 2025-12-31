<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">

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

    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/my-profile.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/acc-setting.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/mail.css') }}">



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
            <a class="navbar-brand" style="cursor: default;">

                <!--Logo start-->
                <div class="logo-main">
                    <div class="logo-normal">
                        <img src="{{ asset('assets/images/icon.png') }}" style="height: 30px;" alt="logo">
                    </div>
                    <div class="logo-mini">
                        <img src="{{ asset('assets/images/icon.png') }}" style="height: 30px;" alt="logo">
                    </div>
                </div>
                <!--logo End-->




                <h4 class="logo-title">TechNote</h4>
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
                @include('template_admin.barside')
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

            <div class="modal fade show-modal-glass my-profile-modal" id="myProfileModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content glass-popup">
                        <div class="modal-header">
                            <h5 class="modal-title">My Profile</h5>
                            <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="myProfileContent">
                            <!-- Konten AJAX akan dimuat di sini -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary close-modal" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="accountSettingsModal" tabindex="-1" aria-labelledby="accountSettingsLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content glass-popup">
                        <div class="modal-header">
                            <h5 class="modal-title">Account Settings</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('account.update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @php
                            $foto = Auth::user()->foto && file_exists(public_path('foto/' . Auth::user()->foto))
                            ? asset('foto/' . Auth::user()->foto)
                            : asset('assets/images/default.png');
                            @endphp
                            <div class="modal-body">
                                <div class="row mb-3 text-center">
                                    <div class="account-photo-wrapper">
                                        <img src="{{ $foto }}" alt="User Profile">
                                    </div>

                                    <div>
                                        <label class="form-label">Ganti Photo</label>
                                        <input type="file" name="foto" class="form-control form-control-sm w-50 mx-auto">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" value="{{ Auth::user()->username }}" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                                    <small id="pw-not-match" style="color: red; display:none; font-size: 12px;">
                                        Password tidak sama
                                    </small>

                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary btn-sm" id="btn-submit">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Detail Pesan -->
            <div class="modal fade" id="modalDetailPesan" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content glass-popup">

                        <div class="modal-header border-0">
                            <h6 class="modal-title glass-title">Detail Pesan</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="detail-row">
                                <span class="detail-label">Nama:</span>
                                <span class="detail-value" id="detail-nama"></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Email:</span>
                                <span class="detail-value" id="detail-email"></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Pesan:</span>
                                <span class="detail-value" id="detail-pesan"></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Dikirim:</span>
                                <span class="detail-value" id="detail-tanggal"></span>
                            </div>

                            <div class="detail-row">
                                <span class="detail-label">Oleh (user):</span>
                                <span class="detail-value" id="detail-user"></span>
                            </div>

                        </div>

                        <div class="modal-footer border-0">
                            <button class="btn btn-glass-blue" data-bs-dismiss="modal">
                                Tutup
                            </button>
                        </div>


                    </div>
                </div>
            </div>



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

    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const dropToggle = document.getElementById('notification-drop');
            const btnClose = document.querySelector('.btn-tutup-dropdown');
            let allowClose = false;

            // --- tombol "Tutup Menu" ---
            if (btnClose) {
                btnClose.addEventListener('click', function(e) {
                    e.stopPropagation();
                    allowClose = true;
                    const inst = bootstrap.Dropdown.getOrCreateInstance(dropToggle);
                    inst.hide();
                    setTimeout(() => allowClose = false, 150);
                });
            }

            // --- cegah dropdown menutup otomatis ---
            document.addEventListener('hide.bs.dropdown', function(e) {
                if (!allowClose && e.target.id === 'notification-drop') {
                    e.preventDefault();
                }
            });

            // --- saat klik "Detail" tetap buka dropdown ---
            document.querySelectorAll('.btn-detail-pesan').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const inst = bootstrap.Dropdown.getOrCreateInstance(dropToggle);
                    inst.show();
                });
            });

            // --- modal muncul → dropdown tetap buka ---
            const modal = document.getElementById('modalDetailPesan');
            if (modal) {
                modal.addEventListener('shown.bs.modal', function() {
                    const inst = bootstrap.Dropdown.getOrCreateInstance(dropToggle);
                    inst.show();
                });
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const pw = document.querySelector("input[name='password']");
            const confirm = document.querySelector("input[name='password_confirmation']");
            const warning = document.getElementById("pw-not-match");
            const submitBtn = document.getElementById("btn-submit");

            function checkMatch() {
                if (confirm.value.length > 0 || pw.value.length > 0) {
                    if (pw.value !== confirm.value) {
                        warning.style.display = "block";
                        submitBtn.disabled = true; // tombol tidak bisa diklik
                    } else {
                        warning.style.display = "none";
                        submitBtn.disabled = false; // tombol aktif kembali
                    }
                } else {
                    warning.style.display = "none";
                    submitBtn.disabled = false;
                }
            }

            pw.addEventListener("input", checkMatch);
            confirm.addEventListener("input", checkMatch);
        });
    </script>

    <script>
        $(document).ready(function() {
            $('.my-profile-btn').click(function(e) {
                e.preventDefault();
                $('#myProfileContent').html('<div class="spinner"><div></div><div></div><div></div></div>'); // loading
                $.get("{{ route('my-profile') }}", function(data) {
                    $('#myProfileContent').html(data);
                });
            });

            $('.close-modal').click(function() {
                $('.show-modal-glass').modal('hide');
            });
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