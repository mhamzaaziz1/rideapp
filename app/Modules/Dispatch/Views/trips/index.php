<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Layout Grid: Content + Sidebar */
    .dispatch-layout {
        display: block;
        gap: 1.5rem;
        height: calc(100vh - 100px); /* Adjust based on navbar height */
        overflow: hidden;
    }
    .dispatch-main {
        display: flex;
        flex-direction: column;
        overflow: hidden; /* Scroll inside lists */
    }
    /* Sidebar removed as per simplification request */

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
        position: relative;
        z-index: 1;
    }
    .trip-wrapper:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-sm);
    }
    .trip-card {
        padding: 1rem;
        display: grid;
        grid-template-columns: 80px 1.5fr 1.25fr 100px 130px; /* Status, Route, Customer/Driver, Price, Action */
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
        display: none; position: absolute; right: 0; top: 100%; margin-top: 4px;
        background-color: var(--bg-surface);
        min-width: 160px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        z-index: 100;
        padding: 4px 0;
        transform-origin: top right;
        animation: dropdownFadeIn 0.2s ease-out;
    }
    @keyframes dropdownFadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .dropdown-menu.show { display: block; }
    .dropdown-item {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 16px;
        font-size: 0.875rem;
        color: var(--text-primary);
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s;
        border: none; background: none; width: 100%; text-align: left;
        font-weight: 500;
    }
    .dropdown-item:hover { background-color: var(--bg-surface-hover); padding-left: 20px; }
    .dropdown-item i { stroke-width: 1.5px; opacity: 0.6; }
    .dropdown-item.text-danger { color: var(--danger); }
    .dropdown-item.text-danger:hover { background-color: rgba(239, 68, 68, 0.05); }

    /* Modal Animations */
    .modal-overlay {
        animation: fadeIn 0.3s ease-out;
    }
    .modal-content {
        animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { 
        from { opacity: 0; transform: translateY(20px) scale(0.95); } 
        to { opacity: 1; transform: translateY(0) scale(1); } 
    }

    /* Trip Card Interactivity */
    .trip-card:active {
        transform: scale(0.995);
        background: var(--bg-surface-hover);
    }
</style>

<div style="padding: 1.5rem; height: 100vh; overflow: hidden; display: flex; flex-direction: column;">
    
    <!-- Top Header -->
    <div style="flex-shrink: 0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="h3" style="margin:0;">Dispatch Board</h1>
            <div style="color:var(--text-secondary); font-size:0.9rem;">Live Operations Console</div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="openQuickDispatchModal()" class="btn btn-primary">
                <i data-lucide="zap" width="16" style="margin-right:6px;"></i> Dispatch
            </button>
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

        <!-- Right: Driver Sidebar Removed -->
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

<?= view('Modules\Dispatch\Views\trips\_quick_dispatch_modal') ?>
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

<!-- Trip Details Modal -->
<div id="tripDetailsModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1300; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
    <div class="modal-content" style="background:var(--bg-surface); border-radius:16px; width:540px; max-width:95vw; box-shadow:var(--shadow-lg); border:1px solid var(--border-color); overflow:hidden; position:relative;">
        <!-- Modal Header -->
        <div style="padding:1.5rem 1.5rem 0.5rem; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:12px;">
                <h3 id="mdTripNumber" style="margin:0; font-size:1.5rem; font-weight:800; color:var(--text-primary); letter-spacing:-0.02em;">TRP-000000</h3>
                <span id="mdTripStatus" class="status-badge" style="padding:4px 12px; border-radius:8px; font-weight:700; font-size:0.75rem;">Pending</span>
            </div>
            <button onclick="closeTripDetailsModal()" style="background:var(--bg-body); border:1px solid var(--border-color); cursor:pointer; color:var(--text-secondary); border-radius:8px; width:32px; height:32px; display:flex; align-items:center; justify-content:center;">&times;</button>
        </div>

        <div style="padding:1.5rem; display:flex; flex-direction:column; gap:1.5rem;">
            <!-- Customer & Driver Row -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.25rem;">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; font-weight:700; margin-bottom:8px; letter-spacing:0.02em;">Customer</div>
                    <div style="background:var(--bg-body); padding:1.25rem; border-radius:12px; border:1px solid var(--border-color);">
                        <div id="mdCustomerName" style="font-weight:700; font-size:1.1rem; color:var(--text-primary);">John Doe</div>
                        <div style="display:flex; align-items:center; gap:6px; color:var(--text-secondary); margin-top:6px; font-size:0.9rem;">
                            <i data-lucide="phone" width="14"></i>
                            <span id="mdCustomerPhone">+1 (555) 000-0000</span>
                        </div>
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; font-weight:700; margin-bottom:8px; letter-spacing:0.02em;">Driver</div>
                    <div style="background:var(--bg-body); padding:1.25rem; border-radius:12px; border:1px solid var(--border-color);">
                        <div id="mdDriverName" style="font-weight:700; font-size:1.1rem; color:var(--text-primary);">Unassigned</div>
                        <div style="display:flex; align-items:center; gap:6px; color:var(--text-secondary); margin-top:6px; font-size:0.9rem;">
                            <i data-lucide="car" width="14"></i>
                            <span id="mdDriverVehicle">—</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Route Info -->
            <div>
                <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; font-weight:700; margin-bottom:8px; letter-spacing:0.02em;">Route</div>
                <div style="background:var(--bg-body); padding:1.25rem; border-radius:12px; border:1px solid var(--border-color); display:flex; flex-direction:column; gap:1rem;">
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:24px; height:24px; border-radius:50%; background:rgba(16, 185, 129, 0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i data-lucide="map-pin" width="14" style="color:var(--success);"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem; color:var(--text-secondary); font-weight:600;">Pickup</div>
                            <div id="mdPickupAddress" style="font-size:0.95rem; color:var(--text-primary); font-weight:500;">Pickup Address Area</div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:flex-start; gap:12px;">
                        <div style="width:24px; height:24px; border-radius:50%; background:rgba(239, 68, 68, 0.1); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i data-lucide="map-pin" width="14" style="color:var(--danger);"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem; color:var(--text-secondary); font-weight:600;">Dropoff</div>
                            <div id="mdDropoffAddress" style="font-size:0.95rem; color:var(--text-primary); font-weight:500;">Dropoff Address Area</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:1rem; border-top:1px solid var(--border-color); padding-top:1.5rem;">
                <div>
                    <div style="font-size:0.7rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Date & Time</div>
                    <div id="mdDateTime" style="font-weight:700; font-size:1rem; color:var(--text-primary);">2024-01-01</div>
                    <div style="font-size:0.85rem; color:var(--text-secondary);" id="mdTimeOnly">00:00</div>
                </div>
                <div>
                    <div style="font-size:0.7rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Distance</div>
                    <div id="mdDistance" style="font-weight:700; font-size:1rem; color:var(--text-primary);">0.0 miles</div>
                    <div style="font-size:0.85rem; color:var(--text-secondary);" id="mdDurationOnly">—</div>
                </div>
                <div>
                    <div style="font-size:0.7rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Price</div>
                    <div id="mdPrice" style="font-weight:800; font-size:1.25rem; color:var(--info);">$0.00</div>
                </div>
                <div>
                    <div style="font-size:0.7rem; color:var(--text-secondary); font-weight:600; text-transform:uppercase; margin-bottom:4px;">Payment</div>
                    <div style="display:flex; align-items:center; gap:4px; font-weight:700; font-size:0.9rem; background:var(--bg-body); padding:4px 8px; border-radius:6px; border:1px solid var(--border-color); width:fit-content;">
                        <i data-lucide="credit-card" width="14"></i>
                        <span id="mdPayment">Card</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; font-weight:700; margin-bottom:8px; letter-spacing:0.02em;">Notes</div>
                <div id="mdNotes" style="background:var(--bg-body); padding:1rem; border-radius:12px; border:1px solid var(--border-color); font-size:0.9rem; color:var(--text-primary); line-height:1.5; min-height:60px;">No notes provided.</div>
            </div>
        </div>
    </div>
</div>


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

    function openTripDetailsModal(trip) {
        document.getElementById('mdTripNumber').textContent = '#' + trip.trip_number;
        
        const statusEl = document.getElementById('mdTripStatus');
        statusEl.textContent = (trip.status || 'Pending').charAt(0).toUpperCase() + (trip.status || 'pending').slice(1);
        statusEl.className = 'status-badge status-' + (trip.status || 'pending');

        document.getElementById('mdCustomerName').textContent = (trip.c_first || 'Guest') + ' ' + (trip.c_last || '');
        document.getElementById('mdCustomerPhone').textContent = trip.c_phone || 'No phone';
        
        document.getElementById('mdDriverName').textContent = trip.d_first ? (trip.d_first + ' ' + (trip.d_last || '')) : 'Unassigned';
        document.getElementById('mdDriverVehicle').textContent = trip.vehicle_model || (trip.driver_id ? 'Vehicle Loading...' : 'No vehicle');

        document.getElementById('mdPickupAddress').textContent = trip.pickup_address || '—';
        document.getElementById('mdDropoffAddress').textContent = trip.dropoff_address || '—';

        const date = new Date(trip.created_at);
        document.getElementById('mdDateTime').textContent = date.toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'});
        document.getElementById('mdTimeOnly').textContent = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        const dist = parseFloat(trip.distance_miles) || 0;
        document.getElementById('mdDistance').textContent = dist.toFixed(1) + ' miles';
        document.getElementById('mdDurationOnly').textContent = Math.round(dist * 2.5) + ' min'; // Estimated duration
        
        document.getElementById('mdPrice').textContent = '$' + (parseFloat(trip.fare_amount) || 0).toFixed(2);
        document.getElementById('mdPayment').textContent = (trip.payment_method || 'Cash').charAt(0).toUpperCase() + (trip.payment_method || 'cash').slice(1);
        
        document.getElementById('mdNotes').textContent = trip.notes || 'No notes provided.';

        document.getElementById('tripDetailsModal').style.display = 'flex';
        lucide.createIcons();
    }

    function toggleDropdown(btn) {
        const menu = btn.nextElementSibling;
        const wrapper = btn.closest('.trip-wrapper');
        const isOpen = menu.classList.contains('show');
        
        // Close all other dropdowns and reset z-indexes
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.trip-wrapper').forEach(w => w.style.zIndex = '1');
        
        if (!isOpen) {
            menu.classList.add('show');
            if (wrapper) wrapper.style.zIndex = '100';
            lucide.createIcons();
        }
    }

    // Close dropdowns on outside click
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
            document.querySelectorAll('.trip-wrapper').forEach(w => w.style.zIndex = '1');
        }
    });

    function closeTripDetailsModal() {
        document.getElementById('tripDetailsModal').style.display = 'none';
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
