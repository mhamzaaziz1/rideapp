<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Layout Grid: Content + Sidebar */
    .dispatch-layout {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        flex: 1; /* Allow to fill remaining space in the viewport */
        min-height: 0; /* Important for flex children to be scrollable */
        overflow: hidden;
    }
    .dispatch-main {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
        overflow: hidden; /* The actual scrolling will happen inside .tab-pane */
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
    .tab-pane { display: none; flex: 1; overflow-y: auto; padding-right: 4px; min-height: 0; }
    .tab-pane.active { display: flex; flex-direction: column; }
    
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
        grid-template-columns: 40px 80px 1.5fr 1.25fr 100px 130px; /* Checkbox, Status, Route, Customer/Driver, Price, Action */
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

    /* Rating Star Styles */
    .modal-star {
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-tertiary);
    }
    .modal-star:hover, .modal-star.active {
        color: var(--warning);
        transform: scale(1.1);
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

                    <div style="display:flex; gap:0.5rem; align-items:center;">
                        <input type="date" name="from_date" class="form-control" title="From Date" value="<?= esc($filters['from_date'] ?? '') ?>" style="width: 140px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                        <span style="color:var(--text-secondary); font-size:0.8rem;">to</span>
                        <input type="date" name="to_date" class="form-control" title="To Date" value="<?= esc($filters['to_date'] ?? '') ?>" style="width: 140px; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: 4px;">
                    </div>

                    <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1rem;">Search</button>
                    <a href="<?= base_url('dispatch/trips') ?>" class="btn btn-outline" style="padding: 0.5rem 1rem; text-decoration: none; border: 1px solid var(--border-color); color: var(--text-secondary); border-radius: 4px; display: inline-flex; align-items: center;">Clear</a>
                    
                    <button id="bulkPrintTripBtn" type="button" onclick="bulkPrintSelectedTrips()" class="btn btn-outline" style="display:none; align-items:center; gap:6px; padding: 0.5rem 1rem; font-weight: 600; border-color: var(--primary); color: var(--primary);">
                        <i data-lucide="printer" width="16"></i> Bulk Print (<span id="selectedTripCount">0</span>)
                    </button>
                </form>
            </div>

            <!-- Global Selection Toggle -->
            <div style="padding: 0 1rem; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px; font-size: 0.85rem; color: var(--text-secondary);">
                <input type="checkbox" id="selectAllTrips" onclick="toggleAllTrips(this)">
                <label for="selectAllTrips" style="cursor: pointer; margin: 0;">Select All on Current View</label>
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

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" style="z-index: 2000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: var(--bg-surface);">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-800" id="rate-modal-title" style="font-size: 1.25rem;">Rate Participant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeRateModal()"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="rate-trip-id">
                <input type="hidden" id="ratee-type">
                <input type="hidden" id="ratee-id">

                <div class="text-center mb-4">
                    <div id="rate-modal-subtitle" style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.25rem;">Share feedback about <strong>TRP-<span id="lbl-rate-trip-num"></span></strong></div>
                    <div class="star-rating justify-content-center" id="modal-star-group" style="font-size: 2.25rem; gap: 12px; display: flex;">
                        <span data-value="1" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="2" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="3" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="4" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="5" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                    </div>
                </div>

                <div class="premium-input-group mb-4">
                    <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 8px; display: block;">Review & Comment</label>
                    <textarea id="rate-comment" class="form-control border-0 bg-light-subtle" placeholder="What was your experience with this participant?" style="height: 120px; padding: 15px; border-radius: 12px; resize: none; width: 100%; border: 1px solid var(--border-color);"></textarea>
                </div>

                <button type="button" class="btn btn-primary w-100 py-3 fw-800" id="btn-submit-rating" style="border-radius: 12px; font-size: 1rem; letter-spacing: 0.02em;">Submit Rating</button>
            </div>
        </div>
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
             
             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" placeholder="e.g. Fare disagreement" required style="width:100%; padding:0.5rem; border:1px solid var(--border-color); border-radius:4px;">
             </div>

             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Tell us what happened..." required style="width:100%; padding:0.5rem; border:1px solid var(--border-color); border-radius:4px;"></textarea>
             </div>

             <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label">Attachments (Optional)</label>
                <input type="file" name="attachments[]" multiple class="form-control" style="width:100%;">
             </div>
             
             <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeDisputeModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">File Dispute</button>
             </div>
        </form>
    </div>
</div>

<!-- Trip Details Modal -->
<div id="tripDetailsModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:var(--bg-surface); padding:0; border-radius:var(--radius-md); width:600px; max-height:90vh; overflow-y:auto; box-shadow:var(--shadow-lg);">
        <div class="sidebar-header" style="background:var(--bg-surface-hover); border-bottom:1px solid var(--border-color); position:sticky; top:0; z-index:10;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span id="mdTripNumber" class="h4" style="margin:0;">#TRP-XXXX</span>
                <span id="mdTripStatus" class="status-badge">Status</span>
            </div>
            <button onclick="closeTripDetailsModal()" style="background:none; border:none; cursor:pointer;"><i data-lucide="x" width="20"></i></button>
        </div>
        
        <div style="padding:1.5rem;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; margin-bottom:0.5rem;">Customer</div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div class="driver-avatar-sm" style="width:40px; height:40px;">C</div>
                        <div>
                            <div id="mdCustomerName" style="font-weight:700;">Name</div>
                            <div id="mdCustomerPhone" style="font-size:0.85rem; color:var(--text-secondary);">Phone</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; margin-bottom:0.5rem;">Driver</div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div class="driver-avatar-sm" style="width:40px; height:40px; background:var(--primary-subtle); color:var(--primary);">D</div>
                        <div>
                            <div id="mdDriverName" style="font-weight:700;">Unassigned</div>
                            <div id="mdDriverVehicle" style="font-size:0.85rem; color:var(--text-secondary);">Vehicle</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background:var(--bg-surface-hover); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem;">
                <div style="margin-bottom:1rem;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <i data-lucide="map-pin" width="14" style="color:var(--success);"></i>
                        <span style="font-size:0.75rem; color:var(--text-secondary); font-weight:700;">PICKUP</span>
                    </div>
                    <div id="mdPickupAddress" style="padding-left:22px; font-size:0.9rem;">Address</div>
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <i data-lucide="navigation" width="14" style="color:var(--danger);"></i>
                        <span style="font-size:0.75rem; color:var(--text-secondary); font-weight:700;">DROPOFF</span>
                    </div>
                    <div id="mdDropoffAddress" style="padding-left:22px; font-size:0.9rem;">Address</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem; border-top:1px solid var(--border-color); pt-1.5rem; padding-top:1.5rem;">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:4px;">Date/Time</div>
                    <div id="mdDateTime" style="font-weight:600;">Date</div>
                    <div id="mdTimeOnly" style="font-size:0.85rem; color:var(--text-secondary);">Time</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:4px;">Fare & Method</div>
                    <div id="mdPrice" style="font-weight:700; color:var(--primary);">Amount</div>
                    <div id="mdPayment" style="font-size:0.85rem; color:var(--text-secondary);">Cash</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-secondary); margin-bottom:4px;">Trip Stats</div>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span id="mdDistance" style="font-weight:600;">Distance</span>
                        <span id="mdDurationOnly" style="font-size:0.85rem; color:var(--text-secondary);">Duration</span>
                    </div>
                </div>
            </div>

            <div style="margin-top:1.5rem; border-top:1px solid var(--border-color); padding-top:1.5rem;">
                <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; margin-bottom:0.5rem;">Dispatcher Notes</div>
                <p id="mdNotes" style="font-size:0.9rem; font-style:italic;">No notes.</p>
            </div>
        </div>
    </div>
</div>

<?= view('Modules\\Dispatch\\Views\\trips\\_quick_dispatch_modal') ?>

<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        
        document.getElementById('tab-' + tabId).classList.add('active');
        const activeBtn = document.querySelector(`.tab-btn[onclick*="${tabId}"]`);
        if(activeBtn) activeBtn.classList.add('active');

        // Reset selection when switching tabs
        document.getElementById('selectAllTrips').checked = false;
        document.querySelectorAll('.trip-checkbox').forEach(cb => cb.checked = false);
        updateTripBulkBtn();
    }

    function toggleAllTrips(source) {
        const activePane = document.querySelector('.tab-pane.active');
        const checkboxes = activePane.querySelectorAll('.trip-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateTripBulkBtn();
    }

    function updateTripBulkBtn() {
        const checkboxes = document.querySelectorAll('.trip-checkbox:checked');
        const bulkBtn = document.getElementById('bulkPrintTripBtn');
        const countSpan = document.getElementById('selectedTripCount');
        
        if (bulkBtn) {
            if (checkboxes.length > 0) {
                bulkBtn.style.display = 'inline-flex';
                countSpan.innerText = checkboxes.length;
            } else {
                bulkBtn.style.display = 'none';
            }
        }
    }

    function bulkPrintSelectedTrips() {
        const checkboxes = document.querySelectorAll('.trip-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
        if (ids) {
            window.open('<?= base_url('dispatch/trips/bulk_print') ?>?ids=' + ids, '_blank');
        }
    }

    function openAssignModal(tripId) {
        const modal = document.getElementById('assignModal');
        const form = document.getElementById('assignForm');
        form.action = '<?= base_url('dispatch/trips/update') ?>/' + tripId;
        modal.style.display = 'flex';
    }

    // Handle AJAX filtering
    if(document.getElementById('filterForm')) {
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const url = new URL(this.action);
            const formData = new FormData(this);
            for (const [key, value] of formData) {
                if(value) url.searchParams.set(key, value);
            }

            history.pushState(null, '', url);

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
        const btn = document.querySelector(`.tab-btn[onclick*="${type}"]`);
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
        document.getElementById('mdDurationOnly').textContent = Math.round(dist * 2.5) + ' min';
        
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
        document.querySelectorAll('.dropdown-menu').forEach(m => m.classList.remove('show'));
        document.querySelectorAll('.trip-wrapper').forEach(w => w.style.zIndex = '1');
        if (!isOpen) {
            menu.classList.add('show');
            if (wrapper) wrapper.style.zIndex = '100';
            lucide.createIcons();
        }
    }

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

    // Rating Logic Consolidated
    window.openRateModal = (t, type) => {
        if (type === 'driver' && !t.driver_id) {
            alert("This trip has no driver assigned yet.");
            return;
        }

        document.getElementById('rate-trip-id').value = t.id;
        document.getElementById('lbl-rate-trip-num').innerText = t.trip_number;
        document.getElementById('ratee-type').value = type;
        document.getElementById('ratee-id').value = (type === 'driver') ? t.driver_id : t.customer_id;
        document.getElementById('rate-modal-title').innerText = "Rate " + (type.charAt(0).toUpperCase() + type.slice(1));
        
        document.getElementById('rate-comment').value = '';
        document.querySelectorAll('.modal-star').forEach(s => s.classList.remove('active'));
        
        const modalEl = document.getElementById('ratingModal');
        let modal = bootstrap.Modal.getInstance(modalEl);
        if(!modal) modal = new bootstrap.Modal(modalEl);
        modal.show();
    };

    function closeRateModal() {
        const modalEl = document.getElementById('ratingModal');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if(modal) modal.hide();
    }

    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-star')) {
            const val = e.target.dataset.value;
            document.querySelectorAll('.modal-star').forEach(s => {
                if (parseInt(s.dataset.value) <= parseInt(val)) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        }
    });

    const btnSubmitRate = document.getElementById('btn-submit-rating');
    if (btnSubmitRate) {
        btnSubmitRate.addEventListener('click', () => {
            const tripId = document.getElementById('rate-trip-id').value;
            const type = document.getElementById('ratee-type').value;
            const id = document.getElementById('ratee-id').value;
            const comment = document.getElementById('rate-comment').value;
            const activeStars = document.querySelectorAll('.modal-star.active');
            const val = activeStars.length > 0 ? activeStars[activeStars.length-1].dataset.value : 0;

            if (val == 0) { alert("Please select a star rating."); return; }

            btnSubmitRate.disabled = true;
            btnSubmitRate.innerText = "Submitting...";

            const payload = {
                trip_id: tripId,
                rating: val,
                ratee_type: type,
                ratee_id: id,
                rater_type: (type === 'customer') ? 'driver' : 'customer',
                rater_id: 0,
                comment: comment
            };

            const subAction = "<?= base_url('dispatch/ratings/submit') ?>";
            fetch(subAction, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    alert('Rating submitted successfully!');
                    closeRateModal();
                    location.reload(); 
                } else {
                    alert(data.message || 'Error submitting rating');
                    btnSubmitRate.disabled = false;
                    btnSubmitRate.innerText = 'Submit Rating';
                }
            })
            .catch(err => {
                console.error(err);
                btnSubmitRate.disabled = false;
                btnSubmitRate.innerText = 'Submit Rating';
            });
        });
    }

    window.updateTripStatus = (id, newStatus) => {
        if(!confirm(`Are you sure you want to mark this trip as ${newStatus}?`)) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', newStatus);

        fetch("<?= base_url('dispatch/trips/update_status') ?>", {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'Failed to update status');
            }
        })
        .catch(console.error);
    };
</script>

<?= $this->endSection() ?>
