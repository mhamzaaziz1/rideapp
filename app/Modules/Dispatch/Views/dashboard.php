<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<!-- Custom Premium Dashboard Styles -->
<style>
    :root {
        --dash-bg: #f8fafc;
        --card-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.05);
        --accent-orange: #f59e0b;
        --accent-blue: #3b82f6;
        --accent-green: #10b981;
    }

    [data-theme="dark"] {
        --dash-bg: #0f172a;
        --card-shadow: 0 4px 20px -5px rgba(0, 0, 0, 0.5);
    }

    .dashboard-wrapper {
        display: flex;
        flex-direction: column;
        height: calc(100vh - var(--header-height));
        background-color: var(--dash-bg);
        overflow: hidden;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: 280px 280px 340px 1fr 260px;
        gap: 12px;
        padding: 12px;
        height: 100%;
        overflow: hidden;
    }

    /* Column Panels */
    .col-panel {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: var(--card-shadow);
        transition: var(--transition);
    }

    .panel-header {
        padding: 14px 18px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-primary);
        letter-spacing: -0.01em;
    }

    .panel-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        scrollbar-width: thin;
    }

    /* Call Cards (Mockup Style) */
    .call-card {
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        margin-bottom: 12px;
        position: relative;
        cursor: pointer;
        transition: var(--transition);
        background: var(--bg-body);
    }
    .call-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); border-color: var(--primary); }
    
    .call-card .status-badge {
        font-size: 10px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 4px;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: inline-block;
    }
    .status-active { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.3); }
    .status-hold { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .status-ring { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }

    .call-card .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        color: var(--danger);
        opacity: 0.5;
        transition: 0.2s;
    }
    .call-card .close-btn:hover { opacity: 1; }

    /* Trip Details Inputs */
    .premium-input-group {
        position: relative;
        margin-bottom: 16px;
    }
    .premium-input-group label {
        display: block;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-tertiary);
        margin-bottom: 4px;
        letter-spacing: 0.05em;
    }
    .premium-input-group .icon-wrapper {
        position: absolute;
        left: 12px;
        bottom: 10px;
        color: var(--text-tertiary);
    }
    .premium-input-group input, .premium-input-group select, .premium-input-group textarea {
        width: 100%;
        padding: 10px 12px 10px 38px;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 0.85rem;
        transition: var(--transition);
    }
    .premium-input-group select { appearance: none; }
    .premium-input-group input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 0 3px var(--primary-glow); }

    /* Map Stats Bar */
    .map-stats-bar {
        display: flex;
        gap: 12px;
        padding: 12px;
        background: var(--bg-surface);
        border-top: 1px solid var(--border-color);
    }
    .stat-card {
        flex: 1;
        padding: 10px;
        border-radius: 12px;
        text-align: center;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
    }
    .stat-card .val { font-weight: 800; font-size: 1.1rem; color: var(--text-primary); }
    .stat-card .lbl { font-size: 10px; color: var(--text-secondary); text-transform: uppercase; }

    /* VIP Badge */
    .vip-tag {
        background: #f59e0b;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 4px;
        font-weight: 800;
        margin-left: 6px;
    }

    /* Live list small style */
    .live-trip-item {
        padding: 10px;
        border-radius: 10px;
        margin-bottom: 8px;
        border: 1px dashed var(--border-color);
        transition: 0.2s;
    }
    /* Rating Stars */
    .star-item { cursor: pointer; color: var(--text-tertiary); transition: 0.2s; }
    .star-item:hover, .star-item.active { color: #f59e0b; fill: #f59e0b; }
    .star-rating { display: flex; gap: 4px; }
    
    .modal-star {
        cursor: pointer;
        transition: all 0.2s;
        color: var(--text-tertiary);
    }
    .modal-star:hover, .modal-star.active {
        color: #f59e0b;
        transform: scale(1.1);
    }

</style>

<div class="dashboard-wrapper">
    <!-- Ensure calls variable exists for direct rendering -->
    <?php if(!isset($calls)) { 
        $calls = [
            (object)['id' => 1, 'name' => 'John Doe', 'phone' => '+1 (555) 123-4567', 'status' => 'active', 'is_vip' => true],
            (object)['id' => 2, 'name' => 'Emily Davis', 'phone' => '+1 (555) 456-7890', 'status' => 'hold', 'is_vip' => false],
        ];
    } ?>

    <div class="dashboard-grid" style="grid-template-rows: 1fr;">
        
        <!-- COLUMN 1: CALLS & LIVE -->
        <div style="display:flex; flex-direction:column; gap:12px; height: 100%; min-height: 0; min-width: 0;">
            <!-- Calls Section -->
            <div class="col-panel" style="flex: 1; min-height: 0; display: flex; flex-direction: column;">
                <div class="panel-header">
                    <i data-lucide="phone-call" width="16"></i> Calls
                    <span class="badge bg-secondary-subtle ms-auto"><?= count($calls) ?></span>
                </div>
                <div class="panel-body" style="flex: 1; overflow-y: auto;">
                    <?php if(!empty($calls)): ?>
                        <?php foreach($calls as $call): ?>
                        <div class="call-card" onclick="selectCustomer(<?= $call->id ?>)" style="margin-bottom: 8px;">
                            <span class="status-badge status-<?= $call->status ?>"><?= $call->status ?></span>
                            <span style="font-size:10px; color:var(--text-tertiary); position:absolute; right:10px; top:35px;"><?= $call->time ?></span>
                            <a href="javascript:void(0)" class="close-btn"><i data-lucide="x" width="14"></i></a>
                            <div style="font-weight:700; font-size: 0.9rem;"><?= esc($call->name) ?> <?= $call->is_vip ? '<span class="vip-tag">VIP</span>' : '' ?></div>
                            <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:2px;"><?= esc($call->phone) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center; padding: 1rem; color:var(--text-tertiary); font-size:0.8rem;">
                            No active calls.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Live Section -->
            <div class="col-panel" style="flex: 1; min-height: 0; display: flex; flex-direction: column;">
                <div class="panel-header">
                    <i data-lucide="navigation" width="16"></i> Live
                    <span class="badge bg-secondary-subtle ms-auto"><?= isset($activeTrips) ? count($activeTrips) : 0 ?></span>
                </div>
                <div class="panel-body" style="flex: 1; overflow-y: auto;">
                    <?php if(isset($activeTrips) && !empty($activeTrips)): ?>
                        <?php foreach($activeTrips as $t): ?>
                        <div class="live-trip-item" onclick="selectTrip(this)" data-trip='<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>'>
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.75rem;">TRP-<?= esc($t->trip_number) ?></span>
                                <div class="dropdown" style="display:inline-block;">
                                    <span class="badge bg-success-subtle" style="font-size:9px;"><?= esc($t->status) ?></span>
                                    <a href="javascript:void(0)" data-bs-toggle="dropdown" onclick="event.stopPropagation()" style="color:var(--text-tertiary); margin-left:6px;"><i data-lucide="more-vertical" width="14"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="font-size: 0.8rem; border-radius: 10px;">
                                        <?php if(empty($t->system_rated_driver)): ?>
                                            <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="event.stopPropagation(); window.openRateModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>, 'driver')"><i data-lucide="star" width="14" class="me-2 text-warning"></i> Rate Driver</a></li>
                                        <?php endif; ?>
                                        <?php if(empty($t->system_rated_customer)): ?>
                                            <li><a class="dropdown-item py-2" href="javascript:void(0)" onclick="event.stopPropagation(); window.openRateModal(<?= htmlspecialchars(json_encode($t), ENT_QUOTES, 'UTF-8') ?>, 'customer')"><i data-lucide="star-half" width="14" class="me-2 text-primary"></i> Rate Customer</a></li>
                                        <?php endif; ?>
                                        <?php if(in_array($t->status, ['active', 'dispatching'])): ?>
                                            <li><a class="dropdown-item py-2 text-success" href="javascript:void(0)" onclick="event.stopPropagation(); window.updateTripStatus(<?= $t->id ?>, 'completed')"><i data-lucide="check-circle" width="14" class="me-2 text-success"></i> Mark Complete</a></li>
                                        <?php endif; ?>
                                        <li><a class="dropdown-item py-2 text-danger" href="javascript:void(0)" onclick="event.stopPropagation(); window.updateTripStatus(<?= $t->id ?>, 'cancelled')"><i data-lucide="slash" width="14" class="me-2"></i> Cancel Trip</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div style="font-weight:600; font-size:0.85rem;"><?= esc(($t->c_first ?? 'Guest') . ' ' . ($t->c_last ?? '')) ?></div>
                            <div style="display:flex; justify-content:space-between; font-size:0.75rem; color:var(--text-secondary); margin-top:4px;">
                                <span>ETA: <?= rand(2, 15) ?> min</span>
                                <span style="font-weight:700; color:var(--text-primary);">$<?= number_format($t->fare_amount, 2) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align:center; padding: 1rem; color:var(--text-tertiary); font-size:0.8rem;">
                            No live trips.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- COLUMN 2: PARTICIPANTS (CUSTOMER & DRIVER) -->
        <div style="display:flex; flex-direction:column; gap:12px; height: 100%; min-height: 0;">
            <!-- Customer Panel -->
            <div class="col-panel" style="flex: 1; min-height: 0; display: flex; flex-direction: column;">
                <div class="panel-header">
                    <i data-lucide="user" width="16"></i> Customer
                </div>
                <div class="panel-body" id="customer-section-parent">
                    <div style="text-align:center; padding: 2rem 1rem; color:var(--text-tertiary);" id="customer-empty">
                        <i data-lucide="user-plus" width="32" style="opacity:0.2; margin-bottom:1rem;"></i>
                        <p style="font-size:0.75rem;">Select trip/call</p>
                    </div>

                    <div id="customer-section" style="display:none;">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:1rem;">
                            <div id="prof-avatar" style="width:48px; height:48px; background:var(--primary); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:1rem;">JD</div>
                            <div>
                                <div style="font-weight:800; font-size:0.9rem; display:flex; align-items:center;">
                                    <span id="prof-name">John Doe</span> <span id="prof-vip" class="vip-tag">VIP</span>
                                </div>
                                <div id="prof-phone" style="font-size:0.8rem; color:var(--text-secondary);">+1 (555) 123-4567</div>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <div style="display:flex; gap:10px; align-items:center; font-size:0.75rem;">
                                <i data-lucide="mail" width="14" style="color:var(--text-tertiary);"></i>
                                <span id="prof-email">john@example.com</span>
                            </div>
                            <div style="display:flex; gap:10px; align-items:center; font-size:0.75rem;">
                                <i data-lucide="wallet" width="14" style="color:var(--success);"></i>
                                <span id="prof-wallet" style="font-weight:700; color:var(--success);">$150.00</span>
                            </div>
                            <div style="margin-top:8px; border-top: 1px dashed var(--border-color); padding-top:8px;">
                                <div style="font-size:9px; color:var(--text-tertiary); margin-bottom:4px;">RATE CUSTOMER</div>
                                <div class="star-rating" data-type="customer">
                                    <i data-lucide="star" width="14" data-value="1" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="2" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="3" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="4" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="5" class="star-item"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Panel -->
            <div class="col-panel" style="flex: 1; min-height: 0; display: flex; flex-direction: column;">
                <div class="panel-header">
                    <i data-lucide="car" width="16"></i> Driver
                </div>
                <div class="panel-body" id="driver-section-parent">
                    <div style="text-align:center; padding: 2rem 1rem; color:var(--text-tertiary);" id="driver-empty">
                        <i data-lucide="user-check" width="32" style="opacity:0.2; margin-bottom:1rem;"></i>
                        <p style="font-size:0.75rem;">Select trip</p>
                    </div>

                    <div id="driver-section" style="display:none;">
                        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: var(--text-tertiary); margin-bottom: 12px; letter-spacing: 0.05em;">Assigned Driver</div>
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:1rem;">
                            <div id="driver-avatar" style="width:48px; height:48px; background:var(--accent-green); border-radius:10px; display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:1rem;">AS</div>
                            <div>
                                <div style="font-weight:800; font-size:0.9rem; display:flex; align-items:center;">
                                    <span id="driver-name">Alex Smith</span>
                                </div>
                                <div id="driver-phone" style="font-size:0.8rem; color:var(--text-secondary);">+1 (555) 222-3333</div>
                            </div>
                        </div>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <div style="display:flex; gap:10px; align-items:center; font-size:0.75rem;">
                                <i data-lucide="car" width="14" style="color:var(--text-tertiary);"></i>
                                <span id="driver-vehicle">Blue Toyota Camry</span>
                            </div>
                            <div style="display:flex; gap:10px; align-items:center; font-size:0.75rem;">
                                <i data-lucide="star" width="14" style="color:var(--accent-orange);"></i>
                                <span id="driver-rating">4.8 (1.2k trips)</span>
                            </div>
                            <div style="margin-top:8px; border-top: 1px dashed var(--border-color); padding-top:8px;">
                                <div style="font-size:9px; color:var(--text-tertiary); margin-bottom:4px;">RATE DRIVER</div>
                                <div class="star-rating" data-type="driver">
                                    <i data-lucide="star" width="14" data-value="1" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="2" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="3" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="4" class="star-item"></i>
                                    <i data-lucide="star" width="14" data-value="5" class="star-item"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- COLUMN 3: TRIP DETAILS (FORM) -->
        <div class="col-panel">
            <div class="panel-header">
                <i data-lucide="file-text" width="16"></i> Trip Details
            </div>
            <div class="panel-body">
                <form id="trip-dispatch-form">
                    <input type="hidden" id="input-customer-id">
                    <div class="premium-input-group">
                        <label>Pickup</label>
                        <div class="icon-wrapper"><i data-lucide="circle-dot" style="color:var(--success)" width="20"></i></div>
                        <input type="text" id="input-pickup" placeholder="Pickup Address">
                    </div>

                    <div class="premium-input-group">
                        <label>Dropoff</label>
                        <div class="icon-wrapper"><i data-lucide="map-pin" style="color:var(--danger)" width="20"></i></div>
                        <input type="text" id="input-dropoff" placeholder="Dropoff Address">
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div class="premium-input-group">
                            <label>Date</label>
                            <div class="icon-wrapper"><i data-lucide="calendar" width="16"></i></div>
                            <input type="date" id="input-date" value="<?= date('Y-m-d') ?>" style="padding-left:36px;">
                        </div>
                        <div class="premium-input-group">
                            <label>Time</label>
                            <div class="icon-wrapper"><i data-lucide="clock" width="16"></i></div>
                            <input type="time" id="input-time" value="<?= date('H:i') ?>" style="padding-left:36px;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div class="premium-input-group">
                            <label>Vehicle</label>
                            <div class="icon-wrapper"><i data-lucide="car" width="16"></i></div>
                            <select id="input-vehicle" style="padding-left:36px;">
                                <option value="sedan">Sedan</option>
                                <option value="suv">SUV</option>
                                <option value="van">Van</option>
                                <option value="luxury">Luxury</option>
                            </select>
                            <div style="position:absolute; right:12px; bottom:11px; pointer-events:none; opacity:0.5;"><i data-lucide="chevron-down" width="14"></i></div>
                        </div>
                        <div class="premium-input-group">
                            <label>Passengers</label>
                            <div class="icon-wrapper"><i data-lucide="users" width="16"></i></div>
                            <select id="input-passengers" style="padding-left:36px;">
                                <option>1</option>
                                <option selected>2</option>
                                <option>3</option>
                                <option>4+</option>
                            </select>
                            <div style="position:absolute; right:12px; bottom:11px; pointer-events:none; opacity:0.5;"><i data-lucide="chevron-down" width="14"></i></div>
                        </div>
                    </div>

                    <div class="premium-input-group">
                        <label>Payment</label>
                        <div class="icon-wrapper"><i data-lucide="credit-card" width="16"></i></div>
                        <select id="input-payment" style="padding-left:36px;">
                            <option>Card</option>
                            <option>Cash</option>
                            <option>Wallet</option>
                        </select>
                        <div style="position:absolute; right:12px; bottom:11px; pointer-events:none; opacity:0.5;"><i data-lucide="chevron-down" width="14"></i></div>
                    </div>

                    <div class="premium-input-group">
                        <label>Notes</label>
                        <textarea id="input-notes" placeholder="Special instructions.." style="padding-left:12px; height:80px;"></textarea>
                    </div>

                    <div style="background:rgba(99, 102, 241, 0.05); border-radius:12px; padding:12px; display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border:1px solid var(--primary-glow);">
                        <div>
                            <div style="font-size:10px; color:var(--text-secondary);">ESTIMATED PRICE</div>
                            <div style="font-size:0.7rem; color:var(--text-tertiary);"><span id="lbl-dist">18.5 mi</span> | ~<span id="lbl-dur">35 min</span></div>
                        </div>
                        <div style="font-size:1.4rem; font-weight:800; color:var(--primary);" id="lbl-price">$51.25</div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" style="padding:14px; border-radius:12px; font-weight:700; gap:8px;" id="btn-dispatch">
                        <i data-lucide="send" width="18"></i> Send to Drivers
                    </button>
                </form>
            </div>
        </div>

        <!-- COLUMN 4: ROUTE MAP -->
        <div class="col-panel">
            <div class="panel-header">
                <i data-lucide="map" width="16"></i> Route Map
                <button class="btn btn-primary btn-sm ms-auto" style="border-radius:6px; font-size:10px; padding:4px 10px;" id="btn-traffic">
                    <i data-lucide="traffic-cone" width="12" style="margin-right:4px;"></i> Traffic
                </button>
            </div>
            <div style="flex:1; position:relative; background:#eee;">
                <div id="map" style="height:100%; width:100%;"></div>
            </div>
            <div class="map-stats-bar" id="map-stats-bar" style="display: none;">
                <div class="stat-card">
                    <div class="val">-- mi</div>
                    <div class="lbl">Distance</div>
                </div>
                <div class="stat-card">
                    <div class="val">-- min</div>
                    <div class="lbl">Duration</div>
                </div>
                <div class="stat-card" id="traffic-card" style="background:rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);">
                    <div class="val" style="color:#f59e0b;">Moderate</div>
                    <div class="lbl">Traffic</div>
                </div>
            </div>
        </div>

        <!-- COLUMN 5: DRIVERS -->
        <div class="col-panel">
            <div class="panel-header">
                <i data-lucide="car" width="16"></i> Drivers
            </div>
            <div class="panel-body">
                <div style="text-align:center; padding: 4rem 1rem; color:var(--text-tertiary);" id="drivers-empty">
                    <i data-lucide="user-check" width="48" style="opacity:0.2; margin-bottom:1rem;"></i>
                    <p style="font-size:0.85rem; font-weight:600; color:var(--text-secondary);">Enter trip details and dispatch</p>
                    <p style="font-size:0.75rem; margin-top:4px;">4 drivers online</p>
                </div>

                <!-- Driver List (Hidden initially as per image) -->
                <div id="driver-list" style="display:none;">
                    <?php foreach($drivers as $d): ?>
                        <div style="display:flex; align-items:center; gap:10px; padding:10px; border-radius:10px; border:1px solid var(--border-color); margin-bottom:8px;">
                            <div style="width:32px; height:32px; background:var(--bg-surface-hover); border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                <i data-lucide="user" width="16"></i>
                            </div>
                            <div>
                                <div style="font-weight:700; font-size:0.8rem;"><?= esc($d->first_name) ?></div>
                                <div style="font-size:0.7rem; color:var(--text-secondary);"><?= esc($d->vehicle_type) ?></div>
                            </div>
                            <span class="badge bg-success-subtle ms-auto" style="width:8px; height:8px; border-radius:50%; padding:0;"></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // Map Initialization
        let map, trafficLayer, directionsService, directionsRenderer;
        
        const initDashboardMap = () => {
            if (typeof google === 'undefined' || !google.maps) return;
            
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 40.730610, lng: -73.935242 }, // New York
                zoom: 12,
                disableDefaultUI: true,
                styles: [
                    { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
                    { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "road", "elementType": "geometry", "stylers": [{ "color": "#ffffff" }] },
                    { "featureType": "water", "elementType": "geometry", "stylers": [{ "color": "#e9e9e9" }] }
                ]
            });

            trafficLayer = new google.maps.TrafficLayer();
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: false,
                polylineOptions: {
                    strokeColor: "#6366f1",
                    strokeWeight: 5,
                    strokeOpacity: 0.8
                }
            });
        };

        const calculateRoute = () => {
            const pickup = document.getElementById('input-pickup').value;
            const dropoff = document.getElementById('input-dropoff').value;

            if (!pickup || !dropoff) return;

            if (!directionsService) {
                console.warn('Map Directions Service not ready yet.');
                return;
            }

            directionsService.route({
                origin: pickup,
                destination: dropoff,
                travelMode: 'DRIVING'
            }, (response, status) => {
                if (status === 'OK') {
                    if (directionsRenderer) directionsRenderer.setDirections(response);
                    
                    // Show stats bar
                    const statsBar = document.getElementById('map-stats-bar');
                    if (statsBar) statsBar.style.display = 'flex';

                    const leg = response.routes[0].legs[0];
                    const distElements = document.querySelectorAll('.stat-card .val');
                    if (distElements.length >= 2) {
                        distElements[0].innerText = leg.distance.text;
                        distElements[1].innerText = leg.duration.text;
                    }
                    
                    if (document.getElementById('lbl-dist')) document.getElementById('lbl-dist').innerText = leg.distance.text;
                    if (document.getElementById('lbl-dur')) document.getElementById('lbl-dur').innerText = leg.duration.text;
                } else {
                    console.error('Directions request failed due to ' + status);
                    const statsBar = document.getElementById('map-stats-bar');
                    if (statsBar) statsBar.style.display = 'none';
                }
            });
        };

        // Add listeners to address inputs
        const setupAutocomplete = () => {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) {
                console.warn('Autocomplete will be initialized after Google Maps library loads.');
                return;
            }

            const pickupInput = document.getElementById('input-pickup');
            const dropoffInput = document.getElementById('input-dropoff');

            if (pickupInput) {
                const acPickup = new google.maps.places.Autocomplete(pickupInput);
                acPickup.addListener('place_changed', () => {
                    calculateRoute();
                });
            }

            if (dropoffInput) {
                const acDropoff = new google.maps.places.Autocomplete(dropoffInput);
                acDropoff.addListener('place_changed', () => {
                    calculateRoute();
                });
            }
        };

        const btnTraffic = document.getElementById('btn-traffic');
        if(btnTraffic) {
            let trafficOn = false;
            btnTraffic.addEventListener('click', () => {
                trafficOn = !trafficOn;
                if(trafficOn) {
                    if (trafficLayer) trafficLayer.setMap(map);
                    btnTraffic.classList.add('btn-success');
                    btnTraffic.classList.remove('btn-primary');
                } else {
                    if (trafficLayer) trafficLayer.setMap(null);
                    btnTraffic.classList.remove('btn-success');
                    btnTraffic.classList.add('btn-primary');
                }
            });
        }

        const bootstrapMaps = () => {
            if (typeof google !== 'undefined' && google.maps) {
                initDashboardMap();
                setupAutocomplete();
            } else {
                window.addEventListener('google-maps-ready', () => {
                    initDashboardMap();
                    setupAutocomplete();
                });
                
                // Fallback: If event was already fired before listener attached
                setTimeout(() => {
                    if (typeof google !== 'undefined' && google.maps && !map) {
                        initDashboardMap();
                        setupAutocomplete();
                    }
                }, 1000);
            }
        };

        if (window.APP_GOOGLE_MAPS_KEY) {
            bootstrapMaps();
        }

        // Handle selection of a call/customer
        window.selectCustomer = (id) => {
            document.getElementById('customer-empty').style.display = 'none';
            document.getElementById('customer-section').style.display = 'block';
            
            // Set hidden customer ID for form
            document.getElementById('input-customer-id').value = id;

            // Driver section reset for a new call
            document.getElementById('driver-empty').style.display = 'block';
            document.getElementById('driver-section').style.display = 'none';
            
            // Mock data access matching controller
            const call = <?= json_encode($calls) ?>.find(c => c.id == id);
            if(call) {
                document.getElementById('prof-name').innerText = call.name;
                document.getElementById('prof-phone').innerText = call.phone;
                document.getElementById('prof-avatar').innerText = call.name.split(' ').map(n=>n[0]).join('');
                document.getElementById('prof-vip').style.display = call.is_vip ? 'inline-block' : 'none';

                // Automatically set phone based on selection
                document.getElementById('input-notes').value = "Called from: " + call.phone;
            }
        };

        let currentSelectedTrip = null;

        // Handle selection of a live trip
        window.selectTrip = (el) => {
            const t = JSON.parse(el.dataset.trip);
            currentSelectedTrip = t;
            
            // Populate form
            document.getElementById('input-customer-id').value = t.customer_id;
            document.getElementById('input-pickup').value = t.pickup_address;
            document.getElementById('input-dropoff').value = t.dropoff_address;
            document.getElementById('input-vehicle').value = t.vehicle_type || 'sedan';
            document.getElementById('lbl-price').innerText = '$' + parseFloat(t.fare_amount).toFixed(2);
            
            // Show sections
            document.getElementById('customer-empty').style.display = 'none';
            document.getElementById('customer-section').style.display = 'block';
            document.getElementById('driver-empty').style.display = 'none';
            document.getElementById('driver-section').style.display = 'block';

            // Reset Stars
            document.querySelectorAll('.star-item').forEach(s => s.classList.remove('active'));

            // Populate customer profile
            document.getElementById('prof-name').innerText = (t.c_first || 'Guest') + ' ' + (t.c_last || '');
            document.getElementById('prof-phone').innerText = t.c_phone || '--';
            document.getElementById('prof-email').innerText = t.c_email || '--';
            document.getElementById('prof-wallet').innerText = '$' + parseFloat(t.c_wallet_balance || 0).toFixed(2);
            document.getElementById('prof-avatar').innerText = (t.c_first ? t.c_first[0] : 'G') + (t.c_last ? t.c_last[0] : '');

            // Populate driver profile
            if (t.driver_id) {
                const dName = (t.d_first || 'Driver') + ' ' + (t.d_last || '');
                document.getElementById('driver-name').innerText = dName;
                document.getElementById('driver-phone').innerText = t.d_phone || '--';
                document.getElementById('driver-vehicle').innerText = t.d_vehicle || '--';
                document.getElementById('driver-rating').innerText = (parseFloat(t.d_rating) || 0).toFixed(1) + ' ★';
                document.getElementById('driver-avatar').style.background = 'var(--accent-green)';
                document.getElementById('driver-avatar').innerText = (t.d_first ? t.d_first[0] : 'D') + (t.d_last ? t.d_last[0] : '');
            } else {
                document.getElementById('driver-name').innerText = 'Pending Dispatch';
                document.getElementById('driver-phone').innerText = '--';
                document.getElementById('driver-vehicle').innerText = 'Unassigned';
                document.getElementById('driver-rating').innerText = '--';
                document.getElementById('driver-avatar').style.background = 'var(--text-tertiary)';
                document.getElementById('driver-avatar').innerText = '?';
            }
            
            // Update map route
            calculateRoute();

            // Show drivers list
            document.getElementById('drivers-empty').style.display = 'none';
            document.getElementById('driver-list').style.display = 'block';
        };

        // Rating Star Logic for Dashboard Cards
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('star-item') && !e.target.classList.contains('modal-star')) {
                if (!currentSelectedTrip) return;
                
                const container = e.target.closest('.star-rating');
                const rateeType = container.dataset.type; // customer or driver
                const ratingValue = e.target.dataset.value;

                // Highlight stars
                const siblings = container.querySelectorAll('.star-item');
                siblings.forEach(s => {
                    if (parseInt(s.dataset.value) <= parseInt(ratingValue)) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });

                submitRating(currentSelectedTrip.id, rateeType, (rateeType === 'customer' ? currentSelectedTrip.customer_id : currentSelectedTrip.driver_id), ratingValue, '');
            }
            
            // Handling Modal Stars specifically
            if (e.target.classList.contains('modal-star')) {
                const val = e.target.dataset.value;
                document.getElementById('modal-star-group').querySelectorAll('.star-item').forEach(s => {
                    if (parseInt(s.dataset.value) <= parseInt(val)) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
                // Store in global or hidden if needed, we'll just read active ones on submit
            }
        });

        // Modal Specific Logic
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
            
            // Reset Modal
            document.getElementById('rate-comment').value = '';
            document.getElementById('modal-star-group').querySelectorAll('.star-item').forEach(s => s.classList.remove('active'));
            
            lucide.createIcons();
            const modalEl = document.getElementById('ratingModal');
            let modal = bootstrap.Modal.getInstance(modalEl);
            if(!modal) modal = new bootstrap.Modal(modalEl);
            modal.show();
        };

        const btnSubmitRate = document.getElementById('btn-submit-rating');
        if (btnSubmitRate) {
            btnSubmitRate.addEventListener('click', () => {
                const tripId = document.getElementById('rate-trip-id').value;
                const type = document.getElementById('ratee-type').value;
                const id = document.getElementById('ratee-id').value;
                const comment = document.getElementById('rate-comment').value;
                const activeStars = document.getElementById('modal-star-group').querySelectorAll('.star-item.active');
                const val = activeStars.length > 0 ? activeStars[activeStars.length-1].dataset.value : 0;

                if (val == 0) { alert("Please select a star rating."); return; }

                btnSubmitRate.disabled = true;
                btnSubmitRate.innerText = "Submitting...";

                submitRating(tripId, type, id, val, comment).then(() => {
                    bootstrap.Modal.getInstance(document.getElementById('ratingModal')).hide();
                    alert("Rating submitted successfully!");
                    btnSubmitRate.disabled = false;
                    btnSubmitRate.innerText = "Submit Rating";
                });
            });
        }

        function submitRating(tripId, rateeType, rateeId, val, comment) {
            const payload = {
                trip_id: tripId,
                rating: val,
                ratee_type: rateeType,
                ratee_id: rateeId,
                rater_type: (rateeType === 'customer') ? 'driver' : 'customer',
                rater_id: 0,
                comment: comment
            };

            return fetch('<?= base_url('dispatch/ratings/submit') ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            }).then(res => res.json());
        }

        // Handle Dispatch Submission
        const btnDispatch = document.getElementById('btn-dispatch');
        if (btnDispatch) {
            btnDispatch.addEventListener('click', () => {
                const customerId = document.getElementById('input-customer-id').value;
                const pickup = document.getElementById('input-pickup').value;
                const dropoff = document.getElementById('input-dropoff').value;
                const vehicle = document.getElementById('input-vehicle').value;
                const pDate = document.getElementById('input-date').value;
                const pTime = document.getElementById('input-time').value;

                if (!customerId) { alert("Please select a customer first."); return; }
                if (!pickup || !dropoff) { alert("Please enter both pickup and dropoff addresses."); return; }

                btnDispatch.disabled = true;
                btnDispatch.innerText = "Processing...";

                fetch('<?= base_url('dispatch/trips/create') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({
                        customer_id: customerId,
                        pickup_address: pickup,
                        dropoff_address: dropoff,
                        vehicle_type: vehicle,
                        scheduled_at: pDate + ' ' + pTime + ':00',
                        notes: document.getElementById('input-notes').value,
                        fare_amount: parseFloat(document.getElementById('lbl-price').innerText.replace('$', '')),
                        distance_miles: parseFloat(document.getElementById('lbl-dist').innerText)
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert("Trip #" + data.trip_number + " dispatched successfully!");
                        location.reload(); // Simple refresh to see updated lists
                    } else {
                        alert("Error: " + (data.errors ? Object.values(data.errors).join(', ') : data.message));
                        btnDispatch.disabled = false;
                        btnDispatch.innerText = "Send to Drivers";
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("A communication error occurred. Check browser console.");
                    btnDispatch.disabled = false;
                    btnDispatch.innerText = "Send to Drivers";
                });
            });
        }
    });

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

    <!-- RATING MODAL -->
    <div class="modal fade" id="ratingModal" tabindex="-1" style="z-index: 2000;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h5 class="modal-title fw-800" id="rate-modal-title" style="font-size: 1.25rem;">Rate Participant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="rate-trip-id">
                    <input type="hidden" id="ratee-type">
                    <input type="hidden" id="ratee-id">

                    <div class="text-center mb-4">
                        <div id="rate-modal-subtitle" style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 1.25rem;">Share feedback about <strong>TRP-<span id="lbl-rate-trip-num"></span></strong></div>
                        <div class="star-rating justify-content-center" id="modal-star-group" style="font-size: 2.25rem; gap: 12px; display: flex;">
                            <i data-lucide="star" width="32" data-value="1" class="star-item modal-star"></i>
                            <i data-lucide="star" width="32" data-value="2" class="star-item modal-star"></i>
                            <i data-lucide="star" width="32" data-value="3" class="star-item modal-star"></i>
                            <i data-lucide="star" width="32" data-value="4" class="star-item modal-star"></i>
                            <i data-lucide="star" width="32" data-value="5" class="star-item modal-star"></i>
                        </div>
                    </div>

                    <div class="premium-input-group mb-4">
                        <label>Review & Comment</label>
                        <textarea id="rate-comment" class="form-control border-0 bg-light-subtle" placeholder="What was your experience with this participant?" style="height: 120px; padding: 15px; border-radius: 12px; resize: none;"></textarea>
                    </div>

                    <button type="button" class="btn btn-primary w-100 py-3 fw-800" id="btn-submit-rating" style="border-radius: 12px; font-size: 1rem; letter-spacing: 0.02em;">Submit Rating</button>
                </div>
            </div>
        </div>
    </div>

<?= $this->endSection() ?>
