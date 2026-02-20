<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Statement – <?= esc($driver->first_name . ' ' . $driver->last_name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            color: #111;
            padding: 2rem;
        }

        /* ── Toolbar (hidden on print) ───────────────────────── */
        .toolbar {
            max-width: 820px; margin: 0 auto 1.5rem;
            display: flex; justify-content: space-between; align-items: center;
        }
        .toolbar h2 { font-size: 1rem; color: #374151; }
        .toolbar-actions { display: flex; gap: 0.75rem; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0.55rem 1.2rem; border-radius: 6px;
            font-size: 0.875rem; cursor: pointer;
            border: none; text-decoration: none; font-family: inherit;
        }
        .btn-primary { background: #1d4ed8; color: #fff; }
        .btn-outline { background: #fff; color: #374151; border: 1px solid #d1d5db; }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .statement { box-shadow: none !important; }
            @page { margin: 15mm 15mm 20mm; }
        }

        /* ── Statement Page ──────────────────────────────────── */
        .statement {
            max-width: 820px; margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            overflow: hidden;
        }

        /* Header band */
        .stmt-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff;
            padding: 28px 36px 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .stmt-co-name { font-size: 1.25rem; font-weight: 700; letter-spacing: .4px; margin-bottom: 4px; }
        .stmt-co-sub  { font-size: 0.78rem; opacity: .8; line-height: 1.6; }
        .stmt-title-block { text-align: right; }
        .stmt-title   { font-size: 1.1rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .stmt-period  { font-size: 0.75rem; opacity: .8; margin-top: 4px; }

        /* Driver summary bar */
        .stmt-driver {
            background: #f8faff;
            border-bottom: 1px solid #e5e7eb;
            padding: 16px 36px;
            display: flex; gap: 40px; flex-wrap: wrap;
        }
        .stmt-driver-item { font-size: 0.8rem; }
        .stmt-driver-label { color: #6b7280; display: block; margin-bottom: 2px; }
        .stmt-driver-val   { font-weight: 600; color: #111; }

        /* Summary boxes */
        .stmt-summary {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .stmt-sum-box {
            padding: 16px 20px;
            border-right: 1px solid #e5e7eb;
            text-align: center;
        }
        .stmt-sum-box:last-child { border-right: none; }
        .stmt-sum-label { font-size: 0.72rem; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        .stmt-sum-val   { font-size: 1.15rem; font-weight: 700; }
        .c-green { color: #059669; }
        .c-red   { color: #dc2626; }
        .c-blue  { color: #1d4ed8; }
        .c-gray  { color: #374151; }

        /* Table */
        .stmt-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; }
        .stmt-table thead tr {
            background: #f9fafb;
            border-bottom: 2px solid #e5e7eb;
        }
        .stmt-table th {
            padding: 10px 16px; text-align: left;
            color: #6b7280; font-size: 0.75rem; text-transform: uppercase; letter-spacing: .5px;
        }
        .stmt-table th:last-child { text-align: right; }
        .stmt-table tbody tr { border-bottom: 1px solid #f3f4f6; }
        .stmt-table tbody tr:hover { background: #fafafa; }
        .stmt-table td { padding: 10px 16px; vertical-align: middle; }
        .stmt-table td:last-child { text-align: right; font-weight: 600; }

        .type-badge {
            display: inline-block; padding: 2px 9px; border-radius: 12px;
            font-size: 0.72rem; font-weight: 600; text-transform: capitalize;
        }
        .type-deposit    { background: #d1fae5; color: #065f46; }
        .type-refund     { background: #dbeafe; color: #1e40af; }
        .type-withdrawal { background: #fee2e2; color: #991b1b; }
        .type-commission { background: #fef3c7; color: #92400e; }
        .type-payment    { background: #fce7f3; color: #9d174d; }

        .cheque-link {
            font-size: 0.75rem; color: #2563eb; text-decoration: none;
            display: inline-flex; align-items: center; gap: 3px;
        }
        .cheque-link:hover { text-decoration: underline; }

        /* Running balance column */
        .running-bal { font-family: 'Courier New', monospace; font-size: 0.82rem; }

        /* Footer */
        .stmt-footer {
            padding: 16px 36px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.75rem; color: #6b7280;
        }
        .stmt-closing {
            text-align: right;
        }
        .stmt-closing-label { font-size: 0.72rem; color: #6b7280; }
        .stmt-closing-val   { font-size: 1.1rem; font-weight: 700; }
    </style>
</head>
<body>

<?php
/* ── load settings ─────────────────────────────────────────────────────── */
$sf = WRITEPATH . 'settings.json';
$settings = file_exists($sf) ? (json_decode(file_get_contents($sf), true) ?? []) : [];
$companyName  = $settings['company_name']  ?? 'RideApp Inc.';
$companyPhone = $settings['company_phone'] ?? '';
$companyAddr  = trim(implode(', ', array_filter([
    $settings['company_address'] ?? '',
    $settings['company_city']    ?? '',
    $settings['company_state']   ?? '',
])));

/* ── compute summary totals ────────────────────────────────────────────── */
$totalCredits  = 0.0;
$totalDebits   = 0.0;
$runningBal    = 0.0;
$txCount       = count($transactions);

// Sort by id ASC for running balance
$txSorted = array_reverse($transactions); // transactions come DESC from DB

foreach ($txSorted as $row) {
    if (in_array($row['type'], ['deposit','refund'])) {
        $totalCredits += (float)$row['amount'];
    } else {
        $totalDebits  += (float)$row['amount'];
    }
}

$closingBalance = $walletBalance; // passed from controller

/* Compute per-row running balance (ascending order) */
$runningBalArr = [];
$rb = 0.0;
foreach ($txSorted as $row) {
    if (in_array($row['type'], ['deposit','refund'])) {
        $rb += (float)$row['amount'];
    } else {
        $rb -= (float)$row['amount'];
    }
    $runningBalArr[$row['id']] = $rb;
}

$periodStart = !empty($txSorted)  ? date('M j, Y', strtotime($txSorted[0]['created_at']))  : '—';
$periodEnd   = !empty($transactions) ? date('M j, Y', strtotime($transactions[0]['created_at'])) : '—';
$printDate   = date('F j, Y');
?>

<!-- Toolbar -->
<div class="toolbar">
    <h2>Wallet Statement — <?= esc($driver->first_name . ' ' . $driver->last_name) ?></h2>
    <div class="toolbar-actions">
        <a href="<?= base_url('drivers/profile/' . $driver->id) ?>" class="btn btn-outline">← Back</a>
        <a href="<?= base_url('drivers/export_statement/' . $driver->id) ?>" class="btn btn-outline">⬇ Export CSV</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Print Statement</button>
    </div>
</div>

<!-- Statement -->
<div class="statement">

    <!-- Header -->
    <div class="stmt-header">
        <div>
            <div class="stmt-co-name"><?= esc($companyName) ?></div>
            <div class="stmt-co-sub">
                <?= esc($companyAddr) ?><?= $companyPhone ? ' · ' . esc($companyPhone) : '' ?>
            </div>
        </div>
        <div class="stmt-title-block">
            <div class="stmt-title">Wallet Statement</div>
            <div class="stmt-period">Period: <?= $periodStart ?> – <?= $periodEnd ?></div>
            <div class="stmt-period">Printed: <?= $printDate ?></div>
        </div>
    </div>

    <!-- Driver bar -->
    <div class="stmt-driver">
        <div class="stmt-driver-item">
            <span class="stmt-driver-label">Driver</span>
            <span class="stmt-driver-val"><?= esc($driver->first_name . ' ' . $driver->last_name) ?></span>
        </div>
        <div class="stmt-driver-item">
            <span class="stmt-driver-label">Phone</span>
            <span class="stmt-driver-val"><?= esc($driver->phone) ?></span>
        </div>
        <div class="stmt-driver-item">
            <span class="stmt-driver-label">Email</span>
            <span class="stmt-driver-val"><?= esc($driver->email) ?></span>
        </div>
        <div class="stmt-driver-item">
            <span class="stmt-driver-label">Driver ID</span>
            <span class="stmt-driver-val">#<?= $driver->id ?></span>
        </div>
        <div class="stmt-driver-item">
            <span class="stmt-driver-label">Commission Rate</span>
            <span class="stmt-driver-val"><?= number_format($driver->commission_rate ?? 25, 1) ?>%</span>
        </div>
    </div>

    <!-- Summary -->
    <div class="stmt-summary">
        <div class="stmt-sum-box">
            <div class="stmt-sum-label">Total Transactions</div>
            <div class="stmt-sum-val c-gray"><?= $txCount ?></div>
        </div>
        <div class="stmt-sum-box">
            <div class="stmt-sum-label">Total Credits</div>
            <div class="stmt-sum-val c-green">+$<?= number_format($totalCredits, 2) ?></div>
        </div>
        <div class="stmt-sum-box">
            <div class="stmt-sum-label">Total Debits</div>
            <div class="stmt-sum-val c-red">-$<?= number_format($totalDebits, 2) ?></div>
        </div>
        <div class="stmt-sum-box">
            <div class="stmt-sum-label">Closing Balance</div>
            <div class="stmt-sum-val <?= $closingBalance >= 0 ? 'c-green' : 'c-red' ?>">
                <?= $closingBalance < 0 ? '-' : '' ?>$<?= number_format(abs($closingBalance), 2) ?>
            </div>
        </div>
    </div>

    <!-- Table -->
    <?php if (empty($transactions)): ?>
        <div style="text-align:center; padding:3rem; color:#6b7280;">No transactions found.</div>
    <?php else: ?>
    <table class="stmt-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Type</th>
                <th>Description / Reference</th>
                <th style="text-align:right;">Amount</th>
                <th style="text-align:right;">Running Balance</th>
                <th style="text-align:center;">Cheque</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rowNum = 0;
            foreach ($txSorted as $row):
                $rowNum++;
                $isCredit = in_array($row['type'], ['deposit','refund']);
                $rb = $runningBalArr[$row['id']] ?? 0;
            ?>
            <tr>
                <td style="color:#9ca3af;"><?= $rowNum ?></td>
                <td style="color:#374151; white-space:nowrap;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                <td><span class="type-badge type-<?= $row['type'] ?>"><?= ucfirst($row['type']) ?></span></td>
                <td>
                    <div><?= esc($row['description'] ?? '—') ?></div>
                    <div style="font-size:0.72rem; color:#9ca3af;">TXN-<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?></div>
                </td>
                <td style="font-weight:600; color:<?= $isCredit ? '#059669' : '#dc2626' ?>; text-align:right;">
                    <?= $isCredit ? '+' : '-' ?>$<?= number_format($row['amount'], 2) ?>
                </td>
                <td class="running-bal" style="color:<?= $rb >= 0 ? '#059669' : '#dc2626' ?>; text-align:right;">
                    <?= $rb < 0 ? '-' : '' ?>$<?= number_format(abs($rb), 2) ?>
                </td>
                <td style="text-align:center;">
                    <?php if ($row['type'] === 'withdrawal'): ?>
                        <a href="<?= base_url('drivers/cheque/' . $row['id']) ?>" target="_blank" class="cheque-link">
                            🖨 View
                        </a>
                    <?php else: ?>
                        <span style="color:#d1d5db;">—</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Footer -->
    <div class="stmt-footer">
        <div>
            This statement is generated by <?= esc($companyName) ?> on <?= $printDate ?>.<br>
            For queries, contact <?= esc($companyPhone ?: $companyName) ?>.
        </div>
        <div class="stmt-closing">
            <div class="stmt-closing-label">Closing Balance</div>
            <div class="stmt-closing-val <?= $closingBalance >= 0 ? 'c-green' : 'c-red' ?>">
                <?= $closingBalance < 0 ? '-' : '' ?>$<?= number_format(abs($closingBalance), 2) ?>
            </div>
        </div>
    </div>

</div>

</body>
</html>
