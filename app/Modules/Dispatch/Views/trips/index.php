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
    .trip-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        margin-bottom: 0.75rem;
        padding: 1rem;
        display: grid;
        grid-template-columns: 80px 1.5fr 1fr 100px; /* Status, Route, Customer, Action */
        gap: 1rem;
        align-items: center;
        transition: all 0.1s;
        cursor: pointer;
    }
    .trip-card:hover { border-color: var(--primary); transform: translateY(-1px); box-shadow: var(--shadow-sm); }
    
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

<script>
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
        
        // Update form action with Trip ID
        // Note: Controller update expects ID in URL: dispatch/trips/update/(:num)
        form.action = '<?= base_url("dispatch/trips/update") ?>/' + tripId;
        
        modal.style.display = 'flex';
    }
    
    // Auto-close modal on outside click
    window.onclick = function(e) {
        const m = document.getElementById('assignModal');
        if(e.target == m) m.style.display = 'none';

        const rm = document.getElementById('ratingModal');
        if(e.target == rm) rm.style.display = 'none';
        
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

<?= $this->section('scripts') ?>
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
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
