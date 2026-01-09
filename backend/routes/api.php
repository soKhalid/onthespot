<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Categories
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{slug}', [CategoryController::class, 'show']);

// Brands - Public
Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{slug}', [BrandController::class, 'show']);

// Reviews - Public
Route::get('/brands/{brand}/reviews', [ReviewController::class, 'index']);

// Inventory - Public
Route::get('/brands/{brand}/inventory', [InventoryController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // My Brands (for business accounts)
    Route::get('/my-brands', [BrandController::class, 'myBrands']);
    Route::post('/brands', [BrandController::class, 'store']);
    Route::put('/brands/{brand}', [BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);

    // Inventory Management (for business accounts)
    Route::post('/brands/{brand}/inventory', [InventoryController::class, 'store']);
    Route::put('/brands/{brand}/inventory/{item}', [InventoryController::class, 'update']);
    Route::delete('/brands/{brand}/inventory/{item}', [InventoryController::class, 'destroy']);

    // Reviews
    Route::post('/brands/{brand}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/brands/{brand}/favorite', [FavoriteController::class, 'toggle']);
});
