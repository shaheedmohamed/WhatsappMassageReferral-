<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>محادثات واتساب</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background: #111b21;
        }
        .chat-item {
            transition: background-color 0.2s;
        }
        .chat-item:hover {
            background-color: #f0f2f5;
        }
        .message-bubble {
            max-width: 65%;
            word-wrap: break-word;
            position: relative;
            padding: 6px 7px 8px 9px;
            border-radius: 7.5px;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .message-sent {
            background-color: #d9fdd3;
            margin-left: auto;
        }
        .message-sent::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 0 13px 8px;
            border-color: transparent transparent #d9fdd3 transparent;
        }
        .message-received {
            background-color: #ffffff;
            margin-right: auto;
        }
        .message-received::before {
            content: '';
            position: absolute;
            right: -8px;
            top: 0;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 0 8px 13px 0;
            border-color: transparent #ffffff transparent transparent;
        }
        .message-time {
            font-size: 11px;
            color: rgba(0,0,0,0.45);
            margin-top: 4px;
            display: flex;
            align-items: center;
            gap: 3px;
            justify-content: flex-end;
        }
        .message-sent .message-time {
            color: rgba(0,0,0,0.45);
        }
        #messagesContainer {
            height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column-reverse;
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB4PSIwIiB5PSIwIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPjxyZWN0IHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgZmlsbD0iI2UwZTBlMCIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
            background-color: #efeae2;
        }
        #messagesContainer > div {
            display: flex;
            flex-direction: column;
        }
        #chatsList {
            height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
        }
        #chatsList::-webkit-scrollbar,
        #messagesContainer::-webkit-scrollbar {
            width: 6px;
        }
        #chatsList::-webkit-scrollbar-thumb,
        #messagesContainer::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 3px;
        }
        .checkmark {
            display: inline-block;
            width: 16px;
            height: 16px;
        }
        .checkmark-single {
            color: #8696a0;
        }
        .checkmark-double {
            color: #53bdeb;
        }
        .typing-indicator {
            display: flex;
            gap: 3px;
            padding: 10px;
        }
        .typing-indicator span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #90949c;
            animation: typing 1.4s infinite;
        }
        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <div class="w-1/3 bg-white border-l border-gray-300 flex flex-col">
            <div class="bg-[#008069] text-white p-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold">Shaheed WhatsApp</h1>
                    <div class="flex gap-2">
                        <button onclick="showNewMessageModal()" class="text-white hover:bg-[#017561] p-2 rounded-full transition" title="رسالة جديدة">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                        <button onclick="logout()" class="text-white hover:bg-[#017561] p-2 rounded-full transition" title="تسجيل الخروج">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-[#f0f2f5] border-b border-gray-200 space-y-2">
                <input type="text" id="searchChats" placeholder="ابحث أو ابدأ محادثة جديدة" 
                    class="w-full px-4 py-2 rounded-lg bg-white border-none focus:outline-none focus:ring-0" style="box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                
                <select id="deviceFilter" class="w-full px-4 py-2 rounded-lg bg-white border-none focus:outline-none focus:ring-0" style="box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    <option value="">جميع الأجهزة</option>
                </select>
            </div>

            <div id="chatsList">
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"></div>
                        <p class="text-gray-600">جاري تحميل المحادثات...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex-1 flex flex-col bg-[#efeae2]">
            <div id="noChatSelected" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-32 h-32 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
                    </svg>
                    <h2 class="text-2xl font-semibold text-gray-600 mb-2">واتساب ويب</h2>
                    <p class="text-gray-500">اختر محادثة من القائمة للبدء</p>
                </div>
            </div>

            <div id="chatArea" class="hidden flex-1 flex flex-col">
                <div class="bg-[#f0f2f5] border-b border-gray-300 p-3" style="box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-[#6b7c85] rounded-full flex items-center justify-center text-white font-bold ml-3">
                            <span id="chatInitial">A</span>
                        </div>
                        <div class="flex-1">
                            <h2 id="chatName" class="font-semibold text-gray-800">اسم المحادثة</h2>
                            <p id="chatStatus" class="text-sm text-gray-500">متصل</p>
                        </div>
                    </div>
                </div>

                <div id="messagesContainer" class="p-4">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"></div>
                            <p class="text-gray-600">جاري تحميل الرسائل...</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#f0f2f5] border-t border-gray-300 p-2">
                    <form id="messageForm" class="flex items-center gap-2">
                        <input type="file" id="fileInput" class="hidden" accept="image/*,video/*,audio/*,.pdf,.doc,.docx">
                        <button type="button" onclick="document.getElementById('fileInput').click()" class="text-[#54656f] hover:text-[#008069] p-2 rounded-full transition" title="إرفاق">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M1.816 15.556v.002c0 1.502.584 2.912 1.646 3.972s2.472 1.647 3.974 1.647a5.58 5.58 0 003.972-1.645l9.547-9.548c.769-.768 1.147-1.767 1.058-2.817-.079-.968-.548-1.927-1.319-2.698-1.594-1.592-4.068-1.711-5.517-.262l-7.916 7.915c-.881.881-.792 2.25.214 3.261.959.958 2.423 1.053 3.263.215l5.511-5.512c.28-.28.267-.722.053-.936l-.244-.244c-.191-.191-.567-.349-.957.04l-5.506 5.506c-.18.18-.635.127-.976-.214-.098-.097-.576-.613-.213-.973l7.915-7.917c.818-.817 2.267-.699 3.23.262.5.501.802 1.1.849 1.685.051.573-.156 1.111-.589 1.543l-9.547 9.549a3.97 3.97 0 01-2.829 1.171 3.975 3.975 0 01-2.83-1.173 3.973 3.973 0 01-1.172-2.828c0-1.071.415-2.076 1.172-2.83l7.209-7.211c.157-.157.264-.579.028-.814L11.5 4.36a.572.572 0 00-.834.018l-7.205 7.207a5.577 5.577 0 00-1.645 3.971z"/>
                            </svg>
                        </button>
                        <input type="text" id="messageInput" placeholder="اكتب رسالة" 
                            class="flex-1 px-4 py-2 rounded-full bg-white border-none focus:outline-none focus:ring-0">
                        <button type="button" onclick="toggleEmojiPicker()" class="text-[#54656f] hover:text-[#008069] p-2 rounded-full transition" title="إيموجي">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.153 11.603c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962zm-3.204 1.362c-.026-.307-.131 5.218 6.063 5.551 6.066-.25 6.066-5.551 6.066-5.551-6.078 1.416-12.129 0-12.129 0zm11.363 1.108s-.669 1.959-5.051 1.959c-3.505 0-5.388-1.164-5.607-1.959 0 0 5.912 1.055 10.658 0zM11.804 1.011C5.609 1.011.978 6.033.978 12.228s4.826 10.761 11.021 10.761S23.02 18.423 23.02 12.228c.001-6.195-5.021-11.217-11.216-11.217zM12 21.354c-5.273 0-9.381-3.886-9.381-9.159s3.942-9.548 9.215-9.548 9.548 4.275 9.548 9.548c-.001 5.272-4.109 9.159-9.382 9.159zm3.108-9.751c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962z"/>
                            </svg>
                        </button>
                        <button type="submit" id="sendButton" class="bg-[#008069] hover:bg-[#017561] text-white p-2 rounded-full transition">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z"/>
                            </svg>
                        </button>
                        <button type="button" id="voiceButton" class="hidden bg-[#008069] hover:bg-[#017561] text-white p-2 rounded-full transition" title="رسالة صوتية">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                                <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="newMessageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">رسالة جديدة</h2>
                <button onclick="hideNewMessageModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="newMessageForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اختر الجهاز</label>
                    <select id="newMessageDevice" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500" required>
                        <option value="">اختر جهاز...</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                    <input type="text" id="newPhoneNumber" placeholder="966500000000" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                        required>
                    <p class="text-xs text-gray-500 mt-1">أدخل الرقم بدون + أو - (مثال: 966500000000)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة</label>
                    <textarea id="newMessageText" rows="4" placeholder="اكتب رسالتك هنا..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500"
                        required></textarea>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                        إرسال
                    </button>
                    <button type="button" onclick="hideNewMessageModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-4 rounded-lg transition">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let chats = [];
        let devices = [];
        let currentChatId = null;
        let currentDeviceId = null;
        let currentChatDeviceId = null;
        let allChats = [];
        let allDevices = [];
        let messagesInterval = null;

        async function loadChats() {
            try {
                const response = await fetch('{{ route("whatsapp.api.chats") }}');
                const data = await response.json();
                
                if (data.success && data.chats) {
                    chats = data.chats;
                    if (data.devices) {
                        devices = data.devices;
                        updateDeviceFilter();
                    }
                    displayChats(chats);
                } else {
                    document.getElementById('chatsList').innerHTML = `
                        <div class="p-4 text-center text-red-600">
                            <p>خطأ في تحميل المحادثات</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading chats:', error);
                document.getElementById('chatsList').innerHTML = `
                    <div class="p-4 text-center text-red-600">
                        <p>خطأ في الاتصال بالخادم</p>
                    </div>
                `;
            }
        }

        function updateDeviceFilter() {
            const select = document.getElementById('deviceFilter');
            const newMessageSelect = document.getElementById('newMessageDevice');
            const currentValue = select.value;
            
            const optionsHTML = devices.map(device => {
                const displayName = device.phone_number || device.name || 'جهاز غير معروف';
                return `<option value="${device.id}">${displayName}</option>`;
            }).join('');
            
            select.innerHTML = '<option value="">جميع الأجهزة</option>' + optionsHTML;
            select.value = currentValue;
            
            if (newMessageSelect) {
                newMessageSelect.innerHTML = '<option value="">اختر جهاز...</option>' + optionsHTML;
            }
        }

        function displayChats(chatList) {
            const container = document.getElementById('chatsList');
            
            if (chatList.length === 0) {
                container.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <p>لا توجد محادثات</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = chatList.map(chat => {
                const lastMsg = chat.lastMessage ? chat.lastMessage.body.substring(0, 50) : 'لا توجد رسائل';
                const time = chat.timestamp ? new Date(chat.timestamp * 1000).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' }) : '';
                const initial = chat.name.charAt(0).toUpperCase();
                
                const deviceInfo = chat.deviceNumber || chat.deviceName || 'غير معروف';
                const deviceBadge = `<span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full ml-2">📱 ${deviceInfo}</span>`;
                
                return `
                    <div class="chat-item border-b border-gray-200 p-4 cursor-pointer transition" onclick="openChat('${chat.id}', '${chat.name.replace(/'/g, "\\'")}', '${chat.deviceId || ''}')">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-[#6b7c85] rounded-full flex items-center justify-center text-white font-bold ml-3">
                                ${initial}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <h3 class="font-semibold text-gray-800 truncate">${chat.name}</h3>
                                    <span class="text-xs text-[#667781] mr-2">${time}</span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">${lastMsg}</p>
                                <div class="mt-1">${deviceBadge}</div>
                            </div>
                            ${chat.unreadCount > 0 ? `<span class="bg-[#25d366] text-white text-xs rounded-full w-5 h-5 flex items-center justify-center mr-2 font-semibold">${chat.unreadCount}</span>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function openChat(chatId, chatName, deviceId = '') {
            currentChatId = chatId;
            currentChatDeviceId = deviceId;
            
            if (!deviceId) {
                alert('خطأ: لم يتم تحديد الجهاز');
                return;
            }
            
            document.getElementById('noChatSelected').classList.add('hidden');
            document.getElementById('chatArea').classList.remove('hidden');
            document.getElementById('chatName').textContent = chatName;
            document.getElementById('chatInitial').textContent = chatName.charAt(0).toUpperCase();
            
            if (messagesInterval) {
                clearInterval(messagesInterval);
            }
            
            await loadMessages(chatId, deviceId);
            messagesInterval = setInterval(() => loadMessages(chatId, deviceId), 5000);
        }

        async function loadMessages(chatId, deviceId = '') {
            try {
                if (!deviceId) {
                    console.error('Device ID is required');
                    document.getElementById('messagesContainer').innerHTML = `
                        <div class="flex items-center justify-center h-full">
                            <p class="text-red-500">خطأ: لم يتم تحديد الجهاز</p>
                        </div>
                    `;
                    return;
                }
                
                let url = `{{ url('/whatsapp/api/messages') }}/${encodeURIComponent(chatId)}?device_id=${deviceId}`;
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && data.messages) {
                    displayMessages(data.messages);
                } else {
                    document.getElementById('messagesContainer').innerHTML = `
                        <div class="flex items-center justify-center h-full">
                            <p class="text-red-500">خطأ: ${data.error || 'فشل تحميل الرسائل'}</p>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading messages:', error);
                document.getElementById('messagesContainer').innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <p class="text-red-500">خطأ في الاتصال بالخادم</p>
                    </div>
                `;
            }
        }

        function displayMessages(messages) {
            const container = document.getElementById('messagesContainer');
            
            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500">لا توجد رسائل</p>
                    </div>
                `;
                return;
            }

            console.log('Messages received:', messages);

            const messagesHTML = messages.map(msg => {
                const time = new Date(msg.timestamp * 1000).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
                const bubbleClass = msg.fromMe ? 'message-sent' : 'message-received';
                
                let messageContent = '';
                
                // Handle different message types
                if (msg.type === 'chat' || !msg.type) {
                    // Regular text message
                    messageContent = `<p class="text-gray-800">${escapeHtml(msg.body || '')}</p>`;
                } else if (msg.type === 'ptt' || msg.type === 'audio') {
                    // Voice message or audio
                    const audioUrl = msg.mediaUrl ? `{{ config('services.whatsapp.node_service_url', 'http://localhost:3000') }}${msg.mediaUrl}` : null;
                    
                    console.log('Voice message:', { type: msg.type, hasMedia: msg.hasMedia, mediaUrl: msg.mediaUrl, audioUrl: audioUrl });
                    
                    messageContent = `
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-[#008069]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3z"/>
                                <path d="M17 11c0 2.76-2.24 5-5 5s-5-2.24-5-5H5c0 3.53 2.61 6.43 6 6.92V21h2v-3.08c3.39-.49 6-3.39 6-6.92h-2z"/>
                            </svg>
                            ${audioUrl ? `
                                <audio controls class="flex-1" style="height: 32px; max-width: 250px;">
                                    <source src="${audioUrl}" type="audio/ogg">
                                    <source src="${audioUrl}" type="audio/mpeg">
                                </audio>
                            ` : `<span class="text-xs text-gray-500">جاري تحميل...</span>`}
                        </div>
                        ${msg.body ? `<p class="text-sm mt-1">${escapeHtml(msg.body)}</p>` : ''}
                    `;
                } else if (msg.type === 'image') {
                    // Image message
                    const imageUrl = msg.mediaUrl ? `{{ config('services.whatsapp.node_service_url', 'http://localhost:3000') }}${msg.mediaUrl}` : null;
                    
                    messageContent = `
                        <div>
                            ${imageUrl ? `
                                <img src="${imageUrl}" alt="صورة" class="rounded-lg cursor-pointer" style="max-width: 300px; max-height: 300px;" onclick="window.open('${imageUrl}', '_blank')">
                            ` : '<div class="w-64 h-64 bg-gray-200 rounded-lg flex items-center justify-center"><span class="text-gray-500">جاري تحميل...</span></div>'}
                            ${msg.body ? `<p class="text-sm mt-2">${escapeHtml(msg.body)}</p>` : ''}
                        </div>
                    `;
                } else if (msg.type === 'video') {
                    // Video message
                    const videoUrl = msg.mediaUrl ? `{{ config('services.whatsapp.node_service_url', 'http://localhost:3000') }}${msg.mediaUrl}` : null;
                    
                    messageContent = `
                        <div>
                            ${videoUrl ? `
                                <video controls class="rounded-lg" style="max-width: 300px; max-height: 300px;">
                                    <source src="${videoUrl}" type="video/mp4">
                                </video>
                            ` : '<div class="w-64 h-64 bg-gray-200 rounded-lg flex items-center justify-center"><span class="text-gray-500">جاري تحميل...</span></div>'}
                            ${msg.body ? `<p class="text-sm mt-2">${escapeHtml(msg.body)}</p>` : ''}
                        </div>
                    `;
                } else if (msg.type === 'document') {
                    // Document message
                    const docUrl = msg.mediaUrl ? `{{ config('services.whatsapp.node_service_url', 'http://localhost:3000') }}${msg.mediaUrl}` : null;
                    
                    messageContent = `
                        <div class="flex items-center gap-3 bg-white bg-opacity-50 rounded-lg p-3">
                            <div class="w-12 h-12 bg-[#008069] rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate">${msg.body || 'مستند'}</p>
                                ${docUrl ? `
                                    <a href="${docUrl}" download class="text-xs text-[#008069] hover:underline">تحميل</a>
                                ` : '<span class="text-xs text-gray-500">جاري تحميل...</span>'}
                            </div>
                        </div>
                    `;
                } else if (msg.type === 'sticker') {
                    // Sticker message
                    const stickerUrl = msg.mediaUrl ? `{{ config('services.whatsapp.node_service_url', 'http://localhost:3000') }}${msg.mediaUrl}` : null;
                    messageContent = `
                        ${stickerUrl ? `
                            <img src="${stickerUrl}" alt="sticker" style="width: 150px; height: 150px;" class="rounded">
                        ` : '<span class="text-4xl">😀</span>'}
                    `;
                } else if (msg.type === 'location') {
                    // Location message
                    messageContent = `
                        <div class="bg-white bg-opacity-50 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <p class="font-semibold text-sm">موقع</p>
                            </div>
                            ${msg.body ? `<p class="text-sm">${escapeHtml(msg.body)}</p>` : ''}
                        </div>
                    `;
                } else if (msg.type === 'vcard' || msg.type === 'contact_card' || msg.type === 'multi_vcard') {
                    // Contact card
                    messageContent = `
                        <div class="bg-white bg-opacity-50 rounded-lg p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-[#6b7c85] rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-sm">بطاقة اتصال</p>
                                    ${msg.body ? `<p class="text-xs text-gray-600">${escapeHtml(msg.body)}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    `;
                } else if (msg.hasMedia) {
                    // Generic media message
                    messageContent = `
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">📎</span>
                            <div>
                                <p class="text-gray-800 font-semibold">ملف وسائط (${msg.type || 'unknown'})</p>
                                ${msg.body ? `<p class="text-sm text-gray-600">${escapeHtml(msg.body)}</p>` : ''}
                            </div>
                        </div>
                    `;
                } else {
                    // Unknown type - show type and body
                    messageContent = `
                        <div>
                            <p class="text-xs text-gray-500 mb-1">نوع: ${msg.type || 'غير معروف'}</p>
                            <p class="text-gray-800">${escapeHtml(msg.body || '[رسالة بدون محتوى نصي]')}</p>
                        </div>
                    `;
                }
                
                return `
                    <div class="mb-2 flex ${msg.fromMe ? 'justify-end' : 'justify-start'}">
                        <div class="message-bubble ${bubbleClass}">
                            ${messageContent}
                            <div class="message-time">
                                <span>${time}</span>
                                ${msg.fromMe ? `
                                    <svg class="checkmark checkmark-double" viewBox="0 0 16 15" width="16" height="15">
                                        <path fill="currentColor" d="M15.01 3.316l-.478-.372a.365.365 0 0 0-.51.063L8.666 9.88a.32.32 0 0 1-.484.032l-.358-.325a.32.32 0 0 0-.484.032l-.378.48a.418.418 0 0 0 .036.54l1.32 1.267a.32.32 0 0 0 .484-.034l6.272-8.048a.366.366 0 0 0-.064-.512zm-4.1 0l-.478-.372a.365.365 0 0 0-.51.063L4.566 9.88a.32.32 0 0 1-.484.032L1.892 7.77a.366.366 0 0 0-.516.005l-.423.433a.364.364 0 0 0 .006.514l3.255 3.185a.32.32 0 0 0 .484-.033l6.272-8.048a.365.365 0 0 0-.063-.51z"/>
                                    </svg>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = messagesHTML;
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }

        // Toggle between send and voice button based on input
        const messageInput = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const voiceButton = document.getElementById('voiceButton');
        
        messageInput.addEventListener('input', () => {
            if (messageInput.value.trim()) {
                sendButton.classList.remove('hidden');
                voiceButton.classList.add('hidden');
            } else {
                sendButton.classList.add('hidden');
                voiceButton.classList.remove('hidden');
            }
        });
        
        // Voice recording functionality
        let mediaRecorder;
        let audioChunks = [];
        let isRecording = false;
        let recordingStartTime;
        let recordingTimer;
        let recordingOverlay;
        
        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins}:${secs.toString().padStart(2, '0')}`;
        }
        
        function showRecordingUI() {
            recordingOverlay = document.createElement('div');
            recordingOverlay.className = 'fixed bottom-0 left-0 right-0 bg-[#f0f2f5] p-4 z-50 flex items-center justify-between';
            recordingOverlay.style.boxShadow = '0 -2px 10px rgba(0,0,0,0.1)';
            recordingOverlay.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    <span id="recordingTime" class="text-gray-700 font-mono">0:00</span>
                    <span class="text-gray-500 text-sm">جاري التسجيل...</span>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="cancelRecording()" class="text-red-500 hover:text-red-600 px-4 py-2 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button onclick="stopAndSendRecording()" class="bg-[#008069] hover:bg-[#017561] text-white p-3 rounded-full transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M1.101 21.757L23.8 12.028 1.101 2.3l.011 7.912 13.623 1.816-13.623 1.817-.011 7.912z"/>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(recordingOverlay);
        }
        
        function hideRecordingUI() {
            if (recordingOverlay) {
                recordingOverlay.remove();
                recordingOverlay = null;
            }
            if (recordingTimer) {
                clearInterval(recordingTimer);
                recordingTimer = null;
            }
        }
        
        window.cancelRecording = function() {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
                audioChunks = [];
                isRecording = false;
                hideRecordingUI();
            }
        };
        
        window.stopAndSendRecording = function() {
            if (mediaRecorder && isRecording) {
                mediaRecorder.stop();
            }
        };
        
        async function sendAudioMessage(audioBlob) {
            if (!currentChatId || !currentChatDeviceId) {
                alert('خطأ: لم يتم فتح محادثة');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('audio', audioBlob, 'voice-message.ogg');
                formData.append('to', currentChatId);
                formData.append('sessionId', await getSessionId(currentChatDeviceId));
                
                const response = await fetch('{{ config("services.whatsapp.node_service_url", "http://localhost:3000") }}/send-audio', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    await loadMessages(currentChatId, currentChatDeviceId);
                } else {
                    alert('فشل إرسال الرسالة الصوتية: ' + (data.error || 'خطأ غير معروف'));
                }
            } catch (error) {
                console.error('Error sending audio:', error);
                alert('خطأ في إرسال الرسالة الصوتية');
            }
        }
        
        async function getSessionId(deviceId) {
            const device = devices.find(d => d.id == deviceId);
            return device ? device.session_id : null;
        }
        
        voiceButton.addEventListener('click', async () => {
            if (!isRecording) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(stream);
                    audioChunks = [];
                    recordingStartTime = Date.now();
                    
                    mediaRecorder.ondataavailable = (event) => {
                        audioChunks.push(event.data);
                    };
                    
                    mediaRecorder.onstop = async () => {
                        stream.getTracks().forEach(track => track.stop());
                        hideRecordingUI();
                        
                        if (audioChunks.length > 0) {
                            const audioBlob = new Blob(audioChunks, { type: 'audio/ogg; codecs=opus' });
                            await sendAudioMessage(audioBlob);
                        }
                        
                        isRecording = false;
                        audioChunks = [];
                    };
                    
                    mediaRecorder.start();
                    isRecording = true;
                    
                    showRecordingUI();
                    
                    // Update timer
                    recordingTimer = setInterval(() => {
                        const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
                        const timeElement = document.getElementById('recordingTime');
                        if (timeElement) {
                            timeElement.textContent = formatTime(elapsed);
                        }
                    }, 1000);
                    
                } catch (error) {
                    console.error('Error accessing microphone:', error);
                    alert('لا يمكن الوصول للميكروفون.\n\nتأكد من منح الإذن للمتصفح');
                }
            }
        });
        
        // File upload handler
        document.getElementById('fileInput').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                alert(`تم اختيار الملف: ${file.name}\n\nميزة إرسال الملفات قيد التطوير.`);
                e.target.value = '';
            }
        });

        document.getElementById('messageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            console.log('Sending message:', { message, currentChatId, currentChatDeviceId });
            
            if (!message) {
                console.log('No message to send');
                return;
            }
            
            if (!currentChatId) {
                alert('خطأ: لم يتم فتح محادثة');
                return;
            }
            
            if (!currentChatDeviceId) {
                alert('خطأ: لم يتم تحديد الجهاز');
                return;
            }
            
            try {
                const response = await fetch('{{ route("whatsapp.api.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        to: currentChatId,
                        message: message,
                        device_id: parseInt(currentChatDeviceId)
                    })
                });
                
                console.log('Response status:', response.status);
                const data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    input.value = '';
                    // Trigger input event to show voice button
                    messageInput.dispatchEvent(new Event('input'));
                    await loadMessages(currentChatId, currentChatDeviceId);
                } else {
                    alert('فشل إرسال الرسالة: ' + (data.error || 'خطأ غير معروف'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('خطأ في إرسال الرسالة: ' + error.message);
            }
        });

        document.getElementById('searchChats').addEventListener('input', (e) => {
            filterChats();
        });

        document.getElementById('deviceFilter').addEventListener('change', (e) => {
            filterChats();
        });

        function filterChats() {
            const search = document.getElementById('searchChats').value.toLowerCase();
            const deviceId = document.getElementById('deviceFilter').value;
            
            let filtered = chats.filter(chat => 
                chat.name.toLowerCase().includes(search)
            );
            
            if (deviceId) {
                filtered = filtered.filter(chat => String(chat.deviceId) === String(deviceId));
            }
            
            displayChats(filtered);
        }

        async function logout() {
            if (!confirm('هل تريد تسجيل الخروج من واتساب؟')) return;
            
            try {
                await fetch('{{ route("whatsapp.api.logout") }}', { method: 'POST' });
                window.location.href = '{{ route("whatsapp.connect") }}';
            } catch (error) {
                console.error('Error logging out:', error);
            }
        }

        function showNewMessageModal() {
            document.getElementById('newMessageModal').classList.remove('hidden');
        }

        function hideNewMessageModal() {
            document.getElementById('newMessageModal').classList.add('hidden');
            document.getElementById('newMessageDevice').value = '';
            document.getElementById('newPhoneNumber').value = '';
            document.getElementById('newMessageText').value = '';
        }

        document.getElementById('newMessageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const deviceId = document.getElementById('newMessageDevice').value;
            const phoneNumber = document.getElementById('newPhoneNumber').value.trim();
            const messageText = document.getElementById('newMessageText').value.trim();
            
            if (!deviceId || !phoneNumber || !messageText) {
                if (!deviceId) {
                    alert('يرجى اختيار الجهاز');
                }
                return;
            }
            
            try {
                const response = await fetch('{{ route("whatsapp.api.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        to: phoneNumber,
                        message: messageText,
                        device_id: parseInt(deviceId)
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('تم إرسال الرسالة بنجاح! ✅');
                    hideNewMessageModal();
                    loadChats();
                } else {
                    alert('فشل إرسال الرسالة: ' + (data.error || 'خطأ غير معروف'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('خطأ في إرسال الرسالة');
            }
        });

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function toggleEmojiPicker() {
            const emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '🤣', '😂', '🙂', '🙃', '😉', '😊', '😇', '🥰', '😍', '🤩', '😘', '😗', '😚', '😙', '🥲', '😋', '😛', '😜', '🤪', '😝', '🤑', '🤗', '🤭', '🤫', '🤔', '🤐', '🤨', '😐', '😑', '😶', '😏', '😒', '🙄', '😬', '🤥', '😌', '😔', '😪', '🤤', '😴', '👍', '👎', '👌', '✌️', '🤞', '🤟', '🤘', '🤙', '👏', '🙌', '👐', '🤲', '🤝', '🙏', '✍️', '💪', '🦾', '🦿', '🦵', '🦶', '👂', '🦻', '👃', '🧠', '🫀', '🫁', '🦷', '🦴', '👀', '👁️', '👅', '👄', '💋', '🩸'];
            
            const emojiPicker = document.createElement('div');
            emojiPicker.id = 'emojiPicker';
            emojiPicker.className = 'absolute bottom-16 left-4 bg-white rounded-lg shadow-2xl p-4 z-50';
            emojiPicker.style.width = '320px';
            emojiPicker.style.maxHeight = '300px';
            emojiPicker.style.overflowY = 'auto';
            emojiPicker.style.display = 'grid';
            emojiPicker.style.gridTemplateColumns = 'repeat(8, 1fr)';
            emojiPicker.style.gap = '8px';
            
            emojis.forEach(emoji => {
                const btn = document.createElement('button');
                btn.textContent = emoji;
                btn.className = 'text-2xl hover:bg-gray-100 rounded p-1 transition';
                btn.type = 'button';
                btn.onclick = () => {
                    const input = document.getElementById('messageInput');
                    input.value += emoji;
                    input.focus();
                    input.dispatchEvent(new Event('input'));
                };
                emojiPicker.appendChild(btn);
            });
            
            const existing = document.getElementById('emojiPicker');
            if (existing) {
                existing.remove();
            } else {
                document.body.appendChild(emojiPicker);
                
                // Close on click outside
                setTimeout(() => {
                    document.addEventListener('click', function closeEmojiPicker(e) {
                        if (!emojiPicker.contains(e.target) && e.target.closest('button')?.getAttribute('title') !== 'إيموجي') {
                            emojiPicker.remove();
                            document.removeEventListener('click', closeEmojiPicker);
                        }
                    });
                }, 100);
            }
        }

        loadChats();
        setInterval(loadChats, 30000);
    </script>
</body>
</html>
