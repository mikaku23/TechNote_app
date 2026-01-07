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
                                <h2 class="mb-2 text-center">Verifikasi OTP</h2>
                                <p class="text-center">Masukkan kode 5 digit yang dikirim via WhatsApp</p>

                                <form method="POST" action="{{ route('forgot.phone.verify.post') }}" id="otpForm">
                                    @csrf
                                    <input type="hidden" name="otp_id" value="{{ $otpId }}">

                                    <div class="d-flex justify-content-center gap-2">
                                        <input maxlength="1" class="form-control otp-input text-center" style="width:48px" />
                                        <input maxlength="1" class="form-control otp-input text-center" style="width:48px" />
                                        <input maxlength="1" class="form-control otp-input text-center" style="width:48px" />
                                        <input maxlength="1" class="form-control otp-input text-center" style="width:48px" />
                                        <input maxlength="1" class="form-control otp-input text-center" style="width:48px" />
                                    </div>

                                    <input type="hidden" name="otp" id="otpHidden">

                                    <div class="d-flex justify-content-center mt-3">
                                        <button class="btn btn-primary">Verifikasi</button>
                                    </div>
                                </form>

                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const inputs = document.querySelectorAll('.otp-input');
                                        inputs.forEach((el, idx) => {
                                            el.addEventListener('input', () => {
                                                if (el.value.length === 1 && idx < inputs.length - 1) {
                                                    inputs[idx + 1].focus();
                                                }
                                                collect();
                                            });
                                            el.addEventListener('keydown', (e) => {
                                                if (e.key === 'Backspace' && el.value === '' && idx > 0) {
                                                    inputs[idx - 1].focus();
                                                }
                                            });
                                        });

                                        function collect() {
                                            let val = '';
                                            inputs.forEach(i => val += (i.value || ''));
                                            document.getElementById('otpHidden').value = val;
                                        }

                                        // optional: focus first
                                        inputs[0].focus();
                                    });
                                </script>
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