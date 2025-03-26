<?php

use App\Http\Controllers\BranchController;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\BusinessController;
use Illuminate\Support\Facades\Route;

// Route::get('/', [BranchController::class, 'index']); // Homepage showing branches and status

Route::prefix('businesses')->group(function () {
    Route::get('/', [BusinessController::class, 'index'])->name('businesses.index');
    Route::get('/create', [BusinessController::class, 'create'])->name('businesses.create');
    Route::post('/', [BusinessController::class, 'store'])->name('businesses.store');
    Route::delete('/{id}', [BusinessController::class, 'destroy'])->name('businesses.destroy');
});

Route::prefix('branches')->group(function () {
    Route::get('/{id}', [BranchController::class, 'show'])->name('branches.show');
    Route::get('/create/{business_id}', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/', [BranchController::class, 'store'])->name('branches.store');
    Route::delete('/{id}', [BranchController::class, 'destroy'])->name('branches.destroy');
});
