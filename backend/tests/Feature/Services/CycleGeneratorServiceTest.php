<?php

use App\Enums\FrequencyUnitType;
use App\Models\Group;
use App\Models\Member;
use App\Models\Cycle;
use App\Services\CycleGeneratorService;

// RefreshDatabase resets the DB between each test, so tests never
// interfere with each other's data. Make sure your TestCase uses it
// (usually already set up by default in tests/Pest.php or TestCase.php).
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Small helper so we're not repeating "create a group with N members"
 * setup in every single test below.
 */
function makeGroupWithMembers(array $groupAttributes, int $memberCount): Group
{
    $group = Group::factory()->create($groupAttributes);

    foreach (range(1, $memberCount) as $order) {
        Member::factory()->create([
            'group_id' => $group->id,
            'payout_order' => $order,
        ]);
    }

    return $group->fresh();
}

it('generates one cycle per member for round 1', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-05',
        'frequency_unit' => FrequencyUnitType::WEEKLY,
        'frequency_interval' => 1,
    ], memberCount: 4);

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles)->toHaveCount(4);
});

it('assigns sequential cycle numbers starting at 1 for round 1', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-05',
        'frequency_unit' => FrequencyUnitType::WEEKLY,
        'frequency_interval' => 1,
    ], memberCount: 3);

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles[0]['cycle_number'])->toBe(1)
        ->and($cycles[1]['cycle_number'])->toBe(2)
        ->and($cycles[2]['cycle_number'])->toBe(3);
});

it('assigns the recipient in payout_order sequence', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-05',
        'frequency_unit' => FrequencyUnitType::WEEKLY,
        'frequency_interval' => 1,
    ], memberCount: 3);

    $members = $group->members()->orderBy('payout_order')->get();

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles[0]['recipient_member_id'])->toBe($members[0]->id)
        ->and($cycles[1]['recipient_member_id'])->toBe($members[1]->id)
        ->and($cycles[2]['recipient_member_id'])->toBe($members[2]->id);
});

it('calculates weekly due dates correctly', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-05', // a Monday
        'frequency_unit' => FrequencyUnitType::WEEKLY,
        'frequency_interval' => 1,
    ], memberCount: 3);

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles[0]['due_date']->toDateString())->toBe('2026-01-05')
        ->and($cycles[1]['due_date']->toDateString())->toBe('2026-01-12')
        ->and($cycles[2]['due_date']->toDateString())->toBe('2026-01-19');
});

it('calculates custom-interval due dates correctly (every 9 days)', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-01',
        'frequency_unit' => FrequencyUnitType::DAILY,
        'frequency_interval' => 9,
    ], memberCount: 3);

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles[0]['due_date']->toDateString())->toBe('2026-01-01')
        ->and($cycles[1]['due_date']->toDateString())->toBe('2026-01-10')
        ->and($cycles[2]['due_date']->toDateString())->toBe('2026-01-19');
});

/**
 * This is the important one — the month-end overflow case. If someone
 * accidentally used addMonths() instead of addMonthsNoOverflow() in the
 * service, this test is what catches it.
 */
it('clamps monthly due dates at month-end instead of overflowing', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-31',
        'frequency_unit' => FrequencyUnitType::MONTHLY,
        'frequency_interval' => 1,
    ], memberCount: 3);

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles[0]['due_date']->toDateString())->toBe('2026-01-31')
        // NOT 2026-03-03 — that would mean addMonths() was used instead
        // of addMonthsNoOverflow().
        ->and($cycles[1]['due_date']->toDateString())->toBe('2026-02-28')
        ->and($cycles[2]['due_date']->toDateString())->toBe('2026-03-31');
});

it('continues cycle numbering and dates into a new round without resetting', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-05',
        'frequency_unit' => FrequencyUnitType::WEEKLY,
        'frequency_interval' => 1,
    ], memberCount: 2);

    $generator = app(CycleGeneratorService::class);

    // Simulate round 1 already having been generated and saved.
    $round1 = $generator->generateRound($group, roundNumber: 1);
    foreach ($round1 as $cycleData) {
        $group->cycles()->create($cycleData);
    }

    // Now generate round 2 and confirm it picks up where round 1 left off.
    $round2 = $generator->generateRound($group, roundNumber: 2);

    // Round 1 was cycles 1-2, due Jan 5 and Jan 12.
    // Round 2 should continue at cycle 3, starting one week after the
    // last cycle of round 1 (Jan 12 + 1 week = Jan 19).
    expect($round2[0]['cycle_number'])->toBe(3)
        ->and($round2[0]['due_date']->toDateString())->toBe('2026-01-19')
        ->and($round2[1]['cycle_number'])->toBe(4)
        ->and($round2[1]['due_date']->toDateString())->toBe('2026-01-26');
});

it('only includes active (non soft-deleted) members', function () {
    $group = makeGroupWithMembers([
        'start_date' => '2026-01-05',
        'frequency_unit' => FrequencyUnitType::WEEKLY,
        'frequency_interval' => 1,
    ], memberCount: 3);

    $group->members()->first()->delete(); // soft delete

    $cycles = app(CycleGeneratorService::class)->generateRound($group, roundNumber: 1);

    expect($cycles)->toHaveCount(2);
});