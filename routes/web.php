<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ActivityController;
use App\Http\Controllers\Agent\DashboardController as AgentDashboardController;
use App\Http\Controllers\WhatsAppWebController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        } elseif ($user->isEmployee()) {
            return redirect()->route('employee.dashboard');
        }
        return redirect()->route('whatsapp.chats');
    }
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserManagementController::class, 'index'])->name('index');
            Route::get('/create', [UserManagementController::class, 'create'])->name('create');
            Route::post('/', [UserManagementController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        Route::prefix('activity')->name('activity.')->group(function () {
            Route::get('/', [ActivityController::class, 'index'])->name('index');
            Route::get('/user/{user}', [ActivityController::class, 'userActivity'])->name('user');
            Route::post('/assign-chat', [ActivityController::class, 'assignChat'])->name('assign-chat');
        });
        
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
            Route::get('/general', [\App\Http\Controllers\Admin\ReportsController::class, 'general'])->name('general');
            Route::get('/agents', [\App\Http\Controllers\Admin\ReportsController::class, 'agents'])->name('agents');
            Route::get('/agents/{user}', [\App\Http\Controllers\Admin\ReportsController::class, 'agentDetail'])->name('agent-detail');
            Route::get('/export/general', [\App\Http\Controllers\Admin\ReportsController::class, 'exportGeneral'])->name('export-general');
            Route::get('/export/agents', [\App\Http\Controllers\Admin\ReportsController::class, 'exportAgents'])->name('export-agents');
        });
        
        Route::prefix('devices')->name('devices.')->group(function () {
            Route::get('/', [DeviceController::class, 'index'])->name('index');
            Route::get('/create', [DeviceController::class, 'create'])->name('create');
            Route::post('/', [DeviceController::class, 'store'])->name('store');
            Route::get('/{id}', [DeviceController::class, 'show'])->name('show');
            Route::get('/{id}/qr', [DeviceController::class, 'getQr'])->name('qr');
            Route::get('/{id}/status', [DeviceController::class, 'getStatus'])->name('status');
            Route::post('/{id}/disconnect', [DeviceController::class, 'disconnect'])->name('disconnect');
            Route::delete('/{id}', [DeviceController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('super-admins')->name('super-admins.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\SuperAdminManagementController::class, 'index'])->name('index');
            Route::get('/{superAdmin}', [\App\Http\Controllers\Admin\SuperAdminManagementController::class, 'show'])->name('show');
        });
    });
    
    // Super Admin Routes
    Route::middleware(['role:super_admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('dashboard');
        
        Route::prefix('communities')->name('communities.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdminController::class, 'communities'])->name('index');
            Route::get('/create', [\App\Http\Controllers\SuperAdminController::class, 'createCommunity'])->name('create');
            Route::post('/', [\App\Http\Controllers\SuperAdminController::class, 'storeCommunity'])->name('store');
            Route::get('/{community}/edit', [\App\Http\Controllers\SuperAdminController::class, 'editCommunity'])->name('edit');
            Route::put('/{community}', [\App\Http\Controllers\SuperAdminController::class, 'updateCommunity'])->name('update');
            Route::get('/{community}/employees', [\App\Http\Controllers\SuperAdminController::class, 'manageCommunityEmployees'])->name('employees');
            Route::post('/{community}/employees/assign', [\App\Http\Controllers\SuperAdminController::class, 'assignEmployeeToCommunity'])->name('employees.assign');
            Route::post('/{community}/employees/create-and-assign', [\App\Http\Controllers\SuperAdminController::class, 'createAndAssignEmployee'])->name('employees.create-and-assign');
            Route::delete('/{community}/employees/{employee}', [\App\Http\Controllers\SuperAdminController::class, 'removeEmployeeFromCommunity'])->name('employees.remove');
        });
        
        Route::prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdminController::class, 'employees'])->name('index');
            Route::get('/create', [\App\Http\Controllers\SuperAdminController::class, 'createEmployee'])->name('create');
            Route::post('/', [\App\Http\Controllers\SuperAdminController::class, 'storeEmployee'])->name('store');
            Route::get('/{employee}', [\App\Http\Controllers\SuperAdminController::class, 'showEmployee'])->name('show');
            Route::get('/{employee}/edit', [\App\Http\Controllers\SuperAdminController::class, 'editEmployee'])->name('edit');
            Route::put('/{employee}', [\App\Http\Controllers\SuperAdminController::class, 'updateEmployee'])->name('update');
            Route::get('/{employee}/login-logs', [\App\Http\Controllers\SuperAdminController::class, 'employeeLoginLogs'])->name('login-logs');
        });
    });
    
    // Employee Routes
    Route::middleware(['role:employee'])->prefix('employee')->name('employee.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\EmployeeController::class, 'dashboard'])->name('dashboard');
        Route::get('/chats', [\App\Http\Controllers\EmployeeController::class, 'chats'])->name('chats');
        
        Route::prefix('chat-assignments')->name('chat-assignments.')->group(function () {
            Route::post('/check', [\App\Http\Controllers\ChatAssignmentController::class, 'checkAssignment'])->name('check');
            Route::post('/claim', [\App\Http\Controllers\ChatAssignmentController::class, 'claimChat'])->name('claim');
            Route::post('/update-status', [\App\Http\Controllers\ChatAssignmentController::class, 'updateStatus'])->name('update-status');
            Route::post('/release', [\App\Http\Controllers\ChatAssignmentController::class, 'releaseChat'])->name('release');
            Route::get('/list', [\App\Http\Controllers\ChatAssignmentController::class, 'getAssignments'])->name('list');
        });
    });
    
    Route::middleware(['role:admin,agent,super_admin,employee'])->prefix('whatsapp')->name('whatsapp.')->group(function () {
        Route::get('/', [WhatsAppWebController::class, 'index'])->name('index');
        Route::get('/connect', [WhatsAppWebController::class, 'connect'])->name('connect');
        Route::get('/chats', [WhatsAppWebController::class, 'chats'])->name('chats');
        Route::get('/api/status', [WhatsAppWebController::class, 'getStatus'])->name('api.status');
        Route::get('/api/qr', [WhatsAppWebController::class, 'getQr'])->name('api.qr');
        Route::get('/api/chats', [WhatsAppWebController::class, 'getChats'])->name('api.chats');
        Route::get('/api/messages/{chatId}', [WhatsAppWebController::class, 'getMessages'])->name('api.messages');
        Route::post('/api/send', [WhatsAppWebController::class, 'sendMessage'])->name('api.send');
        Route::post('/api/logout', [WhatsAppWebController::class, 'logout'])->name('api.logout');
    });
    
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');
        Route::get('/messages/{id}', [DashboardController::class, 'show'])->name('show');
        Route::post('/messages/{id}/reply', [DashboardController::class, 'reply'])->name('reply');
        Route::post('/send-message', [DashboardController::class, 'sendMessage'])->name('sendMessage');
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook');
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
