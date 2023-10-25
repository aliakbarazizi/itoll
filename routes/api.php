<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderLocationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::middleware('role:user')->group(function () {
        Route::resource('orders', OrderController::class)->only([
            'index', 'show', 'store'
        ])->where(['order' => '[0-9]+']);

        Route::put('orders/{order}/cancel', [OrderController::class, 'cancel']);
    });

    // Driver routes
    Route::middleware('role:driver')->group(function () {
        Route::get('orders/driver', [OrderController::class, 'driver']);
        Route::get('orders/pending', [OrderController::class, 'pending']);

        Route::put('orders/{order}/accept', [OrderController::class, 'accept']);

        Route::put('orders/{order}/complete', [OrderController::class, 'complete']);

        Route::resource('orders.locations', OrderLocationController::class)->only([
            'store'
        ]);
    });
});
