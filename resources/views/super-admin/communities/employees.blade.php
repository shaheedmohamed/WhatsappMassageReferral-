@extends('layouts.admin')

@section('title', 'إدارة موظفين المجتمع')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">إدارة موظفين: {{ $community->name }}</h1>
            <p class="text-gray-600 mt-2">إضافة وإزالة الموظفين من هذا المجتمع</p>
        </div>
        <a href="{{ route('super-admin.communities.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة للمجتمعات
        </a>
    </div>
</div>

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    {{ session('error') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-user-plus text-green-600"></i>
            إضافة موظف للمجتمع
        </h2>
        
        <div class="mb-4 border-b border-gray-200">
            <nav class="flex -mb-px">
                <button type="button" onclick="showTab('existing')" id="tab-existing" class="tab-button active py-2 px-4 text-sm font-medium border-b-2 border-green-600 text-green-600">
                    <i class="fas fa-users ml-1"></i>
                    اختيار من الموظفين الموجودين
                </button>
                <button type="button" onclick="showTab('new')" id="tab-new" class="tab-button py-2 px-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    <i class="fas fa-user-plus ml-1"></i>
                    إنشاء موظف جديد
                </button>
            </nav>
        </div>

        <div id="content-existing" class="tab-content">
            @if($availableEmployees->count() > 0)
            <form action="{{ route('super-admin.communities.employees.assign', $community) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اختر موظف</label>
                    <select name="employee_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500" required>
                        <option value="">-- اختر موظف --</option>
                        @foreach($availableEmployees as $employee)
                        <option value="{{ $employee->id }}">
                            {{ $employee->name }} ({{ $employee->email }})
                            @if($employee->community_id)
                                - حالياً في: {{ $employee->community->name }}
                            @else
                                - غير مخصص لأي مجتمع
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">يمكنك إضافة أي موظف من موظفيك للمجتمع</p>
                </div>
                
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة الموظف للمجتمع
                </button>
            </form>
            @else
            <div class="text-center py-8">
                <i class="fas fa-users text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-600 mb-4">لا يوجد موظفين متاحين للإضافة</p>
                <p class="text-sm text-gray-500">يمكنك إنشاء موظف جديد من التبويب الثاني</p>
            </div>
            @endif
        </div>

        <div id="content-new" class="tab-content hidden">
            <form action="{{ route('super-admin.communities.employees.create-and-assign', $community) }}" method="POST" class="space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم الموظف</label>
                    <input type="text" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500" required minlength="8">
                    <p class="text-xs text-gray-500 mt-1">يجب أن تكون 8 أحرف على الأقل</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                    <input type="password" name="password_confirmation" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-500" required minlength="8">
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition">
                    <i class="fas fa-user-plus ml-2"></i>
                    إنشاء الموظف وإضافته للمجتمع
                </button>
            </form>
        </div>
    </div>

    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active', 'border-green-600', 'text-green-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            const activeButton = document.getElementById('tab-' + tab);
            activeButton.classList.add('active', 'border-green-600', 'text-green-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
            
            document.getElementById('content-' + tab).classList.remove('hidden');
        }
    </script>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-users text-blue-600"></i>
            الموظفين الحاليين ({{ $communityEmployees->count() }})
        </h2>
        
        @if($communityEmployees->count() > 0)
        <div class="space-y-3">
            @foreach($communityEmployees as $employee)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="font-semibold text-gray-800">{{ $employee->name }}</h3>
                            @if($employee->is_active)
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">نشط</span>
                            @else
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">غير نشط</span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $employee->email }}</p>
                        @if($employee->loginLogs->first())
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-clock"></i>
                            آخر تسجيل دخول: {{ $employee->loginLogs->first()->logged_in_at->diffForHumans() }}
                        </p>
                        @endif
                    </div>
                    <form action="{{ route('super-admin.communities.employees.remove', [$community, $employee]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إزالة هذا الموظف من المجتمع؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 p-2 rounded-lg hover:bg-red-50 transition" title="إزالة من المجتمع">
                            <i class="fas fa-user-minus"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-users-slash text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-600">لا يوجد موظفين في هذا المجتمع بعد</p>
            <p class="text-sm text-gray-500 mt-2">استخدم النموذج على اليسار لإضافة موظفين</p>
        </div>
        @endif
    </div>
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <div class="flex items-start">
        <i class="fas fa-info-circle text-blue-600 text-xl mt-1 ml-3"></i>
        <div>
            <h3 class="font-semibold text-blue-900 mb-2">معلومات مهمة:</h3>
            <ul class="text-sm text-blue-800 space-y-1">
                <li>• يمكنك إضافة أي موظف من موظفيك إلى هذا المجتمع</li>
                <li>• الموظف المخصص لمجتمع يمكنه الوصول فقط لمحادثات أجهزة هذا المجتمع</li>
                <li>• يمكنك نقل الموظف من مجتمع لآخر بسهولة</li>
                <li>• إزالة الموظف من المجتمع لا تحذف حسابه، فقط يصبح غير مخصص لأي مجتمع</li>
            </ul>
        </div>
    </div>
</div>
@endsection
