import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { disburseCycle } from "../api";
import { getGroupDetailQueryOptions } from "@/features/group/query";
import type { DisburseCycleSchema } from "../type";

export const useDisburseCycleMutation = ({
  cycleId,
  groupId,
}: {
  cycleId: number;
  groupId: number;
}) => {
  const queryClient = useQueryClient();
  const queryKey = getGroupDetailQueryOptions(() => groupId).queryKey;

  const { mutate, isPending } = useMutation({
    mutationFn: (payload: DisburseCycleSchema) =>
      disburseCycle(cycleId, payload),
    onSettled: () => {
      // Picks up the new disbursed_at / disbursed_amount from the server,
      // which is also what flips this dialog into its "already disbursed" state.
      queryClient.invalidateQueries({ queryKey });
    },
  });

  return { mutate, isPending };
};
