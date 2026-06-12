<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\MessageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/clients', [ClientController::class, 'apiIndex'])->name('api.clients.index');
Route::get('/api/messages', [MessageController::class, 'apiIndex'])->name('api.messages.index');
Route::get('/api/message/track', [MessageController::class, 'apiTrack'])->name('api.message.track');
Route::post('/api/message/post', [MessageController::class, 'apiPost'])->name('api.message.post');
Route::post('/api/message/status', [MessageController::class, 'apiStatus'])->name('api.message.status');
