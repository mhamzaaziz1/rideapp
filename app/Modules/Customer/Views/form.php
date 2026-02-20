<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<style>
    .edit-container { max-width: 900px; margin: 2rem auto; }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    
    .form-card {
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

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
        padding: 2rem;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        min-height: 150px;
    }
    .file-upload-wrapper:hover { border-color: var(--primary); background: rgba(var(--primary-rgb), 0.05); }
    .file-upload-input {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0; cursor: pointer;
    }
    .preview-container { margin-top: 1rem; display: none; width: 100%; }
    .preview-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid var(--bg-surface); box-shadow: var(--shadow-sm); }
    .current-img-preview { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 4px solid var(--bg-surface); box-shadow: var(--shadow-sm); margin-bottom:1rem; }
    
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
            <a href="<?= base_url('customers') ?>" style="color:var(--text-secondary); font-size:0.9rem; display:flex; align-items:center; gap:4px; margin-top:0.25rem;">
                <i data-lucide="arrow-left" width="16"></i> Back to Customers
            </a>
        </div>
    </div>

    <!-- Error Display -->
    <?php if (session()->has('errors')): ?>
        <div style="background:rgba(239, 68, 68, 0.1); border:1px solid var(--danger); color:var(--danger); padding:1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem; display:flex; align-items:flex-start; gap:1rem;">
            <i data-lucide="alert-circle" style="flex-shrink:0; margin-top:2px;"></i>
            <ul style="margin:0; padding-left:1rem;">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>

    <form action="<?= isset($customer->id) && $customer->id ? base_url('customers/update/' . $customer->id) : base_url('customers/create') ?>" method="post" enctype="multipart/form-data">
        
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:2rem;">
            
            <!-- Left Column: Info -->
            <div class="form-card">
                <style>
                    .form-section-title {
                        font-size: 1.1rem;
                        font-weight: 700;
                        color: var(--text-primary);
                        margin-bottom: 1.5rem;
                        padding-bottom: 0.75rem;
                        border-bottom: 1px solid var(--border-color);
                        display: flex; align-items: center; gap: 0.75rem;
                    }
                </style>
                <h3 class="form-section-title"><i data-lucide="user"></i> Personal Information</h3>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="<?= old('first_name', $customer->first_name ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="<?= old('last_name', $customer->last_name ?? '') ?>" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= old('email', $customer->email ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="<?= old('phone', $customer->phone ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= old('status', $customer->status ?? '') == 'active' ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= old('status', $customer->status ?? '') == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        <option value="banned" <?= old('status', $customer->status ?? '') == 'banned' ? 'selected' : '' ?>>Banned</option>
                    </select>
                </div>
            </div>

            <!-- Right Column: Avatar -->
             <div class="form-card">
                <h3 class="form-section-title" style="font-size:1rem;"><i data-lucide="image"></i> Profile Photo</h3>
                
                <div class="file-upload-wrapper" id="dropzone_avatar">
                    <input type="file" name="avatar" class="file-upload-input" data-preview="preview_avatar" accept="image/*">
                    
                    <?php if(!empty($customer->avatar)): ?>
                         <div class="current-img">
                            <img src="<?= base_url($customer->avatar) ?>" class="current-img-preview">
                            <div style="font-size:0.8rem; color:var(--text-secondary);">Click to change photo</div>
                         </div>
                    <?php else: ?>
                        <div class="placeholder-content">
                            <i data-lucide="camera" class="upload-icon" width="32"></i>
                            <div class="upload-text">Upload Photo</div>
                        </div>
                    <?php endif; ?>

                     <div class="preview-container" id="preview_avatar">
                         <img src="" class="preview-img">
                     </div>
                </div>
             </div>

        </div>

        <div style="margin-top:2rem; padding-top:1.5rem; border-top:1px solid var(--border-color); display:flex; justify-content:flex-end;">
            <a href="<?= base_url('customers') ?>" class="btn" style="margin-right:1rem; color:var(--text-secondary);">Cancel</a>
            <button type="submit" class="btn-submit"><i data-lucide="save" width="18"></i> Save Customer</button>
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
