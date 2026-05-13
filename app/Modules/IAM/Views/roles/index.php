<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h1 class="h3" style="margin:0;">Roles & Permissions</h1>
            <div style="color:var(--text-secondary); font-size:0.9rem;">Manage system roles and their access levels</div>
        </div>
        <div style="display:flex; gap:0.75rem;">
            <a href="<?= base_url('roles/audit-log') ?>" class="btn" style="background:var(--bg-surface); border:1px solid var(--border-color); color:var(--text-primary); display:inline-flex; align-items:center; gap:6px; padding:0.5rem 1rem; border-radius:var(--radius-sm); text-decoration:none; font-size:0.875rem;">
                <i data-lucide="scroll-text" width="16"></i> Audit Log
            </a>
            <a href="<?= base_url('roles/new') ?>" class="btn btn-primary"><i data-lucide="plus" width="16" style="margin-right:6px"></i> Create Role</a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('success')): ?>
    <div style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.3); color:#22c55e; padding:0.75rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; display:flex; align-items:center; gap:8px;">
        <i data-lucide="check-circle" width="16"></i> <?= session()->getFlashdata('success') ?>
    </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
    <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#ef4444; padding:0.75rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; display:flex; align-items:center; gap:8px;">
        <i data-lucide="alert-circle" width="16"></i> <?= session()->getFlashdata('error') ?>
    </div>
    <?php endif; ?>

    <!-- Roles Grid -->
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap:1.25rem;">
        <?php foreach($roles as $role): ?>
        <div class="card role-card" style="padding:0; overflow:hidden; position:relative; transition:transform 0.15s, box-shadow 0.15s;">
            <!-- Header -->
            <div style="padding:1.5rem 1.5rem 1rem; display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="display:flex; align-items:center; gap:12px;">
                    <div style="width:42px; height:42px; border-radius:10px; background:<?= $role->is_system ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #3b82f6, #06b6d4)' ?>; display:flex; align-items:center; justify-content:center;">
                        <i data-lucide="<?= $role->is_system ? 'shield-check' : 'shield' ?>" width="20" style="color:white;"></i>
                    </div>
                    <div>
                        <div style="font-weight:700; font-size:1.05rem; color:var(--text-primary);"><?= esc($role->name) ?></div>
                        <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:2px;"><?= esc($role->description ?? 'No description') ?></div>
                    </div>
                </div>
                <?php if($role->is_system): ?>
                <span style="background:rgba(139,92,246,0.15); color:#a78bfa; padding:2px 8px; border-radius:4px; font-size:0.7rem; font-weight:600; letter-spacing:0.5px;">SYSTEM</span>
                <?php endif; ?>
            </div>

            <!-- Stats -->
            <div style="display:flex; gap:0; border-top:1px solid var(--border-color); border-bottom:1px solid var(--border-color);">
                <div style="flex:1; padding:0.85rem 1.5rem; text-align:center; border-right:1px solid var(--border-color);">
                    <div style="font-size:1.4rem; font-weight:800; color:var(--text-primary);"><?= $role->user_count ?></div>
                    <div style="font-size:0.7rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px;">Users</div>
                </div>
                <div style="flex:1; padding:0.85rem 1.5rem; text-align:center;">
                    <div style="font-size:1.4rem; font-weight:800; color:var(--text-primary);"><?= $role->permission_count ?></div>
                    <div style="font-size:0.7rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px;">Permissions</div>
                </div>
            </div>

            <!-- Actions -->
            <div style="padding:1rem 1.5rem; display:flex; gap:0.5rem;">
                <a href="<?= base_url('roles/view/'.$role->id) ?>" class="role-action-btn" style="flex:1; text-align:center;">
                    <i data-lucide="eye" width="14"></i> View
                </a>
                <a href="<?= base_url('roles/edit/'.$role->id) ?>" class="role-action-btn" style="flex:1; text-align:center;">
                    <i data-lucide="edit-2" width="14"></i> Edit
                </a>
                <?php if(!$role->is_system): ?>
                <a href="<?= base_url('roles/delete/'.$role->id) ?>" class="role-action-btn role-action-danger" style="flex:1; text-align:center;" onclick="return confirm('Are you sure you want to delete this role?')">
                    <i data-lucide="trash-2" width="14"></i> Delete
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if(empty($roles)): ?>
        <div style="grid-column: 1/-1; text-align:center; padding:4rem; color:var(--text-secondary);">
            <i data-lucide="shield-off" width="48" style="opacity:0.3; margin-bottom:1rem;"></i>
            <div>No roles found. Create your first role to get started.</div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .role-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-lg); }
    .role-action-btn { 
        display:inline-flex; align-items:center; justify-content:center; gap:6px; 
        padding:0.5rem 0.75rem; border-radius:var(--radius-sm); font-size:0.8rem; font-weight:500; 
        color:var(--text-secondary); background:var(--bg-surface-hover); border:1px solid transparent; 
        text-decoration:none; transition:all 0.15s; cursor:pointer;
    }
    .role-action-btn:hover { color:var(--primary); background:rgba(99,102,241,0.1); border-color:rgba(99,102,241,0.3); }
    .role-action-danger:hover { color:#ef4444 !important; background:rgba(239,68,68,0.1) !important; border-color:rgba(239,68,68,0.3) !important; }
</style>

<?= $this->endSection() ?>
