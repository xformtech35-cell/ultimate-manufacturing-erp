<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<body class="hold-transition skin-blue sidebar-mini">
<div id="loader" class="center"></div>
<div class="wrapper">

<!-- Google Fonts & Marked.js -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/9.1.6/marked.min.js"></script>

<style>
/* Modern Claude-Style Chat layout styling */
.chat-container-card {
    font-family: 'Inter', sans-serif;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
    border: 1px solid #eef2f6;
    overflow: hidden;
    margin-bottom: 30px;
}

.chat-header-bar {
    background: #f8fafc;
    border-bottom: 1px solid #f1f5f9;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.chat-header-title {
    font-size: 16px;
    font-weight: 600;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Flex Wrapper for Columns */
.chat-layout-wrapper {
    display: flex;
    height: 580px;
    background: #ffffff;
}

/* Left Sidebar - Chat History */
.chat-sidebar {
    width: 270px;
    border-right: 1px solid #e2e8f0;
    background: #f8fafc;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
}

.new-chat-section {
    padding: 14px 16px;
    border-bottom: 1px solid #e2e8f0;
}

.new-chat-btn {
    width: 100%;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px;
    font-size: 13px;
    font-weight: 500;
    color: #4f46e5;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.02);
}

.new-chat-btn:hover {
    border-color: #4f46e5;
    background: #f1f5f9;
    transform: translateY(-0.5px);
}

.history-section {
    flex-grow: 1;
    overflow-y: auto;
    padding: 16px 12px;
}

.history-title {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    margin-bottom: 10px;
    padding-left: 8px;
    font-weight: 700;
}

.history-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.history-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 9px 12px;
    border-radius: 8px;
    cursor: pointer;
    margin-bottom: 5px;
    transition: all 0.15s ease;
    font-size: 13px;
    color: #475569;
    position: relative;
}

.history-item:hover {
    background: #f1f5f9;
    color: #1e293b;
}

.history-item.active {
    font-weight: 500;
    background: #e2e8f0;
    color: #1e293b;
    border-left: 3px solid #4f46e5;
}

.history-text {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    padding-right: 10px;
}

.history-actions {
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0.15;
    transition: opacity 0.2s;
}

.history-item:hover .history-actions, .history-item.active .history-actions {
    opacity: 1;
}

.action-icon {
    font-size: 11px;
    color: #64748b;
    cursor: pointer;
    padding: 2px 4px;
    border-radius: 4px;
    transition: all 0.15s ease;
}

.action-icon:hover {
    color: #1e293b;
    background: #cbd5e1;
}

.action-icon.starred {
    color: #eab308 !important;
    opacity: 1 !important;
}

/* Right Panel - Active Chat Panel */
.chat-main-panel {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #fafbfd;
}

.chat-body-area {
    flex-grow: 1;
    overflow-y: auto;
    padding: 24px;
}

/* Custom Message bubbles styling */
.msg-row {
    display: flex;
    margin-bottom: 20px;
    align-items: flex-start;
    gap: 12px;
    max-width: 85%;
}

.msg-row.user-msg {
    margin-left: auto;
    flex-direction: row-reverse;
}

.avatar-container {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.avatar-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.bubble-content {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px 16px 16px 16px;
    padding: 12px 16px;
    color: #334155;
    font-size: 14px;
    line-height: 1.5;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.user-msg .bubble-content {
    background: #4f46e5;
    border: 1px solid #4f46e5;
    color: #ffffff;
    border-radius: 16px 4px 16px 16px;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15);
}

.bubble-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 10px 0;
    font-size: 13px;
    background: #ffffff;
}

.bubble-content th, .bubble-content td {
    border: 1px solid #e2e8f0;
    padding: 8px 10px;
    text-align: left;
}

.bubble-content th {
    background: #f8fafc;
    color: #1e293b;
    font-weight: 600;
}

.msg-meta {
    font-size: 11px;
    color: #94a3b8;
    margin-top: 4px;
    display: block;
}

.user-msg .msg-meta {
    text-align: right;
}

/* Suggestions container */
.suggestions-panel {
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    padding: 12px 20px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.pill-btn {
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 6px 14px;
    font-size: 12px;
    font-weight: 500;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.pill-btn:hover {
    background: #4f46e5;
    color: #ffffff;
    border-color: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(79, 70, 229, 0.15);
}

/* Chat Input Bar */
.chat-input-bar {
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    padding: 16px 20px;
}

.modern-input-group {
    display: flex;
    gap: 10px;
}

.modern-chat-input {
    flex-grow: 1;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    color: #1e293b;
    outline: none;
    transition: border-color 0.2s;
}

.modern-chat-input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.modern-send-btn {
    background: #4f46e5;
    border: none;
    color: #ffffff;
    padding: 0 20px;
    border-radius: 8px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: background 0.2s;
    display: flex;
    align-items: center;
    gap: 6px;
}

.modern-send-btn:hover {
    background: #4338ca;
}

/* Pulsing typing animation */
.typing-indicator {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 6px 10px;
}

.typing-dot {
    width: 6px;
    height: 6px;
    background: #94a3b8;
    border-radius: 50%;
    animation: pulse 1.4s infinite ease-in-out both;
}

.typing-dot:nth-child(2) { animation-delay: 0.2s; }
.typing-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes pulse {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Content Header -->
    <section class="content-header">
        <h1>Ask AI Assistant <small>Interactive query control interface</small></h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url() . 'AiController/index'; ?>">AI Insights</a></li>
            <li class="active">Ask AI</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                
                <!-- Premium Chat Container -->
                <div class="chat-container-card">
                    <div class="chat-header-bar">
                        <h3 class="chat-header-title">
                            <i class="fa fa-android text-primary"></i> 
                            ERP AI Co-Pilot
                        </h3>
                        <span class="label label-success" style="font-size: 11px;">Grounded Mode Active</span>
                    </div>

                    <!-- Layout Wrapper for columns -->
                    <div class="chat-layout-wrapper">
                        
                        <!-- Left Sidebar: Recents -->
                        <div class="chat-sidebar">
                            <div class="new-chat-section">
                                <button type="button" class="new-chat-btn" id="newChatSessionBtn">
                                    <i class="fa fa-plus"></i> New Chat
                                </button>
                            </div>
                            
                            <div class="history-section">
                                <div class="history-title">Recents</div>
                                <ul class="history-list">
                                    <?php if (!empty($sessions)): ?>
                                        <?php foreach ($sessions as $session): ?>
                                            <li class="history-item <?= ($session['id'] == $active_session_id) ? 'active' : '' ?>" 
                                                data-id="<?= $session['id'] ?>">
                                                <span class="history-text" onclick="window.location.href='<?= base_url() ?>AiController/chat?session_id=<?= $session['id'] ?>'">
                                                    <?= htmlspecialchars($session['title']) ?>
                                                </span>
                                                <div class="history-actions">
                                                    <!-- Star Button -->
                                                    <i class="fa <?= $session['is_starred'] ? 'fa-star starred' : 'fa-star-o' ?> action-icon star-session-btn" 
                                                       data-id="<?= $session['id'] ?>" title="Star Conversation"></i>
                                                    <!-- Rename Button -->
                                                    <i class="fa fa-pencil action-icon rename-session-btn" 
                                                       data-id="<?= $session['id'] ?>" data-title="<?= htmlspecialchars($session['title']) ?>" title="Rename"></i>
                                                    <!-- Delete Button -->
                                                    <i class="fa fa-trash action-icon delete-session-btn" 
                                                       data-id="<?= $session['id'] ?>" title="Delete"></i>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <li class="text-muted text-center" style="font-size: 12px; padding: 10px;">No recent chats.</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Right Panel: Active Chat -->
                        <div class="chat-main-panel">
                            
                            <!-- Chat Body Area -->
                            <div class="chat-body-area" id="chatMessages">
                                
                                <!-- Welcome message if conversation is new/empty -->
                                <?php if (empty($chat_messages)): ?>
                                    <div class="msg-row">
                                        <div class="avatar-container">
                                            <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="AI Avatar">
                                        </div>
                                        <div>
                                            <div class="bubble-content">
                                                Hello! I am your manufacturing ERP assistant. I can query live database structures safely to report anomalies, duplicates, or specific records.
                                                <br><br>
                                                <strong>How can I help you today?</strong> Try typing or clicking one of the suggestions below.
                                            </div>
                                            <span class="msg-meta">AI Assistant &bull; <?= date('h:i A') ?></span>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <!-- Render message history -->
                                    <?php foreach ($chat_messages as $msg): 
                                        $is_user = ($msg['sender'] === 'user');
                                        $row_class = $is_user ? 'user-msg' : '';
                                        $sender_lbl = $is_user ? 'You' : 'AI Assistant';
                                        $avatar = $is_user 
                                            ? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' 
                                            : 'https://cdn-icons-png.flaticon.com/512/4712/4712035.png';
                                    ?>
                                        <div class="msg-row <?= $row_class ?>">
                                            <div class="avatar-container">
                                                <img src="<?= $avatar ?>" alt="Avatar">
                                            </div>
                                            <div>
                                                <div class="bubble-content">
                                                    <?php if ($is_user): ?>
                                                        <?= nl2br(htmlspecialchars($msg['message_text'])) ?>
                                                    <?php else: ?>
                                                        <script>
                                                            document.write(marked.parse(<?= json_encode($msg['message_text']) ?>));
                                                        </script>
                                                    <?php endif; ?>
                                                </div>
                                                <span class="msg-meta"><?= $sender_lbl ?> &bull; <?= date('h:i A', strtotime($msg['created_at'])) ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </div>

                            <!-- Suggestions Panel -->
                            <div class="suggestions-panel">
                                <button type="button" class="pill-btn suggestion-btn" data-text="Scan for duplicate BOMs for approved systems">
                                    <i class="fa fa-copy text-danger"></i> Scan Duplicates
                                </button>
                                <button type="button" class="pill-btn suggestion-btn" data-text="Show unapproved BOMs with active MRP runs">
                                    <i class="fa fa-cogs text-warning"></i> MRP Anomalies
                                </button>
                                <button type="button" class="pill-btn suggestion-btn" data-text="List the oldest stale drafts in the queue">
                                    <i class="fa fa-clock-o text-info"></i> Oldest Drafts
                                </button>
                            </div>

                            <!-- Chat Input form -->
                            <div class="chat-input-bar">
                                <form id="chatForm">
                                    <div class="modern-input-group">
                                        <input type="text" id="chatInput" name="message" 
                                               placeholder="Ask about BOMs, Sales Orders (e.g. SO XFORM-2627-PS-4015-OC-129), or shortages..." 
                                               class="modern-chat-input" autocomplete="off">
                                        <input type="hidden" id="activeSessionId" value="<?= $active_session_id ?>">
                                        <button type="submit" id="sendBtn" class="modern-send-btn">
                                            Send <i class="fa fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php $this->load->view('admin/footer'); ?>
<div class="control-sidebar-bg"></div>
</div><!-- /.wrapper -->

<script>
$(document).ready(function() {
    
    function scrollChat() {
        var $msg = $('#chatMessages');
        if ($msg.length) {
            $msg.scrollTop($msg[0].scrollHeight);
        }
    }
    scrollChat();

    function appendMessage(sender, text, isUser) {
        var time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        var rowClass = isUser ? 'user-msg' : '';
        var avatarImg = isUser 
            ? 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' 
            : 'https://cdn-icons-png.flaticon.com/512/4712/4712035.png';

        var formattedText = isUser ? text.replace(/\n/g, '<br>') : marked.parse(text);

        var html = '<div class="msg-row ' + rowClass + '">' +
                   '  <div class="avatar-container">' +
                   '    <img src="' + avatarImg + '" alt="Avatar">' +
                   '  </div>' +
                   '  <div>' +
                   '    <div class="bubble-content">' +
                            formattedText +
                   '    </div>' +
                   '    <span class="msg-meta">' + sender + ' &bull; ' + time + '</span>' +
                   '  </div>' +
                   '</div>';

        $('#chatMessages').append(html);
        scrollChat();
    }

    function handleSend(messageText) {
        if (!messageText.trim()) return;

        var sessionId = $('#activeSessionId').val();
        appendMessage('You', messageText, true);
        $('#chatInput').val('');
        
        // Show typing indicator
        var indicatorId = 'ai-typing-' + Date.now();
        var indicatorHtml = '<div class="msg-row" id="' + indicatorId + '">' +
                            '  <div class="avatar-container">' +
                            '    <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Avatar">' +
                            '  </div>' +
                            '  <div>' +
                            '    <div class="bubble-content" style="padding: 10px 14px;">' +
                            '      <div class="typing-indicator">' +
                            '        <div class="typing-dot"></div>' +
                            '        <div class="typing-dot"></div>' +
                            '        <div class="typing-dot"></div>' +
                            '      </div>' +
                            '    </div>' +
                            '    <span class="msg-meta">AI Assistant &bull; Typing...</span>' +
                            '  </div>' +
                            '</div>';
        $('#chatMessages').append(indicatorHtml);
        scrollChat();

        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_chat_message',
            type: 'POST',
            dataType: 'json',
            data: { message: messageText, session_id: sessionId },
            success: function(response) {
                $('#' + indicatorId).remove();
                if (response.success) {
                    appendMessage('AI Assistant', response.reply, false);
                } else {
                    appendMessage('AI Assistant', 'Sorry, I encountered an issue: ' + response.message, false);
                }
            },
            error: function() {
                $('#' + indicatorId).remove();
                appendMessage('AI Assistant', 'Connection timeout. Failed to reach the server.', false);
            }
        });
    }

    $('#chatForm').on('submit', function(e) {
        e.preventDefault();
        var text = $('#chatInput').val();
        handleSend(text);
    });

    $(document).on('click', '.suggestion-btn', function() {
        var text = $(this).data('text');
        handleSend(text);
    });

    // ----------------------------------------------------
    // PERSISTENT CHAT SESSIONS SIDEBAR HANDLERS
    // ----------------------------------------------------
    
    // Create new chat session
    $('#newChatSessionBtn').on('click', function() {
        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_new_chat_session',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = '<?= base_url() ?>AiController/chat?session_id=' + response.session_id;
                } else {
                    alert('Error creating session');
                }
            }
        });
    });

    // Rename chat session
    $(document).on('click', '.rename-session-btn', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        var oldTitle = $(this).data('title');
        var newTitle = prompt("Enter new chat conversation title:", oldTitle);
        
        if (newTitle !== null && newTitle.trim() !== '') {
            $.ajax({
                url: '<?= base_url() ?>AiController/ajax_rename_chat_session',
                type: 'POST',
                dataType: 'json',
                data: { id: id, title: newTitle.trim() },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error renaming conversation');
                    }
                }
            });
        }
    });

    // Star/Unstar chat session
    $(document).on('click', '.star-session-btn', function(e) {
        e.stopPropagation();
        var $btn = $(this);
        var id = $btn.data('id');
        
        $.ajax({
            url: '<?= base_url() ?>AiController/ajax_star_chat_session',
            type: 'POST',
            dataType: 'json',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    if (response.is_starred) {
                        $btn.removeClass('fa-star-o').addClass('fa-star starred');
                    } else {
                        $btn.removeClass('fa-star starred').addClass('fa-star-o');
                    }
                    location.reload(); // Reload to update ordering
                }
            }
        });
    });

    // Delete chat session
    $(document).on('click', '.delete-session-btn', function(e) {
        e.stopPropagation();
        var id = $(this).data('id');
        
        if (confirm("Are you sure you want to permanently delete this chat conversation and all message logs?")) {
            $.ajax({
                url: '<?= base_url() ?>AiController/ajax_delete_chat_session',
                type: 'POST',
                dataType: 'json',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        window.location.href = '<?= base_url() ?>AiController/chat';
                    } else {
                        alert('Error deleting conversation');
                    }
                }
            });
        }
    });
});
</script>
