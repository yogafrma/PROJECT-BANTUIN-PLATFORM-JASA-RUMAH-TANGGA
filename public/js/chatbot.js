/**
 * Chatbot AI Client Script (BANTUIN AI Assistant)
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Inject Chatbot HTML Structure if not already present
    if (!document.getElementById('chatbot-launcher')) {
        // Determine personalized initial welcome message
        const currentUser = (typeof window !== 'undefined' && window.CURRENT_USER) ? window.CURRENT_USER : null;
        let initialGreeting = 'Halo! 👋 Saya <strong>BANTUIN AI Assistant</strong>.';
        if (currentUser && currentUser.name) {
            const roleLabel = currentUser.role === 'admin' ? 'Administrator' : (currentUser.role === 'technician' ? 'Mitra Teknisi' : 'Customer');
            initialGreeting = `Halo, <strong>${escapeHtml(currentUser.name)}</strong>! 👋 Selamat datang kembali (${roleLabel}).`;
        }

        const chatbotHTML = `
            <!-- Floating Chat Launcher Button -->
            <button id="chatbot-launcher" class="chatbot-launcher-btn" title="Tanya BANTUIN AI Assistant" aria-label="Buka Chatbot AI">
                <div class="launcher-icon-wrap">
                    <svg class="launcher-ai-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- AI Assistant Robot Head with Glowing Visor -->
                        <rect x="3" y="6" width="18" height="13" rx="5" fill="#FFFFFF"/>
                        <circle cx="8.5" cy="12.5" r="1.75" fill="#0067B8"/>
                        <circle cx="15.5" cy="12.5" r="1.75" fill="#0067B8"/>
                        <path d="M10 15.2C10.6 15.8 11.3 16.1 12 16.1C12.7 16.1 13.4 15.8 14 15.2" stroke="#0067B8" stroke-width="1.6" stroke-linecap="round"/>
                        <!-- AI Antenna Stem -->
                        <path d="M12 6V3.5" stroke="#FFFFFF" stroke-width="1.8" stroke-linecap="round"/>
                        <!-- Side Receptors -->
                        <rect x="1" y="10" width="2" height="5" rx="1" fill="#FFFFFF"/>
                        <rect x="21" y="10" width="2" height="5" rx="1" fill="#FFFFFF"/>
                        <!-- Sparkle Star Accent on Antenna -->
                        <path d="M12 1L12.7 2.3C12.9 2.7 13.3 3.1 13.7 3.3L15 4L13.7 4.7C13.3 4.9 12.9 5.3 12.7 5.7L12 7L11.3 5.7C11.1 5.3 10.7 4.9 10.3 4.7L9 4L10.3 3.3C10.7 3.1 11.1 2.7 11.3 2.3L12 1Z" fill="#38BDF8"/>
                    </svg>
                    <i class="bi bi-x-lg launcher-close-icon"></i>
                </div>
                <span class="badge-pulse" title="AI Siaga & Online"></span>
            </button>

            <!-- Chatbot Popup Window Container -->
            <div id="chatbot-container" class="chatbot-container">
                <!-- Header -->
                <div class="chatbot-header">
                    <div class="bot-info">
                        <div class="bot-avatar">
                            <i class="bi bi-robot"></i>
                        </div>
                        <div>
                            <h6 class="bot-title">BANTUIN AI Assistant</h6>
                            <p class="bot-subtitle"><i class="bi bi-circle-fill text-success" style="font-size: 8px;"></i> Online &bull; AI Assistant</p>
                        </div>
                    </div>
                    <button id="chatbot-close" class="chatbot-close-btn" title="Tutup Chat">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Messages Area -->
                <div id="chatbot-messages" class="chatbot-messages">
                    <div class="chat-msg bot">
                        <div class="chat-bubble">
                            ${initialGreeting} Saya siap membantu Anda seputar perbaikan AC, Listrik, Kunci, Elektronik Rumah, konsultasi kerusakan, maupun info akun Anda. Silakan tanyakan apa saja!
                        </div>
                        <span class="chat-time">${getFormattedTime()}</span>
                    </div>
                </div>

                <!-- Input Footer -->
                <div class="chatbot-footer">
                    <input type="text" id="chatbot-input" class="chatbot-input" placeholder="Tanya seputar perbaikan rumah..." autocomplete="off">
                    <button id="chatbot-send" class="chatbot-send-btn" title="Kirim Pesan">
                        <i class="bi bi-send-fill"></i>
                    </button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', chatbotHTML);
    }

    // 2. DOM Elements
    const launcherBtn = document.getElementById('chatbot-launcher');
    const container = document.getElementById('chatbot-container');
    const closeBtn = document.getElementById('chatbot-close');
    const inputField = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send');
    const messagesBox = document.getElementById('chatbot-messages');

    // Conversation memory history
    const historyMemory = [];

    // Backend endpoints (Native PHP Controller as primary, Node.js proxy as backup)
    function getPhpEndpoint() {
        if (window.BASE_URL && window.BASE_URL.length > 2) {
            return window.BASE_URL.replace(/\/+$/, '') + '/chatbot/message';
        }
        const pathname = window.location.pathname;
        const publicIdx = pathname.indexOf('/public');
        if (publicIdx !== -1) {
            return pathname.substring(0, publicIdx + 7) + '/chatbot/message';
        }
        return '/chatbot/message';
    }
    const PHP_ENDPOINT = getPhpEndpoint();
    const NODE_ENDPOINT = 'http://localhost:3000/api/chat';

    // 3. Event Listeners
    launcherBtn.addEventListener('click', () => {
        const isActive = container.classList.toggle('active');
        launcherBtn.classList.toggle('is-open', isActive);
        if (isActive) {
            inputField.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        container.classList.remove('active');
        launcherBtn.classList.remove('is-open');
    });

    sendBtn.addEventListener('click', handleSendMessage);

    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSendMessage();
        }
    });

    // Send Message Handler
    async function handleSendMessage() {
        const text = inputField.value.trim();
        if (!text) return;

        // Render User Message
        appendMessage('user', text);
        inputField.value = '';
        sendBtn.disabled = true;

        // Render Typing Indicator
        const typingElement = appendTypingIndicator();
        scrollToBottom();

        try {
            let data = null;

            // Get current user context if available
            const currentUser = (typeof window !== 'undefined' && window.CURRENT_USER) ? window.CURRENT_USER : null;

            // 1. Try PHP Native Chatbot Controller first (No Node.js needed!)
            try {
                const response = await fetch(PHP_ENDPOINT, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        message: text,
                        history: historyMemory,
                        user: currentUser
                    })
                });
                if (response.ok) {
                    const rawText = await response.text();
                    try {
                        data = JSON.parse(rawText);
                    } catch (parseErr) {
                        const jsonStart = rawText.indexOf('{');
                        const jsonEnd = rawText.lastIndexOf('}');
                        if (jsonStart !== -1 && jsonEnd !== -1) {
                            data = JSON.parse(rawText.substring(jsonStart, jsonEnd + 1));
                        } else {
                            throw parseErr;
                        }
                    }
                }
            } catch (phpErr) {
                console.warn('PHP Chatbot endpoint unreachable, trying Node.js server fallback...', phpErr);
            }

            // 2. Fallback to Node.js backend if PHP endpoint failed
            if (!data || !data.reply) {
                try {
                    const nodeResponse = await fetch(NODE_ENDPOINT, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            message: text,
                            history: historyMemory,
                            user: currentUser
                        })
                    });
                    if (nodeResponse.ok) {
                        data = await nodeResponse.json();
                    }
                } catch (nodeErr) {
                    console.warn('Node.js endpoint also unreachable:', nodeErr);
                }
            }

            // Remove typing indicator
            typingElement.remove();

            if (data && data.reply) {
                appendMessage('bot', data.reply);
                // Save context history
                historyMemory.push({ role: 'user', content: text });
                historyMemory.push({ role: 'assistant', content: data.reply });
                if (historyMemory.length > 12) {
                    historyMemory.splice(0, historyMemory.length - 12);
                }
            } else {
                appendMessage('bot', 'Mohon maaf, saat ini sedang terjadi gangguan jaringan pada asisten AI. Silakan coba kembali sesaat lagi.');
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            typingElement.remove();
            appendMessage('bot', 'Mohon maaf, sistem sedang sibuk. Silakan coba kirim ulang pertanyaan Anda.');
        } finally {
            sendBtn.disabled = false;
            scrollToBottom();
        }
    }

    // Helper: Append Bubble Message with clean Markdown rendering
    function appendMessage(sender, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${sender}`;

        const formattedText = renderRichText(text);

        msgDiv.innerHTML = `
            <div class="chat-bubble">${formattedText}</div>
            <span class="chat-time">${getFormattedTime()}</span>
        `;
        messagesBox.appendChild(msgDiv);
        scrollToBottom();
        return msgDiv;
    }

    // Helper: Rich text markdown parser
    function renderRichText(text) {
        if (!text) return '';
        let str = escapeHtml(text);

        // Convert bold: **text** or __text__
        str = str.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
        str = str.replace(/__(.+?)__/g, '<strong>$1</strong>');

        // Convert italic: *text*
        str = str.replace(/(^|\s)\*([^\*\r\n]+?)\*(\s|$|[.,!?])/g, '$1<em>$2</em>$3');

        // Convert markdown links: [text](url)
        str = str.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_self">$1</a>');

        // Convert newlines
        str = str.replace(/\r\n|\n|\r/g, '<br>');

        return str;
    }

    // Helper: Typing Indicator
    function appendTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-msg bot';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        messagesBox.appendChild(typingDiv);
        return typingDiv;
    }

    // Helper: Time Format
    function getFormattedTime() {
        const now = new Date();
        return now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    }

    // Helper: Scroll to bottom
    function scrollToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    // Helper: Sanitize HTML
    function escapeHtml(str) {
        return str.replace(/[&<>"']/g, function (m) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[m];
        });
    }
});
