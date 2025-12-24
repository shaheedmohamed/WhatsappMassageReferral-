@extends('layouts.admin')

@section('title', 'عرض المحادثة')

@section('content')
<div class="container mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.activity.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-right ml-2"></i>العودة للوحة المراقبة
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">تفاصيل المحادثة</h1>
        
        @if($assignment)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">الموظف المسؤول</p>
                <p class="text-lg font-semibold text-gray-900">{{ $assignment->user->name ?? 'غير محدد' }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">رقم العميل</p>
                <p class="text-lg font-semibold text-gray-900">{{ $chatNumber }}</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">الحالة</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full
                    {{ $assignment->status === 'in_progress' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $assignment->status === 'on_hold' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $assignment->status === 'completed' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ $assignment->status_text }}
                </span>
            </div>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">المحادثة</h2>
        
        <div class="space-y-4 max-h-[600px] overflow-y-auto p-4 bg-gray-50 rounded-lg">
            @forelse($messages as $message)
            <div class="flex {{ $message->is_from_me ? 'justify-start' : 'justify-end' }}">
                <div class="max-w-[70%]">
                    <div class="rounded-lg p-4 {{ $message->is_from_me ? 'bg-white border border-gray-200' : 'bg-blue-500 text-white' }}">
                        @if($message->message_type === 'image' && $message->media_url)
                        <div class="mb-2">
                            <img src="{{ $message->media_url }}" alt="صورة" class="rounded-lg max-w-full h-auto">
                        </div>
                        @endif
                        
                        @if($message->message_type === 'video' && $message->media_url)
                        <div class="mb-2">
                            <video controls class="rounded-lg max-w-full h-auto">
                                <source src="{{ $message->media_url }}" type="video/mp4">
                                المتصفح لا يدعم عرض الفيديو
                            </video>
                        </div>
                        @endif
                        
                        @if($message->message_type === 'audio' && $message->media_url)
                        <div class="mb-2">
                            <audio controls class="w-full">
                                <source src="{{ $message->media_url }}" type="audio/ogg">
                                المتصفح لا يدعم تشغيل الصوت
                            </audio>
                        </div>
                        @endif
                        
                        @if($message->message_type === 'document' && $message->media_url)
                        <div class="mb-2">
                            <a href="{{ $message->media_url }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-file ml-2"></i>
                                <span>{{ $message->file_name ?? 'ملف' }}</span>
                            </a>
                        </div>
                        @endif
                        
                        @if($message->message_body)
                        <p class="text-sm {{ $message->is_from_me ? 'text-gray-800' : 'text-white' }} whitespace-pre-wrap">{{ $message->message_body }}</p>
                        @endif
                        
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs {{ $message->is_from_me ? 'text-gray-500' : 'text-blue-100' }}">
                                {{ $message->message_timestamp->format('h:i A') }}
                            </span>
                            @if(!$message->is_from_me)
                            <span class="text-xs {{ $message->is_from_me ? 'text-gray-500' : 'text-blue-100' }}">
                                {{ $message->is_from_me ? 'العميل' : ($assignment->user->name ?? 'الموظف') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 {{ $message->is_from_me ? 'text-right' : 'text-left' }}">
                        {{ $message->message_timestamp->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-comments text-4xl mb-3"></i>
                <p>لا توجد رسائل في هذه المحادثة</p>
            </div>
            @endforelse
        </div>
        
        <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-yellow-600 text-xl ml-3 mt-1"></i>
                <div>
                    <p class="text-sm font-semibold text-yellow-800 mb-1">وضع المشاهدة فقط</p>
                    <p class="text-sm text-yellow-700">أنت تشاهد المحادثة في وضع القراءة فقط. لا يمكنك إرسال رسائل من هنا.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
