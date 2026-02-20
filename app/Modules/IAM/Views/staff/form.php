<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div style="padding: 2rem; max-width: 800px; margin: 0 auto;">
    
    <div style="margin-bottom: 2rem;">
        <a href="<?= base_url('staff') ?>" style="color:var(--text-secondary); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem; margin-bottom:1rem;">
            <i data-lucide="arrow-left" width="16"></i> Back to Staff
        </a>
        <h1 class="h3"><?= esc($title) ?></h1>
    </div>

    <!-- Form -->
    <?php $isEdit = isset($staff->id); ?>
    <form action="<?= $isEdit ? base_url('staff/update/'.$staff->id) : base_url('staff/create') ?>" method="post">
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem;">
            
            <!-- Left: Personal Details -->
            <div class="card" style="padding:2rem;">
                <h4 style="margin-bottom:1.5rem; color:var(--text-primary); font-size:1rem; font-weight:700;">Account Details</h4>
                
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; margin-bottom:1.5rem;">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $staff->first_name ?? '') ?>" required placeholder="e.g. John">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $staff->last_name ?? '') ?>" required placeholder="e.g. Doe">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:1.5rem;">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?= old('email', $staff->email ?? '') ?>" required placeholder="john@rideflow.app">
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">Password</label>
                    <div style="position:relative;">
                        <input type="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?> minlength="6" placeholder="******">
                        <i data-lucide="lock" width="16" style="position:absolute; right:12px; top:12px; color:var(--text-secondary);"></i>
                    </div>
                    <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:6px;">
                        <?= $isEdit ? 'Leave blank to keep current password.' : 'Must be at least 6 characters long.' ?>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:1rem;">
                    <label class="form-label">Account Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= (old('status', $staff->status ?? 'active') == 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="banned" <?= (old('status', $staff->status ?? '') == 'banned') ? 'selected' : '' ?>>Banned</option>
                    </select>
                </div>
            </div>

            <!-- Right: Role & Actions -->
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                
                <!-- Role Selection -->
                <div class="card" style="padding:1.5rem;">
                    <h4 style="margin-bottom:1rem; color:var(--text-primary); font-size:0.95rem; font-weight:700;">Assign Role</h4>
                    <div style="display:flex; flex-direction:column; gap:0.75rem;">
                        <?php foreach($roles as $r): ?>
                        <label class="role-option" style="display:flex; align-items:flex-start; gap:10px; padding:0.75rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); cursor:pointer; transition:all 0.1s;">
                            <input type="radio" name="role_id" value="<?= $r->id ?>" <?= (old('role_id', $staff->role_id ?? '') == $r->id) ? 'checked' : '' ?> required style="margin-top:4px;">
                            <div>
                                <div style="font-weight:600; font-size:0.9rem;"><?= esc($r->name) ?></div>
                                <div style="font-size:0.75rem; color:var(--text-secondary); line-height:1.3;"><?= esc($r->description) ?></div>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card" style="padding:1.5rem; text-align:center;">
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-bottom:0.75rem;"><?= $isEdit ? 'Update Staff Member' : 'Create Staff Member' ?></button>
                    <a href="<?= base_url('staff') ?>" style="color:var(--text-secondary); font-size:0.9rem; text-decoration:none;">Cancel</a>
                </div>

            </div>
        </div>
    </form>

    <style>
        .role-option:hover { border-color: var(--primary) !important; background: var(--bg-surface-hover); }
        .role-option input:checked + div { color: var(--primary); }
    </style>

<?= $this->endSection() ?>
