<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - لوحة التحكم</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gradient-to-b from-green-600 to-green-700 text-white flex-shrink-0">
            <div class="p-6">
                <h1 class="text-2xl font-bold">Care Bot Admin</h1>
                <p class="text-green-200 text-sm mt-1">نظام إدارة Care Bot</p>
            </div>
            
            <nav class="mt-6">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('admin.dashboard') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-home ml-3"></i>
                        <span>الرئيسية</span>
                    </a>
                    
                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('admin.users.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-users ml-3"></i>
                        <span>إدارة المستخدمين</span>
                    </a>
                    
                    <a href="{{ route('admin.super-admins.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('admin.super-admins.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-user-shield ml-3"></i>
                        <span>المديرين الفائقين</span>
                    </a>
                    
                    <a href="{{ route('admin.activity.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('admin.activity.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-chart-line ml-3"></i>
                        <span>نشاط المستخدمين</span>
                    </a>
                    
                    <a href="{{ route('admin.devices.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('admin.devices.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-mobile-alt ml-3"></i>
                        <span>الأجهزة المتصلة</span>
                    </a>
                    
                    <a href="{{ route('admin.reports.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('admin.reports.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-chart-bar ml-3"></i>
                        <span>التقارير</span>
                    </a>
                    
                    <a href="{{ route('whatsapp.chats') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('whatsapp.chats') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-comments ml-3"></i>
                        <span>المحادثات</span>
                    </a>
                @elseif(auth()->user()->isSuperAdmin())
                    <a href="{{ route('super-admin.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('super-admin.dashboard') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-home ml-3"></i>
                        <span>الرئيسية</span>
                    </a>
                    
                    <a href="{{ route('super-admin.communities.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('super-admin.communities.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-users ml-3"></i>
                        <span>المجتمعات</span>
                    </a>
                    
                    <a href="{{ route('super-admin.employees.index') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('super-admin.employees.*') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-user-tie ml-3"></i>
                        <span>الموظفين</span>
                    </a>
                    
                    <a href="{{ route('whatsapp.chats') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('whatsapp.chats') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-comments ml-3"></i>
                        <span>المحادثات</span>
                    </a>
                @elseif(auth()->user()->isEmployee())
                    <a href="{{ route('employee.dashboard') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('employee.dashboard') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-home ml-3"></i>
                        <span>الرئيسية</span>
                    </a>
                    
                    <a href="{{ route('employee.chats') }}" class="flex items-center px-6 py-3 hover:bg-green-500 transition {{ request()->routeIs('employee.chats') ? 'bg-green-500' : '' }}">
                        <i class="fas fa-comments ml-3"></i>
                        <span>الدردشات</span>
                    </a>
                    
                @endif
            </nav>

            <div class="absolute bottom-0 w-64 p-6 border-t border-green-500">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center ml-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-green-200">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-lg transition">
                        <i class="fas fa-sign-out-alt ml-2"></i>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between px-6 py-4">
                    <h2 class="text-xl font-semibold text-gray-800">@yield('title')</h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">{{ now()->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-check-circle ml-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
