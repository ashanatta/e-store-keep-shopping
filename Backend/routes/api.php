<?php


use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\SizeController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\AdminCommerceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/user', [UserController::class, 'me'])->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/logout-all', [AuthController::class, 'logoutAll'])->middleware('auth:sanctum');

// Public catalog endpoints
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::get('/products/{product}/reviews', [ReviewController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/colors', [ColorController::class, 'index']);
Route::get('/sizes', [SizeController::class, 'index']);

Route::get('/files/{path}', function (string $path) {
    if (! Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $file = Storage::disk('public')->path($path);
    $mimeType = mime_content_type($file);

    return response()->file($file, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*')->name('files.serve');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/products/{product}/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);
    Route::delete('/wishlist/{product}', [WishlistController::class, 'destroy']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::patch('/cart/{cartItem}', [CartController::class, 'update']);
    Route::delete('/cart/{cartItem}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('colors', ColorController::class);
    Route::apiResource('sizes', SizeController::class);
    Route::get('/admin/reviews', [AdminCommerceController::class, 'reviews']);
    Route::get('/admin/wishlists', [AdminCommerceController::class, 'wishlists']);
    Route::get('/admin/carts', [AdminCommerceController::class, 'carts']);
    Route::get('/admin/orders', [AdminCommerceController::class, 'orders']);
    Route::patch('/admin/orders/{order}/status', [AdminCommerceController::class, 'updateOrderStatus']);
});
