<?php

use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RestockController;
use App\Http\Controllers\Api\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('products', ProductController::class);

Route::get('/stocks', [StockController::class, 'index']);
Route::post('/restocks', [RestockController::class, 'store']);
