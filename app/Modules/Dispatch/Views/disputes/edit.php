<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="container-fluid" style="padding: 1.5rem; max-width: 800px; margin: 0 auto;">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        .edit-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 2rem;
        }
        .form-group { margin-bottom: 1.5rem; }
        label { font-size: 0.9rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 6px; display: block; }
        .form-control, .form-select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            background: var(--bg-body);
            color: var(--text-primary);
            font-size: 1rem;
        }
        .form-control:focus, .form-select:focus { border-color: var(--primary); outline: none; }
    </style>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem;">
        <div>
            <h1 class="h3" style="margin:0;">Edit Dispute #DSP-<?= esc($dispute->id) ?></h1>
            <div style="color:var(--text-secondary); font-size:0.95rem;">Update the case description and initial details.</div>
        </div>
        <a href="<?= base_url('admin/disputes/view/'.$dispute->id) ?>" class="btn btn-outline" style="display:flex; align-items:center; gap:8px;">
            <i data-lucide="arrow-left" width="16"></i> Cancel
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div style="background:rgba(239, 68, 68, 0.1); color:var(--danger); padding:1rem; border-radius:8px; margin-bottom:1.5rem;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="edit-card">
        <form action="<?= base_url('admin/disputes/update_details/' . $dispute->id) ?>" method="POST">
            <div class="form-group">
                <label>Dispute Title</label>
                <input type="text" name="title" class="form-control" value="<?= esc($dispute->title) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Problem Type</label>
                <select name="dispute_type" class="form-select" required>
                    <option value="Fare Issue" <?= ($dispute->dispute_type == 'Fare Issue') ? 'selected' : '' ?>>Fare Issue</option>
                    <option value="Conduct" <?= ($dispute->dispute_type == 'Conduct') ? 'selected' : '' ?>>Unprofessional Conduct</option>
                    <option value="Route Issue" <?= ($dispute->dispute_type == 'Route Issue') ? 'selected' : '' ?>>Inefficient Route</option>
                    <option value="Vehicle Condition" <?= ($dispute->dispute_type == 'Vehicle Condition') ? 'selected' : '' ?>>Vehicle Condition</option>
                    <option value="Accident" <?= ($dispute->dispute_type == 'Accident') ? 'selected' : '' ?>>Accident</option>
                    <option value="Lost Item" <?= ($dispute->dispute_type == 'Lost Item') ? 'selected' : '' ?>>Lost Item</option>
                    <option value="Other" <?= ($dispute->dispute_type == 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label>Case Description</label>
                <textarea name="description" class="form-control" rows="8" required><?= esc($dispute->description) ?></textarea>
            </div>

            <div style="text-align: right; margin-top:2rem;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 600; font-size: 1.05rem; display:inline-flex; align-items:center; gap:8px;">
                    <i data-lucide="save" width="18"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    if(typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
<?= $this->endSection() ?>
