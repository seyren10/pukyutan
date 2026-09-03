<?php

namespace App\Http\Controllers\V1;

use App\Enums\PdfExportStatus;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberLedgerPdfExport;
use App\Services\MemberLedgerPdfService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberLedgerPdfDownloadController extends Controller
{
    public function __invoke(
        Member $member,
        MemberLedgerPdfExport $export,
        MemberLedgerPdfService $pdfService,
    ): StreamedResponse {

        Gate::authorize('view', $member->group);
        abort_unless(
            $export->member_id === $member->id && $export->status === PdfExportStatus::Completed,
            404,
        );

        return Storage::disk('local')->download($export->file_path, $pdfService->filenameFor($member));
    }
}