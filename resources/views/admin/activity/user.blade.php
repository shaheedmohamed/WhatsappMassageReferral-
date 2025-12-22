@extends('layouts.admin')

@section('title', 'نشاط المستخدم: ' . $user->name)

@section('content')
<div class="container mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center">
            <a href="{{ route('admin.activity.index') }}" class="text-gray-600 hover:text-gray-800 ml-4">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">نشاط المستخدم: {{ $user->name }}</h1>
        </div>
        <div class="flex items-center">
            <span class="w-3 h-3 rounded-full ml-2 {{ $user->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
            <span class="text-sm text-gray-600">{{ $user->status === 'online' ? 'متصل' : 'غير متصل' }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي المحادثات</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_assigned'] }}</p>
                </div>
                <i class="fas fa-comments text-3xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المحادثات النشطة</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['active_chats'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المحادثات المكتملة</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed_chats'] }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي الرسائل</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_messages'] }}</p>
                </div>
                <i class="fas fa-envelope text-3xl text-gray-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">الردود</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['replied_messages'] }}</p>
                </div>
                <i class="fas fa-reply text-3xl text-green-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">المحادثات المعينة</h2>
            <div class="space-y-3">
                @forelse($assignments as $assignment)
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $assignment->chat_number }}</p>
                            <p class="text-xs text-gray-500">معرف المحادثة: {{ $assignment->chat_id }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $assignment->status === 'active' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ $assignment->status === 'active' ? 'نشط' : 'مكتمل' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>
                            <i class="fas fa-mobile-alt ml-1"></i>
                            {{ $assignment->device->phone_number ?? 'غير محدد' }}
                        </span>
                        <span>{{ $assignment->assigned_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-8">لا توجد محادثات معينة</p>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $assignments->links() }}
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">الرسائل الأخيرة</h2>
            <div class="space-y-3">
                @forelse($messages as $message)
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $message->from_name }}</p>
                            <p class="text-xs text-gray-500">{{ $message->from_number }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $message->replied ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $message->replied ? 'تم الرد' : 'في الانتظار' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700 mb-2">{{ $message->message_body }}</p>
                    <div class="flex items-center justify-between text-xs text-gray-500">
                        <span>
                            <i class="fas fa-mobile-alt ml-1"></i>
                            {{ $message->device->phone_number ?? 'غير محدد' }}
                        </span>
                        <span>{{ $message->message_timestamp->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-8">لا توجد رسائل</p>
                @endforelse
            </div>
            <div class="mt-4">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
