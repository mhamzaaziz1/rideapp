<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .detail-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid var(--border-color);
    }
    
    .detail-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
    }
    
    .detail-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .card-header {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .info-row {
        display: grid;
        grid-template-columns: 140px 1fr;
        padding: 0.75rem 0;
        border-bottom: 1px dashed var(--border-color);
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.85rem;
    }
    
    .info-value {
        color: var(--text-primary);
        font-size: 0.9rem;
    }
    
    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .desc-box { background: var(--bg-body); padding: 1.5rem; border-radius: var(--radius-md); font-size: 0.95rem; line-height: 1.6; color: var(--text-secondary); border-left: 4px solid var(--danger); margin-bottom: 1rem; margin-top: 1rem;}
    
    .update-form label { font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 8px; display: block; }
    .update-form .form-control, .update-form .form-select { width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-body); color: var(--text-primary); margin-bottom: 1rem; }
    .update-form .form-control:focus, .update-form .form-select:focus { border-color: var(--primary); outline: none; }
    
    .resolution-box { background: rgba(16, 185, 129, 0.05); padding: 1.5rem; border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--radius-md); }
</style>

<div class="detail-container">
    <!-- Header -->
    <div class="detail-header">
        <div>
            <div style="display:flex; align-items:center; gap:1rem; margin-bottom:0.5rem;">
                <h1 class="h3" style="margin:0;">Dispute #DSP-<?= esc($dispute->id) ?></h1>
                <?php
                    $statusStyles = [
                        'open' => 'background:rgba(239, 68, 68, 0.1); color:var(--danger); border:1px solid var(--danger);',
                        'investigating' => 'background:rgba(245, 158, 11, 0.1); color:var(--warning); border:1px solid var(--warning);',
                        'resolved' => 'background:rgba(16, 185, 129, 0.1); color:var(--success); border:1px solid var(--success);',
                        'closed' => 'background:rgba(107, 114, 128, 0.1); color:var(--text-secondary); border:1px solid var(--text-secondary);',
                    ];
                    $style = $statusStyles[$dispute->status] ?? $statusStyles['open'];
                ?>
                <span class="status-badge" style="<?= $style ?>"><?= ucfirst($dispute->status) ?></span>
            </div>
            <div style="color:var(--text-secondary); font-size:0.9rem;">
                Created: <?= date('M j, Y H:i A', strtotime($dispute->created_at)) ?>
            </div>
        </div>
        <div style="display:flex; gap:1rem;">
            <a href="<?= base_url('admin/disputes/edit/'.$dispute->id) ?>" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary); padding: 0.6rem;" title="Edit Dispute">
                <i data-lucide="edit-2" width="18"></i>
            </a>
            <a href="<?= base_url('admin/disputes/delete/'.$dispute->id) ?>" class="btn btn-outline" style="border-color: var(--danger); color: var(--danger); padding: 0.6rem;" title="Delete Dispute" onclick="return confirm('Are you sure you want to delete this dispute? This cannot be undone.');">
                <i data-lucide="trash-2" width="18"></i>
            </a>
            <a href="<?= base_url('admin/disputes') ?>" class="btn btn-outline">
                <i data-lucide="arrow-left" width="16"></i> Back to List
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div style="background:rgba(16, 185, 129, 0.1); color:var(--success); padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:rgba(239, 68, 68, 0.1); color:var(--danger); padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="detail-grid">
        <!-- Left Column -->
        <div>
            <!-- Main Info -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="file-text" width="20"></i> Case Details
                </div>
                
                <div class="info-row">
                    <div class="info-label">Title</div>
                    <div class="info-value" style="font-weight: 600; font-size: 1.05rem;"><?= esc($dispute->title) ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Dispute Type</div>
                    <div class="info-value">
                        <span style="background:var(--bg-body); padding:4px 10px; border-radius:12px; font-size:0.8rem; border:1px solid var(--border-color); display:inline-flex; align-items:center; gap:6px;">
                            <i data-lucide="tag" width="12" style="color:var(--primary);"></i> <?= esc(ucfirst($dispute->dispute_type)) ?>
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">Reporter</div>
                    <div class="info-value">
                        <?php if($dispute->reported_by == 'customer'): ?>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="background:rgba(59, 130, 246, 0.1); color:var(--info); padding:4px; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center;"><i data-lucide="briefcase" width="10"></i></span>
                                <?= esc($dispute->c_first_name . ' ' . $dispute->c_last_name) ?> (Customer)
                            </span>
                        <?php else: ?>
                            <span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="background:rgba(139, 92, 246, 0.1); color:#8b5cf6; padding:4px; border-radius:50%; width:20px; height:20px; display:flex; align-items:center; justify-content:center;"><i data-lucide="car-front" width="10"></i></span>
                                <?= esc($dispute->d_first_name . ' ' . $dispute->d_last_name) ?> (Driver)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="info-row" style="border-bottom:none; padding-bottom:0;">
                    <div class="info-label" style="padding-top:0.75rem;">Description / Claim</div>
                </div>
                <div class="desc-box">
                    <?= nl2br(esc($dispute->description)) ?>
                </div>

                <?php if ($dispute->trip_id && $dispute->dispute_type !== 'Fare Issue'): ?>
                    <div style="margin-bottom: 1.5rem; padding: 1.5rem; border: 1px solid var(--info); background: rgba(59, 130, 246, 0.05); border-radius: var(--radius-md);">
                        <div style="font-weight: 700; color: var(--info); font-size: 1.05rem; display:flex; align-items:center; gap:8px; margin-bottom: 0.5rem;">
                            <i data-lucide="package" width="20"></i>
                            <?php if ($dispute->dispute_type == 'Lost Item'): ?>
                                Lost Item Return Trip
                            <?php else: ?>
                                Dispatch Resolution Trip
                            <?php endif; ?>
                        </div>
                        <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1rem;">
                            <?php if ($dispute->dispute_type == 'Lost Item'): ?>
                                A passenger may have left an item in the driver's vehicle. Dispatch a free return trip to deliver it back — the same driver will be assigned automatically.
                            <?php else: ?>
                                As a resolution to this dispute, you can dispatch an additional trip using the same driver. The route will be reversed from the original trip.
                            <?php endif; ?>
                        </p>
                        <button type="button" onclick="openReturnTripModal()" class="btn" style="background:#2563eb; color:white; font-weight:600; padding:0.6rem 1.25rem; display:flex; align-items:center; gap:8px; border:none; border-radius:var(--radius-sm); cursor:pointer;">
                            <i data-lucide="navigation" width="16"></i>
                            <?php if ($dispute->dispute_type == 'Lost Item'): ?>
                                Dispatch Return Trip
                            <?php else: ?>
                                Dispatch Resolution Trip
                            <?php endif; ?>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Return Trip Modal -->
                <?php if ($dispute->trip_id && $dispute->dispute_type !== 'Fare Issue'): ?>
                <div id="returnTripModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.55); z-index:2000; align-items:center; justify-content:center; padding:1rem;">
                    <div style="background:var(--bg-surface); border-radius:var(--radius-md); width:580px; max-width:100%; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,0.3); border:1px solid var(--border-color); position:relative; margin:auto;">

                        <!-- Modal Header (sticky) -->
                        <div style="display:flex; justify-content:space-between; align-items:center; padding:1.5rem 2rem; border-bottom:1px solid var(--border-color); flex-shrink:0;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:38px; height:38px; background:rgba(59, 130, 246, 0.1); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                    <i data-lucide="truck" width="20" style="color:var(--info);"></i>
                                </div>
                                <div>
                                    <div style="font-weight:700; font-size:1.05rem;"><?= $dispute->dispute_type == 'Lost Item' ? 'Dispatch Return Trip' : 'Dispatch Resolution Trip' ?></div>
                                    <div style="font-size:0.8rem; color:var(--text-secondary);">Dispute #DSP-<?= esc($dispute->id) ?> &bull; Same driver will be assigned</div>
                                </div>
                            </div>
                            <button onclick="closeReturnTripModal()" type="button" style="background:none; border:none; cursor:pointer; color:var(--text-secondary); padding:4px;"><i data-lucide="x" width="20"></i></button>
                        </div>

                        <!-- Scrollable Form Body -->
                        <div style="overflow-y:auto; flex:1; padding:1.5rem 2rem;">
                        <form action="<?= base_url('admin/disputes/arrange_return_trip/' . $dispute->id) ?>" method="POST" id="returnTripForm">

                            <!-- Address Section -->
                            <div style="background:var(--bg-body); border-radius:var(--radius-sm); padding:1.25rem; margin-bottom:1.25rem; border:1px solid var(--border-color); position:relative;">
                                <!-- Route line decoration -->
                                <div style="position:absolute; left:2.1rem; top:4.5rem; bottom:4.5rem; width:2px; background:var(--border-color);"></div>

                                <!-- Pickup -->
                                <div style="margin-bottom:1rem; position:relative;">
                                    <label style="display:block; font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:5px; display:flex; align-items:center; gap:6px;">
                                        <span style="width:10px; height:10px; background:var(--success); border-radius:50%; display:inline-block; border:2px solid white; box-shadow:0 0 0 2px var(--success);"></span>
                                        Pickup Address
                                    </label>
                                    <div style="position:relative;">
                                        <input type="text" id="rtn_pickup_address" name="pickup_address" class="form-control"
                                               placeholder="Type pickup address..." required autocomplete="off"
                                               style="width:100%; padding:0.7rem 1rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface); color:var(--text-primary); font-size:0.9rem;">
                                        <div id="rtn_pickup_suggestions" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); z-index:999; max-height:180px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
                                    </div>
                                    <input type="hidden" id="rtn_pickup_lat" name="pickup_lat">
                                    <input type="hidden" id="rtn_pickup_lng" name="pickup_lng">
                                </div>

                                <!-- Dropoff -->
                                <div style="position:relative;">
                                    <label style="display:block; font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:5px; display:flex; align-items:center; gap:6px;">
                                        <span style="width:10px; height:10px; background:var(--danger); border-radius:50%; display:inline-block; border:2px solid white; box-shadow:0 0 0 2px var(--danger);"></span>
                                        Dropoff Address
                                    </label>
                                    <div style="position:relative;">
                                        <input type="text" id="rtn_dropoff_address" name="dropoff_address" class="form-control"
                                               placeholder="Type dropoff address..." required autocomplete="off"
                                               style="width:100%; padding:0.7rem 1rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface); color:var(--text-primary); font-size:0.9rem;">
                                        <div id="rtn_dropoff_suggestions" style="display:none; position:absolute; top:100%; left:0; right:0; background:var(--bg-surface); border:1px solid var(--border-color); border-radius:var(--radius-sm); z-index:999; max-height:180px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,0.15);"></div>
                                    </div>
                                    <input type="hidden" id="rtn_dropoff_lat" name="dropoff_lat">
                                    <input type="hidden" id="rtn_dropoff_lng" name="dropoff_lng">
                                </div>

                                <input type="hidden" id="rtn_distance_miles" name="distance_miles">
                                <input type="hidden" id="rtn_duration_minutes" name="duration_minutes">
                            </div>

                            <!-- Vehicle Type -->
                            <div style="margin-bottom:1rem;">
                                <label style="display:block; font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:5px;">Vehicle Type</label>
                                <select id="rtn_vehicle_type" name="vehicle_type" onchange="rtnRecalculateFare()"
                                        style="width:100%; padding:0.7rem 1rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface); color:var(--text-primary); font-size:0.9rem;">
                                    <option value="Sedan">Sedan (Standard)</option>
                                    <option value="SUV">SUV (+50%)</option>
                                    <option value="Van">Van (+80%)</option>
                                    <option value="Luxury">Luxury (+100%)</option>
                                </select>
                            </div>

                            <!-- Fare Preview -->
                            <div id="rtn_fare_preview" style="display:none; background:rgba(59,130,246,0.05); border:1px solid rgba(59,130,246,0.2); border-radius:var(--radius-sm); padding:1rem; margin-bottom:1rem;">
                                <div style="font-size:0.75rem; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.75rem;">Fare Estimate</div>
                                <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:0.5rem; margin-bottom:0.75rem; text-align:center;">
                                    <div style="background:var(--bg-surface); padding:0.5rem; border-radius:4px; border:1px solid var(--border-color);">
                                        <div style="font-size:0.7rem; color:var(--text-secondary);">Distance</div>
                                        <div id="rtn_display_dist" style="font-weight:700; color:var(--text-primary); font-size:0.95rem;">—</div>
                                    </div>
                                    <div style="background:var(--bg-surface); padding:0.5rem; border-radius:4px; border:1px solid var(--border-color);">
                                        <div style="font-size:0.7rem; color:var(--text-secondary);">Est. Duration</div>
                                        <div id="rtn_display_dur" style="font-weight:700; color:var(--text-primary); font-size:0.95rem;">—</div>
                                    </div>
                                    <div style="background:var(--bg-surface); padding:0.5rem; border-radius:4px; border:1px solid var(--border-color);">
                                        <div style="font-size:0.7rem; color:var(--text-secondary);">Calc. Fare</div>
                                        <div id="rtn_display_fare" style="font-weight:700; color:var(--info); font-size:0.95rem;">—</div>
                                    </div>
                                </div>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <label style="font-size:0.8rem; font-weight:600; color:var(--text-secondary); white-space:nowrap;">Fare ($):</label>
                                    <input type="number" id="rtn_trip_fare" name="trip_fare" min="0" step="0.01" value="0.00"
                                           style="flex:1; padding:0.5rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem;">
                                    <span style="font-size:0.75rem; color:var(--text-secondary);">(editable)</span>
                                </div>
                            </div>

                            <!-- No addresses yet placeholder -->
                            <div id="rtn_fare_placeholder" style="background:var(--bg-body); border:1px dashed var(--border-color); border-radius:var(--radius-sm); padding:1rem; margin-bottom:1rem; text-align:center; color:var(--text-secondary); font-size:0.85rem;">
                                <i data-lucide="map-pin" width="16" style="opacity:0.4; margin-right:4px;"></i> Enter pickup & dropoff to calculate fare
                            </div>

                            <!-- Trip Notes -->
                            <div style="margin-bottom:1.25rem;">
                                <label style="display:block; font-size:0.8rem; font-weight:600; color:var(--text-secondary); margin-bottom:5px;">Trip Notes</label>
                                <textarea name="trip_notes" id="rtn_trip_notes" rows="2"
                                          style="width:100%; padding:0.7rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-body); color:var(--text-primary); font-size:0.9rem; resize:vertical;"
                                          placeholder="e.g. Passenger forgot a bag. Driver to collect and deliver back."><?= $dispute->dispute_type == 'Lost Item' ? 'Lost Item Return Trip for Dispute #DSP-' . esc($dispute->id) . '. Please collect item from vehicle and deliver to customer.' : esc('Resolution trip for Dispute #DSP-' . $dispute->id . ' (' . $dispute->dispute_type . '). Arranged by admin.') ?></textarea>
                            </div>

                            <?php if ($dispute->driver_id): ?>
                            <div style="margin-bottom:1.25rem; padding:0.75rem; background:rgba(16,185,129,0.05); border:1px solid rgba(16,185,129,0.2); border-radius:var(--radius-sm); display:flex; align-items:center; gap:8px; font-size:0.85rem;">
                                <i data-lucide="steering-wheel" width="16" style="color:var(--success);"></i>
                                <span style="color:var(--text-secondary);">Assigned Driver:</span>
                                <strong><?= esc($dispute->d_first_name . ' ' . $dispute->d_last_name) ?></strong>
                                <span style="color:var(--text-secondary); font-size:0.75rem;">(auto-assigned from original trip)</span>
                            </div>
                            <?php endif; ?>

                            <div style="display:flex; gap:1rem; justify-content:flex-end; padding-top:0.5rem; border-top:1px solid var(--border-color);">
                                <button type="button" onclick="closeReturnTripModal()" class="btn btn-outline">Cancel</button>
                                <button type="submit" id="rtn_submit_btn" class="btn" style="background:#2563eb; color:white; font-weight:600; display:flex; align-items:center; gap:8px; border:none; padding:0.65rem 1.25rem; border-radius:var(--radius-sm); cursor:pointer;">
                                    <i data-lucide="send" width="16"></i> Confirm & Dispatch Trip
                                </button>
                            </div>
                        </form>
                        </div><!-- /scrollable body -->
                    </div><!-- /modal box -->
                </div><!-- /modal overlay -->
                <?php endif; ?>


                <?php if (!empty($dispute->attachment)): ?>
                    <div class="info-row" style="border-bottom:none; padding-bottom:0.5rem; padding-top:1rem; border-top:1px dashed var(--border-color);">
                        <div class="info-label">Attached Evidence</div>
                    </div>
                    <div style="background:var(--bg-body); padding:1rem; border-radius:var(--radius-md); border:1px solid var(--border-color); display:inline-block; margin-top: 0.5rem;">
                        <?php 
                            $ext = pathinfo($dispute->attachment, PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])):
                        ?>
                            <a href="<?= base_url($dispute->attachment) ?>" target="_blank">
                                <img src="<?= base_url($dispute->attachment) ?>" alt="Attachment" style="max-width: 100%; max-height: 300px; border-radius: 8px;">
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url($dispute->attachment) ?>" target="_blank" style="display:flex; align-items:center; gap:8px; color:var(--primary); text-decoration:none; font-weight:600;">
                                <i data-lucide="paperclip" width="18"></i> View Attached Document (<?= strtoupper($ext) ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Resolution Block -->
            <?php if (!empty($dispute->resolution)): ?>
                <div class="detail-card border-success" style="border-color: rgba(16, 185, 129, 0.3);">
                    <div class="card-header" style="color:var(--success);">
                        <i data-lucide="check-circle" width="20"></i> Resolution Decision
                    </div>
                    <div class="resolution-box">
                        <p style="margin-bottom:1rem; line-height:1.6; color:var(--text-primary);"><?= nl2br(esc($dispute->resolution)) ?></p>
                        <div style="font-size:0.8rem; color:var(--text-secondary); border-top:1px solid rgba(16, 185, 129, 0.2); padding-top:1rem; display:flex; justify-content:space-between;">
                            <span>Resolved computing admin: <strong><?= esc($dispute->admin_first_name . ' ' . $dispute->admin_last_name) ?></strong></span>
                            <span><?= date('M j, Y H:i', strtotime($dispute->updated_at)) ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Resolution Notes & Tracking -->
            <div class="detail-card" style="margin-top: 1.5rem;">
                <div class="card-header">
                    <i data-lucide="message-square" width="20"></i> Resolution Center Tracking Log
                </div>
                
                <div style="padding: 1rem 0;">
                    <?php if(empty($comments)): ?>
                        <div style="text-align:center; padding: 2rem; color: var(--text-secondary); font-size: 0.95rem;">
                            No tracking notes have been recorded for this dispute yet.
                        </div>
                    <?php else: ?>
                        <div style="display:flex; flex-direction:column; gap:0.75rem;">
                            <?php foreach($comments as $comment): ?>
                                <div style="background:var(--bg-body); padding:1rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); display:flex; flex-direction:column; gap:6px;">
                                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:4px;">
                                        <div style="font-size:0.85rem; font-weight:600; color:var(--text-primary); display:flex; align-items:center; gap:6px;">
                                            <span style="width:24px; height:24px; background:var(--bg-surface-hover); border-radius:50%; display:flex; align-items:center; justify-content:center; color:var(--primary);"><i data-lucide="user" width="12"></i></span>
                                            <?= esc($comment->first_name . ' ' . $comment->last_name) ?>
                                        </div>
                                        <div style="font-size:0.75rem; color:var(--text-secondary);">
                                            <?= date('M j, Y H:i', strtotime($comment->created_at)) ?>
                                        </div>
                                    </div>
                                    <div style="font-size:0.95rem; color:var(--text-primary); line-height:1.5; white-space:pre-wrap;"><?= esc($comment->comment) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Add Note Form -->
                <?php if (!in_array($dispute->status, ['resolved', 'closed'])): ?>
                <div style="margin-top: 1rem; border-top: 1px dashed var(--border-color); padding-top: 1.5rem;">
                    <form action="<?= base_url('admin/disputes/comment/'.$dispute->id) ?>" method="POST" class="update-form">
                        <label>Add Progress Note</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Log details from a phone call, evidence review, etc..." required></textarea>
                        
                        <div style="text-align: right; margin-top: 0.75rem;">
                            <button type="submit" class="btn btn-primary" style="padding:0.6rem 1.25rem; font-weight:600;">
                                <i data-lucide="plus" width="16" style="margin-right:6px;"></i> Post Note
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Right Column -->
        <div>
            <!-- Customer Card -->
            <?php if(isset($dispute->customer_id) && $dispute->customer_id > 0): ?>
            <div class="detail-card">
                <div class="card-header" style="margin-bottom:1rem; border-bottom:none;">
                    <i data-lucide="user" width="20"></i> Customer Profile
                </div>
                <div style="padding: 0 0.5rem;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:1rem;">
                        <div style="width:48px; height:48px; border-radius:50%; background:rgba(59, 130, 246, 0.1); color:var(--info); display:flex; align-items:center; justify-content:center; font-weight:600; font-size:1.2rem;">
                            <?= substr($dispute->c_first_name, 0, 1) . substr($dispute->c_last_name, 0, 1) ?>
                        </div>
                        <div>
                            <div style="font-weight:700; color:var(--text-primary); font-size:1.1rem;"><?= esc($dispute->c_first_name . ' ' . $dispute->c_last_name) ?></div>
                            <div style="color:var(--text-secondary); font-size:0.85rem; display:flex; align-items:center; gap:4px;"><i data-lucide="phone" width="12"></i> <?= esc($dispute->c_phone) ?></div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-bottom:1.5rem;">
                        <div style="background:var(--bg-body); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); text-align:center;">
                            <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Wallet</div>
                            <div style="font-weight:700; color:var(--text-primary);">$<?= number_format($dispute->c_wallet_balance ?? 0, 2) ?></div>
                        </div>
                        <div style="background:var(--bg-body); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); text-align:center;">
                            <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Avg Rating</div>
                            <div style="font-weight:700; color:var(--warning); display:flex; align-items:center; justify-content:center; gap:4px;"><i data-lucide="star" width="14" style="fill:currentColor;"></i> <?= number_format($dispute->c_rating ?? 5.0, 1) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Driver Card -->
            <?php if(isset($dispute->driver_id) && $dispute->driver_id > 0): ?>
            <div class="detail-card">
                <div class="card-header" style="margin-bottom:1rem; border-bottom:none;">
                    <i data-lucide="car-front" width="20"></i> Driver Profile
                </div>
                <div style="padding: 0 0.5rem;">
                    <div style="display:flex; align-items:center; gap:12px; margin-bottom:1rem;">
                        <div style="width:48px; height:48px; border-radius:50%; background:rgba(139, 92, 246, 0.1); color:#8b5cf6; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:1.2rem;">
                            <?= substr($dispute->d_first_name, 0, 1) . substr($dispute->d_last_name, 0, 1) ?>
                        </div>
                        <div>
                            <div style="font-weight:700; color:var(--text-primary); font-size:1.1rem;"><?= esc($dispute->d_first_name . ' ' . $dispute->d_last_name) ?></div>
                            <div style="color:var(--text-secondary); font-size:0.85rem; display:flex; align-items:center; gap:4px;"><i data-lucide="phone" width="12"></i> <?= esc($dispute->d_phone) ?></div>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem; margin-bottom:1.5rem;">
                        <div style="background:var(--bg-body); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); text-align:center;">
                            <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Wallet</div>
                            <div style="font-weight:700; color:var(--text-primary);">$<?= number_format($dispute->d_wallet_balance ?? 0, 2) ?></div>
                        </div>
                        <div style="background:var(--bg-body); padding:0.75rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); text-align:center;">
                            <div style="font-size:0.75rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px;">Avg Rating</div>
                            <div style="font-weight:700; color:var(--warning); display:flex; align-items:center; justify-content:center; gap:4px;"><i data-lucide="star" width="14" style="fill:currentColor;"></i> <?= number_format($dispute->d_rating ?? 5.0, 1) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Related Trip Card -->
            <?php if(isset($dispute->trip_id) && $dispute->trip_id != null): ?>
            <div class="detail-card" style="background:var(--bg-body);">
                <div class="card-header" style="margin-bottom:1rem; border-bottom:none;">
                    <i data-lucide="map" width="20"></i> Related Trip
                </div>
                <div style="padding: 0 0.5rem;">
                    <div style="font-size:1.5rem; font-family:monospace; font-weight:700; color:var(--primary); margin-bottom:1.5rem;">#<?= esc($dispute->trip_number) ?></div>
                    <a href="<?= base_url('dispatch/trips/view/'.$dispute->trip_id) ?>" class="btn btn-outline" style="width:100%; text-align:center; display:flex; align-items:center; justify-content:center; gap:8px;">
                        Inspect Trip Logs <i data-lucide="arrow-right" width="16"></i>
                    </a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Form -->
            <div class="detail-card">
                <div class="card-header">
                    <i data-lucide="edit-3" width="20"></i> Update Status
                </div>
                <form action="<?= base_url('admin/disputes/update/'.$dispute->id) ?>" method="POST" class="update-form" style="padding: 0.5rem;">
                    
                    <label>Current Status</label>
                    <select name="status" class="form-select">
                        <option value="open" <?= ($dispute->status == 'open') ? 'selected' : '' ?>>Open / Unassigned</option>
                        <option value="investigating" <?= ($dispute->status == 'investigating') ? 'selected' : '' ?>>Investigating</option>
                        <option value="resolved" <?= ($dispute->status == 'resolved') ? 'selected' : '' ?>>Resolved</option>
                        <option value="closed" <?= ($dispute->status == 'closed') ? 'selected' : '' ?>>Closed (No Action)</option>
                    </select>
                    
                    <label>Resolution Remarks <span style="font-weight:normal; opacity:0.6;">(Optional unless resolving)</span></label>
                    <textarea name="resolution" class="form-control" rows="3" placeholder="Detail the outcome or notes taken during investigation..."><?= esc($dispute->resolution) ?></textarea>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%; padding:0.75rem; font-weight:600; font-size:1rem; margin-top:0.5rem;">
                        <i data-lucide="save" width="16" style="margin-right:8px;"></i> Save Changes
                    </button>
                </form>
            </div>

            <!-- Settle Fare Form -->
            <?php if (!in_array($dispute->status, ['resolved', 'closed'])): ?>
            <div class="detail-card" style="border-top: 4px solid var(--info);">
                <div class="card-header" style="color:var(--info);">
                    <i data-lucide="dollar-sign" width="20"></i> Settle Fare
                </div>
                <form action="<?= base_url('admin/disputes/settle/'.$dispute->id) ?>" method="POST" class="update-form" style="padding: 0.5rem;">
                    <p style="font-size:0.85rem; color:var(--text-secondary); margin-bottom:1rem; line-height:1.4;">Issue a refund or payout to resolve this case. This will permanently mark the case as resolved.</p>
                    
                    <label style="margin-bottom: 0.5rem; display:block;">Resolution Method</label>
                    <style>
                        .res-method-card {
                            border: 1px solid var(--border-color);
                            border-radius: var(--radius-sm);
                            padding: 0.75rem;
                            margin-bottom: 0.5rem;
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            gap: 0.75rem;
                            transition: all 0.2s;
                        }
                        .res-method-card:hover { border-color: var(--info); background: rgba(59, 130, 246, 0.05); }
                        .res-method-card input[type="radio"] { margin: 0; transform: scale(1.1); accent-color: var(--info); }
                        .res-method-card:has(input[type="radio"]:checked) { border-color: var(--info); background: rgba(59, 130, 246, 0.05); }
                        .res-method-title { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); margin-bottom: 2px; }
                        .res-method-desc { font-size: 0.75rem; color: var(--text-secondary); line-height: 1.3; }
                    </style>

                    <div style="margin-bottom: 1rem;">
                        <label class="res-method-card">
                            <input type="radio" name="settle_to" value="customer" required>
                            <div class="res-method-info">
                                <div class="res-method-title">Refund Customer</div>
                                <div class="res-method-desc">Issue system funds directly to the Customer's wallet.</div>
                            </div>
                        </label>

                        <label class="res-method-card">
                            <input type="radio" name="settle_to" value="driver" required>
                            <div class="res-method-info">
                                <div class="res-method-title">Payout Driver</div>
                                <div class="res-method-desc">Issue system funds directly to the Driver's wallet.</div>
                            </div>
                        </label>

                        <label class="res-method-card">
                            <input type="radio" name="settle_to" value="transfer_to_customer" required>
                            <div class="res-method-info">
                                <div class="res-method-title">Settle: Deduct Driver &rightarrow; Refund Customer</div>
                                <div class="res-method-desc">Take from Driver's wallet and deposit to Customer's wallet.</div>
                            </div>
                        </label>

                        <label class="res-method-card">
                            <input type="radio" name="settle_to" value="transfer_to_driver" required>
                            <div class="res-method-info">
                                <div class="res-method-title">Settle: Deduct Customer &rightarrow; Payout Driver</div>
                                <div class="res-method-desc">Take from Customer's wallet and deposit to Driver's wallet.</div>
                            </div>
                        </label>
                    </div>

                    <label>Amount ($)</label>
                    <input type="number" step="0.01" min="0.01" name="amount" class="form-control" required placeholder="0.00">

                    <label>Settlement Notes</label>
                    <input type="text" name="notes" class="form-control" required placeholder="Reason for payout...">

                    <button type="submit" class="btn btn-outline" style="width:100%; border-color:var(--info); color:var(--info); font-weight:600; background:rgba(59, 130, 246, 0.05); padding:0.75rem; font-size:1rem; display:flex; align-items:center; justify-content:center; gap:8px;" onclick="return confirm('Are you sure? This will issue funds immediately and resolve the dispute.')">
                        <i data-lucide="check-circle" width="16"></i> Issue Settlement
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    if(typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    function openReturnTripModal() {
        const modal = document.getElementById('returnTripModal');
        if (modal) { modal.style.display = 'flex'; lucide.createIcons(); }
    }

    function closeReturnTripModal() {
        const modal = document.getElementById('returnTripModal');
        if (modal) { modal.style.display = 'none'; }
    }

    window.addEventListener('click', function(e) {
        const modal = document.getElementById('returnTripModal');
        if (modal && e.target === modal) { modal.style.display = 'none'; }
    });

    // ── Address Autocomplete (Nominatim) ─────────────────────────────────────
    let rtnPickupCoords = null;
    let rtnDropoffCoords = null;
    let rtnAutoTimeout = null;

    function rtnSetupAutocomplete(inputId, suggestionsId, onSelect) {
        const input = document.getElementById(inputId);
        const box   = document.getElementById(suggestionsId);
        if (!input || !box) return;

        input.addEventListener('input', function() {
            clearTimeout(rtnAutoTimeout);
            const q = this.value.trim();
            box.style.display = 'none';
            if (q.length < 3) return;

            rtnAutoTimeout = setTimeout(async () => {
                try {
                    const res = await fetch(
                        `https://nominatim.openstreetmap.org/search?format=json&limit=6&q=${encodeURIComponent(q)}&email=admin@rideapp.com`,
                        { headers: { 'Accept-Language': 'en' } }
                    );
                    const results = await res.json();
                    box.innerHTML = '';
                    if (!results.length) {
                        box.innerHTML = '<div style="padding:0.6rem 1rem; color:var(--text-secondary); font-size:0.85rem;">No results found</div>';
                        box.style.display = 'block';
                        return;
                    }
                    results.forEach(r => {
                        const item = document.createElement('div');
                        item.textContent = r.display_name;
                        item.style.cssText = 'padding:0.6rem 1rem; cursor:pointer; font-size:0.85rem; border-bottom:1px solid var(--border-color); color:var(--text-primary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;';
                        item.addEventListener('mouseenter', () => item.style.background = 'var(--bg-surface-hover)');
                        item.addEventListener('mouseleave', () => item.style.background = '');
                        item.addEventListener('click', () => {
                            input.value = r.display_name;
                            box.style.display = 'none';
                            onSelect({ lat: parseFloat(r.lat), lng: parseFloat(r.lon), address: r.display_name });
                        });
                        box.appendChild(item);
                    });
                    box.style.display = 'block';
                } catch(e) { console.error('Nominatim error:', e); }
            }, 400);
        });

        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !box.contains(e.target)) box.style.display = 'none';
        });
    }

    rtnSetupAutocomplete('rtn_pickup_address', 'rtn_pickup_suggestions', function(loc) {
        rtnPickupCoords = loc;
        document.getElementById('rtn_pickup_lat').value = loc.lat;
        document.getElementById('rtn_pickup_lng').value = loc.lng;
        rtnTryCalculate();
    });

    rtnSetupAutocomplete('rtn_dropoff_address', 'rtn_dropoff_suggestions', function(loc) {
        rtnDropoffCoords = loc;
        document.getElementById('rtn_dropoff_lat').value = loc.lat;
        document.getElementById('rtn_dropoff_lng').value = loc.lng;
        rtnTryCalculate();
    });

    // ── Haversine Distance (miles) ────────────────────────────────────────────
    function rtnHaversine(lat1, lon1, lat2, lon2) {
        const R = 3959;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2)**2 +
                  Math.cos(lat1 * Math.PI/180) * Math.cos(lat2 * Math.PI/180) * Math.sin(dLon/2)**2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    // ── Fare Calculation (matches PricingService.php) ─────────────────────────
    const rtnVehicleMultipliers = { sedan: 1.0, suv: 1.5, van: 1.8, luxury: 2.0 };

    function rtnCalculateFare(distMiles, durationMin, vehicleType) {
        const baseF = 5.00, perMile = 1.50, perMin = 0.25, minFare = 10.00;
        const mult  = rtnVehicleMultipliers[vehicleType.toLowerCase()] ?? 1.0;
        const raw   = (baseF + distMiles * perMile + durationMin * perMin) * mult;
        return Math.max(raw, minFare).toFixed(2);
    }

    function rtnRecalculateFare() {
        rtnTryCalculate();
    }

    function rtnTryCalculate() {
        if (!rtnPickupCoords || !rtnDropoffCoords) return;

        const dist = rtnHaversine(rtnPickupCoords.lat, rtnPickupCoords.lng, rtnDropoffCoords.lat, rtnDropoffCoords.lng);
        const dur  = Math.round((dist / 25) * 60); // 25 mph city avg
        const vType = document.getElementById('rtn_vehicle_type').value;
        const fare  = rtnCalculateFare(dist, dur, vType);

        // Store in hidden fields
        document.getElementById('rtn_distance_miles').value  = dist.toFixed(2);
        document.getElementById('rtn_duration_minutes').value = dur;

        // Update display
        document.getElementById('rtn_display_dist').textContent = dist.toFixed(2) + ' mi';
        document.getElementById('rtn_display_dur').textContent  = dur + ' min';
        document.getElementById('rtn_display_fare').textContent = '$' + fare;
        document.getElementById('rtn_trip_fare').value = fare;

        // Show fare panel, hide placeholder
        document.getElementById('rtn_fare_preview').style.display = 'block';
        document.getElementById('rtn_fare_placeholder').style.display = 'none';
    }
</script>
<?= $this->endSection() ?>


