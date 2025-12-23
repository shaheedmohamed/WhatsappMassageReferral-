@extends('layouts.admin')

@section('title', 'التقرير العام')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">التقرير العام</h1>
            <p class="text-gray-600 mt-2">تقرير شامل عن الرسائل والأداء</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-right ml-2"></i>
            رجوع
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.general') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-filter ml-2"></i>
                تصفية
            </button>
            <a href="{{ route('admin.reports.export-general', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-file-excel ml-2"></i>
                تصدير Excel
            </a>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">الرسائل المستقبلة</h3>
                <i class="fas fa-inbox text-2xl opacity-75"></i>
            </div>
            <p class="text-4xl font-bold">{{ number_format($totalReceived) }}</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">الرسائل المرسلة</h3>
                <i class="fas fa-paper-plane text-2xl opacity-75"></i>
            </div>
            <p class="text-4xl font-bold">{{ number_format($totalSent) }}</p>
        </div>

        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-semibold">متوسط وقت الرد</h3>
                <i class="fas fa-clock text-2xl opacity-75"></i>
            </div>
            <p class="text-4xl font-bold">{{ number_format($avgResponseTime ?? 0, 1) }}</p>
            <p class="text-sm opacity-75 mt-1">دقيقة</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Messages by Group -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الرسائل حسب المجموعات</h3>
            @if($messagesByGroup->count() > 0)
            <div class="space-y-3">
                @foreach($messagesByGroup as $group)
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="w-4 h-4 rounded-full bg-blue-500 ml-3"></div>
                        <span class="text-gray-700">{{ $group->group_type ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-gray-800">{{ number_format($group->count) }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($group->count / $messagesByGroup->max('count')) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-500 py-8">لا توجد بيانات</p>
            @endif
        </div>

        <!-- Messages by Device -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الرسائل حسب الأجهزة</h3>
            @if($messagesByDevice->count() > 0)
            <div class="space-y-3">
                @foreach($messagesByDevice as $device)
                <div class="flex items-center justify-between">
                    <div class="flex items-center flex-1">
                        <div class="w-4 h-4 rounded-full bg-green-500 ml-3"></div>
                        <span class="text-gray-700">{{ $device->device_name }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-gray-800">{{ number_format($device->count) }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($device->count / $messagesByDevice->max('count')) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-500 py-8">لا توجد بيانات</p>
            @endif
        </div>
    </div>

    <!-- Daily Trend -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">الاتجاه اليومي للرسائل</h3>
        @if($dailyTrend->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="text-right py-3 px-4 text-gray-700">التاريخ</th>
                        <th class="text-right py-3 px-4 text-gray-700">عدد الرسائل</th>
                        <th class="text-right py-3 px-4 text-gray-700">الرسم البياني</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyTrend as $trend)
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($trend->date)->format('Y-m-d') }}</td>
                        <td class="py-3 px-4 font-semibold">{{ number_format($trend->count) }}</td>
                        <td class="py-3 px-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($trend->count / $dailyTrend->max('count')) * 100 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-center text-gray-500 py-8">لا توجد بيانات</p>
        @endif
    </div>
</div>
@endsection
