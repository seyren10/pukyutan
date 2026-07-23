<?php

use App\Http\Controllers\V1\GroupActivateController;
use App\Http\Controllers\V1\GroupController;
use App\Http\Controllers\V1\MemberController;
use App\Http\Controllers\V1\MemberLedgerController;
use Illuminate\Support\Facades\Route;

Route::apiResource("groups", GroupController::class);
Route::post("groups/{group}/activate", GroupActivateController::class);

Route::apiResource("groups.members", MemberController::class)->shallow();
Route::get("members/{member}/ledger", MemberLedgerController::class);