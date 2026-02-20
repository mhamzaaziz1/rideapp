<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div style="max-width: 1000px; margin: 0 auto; padding: 2rem;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <h1 class="h3">My Trips</h1>
        <a href="<?= base_url('customer/book') ?>" class="btn btn-primary"><i data-lucide="plus" width="16"></i> Book a Ride</a>
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Reference</th>
                            <th>Route</th>
                            <th>Vehicle</th>
                            <th>Fare</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($trips as $t): ?>
                        <tr>
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
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
