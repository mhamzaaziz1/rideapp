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
    .profile-tabs { border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem; display: flex; gap: 2rem; }
    .p-tab { padding-bottom: 1rem; font-weight: 600; color: var(--text-secondary); cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.2s; }
    .p-tab:hover { color: var(--text-primary); }
    .p-tab.active { color: var(--primary); border-bottom-color: var(--primary); }

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
</style>

<div style="padding: 2rem; max-width: 1400px; margin: 0 auto;">
    
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

    <!-- Main Content -->
     <div class="profile-tabs">
        <div class="p-tab active" onclick="switchTab('trips', this)">Trip History (<?= count($trips) ?>)</div>
        <div class="p-tab" onclick="switchTab('wallet', this)">Payout History (<?= count($transactions) ?>)</div>
        <div class="p-tab" onclick="switchTab('ratings', this)">Ratings (<?= count($ratings) ?>)</div>
    </div>

    <div id="tab-trips" class="tab-content">
        <?php if(empty($trips)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="map" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No trips found.</p>
            </div>
        <?php else: ?>
            <?php foreach($trips as $t): ?>
            <div class="history-item">
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
                <div style="margin-left:1rem;">
                    <span class="status-badge status-<?= $t->status ?>"><?= $t->status ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div id="tab-wallet" class="tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
            <h3 class="h5">Recent Transactions</h3>
            <div style="display:flex; gap:0.5rem;">
               <button onclick="openWalletModal()" class="btn btn-primary"><i data-lucide="dollar-sign" width="16" style="margin-right:6px"></i> Record Payout</button>
               <a href="<?= base_url('drivers/print_statement/' . $driver->id) ?>" target="_blank" class="btn btn-outline"><i data-lucide="printer" width="16" style="margin-right:6px"></i> Print Statement</a>
               <a href="<?= base_url('drivers/export_statement/' . $driver->id) ?>" class="btn btn-outline"><i data-lucide="download" width="16" style="margin-right:6px"></i> Export CSV</a>
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
                                <?php if ($tx['type'] === 'withdrawal'): ?>
                                    <a href="<?= base_url('drivers/cheque/' . $tx['id']) ?>" target="_blank"
                                       style="font-size:0.78rem; color:var(--primary); text-decoration:none; display:inline-flex; align-items:center; gap:4px; white-space:nowrap;">
                                        🖨 Print
                                    </a>
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
                <select name="type" class="form-control">
                    <option value="deposit">Deposit (Driver Pays Company)</option>
                    <option value="withdrawal">Withdrawal (Company Pays Driver)</option>
                </select>
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

<script>
    function switchTab(tabName, el) {
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        document.getElementById('tab-' + tabName).style.display = 'block';
        document.querySelectorAll('.p-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }
    function openWalletModal() { document.getElementById('walletModal').classList.add('active'); }
    function closeWalletModal() { document.getElementById('walletModal').classList.remove('active'); }
    
    function openRateModal() { document.getElementById('rateModal').classList.add('active'); }
    function closeRateModal() { document.getElementById('rateModal').classList.remove('active'); }

    <?php if(session()->getFlashdata('success')): ?>
        alert('<?= session()->getFlashdata('success') ?>');
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        alert('<?= session()->getFlashdata('error') ?>');
    <?php endif; ?>
</script>

<?= $this->endSection() ?>
