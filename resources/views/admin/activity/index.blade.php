@extends('layouts.admin')

@section('title', 'نشاط المستخدمين')

@section('content')
<div class="container mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">لوحة المراقبة والإشراف</h1>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500">آخر تحديث: <span id="last-update">الآن</span></span>
            <button onclick="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-sync-alt ml-2"></i>تحديث
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي المحادثات</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_chats'] }}</p>
                </div>
                <i class="fas fa-comments text-3xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">قيد المعالجة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['in_progress_chats'] }}</p>
                </div>
                <i class="fas fa-spinner text-3xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['on_hold_chats'] }}</p>
                </div>
                <i class="fas fa-pause-circle text-3xl text-yellow-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">مكتملة اليوم</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed_today'] }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">موظفين متصلين</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active_agents'] }}</p>
                </div>
                <i class="fas fa-user-check text-3xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">موظفين فاضيين</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $allAgents->where('workload_status', 'free')->count() }}</p>
                </div>
                <i class="fas fa-user-clock text-3xl text-purple-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">موظفين مشغولين</p>
                    <p class="text-2xl font-bold text-red-600">{{ $allAgents->whereIn('workload_status', ['moderate', 'busy'])->count() }}</p>
                </div>
                <i class="fas fa-user-times text-3xl text-red-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4 lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">لم يتم الرد</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_chats'] }}</p>
                </div>
                <i class="fas fa-exclamation-triangle text-3xl text-orange-500"></i>
            </div>
        </div>
    </div>

    <!-- Employee Status Overview -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">حالة الموظفين وأحمال العمل</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($allAgents as $agent)
            <div class="border rounded-lg p-4 {{ $agent->status === 'online' ? 'border-green-300 bg-green-50' : 'border-gray-300 bg-gray-50' }}">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white ml-2
                            {{ $agent->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}">
                            {{ substr($agent->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $agent->name }}</p>
                            <div class="flex items-center">
                                <span class="w-2 h-2 rounded-full ml-1 {{ $agent->status === 'online' ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                <span class="text-xs {{ $agent->status === 'online' ? 'text-green-600' : 'text-gray-500' }}">
                                    {{ $agent->status === 'online' ? 'متصل' : 'غير متصل' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $agent->workload_status === 'free' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $agent->workload_status === 'light' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $agent->workload_status === 'moderate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $agent->workload_status === 'busy' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ $agent->workload_status === 'free' ? 'فاضي' : '' }}
                        {{ $agent->workload_status === 'light' ? 'خفيف' : '' }}
                        {{ $agent->workload_status === 'moderate' ? 'متوسط' : '' }}
                        {{ $agent->workload_status === 'busy' ? 'مشغول' : '' }}
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">محادثات نشطة:</span>
                        <span class="font-semibold text-blue-600">{{ $agent->active_chats }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">في الانتظار:</span>
                        <span class="font-semibold text-yellow-600">{{ $agent->hold_chats }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-600">مكتملة اليوم:</span>
                        <span class="font-semibold text-green-600">{{ $agent->completed_today }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        <div class="h-2 rounded-full {{ $agent->active_chats >= 5 ? 'bg-red-500' : ($agent->active_chats >= 3 ? 'bg-yellow-500' : 'bg-green-500') }}"
                             style="width: {{ min(($agent->active_chats / 5) * 100, 100) }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 text-center mt-1">{{ $agent->active_chats }}/5 محادثات</p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8 text-gray-500">
                لا يوجد موظفين
            </div>
            @endforelse
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">المحادثات</h2>
                    <div class="flex gap-2">
                        <select id="filter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm" onchange="applyFilter()">
                            <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>الكل</option>
                            <option value="pending" {{ $filter === 'pending' ? 'selected' : '' }}>في الانتظار</option>
                            <option value="replied" {{ $filter === 'replied' ? 'selected' : '' }}>تم الرد</option>
                        </select>

                        <select id="device_filter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm" onchange="applyFilter()">
                            <option value="">جميع الأجهزة</option>
                            @foreach($devices as $device)
                            <option value="{{ $device->id }}" {{ $deviceId == $device->id ? 'selected' : '' }}>
                                {{ $device->name }} ({{ $device->phone_number }})
                            </option>
                            @endforeach
                        </select>

                        <select id="user_filter" class="px-4 py-2 border border-gray-300 rounded-lg text-sm" onchange="applyFilter()">
                            <option value="">جميع الموظفين</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">من</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الرسالة</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الجهاز</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموظف</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوقت</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($messages as $message)
                            @php
                                $chatId = $message->chat_id ?? $message->from_number;
                                $assignedEmployee = $message->assigned_employee ?? $message->assignedUser;
                            @endphp
                            <tr class="hover:bg-blue-50 cursor-pointer transition" 
                                onclick="window.location='{{ route('admin.activity.chat.view', ['chat_id' => $chatId, 'device_id' => $message->device_id, 'chat_number' => $message->from_number]) }}'">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $message->from_name ?? 'غير محدد' }}</div>
                                    <div class="text-xs text-gray-500">{{ $message->from_number }}</div>
                                    @if(isset($message->message_count) && $message->message_count > 1)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                        <i class="fas fa-comments ml-1"></i>{{ $message->message_count }} رسالة
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 truncate max-w-xs">{{ $message->message_body ?? 'رسالة وسائط' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($message->device)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $message->device->phone_number }}
                                    </span>
                                    @else
                                    <span class="text-xs text-gray-400">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($assignedEmployee)
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center text-white text-xs ml-2">
                                            {{ substr($assignedEmployee->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm text-gray-900">{{ $assignedEmployee->name }}</span>
                                    </div>
                                    @else
                                    <span class="text-xs text-gray-400">غير معين</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if(isset($message->assignment))
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $message->assignment->status === 'in_progress' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $message->assignment->status_text }}
                                    </span>
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $message->replied ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $message->replied ? 'تم الرد' : 'في الانتظار' }}
                                    </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($message->last_message_time ?? $message->message_timestamp)->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    لا توجد محادثات
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $messages->links() }}
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">المحادثات النشطة حسب الموظف</h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($activeAssignments as $userId => $assignments)
                    @php
                        $user = $assignments->first()->user;
                        $inProgress = $assignments->where('status', 'in_progress')->count();
                        $onHold = $assignments->where('status', 'on_hold')->count();
                    @endphp
                    <div class="p-3 border border-gray-200 rounded-lg hover:border-blue-300 transition">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs ml-2">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $assignments->count() }}
                            </span>
                        </div>
                        <div class="flex gap-2 mb-2">
                            @if($inProgress > 0)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                <i class="fas fa-play-circle"></i> {{ $inProgress }} جارى
                            </span>
                            @endif
                            @if($onHold > 0)
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                <i class="fas fa-pause-circle"></i> {{ $onHold }} معلق
                            </span>
                            @endif
                        </div>
                        <div class="space-y-1">
                            @foreach($assignments->take(3) as $assignment)
                            <a href="{{ route('admin.activity.chat.view', ['chat_id' => $assignment->chat_id, 'device_id' => $assignment->device_id, 'chat_number' => $assignment->chat_number]) }}" 
                               class="text-xs text-gray-600 flex items-center justify-between bg-gray-50 p-2 rounded hover:bg-blue-50 transition cursor-pointer">
                                <span class="flex items-center">
                                    <i class="fas fa-phone ml-1 text-blue-500"></i>
                                    <span class="hover:text-blue-600">{{ $assignment->chat_number }}</span>
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs
                                    {{ $assignment->status === 'in_progress' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $assignment->status_text }}
                                </span>
                            </a>
                            @endforeach
                            @if($assignments->count() > 3)
                            <p class="text-xs text-gray-400 text-center mt-1">+ {{ $assignments->count() - 3 }} أخرى</p>
                            @endif
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-8">لا توجد محادثات نشطة</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">آخر النشاطات</h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($recentActivity as $activity)
                    <div class="p-3 bg-gray-50 rounded-lg border-r-4 
                        {{ $activity->status === 'completed' ? 'border-green-500' : 'border-blue-500' }}">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-gray-900">{{ $activity->user->name ?? 'غير محدد' }}</p>
                            <span class="text-xs text-gray-500">{{ $activity->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="flex items-center text-xs text-gray-600">
                            <i class="fas fa-phone ml-1"></i>
                            <span>{{ $activity->chat_number }}</span>
                        </div>
                        <div class="mt-1">
                            <span class="px-2 py-0.5 text-xs rounded-full
                                {{ $activity->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $activity->status_text }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد نشاطات حديثة</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function applyFilter() {
        const filter = document.getElementById('filter').value;
        const deviceId = document.getElementById('device_filter').value;
        const userId = document.getElementById('user_filter').value;
        
        let url = '{{ route("admin.activity.index") }}?filter=' + filter;
        if (deviceId) url += '&device_id=' + deviceId;
        if (userId) url += '&user_id=' + userId;
        
        window.location.href = url;
    }

    function refreshData() {
        location.reload();
    }

    // Auto-refresh every 30 seconds
    let autoRefreshInterval;
    function startAutoRefresh() {
        autoRefreshInterval = setInterval(() => {
            const lastUpdate = document.getElementById('last-update');
            lastUpdate.textContent = 'جاري التحديث...';
            location.reload();
        }, 30000); // 30 seconds
    }

    // Update last update time
    function updateLastUpdateTime() {
        const lastUpdate = document.getElementById('last-update');
        const now = new Date();
        lastUpdate.textContent = now.toLocaleTimeString('ar-EG');
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateLastUpdateTime();
        startAutoRefresh();
    });
</script>
@endpush
@endsection
