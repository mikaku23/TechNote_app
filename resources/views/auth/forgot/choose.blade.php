<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Lupa Password - TechNoteApp</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
</head>

<body class="">
    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">

                <div class="col-md-4 offset-md-4">
                    <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">

                        <div class="card-body z-3 px-md-0 px-lg-4">

                            <a class="navbar-brand d-flex align-items-center mb-3">
                                <div class="logo-main" style="position:fixed; left:12px; top:12px;">
                                    <div class="logo-normal">
                                        <img src="{{ asset('assets/images/icon.png') }}" class="img-fluid" style="height:30px;">
                                    </div>
                                </div>

                                <h4 class="logo-title ms-3" style="position:fixed; left:30px; top:15px;">
                                    TechNoteApp
                                </h4>
                            </a>
                            <div class="card-body z-3 px-md-0 px-lg-4">
                                <h2 class="mb-2 text-center">Lupa Password</h2>
                                <p class="text-center">Pilih metode untuk mereset password</p>

                                <div class="d-flex flex-column gap-3">
                                    <a href="{{ route('forgot.phone') }}" class="btn btn-primary btn-lg">Gunakan Nomor HP (WhatsApp)</a>
                                    <a href="{{ route('forgot-password') }}" class="btn btn-outline-primary btn-lg">Gunakan Username</a>
                                </div>

                                <p class="mt-3 text-center">
                                    Kembali ke <a href="{{ route('login') }}">Login</a>
                                </p>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>
</body>

</html>