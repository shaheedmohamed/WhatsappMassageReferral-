<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.verify');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'webhook'])->name('whatsapp.webhook');

Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/messages/{id}', [DashboardController::class, 'show'])->name('show');
    Route::post('/messages/{id}/reply', [DashboardController::class, 'reply'])->name('reply');
    Route::post('/send-message', [DashboardController::class, 'sendMessage'])->name('send');
});
