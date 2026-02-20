<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Finance Specific Styles */
    .finance-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .finance-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    
    .finance-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; bottom: 0;
        width: 4px;
        background: var(--primary);
    }
    .card-success::before { background: var(--success); }
    .card-warning::before { background: var(--warning); }
    .card-info::before { background: var(--info); }
    
    .f-label { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .f-value { font-size: 1.8rem; font-weight: 700; color: var(--text-primary); }
    .f-sub { font-size: 0.8rem; color: var(--text-secondary); margin-top: 5px; }

    /* Transactions Table */
    .transactions-panel {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        overflow: hidden;
    }
    .panel-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--bg-body);
    }
    .panel-title { font-weight: 700; font-size: 1rem; }
    
    .t-table { width: 100%; border-collapse: collapse; }
    .t-table th { text-align: left; padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.75rem; text-transform: uppercase; color: var(--text-secondary); }
    .t-table td { padding: 1rem; border-bottom: 1px solid var(--border-color); font-size: 0.9rem; vertical-align: middle; }
    .t-table tr:last-child td { border-bottom: none; }
    
    .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .bg-green { background: var(--success); }
    .bg-yellow { background: var(--warning); }
    .bg-red { background: var(--danger); }
    
    .method-icon { width: 24px; height: 16px; border-radius: 2px; border: 1px solid var(--border-color); display: inline-flex; align-items: center; justify-content: center; margin-right: 6px; font-size: 10px; font-weight: 700; color: var(--text-secondary); }
</style>

<div style="padding: 2rem;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:700;">Financial Overview</h1>
            <p style="color:var(--text-secondary); font-size:0.9rem;">Monitor revenue and transactions</p>
        </div>
        <div>
            <button class="btn" style="margin-right:8px; border:1px solid var(--border-color); background:var(--bg-surface);">Month View</button>
            <button class="btn btn-primary"><i data-lucide="download" width="16" style="margin-right:8px"></i> Export Report</button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="finance-grid">
        <div class="finance-card card-success">
            <div class="f-label">Total Revenue</div>
            <div class="f-value">$<?= number_format($total_revenue, 2) ?></div>
            <div class="f-sub"><i data-lucide="trending-up" width="14" style="color:var(--success); vertical-align:middle"></i> +12.5% from last month</div>
        </div>
        <div class="finance-card card-warning">
            <div class="f-label">Pending Payments</div>
            <div class="f-value">$<?= number_format($pending_amount, 2) ?></div>
            <div class="f-sub">3 invoices unpaid</div>
        </div>
        <div class="finance-card card-info">
            <div class="f-label">Card Payments</div>
            <div class="f-value">$<?= number_format($card_revenue, 2) ?></div>
            <div class="f-sub"><?= $total_revenue > 0 ? round(($card_revenue / $total_revenue) * 100) : 0 ?>% of total</div>
        </div>
        <div class="finance-card">
            <div class="f-label">Cash Collected</div>
            <div class="f-value">$<?= number_format($cash_revenue, 2) ?></div>
            <div class="f-sub">Handled by drivers</div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="transactions-panel">
        <div class="panel-header">
            <div class="panel-title">Recent Transactions</div>
            <a href="#" style="font-size:0.85rem; color:var(--primary); text-decoration:none;">View All</a>
        </div>
        
        <?php if(empty($invoices)): ?>
            <div style="padding:2rem; text-align:center; color:var(--text-secondary);">No transactions found.</div>
        <?php else: ?>
            <table class="t-table">
                <thead>
                    <tr>
                        <th>Invoice ID</th>
                        <th>Trip ID</th>
                        <th>Date & Time</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th style="text-align:right;">Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($invoices as $inv): ?>
                    <tr>
                        <td style="font-family:monospace; font-weight:600; color:var(--text-primary);"><?= $inv->invoice_number ?></td>
                        <td><a href="<?= base_url('trips?id='.$inv->trip_id) ?>" style="color:var(--primary); text-decoration:none;">#<?= $inv->trip_id ?></a></td>
                        <td style="color:var(--text-secondary); font-size:0.85rem;">
                            <?= date('M d, Y', strtotime($inv->created_at)) ?> 
                            <span style="opacity:0.6"><?= date('H:i', strtotime($inv->created_at)) ?></span>
                        </td>
                        <td>
                            <div style="display:flex; align-items:center;">
                                <span class="method-icon"><i data-lucide="<?= $inv->payment_method == 'card' ? 'credit-card' : 'banknote' ?>" width="10"></i></span> 
                                <span style="text-transform:capitalize"><?= $inv->payment_method ?></span>
                            </div>
                        </td>
                        <td>
                            <?php 
                                $colorClass = 'bg-yellow';
                                if($inv->status == 'paid') $colorClass = 'bg-green';
                                if($inv->status == 'void') $colorClass = 'bg-red';
                            ?>
                            <div style="display:flex; align-items:center;">
                                <span class="status-dot <?= $colorClass ?>"></span>
                                <span style="text-transform:capitalize"><?= $inv->status ?></span>
                            </div>
                        </td>
                        <td style="text-align:right; font-weight:700;">$<?= number_format($inv->amount, 2) ?></td>
                        <td style="text-align:right;">
                            <button class="btn" style="padding:4px;"><i data-lucide="download" width="14"></i></button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>
