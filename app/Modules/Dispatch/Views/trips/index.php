<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Layout Grid: Content + Sidebar */
    .dispatch-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.5rem;
        height: calc(100vh - 100px); /* Adjust based on navbar height */
        overflow: hidden;
    }
    .dispatch-main {
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Scroll inside lists */
    }
    .dispatch-sidebar {
        background: var(--bg-surface);
        border-left: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* Stats Bar (Compact) */
    .stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .mini-stat {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        padding: 1rem;
        border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: space-between;
    }
    .mini-stat-label { font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px; }
    .mini-stat-val { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); }

    /* Tabs */
    .tabs-nav {
        display: flex;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1rem;
        background: var(--bg-surface);
        border-radius: var(--radius-sm);
        padding: 0 0.5rem;
    }
    .tab-btn {
        padding: 1rem 1.5rem;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        color: var(--text-secondary);
        font-weight: 600;
        cursor: pointer;
        position: relative;
    }
    .tab-btn:hover { color: var(--text-primary); }
    .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-badge {
        background: var(--danger); color: white;
        font-size: 0.7rem; padding: 2px 6px; border-radius: 10px;
        margin-left: 6px;
    }

    /* Tab Content - Scalable Lists */
    .tab-pane { display: none; flex: 1; overflow-y: auto; padding-right: 4px; }
    .tab-pane.active { display: block; }
    
    /* Trip Item (Premium Card) */
    /* Trip Item (Premium Card) */
    .trip-wrapper {
        margin-bottom: 0.75rem;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        transition: all 0.2s;
    }
    .trip-wrapper:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }
    .trip-card {
        padding: 1rem;
        display: grid;
        grid-template-columns: 80px 1.5fr 1fr 140px; /* Status, Route, Customer, Action */
        gap: 1rem;
        align-items: center;
        cursor: pointer;
    }
    
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    
    .route-visual { position: relative; padding-left: 1.5rem; }
    .route-visual::before {
        content:''; position: absolute; left: 6px; top: 6px; bottom: 6px; width: 2px; background: var(--border-color);
    }
    .route-point { font-size: 0.85rem; margin-bottom: 4px; display: flex; align-items: center; }
    .route-icon { width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--bg-surface); margin-right: 8px; position: absolute; left: 0; }
    
    /* Sidebar Styles */
    .sidebar-header { padding: 1rem; border-bottom: 1px solid var(--border-color); font-weight: 700; display: flex; justify-content: space-between; align-items: center; }
    .driver-list-item {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--border-color);
        display: flex; align-items: center; gap: 0.75rem;
    }
    .driver-status { width: 8px; height: 8px; border-radius: 50%; background: var(--success); }
    .driver-avatar-sm { width: 32px; height: 32px; border-radius: 50%; background: var(--bg-surface-hover); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 600; }

    /* Dropdown Styles */
    .dropdown { position: relative; display: inline-block; }
    .dropdown-menu {
        display: none; position: absolute; right: 0; top: 100%; mt: 4px;
        background-color: var(--bg-surface);
        min-width: 160px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        z-index: 50;
        padding: 4px 0;
    }
    .dropdown-menu.show { display: block; }
    .dropdown-item {
        display: flex; align-items: center; gap: 8px;
        padding: 8px 12px;
        font-size: 0.85rem;
        color: var(--text-primary);
        text-decoration: none;
        cursor: pointer;
        transition: background 0.1s;
        border: none; background: none; width: 100%; text-align: left;
    }
    .dropdown-item:hover { background-color: var(--bg-surface-hover); }
    .dropdown-item i { stroke-width: 1.5px; opacity: 0.7; }
</style>

<div style="padding: 1.5rem; height: 100vh; overflow: hidden; display: flex; flex-direction: column;">
    
    <!-- Top Header -->
    <div style="flex-shrink: 0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="h3" style="margin:0;">Dispatch Board</h1>
            <div style="color:var(--text-secondary); font-size:0.9rem;">Live Operations Console</div>
        </div>
        <div>
            <button onclick="openQuickDispatchModal()" class="btn btn-primary"><i data-lucide="zap" width="16" style="margin-right:6px;"></i> Dispatch</button>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="dispatch-layout">
        
        <!-- Left: Tabbed Lists -->
        <div class="dispatch-main">
            <!-- Filters -->
            <div style="padding: 1rem; background: var(--bg-surface); border-bottom: 1px solid var(--border-color); margin-bottom: 1rem;">
                <form id="filterForm" action="<?= base_url('dispatch/trips') ?>" method="get" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <input type="text" name="search" class="form-control" placeholder="Search trip #, address, name..." value="<?= esc($filters['search'] ?? '') ?>" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                    </div>
                    
                    <div style="width: 150px;">
                        <select name="status" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="dispatching" <?= ($filters['status'] == 'dispatching') ? 'selected' : '' ?>>Dispatching</option>
                            <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="completed" <?= ($filters['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($filters['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                    <div style="width: 180px;">
                        <select name="driver_id" class="form-control" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                            <option value="">All Drivers</option>
                            <?php foreach($drivers as $d): ?>
                                <option value="<?= $d->id ?>" <?= ($filters['driver_id'] == $d->id) ? 'selected' : '' ?>>
                                    <?= esc($d->first_name . ' ' . $d->last_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="width: 150px;">
                        <input type="date" name="date" class="form-control" value="<?= esc($filters['date'] ?? '') ?>" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Search</button>
                    <a href="<?= base_url('dispatch/trips') ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-secondary); border-radius: 4px; display: inline-flex; align-items: center;">Clear</a>
                </form>
            </div>

            <!-- Tabs -->
            <div class="tabs-nav">
                <button class="tab-btn <?= ($active_tab == 'queue') ? 'active' : '' ?>" onclick="switchTab('queue')">
                    Queue 
                    <?php if(count($trips_queue) > 0): ?>
                        <span class="tab-badge"><?= count($trips_queue) ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab-btn <?= ($active_tab == 'active') ? 'active' : '' ?>" onclick="switchTab('active')">
                    Active
                    <?php if(count($trips_active) > 0): ?>
                        <span class="tab-badge" style="background:var(--info);"><?= count($trips_active) ?></span>
                    <?php endif; ?>
                </button>
                <button class="tab-btn <?= ($active_tab == 'history') ? 'active' : '' ?>" onclick="switchTab('history')">History</button>
                <button class="tab-btn <?= ($active_tab == 'all') ? 'active' : '' ?>" onclick="switchTab('all')">All Trips <span class="tab-badge" style="background:var(--text-secondary);"><?= count($trips_all) ?></span></button>
            </div>

            <!-- Content -->
            <div id="tab-queue" class="tab-pane <?= ($active_tab == 'queue') ? 'active' : '' ?>">
                <?php if(empty($trips_queue)): ?>
                    <div class="empty-state" style="text-align:center; padding:3rem; color:var(--text-secondary);">
                        <i data-lucide="check-circle" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                        <p>All caught up! No pending trips.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($trips_queue as $t): ?>
                        <?= view('Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'queue']) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="tab-active" class="tab-pane <?= ($active_tab == 'active') ? 'active' : '' ?>">
                 <?php if(empty($trips_active)): ?>
                    <div class="empty-state" style="text-align:center; padding:3rem; color:var(--text-secondary);">
                        <p>No active trips right now.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($trips_active as $t): ?>
                         <?= view('Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'active']) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="tab-history" class="tab-pane <?= ($active_tab == 'history') ? 'active' : '' ?>">
                 <?php foreach($trips_history as $t): ?>
                     <?= view('Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'history']) ?>
                 <?php endforeach; ?>
            </div>

            <div id="tab-all" class="tab-pane <?= ($active_tab == 'all') ? 'active' : '' ?>">
                 <?php foreach($trips_all as $t): ?>
                     <?= view('Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'all']) ?>
                 <?php endforeach; ?>
            </div>
        </div>

        <!-- Right: Driver Sidebar -->
        <div class="dispatch-sidebar">
            <div class="sidebar-header">
                <span>Available Drivers</span>
                <span style="font-size:0.8rem; background:rgba(16, 185, 129, 0.1); color:var(--success); padding:2px 6px; border-radius:4px;"><?= count($drivers) ?> Online</span>
            </div>
            
            <div style="overflow-y: auto; flex:1;">
                <?php foreach($drivers as $d): ?>
                <div class="driver-list-item">
                    <div class="driver-avatar-sm">
                        <?= substr($d->first_name, 0, 1) . substr($d->last_name, 0, 1) ?>
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:0.9rem; font-weight:600;"><?= esc($d->first_name . ' ' . $d->last_name) ?></div>
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-size:0.75rem; color:var(--text-secondary);"><?= esc($d->vehicle_model) ?></span>
                            <span style="font-size:0.75rem; color:var(--warning); font-weight:bold;">★ <?= number_format($d->rating ?? 0, 1) ?></span>
                        </div>
                    </div>
                    <div class="driver-status"></div>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($drivers)): ?>
                    <div style="padding:1rem; text-align:center; color:var(--text-secondary); font-size:0.9rem;">
                        No drivers available.
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- Quick Assign Modal -->
<div id="assignModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--bg-surface); padding:2rem; border-radius:var(--radius-md); width:400px; box-shadow:var(--shadow-lg);">
        <h3 class="h4" style="margin-bottom:1rem;">Assign Driver</h3>
        <form action="<?= base_url('dispatch/trips/update') ?>/TODO" method="post" id="assignForm">
             <input type="hidden" name="status" value="dispatching"> 
             
             <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Select Driver</label>
                <select name="driver_id" class="form-select" required>
                    <option value="">-- Choose Driver --</option>
                    <?php foreach($drivers as $d): ?>
                        <option value="<?= $d->id ?>"><?= esc($d->first_name . ' ' . $d->last_name) ?> (<?= $d->vehicle_model ?>) - ★ <?= number_format($d->rating ?? 0, 1) ?></option>
                    <?php endforeach; ?>
                </select>
             </div>
             
             <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('assignModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Assign & Dispatch</button>
             </div>
        </form>
    </div>
</div>

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

<!-- Rating Modal -->
<div id="ratingModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--bg-surface); padding:2rem; border-radius:var(--radius-md); width:400px; box-shadow:var(--shadow-lg);">
        <h3 class="h4" style="margin-bottom:1rem;" id="ratingModalTitle">Rate Driver</h3>
        <form action="<?= base_url('dispatch/ratings/submit') ?>" method="post" id="ratingForm">
             <input type="hidden" name="trip_id" id="ratingTripId">
             <input type="hidden" name="rater_type" id="ratingRaterType"> <!-- who is rating? -->
             <input type="hidden" name="rater_id" id="ratingRaterId"> <!-- ID of the rater (opposite party) -->
             
             <div class="form-group" style="margin-bottom:1.5rem; text-align:center;">
                <label class="form-label">Rating</label>
                <div class="star-rating" style="display:flex; justify-content:center; gap:10px; font-size:2rem; cursor:pointer;">
                    <span onclick="setRating(1)" class="star" data-val="1">★</span>
                    <span onclick="setRating(2)" class="star" data-val="2">★</span>
                    <span onclick="setRating(3)" class="star" data-val="3">★</span>
                    <span onclick="setRating(4)" class="star" data-val="4">★</span>
                    <span onclick="setRating(5)" class="star" data-val="5">★</span>
                </div>
                <input type="hidden" name="rating" id="ratingValue" required>
             </div>
             
             <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Comment</label>
                <textarea name="comment" class="form-control" rows="3" placeholder="Any feedback..." style="width:100%; padding:0.5rem; border:1px solid var(--border-color); border-radius:4px;"></textarea>
             </div>
             
             <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeRateModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Rating</button>
             </div>
        </form>
    </div>
</div>

<!-- Dispute Modal -->
<div id="disputeModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--bg-surface); padding:2rem; border-radius:var(--radius-md); width:450px; box-shadow:var(--shadow-lg);">
        <h3 class="h4" style="margin-bottom:1rem;">Report Dispute</h3>
        <form action="<?= base_url('api/disputes/create') ?>" method="post" id="disputeForm" enctype="multipart/form-data">
             <input type="hidden" name="trip_id" id="disputeTripId">
             <input type="hidden" name="customer_id" id="disputeCustomerId">
             <input type="hidden" name="driver_id" id="disputeDriverId">
             <!-- As an admin dispatch action, we'll mark this logically -->
             <input type="hidden" name="reported_by" value="customer"> <!-- Defaults to logging on behalf of customer for now -->
             
             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Report on behalf of</label>
                <select name="reported_by" class="form-select" required>
                    <option value="customer">Customer</option>
                    <option value="driver">Driver</option>
                </select>
             </div>

             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Dispute Type</label>
                <select name="dispute_type" class="form-select" required>
                    <option value="">-- Select Type --</option>
                    <option value="Fare Issue">Fare Issue</option>
                    <option value="Driver Behavior">Driver Behavior</option>
                    <option value="Customer Behavior">Customer Behavior</option>
                    <option value="Lost Item">Lost Item</option>
                    <option value="App Error">App Error</option>
                    <option value="Other">Other</option>
                </select>
             </div>

             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required placeholder="E.g. Driver refused to end trip" style="width:100%; padding:0.5rem; border:1px solid var(--border-color); border-radius:4px;">
             </div>
             
             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required placeholder="Provide details..." style="width:100%; padding:0.5rem; border:1px solid var(--border-color); border-radius:4px;"></textarea>
             </div>

             <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Attachment (Optional)</label>
                <input type="file" name="attachment" class="form-control" accept="image/*,.pdf,.doc,.docx" style="width:100%; padding:0.5rem; border:1px solid var(--border-color); border-radius:4px; cursor:pointer;">
             </div>
             
             <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeDisputeModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">File Dispute</button>
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

    function switchTab(tabId) {
        // Hide all
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        // Show active
        document.getElementById('tab-' + tabId).style.display = 'block';
        event.currentTarget.classList.add('active');
    }

    function openAssignModal(tripId) {
        const modal = document.getElementById('assignModal');
        const form = document.getElementById('assignForm');
        form.action = '<?= base_url("dispatch/trips/update") ?>/' + tripId;
        modal.style.display = 'flex';
    }
    
    // Auto-close modals on outside click
    window.onclick = function(e) {
        const m = document.getElementById('assignModal');
        if(e.target == m) m.style.display = 'none';

        const rm = document.getElementById('ratingModal');
        if(e.target == rm) rm.style.display = 'none';
        
        const dm = document.getElementById('disputeModal');
        if(e.target == dm) dm.style.display = 'none';

        const qd = document.getElementById('quickDispatchModal');
        if(e.target == qd) closeQuickDispatchModal();

        // Close Dropdowns if clicked outside
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(d => d.classList.remove('show'));
        }
    }
    
    function toggleDropdown(btn) {
        // close others
        document.querySelectorAll('.dropdown-menu').forEach(d => {
            if(d !== btn.nextElementSibling) d.classList.remove('show');
        });
        btn.nextElementSibling.classList.toggle('show');
    }
</script>

<script>
    function openRateModal(tripId, rateWho, rateeId, raterId) {
        // rateWho: 'driver' or 'customer' (who we are giving stars TO)
        // rateeId: ID of who we are rating
        // raterId: ID of who is GIVING the rating (opposite party)
        
        const modal = document.getElementById('ratingModal');
        const title = document.getElementById('ratingModalTitle');
        const form = document.getElementById('ratingForm');
        
        document.getElementById('ratingTripId').value = tripId;
        
        if (rateWho === 'driver') {
            title.innerText = "Rate Driver";
            document.getElementById('ratingRaterType').value = 'customer'; // Customer rates driver
            document.getElementById('ratingRaterId').value = raterId;      // Customer ID
        } else {
            title.innerText = "Rate Customer";
            document.getElementById('ratingRaterType').value = 'driver'; // Driver rates customer
            document.getElementById('ratingRaterId').value = raterId;    // Driver ID
        }

        modal.style.display = 'flex';
        resetStars();
    }
    
    function closeRateModal() {
        document.getElementById('ratingModal').style.display = 'none';
        // Auto Close assign modal too if open ? no
    }

    // Star Rating Logic
    function setRating(val) {
        document.getElementById('ratingValue').value = val;
        const stars = document.querySelectorAll('.star');
        stars.forEach(s => {
            if (s.getAttribute('data-val') <= val) {
                s.style.color = '#eab308'; // yellow
            } else {
                s.style.color = '#ccc';
            }
        });
    }
    
    function resetStars() {
        document.getElementById('ratingValue').value = '';
        document.querySelectorAll('.star').forEach(s => s.style.color = '#ccc');
    }

    // Handle Form Submit via AJAX
    document.getElementById('ratingForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Basic validation
        if(!document.getElementById('ratingValue').value) {
            alert('Please select a star rating.');
            return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Rating submitted successfully!');
                closeRateModal();
                location.reload(); 
            } else {
                alert(data.message || 'Error submitting rating');
                submitBtn.disabled = false;
                submitBtn.innerText = 'Submit Rating';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Request failed');
            submitBtn.disabled = false;
            submitBtn.innerText = 'Submit Rating';
        });
    });

    // Handle Filter Form Submit via AJAX
    const filterForm = document.getElementById('filterForm');
    if(filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const url = new URL(this.action);
            const formData = new FormData(this);
            // Append form data to URL search params
            for (const [key, value] of formData) {
                if(value) url.searchParams.set(key, value);
            }

            // Update URL bar
            history.pushState(null, '', url);

            // Fetch Data
            fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    document.getElementById('tab-queue').innerHTML = data.html_queue;
                    document.getElementById('tab-active').innerHTML = data.html_active;
                    document.getElementById('tab-history').innerHTML = data.html_history;
                    document.getElementById('tab-all').innerHTML = data.html_all;
                    
                    // Simple badge update logic (assuming badges exist or need minimal toggling)
                    updateTabBadge('queue', data.count_queue);
                    updateTabBadge('active', data.count_active);
                    updateTabBadge('all', data.count_all);

                    lucide.createIcons();
                }
            })
            .catch(console.error);
        });
    }
    
    function updateTabBadge(type, count) {
        // Find the button with correct onclick
        const btn = document.querySelector(`button[onclick="switchTab('${type}')"]`);
        if(!btn) return;
        
        let badge = btn.querySelector('.tab-badge');
        if(count > 0) {
            if(!badge) {
                badge = document.createElement('span');
                badge.className = 'tab-badge';
                if(type === 'active') badge.style.background = 'var(--info)';
                if(type === 'all') badge.style.background = 'var(--text-secondary)';
                btn.appendChild(badge);
            }
            badge.innerText = count;
            badge.style.display = 'inline-block';
        } else {
            if(badge) badge.style.display = 'none';
        }
    }

    function toggleTripDetails(tripId) {
        const detailsEl = document.getElementById('trip-details-' + tripId);
        const wrapperEl = document.getElementById('trip-wrapper-' + tripId);
        if(!detailsEl) return;
        
        if (detailsEl.style.display === 'none') {
            detailsEl.style.display = 'block';
            if(wrapperEl) wrapperEl.style.borderColor = 'var(--primary)';
        } else {
            detailsEl.style.display = 'none';
            if(wrapperEl) wrapperEl.style.borderColor = 'var(--border-color)';
        }
    }

    function openDisputeModal(tripId, customerId, driverId) {
        const modal = document.getElementById('disputeModal');
        document.getElementById('disputeTripId').value = tripId;
        document.getElementById('disputeCustomerId').value = customerId !== null ? customerId : '';
        document.getElementById('disputeDriverId').value = driverId !== null ? driverId : '';
        modal.style.display = 'flex';
    }

    function closeDisputeModal() {
        document.getElementById('disputeModal').style.display = 'none';
    }

    // Handle Dispute Form Submit via AJAX
    document.getElementById('disputeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Submitting...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                "X-Requested-With": "XMLHttpRequest"
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                alert('Dispute submitted successfully!');
                closeDisputeModal();
                this.reset();
            } else {
                alert(data.message || 'Error submitting dispute');
            }
            submitBtn.disabled = false;
            submitBtn.innerText = 'File Dispute';
        })
        .catch(err => {
            console.error(err);
            alert('Request failed');
            submitBtn.disabled = false;
            submitBtn.innerText = 'File Dispute';
        });
    });
</script>

<?= $this->endSection() ?>
