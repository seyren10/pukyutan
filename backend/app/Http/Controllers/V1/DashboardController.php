<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardStatsService $stats) {}

    public function __invoke(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->stats->forUser($request->user()),
        ]);
    }
}