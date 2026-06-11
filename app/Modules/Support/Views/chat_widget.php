<!-- Chat Widget Container -->
<div id="rideapp-chat-widget" class="chat-widget-collapsed">
    <div id="chat-header" class="chat-header">
        <div class="chat-title">
            <span class="status-indicator"></span>
            RideApp Support
        </div>
        <button id="chat-close" class="chat-close-btn">&times;</button>
    </div>
    
    <div id="chat-form-container" class="chat-form-container">
        <p class="form-instruction">Please fill in your details to start the conversation.</p>
        <div class="form-group">
            <label>Name</label>
            <input type="text" id="guest-name" placeholder="John Doe" />
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="guest-email" placeholder="john@example.com" />
        </div>
        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" id="guest-phone" placeholder="+1234567890" />
        </div>
        <div class="form-group">
            <label>How can we help?</label>
            <select id="chat-subject">
                <option value="general">General Inquiry</option>
                <option value="billing">Billing & Refunds</option>
                <option value="trip">Trip Issue</option>
                <option value="account">Account Access</option>
            </select>
        </div>
        <button id="start-chat-btn" class="start-chat-btn">Start Chat</button>
    </div>

    <div id="chat-main-container" style="display: none; flex: 1; flex-direction: column; overflow: hidden;">
        <div id="chat-body" class="chat-body">
            <div id="chat-messages" class="chat-messages">
                <!-- Messages will appear here -->
            </div>
            
            <div id="quick-actions" class="quick-actions-container" style="display: none;">
                <div class="quick-action-btns">
                    <button class="quick-action-btn" data-text="I want to book a ride">🚙 Book a Ride</button>
                    <button class="quick-action-btn" data-text="I want to cancel my ride">❌ Cancel Ride</button>
                    <button class="quick-action-btn" data-text="What is the status of my ride?">📍 Get Ride Status</button>
                </div>
            </div>
            <div id="chat-typing" class="typing-indicator" style="display: none;">
                <span></span><span></span><span></span>
            </div>
        </div>

        <div class="chat-footer">
            <input type="text" id="chat-input" placeholder="Type your message..." />
            <button id="chat-send" class="chat-send-btn">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path fill="currentColor" d="M2,21L23,12L2,3V10L17,12L2,14V21Z" />
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<button id="chat-fab" class="chat-fab">
    <div class="fab-glow"></div>
    <svg viewBox="0 0 24 24" width="30" height="30" style="position: relative; z-index: 2;">
        <path fill="white" d="M20,2H4C2.9,2,2,2.9,2,4v18l4-4h14c1.1,0,2-0.9,2-2V4C22,2.9,21.1,2,20,2z" />
    </svg>
</button>

<style>
/* CSS for Premium Chat Widget */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

:root {
    --chat-primary: #8b5cf6;
    --chat-secondary: #3b82f6;
    --chat-gradient: linear-gradient(135deg, var(--chat-primary) 0%, var(--chat-secondary) 100%);
    --chat-bg: rgba(255, 255, 255, 0.85);
    --chat-text: #1e293b;
    --chat-bot-bubble: rgba(241, 245, 249, 0.9);
    --chat-user-bubble: var(--chat-gradient);
    --chat-border: rgba(255, 255, 255, 0.4);
}

#rideapp-chat-widget {
    position: fixed;
    bottom: 100px;
    right: 25px;
    width: 360px;
    height: 520px;
    background: var(--chat-bg);
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.1), inset 0 0 0 1px var(--chat-border);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 1000;
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.3s ease-out;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    font-family: 'Outfit', sans-serif;
    transform-origin: bottom right;
}

.chat-widget-collapsed {
    transform: scale(0.5);
    opacity: 0;
    pointer-events: none;
}

.chat-header {
    padding: 20px 24px;
    background: var(--chat-gradient);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
    position: relative;
    z-index: 10;
}

.chat-title {
    font-weight: 600;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
    letter-spacing: 0.5px;
}

.status-indicator {
    width: 10px;
    height: 10px;
    background: #4ade80;
    border-radius: 50%;
    box-shadow: 0 0 10px #4ade80;
    animation: pulse-status 2s infinite;
}

@keyframes pulse-status {
    0% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0.7); }
    70% { box-shadow: 0 0 0 6px rgba(74, 222, 128, 0); }
    100% { box-shadow: 0 0 0 0 rgba(74, 222, 128, 0); }
}

.chat-close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 20px;
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    backdrop-filter: blur(5px);
}

.chat-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.05);
}

.chat-body {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.chat-body::-webkit-scrollbar { width: 5px; }
.chat-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

.chat-messages {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.message {
    max-width: 80%;
    padding: 12px 18px;
    border-radius: 18px;
    font-size: 14px;
    line-height: 1.5;
    white-space: pre-wrap;
    animation: message-slide-up 0.3s ease-out;
}

@keyframes message-slide-up {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message-bot {
    background: var(--chat-bot-bubble);
    color: var(--chat-text);
    align-self: flex-start;
    border-bottom-left-radius: 4px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}

.message-user {
    background: var(--chat-user-bubble);
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.2);
}

.chat-footer {
    padding: 18px 24px;
    background: rgba(255, 255, 255, 0.5);
    border-top: 1px solid rgba(0,0,0,0.05);
    display: flex;
    gap: 12px;
    backdrop-filter: blur(10px);
}

#chat-input {
    flex: 1;
    border: 1px solid rgba(0,0,0,0.1);
    background: rgba(255,255,255,0.8);
    padding: 12px 16px;
    border-radius: 20px;
    outline: none;
    font-family: inherit;
    font-size: 14px;
    transition: all 0.3s;
}

#chat-input:focus {
    border-color: var(--chat-primary);
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}

.chat-send-btn {
    background: var(--chat-gradient);
    color: white;
    border: none;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}

.chat-send-btn:hover {
    transform: scale(1.1) rotate(-5deg);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
}

.chat-fab {
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 65px;
    height: 65px;
    background: var(--chat-gradient);
    border-radius: 50%;
    border: none;
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.fab-glow {
    position: absolute;
    width: 100%;
    height: 100%;
    background: inherit;
    border-radius: inherit;
    z-index: 1;
    animation: fab-pulse 2.5s infinite;
}

@keyframes fab-pulse {
    0% { transform: scale(1); opacity: 0.6; }
    100% { transform: scale(1.5); opacity: 0; }
}

.chat-fab:hover {
    transform: scale(1.1);
}

/* Typing Indicator */
.typing-indicator {
    padding: 12px 18px;
    background: var(--chat-bot-bubble);
    border-radius: 18px;
    border-bottom-left-radius: 4px;
    width: fit-content;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03);
}
.typing-indicator span {
    height: 6px;
    width: 6px;
    background: #94a3b8;
    display: inline-block;
    border-radius: 50%;
    margin-right: 4px;
    animation: typing 1s infinite ease-in-out;
}
.typing-indicator span:nth-child(1) { margin-left: 2px; }
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; margin-right: 2px; }

@keyframes typing {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
}

/* Quick Actions Styles */
.quick-actions-container {
    margin-top: 10px;
    animation: fade-in-up 0.4s ease-out;
}
.quick-action-btns {
    display: flex;
    flex-direction: column;
    gap: 8px;
    align-items: flex-end;
}
.quick-action-btn {
    background: rgba(255, 255, 255, 0.7);
    border: 1px solid var(--chat-primary);
    color: var(--chat-primary);
    padding: 10px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    backdrop-filter: blur(5px);
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.1);
}
.quick-action-btn:hover {
    background: var(--chat-gradient);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
}

/* Pre-chat Form Styles */
.chat-form-container {
    padding: 30px 24px;
    display: flex;
    flex-direction: column;
    gap: 18px;
    background: transparent;
    flex: 1;
    overflow-y: auto;
}
.chat-form-container::-webkit-scrollbar { width: 5px; }
.chat-form-container::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

.form-instruction {
    font-size: 14px;
    color: var(--chat-text);
    margin-bottom: 10px;
    font-weight: 500;
    text-align: center;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #64748b;
    padding-left: 4px;
}
.form-group input, .form-group select {
    padding: 12px 16px;
    border: 1px solid rgba(0,0,0,0.1);
    background: rgba(255,255,255,0.7);
    border-radius: 12px;
    outline: none;
    font-size: 14px;
    font-family: inherit;
    transition: all 0.3s;
}
.form-group input:focus, .form-group select:focus {
    border-color: var(--chat-primary);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
.start-chat-btn {
    margin-top: 15px;
    padding: 14px;
    background: var(--chat-gradient);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 15px;
    letter-spacing: 0.5px;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
}
.start-chat-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
}
</style>

<script>
// JS for Chat Widget
document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= rtrim(base_url(), '/') ?>';
    const fab = document.getElementById('chat-fab');
    const widget = document.getElementById('rideapp-chat-widget');
    const closeBtn = document.getElementById('chat-close');
    const sendBtn = document.getElementById('chat-send');
    const chatInput = document.getElementById('chat-input');
    const chatMessages = document.getElementById('chat-messages');
    
    // Form elements
    const startChatBtn = document.getElementById('start-chat-btn');
    const formContainer = document.getElementById('chat-form-container');
    const mainContainer = document.getElementById('chat-main-container');

    let conversationId = localStorage.getItem('rideapp_chat_id');

    fab.addEventListener('click', () => {
        widget.classList.toggle('chat-widget-collapsed');
        if (!widget.classList.contains('chat-widget-collapsed')) {
            checkExistingConversation();
        }
    });

    closeBtn.addEventListener('click', () => {
        widget.classList.add('chat-widget-collapsed');
    });

    startChatBtn.addEventListener('click', initConversation);

    sendBtn.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    async function checkExistingConversation() {
        if (!conversationId) return;

        try {
            const response = await fetch(`${baseUrl}/api/support/messages/${conversationId}`);
            if (response.ok) {
                // Conversation exists and is active, skip form
                formContainer.style.display = 'none';
                mainContainer.style.display = 'flex';
                await loadMessages();
                if (lastMessageCount <= 1) {
                    document.getElementById('quick-actions').style.display = 'block';
                }
            } else {
                // Stale ID, clear it
                localStorage.removeItem('rideapp_chat_id');
                conversationId = null;
            }
        } catch (error) {
            console.error('Check Conversation Error:', error);
        }
    }

    async function initConversation() {
        try {
            const name = document.getElementById('guest-name').value;
            const email = document.getElementById('guest-email').value;
            const phone = document.getElementById('guest-phone').value;
            const subject = document.getElementById('chat-subject').value;

            if (!name || !email || !phone) {
                alert('Please fill in your name, email, and phone number.');
                return;
            }

            startChatBtn.disabled = true;
            startChatBtn.innerText = 'Connecting...';

            const formData = new FormData();
            formData.append('guest_name', name);
            formData.append('guest_email', email);
            formData.append('guest_phone', phone);
            formData.append('subject', subject);

            const response = await fetch(`${baseUrl}/api/support/conversation`, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const data = await response.json();
            conversationId = data.id;
            localStorage.setItem('rideapp_chat_id', conversationId);
            
            // Switch view
            formContainer.style.display = 'none';
            mainContainer.style.display = 'flex';
            document.getElementById('quick-actions').style.display = 'block';

            loadMessages();
        } catch (error) {
            console.error('Chat Init Error:', error);
            alert('Could not start chat. Please check your connection.');
            startChatBtn.disabled = false;
            startChatBtn.innerText = 'Start Chat';
        }
    }

    let lastMessageCount = 0;
    let pollInterval = null;

    async function loadMessages() {
        if (!conversationId) return;
        try {
            const response = await fetch(`${baseUrl}/api/support/messages/${conversationId}`);
            
            // If conversation closed or not found
            if (response.status === 404) {
                localStorage.removeItem('rideapp_chat_id');
                conversationId = null;
                if (pollInterval) clearInterval(pollInterval);
                alert('This conversation has been closed.');
                location.reload();
                return;
            }

            const messages = await response.json();
            
            if (messages.length > lastMessageCount) {
                // Only append new messages
                const newMessages = messages.slice(lastMessageCount);
                newMessages.forEach(msg => {
                    appendMessage(msg.message, msg.sender_role);
                });
                lastMessageCount = messages.length;
                scrollToBottom();
            }
        } catch (error) {
            console.error('Load Messages Error:', error);
        }
    }

    async function sendMessage(presetText = null) {
        const message = presetText || chatInput.value.trim();
        if (!message) return;

        chatInput.value = '';
        document.getElementById('quick-actions').style.display = 'none';
        
        appendMessage(message, 'user');
        lastMessageCount++; // Increment optimistically
        scrollToBottom();

        try {
            const formData = new FormData();
            formData.append('conversation_id', conversationId);
            formData.append('message', message);

            await fetch(`${baseUrl}/api/support/message`, {
                method: 'POST',
                body: formData
            });

            // Force immediate reload to get bot response
            setTimeout(loadMessages, 500);
        } catch (error) {
            console.error('Send Message Error:', error);
        }
    }

    // Handle Quick Action Clicks
    document.querySelectorAll('.quick-action-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            sendMessage(btn.dataset.text);
        });
    });

    function appendMessage(text, role) {
        const div = document.createElement('div');
        div.className = `message message-${role}`;
        div.innerText = text;
        chatMessages.appendChild(div);
    }

    function scrollToBottom() {
        const body = document.getElementById('chat-body');
        if (body) body.scrollTop = body.scrollHeight;
    }

    // Auto-polling for new messages (e.g. from an agent)
    function startPolling() {
        if (!pollInterval) {
            pollInterval = setInterval(loadMessages, 3000);
        }
    }

    // Modify initConversation to start polling
    const originalInit = initConversation;
    initConversation = async function() {
        await originalInit();
        if (conversationId) startPolling();
    };

    // Modify checkExistingConversation to start polling
    const originalCheck = checkExistingConversation;
    checkExistingConversation = async function() {
        await originalCheck();
        if (conversationId) startPolling();
    };
});
</script>
