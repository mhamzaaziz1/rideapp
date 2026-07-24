<tr id="trip-wrapper-<?= $trip->id ?>" onclick="openTripDetailsModal(<?= htmlspecialchars(json_encode($trip)) ?>)">
    <td onclick="event.stopPropagation()">
        <input type="checkbox" class="trip-checkbox" value="<?= $trip->id ?>" onclick="updateTripBulkBtn()">
    </td>
    <td>
        <span class="status-badge status-<?= $trip->status ?>" style="margin-bottom:6px;"><?= ucfirst($trip->status) ?></span>
        <div style="font-weight:700; color:var(--text-dark); margin-bottom:2px;">#<?= $trip->trip_number ?></div>
        <div class="text-muted"><?= date('M d', strtotime($trip->created_at)) ?> | <?= date('H:i', strtotime($trip->created_at)) ?></div>
    </td>
    <td>
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#22c55e;"></div>
            <div style="font-weight:500; font-size:0.9rem; color:var(--text-dark);"><?= esc($trip->pickup_address) ?></div>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <div style="width:8px;height:8px;border-radius:50%;background:#ef4444;"></div>
            <div class="text-muted"><?= esc($trip->dropoff_address) ?></div>
        </div>
        <div style="display:flex; gap:6px; margin-top:8px;">
            <span style="font-size:0.75rem; background:#f1f5f9; color:var(--text-gray); padding:2px 8px; border-radius:4px;"><i data-lucide="navigation" width="12"></i> <?= number_format($trip->distance_miles ?? 0, 1) ?> mi</span>
            <span style="font-size:0.75rem; background:#e0f2fe; color:#0284c7; padding:2px 8px; border-radius:4px;"><i data-lucide="car-front" width="12"></i> <?= ucfirst(substr($trip->vehicle_type ?? 'Std', 0, 8)) ?></span>
            <?php if(isset($trip->dispute) && $trip->dispute): ?>
                <span style="font-size:0.75rem; background:#fee2e2; color:#ef4444; padding:2px 8px; border-radius:4px;"><i data-lucide="alert-triangle" width="12"></i> Dispute</span>
            <?php endif; ?>
        </div>
    </td>
    <td>
        <div style="font-weight:600; color:var(--text-dark); margin-bottom:6px; display:flex; align-items:center; gap:6px;">
            <i data-lucide="user" width="14" class="text-muted"></i> 
            <span><?= esc($trip->c_first ?? 'Guest') ?></span>
            <?php if(!empty($trip->passengers)): ?>
                <span style="color:var(--text-gray); font-size:0.7rem; background:#f1f5f9; padding:2px 6px; border-radius:4px;" title="Passengers"><?= $trip->passengers ?></span>
            <?php endif; ?>
        </div>
        <div class="text-muted" style="display:flex; align-items:center; gap:6px;">
            <i data-lucide="steering-wheel" width="14"></i> 
            <?php if($trip->d_first): ?>
                <span style="color:var(--text-dark); font-weight:500;"><?= esc($trip->d_first) ?></span>
            <?php else: ?>
                <span style="color:#f59e0b; font-weight:600; font-size:0.75rem; text-transform:uppercase;">Unassigned</span>
            <?php endif; ?>
        </div>
    </td>
    <td>
        <div style="font-size: 1.15rem; font-weight: 700; color: #0284c7; margin-bottom:2px;">$<?= number_format($trip->fare_amount ?? 0, 2) ?></div>
        <div class="text-muted" style="text-transform:uppercase; font-size:0.75rem; display:flex; align-items:center; gap:4px;"><i data-lucide="credit-card" width="12"></i> <?= esc($trip->payment_method ?? 'Cash') ?></div>
    </td>
    <td onclick="event.stopPropagation()">
        <div style="display:flex; align-items:center; gap:6px;">
            <?php if($type == 'queue' && !$trip->driver_id): ?>
                <button onclick="event.stopPropagation(); openAssignModal(<?= $trip->id ?>)" style="background:var(--primary-blue); color:white; border:none; padding:6px 12px; border-radius:6px; font-weight:600; cursor:pointer;">Assign</button>
            <?php else: ?>
                <div class="dropdown" style="position:relative; display:inline-block;">
                    <button class="btn-outline-action" onclick="event.stopPropagation(); toggleDropdown(this)" style="padding:6px 10px; font-size:0.85rem;">
                        Actions <i data-lucide="chevron-down" width="14"></i>
                    </button>
                    <div class="dropdown-menu" style="right:0; top:100%; min-width:160px; padding:4px 0;">
                        <?php if(in_array($trip->status, ['active', 'dispatching'])): ?>
                            <button onclick="event.stopPropagation(); window.updateTripStatus(<?= $trip->id ?>, 'completed')" class="dropdown-item" style="color:#16a34a;">
                                <i data-lucide="check-circle" width="14"></i> Mark Complete
                            </button>
                        <?php endif; ?>
                        <?php if(!in_array($trip->status, ['completed', 'cancelled'])): ?>
                            <button onclick="event.stopPropagation(); window.updateTripStatus(<?= $trip->id ?>, 'cancelled')" class="dropdown-item text-danger">
                                <i data-lucide="x-circle" width="14"></i> Cancel Trip
                            </button>
                        <?php endif; ?>

                        <?php if($trip->status != 'cancelled'): ?>
                            <div style="border-top:1px solid var(--border-light); margin:4px 0;"></div>
                            <?php if($trip->driver_id && empty($trip->system_rated_driver)): ?>
                                <button onclick="event.stopPropagation(); window.openRateModal(<?= htmlspecialchars(json_encode($trip)) ?>, 'driver')" class="dropdown-item">
                                    <i data-lucide="star" width="14" style="color:#eab308"></i> Rate Driver
                                </button>
                            <?php endif; ?>
                            <?php if($trip->customer_id && empty($trip->system_rated_customer)): ?>
                                <button onclick="event.stopPropagation(); window.openRateModal(<?= htmlspecialchars(json_encode($trip)) ?>, 'customer')" class="dropdown-item">
                                    <i data-lucide="star" width="14" style="color:#0284c7"></i> Rate Customer
                                </button>
                            <?php endif; ?>
                            <div style="border-top:1px solid var(--border-light); margin:4px 0;"></div>
                            <a href="<?= base_url('dispatch/trips/view/'.$trip->id) ?>" class="dropdown-item"><i data-lucide="eye" width="14"></i> View Details</a>
                            <a href="<?= base_url('dispatch/trips/edit/'.$trip->id) ?>" class="dropdown-item"><i data-lucide="edit-2" width="14"></i> Edit Trip</a>
                        <?php else: ?>
                            <div style="border-top:1px solid var(--border-light); margin:4px 0;"></div>
                            <a href="<?= base_url('dispatch/trips/view/'.$trip->id) ?>" class="dropdown-item"><i data-lucide="eye" width="14"></i> View Details</a>
                        <?php endif; ?>

                        <a href="<?= base_url('dispatch/trips/print/'.$trip->id) ?>" target="_blank" class="dropdown-item"><i data-lucide="printer" width="14"></i> Print Receipt</a>
                        <div style="border-top:1px solid var(--border-light); margin:4px 0;"></div>
                        <button type="button" onclick="event.stopPropagation(); openDisputeModal(<?= $trip->id ?>, <?= $trip->customer_id ?? 'null' ?>, <?= $trip->driver_id ?? 'null' ?>)" class="dropdown-item text-danger">
                            <i data-lucide="alert-triangle" width="14"></i> Dispute Trip
                        </button>
                    </div>
                </div>
            <?php endif; ?>
            <button class="btn-outline-action" style="padding:6px; display:flex; align-items:center; justify-content:center;" onclick="event.stopPropagation(); openTripDetailsModal(<?= htmlspecialchars(json_encode($trip)) ?>)" title="Expand Details">
                <i data-lucide="maximize-2" width="14"></i>
            </button>
        </div>
    </td>
</tr>
