@extends('layouts.admin')

@section('title', 'الأجهزة المتصلة')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">إدارة الأجهزة</h1>
    <a href="{{ route('admin.devices.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
        <i class="fas fa-plus ml-2"></i>إضافة جهاز جديد
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">إجمالي الأجهزة</p>
                <p class="text-3xl font-bold text-blue-600">{{ $devices->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-mobile-alt text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الأجهزة المتصلة</p>
                <p class="text-3xl font-bold text-green-600">{{ $devices->where('status', 'connected')->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الأجهزة غير المتصلة</p>
                <p class="text-3xl font-bold text-orange-600">{{ $devices->where('status', 'disconnected')->count() }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-times-circle text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">قائمة الأجهزة</h2>
        
        @if($devices->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($devices as $device)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center ml-3
                                {{ $device->status === 'connected' ? 'bg-green-100' : ($device->status === 'connecting' ? 'bg-yellow-100' : 'bg-gray-200') }}">
                                <i class="fas fa-mobile-alt text-2xl
                                    {{ $device->status === 'connected' ? 'text-green-600' : ($device->status === 'connecting' ? 'text-yellow-600' : 'text-gray-500') }}"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $device->device_name }}</h3>
                                <!-- <p class="text-sm text-gray-500">{{ $device->phone_number ?? 'غير متصل' }}</p> -->
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            {{ $device->status === 'connected' ? 'bg-green-100 text-green-800' : ($device->status === 'connecting' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-600') }}">
                            @if($device->status === 'connected')
                                <i class="fas fa-check-circle ml-1"></i>متصل
                            @elseif($device->status === 'connecting')
                                <i class="fas fa-spinner fa-spin ml-1"></i>جاري الاتصال
                            @else
                                <i class="fas fa-times-circle ml-1"></i>غير متصل
                            @endif
                        </span>
                    </div>

                    @if($device->last_connected_at)
                        <p class="text-xs text-gray-500 mb-3">
                            <i class="fas fa-clock ml-1"></i>
                            آخر اتصال: {{ $device->last_connected_at->diffForHumans() }}
                        </p>
                    @endif

                    <div class="flex gap-2">
                        <a href="{{ route('admin.devices.show', $device->id) }}" 
                           class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center py-2 rounded-lg transition text-sm">
                            <i class="fas fa-eye ml-1"></i>عرض
                        </a>
                        
                        @if($device->status === 'connected')
                            <form action="{{ route('admin.devices.disconnect', $device->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('POST')
                                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-2 rounded-lg transition text-sm">
                                    <i class="fas fa-unlink ml-1"></i>قطع
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('admin.devices.destroy', $device->id) }}" method="POST" 
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الجهاز؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg transition text-sm">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-mobile-alt text-gray-300 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg mb-4">لا توجد أجهزة مضافة</p>
                <a href="{{ route('admin.devices.create') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
                    <i class="fas fa-plus ml-2"></i>إضافة جهاز جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
