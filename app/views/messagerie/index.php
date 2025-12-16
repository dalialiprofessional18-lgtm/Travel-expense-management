<?php 
$title = 'Messagerie';
$conversations = $conversations ?? [];
$contacts = $contacts ?? [];
$unreadCount = $unreadCount ?? 0;
$userId = $userId;
$userRole = $userRole;
$currentConversation = $currentConversation ?? null;
$currentConversationId = $currentConversationId ?? null;
$messages = $messages ?? [];

ob_start();
?>

<main class="admin-main">
    <div class="container-fluid p-4 p-lg-4">
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h3 mb-0">Messagerie</h1>
                <p class="text-muted mb-0">Communiquez avec votre équipe</p>
            </div>
            <div class="d-flex gap-2">
                <span class="btn btn-outline-secondary">
                    <i class="bi bi-envelope me-1"></i>
                    <span class="unread-count"><?= $unreadCount ?></span> non lu(s)
                </span>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="bi bi-plus-lg me-2"></i>Nouveau Message
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="messages-container">
            <div class="messages-layout">
                
                <!-- Conversations Sidebar -->
                <div class="messages-sidebar">
                    
                    <!-- Sidebar Header -->
                    <div class="messages-header">
                        <h5 class="header-title mb-0">Messages</h5>
                        <div class="d-flex gap-2 mt-3">
                            <div class="search-container flex-grow-1">
                                <input type="search" 
                                       class="form-control" 
                                       id="searchConversations"
                                       placeholder="Rechercher conversations..."
                                       onkeyup="searchConversations(this.value)">
                                <i class="bi bi-search search-icon"></i>
                            </div>
                            <button class="btn btn-primary btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#newChatModal"
                                    title="Nouveau Message">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Conversations List -->
                    <div class="conversations-list" id="conversationsList">
                        <?php if (empty($conversations)): ?>
                            <div class="empty-conversations">
                                <i class="bi bi-chat-dots"></i>
                                <p>Aucune conversation</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($conversations as $conv): ?>
                                <?php
                                $avatarUrl = !empty($conv['other_user_avatar']) 
                                    ? $conv['other_user_avatar'] 
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($conv['other_user_name']) . '&size=48&background=667eea&color=fff';
                                
                                $isActive = $currentConversationId == $conv['conversation_id'];
                                $isUnread = $conv['unread_count'] > 0;
                                ?>
                                <a href="/messagerie/conversation/<?= $conv['conversation_id'] ?>" 
                                   class="conversation-item <?= $isActive ? 'active' : '' ?> <?= $isUnread ? 'unread' : '' ?>"
                                   data-conversation-id="<?= $conv['conversation_id'] ?>"
                                   data-search-text="<?= strtolower(htmlspecialchars($conv['other_user_name'])) ?>"
                                   onclick="handleConversationClick(event, <?= $conv['conversation_id'] ?>)">
                                    
                                    <div class="conversation-avatar">
                                        <img src="<?= $avatarUrl ?>" 
                                             alt="<?= htmlspecialchars($conv['other_user_name']) ?>">
                                    </div>
                                    
                                    <div class="conversation-info">
                                        <div class="conversation-header">
                                            <h6 class="conversation-name"><?= htmlspecialchars($conv['other_user_name']) ?></h6>
                                            <span class="conversation-time">
                                                <?php
                                                if ($conv['last_message_date']) {
                                                    $date = new DateTime($conv['last_message_date']);
                                                    $now = new DateTime();
                                                    $diff = $now->diff($date);
                                                    
                                                    if ($diff->d == 0) {
                                                        echo $date->format('H:i');
                                                    } elseif ($diff->d == 1) {
                                                        echo 'Hier';
                                                    } else {
                                                        echo $date->format('d/m');
                                                    }
                                                }
                                                ?>
                                            </span>
                                        </div>
                                        <p class="conversation-preview">
                                            <?= htmlspecialchars(substr($conv['last_message'] ?? 'Aucun message', 0, 40)) ?>
                                        </p>
                                        <div class="conversation-footer">
                                            <span class="conversation-type"><?= ucfirst($conv['other_user_role']) ?></span>
                                            <?php if ($conv['unread_count'] > 0): ?>
                                                <span class="unread-badge"><?= $conv['unread_count'] ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Chat Area -->
                <div class="chat-area">
                    <?php if ($currentConversation): ?>
                        
                        <!-- Active Chat -->
                        <div class="active-chat">
                            
                            <!-- Chat Header -->
                            <div class="chat-header">
                                <div class="chat-user-info">
                                    <?php
                                    $chatAvatarUrl = !empty($currentConversation['other_user_avatar']) 
                                        ?  $currentConversation['other_user_avatar'] 
                                        : 'https://ui-avatars.com/api/?name=' . urlencode($currentConversation['other_user_name']) . '&size=40&background=667eea&color=fff';
                                    ?>
                                    <div class="chat-avatar-container">
                                        <img src="<?= $chatAvatarUrl ?>" 
                                             class="chat-avatar" 
                                             alt="<?= htmlspecialchars($currentConversation['other_user_name']) ?>" >
                                    </div>
                                    <div class="chat-details">
                                        <h6 class="chat-name"><?= htmlspecialchars($currentConversation['other_user_name']) ?></h6>
                                        <p class="chat-status"><?= ucfirst($currentConversation['other_user_role']) ?></p>
                                    </div>
                                </div>
                                <div class="chat-actions">
                                    <div class="dropdown">
                                        <button class="btn dropdown-toggle" 
                                                data-bs-toggle="dropdown" 
                                                title="Plus d'options">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item text-danger" 
                                                   href="/messagerie/delete/<?= $currentConversationId ?>"
                                                   onclick="return confirm('Voulez-vous vraiment supprimer cette conversation ?')">
                                                    <i class="bi bi-trash me-2"></i>Supprimer
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages Area -->
                            <div class="chat-messages" id="chatMessages">
                                <?php if (empty($messages)): ?>
                                    <div class="text-center text-muted py-5">
                                        <i class="bi bi-chat fs-1 d-block mb-3"></i>
                                        <p>Aucun message. Commencez la conversation !</p>
                                    </div>
                                <?php else: ?>
                                    <?php 
                                    $lastDate = null;
                                    foreach ($messages as $msg): 
                                        $msgDate = date('Y-m-d', strtotime($msg['created_at']));
                                        
                                        if ($msgDate !== $lastDate):
                                            $lastDate = $msgDate;
                                            $today = date('Y-m-d');
                                            $yesterday = date('Y-m-d', strtotime('-1 day'));
                                            
                                            if ($msgDate === $today) {
                                                $dateLabel = 'Aujourd\'hui';
                                            } elseif ($msgDate === $yesterday) {
                                                $dateLabel = 'Hier';
                                            } else {
                                                $dateLabel = date('d/m/Y', strtotime($msgDate));
                                            }
                                    ?>
                                        <div class="date-separator">
                                            <span class="date-label"><?= $dateLabel ?></span>
                                        </div>
                                    <?php endif; ?>

                                    <div class="message-group">
                                        <?php
                                        $isSent = $msg['sender_id'] == $userId;
                                        $msgAvatarUrl = !empty($msg['sender_avatar']) 
                                            ?  $msg['sender_avatar'] 
                                            : 'https://ui-avatars.com/api/?name=' . urlencode($msg['sender_name']) . '&size=32&background=667eea&color=fff';
                                        ?>
                                        <div class="message <?= $isSent ? 'own-message' : '' ?>">
                                            <?php if (!$isSent): ?>
                                                <img src="<?= $msgAvatarUrl ?>" 
                                                     class="message-avatar" 
                                                     alt="<?= htmlspecialchars($msg['sender_name']) ?>">
                                            <?php endif; ?>
                                            <div class="message-bubble">
                                                <div class="message-content">
                                                    <p><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                                                </div>
                                                <div class="message-info">
                                                    <span class="message-time">
                                                        <?= date('H:i', strtotime($msg['created_at'])) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <!-- Message Input -->
                            <div class="chat-input">
                                <form action="/messagerie/send" method="POST" id="messageForm">
                                    <input type="hidden" name="conversation_id" value="<?= $currentConversationId ?>">
                                    <div class="input-container">
                                        <div class="message-input">
                                            <textarea class="form-control" 
                                                      name="message"
                                                      id="messageTextarea"
                                                      placeholder="Tapez un message..." 
                                                      rows="1" 
                                                      required
                                                      autocomplete="off"
                                                      style="resize: none"></textarea>
                                        </div>
                                        <div class="input-actions">
                                            <button type="submit" class="btn btn-primary" id="sendButton" title="Envoyer">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php else: ?>
                        
                        <!-- Empty Chat State -->
                        <div class="empty-chat">
                            <div class="empty-icon">
                                <i class="bi bi-chat-dots"></i>
                            </div>
                            <h5 class="empty-text">Sélectionnez une conversation</h5>
                            <p class="text-muted mb-4">Choisissez une conversation pour commencer à discuter</p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                                <i class="bi bi-plus-lg me-2"></i>Nouvelle Conversation
                            </button>
                        </div>

                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</main>

<!-- Modal Nouveau Chat -->
<div class="modal fade" id="newChatModal" tabindex="3" style="z-index: 3000; position:absolute;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-chat-dots-fill"></i>
                    Nouvelle conversation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="search-wrapper">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" 
                           id="searchContacts" 
                           class="form-control" 
                           placeholder="Rechercher un contact..."
                           onkeyup="searchContacts(this.value)">
                </div>
                
                <div class="contacts-container" id="contactsList">
                    <?php foreach ($contacts as $contact): ?>
                        <?php
                        $contactAvatarUrl = !empty($contact['avatar_path']) 
                            ?  $contact['avatar_path'] 
                            : 'https://ui-avatars.com/api/?name=' . urlencode($contact['nom']) . '&size=50&background=667eea&color=fff';
                        ?>
                        <form action="/messagerie/start" method="POST" style="display: inline-block; width: 100%;">
                            <input type="hidden" name="other_user_id" value="<?= $contact['id'] ?>">
                            <button type="submit" 
                                    class="contact-item"
                                    data-search-text="<?= strtolower(htmlspecialchars($contact['nom'])) ?>">
                                
                                <div class="contact-avatar-wrapper">
                                    <img src="<?= $contactAvatarUrl ?>" 
                                         alt="<?= htmlspecialchars($contact['nom']) ?>" 
                                         class="contact-avatar">
                                    <?php if (!empty($contact['conversation_id'])): ?>
                                        <span class="contact-status"></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="contact-info">
                                    <div class="contact-name"><?= htmlspecialchars($contact['nom']) ?></div>
                                    <div class="contact-role">
                                        <i class="bi bi-briefcase"></i>
                                        <?= ucfirst($contact['role']) ?>
                                        <?php if (!empty($contact['job_title'])): ?>
                                            • <?= htmlspecialchars($contact['job_title']) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="contact-action">
                                    <?php if (!empty($contact['conversation_id'])): ?>
                                        <i class="bi bi-chat-fill"></i>
                                    <?php else: ?>
                                        <i class="bi bi-plus-circle"></i>
                                    <?php endif; ?>
                                </div>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Tous vos styles existants restent identiques */
.modal-content {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 25px 30px;
}

.modal-header .modal-title {
    font-size: 1.4rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-header .modal-title i {
    font-size: 1.6rem;
}

.modal-header .btn-close {
    filter: brightness(0) invert(1);
    opacity: 0.8;
}

.modal-header .btn-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 25px;
    background: #f8f9fa;
}

.search-wrapper {
    position: relative;
    margin-bottom: 20px;
}

.search-wrapper .form-control {
    padding: 14px 20px 14px 50px;
    border: 2px solid #e0e0e0;
    border-radius: 50px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.search-wrapper .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    outline: none;
}

.search-wrapper .search-icon {
    position: absolute;
    left: 20px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 1.2rem;
}

.contacts-container {
    max-height: 400px;
    overflow-y: auto;
    background: white;
    border-radius: 15px;
    padding: 10px;
}

.contacts-container::-webkit-scrollbar {
    width: 8px;
}

.contacts-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.contacts-container::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 10px;
}

.contact-item {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 15px;
    margin-bottom: 8px;
    border-radius: 12px;
    transition: all 0.3s ease;
    cursor: pointer;
    background: white;
    border: 2px solid transparent;
    text-align: left;
}

.contact-item:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(118, 75, 162, 0.08) 100%);
    border-color: #667eea;
    transform: translateX(5px);
}

.contact-avatar-wrapper {
    position: relative;
    margin-right: 15px;
}

.contact-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #f0f0f0;
}

.contact-status {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #10b981;
    border: 2px solid white;
    border-radius: 50%;
}

.contact-info {
    flex: 1;
}

.contact-name {
    font-weight: 600;
    font-size: 1rem;
    color: #1f2937;
    margin-bottom: 4px;
}

.contact-role {
    font-size: 0.85rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 5px;
}

.contact-role i {
    font-size: 0.8rem;
}

.contact-action {
    margin-left: 10px;
}

.contact-action i {
    font-size: 1.5rem;
    color: #667eea;
    transition: all 0.3s ease;
}

.contact-item:hover .contact-action i {
    transform: scale(1.2);
}

.contacts-container:empty::before {
    content: "Aucun contact disponible";
    display: block;
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
    font-size: 0.95rem;
}

/* Animation pour nouveaux messages */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-group.new-message {
    animation: slideIn 0.3s ease-out;
}
</style>

<!-- ======================== -->
<!-- SCRIPT UNIQUE OPTIMISÉ -->
<!-- ======================== -->


<script>
class MessagerieWebSocket {
    constructor(userId, conversationId) {
        this.userId = userId;
        this.conversationId = conversationId;
        this.ws = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.connect();
    }

    connect() {
        this.ws = new WebSocket('ws://localhost:8080');
        
        this.ws.onopen = () => {
            console.log('✅ WebSocket connecté');
            this.reconnectAttempts = 0;
            
            // S'authentifier
            this.ws.send(JSON.stringify({
                type: 'auth',
                user_id: this.userId
            }));
            
            // Rejoindre la conversation active
            if (this.conversationId) {
                this.ws.send(JSON.stringify({
                    type: 'join_conversation',
                    conversation_id: this.conversationId
                }));
            }
        };

        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('📨 Message reçu:', data);
            
            if (data.type === 'new_message') {
                this.handleNewMessage(data.message);
            } else if (data.type === 'notification') {
                this.handleNotification(data);
            }
        };

        this.ws.onclose = () => {
            console.log('❌ WebSocket déconnecté');
            this.attemptReconnect();
        };

        this.ws.onerror = (error) => {
            console.error('⚠️ Erreur WebSocket:', error);
        };
    }

    attemptReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            const delay = 1000 * Math.pow(2, this.reconnectAttempts);
            setTimeout(() => {
                this.reconnectAttempts++;
                console.log(`🔄 Tentative de reconnexion ${this.reconnectAttempts}...`);
                this.connect();
            }, delay);
        }
    }

    handleNewMessage(message) {
        // Vérifier que le message appartient à la conversation active
        if (message.conversation_id == this.conversationId) {
            this.appendMessage(message);
            this.scrollToBottom();
            this.playNotificationSound();
        }
        
        // Mettre à jour le compteur de messages non lus dans la sidebar
        this.updateUnreadCount();
    }

    appendMessage(msg) {
        const chatMessages = document.getElementById('chatMessages');
        if (!chatMessages) return;

        const isSent = msg.sender_id == this.userId;
        const avatarUrl = msg.sender_avatar || 
            `https://ui-avatars.com/api/?name=${encodeURIComponent(msg.sender_name)}&size=32&background=667eea&color=fff`;

        const messageHtml = `
            <div class="message-group">
                <div class="message ${isSent ? 'own-message' : ''}">
                    ${!isSent ? `<img src="${avatarUrl}" class="message-avatar" alt="${msg.sender_name}">` : ''}
                    <div class="message-bubble">
                        <div class="message-content">
                            <p>${this.escapeHtml(msg.message).replace(/\n/g, '<br>')}</p>
                        </div>
                        <div class="message-info">
                            <span class="message-time">${this.formatTime(msg.created_at)}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        chatMessages.insertAdjacentHTML('beforeend', messageHtml);
    }

    scrollToBottom() {
        const chatMessages = document.getElementById('chatMessages');
        if (chatMessages) {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    playNotificationSound() {
        // Son de notification (optionnel)
        const audio = new Audio('/assets/sounds/notification.mp3');
        audio.volume = 0.3;
        audio.play().catch(e => console.log('Son désactivé'));
    }

    updateUnreadCount() {
        // Actualiser le compteur via AJAX
        fetch('/messagerie/api/unread-count')
            .then(r => r.json())
            .then(data => {
                document.querySelectorAll('.unread-count').forEach(el => {
                    el.textContent = data.count;
                });
            });
    }

    formatTime(datetime) {
        const date = new Date(datetime);
        return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    switchConversation(newConversationId) {
        // Quitter l'ancienne conversation
        if (this.conversationId && this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify({
                type: 'leave_conversation',
                conversation_id: this.conversationId
            }));
        }

        // Rejoindre la nouvelle
        this.conversationId = newConversationId;
        if (this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify({
                type: 'join_conversation',
                conversation_id: newConversationId
            }));
        }
    }
}

// Initialisation
let messageWS = null;

document.addEventListener('DOMContentLoaded', function() {
    const userId = <?= $userId ?>;
    const conversationId = <?= $currentConversationId ?? 'null' ?>;
    
    // Initialiser WebSocket
    if (conversationId) {
        messageWS = new MessagerieWebSocket(userId, conversationId);
    }

    // Gérer l'envoi de messages via AJAX
    const messageForm = document.getElementById('messageForm');
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const textarea = this.querySelector('textarea[name="message"]');
            const message = textarea.value.trim();
            
            if (!message) return;

            try {
                const response = await fetch('/messagerie/send', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Afficher immédiatement le message envoyé
                    messageWS.appendMessage(data.message);
                    messageWS.scrollToBottom();
                    
                    // Vider le textarea
                    textarea.value = '';
                    textarea.style.height = 'auto';
                } else {
                    alert('Erreur: ' + data.error);
                }
            } catch (error) {
                console.error('Erreur envoi:', error);
                alert('Erreur lors de l\'envoi du message');
            }
        });
    }

    // Auto-resize textarea
    const textarea = document.querySelector('.chat-input textarea');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });

        // Envoyer avec Enter (Shift+Enter = nouvelle ligne)
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                messageForm.dispatchEvent(new Event('submit'));
            }
        });
    }

    // Scroll initial
    if (messageWS) {
        messageWS.scrollToBottom();
    }
});
</script>