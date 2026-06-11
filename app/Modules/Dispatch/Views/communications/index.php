<?= $this->extend('layouts/master') ?>

<?= $this->section('content') ?>
<div class="container-fluid" style="padding: 1.5rem; height: 100vh; overflow: hidden; display: flex; flex-direction: column;">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        .log-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            margin-bottom: 0.75rem;
            padding: 1.25rem 1.5rem;
            display: grid;
            grid-template-columns: 140px 1.5fr 2fr 180px;
            gap: 1.5rem;
            align-items: center;
            transition: all 0.2s;
        }
        .log-card:hover { 
            border-color: var(--primary); 
            transform: translateY(-2px); 
            box-shadow: var(--shadow-sm); 
        }

        .log-time { font-family: monospace; font-weight: 700; color: var(--text-primary); font-size: 1rem; }
        .log-date { font-size: 0.8rem; color: var(--text-secondary); margin-top: 4px; }
        
        .participant-container { display: flex; align-items: center; gap: 0.75rem; }
        .participant-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.9rem; }
        .avatar-customer { background: rgba(59, 130, 246, 0.1); color: var(--info); }
        .avatar-driver { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; } 
        .avatar-system { background: rgba(239, 68, 68, 0.1); color: var(--danger); }
        .avatar-unknown { background: var(--bg-body); color: var(--text-secondary); border: 1px solid var(--border-color); }

        .participant-name { font-weight: 600; font-size: 0.95rem; color: var(--text-primary); }
        .participant-phone { font-size: 0.8rem; color: var(--text-secondary); font-family: monospace; margin-top: 2px;}

        .log-content { 
            font-size: 0.95rem; 
            color: var(--text-primary); 
            background: var(--bg-body); 
            padding: 0.75rem; 
            border-radius: 6px; 
            border: 1px solid var(--border-color);
            word-break: break-word;
        }
        .log-content.voice-input {
            font-family: monospace;
            letter-spacing: 2px;
            font-weight: 600;
        }

        .action-taken { font-weight: 600; font-size: 0.9rem; color: var(--text-primary); text-align: right; margin-bottom: 8px;}
        .action-error { color: var(--danger); }
        .action-proxy { color: var(--primary); }
        
        .badges-container { display: flex; justify-content: flex-end; gap: 6px; }
        .badge-custom {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        
        .badge-inbound { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .badge-outbound { background: rgba(59, 130, 246, 0.1); color: var(--info); border: 1px solid rgba(59, 130, 246, 0.2); }
        
        .badge-sms { background: rgba(99, 102, 241, 0.1); color: var(--primary); border: 1px solid rgba(99, 102, 241, 0.2); }
        .badge-voice { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); }

        .empty-state { text-align: center; padding: 4rem 2rem; color: var(--text-secondary); }
        .empty-state i { stroke-width: 1px; color: var(--border-color); margin-bottom: 1rem; }
    </style>

    <!-- Top Header -->
    <div style="flex-shrink: 0; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; padding-top: 1rem;">
        <div>
            <h1 class="h3" style="margin:0;">Communication Center</h1>
            <div style="color:var(--text-secondary); font-size:0.95rem;">Real-time log of all Twilio SMS and Automated Voice interactions.</div>
        </div>
        <div>
            <a href="javascript:window.location.reload(true)" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary); display: flex; align-items: center; gap: 6px;">
                <i data-lucide="refresh-cw" style="width: 16px; height: 16px;"></i> Refresh Feed
            </a>
        </div>
    </div>

    <!-- Main Content Area with Logs and Test Simulator -->
    <div style="flex: 1; overflow: hidden; display: flex; gap: 1.5rem;">
        
        <!-- Scrollable Content (Logs) -->
        <div style="flex: 1; overflow-y: auto; padding-right: 4px; padding-bottom: 2rem;">
            <?php if (empty($logs)): ?>
                <div class="empty-state">
                    <i data-lucide="message-square-dashed" width="64" height="64"></i>
                    <h4 style="color: var(--text-primary);">No Communications</h4>
                    <p>No Twilio communications have been logged yet. Check your webhooks!</p>
                </div>
            <?php else: ?>
                <?php foreach($logs as $log): ?>
                    <?php
                        // Process Action styling
                        $actionClass = 'action-taken';
                        $actionText = strtolower($log->action_taken);
                        if (strpos($actionText, 'error') !== false || strpos($actionText, 'invalid') !== false || strpos($actionText, 'failed') !== false) {
                            $actionClass .= ' action-error';
                        } elseif (strpos($actionText, 'proxy') !== false || strpos($actionText, 'proxied') !== false) {
                            $actionClass .= ' action-proxy';
                        }
                        
                        // Determine Avatar Initials & Style
                        $avatarClass = 'avatar-unknown';
                        $initials = '?';
                        $nameText = 'Unknown Caller';
                        
                        if ($log->user_type === 'driver') {
                            $avatarClass = 'avatar-driver';
                            $initials = substr($log->user_first_name, 0, 1) . substr($log->user_last_name, 0, 1);
                            $nameText = esc($log->user_first_name . ' ' . $log->user_last_name) . ' <span style="font-size:0.7rem; color:var(--text-secondary); font-weight:normal;">(Driver)</span>';
                        } elseif ($log->user_type === 'customer') {
                            $avatarClass = 'avatar-customer';
                            $initials = substr($log->user_first_name, 0, 1) . substr($log->user_last_name, 0, 1);
                            $nameText = esc($log->user_first_name . ' ' . $log->user_last_name) . ' <span style="font-size:0.7rem; color:var(--text-secondary); font-weight:normal;">(Customer)</span>';
                        } elseif ($log->user_type === 'system') {
                            $avatarClass = 'avatar-system';
                            $initials = 'SYS';
                            $nameText = 'Automated System';
                        }
                    ?>
                    <div class="log-card">
                        <!-- Col 1: Time & Date -->
                        <div>
                            <div class="log-time"><?= date('g:i:s A', strtotime($log->created_at)) ?></div>
                            <div class="log-date"><?= date('M j, Y', strtotime($log->created_at)) ?></div>
                        </div>

                        <!-- Col 2: Participant -->
                        <div class="participant-container">
                            <div class="participant-avatar <?= $avatarClass ?>">
                                <?= esc($initials) ?>
                            </div>
                            <div>
                                <div class="participant-name"><?= $nameText ?></div>
                                <div class="participant-phone">
                                    <?= $log->direction === 'inbound' ? esc($log->from_number) : esc($log->to_number) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Col 3: Content -->
                        <div>
                            <div class="log-content <?= $log->type === 'voice' ? 'voice-input' : '' ?>">
                                <?php if ($log->type === 'voice'): ?>
                                    <i data-lucide="mic" style="width: 14px; height: 14px; color: var(--text-secondary); margin-right: 6px; vertical-align: middle;"></i>
                                <?php endif; ?>
                                <?= nl2br(esc($log->content)) ?>
                            </div>
                        </div>

                        <!-- Col 4: Action & Badges -->
                        <div>
                            <div class="<?= $actionClass ?>">
                                <?php if (strpos($actionClass, 'action-proxy') !== false): ?>
                                    <i data-lucide="phone-forwarded" style="width: 14px; height: 14px; margin-right: 4px; vertical-align: middle;"></i>
                                <?php endif; ?>
                                <?= esc($log->action_taken) ?>
                            </div>
                            <div class="badges-container">
                                <?php if ($log->direction === 'inbound'): ?>
                                    <span class="badge-custom badge-inbound"><i data-lucide="arrow-down-left" style="width: 10px; height: 10px;"></i> Inbound</span>
                                <?php else: ?>
                                    <span class="badge-custom badge-outbound"><i data-lucide="arrow-up-right" style="width: 10px; height: 10px;"></i> Outbound</span>
                                <?php endif; ?>
                                
                                <?php if ($log->type === 'sms'): ?>
                                    <span class="badge-custom badge-sms">SMS</span>
                                <?php else: ?>
                                    <span class="badge-custom badge-voice">Voice</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- SMS Test Simulator Chat Box -->
        <div style="width: 400px; flex-shrink: 0; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-surface);">
            
            <!-- Header -->
            <div style="padding: 1.25rem 1rem; border-bottom: 1px solid var(--border-color); background: var(--bg-surface); display: flex; flex-direction: column; gap: 12px; flex-shrink: 0;">
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="message-square" style="color: var(--primary);"></i>
                        <h3 style="margin: 0; font-size: 1.05rem; color: var(--text-primary);">Test SMS Simulator</h3>
                    </div>
                </div>
                <div>
                    <label style="display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 4px; text-transform: uppercase;">Simulate as User</label>
                    <input list="test_users_list" id="sim_phone" class="form-control" placeholder="Select or type phone number..." required style="width: 100%; padding: 0.6rem; border: 1px solid var(--border-color); border-radius: 6px; background: var(--bg-body); color: var(--text-primary); font-size: 0.9rem;">
                    <datalist id="test_users_list">
                        <?php if(!empty($testUsers)): ?>
                            <?php foreach($testUsers as $user): ?>
                                <option value="<?= esc($user['phone']) ?>"><?= esc($user['name']) ?> (<?= esc($user['type']) ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </datalist>
                </div>
            </div>

            <!-- Chat History Area -->
            <div id="chat-history" style="flex: 1; overflow-y: auto; padding: 1.25rem 1rem; display: flex; flex-direction: column; gap: 12px; background: var(--bg-body);">
                <div style="text-align: center; color: var(--text-secondary); font-size: 0.8rem; margin-top: 1rem; padding: 0 1rem;">
                    Select a phone number above and send a message. Your exchange will appear here in real-time.
                </div>
            </div>

            <!-- Input Area -->
            <form id="sms-simulator-form" style="padding: 1rem; border-top: 1px solid var(--border-color); background: var(--bg-surface); display: flex; gap: 10px; align-items: center; flex-shrink: 0;">
                <input type="text" id="sim_message" class="form-control" placeholder="Type numeric reply (e.g. 1)..." required autocomplete="off" style="flex: 1; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: 24px; background: var(--bg-body); color: var(--text-primary); font-size: 0.95rem;">
                <button type="submit" class="btn btn-primary" style="border-radius: 50%; width: 44px; height: 44px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2); transition: transform 0.1s;">
                    <i data-lucide="send" style="width: 18px; height: 18px; margin-left: -2px;"></i>
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    const chatHistory = document.getElementById('chat-history');

    function appendMessage(text, isSystem) {
        // Remove the empty state placeholder if it exists
        const placeholder = chatHistory.querySelector('div[style*="text-align: center"]');
        if (placeholder) {
            placeholder.remove();
        }

        const bubbleContainer = document.createElement('div');
        bubbleContainer.style.display = 'flex';
        bubbleContainer.style.flexDirection = 'column';
        bubbleContainer.style.maxWidth = '85%';
        
        const bubble = document.createElement('div');
        bubble.style.padding = '10px 14px';
        bubble.style.fontSize = '0.95rem';
        bubble.style.wordBreak = 'break-word';
        bubble.style.lineHeight = '1.4';
        
        const timeLabel = document.createElement('span');
        timeLabel.style.fontSize = '0.7rem';
        timeLabel.style.color = 'var(--text-secondary)';
        timeLabel.style.marginTop = '4px';
        const timeString = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        timeLabel.innerText = timeString;

        if (isSystem) {
            // System (Outbound) - Left side, Gray
            bubbleContainer.style.alignSelf = 'flex-start';
            bubble.style.background = 'var(--bg-surface)';
            bubble.style.border = '1px solid var(--border-color)';
            bubble.style.color = 'var(--text-primary)';
            bubble.style.borderRadius = '16px 16px 16px 4px';
            timeLabel.style.alignSelf = 'flex-start';
        } else {
            // User (Inbound) - Right side, Blue
            bubbleContainer.style.alignSelf = 'flex-end';
            bubble.style.background = 'var(--primary)';
            bubble.style.color = '#ffffff';
            bubble.style.borderRadius = '16px 16px 4px 16px';
            bubble.style.boxShadow = '0 2px 4px rgba(59, 130, 246, 0.1)';
            timeLabel.style.alignSelf = 'flex-end';
        }
        
        // Handle newlines gracefully
        bubble.innerHTML = text.replace(/\n/g, '<br>');
        
        bubbleContainer.appendChild(bubble);
        bubbleContainer.appendChild(timeLabel);
        chatHistory.appendChild(bubbleContainer);
        chatHistory.scrollTop = chatHistory.scrollHeight;
        
        return bubble;
    }

    document.getElementById('sms-simulator-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const phone = document.getElementById('sim_phone').value.trim();
        const messageInput = document.getElementById('sim_message');
        const message = messageInput.value.trim();
        
        if (!phone || !message) return;

        // Clear input immediately and show user message
        messageInput.value = '';
        appendMessage(message, false);

        // Prepare data to hit webhook exactly like Twilio
        const formData = new URLSearchParams();
        formData.append('From', phone);
        formData.append('To', '+15555555555'); // Dummy system number
        formData.append('Body', message);
        formData.append('MessageSid', 'SM' + Math.random().toString(36).substring(2, 15));

        try {
            const res = await fetch('<?= base_url('sms/webhook/twilio') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData.toString()
            });

            if (res.ok) {
                const text = await res.text();
                // Parse XML TwiML to show reply
                const parser = new DOMParser();
                const xmlDoc = parser.parseFromString(text, "text/xml");
                const messageNode = xmlDoc.getElementsByTagName("Message")[0];
                
                if (messageNode && messageNode.childNodes[0]) {
                    appendMessage(messageNode.childNodes[0].nodeValue, true);
                } else {
                    // No TwiML auto-reply was generated
                    appendMessage("✅ System processed message (No auto-reply).", true);
                }
            } else {
                appendMessage("❌ Error: Webhook returned " + res.status, true);
            }
        } catch (error) {
            appendMessage("❌ Request failed: " + error.message, true);
        }
    });
</script>
<?= $this->endSection() ?>
