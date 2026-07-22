<?php

use App\Http\Controllers\V1\GroupActivateController;
use App\Http\Controllers\V1\GroupController;
use App\Http\Controllers\V1\MemberController;
use Illuminate\Support\Facades\Route;

Route::apiResource("groups", GroupController::class);

Route::apiResource("groups.members", MemberController::class)->shallow();
Route::post("groups/{group}/activate", GroupActivateController::class);