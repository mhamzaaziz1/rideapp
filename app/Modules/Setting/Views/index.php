<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .settings-container {
        display: flex;
        gap: 2rem;
        padding: 2rem;
    }
    
    .settings-sidebar {
        width: 240px;
        flex-shrink: 0;
    }
    
    .nav-item {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: var(--text-secondary);
        border-radius: var(--radius-md);
        margin-bottom: 0.25rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .nav-item:hover { background: var(--bg-surface-hover); color: var(--text-primary); }
    .nav-item.active { background: var(--primary); color: white; }
    
    .settings-content {
        flex: 1;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2rem;
        max-width: 800px;
    }
    
    .section-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
    
    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
    .form-input { width: 100%; padding: 0.6rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-body); color: var(--text-primary); }
    .form-help { font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.25rem; }
</style>

<div class="settings-container">
    
    <!-- Sidebar -->
    <div class="settings-sidebar">
        <h2 style="font-size:1.2rem; font-weight:700; margin-bottom:1.5rem; padding-left:1rem;">Settings</h2>
        <a href="<?= base_url('settings?tab=general') ?>" class="nav-item <?= $tab == 'general' ? 'active' : '' ?>">
            <i data-lucide="settings" width="18" style="margin-right:10px;"></i> General
        </a>
        <a href="<?= base_url('settings?tab=account') ?>" class="nav-item <?= $tab == 'account' ? 'active' : '' ?>">
            <i data-lucide="user" width="18" style="margin-right:10px;"></i> Account
        </a>
        <a href="<?= base_url('settings?tab=permissions') ?>" class="nav-item <?= $tab == 'permissions' ? 'active' : '' ?>">
            <i data-lucide="lock" width="18" style="margin-right:10px;"></i> Permissions
        </a>
        <a href="<?= base_url('settings?tab=security') ?>" class="nav-item <?= $tab == 'security' ? 'active' : '' ?>">
            <i data-lucide="shield" width="18" style="margin-right:10px;"></i> Security
        </a>
        <a href="<?= base_url('settings?tab=notifications') ?>" class="nav-item <?= $tab == 'notifications' ? 'active' : '' ?>">
            <i data-lucide="bell" width="18" style="margin-right:10px;"></i> Notifications
        </a>
        <a href="<?= base_url('settings?tab=templates') ?>" class="nav-item <?= $tab == 'templates' ? 'active' : '' ?>">
            <i data-lucide="message-square" width="18" style="margin-right:10px;"></i> SMS Templates
        </a>
        <a href="<?= base_url('settings?tab=payments') ?>" class="nav-item <?= $tab == 'payments' ? 'active' : '' ?>">
            <i data-lucide="credit-card" width="18" style="margin-right:10px;"></i> Payments
        </a>
    </div>

    <!-- Content -->
    <div class="settings-content">
        <?php if(session()->has('success')): ?>
            <div style="background:rgba(16, 185, 129, 0.1); color:var(--success); padding:1rem; border-radius:6px; margin-bottom:1.5rem;">
                <?= session('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('settings/update') ?>" method="post" enctype="multipart/form-data">
            <input type="hidden" name="tab" value="<?= $tab ?>">
            
            <?php if($tab == 'general'): ?>
                <div class="section-title">General Settings</div>
                
                <div class="form-group">
                    <label class="form-label">Company Logo</label>
                    <?php if(isset($settings['company_logo']) && $settings['company_logo']): ?>
                        <div style="margin-bottom: 10px;">
                            <img src="<?= base_url('uploads/settings/' . $settings['company_logo']) ?>" alt="Company Logo" style="max-height: 80px;">
                        </div>
                    <?php endif; ?>
                    <input type="file" class="form-input" name="company_logo" accept="image/*">
                    <div class="form-help">Upload a company logo to be displayed on documents.</div>
                </div>

                <div class="form-group">
                    <label class="form-label">Company Name</label>
                    <input type="text" class="form-input" name="company_name" value="<?= $settings['company_name'] ?? '' ?>" placeholder="e.g. OMNI-HUB DISTRIBUTION">
                </div>

                <div class="section-title" style="margin-top: 2rem;">Map Settings</div>
                
                <div class="form-group">
                    <label class="form-label">Map Provider</label>
                    <select class="form-input" name="map_provider">
                        <option value="osm" <?= (isset($settings['map_provider']) && $settings['map_provider'] == 'osm') ? 'selected' : '' ?>>OpenStreetMap (Leaflet)</option>
                        <option value="google" <?= (isset($settings['map_provider']) && $settings['map_provider'] == 'google') ? 'selected' : '' ?>>Google Maps</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Google Maps API Key</label>
                    <input type="text" class="form-input" name="google_maps_api_key" value="<?= $settings['google_maps_api_key'] ?? '' ?>" placeholder="AIzaSy...">
                    <div class="form-help">Required if using Google Maps. Optional for OpenStreetMap (only used for address autocomplete).</div>
                </div>

                <div class="section-title" style="margin-top: 2rem;">Address Information</div>

                <div class="form-group">
                    <label class="form-label">Address</label>
                    <input type="text" class="form-input" name="company_address" value="<?= $settings['company_address'] ?? '' ?>" placeholder="e.g. PLOT NO 11892 CHANDWE MUSONDA">
                </div>

                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" class="form-input" name="company_city" value="<?= $settings['company_city'] ?? '' ?>" placeholder="e.g. LUSAKA">
                </div>

                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" class="form-input" name="company_state" value="<?= $settings['company_state'] ?? '' ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Country Code</label>
                    <input type="text" class="form-input" name="company_country_code" value="<?= $settings['company_country_code'] ?? '' ?>" placeholder="e.g. ZAMBIA">
                </div>

                <div class="form-group">
                    <label class="form-label">Zip Code</label>
                    <input type="text" class="form-input" name="company_zip_code" value="<?= $settings['company_zip_code'] ?? '' ?>" placeholder="e.g. 00001">
                </div>

                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-input" name="company_phone" value="<?= $settings['company_phone'] ?? '' ?>" placeholder="e.g. +263773049113">
                </div>

                <div class="form-group">
                    <label class="form-label">VAT Number</label>
                    <input type="text" class="form-input" name="company_vat" value="<?= $settings['company_vat'] ?? '' ?>">
                </div>

            <?php elseif($tab == 'account'): ?>
                <div class="section-title">Staff Management</div>
                <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;">
                    <p style="color:var(--text-secondary); margin:0;">Manage employees and access roles.</p>
                    <a href="<?= base_url('staff/new') ?>" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px; padding:0.4rem 0.8rem; font-size:0.85rem;"><i data-lucide="plus" width="14"></i> Add Staff</a>
                </div>
                
                <div style="border:1px solid var(--border-color); border-radius:var(--radius-md); overflow:hidden;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead style="background:var(--bg-surface-hover); border-bottom:1px solid var(--border-color);">
                            <tr>
                                <th style="text-align:left; padding:0.75rem 1rem; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); font-weight:600;">User</th>
                                <th style="text-align:left; padding:0.75rem 1rem; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); font-weight:600;">Role</th>
                                <th style="text-align:left; padding:0.75rem 1rem; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); font-weight:600;">Status</th>
                                <th style="text-align:right; padding:0.75rem 1rem; font-size:0.75rem; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-secondary); font-weight:600;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($staff)): ?>
                            <?php foreach($staff as $u): ?>
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:0.75rem 1rem;">
                                    <div style="display:flex; align-items:center; gap:10px;">
                                        <div style="width:32px; height:32px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.8rem;">
                                            <?= substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight:600; font-size:0.9rem;"><?= esc($u->first_name . ' ' . $u->last_name) ?></div>
                                            <div style="font-size:0.75rem; color:var(--text-secondary);"><?= esc($u->email) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <?php if($u->role_name): ?>
                                    <span style="background:rgba(99, 102, 241, 0.1); color:var(--primary); padding:2px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;">
                                        <?= esc($u->role_name) ?>
                                    </span>
                                    <?php else: ?>
                                    <span style="color:var(--text-secondary); font-size:0.75rem;">No Role</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding:0.75rem 1rem;">
                                    <span class="status-badge status-<?= $u->status ?>" style="font-size:0.75rem; padding:2px 8px;"><?= ucfirst($u->status) ?></span>
                                </td>
                                <td style="text-align:right; padding:0.75rem 1rem;">
                                    <a href="<?= base_url('staff/edit/'.$u->id) ?>" class="btn-icon-sm" title="Edit" style="color:var(--text-secondary);"><i data-lucide="edit-2" width="16"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="4" style="padding:2rem; text-align:center; color:var(--text-secondary);">No staff members found.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif($tab == 'permissions'): ?>
                <div class="section-title">Role Permissions</div>
                <p style="color:var(--text-secondary); margin-bottom:1.5rem;">Manage what each role can access and perform.</p>

                <div style="border:1px solid var(--border-color); border-radius:var(--radius-md); overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:600px;">
                        <thead style="background:var(--bg-surface-hover); border-bottom:1px solid var(--border-color);">
                            <tr>
                                <th style="text-align:left; padding:0.75rem 1rem; color:var(--text-secondary); font-size:0.8rem; text-transform:uppercase;">Permission / Role</th>
                                <?php foreach($roles as $role): ?>
                                    <th style="padding:0.75rem 1rem; text-align:center; color:var(--text-primary); font-size:0.9rem; min-width:100px;">
                                        <?= esc($role->name) ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($permissions as $perm): ?>
                            <tr style="border-bottom:1px solid var(--border-color);">
                                <td style="padding:0.75rem 1rem; font-weight:500;">
                                    <?= esc(ucwords(str_replace('_', ' ', $perm->name))) ?>
                                    <div style="font-size:0.75rem; color:var(--text-secondary); font-weight:normal;">
                                        <?= esc($perm->description ?? '') ?>
                                    </div>
                                </td>
                                <?php foreach($roles as $role): ?>
                                <td style="padding:0.75rem 1rem; text-align:center;">
                                    <?php 
                                        $isChecked = false;
                                        if (isset($rolePermissions[$role->id]) && in_array($perm->id, $rolePermissions[$role->id])) {
                                            $isChecked = true;
                                        }
                                        
                                        // Admin usually has all permissions, force checked & disabled if needed, 
                                        // or just let it be managed. Let's assume managed for now.
                                        $isDisabled = ($role->name === 'Administrator' && false); // Example: Disable for Admin
                                    ?>
                                    <label class="custom-checkbox">
                                        <input type="checkbox" name="perms[<?= $role->id ?>][<?= $perm->id ?>]" value="1" <?= $isChecked ? 'checked' : '' ?> <?= $isDisabled ? 'disabled' : '' ?>>
                                    </label>
                                </td>
                                <?php endforeach; ?>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($permissions)): ?>
                            <tr><td colspan="<?= count($roles) + 1 ?>" style="padding:2rem; text-align:center; color:var(--text-secondary);">No permissions defined in system.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div style="margin-top:2rem; text-align:right;">
                    <button type="submit" class="btn btn-primary">Save Permissions</button>
                </div>

            <?php elseif($tab == 'security'): ?>
                <div class="section-title">Security & Login</div>
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-input">
                </div>
                <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border-color)">
                    <button type="button" class="btn" style="color:var(--danger); border:1px solid var(--danger);">Log out all devices</button>
                </div>
            <?php elseif($tab == 'notifications'): ?>
                <div class="section-title">Push Notifications (Pusher)</div>
                <div class="form-group">
                    <label class="form-label">App ID</label>
                    <input type="text" class="form-input" name="pusher_app_id" placeholder="e.g. 193...">
                </div>
                <div class="form-group">
                    <label class="form-label">App Key</label>
                    <input type="text" class="form-input" name="pusher_key" placeholder="e.g. a1b2c3...">
                </div>
                <div class="form-group">
                    <label class="form-label">App Secret</label>
                    <input type="password" class="form-input" name="pusher_secret">
                </div>
                <div class="form-group">
                    <label class="form-label">Cluster</label>
                    <input type="text" class="form-input" name="pusher_cluster" placeholder="e.g. mt1">
                </div>

                <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border-color)">
                    <div class="section-title">SMS & Voice (Twilio)</div>
                    <div class="form-group">
                        <label class="form-label">Account SID</label>
                        <input type="text" class="form-input" name="twilio_sid" placeholder="e.g. AC...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Auth Token</label>
                        <input type="password" class="form-input" name="twilio_token">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Twilio Phone Number</label>
                        <input type="text" class="form-input" name="twilio_number" placeholder="e.g. +1555...">
                    </div>
                </div>
            <?php elseif($tab == 'templates'): ?>
                <div class="section-title">SMS Templates</div>
                <p style="color:var(--text-secondary); margin-bottom:1.5rem; font-size:0.9rem;">
                    Customize the SMS messages sent to customers. Use tags like <code>{name}</code>, <code>{driver}</code>, <code>{eta}</code>.
                </p>
                
                <div class="form-group">
                    <label class="form-label">Trip Created</label>
                    <textarea class="form-input" rows="2" name="tpl_trip_created">Hi {name}, your trip has been scheduled. Your driver {driver} will arrive in {eta} min.</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Driver Arrived</label>
                    <textarea class="form-input" rows="2" name="tpl_driver_arrived">Hi {name}, your driver {driver} has arrived at the pickup location.</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Trip Completed (Receipt)</label>
                    <textarea class="form-input" rows="2" name="tpl_trip_completed">Thanks for riding with us, {name}! Your total was {amount}. Receipt: {receipt_url}</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Driver Trip Offer</label>
                    <textarea class="form-input" rows="2" name="tpl_driver_offer">New Trip Request: Pickup at {pickup_address}. Distance: {distance}. Earnings: {fare}. Reply ACCEPT to take this trip.</textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Verification Code (OTP)</label>
                    <textarea class="form-input" rows="2" name="tpl_otp">Your verification code is: {code}. Do not share this with anyone.</textarea>
                </div>
            <?php elseif($tab == 'payments'): ?>
                <div class="section-title">Online Payments (Stripe)</div>
                <div class="form-group">
                    <label class="form-label" style="display:flex; align-items:center;">
                        <input type="checkbox" name="stripe_test_mode" checked style="margin-right:8px;"> Enable Test Mode
                    </label>
                    <div class="form-help">Uncheck for live transactions.</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Publishable Key</label>
                    <input type="text" class="form-input" name="stripe_pk" placeholder="pk_test_...">
                </div>
                <div class="form-group">
                    <label class="form-label">Secret Key</label>
                    <input type="password" class="form-input" name="stripe_sk" placeholder="sk_test_...">
                </div>
                <div class="form-group">
                    <label class="form-label">Currency</label>
                    <select class="form-input" name="stripe_currency">
                        <option value="USD" selected>USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="GBP">GBP (£)</option>
                    </select>
                </div>
            <?php else: ?>
                <div style="text-align:center; padding:3rem; color:var(--text-secondary);">
                    <i data-lucide="wrench" width="48" style="margin-bottom:1rem; opacity:0.3"></i>
                    <p>This section is under construction.</p>
                </div>
            <?php endif; ?>

            <?php if(in_array($tab, ['general', 'security', 'notifications', 'templates', 'payments'])): ?>
            <div style="margin-top:2rem; text-align:right;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
            <?php endif; ?>
        </form>
    </div>

</div>

<?= $this->endSection() ?>
