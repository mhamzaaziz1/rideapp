<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Dashboard Specific Styles matching the Mockup */
    .dashboard-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        background-color: var(--bg-body);
    }
    

    /* Main Grid */
    .dashboard-grid {
        flex: 1;
        display: grid;
        grid-template-columns: 260px 280px 1fr 320px 240px; /* 5 Columns */
        gap: 1rem;
        padding: 1rem;
        overflow: hidden; /* Individual cols scroll */
    }



    .route-map-placeholder {
        width: 100%;
        height: 100%;
        background: url('https://upload.wikimedia.org/wikipedia/commons/e/ec/World_map_blank_without_borders.svg') no-repeat center center;
        background-size: cover;
        opacity: 0.5;
        position: relative;
    }

    /* Form Styles */
    .form-group { margin-bottom: 1rem; }
    .form-label { display: block; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .form-input { width: 100%; background: var(--bg-body); border: 1px solid var(--border-color); padding: 0.5rem; border-radius: 6px; color: var(--text-primary); }
    
    /* Trip Details */
    .trip-price-box {
        background: rgba(99, 102, 241, 0.1);
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
    }
    .price-lg { font-size: 1.25rem; font-weight: 700; color: var(--primary-hover); }

</style>

<div class="dashboard-container">


    <div class="dashboard-grid">
        
        <!-- COL 1: Live Status -->
        <div style="display:flex; flex-direction:column; gap:1rem; overflow:hidden;">
            <!-- Live Panel -->
            <div class="col-panel" style="flex:1">
                <div class="panel-header"><i data-lucide="navigation" width="16"></i> Live <span class="badge" style="margin-left:auto"><?= count($activeTrips) ?></span></div>
                <div class="panel-body" style="overflow-y:auto;">
                     <?php if(empty($activeTrips)): ?>
                         <div style="font-size:0.8rem; color:var(--text-secondary); text-align:center; padding:1rem;">
                             No active trips.
                         </div>
                     <?php else: ?>
                         <?php foreach($activeTrips as $t): ?>
                             <div onclick="selectTrip(this)" 
                                  data-trip='<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>'
                                  style="cursor:pointer; font-size:0.85rem; margin-bottom:1rem; border-bottom:1px dashed var(--border-color); padding-bottom:0.5rem; transition:background 0.2s;"
                                  onmouseover="this.style.background='var(--bg-surface-hover)'"
                                  onmouseout="this.style.background='transparent'">
                                
                                <div style="display:flex; justify-content:space-between;">
                                    <div style="color:var(--info); font-weight:600;">#<?= esc($t->trip_number) ?></div>
                                    <?php
                                        $statusColors = [
                                            'pending' => 'var(--warning)',
                                            'dispatching' => 'var(--info)',
                                            'active' => 'var(--success)',
                                            'completed' => 'var(--text-secondary)',
                                            'cancelled' => 'var(--danger)'
                                        ];
                                        $color = $statusColors[$t->status] ?? 'var(--text-secondary)';
                                    ?>
                                    <span style="font-size:0.7rem; border:1px solid <?= $color ?>; color:<?= $color ?>; padding:2px 6px; border-radius:4px; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;"><?= esc($t->status) ?></span>
                                </div>
                                <div style="font-weight:600; font-size:0.9rem; margin:2px 0;">
                                    <?= esc(($t->c_first ?? 'Guest') . ' ' . ($t->c_last ?? '')) ?>
                                </div>
                                <div style="color:var(--text-secondary); font-size:0.75rem;">
                                    <?= esc($t->vehicle_type) ?> • $<?= number_format($t->fare_amount, 2) ?>
                                </div>
                                <div style="color:var(--text-secondary); font-size:0.75rem; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    <i data-lucide="map-pin" width="10"></i> <?= esc($t->pickup_address) ?>
                                </div>
                             </div>
                         <?php endforeach; ?>
                     <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- COL 2: Customer & Driver -->
        <div class="col-panel">
            <div class="panel-header"><i data-lucide="users" width="16"></i> Trip Participants</div>
            <div class="panel-body" id="customer-panel" style="display:flex; flex-direction:column; gap:0;">
                
                <!-- Customer Section -->
                <div style="padding-bottom:1rem; border-bottom:2px solid var(--border-color);">
                    <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); margin-bottom:0.75rem; font-weight:700;">Customer</div>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem;">
                        <div id="cust-initials" style="width:40px; height:40px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:0.9rem;">--</div>
                        <div style="flex:1;">
                            <div style="font-weight:600; font-size:0.9rem;"><span id="cust-name">No Selection</span></div>
                            <div style="font-size:0.75rem; color:var(--text-secondary);" id="cust-phone">--</div>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem; font-size:0.8rem;">
                        <div style="display:flex; gap:6px; color:var(--text-secondary);"><i data-lucide="mail" width="12"></i> <span id="cust-email">--</span></div>
                        <div style="display:flex; gap:6px;"><i data-lucide="history" width="12"></i> <span id="cust-trips">0</span> trips</div>
                    </div>
                </div>

                <!-- Driver Section -->
                <div style="padding-top:1rem;">
                    <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); margin-bottom:0.75rem; font-weight:700;">Driver</div>
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:1rem;">
                        <div id="driver-initials" style="width:40px; height:40px; background:var(--success); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:0.9rem;">--</div>
                        <div style="flex:1;">
                            <div style="font-weight:600; font-size:0.9rem;"><span id="driver-name">Not Assigned</span></div>
                            <div style="font-size:0.75rem; color:var(--text-secondary);" id="driver-phone">--</div>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:0.5rem; font-size:0.8rem;">
                        <div style="display:flex; gap:6px; color:var(--text-secondary);"><i data-lucide="car" width="12"></i> <span id="driver-vehicle">--</span></div>
                        <div style="display:flex; gap:6px; color:var(--text-secondary);"><i data-lucide="credit-card" width="12"></i> <span id="driver-plate">--</span></div>
                        <div style="display:flex; gap:6px;"><i data-lucide="star" width="12" style="color:var(--warning);"></i> <span id="driver-rating">--</span></div>
                    </div>
                </div>

                <div style="margin-top:1rem; padding-top:1rem; border-top:1px dashed var(--border-color); font-size:0.75rem; color:var(--text-secondary); font-style:italic; text-align:center;" id="selection-hint">
                    Select a trip to view details
                </div>
            </div>
        </div>

        <!-- COL 3: Trip Details (The Core Form) -->
        <div class="col-panel">
            <div class="panel-header"><i data-lucide="file-text" width="16"></i> Trip Details</div>
            <div class="panel-body" style="display:flex; flex-direction:column;">
                
                <div class="form-group">
                    <label class="form-label">Pickup</label>
                    <div style="position:relative">
                        <i data-lucide="map-pin" style="position:absolute; left:8px; top:8px; width:16px; color:var(--success)"></i>
                        <input type="text" id="input-pickup" class="form-input" placeholder="Pickup Address" style="padding-left:30px">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Dropoff</label>
                    <div style="position:relative">
                        <i data-lucide="map-pin" style="position:absolute; left:8px; top:8px; width:16px; color:var(--danger)"></i>
                        <input type="text" id="input-dropoff" class="form-input" placeholder="Dropoff Address" style="padding-left:30px">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" id="input-date" class="form-input" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Time</label>
                        <input type="text" id="input-time" class="form-input" value="<?= date('H:i') ?>">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Vehicle</label>
                        <select id="input-vehicle" class="form-input">
                            <option value="standard">Standard</option>
                            <option value="suv">SUV</option>
                            <option value="luxury">Luxury</option>
                            <option value="van">Van</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Passengers</label>
                        <input type="number" id="input-passengers" class="form-input" value="1">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment</label>
                    <div style="display:flex; align-items:center; gap:8px; background:var(--bg-body); border:1px solid var(--border-color); padding:0.5rem; border-radius:6px;">
                        <i data-lucide="credit-card" width="16"></i> Card <i data-lucide="chevron-down" width="14" style="margin-left:auto"></i>
                    </div>
                </div>

                <div class="form-group" style="flex:1">
                    <label class="form-label">Notes</label>
                    <textarea id="input-notes" class="form-input" style="height:80px; resize:none;" placeholder="Notes..."></textarea>
                </div>

                <div class="trip-price-box">
                    <div>
                        <div style="font-size:0.75rem; color:var(--text-secondary);"><span id="lbl-dist">--</span> mi | ~<span id="lbl-dur">--</span> min</div>
                    </div>
                    <div class="price-lg" id="lbl-price">$0.00</div>
                </div>

                <button class="btn btn-primary" id="btn-dispatch" style="width:100%; margin-top:1rem; padding:0.75rem;">
                    <i data-lucide="send" width="16" style="margin-right:8px;"></i> Send to Drivers
                </button>

            </div>
        </div>

        <!-- COL 4: Route Map -->
        <div class="col-panel">
            <div class="panel-header">
                <i data-lucide="map" width="16"></i> Route Map
                <button id="btn-traffic" class="btn btn-primary" style="padding:2px 8px; font-size:0.7rem; margin-left:auto;"><i data-lucide="traffic-cone" width="12"></i> Traffic</button>
            </div>
            <div class="panel-body" style="padding:0;">
                
                <?php if (!isset($siteSettings)) {
                    $settingsFile = WRITEPATH . 'settings.json';
                    $siteSettings = [];
                    if (file_exists($settingsFile)) {
                        $siteSettings = json_decode(file_get_contents($settingsFile), true) ?? [];
                    }
                }
                $mapProvider = $siteSettings['map_provider'] ?? 'osm';
                ?>
                
                <?php if ($mapProvider === 'osm'): ?>
                    <!-- Leaflet CSS & JS -->
                    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
                    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
                <?php endif; ?>
                
                <style>
                    #map { height: 100%; width: 100%; background: #f0f0f0; }
                    .leaflet-popup-content-wrapper { border-radius: 4px; font-size: 0.8rem; }
                    .leaflet-popup-content { margin: 8px; }
                    /* Google Maps Custom Styles */
                    .gm-style .gm-style-iw-c { padding: 0 !important; border-radius: var(--radius-sm); }
                    .gm-style .gm-style-iw-d { padding: 10px !important; overflow: hidden !important; }
                </style>

                <div id="map" class="route-map-placeholder" style="background:none;"></div>

                <!-- Distance/Time Stats below map -->
                <div style="padding:1rem; display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem; text-align:center;">
                    <div>
                        <div style="font-weight:700;">18.5 mi</div>
                        <div style="font-size:0.7rem; color:var(--text-secondary);">Distance</div>
                    </div>
                    <div>
                        <div style="font-weight:700;">35 min</div>
                        <div style="font-size:0.7rem; color:var(--text-secondary);">Duration</div>
                    </div>
                    <div style="background:rgba(245, 158, 11, 0.1); color:var(--warning); border-radius:4px; padding:2px;">
                        <div style="font-weight:700;">Moderate</div>
                        <div style="font-size:0.7rem;">Traffic</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COL 5: Drivers -->
        <div class="col-panel">
            <div class="panel-header"><i data-lucide="car" width="16"></i> Drivers</div>
            <div class="panel-body" style="display:flex; align-items:center; justify-content:center; flex-direction:column; color:var(--text-secondary); text-align:center;">
                <i data-lucide="users" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p style="font-size:0.85rem;">Enter trip details and dispatch</p>
                <small>4 drivers online</small>
            </div>
        </div>

    </div>
</div>

<script>
    let map, tripLayer;
    let mapProvider = window.APP_MAP_PROVIDER || 'osm';
    
    // Leaflet specific
    let pickupIcon, dropoffIcon;
    if (mapProvider === 'osm' && typeof L !== 'undefined') {
        pickupIcon = L.divIcon({
            html: '<div style="background:var(--success); width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow:0 2px 4px rgba(0,0,0,0.3);"></div>',
            className: 'custom-div-icon',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });
        dropoffIcon = L.divIcon({
            html: '<div style="background:var(--danger); width:12px; height:12px; border-radius:50%; border:2px solid white; box-shadow:0 2px 4px rgba(0,0,0,0.3);"></div>',
            className: 'custom-div-icon',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });
    }

    // Google Maps specific
    let gMapMarkers = [];
    let gMapPolyline = null;
    let gMapTrafficLayer = null;

    // Variables to store coordinates
    let pickupCoords = { lat: 40.7128, lng: -74.0060 };
    let dropoffCoords = { lat: 40.6413, lng: -73.7781 };

    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // --- Google Autocomplete for Pickup/Dropoff ---
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            const pickupInput = document.getElementById('input-pickup');
            const dropoffInput = document.getElementById('input-dropoff');

            const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput);
            const dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput);

            pickupAutocomplete.addListener('place_changed', () => {
                const place = pickupAutocomplete.getPlace();
                if (place.geometry) {
                    pickupCoords = {
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng()
                    };
                    console.log('Pickup coords:', pickupCoords);
                }
            });

            dropoffAutocomplete.addListener('place_changed', () => {
                const place = dropoffAutocomplete.getPlace();
                if (place.geometry) {
                    dropoffCoords = {
                        lat: place.geometry.location.lat(),
                        lng: place.geometry.location.lng()
                    };
                    console.log('Dropoff coords:', dropoffCoords);
                }
            });
        }

        // --- Hourly Stats Modal Logic ---
        const modal = document.getElementById('hourlyStatsModal');
        const btn = document.getElementById('btn-hourly-stats');
        const closeBtn = document.querySelector('.close-modal');

        if(btn && modal) {
            btn.addEventListener('click', () => {
                modal.style.display = 'flex';
                setTimeout(() => {
                    modal.querySelector('.modal-content').style.transform = 'scale(1)';
                    modal.querySelector('.modal-content').style.opacity = '1';
                }, 10);
            });
        }

        if(closeBtn && modal) {
            closeBtn.addEventListener('click', () => {
                modal.querySelector('.modal-content').style.transform = 'scale(0.95)';
                modal.querySelector('.modal-content').style.opacity = '0';
                setTimeout(() => modal.style.display = 'none', 200);
            });
        }

        window.onclick = function(event) {
            if (modal && event.target == modal) {
                modal.querySelector('.modal-content').style.transform = 'scale(0.95)';
                modal.querySelector('.modal-content').style.opacity = '0';
                setTimeout(() => modal.style.display = 'none', 200);
            }
        }

        // --- Map Init ---
        if (mapProvider === 'osm') {
            // Leaflet Map Init
            map = L.map('map', { zoomControl: false }).setView([40.7128, -74.0060], 11);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            tripLayer = L.featureGroup().addTo(map);

            // Traffic Layer (TomTom Traffic Flow Tile Layer - free alternative)
            let trafficLayer = null;
            let trafficEnabled = false;

            const btnTraffic = document.getElementById('btn-traffic');
            if(btnTraffic) {
                btnTraffic.addEventListener('click', () => {
                    trafficEnabled = !trafficEnabled;
                    
                    if(trafficEnabled) {
                        // Add traffic layer overlay
                        trafficLayer = L.tileLayer('https://{s}.tile.thunderforest.com/transport/{z}/{x}/{y}.png?apikey=placeholder', {
                            attribution: 'Traffic Data',
                            opacity: 0.6,
                            maxZoom: 18
                        }).addTo(map);
                        
                        // Update button style
                        btnTraffic.style.background = 'var(--success)';
                        btnTraffic.innerHTML = '<i data-lucide="traffic-cone" width="12"></i> Traffic On';
                        lucide.createIcons();
                        
                        // Show traffic info popup
                        showTrafficInfoLeaflet();
                    } else {
                        // Remove traffic layer
                        if(trafficLayer) {
                            map.removeLayer(trafficLayer);
                            trafficLayer = null;
                        }
                        
                        // Restore button
                        btnTraffic.style.background = '';
                        btnTraffic.innerHTML = '<i data-lucide="traffic-cone" width="12"></i> Traffic';
                        lucide.createIcons();
                    }
                });
            }

            function showTrafficInfoLeaflet() {
                const html = buildTrafficHtml();
                L.popup({ closeButton: true, autoClose: false })
                    .setLatLng([40.7128, -74.0060])
                    .setContent(html)
                    .openOn(map);
                setTimeout(() => lucide.createIcons(), 100);
            }
        } else if (mapProvider === 'google' && typeof google !== 'undefined') {
            // Google Maps Init
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 40.7128, lng: -74.0060 },
                zoom: 11,
                disableDefaultUI: true,
                zoomControl: true,
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
                    { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                    { "elementType": "labels.text.fill", "stylers": [{ "color": "#616161" }] },
                    { "elementType": "labels.text.stroke", "stylers": [{ "color": "#f5f5f5" }] },
                    { "featureType": "administrative.land_parcel", "elementType": "labels.text.fill", "stylers": [{ "color": "#bdbdbd" }] },
                    { "featureType": "poi", "elementType": "geometry", "stylers": [{ "color": "#eeeeee" }] },
                    { "featureType": "poi", "elementType": "labels.text.fill", "stylers": [{ "color": "#757575" }] },
                    { "featureType": "poi.park", "elementType": "geometry", "stylers": [{ "color": "#e5e5e5" }] },
                    { "featureType": "poi.park", "elementType": "labels.text.fill", "stylers": [{ "color": "#9e9e9e" }] },
                    { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }] },
                    { "featureType": "road.arterial", "elementType": "labels.text.fill", "stylers": [{ "color": "#757575" }] },
                    { "featureType": "road.highway", "elementType": "geometry", "stylers": [{ "color": "#dadada" }] },
                    { "featureType": "road.highway", "elementType": "labels.text.fill", "stylers": [{ "color": "#616161" }] },
                    { "featureType": "road.local", "elementType": "labels.text.fill", "stylers": [{ "color": "#9e9e9e" }] },
                    { "featureType": "transit.line", "elementType": "geometry", "stylers": [{ "color": "#e5e5e5" }] },
                    { "featureType": "transit.station", "elementType": "geometry", "stylers": [{ "color": "#eeeeee" }] },
                    { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#c9c9c9" }] },
                    { "featureType": "water", "elementType": "labels.text.fill", "stylers": [{ "color": "#9e9e9e" }] }
                ]
            });

            gMapTrafficLayer = new google.maps.TrafficLayer();
            let trafficEnabled = false;

            const btnTraffic = document.getElementById('btn-traffic');
            if(btnTraffic) {
                btnTraffic.addEventListener('click', () => {
                    trafficEnabled = !trafficEnabled;
                    if(trafficEnabled) {
                        gMapTrafficLayer.setMap(map);
                        btnTraffic.style.background = 'var(--success)';
                        btnTraffic.innerHTML = '<i data-lucide="traffic-cone" width="12"></i> Traffic On';
                        lucide.createIcons();
                        showTrafficInfoGoogle();
                    } else {
                        gMapTrafficLayer.setMap(null);
                        btnTraffic.style.background = '';
                        btnTraffic.innerHTML = '<i data-lucide="traffic-cone" width="12"></i> Traffic';
                        lucide.createIcons();
                    }
                });
            }

            function showTrafficInfoGoogle() {
                const infoWindow = new google.maps.InfoWindow({
                    content: buildTrafficHtml(),
                    position: { lat: 40.7128, lng: -74.0060 }
                });
                infoWindow.open(map);
                setTimeout(() => lucide.createIcons(), 100);
            }
        }

        function buildTrafficHtml() {
            const trafficData = [
                { area: 'Downtown', status: 'Heavy', color: 'var(--danger)', delay: '+15 min' },
                { area: 'Highway I-95', status: 'Moderate', color: 'var(--warning)', delay: '+8 min' },
                { area: 'Airport Route', status: 'Light', color: 'var(--success)', delay: '+2 min' }
            ];

            let html = '<div style="padding:1rem; background:var(--bg-surface); border-radius:var(--radius-sm); max-width:300px;">';
            html += '<h4 style="margin:0 0 0.75rem 0; font-size:0.9rem; font-weight:700; display:flex; align-items:center; gap:0.5rem;"><i data-lucide="traffic-cone" width="16"></i> Current Traffic</h4>';
            
            trafficData.forEach(t => {
                html += `<div style="display:flex; justify-content:space-between; padding:0.5rem 0; border-bottom:1px dashed var(--border-color);">
                    <div>
                        <div style="font-weight:600; font-size:0.85rem;">${t.area}</div>
                        <div style="font-size:0.75rem; color:${t.color}; font-weight:600;">${t.status}</div>
                    </div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">${t.delay}</div>
                </div>`;
            });
            
            html += '</div>';
            return html;
        }

        // Dispatch Button Logic
        const sendBtn = document.getElementById('btn-dispatch');
        if(sendBtn) {
            sendBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                
                const payload = {
                    pickup_address: document.getElementById('input-pickup').value,
                    dropoff_address: document.getElementById('input-dropoff').value,
                    scheduled_at: document.getElementById('input-date').value + ' ' + document.getElementById('input-time').value,
                    vehicle_type: document.getElementById('input-vehicle').value,
                    passengers: document.getElementById('input-passengers').value,
                    notes: document.getElementById('input-notes').value,
                    
                    // Use captured coordinates from Autocomplete
                    pickup_lat: pickupCoords.lat,
                    pickup_lng: pickupCoords.lng,
                    dropoff_lat: dropoffCoords.lat,
                    dropoff_lng: dropoffCoords.lng
                };

                // Loading State
                const originalContent = sendBtn.innerHTML;
                sendBtn.innerHTML = '<i data-lucide="loader-2" class="animate-spin" width="16"></i> Dispatching...';
                sendBtn.disabled = true;
                lucide.createIcons();

                try {
                    const response = await fetch('<?= base_url('dispatch/trips/create') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify(payload)
                    });
                    const result = await response.json();

                    if (response.ok) {
                        alert('Trip Dispatched! #' + result.trip_number);
                        window.location.reload(); 
                    } else {
                        alert('Error: ' + (result.message || JSON.stringify(result.errors)));
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Network Error');
                } finally {
                     sendBtn.innerHTML = '<i data-lucide="check" width="16"></i> Sent';
                     setTimeout(() => {
                        sendBtn.innerHTML = originalContent;
                        sendBtn.disabled = false;
                        lucide.createIcons();
                     }, 2000);
                }
            });
        }
    });

    // --- Global Function for Trip Selection ---
    function selectTrip(el) {
        // Highlight logic
        document.querySelectorAll('.col-panel .panel-body > div').forEach(d => d.style.background = 'transparent');
        el.style.background = 'var(--bg-surface-hover)';

        let t;
        try {
            t = JSON.parse(el.dataset.trip);
        } catch(e) {
            console.error('Invalid Trip Data', e);
            return;
        }

        // Hide/Show dispatch button logic (Moved to top for priority)
        const dispatchBtn = document.getElementById('btn-dispatch');
        if(dispatchBtn) {
            // Check if driver_id is present and greater than 0
            if(t.driver_id && t.driver_id > 0) {
                dispatchBtn.style.display = 'none';
            } else {
                dispatchBtn.style.display = 'block';
                // Reset button text just in case
                dispatchBtn.innerHTML = '<i data-lucide="send" width="16" style="margin-right:8px;"></i> Send to Drivers';
                if(typeof lucide !== 'undefined') lucide.createIcons();
            }
        }
        
        // 1. Update Customer Panel
        const fName = t.c_first || 'Guest';
        const lName = t.c_last || '';
        document.getElementById('cust-initials').innerText = (fName.charAt(0) + lName.charAt(0)).toUpperCase();
        document.getElementById('cust-name').innerText = fName + ' ' + lName;
        document.getElementById('cust-phone').innerText = t.c_phone || '--'; 
        document.getElementById('cust-email').innerText = t.c_email || '--';
        document.getElementById('cust-trips').innerText = t.c_trip_count || '0';
        
        // 2. Update Driver Panel
        if(t.driver_id) {
            const dFirst = t.d_first || 'Driver';
            const dLast = t.d_last || '';
            document.getElementById('driver-initials').innerText = (dFirst.charAt(0) + dLast.charAt(0)).toUpperCase();
            document.getElementById('driver-name').innerText = dFirst + ' ' + dLast;
            document.getElementById('driver-phone').innerText = t.d_phone || '--';
            document.getElementById('driver-vehicle').innerText = t.d_vehicle || t.vehicle_type || 'Vehicle';
            document.getElementById('driver-plate').innerText = t.d_plate || '--';
            document.getElementById('driver-rating').innerText = (parseFloat(t.d_rating) || 0).toFixed(1) + ' ★';
        } else {
            document.getElementById('driver-initials').innerText = '--';
            document.getElementById('driver-name').innerText = 'Not Assigned';
            document.getElementById('driver-phone').innerText = '--';
            document.getElementById('driver-vehicle').innerText = '--';
            document.getElementById('driver-plate').innerText = '--';
            document.getElementById('driver-rating').innerText = '--';
        }

        // 3. Update Trip Form
        document.getElementById('input-pickup').value = t.pickup_address;
        document.getElementById('input-dropoff').value = t.dropoff_address;
        
        if(t.created_at) {
             const d = new Date(t.created_at);
             document.getElementById('input-date').value = d.toISOString().split('T')[0];
             document.getElementById('input-time').value = d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        document.getElementById('input-vehicle').value = t.vehicle_type || 'standard';
        document.getElementById('lbl-price').innerText = '$' + parseFloat(t.fare_amount).toFixed(2);
        document.getElementById('lbl-dist').innerText = t.distance_miles;
        document.getElementById('lbl-dur').innerText = t.duration_minutes;

        // 4. Update Map
        const lat1 = parseFloat(t.pickup_lat);
        const lng1 = parseFloat(t.pickup_lng);
        const lat2 = parseFloat(t.dropoff_lat);
        const lng2 = parseFloat(t.dropoff_lng);

        if(!isNaN(lat1) && !isNaN(lat2)) {
            const p1 = { lat: lat1, lng: lng1 };
            const p2 = { lat: lat2, lng: lng2 };

            if (mapProvider === 'osm') {
                tripLayer.clearLayers();
                L.marker([lat1, lng1], {icon: pickupIcon}).addTo(tripLayer).bindPopup("Pickup: " + t.pickup_address);
                L.marker([lat2, lng2], {icon: dropoffIcon}).addTo(tripLayer).bindPopup("Dropoff: " + t.dropoff_address);
                
                L.polyline([[lat1, lng1], [lat2, lng2]], {
                    color: 'var(--primary)',
                    weight: 4,
                    opacity: 0.8,
                    dashArray: '10, 10'
                }).addTo(tripLayer);
                
                map.fitBounds(L.latLngBounds([lat1, lng1], [lat2, lng2]).pad(0.2));
                map.invalidateSize();
            } else if (mapProvider === 'google' && typeof google !== 'undefined') {
                // Clear existing GMaps markers
                gMapMarkers.forEach(m => m.setMap(null));
                gMapMarkers = [];
                if (gMapPolyline) gMapPolyline.setMap(null);

                // Add Google Maps SVG Icons
                const pinSVGFilled = "M 12,2 C 8.134,2 5,5.134 5,9 c 0,5.25 7,13 7,13 0,0 7,-7.75 7,-13 0,-3.866 -3.134,-7 -7,-7 z";
                
                const pickupMarker = new google.maps.Marker({
                    position: p1,
                    map: map,
                    title: "Pickup: " + t.pickup_address,
                    icon: { path: pinSVGFilled, fillColor: "#10b981", fillOpacity: 1, strokeColor: "#ffffff", strokeWeight: 2, scale: 1.2, anchor: new google.maps.Point(12, 22) }
                });
                const dropoffMarker = new google.maps.Marker({
                    position: p2,
                    map: map,
                    title: "Dropoff: " + t.dropoff_address,
                    icon: { path: pinSVGFilled, fillColor: "#ef4444", fillOpacity: 1, strokeColor: "#ffffff", strokeWeight: 2, scale: 1.2, anchor: new google.maps.Point(12, 22) }
                });

                gMapMarkers.push(pickupMarker, dropoffMarker);

                // Add Polyline
                gMapPolyline = new google.maps.Polyline({
                    path: [p1, p2],
                    geodesic: true,
                    strokeColor: "#6366f1",
                    strokeOpacity: 0.8,
                    strokeWeight: 4
                });
                gMapPolyline.setMap(map);

                const bounds = new google.maps.LatLngBounds();
                bounds.extend(p1);
                bounds.extend(p2);
                map.fitBounds(bounds);
            }
        }
    }
</script>

<?= $this->endSection() ?>
