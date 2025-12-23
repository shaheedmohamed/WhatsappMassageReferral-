@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">إدارة المستخدمين</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition">
            <i class="fas fa-plus ml-2"></i>
            إضافة مستخدم جديد
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النشاط</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المحادثات النشطة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white ml-3">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($user->role === 'admin') bg-purple-100 text-purple-800
                            @elseif($user->role === 'super_admin') bg-blue-100 text-blue-800
                            @else bg-green-100 text-green-800
                            @endif">
                            @if($user->role === 'admin') مدير
                            @elseif($user->role === 'super_admin') مدير فائق
                            @else موظف
                            @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $user->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <span class="w-2 h-2 rounded-full ml-2 {{ $user->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            <span class="text-sm text-gray-500">{{ $user->status === 'online' ? 'متصل' : 'غير متصل' }}</span>
                        </div>
                        @if($user->loginLogs->isNotEmpty())
                        <div class="text-xs text-gray-400 mt-1">
                            آخر نشاط: {{ $user->loginLogs->first()->logged_in_at->diffForHumans() }}
                        </div>
                        @else
                        <div class="text-xs text-gray-400 mt-1">
                            لم يسجل دخول بعد
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="font-semibold">{{ $user->chat_assignments_count }}</span> محادثة
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex gap-2">
                            @if($user->isSuperAdmin())
                            <a href="{{ route('admin.super-admins.show', $user) }}" class="text-green-600 hover:text-green-900" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endif
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 hover:text-blue-900" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('admin.activity.user', $user) }}" class="text-purple-600 hover:text-purple-900" title="النشاط">
                                <i class="fas fa-chart-bar"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" 
                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        لا يوجد مستخدمين
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
