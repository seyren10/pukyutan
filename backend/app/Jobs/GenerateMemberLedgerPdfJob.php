<?php

namespace App\Jobs;

use App\Enums\PdfExportStatus;
use App\Models\MemberLedgerPdfExport;
use App\Services\LedgerCalculatorService;
use App\Services\MemberLedgerPdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

#[Tries(2)]
#[Backoff([10, 30])]
#[Timeout(90)]
class GenerateMemberLedgerPdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public MemberLedgerPdfExport $export)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        LedgerCalculatorService $ledgerCalculatorService,
        MemberLedgerPdfService $pdfService,
    ): void {
        $this->export->update(['status' => PdfExportStatus::Processing]);

        $member = $this->export->member;

        $pdf = $pdfService->generate(
            $member,
            $ledgerCalculatorService->ledgerForMember($member),
            $ledgerCalculatorService->balanceForMember($member),
        );

        $path = "ledger-pdfs/{$this->export->id}.pdf";

        Storage::disk('local')->put($path, $pdf);

        $this->export->update([
            'status' => PdfExportStatus::Completed,
            'file_path' => $path,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        $this->export->update([
            'status' => PdfExportStatus::Failed,
            'error_message' => $exception?->getMessage(),
        ]);

        Log::error('Member ledger PDF generation failed', [
            'export_id' => $this->export->id,
            'member_id' => $this->export->member_id,
            'error' => $exception?->getMessage(),
        ]);
    }
}
