<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/dash-mhs.css') }}">

    @yield('css')
</head>

<body>
    <div class="shape-1"></div>
    <div class="shape-2"></div>

    <header class="site-nav" role="navigation" aria-label="Navigasi utama">
        @include('template_mahasiswa.header')
    </header>

    <main class="hero">
        @yield('konten')

    </main>

    <footer class="site-footer">
        @include('template_mahasiswa.footer')
    </footer>

    <script>
        // contoh aksi sederhana untuk tombol sign out
        document.getElementById('signOutBtn').addEventListener('click', function() {
            // untuk demo, tampilkan konfirmasi browser
            if (confirm('Yakin ingin keluar?')) {
                // aksi logout: arahkan ke endpoint logout (ganti sesuai backend)
                window.location.href = '/logout';
            }
        });
    </script>
    @yield('js')
</body>

</html>