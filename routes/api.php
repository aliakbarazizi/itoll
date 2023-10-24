<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderLocationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::middleware('role:user')->group(function () {
        Route::resource('orders', OrderController::class)->only([
            'index', 'show', 'store'
        ]);

        Route::put('orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    // Driver routes
    Route::middleware('role:driver')->group(function () {
        Route::put('orders/{order}/accept', [OrderController::class, 'accept']);

        Route::put('orders/{order}/complete', [OrderController::class, 'complete']);

        Route::resource('orders.locations', OrderLocationController::class)->only([
            'store'
        ]);
    });
});
