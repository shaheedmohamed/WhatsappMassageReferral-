@extends('layouts.app')

@section('title', 'تفاصيل الرسالة')

@section('content')
<div class="mb-6">
    <a href="{{ route('dashboard.index') }}" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-right ml-2"></i>العودة للرسائل
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">تفاصيل الرسالة</h2>
            
            <div class="space-y-4">
                <div class="border-b pb-4">
                    <label class="text-sm font-medium text-gray-500">المرسل</label>
                    <div class="flex items-center mt-2">
                        <div class="bg-green-100 rounded-full p-3 ml-3">
                            <i class="fas fa-user text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">{{ $message->from_name }}</p>
                            <p class="text-sm text-gray-500">{{ $message->from_number }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-b pb-4">
                    <label class="text-sm font-medium text-gray-500">نص الرسالة</label>
                    <p class="mt-2 text-gray-900 whitespace-pre-wrap">{{ $message->message_body }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 border-b pb-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">وقت الاستلام</label>
                        <p class="mt-1 text-gray-900">{{ $message->message_timestamp->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">نوع الرسالة</label>
                        <p class="mt-1 text-gray-900">{{ $message->message_type }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">تم التوجيه للمسؤول</label>
                        <p class="mt-1">
                            @if($message->forwarded_to_admin)
                                <span class="text-green-600"><i class="fas fa-check ml-1"></i>نعم</span>
                                <span class="text-xs text-gray-500 block">{{ $message->forwarded_at->format('Y-m-d H:i') }}</span>
                            @else
                                <span class="text-red-600"><i class="fas fa-times ml-1"></i>لا</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">حالة الرد</label>
                        <p class="mt-1">
                            @if($message->replied)
                                <span class="text-green-600"><i class="fas fa-check ml-1"></i>تم الرد</span>
                                <span class="text-xs text-gray-500 block">{{ $message->replied_at->format('Y-m-d H:i') }}</span>
                            @else
                                <span class="text-orange-600"><i class="fas fa-clock ml-1"></i>بانتظار الرد</span>
                            @endif
                        </p>
                    </div>
                </div>

                @if($message->replied && $message->reply_message)
                <div class="border-t pt-4">
                    <label class="text-sm font-medium text-gray-500">الرد المرسل</label>
                    <div class="mt-2 bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-wrap">{{ $message->reply_message }}</p>
                    </div>
                </div>
                @endif
            </div>

            @if(!$message->replied)
            <div class="mt-6 pt-6 border-t">
                <h3 class="text-lg font-medium text-gray-900 mb-4">الرد على الرسالة</h3>
                <textarea id="replyMessage" rows="4" 
                          class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                          placeholder="اكتب ردك هنا..."></textarea>
                <button onclick="sendReply({{ $message->id }})" 
                        class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-paper-plane ml-2"></i>إرسال الرد
                </button>
            </div>
            @endif
        </div>
    </div>

    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-history ml-2"></i>سجل المحادثات
            </h3>
            <p class="text-sm text-gray-500 mb-4">جميع الرسائل من {{ $message->from_name }}</p>
            
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @forelse($conversationMessages as $msg)
                <div class="border rounded-lg p-3 {{ $msg->id === $message->id ? 'bg-blue-50 border-blue-300' : 'hover:bg-gray-50' }}">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs text-gray-500">{{ $msg->message_timestamp->format('Y-m-d H:i') }}</span>
                        @if($msg->replied)
                            <span class="text-xs text-green-600"><i class="fas fa-check"></i></span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-700">{{ Str::limit($msg->message_body, 80) }}</p>
                    @if($msg->id !== $message->id)
                    <a href="{{ route('dashboard.show', $msg->id) }}" 
                       class="text-xs text-blue-600 hover:text-blue-800 mt-2 inline-block">
                        عرض التفاصيل
                    </a>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-500">لا توجد رسائل أخرى</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-paper-plane ml-2"></i>إرسال رسالة جديدة
            </h3>
            <p class="text-sm text-gray-500 mb-4">إلى: {{ $message->from_number }}</p>
            <textarea id="newMessage" rows="3" 
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                      placeholder="اكتب رسالة جديدة..."></textarea>
            <button onclick="sendNewMessageToContact('{{ $message->from_number }}')" 
                    class="mt-4 w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                <i class="fas fa-paper-plane ml-2"></i>إرسال
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function sendReply(messageId) {
    const message = document.getElementById('replyMessage').value;
    if (!message.trim()) {
        alert('الرجاء كتابة رد');
        return;
    }

    try {
        const response = await fetch(`/dashboard/messages/${messageId}/reply`, {
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

async function sendNewMessageToContact(phoneNumber) {
    const message = document.getElementById('newMessage').value;
    
    if (!message.trim()) {
        alert('الرجاء كتابة رسالة');
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
            document.getElementById('newMessage').value = '';
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
