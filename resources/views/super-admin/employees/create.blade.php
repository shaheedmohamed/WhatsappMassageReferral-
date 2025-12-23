@extends('layouts.admin')

@section('title', 'إضافة موظف جديد')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">إضافة موظف جديد</h1>
    <p class="text-gray-600 mt-2">أضف موظف جديد وعينه لمجتمع</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <form action="{{ route('super-admin.employees.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور *</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
        </div>

        <div class="mb-6">
            <label for="community_id" class="block text-sm font-medium text-gray-700 mb-2">المجتمع *</label>
            <select name="community_id" id="community_id" required
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('community_id') border-red-500 @enderror">
                <option value="">اختر المجتمع</option>
                @foreach($communities as $community)
                <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                    {{ $community->name }} ({{ $community->devices->count() }} جهاز)
                </option>
                @endforeach
            </select>
            @error('community_id')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('super-admin.employees.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                إلغاء
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                <i class="fas fa-user-plus ml-2"></i>
                إضافة الموظف
            </button>
        </div>
    </form>
</div>
@endsection
