<?php
//--//
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CategoryController;

// About page
Route::get('/about', function () {
    return view('about');
})->name('about');

// Contact page
Route::get('/contact', function () {
    return view('contact');
})->name('contact');

// Sending contact form
Route::post('/contact', [ContactController::class, 'send'])
    ->middleware('auth.custom')
    ->name('contact.send');

// Home page
Route::get('/', [HomeController::class, 'fetchData'])->name('home');

// Showing each work category page
Route::get('/categories/{category:url}', [CategoryController::class, 'show'])
    ->name('categories.show');
