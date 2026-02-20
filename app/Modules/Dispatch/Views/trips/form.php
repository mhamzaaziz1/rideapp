<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .edit-container { max-width: 1000px; margin: 2rem auto; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    
    .form-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        display: flex; align-items: center; gap: 0.75rem;
    }
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.5rem; }
    .form-control, .form-select, .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: border-color 0.15s;
    }
    .form-control:focus, .form-select:focus, .form-textarea:focus { border-color: var(--primary); outline: none; }
    .form-textarea { resize: vertical; min-height: 100px; }

    .btn-submit {
        background: var(--primary); color: white; border: none; padding: 0.75rem 2rem;
        border-radius: var(--radius-sm); font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-submit:hover { opacity: 0.9; }
</style>

<div class="edit-container">
    <div class="page-header" style="background:var(--bg-surface); padding:1.5rem; border:1px solid var(--border-color); border-radius:var(--radius-md); box-shadow:0 1px 2px rgba(0,0,0,0.05);">
        <div>
            <div style="display:flex; align-items:center; gap:10px;">
                <a href="<?= base_url('dispatch/trips') ?>" class="btn-icon-sm" style="width:32px; height:32px; border-radius:50%; margin-right:4px;"><i data-lucide="arrow-left" width="18"></i></a>
                <div>
                     <h1 style="font-size:1.5rem; font-weight:700; color:var(--text-primary); margin:0; line-height:1.2;"><?= esc($title) ?></h1>
                     <div style="color:var(--text-secondary); font-size:0.85rem; margin-top:2px;">Fill in the details below</div>
                </div>
            </div>
        </div>
        <?php if(!empty($trip->trip_number)): ?>
            <div style="font-family:monospace; font-weight:600; color:var(--primary); background:rgba(var(--primary-rgb), 0.1); padding:0.5rem 1rem; border-radius:var(--radius-sm); border:1px solid rgba(var(--primary-rgb), 0.2);">
                <?= $trip->trip_number ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Error Display -->
    <?php if (session()->has('errors')): ?>
        <div style="background:rgba(239, 68, 68, 0.1); border:1px solid var(--danger); color:var(--danger); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; display:flex; align-items:flex-start; gap:1rem;">
            <i data-lucide="alert-circle" style="flex-shrink:0; margin-top:2px;"></i>
            <ul style="margin:0; padding-left:1rem;">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form action="<?= isset($trip->id) && $trip->id ? base_url('dispatch/trips/update/' . $trip->id) : base_url('dispatch/trips/create') ?>" method="post">
        
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem;">
            
            <!-- Left Column: Route & People -->
            <div>
                <!-- Assignment -->
                <div class="form-card">
                    <h3 class="form-section-title"><i data-lucide="users"></i> Assignment</h3>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select" required>
                                <option value="">Select Customer</option>
                                <?php foreach($customers as $c): ?>
                                    <option value="<?= $c->id ?>" <?= ($trip->customer_id == $c->id) ? 'selected' : '' ?>>
                                        <?= esc($c->first_name . ' ' . $c->last_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Driver</label>
                            <select name="driver_id" class="form-select">
                                <option value="">Unassigned</option>
                                <?php foreach($drivers as $d): ?>
                                    <option value="<?= $d->id ?>" <?= ($trip->driver_id == $d->id) ? 'selected' : '' ?>>
                                        <?= esc($d->first_name . ' ' . $d->last_name) ?> (<?= $d->vehicle_model ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Route -->
                <div class="form-card">
                    <h3 class="form-section-title"><i data-lucide="map-pin"></i> Route Details</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Pickup Address</label>
                        <input type="text" name="pickup_address" class="form-control addr-autocomplete" value="<?= old('pickup_address', $trip->pickup_address) ?>" placeholder="Enter pickup location" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dropoff Address</label>
                        <input type="text" name="dropoff_address" class="form-control addr-autocomplete" value="<?= old('dropoff_address', $trip->dropoff_address) ?>" placeholder="Enter destination" required>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Distance (Miles)</label>
                            <input type="number" step="0.01" name="distance_miles" class="form-control" value="<?= old('distance_miles', $trip->distance_miles) ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Est. Duration (Min)</label>
                            <input type="number" name="duration_minutes" class="form-control" value="<?= old('duration_minutes', $trip->duration_minutes) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings & Fare -->
             <div>
                
                <div class="form-card">
                    <h3 class="form-section-title" style="font-size:1rem;"><i data-lucide="settings"></i> Trip Settings</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" <?= ($trip->status == 'pending') ? 'selected' : '' ?>>Pending</option>
                            <option value="dispatching" <?= ($trip->status == 'dispatching') ? 'selected' : '' ?>>Dispatching</option>
                            <option value="active" <?= ($trip->status == 'active') ? 'selected' : '' ?>>Active (In Progress)</option>
                            <option value="completed" <?= ($trip->status == 'completed') ? 'selected' : '' ?>>Completed</option>
                            <option value="cancelled" <?= ($trip->status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>

                     <div class="form-group">
                        <label class="form-label">Scheduled For</label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" value="<?= old('scheduled_at', $trip->scheduled_at ? date('Y-m-d\TH:i', strtotime($trip->scheduled_at)) : '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Vehicle Type</label>
                        <select name="vehicle_type" class="form-select">
                             <option value="Sedan" <?= ($trip->vehicle_type == 'Sedan') ? 'selected' : '' ?>>Sedan</option>
                             <option value="SUV" <?= ($trip->vehicle_type == 'SUV') ? 'selected' : '' ?>>SUV</option>
                             <option value="Van" <?= ($trip->vehicle_type == 'Van') ? 'selected' : '' ?>>Van</option>
                             <option value="Luxury" <?= ($trip->vehicle_type == 'Luxury') ? 'selected' : '' ?>>Luxury</option>
                        </select>
                    </div>
                 </div>

                 <div class="form-card">
                    <h3 class="form-section-title" style="font-size:1rem;"><i data-lucide="dollar-sign"></i> Payment</h3>
                    <div class="form-group">
                        <label class="form-label">Fare Amount</label>
                        <div style="position:relative;">
                            <span style="position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:var(--text-secondary);">$</span>
                            <input type="number" step="0.01" name="fare_amount" class="form-control" style="padding-left:2rem;" value="<?= old('fare_amount', $trip->fare_amount) ?>">
                        </div>
                    </div>
                 </div>

             </div>

        </div>

        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border-color); display:flex; justify-content:flex-end;">
            <a href="<?= base_url('dispatch/trips') ?>" class="btn" style="margin-right:1rem; color:var(--text-secondary);">Cancel</a>
            <button type="submit" class="btn-submit"><i data-lucide="save" width="18"></i> Save Trip</button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
