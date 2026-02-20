<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RideFlow | <?= $title ?? 'Dispatch' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <script src="https://unpkg.com/lucide@latest"></script> <!-- Icons -->
</head>
<body>
    <div class="app-container">
        <!-- Header Navigation -->
        <header class="app-header">
            <div class="header-left">
                <!-- Logo -->
                <div class="logo-text">Ride<span class="logo-accent">Flow</span></div>

                <!-- Navigation Links -->
                <nav class="nav-menu">
                    <a href="<?= base_url('dashboard') ?>" class="nav-item <?= uri_string() == 'dashboard' ? 'active' : '' ?>">
                        <i data-lucide="layout-dashboard" class="nav-icon"></i> Dashboard
                    </a>
                    <a href="<?= base_url('dispatch') ?>" class="nav-item <?= uri_string() == 'dispatch' ? 'active' : '' ?>">
                        <i data-lucide="map" class="nav-icon"></i> Dispatch
                    </a>
                    <a href="<?= base_url('trips') ?>" class="nav-item <?= uri_string() == 'trips' ? 'active' : '' ?>">
                        <i data-lucide="car" class="nav-icon"></i> Trips
                    </a>
                    <a href="<?= base_url('drivers') ?>" class="nav-item <?= uri_string() == 'drivers' ? 'active' : '' ?>">
                        <i data-lucide="users" class="nav-icon"></i> Drivers
                    </a>
                    <a href="<?= base_url('customers') ?>" class="nav-item <?= uri_string() == 'customers' ? 'active' : '' ?>">
                        <i data-lucide="briefcase" class="nav-icon"></i> Customers
                    </a>
                    <a href="<?= base_url('finance') ?>" class="nav-item <?= uri_string() == 'finance' ? 'active' : '' ?>">
                        <i data-lucide="dollar-sign" class="nav-icon"></i> Financials
                    </a>
                    <a href="<?= base_url('pricing') ?>" class="nav-item <?= uri_string() == 'pricing' ? 'active' : '' ?>">
                        <i data-lucide="tag" class="nav-icon"></i> Pricing
                    </a>
                    <a href="<?= base_url('call-logs') ?>" class="nav-item <?= uri_string() == 'call-logs' ? 'active' : '' ?>">
                        <i data-lucide="phone" class="nav-icon"></i> Call Logs
                    </a>
                    <a href="<?= base_url('admin/disputes') ?>" class="nav-item <?= uri_string() == 'admin/disputes' ? 'active' : '' ?>">
                        <i data-lucide="alert-triangle" class="nav-icon"></i> Disputes
                    </a>
                </nav>
            </div>

            <div class="header-right" style="display:flex; align-items:center; gap:1rem;">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="nav-item btn-icon" style="background:transparent; border:none; cursor:pointer; color:var(--text-secondary); display:flex; align-items:center; justify-content:center; padding:0.5rem;" title="Toggle Theme">
                    <!-- Sun Icon (Show in Dark Mode) -->
                    <i data-lucide="sun" id="sunIcon" style="display:none;"></i>
                    <!-- Moon Icon (Show in Light Mode) -->
                    <i data-lucide="moon" id="moonIcon"></i>
                </button>

                <a href="<?= base_url('settings') ?>" class="nav-item" style="display:flex; align-items:center; color:var(--text-secondary);">
                    <i data-lucide="settings" class="nav-icon"></i>
                </a>
                
                <div class="user-dropdown" style="position:relative;">
                    <button id="userMenuBtn" class="nav-avatar" style="width:32px; height:32px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; border:none; cursor:pointer; padding:0;">
                        <span style="font-weight:600; color:white; font-size:0.85rem;">AD</span>
                    </button>
                    
                    <!-- Dropdown Content -->
                    <div id="userMenuDropdown" class="dropdown-menu" style="display:none; position:absolute; top:120%; right:0; width:200px; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-md); box-shadow:var(--shadow-lg); z-index:1000; overflow:hidden;">
                        <div style="padding:1rem; border-bottom:1px solid var(--border-color); background:var(--bg-surface-hover);">
                            <div style="font-weight:600; color:var(--text-primary);">Admin User</div>
                            <div style="font-size:0.75rem; color:var(--text-secondary);">admin@rideflow.app</div>
                        </div>
                        <a href="<?= base_url('settings') ?>" class="dropdown-item" style="display:block; padding:0.75rem 1rem; color:var(--text-primary); text-decoration:none; transition:background 0.1s; font-size:0.9rem;">
                            <i data-lucide="settings" width="14" style="vertical-align:middle; margin-right:8px;"></i> Settings
                        </a>
                        <div style="border-top:1px solid var(--border-color);"></div>
                        <a href="<?= base_url('logout') ?>" class="dropdown-item" style="display:block; padding:0.75rem 1rem; color:var(--danger); text-decoration:none; transition:background 0.1s; font-size:0.9rem;">
                            <i data-lucide="log-out" width="14" style="vertical-align:middle; margin-right:8px;"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <script>
            // Simple Dropdown Toggle
            document.getElementById('userMenuBtn').addEventListener('click', (e) => {
                e.stopPropagation();
                const menu = document.getElementById('userMenuDropdown');
                menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
            });
            
            // Close on outside click
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
            // Because Lucide replaces <i> with <svg>, we need to target the SVGs if they exist, 
            // OR we just rely on parent classes if we structured it that way.
            // But getting references to the SVGs after Lucide runs is tricky if we don't re-query.
            // BETTER WAY: Use classes on the button to control visibility via CSS.
            
            if (theme === 'light') {
                html.setAttribute('data-theme', 'light');
                // We need to hide Moon, Show Sun
                // Since Lucide replaces the elements, direct style manipulation on the IDs might fail if IDs are gone.
                // Lucide copies IDs to the SVG. So getElementById SHOULD work.
                
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');
                if(sun) sun.style.display = 'none'; // Wait, usually Light Mode = Sun is ON? No, Light Mode = Switch to Dark (Moon)
                if(moon) moon.style.display = 'block'; 
                
                // Let's standardise: 
                // Dark Mode Default -> Show Sun (to toggle light) ? Or Show Moon (symbolizing night)?
                // Let's do: Dark Mode -> Show Sun. Light Mode -> Show Moon.
                
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
        
        // Determine initial visibility BEFORE Lucide runs to avoid flash
        // Actually Lucide runs fast.
        
        // Event Listener
        toggleBtn.addEventListener('click', () => {
            const isDark = !html.hasAttribute('data-theme');
            if (isDark) {
                // Switch to Light
                html.setAttribute('data-theme', 'light');
                localStorage.setItem('theme', 'light');
                
                // Toggle Icons
                const sun = document.getElementById('sunIcon');
                const moon = document.getElementById('moonIcon');
                if(sun) sun.style.display = 'none';
                if(moon) moon.style.display = 'block';
            } else {
                // Switch to Dark
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
            // Hide sun, show moon
            // We set inline styles on formatting above, now we correct them
             document.getElementById('sunIcon').style.display = 'none';
             document.getElementById('moonIcon').style.display = 'block';
        } else {
             html.removeAttribute('data-theme');
             document.getElementById('sunIcon').style.display = 'block';
             document.getElementById('moonIcon').style.display = 'none';
        }

    </script>
    <script>
        // Google Maps Autocomplete Initialization
        function initAutocomplete() {
            const inputs = document.querySelectorAll('.addr-autocomplete');
            inputs.forEach(input => {
                new google.maps.places.Autocomplete(input);
            });
        }
    </script>
    <?php if(getenv('GOOGLE_MAPS_API_KEY')): ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= getenv('GOOGLE_MAPS_API_KEY') ?>&libraries=places&callback=initAutocomplete" async defer></script>
    <?php endif; ?>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>
