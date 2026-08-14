// Mirrors App\Enums\GroupActivityEvent on the backend. Keep in sync manually
// — there's no shared codegen between the two yet.
export const GROUP_ACTIVITY_EVENT = [
  "group.activated",
  "group.completed",
  "round.started",
  "cycle.disbursed",
  "share.accepted",
  "share.rejected",
  "contribution.recorded",
] as const;

export type GroupActivityEvent = (typeof GROUP_ACTIVITY_EVENT)[number];