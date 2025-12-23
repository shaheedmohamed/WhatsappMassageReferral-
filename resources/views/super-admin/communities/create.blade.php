@extends('layouts.admin')

@section('title', 'إنشاء مجتمع جديد')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">إنشاء مجتمع جديد</h1>
    <p class="text-gray-600 mt-2">أضف مجتمع جديد وخصص له الأجهزة</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('super-admin.communities.store') }}" method="POST">
        @csrf
        
        <div class="mb-6">
            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم المجتمع *</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
            @error('name')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
            <textarea name="description" id="description" rows="4"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
            @error('description')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">الأجهزة المخصصة</label>
            <p class="text-sm text-gray-600 mb-3">اختر الأجهزة التي سيعمل عليها موظفو هذا المجتمع</p>
            
            @if($availableDevices->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($availableDevices as $device)
                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox" name="devices[]" value="{{ $device->id }}" 
                        {{ in_array($device->id, old('devices', [])) ? 'checked' : '' }}
                        class="ml-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">{{ $device->name }}</div>
                        <div class="text-sm text-gray-600">{{ $device->phone_number }}</div>
                    </div>
                    @if($device->status === 'connected')
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">متصل</span>
                    @else
                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">غير متصل</span>
                    @endif
                </label>
                @endforeach
            </div>
            @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-yellow-800">لا توجد أجهزة مخصصة لك بعد. يرجى التواصل مع المدير لتخصيص أجهزة.</p>
            </div>
            @endif
            
            @error('devices')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('super-admin.communities.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                <i class="fas fa-save ml-2"></i>
                إنشاء المجتمع
            </button>
        </div>
    </form>
</div>
@endsection
