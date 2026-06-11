<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="faq-wrapper animate-fade-in">
    <!-- Header -->
    <div class="faq-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 font-weight-bold text-gradient">Bot Knowledge Base</h1>
            <p class="text-secondary mb-0">Train your automated assistant with keywords and pre-defined answers.</p>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('admin/support') ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left"></i> Back to Chats
            </a>
            <button class="btn btn-primary-premium" data-bs-toggle="modal" data-bs-target="#faqModal">
                <i class="fas fa-plus-circle me-2"></i> Add New Keyword
            </button>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-blue"><i class="fas fa-robot"></i></div>
                <div class="stat-info">
                    <h3><?= count($faqs) ?></h3>
                    <p>Trained Keywords</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-green"><i class="fas fa-check-double"></i></div>
                <div class="stat-info">
                    <h3>100%</h3>
                    <p>Bot Uptime</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-icon bg-soft-purple"><i class="fas fa-bolt"></i></div>
                <div class="stat-info">
                    <h3>Instant</h3>
                    <p>Avg. Response Time</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Table -->
    <div class="card premium-card border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Keyword</th>
                            <th>Response Content</th>
                            <th>Category</th>
                            <th class="text-end pe-4">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faqs as $faq): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="keyword-pill">
                                    <i class="fas fa-key me-2"></i>
                                    <?= esc($faq['question_keyword']) ?>
                                </div>
                            </td>
                            <td>
                                <div class="answer-preview text-truncate" style="max-width: 500px;" title="<?= esc($faq['answer']) ?>">
                                    <?= esc($faq['answer']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge-premium badge-<?= esc($faq['category']) ?>">
                                    <?= ucfirst(esc($faq['category'])) ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group-premium">
                                    <button class="btn-action edit-faq" 
                                            data-id="<?= $faq['id'] ?>"
                                            data-keyword="<?= esc($faq['question_keyword']) ?>"
                                            data-answer="<?= esc($faq['answer']) ?>"
                                            data-category="<?= esc($faq['category']) ?>"
                                            data-bs-toggle="modal" data-bs-target="#faqModal">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <form action="<?= base_url('admin/support/faq/delete/' . $faq['id']) ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete this keyword?')">
                                        <button type="submit" class="btn-action text-danger">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($faqs)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fas fa-brain"></i></div>
                                    <h4>The bot is currently empty</h4>
                                    <p>Start by adding keywords and answers to automate your support.</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Modal -->
<div class="modal fade" id="faqModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg premium-modal">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="modalTitle">New Knowledge Entry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/support/faq/store') ?>" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="faq-id">
                    <div class="form-group-premium mb-4">
                        <label>Trigger Keyword</label>
                        <div class="input-wrapper">
                            <i class="fas fa-keyboard"></i>
                            <input type="text" name="question_keyword" id="faq-keyword" placeholder="e.g. refund, password" required>
                        </div>
                    </div>
                    <div class="form-group-premium mb-4">
                        <label>Automated Response</label>
                        <textarea name="answer" id="faq-answer" rows="4" placeholder="Enter the message the bot should send..." required></textarea>
                    </div>
                    <div class="form-group-premium">
                        <label>Intent Category</label>
                        <select name="category" id="faq-category">
                            <option value="general">General Inquiry</option>
                            <option value="billing">Billing & Financial</option>
                            <option value="account">Account Security</option>
                            <option value="driver">Driver Logistics</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-premium px-5">Save Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Premium FAQ Styles */
:root {
    --faq-primary: #2563eb;
    --faq-border: #eef2f6;
    --faq-text: #1e293b;
    --radius-xl: 20px;
}

.text-gradient {
    background: linear-gradient(135deg, #1e293b 0%, #2563eb 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.animate-fade-in {
    animation: fadeIn 0.6s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.btn-primary-premium {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
    transition: all 0.3s;
}

.btn-primary-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
    color: white;
}

/* Stats Cards */
.stat-card {
    background: white;
    border-radius: var(--radius-xl);
    padding: 25px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    border: 1px solid var(--faq-border);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.bg-soft-blue { background: #eff6ff; color: #2563eb; }
.bg-soft-green { background: #f0fdf4; color: #10b981; }
.bg-soft-purple { background: #faf5ff; color: #a855f7; }

.stat-info h3 { margin: 0; font-weight: 800; color: var(--faq-text); }
.stat-info p { margin: 0; font-size: 14px; color: #64748b; }

/* Table Styles */
.premium-card {
    border-radius: var(--radius-xl);
    box-shadow: 0 10px 40px rgba(0,0,0,0.03);
    overflow: hidden;
}

.table thead th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 20px 15px;
    border: none;
}

.keyword-pill {
    background: rgba(37, 99, 235, 0.05);
    color: #2563eb;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    font-size: 14px;
}

.answer-preview {
    color: #4b5563;
    font-size: 14px;
}

.badge-premium {
    padding: 6px 14px;
    border-radius: 30px;
    font-size: 12px;
    font-weight: 700;
}

.badge-general { background: #f1f5f9; color: #475569; }
.badge-billing { background: #f0fdf4; color: #166534; }
.badge-account { background: #fff7ed; color: #9a3412; }
.badge-driver { background: #eff6ff; color: #1e40af; }

.btn-action {
    background: none;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 10px;
    color: #94a3b8;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.btn-action:hover {
    background: #f1f5f9;
    color: #1e293b;
}

/* Modal Styles */
.premium-modal {
    border-radius: 24px;
    overflow: hidden;
}

.form-group-premium label {
    display: block;
    font-weight: 700;
    color: var(--faq-text);
    margin-bottom: 10px;
    font-size: 14px;
}

.input-wrapper {
    position: relative;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 15px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.3s;
}

.input-wrapper:focus-within {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.input-wrapper i { color: #94a3b8; }
.input-wrapper input { background: none; border: none; outline: none; flex: 1; font-size: 14px; }

textarea, select {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 15px;
    outline: none;
    font-size: 14px;
    transition: all 0.3s;
}

textarea:focus, select:focus {
    border-color: #2563eb;
    background: white;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
}

.empty-state {
    padding: 40px;
    color: #94a3b8;
}

.empty-icon { font-size: 60px; margin-bottom: 20px; color: #e2e8f0; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqModal = document.getElementById('faqModal');
    const modalTitle = document.getElementById('modalTitle');
    const idInput = document.getElementById('faq-id');
    const keywordInput = document.getElementById('faq-keyword');
    const answerInput = document.getElementById('faq-answer');
    const categorySelect = document.getElementById('faq-category');

    // Handle Edit
    document.querySelectorAll('.edit-faq').forEach(btn => {
        btn.addEventListener('click', function() {
            modalTitle.innerText = 'Refine Knowledge Entry';
            idInput.value = this.dataset.id;
            keywordInput.value = this.dataset.keyword;
            answerInput.value = this.dataset.answer;
            categorySelect.value = this.dataset.category;
        });
    });

    // Reset modal on close
    faqModal.addEventListener('hidden.bs.modal', function () {
        modalTitle.innerText = 'New Knowledge Entry';
        idInput.value = '';
        keywordInput.value = '';
        answerInput.value = '';
        categorySelect.value = 'general';
    });
});
</script>
<?= $this->endSection() ?>
