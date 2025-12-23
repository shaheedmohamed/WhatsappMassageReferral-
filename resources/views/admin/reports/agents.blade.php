@extends('layouts.admin')

@section('title', 'تقرير الموظفين')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">تقرير الموظفين</h1>
            <p class="text-gray-600 mt-2">تقرير مفصل عن أداء جميع الموظفين</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.agents') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">الموظف</label>
                <select name="agent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    <option value="">جميع الموظفين</option>
                    @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-filter ml-2"></i>
                تصفية
            </button>
            <a href="{{ route('admin.reports.export-agents', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-file-excel ml-2"></i>
                تصدير Excel
            </a>
        </form>
    </div>

    <!-- Agents Summary Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800">ملخص أداء الموظفين</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">اسم الموظف</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">ساعات العمل</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">الرسائل المعالجة</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">تحويل تلقائي</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">تحويل يدوي</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">متوسط الرد (دقيقة)</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">الجلسات</th>
                        <th class="text-right py-3 px-4 text-gray-700 font-semibold">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agentStats as $stat)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center ml-2">
                                    <i class="fas fa-user text-blue-600"></i>
                                </div>
                                <span class="font-semibold text-gray-800">{{ $stat['name'] }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                {{ number_format($stat['total_hours']) }} ساعة
                            </span>
                        </td>
                        <td class="py-3 px-4 font-semibold text-gray-800">{{ number_format($stat['messages_replied']) }}</td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded text-xs">
                                {{ number_format($stat['auto_transferred']) }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs">
                                {{ number_format($stat['manual_transferred']) }}
                            </span>
                        </td>
                        <td class="py-3 px-4 font-semibold text-blue-600">{{ number_format($stat['avg_response_time'] ?? 0, 1) }}</td>
                        <td class="py-3 px-4">{{ number_format($stat['sessions_count']) }}</td>
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.reports.agent-detail', ['user' => $stat['id'], 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="text-blue-500 hover:text-blue-600">
                                <i class="fas fa-eye ml-1"></i>
                                التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>لا توجد بيانات للعرض</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance Comparison -->
    @if($agentStats->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Performers by Messages -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الأكثر معالجة للرسائل</h3>
            <div class="space-y-3">
                @foreach($agentStats->sortByDesc('messages_replied')->take(5) as $index => $stat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center flex-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center ml-3 text-white font-bold">
                            {{ $index + 1 }}
                        </div>
                        <span class="font-semibold text-gray-800">{{ $stat['name'] }}</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600">{{ number_format($stat['messages_replied']) }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Fastest Response Time -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الأسرع في الرد</h3>
            <div class="space-y-3">
                @foreach($agentStats->sortBy('avg_response_time')->take(5) as $index => $stat)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center flex-1">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-green-500 to-teal-500 flex items-center justify-center ml-3 text-white font-bold">
                            {{ $index + 1 }}
                        </div>
                        <span class="font-semibold text-gray-800">{{ $stat['name'] }}</span>
                    </div>
                    <span class="text-lg font-bold text-green-600">{{ number_format($stat['avg_response_time'] ?? 0, 1) }} د</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
