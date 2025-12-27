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
    <style>
        /* floating button */
        #floating-chat {
            position: fixed;
            right: 22px;
            bottom: 22px;
            z-index: 9999;
        }

        #floating-chat button {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0ea5a9, #06b6d4);
            box-shadow: 0 8px 20px rgba(3, 7, 18, 0.2);
            cursor: pointer;
        }

        #floating-chat button svg {
            display: block;
        }

        /* popup chat */
        #chat-popup {
            position: fixed;
            right: 22px;
            bottom: 92px;
            /* di atas tombol */
            width: 340px;
            max-width: calc(100% - 44px);
            height: 460px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(2, 6, 23, 0.18);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 10000;
            font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
        }

        /* sembunyikan default, class 'hidden' akan menampilkan */
        .hidden {
            display: none;
        }

        /* header */
        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, rgba(246, 252, 255, 1), #ffffff);
        }

        .chat-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #0f172a;
        }

        .chat-logo {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            object-fit: cover;
        }

        /* messages area */
        .chat-messages {
            padding: 14px;
            flex: 1 1 auto;
            overflow-y: auto;
            background: linear-gradient(180deg, #fbfdff, #ffffff);
        }

        /* message bubbles */
        .msg {
            max-width: 78%;
            margin-bottom: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            white-space: pre-line;
            /* \n => break, spasi berlebih dirapikan */
            word-break: break-word;
            /* memecah kata agar tidak meluber */
            line-height: 1.45;

            font-size: 14px;
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.02);
        }

        .msg.user {
            margin-left: auto;
            background: #e6fffa;
            color: #064e3b;
            border-bottom-right-radius: 6px;
        }

        .msg.bot {
            margin-right: auto;
            background: #f1f5f9;
            color: #0f172a;
            border-bottom-left-radius: 6px;
        }

        /* form */
        .chat-form {
            display: flex;
            gap: 8px;
            padding: 10px;
            border-top: 1px solid #eef2f7;
            background: #fff;
        }

        #chat-input {
            flex: 1 1 auto;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #e6eef6;
            outline: none;
            font-size: 14px;
        }

        #chat-send {
            padding: 10px 14px;
            border-radius: 8px;
            border: none;
            background: linear-gradient(90deg, #06b6d4, #0ea5a9);
            color: white;
            font-weight: 600;
            cursor: pointer;
        }

        /* small screen tweaks */
        @media (max-width:480px) {
            #chat-popup {
                width: 92%;
                right: 4%;
                left: 4%;
                bottom: 80px;
                height: 60vh;
            }

            #floating-chat {
                right: 16px;
                bottom: 16px;
            }
        }

       
    </style>


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


    <!-- Floating Chat Button -->
    <div id="floating-chat" aria-hidden="false">
        <button id="chat-toggle" aria-label="Buka chat">
            <!-- simple chatbot icon (speech robot) -->
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H8l-4 3V6z" stroke="white" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="9" cy="10" r="1.25" fill="white" />
                <circle cx="15" cy="10" r="1.25" fill="white" />
            </svg>
        </button>
    </div>

    <!-- Chat Popup -->
    <div id="chat-popup" class="hidden" role="dialog" aria-label="Chatbot layanan teknisi">
        <div class="chat-header">
            <div class="chat-title">
                <img src="{{ asset('assets/images/icon.png') }}" alt="chatbot" class="chat-logo">
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

    <script>
        (function() {
            const toggleBtn = document.getElementById('chat-toggle');
            const chatPopup = document.getElementById('chat-popup');
            const closeBtn = document.getElementById('chat-close');
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatMessages = document.getElementById('chat-messages');
            const sendBtn = document.getElementById('chat-send');

            // buka/tutup popup
            toggleBtn.addEventListener('click', () => {
                chatPopup.classList.toggle('hidden');
                // fokus pada input saat dibuka
                if (!chatPopup.classList.contains('hidden')) {
                    setTimeout(() => chatInput.focus(), 120);
                }
            });

            closeBtn.addEventListener('click', () => {
                chatPopup.classList.add('hidden');
            });

            // helper menambahkan pesan ke UI
            function appendMessage(text, who = 'bot') {
                const div = document.createElement('div');
                div.className = 'msg ' + (who === 'user' ? 'user' : 'bot');
                div.textContent = text;
                chatMessages.appendChild(div);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // tampilkan pesan pembuka (opsional)
            appendMessage('Halo — chat ini khusus untuk bantuan aplikasi layanan teknisi. Ketik pertanyaan tentang penginstalan, perbaikan, rekap, atau contact.', 'bot');

            // kirim pesan
            async function sendMessage() {
                const text = chatInput.value.trim();
                if (!text) return;
                appendMessage(text, 'user');
                chatInput.value = '';
                sendBtn.disabled = true;
                sendBtn.textContent = 'Mengirim...';

                try {
                    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                    const token = tokenMeta ? tokenMeta.getAttribute('content') : null;

                    const res = await fetch('/chatbot', {
                        method: 'POST',
                        credentials: 'same-origin', // penting: sertakan cookie session
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            message: text
                        })
                    });

                    // debug: tampilkan status di console
                    console.log('chatbot response status:', res.status);

                    if (!res.ok) {
                        // coba ambil body untuk pesan error server
                        let serverText = '';
                        try {
                            const json = await res.json();
                            serverText = json.message ?? json.reply ?? JSON.stringify(json);
                        } catch (e) {
                            serverText = await res.text();
                        }

                        // tampilkan pesan lebih detail di UI (bisa diubah jadi user-friendly)
                        appendMessage(`Maaf, gagal menghubungi server (status ${res.status}). ${serverText}`, 'bot');
                    } else {
                        const data = await res.json();
                        const reply = data.reply || 'Maaf, terjadi kesalahan pada server.';
                        appendMessage(reply, 'bot');
                    }
                } catch (err) {
                    appendMessage('Terjadi kesalahan jaringan. Cek konsol browser untuk detail.', 'bot');
                    console.error('Chat send error:', err);
                } finally {
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Kirim';
                }
            }


            sendBtn.addEventListener('click', sendMessage);
            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                sendMessage();
            });

            // kirim bila tekan Enter (tanpa Shift)
            chatInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        })();
    </script>
    @yield('js')
</body>

</html>