<?php
//--//
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookmarkController;

// Bookmarks list
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
