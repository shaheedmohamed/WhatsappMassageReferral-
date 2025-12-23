@extends('layouts.admin')

@section('title', 'إدارة الموظفين')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة الموظفين</h1>
            <p class="text-gray-600 mt-2">إدارة جميع الموظفين تحت إشرافك</p>
        </div>
        <a href="{{ route('super-admin.employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-user-plus ml-2"></i>
            إضافة موظف جديد
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
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد الإلكتروني</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المجتمع</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">آخر نشاط</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($employees as $employee)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-10 w-10 flex-shrink-0 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold">{{ substr($employee->name, 0, 1) }}</span>
                        </div>
                        <div class="mr-4">
                            <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $employee->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $employee->community->name ?? 'غير محدد' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($employee->is_active)
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
                    {{ $employee->last_activity_at ? $employee->last_activity_at->diffForHumans() : 'لم يسجل دخول بعد' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ route('super-admin.employees.show', $employee) }}" class="text-green-600 hover:text-green-900 ml-3">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="{{ route('super-admin.employees.edit', $employee) }}" class="text-blue-600 hover:text-blue-900 ml-3">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    لا يوجد موظفين بعد. <a href="{{ route('super-admin.employees.create') }}" class="text-blue-600 hover:text-blue-800">أضف موظف جديد</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $employees->links() }}
</div>
@endsection
