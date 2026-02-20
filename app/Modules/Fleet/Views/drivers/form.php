<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .edit-container { max-width: 1000px; margin: 2rem auto; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    
    .form-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-color);
        display: flex; align-items: center; gap: 0.75rem;
    }
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; }

    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.5rem; }
    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-sm);
        background: var(--bg-body);
        color: var(--text-primary);
        font-size: 0.95rem;
        transition: border-color 0.15s;
    }
    .form-control:focus, .form-select:focus { border-color: var(--primary); outline: none; }

    /* Custom File Input */
    .file-upload-wrapper {
        position: relative;
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-md);
        background: var(--bg-surface-hover);
        padding: 1.5rem;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 120px;
    }
    .file-upload-wrapper:hover { border-color: var(--primary); background: rgba(var(--primary-rgb), 0.05); }
    .file-upload-input {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0; cursor: pointer;
    }
    .preview-container { margin-top: 1rem; display: none; width: 100%; }
    .preview-img { width: 100%; height: 150px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color); }
    .current-img-preview { width: 100%; height: 150px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border-color); margin-bottom:1rem; }
    
    .upload-icon { color: var(--text-secondary); margin-bottom: 0.5rem; }
    .upload-text { font-size: 0.85rem; color: var(--text-secondary); }
    
    .btn-submit {
        background: var(--primary); color: white; border: none; padding: 0.75rem 2rem;
        border-radius: var(--radius-sm); font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; gap: 0.5rem;
    }
    .btn-submit:hover { opacity: 0.9; }
</style>

<div class="edit-container">
    <div class="page-header">
        <div>
            <h1 style="font-size:1.75rem; font-weight:700; color:var(--text-primary);"><?= esc($title) ?></h1>
            <a href="<?= base_url('drivers') ?>" style="color:var(--text-secondary); font-size:0.9rem; display:flex; align-items:center; gap:4px; margin-top:0.25rem;">
                <i data-lucide="arrow-left" width="16"></i> Back to Drivers List
            </a>
        </div>
        <div>
            <?php if(is_numeric($driver->id)): ?>
            <span class="status-badge status-<?= $driver->status ?>" style="font-size:0.9rem; padding:0.5rem 1rem;"><?= ucfirst($driver->status) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Error Display -->
    <?php if (!empty($errors)): ?>
        <div style="background:rgba(239, 68, 68, 0.1); border:1px solid var(--danger); color:var(--danger); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; display:flex; align-items:flex-start; gap:1rem;">
            <i data-lucide="alert-circle" style="flex-shrink:0; margin-top:2px;"></i>
            <ul style="margin:0; padding-left:1rem;">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form action="<?= is_numeric($driver->id) ? base_url('drivers/update/' . $driver->id) : base_url('drivers/create') ?>" method="post" enctype="multipart/form-data">
        
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem;">
            
            <!-- Left Column: Personal & Vehicle -->
            <div>
                <!-- Personal Info -->
                <div class="form-card">
                    <h3 class="form-section-title"><i data-lucide="user"></i> Personal Information</h3>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $driver->first_name) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $driver->last_name) ?>" required>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= old('email', $driver->email) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?= old('phone', $driver->phone) ?>" required>
                        </div>
                    </div>

                    <div class="grid-2">
                         <div class="form-group">
                            <label class="form-label">License Number</label>
                            <input type="text" name="license_number" class="form-control" value="<?= old('license_number', $driver->license_number) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Account Status</label>
                            <select name="status" class="form-select">
                                <option value="active" <?= old('status', $driver->status) == 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= old('status', $driver->status) == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= old('status', $driver->status) == 'suspended' ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Vehicle Info -->
                <div class="form-card">
                    <h3 class="form-section-title"><i data-lucide="car"></i> Vehicle Information</h3>
                    
                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Make</label>
                            <input type="text" name="vehicle_make" class="form-control" value="<?= old('vehicle_make', $driver->vehicle_make) ?>" placeholder="e.g. Toyota">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Model</label>
                            <input type="text" name="vehicle_model" class="form-control" value="<?= old('vehicle_model', $driver->vehicle_model) ?>" placeholder="e.g. Camry">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Year</label>
                            <input type="number" name="vehicle_year" class="form-control" value="<?= old('vehicle_year', $driver->vehicle_year) ?>" placeholder="2023">
                        </div>
                    </div>

                    <div class="grid-3">
                        <div class="form-group">
                            <label class="form-label">Color</label>
                            <input type="text" name="vehicle_color" class="form-control" value="<?= old('vehicle_color', $driver->vehicle_color) ?>" placeholder="Black">
                        </div>
                         <div class="form-group">
                            <label class="form-label">License Plate</label>
                            <input type="text" name="license_plate" class="form-control" value="<?= old('license_plate', $driver->license_plate) ?>" placeholder="ABC-1234">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select name="vehicle_type" class="form-select">
                                <option value="Sedan" <?= old('vehicle_type', $driver->vehicle_type) == 'Sedan' ? 'selected' : '' ?>>Sedan</option>
                                <option value="SUV" <?= old('vehicle_type', $driver->vehicle_type) == 'SUV' ? 'selected' : '' ?>>SUV</option>
                                <option value="Van" <?= old('vehicle_type', $driver->vehicle_type) == 'Van' ? 'selected' : '' ?>>Van</option>
                                <option value="Luxury" <?= old('vehicle_type', $driver->vehicle_type) == 'Luxury' ? 'selected' : '' ?>>Luxury</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Images & Documents -->
             <div>
                
                <!-- Driver Avatar -->
                <div class="form-card">
                    <h3 class="form-section-title" style="font-size:1rem;"><i data-lucide="image"></i> Driver Photo</h3>
                    <div class="file-upload-wrapper" id="dropzone_avatar">
                        <input type="file" name="avatar" class="file-upload-input" data-preview="preview_avatar" accept="image/*">
                        <?php if($driver->avatar): ?>
                             <div class="current-img" style="width:100%; text-align:center;">
                                <img src="<?= base_url($driver->avatar) ?>" style="width:80px; height:80px; border-radius:50%; object-fit:cover; border:2px solid var(--border-color); margin-bottom:0.5rem;">
                                <div style="font-size:0.75rem; color:var(--text-secondary);">Click to change</div>
                             </div>
                        <?php else: ?>
                            <div class="placeholder-content">
                                <i data-lucide="upload-cloud" class="upload-icon" width="32"></i>
                                <div class="upload-text">Drop photo here or click</div>
                            </div>
                        <?php endif; ?>
                         <div class="preview-container" id="preview_avatar">
                             <img src="" class="preview-img" style="width:100px; height:100px; border-radius:50%;">
                         </div>
                    </div>
                </div>

                 <!-- Vehicle Image -->
                <div class="form-card">
                    <h3 class="form-section-title" style="font-size:1rem;"><i data-lucide="image"></i> Vehicle Photo</h3>
                    <div class="file-upload-wrapper" id="dropzone_vehicle">
                         <input type="file" name="vehicle_image" class="file-upload-input" data-preview="preview_vehicle" accept="image/*">
                         <?php if($driver->vehicle_image): ?>
                             <div class="current-img" style="width:100%;">
                                <img src="<?= base_url($driver->vehicle_image) ?>" class="current-img-preview">
                                <div style="font-size:0.75rem; color:var(--text-secondary); text-align:center; margin-top:-0.5rem;">Click to replace</div>
                             </div>
                        <?php else: ?>
                            <div class="placeholder-content">
                                <i data-lucide="upload-cloud" class="upload-icon" width="32"></i>
                                <div class="upload-text">Drop photo here or click</div>
                            </div>
                        <?php endif; ?>
                        <div class="preview-container" id="preview_vehicle">
                             <img src="" class="preview-img">
                        </div>
                    </div>
                </div>

                <!-- KYC Docs -->
                <div class="form-card">
                    <h3 class="form-section-title" style="font-size:1rem;"><i data-lucide="file-check"></i> KYC Documents</h3>
                    
                    <div class="form-group">
                         <label class="form-label">KYC Status</label>
                         <select name="kyc_status" class="form-select">
                            <option value="pending" <?= old('kyc_status', $driver->kyc_status ?? 'pending') == 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= old('kyc_status', $driver->kyc_status ?? 'pending') == 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= old('kyc_status', $driver->kyc_status ?? 'pending') == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:1rem;">
                        
                        <!-- License Front -->
                        <div class="form-group">
                            <label class="form-label">License Front</label>
                             <div class="file-upload-wrapper" style="padding:1rem; min-height:80px;">
                                <input type="file" name="doc_license_front" class="file-upload-input" data-preview="preview_doc_front" accept="image/*,.pdf">
                                <?php if($driver->doc_license_front): ?>
                                     <div class="current-img" style="display:flex; align-items:center; gap:0.5rem;">
                                        <i data-lucide="check-circle" color="var(--success)" width="16"></i>
                                        <div style="font-size:0.8rem; text-decoration:underline;"><?= basename($driver->doc_license_front) ?></div>
                                     </div>
                                <?php else: ?>
                                    <div class="placeholder-content">
                                        <span class="upload-text">Click to upload</span>
                                    </div>
                                <?php endif; ?>
                                <div class="preview-container" id="preview_doc_front">
                                    <img src="" class="preview-img" style="height:100px;">
                                </div>
                            </div>
                        </div>

                        <!-- License Back -->
                        <div class="form-group">
                            <label class="form-label">License Back</label>
                             <div class="file-upload-wrapper" style="padding:1rem; min-height:80px;">
                                <input type="file" name="doc_license_back" class="file-upload-input" data-preview="preview_doc_back" accept="image/*,.pdf">
                                <?php if($driver->doc_license_back): ?>
                                     <div class="current-img" style="display:flex; align-items:center; gap:0.5rem;">
                                        <i data-lucide="check-circle" color="var(--success)" width="16"></i>
                                        <div style="font-size:0.8rem; text-decoration:underline;"><?= basename($driver->doc_license_back) ?></div>
                                     </div>
                                <?php else: ?>
                                    <div class="placeholder-content">
                                        <span class="upload-text">Click to upload</span>
                                    </div>
                                <?php endif; ?>
                                <div class="preview-container" id="preview_doc_back">
                                    <img src="" class="preview-img" style="height:100px;">
                                </div>
                            </div>
                        </div>

                         <!-- ID Proof -->
                         <div class="form-group">
                            <label class="form-label">ID Proof</label>
                             <div class="file-upload-wrapper" style="padding:1rem; min-height:80px;">
                                <input type="file" name="doc_id_proof" class="file-upload-input" data-preview="preview_doc_id" accept="image/*,.pdf">
                                <?php if($driver->doc_id_proof): ?>
                                     <div class="current-img" style="display:flex; align-items:center; gap:0.5rem;">
                                        <i data-lucide="check-circle" color="var(--success)" width="16"></i>
                                        <div style="font-size:0.8rem; text-decoration:underline;"><?= basename($driver->doc_id_proof) ?></div>
                                     </div>
                                <?php else: ?>
                                    <div class="placeholder-content">
                                        <span class="upload-text">Click to upload</span>
                                    </div>
                                <?php endif; ?>
                                <div class="preview-container" id="preview_doc_id">
                                    <img src="" class="preview-img" style="height:100px;">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

             </div>

        </div>

        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border-color); display:flex; justify-content:flex-end;">
            <a href="<?= base_url('drivers') ?>" class="btn" style="margin-right:1rem; color:var(--text-secondary);">Cancel</a>
            <button type="submit" class="btn-submit"><i data-lucide="save" width="18"></i> Save Changes</button>
        </div>

    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // File Preview Logic
        const fileInputs = document.querySelectorAll('.file-upload-input');
        
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                const previewId = this.getAttribute('data-preview');
                const previewContainer = document.getElementById(previewId);
                const wrapper = this.closest('.file-upload-wrapper');
                const currentImg = wrapper.querySelector('.current-img');
                const placeholder = wrapper.querySelector('.placeholder-content');

                if (file) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                         const img = previewContainer.querySelector('img');
                         if(img) {
                             img.src = e.target.result;
                             previewContainer.style.display = 'block';
                             
                             // Hide other elements
                             if(currentImg) currentImg.style.display = 'none';
                             if(placeholder) placeholder.style.display = 'none';
                         }
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        });
    });
</script>

<?= $this->endSection() ?>
