<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;

// User information (protected route)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// User Authentication
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Products (protected routes)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/postProducts', [ProductController::class, 'create']);
    Route::get('/getProducts', [ProductController::class, 'index']);
});
