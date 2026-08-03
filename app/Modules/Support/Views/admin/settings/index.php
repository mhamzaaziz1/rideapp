<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="settings-wrapper animate-fade-in">
    <div class="settings-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gradient">AI Command Center</h1>
            <p class="text-secondary mb-0">Configure your bot's brain. Choose between OpenAI, Claude, or Gemini.</p>
        </div>
        <a href="<?= base_url('admin/support') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/support/settings/save') ?>" method="POST">
        <div class="row">
            <!-- Left Column: Provider Selection -->
            <div class="col-md-4">
                <div class="card premium-card border-0 mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="font-weight-bold mb-0">Brain Provider</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="provider-selector">
                            <label class="provider-option <?= ($settings['ai_provider'] == 'openai') ? 'active' : '' ?>">
                                <input type="radio" name="ai_provider" value="openai" <?= ($settings['ai_provider'] == 'openai') ? 'checked' : '' ?>>
                                <div class="option-content">
                                    <div class="option-icon"><i class="fas fa-robot"></i></div>
                                    <div class="option-text">
                                        <strong>OpenAI</strong>
                                        <span>GPT-4o / GPT-3.5</span>
                                    </div>
                                </div>
                            </label>

                            <label class="provider-option <?= ($settings['ai_provider'] == 'claude') ? 'active' : '' ?>">
                                <input type="radio" name="ai_provider" value="claude" <?= ($settings['ai_provider'] == 'claude') ? 'checked' : '' ?>>
                                <div class="option-content">
                                    <div class="option-icon text-warning"><i class="fas fa-feather"></i></div>
                                    <div class="option-text">
                                        <strong>Claude</strong>
                                        <span>Anthropic Claude 3.5</span>
                                    </div>
                                </div>
                            </label>

                            <label class="provider-option <?= ($settings['ai_provider'] == 'gemini') ? 'active' : '' ?>">
                                <input type="radio" name="ai_provider" value="gemini" <?= ($settings['ai_provider'] == 'gemini') ? 'checked' : '' ?>>
                                <div class="option-content">
                                    <div class="option-icon text-info"><i class="fas fa-gem"></i></div>
                                    <div class="option-text">
                                        <strong>Gemini</strong>
                                        <span>Google Gemini 1.5</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card premium-card border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="font-weight-bold mb-0">System Personality</h5>
                    </div>
                    <div class="card-body px-4 pb-4">
                        <div class="form-group-premium">
                            <label>System Prompt</label>
                            <textarea name="ai_system_prompt" rows="6" placeholder="Define how the bot should behave..."><?= esc($settings['ai_system_prompt']) ?></textarea>
                            <small class="text-muted mt-2 d-block">This defines the 'soul' of your bot. Keep it professional.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: API Keys -->
            <div class="col-md-8">
                <div class="card premium-card border-0">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="font-weight-bold mb-0">Credentials & Keys</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="api-key-section mb-4 p-4 border rounded-lg">
                            <div class="d-flex align-items-center mb-3">
                                <div class="key-icon bg-soft-blue"><i class="fas fa-key"></i></div>
                                <h6 class="mb-0 font-weight-bold ms-3">OpenAI API Key</h6>
                            </div>
                            <div class="form-group-premium">
                                <input type="password" name="openai_key" value="<?= esc($settings['openai_key']) ?>" placeholder="sk-...">
                            </div>
                        </div>

                        <div class="api-key-section mb-4 p-4 border rounded-lg">
                            <div class="d-flex align-items-center mb-3">
                                <div class="key-icon bg-soft-warning"><i class="fas fa-lock"></i></div>
                                <h6 class="mb-0 font-weight-bold ms-3">Claude API Key</h6>
                            </div>
                            <div class="form-group-premium">
                                <input type="password" name="claude_key" value="<?= esc($settings['claude_key']) ?>" placeholder="x-api-key...">
                            </div>
                        </div>

                        <div class="api-key-section mb-4 p-4 border rounded-lg">
                            <div class="d-flex align-items-center mb-3">
                                <div class="key-icon bg-soft-info"><i class="fas fa-shield-alt"></i></div>
                                <h6 class="mb-0 font-weight-bold ms-3">Gemini API Key</h6>
                            </div>
                            <div class="form-group-premium">
                                <input type="password" name="gemini_key" value="<?= esc($settings['gemini_key'] ?? '') ?>" placeholder="AIza...">
                            </div>
                        </div>

                        <div class="api-key-section p-4 border rounded-lg">
                            <div class="d-flex align-items-center mb-3">
                                <div class="key-icon bg-soft-success"><i class="fas fa-map-marked-alt"></i></div>
                                <h6 class="mb-0 font-weight-bold ms-3">Google Maps API Key</h6>
                            </div>
                            <div class="form-group-premium">
                                <input type="password" name="google_maps_key" value="<?= esc($settings['google_maps_key'] ?? '') ?>" placeholder="AIza...">
                            </div>
                        </div>

                        <!-- Embed Integration Section -->
                        <div class="embed-integration-section mt-5 border-top pt-4">
                            <h6 class="font-weight-bold mb-3"><i class="fas fa-code text-primary me-2"></i> Website Embed Integration</h6>
                            <p class="text-secondary" style="font-size: 13px;">Embed the AI chatbot on any external website (WordPress, Shopify, etc.). Copy the code below and paste it right before the closing <code>&lt;/body&gt;</code> tag on your site.</p>
                            
                            <div class="bg-dark p-3 rounded-lg position-relative">
                                <button type="button" class="btn btn-sm btn-light position-absolute" style="top: 10px; right: 10px;" onclick="copyEmbedCode(this)">
                                    <i class="fas fa-copy"></i> Copy
                                </button>
                                <pre class="mb-0 text-light" style="font-size: 12px; white-space: pre-wrap; font-family: monospace;" id="embed-code-text">&lt;!-- RideApp Support Widget --&gt;
&lt;script&gt;
(function(){
    // The Floating Action Button (FAB)
    var fab = document.createElement('button');
    fab.innerHTML = '&lt;svg viewBox="0 0 24 24" width="24" height="24"&gt;&lt;path fill="white" d="M12,2C6.477,2 2,6.477 2,12C2,13.596 2.373,15.105 3.033,16.446L2,22L7.554,20.967C8.895,21.627 10.404,22 12,22C17.523,22 22,17.523 22,12C22,6.477 17.523,2 12,2ZM12,18C10.74,18 9.537,17.728 8.442,17.235L5.056,17.868L5.689,14.482C5.196,13.387 4.923,12.184 4.923,10.923C4.923,6.551 8.472,3 12.844,3C17.215,3 20.764,6.551 20.764,10.923C20.764,15.295 17.215,18.844 12.844,18.844H12Z"/&gt;&lt;/svg&gt;';
    fab.style.cssText = 'position:fixed; bottom:20px; right:20px; width:60px; height:60px; border-radius:50%; background:#2563eb; border:none; cursor:pointer; box-shadow:0 4px 15px rgba(37,99,235,0.4); z-index:999999; display:flex; align-items:center; justify-content:center; transition:transform 0.2s;';
    
    // The Iframe Container (Hidden by default)
    var frameDiv = document.createElement('div');
    frameDiv.style.cssText = 'position:fixed; bottom:90px; right:20px; width:360px; height:600px; z-index:999999; display:none; opacity:0; transition:opacity 0.2s;';
    frameDiv.innerHTML = '&lt;iframe src="<?= rtrim(base_url(), '/') ?>/api/support/embed" style="width:100%; height:100%; border:none; border-radius:20px; box-shadow:0 10px 40px rgba(0,0,0,0.15);" allowtransparency="true"&gt;&lt;/iframe&gt;';
    
    // Toggle Logic
    fab.onclick = function() {
        var isHidden = frameDiv.style.display === 'none';
        if(isHidden) {
            frameDiv.style.display = 'block';
            setTimeout(function(){ frameDiv.style.opacity = '1'; }, 10);
            fab.style.transform = 'scale(0.9)';
        } else {
            frameDiv.style.opacity = '0';
            setTimeout(function(){ frameDiv.style.display = 'none'; }, 200);
            fab.style.transform = 'scale(1)';
        }
    };
    
    document.body.appendChild(frameDiv);
    document.body.appendChild(fab);
})();
&lt;/script&gt;</pre>
                            </div>
                        </div>

                        <div class="mt-5 text-end">
                            <button type="submit" class="btn btn-primary-premium px-5">
                                <i class="fas fa-save me-2"></i> Apply Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.settings-wrapper { padding: 20px; }
.text-gradient {
    background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.premium-card {
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.02);
}

.provider-selector { display: flex; flex-direction: column; gap: 15px; }
.provider-option {
    cursor: pointer;
    border: 2px solid #f1f5f9;
    border-radius: 16px;
    padding: 15px;
    transition: all 0.3s;
    position: relative;
}

.provider-option input { position: absolute; opacity: 0; }
.provider-option .option-content { display: flex; align-items: center; gap: 15px; }
.provider-option .option-icon { font-size: 20px; width: 40px; height: 40px; background: #f8fafc; border-radius: 10px; display: flex; align-items: center; justify-content: center; }

.provider-option strong { display: block; font-size: 15px; color: #1e293b; }
.provider-option span { font-size: 11px; color: #64748b; }

.provider-option.active {
    border-color: #2563eb;
    background: rgba(37, 99, 235, 0.05);
}

.form-group-premium label { font-weight: 700; font-size: 13px; color: #475569; margin-bottom: 8px; display: block; }
.form-group-premium input, .form-group-premium textarea {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 15px;
    outline: none;
    font-size: 14px;
}

.form-group-premium input:focus, .form-group-premium textarea:focus {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.key-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
.bg-soft-blue { background: #eff6ff; color: #2563eb; }
.bg-soft-warning { background: #fffbeb; color: #d97706; }
.bg-soft-info { background: #ecfeff; color: #0891b2; }
.bg-soft-success { background: #dcfce7; color: #16a34a; }

.btn-primary-premium {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
    transition: all 0.3s;
}

.btn-primary-premium:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3); color: white; }
</style>

<script>
document.querySelectorAll('.provider-option').forEach(option => {
    option.addEventListener('click', () => {
        document.querySelectorAll('.provider-option').forEach(o => o.classList.remove('active'));
        option.classList.add('active');
        option.querySelector('input').checked = true;
    });
});

function copyEmbedCode(btn) {
    const code = document.getElementById('embed-code-text').innerText;
    navigator.clipboard.writeText(code).then(() => {
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
        setTimeout(() => {
            btn.innerHTML = originalText;
        }, 2000);
    });
}
</script>
<?= $this->endSection() ?>
