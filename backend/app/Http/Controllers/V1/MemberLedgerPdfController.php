<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\LedgerCalculatorService;
use App\Services\MemberLedgerPdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class MemberLedgerPdfController extends Controller
{
    /**
     * Stream the given member's contribution ledger as a downloadable PDF.
     */
    public function __invoke(
        Member $member,
        LedgerCalculatorService $ledgerCalculatorService,
        MemberLedgerPdfService $pdfService,
    ): Response {
        Gate::authorize('view', $member->group);

        $pdf = $pdfService->generate(
            $member,
            $ledgerCalculatorService->ledgerForMember($member),
            $ledgerCalculatorService->balanceForMember($member),
        );

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $pdfService->filenameFor($member) . '"',
        ]);
    }
}