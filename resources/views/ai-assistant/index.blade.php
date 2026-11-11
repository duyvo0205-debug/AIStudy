@extends('layouts.app')

@section('content')

{{-- Thêm thư viện Marked.js để render Markdown và Bảng biểu từ AI --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
    /* =========================================================
        CSS VARIABLES & RESET
    ========================================================= */
    :root {
        --primary: #2563eb;
        --primary-hover: #1d4ed8;
        --primary-light: #eff6ff;
        --bg-main: #f8fafc;
        --border-color: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
    }

    .ai-page {
        display: flex;
        width: 100%;
        height: calc(100vh - 120px);
        min-height: 600px;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: relative;
    }

    /* =========================================================
        SIDEBAR
    ========================================================= */
    .ai-sidebar {
        width: 280px;
        min-width: 280px;
        background: var(--bg-main);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        transition: transform 0.3s ease;
        z-index: 20;
    }

    .ai-sidebar-header {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
        background: #ffffff;
    }

    .new-chat-btn {
        width: 100%;
        height: 44px;
        border: 1px dashed var(--primary);
        border-radius: 10px;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .new-chat-btn:hover {
        background: var(--primary);
        color: #ffffff;
        border-style: solid;
    }

    .conversation-list {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
    }

    .conversation-list::-webkit-scrollbar {
        width: 5px;
    }

    .conversation-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .conversation-item {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 10px 12px;
        margin-bottom: 4px;
        border-radius: 8px;
        text-decoration: none;
        color: #334155;
        transition: all 0.2s ease;
    }

    .conversation-item:hover {
        background: #e2e8f0;
        color: var(--text-main);
    }

    .conversation-item.active {
        background: #dbeafe;
        color: var(--primary);
        font-weight: 600;
    }

    .conversation-icon {
        font-size: 16px;
    }

    .conversation-title {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        font-size: 14px;
    }

    .conversation-empty {
        padding: 40px 15px;
        text-align: center;
        color: var(--text-muted);
        font-size: 13px;
    }

    /* =========================================================
        CHAT AREA
    ========================================================= */
    .ai-chat {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        background: #ffffff;
    }

    .ai-chat-header {
        height: 68px;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-color);
        background: #ffffff;
    }

    .sidebar-toggle-btn {
        display: none;
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--text-muted);
        margin-right: 12px;
    }

    .ai-chat-title {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .ai-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    }

    .ai-chat-title-info h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-main);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ai-chat-title-info p {
        margin: 2px 0 0;
        color: var(--text-muted);
        font-size: 12px;
    }

    .delete-chat-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        background: transparent;
        color: #94a3b8;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .delete-chat-btn:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    /* =========================================================
        MESSAGES CONTAINER & MARKDOWN STYLING (TABLES, ETC.)
    ========================================================= */
    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        background: var(--bg-main);
        scroll-behavior: smooth;
    }

    .messages-container::-webkit-scrollbar {
        width: 6px;
    }

    .messages-container::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .message-row {
        display: flex;
        margin-bottom: 20px;
        width: 100%;
    }

    .message-row.user {
        justify-content: flex-end;
    }

    .message-row.assistant {
        justify-content: flex-start;
    }

    .message-wrapper {
        max-width: 80%;
        display: flex;
        flex-direction: column;
    }

    .message-row.user .message-wrapper {
        align-items: flex-end;
    }

    .message-row.assistant .message-wrapper {
        align-items: flex-start;
        width: 100%;
        max-width: 85%;
    }

    .message-role {
        margin-bottom: 4px;
        color: var(--text-muted);
        font-size: 11px;
        font-weight: 500;
    }

    .message-bubble {
        padding: 14px 18px;
        border-radius: 16px;
        font-size: 14px;
        line-height: 1.6;
        word-break: break-word;
    }

    .message-row.user .message-bubble {
        background: var(--primary);
        color: #ffffff;
        border-bottom-right-radius: 4px;
        white-space: pre-wrap;
    }

    .message-row.assistant .message-bubble {
        background: #ffffff;
        color: #334155;
        border: 1px solid var(--border-color);
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        width: 100%;
    }

    /* Định dạng Markdown bên trong khung chat AI (Bảng biểu, Code, List...) */
    .message-bubble table {
        width: 100%;
        border-collapse: collapse;
        margin: 12px 0;
        font-size: 13px;
        display: block;
        overflow-x: auto;
    }

    .message-bubble th, 
    .message-bubble td {
        border: 1px solid var(--border-color);
        padding: 8px 12px;
        text-align: left;
    }

    .message-bubble th {
        background: #f1f5f9;
        font-weight: 600;
        color: var(--text-main);
    }

    .message-bubble tr:nth-child(even) {
        background: rgba(0, 0, 0, 0.02);
    }

    .message-bubble pre {
        background: #1e293b;
        color: #f8fafc;
        padding: 12px;
        border-radius: 8px;
        overflow-x: auto;
        margin: 10px 0;
        font-family: monospace;
    }

    .message-bubble code {
        font-family: monospace;
        font-size: 13px;
    }

    .message-bubble p code {
        background: #f1f5f9;
        color: #0f172a;
        padding: 2px 6px;
        border-radius: 4px;
        border: 1px solid var(--border-color);
    }

    .message-bubble ul, 
    .message-bubble ol {
        padding-left: 20px;
        margin: 8px 0;
    }

    .message-bubble p {
        margin: 0 0 8px 0;
    }

    .message-bubble p:last-child {
        margin-bottom: 0;
    }

    /* =========================================================
        EMPTY STATE
    ========================================================= */
    .empty-chat {
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 20px;
    }

    .empty-chat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
    }

    .empty-chat h3 {
        margin: 0 0 8px;
        color: var(--text-main);
        font-size: 18px;
        font-weight: 600;
    }

    .empty-chat p {
        margin: 0;
        color: var(--text-muted);
        font-size: 14px;
    }

    /* =========================================================
        SUGGESTIONS & INPUT AREA
    ========================================================= */
    .suggestions {
        padding: 10px 20px;
        display: flex;
        gap: 8px;
        overflow-x: auto;
        background: #ffffff;
        border-top: 1px solid var(--border-color);
    }

    .suggestions::-webkit-scrollbar {
        display: none;
    }

    .suggestion-btn {
        white-space: nowrap;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: #475569;
        border-radius: 20px;
        padding: 6px 14px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .suggestion-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .ai-input-area {
        padding: 16px 20px;
        background: #ffffff;
        border-top: 1px solid var(--border-color);
    }

    .ai-input-form {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        background: var(--bg-main);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 8px 12px;
        transition: border-color 0.2s ease;
    }

    .ai-input-form:focus-within {
        border-color: var(--primary);
        background: #ffffff;
    }

    .ai-input {
        flex: 1;
        border: none;
        outline: none;
        background: transparent;
        color: var(--text-main);
        font-family: inherit;
        font-size: 14px;
        line-height: 1.5;
        max-height: 120px;
        resize: none;
        padding: 4px 0;
    }

    .send-btn {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        background: var(--primary);
        color: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        flex-shrink: 0;
    }

    .send-btn:hover {
        background: var(--primary-hover);
    }

    .input-hint {
        margin: 8px 0 0;
        text-align: center;
        color: var(--text-muted);
        font-size: 11px;
    }

    /* =========================================================
        RESPONSIVE DESIGN
    ========================================================= */
    @media (max-width: 768px) {
        .sidebar-toggle-btn {
            display: block;
        }

        .ai-sidebar {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            transform: translateX(-100%);
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .ai-sidebar.open {
            transform: translateX(0);
        }

        .message-wrapper {
            max-width: 90%;
        }
    }
</style>

<div class="ai-page">

    {{-- SIDEBAR --}}
    <aside class="ai-sidebar" id="aiSidebar">
        <div class="ai-sidebar-header">
            <form action="{{ route('ai-assistant.conversations.create') }}" method="POST">
                @csrf
                <button type="submit" class="new-chat-btn">
                    <span>+</span> Chat mới
                </button>
            </form>
        </div>

        <div class="conversation-list">
            @forelse($conversations as $item)
                <a href="{{ route('ai-assistant.show', $item) }}"
                   class="conversation-item {{ $conversation->id === $item->id ? 'active' : '' }}">
                    <span class="conversation-icon">💬</span>
                    <span class="conversation-title">{{ $item->title }}</span>
                </a>
            @empty
                <div class="conversation-empty">Chưa có cuộc trò chuyện nào.</div>
            @endforelse
        </div>
    </aside>

    {{-- CHAT AREA --}}
    <main class="ai-chat">

        {{-- HEADER --}}
        <div class="ai-chat-header">
            <div class="ai-chat-title">
                <button type="button" class="sidebar-toggle-btn" id="sidebarToggleBtn" title="Danh sách chat">
                    ☰
                </button>
                <div class="ai-avatar">AI</div>
                <div class="ai-chat-title-info">
                    <h3>{{ $conversation->title }}</h3>
                    <p>Trợ lý AI học tập cá nhân</p>
                </div>
            </div>

            <form action="{{ route('ai-assistant.destroy', $conversation) }}" method="POST"
                  onsubmit="return confirm('Bạn có chắc muốn xóa cuộc trò chuyện này không?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-chat-btn" title="Xóa cuộc trò chuyện">
                    🗑
                </button>
            </form>
        </div>

        {{-- MESSAGES LIST --}}
        <div class="messages-container" id="messagesContainer">

            @if(session('error'))
                <div style="
                    margin-bottom: 16px;
                    padding: 12px 16px;
                    border-radius: 10px;
                    background: #fef2f2;
                    color: #b91c1c;
                    border: 1px solid #fecaca;
                    font-size: 14px;
                ">
                    {{ session('error') }}
                </div>
            @endif

            @if($messages->isEmpty())
                <div class="empty-chat">
                    <div class="empty-chat-icon">✦</div>
                    <h3>Xin chào! Tôi có thể giúp gì cho bạn?</h3>
                    <p>Hãy chọn câu hỏi gợi ý bên dưới hoặc gửi thắc mắc của bạn.</p>
                </div>
            @else
                @foreach($messages as $message)
                    <div class="message-row {{ $message->role }}">
                        <div class="message-wrapper">
                            <div class="message-role">
                                {{ $message->role === 'user' ? 'Bạn' : 'Trợ lý AI' }}
                            </div>

                            <div class="message-bubble">
                                @if($message->role === 'assistant')
                                    {!! Str::markdown($message->message) !!}
                                @else
                                    {{ $message->message }}
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

        </div>

        {{-- SUGGESTIONS --}}
        <div class="suggestions">
            <button type="button" class="suggestion-btn" onclick="setSuggestion('Giải thích cho tôi bài học này')">
                📚 Giải thích bài học
            </button>
            <button type="button" class="suggestion-btn" onclick="setSuggestion('Giúp tôi lập kế hoạch học tập')">
                📅 Lập kế hoạch học tập
            </button>
            <button type="button" class="suggestion-btn" onclick="setSuggestion('Tạo cho tôi một số câu hỏi luyện tập')">
                ✏️ Tạo câu hỏi
            </button>
            <button type="button" class="suggestion-btn" onclick="setSuggestion('Giúp tôi hiểu phần tôi đang học')">
                💡 Giải thích kiến thức
            </button>
        </div>

        {{-- INPUT AREA --}}
        <div class="ai-input-area">
            <form action="{{ route('ai-assistant.send', $conversation) }}" method="POST" class="ai-input-form" id="chatForm">
                @csrf
                <textarea name="message" id="aiMessage" class="ai-input" placeholder="Nhập câu hỏi... (Enter để gửi, Shift+Enter xuống dòng)" rows="1" required></textarea>
                <button type="submit" class="send-btn" title="Gửi">➤</button>
            </form>
            <p class="input-hint">Trợ lý AI hỗ trợ giải đáp thắc mắc và lộ trình học tập.</p>
        </div>

    </main>

</div>

<script>
    // Hàm toàn cục hỗ trợ nút gợi ý
    function setSuggestion(text) {
        const textarea = document.getElementById('aiMessage');
        if (textarea) {
            textarea.value = text;
            textarea.focus();
            textarea.style.height = 'auto';
            textarea.style.height = Math.min(textarea.scrollHeight, 150) + 'px';
        }
    }

    document.addEventListener('DOMContentLoaded', function () {

        const chatForm = document.getElementById('chatForm');
        const messagesContainer = document.getElementById('messagesContainer');
        const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
        const aiSidebar = document.getElementById('aiSidebar');

        if (!chatForm || !messagesContainer) {
            return;
        }

        const textarea = chatForm.querySelector('textarea');
        const submitButton = chatForm.querySelector('button[type="submit"]');

        /* Toggle Sidebar trên mobile */
        if (sidebarToggleBtn && aiSidebar) {
            sidebarToggleBtn.addEventListener('click', function () {
                aiSidebar.classList.toggle('open');
            });
        }

        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        scrollToBottom();

        function createMessage(role, message) {
            const row = document.createElement('div');
            row.className = 'message-row ' + role;

            const wrapper = document.createElement('div');
            wrapper.className = 'message-wrapper';

            const roleElement = document.createElement('div');
            roleElement.className = 'message-role';
            roleElement.textContent = role === 'user' ? 'Bạn' : 'Trợ lý AI';

            const bubble = document.createElement('div');
            bubble.className = 'message-bubble';

            if (role === 'assistant') {
                // Parse markdown và bảng biểu bằng Marked.js cho tin nhắn AI
                bubble.innerHTML = marked.parse(message);
            } else {
                bubble.textContent = message;
            }

            wrapper.appendChild(roleElement);
            wrapper.appendChild(bubble);
            row.appendChild(wrapper);
            messagesContainer.appendChild(row);

            scrollToBottom();
            return row;
        }

        function removeEmptyChat() {
            const emptyChat = messagesContainer.querySelector('.empty-chat');
            if (emptyChat) {
                emptyChat.remove();
            }
        }

        chatForm.addEventListener('submit', async function (event) {
            event.preventDefault();

            const message = textarea.value.trim();
            if (!message) return;

            const csrfInput = chatForm.querySelector('input[name="_token"]');
            if (!csrfInput) {
                alert('Không tìm thấy CSRF token.');
                return;
            }

            const csrfToken = csrfInput.value;

            removeEmptyChat();
            createMessage('user', message);

            textarea.value = '';
            textarea.style.height = 'auto';

            const thinkingMessage = createMessage('assistant', 'Đang suy nghĩ...');

            if (submitButton) submitButton.disabled = true;
            textarea.disabled = true;

            try {
                const response = await fetch(chatForm.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                thinkingMessage.remove();

                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Không thể nhận phản hồi từ AI.');
                }

                if (data.title) {
                    const activeConversation = document.querySelector('.conversation-item.active');
                    if (activeConversation) {
                        const titleElement = activeConversation.querySelector('.conversation-title');
                        if (titleElement) {
                            titleElement.textContent = data.title;
                        }
                    }
                }

                createMessage('assistant', data.message);

            } catch (error) {
                if (thinkingMessage.parentNode) {
                    thinkingMessage.remove();
                }
                createMessage('assistant', 'Xin lỗi, hiện tại AI không thể trả lời. Vui lòng thử lại sau.');
                console.error('AI Assistant Error:', error);
            } finally {
                textarea.disabled = false;
                if (submitButton) submitButton.disabled = false;
                textarea.focus();
                scrollToBottom();
            }
        });

        textarea.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                chatForm.requestSubmit();
            }
        });

        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 150) + 'px';
        });
    });
</script>

@endsection