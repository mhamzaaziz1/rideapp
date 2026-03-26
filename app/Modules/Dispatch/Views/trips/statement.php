<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Statement – <?= esc($customer->first_name . ' ' . $customer->last_name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            color: #111;
            padding: 2rem;
        }

        .toolbar {
            max-width: 900px; margin: 0 auto 1.5rem;
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
            .statement { box-shadow: none !important; border: 1px solid #eee !important; }
            @page { margin: 15mm; }
        }

        .statement {
            max-width: 900px; margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,.10);
            overflow: hidden;
        }

        /* Header band */
        .stmt-header {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #fff; padding: 32px 40px 24px;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .stmt-co-name { font-size: 1.4rem; font-weight: 700; letter-spacing: .4px; margin-bottom: 4px; }
        .stmt-co-sub  { font-size: 0.82rem; opacity: .8; line-height: 1.6; }
        .stmt-title-block { text-align: right; }
        .stmt-title   { font-size: 1.2rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; color: #38bdf8; }
        .stmt-date    { font-size: 0.8rem; opacity: .8; margin-top: 6px; }

        /* Info Bar */
        .stmt-info-bar {
            background: #f8fafc; border-bottom: 1px solid #e2e8f0;
            padding: 20px 40px; display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;
        }
        .info-item-label { font-size: .7rem; color: #64748b; text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 4px; }
        .info-item-val { font-weight: 600; color: #1e293b; font-size: 0.9rem; }

        /* Customer Section */
        .stmt-content { padding: 32px 40px; }
        .stmt-section-title { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px; }

        /* Table */
        .stmt-table { width: 100%; border-collapse: collapse; font-size: .85rem; margin-bottom: 32px; }
        .stmt-table thead tr { background: #f1f5f9; }
        .stmt-table th { padding: 12px 16px; text-align: left; color: #475569; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; }
        .stmt-table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: top; }
        .stmt-table tr:last-child td { border-bottom: none; }
        
        .trip-num { font-family: monospace; font-weight: 700; color: #0284c7; }
        .addr-text { color: #64748b; font-size: 0.8rem; line-height: 1.4; display: block; margin-top: 4px; }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }

        /* Totals */
        .stmt-totals { display: flex; justify-content: flex-end; }
        .totals-table { width: 300px; border-collapse: collapse; }
        .totals-table td { padding: 8px 16px; font-size: 0.9rem; }
        .totals-table td:last-child { text-align: right; font-weight: 600; }
        .total-row { border-top: 2px solid #1e293b; font-weight: 700 !important; font-size: 1.1rem !important; color: #0284c7; }

        /* Footer */
        .stmt-footer {
            padding: 24px 40px; border-top: 1px solid #e2e8f0; background: #f8fafc;
            text-align: center; font-size: 0.75rem; color: #94a3b8;
        }
    </style>
</head>
<body>

<?php
$sf = WRITEPATH . 'settings.json';
$settings = file_exists($sf) ? (json_decode(file_get_contents($sf), true) ?? []) : [];
$companyName  = $settings['company_name']  ?? 'RideApp Inc.';
$companyPhone = $settings['company_phone'] ?? '';
$companyAddr  = trim(implode(', ', array_filter([
    $settings['company_address'] ?? '',
    $settings['company_city']    ?? '',
    $settings['company_state']   ?? '',
])));

$totalAmount = 0;
foreach ($trips as $t) {
    if ($t->status !== 'cancelled') {
        $totalAmount += $t->fare_amount;
    }
}
$printDate = date('F j, Y');
?>

<div class="toolbar">
    <h2>Statement Preview</h2>
    <div class="toolbar-actions">
        <button onclick="window.history.back()" class="btn btn-outline">← Back</button>
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
            <div class="stmt-title">Trip Activity Statement</div>
            <div class="stmt-date"><?= $printDate ?></div>
        </div>
    </div>

    <!-- Info Bar -->
    <div class="stmt-info-bar">
        <div>
            <span class="info-item-label">Customer</span>
            <span class="info-item-val"><?= esc($customer->first_name . ' ' . $customer->last_name) ?></span>
        </div>
        <div>
            <span class="info-item-label">Account #</span>
            <span class="info-item-val"><?= $customer->id ?></span>
        </div>
        <div>
            <span class="info-item-label">Trip Count</span>
            <span class="info-item-val"><?= count($trips) ?></span>
        </div>
        <div style="text-align:right;">
            <span class="info-item-label">Total Amount</span>
            <span class="info-item-val" style="color:#0284c7;">$<?= number_format($totalAmount, 2) ?></span>
        </div>
    </div>

    <div class="stmt-content">
        <div class="stmt-section-title">Activity Details</div>
        
        <table class="stmt-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Trip #</th>
                    <th>Route / Details</th>
                    <th>Status</th>
                    <th style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trips as $t): ?>
                <tr>
                    <td style="white-space:nowrap;"><?= date('M d, Y', strtotime($t->created_at)) ?></td>
                    <td><span class="trip-num">#<?= esc($t->trip_number) ?></span></td>
                    <td>
                        <div style="font-weight:600;"><?= esc($t->vehicle_type ?? 'Standard') ?> Trip</div>
                        <span class="addr-text">From: <?= esc($t->pickup_address) ?></span>
                        <span class="addr-text">To: <?= esc($t->dropoff_address) ?></span>
                        <?php if($t->distance_miles): ?>
                            <span class="addr-text" style="font-style:italic;"><?= number_format($t->distance_miles, 2) ?> miles · <?= $t->duration_minutes ?> mins</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status-badge status-<?= $t->status ?>"><?= $t->status ?></span>
                    </td>
                    <td style="text-align:right; font-weight:700;">
                        $<?= number_format($t->fare_amount, 2) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="stmt-totals">
            <table class="totals-table">
                <tr>
                    <td>Subtotal (<?= count($trips) ?> trips)</td>
                    <td>$<?= number_format($totalAmount, 2) ?></td>
                </tr>
                <tr class="total-row">
                    <td>Statement Total</td>
                    <td>$<?= number_format($totalAmount, 2) ?></td>
                </tr>
            </table>
        </div>
    </div>

    <div class="stmt-footer">
        <p>Thank you for choosing <?= esc($companyName) ?>. This is a formal statement of your trip activities.</p>
        <p style="margin-top:8px; opacity:0.7;">Generated on <?= date('Y-m-d H:i:s') ?> · Account ID: <?= $customer->id ?></p>
    </div>
</div>

</body>
</html>
