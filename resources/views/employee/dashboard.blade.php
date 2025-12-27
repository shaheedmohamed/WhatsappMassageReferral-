@extends('layouts.admin')

@section('title', 'لوحة تحكم الموظف')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">لوحة تحكم الموظف</h1>
    <p class="text-gray-600 mt-2">مرحباً {{ auth()->user()->name }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">إجمالي الرسائل</p>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_messages'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-envelope text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الرسائل المخصصة لي</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['assigned_messages'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-user-check text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الرسائل المعلقة</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_messages'] }}</p>
            </div>
            <div class="bg-yellow-100 rounded-full p-4">
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الرسائل المكتملة</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['completed_messages'] }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">الرسائل الأخيرة</h2>
            <a href="{{ route('employee.chats') }}" class="text-blue-600 hover:text-blue-800">
                عرض الكل <i class="fas fa-arrow-left mr-1"></i>
            </a>
        </div>
        <div class="space-y-4">
            @forelse($recentMessages as $message)
            <div class="border-b pb-4 last:border-b-0">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $message->chat_id }}</p>
                        <p class="text-sm text-gray-600">{{ Str::limit($message->body, 50) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $message->created_at->diffForHumans() }}</p>
                    </div>
                    @if($message->status === 'completed')
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">مكتمل</span>
                    @else
                    <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">معلق</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">لا توجد رسائل بعد</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">روابط سريعة</h2>
        <div class="grid grid-cols-1 gap-3">
            <a href="{{ route('employee.chats') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                <i class="fas fa-comments text-blue-600 text-2xl ml-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">الدردشات</h3>
                    <p class="text-sm text-gray-600">عرض جميع الدردشات المخصصة</p>
                </div>
            </a>
            <a href="{{ route('whatsapp.chats') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition">
                <i class="fas fa-comments text-green-600 text-2xl ml-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">Care Bot</h3>
                    <p class="text-sm text-gray-600">الرد على الرسائل</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
