<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sign Up - TechNoteApp</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">

    <style>
        .input-placeholder-muted::placeholder {
            color: #9aa0a6;
            opacity: 1;
        }
    </style>

</head>

<body class=" ">
    <!-- loader -->
    <div id="loaderWrapper" class="loader-fullscreen">
        <div class="loader-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
    </div>

    <div class="wrapper">
        <section class="login-content">
            <div class="row m-0 align-items-center bg-white vh-100">
                <div class="col-md-6">
                    <div class="row justify-content-center">
                        <div class="col-md-10">
                            <div class="card card-transparent shadow-none d-flex justify-content-center mb-0 auth-card">
                                <div class="card-body z-3 px-md-0 px-lg-4">

                                    <!-- Logo -->
                                    <a class="navbar-brand d-flex align-items-center mb-3">
                                        <div class="logo-main" style="position:fixed; left:12px; top:12px;">
                                            <img src="{{ asset('assets/images/icon.png') }}" class="img-fluid" style="height:30px;">
                                        </div>

                                        <h4 class="logo-title ms-3" style="position:fixed; left:30px; top:15px; margin:0; color:#1b1f23;">
                                            TechNoteApp
                                        </h4>
                                    </a>

                                    <h2 class="mb-2 text-center">Sign Up</h2>
                                    <p class="text-center">Isi data berikut untuk membuat akun.</p>

                                    <form method="POST" enctype="multipart/form-data" action="{{ route('signup.store') }}">
                                        @csrf

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
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>NIM / NIP</label>
                                                    <input type="number" name="idnumber" class="form-control input-placeholder-muted"
                                                        placeholder="Masukkan NIM atau NIP" value="{{ old('idnumber') }}" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Nama Lengkap</label>
                                                    <input type="text" name="nama" class="form-control input-placeholder-muted"
                                                        placeholder="Masukkan nama" value="{{ old('nama') }}" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control input-placeholder-muted"
                                                placeholder="Buat username" value="{{ old('username') }}" required>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Password</label>
                                                    <input type="password" name="password" class="form-control input-placeholder-muted" placeholder="Buat Password" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Konfirmasi Password</label>
                                                    <input type="password" name="password_confirmation" class="form-control input-placeholder-muted" placeholder="Ulangi Password" required>
                                                    <small id="pw-error" class="text-danger" style="display:none;">Password tidak cocok</small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Pertanyaan Keamanan</label>
                                                    <input type="text" readonly name="security_question"
                                                        class="form-control input-placeholder-muted"
                                                        value="{{ $question }}">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label>Jawaban Keamanan</label>
                                                    <input type="text" name="security_answer" class="form-control input-placeholder-muted" required
                                                        placeholder="Masukkan jawaban keamanan" value="{{ old('security_answer') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>Foto Profil</label>
                                            <input type="file" name="foto" class="form-control">
                                        </div>

                                        <div class="d-flex justify-content-center mt-3">
                                            <button class="btn btn-primary w-100 py-2" id="btn-submit">Daftar</button>
                                        </div>

                                        <p class="mt-3 text-center">
                                            Sudah punya akun?
                                            <a href="{{ route('login') }}">Login</a>
                                        </p>

                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Gambar kanan -->
                <div class="col-md-6 d-md-block d-none bg-primary p-0 mt-n1 vh-100 overflow-hidden">
                    <img src="{{ asset('assets/images/stmik.jpg') }}" class="img-fluid gradient-main" alt="images">
                </div>
            </div>
        </section>
    </div>

    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const password = document.querySelector("input[name='password']");
            const passwordConfirmation = document.querySelector("input[name='password_confirmation']");
            const errorText = document.getElementById("pw-error");
            const submitBtn = document.getElementById("btn-submit");
            const form = document.querySelector("form");

            function checkPasswordMatch() {
                if (password.value.length > 0 || passwordConfirmation.value.length > 0) {
                    if (password.value !== passwordConfirmation.value) {
                        errorText.style.display = "block";
                        submitBtn.disabled = true;
                    } else {
                        errorText.style.display = "none";
                        submitBtn.disabled = false;
                    }
                } else {
                    errorText.style.display = "none";
                    submitBtn.disabled = false;
                }
            }

            // cek realtime
            password.addEventListener("input", checkPasswordMatch);
            passwordConfirmation.addEventListener("input", checkPasswordMatch);

            // cegah submit kalau tidak sama
            form.addEventListener("submit", function(e) {
                if (password.value !== passwordConfirmation.value) {
                    e.preventDefault();
                    errorText.style.display = "block";
                    submitBtn.disabled = true;
                }
            });
        });
    </script>

</body>

</html>