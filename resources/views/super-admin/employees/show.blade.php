@extends('layouts.admin')

@section('title', 'تفاصيل الموظف')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تفاصيل الموظف: {{ $employee->name }}</h1>
            <p class="text-gray-600 mt-2">عرض جميع معلومات وإحصائيات الموظف</p>
        </div>
        <a href="{{ route('super-admin.employees.edit', $employee) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
            <i class="fas fa-edit ml-2"></i>
            تعديل
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">المعلومات الأساسية</h3>
            @if($employee->is_active)
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">نشط</span>
            @else
            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">غير نشط</span>
            @endif
        </div>
        <div class="space-y-3">
            <div>
                <p class="text-sm text-gray-600">الاسم</p>
                <p class="font-medium text-gray-900">{{ $employee->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">البريد الإلكتروني</p>
                <p class="font-medium text-gray-900">{{ $employee->email }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">المجتمع</p>
                <p class="font-medium text-gray-900">{{ $employee->community->name ?? 'غير محدد' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">تاريخ الانضمام</p>
                <p class="font-medium text-gray-900">{{ $employee->created_at->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">آخر نشاط</p>
                <p class="font-medium text-gray-900">{{ $employee->last_activity_at ? $employee->last_activity_at->diffForHumans() : 'لم يسجل دخول بعد' }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">إحصائيات الرسائل</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الرسائل</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $employee->assignedMessages->count() }}</p>
                </div>
                <i class="fas fa-envelope text-blue-600 text-2xl"></i>
            </div>
            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">الرسائل المكتملة</p>
                    <p class="text-2xl font-bold text-green-600">{{ $employee->assignedMessages->where('status', 'completed')->count() }}</p>
                </div>
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600">الرسائل المعلقة</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $employee->assignedMessages->where('status', 'pending')->count() }}</p>
                </div>
                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">سجل العمل</h3>
        <div class="space-y-3">
            @forelse($employee->workLogs->take(5) as $log)
            <div class="border-b pb-2 last:border-b-0">
                <p class="text-sm font-medium text-gray-900">{{ $log->action }}</p>
                <p class="text-xs text-gray-600">{{ $log->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-gray-500 text-sm text-center py-4">لا يوجد سجل عمل بعد</p>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">الرسائل الأخيرة</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المحادثة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرسالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($employee->assignedMessages->take(10) as $message)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $message->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $message->chat_id }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ Str::limit($message->body, 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($message->status === 'completed')
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">مكتمل</span>
                        @else
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">معلق</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">لا توجد رسائل بعد</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
