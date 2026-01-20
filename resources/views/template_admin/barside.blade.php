<ul class="navbar-nav iq-main-menu" id="sidebar-menu">
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Home</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'dashboard' ? 'active' : '' }}" aria-current="page" href="{{ route('dashboard-admin') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z" fill="currentColor"></path>
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Dashboard</span>
        </a>
    </li>

    <li>
        <hr class="hr-horizontal">
    </li>
    <li class="nav-item static-item">
        <a class="nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Users</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ in_array($menu ?? '', ['pengguna', 'role']) ? 'active' : '' }}" href="{{ route('pengguna.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M12 12C15.3137 12 18 9.31371 18 6C18 2.68629 15.3137 0 12 0C8.68629 0 6 2.68629 6 6C6 9.31371 8.68629 12 12 12Z" fill="currentColor"></path>
                    <path d="M2 22C2 17.5817 6.02944 14 12 14C17.9706 14 22 17.5817 22 22H2Z" fill="currentColor"></path>
                </svg>
            </i>
            <span class="item-name">Users</span>
        </a>
    </li>

    <li>
        <hr class="hr-horizontal">
    </li>
    <li class="nav-item static-item" style="margin-bottom:0;">
        <a class=" nav-link static-item disabled" href="#" tabindex="-1">
            <span class="default-icon">Layanan</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'software' ? 'active' : '' }}" aria-current="page" href="{{ route('software.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <rect x="2" y="4" width="20" height="12" rx="1.5" opacity="0.4" fill="currentColor" />
                    <path d="M2 18h20v1.5c0 .828-.672 1.5-1.5 1.5h-17C2.672 21 2 20.328 2 19.5V18z" fill="currentColor" />
                    <path d="M9.5 10.5L8 12l1.5 1.5M14.5 10.5L16 12l-1.5 1.5" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </i>
            <span class="item-name">Software</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'penginstalan' ? 'active' : '' }}" aria-current="page" href="{{ route('penginstalan.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <rect x="3" y="4" width="18" height="12" rx="1.5" opacity="0.4" fill="currentColor" />
                    <path d="M8 12l4 4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 8v8" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M3 18h18v1.5c0 .828-.672 1.5-1.5 1.5h-15C3.672 21 3 20.328 3 19.5V18z" fill="currentColor" />
                </svg>
            </i>
            <span class="item-name">Instalasi</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'perbaikan' ? 'active' : '' }}" aria-current="page" href="{{ route('perbaikan.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M21 16v-6a1 1 0 0 0-.553-.895l-8-4.5a1 1 0 0 0-.894 0l-8 4.5A1 1 0 0 0 3 10v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2z" fill="currentColor" />
                    <path d="M12 2v6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M3.5 9.5L12 14l8.5-4.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M12 14v6" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </i>
            <span class="item-name">Perbaikan</span>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'rekap' ? 'active' : '' }}" aria-current="page" href="{{ route('rekap.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M7 2h6l2 2h3v17a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" fill="currentColor" />
                    <path d="M12 7v2" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8.5 11h7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8.5 14h7" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </i>
            <span class="item-name">Rekap</span>
        </a>
    </li>
    <li>
        <hr class="hr-horizontal">
    </li>

    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'logLogin' ? 'active' : '' }}" aria-current="page" href="{{ route('logLogin.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M3 4h18a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1z" fill="currentColor" />
                    <path d="M3 9h18" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7 13h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7 17h10" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </i>
            <span class="item-name">Log Login</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ ($menu ?? '') == 'logAktif' ? 'active' : '' }}" aria-current="page" href="{{ route('logAktif.index') }}">
            <i class="icon">
                <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-20">
                    <path opacity="0.4" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" fill="currentColor" />
                    <path d="M12 6v6l4.25 2.52" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </i>
            <span class="item-name">Log aktivitas</span>
        </a>
    </li>


</ul>