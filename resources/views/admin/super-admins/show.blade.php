@extends('layouts.admin')

@section('title', 'تفاصيل المدير الفائق')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تفاصيل المدير الفائق: {{ $superAdmin->name }}</h1>
            <p class="text-gray-600 mt-2">عرض جميع المعلومات والإحصائيات</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.users.edit', $superAdmin) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                <i class="fas fa-edit ml-2"></i>
                تعديل
            </a>
            <a href="{{ route('admin.super-admins.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg">
                <i class="fas fa-arrow-right ml-2"></i>
                رجوع
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
            @if($superAdmin->is_active)
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">نشط</span>
            @else
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">غير نشط</span>
            @endif
        </div>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">الاسم</p>
                <p class="font-medium text-gray-900">{{ $superAdmin->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                <p class="font-medium text-gray-900">{{ $superAdmin->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">الدور</p>
                <p class="font-medium text-gray-900">مدير فائق</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ الانضمام</p>
                <p class="font-medium text-gray-900">{{ $superAdmin->created_at->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">آخر نشاط</p>
                <p class="font-medium text-gray-900">{{ $superAdmin->last_activity_at ? $superAdmin->last_activity_at->diffForHumans() : 'لم يسجل دخول بعد' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">الإحصائيات</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">المجتمعات</p>
                    <p class="text-2xl font-bold text-green-600">{{ $superAdmin->ownedCommunities->count() }}</p>
                </div>
                <i class="fas fa-users text-green-600 text-2xl"></i>
            </div>
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">الموظفين</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $superAdmin->employees->count() }}</p>
                </div>
                <i class="fas fa-user-tie text-blue-600 text-2xl"></i>
            </div>
            <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">الأجهزة المخصصة</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $superAdmin->assignedDevices->count() }}</p>
                </div>
                <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">الأجهزة المخصصة</h3>
        <div class="space-y-2 max-h-64 overflow-y-auto">
            @forelse($superAdmin->assignedDevices as $device)
            <div class="flex items-center justify-between p-2 border border-gray-200 rounded">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $device->name }}</p>
                    <p class="text-xs text-gray-600">{{ $device->phone_number }}</p>
                </div>
                @if($device->status === 'connected')
                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">متصل</span>
                @else
                <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">غير متصل</span>
                @endif
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-4">لا توجد أجهزة مخصصة</p>
            @endforelse
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">المجتمعات</h3>
        <div class="space-y-3">
            @forelse($superAdmin->ownedCommunities as $community)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800">{{ $community->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $community->employees->count() }} موظف • {{ $community->devices->count() }} جهاز</p>
                    </div>
                    @if($community->is_active)
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">نشط</span>
                    @else
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">غير نشط</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">لا توجد مجتمعات بعد</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">الموظفين</h3>
        <div class="space-y-3 max-h-96 overflow-y-auto">
            @forelse($superAdmin->employees as $employee)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center ml-2">
                            <span class="text-blue-600 text-sm font-semibold">{{ substr($employee->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $employee->name }}</p>
                            <p class="text-xs text-gray-600">{{ $employee->community->name ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    @if($employee->is_active)
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">نشط</span>
                    @else
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">غير نشط</span>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">لا يوجد موظفين بعد</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
