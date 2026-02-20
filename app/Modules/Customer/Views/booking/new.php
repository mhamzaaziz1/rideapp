<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<style>
    .booking-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        height: calc(100vh - 100px);
        overflow: hidden;
    }
    .booking-panel {
        background: var(--bg-surface);
        padding: 2rem;
        border-right: 1px solid var(--border-color);
        overflow-y: auto;
    }
    .map-panel {
        position: relative;
        background: #eee;
    }
    .vehicle-card {
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .vehicle-card:hover { border-color: var(--primary); background: var(--bg-surface-hover); }
    .vehicle-card.selected { border-color: var(--primary); background: rgba(59, 130, 246, 0.05); ring: 2px solid var(--primary); }
    
    .price-est { font-weight: 700; font-size: 1.1rem; }
    
    @media (max-width: 768px) {
        .booking-grid { grid-template-columns: 1fr; height: auto; overflow: visibile; }
        .map-panel { height: 300px; order: -1; } /* Map on top mobile */
    }
</style>

<div class="booking-grid">
    <!-- Left Panel: Form -->
    <div class="booking-panel">
        <h1 class="h3" style="margin-bottom:0.5rem;">Where to?</h1>
        <p style="color:var(--text-secondary); margin-bottom:2rem;">Book your ride instantly.</p>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('customer/book') ?>" method="post" id="bookingForm">
             <!-- Pickup -->
             <div class="form-group mb-4">
                <label class="form-label">Pickup Location</label>
                <div style="position:relative;">
                    <i data-lucide="map-pin" style="position:absolute; left:12px; top:12px; height:20px; width:20px; color:var(--success);"></i>
                    <input type="text" id="pickup_addr" name="pickup_address" class="form-control" style="padding-left:40px;" placeholder="Enter pickup address" required>
                </div>
                <input type="hidden" id="pickup_lat" name="pickup_lat">
                <input type="hidden" id="pickup_lng" name="pickup_lng">
             </div>

             <!-- Dropoff -->
             <div class="form-group mb-4">
                <label class="form-label">Destination</label>
                <div style="position:relative;">
                    <i data-lucide="navigation" style="position:absolute; left:12px; top:12px; height:20px; width:20px; color:var(--danger);"></i>
                    <input type="text" id="dropoff_addr" name="dropoff_address" class="form-control" style="padding-left:40px;" placeholder="Where are you going?" required>
                </div>
                <input type="hidden" id="dropoff_lat" name="dropoff_lat">
                <input type="hidden" id="dropoff_lng" name="dropoff_lng">
             </div>

             <!-- Vehicle Selection -->
             <div class="form-group mb-4">
                 <label class="form-label">Choose Ride</label>
                 <div id="vehicle-list">
                     <?php foreach($vehicle_types as $key => $label): ?>
                     <div class="vehicle-card" onclick="selectVehicle('<?= $key ?>')">
                         <div style="display:flex; align-items:center; gap:1rem;">
                             <!-- Icon placeholder -->
                             <div style="background:var(--bg-body); padding:8px; border-radius:50%;"><i data-lucide="car" width="20"></i></div>
                             <div>
                                 <div style="font-weight:600;"><?= esc($label) ?></div>
                                 <div style="font-size:0.8rem; color:var(--text-secondary);">Nerby • 3 min</div>
                             </div>
                         </div>
                         <div class="price-est" id="price-<?= $key ?>">--</div>
                     </div>
                     <?php endforeach; ?>
                 </div>
                 <input type="hidden" name="vehicle_type" id="selected_vehicle" value="standard">
             </div>

             <!-- Summary -->
             <div id="trip-summary" style="display:none; background:var(--bg-body); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem;">
                 <div style="display:flex; justify-content:space-between; margin-bottom:0.5rem;">
                     <span>Distance</span>
                     <span id="sum-dist" style="font-weight:600;">--</span>
                 </div>
                 <div style="display:flex; justify-content:space-between;">
                     <span>Est. Duration</span>
                     <span id="sum-dur" style="font-weight:600;">--</span>
                 </div>
             </div>

             <button type="submit" class="btn btn-primary btn-lg" style="width:100%;">Confirm Ride</button>
        </form>
    </div>

    <!-- Right Panel: Map -->
    <div class="map-panel">
        <div id="booking-map" style="width:100%; height:100%;"></div>
    </div>
</div>

<script>
    let map, pickupMarker, dropoffMarker, directionsRenderer, directionsService;
    let pickupAutocomplete, dropoffAutocomplete;

    function initBookingMap() {
        const defaultLoc = { lat: 40.7128, lng: -74.0060 };
        
        map = new google.maps.Map(document.getElementById("booking-map"), {
            zoom: 13,
            center: defaultLoc,
            disableDefaultUI: true,
             styles: [
                { elementType: "geometry", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.stroke", stylers: [{ color: "#242f3e" }] },
                { elementType: "labels.text.fill", stylers: [{ color: "#746855" }] },
                { FEATURE_ROAD_HIGHWAY: "geometry", stylers: [{ color: "#746855" }] },
            ]
        });

        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: false // Let google handle default markers for route
        });

        // Autocomplete
        initPlaces();
    }

    function initPlaces() {
        const pInput = document.getElementById('pickup_addr');
        const dInput = document.getElementById('dropoff_addr');

        pickupAutocomplete = new google.maps.places.Autocomplete(pInput);
        pickupAutocomplete.addListener('place_changed', () => {
            const place = pickupAutocomplete.getPlace();
            if (!place.geometry) return;
            
            document.getElementById('pickup_lat').value = place.geometry.location.lat();
            document.getElementById('pickup_lng').value = place.geometry.location.lng();
            
            checkRoute();
        });

        dropoffAutocomplete = new google.maps.places.Autocomplete(dInput);
        dropoffAutocomplete.addListener('place_changed', () => {
             const place = dropoffAutocomplete.getPlace();
            if (!place.geometry) return;
            
            document.getElementById('dropoff_lat').value = place.geometry.location.lat();
            document.getElementById('dropoff_lng').value = place.geometry.location.lng();
            
            checkRoute();
        });
    }

    function checkRoute() {
        const pLat = document.getElementById('pickup_lat').value;
        const pLng = document.getElementById('pickup_lng').value;
        const dLat = document.getElementById('dropoff_lat').value;
        const dLng = document.getElementById('dropoff_lng').value;

        if(pLat && dLat) {
            // Draw Route
            const start = new google.maps.LatLng(pLat, pLng);
            const end = new google.maps.LatLng(dLat, dLng);
            
            directionsService.route({
                origin: start,
                destination: end,
                travelMode: google.maps.TravelMode.DRIVING
            }, (response, status) => {
                if (status === "OK") {
                    directionsRenderer.setDirections(response);
                    // Fetch Estimate
                    fetchEstimate();
                } else {
                    console.error("Directions failed: " + status);
                }
            });
        }
    }

    async function fetchEstimate() {
        const formData = {
            pickup_lat: document.getElementById('pickup_lat').value,
            pickup_lng: document.getElementById('pickup_lng').value,
            dropoff_lat: document.getElementById('dropoff_lat').value,
            dropoff_lng: document.getElementById('dropoff_lng').value,
            vehicle_type: document.getElementById('selected_vehicle').value
        };

        try {
            const res = await fetch('<?= base_url("customer/estimate") ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const data = await res.json();
            
            // Update UI
            document.getElementById('trip-summary').style.display = 'block';
            document.getElementById('sum-dist').innerText = data.distance_miles + ' mi';
            document.getElementById('sum-dur').innerText = data.duration_minutes + ' min';
            
            // Update prices for all vehicle types
            if(data.fares) {
                for (const [type, priceStr] of Object.entries(data.fares)) {
                    const el = document.getElementById('price-' + type);
                    if(el) el.innerText = priceStr;
                }
            } else {
                // Fallback (shouldn't happen with new controller)
                console.warn("No fares returned");
            }
            
        } catch(e) { console.error(e); }
    }

    function selectVehicle(type) {
        document.getElementById('selected_vehicle').value = type;
        document.querySelectorAll('.vehicle-card').forEach(el => el.classList.remove('selected'));
        event.currentTarget.classList.add('selected');
        
        // Re-fetch estimate if route is set
        if(document.getElementById('pickup_lat').value && document.getElementById('dropoff_lat').value) {
            fetchEstimate();
        }
    }

    // Load Map
    window.addEventListener('load', initBookingMap);

</script>
<!-- Override Master initAutocomplete if needed, but we used custom initPlaces -->
<?= $this->endSection() ?>
