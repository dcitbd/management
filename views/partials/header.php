<header class="app-topbar">
    <div class="topbar-left">
        <button type="button" class="mobile-menu-btn" onclick="App.toggleMobileSidebar()">
            <i class="bi bi-list"></i>
        </button>
        <div class="search-input-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text" placeholder="Search contacts, campaigns, logs, tickets..." class="global-search-input" id="globalSearch">
        </div>
    </div>

    <div class="topbar-right">
        <!-- Date range indicator -->
        <div class="date-badge">
            <i class="bi bi-calendar3"></i>
            <span><?= date('M d, Y') ?></span>
        </div>

        <!-- Theme Toggle -->
        <button type="button" class="topbar-action-btn" onclick="App.toggleTheme()" title="Switch Light/Dark Theme">
            <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
            <i class="bi bi-sun-fill theme-icon-light"></i>
        </button>

        <!-- Notifications Dropdown -->
        <div class="notifications-wrapper">
            <button type="button" class="topbar-action-btn" onclick="App.toggleNotifications()">
                <i class="bi bi-bell-fill"></i>
                <span class="notification-dot"></span>
            </button>
        </div>

        <!-- Shop Link Quick Button -->
        <a href="https://dreamcartbd.com" target="_blank" rel="noopener" class="topbar-shop-btn">
            <i class="bi bi-cart3"></i>
            <span>Shop</span>
        </a>

        <!-- User Profile Dropdown -->
        <div class="user-dropdown">
            <div class="user-pill" onclick="App.toggleUserMenu()">
                <div class="user-avatar-small">
                    <?= strtoupper(substr(\App\Core\Session::get('user_name', 'U'), 0, 1)) ?>
                </div>
                <span class="user-pill-name"><?= \App\Core\View::e(\App\Core\Session::get('user_name', 'User')) ?></span>
                <i class="bi bi-chevron-down"></i>
            </div>
            <div class="user-menu-dropdown" id="userMenuDropdown">
                <div class="user-menu-header">
                    <strong><?= \App\Core\View::e(\App\Core\Session::get('user_name', 'User')) ?></strong>
                    <small><?= \App\Core\View::e(\App\Core\Session::get('user_email', '')) ?></small>
                </div>
                <hr>
                <a href="/profile"><i class="bi bi-person"></i> Account Profile</a>
                <a href="/settings"><i class="bi bi-gear"></i> Preferences</a>
                <hr>
                <a href="/logout" class="text-danger"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
            </div>
        </div>
    </div>
</header>
