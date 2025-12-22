@extends('layouts.admin')

@section('title', 'لوحة التحكم')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">لوحة التحكم</h1>
    <p class="text-gray-600 mt-2">مرحباً {{ auth()->user()->name }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الأجهزة المتصلة</p>
                <p class="text-3xl font-bold text-green-600">{{ $connectedDevices }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-mobile-alt text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">إجمالي الأجهزة</p>
                <p class="text-3xl font-bold text-blue-600">{{ $totalDevices }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-server text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الرسائل اليوم</p>
                <p class="text-3xl font-bold text-purple-600">{{ $todayMessages }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-envelope text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">رسائل بدون رد</p>
                <p class="text-3xl font-bold text-orange-600">{{ $unrepliedMessages }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-clock text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">الأجهزة المتصلة</h2>
        @if($devices->count() > 0)
            <div class="space-y-3">
                @foreach($devices as $device)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3
                            {{ $device->status === 'connected' ? 'bg-green-100' : 'bg-gray-200' }}">
                            <i class="fas fa-mobile-alt {{ $device->status === 'connected' ? 'text-green-600' : 'text-gray-500' }}"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $device->device_name }}</h3>
                            <p class="text-sm text-gray-500">{{ $device->phone_number ?? 'غير متصل' }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $device->status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ $device->status === 'connected' ? 'متصل' : 'غير متصل' }}
                    </span>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">لا توجد أجهزة متصلة</p>
        @endif
        <a href="{{ route('admin.devices.index') }}" class="block mt-4 text-center text-blue-600 hover:text-blue-800">
            عرض جميع الأجهزة <i class="fas fa-arrow-left mr-2"></i>
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">آخر الرسائل</h2>
        @if($recentMessages->count() > 0)
            <div class="space-y-3">
                @foreach($recentMessages as $message)
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold text-gray-800">{{ $message->from_name }}</h3>
                        <span class="text-xs text-gray-500">{{ $message->message_timestamp->diffForHumans() }}</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ Str::limit($message->message_body, 60) }}</p>
                </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">لا توجد رسائل</p>
        @endif
        <a href="{{ route('dashboard.index') }}" class="block mt-4 text-center text-blue-600 hover:text-blue-800">
            عرض جميع الرسائل <i class="fas fa-arrow-left mr-2"></i>
        </a>
    </div>
</div>
@endsection
