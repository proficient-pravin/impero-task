<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BusinessController::class, 'index']);
Route::prefix('businesses')->name('businesses.')->group(function () {
    Route::controller(BusinessController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/branches', 'branches')->name('branches');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::get('/data', 'getData')->name('data');
    });
});

Route::prefix('branches')->name('branches.')->group(function () {
    Route::controller(BranchController::class)->group(function () {
        Route::get('/{id}', 'show')->name('show');
        Route::get('/data/{business_id}', 'getData')->name('data');
        Route::get('/create/{business_id}', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::delete('/{id}', 'destroy')->name('destroy');

        Route::get('/{id}/availability', 'editAvailability')->name('availability');
        Route::post('/{id}/availability', 'updateAvailability')->name('availability.update');
        Route::get('/{branch}/availability/show', 'showAvailability')->name('availability.show');
    });
});
