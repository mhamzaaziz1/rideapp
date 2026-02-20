<!-- Quick Dispatch Modal -->
<div id="quickDispatchModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center; padding:1rem;">
    <style>
        @keyframes modalFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .modal-animate { animation: modalFadeIn 0.2s ease-out; }
    </style>
    <div class="modal-content modal-animate" style="background:var(--bg-surface); padding:0; border-radius:var(--radius-md); width:100%; max-width:600px; box-shadow:var(--shadow-lg); display:flex; flex-direction:column; max-height:90vh;">
        
        <!-- Header -->
        <div style="padding:1.5rem; border-bottom:1px solid var(--border-color); display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h3 class="h4" style="margin:0;">New Dispatch</h3>
                <div style="font-size:0.85rem; color:var(--text-secondary);">Create a new trip quickly</div>
            </div>
            <button type="button" onclick="closeQuickDispatchModal()" style="background:none; border:none; color:var(--text-secondary); cursor:pointer;"><i data-lucide="x" width="20"></i></button>
        </div>

        <!-- Scrollable Form Body -->
        <div style="padding:1.5rem; overflow-y:auto; flex:1;">
            <form action="<?= base_url('dispatch/trips/create') ?>" method="post" id="quickDispatchForm">
                
                <!-- Customer Selection -->
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Select Customer...</option>
                        <!-- PHP loop for customers, usually passed to view -->
                        <?php if(isset($customers)): ?>
                            <?php foreach($customers as $c): ?>
                                <option value="<?= $c->id ?>"><?= esc($c->first_name . ' ' . $c->last_name) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Locations -->
                <div style="position:relative; padding-left:1.5rem;">
                    <div style="position:absolute; left:4px; top:32px; bottom:32px; width:2px; background:var(--border-color);"></div>
                    <div style="position:absolute; left:0; top:8px; width:10px; height:10px; border-radius:50%; border:2px solid var(--success); background:var(--bg-surface);"></div>
                    <div style="position:absolute; left:0; bottom:25px; width:10px; height:10px; border-radius:50%; border:2px solid var(--danger); background:var(--bg-surface);"></div>

                    <div class="form-group">
                        <label class="form-label">Pickup Location</label>
                        <input type="text" name="pickup_address" class="form-control addr-autocomplete" placeholder="123 Pickup St" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dropoff Location</label>
                        <input type="text" name="dropoff_address" class="form-control addr-autocomplete" placeholder="456 Dropoff Ave" required>
                    </div>
                </div>

                <div class="grid-2" style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                    <div class="form-group">
                        <label class="form-label">Scheduled Time (Optional)</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select">
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV</option>
                            <option value="Van">Van</option>
                            <option value="Luxury">Luxury</option>
                        </select>
                    </div>
                </div>

                <!-- Driver Assignment (Optional for quick dispatch) -->
                <div class="form-group" style="padding:1rem; background:var(--bg-body); border-radius:var(--radius-sm);">
                    <label class="form-label" style="display:flex; justify-content:space-between;">
                        <span>Assign Driver Now?</span>
                        <span style="font-size:0.75rem; color:var(--text-secondary);">(Optional)</span>
                    </label>
                    <select name="driver_id" class="form-select" onchange="updateDispatchStatus(this)">
                        <option value="">Auto-Assign Later / Queue</option>
                        <?php if(isset($drivers)): ?>
                            <?php foreach($drivers as $d): ?>
                                <option value="<?= $d->id ?>"><?= esc($d->first_name . ' ' . $d->last_name) ?> (<?= $d->vehicle_model ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <input type="hidden" name="status" id="tripStatus" value="pending"> 
                
                <div style="margin-top:1.5rem; display:flex; justify-content:flex-end; gap:1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeQuickDispatchModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Dispatch Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updateDispatchStatus(select) {
        const statusInput = document.getElementById('tripStatus');
        if(select.value) {
            statusInput.value = 'dispatching';
        } else {
            statusInput.value = 'pending';
        }
    }

    function openQuickDispatchModal() {
        document.getElementById('quickDispatchModal').style.display = 'flex';
    }
    function closeQuickDispatchModal() {
        document.getElementById('quickDispatchModal').style.display = 'none';
        document.getElementById('quickDispatchForm').reset();
    }
    // Outside click
    /*
    document.getElementById('quickDispatchModal').addEventListener('click', (e) => {
        if(e.target === document.getElementById('quickDispatchModal')) closeQuickDispatchModal();
    });
    */
</script>
