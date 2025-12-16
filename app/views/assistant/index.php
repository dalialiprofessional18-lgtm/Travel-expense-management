 <div class="app-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" style="position: absolute; top: 0; right: 0; height: 100vh; z-index: 1000;">
            <div class="sidebar-header">
                <button class="new-chat-btn" onclick="newConversation()">
                    <i class="bi bi-plus-lg"></i>
                    <span>Nouvelle conversation</span>
                </button>
            </div>

            <div class="sidebar-content">
                <!-- Stats Card -->
                <?php if (isset($stats)): ?>
                <div class="stats-card">
                    <h6><i class="bi bi-graph-up-arrow me-2"></i>Vos statistiques</h6>
                    <?php if ($stats['role'] === 'admin'): ?>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <small>Utilisateurs</small>
                            <h3><?= $stats['totalUsers'] ?></h3>
                        </div>
                        <div class="stat-item">
                            <small>Déplacements</small>
                            <h3><?= $stats['totalDeplacements'] ?></h3>
                        </div>
                    </div>
                    <?php elseif ($stats['role'] === 'manager'): ?>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <small>Équipe</small>
                            <h3><?= $stats['teamSize'] ?></h3>
                        </div>
                        <div class="stat-item">
                            <small>En attente</small>
                            <h3><?= $stats['pendingNotes'] ?></h3>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Conversations List -->
                <div id="conversationsList">
                    <?php if (empty($conversations)): ?>
                        <p style="color: var(--text-secondary); font-size: 13px; padding: 12px;">Aucune conversation</p>
                    <?php else: ?>
                        <?php foreach ($conversations as $conv): ?>
                        <div class="conversation-item" onclick="loadConversation(<?= $conv->getId() ?>)">
                            <div class="conversation-title"><?= htmlspecialchars($conv->getTitle()) ?></div>
                            <div class="conversation-time"><?= date('d/m/Y H:i', strtotime($conv->getUpdatedAt())) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- Header -->
            <div class="chat-header">
                <div class="chat-title">
                    <i class="bi bi-robot" style="font-size: 24px; color: var(--primary);"></i>
                    <h1>Assistant IA</h1>
                </div>
                <button class="theme-toggle" onclick="toggleTheme()">
                    <i class="bi bi-moon-fill" id="themeIcon"></i>
                </button>
            </div>

            <!-- Messages -->
            <div class="messages-container" id="chatMessages">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-magic"></i>
                    </div>
                    <h2>Bonjour ! Comment puis-je vous aider ?</h2>
                    <p>Posez-moi des questions sur vos déplacements, notes de frais et statistiques</p>
                </div>

                <div class="suggestions-grid">
                    <div class="suggestion-card" onclick="sendPredefinedMessage('Donne-moi un résumé des notes de frais en attente')">
                        <div class="suggestion-icon">
                            <i class="bi bi-file-text"></i>
                        </div>
                        <div class="suggestion-text">Résumé des notes en attente</div>
                    </div>
                    <div class="suggestion-card" onclick="sendPredefinedMessage('Quelles sont les tendances des dépenses ce mois-ci ?')">
                        <div class="suggestion-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div class="suggestion-text">Tendances des dépenses</div>
                    </div>
                    <div class="suggestion-card" onclick="sendPredefinedMessage('Comment améliorer le processus de validation ?')">
                        <div class="suggestion-icon">
                            <i class="bi bi-lightbulb"></i>
                        </div>
                        <div class="suggestion-text">Conseils d'amélioration</div>
                    </div>
                </div>
            </div>

            <!-- Input Area -->
            <div class="input-area">
                <div class="input-container">
                    <form id="chatForm" onsubmit="sendMessage(event)">
                        <div class="input-wrapper">
                            <textarea 
                                class="message-input" 
                                id="messageInput" 
                                placeholder="Envoyez un message..."
                                rows="1"
                                onkeydown="handleKeyDown(event)"
                            ></textarea>
                            <button type="submit" class="send-button" id="sendBtn">
                                <i class="bi bi-send-fill"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>      
    <style>
        :root {
            --primary: #10a37f;
            --primary-hover: #0d8c6f;
            --bg-main: #ffffff;
            --bg-secondary: #f7f7f8;
            --bg-sidebar: #ffffff;
            --text-primary: #202123;
            --text-secondary: #565869;
            --border-color: #e5e5e5;
            --message-user: #10a37f;
            --message-ai: #f7f7f8;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        [data-theme="dark"] {
            --primary: #10a37f;
            --bg-main: #343541;
            --bg-secondary: #444654;
            --bg-sidebar: #202123;
            --text-primary: #ececf1;
            --text-secondary: #c5c5d2;
            --border-color: #565869;
            --message-user: #10a37f;
            --message-ai: #444654;
            --shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
            overflow: hidden;
            transition: background-color 0.3s, color 0.3s;
        }

        .app-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: var(--shadow);
        }

        .sidebar-header {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
        }

        .new-chat-btn {
            width: 100%;
            padding: 12px 16px;
            background: transparent;
            border: 1.5px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .new-chat-btn:hover {
            background: var(--bg-secondary);
            border-color: var(--primary);
        }

        .sidebar-content {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .sidebar-content::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-content::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary) 0%, #0d8c6f 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 163, 127, 0.2);
        }

        .stats-card h6 {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-item {
            background: rgba(255,255,255,0.1);
            padding: 10px;
            border-radius: 8px;
            backdrop-filter: blur(10px);
        }

        .stat-item small {
            font-size: 11px;
            opacity: 0.8;
            display: block;
            margin-bottom: 4px;
        }

        .stat-item h3 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .conversation-item {
            padding: 12px;
            margin-bottom: 4px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .conversation-item:hover {
            background: var(--bg-secondary);
        }

        .conversation-item.active {
            background: var(--bg-secondary);
        }

        .conversation-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: var(--primary);
        }

        .conversation-title {
            font-weight: 500;
            font-size: 14px;
            color: var(--text-primary);
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 12px;
            color: var(--text-secondary);
        }

        /* MAIN CONTENT */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--bg-main);
        }

        .chat-header {
            padding: 16px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-sidebar);
        }

        .chat-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .chat-title h1 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .theme-toggle {
            background: transparent;
            border: 1.5px solid var(--border-color);
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .theme-toggle:hover {
            background: var(--bg-secondary);
            border-color: var(--primary);
        }

        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        .messages-container::-webkit-scrollbar {
            width: 8px;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 10px;
        }

        .message-group {
            max-width: 900px;
            margin: 0 auto 24px;
            animation: fadeInUp 0.4s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .message-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .message-group.user .message-avatar {
            background: var(--message-user);
            color: white;
        }

        .message-group.assistant .message-avatar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .message-content {
            line-height: 1.7;
            font-size: 15px;
            color: var(--text-primary);
            padding-left: 44px;
        }

        .message-content p {
            margin-bottom: 12px;
        }

        .message-content ul, .message-content ol {
            margin: 12px 0;
            padding-left: 24px;
        }

        .message-content li {
            margin-bottom: 6px;
        }

        .message-content strong {
            font-weight: 600;
            color: var(--text-primary);
        }

        .message-content code {
            background: var(--bg-secondary);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
        }

        .message-content pre {
            background: var(--bg-secondary);
            padding: 16px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 12px 0;
        }

        /* SUGGESTIONS */
        .suggestions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
            max-width: 900px;
            margin: 0 auto;
        }

        .suggestion-card {
            background: var(--bg-sidebar);
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: var(--shadow);
        }

        .suggestion-card:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 163, 127, 0.15);
        }

        .suggestion-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--primary) 0%, #0d8c6f 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-bottom: 8px;
        }

        .suggestion-text {
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 500;
        }

        /* INPUT AREA */
        .input-area {
            padding: 24px;
            border-top: 1px solid var(--border-color);
            background: var(--bg-sidebar);
        }

        .input-container {
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        .input-wrapper {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            background: var(--bg-main);
            border: 1.5px solid var(--border-color);
            border-radius: 12px;
            padding: 12px 16px;
            transition: all 0.2s;
            box-shadow: var(--shadow);
        }

        .input-wrapper:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 163, 127, 0.1);
        }

        .message-input {
            flex: 1;
            border: none;
            background: transparent;
            resize: none;
            outline: none;
            font-size: 15px;
            color: var(--text-primary);
            max-height: 200px;
            min-height: 24px;
        }

        .send-button {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .send-button:hover {
            background: var(--primary-hover);
            transform: scale(1.05);
        }

        .send-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* TYPING INDICATOR */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 12px 16px;
            background: var(--bg-secondary);
            border-radius: 20px;
            width: fit-content;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--text-secondary);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                transform: translateY(0);
            }
            30% {
                transform: translateY(-10px);
            }
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
            max-width: 600px;
            margin: 0 auto;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--primary) 0%, #0d8c6f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(16, 163, 127, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 20px rgba(16, 163, 127, 0);
            }
        }

        .empty-state h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            background: linear-gradient(135deg, var(--primary) 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .empty-state p {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                height: 100vh;
                z-index: 1000;
            }

            .sidebar.open {
                left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script>
        let currentConversationId = null;

        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            document.getElementById('themeIcon').className = newTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
            localStorage.setItem('theme', newTheme);
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.getElementById('themeIcon').className = savedTheme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';

        // Auto-resize textarea
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Handle Enter key
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                document.getElementById('chatForm').dispatchEvent(new Event('submit'));
            }
        }

        function newConversation() {
            currentConversationId = null;
            location.reload();
        }

        async function sendMessage(event) {
            event.preventDefault();
            
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) return;
            
            addMessage('user', message);
            input.value = '';
            input.style.height = 'auto';
            
            showTypingIndicator();
            
            try {
                const response = await fetch('/assistant/chat', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        message: message,
                        conversation_id: currentConversationId
                    })
                });
                
                const data = await response.json();
                hideTypingIndicator();
                
                if (data.success) {
                    currentConversationId = data.conversation_id;
                    addMessage('assistant', data.response);
                } else {
                    addMessage('assistant', '❌ ' + (data.error || 'Erreur'));
                }
            } catch (error) {
                hideTypingIndicator();
                addMessage('assistant', '❌ Erreur de connexion');
            }
        }

        function sendPredefinedMessage(message) {
            document.getElementById('messageInput').value = message;
            document.getElementById('chatForm').dispatchEvent(new Event('submit'));
        }

        function addMessage(role, content) {
            const container = document.getElementById('chatMessages');
            const emptyState = container.querySelector('.empty-state');
            const suggestions = container.querySelector('.suggestions-grid');
            
            if (emptyState) emptyState.remove();
            if (suggestions) suggestions.remove();
            
            const avatar = role === 'user' ? '<?= substr($stats['userName'] ?? 'U', 0, 1) ?>' : 'AI';
            const htmlContent = role === 'assistant' ? marked.parse(content) : content;
            
            const messageHTML = `
                <div class="message-group ${role}">
                    <div class="message-header">
                        <div class="message-avatar">${avatar}</div>
                        <strong>${role === 'user' ? 'Vous' : 'Assistant'}</strong>
                    </div>
                    <div class="message-content">${htmlContent}</div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', messageHTML);
            container.scrollTop = container.scrollHeight;
        }

        function showTypingIndicator() {
            const container = document.getElementById('chatMessages');
            const indicator = `
                <div class="message-group assistant" id="typingIndicator">
                    <div class="message-header">
                        <div class="message-avatar">AI</div>
                        <strong>Assistant</strong>
                    </div>
                    <div class="message-content">
                        <div class="typing-indicator">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', indicator);
            container.scrollTop = container.scrollHeight;
        }

        function hideTypingIndicator() {
            document.getElementById('typingIndicator')?.remove();
        }

        async function loadConversation(id) {
            try {
                const response = await fetch(`/assistant/conversation/${id}`);
                const data = await response.json();
                
                if (data.success) {
                    currentConversationId = id;
                    const container = document.getElementById('chatMessages');
                    container.innerHTML = '';
                    
                    data.messages.forEach(msg => addMessage(msg.role, msg.content));
                }
            } catch (error) {
                console.error('Erreur:', error);
            }
        }
    </script>
