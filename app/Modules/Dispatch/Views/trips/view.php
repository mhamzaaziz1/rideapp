<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }
    
    .detail-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        padding: 0.75rem 0;
        border-bottom: 1px dashed var(--border-color);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.85rem;
    }
    
    .info-value {
        color: var(--text-primary);
        font-size: 0.9rem;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .payment-breakdown {
        background: var(--bg-body);
        padding: 1rem;
        border-radius: var(--radius-sm);
        margin-top: 1rem;
    }
    
    .breakdown-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.9rem;
    }
    
    .breakdown-row.total {
        border-top: 2px solid var(--border-color);
        margin-top: 0.5rem;
        padding-top: 1rem;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    #trip-map {
        width: 100%;
        height: 400px;
        border-radius: var(--radius-sm);
    }
</style>

<div class="detail-container">
    <!-- Header -->
    <div class="detail-header">
        <div>
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.5rem;">
                <h1 class="h3" style="margin:0;">Trip #<?= esc($trip->trip_number) ?></h1>
                <?php
                    $statusStyles = [
                        'pending' => 'background:rgba(245, 158, 11, 0.1); color:var(--warning); border:1px solid var(--warning);',
                        'dispatching' => 'background:rgba(59, 130, 246, 0.1); color:var(--info); border:1px solid var(--info);',
                        'active' => 'background:rgba(16, 185, 129, 0.1); color:var(--success); border:1px solid var(--success);',
                        'completed' => 'background:rgba(107, 114, 128, 0.1); color:var(--text-secondary); border:1px solid var(--text-secondary);',
                        'cancelled' => 'background:rgba(239, 68, 68, 0.1); color:var(--danger); border:1px solid var(--danger);'
                    ];
                    $style = $statusStyles[$trip->status] ?? $statusStyles['pending'];
                ?>
                <span class="status-badge" style="<?= $style ?>"><?= ucfirst($trip->status) ?></span>
            </div>
            <div style="color:var(--text-secondary); font-size:0.9rem;">
                Created: <?= $trip->created_at->format('M d, Y H:i') ?>
            </div>
        </div>
        <div style="display:flex; gap:1rem;">
            <a href="<?= base_url('dispatch/trips/edit/' . $trip->id) ?>" class="btn btn-secondary">
                <i data-lucide="edit-2" width="16"></i> Edit Trip
            </a>
            <a href="<?= base_url('dispatch/trips') ?>" class="btn btn-outline">
                <i data-lucide="arrow-left" width="16"></i> Back to List
            </a>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Left Column -->
        <div>
            <!-- Route Map -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="map" width="20"></i> Route Map
                </div>
                <div id="trip-map"></div>
                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; margin-top:1rem; text-align:center;">
                    <div>
                        <div style="font-size:1.5rem; font-weight:700; color:var(--primary);"><?= number_format($trip->distance_miles, 2) ?></div>
                        <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase;">Miles</div>
                    </div>
                    <div>
                        <div style="font-size:1.5rem; font-weight:700; color:var(--info);"><?= $trip->duration_minutes ?? '--' ?></div>
                        <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase;">Minutes</div>
                    </div>
                    <div>
                        <div style="font-size:1.5rem; font-weight:700; color:var(--success);"><?= ucfirst($trip->vehicle_type ?? 'Standard') ?></div>
                        <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase;">Vehicle</div>
                    </div>
                </div>
            </div>

            <!-- Route Details -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="navigation" width="20"></i> Route Details
                </div>
                <div class="info-row">
                    <div class="info-label"><i data-lucide="map-pin" width="14" style="color:var(--success);"></i> Pickup</div>
                    <div class="info-value"><?= esc($trip->pickup_address) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i data-lucide="map-pin" width="14" style="color:var(--danger);"></i> Dropoff</div>
                    <div class="info-value"><?= esc($trip->dropoff_address) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Coordinates</div>
                    <div class="info-value" style="font-family:monospace; font-size:0.8rem;">
                        <div>Pickup: <?= $trip->pickup_lat ?>, <?= $trip->pickup_lng ?></div>
                        <div>Dropoff: <?= $trip->dropoff_lat ?>, <?= $trip->dropoff_lng ?></div>
                    </div>
                </div>
            </div>

            <!-- Payment Breakdown -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="dollar-sign" width="20"></i> Payment Breakdown
                </div>
                
                <div class="payment-breakdown">
                    <div class="breakdown-row">
                        <span>Base Fare</span>
                        <span>$<?= number_format($trip->fare_amount * 0.3, 2) ?></span>
                    </div>
                    <div class="breakdown-row">
                        <span>Distance (<?= number_format($trip->distance_miles, 2) ?> mi × $2.50)</span>
                        <span>$<?= number_format($trip->distance_miles * 2.5, 2) ?></span>
                    </div>
                    <div class="breakdown-row">
                        <span>Duration (<?= $trip->duration_minutes ?? 0 ?> min × $0.35)</span>
                        <span>$<?= number_format(($trip->duration_minutes ?? 0) * 0.35, 2) ?></span>
                    </div>
                    <?php if(isset($trip->surcharge_amount) && $trip->surcharge_amount > 0): ?>
                    <div class="breakdown-row">
                        <span>Surcharge (Peak Hours)</span>
                        <span>$<?= number_format($trip->surcharge_amount, 2) ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="breakdown-row total">
                        <span>Total Fare</span>
                        <span style="color:var(--primary);">$<?= number_format($trip->fare_amount, 2) ?></span>
                    </div>
                </div>

                <!-- Commission Split -->
                <?php if(isset($trip->driver_earnings) && $trip->driver_earnings > 0): ?>
                <div style="margin-top:1.5rem; padding-top:1.5rem; border-top:2px solid var(--border-color);">
                    <h4 style="font-size:0.9rem; font-weight:700; margin-bottom:1rem;">Commission Split</h4>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                        <div style="background:rgba(16, 185, 129, 0.1); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--success);">
                            <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:0.25rem;">Driver Earnings</div>
                            <div style="font-size:1.5rem; font-weight:700; color:var(--success);">$<?= number_format($trip->driver_earnings, 2) ?></div>
                            <div style="font-size:0.7rem; color:var(--text-secondary); margin-top:0.25rem;">
                                <?= number_format((($trip->driver_earnings / $trip->fare_amount) * 100), 1) ?>% of fare
                            </div>
                        </div>
                        <div style="background:rgba(59, 130, 246, 0.1); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--info);">
                            <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:0.25rem;">Company Commission</div>
                            <div style="font-size:1.5rem; font-weight:700; color:var(--info);">$<?= number_format($trip->commission_amount, 2) ?></div>
                            <div style="font-size:0.7rem; color:var(--text-secondary); margin-top:0.25rem;">
                                <?= number_format((($trip->commission_amount / $trip->fare_amount) * 100), 1) ?>% of fare
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- Associated Dispute Card -->
            <?php if(isset($dispute) && $dispute): ?>
            <div class="detail-card" style="border-color: var(--danger); background: rgba(239, 68, 68, 0.02);">
                <div class="card-header" style="color: var(--danger); border-bottom-color: rgba(239, 68, 68, 0.2);">
                    <i data-lucide="alert-triangle" width="20"></i> Active Dispute Found
                </div>
                <div style="padding: 0.5rem 0;">
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.5rem;">#DSP-<?= esc($dispute->id) ?>: <?= esc($dispute->title) ?></div>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
                        <span style="font-size:0.85rem; color:var(--text-secondary); background:var(--bg-body); padding:2px 8px; border-radius:12px; border:1px solid var(--border-color);">
                            <?= esc(ucfirst($dispute->dispute_type)) ?>
                        </span>
                        <?php
                            $statusColors = [
                                'open' => 'var(--danger)',
                                'investigating' => 'var(--warning)',
                                'resolved' => 'var(--success)',
                                'closed' => 'var(--text-secondary)',
                            ];
                            $color = $statusColors[$dispute->status] ?? 'var(--danger)';
                        ?>
                        <span style="font-size:0.85rem; font-weight:700; color:<?= $color ?>; text-transform:uppercase;">
                            <?= ucfirst($dispute->status) ?>
                        </span>
                    </div>
                    
                    <a href="<?= base_url('admin/disputes/view/'.$dispute->id) ?>" class="btn btn-primary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        View Dispute Case <i data-lucide="arrow-right" width="16"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Customer Info -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="user" width="20"></i> Customer Information
                </div>
                <?php if($customer): ?>
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
                        <div style="width:60px; height:60px; background:var(--primary); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:1.5rem;">
                            <?= substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1) ?>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:1.1rem;"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></div>
                            <div style="color:var(--text-secondary); font-size:0.85rem;"><?= esc($customer->email ?? 'N/A') ?></div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?= esc($customer->phone ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Rating</div>
                        <div class="info-value">
                            <span style="color:var(--warning); font-weight:700;">★ <?= number_format($customer->rating ?? 0, 1) ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status</div>
                        <div class="info-value">
                            <span style="text-transform:capitalize; color:<?= $customer->status == 'active' ? 'var(--success)' : 'var(--danger)' ?>;">
                                <?= esc($customer->status) ?>
                            </span>
                        </div>
                    </div>
                    <?php if(isset($trip_customer_rating)): ?>
                    <div class="info-row" style="background:rgba(255, 255, 0, 0.1);">
                        <div class="info-label">Rated By Driver</div>
                        <div class="info-value">
                            <div style="color:var(--warning); font-weight:700;">★ <?= number_format($trip_customer_rating['rating'], 1) ?></div>
                            <?php if(!empty($trip_customer_rating['comment'])): ?>
                                <div style="font-size:0.85rem; font-style:italic; color:var(--text-secondary);">"<?= esc($trip_customer_rating['comment']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <a href="<?= base_url('customers/profile/' . $customer->id) ?>" class="btn btn-outline" style="width:100%; margin-top:1rem; text-align:center;">
                        View Full Profile
                    </a>
                <?php else: ?>
                    <p style="color:var(--text-secondary); text-align:center; padding:1rem;">Customer information not available</p>
                <?php endif; ?>
            </div>

            <!-- Driver Info -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="car" width="20"></i> Driver Information
                </div>
                <?php if($driver): ?>
                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1rem;">
                        <div style="width:60px; height:60px; background:var(--success); border-radius:50%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:1.5rem;">
                            <?= substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1) ?>
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight:700; font-size:1.1rem;"><?= esc($driver->first_name . ' ' . $driver->last_name) ?></div>
                            <div style="color:var(--text-secondary); font-size:0.85rem;"><?= esc($driver->vehicle_model ?? 'N/A') ?></div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?= esc($driver->phone ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">License Plate</div>
                        <div class="info-value" style="font-family:monospace; font-weight:700;"><?= esc($driver->license_plate ?? 'N/A') ?></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Rating</div>
                        <div class="info-value">
                            <span style="color:var(--warning); font-weight:700;">★ <?= number_format($driver->rating ?? 0, 1) ?></span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Commission Rate</div>
                        <div class="info-value">
                            <span style="font-weight:700; color:var(--info);"><?= number_format($driver->commission_rate ?? 20, 1) ?>%</span>
                        </div>
                    </div>
                    <?php if(isset($trip_driver_rating)): ?>
                    <div class="info-row" style="background:rgba(255, 255, 0, 0.1);">
                        <div class="info-label">Rated By Customer</div>
                        <div class="info-value">
                            <div style="color:var(--warning); font-weight:700;">★ <?= number_format($trip_driver_rating['rating'], 1) ?></div>
                            <?php if(!empty($trip_driver_rating['comment'])): ?>
                                <div style="font-size:0.85rem; font-style:italic; color:var(--text-secondary);">"<?= esc($trip_driver_rating['comment']) ?>"</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <a href="<?= base_url('drivers/profile/' . $driver->id) ?>" class="btn btn-outline" style="width:100%; margin-top:1rem; text-align:center;">
                        View Full Profile
                    </a>
                <?php else: ?>
                    <p style="color:var(--text-secondary); text-align:center; padding:1rem;">No driver assigned yet</p>
                <?php endif; ?>
            </div>

            <!-- Additional Details -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="info" width="20"></i> Additional Details
                </div>
                <div class="info-row">
                    <div class="info-label">Trip ID</div>
                    <div class="info-value" style="font-family:monospace;"><?= $trip->id ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Created At</div>
                    <div class="info-value"><?= $trip->created_at->format('M d, Y H:i:s') ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Updated At</div>
                    <div class="info-value"><?= $trip->updated_at->format('M d, Y H:i:s') ?></div>
                </div>
                <?php if($trip->notes): ?>
                <div class="info-row">
                    <div class="info-label">Notes</div>
                    <div class="info-value"><?= nl2br(esc($trip->notes)) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Leaflet Map (Only load if OSM) -->
<script>
    const mapProvider = window.APP_MAP_PROVIDER || 'osm';
    if (mapProvider === 'osm') {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(link);
        
        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        document.head.appendChild(script);
    }
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // Initialize Map
        const pickupLat = <?= $trip->pickup_lat ?>;
        const pickupLng = <?= $trip->pickup_lng ?>;
        const dropoffLat = <?= $trip->dropoff_lat ?>;
        const dropoffLng = <?= $trip->dropoff_lng ?>;

        // If coordinates are missing, don't try to render map
        if (isNaN(pickupLat) || isNaN(dropoffLat)) {
            document.getElementById('trip-map').innerHTML = '<div style="padding:2rem;text-align:center;color:var(--text-secondary);">Map coordinates unavailable</div>';
            return;
        }

        const checkDependencies = setInterval(() => {
            if (mapProvider === 'osm' && typeof L === 'undefined') return;
            if (mapProvider === 'google' && typeof google === 'undefined') return;
            
            clearInterval(checkDependencies);
            renderMap();
        }, 100);

        function renderMap() {
            if (mapProvider === 'osm') {
                const map = L.map('trip-map').setView([pickupLat, pickupLng], 12);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(map);

                // Custom icons
                const pickupIcon = L.divIcon({
                    html: '<div style="background:#10b981; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>',
                    className: '',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                const dropoffIcon = L.divIcon({
                    html: '<div style="background:#ef4444; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 2px 6px rgba(0,0,0,0.4);"></div>',
                    className: '',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                // Add markers
                L.marker([pickupLat, pickupLng], {icon: pickupIcon})
                    .addTo(map)
                    .bindPopup('<b>Pickup:</b><br><?= addslashes((string)$trip->pickup_address) ?>');

                L.marker([dropoffLat, dropoffLng], {icon: dropoffIcon})
                    .addTo(map)
                    .bindPopup('<b>Dropoff:</b><br><?= addslashes((string)$trip->dropoff_address) ?>');

                // Draw route line
                L.polyline([[pickupLat, pickupLng], [dropoffLat, dropoffLng]], {
                    color: '#3b82f6',
                    weight: 4,
                    opacity: 0.7,
                    dashArray: '10, 10'
                }).addTo(map);

                // Fit bounds to show both markers
                const bounds = L.latLngBounds([[pickupLat, pickupLng], [dropoffLat, dropoffLng]]);
                map.fitBounds(bounds.pad(0.2));
            } else if (mapProvider === 'google') {
                const map = new google.maps.Map(document.getElementById('trip-map'), {
                    center: { lat: pickupLat, lng: pickupLng },
                    zoom: 12,
                    disableDefaultUI: true,
                    zoomControl: true
                });

                const pinSVGFilled = "M 12,2 C 8.134,2 5,5.134 5,9 c 0,5.25 7,13 7,13 0,0 7,-7.75 7,-13 0,-3.866 -3.134,-7 -7,-7 z";
                
                const pickupMarker = new google.maps.Marker({
                    position: { lat: pickupLat, lng: pickupLng },
                    map: map,
                    title: "Pickup: <?= addslashes((string)$trip->pickup_address) ?>",
                    icon: { path: pinSVGFilled, fillColor: "#10b981", fillOpacity: 1, strokeColor: "#ffffff", strokeWeight: 2, scale: 1.5, anchor: new google.maps.Point(12, 22) }
                });

                const dropoffMarker = new google.maps.Marker({
                    position: { lat: dropoffLat, lng: dropoffLng },
                    map: map,
                    title: "Dropoff: <?= addslashes((string)$trip->dropoff_address) ?>",
                    icon: { path: pinSVGFilled, fillColor: "#ef4444", fillOpacity: 1, strokeColor: "#ffffff", strokeWeight: 2, scale: 1.5, anchor: new google.maps.Point(12, 22) }
                });

                const flightPath = new google.maps.Polyline({
                    path: [
                        { lat: pickupLat, lng: pickupLng },
                        { lat: dropoffLat, lng: dropoffLng }
                    ],
                    geodesic: true,
                    strokeColor: "#3b82f6",
                    strokeOpacity: 0.8,
                    strokeWeight: 4
                });
                flightPath.setMap(map);

                const bounds = new google.maps.LatLngBounds();
                bounds.extend({ lat: pickupLat, lng: pickupLng });
                bounds.extend({ lat: dropoffLat, lng: dropoffLng });
                map.fitBounds(bounds);
            }
        }
    });
</script>

<?= $this->endSection() ?>
