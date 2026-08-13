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
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('groups/shared', [GroupController::class, 'shared']);
    Route::apiResource("groups", GroupController::class);
    Route::post("groups/{group}/activate", GroupActivateController::class);
    Route::post("groups/{group}/rounds", [GroupRoundController::class, 'newRound']);
    Route::get("groups/{group}/rounds/{roundNumber}/summary", [GroupRoundController::class, 'summary']);
    Route::post("groups/{group}/complete", GroupCompleteController::class);
    Route::get("groups/{group}/activities", GroupActivityController::class);
    Route::put('groups/{group}/members/reorder', MemberReorderController::class);

    Route::post("groups/join/{invite_code}", GroupJoinController::class)
        ->whereAlphaNumeric("invite_code");

    Route::get("groups/{group}/share-requests", [GroupShareController::class, "index"]);
    Route::post("share-requests/{share_request}/accept", [GroupShareController::class, "accept"]);
    Route::post("share-requests/{share_request}/reject", [GroupShareController::class, "reject"]);

    Route::apiResource("groups.members", MemberController::class)->shallow();
    Route::get("members/{member}/ledger", MemberLedgerController::class);

    Route::post("cycles/{cycle}/disburse", CycleDisburseController::class);
    Route::apiResource('cycles.contributions', ContributionController::class)->shallow()->only(['index', 'store']);

});
