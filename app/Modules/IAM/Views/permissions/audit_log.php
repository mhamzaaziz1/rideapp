<?= $this->extend('layouts/master') ?>
<?= $this->section('content') ?>
<div style="padding:2rem;max-width:1200px;margin:0 auto;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
        <div>
            <a href="<?= base_url('roles') ?>" style="color:var(--text-secondary);display:inline-flex;align-items:center;gap:4px;font-size:0.9rem;margin-bottom:0.5rem;text-decoration:none;"><i data-lucide="arrow-left" width="16"></i> Back to Roles</a>
            <h1 class="h3" style="margin:0;">Permission Audit Log</h1>
            <div style="color:var(--text-secondary);font-size:0.9rem;">Track all permission changes across the system</div>
        </div>
    </div>
    <div class="card" style="overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:var(--bg-surface-hover);border-bottom:1px solid var(--border-color);">
                <tr>
                    <th style="text-align:left;padding:0.85rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-secondary);">Timestamp</th>
                    <th style="text-align:left;padding:0.85rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-secondary);">Actor</th>
                    <th style="text-align:left;padding:0.85rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-secondary);">Action</th>
                    <th style="text-align:left;padding:0.85rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-secondary);">Target</th>
                    <th style="text-align:left;padding:0.85rem 1rem;font-size:0.78rem;text-transform:uppercase;color:var(--text-secondary);">Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $log): ?>
                <?php
                    $actionColors = [
                        'role_created'=>'#22c55e','role_updated'=>'#f59e0b','role_deleted'=>'#ef4444',
                        'permission_granted'=>'#3b82f6','permission_denied'=>'#ef4444',
                        'permission_override_removed'=>'#8b5cf6',
                    ];
                    $color = $actionColors[$log->action] ?? 'var(--text-secondary)';
                ?>
                <tr style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:0.75rem 1rem;font-size:0.85rem;color:var(--text-secondary);white-space:nowrap;"><?= date('M d, Y H:i', strtotime($log->created_at)) ?></td>
                    <td style="padding:0.75rem 1rem;font-size:0.85rem;font-weight:500;"><?= esc(($log->actor_first ?? 'System').' '.($log->actor_last ?? '')) ?></td>
                    <td style="padding:0.75rem 1rem;"><span style="background:color-mix(in srgb,<?= $color ?> 12%,transparent);color:<?= $color ?>;padding:3px 8px;border-radius:4px;font-size:0.75rem;font-weight:600;"><?= esc(str_replace('_',' ',ucfirst($log->action))) ?></span></td>
                    <td style="padding:0.75rem 1rem;font-size:0.85rem;"><?= esc(ucfirst($log->target_type)) ?> #<?= $log->target_id ?></td>
                    <td style="padding:0.75rem 1rem;font-size:0.8rem;color:var(--text-secondary);max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= esc($log->details) ?>"><?= esc($log->details) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($logs)): ?>
                <tr><td colspan="5" style="padding:3rem;text-align:center;color:var(--text-secondary);">No audit log entries found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
