<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="support-wrapper animate-fade-in" style="background: linear-gradient(135deg, #0f172a 0%, #1e1e2e 100%);">
    <!-- Header -->
    <div class="support-header d-flex justify-content-between align-items-center" style="padding: 1.5rem 2rem;">
        <div class="header-info">
            <h1 class="h3 font-weight-bold text-gradient-neon">Support Command Center</h1>
            <p class="text-secondary-neon mb-0">Real-time interaction with customers and drivers.</p>
        </div>
        <div class="header-actions">
            <a href="<?= base_url('admin/support/settings') ?>" class="btn btn-glass-neon me-2">
                <i class="fas fa-cog me-2"></i> Config AI
            </a>
            <a href="<?= base_url('admin/support/faq') ?>" class="btn btn-glass">
                <i class="fas fa-robot me-2"></i> FAQ Settings
            </a>
        </div>
    </div>

    <!-- Main Container -->
    <div class="support-container mt-4">
        <!-- Sidebar -->
        <div class="conversations-sidebar">
            <div class="sidebar-search">
                <div class="search-input-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="chat-search" placeholder="Search active chats...">
                </div>
            </div>
            
            <div class="sidebar-tabs">
                <button class="tab-btn active" data-tab="active">Active</button>
                <button class="tab-btn" data-tab="closed">Closed</button>
            </div>

            <div class="conversation-list" id="conv-list">
                <?php foreach ($conversations as $conv): ?>
                    <div class="conv-card <?= $conv['status'] ?>" data-id="<?= $conv['id'] ?>">
                        <div class="conv-avatar">
                            <?= strtoupper(substr($conv['user_name'], 0, 1)) ?>
                        </div>
                        <div class="conv-info">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="user-name"><?= esc($conv['user_name']) ?></span>
                                <span class="time"><?= date('H:i', strtotime($conv['updated_at'])) ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="subject text-truncate"><?= esc($conv['subject'] ?? 'No Subject') ?></span>
                                <span class="status-dot"></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <?php if (empty($conversations)): ?>
                    <div class="empty-list-state">
                        <i class="fas fa-ghost"></i>
                        <p>No active conversations</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Area -->
        <div class="chat-area">
            <div id="no-chat-state" class="chat-empty-state">
                <div class="state-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3>Select a conversation</h3>
                <p>Pick a chat from the sidebar to start responding to your users in real-time.</p>
            </div>

            <div id="active-chat-state" class="chat-main" style="display: none;">
                <div class="chat-header">
                    <div class="active-user-info">
                        <div class="active-avatar" id="active-avatar">?</div>
                        <div>
                            <h4 id="active-user-name" class="mb-0">User Name</h4>
                            <span id="active-status" class="status-badge">Online</span>
                        </div>
                    </div>
                    <div class="chat-controls">
                        <button class="btn btn-icon text-danger" id="close-conv-btn" title="Close Ticket">
                            <i class="fas fa-check-circle"></i>
                        </button>
                    </div>
                </div>

                <div id="admin-chat-messages" class="chat-messages-container">
                    <!-- Messages will be injected here -->
                </div>

                <div class="chat-input-area">
                    <div id="canned-responses-menu" class="canned-menu" style="display: none;">
                        <span class="canned-title">Quick Replies:</span>
                        <div class="canned-options">
                            <button class="canned-btn" data-text="Hello! How can I assist you today?">Greeting</button>
                            <button class="canned-btn" data-text="I am looking into this issue right now. Please give me a moment.">Investigating</button>
                            <button class="canned-btn" data-text="I am escalating your issue to our technical team. They will review it shortly.">Escalating</button>
                            <button class="canned-btn" data-text="I apologize for the inconvenience this has caused. Let me fix this for you.">Apology</button>
                            <button class="canned-btn" data-text="Is there anything else I can help you with before I close this ticket?">Closing</button>
                        </div>
                    </div>
                    <div class="input-container">
                        <button id="toggle-canned-btn" class="quick-reply-toggle" title="Pre-defined Replies">
                            <i class="fas fa-bolt"></i>
                        </button>
                        <textarea id="admin-reply-input" placeholder="Type your message..." rows="1"></textarea>
                        <button id="admin-send-btn" class="send-button" title="Send Reply">
                            <svg viewBox="0 0 24 24" width="20" height="20">
                                <path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Premium Dark Support Dashboard Styles */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

:root {
    --support-primary: #8b5cf6;
    --support-primary-hover: #a78bfa;
    --support-bg-dark: #0f172a;
    --support-surface: rgba(30, 41, 59, 0.7);
    --support-surface-hover: rgba(51, 65, 85, 0.8);
    --support-border: rgba(255, 255, 255, 0.1);
    --support-text-main: #f8fafc;
    --support-text-muted: #94a3b8;
    --radius-premium: 20px;
}

.support-wrapper {
    min-height: calc(100vh - 100px);
    display: flex;
    flex-direction: column;
    font-family: 'Outfit', sans-serif;
}

.text-gradient-neon {
    background: linear-gradient(135deg, #a855f7 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 30px rgba(168, 85, 247, 0.3);
}

.text-secondary-neon {
    color: var(--support-text-muted);
    font-size: 0.95rem;
}

.btn-glass-neon {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: var(--support-text-main);
    font-weight: 600;
    border-radius: 12px;
    padding: 10px 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.btn-glass-neon:hover {
    background: rgba(139, 92, 246, 0.15);
    border-color: rgba(139, 92, 246, 0.4);
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
}

.support-container {
    display: grid;
    grid-template-columns: 380px 1fr;
    background: var(--support-surface);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-radius: var(--radius-premium);
    box-shadow: 0 20px 50px rgba(0,0,0,0.4), inset 0 0 0 1px var(--support-border);
    overflow: hidden;
    height: calc(100vh - 200px);
    min-height: 600px;
    margin: 0 2rem 2rem 2rem;
}

/* Sidebar Styles */
.conversations-sidebar {
    border-right: 1px solid var(--support-border);
    display: flex;
    flex-direction: column;
    background: rgba(15, 23, 42, 0.3);
}

.sidebar-search {
    padding: 24px;
}

.search-input-wrapper {
    position: relative;
    background: rgba(0, 0, 0, 0.2);
    border-radius: 12px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--support-border);
    transition: all 0.3s;
}

.search-input-wrapper:focus-within {
    border-color: var(--support-primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.search-input-wrapper i {
    color: var(--support-text-muted);
}

.search-input-wrapper input {
    background: none;
    border: none;
    outline: none;
    width: 100%;
    font-size: 14px;
    color: var(--support-text-main);
}
.search-input-wrapper input::placeholder {
    color: var(--support-text-muted);
}

.sidebar-tabs {
    display: flex;
    padding: 0 24px 15px;
    gap: 20px;
    border-bottom: 1px solid var(--support-border);
}

.tab-btn {
    background: none;
    border: none;
    padding-bottom: 10px;
    font-weight: 500;
    font-size: 14px;
    color: var(--support-text-muted);
    cursor: pointer;
    position: relative;
    transition: color 0.3s;
}

.tab-btn:hover {
    color: var(--support-text-main);
}

.tab-btn.active {
    color: var(--support-text-main);
    font-weight: 600;
}

.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, #8b5cf6, #3b82f6);
    border-radius: 3px 3px 0 0;
    box-shadow: 0 -2px 10px rgba(139, 92, 246, 0.5);
}

.conversation-list {
    flex: 1;
    overflow-y: auto;
}

.conversation-list::-webkit-scrollbar {
    width: 6px;
}
.conversation-list::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.1);
    border-radius: 10px;
}

.conv-card {
    padding: 18px 24px;
    display: flex;
    gap: 16px;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    border-bottom: 1px solid rgba(255,255,255,0.03);
    position: relative;
}

.conv-card:hover {
    background: var(--support-surface-hover);
}

.conv-card.active {
    background: rgba(139, 92, 246, 0.1);
}
.conv-card.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(180deg, #8b5cf6, #3b82f6);
    box-shadow: 2px 0 10px rgba(139, 92, 246, 0.5);
}

.conv-avatar {
    width: 48px;
    height: 48px;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--support-border);
    border-radius: 14px;
    color: var(--support-text-main);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 18px;
    flex-shrink: 0;
    transition: all 0.3s;
}

.agent_active .conv-avatar {
    background: linear-gradient(135deg, #a855f7 0%, #3b82f6 100%);
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.conv-info {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 600;
    color: var(--support-text-main);
    font-size: 15px;
    letter-spacing: 0.3px;
}

.time {
    font-size: 12px;
    color: var(--support-text-muted);
}

.subject {
    font-size: 13px;
    color: var(--support-text-muted);
    display: block;
    margin-top: 4px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #475569;
}

.agent_active .status-dot { background: #10b981; animation: pulse-green 2s infinite; box-shadow: 0 0 10px #10b981; }
.bot_active .status-dot { background: #3b82f6; box-shadow: 0 0 10px #3b82f6; }

/* Chat Area Styles */
.chat-area {
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
    background: rgba(15, 23, 42, 0.1);
}

.chat-empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px;
    text-align: center;
}

.state-icon {
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--support-border);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: var(--support-primary);
    margin-bottom: 24px;
    box-shadow: 0 0 30px rgba(139, 92, 246, 0.1);
}

.chat-empty-state h3 {
    color: var(--support-text-main);
    font-weight: 600;
}
.chat-empty-state p {
    color: var(--support-text-muted);
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    min-height: 0;
}

.chat-header {
    padding: 20px 30px;
    border-bottom: 1px solid var(--support-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(10px);
    z-index: 10;
    flex-shrink: 0;
}

.active-user-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.active-avatar {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
    color: white;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 18px;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.active-user-info h4 {
    color: var(--support-text-main);
    font-weight: 600;
}

.status-badge {
    font-size: 12px;
    color: #10b981;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}

.status-badge::before {
    content: '';
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 8px #10b981;
}

.btn-icon {
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(239, 68, 68, 0.2);
    color: #ef4444;
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}
.btn-icon:hover {
    background: #ef4444;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.chat-messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 30px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.chat-messages-container::-webkit-scrollbar { width: 6px; }
.chat-messages-container::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

.msg-wrapper {
    display: flex;
    flex-direction: column;
    max-width: 75%;
    animation: fade-in-up 0.3s ease-out forwards;
}

.msg-wrapper.user { align-self: flex-start; }
.msg-wrapper.agent { align-self: flex-end; }
.msg-wrapper.bot { align-self: center; max-width: 90%; }

.msg-bubble {
    padding: 14px 20px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.6;
    position: relative;
    white-space: pre-wrap;
}

.user .msg-bubble {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid var(--support-border);
    color: var(--support-text-main);
    border-bottom-left-radius: 4px;
    backdrop-filter: blur(10px);
}

.agent .msg-bubble {
    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
}

.bot .msg-bubble {
    background: rgba(59, 130, 246, 0.05);
    color: var(--support-text-muted);
    font-style: italic;
    border: 1px dashed rgba(59, 130, 246, 0.3);
    font-size: 13px;
    text-align: center;
    border-radius: 12px;
}

.msg-time {
    font-size: 11px;
    color: var(--support-text-muted);
    margin-top: 6px;
    display: block;
}

.agent .msg-time { text-align: right; }

.chat-input-area {
    padding: 24px 30px;
    border-top: 1px solid var(--support-border);
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(10px);
    flex-shrink: 0;
    position: relative;
}

.canned-menu {
    position: absolute;
    bottom: 100%;
    left: 30px;
    background: var(--support-surface);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--support-border);
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 -10px 40px rgba(0,0,0,0.3);
    margin-bottom: 20px;
    z-index: 20;
    animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-width: 400px;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(15px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-up {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.canned-title {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: var(--support-primary-hover);
    text-transform: uppercase;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
}

.canned-options {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.canned-btn {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--support-text-main);
    padding: 8px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.canned-btn:hover {
    background: rgba(139, 92, 246, 0.2);
    border-color: rgba(139, 92, 246, 0.5);
    color: white;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
}

.input-container {
    background: rgba(0,0,0,0.2);
    border: 1px solid var(--support-border);
    border-radius: 16px;
    padding: 12px 16px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    transition: all 0.3s;
}
.input-container:focus-within {
    border-color: var(--support-primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.quick-reply-toggle {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--support-text-muted);
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.quick-reply-toggle:hover, .quick-reply-toggle.active {
    background: rgba(139, 92, 246, 0.15);
    border-color: rgba(139, 92, 246, 0.4);
    color: var(--support-primary-hover);
}

.input-container textarea {
    background: none;
    border: none;
    outline: none;
    width: 100%;
    padding: 10px 0;
    resize: none;
    font-size: 15px;
    color: var(--support-text-main);
}
.input-container textarea::placeholder {
    color: var(--support-text-muted);
}

.send-button {
    background: linear-gradient(135deg, #8b5cf6 0%, #3b82f6 100%);
    color: white;
    border: none;
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.send-button:hover {
    transform: scale(1.05) translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
}

@keyframes pulse-green {
    0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}

@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in { animation: fade-in 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= rtrim(base_url(), '/') ?>';
    const convCards = document.querySelectorAll('.conv-card');
    const noChatState = document.getElementById('no-chat-state');
    const activeChatState = document.getElementById('active-chat-state');
    const messageContainer = document.getElementById('admin-chat-messages');
    const replyInput = document.getElementById('admin-reply-input');
    const sendBtn = document.getElementById('admin-send-btn');
    const userNameDisplay = document.getElementById('active-user-name');
    const avatarDisplay = document.getElementById('active-avatar');
    const closeConvBtn = document.getElementById('close-conv-btn');
    
    let currentConversationId = null;
    let lastAdminMessageCount = 0;

    // Canned Responses Logic
    const toggleCannedBtn = document.getElementById('toggle-canned-btn');
    const cannedMenu = document.getElementById('canned-responses-menu');
    const cannedBtns = document.querySelectorAll('.canned-btn');

    toggleCannedBtn.addEventListener('click', () => {
        const isVisible = cannedMenu.style.display === 'block';
        cannedMenu.style.display = isVisible ? 'none' : 'block';
        toggleCannedBtn.classList.toggle('active', !isVisible);
    });

    cannedBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const text = btn.dataset.text;
            replyInput.value = text;
            cannedMenu.style.display = 'none';
            toggleCannedBtn.classList.remove('active');
            replyInput.focus();
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('#canned-responses-menu') && !e.target.closest('#toggle-canned-btn')) {
            cannedMenu.style.display = 'none';
            toggleCannedBtn.classList.remove('active');
        }
    });

    // Tab Logic
    const tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const targetStatus = btn.dataset.tab;
            
            convCards.forEach(card => {
                if (targetStatus === 'active' && card.classList.contains('closed')) {
                    card.style.display = 'none';
                } else if (targetStatus === 'closed' && !card.classList.contains('closed')) {
                    card.style.display = 'none';
                } else {
                    card.style.display = 'flex';
                }
            });
        });
    });

    convCards.forEach(card => {
        card.addEventListener('click', () => {
            convCards.forEach(c => c.classList.remove('active'));
            card.classList.add('active');
            
            if (currentConversationId !== card.dataset.id) {
                lastAdminMessageCount = 0; // Reset message counter when switching chats
                messageContainer.innerHTML = '';
            }
            
            currentConversationId = card.dataset.id;
            const name = card.querySelector('.user-name').innerText;
            userNameDisplay.innerText = name;
            avatarDisplay.innerText = name.charAt(0).toUpperCase();

            noChatState.style.display = 'none';
            activeChatState.style.display = 'flex';
            
            loadMessages();
        });
    });

    async function loadMessages() {
        if (!currentConversationId) return;
        const fetchId = currentConversationId; // Capture ID to prevent race conditions
        
        try {
            const response = await fetch(`${baseUrl}/admin/support/conversation/${fetchId}`);
            const data = await response.json();
            
            // If admin switched chats while this request was pending, discard the data
            if (currentConversationId !== fetchId) return;
            
            // Handle Closed Status
            const inputArea = document.querySelector('.chat-input-area');
            if (data.conversation.status === 'closed') {
                inputArea.style.display = 'none';
                closeConvBtn.style.display = 'none';
                document.getElementById('active-status').innerText = 'Resolved';
                document.getElementById('active-status').style.color = '#64748b';
            } else {
                inputArea.style.display = 'block';
                closeConvBtn.style.display = 'block';
                document.getElementById('active-status').innerText = 'Online';
                document.getElementById('active-status').style.color = '#10b981';
            }
            
            // Smart Update Messages
            if (data.messages.length > lastAdminMessageCount) {
                const newMessages = data.messages.slice(lastAdminMessageCount);
                newMessages.forEach(msg => {
                    const wrapper = document.createElement('div');
                    wrapper.className = `msg-wrapper ${msg.sender_role}`;
                    
                    const bubble = document.createElement('div');
                    bubble.className = 'msg-bubble';
                    bubble.innerText = msg.message;
                    
                    const time = document.createElement('span');
                    time.className = 'msg-time';
                    time.innerText = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                    
                    wrapper.appendChild(bubble);
                    wrapper.appendChild(time);
                    messageContainer.appendChild(wrapper);
                });
                lastAdminMessageCount = data.messages.length;
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        } catch (error) {
            console.error('Load Error:', error);
        }
    }

    sendBtn.addEventListener('click', async () => {
        const message = replyInput.value.trim();
        if (!message || !currentConversationId) return;

        replyInput.value = '';
        
        // Optimistic append
        const wrapper = document.createElement('div');
        wrapper.className = `msg-wrapper agent`;
        const bubble = document.createElement('div');
        bubble.className = 'msg-bubble';
        bubble.innerText = message;
        wrapper.appendChild(bubble);
        messageContainer.appendChild(wrapper);
        messageContainer.scrollTop = messageContainer.scrollHeight;
        lastAdminMessageCount++;

        const formData = new FormData();
        formData.append('conversation_id', currentConversationId);
        formData.append('message', message);

        try {
            await fetch(`${baseUrl}/admin/support/reply`, {
                method: 'POST',
                body: formData
            });
            setTimeout(loadMessages, 500);
        } catch (error) {
            console.error('Send Error:', error);
        }
    });

    replyInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendBtn.click();
        }
    });

    closeConvBtn.addEventListener('click', async () => {
        if (!currentConversationId || !confirm('Are you sure you want to resolve and close this ticket?')) return;

        try {
            await fetch(`${baseUrl}/admin/support/close/${currentConversationId}`, {
                method: 'POST'
            });
            location.reload();
        } catch (error) {
            console.error('Close Error:', error);
        }
    });

    // Auto-refresh logic
    setInterval(() => {
        if (currentConversationId) loadMessages();
    }, 5000);

    // Sidebar search
    document.getElementById('chat-search').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase();
        const targetStatus = document.querySelector('.tab-btn.active').dataset.tab;
        
        convCards.forEach(card => {
            const name = card.querySelector('.user-name').innerText.toLowerCase();
            const subject = card.querySelector('.subject').innerText.toLowerCase();
            const matchesSearch = name.includes(term) || subject.includes(term);
            const matchesTab = (targetStatus === 'active' && !card.classList.contains('closed')) || (targetStatus === 'closed' && card.classList.contains('closed'));
            
            if (matchesSearch && matchesTab) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
