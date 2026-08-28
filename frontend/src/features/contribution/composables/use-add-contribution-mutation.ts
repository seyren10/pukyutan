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
      queryClient.invalidateQueries({ queryKey });
      queryClient.invalidateQueries({
        queryKey: ["cycles", "detail", toValue(cycleId)],
      });
      queryClient.invalidateQueries({ queryKey: ["groups", "stats"] });
      queryClient.invalidateQueries({ queryKey: ["groups", groupId, 'members'] });
      queryClient.invalidateQueries({ queryKey: ["members"] });
    },
  });

  return { mutate, isPending };
};
