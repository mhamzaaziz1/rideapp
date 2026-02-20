<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Cheque – <?= esc($driver->first_name . ' ' . $driver->last_name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Georgia', 'Times New Roman', serif;
            background: #e5e7eb;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 2rem;
        }

        /* ── Toolbar (hidden on print) ──────────────────────────────────── */
        .toolbar {
            width: 800px;
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
            align-items: center;
            justify-content: space-between;
        }
        .toolbar h2 { font-size: 1rem; color: #374151; font-family: sans-serif; }
        .toolbar-actions { display: flex; gap: 0.75rem; }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 0.55rem 1.2rem;
            border-radius: 6px; font-size: 0.875rem;
            cursor: pointer; border: none; text-decoration: none;
            font-family: sans-serif;
        }
        .btn-primary { background: #1d4ed8; color: #fff; }
        .btn-outline  { background: #fff; color: #374151; border: 1px solid #d1d5db; }
        @media print {
            .toolbar { display: none !important; }
            body { background: #fff; padding: 0; }
        }

        /* ── Cheque Paper ─────────────────────────────────────────────── */
        .cheque-wrap {
            width: 800px;
        }
        .cheque {
            background: #fff;
            border: 1.5px solid #c7d2e2;
            border-radius: 5px;
            padding: 30px 38px 24px;
            position: relative;
            box-shadow: 0 6px 32px rgba(0,0,0,0.14);
            /* subtle guilloche-style security pattern */
            background-image:
                repeating-linear-gradient(135deg, transparent, transparent 18px, rgba(99,130,255,0.04) 18px, rgba(99,130,255,0.04) 19px),
                repeating-linear-gradient(45deg,  transparent, transparent 18px, rgba(99,130,255,0.04) 18px, rgba(99,130,255,0.04) 19px);
        }
        /* colour band at top */
        .cheque::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 7px;
            background: linear-gradient(90deg, #1e3a8a 0%, #2563eb 50%, #1e3a8a 100%);
            border-radius: 5px 5px 0 0;
        }

        /* ── Company & Cheque No ────────────────────────────────────────── */
        .cheque-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .co-name { font-size: 1.1rem; font-weight: 700; color: #1e3a8a; letter-spacing: .4px; }
        .co-addr { font-size: 0.72rem; color: #6b7280; line-height: 1.6; margin-top: 3px; }

        .cheque-no { font-size: 0.7rem; color: #9ca3af; font-family: monospace; margin-bottom: 6px; text-align: right; }
        .date-block { text-align: right; }
        .date-label { font-size: 0.68rem; color: #6b7280; text-transform: uppercase; letter-spacing: .5px; }
        .date-val {
            display: inline-block;
            border-bottom: 1.2px solid #374151;
            min-width: 160px;
            text-align: center;
            font-size: 0.85rem;
            font-weight: 600;
            padding-bottom: 2px;
            margin-top: 2px;
        }

        /* ── Payee ──────────────────────────────────────────────────────── */
        .payee-row {
            display: flex; align-items: flex-end; gap: 10px;
            margin-bottom: 14px;
        }
        .payee-label { font-size: 0.78rem; color: #374151; white-space: nowrap; }
        .payee-name {
            flex: 1;
            font-size: 1.05rem; font-weight: 700; color: #111;
            border-bottom: 1.5px solid #374151; padding-bottom: 3px;
        }
        .amount-box {
            border: 1.8px solid #1e3a8a;
            border-radius: 3px; padding: 5px 16px;
            font-size: 1.15rem; font-weight: 700; color: #1e3a8a;
            font-family: 'Courier New', monospace;
            white-space: nowrap; background: #eff6ff;
        }

        /* ── Words ──────────────────────────────────────────────────────── */
        .words-row {
            display: flex; align-items: flex-end; gap: 8px;
            margin-bottom: 10px;
        }
        .words-label { font-size: 0.76rem; color: #374151; white-space: nowrap; }
        .words-val {
            flex: 1;
            border-bottom: 1px solid #9ca3af; padding-bottom: 2px;
            font-size: 0.88rem; font-weight: 600; color: #111;
            text-transform: capitalize;
        }
        .words-suffix { font-size: 0.72rem; color: #6b7280; white-space: nowrap; }

        /* ── Bank info strip ────────────────────────────────────────────── */
        .bank-strip {
            background: #f0f4ff;
            border: 1px solid #c7d2e2;
            border-radius: 3px;
            padding: 6px 12px;
            font-size: 0.72rem;
            color: #4b5563;
            margin-bottom: 14px;
            display: flex; gap: 24px;
        }
        .bank-strip span { display: flex; align-items: center; gap: 4px; }

        /* ── Footer ─────────────────────────────────────────────────────── */
        .cheque-footer {
            display: flex; justify-content: space-between; align-items: flex-end;
            margin-top: 18px;
        }
        .memo-label { font-size: 0.68rem; color: #9ca3af; text-transform: uppercase; margin-bottom: 3px; }
        .memo-val {
            border-bottom: 1px solid #d1d5db;
            font-size: 0.82rem; color: #374151;
            min-width: 240px; padding-bottom: 2px;
        }
        .sig-area { text-align: center; }
        .sig-line-top { border-top: 1.5px solid #374151; width: 210px; }
        .sig-text { font-size: 0.68rem; color: #6b7280; padding-top: 4px; text-align: center; }

        /* ── MICR ───────────────────────────────────────────────────────── */
        .micr {
            margin-top: 18px;
            border-top: 1px dashed #d1d5db;
            padding-top: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.72rem; color: #9ca3af;
            display: flex; justify-content: space-between;
        }

        /* ── Watermark ──────────────────────────────────────────────────── */
        .watermark {
            position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-28deg);
            font-size: 88px; font-weight: 900; letter-spacing: 6px;
            color: rgba(37,99,235,0.05); pointer-events: none; user-select: none;
            white-space: nowrap;
        }

        /* ── Stub (tear-off) ────────────────────────────────────────────── */
        .stub {
            border: 1.5px dashed #9ca3af;
            border-top: none;
            border-radius: 0 0 5px 5px;
            padding: 14px 38px;
            background: #f9fafb;
            display: flex; justify-content: space-between;
            font-size: 0.78rem; color: #374151; font-family: sans-serif;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .stub-col { display: flex; flex-direction: column; gap: 4px; }
        .stub-row { display: flex; gap: 12px; }
        .stub-label { color: #9ca3af; min-width: 100px; }
        .stub-val   { font-weight: 600; color: #111; }
    </style>
</head>
<body>

<?php
/* ── Amount to words helper ─────────────────────────────────────────────── */
function numberToWords(float $amount): string {
    $dollars = (int) $amount;
    $cents   = (int) round(($amount - $dollars) * 100);

    $ones = ['','one','two','three','four','five','six','seven','eight','nine',
             'ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen',
             'seventeen','eighteen','nineteen'];
    $tens = ['','','twenty','thirty','forty','fifty','sixty','seventy','eighty','ninety'];

    $conv = function(int $n) use ($ones, $tens, &$conv): string {
        if ($n < 20)  return $ones[$n];
        if ($n < 100) return $tens[(int)($n/10)] . ($n % 10 ? '-' . $ones[$n%10] : '');
        return $ones[(int)($n/100)] . ' hundred' . ($n % 100 ? ' ' . $conv($n%100) : '');
    };

    if ($dollars === 0 && $cents === 0) return 'zero';
    $out = $dollars > 0 ? $conv($dollars) . ' dollar' . ($dollars === 1 ? '' : 's') : '';
    $out .= ' and ' . str_pad($cents, 2, '0', STR_PAD_LEFT) . '/100';
    return ucfirst(trim($out));
}

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

/* ── Cheque data ────────────────────────────────────────────────────────── */
$chequeNo  = 'CHQ-' . strtoupper(substr(md5($tx['id'] . ($tx['created_at'] ?? '')), 0, 8));
$payeeName = $driver->first_name . ' ' . $driver->last_name;
$amount    = (float) $tx['amount'];
$dateStr   = date('F j, Y', strtotime($tx['created_at'] ?? 'now'));
$memo      = $tx['description'] ?? 'Driver Payout / Withdrawal';
$txRef     = 'TXN-' . str_pad($tx['id'], 6, '0', STR_PAD_LEFT);
?>

<!-- Toolbar -->
<div class="toolbar">
    <h2>Driver Cheque — <?= esc($payeeName) ?></h2>
    <div class="toolbar-actions">
        <a href="<?= base_url('drivers/profile/' . $driver->id) ?>" class="btn btn-outline">← Back to Profile</a>
        <button onclick="window.print()" class="btn btn-primary">🖨&nbsp; Print / Save PDF</button>
    </div>
</div>

<div class="cheque-wrap">

    <!-- ── Main cheque body ─────────────────────────────────────────────── -->
    <div class="cheque">
        <div class="watermark">PAY</div>

        <!-- Header -->
        <div class="cheque-top">
            <div>
                <div class="co-name"><?= esc($companyName) ?></div>
                <div class="co-addr">
                    <?= esc($companyAddr) ?>
                    <?php if ($companyPhone): ?>&nbsp;·&nbsp; Tel: <?= esc($companyPhone) ?><?php endif; ?>
                </div>
            </div>
            <div>
                <div class="cheque-no"># <?= esc($chequeNo) ?></div>
                <div class="date-block">
                    <div class="date-label">Date</div>
                    <div class="date-val"><?= esc($dateStr) ?></div>
                </div>
            </div>
        </div>

        <!-- Payee -->
        <div class="payee-row">
            <span class="payee-label">Pay to the order of</span>
            <span class="payee-name"><?= esc($payeeName) ?></span>
            <span class="amount-box">$ <?= number_format($amount, 2) ?></span>
        </div>

        <!-- Words -->
        <div class="words-row">
            <span class="words-label">Amount in words:</span>
            <span class="words-val"><?= esc(numberToWords($amount)) ?></span>
            <span class="words-suffix">Dollars</span>
        </div>

        <!-- Bank info -->
        <div class="bank-strip">
            <span><strong>Driver ID:</strong>&nbsp; <?= esc($driver->id) ?></span>
            <span><strong>Phone:</strong>&nbsp; <?= esc($driver->phone) ?></span>
            <span><strong>Ref:</strong>&nbsp; <?= esc($txRef) ?></span>
            <span><strong>Type:</strong>&nbsp; Withdrawal / Payout</span>
        </div>

        <!-- Memo & Signature -->
        <div class="cheque-footer">
            <div>
                <div class="memo-label">Memo</div>
                <div class="memo-val"><?= esc($memo) ?></div>
            </div>
            <div class="sig-area">
                <div class="sig-line-top"></div>
                <div class="sig-text">Authorised Signature</div>
            </div>
        </div>

        <!-- MICR -->
        <div class="micr">
            <span>⑆ <?= str_pad((string)$driver->id, 9, '0', STR_PAD_LEFT) ?> ⑆</span>
            <span><?= esc($chequeNo) ?></span>
            <span>⑈ $ <?= number_format($amount, 2) ?> ⑈</span>
        </div>
    </div>

    <!-- ── Tear-off stub ────────────────────────────────────────────────── -->
    <div class="stub">
        <div class="stub-col">
            <div class="stub-row"><span class="stub-label">Payee</span><span class="stub-val"><?= esc($payeeName) ?></span></div>
            <div class="stub-row"><span class="stub-label">Date</span><span class="stub-val"><?= esc($dateStr) ?></span></div>
        </div>
        <div class="stub-col">
            <div class="stub-row"><span class="stub-label">Cheque No.</span><span class="stub-val"><?= esc($chequeNo) ?></span></div>
            <div class="stub-row"><span class="stub-label">Ref</span><span class="stub-val"><?= esc($txRef) ?></span></div>
        </div>
        <div class="stub-col">
            <div class="stub-row"><span class="stub-label">Amount</span><span class="stub-val">$ <?= number_format($amount, 2) ?></span></div>
            <div class="stub-row"><span class="stub-label">Memo</span><span class="stub-val"><?= esc(substr($memo, 0, 30)) ?></span></div>
        </div>
    </div>

</div>

<script>
    if (new URLSearchParams(window.location.search).get('autoprint') === '1') {
        window.addEventListener('load', () => setTimeout(() => window.print(), 500));
    }
</script>
</body>
</html>
