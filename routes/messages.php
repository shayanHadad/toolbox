<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;

// Message list page
Route::get('/messages', [MessageController::class, 'index'])
    ->middleware(['auth.custom'])
    ->name('messages.index');

// Specific chat page
Route::get('/messages/{partner}', [MessageController::class, 'show'])
    ->middleware(['auth.custom'])
    ->name('messages.show');

// Sending a new message
Route::post('/messages/{partner}', [MessageController::class, 'store'])
    ->middleware(['auth.custom', 'role:1,2,3,4'])
    ->name('messages.store');
