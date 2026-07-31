import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { getGroupMembersQueryOptions } from "../query";
import { reorderMembers } from "../api";

export const useReorderMembersMutation = (groupId: number) => {
  const queryClient = useQueryClient();
  const queryKey = getGroupMembersQueryOptions(groupId).queryKey;

  const { mutate, isPending } = useMutation({
    mutationFn: (memberIds: number[]) => reorderMembers(groupId, memberIds),
    onSettled: () => {
      // Reconcile with the server's payout_order once the request lands,
      // success or failure — the component handles the failure-case
      // rollback itself since it owns the pre-drag snapshot.
      queryClient.invalidateQueries({ queryKey });
    },
  });

  return { mutate, isPending };
};
