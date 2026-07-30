<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Customer ordering a service from an expert
Route::post('/experts/{expert}/order', [OrderController::class, 'storeForExpert'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('orders.expert.store');

// Customer ordering a service from a company
Route::post('/companies/{company}/order', [OrderController::class, 'storeForCompany'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('orders.company.store');

// Showing orders list
Route::get('/orders', [OrderController::class, 'index'])
    ->middleware(['auth.custom', 'role:1,2,3,4'])
    ->name('orders.index');

// Showing order requests to roles 2, 3, 4
Route::get('/orders/requests', [OrderController::class, 'requests'])
    ->middleware(['auth.custom', 'role:2,3,4'])
    ->name('orders.requests');

// Approving an order request
Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])
    ->middleware(['auth.custom', 'role:2,3,4'])
    ->name('orders.approve');

// Rejecting an order request
Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])
    ->middleware(['auth.custom', 'role:2,3,4'])
    ->name('orders.reject');

// Canceling an order request by role 1
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('orders.cancel');

// Submiting by user after the order is done
Route::post('/orders/{order}/review', [OrderController::class, 'review'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('orders.review');
