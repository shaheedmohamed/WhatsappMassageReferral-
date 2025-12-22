@extends('layouts.admin')

@section('title', 'نشاط المستخدمين')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">نشاط المستخدمين والمحادثات</h1>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي المحادثات</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_chats'] }}</p>
                </div>
                <i class="fas fa-comments text-3xl text-blue-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_chats'] }}</p>
                </div>
                <i class="fas fa-clock text-3xl text-yellow-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">تم الرد عليها</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['replied_chats'] }}</p>
                </div>
                <i class="fas fa-check-circle text-3xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">الموظفين المتصلين</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active_agents'] }}</p>
                </div>
                <i class="fas fa-user-check text-3xl text-green-500"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي الموظفين</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_agents'] }}</p>
                </div>
                <i class="fas fa-users text-3xl text-gray-500"></i>
            </div>
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
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $message->from_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $message->from_number }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-900 truncate max-w-xs">{{ $message->message_body }}</div>
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
                                    @if($message->assignedUser)
                                    <span class="text-sm text-gray-900">{{ $message->assignedUser->name }}</span>
                                    @else
                                    <span class="text-xs text-gray-400">غير معين</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $message->replied ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $message->replied ? 'تم الرد' : 'في الانتظار' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $message->message_timestamp->diffForHumans() }}
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
                <h2 class="text-xl font-bold text-gray-800 mb-4">الموظفين المتصلين</h2>
                <div class="space-y-3">
                    @forelse($onlineUsers as $user)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white ml-3">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <div class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full ml-1"></span>
                                    <span class="text-xs text-gray-500">متصل الآن</span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.activity.user', $user) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-chart-bar"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا يوجد موظفين متصلين</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4">المحادثات النشطة</h2>
                <div class="space-y-3">
                    @forelse($activeAssignments as $userId => $assignments)
                    @php
                        $user = $assignments->first()->user;
                    @endphp
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $assignments->count() }} محادثة
                            </span>
                        </div>
                        <div class="space-y-1">
                            @foreach($assignments->take(3) as $assignment)
                            <div class="text-xs text-gray-600 flex items-center">
                                <i class="fas fa-phone ml-1"></i>
                                {{ $assignment->chat_number }}
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500 text-center py-4">لا توجد محادثات نشطة</p>
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
</script>
@endpush
@endsection
