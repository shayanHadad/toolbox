<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CompanyAdminController;
use App\Http\Controllers\AdminCompanyController;
use App\Http\Controllers\ExpertProfileController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\ExpertDashboardController;
use App\Http\Controllers\Dashboard\CompanyDashboardController;
use App\Http\Controllers\Dashboard\CustomerDashboardController;

// Delete account (only for roles 1 and 2)
Route::delete('/dashboard/account', [AccountController::class, 'destroy'])
    ->middleware(['auth.custom', 'role:1,2'])
    ->name('account.destroy');

////////////////////////////////////////////////// ADMIN ////////////////////////////////////////////////////////////////
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

////////////////////////////////////////////////// CUSTOMER ////////////////////////////////////////////////////////////////
// Customer dashboard
Route::get('/dashboard/customer', CustomerDashboardController::class)
    ->middleware(['auth.custom', 'role:1'])
    ->name('dashboard.customer');

// Update customer profile
Route::patch('/dashboard/customer/profile', [ProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:1'])
    ->name('customer.profile.update');

////////////////////////////////////////////////// EXPERT ////////////////////////////////////////////////////////////////
// Expert dashboard
Route::get('/dashboard/expert', ExpertDashboardController::class)
    ->middleware(['auth.custom', 'role:2'])
    ->name('dashboard.expert');

// Update expert profile
Route::patch('/dashboard/expert/profile', [ExpertProfileController::class, 'update'])
    ->middleware(['auth.custom', 'role:2'])
    ->name('expert.profile.update');

////////////////////////////////////////////////// COMPANY ////////////////////////////////////////////////////////////////
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
