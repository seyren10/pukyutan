<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $member->name }} — {{ $group->name }} Ledger</title>

    <style>
        /*
         * Same palette as resources/css/style.css's :root theme, inlined as
         * plain values (no @theme/@import build step for a single rendered
         * document) so this PDF stays visually identical to the app itself.
         */
        :root {
            --background: oklch(0.97 0.02 85);
            --foreground: oklch(0.28 0.035 55);
            --card: oklch(0.99 0.012 85);
            --primary: oklch(0.72 0.16 70);
            --primary-foreground: oklch(0.22 0.03 55);
            --secondary: oklch(0.93 0.045 80);
            --muted-foreground: oklch(0.5 0.025 60);
            --accent: oklch(89.887% 0.05009 71.971);
            --accent-foreground: oklch(0.4 0.12 55);
            --destructive: oklch(0.55 0.16 35);
            --success: oklch(0.58 0.08 135);
            --border: oklch(0.87 0.02 80);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--background);
            color: var(--foreground);
            font-family: 'Public Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif;
            font-size: 13px;
            line-height: 1.5;
            padding: 40px 44px;
        }

        h1,
        h2,
        h3 {
            font-family: 'Fraunces', Georgia, 'Times New Roman', serif;
            margin: 0;
        }

        .num {
            font-family: 'IBM Plex Mono', ui-monospace, monospace;
            font-variant-numeric: tabular-nums;
        }

        .muted {
            color: var(--muted-foreground);
        }

        /* Brand header */
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 28px;
        }

        .brand img {
            width: 30px;
            height: 30px;
            border-radius: 8px;
        }

        .brand .name {
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: 17px;
        }

        .brand .doc-type {
            margin-left: auto;
            text-align: right;
            font-size: 11px;
        }

        /* Group + member summary card */
        .summary-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 22px 24px;
            margin-bottom: 22px;
        }

        .group-name {
            font-size: 19px;
            font-weight: 600;
        }

        .group-meta {
            margin-top: 4px;
            font-size: 12px;
        }

        .divider {
            border-top: 1px solid var(--border);
            margin: 16px 0;
        }

        .member-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 36px;
            height: 36px;
            border-radius: 999px;
            background: var(--secondary);
            color: var(--accent-foreground);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Fraunces', Georgia, serif;
            font-weight: 600;
            font-size: 14px;
            flex-shrink: 0;
        }

        .member-name {
            font-weight: 600;
            font-size: 14px;
        }

        .member-sub {
            font-size: 11.5px;
        }

        .status-pill {
            margin-left: auto;
            text-align: right;
        }

        .status-pill .label {
            font-size: 11px;
            font-weight: 600;
        }

        .status-pill .amount {
            font-size: 15px;
            font-weight: 600;
        }

        .totals-row {
            display: flex;
            gap: 28px;
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px dashed var(--border);
        }

        .totals-row .stat .label {
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted-foreground);
        }

        .totals-row .stat .value {
            font-size: 15px;
            font-weight: 600;
            margin-top: 2px;
        }

        /* Cycle breakdown */
        .cycles-heading {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--muted-foreground);
            margin: 0 0 10px 2px;
        }

        .cycle {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 15px;
            padding: 16px 20px;
            margin-bottom: 12px;
            break-inside: avoid;
        }

        .cycle-head {
            display: flex;
            align-items: flex-start;
        }

        .cycle-title {
            font-weight: 600;
            font-size: 13.5px;
        }

        .cycle-sub {
            font-size: 11.5px;
            margin-top: 2px;
        }

        .cycle-amount {
            margin-left: auto;
            text-align: right;
        }

        .cycle-amount .paid {
            font-size: 13.5px;
            font-weight: 600;
        }

        .cycle-amount .status {
            font-size: 11px;
            font-weight: 600;
            margin-top: 2px;
        }

        .status-settled {
            color: var(--success);
        }

        .status-partial {
            color: var(--accent-foreground);
        }

        .status-unpaid {
            color: var(--destructive);
        }

        .contributions {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed var(--border);
        }

        .contribution-row {
            display: flex;
            font-size: 12px;
            padding: 3px 0;
        }

        .contribution-row .amount {
            margin-left: auto;
            font-weight: 500;
        }

        .no-payments {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px dashed var(--border);
            font-size: 12px;
            color: var(--muted-foreground);
            font-style: italic;
        }

        .empty-state {
            text-align: center;
            color: var(--muted-foreground);
            padding: 40px 0;
            font-size: 13px;
        }

        footer {
            margin-top: 24px;
            text-align: center;
            font-size: 10.5px;
            color: var(--muted-foreground);
        }

        footer > a {
            color: currentColor;
        }
    </style>
</head>

<body>
    {{-- Brand header --}}
    <div class="brand">
        <img src="{{ $logoDataUri }}" alt="Puyo">
        <span class="name">{{ config('app.name') }}</span>
        <div class="doc-type muted">
            Member Ledger<br>
            Generated {{ $generatedAt->format('M d, Y') }}
        </div>
    </div>

    {{-- Group + member summary --}}
    <div class="summary-card">
        <div class="group-name">{{ $group->name }}</div>
        <div class="group-meta muted">
            {{ $contributionLabel }} &middot; {{ $frequencyLabel }} &middot; Started {{ $group->start_date->format('M d, Y') }}
        </div>

        <div class="divider"></div>

        <div class="member-row">
            <div class="avatar">{{ $memberInitials }}</div>
            <div>
                <div class="member-name">{{ $member->name }}</div>
                <div class="member-sub muted">
                    {{ $member->email ?? 'No email on file' }} &middot; Payout position #{{ $member->payout_order }}
                </div>
            </div>
            <div class="status-pill">
                <div class="label {{ $balanceStatusClass }}">{{ $balanceStatusLabel }}</div>
                @if ($balance['balance'] != 0)
                    <div class="amount">{{ $balanceAmountLabel }}</div>
                @endif
            </div>
        </div>

        <div class="totals-row">
            <div class="stat">
                <div class="label">Expected</div>
                <div class="value num">₱{{ number_format($balance['expected_total'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="label">Paid</div>
                <div class="value num">₱{{ number_format($balance['paid_total'], 2) }}</div>
            </div>
            <div class="stat">
                <div class="label">Balance</div>
                <div class="value num {{ $balanceStatusClass }}">
                    {{ $balance['balance'] > 0 ? '-' : '' }}₱{{ number_format(abs($balance['balance']), 2) }}
                </div>
            </div>
        </div>
    </div>

    {{-- Per-cycle breakdown --}}
    @if ($ledger->isEmpty())
        <div class="empty-state">No cycles are due yet for this member.</div>
    @else
        <div class="cycles-heading">Cycle-by-cycle contributions</div>

        @foreach ($ledger as $cycle)
            @php
                $expected = $cycle['expected'];
                $paid = $cycle['paid'];
                $statusClass = $paid <= 0 ? 'status-unpaid' : ($paid < $expected ? 'status-partial' : 'status-settled');
                $statusLabel = $paid <= 0 ? 'Unpaid' : ($paid < $expected ? 'Partial' : 'Settled');
            @endphp

            <div class="cycle">
                <div class="cycle-head">
                    <div>
                        <div class="cycle-title">Cycle {{ $cycle['cycle_number'] }}</div>
                        <div class="cycle-sub muted">
                            Round {{ $cycle['round_number'] }} &middot; Due
                            {{ \Illuminate\Support\Carbon::parse($cycle['due_date'])->format('M d, Y') }}
                        </div>
                    </div>
                    <div class="cycle-amount">
                        <div class="paid num">₱{{ number_format($paid, 2) }} / ₱{{ number_format($expected, 2) }}</div>
                        <div class="status {{ $statusClass }}">{{ $statusLabel }}</div>
                    </div>
                </div>

                @if ($cycle['contributions']->isNotEmpty())
                    <div class="contributions">
                        @foreach ($cycle['contributions'] as $contribution)
                            <div class="contribution-row">
                                <span class="muted">
                                    {{ \Illuminate\Support\Carbon::parse($contribution['paid_at'])->format('M d, Y') }}
                                    @if ($contribution['notes'])
                                        — {{ $contribution['notes'] }}
                                    @endif
                                </span>
                                <span class="amount num">₱{{ number_format($contribution['amount'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="no-payments">No payments recorded for this cycle.</div>
                @endif
            </div>
        @endforeach
    @endif

    <footer>
        Generated via {{ config('app.name') }} on {{ $generatedAt->format('M d, Y \a\t g:i A') }} &middot; <a href="{{ config('app.frontend_url') }}">{{ config('app.name') }} App</a>
    </footer>
</body>

</html>