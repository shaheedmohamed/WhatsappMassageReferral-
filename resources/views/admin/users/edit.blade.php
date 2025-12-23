@extends('layouts.admin')

@section('title', 'تعديل المستخدم')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">تعديل المستخدم: {{ $user->name }}</h2>

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور الجديدة (اتركها فارغة إذا لم ترد التغيير)</label>
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">الدور</label>
                <select name="role" id="role" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="agent" {{ old('role', $user->role) === 'agent' ? 'selected' : '' }}>موظف خدمة عملاء</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>مدير</option>
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
                            {{ in_array('reply_to_chats', old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">الرد على المحادثات</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="view_all_chats" class="ml-2"
                            {{ in_array('view_all_chats', old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">عرض جميع المحادثات</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="assign_chats" class="ml-2"
                            {{ in_array('assign_chats', old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">تعيين المحادثات</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="permissions[]" value="send_messages" class="ml-2"
                            {{ in_array('send_messages', old('permissions', $user->permissions ?? [])) ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">إرسال رسائل جديدة</span>
                    </label>
                </div>
            </div>

            <div class="mb-4" id="devices-section">
                <label class="block text-sm font-medium text-gray-700 mb-2">الأجهزة المسندة</label>
                <p class="text-xs text-gray-500 mb-3">اختر الأجهزة التي يمكن للموظف الوصول إليها</p>
                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @php
                        $devices = \App\Models\WhatsappDevice::all();
                        $assignedDevices = old('assigned_devices', $user->assigned_devices ? json_decode($user->assigned_devices, true) : []);
                    @endphp
                    @forelse($devices as $device)
                    <label class="flex items-center p-2 hover:bg-gray-50 rounded">
                        <input type="checkbox" name="assigned_devices[]" value="{{ $device->id }}" class="ml-2"
                            {{ in_array($device->id, $assignedDevices) ? 'checked' : '' }}>
                        <div class="flex items-center flex-1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center ml-2
                                {{ $device->status === 'connected' ? 'bg-green-100' : 'bg-gray-200' }}">
                                <i class="fas fa-mobile-alt text-sm
                                    {{ $device->status === 'connected' ? 'text-green-600' : 'text-gray-500' }}"></i>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">{{ $device->device_name }}</span>
                                <span class="text-xs text-gray-500 mr-2">
                                    @if($device->status === 'connected')
                                        <i class="fas fa-check-circle text-green-500"></i> متصل
                                    @else
                                        <i class="fas fa-times-circle text-gray-400"></i> غير متصل
                                    @endif
                                </span>
                            </div>
                        </div>
                    </label>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد أجهزة متاحة</p>
                    @endforelse
                </div>
            </div>

            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" class="ml-2" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                    <span class="text-sm font-medium text-gray-700">تفعيل الحساب</span>
                </label>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التغييرات
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
