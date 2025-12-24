@extends('layouts.admin')

@section('title', 'التقارير')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">التقارير والإحصائيات</h1>
        <p class="text-gray-600 mt-2">تقارير شاملة عن أداء النظام والموظفين</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- General Report -->
        <a href="{{ route('admin.reports.general') }}" class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
                <i class="fas fa-arrow-left text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">التقرير العام</h3>
            <p class="text-blue-100">تقرير شامل عن الرسائل والأداء خلال فترة محددة</p>
            <ul class="mt-4 space-y-1 text-sm text-blue-100">
                <li><i class="fas fa-check ml-2"></i>عدد الرسائل المستقبلة والمرسلة</li>
                <li><i class="fas fa-check ml-2"></i>متوسط سرعة الرد</li>
                <li><i class="fas fa-check ml-2"></i>الرسائل حسب المجموعات</li>
                <li><i class="fas fa-check ml-2"></i>الرسائل حسب الأجهزة</li>
            </ul>
        </a>

        <!-- Agents Report -->
        <a href="{{ route('admin.reports.agents') }}" class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white hover:shadow-xl transition transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-users text-3xl"></i>
                </div>
                <i class="fas fa-arrow-left text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold mb-2">تقرير الموظفين</h3>
            <p class="text-green-100">تقرير مفصل عن أداء جميع الموظفين</p>
            <ul class="mt-4 space-y-1 text-sm text-green-100">
                <li><i class="fas fa-check ml-2"></i>ساعات العمل</li>
                <li><i class="fas fa-check ml-2"></i>عدد الرسائل المعالجة</li>
                <li><i class="fas fa-check ml-2"></i>التحويلات التلقائية واليدوية</li>
                <li><i class="fas fa-check ml-2"></i>متوسط سرعة الرد</li>
            </ul>
        </a>

        <!-- Performance Metrics -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-tachometer-alt text-3xl"></i>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-2">مقاييس الأداء</h3>
            <p class="text-purple-100">إحصائيات سريعة عن الأداء الحالي</p>
            <div class="mt-4 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm">الرسائل اليوم</span>
                    <span class="font-bold text-lg">{{ \App\Models\WhatsappMessage::whereDate('created_at', today())->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm">الموظفين النشطين</span>
                    <span class="font-bold text-lg">{{ \App\Models\User::where('role', 'employee')->where('is_active', true)->count() }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm">الأجهزة المتصلة</span>
                    <span class="font-bold text-lg">{{ \App\Models\WhatsappDevice::where('status', 'connected')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي الرسائل</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format(\App\Models\WhatsappMessage::count()) }}</h3>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-envelope text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">رسائل هذا الشهر</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format(\App\Models\WhatsappMessage::whereMonth('created_at', now()->month)->count()) }}</h3>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-calendar-alt text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">متوسط الرد (دقيقة)</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ number_format(\App\Models\WhatsappMessage::whereNotNull('response_time_minutes')->avg('response_time_minutes') ?? 0, 1) }}</h3>
                </div>
                <div class="bg-orange-100 rounded-full p-3">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي الموظفين</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ \App\Models\User::where('role', 'employee')->count() }}</h3>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-user-tie text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">تصدير التقارير</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">تصدير التقرير العام</h4>
                <p class="text-sm text-gray-600 mb-3">تصدير تقرير شامل بصيغة Excel</p>
                <form action="{{ route('admin.reports.export-general') }}" method="GET" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="date" name="start_date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                        <input type="date" name="end_date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition">
                        <i class="fas fa-file-excel ml-2"></i>
                        تصدير Excel
                    </button>
                </form>
            </div>

            <div class="border border-gray-200 rounded-lg p-4">
                <h4 class="font-semibold text-gray-800 mb-2">تصدير تقرير الموظفين</h4>
                <p class="text-sm text-gray-600 mb-3">تصدير تقرير الموظفين بصيغة Excel</p>
                <form action="{{ route('admin.reports.export-agents') }}" method="GET" class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="date" name="start_date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ now()->subDays(30)->format('Y-m-d') }}">
                        <input type="date" name="end_date" class="px-3 py-2 border border-gray-300 rounded-lg text-sm" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition">
                        <i class="fas fa-file-excel ml-2"></i>
                        تصدير Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
