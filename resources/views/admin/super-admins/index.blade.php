@extends('layouts.admin')

@section('title', 'إدارة المديرين الفائقين')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة المديرين الفائقين</h1>
            <p class="text-gray-600 mt-2">مراقبة وإدارة جميع المديرين الفائقين ومجتمعاتهم</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-user-plus ml-2"></i>
            إضافة مدير فائق
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">إجمالي المديرين</p>
                <p class="text-3xl font-bold text-blue-600">{{ $superAdmins->count() }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-user-shield text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">المجتمعات</p>
                <p class="text-3xl font-bold text-green-600">{{ $totalCommunities }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-4">
                <i class="fas fa-users text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الموظفين</p>
                <p class="text-3xl font-bold text-purple-600">{{ $totalEmployees }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-user-tie text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">النشطين</p>
                <p class="text-3xl font-bold text-orange-600">{{ $activeSuperAdmins }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-user-check text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المدير الفائق</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المجتمعات</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظفين</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأجهزة</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">آخر نشاط</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($superAdmins as $superAdmin)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">{{ substr($superAdmin->name, 0, 1) }}</span>
                        </div>
                        <div class="mr-4">
                            <div class="text-sm font-medium text-gray-900">{{ $superAdmin->name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $superAdmin->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $superAdmin->ownedCommunities->count() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $superAdmin->employees->count() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $superAdmin->assignedDevices->count() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($superAdmin->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        نشط
                    </span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        غير نشط
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $superAdmin->last_activity_at ? $superAdmin->last_activity_at->diffForHumans() : 'لم يسجل دخول بعد' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('admin.super-admins.show', $superAdmin) }}" class="text-green-600 hover:text-green-900 ml-3">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="{{ route('admin.users.edit', $superAdmin) }}" class="text-blue-600 hover:text-blue-900 ml-3">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <form action="{{ route('admin.users.toggle-status', $superAdmin) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-yellow-600 hover:text-yellow-900">
                            <i class="fas fa-power-off"></i> {{ $superAdmin->is_active ? 'تعطيل' : 'تفعيل' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                    لا يوجد مديرين فائقين بعد. <a href="{{ route('admin.users.create') }}" class="text-blue-600 hover:text-blue-800">أضف مدير فائق جديد</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
