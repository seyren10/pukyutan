import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { disburseCycle } from "../api";
import type { DisburseCycleSchema } from "../type";

export const useDisburseCycleMutation = ({
  cycleId,
}: {
  cycleId: number;
  groupId: number;
}) => {
  const queryClient = useQueryClient();

  const { mutate, isPending } = useMutation({
    mutationFn: (payload: DisburseCycleSchema) =>
      disburseCycle(cycleId, payload),
    onSettled: () => {
      // Picks up the new disbursed_at / disbursed_amount from the server,
      // which is also what flips this dialog into its "already disbursed" state.
      queryClient.invalidateQueries({ queryKey: ["groups"] });
    },
  });

  return { mutate, isPending };
};
