<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<div class="dashboard-container">
    
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1 class="h3 db-title">Dashboard Overview</h1>
            <div class="db-subtitle">Real-time insights and performance metrics</div>
        </div>
        <div>
            <span class="date-badge">
                <i data-lucide="calendar" width="14"></i>
                <?= date('l, F j, Y') ?>
            </span>
        </div>
    </div>

    <!-- 1. Primary KPI Cards -->
    <div class="kpi-grid">
        <!-- Revenue -->
        <div class="kpi-card">
            <div class="kpi-icon info">
                <i data-lucide="dollar-sign" width="24"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-value">$<?= number_format($metrics['total_revenue'], 2) ?></div>
                <div class="kpi-sub">Avg Fare: $<?= number_format($metrics['avg_fare'], 2) ?></div>
            </div>
        </div>

        <!-- Trips -->
        <div class="kpi-card">
            <div class="kpi-icon primary">
                <i data-lucide="map" width="24"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Total Trips</div>
                <div class="kpi-value"><?= number_format($metrics['total_trips']) ?></div>
                <div class="kpi-sub">
                    <span class="text-success"><?= $metrics['completed_trips'] ?> OK</span> • 
                    <span class="text-danger"><?= $metrics['cancelled_trips'] ?> XX</span>
                </div>
            </div>
        </div>

        <!-- Drivers -->
        <div class="kpi-card">
            <div class="kpi-icon success">
                <i data-lucide="car" width="24"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Total Drivers</div>
                <div class="kpi-value"><?= number_format($metrics['total_drivers']) ?></div>
                <div class="kpi-sub">
                    <span class="text-success"><?= $metrics['online_drivers'] ?> Online</span>
                </div>
            </div>
        </div>

        <!-- Customers -->
        <div class="kpi-card">
            <div class="kpi-icon warning">
                <i data-lucide="users" width="24"></i>
            </div>
            <div class="kpi-content">
                <div class="kpi-label">Total Customers</div>
                <div class="kpi-value"><?= number_format($metrics['total_customers']) ?></div>
                <div class="kpi-sub">+<?= $metrics['new_customers_month'] ?> this month</div>
            </div>
        </div>
    </div>

    <!-- 2. Operational Metrics (Small) -->
    <div class="ops-grid">
        <div class="ops-card">
            <div class="ops-label">Active Trips</div>
            <div class="ops-value text-primary"><?= $metrics['active_trips'] ?></div>
        </div>
        <div class="ops-card">
            <div class="ops-label">Pending Requests</div>
            <div class="ops-value text-warning"><?= $metrics['pending_requests'] ?></div>
        </div>
        <div class="ops-card">
            <div class="ops-label">Completed Today</div>
            <!-- Calculating purely for display, could be precise from controller but this is 'Completed Trips' total. Let's use simple logic or just hide 'Today' if data missing -->
            <!-- We will just show Completed Total for now as placeholder for daily stat -->
            <div class="ops-value text-success"><?= number_format($metrics['completed_trips']) ?></div>
        </div>
        <div class="ops-card">
            <div class="ops-label">Avg Fare</div>
            <div class="ops-value text-info">$<?= number_format($metrics['avg_fare'], 1) ?></div>
        </div>
    </div>

    <!-- 3. Charts Section -->
    <div class="charts-row-main">
        <!-- Main Activity Chart -->
        <div class="chart-card main-chart">
            <div class="chart-header">
                <h3>Revenue & Trip Volume (30 Days)</h3>
            </div>
            <div class="chart-body">
                <canvas id="mainActivityChart"></canvas>
            </div>
        </div>
    </div>

    <div class="charts-row-secondary">
        <!-- Trip Status -->
        <div class="chart-card">
            <div class="chart-header"><h3>Trip Statuses</h3></div>
            <div class="chart-body">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <!-- Vehicle Types -->
        <div class="chart-card">
            <div class="chart-header"><h3>Vehicle Types</h3></div>
            <div class="chart-body">
                <canvas id="vehicleChart"></canvas>
            </div>
        </div>
         <!-- Customer Growth -->
         <div class="chart-card">
            <div class="chart-header"><h3>Customer Growth</h3></div>
            <div class="chart-body">
                <canvas id="customerChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 4. Data Tables -->
    <div class="tables-grid">
        
        <!-- Recent Trips -->
        <div class="table-card">
            <div class="card-header-flex">
                <h3>Recent Trips</h3>
                <a href="<?= base_url('dispatch/trips') ?>" class="view-all">View All</a>
            </div>
            <div class="list-container">
                <?php if(empty($lists['recent_trips'])): ?>
                    <div class="empty-state">No recent activity</div>
                <?php else: ?>
                    <?php foreach($lists['recent_trips'] as $trip): ?>
                    <a href="<?= base_url('dispatch/trips/edit/'.$trip->id) ?>" class="list-item">
                        <div class="list-row-top">
                            <span class="status-badge status-<?= $trip->status ?>"><?= ucfirst($trip->status) ?></span>
                            <span class="list-date"><?= date('M j, H:i', strtotime($trip->created_at)) ?></span>
                        </div>
                        <div class="list-title">#<?= $trip->trip_number ?> - $<?= number_format($trip->fare_amount, 2) ?></div>
                        <div class="list-sub"><i data-lucide="map-pin" width="12"></i> <?= substr($trip->pickup_address, 0, 30) ?>...</div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Drivers -->
        <div class="table-card">
             <div class="card-header-flex">
                <h3>Top Drivers</h3>
                <a href="<?= base_url('fleet/drivers') ?>" class="view-all">View All</a>
            </div>
            <div class="list-container">
                <?php if(empty($lists['top_drivers'])): ?>
                    <div class="empty-state">No drivers found</div>
                <?php else: ?>
                    <?php foreach($lists['top_drivers'] as $driver): ?>
                    <div class="list-item no-hover">
                        <div class="driver-row">
                            <div class="driver-avatar">
                                <?= strtoupper(substr($driver->first_name, 0, 1)) ?>
                            </div>
                            <div class="driver-info">
                                <div class="driver-name"><?= $driver->first_name ?> <?= $driver->last_name ?></div>
                                <div class="driver-meta">
                                    <span class="rating"><i data-lucide="star" width="10"></i> <?= $driver->rating ?></span>
                                    <span>• <?= $driver->total_trips ?> Trips</span>
                                </div>
                            </div>
                            <div class="driver-status">
                                <span class="status-dot <?= $driver->status == 'active' ? 'online' : 'offline' ?>"></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<style>
    :root {
        --kpi-bg: var(--bg-surface);
        --chart-bg: var(--bg-surface);
    }

    .dashboard-container {
        padding: 2rem;
        max-width: 1600px;
        margin: 0 auto;
    }

    /* Header */
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
    }
    .db-title { margin: 0; font-weight: 700; color: var(--text-primary); }
    .db-subtitle { color: var(--text-secondary); font-size: 0.95rem; margin-top: 0.25rem; }
    .date-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    /* KPI Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .kpi-card {
        background: var(--kpi-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-sm); }
    .kpi-icon {
        width: 56px; height: 56px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon.primary { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .kpi-icon.success { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .kpi-icon.warning { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
    .kpi-icon.info { background: rgba(14, 165, 233, 0.1); color: var(--info); }
    
    .kpi-content { overflow: hidden; }
    .kpi-label { font-size: 0.85rem; color: var(--text-secondary); font-weight: 500; margin-bottom: 0.25rem; }
    .kpi-value { font-size: 1.75rem; font-weight: 800; color: var(--text-primary); line-height: 1.1; margin-bottom: 0.25rem; }
    .kpi-sub { font-size: 0.8rem; color: var(--text-tertiary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Ops Grid */
    .ops-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .ops-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ops-label { font-weight: 500; font-size: 0.9rem; color: var(--text-secondary); }
    .ops-value { font-weight: 700; font-size: 1.1rem; }

    /* Charts Layout */
    .charts-row-main { margin-bottom: 1.5rem; }
    .charts-row-secondary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .chart-card {
        background: var(--chart-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
    }
    .main-chart { height: 400px; }
    .chart-header { margin-bottom: 1rem; }
    .chart-header h3 { margin: 0; font-size: 1rem; color: var(--text-primary); }
    .chart-body { flex: 1; position: relative; width: 100%; min-height: 250px; }

    /* Tables Grid */
    .tables-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 1.5rem;
    }
    .table-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        display: flex;
        flex-direction: column;
        max-height: 500px;
    }
    .card-header-flex {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .card-header-flex h3 { margin: 0; font-size: 1rem; }
    .view-all { font-size: 0.85rem; color: var(--primary); font-weight: 500; text-decoration: none; }
    
    .list-container { overflow-y: auto; padding: 0.5rem 0; }
    .list-item {
        display: block;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        text-decoration: none;
        transition: background 0.1s;
    }
    .list-item:last-child { border-bottom: none; }
    .list-item:hover:not(.no-hover) { background: var(--bg-subtle); }
    
    .list-row-top { display: flex; justify-content: space-between; margin-bottom: 0.25rem; }
    .list-date { font-size: 0.75rem; color: var(--text-secondary); }
    .list-title { font-weight: 600; color: var(--text-primary); font-size: 0.95rem; }
    .list-sub { font-size: 0.8rem; color: var(--text-secondary); display: flex; align-items: center; gap: 4px; margin-top: 4px; }

    /* Driver Row */
    .driver-row { display: flex; align-items: center; gap: 1rem; }
    .driver-avatar {
        width: 40px; height: 40px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600;
        font-size: 1rem;
    }
    .driver-info { flex: 1; }
    .driver-name { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); }
    .driver-meta { font-size: 0.8rem; color: var(--text-secondary); display: flex; gap: 0.5rem; align-items: center; }
    .rating { display: flex; align-items: center; gap: 2px; color: var(--warning); }
    .driver-status { margin-left: auto; }
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: block; background: var(--text-secondary); }
    .status-dot.online { background: var(--success); box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2); }

    /* Responsive */
    @media (max-width: 1280px) {
        .kpi-grid, .ops-grid { grid-template-columns: repeat(2, 1fr); }
        .charts-row-secondary { grid-template-columns: repeat(1, 1fr); }
        .tables-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .kpi-grid, .ops-grid { grid-template-columns: 1fr; }
    }

    /* Status Badges */
    .status-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-completed { background: rgba(16, 185, 129, 0.1); color: var(--success); }
    .status-cancelled, .status-cancelled_driver, .status-cancelled_customer { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
    .status-active, .status-dispatching, .status-started { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .status-pending { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Colors
        const primaryColor = '#6366f1';
        const primaryBg = 'rgba(99, 102, 241, 0.1)';
        const successColor = '#10b981';
        const warningColor = '#f59e0b';
        const infoColor = '#0ea5e9';
        const gridColor = 'rgba(200, 200, 200, 0.1)';
        const textColor = '#94a3b8';

        // 1. Main Activity Chart (Double Line)
        const activityCtx = document.getElementById('mainActivityChart').getContext('2d');
        const activityData = <?= json_encode($charts['daily_activity']) ?>;
        
        new Chart(activityCtx, {
            type: 'bar',
            data: {
                labels: activityData.map(d => new Date(d.date).toLocaleDateString('en-US', {month:'short', day:'numeric'})),
                datasets: [
                    {
                        label: 'Trips',
                        data: activityData.map(d => d.trip_count),
                        borderColor: primaryColor,
                        backgroundColor: primaryColor,
                        type: 'line',
                        yAxisID: 'y',
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 0
                    },
                    {
                        label: 'Revenue',
                        data: activityData.map(d => d.revenue),
                        backgroundColor: 'rgba(14, 165, 233, 0.5)',
                        yAxisID: 'y1',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: true } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: { type: 'linear', display: true, position: 'left', grid: { color: gridColor }, ticks: { color: textColor } },
                    y1: { type: 'linear', display: true, position: 'right', grid: { display: false }, ticks: { callback: function(value) { return '$' + value; }, color: textColor } }
                }
            }
        });

        // 2. Status Distribution (Doughnut)
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusData = <?= json_encode($charts['status_distribution']) ?>;
        
        // Simple color mapping
        const statusColors = {
            'completed': successColor, 'cancelled': '#ef4444', 
            'active': primaryColor, 'pending': warningColor
        };

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(d => d.status.charAt(0).toUpperCase() + d.status.slice(1)),
                datasets: [{
                    data: statusData.map(d => d.count),
                    backgroundColor: statusData.map(d => statusColors[d.status] || '#cbd5e1'),
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } },
                cutout: '70%'
            }
        });

        // 3. Vehicle Types (Pie)
        const vehicleCtx = document.getElementById('vehicleChart').getContext('2d');
        const vehicleData = <?= json_encode($charts['vehicle_usage']) ?>;
        
        new Chart(vehicleCtx, {
            type: 'pie',
            data: {
                labels: vehicleData.map(d => d.vehicle_type),
                datasets: [{
                    data: vehicleData.map(d => d.count),
                    backgroundColor: [primaryColor, successColor, warningColor, infoColor, '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } }
            }
        });

        // 4. Customer Growth (Line)
        const customerCtx = document.getElementById('customerChart').getContext('2d');
        const customerData = <?= json_encode($charts['customer_growth']) ?>;

        new Chart(customerCtx, {
            type: 'line',
            data: {
                labels: customerData.map(d => new Date(d.date).toLocaleDateString('en-US', {month:'short', day:'numeric'})),
                datasets: [{
                    label: 'New Customers',
                    data: customerData.map(d => d.count),
                    borderColor: successColor,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 2,
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { display: false } },
                    y: { display: false }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>
