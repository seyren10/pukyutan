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
    },
  });

  return { mutate, isPending };
};
