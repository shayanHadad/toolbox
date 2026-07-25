<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Dashboard\CustomerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\ExpertDashboardController;
use App\Http\Controllers\ExpertProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Dashboard\CompanyDashboardController;




Route::get('/', [HomeController::class, 'fetchData'])->name('home');

Route::get('/experts', [ExpertController::class, 'index'])->name('experts.index');
Route::get('/experts/{expert}', [ExpertController::class, 'show'])->name('experts.show');

Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('auth')
    ->name('contact.send');


Route::get('/register', [RegisterController::class, 'show'])->name('register');

Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/login', [HomeController::class, 'fetchWorkCategories'])->name('login');

Route::get('/dashboard/customer', CustomerDashboardController::class)
    ->middleware(['auth.custom', 'role:1'])
    ->name('dashboard.customer');

Route::patch('/dashboard/customer/profile', [ProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('customer.profile.update');

Route::get('/dashboard/expert', ExpertDashboardController::class)
    ->middleware(['auth.custom', 'role:2'])
    ->name('dashboard.expert');

Route::patch('/dashboard/expert/profile', [ExpertProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:2'])
    ->name('expert.profile.update');

Route::get('/dashboard/company', CompanyDashboardController::class)
    ->middleware(['auth.custom', 'role:3'])
    ->name('dashboard.company');


Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/messages', [MessageController::class, 'index'])
    ->middleware(['auth.custom'])
    ->name('messages.index');

Route::get('/messages/{partner}', [MessageController::class, 'show'])
    ->middleware(['auth.custom'])
    ->name('messages.show');

Route::post('/messages/{partner}', [MessageController::class, 'store'])
    ->middleware(['auth.custom', 'role:1,2'])
    ->name('messages.store');

Route::get('/bookmarks', [BookmarkController::class, 'index'])
    ->middleware(['auth.custom'])
    ->name('bookmarks.index');

Route::post('/experts/{expert}/bookmark', [BookmarkController::class, 'toggle'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('bookmarks.toggle');
