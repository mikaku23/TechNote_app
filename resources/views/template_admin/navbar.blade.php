<!-- ===== CSS (taruh di head / file CSS utama) ===== -->
<style>
    /* tampilan navbar modern */
    .navbar-modern {
        backdrop-filter: blur(6px);
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.72), rgba(250, 250, 250, 0.6));
        border-bottom: 1px solid rgba(30, 40, 60, 0.06);
        box-shadow: 0 6px 18px rgba(20, 30, 60, 0.03);
    }

    /* spacing item */
    .navbar-modern .navbar-list .nav-item {
        margin-left: 6px;
        margin-right: 2px;
    }

    /* link look */
    .navbar-modern .nav-link {
        color: #333;
        padding: .45rem .7rem;
        border-radius: 10px;
        transition: all .18s ease;
        display: inline-flex;
        align-items: center;
        gap: .55rem;
    }

    /* hover halus */
    .navbar-modern .nav-link:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(15, 30, 80, 0.05);
        background: rgba(18, 92, 255, 0.04);
    }

    /* marquee */
    .navbar-marquee {
        width: 360px;
        overflow: hidden;
        position: relative;
        height: 38px;
        display: flex;
        align-items: center;
        padding: 0 .35rem;
        border-radius: 8px;
        background: rgba(250, 250, 250, 0.45);
        border: 1px solid rgba(200, 200, 200, 0.12);
    }

    .navbar-marquee .marquee-content {
        display: inline-block;
        white-space: nowrap;
        will-change: transform;
        animation: marquee 14s linear infinite;
        font-weight: 600;
        color: #244;
        letter-spacing: .2px;
    }

    .navbar-marquee:hover .marquee-content {
        animation-play-state: paused;
    }

    @keyframes marquee {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    /* notifikasi dot */
    .navbar-modern .dots {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 9px;
        height: 9px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .12);
    }

    /* jam */
    #navbarSupportedContent #waktuWIB {
        min-width: 180px;
        text-align: right;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: .95rem;
        color: #5b6770;
    }

    /* profil avatar */
    .navbar-modern .avatar {
        width: 40px;
        height: 40px;
        object-fit: cover;
    }

    /* responsive */
    @media (max-width: 991px) {
        .navbar-marquee {
            display: none;
        }

        #navbarSupportedContent #waktuWIB {
            min-width: 140px;
        }
    }

    @media (max-width: 575px) {
        .caption {
            display: none !important;
        }
    }

    .btn-detail-pesan {
        background: #ffffff;
        border: 1px solid #007bff;
        padding: 4px 10px;
        font-size: 12px;
        border-radius: 8px;
        color: #007bff;
        transition: 0.25s ease;
    }

    .btn-detail-pesan:hover {
        background: #007bff;
        color: white;
        box-shadow: 0 3px 10px rgba(0, 123, 255, 0.35);
        transform: translateY(-1px);
    }

    .btn-detail-pesan:active {
        background: #005fcc;
        color: white;
        transform: scale(0.97);
    }

    .btn-tutup-dropdown {
        width: 70%;
        background: #ffffff;
        border: 1px solid #007bff;
        color: #007bff;
        padding: 6px 10px;
        border-radius: 10px;
        font-size: 13px;
        transition: .25s ease;
    }

    .btn-tutup-dropdown:hover {
        background: #007bff;
        color: #fff;
        box-shadow: 0 3px 10px rgba(0, 123, 255, 0.35);
        transform: translateY(-1px);
    }

    .btn-tutup-dropdown:active {
        background: #005fcc;
        color: white;
        transform: scale(.97);
    }
</style>

<!-- ===== Ganti bagian collapse navbar-collapse dengan ini ===== -->
<div class="collapse navbar-collapse navbar-modern" id="navbarSupportedContent">
    <ul class="mb-2 navbar-nav ms-auto align-items-center navbar-list mb-lg-0">

        <!-- Judul bergerak (marquee) -->
        <li class="nav-item d-none d-lg-flex me-3 align-items-center">
            <div class="navbar-marquee" aria-hidden="true">
                <div class="marquee-content">
                    {{ $title ?? config('app.name', 'TechNote') }} • Sistem Layanan Instalasi & Perbaikan — STMIK Triguna Dharma
                </div>
            </div>
        </li>

        <!-- Quick Add (ikon +) - di kiri collapse (tidak menggantikan toggle yang sudah ada) -->
        <li class="nav-item dropdown me-2">
            <a class="nav-link" href="#" id="quickAdd" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Tambah cepat">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="quickAdd">

                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dashboard-admin') }}"><i class="bi bi-house"></i> Home</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('penginstalan.create') }}"><i class="bi bi-download"></i> Tambah Instalasi</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('perbaikan.create') }}"><i class="bi bi-tools"></i> Tambah Perbaikan</a></li>
                <li><a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('software.create') }}"><i class="bi bi-hdd-stack"></i> Tambah Software</a></li>
            </ul>
        </li>

        <li class="nav-item dropdown me-2 position-relative">
            <a href="#" class="nav-link position-relative" id="notification-drop"
                data-bs-toggle="dropdown" aria-expanded="false" aria-label="Pesan Masuk">

                <!-- Ikon Pesan -->
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                    <path d="M2 6.5L12 13L22 6.5V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6.5Z"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M2 6L12 12.5L22 6" stroke="currentColor" stroke-width="2" />
                </svg>

                <!-- Badge jumlah pesan -->
                @if($unreadCount > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    {{ $unreadCount }}
                    <span class="visually-hidden">pesan baru</span>
                </span>
                @endif

            </a>

            <div class="p-0 sub-drop dropdown-menu dropdown-menu-end" aria-labelledby="notification-drop">
                <div class="m-0 shadow-none card">
                    <div class="py-3 card-header d-flex justify-content-between bg-primary rounded-top">
                        <h6 class="mb-0 text-white">Pesan Masuk</h6>
                    </div>

                    <div class="p-0 card-body" style="max-height: 330px; overflow-y: auto">
                        @forelse($recentMessages as $msg)
                        @php
                        $nama = htmlspecialchars($msg->nama ?? '-', ENT_QUOTES);
                        $email = htmlspecialchars($msg->email ?? '-', ENT_QUOTES);
                        $pesan = htmlspecialchars($msg->pesan ?? '-', ENT_QUOTES);
                        $tgl = \Carbon\Carbon::parse($msg->created_at)->translatedFormat('d M Y H:i');
                        $namaUser = htmlspecialchars($msg->user->nama ?? 'Guest', ENT_QUOTES);
                        $contactId = $msg->id; // simpan id di variabel PHP
                        @endphp

                        <div class="iq-sub-card px-3 py-2">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-0">{{ $namaUser }}</h6>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($pesan, 40) }}</small><br>
                                    <small class="font-size-12">{{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</small>
                                </div>

                                <!-- Tombol Detail -->
                                <button class="btn-detail-pesan"
                                    onclick="event.stopPropagation(); showDetailPesan('{{ $nama }}','{{ $email }}','{{ $pesan }}','{{ $tgl }}','{{ $namaUser }}', '{{ $contactId }}')">
                                    Detail
                                </button>
                            </div>

                            <div class="p-2 border-top text-center">
                                <button onclick="closeDropdownNotif()" class="btn-tutup-dropdown">Tutup Menu</button>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-muted py-3">Tidak ada pesan baru</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </li>



        <!-- Jam WIB -->
        <li class="nav-item d-flex align-items-center me-3">
            <span id="waktuWIB" class="nav-link text-muted"></span>
        </li>

        <!-- Profil -->
        @if(Auth::check())
        <li class="nav-item dropdown custom-drop d-flex align-items-center">
            @php
            $foto = Auth::user()->foto && file_exists(public_path('foto/' . Auth::user()->foto))
            ? asset('foto/' . Auth::user()->foto)
            : asset('assets/images/default.png');
            @endphp

            <a class="py-0 nav-link d-flex align-items-center" href="#" id="navbarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ $foto }}" class="avatar rounded-circle me-2" alt="profile">
                <div class="caption d-none d-md-block">
                    <h6 class="mb-0 caption-title">{{ Auth::user()->username }}</h6>
                    <p class="mb-0 caption-sub-title text-muted">{{ Auth::user()->role->status ?? '-' }}</p>
                </div>
            </a>

            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-1 my-profile-btn" href="#" data-bs-toggle="modal" data-bs-target="#myProfileModal">
                        <i class="bi bi-person"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-1" href="#" data-bs-toggle="modal" data-bs-target="#accountSettingsModal">
                        <i class="bi bi-gear"></i> Account Settings
                    </a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item d-flex align-items-center gap-1">
                        <i class="bi bi-box-arrow-right"></i> Sign Out
                    </button>
                </form>
            </ul>
        </li>
        @endif

    </ul>
</div>


<script>
    function showDetailPesan(nama, email, pesan, tanggal, namaUser, contactId) {
        document.getElementById('detail-nama').innerText = nama;
        document.getElementById('detail-email').innerText = email;
        document.getElementById('detail-pesan').innerText = pesan;
        document.getElementById('detail-tanggal').innerText = tanggal;
        document.getElementById('detail-user').innerText = namaUser;

        var modal = new bootstrap.Modal(document.getElementById('modalDetailPesan'));
        modal.show();

        // AJAX untuk menandai sudah dibaca
        fetch(`/contact/${contactId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(res => res.json())
            .then(data => {
                if (data.success) {
                    // opsional: kurangi badge count atau sembunyikan badge
                    let badge = document.querySelector('#notification-drop .badge');
                    if (badge) {
                        let count = parseInt(badge.innerText);
                        if (count > 1) {
                            badge.innerText = count - 1;
                        } else {
                            badge.remove();
                        }
                    }
                }
            });
    }
</script>

<!-- ===== JS: jam WIB (sebelum </body>) ===== -->
<script>
    function updateWaktuWIB() {
        const now = new Date();
        const options = {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        };
        const formatter = new Intl.DateTimeFormat('id-ID', options);
        const teks = formatter.format(now).replace(',', ' •');
        const el = document.getElementById('waktuWIB');
        if (el) el.textContent = teks;
    }
    updateWaktuWIB();
    setInterval(updateWaktuWIB, 1000);
</script>