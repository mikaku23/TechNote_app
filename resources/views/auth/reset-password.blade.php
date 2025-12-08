<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="light" data-bs-theme-color="theme-color-default">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password - TechNoteApp</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hope-ui.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/customizer.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/rtl.min.css?v=5.0.0') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

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

                            <h2 class="mb-2 text-center">Reset Password</h2>
                            <p class="text-center">Masukkan password baru anda.</p>

                            <form action="{{ route('forgot-reset') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">

                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-label">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password anda">
                                        <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>

                                </div>


                                <div class="form-group mt-2">
                                    <label class="form-label">Konfirmasi Password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Konfirmasi password anda">
                                    <small id="passwordError" class="text-danger" style="display:none;">Password tidak cocok</small>
                                </div>


                                <div class="d-flex justify-content-center mt-3">
                                    <button class="btn btn-primary" id="btn-submit">Simpan Password</button>
                                </div>

                                <p class="mt-3 text-center">
                                    Kembali ke <a href="{{ route('login') }}">Login</a>
                                </p>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>
    <script src="{{ asset('assets/js/hope-ui.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Show/hide password
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

            // Realtime password match validation
            const form = document.querySelector('form');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            const passwordError = document.getElementById('passwordError');
            const submitBtn = document.getElementById('btn-submit');

            function checkMatch() {
                if (password.value.length > 0 || passwordConfirmation.value.length > 0) {
                    if (password.value !== passwordConfirmation.value) {
                        passwordError.style.display = 'block';
                        submitBtn.disabled = true; // tombol tidak bisa di submit
                    } else {
                        passwordError.style.display = 'none';
                        submitBtn.disabled = false; // tombol aktif
                    }
                } else {
                    passwordError.style.display = 'none';
                    submitBtn.disabled = false;
                }
            }

            password.addEventListener('input', checkMatch);
            passwordConfirmation.addEventListener('input', checkMatch);

            form.addEventListener('submit', function(e) {
                if (password.value !== passwordConfirmation.value) {
                    e.preventDefault();
                    passwordError.style.display = 'block';
                    submitBtn.disabled = true;
                }
            });

        });
    </script>

</body>

</html>