<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --bg-body-new: #f8f9fc;
        --border-light: #e5e7eb;
        --text-dark: #111827;
        --text-gray: #6b7280;
        --primary-blue: #3b82f6;
        --primary-blue-hover: #2563eb;
        --success-bg: #dcfce7;
        --success-text: #166534;
        --success-border: #bbf7d0;
        --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    
    body { background-color: var(--bg-body-new); font-family: 'Inter', sans-serif; }
    .main-content { background-color: var(--bg-body-new); }

    /* Header */
    .page-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 2rem;
    }
    .page-title { font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem; }
    .page-desc { color: var(--text-gray); font-size: 0.95rem; }
    
    .btn-add {
        background-color: var(--primary-blue); color: white;
        border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.5rem; border: none;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        transition: background 0.2s;
        cursor: pointer;
        text-decoration: none;
    }
    .btn-add:hover { background-color: var(--primary-blue-hover); color: white; text-decoration: none; }

    /* Stats Cards */
    .stats-container {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;
    }
    .stat-card {
        background: #ffffff; border-radius: 12px; padding: 1.5rem;
        box-shadow: var(--shadow-card); border: 1px solid var(--border-light);
        display: flex; flex-direction: column; position: relative;
    }
    .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .stat-title { font-size: 0.95rem; font-weight: 600; color: var(--text-dark); }
    .stat-icon {
        width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
    }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-orange { background: #fff7ed; color: #f97316; }
    .icon-green { background: #f0fdf4; color: #22c55e; }
    .icon-purple { background: #f3e8ff; color: #a855f7; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-dark); line-height: 1.2; }
    .stat-sub { font-size: 0.8rem; color: var(--text-gray); display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; }
    .mini-chart { flex-grow: 1; height: 2px; background: #cbd5e1; border-radius: 2px; position: relative; }
    .mini-chart::after { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 60%; background: var(--primary-blue); border-radius: 2px; }

    /* Table Container */
    .table-container {
        background: #ffffff; border-radius: 12px; box-shadow: var(--shadow-card); border: 1px solid var(--border-light); padding: 1.5rem;
    }

    /* Toolbar */
    .toolbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
    .search-wrapper { position: relative; width: 320px; }
    .search-input {
        width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid var(--border-light);
        border-radius: 8px; font-size: 0.95rem; color: var(--text-dark); background: white;
    }
    .search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-gray); }
    .filter-input {
        padding: 0.6rem 1rem; border: 1px solid var(--border-light);
        border-radius: 8px; font-size: 0.95rem; color: var(--text-dark); background: white; height: 42px;
    }
    
    .toolbar-actions { display: flex; gap: 0.75rem; }
    .btn-outline-action {
        border: 1px solid var(--border-light); background: #ffffff; color: var(--text-dark);
        border-radius: 8px; padding: 0.6rem 1rem; font-size: 0.9rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: background 0.2s;
    }
    .btn-outline-action:hover { background: #f8fafc; }

    /* Table */
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { 
        text-align: left; padding: 1rem 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); 
        border-bottom: 1px solid var(--border-light); border-top: 1px solid var(--border-light);
    }
    .custom-table td { padding: 1.25rem 0.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .custom-table tr:last-child td { border-bottom: none; }
    .custom-table tr:hover { background: #f8fafc; cursor: pointer; }
    
    .text-muted { color: var(--text-gray); font-size: 0.85rem; }
    
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem;
        border-radius: 16px; font-size: 0.8rem; font-weight: 600; cursor: pointer; text-transform: capitalize;
    }
    .status-active, .status-completed { background: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border); }
    .status-pending { background: #fef08a; color: #854d0e; border: 1px solid #fef08a; }
    .status-dispatching { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
    .status-cancelled { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    
    /* Tabs */
    .tabs-nav { display: flex; border-bottom: 1px solid var(--border-light); margin-bottom: 1.5rem; }
    .tab-btn { padding: 0.75rem 1.5rem; background: none; border: none; font-weight: 600; color: var(--text-gray); cursor: pointer; border-bottom: 2px solid transparent; }
    .tab-btn.active { color: var(--primary-blue); border-bottom-color: var(--primary-blue); }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .tab-badge { background: #cbd5e1; color: white; padding: 2px 6px; border-radius: 12px; font-size: 0.75rem; margin-left: 6px; }

    /* Modals & Dropdowns from original */
    .dropdown { position: relative; display: inline-block; }
    .dropdown-menu { display: none; position: absolute; right: 0; top: 100%; margin-top: 4px; background-color: #fff; min-width: 160px; box-shadow: var(--shadow-card); border: 1px solid var(--border-light); border-radius: 8px; z-index: 100; padding: 4px 0; }
    .dropdown-menu.show { display: block; }
    .dropdown-item { display: flex; align-items: center; gap: 10px; padding: 10px 16px; font-size: 0.875rem; color: var(--text-dark); text-decoration: none; cursor: pointer; border: none; background: none; width: 100%; text-align: left; font-weight: 500; }
    .dropdown-item:hover { background-color: #f8fafc; }
    .dropdown-item.text-danger { color: #ef4444; }
    
    .modal-overlay { animation: fadeIn 0.3s ease-out; }
    .modal-content { animation: slideUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1); }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .modal-star { cursor: pointer; transition: all 0.2s; color: #cbd5e1; }
    .modal-star:hover, .modal-star.active { color: #eab308; transform: scale(1.1); }
</style>

<div style="padding: 2.5rem;">
    
    <div class="page-header">
        <div>
            <h1 class="page-title">Dispatch Board</h1>
            <div class="page-desc">Live Operations Console</div>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <button onclick="openQuickDispatchModal()" class="btn-add">
                <i data-lucide="zap" width="16"></i> Dispatch
            </button>
        </div>
    </div>

    <!-- Main Layout -->
    <div>
        <div>
            <!-- Stats -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Pending</div>
                        <div class="stat-icon icon-orange"><i data-lucide="clock" width="18"></i></div>
                    </div>
                    <div class="stat-value"><?= count($trips_queue) ?></div>
                    <div class="stat-sub">Awaiting assignment <div class="mini-chart"></div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Active</div>
                        <div class="stat-icon icon-blue"><i data-lucide="activity" width="18"></i></div>
                    </div>
                    <div class="stat-value"><?= count($trips_active) ?></div>
                    <div class="stat-sub">Currently en-route <div class="mini-chart"></div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Completed (Today)</div>
                        <div class="stat-icon icon-green"><i data-lucide="check-circle" width="18"></i></div>
                    </div>
                    <div class="stat-value"><?= count(array_filter($trips_history, fn($t) => $t->status == 'completed')) ?></div>
                    <div class="stat-sub">Successfully finished <div class="mini-chart"></div></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Total Trips</div>
                        <div class="stat-icon icon-purple"><i data-lucide="map" width="18"></i></div>
                    </div>
                    <div class="stat-value"><?= count($trips_all) ?></div>
                    <div class="stat-sub">All time <div class="mini-chart"></div></div>
                </div>
            </div>

            <!-- Filters & Tabs Container -->
            <div class="table-container">
                
                <form id="filterForm" action="<?= base_url('dispatch/trips') ?>" method="get" class="toolbar" style="margin-bottom:0;">
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items:center;">
                        <div class="search-wrapper">
                            <i data-lucide="search" width="16" class="search-icon"></i>
                            <input type="text" name="search" class="search-input" placeholder="Search trip #, address..." value="<?= esc($filters['search'] ?? '') ?>">
                        </div>
                        <select name="status" class="filter-input">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="dispatching" <?= ($filters['status'] == 'dispatching') ? 'selected' : '' ?>>Dispatching</option>
                            <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                            <option value="completed" <?= ($filters['status'] == 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($filters['status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <select name="driver_id" class="filter-input">
                            <option value="">All Drivers</option>
                            <?php foreach($drivers as $d): ?>
                                <option value="<?= $d->id ?>" <?= ($filters['driver_id'] == $d->id) ? 'selected' : '' ?>>
                                    <?= esc($d->first_name . ' ' . $d->last_name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="date" name="from_date" class="filter-input" value="<?= esc($filters['from_date'] ?? '') ?>">
                        <input type="date" name="to_date" class="filter-input" value="<?= esc($filters['to_date'] ?? '') ?>">
                        
                        <button type="submit" class="btn-add" style="border-radius:8px; padding:0.6rem 1rem;">Search</button>
                        <a href="<?= base_url('dispatch/trips') ?>" class="btn-outline-action" style="text-decoration:none;">Clear</a>
                    </div>
                    <div class="toolbar-actions">
                        <button id="bulkPrintTripBtn" type="button" onclick="bulkPrintSelectedTrips()" class="btn-outline-action" style="display:none; color: var(--primary-blue); border-color: var(--primary-blue);">
                            <i data-lucide="printer" width="16"></i> Bulk Print (<span id="selectedTripCount">0</span>)
                        </button>
                    </div>
                </form>

                <div style="padding: 1rem 0; font-size: 0.85rem; color: var(--text-gray); display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" id="selectAllTrips" onclick="toggleAllTrips(this)">
                    <label for="selectAllTrips" style="cursor: pointer; margin: 0;">Select All on Current View</label>
                </div>

                <!-- Tabs -->
                <div class="tabs-nav">
                    <button type="button" class="tab-btn <?= ($active_tab == 'queue') ? 'active' : '' ?>" onclick="switchTab('queue')">
                        Queue 
                        <?php if(count($trips_queue) > 0): ?>
                            <span class="tab-badge" style="background:var(--primary-blue);"><?= count($trips_queue) ?></span>
                        <?php endif; ?>
                    </button>
                    <button type="button" class="tab-btn <?= ($active_tab == 'active') ? 'active' : '' ?>" onclick="switchTab('active')">
                        Active
                        <?php if(count($trips_active) > 0): ?>
                            <span class="tab-badge" style="background:#0ea5e9;"><?= count($trips_active) ?></span>
                        <?php endif; ?>
                    </button>
                    <button type="button" class="tab-btn <?= ($active_tab == 'history') ? 'active' : '' ?>" onclick="switchTab('history')">History</button>
                    <button type="button" class="tab-btn <?= ($active_tab == 'all') ? 'active' : '' ?>" onclick="switchTab('all')">All Trips <span class="tab-badge"><?= count($trips_all) ?></span></button>
                </div>

                <!-- Content -->
                <div id="tab-queue" class="tab-pane <?= ($active_tab == 'queue') ? 'active' : '' ?>">
                    <table class="custom-table">
                        <thead><tr><th style="width:5%;">Sel</th><th style="width:15%;">Trip</th><th style="width:30%;">Route</th><th style="width:20%;">Participants</th><th style="width:15%;">Price</th><th style="width:15%;">Actions</th></tr></thead>
                        <tbody>
                            <?php if(empty($trips_queue)): ?>
                                <tr><td colspan="6" style="text-align:center; padding:3rem; color:var(--text-gray);">All caught up! No pending trips.</td></tr>
                            <?php else: ?>
                                <?php foreach($trips_queue as $t): ?>
                                    <?= view('App\Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'queue']) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div id="tab-active" class="tab-pane <?= ($active_tab == 'active') ? 'active' : '' ?>">
                    <table class="custom-table">
                        <thead><tr><th style="width:5%;">Sel</th><th style="width:15%;">Trip</th><th style="width:30%;">Route</th><th style="width:20%;">Participants</th><th style="width:15%;">Price</th><th style="width:15%;">Actions</th></tr></thead>
                        <tbody>
                            <?php if(empty($trips_active)): ?>
                                <tr><td colspan="6" style="text-align:center; padding:3rem; color:var(--text-gray);">No active trips right now.</td></tr>
                            <?php else: ?>
                                <?php foreach($trips_active as $t): ?>
                                    <?= view('App\Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'active']) ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div id="tab-history" class="tab-pane <?= ($active_tab == 'history') ? 'active' : '' ?>">
                    <table class="custom-table">
                        <thead><tr><th style="width:5%;">Sel</th><th style="width:15%;">Trip</th><th style="width:30%;">Route</th><th style="width:20%;">Participants</th><th style="width:15%;">Price</th><th style="width:15%;">Actions</th></tr></thead>
                        <tbody>
                            <?php foreach($trips_history as $t): ?>
                                <?= view('App\Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'history']) ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div id="tab-all" class="tab-pane <?= ($active_tab == 'all') ? 'active' : '' ?>">
                    <table class="custom-table">
                        <thead><tr><th style="width:5%;">Sel</th><th style="width:15%;">Trip</th><th style="width:30%;">Route</th><th style="width:20%;">Participants</th><th style="width:15%;">Price</th><th style="width:15%;">Actions</th></tr></thead>
                        <tbody>
                            <?php foreach($trips_all as $t): ?>
                                <?= view('App\Modules\Dispatch\Views\trips\_card', ['trip' => $t, 'type' => 'all']) ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Assign Modal -->
<div id="assignModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#ffffff; padding:2rem; border-radius:12px; width:400px; box-shadow:var(--shadow-card);">
        <h3 class="h4" style="margin-bottom:1rem; color:var(--text-dark);">Assign Driver</h3>
        <form action="<?= base_url('dispatch/trips/update') ?>/TODO" method="post" id="assignForm">
             <input type="hidden" name="status" value="dispatching"> 
             
             <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" style="color:var(--text-gray);">Select Driver</label>
                <select name="driver_id" class="form-select filter-input" style="width:100%; margin-top:0.5rem;" required>
                    <option value="">-- Choose Driver --</option>
                    <?php foreach($drivers as $d): ?>
                        <option value="<?= $d->id ?>"><?= esc($d->first_name . ' ' . $d->last_name) ?> (<?= $d->vehicle_model ?>) - ★ <?= number_format($d->rating ?? 0, 1) ?></option>
                    <?php endforeach; ?>
                </select>
             </div>
             
             <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn-outline-action" onclick="document.getElementById('assignModal').style.display='none'">Cancel</button>
                <button type="submit" class="btn-add" style="border-radius:8px;">Assign & Dispatch</button>
             </div>
        </form>
    </div>
</div>

<!-- Rating Modal -->
<div class="modal fade" id="ratingModal" tabindex="-1" style="z-index: 2000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 12px; overflow: hidden; background: #ffffff; box-shadow: var(--shadow-card);">
            <div class="modal-header border-0 pb-0 pt-4 px-4" style="display:flex; justify-content:space-between;">
                <h5 class="modal-title" id="rate-modal-title" style="font-size: 1.25rem; font-weight:700; color:var(--text-dark); margin:0;">Rate Participant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="closeRateModal()" style="background:none; border:none; cursor:pointer;"><i data-lucide="x" width="20"></i></button>
            </div>
            <div class="modal-body p-4" style="padding:1.5rem;">
                <input type="hidden" id="rate-trip-id">
                <input type="hidden" id="ratee-type">
                <input type="hidden" id="ratee-id">

                <div class="text-center" style="margin-bottom: 1.5rem; text-align:center;">
                    <div id="rate-modal-subtitle" style="font-size: 0.9rem; color: var(--text-gray); margin-bottom: 1.25rem;">Share feedback about <strong style="color:var(--text-dark);">TRP-<span id="lbl-rate-trip-num"></span></strong></div>
                    <div class="star-rating" id="modal-star-group" style="font-size: 2.25rem; gap: 12px; display: flex; justify-content:center;">
                        <span data-value="1" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="2" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="3" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="4" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                        <span data-value="5" class="star-item modal-star" style="cursor:pointer; transition:0.2s;">★</span>
                    </div>
                </div>

                <div class="premium-input-group" style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-gray); text-transform: uppercase; margin-bottom: 8px; display: block;">Review & Comment</label>
                    <textarea id="rate-comment" class="form-control" placeholder="What was your experience with this participant?" style="height: 120px; padding: 15px; border-radius: 8px; resize: none; width: 100%; border: 1px solid var(--border-light); background:#f8fafc; color:var(--text-dark); box-sizing:border-box;"></textarea>
                </div>

                <button type="button" class="btn-add" id="btn-submit-rating" style="width: 100%; justify-content:center; padding:0.8rem; font-size:1rem;">Submit Rating</button>
            </div>
        </div>
    </div>
</div>

<!-- Dispute Modal -->
<div id="disputeModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#ffffff; padding:2rem; border-radius:12px; width:450px; box-shadow:var(--shadow-card);">
        <h3 class="h4" style="margin-bottom:1rem; color:var(--text-dark);">Report Dispute</h3>
        <form action="<?= base_url('api/disputes/create') ?>" method="post" id="disputeForm" enctype="multipart/form-data">
             <input type="hidden" name="trip_id" id="disputeTripId">
             <input type="hidden" name="customer_id" id="disputeCustomerId">
             <input type="hidden" name="driver_id" id="disputeDriverId">
             
             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" style="color:var(--text-gray); margin-bottom:0.5rem; display:block;">Subject</label>
                <input type="text" name="subject" class="filter-input" placeholder="e.g. Fare disagreement" required style="width:100%; box-sizing:border-box;">
             </div>

             <div class="form-group" style="margin-bottom:1rem;">
                <label class="form-label" style="color:var(--text-gray); margin-bottom:0.5rem; display:block;">Description</label>
                <textarea name="description" class="filter-input" rows="3" placeholder="Tell us what happened..." required style="width:100%; height:auto; box-sizing:border-box;"></textarea>
             </div>

             <div class="form-group" style="margin-bottom:1.5rem;">
                <label class="form-label" style="color:var(--text-gray); margin-bottom:0.5rem; display:block;">Attachments (Optional)</label>
                <input type="file" name="attachments[]" multiple class="filter-input" style="width:100%; box-sizing:border-box;">
             </div>
             
             <div style="display:flex; justify-content:flex-end; gap:1rem;">
                <button type="button" class="btn-outline-action" onclick="closeDisputeModal()">Cancel</button>
                <button type="submit" class="btn-add" style="background:#ef4444;">File Dispute</button>
             </div>
        </form>
    </div>
</div>

<!-- Trip Details Modal -->
<div id="tripDetailsModal" class="modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1100; align-items:center; justify-content:center;">
    <div class="modal-content" style="background:#ffffff; padding:0; border-radius:12px; width:600px; max-height:90vh; overflow-y:auto; box-shadow:var(--shadow-card);">
        <div class="sidebar-header" style="background:#f8fafc; border-bottom:1px solid var(--border-light); position:sticky; top:0; z-index:10; padding:1.5rem; display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:10px;">
                <span id="mdTripNumber" class="h4" style="margin:0; font-weight:700; color:var(--text-dark);">#TRP-XXXX</span>
                <span id="mdTripStatus" class="status-badge">Status</span>
            </div>
            <button onclick="closeTripDetailsModal()" style="background:none; border:none; cursor:pointer;"><i data-lucide="x" width="20"></i></button>
        </div>
        
        <div style="padding:1.5rem;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-gray); text-transform:uppercase; margin-bottom:0.5rem; font-weight:600;">Customer</div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div class="driver-avatar-sm" style="width:40px; height:40px; border-radius:50%; background:#e2e8f0; display:flex; align-items:center; justify-content:center; font-weight:600; color:var(--text-gray);">C</div>
                        <div>
                            <div id="mdCustomerName" style="font-weight:700; color:var(--text-dark);">Name</div>
                            <div id="mdCustomerPhone" style="font-size:0.85rem; color:var(--text-gray);">Phone</div>
                        </div>
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-gray); text-transform:uppercase; margin-bottom:0.5rem; font-weight:600;">Driver</div>
                    <div style="display:flex; align-items:center; gap:0.75rem;">
                        <div class="driver-avatar-sm" style="width:40px; height:40px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; font-weight:600; color:var(--primary-blue);">D</div>
                        <div>
                            <div id="mdDriverName" style="font-weight:700; color:var(--text-dark);">Unassigned</div>
                            <div id="mdDriverVehicle" style="font-size:0.85rem; color:var(--text-gray);">Vehicle</div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="background:#f8fafc; padding:1rem; border-radius:8px; margin-bottom:1.5rem; border:1px solid var(--border-light);">
                <div style="margin-bottom:1rem;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <i data-lucide="map-pin" width="14" style="color:#22c55e;"></i>
                        <span style="font-size:0.75rem; color:var(--text-gray); font-weight:700;">PICKUP</span>
                    </div>
                    <div id="mdPickupAddress" style="padding-left:22px; font-size:0.9rem; color:var(--text-dark); font-weight:500;">Address</div>
                </div>
                <div>
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <i data-lucide="navigation" width="14" style="color:#ef4444;"></i>
                        <span style="font-size:0.75rem; color:var(--text-gray); font-weight:700;">DROPOFF</span>
                    </div>
                    <div id="mdDropoffAddress" style="padding-left:22px; font-size:0.9rem; color:var(--text-dark); font-weight:500;">Address</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:1rem; border-top:1px solid var(--border-light); padding-top:1.5rem;">
                <div>
                    <div style="font-size:0.75rem; color:var(--text-gray); margin-bottom:4px; font-weight:600;">Date/Time</div>
                    <div id="mdDateTime" style="font-weight:600; color:var(--text-dark);">Date</div>
                    <div id="mdTimeOnly" style="font-size:0.85rem; color:var(--text-gray);">Time</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-gray); margin-bottom:4px; font-weight:600;">Fare & Method</div>
                    <div id="mdPrice" style="font-weight:700; color:var(--primary-blue); font-size:1.1rem;">Amount</div>
                    <div id="mdPayment" style="font-size:0.85rem; color:var(--text-gray); text-transform:uppercase; font-weight:500;">Cash</div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:var(--text-gray); margin-bottom:4px; font-weight:600;">Trip Stats</div>
                    <div style="display:flex; flex-direction:column; gap:2px;">
                        <span id="mdDistance" style="font-weight:600; color:var(--text-dark);">Distance</span>
                        <span id="mdDurationOnly" style="font-size:0.85rem; color:var(--text-gray);">Duration</span>
                    </div>
                </div>
            </div>

            <div style="margin-top:1.5rem; border-top:1px solid var(--border-light); padding-top:1.5rem;">
                <div style="font-size:0.75rem; color:var(--text-gray); text-transform:uppercase; margin-bottom:0.5rem; font-weight:600;">Dispatcher Notes</div>
                <p id="mdNotes" style="font-size:0.9rem; font-style:italic; color:var(--text-dark); margin:0;">No notes.</p>
            </div>
        </div>
    </div>
</div>

<?= view('App\Modules\\Dispatch\\Views\\trips\\_quick_dispatch_modal') ?>

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
