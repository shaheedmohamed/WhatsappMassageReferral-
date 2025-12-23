@extends('layouts.admin')

@section('title', 'لوحة تحكم المدير الفائق')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">لوحة تحكم المدير الفائق</h1>
    <p class="text-gray-600 mt-2">مرحباً {{ auth()->user()->name }}</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">المجتمعات</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['communities'] }}</p>
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
                <p class="text-3xl font-bold text-blue-600">{{ $stats['employees'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-4">
                <i class="fas fa-user-tie text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الأجهزة المخصصة</p>
                <p class="text-3xl font-bold text-purple-600">{{ $stats['devices'] }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-4">
                <i class="fas fa-mobile-alt text-purple-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-sm">الموظفين النشطين</p>
                <p class="text-3xl font-bold text-orange-600">{{ $stats['active_employees'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-4">
                <i class="fas fa-user-check text-orange-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">المجتمعات الأخيرة</h2>
            <a href="{{ route('super-admin.communities.index') }}" class="text-blue-600 hover:text-blue-800">
                عرض الكل <i class="fas fa-arrow-left mr-1"></i>
            </a>
        </div>
        <div class="space-y-4">
            @forelse($recentCommunities as $community)
            <div class="border-b pb-4 last:border-b-0">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $community->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $community->employees_count }} موظف • {{ $community->devices_count }} جهاز</p>
                    </div>
                    <a href="{{ route('super-admin.communities.edit', $community) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">لا توجد مجتمعات بعد</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800">روابط سريعة</h2>
        </div>
        <div class="grid grid-cols-1 gap-3">
            <a href="{{ route('super-admin.communities.create') }}" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition">
                <i class="fas fa-plus-circle text-green-600 text-2xl ml-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">إنشاء مجتمع جديد</h3>
                    <p class="text-sm text-gray-600">أضف مجتمع جديد وخصص له الأجهزة</p>
                </div>
            </a>
            <a href="{{ route('super-admin.employees.create') }}" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
                <i class="fas fa-user-plus text-blue-600 text-2xl ml-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">إضافة موظف جديد</h3>
                    <p class="text-sm text-gray-600">أضف موظف وعينه لمجتمع</p>
                </div>
            </a>
            <a href="{{ route('super-admin.employees.index') }}" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition">
                <i class="fas fa-users text-purple-600 text-2xl ml-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-800">إدارة الموظفين</h3>
                    <p class="text-sm text-gray-600">عرض وتعديل الموظفين</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
