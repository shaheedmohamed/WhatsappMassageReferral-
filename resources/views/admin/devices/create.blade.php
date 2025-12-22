@extends('layouts.admin')

@section('title', 'إضافة جهاز جديد')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">إضافة جهاز واتساب جديد</h1>
            <p class="text-gray-600 mt-2">قم بإضافة جهاز جديد لربطه بحساب واتساب</p>
        </div>

        <form action="{{ route('admin.devices.store') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="device_name" class="block text-sm font-medium text-gray-700 mb-2">
                    اسم الجهاز <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="device_name" 
                       name="device_name" 
                       value="{{ old('device_name') }}"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="مثال: جهاز المبيعات، جهاز الدعم الفني"
                       required>
                @error('device_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-gray-500 text-sm mt-1">
                    <i class="fas fa-info-circle ml-1"></i>
                    اختر اسماً مميزاً للجهاز لسهولة التعرف عليه
                </p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-blue-800 mb-2">
                    <i class="fas fa-lightbulb ml-2"></i>ملاحظات مهمة:
                </h3>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li><i class="fas fa-check ml-2"></i>بعد إنشاء الجهاز، ستحتاج لمسح QR Code من هاتفك</li>
                    <li><i class="fas fa-check ml-2"></i>يمكنك إضافة عدة أجهزة وإدارتها بشكل منفصل</li>
                    <li><i class="fas fa-check ml-2"></i>كل جهاز يحتاج لحساب واتساب منفصل</li>
                </ul>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition font-semibold">
                    <i class="fas fa-plus ml-2"></i>إضافة الجهاز
                </button>
                <a href="{{ route('admin.devices.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg transition font-semibold text-center">
                    <i class="fas fa-times ml-2"></i>إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
