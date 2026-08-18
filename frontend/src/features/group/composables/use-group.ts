import { computed, toValue, type MaybeRefOrGetter } from "vue";
import type { Group, GroupDetail } from "../type";
import { formatFrequencyLabel } from "@/lib/helpers";
import { formatDate } from "date-fns";

type GroupBase = Pick<
  Group,
  | "name"
  | "user"
  | "status"
  | "contribution_amount"
  | "frequency_unit"
  | "frequency_interval"
  | "start_date"
  | "next_cycle"
> &
  Partial<Pick<Group, "recent_members" | "members_count" | "cycles_count">>;

export const useGroup = <T extends GroupBase | undefined>(
  group: MaybeRefOrGetter<T>,
) => {
  const groupValue = computed(() => toValue(group));

  const name = computed(() => groupValue.value?.name ?? "No group");
  const user = computed(() => groupValue.value?.user);
  const status = computed(() => groupValue.value?.status);
  const recentMembers = computed(() => groupValue.value?.recent_members ?? []);
  const membersCount = computed(() => groupValue.value?.members_count ?? 0);
  const nextCycle = computed(() => groupValue.value?.next_cycle);
  const cyclesCount = computed(() => groupValue.value?.cycles_count ?? 0);
  const contributionAmount = computed(() =>
    Number.parseInt(
      String(groupValue.value?.contribution_amount ?? "0"),
      10,
    ).toLocaleString("en-PH"),
  );
  const frequencyUnit = computed(() => groupValue.value?.frequency_unit);
  const frequencyInterval = computed(
    () => groupValue.value?.frequency_interval,
  );
  const frequencyLabel = computed(() => {
    const unit = frequencyUnit.value;
    const interval = frequencyInterval.value;

    return unit && interval ? formatFrequencyLabel(unit, interval) : "";
  });
  const startDateLabel = computed(() => {
    const startDate = groupValue.value?.start_date;

    return startDate ? formatDate(new Date(startDate), "MMMM dd, yyyy") : "";
  });

  return {
    name,
    user,
    status,
    recentMembers,
    membersCount,
    nextCycle,
    cyclesCount,
    contributionAmount,
    frequencyUnit,
    frequencyInterval,
    frequencyLabel,
    startDateLabel,
  };
};

export const useGroupDetail = (
  groupDetail: MaybeRefOrGetter<GroupDetail | undefined>,
) => {
  const groupDetailValue = computed(() => toValue(groupDetail));
  const base = useGroup(groupDetail);

  const members = computed(() => groupDetailValue.value?.members ?? []);
  // Both overrides below fix the same root cause: GroupDetail never has
  // recent_members/members_count (only Group's list-view shape does),
  // so base's versions of these are unconditionally empty/0 here.
  //
  // recentMembers: rather than re-impose the list-view's "5 most recent"
  // cap (which existed specifically for a card preview, a concept that
  // doesn't obviously apply on a page already showing the full roster),
  // this just exposes the full members array under the same name for
  // symmetry with membersCount below. If you actually want a capped
  // "recently added" preview here too, this needs sorting by
  // created_at first, since `members` itself is payout_order-sorted
  // (global scope on Member), not recency-sorted.
  const recentMembers = computed(() => members.value);
  const membersCount = computed(() => members.value.length);
  const isRoundCompleted = computed(
    () => groupDetailValue.value?.is_round_completed ?? false,
  );

  return {
    ...base,
    members,
    recentMembers,
    membersCount,
    isRoundCompleted,
  };
};
