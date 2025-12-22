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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        .chat-item:hover {
            background-color: #f3f4f6;
        }
        .message-bubble {
            max-width: 70%;
            word-wrap: break-word;
        }
        .message-sent {
            background-color: #dcf8c6;
            margin-left: auto;
        }
        .message-received {
            background-color: #ffffff;
            margin-right: auto;
        }
        #messagesContainer {
            height: calc(100vh - 180px);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column-reverse;
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
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <div class="w-1/3 bg-white border-l border-gray-200 flex flex-col">
            <div class="bg-green-600 text-white p-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold">Shaheed WhatsApp</h1>
                    <div class="flex gap-2">
                        <button onclick="showNewMessageModal()" class="text-white hover:bg-green-700 p-2 rounded-lg transition" title="رسالة جديدة">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                        </button>
                        <button onclick="logout()" class="text-white hover:bg-green-700 p-2 rounded-lg transition" title="تسجيل الخروج">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="p-3 bg-gray-50 border-b border-gray-200 space-y-2">
                <input type="text" id="searchChats" placeholder="ابحث في المحادثات..." 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-green-500">
                
                <select id="deviceFilter" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-green-500">
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

        <div class="flex-1 flex flex-col bg-gray-50">
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
                <div class="bg-white border-b border-gray-200 p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold ml-3">
                            <span id="chatInitial">A</span>
                        </div>
                        <div class="flex-1">
                            <h2 id="chatName" class="font-semibold text-gray-800">اسم المحادثة</h2>
                            <p id="chatStatus" class="text-sm text-gray-500">متصل</p>
                        </div>
                    </div>
                </div>

                <div id="messagesContainer" class="p-4 bg-[#e5ddd5]">
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"></div>
                            <p class="text-gray-600">جاري تحميل الرسائل...</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white border-t border-gray-200 p-4">
                    <form id="messageForm" class="flex items-center gap-2">
                        <input type="text" id="messageInput" placeholder="اكتب رسالة..." 
                            class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:border-green-500"
                            required>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white p-3 rounded-lg transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
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
            const currentValue = select.value;
            
            select.innerHTML = '<option value="">جميع الأجهزة</option>' + 
                devices.map(device => {
                    const displayName = device.phone_number || device.name || 'جهاز غير معروف';
                    return `<option value="${device.id}">${displayName}</option>`;
                }).join('');
            
            select.value = currentValue;
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
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold ml-3">
                                ${initial}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-baseline mb-1">
                                    <h3 class="font-semibold text-gray-800 truncate">${chat.name}</h3>
                                    <span class="text-xs text-gray-500 mr-2">${time}</span>
                                </div>
                                <p class="text-sm text-gray-600 truncate">${lastMsg}</p>
                                <div class="mt-1">${deviceBadge}</div>
                            </div>
                            ${chat.unreadCount > 0 ? `<span class="bg-green-500 text-white text-xs rounded-full px-2 py-1 mr-2">${chat.unreadCount}</span>` : ''}
                        </div>
                    </div>
                `;
            }).join('');
        }

        async function openChat(chatId, chatName, deviceId = '') {
            currentChatId = chatId;
            currentDeviceId = deviceId;
            
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
                let url = `{{ url('/whatsapp/api/messages') }}/${encodeURIComponent(chatId)}`;
                if (deviceId) {
                    url += `?device_id=${deviceId}`;
                }
                const response = await fetch(url);
                const data = await response.json();
                
                if (data.success && data.messages) {
                    displayMessages(data.messages);
                }
            } catch (error) {
                console.error('Error loading messages:', error);
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

            const messagesHTML = messages.map(msg => {
                const time = new Date(msg.timestamp * 1000).toLocaleTimeString('ar-EG', { hour: '2-digit', minute: '2-digit' });
                const bubbleClass = msg.fromMe ? 'message-sent' : 'message-received';
                
                return `
                    <div class="mb-2 flex ${msg.fromMe ? 'justify-end' : 'justify-start'}">
                        <div class="message-bubble ${bubbleClass} rounded-lg px-4 py-2 shadow">
                            <p class="text-gray-800">${escapeHtml(msg.body)}</p>
                            <span class="text-xs text-gray-600 block mt-1">${time}</span>
                        </div>
                    </div>
                `;
            }).join('');
            
            container.innerHTML = messagesHTML;
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }

        document.getElementById('messageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message || !currentChatId) return;
            
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
                        device_id: currentDeviceId
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    input.value = '';
                    await loadMessages(currentChatId, currentDeviceId);
                } else {
                    alert('فشل إرسال الرسالة: ' + (data.error || 'خطأ غير معروف'));
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('خطأ في إرسال الرسالة');
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
            document.getElementById('newPhoneNumber').value = '';
            document.getElementById('newMessageText').value = '';
        }

        document.getElementById('newMessageForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const phoneNumber = document.getElementById('newPhoneNumber').value.trim();
            const messageText = document.getElementById('newMessageText').value.trim();
            
            if (!phoneNumber || !messageText) return;
            
            try {
                const response = await fetch('{{ route("whatsapp.api.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        to: phoneNumber,
                        message: messageText
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

        loadChats();
        setInterval(loadChats, 30000);
    </script>
</body>
</html>
