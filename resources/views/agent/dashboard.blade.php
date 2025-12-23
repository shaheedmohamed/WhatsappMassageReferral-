@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">لوحة تحكم الموظف</h1>
        <p class="text-gray-600 mt-2">مرحباً {{ Auth::user()->name }} - إليك ملخص أدائك اليوم</p>
    </div>

    @if(!$hasDevices)
    <div class="bg-yellow-50 border-r-4 border-yellow-400 p-6 rounded-lg mb-6">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl ml-4"></i>
            <div>
                <h3 class="text-lg font-bold text-yellow-800">لم يتم تعيين أجهزة لك بعد</h3>
                <p class="text-yellow-700 mt-1">يرجى التواصل مع المدير لتعيين الأجهزة التي ستعمل عليها</p>
            </div>
        </div>
    </div>
    @else

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Total Messages -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">إجمالي الرسائل</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($totalMessages) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-envelope text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Messages -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">رسائل اليوم</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($todayMessages) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-calendar-day text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Assigned Chats -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">المحادثات المسندة</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($assignedChats) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-comments text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Messages -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">رسائل قيد الانتظار</p>
                    <h3 class="text-3xl font-bold mt-2">{{ number_format($pendingMessages) }}</h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-clock text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Response Time -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">متوسط وقت الاستجابة</h3>
                <i class="fas fa-stopwatch text-2xl text-indigo-500"></i>
            </div>
            <div class="text-center">
                <p class="text-4xl font-bold text-indigo-600">{{ $responseTime }}</p>
                <p class="text-gray-600 mt-2">دقيقة</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <p class="text-sm text-gray-500">آخر 7 أيام</p>
            </div>
        </div>

        <!-- Assigned Devices -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-800">الأجهزة المسندة</h3>
                <i class="fas fa-mobile-alt text-2xl text-green-500"></i>
            </div>
            <div class="space-y-3">
                @foreach($devices as $device)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center ml-3">
                            <i class="fas fa-mobile-alt text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $device->device_name }}</p>
                            <p class="text-xs text-gray-500">{{ $device->phone_number ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                        <i class="fas fa-check-circle"></i> متصل
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">إجراءات سريعة</h3>
            <div class="space-y-3">
                <a href="{{ route('whatsapp.chats') }}" class="block w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-4 rounded-lg transition text-center">
                    <i class="fas fa-comments ml-2"></i>
                    عرض المحادثات
                </a>
                <a href="{{ route('whatsapp.chats') }}?filter=unread" class="block w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition text-center">
                    <i class="fas fa-envelope-open-text ml-2"></i>
                    الرسائل غير المقروءة
                </a>
                <a href="{{ route('whatsapp.chats') }}" class="block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-3 px-4 rounded-lg transition text-center">
                    <i class="fas fa-paper-plane ml-2"></i>
                    إرسال رسالة جديدة
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Chats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Messages -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">آخر الرسائل</h3>
            <div class="space-y-3">
                @forelse($recentMessages as $message)
                <div class="flex items-start p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center ml-3 flex-shrink-0">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="font-semibold text-gray-800 truncate">{{ $message->chat_name ?? 'غير معروف' }}</p>
                            <span class="text-xs text-gray-500">{{ $message->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 truncate">{{ Str::limit($message->body, 50) }}</p>
                        <div class="flex items-center mt-1">
                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded">
                                {{ $message->device->device_name ?? 'جهاز غير معروف' }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">لا توجد رسائل حديثة</p>
                @endforelse
            </div>
        </div>

        <!-- Top Chats -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">أكثر المحادثات نشاطاً</h3>
            <div class="space-y-3">
                @forelse($topChats as $index => $chat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center flex-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center ml-3 text-white font-bold">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 truncate">{{ $chat->chat_name ?? 'غير معروف' }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($chat->message_count) }} رسالة</p>
                        </div>
                    </div>
                    <a href="{{ route('whatsapp.chats') }}?chat={{ $chat->chat_id }}" class="text-blue-500 hover:text-blue-600">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
                @empty
                <p class="text-center text-gray-500 py-8">لا توجد محادثات</p>
                @endforelse
            </div>
        </div>
    </div>

    @endif
</div>
@endsection
