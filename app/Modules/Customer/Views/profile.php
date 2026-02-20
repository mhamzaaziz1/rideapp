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
        <a href="<?= base_url('customers') ?>" style="color:var(--text-secondary); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem;">
            <i data-lucide="arrow-left" width="16"></i> Back to Customers
        </a>
    </div>

    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        
        <!-- Card 1: Profile -->
        <div class="db-card">
            <div>
                <div class="profile-card-header">
                    <div class="p-avatar">
                        <?php if($customer->avatar): ?>
                            <img src="<?= base_url($customer->avatar) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        <?php else: ?>
                            <?= substr($customer->first_name, 0, 1) . substr($customer->last_name, 0, 1) ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-info">
                        <h3>
                            <?= esc($customer->first_name . ' ' . $customer->last_name) ?>
                             <span style="display:inline-flex; align-items:center; gap:2px; font-size:0.85rem; background:rgba(234, 179, 8, 0.15); color:#ca8a04; padding:2px 6px; border-radius:12px; margin-left:8px; vertical-align:middle;">
                                <i data-lucide="star" width="12" fill="currentColor"></i>
                                <?= number_format($customer->rating ?? 5.0, 1) ?>
                            </span>
                        </h3>
                        <div><i data-lucide="phone" width="12"></i> <?= esc($customer->phone) ?></div>
                        <div><i data-lucide="mail" width="12"></i> <?= esc($customer->email) ?></div>
                    </div>
                </div>
            </div>
            <div class="p-details">
                <div class="p-row"><span class="p-label">Acct ID:</span> <span class="p-val">#<?= $customer->id ?></span></div>
                <div class="p-row"><span class="p-label">Status:</span> <span class="p-val status-badge status-<?= $customer->status ?>"><?= ucfirst($customer->status) ?></span></div>
                <div class="p-row"><span class="p-label">Joined:</span> <span class="p-val"><?= date('M d, Y', strtotime($customer->created_at)) ?></span></div>
            </div>
        </div>

        <!-- Card 2: Spending Stats -->
        <div class="db-card">
            <div>
                <div class="stat-header">
                    <span class="stat-title">Total Spend</span>
                    <i data-lucide="trending-up" class="stat-icon"></i>
                </div>
                <div class="stat-main-val">$<?= number_format($stats['total_spent'], 2) ?></div>
                <div class="stat-sub"><?= $stats['total_trips'] ?> trips completed</div>
            </div>
            <div class="stat-rows">
                <div class="stat-row">
                    <span class="p-label">Avg. per trip:</span> 
                    <span class="p-val">$<?= number_format($stats['avg_spend'], 2) ?></span>
                </div>
                 <div class="stat-row">
                    <span class="p-label">Last trip:</span> 
                    <span class="p-val text-success">
                        <?php if(!empty($trips)): ?>
                            $<?= number_format($trips[0]->fare_amount ?? 0, 2) ?>
                        <?php else: ?>
                            $0.00
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Card 3: Payment Methods -->
        <div class="db-card">
             <div>
                <div class="stat-header">
                    <span class="stat-title">Payment Info</span>
                    <i data-lucide="credit-card" width="16" style="color:var(--text-secondary)"></i>
                </div>
                <?php 
                    $defaultCard = null;
                    if(isset($cards)) {
                        foreach($cards as $c) { if($c->is_default) { $defaultCard = $c; break; } }
                    }
                ?>
                <?php if($defaultCard): ?>
                    <div class="stat-main-val" style="font-size:1.5rem;"><?= $defaultCard->card_brand ?> ****<?= $defaultCard->card_last_four ?></div>
                    <div class="stat-sub">Expires <?= str_pad($defaultCard->expiry_month, 2, '0', STR_PAD_LEFT) ?>/<?= substr($defaultCard->expiry_year, -2) ?></div>
                <?php else: ?>
                    <div class="stat-main-val" style="font-size:1.2rem; color:var(--text-secondary);">No Card</div>
                    <div class="stat-sub">Add a payment method</div>
                <?php endif; ?>
            </div>
            <div class="stat-rows">
                 <div class="stat-row">
                    <span class="p-label">Cards stored:</span> 
                    <span class="p-val"><?= isset($cards) ? count($cards) : 0 ?></span>
                </div>
            </div>
        </div>

        <!-- Card 4: Balance -->
        <div class="db-card balance-card">
            <div>
                <div class="stat-header">
                    <span class="stat-title">App Wallet</span>
                    <button onclick="openWalletModal()" class="btn btn-sm btn-outline" style="padding: 2px 8px; font-size: 0.75rem; display:flex; align-items:center; gap:4px;">
                        <i data-lucide="wallet" width="12"></i> Adjust
                    </button>
                </div>
                <div class="stat-main-val text-success">$<?= number_format($stats['wallet_balance'], 2) ?></div>
                <div class="stat-sub">Available Funds</div>
            </div>
            <div class="stat-rows">
                 <div style="font-size:0.8rem; color:var(--text-secondary); line-height:1.4;">
                    Funds can be used for future trips automatically.
                 </div>
            </div>
        </div>

    </div>

    <!-- MAIN CONTENT TABS -->
    <div class="profile-tabs">
        <div class="p-tab active" onclick="switchTab('trips', this)">Trip History (<?= count($trips) ?>)</div>
        <div class="p-tab" onclick="switchTab('wallet', this)">Wallet History</div>
        <div class="p-tab" onclick="switchTab('cards', this)">Cards (<?= isset($cards) ? count($cards) : 0 ?>)</div>
        <div class="p-tab" onclick="switchTab('addresses', this)">Addresses (<?= count($addresses) ?>)</div>
        <div class="p-tab" onclick="switchTab('ratings', this)">Ratings (<?= count($ratings) ?>)</div>
    </div>

    <!-- TRIP HISTORY TAB -->
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

    <!-- WALLET HISTORY TAB -->
    <div id="tab-wallet" class="tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
            <h3 class="h5">Recent Transactions</h3>
            <div style="display:flex; gap:0.5rem;">
               <button onclick="openWalletModal()" class="btn btn-primary"><i data-lucide="plus" width="16" style="margin-right:6px"></i> Add Funds</button>
               <a href="<?= base_url('customers/print_statement/' . $customer->id) ?>" target="_blank" class="btn btn-outline"><i data-lucide="printer" width="16" style="margin-right:6px"></i> Print Statement</a>
               <a href="<?= base_url('customers/export_statement/' . $customer->id) ?>" class="btn btn-outline"><i data-lucide="download" width="16" style="margin-right:6px"></i> Export CSV</a>
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
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
     <!-- RATINGS TAB -->
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

    <!-- ADDRESSES TAB -->
    <div id="tab-addresses" class="tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
            <h3 class="h5">Saved Addresses</h3>
            <button onclick="openAddressModal()" class="btn btn-primary"><i data-lucide="plus" width="16" style="margin-right:6px"></i> Add Address</button>
        </div>

        <?php if(empty($addresses)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="map-pin" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No addresses found.</p>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:1rem;">
                <?php foreach($addresses as $addr): ?>
                    <div style="background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:1.25rem; position:relative;">
                        <?php if($addr->is_default): ?>
                            <span style="position:absolute; top:10px; right:10px; background:var(--primary); color:#fff; font-size:0.7rem; padding:2px 6px; border-radius:4px;">Default</span>
                        <?php endif; ?>
                        
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.5rem;">
                            <i data-lucide="<?= $addr->type == 'Home' ? 'home' : ($addr->type == 'Work' ? 'briefcase' : 'map-pin') ?>" width="16" style="color:var(--text-secondary)"></i>
                            <span style="font-weight:600; font-size:1rem;"><?= esc($addr->type) ?></span>
                        </div>
                        
                        <div style="font-size:0.9rem; color:var(--text-secondary); margin-bottom:0.5rem; line-height:1.4;">
                            <?= esc($addr->address) ?><br>
                            <?= esc($addr->city) ?>, <?= esc($addr->state) ?> <?= esc($addr->zip_code) ?>
                        </div>

                        <?php if($addr->latitude && $addr->longitude): ?>
                            <div style="font-size:0.8rem; color:var(--text-secondary); margin-bottom:1rem; font-family:monospace;">
                                <i data-lucide="crosshair" width="12" style="vertical-align:middle"></i> 
                                <?= $addr->latitude ?>, <?= $addr->longitude ?>
                            </div>
                        <?php endif; ?>

                        <div style="display:flex; gap:0.5rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color);">
                            <button onclick='editAddress(<?= json_encode($addr) ?>)' class="btn btn-sm btn-outline" style="flex:1;">Edit</button>
                            <?php if(!$addr->is_default): ?>
                                <a href="<?= base_url('customers/address/set_default/' . $addr->id) ?>" class="btn btn-sm btn-outline" style="flex:1;">Set Default</a>
                            <?php endif; ?>
                            <a href="<?= base_url('customers/address/delete/' . $addr->id) ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-outline text-danger" style="flex:1;">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- CARDS TAB -->
    <div id="tab-cards" class="tab-content" style="display:none;">
        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
            <h3 class="h5">Saved Cards</h3>
            <button onclick="openCardModal()" class="btn btn-primary"><i data-lucide="plus" width="16" style="margin-right:6px"></i> Add Card</button>
        </div>

        <?php if(empty($cards)): ?>
            <div style="text-align:center; padding:3rem; color:var(--text-secondary); background:var(--bg-surface); border:1px dashed var(--border-color); border-radius:var(--radius-md);">
                <i data-lucide="credit-card" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                <p>No cards saved.</p>
            </div>
        <?php else: ?>
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap:1rem;">
                <?php foreach($cards as $c): ?>
                    <div style="background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); padding:1.25rem; position:relative;">
                        <?php if($c->is_default): ?>
                            <span style="position:absolute; top:10px; right:10px; background:var(--primary); color:#fff; font-size:0.7rem; padding:2px 6px; border-radius:4px;">Default</span>
                        <?php endif; ?>
                        
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:0.5rem;">
                            <i data-lucide="credit-card" width="20" style="color:var(--text-primary)"></i>
                            <span style="font-weight:700; font-size:1.1rem;"><?= esc($c->card_brand) ?></span>
                        </div>
                        
                        <div style="font-size:1.2rem; font-family:monospace; margin-bottom:0.5rem; letter-spacing:2px;">
                            **** **** **** <?= esc($c->card_last_four) ?>
                        </div>

                        <div style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:1rem;">
                            Expires: <?= str_pad($c->expiry_month, 2, '0', STR_PAD_LEFT) ?>/<?= $c->expiry_year ?>
                        </div>

                        <div style="display:flex; gap:0.5rem; margin-top:1rem; padding-top:1rem; border-top:1px solid var(--border-color);">
                            <?php if(!$c->is_default): ?>
                                <a href="<?= base_url('customers/card/set_default/' . $c->id) ?>" class="btn btn-sm btn-outline" style="flex:1;">Set Default</a>
                            <?php else: ?>
                                <button class="btn btn-sm btn-outline" disabled style="flex:1; opacity:0.5; cursor:not-allowed;">Default</button>
                            <?php endif; ?>
                            <a href="<?= base_url('customers/card/delete/' . $c->id) ?>" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-outline text-danger" style="flex:1;">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Wallet Modal -->
<div id="walletModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeWalletModal()">&times;</span>
        <h2 style="margin-bottom:1.5rem;">Adjust Wallet Funds</h2>
        <form action="<?= base_url('customers/add_fund') ?>" method="post">
            <input type="hidden" name="customer_id" value="<?= $customer->id ?>">
            
            <div class="form-group">
                <label class="form-label">Transaction Type</label>
                <select name="type" class="form-control">
                    <option value="deposit">Deposit (Add Funds)</option>
                    <option value="withdrawal">Withdrawal (Refund/Pay Out)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Amount ($)</label>
                <input type="number" name="amount" step="0.01" min="0.01" class="form-control" required>
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required placeholder="e.g. Added bonus credit"></textarea>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Confirm Transaction</button>
        </form>
    </div>
</div>

<!-- Address Modal -->
<div id="addressModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAddressModal()">&times;</span>
        <h2 style="margin-bottom:1.5rem;" id="addrModalTitle">Add New Address</h2>
        <form action="<?= base_url('customers/address/create') ?>" method="post" id="addressForm">
            <input type="hidden" name="customer_id" value="<?= $customer->id ?>">
            
            <div class="form-group">
                <label class="form-label">Address Type</label>
                <select name="type" class="form-control" id="addrType">
                    <option value="Home">Home</option>
                    <option value="Work">Work</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Full Address</label>
                <textarea name="address" class="form-control" rows="2" required id="addrText" placeholder="123 Main St..."></textarea>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" id="addrCity">
                </div>
                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" name="state" class="form-control" id="addrState">
                </div>
            </div>

             <div class="form-group">
                <label class="form-label">Zip Code</label>
                <input type="text" name="zip_code" class="form-control" id="addrZip">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Latitude</label>
                    <input type="text" name="latitude" class="form-control" id="addrLat" placeholder="e.g. 40.7128">
                </div>
                <div class="form-group">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control" id="addrLng" placeholder="e.g. -74.0060">
                </div>
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="is_default" id="addrDefault">
                <label for="addrDefault" style="margin:0; font-size:0.9rem;">Set as default address</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Save Address</button>
        </form>
    </div>
</div>

<!-- Card Modal -->
<div id="cardModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeCardModal()">&times;</span>
        <h2 style="margin-bottom:1.5rem;">Add New Card</h2>
        <form action="<?= base_url('customers/card/create') ?>" method="post">
            <input type="hidden" name="customer_id" value="<?= $customer->id ?>">
            
            <div class="form-group">
                <label class="form-label">Card Number</label>
                <input type="text" name="card_number" class="form-control" placeholder="0000 0000 0000 0000" required minlength="13" maxlength="19">
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Expiry (MM/YY)</label>
                    <input type="text" name="expiry" class="form-control" placeholder="MM/YY" required pattern="\d{2}/\d{2}">
                </div>
                <div class="form-group">
                    <label class="form-label">CVV</label>
                    <input type="text" name="cvv" class="form-control" placeholder="123" required minlength="3" maxlength="4">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Cardholder Name</label>
                 <input type="text" name="card_holder_name" class="form-control" required>
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:8px;">
                <input type="checkbox" name="is_default" id="cardDefault">
                <label for="cardDefault" style="margin:0; font-size:0.9rem;">Set as default payment method</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Add Card</button>
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

    // Card Modal
    function openCardModal() { document.getElementById('cardModal').classList.add('active'); }
    function closeCardModal() { document.getElementById('cardModal').classList.remove('active'); }

    // Address Modal Functions
    function openAddressModal() { 
        document.getElementById('addressModal').classList.add('active'); 
        document.getElementById('addrModalTitle').innerText = 'Add New Address';
        document.getElementById('addressForm').action = '<?= base_url('customers/address/create') ?>';
        document.getElementById('addressForm').reset();
    }
    function closeAddressModal() { document.getElementById('addressModal').classList.remove('active'); }
    
    function editAddress(addr) {
        openAddressModal();
        document.getElementById('addrModalTitle').innerText = 'Edit Address';
        document.getElementById('addressForm').action = '<?= base_url('customers/address/update') ?>/' + addr.id;
        
        document.getElementById('addrType').value = addr.type;
        document.getElementById('addrText').value = addr.address;
        document.getElementById('addrCity').value = addr.city || '';
        document.getElementById('addrState').value = addr.state || '';
        document.getElementById('addrZip').value = addr.zip_code || '';
        document.getElementById('addrLat').value = addr.latitude || '';
        document.getElementById('addrLng').value = addr.longitude || '';
        document.getElementById('addrDefault').checked = addr.is_default == 1;
    }

    // Flash Messages
    <?php if(session()->getFlashdata('success')): ?>
        alert('<?= session()->getFlashdata('success') ?>');
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        alert('<?= session()->getFlashdata('error') ?>');
    <?php endif; ?>
</script>

<?= $this->endSection() ?>
