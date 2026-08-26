<?php

namespace App\Services;

use App\Enums\FrequencyUnitType;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

class MemberLedgerPdfService
{
    /**
     * Render a member's ledger — group info, running totals, and the
     * cycle-by-cycle breakdown — to PDF bytes via a headless Chrome
     * instance. Reuses the same data LedgerCalculatorService already
     * produces for the JSON endpoints, so the PDF can never drift out of
     * sync with what the app itself shows.
     */
    public function generate(Member $member, Collection $ledger, array $balance): string
    {
        $html = view('pdfs.member-ledger', [
            'group' => $member->group,
            'member' => $member,
            'ledger' => $ledger,
            'balance' => $balance,
            'logoDataUri' => $this->logoDataUri(),
            'generatedAt' => now(),
            'memberInitials' => $this->initialsFor($member->name),
            'contributionLabel' => '₱' . number_format((float) $member->group->contribution_amount, 2),
            'frequencyLabel' => $this->frequencyLabel($member->group->frequency_unit, $member->group->frequency_interval),
            'balanceStatusLabel' => $this->balanceStatusLabel($balance['balance']),
            'balanceStatusClass' => $this->balanceStatusClass($balance['balance']),
            'balanceAmountLabel' => $this->balanceAmountLabel($balance['balance']),
        ])->render();

        return Browsershot::html($html)
            ->setChromePath(config('services.browsershot.chrome_path'))
            ->noSandbox()
            ->showBackground()
            ->format('A4')
            // ->margins(15, 15, 15, 15)
            ->timeout(60)
        ->pdf();
    }

    public function filenameFor(Member $member): string
    {
        return Str::slug($member->group->name . '-' . $member->name . '-ledger') . '.pdf';
    }

    /**
     * Embedded as a data URI rather than linked by URL — the HTML is
     * rendered from a temp file with no guarantee Chromium can reach the
     * app's own web server, so this is the only reliable way to make the
     * logo render every time.
     */
    private function logoDataUri(): string
    {
        $path = public_path('puyo.png');

        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }

    private function initialsFor(string $name): string
    {
        $words = collect(explode(' ', trim($name)))->filter();

        return $words->take(2)->map(fn(string $word) => Str::upper(Str::substr($word, 0, 1)))->implode('');
    }

    private function frequencyLabel(FrequencyUnitType $unit, int $interval): string
    {
        if ($interval === 1) {
            return match ($unit) {
                FrequencyUnitType::DAY => 'Daily',
                FrequencyUnitType::WEEK => 'Weekly',
                FrequencyUnitType::MONTH => 'Monthly',
            };
        }

        return "Every {$interval} {$unit->value}s";
    }

    private function balanceStatusLabel(float $balance): string
    {
        return match (true) {
            $balance > 0.005 => 'Owes',
            $balance < -0.005 => 'Credit',
            default => 'All caught up',
        };
    }

    private function balanceStatusClass(float $balance): string
    {
        return match (true) {
            $balance > 0.005 => 'status-unpaid',
            default => 'status-settled',
        };
    }

    private function balanceAmountLabel(float $balance): string
    {
        return '₱' . number_format(abs($balance), 2);
    }
}