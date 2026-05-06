<aside class="sidebar" id="mainSidebar">
  
      {{-- Brand / Logo --}}
   <!-- <div class="sidebar-brand">
        <div class="brand-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 17L12 22L22 17" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M2 12L12 17L22 12" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <span>AdminPanel</span>
    </div> -->

    {{-- Toggle Button --}}
    <div class="sidebar-header">
    
        <button class="menu-toggle" id="sidebarToggle" title="Toggle sidebar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    <div class="sidebar-divider"></div>

    {{-- Navigation --}}
    <p class="nav-section-label">Menu</p>
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}" data-tooltip="Dashboard">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M3 12L5 10M5 10L12 3L19 10M5 10V20C5 20.5523 5.44772 21 6 21H9M19 10L21 12M19 10V20C19 20.5523 18.5523 21 18 21H15M9 21V14C9 13.4477 9.44772 13 10 13H14C14.5523 13 15 13.4477 15 14V21M9 21H15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('ledenpagina') ? 'active' : '' }}">
                <a href="{{ route('ledenpagina') }}" data-tooltip="Leden">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17 21V19C17 17.9391 16.5786 17.0217 15.8284 16.2716C15.0783 15.5214 14.1609 15.1 13.1 15.1H6.9C5.83913 15.1 4.92174 15.5214 4.17157 16.2716C3.42143 17.0217 3 17.9391 3 19V21M16 3.12999C17.4273 3.51199 18.6738 4.3814 19.5401 5.59922C20.4063 6.81704 20.8385 8.2933 20.767 9.78918C20.6955 11.285 20.1245 12.7031 19.1466 13.8122C18.1687 14.9213 16.8437 15.6517 15.39 15.91M15 7C15 9.20914 13.2091 11 11 11C8.79086 11 7 9.20914 7 7C7 4.79086 8.79086 3 11 3C13.2091 3 15 4.79086 15 7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="nav-text">Leden</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('betalingPagina') ? 'active' : '' }}">
                <a href="{{ route('betalingPagina') }}" data-tooltip="Betaling">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="5" width="20" height="14" rx="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M2 10H22" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="nav-text">Betaling</span>
                </a>
            </li>
            <li class="nav-item {{ request()->routeIs('rapport') ? 'active' : '' }}">
                <a href="#" data-tooltip="Rapport">
                    <span class="nav-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 20V10M12 20V4M6 20V14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="nav-text">Rapport</span>
                </a>
            </li>
        </ul>
    </nav>

    {{-- Footer --}}
    <div class="sidebar-footer">
        <div class="footer-avatar">A</div>
        <span>Admin</span>
    </div>
</aside>
