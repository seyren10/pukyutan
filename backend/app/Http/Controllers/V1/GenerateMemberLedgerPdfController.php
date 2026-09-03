<?php

namespace App\Http\Controllers\V1;

use App\Enums\PdfExportStatus;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateMemberLedgerPdfJob;
use App\Models\Member;
use App\Models\MemberLedgerPdfExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class GenerateMemberLedgerPdfController extends Controller
{
    public function __invoke(Member $member): JsonResponse
    {
        Gate::authorize('view', $member->group);

        $export = MemberLedgerPdfExport::create([
            'member_id' => $member->id,
            'status' => PdfExportStatus::Pending,
        ]);

        GenerateMemberLedgerPdfJob::dispatch($export);

        return response()->json([
            'export_id' => $export->id,
            'status' => $export->status,
        ], 202);
    }
}