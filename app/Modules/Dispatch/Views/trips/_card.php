<div class="trip-wrapper" id="trip-wrapper-<?= $trip->id ?>">
    <div class="trip-card" onclick="toggleTripDetails(<?= $trip->id ?>)">
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
                    <i data-lucide="navigation" width="12"></i> <?= number_format($trip->distance_miles ?? 0, 1) ?> mi
                </span>
                
                <!-- Vehicle -->
                <span class="detail-badge vehicle-badge" title="Vehicle: <?= ucfirst($trip->vehicle_type ?? 'Standard') ?>">
                    <i data-lucide="car-front" width="12"></i> <?= ucfirst(substr($trip->vehicle_type ?? 'Std', 0, 8)) ?>
                </span>
                
                <!-- Dispute Warnings -->
                <?php if(isset($trip->dispute) && $trip->dispute): ?>
                    <span class="detail-badge" style="background:rgba(239, 68, 68, 0.1); color:var(--danger); border-color:transparent;" title="Active Dispute">
                        <i data-lucide="alert-triangle" width="12"></i> Dispute #<?= $trip->dispute->id ?>
                    </span>
                <?php endif; ?>
                <?php if(isset($trip->linked_dispute) && $trip->linked_dispute): ?>
                    <span class="detail-badge" style="background:rgba(59, 130, 246, 0.1); color:var(--info); border-color:transparent;" title="Linked Delivery/Return Trip">
                        <i data-lucide="package" width="12"></i> Linked RTN
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3. Customer/Driver Info -->
        <div>
            <div style="font-weight:600; font-size:0.9rem; margin-bottom:2px; display:flex; align-items:center; gap:4px;">
                <i data-lucide="user" width="12" style="color:var(--text-secondary)"></i> <?= esc($trip->c_first ?? 'Guest') ?>
                <?php if(!empty($trip->passengers)): ?>
                    <span style="color:var(--text-secondary); font-size:0.75rem;" title="Passengers">
                        (<?= $trip->passengers ?>)
                    </span>
                <?php endif; ?>
            </div>
            <div style="font-size:0.8rem; color:var(--text-secondary); display:flex; align-items:center; gap:4px;">
                <i data-lucide="steering-wheel" width="12"></i>
                <?php if($trip->d_first): ?>
                    <?= esc($trip->d_first) ?>
                <?php else: ?>
                    <span style="color:var(--warning);">Unassigned</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4. Action Column -->
        <div style="text-align:right; display:flex; align-items:center; justify-content:flex-end; gap:6px;">
            <?php if($type == 'queue' && !$trip->driver_id): ?>
                <button onclick="event.stopPropagation(); openAssignModal(<?= $trip->id ?>)" class="btn-xs btn-primary">Assign</button>
            <?php else: ?>
                <div class="dropdown" style="position:relative; display:inline-block;">
                    <button class="btn-xs btn-outline" onclick="event.stopPropagation(); toggleDropdown(this)" style="display:flex; align-items:center; gap:4px; padding:4px 8px;">
                        Actions <i data-lucide="chevron-down" width="12"></i>
                    </button>
                    <div class="dropdown-menu" style="right:0; top:100%; min-width:140px;">
                        <?php if($trip->status == 'completed'): ?>
                            <?php if($trip->driver_id): ?>
                                <?php if ($trip->driver_is_rated == 0): ?>
                                    <button onclick="openRateModal(<?= $trip->id ?>, 'driver', <?= $trip->driver_id ?>, <?= $trip->customer_id ?? 'null' ?>)" class="dropdown-item">
                                        <i data-lucide="star" width="14" style="color:var(--warning)"></i> Rate Driver
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if($trip->customer_id): ?>
                                <?php if ($trip->customer_is_rated == 0): ?>
                                    <button onclick="openRateModal(<?= $trip->id ?>, 'customer', <?= $trip->customer_id ?>, <?= $trip->driver_id ?? 'null' ?>)" class="dropdown-item">
                                        <i data-lucide="star" width="14" style="color:var(--info)"></i> Rate Customer
                                    </button>
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
                        <a href="<?= base_url('dispatch/trips/print/'.$trip->id) ?>" target="_blank" class="dropdown-item">
                            <i data-lucide="printer" width="14"></i> Print Receipt
                        </a>
                        <div style="border-top:1px solid var(--border-color); margin:4px 0;"></div>
                        <button type="button" onclick="event.stopPropagation(); openDisputeModal(<?= $trip->id ?>, <?= $trip->customer_id ?? 'null' ?>, <?= $trip->driver_id ?? 'null' ?>)" class="dropdown-item text-danger" style="color: var(--danger);">
                            <i data-lucide="alert-triangle" width="14" style="color:var(--danger)"></i> Dispute Trip
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <button class="btn-xs btn-outline" style="padding:4px; height:28px; width:28px; display:flex; align-items:center; justify-content:center;" onclick="event.stopPropagation(); toggleTripDetails(<?= $trip->id ?>)" title="Expand Details">
                <i data-lucide="maximize-2" width="14"></i>
            </button>
        </div>
    </div>

    <!-- Expanded Max Details Panel -->
    <div id="trip-details-<?= $trip->id ?>" style="display:none; padding:1.5rem; border-top:1px dashed var(--border-color); background:var(--bg-body); border-radius:0 0 var(--radius-sm) var(--radius-sm);">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:2rem;">
            
            <!-- Left Info Block -->
            <div>
                <h4 style="font-size:0.85rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">Trip Notes & GPS Logs</h4>
                
                <div style="font-family:monospace; font-size:0.8rem; background:var(--bg-surface); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); margin-bottom:1rem;">
                    <div><strong style="color:var(--text-secondary);">Pickup Data:</strong> <?= $trip->pickup_lat ?>, <?= $trip->pickup_lng ?></div>
                    <div style="margin-top:4px;"><strong style="color:var(--text-secondary);">Dropoff Data:</strong> <?= $trip->dropoff_lat ?>, <?= $trip->dropoff_lng ?></div>
                </div>

                <?php if(!empty($trip->notes)): ?>
                    <div style="font-size:0.9rem; color:var(--text-primary); border-left:3px solid var(--primary); padding-left:12px; margin-bottom:1rem; font-style:italic;">
                        <?= esc($trip->notes) ?>
                    </div>
                <?php endif; ?>
                
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; font-size:0.85rem;">
                    <div style="background:var(--bg-surface); padding:8px; border-radius:var(--radius-sm); border:1px solid var(--border-color); text-align:center;">
                        <div style="color:var(--text-secondary); font-size:0.75rem;">Created</div>
                        <div style="font-weight:600;"><?= date('M j, Y h:i A', strtotime($trip->created_at)) ?></div>
                    </div>
                    <?php if($trip->completed_at): ?>
                    <div style="background:rgba(16, 185, 129, 0.05); padding:8px; border-radius:var(--radius-sm); border:1px solid rgba(16, 185, 129, 0.2); text-align:center;">
                        <div style="color:var(--success); font-size:0.75rem;">Completed</div>
                        <div style="font-weight:600; color:var(--success);"><?= date('M j, Y h:i A', strtotime($trip->completed_at)) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Info Block -->
            <div>
                <!-- Dispute Cross-Tracking -->
                <?php if (isset($trip->dispute) && $trip->dispute): ?>
                    <div style="background:rgba(239, 68, 68, 0.05); border:1px solid rgba(239, 68, 68, 0.3); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
                            <div style="font-weight:700; color:var(--danger); display:flex; align-items:center; gap:6px;">
                                <i data-lucide="alert-triangle" width="16"></i> Active Dispute #DSP-<?= $trip->dispute->id ?>
                            </div>
                            <span style="font-size:0.75rem; background:rgba(239, 68, 68, 0.1); color:var(--danger); padding:2px 6px; border-radius:4px; font-weight:600; text-transform:uppercase;"><?= $trip->dispute->status ?></span>
                        </div>
                        <div style="font-size:0.85rem; color:var(--text-primary); margin-bottom:0.75rem;"><strong>Type:</strong> <?= $trip->dispute->dispute_type ?></div>
                        <a href="<?= base_url('admin/disputes/view/' . $trip->dispute->id) ?>" class="btn-xs btn-outline" style="border-color:var(--danger); color:var(--danger); font-size:0.8rem; display:inline-flex; align-items:center; gap:4px;">
                            View Dispute Logs <i data-lucide="arrow-right" width="12"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if (isset($trip->linked_dispute) && $trip->linked_dispute): ?>
                    <div style="background:rgba(59, 130, 246, 0.05); border:1px solid rgba(59, 130, 246, 0.3); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1rem;">
                        <div style="display:flex; align-items:center; gap:6px; font-weight:700; color:var(--info); margin-bottom:0.5rem;">
                            <i data-lucide="package-search" width="16"></i> Registered Lost Item Delivery
                        </div>
                        <p style="font-size:0.85rem; margin-bottom:0.5rem; color:var(--text-secondary);">This is an automatically sequenced return trip linked to a passenger dispute claim.</p>
                        <a href="<?= base_url('admin/disputes/view/' . $trip->linked_dispute->id) ?>" class="btn-xs btn-outline" style="border-color:var(--info); color:var(--info); font-size:0.8rem; display:inline-flex; align-items:center; gap:4px;">
                            View Parent Dispute #DSP-<?= $trip->linked_dispute->id ?>
                        </a>
                    </div>
                <?php endif; ?>

                <h4 style="font-size:0.85rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:1rem;">Billing Ledger</h4>
                <div style="border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface);">
                    <div style="display:flex; justify-content:space-between; padding:8px 12px; border-bottom:1px solid var(--border-color); font-size:0.85rem;">
                        <span style="color:var(--text-secondary);">Payment Method:</span>
                        <strong style="text-transform:uppercase;"><?= $trip->payment_method ?? 'CREDIT CARD' ?></strong>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding:8px 12px; border-bottom:1px solid var(--border-color); font-size:0.85rem;">
                        <span style="color:var(--text-secondary);">Total Fare:</span>
                        <strong>$<?= number_format($trip->fare_amount ?? 0, 2) ?></strong>
                    </div>
                    <?php if (isset($trip->driver_earnings) && $trip->driver_earnings > 0): ?>
                    <div style="display:flex; justify-content:space-between; padding:8px 12px; font-size:0.85rem; background:rgba(16, 185, 129, 0.02);">
                        <span style="color:var(--text-secondary);">Driver Commission & Earnings:</span>
                        <strong style="color:var(--success);">+$<?= number_format($trip->driver_earnings ?? 0, 2) ?></strong>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
