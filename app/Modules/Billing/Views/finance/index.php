<?= $this->extend('layouts/master') ?>
<?= $this->section('content') ?>

<style>
.fin-wrap{padding:1.5rem 2rem}
.fin-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem}
.fin-header h1{font-size:1.5rem;font-weight:800;margin:0}
.fin-header .sub{font-size:.85rem;color:var(--text-secondary);margin-top:2px}
.fin-filters{display:flex;gap:6px;flex-wrap:wrap}
.fin-filters .fb{padding:7px 14px;border-radius:8px;font-size:.8rem;font-weight:600;border:1px solid var(--border-color);background:var(--bg-surface);color:var(--text-secondary);cursor:pointer;transition:var(--transition)}
.fin-filters .fb:hover{border-color:var(--primary);color:var(--text-primary)}
.fin-filters .fb.active{background:var(--primary);color:#fff;border-color:var(--primary);box-shadow:0 4px 12px var(--primary-glow)}
.fin-actions{display:flex;gap:8px;align-items:center}

/* KPI Grid */
.kpi-grid{display:grid;grid-template-columns:repeat(6,1fr);gap:1rem;margin-bottom:1.5rem}
.kpi-card{background:var(--bg-surface);border:1px solid var(--border-color);border-radius:14px;padding:1.2rem;position:relative;overflow:hidden;transition:var(--transition)}
.kpi-card:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.15)}
.kpi-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.kpi-card.kc-green::before{background:linear-gradient(90deg,#10b981,#34d399)}
.kpi-card.kc-indigo::before{background:linear-gradient(90deg,#6366f1,#818cf8)}
.kpi-card.kc-amber::before{background:linear-gradient(90deg,#f59e0b,#fbbf24)}
.kpi-card.kc-red::before{background:linear-gradient(90deg,#ef4444,#f87171)}
.kpi-card.kc-cyan::before{background:linear-gradient(90deg,#0ea5e9,#38bdf8)}
.kpi-card.kc-purple::before{background:linear-gradient(90deg,#8b5cf6,#a78bfa)}
.kpi-label{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:6px}
.kpi-val{font-size:1.5rem;font-weight:800;color:var(--text-primary);line-height:1.2}
.kpi-growth{display:inline-flex;align-items:center;gap:3px;font-size:.72rem;font-weight:700;margin-top:6px;padding:2px 8px;border-radius:6px}
.kpi-growth.up{background:rgba(16,185,129,.12);color:#10b981}
.kpi-growth.down{background:rgba(239,68,68,.12);color:#ef4444}
.kpi-growth.flat{background:rgba(148,163,184,.12);color:#94a3b8}

/* Section panels */
.fin-panel{background:var(--bg-surface);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;margin-bottom:1.5rem}
.fp-head{padding:1rem 1.25rem;border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;align-items:center;background:var(--bg-body)}
.fp-title{font-weight:700;font-size:.95rem;display:flex;align-items:center;gap:8px}

/* Layout grid */
.fin-row{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem}
.fin-row-3{display:grid;grid-template-columns:2fr 1fr 1fr;gap:1.5rem;margin-bottom:1.5rem}

/* Mini stat */
.ms-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:1.25rem}
.ms-item{padding:1rem;border-radius:10px;background:var(--bg-body);border:1px solid var(--border-color)}
.ms-item .ml{font-size:.7rem;text-transform:uppercase;color:var(--text-tertiary);font-weight:700;letter-spacing:.04em}
.ms-item .mv{font-size:1.15rem;font-weight:800;margin-top:4px;color:var(--text-primary)}

/* Chart area */
.chart-bars{display:flex;align-items:flex-end;gap:8px;height:180px;padding:1.25rem 1.25rem .5rem}
.chart-bar-wrap{flex:1;display:flex;flex-direction:column;align-items:center;gap:4px}
.chart-bar{width:100%;border-radius:6px 6px 0 0;min-height:4px;transition:height .6s cubic-bezier(.4,0,.2,1);position:relative}
.chart-bar:hover{opacity:.85}
.chart-bar .cb-tip{position:absolute;top:-22px;left:50%;transform:translateX(-50%);font-size:.65rem;font-weight:700;color:var(--text-primary);white-space:nowrap;opacity:0;transition:.2s}
.chart-bar:hover .cb-tip{opacity:1}
.chart-bar-label{font-size:.6rem;color:var(--text-tertiary);font-weight:600;text-align:center}

/* Donut */
.donut-wrap{display:flex;align-items:center;justify-content:center;gap:2rem;padding:1.5rem}
.donut-legend{display:flex;flex-direction:column;gap:10px}
.donut-legend .dl-item{display:flex;align-items:center;gap:8px;font-size:.8rem}
.dl-dot{width:10px;height:10px;border-radius:3px;flex-shrink:0}

/* Table */
.ft-table{width:100%;border-collapse:collapse}
.ft-table th{text-align:left;padding:.85rem 1rem;border-bottom:1px solid var(--border-color);font-size:.7rem;text-transform:uppercase;color:var(--text-tertiary);font-weight:700;letter-spacing:.04em;background:var(--bg-body)}
.ft-table td{padding:.75rem 1rem;border-bottom:1px solid var(--border-color);font-size:.82rem;vertical-align:middle}
.ft-table tr:last-child td{border-bottom:none}
.ft-table tr:hover td{background:var(--bg-surface-hover)}
.ft-check{width:16px;height:16px;accent-color:var(--primary);cursor:pointer}
.status-pill{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;font-size:.72rem;font-weight:700}
.sp-completed{background:rgba(16,185,129,.12);color:#10b981}
.sp-active{background:rgba(59,130,246,.12);color:#3b82f6}
.sp-cancelled{background:rgba(239,68,68,.12);color:#ef4444}
.sp-dispatching{background:rgba(245,158,11,.12);color:#f59e0b}

/* Print */
@media print{
 .fin-header,.fin-filters,.fin-actions,.no-print{display:none!important}
 .fin-wrap{padding:0}
 body{background:#fff;color:#000}
 .kpi-card,.fin-panel{border:1px solid #ddd;box-shadow:none}
}

@media(max-width:1200px){.kpi-grid{grid-template-columns:repeat(3,1fr)}.fin-row,.fin-row-3{grid-template-columns:1fr}}
@media(max-width:768px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="fin-wrap">

<!-- HEADER -->
<div class="fin-header">
    <div>
        <h1><i data-lucide="bar-chart-3" width="24" style="vertical-align:middle;margin-right:6px;color:var(--primary)"></i> Financial Dashboard</h1>
        <div class="sub">Period: <strong><?= $date_range['label'] ?></strong> &nbsp;|&nbsp; <?= date('M d', strtotime($date_range['start'])) ?> – <?= date('M d, Y', strtotime($date_range['end'])) ?></div>
    </div>
    <div style="display:flex;flex-direction:column;gap:8px;align-items:flex-end">
        <div class="fin-filters">
            <?php $periods = ['monthly'=>'Monthly','quarterly'=>'Quarterly','half_year'=>'Half Year','yearly'=>'Yearly','last_year'=>'Last Year','all'=>'All Time']; ?>
            <?php foreach($periods as $k=>$v): ?>
                <a href="<?= base_url('finance?period='.$k) ?>" class="fb <?= $period===$k?'active':'' ?>"><?= $v ?></a>
            <?php endforeach; ?>
        </div>
        <div class="fin-actions no-print">
            <button class="btn btn-secondary btn-sm" onclick="window.print()" style="gap:6px"><i data-lucide="printer" width="14"></i> Print</button>
            <button class="btn btn-secondary btn-sm" id="btn-bulk-print" style="gap:6px" disabled><i data-lucide="file-stack" width="14"></i> Bulk Print (<span id="sel-count">0</span>)</button>
            <a href="<?= base_url('finance/export-csv?period='.$period) ?>" class="btn btn-primary btn-sm" style="gap:6px"><i data-lucide="download" width="14"></i> Export CSV</a>
        </div>
    </div>
</div>

<!-- KPI CARDS -->
<div class="kpi-grid">
    <div class="kpi-card kc-green">
        <div class="kpi-label">Total Revenue</div>
        <div class="kpi-val">$<?= number_format($total_revenue, 2) ?></div>
        <div class="kpi-growth <?= $growth_revenue['direction'] ?>">
            <i data-lucide="<?= $growth_revenue['direction']==='up'?'trending-up':($growth_revenue['direction']==='down'?'trending-down':'minus') ?>" width="12"></i>
            <?= $growth_revenue['percent'] ?>%
        </div>
    </div>
    <div class="kpi-card kc-indigo">
        <div class="kpi-label">System Liability (Wallet Funds)</div>
        <div class="kpi-val">$<?= number_format($system_liability, 2) ?></div>
        <div class="kpi-growth flat">
            <i data-lucide="shield-alert" width="12"></i> Out in user wallets
        </div>
    </div>
    <div class="kpi-card kc-amber">
        <div class="kpi-label">Driver Payouts</div>
        <div class="kpi-val">$<?= number_format($driver_payouts, 2) ?></div>
        <div class="kpi-growth <?= $growth_driver_pay['direction'] ?>">
            <i data-lucide="<?= $growth_driver_pay['direction']==='up'?'trending-up':'trending-down' ?>" width="12"></i>
            <?= $growth_driver_pay['percent'] ?>%
        </div>
    </div>
    <div class="kpi-card kc-green">
        <div class="kpi-label">Daily Revenue (24hr Cut)</div>
        <div class="kpi-val">$<?= number_format($daily_revenue ?? 0, 2) ?></div>
        <div class="kpi-growth up"><i data-lucide="clock" width="12"></i> Today's ledger config</div>
    </div>
    <div class="kpi-card kc-cyan">
        <div class="kpi-label">Total Trips</div>
        <div class="kpi-val"><?= number_format($total_trips) ?></div>
        <div class="kpi-growth <?= $growth_trips['direction'] ?>">
            <i data-lucide="<?= $growth_trips['direction']==='up'?'trending-up':'trending-down' ?>" width="12"></i>
            <?= $growth_trips['percent'] ?>%
        </div>
    </div>
    <div class="kpi-card kc-purple">
        <div class="kpi-label">Avg Fare</div>
        <div class="kpi-val">$<?= number_format($avg_fare, 2) ?></div>
        <div class="kpi-growth flat"><?= number_format($total_miles,1) ?> mi total</div>
    </div>
</div>

<!-- ROW: Revenue Trend + Payment Split + Trip Status -->
<div class="fin-row-3">
    <!-- Revenue Trend Chart -->
    <div class="fin-panel">
        <div class="fp-head"><div class="fp-title"><i data-lucide="activity" width="16"></i> Revenue Trend (12 Months)</div></div>
        <div class="chart-bars" id="trend-chart">
            <?php $maxRev = max(array_column($monthly_trend, 'revenue') ?: [1]); ?>
            <?php foreach($monthly_trend as $m): ?>
            <div class="chart-bar-wrap">
                <div class="chart-bar" style="height:<?= $maxRev > 0 ? max(4, ($m['revenue']/$maxRev)*160) : 4 ?>px;background:linear-gradient(180deg,#6366f1,#818cf8)">
                    <span class="cb-tip">$<?= number_format($m['revenue'],0) ?></span>
                </div>
                <div class="chart-bar-label"><?= substr($m['month_label'],0,3) ?></div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($monthly_trend)): ?>
                <div style="flex:1;text-align:center;color:var(--text-tertiary);font-size:.85rem;align-self:center">No trend data</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Payment Method Donut -->
    <div class="fin-panel">
        <div class="fp-head"><div class="fp-title"><i data-lucide="pie-chart" width="16"></i> Payment Split</div></div>
        <div class="donut-wrap">
            <?php
                $payTotal = $card_revenue + $cash_revenue + $wallet_revenue;
                $cardPct  = $payTotal > 0 ? round(($card_revenue / $payTotal) * 100) : 0;
                $cashPct  = $payTotal > 0 ? round(($cash_revenue / $payTotal) * 100) : 0;
                $walPct   = $payTotal > 0 ? 100 - $cardPct - $cashPct : 0;
            ?>
            <svg width="120" height="120" viewBox="0 0 36 36">
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--border-color)" stroke-width="3"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#6366f1" stroke-width="3" stroke-dasharray="<?= $cardPct ?> <?= 100-$cardPct ?>" stroke-dashoffset="25"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#10b981" stroke-width="3" stroke-dasharray="<?= $cashPct ?> <?= 100-$cashPct ?>" stroke-dashoffset="<?= 25-$cardPct ?>"/>
                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f59e0b" stroke-width="3" stroke-dasharray="<?= $walPct ?> <?= 100-$walPct ?>" stroke-dashoffset="<?= 25-$cardPct-$cashPct ?>"/>
            </svg>
            <div class="donut-legend">
                <div class="dl-item"><span class="dl-dot" style="background:#6366f1"></span> Card $<?= number_format($card_revenue,0) ?> (<?= $cardPct ?>%)</div>
                <div class="dl-item"><span class="dl-dot" style="background:#10b981"></span> Cash $<?= number_format($cash_revenue,0) ?> (<?= $cashPct ?>%)</div>
                <div class="dl-item"><span class="dl-dot" style="background:#f59e0b"></span> Wallet $<?= number_format($wallet_revenue,0) ?> (<?= $walPct ?>%)</div>
            </div>
        </div>
    </div>

    <!-- Trip Status -->
    <div class="fin-panel">
        <div class="fp-head"><div class="fp-title"><i data-lucide="check-circle" width="16"></i> Trip Status</div></div>
        <div class="ms-grid">
            <div class="ms-item"><div class="ml">Completed</div><div class="mv" style="color:var(--success)"><?= $completed_trips ?></div></div>
            <div class="ms-item"><div class="ml">Active</div><div class="mv" style="color:var(--info)"><?= $active_trips ?></div></div>
            <div class="ms-item"><div class="ml">Cancelled</div><div class="mv" style="color:var(--danger)"><?= $cancelled_trips ?></div></div>
            <div class="ms-item"><div class="ml">Completion %</div><div class="mv"><?= $total_trips > 0 ? round(($completed_trips/$total_trips)*100) : 0 ?>%</div></div>
        </div>
    </div>
</div>

<!-- ROW: Earnings Split + Wallet -->
<div class="fin-row">
    <div class="fin-panel">
        <div class="fp-head"><div class="fp-title"><i data-lucide="split" width="16"></i> Earnings Breakdown</div></div>
        <div class="ms-grid">
            <div class="ms-item"><div class="ml">Commission to Company</div><div class="mv" style="color:var(--primary)">$<?= number_format($total_commission, 2) ?></div></div>
            <div class="ms-item"><div class="ml">Surcharges Earned</div><div class="mv">$<?= number_format($total_surcharges, 2) ?></div></div>
            <div class="ms-item"><div class="ml">Driver Net Pay</div><div class="mv" style="color:var(--warning)">$<?= number_format($driver_payouts, 2) ?></div></div>
            <div class="ms-item"><div class="ml">Company Net</div><div class="mv" style="color:var(--success)">$<?= number_format($company_earnings, 2) ?></div></div>
        </div>
    </div>
    <div class="fin-panel" style="grid-column: span 1">
        <div class="fp-head"><div class="fp-title"><i data-lucide="shield-check" width="16"></i> Ledger Sanity & Reconciliation Audit</div></div>
        <div style="overflow-x:auto">
        <table class="ft-table" style="font-size: 0.75rem;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Cust Debits</th>
                    <th>Driver + Co Credits</th>
                    <th>Discrepancy Check</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($sanity_checks)): ?>
                    <?php foreach($sanity_checks as $sc): ?>
                    <tr style="<?= floatval($sc['Discrepancy']) != 0 ? 'background:rgba(239,68,68,0.1)' : '' ?>">
                        <td><?= date('M d', strtotime($sc['operation_date'])) ?></td>
                        <td>$<?= number_format($sc['Total_Customer_Debits'], 2) ?></td>
                        <td>$<?= number_format($sc['Driver_Credits'] + $sc['Company_Revenue'], 2) ?></td>
                        <td style="font-weight:700;color:<?= floatval($sc['Discrepancy']) == 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                            <?= floatval($sc['Discrepancy']) == 0 ? '✅ 0.00' : '❌ $'.number_format($sc['Discrepancy'], 2) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center;color:var(--text-tertiary);padding:1.5rem;">No ledger transactions yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- DRIVER PAYOUTS TABLE -->
<div class="fin-panel">
    <div class="fp-head">
        <div class="fp-title"><i data-lucide="users" width="16"></i> Driver Earnings Summary</div>
        <span style="font-size:.75rem;color:var(--text-tertiary)">Top 20 drivers</span>
    </div>
    <?php if(!empty($driver_payouts_list)): ?>
    <div style="overflow-x:auto">
    <table class="ft-table">
        <thead><tr><th>Driver</th><th>Trips</th><th>Total Fares</th><th>Earnings</th><th>Commission</th><th>Rate</th><th>Balance</th></tr></thead>
        <tbody>
        <?php foreach($driver_payouts_list as $dp): ?>
        <tr>
            <td style="font-weight:700"><?= esc($dp['first_name'].' '.$dp['last_name']) ?></td>
            <td><?= $dp['trip_count'] ?></td>
            <td>$<?= number_format($dp['total_fares'],2) ?></td>
            <td style="color:var(--success);font-weight:700">$<?= number_format($dp['total_earnings'],2) ?></td>
            <td style="color:var(--primary)">$<?= number_format($dp['total_commission'],2) ?></td>
            <td><?= $dp['commission_rate'] ?? 25 ?>%</td>
            <td style="font-weight:600">$<?= number_format($dp['wallet_balance'] ?? 0, 2) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div style="padding:2rem;text-align:center;color:var(--text-tertiary)">No driver data for this period.</div>
    <?php endif; ?>
</div>

<!-- DETAILED TRANSACTIONS TABLE -->
<div class="fin-panel">
    <div class="fp-head">
        <div class="fp-title"><i data-lucide="receipt" width="16"></i> Trip Financial Details</div>
        <div class="fin-actions no-print">
            <input type="text" id="trip-search" placeholder="Search trips..." class="form-control" style="width:220px;font-size:.8rem;padding:6px 12px">
        </div>
    </div>
    <?php if(!empty($recent_trips)): ?>
    <div style="overflow-x:auto">
    <table class="ft-table" id="trips-table">
        <thead><tr>
            <th><input type="checkbox" class="ft-check" id="check-all"></th>
            <th>Trip #</th><th>Customer</th><th>Driver</th><th>Fare</th><th>Driver Pay</th><th>Commission</th><th>Surcharge</th><th>Method</th><th>Status</th><th>Date</th><th class="no-print">Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach($recent_trips as $rt): ?>
        <tr data-id="<?= $rt['id'] ?>">
            <td><input type="checkbox" class="ft-check row-check" value="<?= $rt['id'] ?>"></td>
            <td style="font-family:monospace;font-weight:700;color:var(--primary)"><?= esc($rt['trip_number']) ?></td>
            <td><?= esc($rt['customer_name'] ?? '—') ?></td>
            <td><?= esc($rt['driver_name'] ?? 'Unassigned') ?></td>
            <td style="font-weight:700">$<?= number_format($rt['fare_amount'],2) ?></td>
            <td style="color:var(--success)">$<?= number_format($rt['driver_earnings'] ?? 0, 2) ?></td>
            <td style="color:var(--primary)">$<?= number_format($rt['commission_amount'] ?? 0, 2) ?></td>
            <td>$<?= number_format($rt['surcharge_amount'] ?? 0, 2) ?></td>
            <td><span style="text-transform:capitalize"><?= esc($rt['payment_method'] ?? 'N/A') ?></span></td>
            <td>
                <?php
                    $sc = 'sp-active';
                    if($rt['status']==='completed') $sc='sp-completed';
                    elseif($rt['status']==='cancelled') $sc='sp-cancelled';
                    elseif($rt['status']==='dispatching') $sc='sp-dispatching';
                ?>
                <span class="status-pill <?= $sc ?>"><?= ucfirst($rt['status']) ?></span>
            </td>
            <td style="font-size:.78rem;color:var(--text-secondary)"><?= date('M d, Y H:i', strtotime($rt['created_at'])) ?></td>
            <td class="no-print">
                <button class="btn btn-sm" onclick="printSingle(<?= $rt['id'] ?>)" title="Print"><i data-lucide="printer" width="14"></i></button>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div style="padding:2.5rem;text-align:center;color:var(--text-tertiary)"><i data-lucide="inbox" width="32" style="opacity:.3;display:block;margin:0 auto .5rem"></i>No trips found for this period.</div>
    <?php endif; ?>
</div>

</div><!-- /fin-wrap -->

<!-- Print Modal (hidden, used for single/bulk print) -->
<div class="modal fade" id="printModal" tabindex="-1"><div class="modal-dialog modal-lg"><div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:14px">
    <div class="modal-header" style="border-bottom:1px solid var(--border-color);padding:1rem 1.5rem">
        <h5 class="modal-title" style="font-weight:700">Print Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body" id="print-content" style="padding:1.5rem"></div>
    <div class="modal-footer" style="border-top:1px solid var(--border-color);padding:1rem 1.5rem">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="doPrint()" style="gap:6px"><i data-lucide="printer" width="14"></i> Print</button>
    </div>
</div></div></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Check all
    const checkAll = document.getElementById('check-all');
    if (checkAll) {
        checkAll.addEventListener('change', () => {
            document.querySelectorAll('.row-check').forEach(c => c.checked = checkAll.checked);
            updateSelCount();
        });
    }
    document.querySelectorAll('.row-check').forEach(c => c.addEventListener('change', updateSelCount));

    function updateSelCount() {
        const count = document.querySelectorAll('.row-check:checked').length;
        document.getElementById('sel-count').textContent = count;
        document.getElementById('btn-bulk-print').disabled = count === 0;
    }

    // Search
    const searchInput = document.getElementById('trip-search');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase();
            document.querySelectorAll('#trips-table tbody tr').forEach(r => {
                r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        });
    }

    // Bulk print
    document.getElementById('btn-bulk-print')?.addEventListener('click', () => {
        const ids = [...document.querySelectorAll('.row-check:checked')].map(c => c.value);
        if (!ids.length) return;
        fetch('<?= base_url("finance/bulk-print") ?>', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ids})
        })
        .then(r => r.json())
        .then(data => {
            if (data.trips) renderPrintPreview(data.trips);
        });
    });
});

function printSingle(id) {
    fetch('<?= base_url("finance/print-trip/") ?>' + id)
    .then(r => r.json())
    .then(data => {
        if (data.trip) renderPrintPreview([data.trip]);
    });
}

function renderPrintPreview(trips) {
    let html = '<div style="font-family:Inter,sans-serif">';
    html += '<div style="text-align:center;margin-bottom:1.5rem"><h2 style="margin:0;font-size:1.3rem">RideFlow — Financial Report</h2><p style="color:var(--text-secondary);font-size:.85rem">Generated: ' + new Date().toLocaleString() + '</p></div>';
    trips.forEach(t => {
        html += `<div style="border:1px solid var(--border-color);border-radius:10px;padding:1rem;margin-bottom:1rem">
            <div style="display:flex;justify-content:space-between;margin-bottom:.75rem">
                <div><strong style="color:var(--primary)">${t.trip_number || '—'}</strong></div>
                <div style="font-size:.8rem;color:var(--text-secondary)">${t.created_at || ''}</div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;font-size:.82rem">
                <div><span style="color:var(--text-tertiary)">Customer:</span> ${t.customer_name||'—'}</div>
                <div><span style="color:var(--text-tertiary)">Driver:</span> ${t.driver_name||'—'}</div>
                <div><span style="color:var(--text-tertiary)">Vehicle:</span> ${t.vehicle_type||'—'}</div>
                <div><span style="color:var(--text-tertiary)">Fare:</span> <strong>$${parseFloat(t.fare_amount||0).toFixed(2)}</strong></div>
                <div><span style="color:var(--text-tertiary)">Driver Pay:</span> $${parseFloat(t.driver_earnings||0).toFixed(2)}</div>
                <div><span style="color:var(--text-tertiary)">Commission:</span> $${parseFloat(t.commission_amount||0).toFixed(2)}</div>
                <div><span style="color:var(--text-tertiary)">Payment:</span> ${t.payment_method||'—'}</div>
                <div><span style="color:var(--text-tertiary)">Status:</span> ${t.status||'—'}</div>
                <div><span style="color:var(--text-tertiary)">Distance:</span> ${t.distance_miles||0} mi</div>
            </div>
        </div>`;
    });
    html += '</div>';
    document.getElementById('print-content').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('printModal'));
    modal.show();
    lucide.createIcons();
}

function doPrint() {
    const content = document.getElementById('print-content').innerHTML;
    const win = window.open('','_blank');
    win.document.write(`<html><head><title>RideFlow Financial Report</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>body{font-family:Inter,sans-serif;padding:2rem;color:#1e293b}
        strong{font-weight:700}h2{color:#6366f1}</style>
    </head><body>${content}</body></html>`);
    win.document.close();
    win.focus();
    setTimeout(() => { win.print(); win.close(); }, 500);
}
</script>

<?= $this->endSection() ?>
