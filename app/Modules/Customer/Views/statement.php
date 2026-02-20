<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Statement – <?= esc($customer->first_name . ' ' . $customer->last_name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            color: #111;
            padding: 2rem;
        }

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
        .btn-outline  { background: #fff; color: #374151; border: 1px solid #d1d5db; }

        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none !important; }
            .statement { box-shadow: none !important; }
            @page { margin: 15mm 15mm 20mm; }
        }

        .statement {
            max-width: 820px; margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Header band */
        .stmt-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff; padding: 28px 36px 20px;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .stmt-co-name { font-size: 1.25rem; font-weight: 700; letter-spacing: .4px; margin-bottom: 4px; }
        .stmt-co-sub  { font-size: 0.78rem; opacity: .8; line-height: 1.6; }
        .stmt-title-block { text-align: right; }
        .stmt-title   { font-size: 1.1rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .stmt-period  { font-size: 0.75rem; opacity: .8; margin-top: 4px; }

        /* Customer bar */
        .stmt-customer {
            background: #f8faff; border-bottom: 1px solid #e5e7eb;
            padding: 16px 36px; display: flex; gap: 40px; flex-wrap: wrap;
        }
        .stmt-ci      { font-size: .8rem; }
        .stmt-ci-label{ color: #6b7280; display: block; margin-bottom: 2px; }
        .stmt-ci-val  { font-weight: 600; color: #111; }

        /* Summary boxes */
        .stmt-summary {
            display: grid; grid-template-columns: repeat(4, 1fr);
            border-bottom: 1px solid #e5e7eb;
        }
        .stmt-sum-box {
            padding: 16px 20px; text-align: center;
            border-right: 1px solid #e5e7eb;
        }
        .stmt-sum-box:last-child { border-right: none; }
        .stmt-sum-label { font-size: .72rem; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; }
        .stmt-sum-val   { font-size: 1.15rem; font-weight: 700; }
        .c-green { color: #059669; }
        .c-red   { color: #dc2626; }
        .c-blue  { color: #1d4ed8; }
        .c-gray  { color: #374151; }

        /* Table */
        .stmt-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
        .stmt-table thead tr { background: #f9fafb; border-bottom: 2px solid #e5e7eb; }
        .stmt-table th {
            padding: 10px 16px; text-align: left;
            color: #6b7280; font-size: .75rem; text-transform: uppercase; letter-spacing: .5px;
        }
        .stmt-table th:last-child { text-align: right; }
        .stmt-table tbody tr { border-bottom: 1px solid #f3f4f6; }
        .stmt-table tbody tr:hover { background: #fafafa; }
        .stmt-table td { padding: 10px 16px; vertical-align: middle; }
        .stmt-table td:last-child { text-align: right; font-weight: 600; }

        .type-badge {
            display: inline-block; padding: 2px 9px; border-radius: 12px;
            font-size: .72rem; font-weight: 600; text-transform: capitalize;
        }
        .type-deposit    { background: #d1fae5; color: #065f46; }
        .type-refund     { background: #dbeafe; color: #1e40af; }
        .type-withdrawal { background: #fee2e2; color: #991b1b; }
        .type-payment    { background: #fce7f3; color: #9d174d; }

        .running-bal { font-family: 'Courier New', monospace; font-size: .82rem; }

        /* Footer */
        .stmt-footer {
            padding: 16px 36px; border-top: 1px solid #e5e7eb; background: #f9fafb;
            display: flex; justify-content: space-between; align-items: center;
            font-size: .75rem; color: #6b7280;
        }
        .stmt-closing-label { font-size: .72rem; color: #6b7280; }
        .stmt-closing-val   { font-size: 1.1rem; font-weight: 700; }
    </style>
</head>
<body>

<?php
/* ── settings ───────────────────────────────────── */
$sf = WRITEPATH . 'settings.json';
$settings = file_exists($sf) ? (json_decode(file_get_contents($sf), true) ?? []) : [];
$companyName  = $settings['company_name']  ?? 'RideApp Inc.';
$companyPhone = $settings['company_phone'] ?? '';
$companyAddr  = trim(implode(', ', array_filter([
    $settings['company_address'] ?? '',
    $settings['company_city']    ?? '',
    $settings['company_state']   ?? '',
])));

/* ── totals ─────────────────────────────────────── */
$txSorted      = array_reverse($transactions); // ascending
$totalCredits  = 0.0;
$totalDebits   = 0.0;

foreach ($txSorted as $row) {
    in_array($row['type'], ['deposit','refund'])
        ? $totalCredits += (float)$row['amount']
        : $totalDebits  += (float)$row['amount'];
}

/* Running balance per row */
$rb = 0.0;
$runningBalArr = [];
foreach ($txSorted as $row) {
    in_array($row['type'], ['deposit','refund'])
        ? $rb += (float)$row['amount']
        : $rb -= (float)$row['amount'];
    $runningBalArr[$row['id']] = $rb;
}

$periodStart = !empty($txSorted)     ? date('M j, Y', strtotime($txSorted[0]['created_at']))     : '—';
$periodEnd   = !empty($transactions) ? date('M j, Y', strtotime($transactions[0]['created_at'])) : '—';
$printDate   = date('F j, Y');
$closingBal  = $walletBalance;
?>

<!-- Toolbar -->
<div class="toolbar">
    <h2>Wallet Statement — <?= esc($customer->first_name . ' ' . $customer->last_name) ?></h2>
    <div class="toolbar-actions">
        <a href="<?= base_url('customers/profile/' . $customer->id) ?>" class="btn btn-outline">← Back</a>
        <a href="<?= base_url('customers/export_statement/' . $customer->id) ?>" class="btn btn-outline">⬇ Export CSV</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Print Statement</button>
    </div>
</div>

<div class="statement">

    <!-- Header -->
    <div class="stmt-header">
        <div>
            <div class="stmt-co-name"><?= esc($companyName) ?></div>
            <div class="stmt-co-sub"><?= esc($companyAddr) ?><?= $companyPhone ? ' · ' . esc($companyPhone) : '' ?></div>
        </div>
        <div class="stmt-title-block">
            <div class="stmt-title">Customer Wallet Statement</div>
            <div class="stmt-period">Period: <?= $periodStart ?> – <?= $periodEnd ?></div>
            <div class="stmt-period">Printed: <?= $printDate ?></div>
        </div>
    </div>

    <!-- Customer bar -->
    <div class="stmt-customer">
        <div class="stmt-ci">
            <span class="stmt-ci-label">Customer</span>
            <span class="stmt-ci-val"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></span>
        </div>
        <div class="stmt-ci">
            <span class="stmt-ci-label">Phone</span>
            <span class="stmt-ci-val"><?= esc($customer->phone) ?></span>
        </div>
        <div class="stmt-ci">
            <span class="stmt-ci-label">Email</span>
            <span class="stmt-ci-val"><?= esc($customer->email) ?></span>
        </div>
        <div class="stmt-ci">
            <span class="stmt-ci-label">Account ID</span>
            <span class="stmt-ci-val">#<?= $customer->id ?></span>
        </div>
        <div class="stmt-ci">
            <span class="stmt-ci-label">Status</span>
            <span class="stmt-ci-val"><?= ucfirst($customer->status) ?></span>
        </div>
    </div>

    <!-- Summary -->
    <div class="stmt-summary">
        <div class="stmt-sum-box">
            <div class="stmt-sum-label">Transactions</div>
            <div class="stmt-sum-val c-gray"><?= count($transactions) ?></div>
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
            <div class="stmt-sum-val <?= $closingBal >= 0 ? 'c-green' : 'c-red' ?>">
                <?= $closingBal < 0 ? '-' : '' ?>$<?= number_format(abs($closingBal), 2) ?>
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
                <th>Ref</th>
                <th>Type</th>
                <th>Description</th>
                <th style="text-align:right;">Amount</th>
                <th style="text-align:right;">Running Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php $rowNum = 0; foreach ($txSorted as $row):
                $rowNum++;
                $isCredit = in_array($row['type'], ['deposit','refund']);
                $rb       = $runningBalArr[$row['id']] ?? 0;
            ?>
            <tr>
                <td style="color:#9ca3af;"><?= $rowNum ?></td>
                <td style="white-space:nowrap;"><?= date('M j, Y', strtotime($row['created_at'])) ?></td>
                <td style="font-family:monospace; font-size:.75rem; color:#9ca3af;">TXN-<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?></td>
                <td><span class="type-badge type-<?= $row['type'] ?>"><?= ucfirst($row['type']) ?></span></td>
                <td><?= esc($row['description'] ?? '—') ?></td>
                <td style="color:<?= $isCredit ? '#059669' : '#dc2626' ?>; text-align:right;">
                    <?= $isCredit ? '+' : '-' ?>$<?= number_format($row['amount'], 2) ?>
                </td>
                <td class="running-bal" style="color:<?= $rb >= 0 ? '#059669' : '#dc2626' ?>; text-align:right;">
                    <?= $rb < 0 ? '-' : '' ?>$<?= number_format(abs($rb), 2) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <!-- Footer -->
    <div class="stmt-footer">
        <span>Generated by <?= esc($companyName) ?> on <?= $printDate ?><?= $companyPhone ? ' · ' . esc($companyPhone) : '' ?></span>
        <div style="text-align:right;">
            <div class="stmt-closing-label">Closing Balance</div>
            <div class="stmt-closing-val <?= $closingBal >= 0 ? 'c-green' : 'c-red' ?>">
                <?= $closingBal < 0 ? '-' : '' ?>$<?= number_format(abs($closingBal), 2) ?>
            </div>
        </div>
    </div>

</div>
</body>
</html>
