<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Receipt – #<?= esc($trip->trip_number) ?></title>
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
            max-width: 680px; margin: 0 auto 1.5rem;
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
            .receipt { box-shadow: none !important; border: 1px solid #ccc !important; }
            @page { margin: 12mm; }
        }

        /* ── Receipt Card ─────────────────────────────────────── */
        .receipt {
            max-width: 680px; margin: 0 auto;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            overflow: hidden;
        }

        /* Header band */
        .receipt-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff;
            padding: 24px 32px 18px;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .co-name { font-size: 1.15rem; font-weight: 700; letter-spacing: .4px; margin-bottom: 4px; }
        .co-sub  { font-size: 0.75rem; opacity: .8; line-height: 1.6; }
        .trip-num-block { text-align: right; }
        .trip-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 1px; opacity: .7; }
        .trip-num   { font-size: 1.3rem; font-weight: 700; font-family: monospace; letter-spacing: 2px; }
        .trip-date  { font-size: 0.75rem; opacity: .8; margin-top: 4px; }

        /* Status ribbon */
        .status-ribbon {
            padding: 8px 32px;
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-pending   { background: #fef3c7; color: #92400e; }
        .status-active    { background: #dbeafe; color: #1e40af; }
        .status-default   { background: #f3f4f6; color: #374151; }

        /* Body sections */
        .receipt-body { padding: 24px 32px; }

        .section-title {
            font-size: 0.68rem; text-transform: uppercase; letter-spacing: .8px;
            color: #9ca3af; margin-bottom: 10px; font-weight: 600;
        }

        /* Route */
        .route-block {
            display: flex; flex-direction: column; gap: 0;
            border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;
            margin-bottom: 20px;
        }
        .route-row {
            display: flex; align-items: center; gap: 14px;
            padding: 12px 16px;
        }
        .route-row + .route-row { border-top: 1px solid #f3f4f6; }
        .route-dot {
            width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0;
        }
        .dot-pickup  { background: #10b981; }
        .dot-dropoff { background: #ef4444; }
        .route-label { font-size: 0.7rem; color: #9ca3af; margin-bottom: 2px; }
        .route-addr  { font-size: 0.9rem; font-weight: 500; color: #111; }

        /* Info grid */
        .info-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 12px; margin-bottom: 20px;
        }
        .info-item {
            background: #f9fafb;
            border: 1px solid #f3f4f6;
            border-radius: 6px; padding: 10px 14px;
        }
        .info-label { font-size: 0.68rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .info-val   { font-size: 0.9rem; font-weight: 600; color: #111; }

        /* Parties */
        .party-block {
            display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;
        }
        .party-card {
            border: 1px solid #e5e7eb; border-radius: 6px; padding: 12px 16px;
        }
        .party-role { font-size: 0.68rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 4px; }
        .party-name { font-size: 0.95rem; font-weight: 700; color: #111; margin-bottom: 2px; }
        .party-sub  { font-size: 0.78rem; color: #6b7280; }

        /* Billing table */
        .billing-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; margin-bottom: 8px; }
        .billing-table td { padding: 7px 0; }
        .billing-table td:last-child { text-align: right; font-weight: 600; }
        .billing-table tr { border-bottom: 1px solid #f3f4f6; }
        .billing-table tr:last-child { border-bottom: none; }
        .billing-label { color: #6b7280; }
        .total-row td { border-top: 2px solid #e5e7eb !important; font-weight: 700; font-size: 1rem; padding-top: 10px !important; }
        .total-row td:last-child { color: #1d4ed8; }

        /* Notes */
        .notes-box {
            background: #f9fafb; border: 1px solid #f3f4f6;
            border-radius: 6px; padding: 12px 16px;
            font-size: 0.85rem; color: #374151;
            border-left: 3px solid #2563eb;
            margin-bottom: 16px;
        }

        /* Footer */
        .receipt-footer {
            border-top: 1px solid #f3f4f6;
            padding: 14px 32px;
            display: flex; justify-content: space-between; align-items: center;
            font-size: 0.72rem; color: #9ca3af; background: #fafafa;
        }
    </style>
</head>
<body>

<?php
/* ── Load settings ──────────────────────────────────────────────────────── */
$sf = WRITEPATH . 'settings.json';
$settings = file_exists($sf) ? (json_decode(file_get_contents($sf), true) ?? []) : [];
$companyName  = $settings['company_name']  ?? 'RideApp Inc.';
$companyPhone = $settings['company_phone'] ?? '';
$companyAddr  = trim(implode(', ', array_filter([
    $settings['company_address'] ?? '',
    $settings['company_city']    ?? '',
    $settings['company_state']   ?? '',
])));

/* ── Status class ───────────────────────────────────────────────────────── */
$statusClass = match($trip->status) {
    'completed' => 'status-completed',
    'cancelled' => 'status-cancelled',
    'pending'   => 'status-pending',
    'active'    => 'status-active',
    default     => 'status-default',
};

$tripDate    = date('F j, Y  H:i', strtotime($trip->created_at));
$printDate   = date('F j, Y H:i');
$fareTotal   = (float)($trip->fare_amount ?? 0);
$driverEarns = (float)($trip->driver_earnings ?? 0);
$commission  = $fareTotal - $driverEarns;
$surcharge   = (float)($trip->surcharge_amount ?? 0);
?>

<!-- Toolbar -->
<div class="toolbar">
    <h2>Trip Receipt — #<?= esc($trip->trip_number) ?></h2>
    <div class="toolbar-actions">
        <a href="<?= base_url('dispatch/trips/view/' . $trip->id) ?>" class="btn btn-outline">← Back</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Print / Save PDF</button>
    </div>
</div>

<!-- Receipt -->
<div class="receipt">

    <!-- Header -->
    <div class="receipt-header">
        <div>
            <div class="co-name"><?= esc($companyName) ?></div>
            <div class="co-sub"><?= esc($companyAddr) ?><?= $companyPhone ? ' · ' . esc($companyPhone) : '' ?></div>
        </div>
        <div class="trip-num-block">
            <div class="trip-label">Trip Receipt</div>
            <div class="trip-num">#<?= esc($trip->trip_number) ?></div>
            <div class="trip-date"><?= esc($tripDate) ?></div>
        </div>
    </div>

    <!-- Status ribbon -->
    <div class="status-ribbon <?= $statusClass ?>">
        <span>Status: <?= strtoupper($trip->status) ?></span>
        <?php if ($trip->completed_at): ?>
            <span>Completed: <?= date('M j, Y H:i', strtotime($trip->completed_at)) ?></span>
        <?php elseif ($trip->started_at): ?>
            <span>Started: <?= date('M j, Y H:i', strtotime($trip->started_at)) ?></span>
        <?php endif; ?>
    </div>

    <div class="receipt-body">

        <!-- Route -->
        <div class="section-title">Route</div>
        <div class="route-block">
            <div class="route-row">
                <div class="route-dot dot-pickup"></div>
                <div>
                    <div class="route-label">Pickup</div>
                    <div class="route-addr"><?= esc($trip->pickup_address) ?></div>
                </div>
            </div>
            <div class="route-row">
                <div class="route-dot dot-dropoff"></div>
                <div>
                    <div class="route-label">Dropoff</div>
                    <div class="route-addr"><?= esc($trip->dropoff_address) ?></div>
                </div>
            </div>
        </div>

        <!-- Trip Details -->
        <div class="section-title">Trip Details</div>
        <div class="info-grid" style="margin-bottom:20px;">
            <div class="info-item">
                <div class="info-label">Distance</div>
                <div class="info-val"><?= number_format($trip->distance_miles ?? 0, 2) ?> mi</div>
            </div>
            <div class="info-item">
                <div class="info-label">Duration</div>
                <div class="info-val"><?= $trip->duration_minutes ?? '—' ?> min</div>
            </div>
            <div class="info-item">
                <div class="info-label">Vehicle Type</div>
                <div class="info-val"><?= esc(ucfirst($trip->vehicle_type ?? 'Standard')) ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Passengers</div>
                <div class="info-val"><?= $trip->passengers ?? 1 ?></div>
            </div>
        </div>

        <!-- Parties -->
        <div class="section-title">Parties</div>
        <div class="party-block">
            <div class="party-card">
                <div class="party-role">Customer</div>
                <div class="party-name">
                    <?= esc(($trip->c_first ?? '') . ' ' . ($trip->c_last ?? '')) ?: 'Guest' ?>
                </div>
                <?php if (!empty($trip->c_phone)): ?>
                    <div class="party-sub"><?= esc($trip->c_phone) ?></div>
                <?php endif; ?>
            </div>
            <div class="party-card">
                <div class="party-role">Driver</div>
                <?php if (!empty($trip->d_first)): ?>
                    <div class="party-name"><?= esc($trip->d_first . ' ' . $trip->d_last) ?></div>
                    <?php if (!empty($trip->d_phone)): ?>
                        <div class="party-sub"><?= esc($trip->d_phone) ?></div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="party-name" style="color:#9ca3af;">Unassigned</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Billing -->
        <div class="section-title">Billing</div>
        <table class="billing-table">
            <tr>
                <td class="billing-label">Base Fare</td>
                <td>$<?= number_format($fareTotal - $surcharge, 2) ?></td>
            </tr>
            <?php if ($surcharge > 0): ?>
            <tr>
                <td class="billing-label">Surcharge</td>
                <td>$<?= number_format($surcharge, 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td class="billing-label">Payment Method</td>
                <td><?= esc(strtoupper($trip->payment_method ?? 'N/A')) ?></td>
            </tr>
            <?php if ($driverEarns > 0): ?>
            <tr>
                <td class="billing-label">Driver Earnings</td>
                <td style="color:#059669;">$<?= number_format($driverEarns, 2) ?></td>
            </tr>
            <tr>
                <td class="billing-label">Company Commission</td>
                <td style="color:#9ca3af;">$<?= number_format($commission, 2) ?></td>
            </tr>
            <?php endif; ?>
            <tr class="total-row">
                <td>Total Charged</td>
                <td>$<?= number_format($fareTotal, 2) ?></td>
            </tr>
        </table>

        <!-- Notes -->
        <?php if (!empty($trip->notes)): ?>
            <div class="section-title" style="margin-top:16px;">Notes</div>
            <div class="notes-box"><?= esc($trip->notes) ?></div>
        <?php endif; ?>

    </div><!-- /.receipt-body -->

    <!-- Footer -->
    <div class="receipt-footer">
        <span>Printed: <?= $printDate ?> · Ref: TRP-<?= str_pad($trip->id, 6, '0', STR_PAD_LEFT) ?></span>
        <span><?= esc($companyName) ?><?= $companyPhone ? ' · ' . esc($companyPhone) : '' ?></span>
    </div>

</div>

<script>
    if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    }
</script>
</body>
</html>
