<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WhatsAppWebController;

Route::get('/', function () {
    return redirect()->route('whatsapp.index');
});

Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.verify');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'webhook'])->name('whatsapp.webhook');

Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
    Route::get('/', [WhatsAppWebController::class, 'index'])->name('index');
    Route::get('/connect', [WhatsAppWebController::class, 'connect'])->name('connect');
    Route::get('/chats', [WhatsAppWebController::class, 'chats'])->name('chats');
    
    Route::get('/api/status', [WhatsAppWebController::class, 'getStatus'])->name('api.status');
    Route::get('/api/qr', [WhatsAppWebController::class, 'getQRCode'])->name('api.qr');
    Route::get('/api/chats', [WhatsAppWebController::class, 'getChats'])->name('api.chats');
    Route::get('/api/messages/{chatId}', [WhatsAppWebController::class, 'getMessages'])->name('api.messages');
    Route::post('/api/send', [WhatsAppWebController::class, 'sendMessage'])->name('api.send');
    Route::post('/api/logout', [WhatsAppWebController::class, 'logout'])->name('api.logout');
});

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/messages/{id}', [DashboardController::class, 'show'])->name('show');
    Route::post('/messages/{id}/reply', [DashboardController::class, 'reply'])->name('reply');
    Route::post('/send-message', [DashboardController::class, 'sendMessage'])->name('send');
});
