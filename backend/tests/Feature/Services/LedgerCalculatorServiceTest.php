<?php

use App\Models\Contribution;
use App\Models\Cycle;
use App\Models\Group;
use App\Models\Member;
use App\Services\LedgerCalculatorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

beforeEach(function () {
    // Pin "now" so due_date comparisons in the ledger logic are
    // predictable across every test, instead of depending on
    // whatever day you actually run the suite.
    Carbon::setTestNow('2026-06-15');
});

afterEach(function () {
    Carbon::setTestNow(); // always reset, so other tests aren't affected
});

/**
 * Helper: build a group with one member and a set of cycles at given
 * due dates, all recipient-assigned to that same member for simplicity
 * (recipient assignment doesn't matter for ledger math — only which
 * cycles exist and what's been paid does).
 */
function makeMemberWithCycles(array $dueDates, ?string $memberCreatedAt = null): Member
{
    $group = Group::factory()->create([
        'contribution_amount' => 500,
    ]);

    $member = Member::factory()->create([
        'group_id' => $group->id,
        'payout_order' => 1,
        'created_at' => $memberCreatedAt ?? now(),
    ]);

    foreach ($dueDates as $i => $dueDate) {
        Cycle::factory()->create([
            'group_id' => $group->id,
            'round_number' => 1,
            'cycle_number' => $i + 1,
            'due_date' => $dueDate,
            'recipient_member_id' => $member->id,
            'created_at' => $memberCreatedAt ?? now(),
        ]);
    }

    return $member->fresh();
}

it('shows zero balance when a member has paid exactly what is expected', function () {
    $member = makeMemberWithCycles([
        '2026-05-01', // past due
        '2026-06-01', // past due
    ]);

    foreach ($member->group->cycles as $cycle) {
        Contribution::factory()->create([
            'cycle_id' => $cycle->id,
            'member_id' => $member->id,
            'amount' => 500,
        ]);
    }

    $result = app(LedgerCalculatorService::class)->balanceForMember($member);

    expect($result['expected_total'])->toBe(1000.0)
        ->and($result['paid_total'])->toBe(1000.0)
        ->and($result['balance'])->toBe(0.0);
});

it('carries an underpayment forward as a positive balance owed', function () {
    $member = makeMemberWithCycles([
        '2026-05-01',
        '2026-06-01',
    ]);

    $cycles = $member->group->cycles;

    // Paid in full for cycle 1, but only partially for cycle 2.
    Contribution::factory()->create([
        'cycle_id' => $cycles[0]->id,
        'member_id' => $member->id,
        'amount' => 500,
    ]);
    Contribution::factory()->create([
        'cycle_id' => $cycles[1]->id,
        'member_id' => $member->id,
        'amount' => 300,
    ]);

    $result = app(LedgerCalculatorService::class)->balanceForMember($member);

    // Expected 1000 total, paid 800 total -> owes 200 more.
    expect($result['balance'])->toBe(200.0);
});

it('carries an overpayment forward as a negative balance (credit)', function () {
    $member = makeMemberWithCycles([
        '2026-05-01',
        '2026-06-01',
    ]);

    $cycles = $member->group->cycles;

    // Overpaid on cycle 1, nothing yet on cycle 2.
    Contribution::factory()->create([
        'cycle_id' => $cycles[0]->id,
        'member_id' => $member->id,
        'amount' => 1000,
    ]);

    $result = app(LedgerCalculatorService::class)->balanceForMember($member);

    // Expected 1000 total, paid 1000 total -> balance is 0, even though
    // cycle 2 individually looks unpaid. This is the rollover behavior:
    // the overpayment on cycle 1 already covers cycle 2.
    expect($result['balance'])->toBe(0.0);
});

it('does not count cycles whose due date has not arrived yet', function () {
    $member = makeMemberWithCycles([
        '2026-05-01', // past due — should count
        '2026-12-01', // future — should NOT count yet
    ]);

    Contribution::factory()->create([
        'cycle_id' => $member->group->cycles->first()->id,
        'member_id' => $member->id,
        'amount' => 500,
    ]);

    $result = app(LedgerCalculatorService::class)->balanceForMember($member);

    // Only the past-due cycle counts toward expected_total, so a member
    // who's paid that one in full should show a zero balance — not a
    // balance implying they owe for the future cycle too.
    expect($result['expected_total'])->toBe(500.0)
        ->and($result['balance'])->toBe(0.0);
});

it('counts a cycle due exactly today', function () {
    $member = makeMemberWithCycles([
        '2026-06-15', // matches Carbon::setTestNow() above, exactly "today"
    ]);

    $result = app(LedgerCalculatorService::class)->balanceForMember($member);

    expect($result['expected_total'])->toBe(500.0);
});

it('does not charge a replaced-in member for cycles that existed before they joined', function () {
    // Simulate a member who joined (was created) partway through —
    // e.g. as a replacement for someone else. Cycles that existed
    // before this member's created_at should not count against them.
    $member = makeMemberWithCycles(
        dueDates: ['2026-04-01', '2026-05-01', '2026-06-01'],
        memberCreatedAt: '2026-05-15' // joined after cycle 1, before cycles 2-3
    );

    // Note: makeMemberWithCycles backdates cycle created_at to match
    // the member for simplicity above, so this test overrides that by
    // rebuilding cycles with mixed created_at values directly.
    $member->group->cycles()->delete();

    Cycle::factory()->create([
        'group_id' => $member->group_id,
        'cycle_number' => 1,
        'due_date' => '2026-04-01',
        'recipient_member_id' => $member->id,
        'created_at' => '2026-04-01', // existed before member joined
    ]);
    Cycle::factory()->create([
        'group_id' => $member->group_id,
        'cycle_number' => 2,
        'due_date' => '2026-05-01',
        'recipient_member_id' => $member->id,
        'created_at' => '2026-05-20', // created after member joined
    ]);

    $result = app(LedgerCalculatorService::class)->balanceForMember($member);

    // Only the cycle created after this member's tenure began should count.
    expect($result['expected_total'])->toBe(500.0);
});

it('computes the collection summary for a cycle across all members', function () {
    $group = Group::factory()->create(['contribution_amount' => 500]);

    $members = Member::factory()->count(3)->create(['group_id' => $group->id]);

    $cycle = Cycle::factory()->create([
        'group_id' => $group->id,
        'recipient_member_id' => $members->first()->id,
        'due_date' => '2026-06-01',
    ]);

    Contribution::factory()->create([
        'cycle_id' => $cycle->id,
        'member_id' => $members[0]->id,
        'amount' => 500,
    ]);
    Contribution::factory()->create([
        'cycle_id' => $cycle->id,
        'member_id' => $members[1]->id,
        'amount' => 300,
    ]);
    // members[2] hasn't paid at all

    $result = app(LedgerCalculatorService::class)->collectionSummaryForCycle($cycle);

    expect($result['expected_total'])->toBe(1500.0) // 500 x 3 members
        ->and($result['collected_total'])->toBe(800.0);
});

it('rolls an undisbursed surplus forward as reserve for the next cycle', function () {
    // The exact scenario from the conversation: contribution = 100, 3
    // members. Ana pays 100, Roy pays 200 (overpays by 100), Corine pays
    // 100. Total collected = 400, but only 300 (the expected total) is
    // disbursed for cycle 1 — the extra 100 stays with the group.
    $group = Group::factory()->create(['contribution_amount' => 100]);
    $ana = Member::factory()->create(['group_id' => $group->id, 'payout_order' => 1]);
    $roy = Member::factory()->create(['group_id' => $group->id, 'payout_order' => 2]);
    $corine = Member::factory()->create(['group_id' => $group->id, 'payout_order' => 3]);

    $cycle1 = Cycle::factory()->create([
        'group_id' => $group->id,
        'cycle_number' => 1,
        'recipient_member_id' => $ana->id,
        'due_date' => '2026-05-01',
    ]);

    Contribution::factory()->create(['cycle_id' => $cycle1->id, 'member_id' => $ana->id, 'amount' => 100]);
    Contribution::factory()->create(['cycle_id' => $cycle1->id, 'member_id' => $roy->id, 'amount' => 200]);
    Contribution::factory()->create(['cycle_id' => $cycle1->id, 'member_id' => $corine->id, 'amount' => 100]);

    $service = app(LedgerCalculatorService::class);

    // Before disbursing: 400 collected, nothing disbursed yet -> reserve = 400.
    // Recommended disbursement is capped at what cycle 1 actually needs (300).
    $summaryBeforeDisbursement = $service->collectionSummaryForCycle($cycle1);
    expect($summaryBeforeDisbursement['reserve_balance'])->toBe(400.0)
        ->and($summaryBeforeDisbursement['recommended_disbursement'])->toBe(300.0);

    // Disburse only the recommended 300, holding the 100 surplus back.
    $cycle1->update(['disbursed_amount' => 300, 'disbursed_at' => now()]);

    // Treasury balance now reflects the held-back surplus.
    expect($service->treasuryBalanceForGroup($group->fresh()))->toBe(100.0);

    // Cycle 2 opens. Roy already overpaid in cycle 1, so he pays nothing
    // this cycle. Ana and Corine each pay their normal 100.
    $cycle2 = Cycle::factory()->create([
        'group_id' => $group->id,
        'cycle_number' => 2,
        'recipient_member_id' => $roy->id,
        'due_date' => '2026-06-01',
    ]);

    Contribution::factory()->create(['cycle_id' => $cycle2->id, 'member_id' => $ana->id, 'amount' => 100]);
    Contribution::factory()->create(['cycle_id' => $cycle2->id, 'member_id' => $corine->id, 'amount' => 100]);
    // Roy pays nothing — his cycle 1 overpayment should cover him.

    $summaryCycle2 = $service->collectionSummaryForCycle($cycle2);

    // Literal cash collected *for cycle 2* is only 200 (Roy paid nothing
    // this cycle) — but the reserve (100 held back + 200 new) covers the
    // full 300 the recipient is owed.
    expect($summaryCycle2['collected_total'])->toBe(200.0)
        ->and($summaryCycle2['reserve_balance'])->toBe(300.0)
        ->and($summaryCycle2['recommended_disbursement'])->toBe(300.0);
});

it('never recommends disbursing more than the group actually has on hand', function () {
    // Only 2 of 3 members have paid anything at all — reserve is short
    // of what's expected, so the recommendation should reflect what's
    // actually available, not the full expected total.
    $group = Group::factory()->create(['contribution_amount' => 100]);
    $members = Member::factory()->count(3)->create(['group_id' => $group->id]);

    $cycle = Cycle::factory()->create([
        'group_id' => $group->id,
        'recipient_member_id' => $members->first()->id,
        'due_date' => '2026-06-01',
    ]);

    Contribution::factory()->create(['cycle_id' => $cycle->id, 'member_id' => $members[0]->id, 'amount' => 100]);
    Contribution::factory()->create(['cycle_id' => $cycle->id, 'member_id' => $members[1]->id, 'amount' => 50]);

    $result = app(LedgerCalculatorService::class)->collectionSummaryForCycle($cycle);

    expect($result['expected_total'])->toBe(300.0)
        ->and($result['reserve_balance'])->toBe(150.0)
        ->and($result['recommended_disbursement'])->toBe(150.0); // capped by reserve, not expected
});