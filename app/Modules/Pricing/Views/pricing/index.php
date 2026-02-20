<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Tabs */
    .tabs-header {
        display: flex; gap: 4px; border-bottom: 1px solid var(--border-color);
        margin-bottom: 2rem;
    }
    .tab-item {
        padding: 0.75rem 1.5rem;
        border-bottom: 2px solid transparent;
        color: var(--text-secondary);
        font-weight: 500;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.1s;
    }
    .tab-item:hover { color: var(--text-primary); background: var(--bg-surface-hover); }
    .tab-item.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        font-weight: 600;
    }

    /* Layout */
    .pricing-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; }
    
    .pricing-section {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .section-title { font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin:0; }
    .section-desc { font-size: 0.85rem; color: var(--text-secondary); margin-top: 4px; }

    /* Inputs */
    .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem; }
    .input-group label { font-weight: 600; font-size: 0.9rem; display: block; margin-bottom: 0.5rem; }
    .input-group small { display: block; color: var(--text-secondary); font-size: 0.75rem; margin-top: 4px; }
    .money-input { 
        font-family: monospace; font-size: 1.2rem; font-weight: 600; 
        padding-left: 1.5rem; 
    }
    .input-wrapper { position: relative; }
    .currency-symbol { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-weight: 600; }

    /* Peak Hours */
    .peak-row { 
        display: flex; align-items: center; justify-content: space-between; 
        padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); margin-bottom: 0.75rem; background: var(--bg-body);
    }
    .peak-tag { display: flex; align-items: center; gap: 1rem; }
    .toggle-switch { /* ... */ } 

    /* Calculator */
    .calc-row { display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 0.75rem; color: var(--text-secondary); }
    .calc-total { display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 700; color: var(--primary); border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: 1rem; }
</style>

<div style="padding: 2rem; max-width: 1400px; margin: 0 auto;">
    
    <div style="margin-bottom: 1.5rem;">
        <h1 class="h3">Pricing Configuration</h1>
        <div style="color:var(--text-secondary);">Set your rates, peak hours, and zone-based pricing</div>
    </div>

    <!-- Tabs -->
    <div class="tabs-header">
        <?php foreach($rules as $r): ?>
        <a href="?tab=<?= $r['vehicle_type'] ?>" class="tab-item <?= $activeTab == $r['vehicle_type'] ? 'active' : '' ?>">
            <?= $r['vehicle_type'] ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="pricing-grid">
        
        <!-- Left: Settings -->
        <div>
            
            <form action="<?= base_url('pricing/update/'.$currentRule['id']) ?>" method="post">
                <!-- Base Pricing -->
                <div class="pricing-section">
                    <div class="section-header">
                        <div>
                            <h3 class="section-title"><i data-lucide="dollar-sign" width="20" style="vertical-align:bottom; margin-right:6px; color:var(--primary);"></i> Base Pricing</h3>
                            <div class="section-desc">Standard rates for distance and time</div>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline"><i data-lucide="save" width="14" style="margin-right:4px;"></i> Save Changes</button>
                    </div>

                    <div class="input-row">
                        <div class="input-group">
                            <label>Base Fare</label>
                            <div class="input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" step="0.01" name="base_fare" id="base_fare" class="form-control money-input" value="<?= $currentRule['base_fare'] ?>">
                            </div>
                            <small>Starting fare for each trip</small>
                        </div>
                        <div class="input-group">
                            <label>Per Mile Rate</label>
                            <div class="input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" step="0.01" name="distance_rate_per_mile" id="mile_rate" class="form-control money-input" value="<?= $currentRule['distance_rate_per_mile'] ?>">
                            </div>
                            <small>Charge per mile driven</small>
                        </div>
                    </div>

                    <div class="input-row" style="margin-bottom:0;">
                        <div class="input-group">
                            <label>Per Minute Rate</label>
                            <div class="input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" step="0.01" name="time_rate_per_minute" id="minute_rate" class="form-control money-input" value="<?= $currentRule['time_rate_per_minute'] ?>">
                            </div>
                            <small>Charge per minute of travel time</small>
                        </div>
                        <div class="input-group">
                            <label>Minimum Fare</label>
                            <div class="input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" step="0.01" name="minimum_fare" id="min_fare" class="form-control money-input" value="<?= $currentRule['minimum_fare'] ?>">
                            </div>
                            <small>Minimum charge for any trip</small>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Peak Hours -->
            <div class="pricing-section">
                <div class="section-header">
                    <div>
                        <h3 class="section-title"><i data-lucide="clock" width="20" style="vertical-align:bottom; margin-right:6px; color:var(--warning);"></i> Peak Hours</h3>
                        <div class="section-desc">Configure surge pricing for busy times</div>
                    </div>
                    <button type="button" onclick="document.getElementById('addPeakModal').style.display='flex'" class="btn btn-sm btn-outline"><i data-lucide="plus" width="14" style="margin-right:4px;"></i> Add Peak Hour</button>
                </div>

                <?php foreach($peakHours as $ph): ?>
                <div class="peak-row">
                    <div class="peak-tag">
                        <!-- Toggle (Visual only for now) -->
                        <div class="toggle-icon"><i data-lucide="toggle-right" style="color:var(--primary); cursor:pointer;"></i></div>
                        <div>
                            <div style="font-weight:600; font-size:0.9rem;"><?= $ph['day_of_week'] ?></div>
                            <div style="font-size:0.8rem; color:var(--text-secondary);"><?= substr($ph['start_time'], 0, 5) ?> - <?= substr($ph['end_time'], 0, 5) ?></div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <span style="background:var(--bg-surface-hover); padding:4px 8px; border-radius:4px; font-weight:700; font-size:0.85rem;"><?= $ph['multiplier'] ?>x</span>
                        <a href="<?= base_url('pricing/deletePeakHour/'.$ph['id']) ?>" onclick="return confirm('Remove this rule?')" style="color:var(--text-secondary); margin-top:4px;"><i data-lucide="trash-2" width="16"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($peakHours)): ?>
                    <div style="text-align:center; padding:2rem; color:var(--text-secondary); font-style:italic;">
                        No peak hours configured.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Zone-Based Pricing -->
            <div class="pricing-section">
                <div class="section-header">
                    <div>
                        <h3 class="section-title"><i data-lucide="map-pin" width="20" style="vertical-align:bottom; margin-right:6px; color:var(--info);"></i> Zone-Based Pricing</h3>
                        <div class="section-desc">Flat rates for specific routes (e.g., airports)</div>
                    </div>
                    <button type="button" onclick="document.getElementById('addZoneModal').style.display='flex'" class="btn btn-sm btn-outline"><i data-lucide="plus" width="14" style="margin-right:4px;"></i> Add Zone</button>
                </div>

                <?php foreach($zones as $z): ?>
                <div class="peak-row">
                    <div class="peak-tag">
                        <!-- Toggle (Visual only for now) -->
                        <div class="toggle-icon"><i data-lucide="<?= $z['is_active'] ? 'toggle-right' : 'toggle-left' ?>" style="color:var(--<?= $z['is_active'] ? 'primary' : 'text-secondary' ?>); cursor:pointer;"></i></div>
                        <div>
                            <div style="font-weight:600; font-size:0.9rem;"><?= esc($z['name']) ?></div>
                            <div style="font-size:0.8rem; color:var(--text-secondary);"><?= esc($z['description']) ?></div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:1rem;">
                        <span style="background:rgba(16, 185, 129, 0.1); color:var(--success); padding:4px 8px; border-radius:4px; font-weight:700; font-size:0.85rem;">$<?= $z['price'] ?></span>
                        <a href="<?= base_url('pricing/deleteZone/'.$z['id']) ?>" onclick="return confirm('Remove this zone?')" style="color:var(--text-secondary); margin-top:4px;"><i data-lucide="trash-2" width="16"></i></a>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($zones)): ?>
                    <div style="text-align:center; padding:2rem; color:var(--text-secondary); font-style:italic;">
                        No zones configured.
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right: Calculator -->
        <div>
            <div class="pricing-section" style="position:sticky; top:2rem;">
                <div class="section-header" style="margin-bottom:1rem;">
                    <h3 class="section-title"><i data-lucide="calculator" width="20" style="vertical-align:bottom; margin-right:6px; color:var(--info);"></i> Price Calculator</h3>
                </div>
                <div class="section-desc" style="margin-bottom:1.5rem;">Test your pricing configuration</div>

                <div class="form-group">
                    <label class="form-label">Distance (miles)</label>
                    <input type="number" id="calc_dist" class="form-control" value="10" oninput="calculatePrice()">
                </div>

                <div class="form-group">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="number" id="calc_time" class="form-control" value="20" oninput="calculatePrice()">
                </div>

                <div class="form-group" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                    <label class="form-label" style="margin:0;">Peak Hour?</label>
                    <label class="switch">
                        <input type="checkbox" id="calc_peak" onchange="calculatePrice()">
                        <span class="slider round"></span> <!-- Need CSS for switch, using simple checkbox for now -->
                    </label>
                </div>
                
                <div style="border-top:1px solid var(--border-color); margin: 1rem 0;"></div>

                <div class="calc-row">
                    <span>Base Fare</span>
                    <span id="disp_base">$0.00</span>
                </div>
                <div class="calc-row">
                    <span>Distance (<span id="txt_dist">10</span> mi)</span>
                    <span id="disp_dist">$0.00</span>
                </div>
                <div class="calc-row">
                    <span>Time (<span id="txt_time">20</span> min)</span>
                    <span id="disp_time">$0.00</span>
                </div>
                
                <div class="calc-total">
                    <span>Total</span>
                    <span id="disp_total">$0.00</span>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- Modal for Peak Hour -->
<div id="addPeakModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--bg-surface); padding:2rem; border-radius:var(--radius-md); width:400px; box-shadow:var(--shadow-lg);">
        <h3 class="h4" style="margin-bottom:1rem;">Add Peak Hour</h3>
        <form action="<?= base_url('pricing/addPeakHour') ?>" method="post">
            <input type="hidden" name="pricing_rule_id" value="<?= $currentRule['id'] ?>">
            
            <div class="form-group">
                <label class="form-label">Day of Week</label>
                <select name="day_of_week" class="form-select">
                    <option>Monday</option>
                    <option>Tuesday</option>
                    <option>Wednesday</option>
                    <option>Thursday</option>
                    <option>Friday</option>
                    <option>Saturday</option>
                    <option>Sunday</option>
                </select>
            </div>
            
            <div class="grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Multiplier (e.g. 1.5)</label>
                <input type="number" step="0.1" name="multiplier" class="form-control" placeholder="1.25" required>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addPeakModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Rule</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Zone -->
<div id="addZoneModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--bg-surface); padding:2rem; border-radius:var(--radius-md); width:400px; box-shadow:var(--shadow-lg);">
        <h3 class="h4" style="margin-bottom:1rem;">Add Zone Flat Rate</h3>
        <form action="<?= base_url('pricing/addZone') ?>" method="post">
            <input type="hidden" name="pricing_rule_id" value="<?= $currentRule['id'] ?>">
            
            <div class="form-group">
                <label class="form-label">Zone Name</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. JFK Airport" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Route Description</label>
                <input type="text" name="description" class="form-control" placeholder="e.g. Manhattan -> JFK" required>
            </div>

            <div class="form-group">
                <label class="form-label">Flat Rate ($)</label>
                <div class="input-wrapper">
                    <span class="currency-symbol">$</span>
                    <input type="number" step="0.01" name="price" class="form-control money-input" placeholder="65.00" required>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:1rem; margin-top:1.5rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('addZoneModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Zone</button>
            </div>
        </form>
    </div>
</div>


<script>
    // Calculator Logic
    function calculatePrice() {
        const base = parseFloat(document.getElementById('base_fare').value) || 0;
        const perMile = parseFloat(document.getElementById('mile_rate').value) || 0;
        const perMin = parseFloat(document.getElementById('minute_rate').value) || 0;
        const minFare = parseFloat(document.getElementById('min_fare').value) || 0;

        const dist = parseFloat(document.getElementById('calc_dist').value) || 0;
        const time = parseFloat(document.getElementById('calc_time').value) || 0;
        
        let multiplier = 1;
        // Simple mock multiplier check, in reality would check active peak hours from list
        if(document.getElementById('calc_peak').checked) {
            multiplier = 1.25; // Default mock surge
        }

        const costDist = dist * perMile;
        const costTime = time * perMin;
        
        let subtotal = (base + costDist + costTime) * multiplier;
        if(subtotal < minFare) subtotal = minFare;

        // Update UI
        document.getElementById('txt_dist').textContent = dist;
        document.getElementById('txt_time').textContent = time;

        document.getElementById('disp_base').textContent = '$' + base.toFixed(2);
        document.getElementById('disp_dist').textContent = '$' + costDist.toFixed(2);
        document.getElementById('disp_time').textContent = '$' + costTime.toFixed(2);
        
        document.getElementById('disp_total').textContent = '$' + subtotal.toFixed(2);
    }

    // Init
    calculatePrice();
</script>

<?= $this->endSection() ?>
