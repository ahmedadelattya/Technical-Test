<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\ProductController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
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
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('can:' . PermissionEnum::USER_READ['name'])->name('index');
        Route::get('create', [UserController::class, 'create'])->middleware('can:' . PermissionEnum::USER_CREATE['name'])->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('can:' . PermissionEnum::USER_CREATE['name'])->name('store');
        Route::get('{user}/edit', [UserController::class, 'edit'])->middleware('can:' . PermissionEnum::USER_UPDATE['name'])->name('edit');
        Route::put('{user}', [UserController::class, 'update'])->middleware('can:' . PermissionEnum::USER_UPDATE['name'])->name('update');
        Route::delete('{user}', [UserController::class, 'destroy'])->middleware('can:' . PermissionEnum::USER_DELETE['name'])->name('destroy');
    });
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->middleware('can:' . PermissionEnum::CATEGORY_READ['name'])->name('index');
        Route::get('create', [CategoryController::class, 'create'])->middleware('can:' . PermissionEnum::CATEGORY_CREATE['name'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->middleware('can:' . PermissionEnum::CATEGORY_CREATE['name'])->name('store');
        Route::get('{category}/edit', [CategoryController::class, 'edit'])->middleware('can:' . PermissionEnum::CATEGORY_UPDATE['name'])->name('edit');
        Route::put('{category}', [CategoryController::class, 'update'])->middleware('can:' . PermissionEnum::CATEGORY_UPDATE['name'])->name('update');
        Route::delete('{category}', [CategoryController::class, 'destroy'])->middleware('can:' . PermissionEnum::CATEGORY_DELETE['name'])->name('destroy');
    });
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('can:' . PermissionEnum::PRODUCT_READ['name'])->name('index');
        Route::get('create', [ProductController::class, 'create'])->middleware('can:' . PermissionEnum::PRODUCT_CREATE['name'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->middleware('can:' . PermissionEnum::PRODUCT_CREATE['name'])->name('store');
        Route::get('{product}/edit', [ProductController::class, 'edit'])->middleware('can:' . PermissionEnum::PRODUCT_UPDATE['name'])->name('edit');
        Route::put('{product}', [ProductController::class, 'update'])->middleware('can:' . PermissionEnum::PRODUCT_UPDATE['name'])->name('update');
        Route::delete('{product}', [ProductController::class, 'destroy'])->middleware('can:' . PermissionEnum::PRODUCT_DELETE['name'])->name('destroy');
    });
});



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
