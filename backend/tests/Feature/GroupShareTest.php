<?php

use App\Models\Group;
use App\Models\GroupShare;
use App\Models\User;
use App\Enums\GroupShareStatus; // adjust namespace if yours differs

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function makeGroupWithOwner(): array
{
    $owner = User::factory()->create();
    $group = Group::factory()->create(['user_id' => $owner->id]);

    return [$owner, $group];
}

// ------------------------------------------------------------------
// Listing share requests
// ------------------------------------------------------------------

it('allows the owner to list group shares', function () {
    [$owner, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    $response = $this->actingAs($owner)
        ->getJson("/api/v1/groups/{$group->id}/share-requests");

    $response->assertOk()->assertJsonCount(1, 'data');
});

it('forbids an unrelated user from listing group shares', function () {
    [, $group] = makeGroupWithOwner();
    $randomUser = User::factory()->create();

    $response = $this->actingAs($randomUser)
        ->getJson("/api/v1/groups/{$group->id}/share-requests");

    $response->assertForbidden();
});

// ------------------------------------------------------------------
// Accepting requests
// ------------------------------------------------------------------

it('allows the owner to accept a pending share request', function () {
    [$owner, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    $share = GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    $response = $this->actingAs($owner)
        ->postJson("/api/v1/share-requests/{$share->id}/accept");

    $response->assertNoContent();

    expect($share->fresh()->status)->toBe(GroupShareStatus::ACCEPTED)
        ->and($share->fresh()->responded_at)->not->toBeNull();
});

it('forbids a non-owner from accepting a share request', function () {
    [, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    $share = GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    // Even the requester themselves shouldn't be able to self-approve.
    $response = $this->actingAs($viewer)
        ->postJson("/api/v1/share-requests/{$share->id}/accept");

    $response->assertForbidden();
    expect($share->fresh()->status)->toBe(GroupShareStatus::PENDING);
});

/**
 * With the group_id no longer duplicated in the URL, there's no second
 * "group" reference that could disagree with the share's actual group —
 * authorization is derived directly from $share_request->group. This
 * test just confirms an owner of a DIFFERENT group can't touch a share
 * that isn't theirs, which is the same underlying concern as before,
 * just no longer expressible as a URL mismatch.
 */
it('prevents an unrelated group owner from accepting someone else\'s share request', function () {
    $ownerOne = User::factory()->create();
    $ownerTwo = User::factory()->create();
    $groupTwo = Group::factory()->create(['user_id' => $ownerTwo->id]);

    $viewer = User::factory()->create();

    $share = GroupShare::factory()->create([
        'group_id' => $groupTwo->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    $response = $this->actingAs($ownerOne)
        ->postJson("/api/v1/share-requests/{$share->id}/accept");

    $response->assertForbidden();
    expect($share->fresh()->status)->toBe(GroupShareStatus::PENDING);
});

// ------------------------------------------------------------------
// Rejecting requests
// ------------------------------------------------------------------

it('allows the owner to reject a pending share request', function () {
    [$owner, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    $share = GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    $response = $this->actingAs($owner)
        ->postJson("/api/v1/share-requests/{$share->id}/reject");

    $response->assertNoContent();
    expect($share->fresh()->status)->toBe(GroupShareStatus::REJECTED);
});

it('prevents an unrelated group owner from rejecting someone else\'s share request', function () {
    $ownerOne = User::factory()->create();
    $ownerTwo = User::factory()->create();
    $groupTwo = Group::factory()->create(['user_id' => $ownerTwo->id]);

    $viewer = User::factory()->create();

    $share = GroupShare::factory()->create([
        'group_id' => $groupTwo->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    $response = $this->actingAs($ownerOne)
        ->postJson("/api/v1/share-requests/{$share->id}/reject");

    $response->assertForbidden();
});

// ------------------------------------------------------------------
// Read-only enforcement (GroupPolicy::view)
// ------------------------------------------------------------------

it('allows an accepted viewer to view the group', function () {
    [$owner, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::ACCEPTED,
    ]);

    $response = $this->actingAs($viewer)
        ->getJson("/api/v1/groups/{$group->id}");

    $response->assertOk();
});

it('forbids a pending viewer from viewing the group', function () {
    [, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::PENDING,
    ]);

    $response = $this->actingAs($viewer)
        ->getJson("/api/v1/groups/{$group->id}");

    $response->assertForbidden();
});

it('forbids a rejected viewer from viewing the group', function () {
    [, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::REJECTED,
    ]);

    $response = $this->actingAs($viewer)
        ->getJson("/api/v1/groups/{$group->id}");

    $response->assertForbidden();
});

/**
 * The critical write-boundary test: even an ACCEPTED viewer must
 * never be able to perform a write action. Adjust the endpoint here
 * to match whatever your actual "add contribution" or similar
 * write route is.
 */
it('forbids an accepted viewer from disbursing a cycle', function () {
    [$owner, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::ACCEPTED,
    ]);

    $cycle = \App\Models\Cycle::factory()->create(['group_id' => $group->id]);

    $response = $this->actingAs($viewer)
        ->postJson("/api/v1/cycles/{$cycle->id}/disburse", [
            'disbursed_amount' => 500,
        ]);

    $response->assertForbidden();
    expect($cycle->fresh()->disbursed_at)->toBeNull();
});

// ------------------------------------------------------------------
// Resend-after-rejection behavior (join endpoint, not this controller,
// but included here since it's part of the same feature)
// ------------------------------------------------------------------

it('allows a rejected user to request access again, updating the same row', function () {
    [, $group] = makeGroupWithOwner();
    $viewer = User::factory()->create();

    $share = GroupShare::factory()->create([
        'group_id' => $group->id,
        'user_id' => $viewer->id,
        'status' => GroupShareStatus::REJECTED,
        'responded_at' => now(),
    ]);

    $response = $this->actingAs($viewer)
        ->postJson("/api/v1/groups/join/{$group->invite_code}");

    $response->assertOk();

    // Same row, not a new one — enforced by the unique(group_id, user_id)
    // constraint plus updateOrCreate() in the join endpoint.
    expect(GroupShare::where('group_id', $group->id)->where('user_id', $viewer->id)->count())->toBe(1)
        ->and($share->fresh()->status)->toBe(GroupShareStatus::PENDING)
        ->and($share->fresh()->responded_at)->toBeNull();
});