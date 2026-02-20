<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h1 class="h3" style="margin:0;">Staff Management</h1>
            <div style="color:var(--text-secondary); font-size:0.9rem;">Manage employees and access roles</div>
        </div>
        <a href="<?= base_url('staff/new') ?>" class="btn btn-primary"><i data-lucide="plus" width="16" style="margin-right:6px"></i> Add Staff</a>
    </div>

    <!-- Staff List -->
    <div class="card" style="overflow:hidden;">
        <table style="width:100%; border-collapse:collapse;">
            <thead style="background:var(--bg-surface-hover); border-bottom:1px solid var(--border-color);">
                <tr>
                    <th style="text-align:left; padding:1rem; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary);">User</th>
                    <th style="text-align:left; padding:1rem; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary);">Role</th>
                    <th style="text-align:left; padding:1rem; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary);">Status</th>
                    <th style="text-align:left; padding:1rem; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary);">Created</th>
                    <th style="text-align:right; padding:1rem; font-size:0.8rem; text-transform:uppercase; color:var(--text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($staff as $u): ?>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:1rem;">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <div style="width:36px; height:36px; border-radius:50%; background:var(--primary); color:white; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:0.9rem;">
                                <?= substr($u->first_name, 0, 1) . substr($u->last_name, 0, 1) ?>
                            </div>
                            <div>
                                <div style="font-weight:600;"><?= esc($u->first_name . ' ' . $u->last_name) ?></div>
                                <div style="font-size:0.8rem; color:var(--text-secondary);"><?= esc($u->email) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="padding:1rem;">
                        <?php if($u->role_name): ?>
                        <span style="background:rgba(99, 102, 241, 0.1); color:var(--primary); padding:4px 8px; border-radius:4px; font-size:0.75rem; font-weight:600;">
                            <?= esc($u->role_name) ?>
                        </span>
                        <?php else: ?>
                        <span style="color:var(--text-secondary); font-size:0.75rem;">No Role</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:1rem;">
                        <span class="status-badge status-<?= $u->status ?>"><?= ucfirst($u->status) ?></span>
                    </td>
                    <td style="padding:1rem; font-size:0.9rem; color:var(--text-secondary);">
                        <?= date('M d, Y', strtotime($u->created_at)) ?>
                    </td>
                    <td style="text-align:right; padding:1rem;">
                        <a href="<?= base_url('staff/edit/'.$u->id) ?>" class="btn-icon-sm" style="display:inline-flex;"><i data-lucide="edit-2" width="14"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if(empty($staff)): ?>
                <tr>
                    <td colspan="5" style="padding:3rem; text-align:center; color:var(--text-secondary);">No staff members found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
