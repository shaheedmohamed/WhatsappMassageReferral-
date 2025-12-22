@extends('layouts.admin')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">إضافة مستخدم جديد</h2>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="أدخل اسم المستخدم">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="example@domain.com">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="أدخل كلمة المرور">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    placeholder="أعد إدخال كلمة المرور">
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">الدور</label>
                <select name="role" id="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="agent" {{ old('role') === 'agent' ? 'selected' : '' }}>موظف خدمة عملاء</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>مدير</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4" id="permissions-section">
                <label class="block text-sm font-medium text-gray-700 mb-2">الصلاحيات</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="reply_to_chats" class="ml-2"
                            {{ in_array('reply_to_chats', old('permissions', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">الرد على المحادثات</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="view_all_chats" class="ml-2"
                            {{ in_array('view_all_chats', old('permissions', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">عرض جميع المحادثات</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="assign_chats" class="ml-2"
                            {{ in_array('assign_chats', old('permissions', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">تعيين المحادثات</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="send_messages" class="ml-2"
                            {{ in_array('send_messages', old('permissions', [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">إرسال رسائل جديدة</span>
                    </label>
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="ml-2" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">تفعيل الحساب</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ المستخدم
                </button>
                <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 rounded-lg text-center transition">
                    <i class="fas fa-times ml-2"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const permissionsSection = document.getElementById('permissions-section');
        if (this.value === 'admin') {
            permissionsSection.style.display = 'none';
        } else {
            permissionsSection.style.display = 'block';
        }
    });
    
    if (document.getElementById('role').value === 'admin') {
        document.getElementById('permissions-section').style.display = 'none';
    }
</script>
@endpush
@endsection
