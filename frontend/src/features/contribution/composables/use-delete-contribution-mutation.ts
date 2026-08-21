import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { deleteContribution } from "../api";
import { toast } from "vue-sonner";

export const useDeleteContributionMutation = (
  groupId: number,
  cycleId: number,
) => {
  const queryClient = useQueryClient();

  const { mutate, isPending } = useMutation({
    mutationFn: deleteContribution,
    onSuccess: () => toast.info("Payment undone."),
    onSettled: () => {
      // Refetches collected_total / recipient / progress on the cycle
      // detail dialog — the whole reason this needed to be reactive.
      queryClient.invalidateQueries({
        queryKey: ["groups", "detail", groupId],
      });
      queryClient.invalidateQueries({
        queryKey: ["cycles", "detail", cycleId],
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
