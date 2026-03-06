<!-- Quick Dispatch Modal -->
<div id="quickDispatchModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); z-index:1200; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="modal-content" style="background:var(--bg-surface); border-radius:var(--radius-md); width:540px; max-width:95vw; box-shadow:var(--shadow-lg); border:1px solid var(--border-color); max-height:90vh; overflow-y:auto;">
        <!-- Modal Header -->
        <div style="padding:1.25rem 1.5rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:var(--bg-surface); z-index:1;">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; border-radius:8px; background:rgba(99,102,241,0.1); display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="zap" width="18" style="color:var(--primary);"></i>
                </div>
                <div>
                    <div style="font-weight:700; font-size:1rem;">Quick Dispatch</div>
                    <div style="font-size:0.75rem; color:var(--text-secondary);">Create and dispatch a new trip</div>
                </div>
            </div>
            <button onclick="closeQuickDispatchModal()" style="background:none; border:none; cursor:pointer; color:var(--text-secondary); font-size:1.5rem; line-height:1; padding:4px;">&times;</button>
        </div>

        <!-- Form -->
        <form id="quickDispatchForm" action="<?= base_url('dispatch/trips/create') ?>" method="post">
            <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1rem;">

                <!-- Customer -->
                <div>
                    <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Customer <span style="color:var(--danger);">*</span></label>
                    <select name="customer_id" id="qdCustomerSelect" class="form-select" required style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                        <option value="">-- Select Customer --</option>
                        <?php foreach($customers as $c): ?>
                            <option value="<?= $c->id ?>"><?= esc($c->first_name . ' ' . $c->last_name) ?> (<?= esc($c->phone) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Pickup / Dropoff -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">
                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--success); margin-right:4px; vertical-align:middle;"></span>
                            Pickup Address <span style="color:var(--danger);">*</span>
                        </label>
                        <div style="position:relative;">
                            <input type="text" name="pickup_address" id="qdPickupAddress" class="form-control" required placeholder="Type or select pickup..." autocomplete="off" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.85rem;">
                            <div id="qdPickupAutocomplete" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); z-index:999; max-height:180px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
                        </div>
                        <input type="hidden" id="qd_pickup_lat" name="pickup_lat">
                        <input type="hidden" id="qd_pickup_lng" name="pickup_lng">
                        <div id="qdPickupSuggestions" style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.4rem;"></div>
                    </div>
                    <div>
                        <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">
                            <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:var(--danger); margin-right:4px; vertical-align:middle;"></span>
                            Dropoff Address <span style="color:var(--danger);">*</span>
                        </label>
                        <div style="position:relative;">
                            <input type="text" name="dropoff_address" id="qdDropoffAddress" class="form-control" required placeholder="Type or select dropoff..." autocomplete="off" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.85rem;">
                            <div id="qdDropoffAutocomplete" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); z-index:999; max-height:180px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
                        </div>
                        <input type="hidden" id="qd_dropoff_lat" name="dropoff_lat">
                        <input type="hidden" id="qd_dropoff_lng" name="dropoff_lng">
                        <div id="qdDropoffSuggestions" style="margin-top:0.5rem; display:flex; flex-direction:column; gap:0.4rem;"></div>
                    </div>
                </div>

                <input type="hidden" id="qd_distance_miles" name="distance_miles">
                <input type="hidden" id="qd_duration_minutes" name="duration_minutes">
                <input type="hidden" id="qd_calculated_fare" name="calculated_fare">

                <!-- Vehicle Type + Payment -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Vehicle Type</label>
                        <select name="vehicle_type" id="qdVehicleType" onchange="qdRecalculateFare()" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                            <option value="sedan">Sedan (Standard)</option>
                            <option value="suv">SUV (+50%)</option>
                            <option value="van">Van (+80%)</option>
                            <option value="luxury">Luxury (+100%)</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Payment Method</label>
                        <select name="payment_method" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="wallet">Wallet</option>
                        </select>
                    </div>
                </div>

                <!-- Assign Driver (optional) -->
                <div>
                    <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Assign Driver <span style="font-weight:400; color:var(--text-secondary);">(optional)</span></label>
                    <select name="driver_id" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                        <option value="">-- Assign Later --</option>
                        <?php foreach($drivers as $d): ?>
                            <option value="<?= $d->id ?>"><?= esc($d->first_name . ' ' . $d->last_name) ?> — <?= esc($d->vehicle_model) ?> ★<?= number_format($d->rating ?? 0, 1) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Passengers + Notes -->
                <div style="display:grid; grid-template-columns:120px 1fr; gap:1rem;">
                    <div>
                        <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Passengers</label>
                        <input type="number" name="passengers" min="1" max="20" value="1" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.85rem; font-weight:600; margin-bottom:6px; color:var(--text-primary);">Notes <span style="font-weight:400; color:var(--text-secondary);">(optional)</span></label>
                        <input type="text" name="notes" placeholder="e.g. Fragile item, 2 stops" style="width:100%; padding:0.6rem 0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                    </div>
                </div>

                <!-- Fare Preview (Auto-calculated) -->
                <div id="qd_fare_preview" style="display:none; background:var(--bg-body); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:1rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; font-weight:600;">Est. Fare</div>
                            <div id="qd_display_fare" style="font-size:1.25rem; font-weight:700; color:var(--info);">$0.00</div>
                        </div>
                        <div style="text-align:right;">
                            <div style="font-size:0.8rem; color:var(--text-secondary);">Distance: <strong id="qd_display_dist" style="color:var(--text-primary);">—</strong></div>
                            <div style="font-size:0.8rem; color:var(--text-secondary);">Duration: <strong id="qd_display_dur" style="color:var(--text-primary);">—</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Status feedback -->
                <div id="qdStatus" style="display:none; padding:0.75rem 1rem; border-radius:var(--radius-sm); font-size:0.875rem;"></div>

            </div><!-- /.padding -->

            <!-- Footer -->
            <div style="padding:1rem 1.5rem; border-top:1px solid var(--border-color); display:flex; justify-content:flex-end; gap:0.75rem; position:sticky; bottom:0; background:var(--bg-surface);">
                <button type="button" onclick="closeQuickDispatchModal()" class="btn btn-outline">Cancel</button>
                <button type="submit" id="qdSubmitBtn" class="btn btn-primary">
                    <i data-lucide="zap" width="14" style="margin-right:5px;"></i> Dispatch Trip
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    let qdPickupCoords = null;
    let qdDropoffCoords = null;
    let qdAutoTimeout = null;

    function openQuickDispatchModal() {
        const modal = document.getElementById('quickDispatchModal');
        document.getElementById('quickDispatchForm').reset();
        document.getElementById('qdStatus').style.display = 'none';
        document.getElementById('qdSubmitBtn').disabled = false;
        document.getElementById('qdSubmitBtn').innerHTML = '<i data-lucide="zap" width="14" style="margin-right:5px;"></i> Dispatch Trip';
        document.getElementById('qdPickupSuggestions').innerHTML = '';
        document.getElementById('qdDropoffSuggestions').innerHTML = '';
        document.getElementById('qd_fare_preview').style.display = 'none';
        qdPickupCoords = null;
        qdDropoffCoords = null;
        modal.style.display = 'flex';
        lucide.createIcons();
    }
    function closeQuickDispatchModal() {
        document.getElementById('quickDispatchModal').style.display = 'none';
    }

    // ── Nominatim Autocomplete for Quick Dispatch ─────────────────────────────
    function qdSetupAutocomplete(inputId, wrapperId, onSelect) {
        const input = document.getElementById(inputId);
        const box   = document.getElementById(wrapperId);
        if (!input || !box) return;

        input.addEventListener('input', function() {
            clearTimeout(qdAutoTimeout);
            const q = this.value.trim();
            box.style.display = 'none';
            if (q.length < 3) return;

            qdAutoTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${encodeURIComponent(q)}&email=admin@rideapp.com`,
                        { headers: { 'Accept-Language': 'en' } }
                    );
                    const results = await res.json();
                    box.innerHTML = '';
                    if (!results.length) {
                        box.innerHTML = '<div style="padding:0.6rem 1rem; color:var(--text-secondary); font-size:0.85rem;">No results found</div>';
                        box.style.display = 'block';
                        return;
                    }
                    results.forEach(r => {
                        const item = document.createElement('div');
                        item.textContent = r.display_name;
                        item.style.cssText = 'padding:0.6rem 1rem; cursor:pointer; font-size:0.85rem; border-bottom:1px solid var(--border-color); color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;';
                        item.addEventListener('mouseenter', () => item.style.background = 'var(--bg-surface-hover)');
                        item.addEventListener('mouseleave', () => item.style.background = '');
                        item.addEventListener('click', () => {
                            input.value = r.display_name;
                            box.style.display = 'none';
                            onSelect({ lat: parseFloat(r.lat), lng: parseFloat(r.lon), address: r.display_name });
                        });
                        box.appendChild(item);
                    });
                    box.style.display = 'block';
                } catch(e) { console.error('Nominatim error:', e); }
            }, 400);
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !box.contains(e.target)) box.style.display = 'none';
        });
    }

    qdSetupAutocomplete('qdPickupAddress', 'qdPickupAutocomplete', function(loc) {
        qdPickupCoords = loc;
        document.getElementById('qd_pickup_lat').value = loc.lat;
        document.getElementById('qd_pickup_lng').value = loc.lng;
        qdTryCalculate();
    });

    qdSetupAutocomplete('qdDropoffAddress', 'qdDropoffAutocomplete', function(loc) {
        qdDropoffCoords = loc;
        document.getElementById('qd_dropoff_lat').value = loc.lat;
        document.getElementById('qd_dropoff_lng').value = loc.lng;
        qdTryCalculate();
    });

    // ── Fare & Distance Calculation ───────────────────────────────────────────
    function qdHaversine(lat1, lon1, lat2, lon2) {
        const R = 3959;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 + Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    const qdVehicleMultipliers = { sedan: 1.0, suv: 1.5, van: 1.8, luxury: 2.0 };

    function qdCalculateFare(distMiles, durationMin, vehicleType) {
        const baseF = 5.00, perMile = 1.50, perMin = 0.25, minFare = 10.00;
        const mult = qdVehicleMultipliers[vehicleType.toLowerCase()] ?? 1.0;
        const raw = (baseF + distMiles * perMile + durationMin * perMin) * mult;
        return Math.max(raw, minFare).toFixed(2);
    }

    function qdRecalculateFare() {
        qdTryCalculate();
    }

    function qdTryCalculate() {
        if (!qdPickupCoords || !qdDropoffCoords) return;
        const dist = qdHaversine(qdPickupCoords.lat, qdPickupCoords.lng, qdDropoffCoords.lat, qdDropoffCoords.lng);
        const dur  = Math.round((dist / 25) * 60);
        const vType = document.getElementById('qdVehicleType').value;
        const fare = qdCalculateFare(dist, dur, vType);

        document.getElementById('qd_distance_miles').value = dist.toFixed(2);
        document.getElementById('qd_duration_minutes').value = dur;
        document.getElementById('qd_calculated_fare').value = fare;

        document.getElementById('qd_display_dist').textContent = dist.toFixed(2) + ' mi';
        document.getElementById('qd_display_dur').textContent  = dur + ' min';
        document.getElementById('qd_display_fare').textContent = '$' + fare;
        document.getElementById('qd_fare_preview').style.display = 'block';
    }

    // Handle Customer Selection -> Fetch Addresses
    document.getElementById('qdCustomerSelect').addEventListener('change', function() {
        const custId = this.value;
        const pSug   = document.getElementById('qdPickupSuggestions');
        const dSug   = document.getElementById('qdDropoffSuggestions');
        
        pSug.innerHTML = '';
        dSug.innerHTML = '';
        
        if (!custId) return;

        fetch('<?= base_url("customer/addresses") ?>/' + custId)
            .then(r => r.json())
            .then(data => {
                if (data.addresses && data.addresses.length > 0) {
                    let chipsHtml = '';
                    data.addresses.forEach(addr => {
                        const badgeStr = addr.type ? `<span style="background:var(--bg-surface); border:1px solid var(--border-color); padding:0 4px; border-radius:4px; font-size:0.65rem; margin-right:4px;">${addr.type}</span>` : '';
                        const defaultStr = addr.is_default ? `<span style="color:var(--primary); font-size:0.65rem; margin-left:4px;">(Default)</span>` : '';
                        const fullAddr = addr.full.replace(/'/g, "\\'");
                        // Pass lat/lng so calculation triggers properly 
                        chipsHtml += `
                            <div onclick="setQdAddressInput('qdPickupAddress', '${fullAddr}', ${addr.latitude||0}, ${addr.longitude||0}, 'pickup')" style="cursor:pointer; font-size:0.75rem; color:var(--text-secondary); padding:4px 6px; background:var(--bg-body); border-radius:4px; border:1px solid var(--border-color); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                ${badgeStr} ${addr.full} ${defaultStr}
                            </div>
                        `;
                    });
                    
                    // Replace names for the dropoff chips
                    let dChipsHtml = chipsHtml.replace(/qdPickupAddress/g, 'qdDropoffAddress').replace(/'pickup'/g, "'dropoff'");

                    pSug.innerHTML = chipsHtml;
                    dSug.innerHTML = dChipsHtml;
                }
            })
            .catch(err => console.error('Error fetching addresses:', err));
    });

    function setQdAddressInput(inputId, address, lat, lng, type) {
        document.getElementById(inputId).value = address;
        
        // Hide autocomplete if it's open
        if (type === 'pickup') {
            document.getElementById('qdPickupAutocomplete').style.display = 'none';
            if(lat && lng) {
                qdPickupCoords = { lat: lat, lng: lng, address: address };
                document.getElementById('qd_pickup_lat').value = lat;
                document.getElementById('qd_pickup_lng').value = lng;
            }
        } else {
            document.getElementById('qdDropoffAutocomplete').style.display = 'none';
            if(lat && lng) {
                qdDropoffCoords = { lat: lat, lng: lng, address: address };
                document.getElementById('qd_dropoff_lat').value = lat;
                document.getElementById('qd_dropoff_lng').value = lng;
            }
        }
        qdTryCalculate();
    }

    // Quick Dispatch AJAX submit
    document.getElementById('quickDispatchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn    = document.getElementById('qdSubmitBtn');
        const status = document.getElementById('qdStatus');

        btn.disabled  = true;
        btn.innerHTML = 'Dispatching...';
        status.style.display = 'none';

        fetch(this.action, {
            method: 'POST',
            body: new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                status.style.cssText = 'display:block; background:rgba(16,185,129,0.1); color:var(--success); border:1px solid rgba(16,185,129,0.3); padding:0.75rem 1rem; border-radius:var(--radius-sm); font-size:0.875rem;';
                status.innerHTML = '✓ Trip <strong>' + data.trip_number + '</strong> dispatched! Fare: <strong>$' + parseFloat(data.fare).toFixed(2) + '</strong>';
                btn.innerHTML = '✓ Dispatched';
                setTimeout(() => { closeQuickDispatchModal(); location.reload(); }, 1800);
            } else {
                status.style.cssText = 'display:block; background:rgba(239,68,68,0.08); color:var(--danger); border:1px solid rgba(239,68,68,0.2); padding:0.75rem 1rem; border-radius:var(--radius-sm); font-size:0.875rem;';
                const errs = data.errors ? Object.values(data.errors).join('<br>') : (data.message || 'Dispatch failed.');
                status.innerHTML = errs;
                btn.disabled  = false;
                btn.innerHTML = '<i data-lucide="zap" width="14" style="margin-right:5px;"></i> Dispatch Trip';
                lucide.createIcons();
            }
        })
        .catch(err => {
            console.error(err);
            status.style.cssText = 'display:block; background:rgba(239,68,68,0.08); color:var(--danger); border:1px solid rgba(239,68,68,0.2); padding:0.75rem 1rem; border-radius:var(--radius-sm); font-size:0.875rem;';
            status.innerHTML = 'Network error. Please try again.';
            btn.disabled  = false;
            btn.innerHTML = '<i data-lucide="zap" width="14" style="margin-right:5px;"></i> Dispatch Trip';
            lucide.createIcons();
        });
    });
</script>
