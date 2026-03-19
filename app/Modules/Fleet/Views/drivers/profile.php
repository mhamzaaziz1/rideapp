<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .db-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    
    /* Card 1: Profile */
    .profile-card-header { display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1rem; }
    .p-avatar { 
        width: 56px; height: 56px; 
        border-radius: 50%; background: var(--primary); color: #fff; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: 700; font-size: 1.25rem;
        flex-shrink: 0;
    }
    .p-info h3 { font-size: 1.1rem; font-weight: 700; margin: 0 0 0.25rem 0; color: var(--text-primary); }
    .p-info div { font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 6px; margin-bottom: 2px; }
    
    .p-details { border-top: 1px solid var(--border-color); padding-top: 1rem; font-size: 0.85rem; }
    .p-row { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
    .p-row:last-child { margin-bottom: 0; }
    .p-label { color: var(--text-secondary); }
    .p-val { font-weight: 500; color: var(--text-primary); text-align: right; }

    /* Card 2, 3, 4: Stats */
    .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
    .stat-title { font-size: 0.85rem; color: var(--text-secondary); }
    .stat-icon { color: var(--success); }
    .stat-main-val { font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
    .stat-sub { font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1.5rem; }

    .stat-rows { font-size: 0.85rem; }
    .stat-row { display: flex; justify-content: space-between; margin-bottom: 0.4rem; }
    .stat-row.total { border-top: 1px solid var(--border-color); padding-top: 0.5rem; margin-top: 0.5rem; font-weight: 600; }
    .text-success { color: var(--success) !important; }
    .text-danger { color: var(--danger) !important; }
    .text-warning { color: var(--warning) !important; }

    /* Card 4 Specific */
    .balance-card { background: #fffcf0; border-color: #fef08a; } /* Light yellow bg for balance */
    .balance-val-neg { color: #d97706; } /* Amber/Orange for owing */
    
    /* Tabs */
    .profile-tabs { border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-end; }
    .tab-labels { display: flex; gap: 2rem; }
    .p-tab { padding-bottom: 1rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
    .p-tab:hover { color: var(--text-primary); }
    .p-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
    .tab-actions { padding-bottom: 0.5rem; }

    /* History */
    .history-item { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; }
    .status-badge { padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

    /* Modal Styles */
    .modal {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1000;
        display: none; align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal.active { display: flex; }
    .modal-content {
        background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg); border: 1px solid var(--border-color);
        width: 500px; max-width: 90%;
        position: relative;
    }
    .close { position: absolute; top: 1rem; right: 1rem; cursor: pointer; font-size: 1.5rem; line-height: 1; color: var(--text-secondary); }
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 500; }
    .form-control { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-body); color: var(--text-primary); }
    .btn-block { width: 100%; }
    
    /* Ratings Grid */
    .ratings-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
    .rating-card { background: var(--bg-surface); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.25rem; }
    .rating-header { display: flex; justify-content: space-between; margin-bottom: 0.5rem; }
    .stars { color: var(--warning); display: flex; align-items: center; gap: 2px; }
    .rating-comment { font-size: 0.9rem; color: var(--text-primary); margin-bottom: 0.75rem; line-height: 1.5; font-style: italic; background: var(--bg-body); padding: 0.75rem; border-radius: var(--radius-sm); }
    .rating-footer { font-size: 0.75rem; color: var(--text-secondary); display: flex; justify-content: space-between; align-items: center; }

    /* Spinner */
    .spinner {
        width: 32px; height: 32px;
        border: 3px solid rgba(var(--primary-rgb), 0.1);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin: 0 auto;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div style="padding: 2rem; max-width: 1400px; margin: 0 auto; padding-bottom: 5rem;">
    
    <!-- Breadcrumb -->
    <div style="margin-bottom: 1.5rem;">
        <a href="<?= base_url('drivers') ?>" style="color:var(--text-secondary); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem;">
            <i data-lucide="arrow-left" width="16"></i> Back to Drivers
        </a>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        
        <!-- Card 1: Profile -->
        <div class="db-card">
            <div>
                <div class="profile-card-header">
                    <div class="p-avatar">
                        <?php if($driver->avatar): ?>
                            <img src="<?= base_url($driver->avatar) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <?php else: ?>
                            <?= substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1) ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-info">
                        <h3>
                            <?= esc($driver->first_name . ' ' . $driver->last_name) ?>
                            <span style="display:inline-flex; align-items:center; gap:2px; font-size:0.85rem; background:rgba(234, 179, 8, 0.15); color:#ca8a04; padding:2px 6px; border-radius:12px; margin-left:8px; vertical-align:middle;">
                                <i data-lucide="star" width="12" fill="currentColor"></i>
                                <?= number_format($driver->rating ?? 0, 1) ?>
                            </span>
                        </h3>
                        <div><i data-lucide="phone" width="12"></i> <?= esc($driver->phone) ?></div>
                        <div><i data-lucide="mail" width="12"></i> <?= esc($driver->email) ?></div>
                    </div>
                </div>
            </div>
            <div class="p-details">
                <div class="p-row"><span class="p-label">Vehicle:</span> <span class="p-val"><?= esc($driver->vehicle_year . ' ' . $driver->vehicle_make . ' ' . $driver->vehicle_model) ?></span></div>
                <div class="p-row"><span class="p-label">Plate:</span> <span class="p-val"><?= esc($driver->license_plate) ?></span></div>
                <div class="p-row"><span class="p-label">License:</span> <span class="p-val"><?= esc($driver->license_number) ?></span></div>
                <div class="p-row"><span class="p-label">Joined:</span> <span class="p-val"><?= date('Y-m-d', strtotime($driver->created_at)) ?></span></div>
            </div>
        </div>

        <!-- Card 2: Total Earnings -->
        <div class="db-card">
            <div>
                <div class="stat-header">
                    <span class="stat-title">Total Earnings</span>
                    <i data-lucide="trending-up" class="stat-icon"></i>
                </div>
                <div class="stat-main-val">$<?= number_format($stats['total_earnings'], 2) ?></div>
                <div class="stat-sub"><?= $stats['trips_completed'] ?> trips completed</div>
            </div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="p-label">Cash collected:</span> 
                    <span class="p-val text-success">$<?= number_format($stats['cash_collected'], 2) ?></span>
                </div>
                <div class="stat-row">
                    <span class="p-label">Card/Account:</span> 
                    <span class="p-val">$<?= number_format($stats['card_earnings'], 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Card 3: Company Rate -->
        <div class="db-card">
            <div>
                <div class="stat-header">
                    <span class="stat-title">Company Rate</span>
                    <i data-lucide="settings" width="16" style="color:var(--text-secondary); cursor:pointer;" onclick="openRateModal()"></i>
                </div>
                <div class="stat-main-val"><?= number_format($stats['company_rate'], 1) ?>%</div>
                <div class="stat-sub">Company takes from each trip</div>
            </div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="p-label">Company share:</span> 
                    <span class="p-val">$<?= number_format($stats['company_share'], 2) ?></span>
                </div>
                <div class="stat-row">
                    <span class="p-label">Driver share:</span> 
                    <span class="p-val">$<?= number_format($stats['driver_share'], 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Card 4: Balance -->
        <div class="db-card balance-card">
            <div>
                <div class="stat-header">
                    <span class="stat-title">Balance</span>
                    <button onclick="openWalletModal()" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.75rem; display:flex; align-items:center; gap:4px;">
                        <i data-lucide="wallet" width="12"></i> Adjust
                    </button>
                </div>
                <!-- Balance computed by WalletService: card trip earnings - cash commission owed + manual transactions -->
                <?php $displayBalance = $stats['wallet_balance']; ?>
                <div class="stat-main-val <?= $displayBalance < 0 ? 'text-danger' : 'text-success' ?>" style="font-size:1.8rem;">
                    <?= $displayBalance < 0 ? '-' : '' ?>$<?= number_format(abs($displayBalance), 2) ?>
                </div>
                <div class="stat-sub" style="color:<?= $displayBalance < 0 ? '#d97706' : 'var(--success)' ?>">
                    <?= $displayBalance < 0 ? 'Driver owes company' : 'Company owes driver' ?>
                </div>
            </div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="p-label">Cash driver has:</span> 
                    <span class="p-val">$<?= number_format($stats['cash_driver_has'], 2) ?></span>
                </div>
                <div class="stat-row">
                    <span class="p-label">Company cut from cash:</span> 
                    <span class="p-val text-danger">-$<?= number_format($stats['company_cut_from_cash'], 2) ?></span>
                </div>
                <div class="stat-row">
                    <span class="p-label">Card payments due:</span> 
                    <span class="p-val text-success">+$<?= number_format($stats['card_payments_due'], 2) ?></span>
                </div>
                <div class="stat-row">
                    <span class="p-label">Already paid:</span> 
                    <span class="p-val">-$<?= number_format($stats['already_paid'], 2) ?></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Global Filters -->
    <div style="margin-bottom:2rem; background:var(--bg-surface); padding:1.25rem; border-radius:var(--radius-md); border:1px solid var(--border-color);">
        <form action="<?= current_url() ?>" method="get" style="display:flex; align-items:flex-end; gap:1.25rem;">
            <div style="flex:1;">
                <label style="display:block; font-size:0.85rem; font-weight:500; color:var(--text-secondary); margin-bottom:0.5rem;">Filter by Date Range</label>
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <div style="flex:1;">
                        <input type="date" name="from_date" class="form-control" value="<?= esc($filters['from_date'] ?? '') ?>" style="padding:0.6rem;">
                    </div>
                    <span style="color:var(--text-secondary); font-size:0.85rem;">to</span>
                    <div style="flex:1;">
                        <input type="date" name="to_date" class="form-control" value="<?= esc($filters['to_date'] ?? '') ?>" style="padding:0.6rem;">
                    </div>
                </div>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="filter" width="16"></i> Apply Filter
                </button>
                <?php if(!empty($filters['from_date']) || !empty($filters['to_date'])): ?>
                    <a href="<?= base_url('drivers/profile/' . $driver->id) ?>" class="btn btn-outline" style="padding: 0.65rem 1.25rem;">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Main Content -->
     <div class="profile-tabs">
        <div class="tab-labels">
            <div class="p-tab active" onclick="switchTab('trips', this)">Trip History (<?= count($trips) ?>)</div>
            <div class="p-tab" onclick="switchTab('wallet', this)">Payout History (<?= count($transactions) ?>)</div>
            <div class="p-tab" onclick="switchTab('bank', this)">Bank Details (<?= count($bankAccounts) ?>)</div>
            <div class="p-tab" onclick="switchTab('ratings', this)">Ratings (<?= count($ratings) ?>)</div>
        </div>
        <div class="tab-actions" id="tab-actions">
            <!-- Dynamic button will be injected by JS -->
        </div>
    </div>

    <div id="tab-trips" class="tab-content">
        <?php if(empty($trips)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="map" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No trips found.</p>
            </div>
        <?php else: ?>
            <div style="margin-bottom:1rem; display:flex; align-items:center; gap:8px; font-size:0.9rem; color:var(--text-secondary);">
                <input type="checkbox" id="selectAllTrips" onclick="toggleAllTrips(this)">
                <label for="selectAllTrips" style="margin:0; cursor:pointer;">Select All Trips</label>
            </div>
            <?php foreach($trips as $t): ?>
            <div class="history-item">
                <div style="margin-right:1rem;">
                    <input type="checkbox" class="trip-checkbox" value="<?= $t->id ?>" onclick="updateTripBulkBtn()">
                </div>
                <div>
                    <div style="font-weight:700; color:var(--primary); font-family:monospace;">#<?= $t->trip_number ?></div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);"><?= date('M d, Y', strtotime($t->created_at)) ?></div>
                </div>
                <div style="flex:1; margin:0 2rem;">
                    <div style="display:flex; align-items:center; gap:10px; font-size:0.9rem;">
                        <span style="width:120px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= esc($t->pickup_address) ?></span>
                        <i data-lucide="arrow-right" width="14" style="color:var(--text-secondary)"></i>
                        <span style="width:120px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= esc($t->dropoff_address) ?></span>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:700;">$<?= number_format($t->fare_amount, 2) ?></div>
                    <div style="font-size:0.75rem; color:var(--text-secondary);"><?= ucfirst($t->payment_method ?? 'cash') ?></div>
                </div>
                <div style="margin-left:1.5rem; display:flex; align-items:center; gap:15px;">
                    <span class="status-badge status-<?= $t->status ?>"><?= $t->status ?></span>
                    <a href="<?= base_url('dispatch/trips/print/' . $t->id) ?>" target="_blank" title="Print Receipt" style="color:var(--text-secondary);">
                        <i data-lucide="printer" width="18"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="tab-wallet" class="tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem; align-items:center;">
            <h3 class="h5" style="margin:0; font-weight:700;">Recent Transactions</h3>
            <div style="display:flex; gap:1.5rem;">
               <a href="<?= base_url('drivers/print_statement/' . $driver->id) ?>" target="_blank" style="color:var(--text-secondary); text-decoration:none; display:flex; align-items:center; gap:8px; font-size:0.9rem; transition: color 0.2s;">
                    <i data-lucide="printer" width="16"></i> Print Statement
               </a>
               <a href="<?= base_url('drivers/export_statement/' . $driver->id) ?>" style="color:var(--text-secondary); text-decoration:none; display:flex; align-items:center; gap:8px; font-size:0.9rem; transition: color 0.2s;">
                    <i data-lucide="download" width="16"></i> Export CSV
               </a>
            </div>
        </div>

        <?php if(empty($transactions)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="credit-card" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No transactions found.</p>
            </div>
        <?php else: ?>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left; color:var(--text-secondary); font-size:0.85rem; border-bottom:1px solid var(--border-color);">
                        <th style="padding:1rem;">ID</th>
                        <th style="padding:1rem;">Type</th>
                        <th style="padding:1rem;">Description</th>
                        <th style="padding:1rem;">Date</th>
                        <th style="padding:1rem; text-align:right;">Amount</th>
                        <th style="padding:1rem; text-align:center;">Cheque</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $tx): ?>
                        <tr style="border-bottom:1px solid var(--border-color);">
                            <td style="padding:1rem; color:var(--text-secondary);">#<?= $tx['id'] ?></td>
                            <td style="padding:1rem;"><span class="status-badge" style="background:var(--bg-surface-hover);"><?= ucfirst($tx['type']) ?></span></td>
                            <td style="padding:1rem;"><?= esc($tx['description']) ?></td>
                            <td style="padding:1rem; color:var(--text-secondary);"><?= date('M d, Y', strtotime($tx['created_at'])) ?></td>
                            <td style="padding:1rem; text-align:right; font-weight:600; color:<?= in_array($tx['type'], ['deposit','refund']) ? 'var(--success)' : 'var(--danger)' ?>">
                                <?= in_array($tx['type'], ['deposit','refund']) ? '+' : '-' ?>$<?= number_format($tx['amount'], 2) ?>
                            </td>
                            <td style="padding:1rem; text-align:center;">
                                <?php if ($tx['type'] === 'withdrawal' && ($tx['payment_method'] ?? 'cheque') !== 'bank'): ?>
                                    <div style="display:flex; align-items:center; justify-content:center; gap:8px;">
                                        <a href="<?= base_url('drivers/cheque/' . $tx['id']) ?>" target="_blank"
                                           class="btn btn-sm"
                                           style="font-size:0.75rem; color:var(--primary); text-decoration:none; display:inline-flex; align-items:center; gap:4px; white-space:nowrap; padding:4px 8px; border:1px solid rgba(var(--primary-rgb), 0.2); border-radius:4px;">
                                            🖨 Print
                                        </a>
                                        <button onclick="showPrintHistory(<?= $tx['id'] ?>)" 
                                                class="btn btn-sm btn-outline" 
                                                title="Print History"
                                                style="padding:4px 6px; display:inline-flex; align-items:center; justify-content:center;">
                                            <i data-lucide="history" width="14"></i>
                                        </button>
                                    </div>
                                <?php elseif($tx['type'] === 'withdrawal'): ?>
                                    <span style="font-size:0.75rem; color:var(--text-secondary); display:flex; align-items:center; justify-content:center; gap:4px;">
                                        <i data-lucide="landmark" width="14"></i> Bank
                                    </span>
                                <?php else: ?>
                                    <span style="color:var(--text-secondary);">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- BANK DETAILS TAB -->
    <div id="tab-bank" class="tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1.5rem; align-items:center;">
            <h3 class="h5" style="margin:0; font-weight:700;">Driver Bank Accounts</h3>
        </div>

        <?php if(empty($bankAccounts)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="landmark" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No bank accounts added yet.</p>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap:1rem;">
                <?php foreach($bankAccounts as $acc): ?>
                    <div style="background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:1.25rem; position:relative;">
                        <?php if($acc->is_default): ?>
                            <span style="position:absolute; top:15px; right:15px; background:var(--success); color:#fff; font-size:0.7rem; padding:3px 8px; border-radius:12px; font-weight:600; text-transform:uppercase;">Primary</span>
                        <?php endif; ?>
                        
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:1rem;">
                            <div style="width:40px; height:40px; border-radius:8px; background:var(--bg-body); display:flex; align-items:center; justify-content:center; color:var(--text-secondary);">
                                <i data-lucide="landmark" width="20"></i>
                            </div>
                            <div>
                                <div style="font-weight:700; color:var(--text-primary);"><?= esc($acc->bank_name) ?></div>
                                <div style="font-size:0.8rem; color:var(--text-secondary);"><?= esc($acc->account_name) ?></div>
                            </div>
                        </div>
                        
                        <div style="background:var(--bg-body); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); margin-bottom:1rem; font-family:monospace; font-size:1.1rem; letter-spacing:1px; text-align:center;">
                            <?= esc($acc->account_number) ?>
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-bottom:1rem;">
                           <div style="font-size:0.8rem; color:var(--text-secondary);">Routing Num: <span style="color:var(--text-primary); font-weight:500;"><?= esc($acc->routing_number ?: '—') ?></span></div>
                           <div style="font-size:0.8rem; color:var(--text-secondary);">SWIFT: <span style="color:var(--text-primary); font-weight:500;"><?= esc($acc->swift_code ?: '—') ?></span></div>
                        </div>

                        <div style="display:flex; gap:0.5rem; padding-top:1rem; border-top:1px solid var(--border-color);">
                            <?php if(!$acc->is_default): ?>
                                <a href="<?= base_url('drivers/set_default_bank/' . $acc->id) ?>" class="btn btn-sm btn-outline" style="flex:1;">Set as Default</a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline" disabled style="flex:1; opacity:0.5; cursor:not-allowed;">Default Account</button>
                            <?php endif; ?>
                            <a href="<?= base_url('drivers/delete_bank/' . $acc->id) ?>" onclick="return confirm('Remove this bank account?')" class="btn btn-sm btn-outline text-danger" style="flex:0 0 40px; display:flex; align-items:center; justify-content:center;">
                                <i data-lucide="trash-2" width="16"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div id="tab-ratings" class="tab-content" style="display:none;">
        <?php if(empty($ratings)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="star" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No ratings yet.</p>
            </div>
        <?php else: ?>
            <div class="ratings-grid">
                <?php foreach($ratings as $r): ?>
                    <div class="rating-card">
                        <div class="rating-header">
                            <div class="stars">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i data-lucide="star" width="14" <?= $i <= $r['rating'] ? 'fill="currentColor"' : '' ?>></i>
                                <?php endfor; ?>
                                <span style="color:var(--text-primary); font-weight:700; margin-left:6px; font-size:0.9rem;"><?= number_format($r['rating'], 1) ?></span>
                            </div>
                            <div style="font-size:0.75rem; color:var(--text-secondary);">
                                Trip #<?= $r['trip_id'] ?>
                            </div>
                        </div>
                        
                        <?php if(!empty($r['comment'])): ?>
                            <div class="rating-comment">"<?= esc($r['comment']) ?>"</div>
                        <?php else: ?>
                             <div style="font-size:0.85rem; color:var(--text-secondary); font-style:italic; margin-bottom:0.75rem;">No comment provided</div>
                        <?php endif; ?>

                        <div class="rating-footer">
                            <span><i data-lucide="user" width="12" style="vertical-align:text-bottom"></i> <?= ucfirst($r['rater_type']) ?></span>
                            <span><?= date('M d, Y', strtotime($r['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Reuse Wallet Modal -->
<div id="walletModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeWalletModal()">&times;</span>
        <h2 style="margin-bottom:1.5rem;">Adjust Wallet Funds</h2>
        <form action="<?= base_url('drivers/add_fund') ?>" method="post">
            <input type="hidden" name="driver_id" value="<?= $driver->id ?>">
            
            <div class="form-group">
                <label class="form-label">Transaction Type</label>
                <select name="type" class="form-control" onchange="togglePayoutMethods(this.value)">
                    <option value="deposit">Deposit (Driver Pays Company)</option>
                    <option value="withdrawal">Withdrawal (Company Pays Driver)</option>
                </select>
            </div>

            <div id="payoutMethodSection" style="display:none;">
                <div class="form-group">
                    <label class="form-label">Payout Method</label>
                    <select name="payment_method" class="form-control" onchange="toggleBankList(this.value)">
                        <option value="cheque">Cheque</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>

                <div class="form-group" id="bankListSection" style="display:none;">
                    <label class="form-label">Select Bank Account</label>
                    <select name="bank_account_id" class="form-control">
                        <?php if(empty($bankAccounts)): ?>
                            <option value="">No bank accounts linked</option>
                        <?php else: ?>
                            <?php foreach($bankAccounts as $acc): ?>
                                <option value="<?= $acc->id ?>"><?= $acc->is_default ? '[PRIMARY] ' : '' ?><?= esc($acc->bank_name) ?> - <?= esc($acc->account_number) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Amount ($)</label>
                <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Description / Note</label>
                <textarea name="description" class="form-control" rows="3" required placeholder="e.g. Weekly Payout, Cash Deposit"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Confirm Transaction</button>
        </form>
    </div>
</div>

<!-- Print History Modal -->
<div id="historyModal" class="modal">
    <div class="modal-content" style="width: 400px; max-width: 90%;">
        <span class="close" onclick="closeHistoryModal()">&times;</span>
        <h3 style="margin-bottom:1.5rem; display:flex; align-items:center; gap:10px;">
            <i data-lucide="history" class="text-primary"></i> 
            Print History
        </h3>
        <div id="historyContent">
            <div style="text-align:center; padding:2rem;">
                <div class="spinner"></div>
                <p style="margin-top:1rem; color:var(--text-secondary);">Fetching records...</p>
            </div>
        </div>
    </div>
</div>

<!-- Rate Modal -->
<div id="rateModal" class="modal">
    <div class="modal-content" style="width: 400px;">
        <span class="close" onclick="closeRateModal()">&times;</span>
        <h2 style="margin-bottom:1.5rem;">Update Commission Rate</h2>
        <form action="<?= base_url('drivers/update_rate') ?>" method="post">
            <input type="hidden" name="driver_id" value="<?= $driver->id ?>">
            
            <div class="form-group">
                <label class="form-label">Commission Percentage (%)</label>
                <input type="number" name="commission_rate" step="0.01" min="0" max="100" class="form-control" value="<?= $driver->commission_rate ?? 25.00 ?>" required>
                <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:0.5rem;">
                    Default is 25%. This determines the company's cut from each trip.
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Rate</button>
        </form>
    </div>
</div>

<!-- Bank Modal -->
<div id="bankModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeBankModal()">&times;</span>
        <h2 style="margin-bottom:1.5rem;">Add Bank Account</h2>
        <form action="<?= base_url('drivers/add_bank/' . $driver->id) ?>" method="post">
            
            <div class="form-group">
                <label class="form-label">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" placeholder="e.g. Chase Bank, HSBC" required>
            </div>

            <div class="form-group">
                <label class="form-label">Account Holder Name</label>
                <input type="text" name="account_name" class="form-control" value="<?= esc($driver->first_name . ' ' . $driver->last_name) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Account Number</label>
                <input type="text" name="account_number" class="form-control" required>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Routing Number (Optional)</label>
                    <input type="text" name="routing_number" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">SWIFT / BIC (Optional)</label>
                    <input type="text" name="swift_code" class="form-control">
                </div>
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="is_default" id="bankDefault" checked>
                <label for="bankDefault" style="margin:0; font-size:0.9rem;">Set as default payout account</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Link Bank Account</button>
        </form>
    </div>
</div>

<script>
    const tabActions = {
        'trips': '<button id="bulkPrintTripBtn" onclick="bulkPrintSelectedTrips()" class="btn btn-outline" style="display:none; align-items:center; gap:6px; padding: 10px 20px; font-weight: 600;"><i data-lucide="printer" width="18"></i> Bulk Print (<span id="selectedTripCount">0</span>)</button>',
        'wallet': '<button onclick="openWalletModal()" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; padding: 10px 20px; font-weight: 600;"><i data-lucide="dollar-sign" width="18"></i> Record Payout</button>',
        'bank': '<button onclick="openBankModal()" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; padding: 10px 20px; font-weight: 600;"><i data-lucide="plus" width="18"></i> Add Bank Account</button>',
    };

    function switchTab(tabName, el) {
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.getElementById('tab-' + tabName).style.display = 'block';
        document.querySelectorAll('.p-tab').forEach(t => t.classList.remove('active'));
        if(el) el.classList.add('active');

        // Update actions
        const actionsDiv = document.getElementById('tab-actions');
        if(actionsDiv) {
            actionsDiv.innerHTML = tabActions[tabName] || '';
            if(typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

    // Initialize first tab actions
    document.addEventListener('DOMContentLoaded', () => {
        const activeTab = document.querySelector('.p-tab.active');
        if(activeTab) {
            const firstTabName = activeTab.getAttribute('onclick').match(/'([^']+)'/)[1];
            switchTab(firstTabName, activeTab);
        }
    });
    function openWalletModal() { document.getElementById('walletModal').classList.add('active'); }
    function closeWalletModal() { document.getElementById('walletModal').classList.remove('active'); }
    
    function openRateModal() { document.getElementById('rateModal').classList.add('active'); }
    function closeRateModal() { document.getElementById('rateModal').classList.remove('active'); }

    function openBankModal() { document.getElementById('bankModal').classList.add('active'); }
    function closeBankModal() { document.getElementById('bankModal').classList.remove('active'); }

    function openHistoryModal() { document.getElementById('historyModal').classList.add('active'); }
    function closeHistoryModal() { document.getElementById('historyModal').classList.remove('active'); }

    function toggleAllTrips(source) {
        const checkboxes = document.querySelectorAll('#tab-trips .trip-checkbox');
        checkboxes.forEach(cb => cb.checked = source.checked);
        updateTripBulkBtn();
    }

    function updateTripBulkBtn() {
        const checkboxes = document.querySelectorAll('#tab-trips .trip-checkbox:checked');
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
        const checkboxes = document.querySelectorAll('#tab-trips .trip-checkbox:checked');
        const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
        if (ids) {
            window.open('<?= base_url('dispatch/trips/bulk_print') ?>?ids=' + ids, '_blank');
        }
    }

    async function showPrintHistory(txId) {
        openHistoryModal();
        const content = document.getElementById('historyContent');
        content.innerHTML = '<div style="text-align:center; padding:2rem; color:var(--text-secondary);">Loading...</div>';

        try {
            const response = await fetch(`<?= base_url('drivers/get_print_history/') ?>${txId}`);
            const data = await response.json();

            if (data.success && data.logs.length > 0) {
                let html = `
                    <div style="background:var(--bg-body); padding:1rem; border-radius:8px; margin-bottom:1.5rem; text-align:center; border:1px solid var(--border-color);">
                        <div style="font-size:0.85rem; color:var(--text-secondary);">Total Prints</div>
                        <div style="font-size:2rem; font-weight:800; color:var(--primary);">${data.count}</div>
                    </div>
                `;
                html += '<div style="display:flex; flex-direction:column; gap:8px;">';
                data.logs.forEach((log, index) => {
                    const date = new Date(log.printed_at);
                    html += `
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:6px; font-size:0.85rem;">
                            <span style="color:var(--text-secondary); font-weight:600;">#${data.count - index}</span>
                            <span style="color:var(--text-primary);">${date.toLocaleString()}</span>
                        </div>
                    `;
                });
                html += '</div>';
                content.innerHTML = html;
            } else {
                content.innerHTML = '<div style="text-align:center; padding:3rem; color:var(--text-secondary); opacity:0.6;"><i data-lucide="info" width="32" style="margin-bottom:1rem;"></i><p>No print records found.</p></div>';
            }
            if(typeof lucide !== 'undefined') lucide.createIcons();
        } catch (error) {
            content.innerHTML = '<div style="color:var(--danger); padding:1rem;">Error loading history.</div>';
        }
    }

    function togglePayoutMethods(val) {
        document.getElementById('payoutMethodSection').style.display = (val === 'withdrawal') ? 'block' : 'none';
        // Reset bank section if flipping back to deposit
        if (val !== 'withdrawal') document.getElementById('bankListSection').style.display = 'none';
    }

    function toggleBankList(val) {
        document.getElementById('bankListSection').style.display = (val === 'bank') ? 'block' : 'none';
    }

    <?php if(session()->getFlashdata('success')): ?>
        alert('<?= session()->getFlashdata('success') ?>');
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        alert('<?= session()->getFlashdata('error') ?>');
    <?php endif; ?>
</script>

<?= $this->endSection() ?>
