<nav class="sidebar">
    <div class="sidebar-top">
        <a href="/beranda" class="brand">
            <span class="brand-accent">Cash.</span><span class="brand-muted">Flow</span>
        </a>
    </div>

    <div class="sidebar-links">
        <a href="/beranda" class="sidebar-link {{ request()->is('beranda') ? 'active' : '' }}">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="2" y="2" width="6" height="6" rx="1" fill="currentColor"/>
                    <rect x="12" y="2" width="6" height="6" rx="1" fill="currentColor"/>
                    <rect x="2" y="12" width="6" height="6" rx="1" fill="currentColor"/>
                    <rect x="12" y="12" width="6" height="6" rx="1" fill="currentColor"/>
                </svg>
            </span>
            <span>Beranda</span>
        </a>

        <a href="/saldo" class="sidebar-link {{ request()->is('saldo') ? 'active' : '' }}">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 7H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M4 13H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M7 4H13V16H7V4Z" stroke="currentColor" stroke-width="1.5"/>
                </svg>
            </span>
            <span>Saldo</span>
        </a>

        <a href="/transaksi" class="sidebar-link {{ request()->is('transaksi') ? 'active' : '' }}">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 7H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M5 10H15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M5 13H12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M6 4L9 7L14 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 16L9 13L14 18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span>Transaksi</span>
        </a>

        <a href="/tagihan" class="sidebar-link {{ request()->is('tagihan') ? 'active' : '' }}">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 6H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M4 10H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M4 14H16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M8 2V18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Tagihan</span>
        </a>

        <a href="{{ route('pengeluaran') }}" class="sidebar-link {{ request()->is('pengeluaran') ? 'active' : '' }}">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 4H15V7H5V4Z" fill="currentColor"/>
                    <path d="M5 9H15V12H5V9Z" fill="currentColor"/>
                    <path d="M5 14H15V16H5V14Z" fill="currentColor"/>
                </svg>
            </span>
            <span>Pengeluaran</span>
        </a>

        <a href="/target" class="sidebar-link {{ request()->is('target') ? 'active' : '' }}">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M10 5V10L13.5 12.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Target</span>
        </a>

        <a href="#" class="sidebar-link">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 4.5C11.1046 4.5 12 5.39543 12 6.5C12 7.60457 11.1046 8.5 10 8.5C8.89543 8.5 8 7.60457 8 6.5C8 5.39543 8.89543 4.5 10 4.5Z" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M3 11.5H17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M6 15.5H14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Setelan</span>
        </a>
    </div>

    <div class="sidebar-bottom">
        <a href="{{ route('logout') }}" class="sidebar-link logout">
            <span class="icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 5L3 10L7 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M3 10H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M13 5H16C16.5523 5 17 5.44772 17 6V14C17 14.5523 16.5523 15 16 15H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </span>
            <span>Keluar</span>
        </a>

        <div class="profile-card">
            <div class="avatar">CP</div>
            <div class="profile-info">
                <div class="profile-name">Christian Prawira G</div>
                <div class="profile-subtitle">View profile</div>
            </div>
            <button class="profile-more" aria-label="More options">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<style>
    .sidebar {
        flex: 0 0 220px;
        min-width: 220px;
        max-width: 220px;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        background: linear-gradient(180deg, #111316 0%, #111922 100%);
        color: #E4E4E4;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 24px 14px;
        overflow: hidden;
    }

    .sidebar::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -40%;
        width: 180%;
        height: 200%;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.18), transparent 22%),
                    linear-gradient(140deg, transparent 0%, rgba(255,255,255,0.08) 48%, transparent 100%);
        transform: rotate(25deg);
        opacity: 0.4;
        animation: navbar-glow 6s linear infinite;
        pointer-events: none;
    }

    @keyframes navbar-glow {
        0% {
            transform: translateX(-25%) rotate(25deg);
            opacity: 0.3;
        }
        50% {
            transform: translateX(25%) rotate(25deg);
            opacity: 0.5;
        }
        100% {
            transform: translateX(-25%) rotate(25deg);
            opacity: 0.3;
        }
    }

    .sidebar-top {
        margin-bottom: 24px;
        position: relative;
        z-index: 1;
    }

    .brand {
        display: inline-flex;
        align-items: center;
        font-size: 22px;
        font-weight: 700;
        text-decoration: none;
        color: inherit;
    }

    .brand-accent {
        color: #299D91;
    }

    .brand-muted {
        color: #808F8E;
    }

    .sidebar-links {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sidebar-link {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 11px 12px;
        border-radius: 14px;
        color: #D1D5DB;
        text-decoration: none;
        font-size: 0.95rem;
        transition: background 0.2s ease, color 0.2s ease;
    }

    .sidebar-link .icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        color: #A1A8AE;
    }

    .sidebar-link:hover,
    .sidebar-link.active {
        background: #1F4F46;
        color: #F8FAFC;
    }

    .sidebar-link.active .icon {
        background: rgba(41, 157, 145, 0.18);
        color: #299D91;
    }

    .sidebar-link.logout {
        margin-top: 12px;
        background: rgba(255, 255, 255, 0.04);
        color: #D1D5DB;
    }

    .sidebar-bottom {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .profile-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px;
        background: #14181B;
        border-radius: 18px;
    }

    .avatar {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        background: #1E9A84;
        color: white;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .profile-info {
        flex: 1;
        min-width: 0;
    }

    .profile-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #F8FAFC;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .profile-subtitle {
        font-size: 0.82rem;
        color: #8A9299;
        margin-top: 2px;
    }

    .profile-more {
        width: 28px;
        height: 28px;
        border: none;
        background: transparent;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 3px;
        cursor: pointer;
        padding: 0;
    }

    .profile-more span {
        display: block;
        width: 4px;
        height: 4px;
        border-radius: 50%;
        background: #8A9299;
    }
</style>