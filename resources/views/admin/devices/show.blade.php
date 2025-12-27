@extends('layouts.admin')

@section('title', 'تفاصيل الجهاز')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('admin.devices.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-right ml-2"></i>العودة للأجهزة
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center ml-4
                    {{ $device->status === 'connected' ? 'bg-green-100' : 'bg-gray-200' }}">
                    <i class="fas fa-mobile-alt text-3xl
                        {{ $device->status === 'connected' ? 'text-green-600' : 'text-gray-500' }}"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ $device->device_name }}</h1>
                    <p class="text-gray-600">{{ $device->phone_number ?? 'غير متصل' }}</p>
                </div>
            </div>
            <span class="px-4 py-2 rounded-full text-sm font-semibold
                {{ $device->status === 'connected' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600' }}">
                {{ $device->status === 'connected' ? 'متصل' : 'غير متصل' }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">معرف الجلسة</p>
                <p class="font-mono text-sm">{{ $device->session_id }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">تاريخ الإنشاء</p>
                <p class="font-semibold">{{ $device->created_at->format('Y-m-d H:i') }}</p>
            </div>
            @if($device->last_connected_at)
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500 mb-1">آخر اتصال</p>
                <p class="font-semibold">{{ $device->last_connected_at->diffForHumans() }}</p>
            </div>
            @endif
        </div>

        @if($device->status === 'disconnected' || $device->status === 'connecting')
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">ربط الجهاز بـ Care Bot</h2>
                
                <div id="qrSection" class="text-center">
                    <div id="qrLoading" class="mb-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"></div>
                        <p class="text-gray-600">جاري تحميل QR Code...</p>
                    </div>
                    
                    <div id="qrDisplay" class="hidden">
                        <div class="bg-white border-4 border-green-500 rounded-lg p-4 inline-block mb-4">
                            <img id="qrImage" src="" alt="QR Code" class="w-64 h-64">
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 max-w-md mx-auto">
                            <h3 class="font-semibold text-blue-800 mb-2">
                                <i class="fas fa-info-circle ml-2"></i>خطوات الربط:
                            </h3>
                            <ol class="text-sm text-blue-700 text-right space-y-1">
                                <li>1. افتح تطبيق Care Bot على هاتفك</li>
                                <li>2. اذهب إلى: الإعدادات > الأجهزة المرتبطة</li>
                                <li>3. اضغط على "ربط جهاز"</li>
                                <li>4. امسح الكود أعلاه</li>
                            </ol>
                        </div>
                    </div>

                    <div id="qrError" class="hidden">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <i class="fas fa-exclamation-circle text-red-500 text-3xl mb-2"></i>
                            <p class="text-red-700">حدث خطأ في تحميل QR Code</p>
                            <button onclick="loadQR()" class="mt-3 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg">
                                إعادة المحاولة
                            </button>
                        </div>
                    </div>

                    <div id="connected" class="hidden">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                            <h3 class="text-xl font-bold text-green-800 mb-2">تم الاتصال بنجاح! 🎉</h3>
                            <p class="text-green-700 mb-4">الجهاز متصل الآن بـ Care Bot</p>
                            <a href="{{ route('whatsapp.chats') }}" class="inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg">
                                <i class="fas fa-comments ml-2"></i>فتح المحادثات
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="border-t border-gray-200 pt-6">
                <div class="bg-green-50 border border-green-200 rounded-lg p-6 text-center mb-6">
                    <i class="fas fa-check-circle text-green-500 text-5xl mb-3"></i>
                    <h3 class="text-xl font-bold text-green-800 mb-2">الجهاز متصل! 🎉</h3>
                    <p class="text-green-700">يمكنك الآن استخدام هذا الجهاز لإرسال واستقبال الرسائل</p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('whatsapp.chats') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg text-center transition">
                        <i class="fas fa-comments ml-2"></i>فتح المحادثات
                    </a>
                    <form action="{{ route('admin.devices.disconnect', $device->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3 rounded-lg transition"
                                onclick="return confirm('هل أنت متأكد من قطع الاتصال؟')">
                            <i class="fas fa-unlink ml-2"></i>قطع الاتصال
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

@if($device->status !== 'connected')
@push('scripts')
<script>
    const deviceId = {{ $device->id }};
    let statusCheckInterval;

    async function loadQR() {
        document.getElementById('qrLoading').classList.remove('hidden');
        document.getElementById('qrDisplay').classList.add('hidden');
        document.getElementById('qrError').classList.add('hidden');

        try {
            const response = await fetch(`/admin/devices/${deviceId}/qr`);
            const data = await response.json();

            if (data.success && (data.qr || data.qrCode)) {
                const qrData = data.qr || data.qrCode;
                
                const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrData)}`;
                document.getElementById('qrImage').src = qrCodeUrl;
                document.getElementById('qrLoading').classList.add('hidden');
                document.getElementById('qrDisplay').classList.remove('hidden');
                
                startStatusCheck();
            } else if (data.message && data.message.includes('متصل')) {
                document.getElementById('qrLoading').classList.add('hidden');
                document.getElementById('connected').classList.remove('hidden');
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Failed to load QR');
            }
        } catch (error) {
            console.error('Error loading QR:', error);
            document.getElementById('qrLoading').classList.add('hidden');
            document.getElementById('qrError').classList.remove('hidden');
        }
    }

    async function checkStatus() {
        try {
            const response = await fetch(`/admin/devices/${deviceId}/status`);
            const data = await response.json();

            if (data.success && data.ready) {
                clearInterval(statusCheckInterval);
                document.getElementById('qrDisplay').classList.add('hidden');
                document.getElementById('qrLoading').classList.add('hidden');
                document.getElementById('connected').classList.remove('hidden');
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            }
        } catch (error) {
            console.error('Error checking status:', error);
        }
    }

    function startStatusCheck() {
        statusCheckInterval = setInterval(checkStatus, 3000);
    }

    window.onload = function() {
        loadQR();
    };
</script>
@endpush
@endif
@endsection
