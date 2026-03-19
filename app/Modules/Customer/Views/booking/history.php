<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div style="max-width: 1000px; margin: 0 auto; padding: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
        <h1 class="h3" style="margin:0;">My Trips</h1>
        <div style="display:flex; gap:10px;">
            <button id="bulkPrintBtn" class="btn btn-outline" style="display:none; align-items:center; gap:6px;" onclick="bulkPrintSelected()">
                <i data-lucide="printer" width="16"></i> Print Selected (<span id="selectedCount">0</span>)
            </button>
            <a href="<?= base_url('customer/book') ?>" class="btn btn-primary"><i data-lucide="plus" width="16"></i> Book a Ride</a>
        </div>
    </div>

    <!-- Filters -->
    <div style="margin-bottom:2rem; background:var(--bg-surface); padding:1rem; border-radius:var(--radius-md); border:1px solid var(--border-color);">
        <form action="<?= base_url('customer/trips') ?>" method="get" style="display:flex; align-items:flex-end; gap:1rem;">
            <div style="flex:1;">
                <label style="display:block; font-size:0.8rem; color:var(--text-secondary); margin-bottom:0.25rem;">From Date</label>
                <input type="date" name="from_date" class="form-control" value="<?= esc($filters['from_date'] ?? '') ?>">
            </div>
            <div style="flex:1;">
                <label style="display:block; font-size:0.8rem; color:var(--text-secondary); margin-bottom:0.25rem;">To Date</label>
                <input type="date" name="to_date" class="form-control" value="<?= esc($filters['to_date'] ?? '') ?>">
            </div>
            <div>
                <button type="submit" class="btn btn-outline" style="padding: 0.75rem 1.25rem;"><i data-lucide="filter" width="16" style="margin-right:4px;"></i> Filter</button>
                <?php if(!empty($filters['from_date']) || !empty($filters['to_date'])): ?>
                    <a href="<?= base_url('customer/trips') ?>" class="btn btn-ghost" style="padding: 0.75rem 1.25rem;">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <?php if(empty($trips)): ?>
        <div style="text-align:center; padding:4rem; background:var(--bg-surface); border-radius:var(--radius-md); border:1px solid var(--border-color);">
            <i data-lucide="map" width="48" style="color:var(--text-secondary); opacity:0.5; margin-bottom:1rem;"></i>
            <h3 class="h5">No trips yet</h3>
            <p style="color:var(--text-secondary);">Your journey history will appear here.</p>
            <a href="<?= base_url('customer/book') ?>" class="btn btn-primary" style="margin-top:1rem;">Start Riding</a>
        </div>
    <?php else: ?>
        <div class="card">
            <div class="table-container">
                <table class="table" id="tripsTable">
                    <thead>
                        <tr>
                            <th style="width:40px;"><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Fare</th>
                            <th>Status</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($trips as $t): ?>
                        <tr>
                            <td><input type="checkbox" class="trip-checkbox" value="<?= $t->id ?>" onclick="updateBulkBtn()"></td>
                            <td><?= $t->created_at->humanize() ?></td>
                            <td><span style="font-family:monospace; background:rgba(0,0,0,0.05); padding:2px 6px; border-radius:4px;">#<?= $t->trip_number ?></span></td>
                            <td>
                                <div style="font-size:0.9rem; font-weight:600;"><?= esc($t->pickup_address) ?></div>
                                <div style="font-size:0.8rem; color:var(--text-secondary);">to <?= esc($t->dropoff_address) ?></div>
                            </td>
                            <td><span style="text-transform:capitalize;"><?= esc($t->vehicle_type ?? 'Standard') ?></span></td>
                            <td>$<?= number_format($t->fare_amount, 2) ?></td>
                            <td>
                                <?php
                                    $statusColor = 'var(--text-secondary)';
                                    $bg = 'var(--bg-body)';
                                    switch($t->status) {
                                        case 'completed': $statusColor = 'var(--success)'; $bg = 'rgba(16, 185, 129, 0.1)'; break;
                                        case 'active': $statusColor = 'var(--info)'; $bg = 'rgba(59, 130, 246, 0.1)'; break;
                                        case 'cancelled': $statusColor = 'var(--danger)'; $bg = 'rgba(239, 68, 68, 0.1)'; break;
                                        case 'pending': $statusColor = 'var(--warning)'; $bg = 'rgba(245, 158, 11, 0.1)'; break;
                                    }
                                ?>
                                <span style="color:<?= $statusColor ?>; background:<?= $bg ?>; padding:4px 8px; border-radius:12px; font-size:0.8rem; font-weight:600; text-transform:capitalize;">
                                    <?= $t->status ?>
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="<?= base_url('dispatch/trips/print/' . $t->id) ?>" target="_blank" title="Print Receipt" style="color:var(--text-secondary);">
                                    <i data-lucide="printer" width="18"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
function toggleAll(source) {
    const checkboxes = document.querySelectorAll('.trip-checkbox');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateBulkBtn();
}

function updateBulkBtn() {
    const checkboxes = document.querySelectorAll('.trip-checkbox:checked');
    const bulkBtn = document.getElementById('bulkPrintBtn');
    const countSpan = document.getElementById('selectedCount');
    
    if (checkboxes.length > 0) {
        bulkBtn.style.display = 'inline-flex';
        countSpan.innerText = checkboxes.length;
    } else {
        bulkBtn.style.display = 'none';
    }
}

function bulkPrintSelected() {
    const checkboxes = document.querySelectorAll('.trip-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value).join(',');
    if (ids) {
        window.open('<?= base_url('dispatch/trips/bulk_print') ?>?ids=' + ids, '_blank');
    }
}
</script>
<?= $this->endSection() ?>
