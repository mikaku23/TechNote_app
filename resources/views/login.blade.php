<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sign In - TechNoteApp</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />


    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">

    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">

    <!-- Hope Ui Design System Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=5.0.0') }}">

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=5.0.0') }}">

    <!-- Customizer Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css?v=5.0.0') }}">

    <!-- RTL Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css?v=5.0.0') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">

    <style>
        .input-placeholder-muted::placeholder {
            color: #9aa0a6;
            opacity: 1;
        }

        .input-placeholder-muted::-webkit-input-placeholder {
            color: #9aa0a6;
        }

        .input-placeholder-muted:-ms-input-placeholder {
            color: #9aa0a6;
        }
    </style>

</head>

<body class=" ">
    <!-- loader Start -->
    <div id="loaderWrapper" class="loader-fullscreen">
        <div class="loader-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>
    <!-- loader END -->


    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                <div class="card-body z-3 px-md-0 px-lg-4">
                                    <a class="navbar-brand d-flex align-items-center mb-3">

                                        <!--Logo start-->
                                        <div class="logo-main" style="position:fixed; left:12px; top:12px; margin:0; z-index:9999; color:#1b1f23;">
                                            <div class="logo-normal">
                                                <img src="{{ asset('assets/images/icon.png') }}" class="img-fluid" alt="logo" style="height: 30px;">
                                            </div>
                                            <div class="logo-mini">
                                                <img src="{{ asset('assets/images/icon.png') }}" class="img-fluid" alt="logo" style="height: 30px;">
                                            </div>
                                        </div>
                                        <!--logo End-->

                                        @if(Auth::check())
                                        @php
                                        $role = Auth::user()->role->status ?? null;
                                        @endphp

                                        @if($role === 'admin')
                                        <script>
                                            window.location = "{{ route('dashboard-admin') }}";
                                        </script>
                                        @elseif($role === 'mahasiswa')
                                        <script>
                                            window.location = "{{ route('dashboard-mahasiswa') }}";
                                        </script>
                                        @elseif($role === 'dosen')
                                        <script>
                                            window.location = "{{ route('dashboard-dosen') }}";
                                        </script>
                                        @endif
                                        @endif
                                        <h4 class="logo-title ms-3" style="position:fixed; left:30px; top:15px; margin:0; z-index:9999; color:#1b1f23;">
                                            TechNoteApp
                                        </h4>
                                    </a>
                                    <h2 class="mb-2 text-center">Sign In</h2>
                                    <p class="text-center">Login to stay connected.</p>
                                    <form method="POST" action="{{ route('authenticate') }}">
                                        @csrf
                                        {{-- Pesan sukses setelah reset password --}}
                                        @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                        @endif

                                        {{-- Error login --}}
                                        @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="username" class="form-label">Username</label>
                                                    <input type="username" class="form-control input-placeholder-muted" id="username" name="username" aria-describedby="username" placeholder="Masukkan username anda">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label for="password" class="form-label">Password</label>
                                                    <div class="input-group">
                                                        <input type="password" class="form-control input-placeholder-muted" id="password" name="password" placeholder="Masukkan password anda">
                                                        <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                                                            <i class="fas fa-eye"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12 d-flex justify-content-between">
                                                <div class="form-check mb-3">
                                                </div>
                                                <a href="{{ route('forgot-password') }}">Forgot Password?</a>

                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary">Sign In</button>
                                        </div>

                                        <p class="mt-3 text-center">
                                            Don’t have an account? <a href="{{ route('signup') }}" class="text-underline">Click here to sign up.</a>
                                        </p>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                    <img src="{{ asset('assets/images/stmik-hd.jpg') }}" class="img-fluid gradient-main " alt="images">
                </div>
            </div>
        </section>
    </div>
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

    <!-- App Script -->
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>

    <script src="{{ asset('assets/js/layout.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const pwd = document.getElementById('password');
            const toggle = document.getElementById('togglePassword');
            if (toggle && pwd) {
                toggle.addEventListener('click', function() {
                    const type = pwd.getAttribute('type') === 'password' ? 'text' : 'password';
                    pwd.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    if (icon) {
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    }
                });
            }
        });
    </script>
</body>

</html>