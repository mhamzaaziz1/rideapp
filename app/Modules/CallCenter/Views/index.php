<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Design System consistent with Customer/Fleet modules */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .stat-label { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text-primary); }
    .stat-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--bg-surface-hover); color: var(--text-accent); }
    
    .filter-bar {
        background: var(--bg-surface);
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .search-input-wrapper {
        position: relative;
        flex: 1;
    }
    .search-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); width: 16px; }
    .search-input {
        width: 100%;
        padding: 0.5rem 1rem 0.5rem 2.5rem;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        color: var(--text-primary);
        font-size: 0.9rem;
    }
    
    .table-container {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
    }
    .table { width: 100%; border-collapse: collapse; }
    .table th { text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-color); background: var(--bg-body); font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); }
    .table td { padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; color: var(--text-primary); vertical-align: middle; }
    .table tr:last-child td { border-bottom: none; }
    
    tr.clickable-row { cursor: pointer; transition: background-color 0.15s; }
    tr.clickable-row:hover { background-color: var(--bg-surface-hover); }
    
    .status-badge { padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
    .status-missed { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    .status-answered { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-outbound { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .status-voicemail { background: rgba(107, 114, 128, 0.1); color: var(--text-secondary); }
    
    .action-btn { 
        padding: 6px; 
        border-radius: 6px; 
        color: var(--text-secondary); 
        transition: all 0.2s; 
        background: transparent;
        border: 1px solid transparent;
    }
    .action-btn:hover { background: var(--bg-body); color: var(--text-primary); border-color: var(--border-color); }
    .action-btn.call-btn:hover { color: var(--success); border-color: var(--success); background: rgba(16, 185, 129, 0.1); }
    .action-btn.delete-btn:hover { color: var(--danger); border-color: var(--danger); background: rgba(239, 68, 68, 0.1); }
</style>

<div style="padding: 2rem;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:700;">Call Center Logs</h1>
            <p style="color:var(--text-secondary); font-size:0.9rem;">Track and manage communication history</p>
        </div>
        <a href="<?= base_url('call-logs/new') ?>" class="btn btn-primary"><i data-lucide="phone-call" width="16" style="margin-right:8px"></i> Log New Call</a>
    </div>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Calls</div>
                <div class="stat-value"><?= $stats['total'] ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="list"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Inbound</div>
                <div class="stat-value" style="color:var(--info)"><?= $stats['inbound'] ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="arrow-down-left" style="color:var(--info)"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Outbound</div>
                <div class="stat-value" style="color:var(--warning)"><?= $stats['outbound'] ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="arrow-up-right" style="color:var(--warning)"></i></div>
        </div>
    </div>

    <!-- Filter -->
    <form action="<?= base_url('call-logs') ?>" method="get" class="filter-bar">
        <div class="search-input-wrapper">
            <i data-lucide="search" class="search-icon"></i>
            <input type="text" name="search" class="search-input" placeholder="Search by name or phone number..." value="<?= esc($search) ?>">
        </div>
        <button type="submit" class="btn" style="border:1px solid var(--border-color); background:var(--bg-body);">Filter</button>
    </form>

    <!-- Layout: Grid with Sidebar -->
    <div style="display:grid; grid-template-columns: 1fr 300px; gap: 1.5rem;">
        
        <!-- Left: Logs Table -->
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Caller Name</th>
                        <th>Phone Number</th>
                        <th>Direction</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th>Date</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($logs) && is_array($logs)): ?>
                        <?php foreach($logs as $log): ?>
                        <tr class="clickable-row" onclick="window.location='<?= base_url('call-logs/edit/' . $log->id) ?>'">
                            <td style="font-weight:600;"><?= esc($log->caller_name ?: 'Unknown') ?></td>
                            <td><?= esc($log->caller_number) ?></td>
                            <td>
                                <?php if($log->direction == 'inbound'): ?>
                                    <span style="display:flex; align-items:center; gap:4px; font-size:0.85rem; color:var(--info);">
                                        <i data-lucide="arrow-down-left" width="14"></i> Inbound
                                    </span>
                                <?php else: ?>
                                    <span style="display:flex; align-items:center; gap:4px; font-size:0.85rem; color:var(--warning);">
                                        <i data-lucide="arrow-up-right" width="14"></i> Outbound
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?= $log->status == 'answered' ? 'answered' : ($log->status == 'missed' ? 'missed' : 'voicemail') ?>">
                                    <?= ucfirst($log->status) ?>
                                </span>
                            </td>
                            <td style="color:var(--text-secondary);"><?= gmdate("H:i:s", $log->duration) ?></td>
                            <td style="color:var(--text-secondary); font-size:0.85rem;"><?= $log->created_at ? $log->created_at->humanize() : '-' ?></td>
                            <td style="text-align:right;">
                                <button class="action-btn call-btn" onclick="event.stopPropagation(); window.location.href='tel:<?= esc($log->caller_number) ?>'" title="Call"><i data-lucide="phone" width="16"></i></button>
                                <button class="action-btn" onclick="event.stopPropagation(); window.location='<?= base_url('call-logs/edit/' . $log->id) ?>'" title="Edit"><i data-lucide="edit-2" width="16"></i></button>
                                <button class="action-btn delete-btn" onclick="event.stopPropagation(); if(confirm('Delete log?')) window.location='<?= base_url('call-logs/delete/' . $log->id) ?>'" title="Delete"><i data-lucide="trash-2" width="16"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:3rem; color:var(--text-secondary);">No calls found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Right: Active Calls Panel -->
        <div class="col-panel" style="height: fit-content;">
            <div class="panel-header"><i data-lucide="phone-incoming" width="16"></i> Active Calls <span class="badge" style="margin-left:auto; background:var(--primary); color:white;">3</span></div>
            <div class="panel-body">
                <!-- Active Call -->
                <div class="call-card active">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span class="badge" style="background:var(--primary); color:white;">ACTIVE</span>
                        <i data-lucide="mic" width="14" style="cursor:pointer; color:var(--text-secondary);"></i>
                    </div>
                    <div style="font-weight:600;">John Doe</div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">+1 (555) 123-4567</div>
                    <div style="margin-top:8px; display:flex; gap:8px;">
                        <button class="btn btn-sm btn-light" style="flex:1; color:var(--danger)">End</button>
                        <button class="btn btn-sm btn-light" style="flex:1;">Hold</button>
                    </div>
                </div>
                <!-- Hold Call -->
                <div class="call-card hold">
                    <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                        <span class="badge" style="background:var(--warning); color:black;">HOLD</span>
                        <i data-lucide="play" width="14" style="cursor:pointer"></i>
                    </div>
                    <div style="font-weight:600;">Emily Davis</div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">+1 (555) 456-7890</div>
                </div>
                 <!-- Ring Call -->
                 <div class="call-card ring">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="badge" style="background:var(--success); color:white;">INCOMING...</span>
                        <div style="display:flex; gap:4px;">
                            <button class="btn btn-sm" style="background:var(--success); color:white; padding:2px 8px;">Accept</button>
                            <button class="btn btn-sm" style="background:var(--danger); color:white; padding:2px 8px;">Reject</button>
                        </div>
                    </div>
                    <div style="font-weight:600; margin-top:4px;">Unknown</div>
                    <div style="font-size:0.8rem; color:var(--text-secondary);">+1 (555) 000-0000</div>
                </div>
            </div>
        </div>

    </div>
    <div style="padding:1rem;">
        <?= $pager->links() ?>
    </div>

</div>

<?= $this->endSection() ?>
