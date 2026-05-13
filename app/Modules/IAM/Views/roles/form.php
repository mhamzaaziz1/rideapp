<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>

<?php $isEdit = isset($role) && $role !== null; ?>

<div style="padding: 2rem; max-width: 1200px; margin: 0 auto;">
    
    <div style="margin-bottom: 2rem;">
        <a href="<?= base_url('roles') ?>" style="color:var(--text-secondary); display:inline-flex; align-items:center; gap:4px; font-size:0.9rem; margin-bottom:1rem; text-decoration:none;">
            <i data-lucide="arrow-left" width="16"></i> Back to Roles
        </a>
        <h1 class="h3"><?= esc($title) ?></h1>
    </div>

    <!-- Flash Messages -->
    <?php if(session()->getFlashdata('errors')): ?>
    <div style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#ef4444; padding:0.75rem 1rem; border-radius:var(--radius-sm); margin-bottom:1.5rem;">
        <ul style="margin:0; padding-left:1.5rem;">
            <?php foreach(session()->getFlashdata('errors') as $err): ?>
            <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form action="<?= $isEdit ? base_url('roles/update/'.$role->id) : base_url('roles/create') ?>" method="post">
        <div style="display:grid; grid-template-columns: 1fr 320px; gap:2rem;">
            
            <!-- Left: Permission Matrix -->
            <div>
                <div class="card" style="padding:1.5rem;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.25rem;">
                        <h4 style="margin:0; color:var(--text-primary); font-size:1rem; font-weight:700;">
                            <i data-lucide="key-round" width="18" style="vertical-align:middle; margin-right:6px; color:var(--primary);"></i>
                            Permission Matrix
                        </h4>
                        <div style="display:flex; gap:0.5rem;">
                            <button type="button" onclick="selectAll()" class="perm-toggle-btn">Select All</button>
                            <button type="button" onclick="deselectAll()" class="perm-toggle-btn">Deselect All</button>
                        </div>
                    </div>

                    <!-- Search -->
                    <div style="margin-bottom:1.25rem;">
                        <input type="text" id="permSearch" class="form-control" placeholder="Search permissions..." style="font-size:0.875rem;" oninput="filterPerms(this.value)">
                    </div>

                    <!-- Permission Groups by Module -->
                    <?php foreach($groupedPerms as $module => $resources): ?>
                    <div class="perm-module" style="margin-bottom:1.5rem;">
                        <div class="perm-module-header" onclick="toggleModule(this)" style="cursor:pointer; display:flex; align-items:center; justify-content:space-between; padding:0.75rem 1rem; background:var(--bg-surface-hover); border-radius:var(--radius-sm); margin-bottom:0.5rem;">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <i data-lucide="chevron-down" width="16" class="module-chevron" style="transition:transform 0.2s;"></i>
                                <span style="font-weight:700; font-size:0.9rem; color:var(--text-primary);"><?= esc($module) ?></span>
                                <span class="module-count" style="background:rgba(99,102,241,0.15); color:var(--primary); padding:1px 6px; border-radius:4px; font-size:0.7rem; font-weight:600;"></span>
                            </div>
                            <label style="display:flex; align-items:center; gap:6px; font-size:0.75rem; color:var(--text-secondary); cursor:pointer;" onclick="event.stopPropagation();">
                                <input type="checkbox" class="module-select-all" data-module="<?= esc($module) ?>" onchange="toggleModuleAll(this, '<?= esc($module) ?>')"> All
                            </label>
                        </div>

                        <div class="perm-module-body" style="padding-left:0.5rem;">
                            <?php foreach($resources as $resource => $perms): ?>
                            <div class="perm-resource" style="margin-bottom:0.75rem;">
                                <div style="font-weight:600; font-size:0.8rem; color:var(--text-secondary); text-transform:uppercase; letter-spacing:0.5px; padding:0.4rem 0.5rem; display:flex; align-items:center; gap:6px;">
                                    <i data-lucide="folder" width="12"></i>
                                    <?= esc($resource) ?>
                                </div>
                                <div style="display:flex; flex-wrap:wrap; gap:0.4rem; padding-left:1.25rem;">
                                    <?php foreach($perms as $perm): ?>
                                    <?php 
                                        $action = basename(str_replace('.', '/', $perm->name));
                                        $isChecked = in_array($perm->id, $assignedPermIds);
                                        $actionColor = match($action) {
                                            'create'  => '#22c55e',
                                            'view'    => '#3b82f6',
                                            'edit', 'update_status' => '#f59e0b',
                                            'delete'  => '#ef4444',
                                            'print', 'export' => '#06b6d4',
                                            default   => '#8b5cf6',
                                        };
                                    ?>
                                    <label class="perm-chip <?= $isChecked ? 'perm-chip-active' : '' ?>" data-module="<?= esc($module) ?>" data-name="<?= esc($perm->name) ?>" style="--chip-color:<?= $actionColor ?>;">
                                        <input type="checkbox" name="permissions[]" value="<?= $perm->id ?>" <?= $isChecked ? 'checked' : '' ?> class="perm-check" data-module="<?= esc($module) ?>" onchange="updateChip(this)" style="display:none;">
                                        <span class="perm-chip-dot"></span>
                                        <span class="perm-chip-label"><?= ucfirst(str_replace('_', ' ', $action)) ?></span>
                                        <span class="perm-chip-tooltip"><?= esc($perm->description ?? $perm->name) ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Role Details & Actions -->
            <div style="display:flex; flex-direction:column; gap:1.5rem;">
                
                <!-- Role Details Card -->
                <div class="card" style="padding:1.5rem;">
                    <h4 style="margin-bottom:1.25rem; color:var(--text-primary); font-size:0.95rem; font-weight:700;">
                        <i data-lucide="info" width="16" style="vertical-align:middle; margin-right:6px; color:var(--primary);"></i>
                        Role Details
                    </h4>
                    
                    <div class="form-group" style="margin-bottom:1.25rem;">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" value="<?= old('name', $role->name ?? '') ?>" required placeholder="e.g. Dispatcher" <?= ($isEdit && !empty($role->is_system)) ? 'readonly' : '' ?>>
                        <?php if($isEdit && !empty($role->is_system)): ?>
                        <div style="font-size:0.75rem; color:var(--text-secondary); margin-top:4px;">System role names cannot be changed</div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group" style="margin-bottom:1.25rem;">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Describe the purpose of this role..."><?= old('description', $role->description ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:0.9rem; color:var(--text-primary);">
                            <input type="checkbox" name="is_default" value="1" <?= old('is_default', $role->is_default ?? 0) ? 'checked' : '' ?>>
                            Set as default role for new users
                        </label>
                    </div>
                </div>

                <!-- Summary Card -->
                <div class="card" style="padding:1.5rem;">
                    <h4 style="margin-bottom:1rem; color:var(--text-primary); font-size:0.95rem; font-weight:700;">
                        <i data-lucide="bar-chart-3" width="16" style="vertical-align:middle; margin-right:6px; color:var(--primary);"></i>
                        Summary
                    </h4>
                    <div id="permSummary" style="font-size:0.875rem; color:var(--text-secondary); line-height:1.7;">
                        <div>Selected: <strong id="selectedCount" style="color:var(--text-primary);">0</strong> permissions</div>
                        <div id="moduleSummary"></div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="card" style="padding:1.5rem; text-align:center;">
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-bottom:0.75rem;">
                        <i data-lucide="<?= $isEdit ? 'save' : 'plus' ?>" width="16" style="margin-right:6px;"></i>
                        <?= $isEdit ? 'Update Role' : 'Create Role' ?>
                    </button>
                    <a href="<?= base_url('roles') ?>" style="color:var(--text-secondary); font-size:0.9rem; text-decoration:none;">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .perm-toggle-btn {
        background:var(--bg-surface-hover); border:1px solid var(--border-color); color:var(--text-secondary);
        padding:4px 10px; border-radius:4px; font-size:0.75rem; cursor:pointer; transition:all 0.15s;
    }
    .perm-toggle-btn:hover { color:var(--primary); border-color:var(--primary); }

    .perm-chip {
        display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:6px;
        font-size:0.78rem; cursor:pointer; transition:all 0.15s; position:relative;
        background:var(--bg-surface-hover); border:1px solid var(--border-color); color:var(--text-secondary);
    }
    .perm-chip:hover { border-color:var(--chip-color); }
    .perm-chip-active { background:color-mix(in srgb, var(--chip-color) 12%, transparent); border-color:var(--chip-color); color:var(--chip-color); }
    .perm-chip-dot { width:6px; height:6px; border-radius:50%; background:var(--border-color); transition:all 0.15s; }
    .perm-chip-active .perm-chip-dot { background:var(--chip-color); box-shadow:0 0 6px var(--chip-color); }
    .perm-chip-label { font-weight:500; }
    .perm-chip-tooltip {
        display:none; position:absolute; bottom:calc(100% + 6px); left:50%; transform:translateX(-50%);
        background:var(--bg-elevated, #1e1e2e); color:var(--text-primary); padding:4px 8px;
        border-radius:4px; font-size:0.7rem; white-space:nowrap; z-index:10; pointer-events:none;
        box-shadow:0 2px 8px rgba(0,0,0,0.3);
    }
    .perm-chip:hover .perm-chip-tooltip { display:block; }

    .perm-module-body { max-height:1000px; overflow:hidden; transition:max-height 0.3s ease; }
    .perm-module.collapsed .perm-module-body { max-height:0; }
    .perm-module.collapsed .module-chevron { transform:rotate(-90deg); }
</style>

<script>
function updateChip(checkbox) {
    const chip = checkbox.closest('.perm-chip');
    if (checkbox.checked) {
        chip.classList.add('perm-chip-active');
    } else {
        chip.classList.remove('perm-chip-active');
    }
    updateSummary();
    updateModuleSelectAll(checkbox.dataset.module);
}

function selectAll() {
    document.querySelectorAll('.perm-check').forEach(cb => {
        cb.checked = true;
        updateChip(cb);
    });
}

function deselectAll() {
    document.querySelectorAll('.perm-check').forEach(cb => {
        cb.checked = false;
        updateChip(cb);
    });
}

function toggleModule(header) {
    header.closest('.perm-module').classList.toggle('collapsed');
}

function toggleModuleAll(masterCb, moduleName) {
    const checks = document.querySelectorAll(`.perm-check[data-module="${moduleName}"]`);
    checks.forEach(cb => {
        cb.checked = masterCb.checked;
        updateChip(cb);
    });
}

function updateModuleSelectAll(moduleName) {
    const checks = document.querySelectorAll(`.perm-check[data-module="${moduleName}"]`);
    const masterCb = document.querySelector(`.module-select-all[data-module="${moduleName}"]`);
    if (masterCb) {
        masterCb.checked = [...checks].every(cb => cb.checked);
        masterCb.indeterminate = !masterCb.checked && [...checks].some(cb => cb.checked);
    }
}

function filterPerms(query) {
    query = query.toLowerCase();
    document.querySelectorAll('.perm-chip').forEach(chip => {
        const name = chip.dataset.name.toLowerCase();
        const label = chip.querySelector('.perm-chip-label').textContent.toLowerCase();
        chip.style.display = (name.includes(query) || label.includes(query) || query === '') ? '' : 'none';
    });
    // Show/hide modules
    document.querySelectorAll('.perm-module').forEach(mod => {
        const visibleChips = mod.querySelectorAll('.perm-chip:not([style*="display: none"])');
        mod.style.display = visibleChips.length > 0 ? '' : 'none';
    });
}

function updateSummary() {
    const checked = document.querySelectorAll('.perm-check:checked');
    document.getElementById('selectedCount').textContent = checked.length;
    
    // Module breakdown
    const moduleCounts = {};
    checked.forEach(cb => {
        const mod = cb.dataset.module;
        moduleCounts[mod] = (moduleCounts[mod] || 0) + 1;
    });
    
    let html = '';
    for (const [mod, count] of Object.entries(moduleCounts)) {
        html += `<div style="display:flex;justify-content:space-between;"><span>${mod}</span><span style="color:var(--primary);font-weight:600;">${count}</span></div>`;
    }
    document.getElementById('moduleSummary').innerHTML = html;

    // Update module header counts
    document.querySelectorAll('.perm-module').forEach(mod => {
        const moduleChecks = mod.querySelectorAll('.perm-check:checked');
        const totalChecks = mod.querySelectorAll('.perm-check');
        const countBadge = mod.querySelector('.module-count');
        if (countBadge) {
            countBadge.textContent = `${moduleChecks.length}/${totalChecks.length}`;
        }
    });
}

// Init
document.addEventListener('DOMContentLoaded', () => {
    updateSummary();
    document.querySelectorAll('.module-select-all').forEach(cb => {
        updateModuleSelectAll(cb.dataset.module);
    });
});
</script>

<?= $this->endSection() ?>
