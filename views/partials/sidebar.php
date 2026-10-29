<aside class="app-sidebar" id="app-sidebar">
    <div class="sidebar-header">
        <a href="/dashboard" class="sidebar-logo-link">
            <img src="/assets/images/logo.svg" alt="Dream Career IT" class="sidebar-logo">
        </a>
        <button type="button" class="sidebar-collapse-btn" id="sidebar-toggle-btn" onclick="App.toggleSidebar()" title="Toggle Sidebar">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <div class="sidebar-nav-scroller">
        <div class="nav-group-label">MAIN CONTROL</div>
        <ul class="sidebar-menu">
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/dashboard') ? 'active' : '' ?>">
                <a href="/dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span class="menu-label">Instrument Panel</span>
                    <span class="badge-live">LIVE</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/services') ? 'active' : '' ?>">
                <a href="/services">
                    <i class="bi bi-briefcase-fill"></i>
                    <span class="menu-label">Services Catalog</span>
                </a>
            </li>
        </ul>

        <div class="nav-group-label">CAMPAIGN & EMAIL ENGINE</div>
        <ul class="sidebar-menu">
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/templates') ? 'active' : '' ?>">
                <a href="/templates">
                    <i class="bi bi-layout-text-window-reverse"></i>
                    <span class="menu-label">Email Templates</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/campaigns/send') ? 'active' : '' ?>">
                <a href="/campaigns/send">
                    <i class="bi bi-send-fill"></i>
                    <span class="menu-label">Send Email Campaign</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/campaigns/logs') ? 'active' : '' ?>">
                <a href="/campaigns/logs">
                    <i class="bi bi-clock-history"></i>
                    <span class="menu-label">Queue & Delivery Logs</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/contacts') ? 'active' : '' ?>">
                <a href="/contacts">
                    <i class="bi bi-person-lines-fill"></i>
                    <span class="menu-label">Contacts & Clients</span>
                </a>
            </li>
        </ul>

        <div class="nav-group-label">COMMUNICATION & AI</div>
        <ul class="sidebar-menu">
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/messages') ? 'active' : '' ?>">
                <a href="/messages">
                    <i class="bi bi-chat-dots-fill"></i>
                    <span class="menu-label">Unified Message Center</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/ai-assistant') ? 'active' : '' ?>">
                <a href="/ai-assistant">
                    <i class="bi bi-robot"></i>
                    <span class="menu-label">AI Assistant</span>
                    <span class="badge-ai">AI</span>
                </a>
            </li>
        </ul>

        <div class="nav-group-label">PROJECTS & OPERATIONS</div>
        <ul class="sidebar-menu">
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/projects') ? 'active' : '' ?>">
                <a href="/projects">
                    <i class="bi bi-kanban"></i>
                    <span class="menu-label">Projects & Tasks</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/tickets') ? 'active' : '' ?>">
                <a href="/tickets">
                    <i class="bi bi-headset"></i>
                    <span class="menu-label">Support Tickets</span>
                </a>
            </li>
        </ul>

        <div class="nav-group-label">SYSTEM & SECURITY</div>
        <ul class="sidebar-menu">
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/settings') ? 'active' : '' ?>">
                <a href="/settings">
                    <i class="bi bi-sliders"></i>
                    <span class="menu-label">Settings & Integrations</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/users') ? 'active' : '' ?>">
                <a href="/users">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span class="menu-label">User Management & RBAC</span>
                </a>
            </li>
            <li class="menu-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/audit-logs') ? 'active' : '' ?>">
                <a href="/audit-logs">
                    <i class="bi bi-shield-check"></i>
                    <span class="menu-label">Activity & Audit Logs</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Sidebar Profile Footer -->
    <div class="sidebar-footer">
        <div class="user-avatar-circle">
            <?= strtoupper(substr(\App\Core\Session::get('user_name', 'U'), 0, 1)) ?>
        </div>
        <div class="user-info-text">
            <div class="user-name"><?= \App\Core\View::e(\App\Core\Session::get('user_name', 'User')) ?></div>
            <div class="user-role"><?= \App\Core\View::e(\App\Core\Session::get('user_role_name', 'Role')) ?></div>
        </div>
        <a href="/logout" class="sidebar-logout-btn" title="Sign Out">
            <i class="bi bi-box-arrow-right"></i>
        </a>
    </div>
</aside>
