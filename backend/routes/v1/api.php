<?php

use App\Http\Controllers\V1\ContributionController;
use App\Http\Controllers\V1\CycleDisburseController;
use App\Http\Controllers\V1\GroupActivateController;
use App\Http\Controllers\V1\GroupActivityController;
use App\Http\Controllers\V1\GroupCompleteController;
use App\Http\Controllers\V1\GroupController;
use App\Http\Controllers\V1\GroupJoinController;
use App\Http\Controllers\V1\GroupRoundController;
use App\Http\Controllers\V1\GroupShareController;
use App\Http\Controllers\V1\MemberController;
use App\Http\Controllers\V1\MemberLedgerController;
use App\Http\Controllers\V1\MemberReorderController;
use App\Http\Controllers\V1\UserActivityController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('activities', UserActivityController::class)->withoutMiddleware('verified');
    Route::get('groups/shared', [GroupController::class, 'shared'])->withoutMiddleware('verified');
    Route::apiResource("groups", GroupController::class)->withoutMiddlewareFor(['index', 'show'], 'verified');
    Route::post("groups/{group}/activate", GroupActivateController::class);
    Route::post("groups/{group}/rounds", [GroupRoundController::class, 'newRound']);
    Route::get("groups/{group}/rounds/{roundNumber}/summary", [GroupRoundController::class, 'summary'])->withoutMiddleware('verified');
    Route::post("groups/{group}/complete", GroupCompleteController::class);
    Route::get("groups/{group}/activities", GroupActivityController::class)->withoutMiddleware('verified');
    Route::put('groups/{group}/members/reorder', MemberReorderController::class);

    Route::post("groups/join/{invite_code}", GroupJoinController::class)
        ->whereAlphaNumeric("invite_code")
        ->withoutMiddleware('verified');

    Route::get("groups/{group}/share-requests", [GroupShareController::class, "index"]);
    Route::post("share-requests/{share_request}/accept", [GroupShareController::class, "accept"]);
    Route::post("share-requests/{share_request}/reject", [GroupShareController::class, "reject"]);

    Route::apiResource("groups.members", MemberController::class)->shallow()->withoutMiddlewareFor(['index', 'show'], 'verified');
    Route::get("members/{member}/ledger", MemberLedgerController::class)->withoutMiddleware('verified');

    Route::post("cycles/{cycle}/disburse", CycleDisburseController::class);
    Route::apiResource('cycles.contributions', ContributionController::class)->shallow()->only(['index', 'store'])->withoutMiddlewareFor(['index'], 'verified');

});