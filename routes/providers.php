<?php
//--//
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExpertController;
use App\Http\Controllers\CompanyController;

// Show experts
Route::get('/experts', [ExpertController::class, 'index'])
    ->name('experts.index');

// Expert profile
Route::get('/experts/{expert}', [ExpertController::class, 'show'])
    ->name('experts.show');


// Show companies
Route::get('/companies', [CompanyController::class, 'index'])
    ->name('companies.index');

// Company profile
Route::get('/companies/{company}', [CompanyController::class, 'show'])
    ->name('companies.show');
