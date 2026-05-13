<?= $this->extend('layouts/master') ?>
<?= $this->section('content') ?>
<div style="padding:2rem;max-width:1200px;margin:0 auto;">
    <div style="margin-bottom:2rem;">
        <a href="<?= base_url('staff') ?>" style="color:var(--text-secondary);display:inline-flex;align-items:center;gap:4px;font-size:0.9rem;margin-bottom:1rem;text-decoration:none;"><i data-lucide="arrow-left" width="16"></i> Back to Staff</a>
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:1.1rem;"><?= substr($user->first_name,0,1).substr($user->last_name,0,1) ?></div>
            <div>
                <h1 class="h3" style="margin:0;"><?= esc($user->first_name.' '.$user->last_name) ?></h1>
                <div style="color:var(--text-secondary);font-size:0.9rem;"><?= esc($user->email) ?><?php if($userRole): ?> &bull; Role: <span style="color:var(--primary);font-weight:600;"><?= esc($userRole->name) ?></span><?php endif; ?></div>
            </div>
        </div>
    </div>
    <?php if(session()->getFlashdata('success')): ?>
    <div style="background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.3);color:#22c55e;padding:0.75rem 1rem;border-radius:var(--radius-sm);margin-bottom:1.5rem;display:flex;align-items:center;gap:8px;"><i data-lucide="check-circle" width="16"></i> <?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <div class="card" style="padding:1rem 1.5rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:2rem;flex-wrap:wrap;font-size:0.8rem;">
            <strong>Legend:</strong>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;"></span> From Role</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:#3b82f6;display:inline-block;"></span> Override: Grant</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;"></span> Override: Deny</span>
            <span style="display:flex;align-items:center;gap:5px;"><span style="width:8px;height:8px;border-radius:50%;background:var(--border-color);display:inline-block;"></span> Not Granted</span>
        </div>
    </div>
    <form action="<?= base_url('staff/permissions/save/'.$user->id) ?>" method="post">
    <div style="display:grid;grid-template-columns:1fr 280px;gap:2rem;">
        <div class="card" style="padding:1.5rem;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
                <h4 style="margin:0;font-weight:700;"><i data-lucide="key-round" width="18" style="color:var(--primary);vertical-align:middle;margin-right:6px;"></i>Permission Overrides</h4>
                <input type="text" id="permSearch" class="form-control" placeholder="Search..." style="width:180px;font-size:0.85rem;" oninput="filterPerms(this.value)">
            </div>
            <?php foreach($groupedPerms as $module => $resources): ?>
            <div class="perm-mod" style="margin-bottom:1.25rem;">
                <div style="padding:0.5rem 0.75rem;background:var(--bg-surface-hover);border-radius:var(--radius-sm);margin-bottom:0.4rem;font-weight:700;font-size:0.9rem;cursor:pointer;" onclick="this.closest('.perm-mod').classList.toggle('collapsed')">
                    <i data-lucide="chevron-down" width="14" class="mc" style="transition:transform .2s;vertical-align:middle;margin-right:4px;"></i><?= esc($module) ?>
                </div>
                <div class="perm-mod-body">
                <?php foreach($resources as $resource => $perms): ?>
                <div style="margin-bottom:0.5rem;padding-left:0.5rem;">
                    <div style="font-weight:600;font-size:0.75rem;color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.5px;padding:0.2rem 0.5rem;"><?= esc($resource) ?></div>
                    <?php foreach($perms as $perm): ?>
                    <?php
                        $act = basename(str_replace('.','/','.'.$perm->name));
                        $fromRole = in_array($perm->id, $rolePermIds);
                        $dov = $directOverrides[$perm->id] ?? null;
                        if ($dov === 1) { $st='grant'; } elseif ($dov === 0) { $st='deny'; } else { $st='inherit'; }
                    ?>
                    <div class="pr" data-name="<?= esc($perm->name) ?>" style="display:flex;align-items:center;justify-content:space-between;padding:0.35rem 0.75rem;border-radius:4px;">
                        <div style="display:flex;align-items:center;gap:6px;flex:1;">
                            <span style="width:6px;height:6px;border-radius:50%;background:<?= $st==='grant'?'#3b82f6':($st==='deny'?'#ef4444':($fromRole?'#22c55e':'var(--border-color)')) ?>;"></span>
                            <span style="font-size:0.84rem;font-weight:500;"><?= ucfirst(str_replace('_',' ',$act)) ?></span>
                            <span style="font-size:0.7rem;color:var(--text-secondary);"><?= esc($perm->description ?? '') ?></span>
                        </div>
                        <div style="display:flex;gap:2px;">
                            <label class="ob oi <?= $st==='inherit'?'active':'' ?>" title="Inherit from role"><input type="radio" name="overrides[<?= $perm->id ?>]" value="inherit" <?= $st==='inherit'?'checked':'' ?> style="display:none;"><i data-lucide="minus" width="12"></i></label>
                            <label class="ob og <?= $st==='grant'?'active':'' ?>" title="Grant override"><input type="radio" name="overrides[<?= $perm->id ?>]" value="grant" <?= $st==='grant'?'checked':'' ?> style="display:none;"><i data-lucide="check" width="12"></i></label>
                            <label class="ob od <?= $st==='deny'?'active':'' ?>" title="Deny override"><input type="radio" name="overrides[<?= $perm->id ?>]" value="deny" <?= $st==='deny'?'checked':'' ?> style="display:none;"><i data-lucide="x" width="12"></i></label>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="display:flex;flex-direction:column;gap:1.5rem;">
            <div class="card" style="padding:1.25rem;">
                <h4 style="margin-bottom:0.5rem;font-weight:700;font-size:0.95rem;">How It Works</h4>
                <div style="font-size:0.8rem;color:var(--text-secondary);line-height:1.6;">
                    <p><strong>Inherit (—)</strong> uses role permission.</p>
                    <p><strong>Grant (✓)</strong> explicitly gives permission.</p>
                    <p><strong>Deny (✗)</strong> explicitly blocks it.</p>
                </div>
            </div>
            <div class="card" style="padding:1.25rem;text-align:center;">
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-bottom:0.75rem;"><i data-lucide="save" width="16" style="margin-right:6px;"></i>Save Overrides</button>
                <a href="<?= base_url('staff') ?>" style="color:var(--text-secondary);font-size:0.9rem;text-decoration:none;">Cancel</a>
            </div>
        </div>
    </div>
    </form>
</div>
<style>
.pr:hover{background:var(--bg-surface-hover)}
.ob{display:inline-flex;align-items:center;justify-content:center;width:26px;height:26px;border-radius:6px;cursor:pointer;background:transparent;border:1px solid var(--border-color);color:var(--text-secondary);transition:all .15s}
.ob:hover{background:var(--bg-surface-hover)}
.ob.active.oi{background:var(--bg-surface-hover);border-color:var(--text-secondary);color:var(--text-primary)}
.ob.active.og{background:rgba(59,130,246,0.15);border-color:#3b82f6;color:#3b82f6}
.ob.active.od{background:rgba(239,68,68,0.15);border-color:#ef4444;color:#ef4444}
.perm-mod-body{max-height:2000px;overflow:hidden;transition:max-height .3s ease}
.perm-mod.collapsed .perm-mod-body{max-height:0}
.perm-mod.collapsed .mc{transform:rotate(-90deg)}
</style>
<script>
document.querySelectorAll('.ob input[type="radio"]').forEach(r=>{r.addEventListener('change',function(){const p=this.closest('.pr')||this.closest('div');p.querySelectorAll('.ob').forEach(b=>b.classList.remove('active'));this.closest('.ob').classList.add('active')})});
function filterPerms(q){q=q.toLowerCase();document.querySelectorAll('.pr').forEach(r=>{r.style.display=r.dataset.name.toLowerCase().includes(q)||q===''?'':'none'});document.querySelectorAll('.perm-mod').forEach(m=>{m.style.display=m.querySelectorAll('.pr:not([style*="display: none"])').length>0?'':'none'})}
</script>
<?= $this->endSection() ?>
