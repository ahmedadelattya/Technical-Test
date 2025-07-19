<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::prefix('dashboard')->middleware(['auth'])->name('dashboard.')->group(function () {
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('can:' . PermissionEnum::ROLE_READ['name'])->name('index');
        Route::get('create', [RoleController::class, 'create'])->middleware('can:' . PermissionEnum::ROLE_CREATE['name'])->name('create');
        Route::post('/', [RoleController::class, 'store'])->middleware('can:' . PermissionEnum::ROLE_CREATE['name'])->name('store');
        Route::get('{role}/edit', [RoleController::class, 'edit'])->middleware('can:' . PermissionEnum::ROLE_UPDATE['name'])->name('edit');
        Route::put('{role}', [RoleController::class, 'update'])->middleware('can:' . PermissionEnum::ROLE_UPDATE['name'])->name('update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->middleware('can:' . PermissionEnum::ROLE_DELETE['name'])->name('destroy');
    });
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
