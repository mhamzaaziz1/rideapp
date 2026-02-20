<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="container-fluid" style="padding: 1.5rem; height: 100vh; overflow: hidden; display: flex; flex-direction: column;">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        /* Tabs */
        .tabs-nav {
            display: flex;
            border-bottom: 1px solid var(--border-color);
            margin-bottom: 1rem;
            background: var(--bg-surface);
            border-radius: var(--radius-sm);
            padding: 0 0.5rem;
            flex-shrink: 0;
        }
        .tab-btn {
            padding: 1rem 1.5rem;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-secondary);
            font-weight: 600;
            cursor: pointer;
            position: relative;
        }
        .tab-btn:hover { color: var(--text-primary); }
        .tab-btn.active { color: var(--primary); border-bottom-color: var(--primary); }
        .tab-badge {
            background: var(--danger); color: white;
            font-size: 0.7rem; padding: 2px 6px; border-radius: 10px;
            margin-left: 6px;
        }

        /* Tab Content */
        .tab-pane { display: none; flex: 1; overflow-y: auto; padding-right: 4px; }
        .tab-pane.active { display: block; }

        /* Dispute Card */
        .dispute-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 100px 1.5fr 1fr 1fr 120px 80px;
            gap: 1.5rem;
            align-items: center;
            transition: all 0.2s;
            cursor: pointer;
        }
        .dispute-card:hover { 
            border-color: var(--primary); 
            transform: translateY(-2px); 
            box-shadow: var(--shadow-md); 
        }

        .dispute-id { font-family: monospace; font-weight: 700; color: var(--text-primary); font-size: 1rem; }
        .dispute-date { font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px; }
        
        .dispute-title { font-weight: 600; font-size: 1.1rem; color: var(--text-primary); margin-bottom: 0.25rem; }
        .dispute-trip { font-size: 0.85rem; color: var(--text-secondary); }
        .dispute-trip a { color: var(--primary); text-decoration: none; font-weight: 500; }
        .dispute-trip a:hover { text-decoration: underline; }

        .dispute-reporter { display: flex; align-items: center; gap: 0.75rem; }
        .reporter-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; }
        .reporter-customer { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .reporter-driver { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; } 
        .reporter-name { font-weight: 600; font-size: 0.95rem; color: var(--text-primary); }
        .reporter-type { font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-top: 2px;}

        .dispute-status { padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; font-weight: 600; text-align: center; display: inline-block; }
        .status-open { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
        .status-investigating { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); }
        .status-resolved { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-closed { background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); }

        .dispute-type-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.8rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-secondary); }
        .empty-state i { stroke-width: 1px; color: var(--border-color); margin-bottom: 1rem; }
    </style>

    <?php
        $d_open = []; $d_inv = []; $d_res = []; $d_cls = [];
        foreach($disputes as $d) {
            if($d->status == 'open') $d_open[] = $d;
            if($d->status == 'investigating') $d_inv[] = $d;
            if($d->status == 'resolved') $d_res[] = $d;
            if($d->status == 'closed') $d_cls[] = $d;
        }
    ?>

    <!-- Top Header -->
    <div style="flex-shrink: 0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem;">
        <div>
            <h1 class="h3" style="margin:0;">Disputes Resource Control</h1>
            <div style="color:var(--text-secondary); font-size:0.95rem;">Manage and resolve flagged trips rapidly.</div>
        </div>
    </div>

    <!-- Tabs Nav -->
    <div class="tabs-nav">
        <button class="tab-btn active" onclick="switchTab('open')">
            Action Required 
            <?php if(count($d_open) > 0): ?><span class="tab-badge"><?= count($d_open) ?></span><?php endif; ?>
        </button>
        <button class="tab-btn" onclick="switchTab('investigating')">
            Investigating
            <?php if(count($d_inv) > 0): ?><span class="tab-badge" style="background:var(--warning);"><?= count($d_inv) ?></span><?php endif; ?>
        </button>
        <button class="tab-btn" onclick="switchTab('resolved')">Resolved</button>
        <button class="tab-btn" onclick="switchTab('closed')">Closed</button>
        <button class="tab-btn" onclick="switchTab('all')">All Disputes</button>
    </div>

    <?php
        // Helper function to render a list of cards
        function render_disputes($list) {
            if(empty($list)) {
                echo '<div class="empty-state">
                        <i data-lucide="check-circle" width="64" height="64"></i>
                        <h4 style="color: var(--text-primary);">All Clear</h4>
                        <p>There are no disputes matching this status right now.</p>
                      </div>';
                return;
            }
            foreach($list as $dispute) {
                ?>
                <div class="dispute-card" onclick="window.location='<?= base_url('admin/disputes/view/'.$dispute->id) ?>'">
                    <!-- Col 1: ID & Date -->
                    <div>
                        <div class="dispute-id">#DSP-<?= $dispute->id ?></div>
                        <div class="dispute-date"><?= date('M j, Y', strtotime($dispute->created_at)) ?></div>
                    </div>

                    <!-- Col 2: Info -->
                    <div>
                        <div class="dispute-title"><?= esc($dispute->title) ?></div>
                        <div class="dispute-trip">
                            Related Trip: <a href="<?= base_url('dispatch/trips/view/'.($dispute->trip_id ?? '')) ?>" onclick="event.stopPropagation();">#<?= $dispute->trip_number ?? 'N/A' ?></a>
                        </div>
                    </div>

                    <!-- Col 3: Reporter -->
                    <div class="dispute-reporter">
                        <?php if($dispute->reported_by == 'customer'): ?>
                            <div class="reporter-avatar reporter-customer">
                                <?= substr($dispute->c_first_name, 0, 1) . substr($dispute->c_last_name, 0, 1) ?>
                            </div>
                            <div>
                                <div class="reporter-name"><?= esc($dispute->c_first_name . ' ' . $dispute->c_last_name) ?></div>
                                <div class="reporter-type"><i data-lucide="briefcase" width="10" style="vertical-align:baseline"></i> Customer</div>
                            </div>
                        <?php else: ?>
                            <div class="reporter-avatar reporter-driver">
                                <?= substr($dispute->d_first_name, 0, 1) . substr($dispute->d_last_name, 0, 1) ?>
                            </div>
                            <div>
                                <div class="reporter-name"><?= esc($dispute->d_first_name . ' ' . $dispute->d_last_name) ?></div>
                                <div class="reporter-type"><i data-lucide="car-front" width="10" style="vertical-align:baseline"></i> Driver</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Col 4: Type Tags -->
                    <div>
                        <span class="dispute-type-pill">
                            <i data-lucide="tag" width="12"></i> <?= esc(ucfirst($dispute->dispute_type)) ?>
                        </span>
                    </div>

                    <!-- Col 5: Status -->
                    <div style="text-align: right;">
                        <span class="dispute-status status-<?= $dispute->status ?>">
                            <?= ucfirst($dispute->status) ?>
                        </span>
                    </div>

                    <!-- Col 6: Actions -->
                    <div style="display:flex; justify-content:flex-end; gap:0.5rem;">
                        <a href="<?= base_url('admin/disputes/edit/'.$dispute->id) ?>" class="btn btn-outline" style="padding: 0.4rem; border-color: var(--primary); color: var(--primary);" title="Edit" onclick="event.stopPropagation();">
                            <i data-lucide="edit-2" width="16"></i>
                        </a>
                        <a href="<?= base_url('admin/disputes/delete/'.$dispute->id) ?>" class="btn btn-outline" style="padding: 0.4rem; border-color: var(--danger); color: var(--danger);" title="Delete" onclick="event.stopPropagation(); return confirm('Delete this dispute? This cannot be undone.');">
                            <i data-lucide="trash-2" width="16"></i>
                        </a>
                    </div>
                </div>
                <?php
            }
        }
    ?>

    <!-- Tab Panes -->
    <div id="tab-open" class="tab-pane active">
        <?php render_disputes($d_open); ?>
    </div>
    
    <div id="tab-investigating" class="tab-pane">
        <?php render_disputes($d_inv); ?>
    </div>

    <div id="tab-resolved" class="tab-pane">
        <?php render_disputes($d_res); ?>
    </div>

    <div id="tab-closed" class="tab-pane">
        <?php render_disputes($d_cls); ?>
    </div>

    <div id="tab-all" class="tab-pane">
        <?php render_disputes($disputes); ?>
    </div>

</div>

<script>
    function switchTab(tabId) {
        // Hide all panes
        document.querySelectorAll('.tab-pane').forEach(el => el.style.display = 'none');
        // Remove active class from all buttons
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        
        // Show selected pane and set button active
        document.getElementById('tab-' + tabId).style.display = 'block';
        event.currentTarget.classList.add('active');
    }

    if(typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>
