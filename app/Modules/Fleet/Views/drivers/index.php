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
        cursor: pointer;
        text-decoration: none;
    }
    .btn-add:hover { background-color: var(--primary-blue-hover); color: white; text-decoration: none; }

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
    
    .driver-cell { display: flex; align-items: center; gap: 1rem; }
    .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: 600; color: var(--text-gray); }
    .driver-name { font-weight: 600; color: var(--text-dark); font-size: 0.95rem; margin-bottom: 0.15rem; }
    .text-muted { color: var(--text-gray); font-size: 0.85rem; }
    
    .contact-cell .text-muted { display: flex; align-items: center; gap: 0.35rem; margin-bottom: 0.25rem; }
    
    .status-badge {
        display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem;
        border-radius: 16px; font-size: 0.8rem; font-weight: 600; cursor: pointer; text-transform: capitalize;
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
            <h1 class="page-title">Driver Management</h1>
            <p class="page-desc">Manage your driver fleet and vehicles.</p>
        </div>
        <a href="#" id="btnAddDriver" class="btn-add">
            <i data-lucide="plus" width="18"></i> Add Driver
        </a>
    </div>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Total Drivers</div>
                <div class="stat-icon icon-blue"><i data-lucide="car" width="18"></i></div>
            </div>
            <div class="stat-value"><?= $total_drivers ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Active</div>
                <div class="stat-icon icon-green"><i data-lucide="check-circle" width="18"></i></div>
            </div>
            <div class="stat-value"><?= $active_drivers ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Inactive</div>
                <div class="stat-icon icon-orange"><i data-lucide="x-circle" width="18"></i></div>
            </div>
            <div class="stat-value"><?= $inactive_drivers ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-title">Total Trips</div>
                <div class="stat-icon icon-purple"><i data-lucide="map-pin" width="18"></i></div>
            </div>
            <div class="stat-value"><?= number_format($total_trips) ?></div>
            <div class="stat-sub">Trips over time <div class="mini-chart"></div></div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
        <div class="toolbar">
            <div>
                <div class="search-wrapper">
                    <i data-lucide="search" width="16" class="search-icon"></i>
                    <input type="text" class="search-input" placeholder="Search by name, phone, or plate...">
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
                    <th style="width: 25%">Driver</th>
                    <th style="width: 25%">Vehicle</th>
                    <th style="width: 15%">Status</th>
                    <th style="width: 20%">Stats</th>
                    <th style="width: 15%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($error)): ?>
                    <tr><td colspan="5" style="color:red; text-align:center;">DB Error: <?= $error ?></td></tr>
                <?php elseif(empty($drivers)): ?>
                    <tr><td colspan="5" style="text-align:center; color:var(--text-gray); padding: 2rem;">No drivers found.</td></tr>
                <?php else: ?>
                    <?php foreach($drivers as $driver): ?>
                    <tr onclick='viewDriver(<?= json_encode($driver) ?>)' style="cursor:pointer">
                        <td>
                            <div class="driver-cell">
                                <?php if($driver->avatar): ?>
                                    <img src="<?= base_url($driver->avatar) ?>" class="avatar">
                                <?php else: ?>
                                    <div class="avatar"><?= strtoupper(substr($driver->first_name, 0, 1) . substr($driver->last_name, 0, 1)) ?></div>
                                <?php endif; ?>
                                <div>
                                    <div class="driver-name"><?= esc($driver->first_name . ' ' . $driver->last_name) ?></div>
                                    <div class="text-muted"><i data-lucide="phone" width="12"></i> <?= esc($driver->phone) ?></div>
                                    <div class="text-muted" style="margin-top:2px;"><i data-lucide="mail" width="12"></i> <?= esc($driver->email) ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="contact-cell">
                            <div class="text-muted" style="color:var(--text-dark); font-weight:600;"><i data-lucide="car" width="14"></i> <?= esc($driver->vehicle_year . ' ' . $driver->vehicle_make . ' ' . $driver->vehicle_model) ?></div>
                            <div class="text-muted" style="margin-top:4px;">Plate: <?= esc($driver->license_plate) ?> • <?= esc($driver->vehicle_type) ?></div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="status-badge status-<?= $driver->status ?>">
                                <?= $driver->status ?>
                            </div>
                        </td>
                        <td>
                            <div class="stats-cell">
                                <div class="stat-item"><i data-lucide="route" width="14" style="color:var(--text-gray)"></i> <?= $driver->total_trips ?> Trips</div>
                            </div>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="actions-cell">
                                <a href="<?= base_url('drivers/profile/'.$driver->id) ?>" class="action-icon" title="Profile"><i data-lucide="user-circle" width="18"></i></a>
                                <a href="<?= base_url('drivers/edit/'.$driver->id) ?>" class="action-icon" title="Edit"><i data-lucide="edit-3" width="18"></i></a>
                                <a href="<?= base_url('drivers/delete/'.$driver->id) ?>" onclick="return confirm('Are you sure?')" class="action-icon delete" title="Delete"><i data-lucide="trash-2" width="18"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>


    <!-- Add Driver Modal -->
    <div id="addDriverModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="h3" style="font-size:1.25rem; margin:0;">Add New Driver</h2>
                <button class="close-modal"><i data-lucide="x" width="20"></i></button>
            </div>
            
            <form action="<?= base_url('drivers/create') ?>" method="post" id="addDriverForm" enctype="multipart/form-data">
                <div class="modal-body" style="display:flex; flex-direction:column; gap:1.5rem; max-height:70vh; overflow-y:auto; padding-right:0.5rem;">
                    
                    <!-- Personal Info -->
                    <div>
                        <h4 style="font-size:0.95rem; font-weight:600; margin-bottom:1rem; color:var(--primary); text-transform:uppercase; letter-spacing:0.05em;">Personal Details</h4>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="form-group" style="grid-column: span 2;">
                                <label class="form-label">License Number</label>
                                <input type="text" name="license_number" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Driver Photo</label>
                                <input type="file" name="avatar" class="form-control" style="font-size:0.8rem;">
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Info -->
                    <div>
                        <h4 style="font-size:0.95rem; font-weight:600; margin-bottom:1rem; color:var(--primary); text-transform:uppercase; letter-spacing:0.05em;">Vehicle Details</h4>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                            <div class="form-group">
                                <label class="form-label">Make</label>
                                <input type="text" name="vehicle_make" class="form-control" placeholder="Toyota">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Model</label>
                                <input type="text" name="vehicle_model" class="form-control" placeholder="Camry">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Year</label>
                                <input type="number" name="vehicle_year" class="form-control" placeholder="2023">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Color</label>
                                <input type="text" name="vehicle_color" class="form-control" placeholder="Black">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Type</label>
                                <select name="vehicle_type" class="form-select">
                                    <option value="Sedan">Sedan</option>
                                    <option value="SUV">SUV</option>
                                    <option value="Van">Van</option>
                                    <option value="Luxury">Luxury</option>
                                </select>
                            </div>
                             <div class="form-group">
                                <label class="form-label">Plate</label>
                                <input type="text" name="license_plate" class="form-control" placeholder="ABC-1234">
                            </div>
                             <div class="form-group">
                                <label class="form-label">Car Image</label>
                                <input type="file" name="vehicle_image" class="form-control" style="font-size:0.8rem;">
                            </div>
                        </div>
                    </div>

                    <!-- KYC & Documents -->
                    <div>
                        <h4 style="font-size:0.95rem; font-weight:600; margin-bottom:1rem; color:var(--primary); text-transform:uppercase; letter-spacing:0.05em;">KYC & Documents</h4>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1rem;">
                             <div class="form-group">
                                <label class="form-label">KYC Status</label>
                                <select name="kyc_status" class="form-select">
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
                            <div class="form-group">
                                <label class="form-label">License Front</label>
                                <input type="file" name="doc_license_front" class="form-control" style="padding:0.5rem; font-size:0.8rem;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">License Back</label>
                                <input type="file" name="doc_license_back" class="form-control" style="padding:0.5rem; font-size:0.8rem;">
                            </div>
                            <div class="form-group">
                                <label class="form-label">ID / Passport</label>
                                <input type="file" name="doc_id_proof" class="form-control" style="padding:0.5rem; font-size:0.8rem;">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="padding-top:1.5rem; border-top:1px solid var(--border-color); display:flex; justify-content:flex-end; gap:1rem; margin-top:1rem;">
                    <button type="button" class="btn btn-secondary close-modal-btn">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Driver</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Styles for Modal -->
    <style>
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6); z-index: 1000;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background: var(--bg-surface);
            padding: 2rem;
            border-radius: var(--radius-md);
            width: 700px;
            max-width: 90%;
            box-shadow: var(--shadow-lg);
            transform: scale(0.95);
            opacity: 0;
            transition: all 0.2s ease-out;
            border: 1px solid var(--border-color);
            display: flex; flex-direction: column;
            max-height: 90vh;
        }
        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 1.5rem; flex-shrink: 0;
        }
        .close-modal {
            background: transparent; border: none; cursor: pointer; color: var(--text-secondary);
        }
        .close-modal:hover { color: var(--text-primary); }
        .form-label { display: block; font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight:600; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
             // Add Driver Modal Logic
             const addModal = document.getElementById('addDriverModal');
             const addBtn = document.getElementById('btnAddDriver');
             
             if(addBtn) {
                 addBtn.addEventListener('click', (e) => {
                     e.preventDefault();
                     openModal(addModal);
                 });
             }

             // Generic Close Logic
             const closeBtns = document.querySelectorAll('.close-modal, .close-modal-btn');
             closeBtns.forEach(b => {
                 b.addEventListener('click', () => {
                     const modal = b.closest('.modal-overlay');
                     if(modal) closeModal(modal);
                 });
             });

             window.onclick = function(event) {
                if (event.target.classList.contains('modal-overlay')) {
                    closeModal(event.target);
                }
            }
        });

        function openModal(modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.querySelector('.modal-content').style.transform = 'scale(1)';
                modal.querySelector('.modal-content').style.opacity = '1';
            }, 10);
        }

        function closeModal(modal) {
            modal.querySelector('.modal-content').style.transform = 'scale(0.95)';
            modal.querySelector('.modal-content').style.opacity = '0';
            setTimeout(() => modal.style.display = 'none', 200);
        }

        // View Driver Logic
        window.viewDriver = function(driver) {
            const modal = document.getElementById('viewDriverModal');
            
            // Populate fields
            document.getElementById('view_name').innerText = driver.first_name + ' ' + driver.last_name;
            document.getElementById('view_email').innerText = driver.email;
            document.getElementById('view_phone').innerText = driver.phone;
            document.getElementById('view_license').innerText = driver.license_number;
            document.getElementById('view_status').innerHTML = `<span class="status-badge status-${driver.status}">${driver.status}</span>`;
            
            // Vehicle
            document.getElementById('view_vehicle').innerText = `${driver.vehicle_year || ''} ${driver.vehicle_make || ''} ${driver.vehicle_model || ''}`;
            document.getElementById('view_plate').innerText = driver.license_plate || '-';
            document.getElementById('view_vtype').innerText = driver.vehicle_type || '-';
            document.getElementById('view_vcolor').innerText = driver.vehicle_color || '-';

            // KYC
            document.getElementById('view_kyc_status').innerHTML = `<span class="badge" style="background:${driver.kyc_status === 'approved' ? 'var(--success)' : (driver.kyc_status === 'rejected' ? 'var(--danger)' : 'var(--warning)')}; color:${driver.kyc_status === 'approved' ? '#fff' : '#000'}">${driver.kyc_status || 'Pending'}</span>`;

            // Images
            const avatarEl = document.getElementById('view_avatar_img');
            if(driver.avatar) {
                avatarEl.src = '<?= base_url() ?>/' + driver.avatar;
                avatarEl.style.display = 'block';
                document.getElementById('view_avatar_placeholder').style.display = 'none';
            } else {
                avatarEl.style.display = 'none';
                document.getElementById('view_avatar_placeholder').innerText = driver.first_name.charAt(0) + driver.last_name.charAt(0);
                document.getElementById('view_avatar_placeholder').style.display = 'flex';
            }

            const carEl = document.getElementById('view_car_img');
            if(driver.vehicle_image) {
                    carEl.src = '<?= base_url() ?>/' + driver.vehicle_image;
                    carEl.style.display = 'block';
            } else {
                    carEl.style.display = 'none';
            }

            // Docs Links
            // Helper to set doc info
            const setDoc = (prefix, path) => {
                const row = document.getElementById('row_' + prefix);
                const thumb = document.getElementById('thumb_' + prefix);
                const icon = document.getElementById('icon_' + prefix);
                const btnView = document.getElementById('btn_view_' + prefix);
                const btnDl = document.getElementById('btn_dl_' + prefix);

                if(path) {
                    const fullUrl = '<?= base_url() ?>/' + path;
                    
                    // Show row (should be visible but just in case)
                    // row.style.display = 'grid';

                    // Update buttons
                    btnView.href = fullUrl;
                    btnDl.href = fullUrl;

                    // Check extension for thumbnail
                    const ext = path.split('.').pop().toLowerCase();
                    if(['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext)) {
                        thumb.src = fullUrl;
                        thumb.style.display = 'block';
                        icon.style.display = 'none';
                    } else {
                         thumb.style.display = 'none';
                         icon.style.display = 'block';
                    }
                    
                    // Enable buttons
                    btnView.style.pointerEvents = 'auto'; btnView.style.opacity = '1';
                    btnDl.style.pointerEvents = 'auto'; btnDl.style.opacity = '1';

                } else {
                    // Disable buttons or show "Not Uploaded"
                    thumb.style.display = 'none';
                    icon.style.display = 'block';
                    
                    btnView.href = '#'; btnView.style.pointerEvents = 'none'; btnView.style.opacity = '0.5';
                    btnDl.href = '#'; btnDl.style.pointerEvents = 'none'; btnDl.style.opacity = '0.5';
                }
            };

            setDoc('avatar', driver.avatar);
            setDoc('vehicle_image', driver.vehicle_image);
            setDoc('doc_license_front', driver.doc_license_front);
            setDoc('doc_license_back', driver.doc_license_back);
            setDoc('doc_id_proof', driver.doc_id_proof);

            // Set Statuses
            window.currentDriverId = driver.id; // Store for AJAX
            document.getElementById('view_profile_link').href = '<?= base_url('drivers/profile') ?>/' + driver.id;

            updateStatusBadge('status_doc_license_front', driver.doc_license_front_status || 'pending');
            updateStatusBadge('status_doc_license_back', driver.doc_license_back_status || 'pending');
            updateStatusBadge('status_doc_id_proof', driver.doc_id_proof_status || 'pending');

            openModal(modal);
        }

        function updateStatusBadge(id, status) {
            const el = document.getElementById(id);
            el.innerText = status;
            el.className = 'status-badge status-' + status;
            // You might want to add specfic styling for these statuses in CSS if not present
            if(status === 'approved') el.style.color = 'var(--success)';
            else if(status === 'rejected') el.style.color = 'var(--danger)';
            else el.style.color = 'var(--text-secondary)';
        }

        window.updateDocStatus = function(field, status) {
            if(!status) return;
            if(!confirm('Are you sure you want to change the status to ' + status + '?')) return;

            const formData = new FormData();
            formData.append('driver_id', window.currentDriverId);
            formData.append('doc_field', field);
            formData.append('status', status);

            fetch('<?= base_url("drivers/update_doc_status") ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Update badge
                    const badgeId = 'status_' + field.replace('_status', ''); // e.g. status_doc_license_front
                    // Actually the field passed is the column name e.g. doc_license_front_status
                    // My ID convention in HTML is 'status_doc_license_front'
                    // So I need to map: doc_license_front_status -> status_doc_license_front
                    const domId = 'status_' + field.replace('_status', '');
                    updateStatusBadge(domId, status);
                    // alert('Status updated successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
        }
    </script>

    <!-- View Driver Modal -->
    <div id="viewDriverModal" class="modal-overlay" style="display:none;">
        <div class="modal-content" style="width:800px;">
             <div class="modal-header">
                <h2 class="h3" style="font-size:1.25rem; margin:0;">Driver Details</h2>
                <button class="close-modal"><i data-lucide="x" width="20"></i></button>
            </div>
            
            <div class="modal-body" style="max-height:80vh; overflow-y:auto; padding-right:0.5rem;">
                <!-- Header with Avatar -->
                <div style="display:flex; gap:1.5rem; align-items:center; margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid var(--border-color);">
                     <div style="width:80px; height:80px; border-radius:50%; overflow:hidden; border:1px solid var(--border-color); flex-shrink:0;">
                        <img id="view_avatar_img" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                        <div id="view_avatar_placeholder" style="width:100%; height:100%; background:var(--bg-surface-hover); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:1.5rem; color:var(--text-secondary);"></div>
                     </div>
                     <div>
                         <h3 id="view_name" style="margin:0; margin-bottom:0.5rem; font-size:1.5rem;"></h3>
                         <div id="view_status"></div>
                     </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem;">
                    <!-- Contact Info -->
                    <div>
                        <h4 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); margin-bottom:1rem;">Contact Info</h4>
                        <div style="display:grid; grid-template-columns: auto 1fr; gap:0.5rem 1rem; align-items:center; font-size:0.95rem;">
                             <i data-lucide="mail" width="16" style="color:var(--text-secondary)"></i> <span id="view_email"></span>
                             <i data-lucide="phone" width="16" style="color:var(--text-secondary)"></i> <span id="view_phone"></span>
                             <i data-lucide="file-text" width="16" style="color:var(--text-secondary)"></i> <span id="view_license"></span>
                        </div>
                    </div>
                     <!-- Vehicle Info -->
                    <div>
                        <h4 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); margin-bottom:1rem;">Vehicle</h4>
                         <div style="display:grid; grid-template-columns: auto 1fr; gap:0.5rem 1rem; align-items:center; font-size:0.95rem;">
                             <span style="color:var(--text-secondary)">Car:</span> <span id="view_vehicle" style="font-weight:600;"></span>
                             <span style="color:var(--text-secondary)">Plate:</span> <span id="view_plate"></span>
                             <span style="color:var(--text-secondary)">Type:</span> <span id="view_vtype"></span>
                             <span style="color:var(--text-secondary)">Color:</span> <span id="view_vcolor"></span>
                        </div>
                        <div style="margin-top:1rem;">
                            <img id="view_car_img" src="" style="width:100%; height:120px; object-fit:cover; border-radius:8px; display:none;">
                        </div>
                    </div>
                </div>
                
                 <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border-color);">
                     <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                        <h4 style="font-size:0.9rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-secondary); margin:0;">KYC & Documents</h4>
                         <div id="view_kyc_status"></div>
                     </div>
                     <div style="display:grid; grid-template-columns: 1fr; gap:1rem;">
                         <!-- Driver Photo -->
                         <div id="row_avatar" class="doc-item" style="display:grid; grid-template-columns: 60px 1fr auto; gap:1rem; align-items:center; padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md); transition:box-shadow 0.2s;">
                            <!-- Thumbnail -->
                            <div style="width:60px; height:60px; background:var(--bg-surface-hover); border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center;">
                                <img id="thumb_avatar" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="icon_avatar" data-lucide="user" width="24" style="color:var(--text-secondary);"></i>
                            </div>
                            
                            <!-- Meta -->
                            <div>
                                <div style="font-size:0.95rem; font-weight:600; margin-bottom:0.25rem;">Driver Photo</div>
                                <div style="display:flex; gap:0.5rem;">
                                     <a id="btn_view_avatar" href="#" target="_blank" class="btn-xs btn-outline" title="View"><i data-lucide="eye" width="14" style="margin-right:4px;"></i> View</a>
                                     <a id="btn_dl_avatar" href="#" download class="btn-xs btn-outline" title="Download"><i data-lucide="download" width="14" style="margin-right:4px;"></i> Download</a>
                                </div>
                            </div>

                            <!-- Status & Action -->
                            <div style="text-align:right;">
                                <!-- No status for avatar currently -->
                                <span class="status-badge" style="background:var(--bg-surface-hover); color:var(--text-secondary);">Profile</span>
                            </div>
                         </div>

                         <!-- Vehicle Photo -->
                         <div id="row_vehicle_image" class="doc-item" style="display:grid; grid-template-columns: 60px 1fr auto; gap:1rem; align-items:center; padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md); transition:box-shadow 0.2s;">
                            <!-- Thumbnail -->
                            <div style="width:60px; height:60px; background:var(--bg-surface-hover); border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center;">
                                <img id="thumb_vehicle_image" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="icon_vehicle_image" data-lucide="car" width="24" style="color:var(--text-secondary);"></i>
                            </div>
                            
                            <!-- Meta -->
                            <div>
                                <div style="font-size:0.95rem; font-weight:600; margin-bottom:0.25rem;">Vehicle Photo</div>
                                <div style="display:flex; gap:0.5rem;">
                                     <a id="btn_view_vehicle_image" href="#" target="_blank" class="btn-xs btn-outline" title="View"><i data-lucide="eye" width="14" style="margin-right:4px;"></i> View</a>
                                     <a id="btn_dl_vehicle_image" href="#" download class="btn-xs btn-outline" title="Download"><i data-lucide="download" width="14" style="margin-right:4px;"></i> Download</a>
                                </div>
                            </div>

                            <!-- Status & Action -->
                            <div style="text-align:right;">
                                <!-- No status for vehicle image currently -->
                                <span class="status-badge" style="background:var(--bg-surface-hover); color:var(--text-secondary);">Vehicle</span>
                            </div>
                         </div>

                         <!-- License Front -->
                         <div id="row_doc_license_front" class="doc-item" style="display:grid; grid-template-columns: 60px 1fr auto; gap:1rem; align-items:center; padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md); transition:box-shadow 0.2s;">
                            <!-- Thumbnail -->
                            <div style="width:60px; height:60px; background:var(--bg-surface-hover); border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center;">
                                <img id="thumb_doc_license_front" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="icon_doc_license_front" data-lucide="file" width="24" style="color:var(--text-secondary);"></i>
                            </div>
                            
                            <!-- Meta -->
                            <div>
                                <div style="font-size:0.95rem; font-weight:600; margin-bottom:0.25rem;">License Front</div>
                                <div style="display:flex; gap:0.5rem;">
                                     <a id="btn_view_doc_license_front" href="#" target="_blank" class="btn-xs btn-outline" title="View"><i data-lucide="eye" width="14" style="margin-right:4px;"></i> View</a>
                                     <a id="btn_dl_doc_license_front" href="#" download class="btn-xs btn-outline" title="Download"><i data-lucide="download" width="14" style="margin-right:4px;"></i> Download</a>
                                </div>
                            </div>

                            <!-- Status & Action -->
                            <div style="text-align:right;">
                                <div style="margin-bottom:0.5rem;"><span id="status_doc_license_front" class="status-badge">Pending</span></div>
                                <select onchange="updateDocStatus('doc_license_front_status', this.value)" style="font-size:0.8rem; padding:0.25rem 0.5rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface); cursor:pointer;">
                                    <option value="">Change Status...</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                         </div>

                         <!-- License Back -->
                         <div id="row_doc_license_back" class="doc-item" style="display:grid; grid-template-columns: 60px 1fr auto; gap:1rem; align-items:center; padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md); transition:box-shadow 0.2s;">
                            <div style="width:60px; height:60px; background:var(--bg-surface-hover); border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center;">
                                <img id="thumb_doc_license_back" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="icon_doc_license_back" data-lucide="file" width="24" style="color:var(--text-secondary);"></i>
                            </div>
                            <div>
                                <div style="font-size:0.95rem; font-weight:600; margin-bottom:0.25rem;">License Back</div>
                                <div style="display:flex; gap:0.5rem;">
                                     <a id="btn_view_doc_license_back" href="#" target="_blank" class="btn-xs btn-outline" title="View"><i data-lucide="eye" width="14" style="margin-right:4px;"></i> View</a>
                                     <a id="btn_dl_doc_license_back" href="#" download class="btn-xs btn-outline" title="Download"><i data-lucide="download" width="14" style="margin-right:4px;"></i> Download</a>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="margin-bottom:0.5rem;"><span id="status_doc_license_back" class="status-badge">Pending</span></div>
                                <select onchange="updateDocStatus('doc_license_back_status', this.value)" style="font-size:0.8rem; padding:0.25rem 0.5rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface); cursor:pointer;">
                                    <option value="">Change Status...</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                         </div>

                         <!-- ID Proof -->
                         <div id="row_doc_id_proof" class="doc-item" style="display:grid; grid-template-columns: 60px 1fr auto; gap:1rem; align-items:center; padding:1rem; border:1px solid var(--border-color); border-radius:var(--radius-md); transition:box-shadow 0.2s;">
                            <div style="width:60px; height:60px; background:var(--bg-surface-hover); border-radius:var(--radius-sm); overflow:hidden; border:1px solid var(--border-color); display:flex; align-items:center; justify-content:center;">
                                <img id="thumb_doc_id_proof" src="" style="width:100%; height:100%; object-fit:cover; display:none;">
                                <i id="icon_doc_id_proof" data-lucide="file" width="24" style="color:var(--text-secondary);"></i>
                            </div>
                            <div>
                                <div style="font-size:0.95rem; font-weight:600; margin-bottom:0.25rem;">ID Proof</div>
                                <div style="display:flex; gap:0.5rem;">
                                     <a id="btn_view_doc_id_proof" href="#" target="_blank" class="btn-xs btn-outline" title="View"><i data-lucide="eye" width="14" style="margin-right:4px;"></i> View</a>
                                     <a id="btn_dl_doc_id_proof" href="#" download class="btn-xs btn-outline" title="Download"><i data-lucide="download" width="14" style="margin-right:4px;"></i> Download</a>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="margin-bottom:0.5rem;"><span id="status_doc_id_proof" class="status-badge">Pending</span></div>
                                <select onchange="updateDocStatus('doc_id_proof_status', this.value)" style="font-size:0.8rem; padding:0.25rem 0.5rem; border:1px solid var(--border-color); border-radius:var(--radius-sm); background:var(--bg-surface); cursor:pointer;">
                                    <option value="">Change Status...</option>
                                    <option value="approved">Approve</option>
                                    <option value="rejected">Reject</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                         </div>
                     </div>

                     <!-- Styles for Doc Items -->
                     <style>
                        .doc-item:hover { box-shadow: var(--shadow-md); border-color:var(--primary); }
                        .btn-xs { padding: 4px 10px; font-size: 0.75rem; border-radius: 4px; display: inline-flex; align-items: center; text-decoration: none; font-weight: 600; }
                        .btn-outline { border: 1px solid var(--border-color); color: var(--text-primary); background: transparent; }
                        .btn-outline:hover { background: var(--bg-surface-hover); border-color: var(--text-secondary); }
                     </style>
                     </style>
                 </div>

                 <a href="#" id="view_profile_link" class="btn btn-primary btn-block" style="margin-top:2rem; width:100%; display:block; text-align:center;">View Full Profile & Wallet</a>

            </div>
        </div>
    </div>

<?= $this->endSection() ?>
