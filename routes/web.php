<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\ActivityController;
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
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
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
    });
    
    Route::middleware(['role:admin,agent'])->prefix('whatsapp')->name('whatsapp.')->group(function () {
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
