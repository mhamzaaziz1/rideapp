<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideFlow | <?= $title ?? 'Dispatch' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script> <!-- Icons -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php 
        $settingsFile = WRITEPATH . 'settings.json';
        $siteSettings = [];
        if (file_exists($settingsFile)) {
            $siteSettings = json_decode(file_get_contents($settingsFile), true) ?? [];
        }
        $googleMapKey = $siteSettings['google_maps_api_key'] ?? getenv('GOOGLE_MAPS_API_KEY');

        // Current user session info
        $sessFirstName = session('first_name') ?? 'User';
        $sessLastName  = session('last_name') ?? '';
        $sessEmail     = session('email') ?? '';
        $sessRole      = session('role_name') ?? '';
        $sessInitials  = substr($sessFirstName, 0, 1) . substr($sessLastName, 0, 1);
    ?>

    <script>
        // Global Map settings exported for frontend use
        window.APP_MAP_PROVIDER = '<?= $siteSettings['map_provider'] ?? 'osm' ?>';
        window.APP_GOOGLE_MAPS_KEY = '<?= $googleMapKey ?>';

        // Google Maps Autocomplete Initialization
        function initAutocomplete() {
            const inputs = document.querySelectorAll('.addr-autocomplete');
            inputs.forEach(input => {
                new google.maps.places.Autocomplete(input);
            });
            document.dispatchEvent(new Event('google-maps-ready'));
        }
    </script>
    
    <?php if($googleMapKey): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $googleMapKey ?>&libraries=places&callback=initAutocomplete" async defer></script>
    <?php endif; ?>
</head>
<body>
    <div class="app-container">
        <!-- Header Navigation -->
        <header class="app-header">
            <div class="header-left">
                <!-- Logo -->
                <div class="logo-text">Ride<span class="logo-accent">Flow</span></div>

                <!-- Navigation Links — permission-gated -->
                <nav class="nav-menu">
                    <?php if(can('dashboard.dashboard.view')): ?>
                    <a href="<?= base_url('dashboard') ?>" class="nav-item <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                        <i data-lucide="layout-dashboard" class="nav-icon"></i> Dashboard
                    </a>
                    <?php endif; ?>
                    <?php if(can('dispatch.dispatch_board.view')): ?>
                    <a href="<?= base_url('dispatch') ?>" class="nav-item <?= uri_string() == 'dispatch' ? 'active' : '' ?>">
                        <i data-lucide="map" class="nav-icon"></i> Dispatch
                    </a>
                    <?php endif; ?>
                    <?php if(can('dispatch.trips.view')): ?>
                    <a href="<?= base_url('trips') ?>" class="nav-item <?= uri_string() == 'trips' ? 'active' : '' ?>">
                        <i data-lucide="car" class="nav-icon"></i> Trips
                    </a>
                    <?php endif; ?>
                    <?php if(can('fleet.drivers.view')): ?>
                    <a href="<?= base_url('drivers') ?>" class="nav-item <?= uri_string() == 'drivers' ? 'active' : '' ?>">
                        <i data-lucide="users" class="nav-icon"></i> Drivers
                    </a>
                    <?php endif; ?>
                    <?php if(can('customer.customers.view')): ?>
                    <a href="<?= base_url('customers') ?>" class="nav-item <?= uri_string() == 'customers' ? 'active' : '' ?>">
                        <i data-lucide="briefcase" class="nav-icon"></i> Customers
                    </a>
                    <?php endif; ?>
                    <?php if(can('billing.finance.view')): ?>
                    <a href="<?= base_url('finance') ?>" class="nav-item <?= uri_string() == 'finance' ? 'active' : '' ?>">
                        <i data-lucide="dollar-sign" class="nav-icon"></i> Financials
                    </a>
                    <?php endif; ?>
                    <?php if(can('pricing.pricing.view')): ?>
                    <a href="<?= base_url('pricing') ?>" class="nav-item <?= uri_string() == 'pricing' ? 'active' : '' ?>">
                        <i data-lucide="tag" class="nav-icon"></i> Pricing
                    </a>
                    <?php endif; ?>
                    <?php if(can('callcenter.call_logs.view')): ?>
                    <a href="<?= base_url('call-logs') ?>" class="nav-item <?= uri_string() == 'call-logs' ? 'active' : '' ?>">
                        <i data-lucide="phone" class="nav-icon"></i> Call Logs
                    </a>
                    <?php endif; ?>
                    <?php if(can('dispatch.communications.view')): ?>
                    <a href="<?= base_url('admin/communications') ?>" class="nav-item <?= uri_string() == 'admin/communications' ? 'active' : '' ?>">
                        <i data-lucide="message-square" class="nav-icon"></i> Communications
                    </a>
                    <?php endif; ?>
                    <?php if(can('dispatch.disputes.view')): ?>
                    <a href="<?= base_url('admin/disputes') ?>" class="nav-item <?= uri_string() == 'admin/disputes' ? 'active' : '' ?>">
                        <i data-lucide="alert-triangle" class="nav-icon"></i> Disputes
                    </a>
                    <?php endif; ?>
                    <?php if(can('support.chat.view')): ?>
                    <a href="<?= base_url('admin/support') ?>" class="nav-item <?= uri_string() == 'admin/support' ? 'active' : '' ?>">
                        <i data-lucide="help-circle" class="nav-icon"></i> Support
                    </a>
                    <?php endif; ?>
                </nav>
            </div>

            <div class="header-right" style="display:flex; align-items:center; gap:1rem;">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="nav-item btn-icon" style="background:transparent; border:none; cursor:pointer; color:var(--text-secondary); display:flex; align-items:center; justify-content:center; padding:0.5rem;" title="Toggle Theme">
                    <i data-lucide="sun" id="sunIcon" style="display:none;"></i>
                    <i data-lucide="moon" id="moonIcon"></i>
                </button>

                <?php if(can('setting.settings.view')): ?>
                <a href="<?= base_url('settings') ?>" class="nav-item" style="display:flex; align-items:center; color:var(--text-secondary);">
                    <i data-lucide="settings" class="nav-icon"></i>
                </a>
                <?php endif; ?>
                
                <div class="user-dropdown" style="position:relative;">
                    <button id="userMenuBtn" class="nav-avatar" style="width:32px; height:32px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; padding:0;">
                        <span style="font-weight:600; color:white; font-size:0.85rem;"><?= esc($sessInitials) ?></span>
                    </button>
                    
                    <div id="userMenuDropdown" class="dropdown-menu" style="display:none; position:absolute; top:120%; right:0; width:220px; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-md); box-shadow:var(--shadow-lg); z-index:1000; overflow:hidden;">
                        <div style="padding:1rem; border-bottom:1px solid var(--border-color); background:var(--bg-surface-hover);">
                            <div style="font-weight:600; color:var(--text-primary);"><?= esc($sessFirstName . ' ' . $sessLastName) ?></div>
                            <div style="font-size:0.75rem; color:var(--text-secondary);"><?= esc($sessEmail) ?></div>
                            <?php if($sessRole): ?>
                            <div style="margin-top:4px;"><span style="background:rgba(99,102,241,0.15); color:var(--primary); padding:2px 6px; border-radius:4px; font-size:0.7rem; font-weight:600;"><?= esc($sessRole) ?></span></div>
                            <?php endif; ?>
                        </div>

                        <?php if(can('iam.staff.view')): ?>
                        <a href="<?= base_url('staff') ?>" class="dropdown-item" style="display:block; padding:0.6rem 1rem; color:var(--text-primary); text-decoration:none; font-size:0.88rem;">
                            <i data-lucide="users" width="14" style="vertical-align:middle; margin-right:8px;"></i> Staff
                        </a>
                        <?php endif; ?>

                        <?php if(can('iam.roles.view')): ?>
                        <a href="<?= base_url('roles') ?>" class="dropdown-item" style="display:block; padding:0.6rem 1rem; color:var(--text-primary); text-decoration:none; font-size:0.88rem;">
                            <i data-lucide="shield" width="14" style="vertical-align:middle; margin-right:8px;"></i> Roles & Permissions
                        </a>
                        <?php endif; ?>

                        <a href="<?= base_url('settings') ?>" class="dropdown-item" style="display:block; padding:0.6rem 1rem; color:var(--text-primary); text-decoration:none; font-size:0.88rem;">
                            <i data-lucide="settings" width="14" style="vertical-align:middle; margin-right:8px;"></i> Settings
                        </a>
                        <div style="border-top:1px solid var(--border-color);"></div>
                        <a href="<?= base_url('logout') ?>" class="dropdown-item" style="display:block; padding:0.6rem 1rem; color:var(--danger); text-decoration:none; font-size:0.88rem;">
                            <i data-lucide="log-out" width="14" style="vertical-align:middle; margin-right:8px;"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <script>
            document.getElementById('userMenuBtn').addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = document.getElementById('userMenuDropdown');
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            });
            window.addEventListener('click', () => {
                const menu = document.getElementById('userMenuDropdown');
                if(menu) menu.style.display = 'none';
            });
        </script>
        
        <style>
            .dropdown-item:hover { background: var(--bg-surface-hover); }
        </style>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-content">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <script>
        // Initialize Icons
        lucide.createIcons();
        
        // Theme Logic
        const toggleBtn = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');
        const html = document.documentElement;
        
        // Function to update UI based on theme
        function updateThemeUI(theme) {
            if (theme === 'light') {
                html.setAttribute('data-theme', 'light');
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');
                if(sun) sun.style.display = 'none'; 
                if(moon) moon.style.display = 'block';
            } else {
                html.removeAttribute('data-theme');
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');
                if(sun) sun.style.display = 'block';
                if(moon) moon.style.display = 'none';
            }
        }

        // Initial Load
        const savedTheme = localStorage.getItem('theme') || 'dark';
        
        // Event Listener
        toggleBtn.addEventListener('click', () => {
            const isDark = !html.hasAttribute('data-theme');
            if (isDark) {
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');
                if(sun) sun.style.display = 'none';
                if(moon) moon.style.display = 'block';
            } else {
                html.removeAttribute('data-theme');
                localStorage.setItem('theme', 'dark');
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');
                if(sun) sun.style.display = 'block';
                if(moon) moon.style.display = 'none';
            }
        });

        // Apply initial state
        if (savedTheme === 'light') {
            html.setAttribute('data-theme', 'light');
             document.getElementById('sunIcon').style.display = 'none';
             document.getElementById('moonIcon').style.display = 'block';
        } else {
             html.removeAttribute('data-theme');
             document.getElementById('sunIcon').style.display = 'block';
             document.getElementById('moonIcon').style.display = 'none';
        }

    </script>
    <?= $this->renderSection('scripts') ?>
    <?= view('App\Modules\Support\Views\chat_widget') ?>
</body>
</html>
