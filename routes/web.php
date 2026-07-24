<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Dashboard\CustomerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\ExpertDashboardController;
use App\Http\Controllers\ExpertProfileController;
use App\Http\Controllers\LoginController;



Route::get('/', [HomeController::class, 'fetchData'])->name('home');

Route::get('/register', [RegisterController::class, 'show'])->name('register');

Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/login', [HomeController::class, 'fetchWorkCategories'])->name('login');

Route::get('/dashboard/customer', CustomerDashboardController::class)
    // ->middleware('auth')
    ->name('dashboard.customer');

Route::patch('/profile', [ProfileController::class, 'update'])
    // ->middleware('auth')
    ->name('profile.update');


Route::get('/dashboard/expert', ExpertDashboardController::class)
    // ->middleware('auth')
    ->name('dashboard.expert');

Route::patch('/dashboard/expert/profile', [ExpertProfileController::class, 'update'])
    // ->middleware('auth')
    ->name('dashboard.expert.profile.update');


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
  