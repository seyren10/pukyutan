import { computed, toValue, type MaybeRefOrGetter } from "vue";
import type { GroupRoundSummary } from "../type";

export const useRoundSummaryHelper = (
  roundSummary: MaybeRefOrGetter<GroupRoundSummary | undefined>,
) => {
  const roundSummaryValue = computed(() => toValue(roundSummary));

  const roundNumber = computed(
    () => roundSummaryValue.value?.round_number || 0,
  );
  const totalCollected = computed(
    () => roundSummaryValue.value?.total_collected.toLocaleString() || "0.00",
  );
  const totalExpected = computed(
    () => roundSummaryValue.value?.total_expected.toLocaleString() || "0.00",
  );
  const membersWithOutstandingBalance = computed(
    () =>
      roundSummaryValue.value?.members_with_outstanding_balance.toLocaleString() ||
      "0.00",
  );

  return {
    roundNumber,
    totalCollected,
    totalExpected,
    membersWithOutstandingBalance,
  };
};
