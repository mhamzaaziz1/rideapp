<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --bg-body-new: #f8f9fc;
        --border-light: #e5e7eb;
        --text-dark: #111827;
        --text-gray: #6b7280;
        --primary-blue: #3b82f6;
        --primary-blue-hover: #2563eb;
        --success-bg: #dcfce7;
        --success-text: #166534;
        --success-border: #bbf7d0;
        --shadow-card: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    }
    
    body { background-color: var(--bg-body-new); font-family: 'Inter', sans-serif; }
    .main-content { background-color: var(--bg-body-new); }

    /* Header */
    .page-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 2rem;
    }
    .page-title { font-size: 1.75rem; font-weight: 700; color: var(--text-dark); margin-bottom: 0.25rem; }
    .page-desc { color: var(--text-gray); font-size: 0.95rem; }
    
    .btn-add {
        background-color: var(--primary-blue); color: white;
        border-radius: 8px; padding: 0.6rem 1.25rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.5rem; border: none;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
        transition: background 0.2s;
    }
    .btn-add:hover { background-color: var(--primary-blue-hover); color: white; }

    /* Stats Cards */
    .stats-container {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; margin-bottom: 2rem;
    }
    .stat-card {
        background: #ffffff; border-radius: 12px; padding: 1.5rem;
        box-shadow: var(--shadow-card); border: 1px solid var(--border-light);
        display: flex; flex-direction: column; position: relative;
    }
    .stat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    .stat-title { font-size: 0.95rem; font-weight: 600; color: var(--text-dark); }
    .stat-icon {
        width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center;
    }
    .icon-blue { background: #eff6ff; color: #3b82f6; }
    .icon-orange { background: #fff7ed; color: #f97316; }
    .icon-green { background: #f0fdf4; color: #22c55e; }
    .icon-purple { background: #f3e8ff; color: #a855f7; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-dark); line-height: 1.2; }
    .stat-sub { font-size: 0.8rem; color: var(--text-gray); display: flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem; }
    .mini-chart { flex-grow: 1; height: 2px; background: #cbd5e1; border-radius: 2px; position: relative; }
    .mini-chart::after { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 60%; background: var(--primary-blue); border-radius: 2px; }

    /* Table Container */
    .table-container {
        background: #ffffff; border-radius: 12px; box-shadow: var(--shadow-card); border: 1px solid var(--border-light); padding: 1.5rem;
    }

    /* Toolbar */
    .toolbar { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; }
    .search-wrapper { position: relative; width: 320px; }
    .search-input {
        width: 100%; padding: 0.6rem 1rem 0.6rem 2.5rem; border: 1px solid var(--border-light);
        border-radius: 8px; font-size: 0.95rem; color: var(--text-dark);
    }
    .search-icon { position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: var(--text-gray); }
    .active-filters { display: inline-flex; align-items: center; background: #f1f5f9; padding: 0.35rem 0.75rem; border-radius: 16px; font-size: 0.85rem; color: var(--text-gray); margin-top: 0.75rem; gap: 0.5rem; font-weight: 500; }
    
    .toolbar-actions { display: flex; gap: 0.75rem; }
    .btn-outline-action {
        border: 1px solid var(--border-light); background: #ffffff; color: var(--text-dark);
        border-radius: 8px; padding: 0.6rem 1rem; font-size: 0.9rem; font-weight: 600;
        display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; transition: background 0.2s;
    }
    .btn-outline-action:hover { background: #f8fafc; }

    /* Table */
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { 
        text-align: left; padding: 1rem 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); 
        border-bottom: 1px solid var(--border-light); border-top: 1px solid var(--border-light);
    }
    .custom-table td { padding: 1.25rem 0.5rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .custom-table tr:last-child td { border-bottom: none; }
    
    .customer-cell { display: flex; align-items: center; gap: 1rem; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--text-gray); }
    .customer-name { font-weight: 600; color: var(--text-dark); font-size: 0.95rem; margin-bottom: 0.15rem; }
    .text-muted { color: var(--text-gray); font-size: 0.85rem; }
    
    .contact-cell .text-muted { display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; }
    
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem;
        border-radius: 16px; font-size: 0.8rem; font-weight: 600; cursor: pointer;
    }
    .status-active { background: var(--success-bg); color: var(--success-text); border: 1px solid var(--success-border); }
    .status-inactive { background: #f1f5f9; color: var(--text-gray); border: 1px solid var(--border-light); }
    
    .stats-cell { font-size: 0.9rem; font-weight: 500; color: var(--text-dark); display: flex; align-items: center; gap: 1rem; }
    .stats-cell .stat-item { display: flex; align-items: center; gap: 0.5rem; }
    
    .actions-cell { display: flex; gap: 0.75rem; }
    .action-icon { color: var(--text-gray); cursor: pointer; transition: color 0.2s; }
    .action-icon:hover { color: var(--text-dark); }
    .action-icon.delete:hover { color: #ef4444; }
</style>

<div style="padding: 2.5rem;">
    
    <div class="page-header">
        <div>
            <h1 class="page-title">Customer Management</h1>
            <p class="page-desc">Customer, maintaining all core functionality and data.</p>
        </div>
        <a href="<?= base_url('customers/new') ?>" class="btn-add">
            <i data-lucide="plus" width="18"></i> Add Customer
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Total Customers</div>
                <div class="stat-icon icon-blue"><i data-lucide="users" width="18"></i></div>
            </div>
            <div class="stat-value"><?= $total_customers ?? '8' ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Active Users</div>
                <div class="stat-icon icon-orange"><i data-lucide="user-check" width="18"></i></div>
            </div>
            <div class="stat-value"><?= $active_customers ?? '8' ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">New This Month</div>
                <div class="stat-icon icon-green"><i data-lucide="calendar" width="18"></i></div>
            </div>
            <div class="stat-value"><?= $new_this_month ?? '7' ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Total Spent</div>
                <div class="stat-icon icon-purple"><i data-lucide="dollar-sign" width="18"></i></div>
            </div>
            <div class="stat-value">$<?= number_format($total_spent ?? 0, 2) ?></div>
            <div class="stat-sub">Spend over time <div class="mini-chart"></div></div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <div class="toolbar">
            <div>
                <div class="search-wrapper">
                    <i data-lucide="search" width="16" class="search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search">
                </div>
                <div class="active-filters">
                    Active Filters <i data-lucide="x" width="14" style="cursor:pointer"></i>
                </div>
            </div>
            <div class="toolbar-actions">
                <button class="btn-outline-action"><i data-lucide="list-filter" width="16"></i> Filter</button>
                <button class="btn-outline-action"><i data-lucide="upload" width="16"></i> Export</button>
            </div>
        </div>

        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 30%">Customer</th>
                    <th style="width: 25%">Contact</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 20%">Stats</th>
                    <th style="width: 10%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($customers)): ?>
                    <?php foreach($customers as $c): ?>
                    <tr onclick="viewCustomer(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)" style="cursor:pointer">
                        <td>
                            <div class="customer-cell">
                                <?php if($c->avatar): ?>
                                    <img src="<?= base_url($c->avatar) ?>" class="avatar">
                                <?php else: ?>
                                    <div class="avatar"><?= strtoupper(substr($c->first_name, 0, 1) . substr($c->last_name, 0, 1)) ?></div>
                                <?php endif; ?>
                                <div>
                                    <div class="customer-name"><?= esc($c->first_name . ' ' . $c->last_name) ?></div>
                                    <div class="text-muted">Email <?= esc($c->email) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="contact-cell">
                            <div class="text-muted"><i data-lucide="phone" width="14"></i> <?= esc($c->phone) ?></div>
                            <div class="text-muted"><i data-lucide="calendar" width="14"></i> Joined <?= date('n/y', strtotime($c->created_at)) ?></div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="status-badge status-<?= $c->status ?>" onclick="toggleStatus(<?= $c->id ?>, '<?= $c->status ?>')">
                                <?= ucfirst($c->status) ?>
                                <i data-lucide="refresh-cw" width="12" style="opacity:0.7"></i>
                            </div>
                        </td>
                        <td>
                            <div class="stats-cell">
                                <div class="stat-item"><i data-lucide="route" width="14" style="color:var(--text-gray)"></i> Trips</div>
                                <div class="stat-item"><i data-lucide="dollar-sign" width="14" style="color:var(--text-gray)"></i> $<?= number_format($c->total_spent, 2) ?></div>
                            </div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="actions-cell">
                                <a href="<?= base_url('customers/profile/'.$c->id) ?>" class="action-icon" title="Profile"><i data-lucide="user-circle" width="18"></i></a>
                                <a href="<?= base_url('customers/edit/'.$c->id) ?>" class="action-icon" title="Edit"><i data-lucide="edit-3" width="18"></i></a>
                                <a href="<?= base_url('customers/delete/'.$c->id) ?>" onclick="return confirm('Delete this customer?')" class="action-icon delete" title="Delete"><i data-lucide="trash-2" width="18"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Mock Data for Display (if database is empty but we want to match mockup) -->
                    <tr>
                        <td>
                            <div class="customer-cell">
                                <div class="avatar" style="background-image:url('https://i.pravatar.cc/150?img=11'); background-size:cover;"></div>
                                <div>
                                    <div class="customer-name">Name</div>
                                    <div class="text-muted">Email nams@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="contact-cell">
                            <div class="text-muted"><i data-lucide="phone" width="14"></i> 685-835-5630</div>
                            <div class="text-muted"><i data-lucide="calendar" width="14"></i> Joined 2/23</div>
                        </td>
                        <td>
                            <div class="status-badge status-active">
                                Active <i data-lucide="refresh-cw" width="12" style="opacity:0.7"></i>
                            </div>
                        </td>
                        <td>
                            <div class="stats-cell">
                                <div class="stat-item"><i data-lucide="route" width="14" style="color:var(--text-gray)"></i> Trips</div>
                                <div class="stat-item"><i data-lucide="dollar-sign" width="14" style="color:var(--text-gray)"></i> $0.00</div>
                            </div>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="#" class="action-icon"><i data-lucide="user-circle" width="18"></i></a>
                                <a href="#" class="action-icon"><i data-lucide="edit-3" width="18"></i></a>
                                <a href="#" class="action-icon delete"><i data-lucide="trash-2" width="18"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-cell">
                                <div class="avatar" style="background-image:url('https://i.pravatar.cc/150?img=12'); background-size:cover;"></div>
                                <div>
                                    <div class="customer-name">John Smith</div>
                                    <div class="text-muted">Email john@imail.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="contact-cell">
                            <div class="text-muted"><i data-lucide="phone" width="14"></i> 675-555-227</div>
                            <div class="text-muted"><i data-lucide="calendar" width="14"></i> Joined 2/23</div>
                        </td>
                        <td>
                            <div class="status-badge status-active">
                                Active <i data-lucide="refresh-cw" width="12" style="opacity:0.7"></i>
                            </div>
                        </td>
                        <td>
                            <div class="stats-cell">
                                <div class="stat-item"><i data-lucide="route" width="14" style="color:var(--text-gray)"></i> Trips</div>
                                <div class="stat-item"><i data-lucide="dollar-sign" width="14" style="color:var(--text-gray)"></i> $0.00</div>
                            </div>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="#" class="action-icon"><i data-lucide="user-circle" width="18"></i></a>
                                <a href="#" class="action-icon"><i data-lucide="edit-3" width="18"></i></a>
                                <a href="#" class="action-icon delete"><i data-lucide="trash-2" width="18"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="customer-cell">
                                <div class="avatar">CS</div>
                                <div>
                                    <div class="customer-name">Caran Smith</div>
                                    <div class="text-muted">Email smith@gmail.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="contact-cell">
                            <div class="text-muted"><i data-lucide="phone" width="14"></i> (30) 320-6720</div>
                            <div class="text-muted"><i data-lucide="calendar" width="14"></i> Joined 3/23</div>
                        </td>
                        <td>
                            <div class="status-badge status-active">
                                Active <i data-lucide="refresh-cw" width="12" style="opacity:0.7"></i>
                            </div>
                        </td>
                        <td>
                            <div class="stats-cell">
                                <div class="stat-item"><i data-lucide="route" width="14" style="color:var(--text-gray)"></i> Trips</div>
                                <div class="stat-item"><i data-lucide="dollar-sign" width="14" style="color:var(--text-gray)"></i> $0.00</div>
                            </div>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="#" class="action-icon"><i data-lucide="user-circle" width="18"></i></a>
                                <a href="#" class="action-icon"><i data-lucide="edit-3" width="18"></i></a>
                                <a href="#" class="action-icon delete"><i data-lucide="trash-2" width="18"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<!-- View Customer Modal -->
<div id="viewCustomerModal" class="modal-overlay" style="display:none;">
    <div class="modal-content" style="width:600px;">
        <div class="modal-header">
            <h2 class="h3" style="font-size:1.25rem;">Customer Profile</h2>
            <button class="close-modal" onclick="closeModal()"><i data-lucide="x" width="20"></i></button>
        </div>
        <div class="modal-body">
            <!-- Header -->
            <div style="display:flex; gap:1.5rem; align-items:center; margin-bottom:2rem;">
                 <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; border:1px solid var(--border-color);">
                    <img id="view_avatar" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                    <div id="view_avatar_placeholder" style="width:100%; height:100%; background:var(--bg-surface-hover); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.5rem;"></div>
                 </div>
                 <div>
                     <h3 id="view_name" style="margin:0; font-size:1.5rem; font-weight:700;"></h3>
                     <div class="meta-text" id="view_email" style="font-size:0.9rem;"></div>
                     <div id="view_status_badge" style="margin-top:0.5rem;"></div>
                 </div>
            </div>

            <!-- Info Grid -->
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.5rem; padding:1.5rem; background:var(--bg-surface-hover); border-radius:var(--radius-md);">
                <div>
                    <div class="form-label">Phone</div>
                    <div id="view_phone" style="font-weight:500;"></div>
                </div>
                <div>
                    <div class="form-label">Joined Date</div>
                    <div id="view_joined" style="font-weight:500;"></div>
                </div>
                <div>
                    <div class="form-label">Total Trips</div>
                    <div id="view_trips" style="font-weight:500;"></div>
                </div>
                <div>
                    <div class="form-label">Total Spent</div>
                    <div id="view_spent" style="font-weight:500;"></div>
                </div>
            </div>

            <!-- Activity Placeholder -->
            <div style="margin-top:2rem;">
                <h4 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); margin-bottom:1rem;">Recent Activity</h4>
                <div style="border-left:2px solid var(--border-color); padding-left:1.5rem; margin-left:0.5rem;">
                    <div style="margin-bottom:1.5rem; position:relative;">
                        <div style="position:absolute; left:-1.95rem; top:0; width:10px; height:10px; background:var(--primary); border-radius:50%;"></div>
                        <div style="font-size:0.9rem; font-weight:600;">Completed Trip #TR-8821</div>
                        <div style="font-size:0.8rem; color:var(--text-secondary);">2 days ago • $24.50</div>
                    </div>
                     <div style="margin-bottom:1.5rem; position:relative;">
                        <div style="position:absolute; left:-1.95rem; top:0; width:10px; height:10px; background:var(--border-color); border-radius:50%;"></div>
                        <div style="font-size:0.9rem; font-weight:600;">Updated Profile</div>
                        <div style="font-size:0.8rem; color:var(--text-secondary);">1 week ago</div>
                    </div>
                </div>
                </div>
            </div>
            
            <a href="#" id="view_profile_link" class="btn btn-primary btn-block" style="margin-top:1rem; width:100%; display:block; text-align:center;">View Full Profile & Wallet</a>
        </div>
    </div>
</div>

<script>
    function toggleStatus(id, currentStatus) {
        let newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        if(confirm('Change status to ' + newStatus + '?')) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', newStatus);

            fetch('<?= base_url("customers/update_status") ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload(); 
                } else {
                    alert('Error: ' + data.message);
                }
            });
        }
    }

    function viewCustomer(c) {
        const modal = document.getElementById('viewCustomerModal');
        
        // Populate
        document.getElementById('view_name').innerText = c.first_name + ' ' + c.last_name;
        document.getElementById('view_email').innerText = c.email;
        document.getElementById('view_phone').innerText = c.phone;
        document.getElementById('view_joined').innerText = new Date(c.created_at).toLocaleDateString();
        document.getElementById('view_trips').innerText = c.total_trips;
        document.getElementById('view_spent').innerHTML = '$' + parseFloat(c.total_spent).toFixed(2);
        
        document.getElementById('view_spent').innerHTML = '$' + parseFloat(c.total_spent).toFixed(2);
        
        document.getElementById('view_profile_link').href = '<?= base_url('customers/profile') ?>/' + c.id;

        // Avatar
        const img = document.getElementById('view_avatar');
        const ph = document.getElementById('view_avatar_placeholder');
        if(c.avatar) {
            img.src = '<?= base_url() ?>/' + c.avatar;
            img.style.display = 'block';
            ph.style.display = 'none';
        } else {
            img.style.display = 'none';
            ph.style.display = 'flex';
            ph.innerText = c.first_name.charAt(0) + c.last_name.charAt(0);
        }

        // Status Badge
        const badge = document.getElementById('view_status_badge');
        badge.innerHTML = `<span class="status-badge status-${c.status}">${c.status}</span>`;

        // Open
        modal.style.display = 'flex';
        setTimeout(() => {
            modal.querySelector('.modal-content').style.transform = 'scale(1)';
            modal.querySelector('.modal-content').style.opacity = '1';
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('viewCustomerModal');
        modal.querySelector('.modal-content').style.transform = 'scale(0.95)';
        modal.querySelector('.modal-content').style.opacity = '0';
        setTimeout(() => modal.style.display = 'none', 200);
    }
    
    // Close on overlay click
    window.onclick = function(e) {
        if(e.target.classList.contains('modal-overlay')) {
            closeModal();
        }
    }
</script>

<?= $this->endSection() ?>
