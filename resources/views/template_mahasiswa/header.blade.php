<div class="brand" aria-hidden="false">
    <div><img src="{{ asset('assets/images/icon.png') }}" style="height: 37px;"></div>
    <h1 class="brand-title">TechNote App</h1>
</div>

<nav class="center-nav" aria-label="Menu tengah">
    <ul>
        <li>
            <a href="{{ route('dashboard-mahasiswa') }}" class="{{ ($menu ?? '') === 'dashboard' ? 'active' : '' }}">
                Home
            </a>
        </li>
        <li>
            <a href="{{ route('contact') }}" class="{{ ($menu ?? '') === 'contact' ? 'active' : '' }}">
                Contact
            </a>
        </li>
        <li>
            <a href="{{ route('about') }}" class="{{ ($menu ?? '') === 'about' ? 'active' : '' }}">
                About
            </a>
        </li>
        <li>
            <a href="{{ route('rank.mhs') }}" class="{{ ($menu ?? '') === 'rank' ? 'active' : '' }}">
                Rank
            </a>
        </li>
    </ul>
</nav>

<div class="actions">
    @php
    $foto = Auth::user()->foto && file_exists(public_path('foto/' . Auth::user()->foto))
    ? asset('foto/' . Auth::user()->foto)
    : asset('assets/images/default.png');
    @endphp

    <img src="{{ $foto }}" alt="Profil" class="logo-profile" onclick="toggleDropdown()">

    <div class="dropdown-menu" id="logoutMenu">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" aria-label="Sign out">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:8px;">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                    <polyline points="16 17 21 12 16 7" />
                    <line x1="21" y1="12" x2="9" y2="12" />
                </svg>
                Sign out
            </button>
        </form>
    </div>
</div>
<style>
    .actions.profile-dropdown {
        position: relative;
        display: inline-block;
    }

    .logo-profile {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        cursor: pointer;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        top: 50px;
        /* tepat di bawah foto */
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 5px 0;
        min-width: 140px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        z-index: 100;
        transition: opacity 0.2s ease;
        opacity: 0;
        pointer-events: none;
    }

    .dropdown-menu.show {
        display: block;
        opacity: 1;
        pointer-events: auto;
    }

    .dropdown-menu button {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 10px 15px;
        background: transparent;
        border: none;
        color: #fff;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-menu button:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .logout-form {
        margin: 0;
    }
</style>

<script>
    function toggleDropdown() {
        const menu = document.getElementById('logoutMenu');
        menu.classList.toggle('show');
    }

    // Klik di luar dropdown untuk menutup
    window.addEventListener('click', function(e) {
        const dropdown = document.getElementById('logoutMenu');
        const pic = document.querySelector('.logo-profile');
        if (!pic.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
</script>