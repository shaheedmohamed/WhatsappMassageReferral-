@extends('layouts.admin')

@section('title', 'إدارة المجتمعات')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة المجتمعات</h1>
            <p class="text-gray-600 mt-2">إدارة جميع المجتمعات والأجهزة المخصصة</p>
        </div>
        <a href="{{ route('super-admin.communities.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-plus ml-2"></i>
            إنشاء مجتمع جديد
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">اسم المجتمع</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الموظفين</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عدد الأجهزة</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($communities as $community)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $community->name }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-600">{{ Str::limit($community->description, 50) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $community->employees->count() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $community->devices->count() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($community->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        نشط
                    </span>
                    @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        غير نشط
                    </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('super-admin.communities.employees', $community) }}" class="text-green-600 hover:text-green-900 ml-3">
                        <i class="fas fa-users"></i> الموظفين
                    </a>
                    <a href="{{ route('super-admin.communities.edit', $community) }}" class="text-blue-600 hover:text-blue-900 ml-3">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    لا توجد مجتمعات بعد. <a href="{{ route('super-admin.communities.create') }}" class="text-blue-600 hover:text-blue-800">أنشئ مجتمع جديد</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $communities->links() }}
</div>
@endsection
