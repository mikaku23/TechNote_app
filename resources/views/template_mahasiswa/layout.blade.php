<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/dash-mhs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chatbot.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/chat.css') }}">




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


    <!-- Floating Chat & Chat Popup (ganti bagian lama dengan ini) -->
    <!-- Pastikan meta csrf-token sudah ada di head seperti sebelumnya -->
    <div id="floating-chat" aria-hidden="false">
        <button id="chat-toggle" aria-label="Buka chat" title="Chatbot">
            <!-- icon simple (robot) -->
            <img src="{{ asset('assets/images/chatbot.png') }}" alt="chatbot" class="chat-logo">

        </button>
    </div>

    <div id="chat-popup" class="chat-popup" role="dialog" aria-label="Chatbot layanan teknisi" aria-hidden="true">
        <div class="chat-header">
            <div class="chat-title">
                <img src="{{ asset('assets/images/chatbot.png') }}" alt="chatbot" class="chat-logo">
                <span>Chatbot</span>
            </div>
            <button id="chat-close" aria-label="Tutup chat">&times;</button>
        </div>

        <div id="chat-messages" class="chat-messages" aria-live="polite"></div>

        <form id="chat-form" class="chat-form" onsubmit="return false;">
            <input id="chat-input" type="text" placeholder="Tulis pesan..." autocomplete="off" />
            <button id="chat-send" type="button">Kirim</button>
        </form>
    </div>





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

    <script src="{{ asset('assets/js/chat.js') }}"></script>
    <script src="{{ asset('assets/js/chatbot.js') }}"></script>

    @yield('js')
</body>

</html>