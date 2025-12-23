@extends('layouts.admin')

@section('title', 'سجل تسجيلات الدخول - ' . $employee->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">سجل تسجيلات الدخول</h1>
            <p class="text-gray-600 mt-2">{{ $employee->name }} - {{ $employee->email }}</p>
        </div>
        <a href="{{ route('super-admin.employees.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للموظفين
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-800">سجل تسجيلات الدخول الكاملة</h2>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ والوقت</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">عنوان IP</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتصفح</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($loginLogs as $log)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $log->logged_in_at->format('Y-m-d') }}</div>
                    <div class="text-sm text-gray-500">{{ $log->logged_in_at->format('h:i A') }}</div>
                    <div class="text-xs text-gray-400">{{ $log->logged_in_at->diffForHumans() }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 font-mono">{{ $log->ip_address ?? 'غير متوفر' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">
                        @php
                            $agent = $log->user_agent ?? 'غير متوفر';
                            if (str_contains($agent, 'Chrome')) {
                                $browser = 'Chrome';
                                $icon = 'fab fa-chrome';
                            } elseif (str_contains($agent, 'Firefox')) {
                                $browser = 'Firefox';
                                $icon = 'fab fa-firefox';
                            } elseif (str_contains($agent, 'Safari')) {
                                $browser = 'Safari';
                                $icon = 'fab fa-safari';
                            } elseif (str_contains($agent, 'Edge')) {
                                $browser = 'Edge';
                                $icon = 'fab fa-edge';
                            } else {
                                $browser = 'متصفح آخر';
                                $icon = 'fas fa-globe';
                            }
                        @endphp
                        <i class="{{ $icon }} ml-2"></i>{{ $browser }}
                    </div>
                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($log->user_agent, 60) }}</div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-info-circle text-3xl text-gray-400 mb-2"></i>
                    <p>لم يسجل هذا الموظف دخوله بعد</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $loginLogs->links() }}
</div>
@endsection
