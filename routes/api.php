<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// Public content routes
Route::get('products', [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);
Route::get('collections', [CollectionController::class, 'index']);
Route::get('collections/{collection}', [CollectionController::class, 'show']);
Route::get('banners', [BannerController::class, 'index']);
Route::get('about', [AboutController::class, 'show']);
Route::post('messages', [MessageController::class, 'store']); // Public contact form

// Protected routes (authentication required)
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::put('profile', [AuthController::class, 'updateProfile']);
    });

    // Customer orders (customers can view their own orders)
    Route::get('my-orders', [OrderController::class, 'myOrders']);
    Route::post('orders', [OrderController::class, 'store']); // Customers can create orders

    // Admin/Editor only routes
    Route::middleware('role:admin,editor')->group(function () {
        // Dashboard routes
        Route::prefix('dashboard')->group(function () {
            Route::get('summary', [DashboardController::class, 'summary']);
            Route::get('top-products', [DashboardController::class, 'topProducts']);
            Route::get('pending-orders', [DashboardController::class, 'pendingOrders']);
        });

        // Product management routes
        Route::prefix('products')->group(function () {
            Route::post('/', [ProductController::class, 'store']);
            Route::put('{product}', [ProductController::class, 'update']);
            Route::delete('{product}', [ProductController::class, 'destroy']);
            Route::post('{product}/restore', [ProductController::class, 'restore']);
            
            // Product images
            Route::post('{product}/images', [ProductImageController::class, 'upload']);
            Route::delete('{product}/images/{image}', [ProductImageController::class, 'destroy']);
        });

        // Collection management routes
        Route::prefix('collections')->group(function () {
            Route::post('/', [CollectionController::class, 'store']);
            Route::put('{collection}', [CollectionController::class, 'update']);
            Route::delete('{collection}', [CollectionController::class, 'destroy']);
            
            // Collection products
            Route::post('{collection}/products', [CollectionController::class, 'assignProducts']);
            Route::delete('{collection}/products/{product}', [CollectionController::class, 'removeProduct']);
        });

        // Order management routes (admin/editor can view all orders)
        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);
        Route::put('orders/{order}', [OrderController::class, 'update']);
        Route::delete('orders/{order}', [OrderController::class, 'destroy']);

        // Banner management routes
        Route::apiResource('banners', BannerController::class)->except(['index', 'show']);

        // About content management
        Route::put('about', [AboutController::class, 'update']);

        // Message management routes
        Route::prefix('messages')->group(function () {
            Route::get('/', [MessageController::class, 'index']);
            Route::get('{message}', [MessageController::class, 'show']);
            Route::put('{message}', [MessageController::class, 'update']);
            Route::delete('{message}', [MessageController::class, 'destroy']);
        });

        // File upload routes
        Route::post('upload', [UploadController::class, 'upload']);
        Route::delete('upload/{filename}', [UploadController::class, 'destroy']);
    });
});