<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 2rem;">
        <a href="<?= base_url('roles') ?>" style="color:var(--text-secondary); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem; margin-bottom:1rem; text-decoration:none;">
            <i data-lucide="arrow-left" width="16"></i> Back to Roles
        </a>
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="width:48px; height:48px; border-radius:12px; background:<?= !empty($role->is_system) ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #3b82f6, #06b6d4)' ?>; display:flex; align-items:center; justify-content:center;">
                    <i data-lucide="<?= !empty($role->is_system) ? 'shield-check' : 'shield' ?>" width="24" style="color:white;"></i>
                </div>
                <div>
                    <h1 class="h3" style="margin:0;"><?= esc($role->name) ?></h1>
                    <div style="color:var(--text-secondary); font-size:0.9rem;"><?= esc($role->description ?? 'No description') ?></div>
                </div>
                <?php if(!empty($role->is_system)): ?>
                <span style="background:rgba(139,92,246,0.15); color:#a78bfa; padding:3px 10px; border-radius:4px; font-size:0.75rem; font-weight:600;">SYSTEM ROLE</span>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('roles/edit/'.$role->id) ?>" class="btn btn-primary"><i data-lucide="edit-2" width="16" style="margin-right:6px;"></i> Edit Role</a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 360px; gap:2rem;">
        
        <!-- Left: Permissions -->
        <div class="card" style="padding:1.5rem;">
            <h4 style="margin-bottom:1.25rem; font-weight:700; display:flex; align-items:center; gap:8px;">
                <i data-lucide="key-round" width="18" style="color:var(--primary);"></i>
                Assigned Permissions
                <span style="background:rgba(99,102,241,0.15); color:var(--primary); padding:2px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;"><?= count($permissions) ?></span>
            </h4>

            <?php 
            // Group permissions by module
            $grouped = [];
            foreach ($permissions as $p) {
                $mod = $p->module ?? 'General';
                $grouped[$mod][] = $p;
            }
            ?>

            <?php if(empty($permissions)): ?>
            <div style="padding:2rem; text-align:center; color:var(--text-secondary);">
                <i data-lucide="key-round" width="32" style="opacity:0.3; margin-bottom:0.5rem;"></i>
                <div>No permissions assigned to this role.</div>
            </div>
            <?php else: ?>
            <?php foreach ($grouped as $module => $perms): ?>
            <div style="margin-bottom:1.25rem;">
                <div style="font-weight:700; font-size:0.85rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; margin-bottom:0.5rem; display:flex; align-items:center; gap:6px;">
                    <i data-lucide="package" width="14"></i> <?= esc($module) ?>
                    <span style="font-size:0.7rem; color:var(--primary);">(<?= count($perms) ?>)</span>
                </div>
                <div style="display:flex; flex-wrap:wrap; gap:0.35rem; padding-left:1rem;">
                    <?php foreach ($perms as $p): ?>
                    <?php 
                        $action = basename(str_replace('.', '/', $p->name));
                        $actionColor = match($action) {
                            'create'  => '#22c55e',
                            'view'    => '#3b82f6',
                            'edit', 'update_status' => '#f59e0b',
                            'delete'  => '#ef4444',
                            'print', 'export' => '#06b6d4',
                            default   => '#8b5cf6',
                        };
                    ?>
                    <span style="display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:6px; font-size:0.75rem; font-weight:500; background:color-mix(in srgb, <?= $actionColor ?> 12%, transparent); color:<?= $actionColor ?>; border:1px solid color-mix(in srgb, <?= $actionColor ?> 25%, transparent);" title="<?= esc($p->description ?? $p->name) ?>">
                        <span style="width:5px;height:5px;border-radius:50%;background:<?= $actionColor ?>;"></span>
                        <?= esc($p->group_name ?? '') ?>: <?= ucfirst(str_replace('_', ' ', $action)) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Right: Users -->
        <div class="card" style="padding:1.5rem;">
            <h4 style="margin-bottom:1.25rem; font-weight:700; display:flex; align-items:center; gap:8px;">
                <i data-lucide="users" width="18" style="color:var(--primary);"></i>
                Assigned Users
                <span style="background:rgba(99,102,241,0.15); color:var(--primary); padding:2px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;"><?= count($users) ?></span>
            </h4>

            <?php if(empty($users)): ?>
            <div style="padding:2rem; text-align:center; color:var(--text-secondary);">
                <i data-lucide="user-x" width="32" style="opacity:0.3; margin-bottom:0.5rem;"></i>
                <div>No users assigned to this role.</div>
            </div>
            <?php else: ?>
            <div style="display:flex; flex-direction:column; gap:0.5rem;">
                <?php foreach ($users as $u): ?>
                <div style="display:flex; align-items:center; gap:12px; padding:0.75rem; border-radius:var(--radius-sm); border:1px solid var(--border-color); transition:background 0.15s;">
                    <div style="width:36px; height:36px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.85rem; flex-shrink:0;">
                        <?= substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1) ?>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:600; font-size:0.9rem;"><?= esc($u->first_name . ' ' . $u->last_name) ?></div>
                        <div style="font-size:0.75rem; color:var(--text-secondary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?= esc($u->email) ?></div>
                    </div>
                    <div style="display:flex; gap:4px;">
                        <a href="<?= base_url('staff/permissions/'.$u->id) ?>" title="Manage Permissions" style="color:var(--text-secondary); padding:4px; display:flex; text-decoration:none;">
                            <i data-lucide="key-round" width="14"></i>
                        </a>
                        <a href="<?= base_url('staff/edit/'.$u->id) ?>" title="Edit" style="color:var(--text-secondary); padding:4px; display:flex; text-decoration:none;">
                            <i data-lucide="edit-2" width="14"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
