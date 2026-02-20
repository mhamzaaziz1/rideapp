<div class="trip-card">
    
    <!-- 1. Status Column -->
    <div style="text-align:center;">
        <span class="status-badge status-<?= $trip->status ?>" style="display:block; margin-bottom:4px; font-size:0.7rem;">
            <?= ucfirst($trip->status) ?>
        </span>
        <div style="font-family:monospace; font-weight:700; color:var(--text-primary); font-size:0.85rem;">
            #<?= $trip->trip_number ?>
        </div>
        <div style="font-size:0.7rem; color:var(--text-secondary); margin-top:4px; font-weight:500; background:var(--bg-body); padding:2px 6px; border-radius:4px; display:inline-block;">
            <?= date('M d', strtotime($trip->created_at)) ?> <span style="opacity:0.6;">|</span> <?= date('H:i', strtotime($trip->created_at)) ?>
        </div>
    </div>

    <!-- 2. Route Column -->
    <div class="route-visual">
        <div class="route-point">
            <div class="route-icon" style="background:var(--success);"></div>
            <div style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-weight:500;">
                <?= esc($trip->pickup_address) ?>
            </div>
        </div>
        <div class="route-point">
            <div class="route-icon" style="background:var(--danger);"></div>
            <div style="color:var(--text-secondary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                <?= esc($trip->dropoff_address) ?>
            </div>
        </div>
            <!-- Detail Chips -->
            <div style="display:flex; gap:6px; flex-wrap:wrap; margin-top:8px;">
                <!-- Distance -->
                <span class="detail-badge" title="Distance">
                    <i data-lucide="navigation" width="12"></i> <?= $trip->distance_miles ?> mi
                </span>
                
                <!-- Vehicle -->
                <span class="detail-badge vehicle-badge" title="Vehicle: <?= ucfirst($trip->vehicle_type ?? 'Standard') ?>">
                    <i data-lucide="car-front" width="12"></i> <?= ucfirst(substr($trip->vehicle_type ?? 'Std', 0, 8)) ?>
                </span>
                
                <!-- Passengers -->
                 <?php if(!empty($trip->passengers)): ?>
                    <span class="detail-badge" title="<?= $trip->passengers ?> Passengers">
                        <i data-lucide="users" width="12"></i> <?= $trip->passengers ?>
                    </span>
                <?php endif; ?>
                
                <!-- Fare & Payment -->
                <div class="detail-badge fare-badge" title="Payment: <?= ucfirst($trip->payment_method ?? 'card') ?>">
                    <i data-lucide="<?= ($trip->payment_method == 'cash') ? 'banknote' : 'credit-card' ?>" width="12" style="margin-right:4px;"></i> 
                    $<?= number_format($trip->fare_amount, 2) ?>
                </div>

                <!-- Earnings (Admin/Driver View) -->
                <?php if(isset($trip->driver_earnings) && $trip->driver_earnings > 0): ?>
                    <span class="detail-badge" style="background:rgba(16, 185, 129, 0.1); color:var(--success); border-color:transparent;" title="Driver Earnings">
                        <i data-lucide="pie-chart" width="12"></i> $<?= number_format($trip->driver_earnings, 2) ?>
                    </span>
                <?php endif; ?>
            </div>
    </div>

    <!-- 3. Customer/Driver Info -->
    <div>
        <div style="font-weight:600; font-size:0.9rem; margin-bottom:2px;">
            <?= esc($trip->c_first ?? 'Guest') ?>
            <?php if(!empty($trip->passengers)): ?>
                <span style="color:var(--text-secondary); font-size:0.75rem; margin-left:4px;" title="Passengers">
                    <i data-lucide="users" width="10" style="vertical-align:middle"></i> <?= $trip->passengers ?>
                </span>
            <?php endif; ?>
            <?php if(isset($trip->c_rating)): ?>
                <span style="color:var(--warning); font-size:0.7rem; font-weight:bold;">★ <?= number_format($trip->c_rating, 1) ?></span>
            <?php endif; ?>
        </div>
        <div style="font-size:0.8rem; color:var(--text-secondary);">
            <?php if($trip->d_first): ?>
                <i data-lucide="car" width="12" style="vertical-align:text-bottom"></i>
                <?= esc($trip->d_first) ?>
                <?php if(isset($trip->d_rating)): ?>
                    <span style="color:var(--warning); font-size:0.7rem; font-weight:bold;">★ <?= number_format($trip->d_rating, 1) ?></span>
                <?php endif; ?>
            <?php else: ?>
                <span style="color:var(--warning);">Unassigned</span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 4. Action Column -->
    <div style="text-align:right;">
        <?php if($type == 'queue' && !$trip->driver_id): ?>
            <button onclick="openAssignModal(<?= $trip->id ?>)" class="btn-xs btn-primary" style="width:100%; margin-bottom:4px;">Assign</button>
        <?php else: ?>
            <div class="dropdown" style="position:relative; display:inline-block;">
                <button class="btn-xs btn-outline" onclick="toggleDropdown(this)" style="display:flex; align-items:center; gap:4px; padding:4px 8px;">
                    Actions <i data-lucide="chevron-down" width="12"></i>
                </button>
                <div class="dropdown-menu" style="right:0; top:100%; min-width:140px;">
                    <?php if($trip->status == 'completed'): ?>
                        <?php if($trip->driver_id): ?>
                            <?php if ($trip->driver_is_rated == 0): ?>
                                <button onclick="openRateModal(<?= $trip->id ?>, 'driver', <?= $trip->driver_id ?>, <?= $trip->customer_id ?? 'null' ?>)" class="dropdown-item">
                                    <i data-lucide="star" width="14" style="color:var(--warning)"></i> Rate Driver
                                </button>
                            <?php else: ?>
                                <div class="dropdown-item" style="color:var(--text-secondary); cursor:default;">
                                    <i data-lucide="check" width="14" style="color:var(--success)"></i> Driver Rated
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if($trip->customer_id): ?>
                            <?php if ($trip->customer_is_rated == 0): ?>
                                <button onclick="openRateModal(<?= $trip->id ?>, 'customer', <?= $trip->customer_id ?>, <?= $trip->driver_id ?? 'null' ?>)" class="dropdown-item">
                                    <i data-lucide="star" width="14" style="color:var(--info)"></i> Rate Customer
                                </button>
                             <?php else: ?>
                                <div class="dropdown-item" style="color:var(--text-secondary); cursor:default;">
                                    <i data-lucide="check" width="14" style="color:var(--success)"></i> Cust. Rated
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        <div style="border-top:1px solid var(--border-color); margin:4px 0;"></div>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('dispatch/trips/view/'.$trip->id) ?>" class="dropdown-item">
                        <i data-lucide="eye" width="14"></i> View Details
                    </a>
                    <a href="<?= base_url('dispatch/trips/edit/'.$trip->id) ?>" class="dropdown-item">
                        <i data-lucide="edit-2" width="14"></i> Edit Trip
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>

<style>
    .btn-xs { 
        padding: 4px 8px; font-size: 0.75rem; border-radius: 4px; border:none; cursor:pointer; font-weight:600; text-decoration:none;
    }
    .btn-xs.btn-primary { background: var(--primary); color:white; }
    .btn-xs.btn-info { background: var(--info); color:white; border:1px solid var(--info); }
    .btn-xs.btn-info:hover { background: var(--info-hover); }
    .btn-xs.btn-outline { border:1px solid var(--border-color); color:var(--text-secondary); background:transparent; }
    .btn-xs.btn-outline:hover { border-color:var(--text-primary); color:var(--text-primary); }
    /* Detail Badges in Card */
    .detail-badge {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 0.75rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        padding: 2px 8px;
        border-radius: 12px;
        color: var(--text-secondary);
        font-weight: 500;
        cursor: help;
        transition: all 0.15s;
    }
    .detail-badge:hover {
        background: var(--bg-surface-hover);
        color: var(--text-primary);
        border-color: var(--primary);
    }
    
    .vehicle-badge { color: var(--info); border-color: rgba(59, 130, 246, 0.2); background: rgba(59, 130, 246, 0.05); }
    .fare-badge { 
        color: var(--text-primary); 
        font-weight: 700; 
        background: var(--bg-surface); 
        border-color: var(--primary);
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
</style>
