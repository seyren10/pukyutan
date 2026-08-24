<?php

use App\Http\Controllers\V1\DeleteUserController;
use App\Http\Controllers\V1\UpdateProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put("/user", UpdateProfileController::class);
    Route::delete('/user', DeleteUserController::class)->middleware(['throttle:4,1']);
});
