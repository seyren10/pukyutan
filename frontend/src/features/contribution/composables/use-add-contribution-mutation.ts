import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { getGroupDetailQueryOptions } from "@/features/group/query";
import type { ContributionSchema } from "../type";
import { addContribution } from "../api";
import { toValue } from "vue";

export const useAddContributionMutation = ({
  cycleId,
  groupId,
}: {
  cycleId: number;
  groupId: number;
}) => {
  const queryClient = useQueryClient();
  const queryKey = getGroupDetailQueryOptions(() => groupId).queryKey;

  const { mutate, isPending } = useMutation({
    mutationFn: (payload: ContributionSchema) =>
      addContribution(cycleId, payload),
    onSettled: () => {
      // Refetches collected_total / recipient / progress on the cycle
      // detail dialog — the whole reason this needed to be reactive.
      queryClient.invalidateQueries({ queryKey });
      queryClient.invalidateQueries({
        queryKey: ["cycles", "detail", toValue(cycleId)],
      });
      // The two invalidations above are scoped to this specific group/cycle,
      // so they don't reach ["groups", "stats"] (a sibling key, not a
      // descendant of either) — recording a payment changes the dashboard's
      // "collected this cycle" figure, so it needs its own invalidation.
      queryClient.invalidateQueries({ queryKey: ["groups", "stats"] });
    },
  });

  return { mutate, isPending };
};
