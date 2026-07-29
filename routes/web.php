<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\Dashboard\CustomerDashboardController;
use App\Http\Controllers\Dashboard\CompanyDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Dashboard\ExpertDashboardController;
use App\Http\Controllers\ExpertProfileController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\BookmarkController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CompanyAdminController;

// About page
Route::get('/about', function () {
    return view('about');
})->name('about');

// Contact page + form
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('auth')
    ->name('contact.send');

// Home page
Route::get('/', [HomeController::class, 'fetchData'])->name('home');

// Showing each work category page
Route::get('/categories/{category:url}', [CategoryController::class, 'show'])
    ->name('categories.show');

// Register
Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// Login
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Experts page (index) + Expert profile (show)
Route::get('/experts', [ExpertController::class, 'index'])->name('experts.index');
Route::get('/experts/{expert}', [ExpertController::class, 'show'])->name('experts.show');

// Companies page (index) + Companies profile (show)
Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');

///////////////////////////////////////////////////////// Dashboards ////////////////////////////////////////////////////////////
// Admin
Route::get('/dashboard/admin', AdminDashboardController::class)
    ->middleware(['auth.custom', 'role:0'])
    ->name('dashboard.admin');

// Admin creating a new company + its owner (role=4)
Route::post('/dashboard/admin/companies', [AdminCompanyController::class, 'store'])
    ->middleware(['auth.custom', 'role:0'])
    ->name('admin.companies.store');

// Admin editing an existing company + its owner
Route::put('/dashboard/admin/companies/{company}', [AdminCompanyController::class, 'update'])
    ->middleware(['auth.custom', 'role:0'])
    ->name('admin.companies.update');

// Admin deleting a company (and its owner/admins/orders/messages)
Route::delete('/dashboard/admin/companies/{company}', [AdminCompanyController::class, 'destroy'])
    ->middleware(['auth.custom', 'role:0'])
    ->name('admin.companies.destroy');

// Customer dashboard
Route::get('/dashboard/customer', CustomerDashboardController::class)
    ->middleware(['auth.custom', 'role:1'])
    ->name('dashboard.customer');

// Update customer profile
Route::patch('/dashboard/customer/profile', [ProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('customer.profile.update');

// Expert dashboard
Route::get('/dashboard/expert', ExpertDashboardController::class)
    ->middleware(['auth.custom', 'role:2'])
    ->name('dashboard.expert');

// Update expert profile
Route::patch('/dashboard/expert/profile', [ExpertProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:2'])
    ->name('expert.profile.update');

// Company dashboard
Route::get('/dashboard/company', CompanyDashboardController::class)
    ->middleware(['auth.custom', 'role:3,4'])
    ->name('dashboard.company');

// Company owner updating company profile
Route::patch('/dashboard/company/profile', [CompanyProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:4'])
    ->name('company.profile.update');

// Company owner adding a new company admin (role=3) user
Route::post('/dashboard/company/admins', [CompanyAdminController::class, 'store'])
    ->middleware(['auth.custom', 'role:4'])
    ->name('company.admins.store');

// Company owner editing an existing company admin (role=3) user
Route::put('/dashboard/company/admins/{admin}', [CompanyAdminController::class, 'update'])
    ->middleware(['auth.custom', 'role:4'])
    ->name('company.admins.update');

// Company owner deleting an existing company admin (role=3) user
Route::delete('/dashboard/company/admins/{admin}', [CompanyAdminController::class, 'destroy'])
    ->middleware(['auth.custom', 'role:4'])
    ->name('company.admins.destroy');

// Delete account (only for roles 1 and 2)
Route::delete('/dashboard/account', [AccountController::class, 'destroy'])
    ->middleware(['auth.custom', 'role:1,2'])
    ->name('account.destroy');

////////////////////////////////////////////////////////////////// Messages ///////////////////////////////////////////////////
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

////////////////////////////////////////////////////////////////// Bookmarks ///////////////////////////////////////////////////
// Bokkmarks list
Route::get('/bookmarks', [BookmarkController::class, 'index'])
    ->middleware(['auth.custom'])
    ->name('bookmarks.index');

// Add or delete an expert from bookmarks
Route::post('/experts/{expert}/bookmark', [BookmarkController::class, 'toggle'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('bookmarks.toggle');

// Add or delete a company from bookmarks
Route::post('/companies/{company}/bookmark', [BookmarkController::class, 'toggleCompany'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('bookmarks.company.toggle');

////////////////////////////////////////////////////////////////// Orders ///////////////////////////////////////////////////
// Customer ordering a service from an expert
Route::post('/experts/{expert}/order', [OrderController::class, 'storeForExpert'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('orders.expert.store');

// Customer ordering a service from a company
Route::post('/companies/{company}/order', [OrderController::class, 'storeForCompany'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('orders.company.store');

// Showing orders list base on usesr role
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
