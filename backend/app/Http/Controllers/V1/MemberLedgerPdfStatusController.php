<?php

namespace App\Http\Controllers\V1;

use App\Enums\PdfExportStatus;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberLedgerPdfExport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class MemberLedgerPdfStatusController extends Controller
{
    public function __invoke(Member $member, MemberLedgerPdfExport $export): JsonResponse
    {
        Gate::authorize('view', $member->group);
        abort_unless($export->member_id === $member->id, 404);

        return response()->json([
            'status' => $export->status,
            'download_url' => $export->status === PdfExportStatus::Completed
                ? route('v1.members.ledger-pdf.download', [$member, $export])
                : null,
            'error' => $export->status === PdfExportStatus::Failed ? $export->error_message : null,
        ]);
    }
}