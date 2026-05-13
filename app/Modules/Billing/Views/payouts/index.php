<?= $this->extend('layouts/master') ?>
<?= $this->section('content') ?>

<style>
.po-wrap{padding:1.5rem 2rem}
.po-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem}
.po-header h1{font-size:1.5rem;font-weight:800;margin:0}

.po-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem}
.po-stat{background:var(--bg-surface);border:1px solid var(--border-color);border-radius:14px;padding:1.2rem;position:relative;overflow:hidden;transition:var(--transition)}
.po-stat:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(0,0,0,.15)}
.po-stat::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
.po-stat:nth-child(1)::before{background:linear-gradient(90deg,#f59e0b,#fbbf24)}
.po-stat:nth-child(2)::before{background:linear-gradient(90deg,#10b981,#34d399)}
.po-stat:nth-child(3)::before{background:linear-gradient(90deg,#6366f1,#818cf8)}
.po-stat:nth-child(4)::before{background:linear-gradient(90deg,#ef4444,#f87171)}
.po-stat .pl{font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--text-tertiary);margin-bottom:6px}
.po-stat .pv{font-size:1.5rem;font-weight:800;color:var(--text-primary);line-height:1.2}

.po-panel{background:var(--bg-surface);border:1px solid var(--border-color);border-radius:14px;overflow:hidden;margin-bottom:1.5rem}
.pp-head{padding:1rem 1.25rem;border-bottom:1px solid var(--border-color);display:flex;justify-content:space-between;align-items:center;background:var(--bg-body)}
.pp-title{font-weight:700;font-size:.95rem;display:flex;align-items:center;gap:8px}

.po-table{width:100%;border-collapse:collapse}
.po-table th{text-align:left;padding:.85rem 1rem;border-bottom:1px solid var(--border-color);font-size:.7rem;text-transform:uppercase;color:var(--text-tertiary);font-weight:700;letter-spacing:.04em;background:var(--bg-body)}
.po-table td{padding:.75rem 1rem;border-bottom:1px solid var(--border-color);font-size:.82rem;vertical-align:middle}
.po-table tr:last-child td{border-bottom:none}
.po-table tr:hover td{background:var(--bg-surface-hover)}

.po-badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:6px;font-size:.72rem;font-weight:700}
.po-pending{background:rgba(245,158,11,.12);color:#f59e0b}
.po-completed{background:rgba(16,185,129,.12);color:#10b981}

@media(max-width:768px){.po-stats{grid-template-columns:repeat(2,1fr)}}
</style>

<div class="po-wrap">

<!-- HEADER -->
<div class="po-header">
    <div>
        <h1><i data-lucide="banknote" width="24" style="vertical-align:middle;margin-right:6px;color:var(--primary)"></i> Driver Payouts</h1>
        <div style="font-size:.85rem;color:var(--text-secondary);margin-top:2px">Manage driver withdrawal requests and cash-outs</div>
    </div>
    <div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newPayoutModal" style="gap:6px">
            <i data-lucide="plus" width="14"></i> New Payout
        </button>
        <a href="<?= base_url('finance') ?>" class="btn btn-secondary btn-sm" style="gap:6px">
            <i data-lucide="arrow-left" width="14"></i> Finance Dashboard
        </a>
    </div>
</div>

<!-- STATS -->
<div class="po-stats">
    <div class="po-stat">
        <div class="pl">Pending Payouts</div>
        <div class="pv"><?= (int)($stats->pending_count ?? 0) ?></div>
    </div>
    <div class="po-stat">
        <div class="pl">Pending Amount</div>
        <div class="pv">$<?= number_format($stats->pending_amount ?? 0, 2) ?></div>
    </div>
    <div class="po-stat">
        <div class="pl">Total Payouts</div>
        <div class="pv"><?= (int)($stats->total_count ?? 0) ?></div>
    </div>
    <div class="po-stat">
        <div class="pl">Total Paid Out</div>
        <div class="pv">$<?= number_format($stats->total_amount ?? 0, 2) ?></div>
    </div>
</div>

<!-- PAYOUTS TABLE -->
<div class="po-panel">
    <div class="pp-head">
        <div class="pp-title"><i data-lucide="list" width="16"></i> Payout History</div>
    </div>
    <?php if(!empty($payouts)): ?>
    <div style="overflow-x:auto">
    <table class="po-table">
        <thead><tr>
            <th>Date</th>
            <th>Driver</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Bank</th>
            <th>Status</th>
            <th>Description</th>
            <th>Actions</th>
        </tr></thead>
        <tbody>
        <?php foreach($payouts as $p): ?>
        <tr>
            <td style="font-size:.78rem;color:var(--text-secondary)"><?= date('M d, Y H:i', strtotime($p['created_at'])) ?></td>
            <td style="font-weight:700"><?= esc($p['driver_name'] ?? 'Unknown') ?></td>
            <td style="font-weight:700;color:var(--danger)">$<?= number_format($p['amount'], 2) ?></td>
            <td style="text-transform:capitalize"><?= esc($p['payment_method'] ?? 'cash') ?></td>
            <td style="font-size:.78rem"><?= esc($p['bank_name'] ?? '—') ?> <?= !empty($p['account_number']) ? '•••'.substr($p['account_number'],-4) : '' ?></td>
            <td>
                <?php if(strpos($p['description'], '[PENDING]') !== false): ?>
                    <span class="po-badge po-pending"><i data-lucide="clock" width="10"></i> Pending</span>
                <?php else: ?>
                    <span class="po-badge po-completed"><i data-lucide="check" width="10"></i> Completed</span>
                <?php endif; ?>
            </td>
            <td style="font-size:.78rem;color:var(--text-secondary);max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($p['description']) ?>"><?= esc($p['description']) ?></td>
            <td>
                <?php if(strpos($p['description'], '[PENDING]') !== false): ?>
                    <form action="<?= base_url('finance/payouts/complete/'.$p['id']) ?>" method="POST" style="display:inline">
                        <button class="btn btn-sm btn-success" title="Mark Completed" onclick="return confirm('Mark this payout as completed?')"><i data-lucide="check-circle" width="14"></i></button>
                    </form>
                    <form action="<?= base_url('finance/payouts/cancel/'.$p['id']) ?>" method="POST" style="display:inline">
                        <button class="btn btn-sm btn-danger" title="Cancel Payout" onclick="return confirm('Cancel this payout and restore balance?')"><i data-lucide="x-circle" width="14"></i></button>
                    </form>
                <?php else: ?>
                    <span style="color:var(--text-tertiary);font-size:.75rem">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php else: ?>
        <div style="padding:3rem;text-align:center;color:var(--text-tertiary)">
            <i data-lucide="inbox" width="32" style="opacity:.3;display:block;margin:0 auto .5rem"></i>
            No payout records yet.
        </div>
    <?php endif; ?>
</div>

</div><!-- /po-wrap -->

<!-- NEW PAYOUT MODAL -->
<div class="modal fade" id="newPayoutModal" tabindex="-1">
<div class="modal-dialog"><div class="modal-content" style="background:var(--bg-surface);border:1px solid var(--border-color);border-radius:14px">
    <div class="modal-header" style="border-bottom:1px solid var(--border-color);padding:1rem 1.5rem">
        <h5 class="modal-title" style="font-weight:700"><i data-lucide="banknote" width="18" style="margin-right:6px;color:var(--primary)"></i> New Driver Payout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <form action="<?= base_url('finance/payouts/request') ?>" method="POST">
    <div class="modal-body" style="padding:1.5rem;display:flex;flex-direction:column;gap:1rem">
        <div>
            <label class="form-label" style="font-size:.8rem;font-weight:600">Driver</label>
            <select name="driver_id" class="form-select" required>
                <option value="">Select driver...</option>
                <?php
                    $driverModel = new \Modules\Fleet\Models\DriverModel();
                    $drivers = $driverModel->where('deleted_at', null)->orderBy('first_name', 'ASC')->findAll();
                    foreach($drivers as $d):
                ?>
                <option value="<?= $d->id ?>"><?= esc($d->first_name . ' ' . $d->last_name) ?> — Balance: $<?= number_format($d->wallet_balance ?? 0, 2) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:.8rem;font-weight:600">Amount ($)</label>
            <input type="number" name="amount" class="form-control" step="0.01" min="0.01" required placeholder="0.00">
        </div>
        <div>
            <label class="form-label" style="font-size:.8rem;font-weight:600">Payment Method</label>
            <select name="payment_method" class="form-select">
                <option value="cash">Cash</option>
                <option value="cheque">Cheque</option>
                <option value="bank_transfer">Bank Transfer</option>
                <option value="mobile_money">Mobile Money</option>
            </select>
        </div>
        <div>
            <label class="form-label" style="font-size:.8rem;font-weight:600">Notes (optional)</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Reason / reference..."></textarea>
        </div>
    </div>
    <div class="modal-footer" style="border-top:1px solid var(--border-color);padding:1rem 1.5rem">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary" style="gap:6px"><i data-lucide="send" width="14"></i> Process Payout</button>
    </div>
    </form>
</div></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => { lucide.createIcons(); });
</script>

<?= $this->endSection() ?>
