@extends('layouts.app')

@section('title', 'الرسائل الواردة')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">إجمالي الرسائل</p>
                <p class="text-3xl font-bold text-gray-800">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-envelope text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">رسائل بدون رد</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['unreplied'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-clock text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">رسائل اليوم</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['today'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-calendar-day text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-4 md:space-y-0">
        <h2 class="text-2xl font-bold text-gray-800">الرسائل الواردة</h2>
        <button onclick="openSendMessageModal()" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
            <i class="fas fa-paper-plane ml-2"></i>إرسال رسالة جديدة
        </button>
    </div>

    <form method="GET" class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" placeholder="بحث برقم أو اسم أو نص الرسالة..." 
                   value="{{ request('search') }}"
                   class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
            
            <select name="replied" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">كل الرسائل</option>
                <option value="yes" {{ request('replied') === 'yes' ? 'selected' : '' }}>تم الرد عليها</option>
                <option value="no" {{ request('replied') === 'no' ? 'selected' : '' }}>لم يتم الرد</option>
            </select>

            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-search ml-2"></i>بحث
            </button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المرسل</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرسالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوقت</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($messages as $message)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 ml-3">
                                <i class="fas fa-user text-green-600"></i>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $message->from_name }}</div>
                                <div class="text-sm text-gray-500">{{ $message->from_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ Str::limit($message->message_body, 50) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $message->message_timestamp->format('Y-m-d') }}</div>
                        <div class="text-sm text-gray-500">{{ $message->message_timestamp->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($message->replied)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check ml-1"></i>تم الرد
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                <i class="fas fa-clock ml-1"></i>بانتظار الرد
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('dashboard.show', $message->id) }}" 
                           class="text-blue-600 hover:text-blue-900 ml-3">
                            <i class="fas fa-eye ml-1"></i>عرض
                        </a>
                        @if(!$message->replied)
                        <button onclick="openReplyModal({{ $message->id }}, '{{ $message->from_name }}', '{{ $message->from_number }}')" 
                                class="text-green-600 hover:text-green-900">
                            <i class="fas fa-reply ml-1"></i>رد
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        لا توجد رسائل
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>
</div>

<div id="replyModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">الرد على الرسالة</h3>
            <p class="text-sm text-gray-500 mb-2">المرسل: <span id="senderName"></span></p>
            <p class="text-sm text-gray-500 mb-4">الرقم: <span id="senderNumber"></span></p>
            <textarea id="replyMessage" rows="4" 
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="اكتب ردك هنا..."></textarea>
            <div class="flex justify-end space-x-2 space-x-reverse mt-4">
                <button onclick="closeReplyModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    إلغاء
                </button>
                <button onclick="sendReply()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-paper-plane ml-2"></i>إرسال
                </button>
            </div>
        </div>
    </div>
</div>

<div id="sendMessageModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">إرسال رسالة جديدة</h3>
            <input type="text" id="phoneNumber" 
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-green-500"
                   placeholder="رقم الهاتف (مثال: 966501234567)">
            <textarea id="newMessage" rows="4" 
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="اكتب رسالتك هنا..."></textarea>
            <div class="flex justify-end space-x-2 space-x-reverse mt-4">
                <button onclick="closeSendMessageModal()" 
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    إلغاء
                </button>
                <button onclick="sendNewMessage()" 
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-paper-plane ml-2"></i>إرسال
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentMessageId = null;

function openReplyModal(messageId, senderName, senderNumber) {
    currentMessageId = messageId;
    document.getElementById('senderName').textContent = senderName;
    document.getElementById('senderNumber').textContent = senderNumber;
    document.getElementById('replyModal').classList.remove('hidden');
}

function closeReplyModal() {
    document.getElementById('replyModal').classList.add('hidden');
    document.getElementById('replyMessage').value = '';
    currentMessageId = null;
}

function openSendMessageModal() {
    document.getElementById('sendMessageModal').classList.remove('hidden');
}

function closeSendMessageModal() {
    document.getElementById('sendMessageModal').classList.add('hidden');
    document.getElementById('phoneNumber').value = '';
    document.getElementById('newMessage').value = '';
}

async function sendReply() {
    const message = document.getElementById('replyMessage').value;
    if (!message.trim()) {
        alert('الرجاء كتابة رد');
        return;
    }

    try {
        const response = await fetch(`/dashboard/messages/${currentMessageId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ reply_message: message })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('تم إرسال الرد بنجاح');
            location.reload();
        } else {
            alert('فشل إرسال الرد: ' + data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء إرسال الرد');
        console.error(error);
    }
}

async function sendNewMessage() {
    const phoneNumber = document.getElementById('phoneNumber').value;
    const message = document.getElementById('newMessage').value;
    
    if (!phoneNumber.trim() || !message.trim()) {
        alert('الرجاء إدخال رقم الهاتف والرسالة');
        return;
    }

    try {
        const response = await fetch('/dashboard/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({ 
                phone_number: phoneNumber,
                message: message 
            })
        });

        const data = await response.json();
        
        if (data.success) {
            alert('تم إرسال الرسالة بنجاح');
            closeSendMessageModal();
        } else {
            alert('فشل إرسال الرسالة: ' + data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء إرسال الرسالة');
        console.error(error);
    }
}
</script>
@endpush
@endsection
