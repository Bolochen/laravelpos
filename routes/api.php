<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RestockController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);

Route::get('/stocks', [StockController::class, 'index']);
Route::post('/restocks', [RestockController::class, 'store']);

Route::get('/cart', [CartController::class, 'show']);
Route::post('/cart/items', [CartController::class, 'addItem']);
Route::patch('/cart/items/{item}', [CartController::class, 'updateItem']);
Route::delete('/cart/items/{item}', [CartController::class, 'removeItem']);
Route::post('/cart/checkout', [CartController::class, 'checkout']);
