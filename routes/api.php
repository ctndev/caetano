<?php

use App\Http\Controllers\Api\WhatsAppController;
use App\Http\Middleware\ValidateWhatsAppSecret;
use Illuminate\Support\Facades\Route;

Route::middleware(ValidateWhatsAppSecret::class)->prefix('whatsapp')->group(function () {
    Route::post('/message', [WhatsAppController::class, 'receiveMessage']);
    Route::get('/status', [WhatsAppController::class, 'status']);
    Route::get('/qrcode', [WhatsAppController::class, 'qrCode']);
});
