<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 1.5rem;
        display: flex; align-items: center; justify-content: space-between;
    }
    .stat-label { font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 0.5rem; }
    .stat-value { font-size: 1.75rem; font-weight: 700; color: var(--text-primary); }
    .stat-icon { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: var(--bg-surface-hover); color: var(--text-accent); }
    
    /* List */
    .customer-list {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        display: flex; flex-direction: column;
    }
    .customer-item {
        display: grid;
        grid-template-columns: 60px 2fr 1.5fr 1fr 1fr auto;
        gap: 1.5rem;
        align-items: center;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.1s;
    }
    .customer-item:hover { background: var(--bg-surface-hover); }
    .customer-item:last-child { border-bottom: none; }

    .avatar-wrapper {
        width: 48px; height: 48px; border-radius: 50%; overflow: hidden; background: var(--bg-surface-hover);
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; color: var(--primary); font-size: 1rem;
        border: 1px solid var(--border-color);
    }
    
    /* Status Badge */
    .status-badge {
        padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.05em; cursor: pointer;
        display: inline-flex; align-items: center; gap: 4px;
        transition: transform 0.1s, box-shadow 0.1s;
    }
    .status-badge:hover { transform: translateY(-1px); box-shadow: var(--shadow-sm); }
    .status-active { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
    .status-inactive { background: rgba(100, 116, 139, 0.1); color: var(--text-secondary); border: 1px solid rgba(100, 116, 139, 0.2); }
    .status-banned { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }

    .meta-text { font-size: 0.85rem; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem; }
    
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.6); z-index: 1000;
        display: flex; align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-content {
        background: var(--bg-surface); padding: 2rem; border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg); border: 1px solid var(--border-color);
        transform: scale(0.95); opacity: 0; transition: all 0.2s;
        max-height: 90vh; overflow-y: auto;
    }
    .modal-header { display: flex; justify-content: space-between; margin-bottom: 1.5rem; }
    .close-modal { background: none; border: none; cursor: pointer; color: var(--text-secondary); }
</style>

<div style="padding: 2rem;">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
        <div>
            <h1 style="font-size:1.5rem; font-weight:700;">Customer Management</h1>
            <p style="color:var(--text-secondary); font-size:0.9rem;">Manage user profiles, activity, and status</p>
        </div>
        <a href="<?= base_url('customers/new') ?>" class="btn btn-primary"><i data-lucide="plus" width="16" style="margin-right:8px"></i> Add Customer</a>
    </div>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Customers</div>
                <div class="stat-value"><?= $total_customers ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="users"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Active Users</div>
                <div class="stat-value" style="color:var(--success)"><?= $active_customers ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="user-check" style="color:var(--success)"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">New This Month</div>
                <div class="stat-value" style="color:var(--primary)"><?= $new_this_month ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="calendar" style="color:var(--primary)"></i></div>
        </div>
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Spent</div>
                <div class="stat-value">$<?= number_format($total_spent, 2) ?></div>
            </div>
            <div class="stat-icon"><i data-lucide="dollar-sign"></i></div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
         <div style="position:relative; width:300px;">
            <i data-lucide="search" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); color:var(--text-secondary); width:16px;"></i>
            <input type="text" placeholder="Search customers..." style="width:100%; padding:0.6rem 0.6rem 0.6rem 2.2rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface);">
         </div>
         <div style="display:flex; gap:0.5rem;">
             <button class="btn btn-outline"><i data-lucide="filter" width="16" style="margin-right:4px;"></i> Filter</button>
             <button class="btn btn-outline"><i data-lucide="download" width="16" style="margin-right:4px;"></i> Export</button>
         </div>
    </div>

    <!-- Customer List -->
    <div class="customer-list">
        <?php if(session()->has('success')): ?>
            <div style="padding:1rem; background:rgba(16, 185, 129, 0.1); color:var(--success); border-bottom:1px solid var(--border-color);"><?= session('success') ?></div>
        <?php endif; ?>

        <!-- Header -->
        <div class="customer-item" style="background:var(--bg-body); font-size:0.8rem; font-weight:600; color:var(--text-secondary); padding-top:0.75rem; padding-bottom:0.75rem;">
            <div></div> <!-- Avatar -->
            <div>NAME & EMAIL</div>
            <div>CONTACT</div>
            <div>STATUS</div>
            <div>STATS</div>
            <div style="text-align:right;">ACTIONS</div>
        </div>

        <?php if(!empty($customers)): ?>
            <?php foreach($customers as $c): ?>
            <div class="customer-item" onclick="viewCustomer(<?= htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8') ?>)">
                <!-- Avatar -->
                <div class="avatar-wrapper">
                    <?php if($c->avatar): ?>
                        <img src="<?= base_url($c->avatar) ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($c->first_name, 0, 1) . substr($c->last_name, 0, 1)) ?>
                    <?php endif; ?>
                </div>

                <!-- Name -->
                <div>
                    <div style="font-weight:600; color:var(--text-primary); font-size:0.95rem;"><?= esc($c->first_name . ' ' . $c->last_name) ?></div>
                    <div class="meta-text"><?= esc($c->email) ?></div>
                </div>

                <!-- Contact -->
                <div>
                    <div class="meta-text"><i data-lucide="phone" width="12"></i> <?= esc($c->phone) ?></div>
                    <div class="meta-text" style="font-size:0.75rem; margin-top:2px;">Joined <?= date('M Y', strtotime($c->created_at)) ?></div>
                </div>

                <!-- Status (Interactable) -->
                <div onclick="event.stopPropagation()">
                    <div class="status-badge status-<?= $c->status ?>" onclick="toggleStatus(<?= $c->id ?>, '<?= $c->status ?>')">
                        <?= ucfirst($c->status) ?>
                        <i data-lucide="refresh-cw" width="10" style="margin-left:4px; opacity:0.6;"></i>
                    </div>
                </div>

                <!-- Stats -->
                <div>
                    <div style="font-weight:600;"><?= $c->total_trips ?> Trips</div>
                    <div class="meta-text">$<?= number_format($c->total_spent, 2) ?></div>
                </div>

                <!-- Actions -->
                <div style="display:flex; justify-content:flex-end; gap:0.5rem;" onclick="event.stopPropagation()">
                    <a href="<?= base_url('customers/profile/'.$c->id) ?>" class="btn btn-sm btn-outline" title="Profile"><i data-lucide="user" width="14"></i></a>
                    <a href="<?= base_url('customers/edit/'.$c->id) ?>" class="btn btn-sm btn-outline" title="Edit"><i data-lucide="edit-2" width="14"></i></a>
                    <a href="<?= base_url('customers/delete/'.$c->id) ?>" onclick="return confirm('Delete this customer?')" class="btn btn-sm btn-outline" style="color:var(--danger); border-color:var(--danger);" title="Delete"><i data-lucide="trash-2" width="14"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="padding:3rem; text-align:center; color:var(--text-secondary);">
                <i data-lucide="users" width="48" style="opacity:0.3; margin-bottom:1rem;"></i>
                <p>No customers found.</p>
            </div>
        <?php endif; ?>
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
