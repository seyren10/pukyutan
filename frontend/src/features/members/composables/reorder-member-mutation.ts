import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { getGroupMembersQueryOptions } from "../query";
import { reorderMembers } from "../api";
import { toValue, type MaybeRefOrGetter } from "vue";

export const useReorderMembersMutation = (
  groupId: MaybeRefOrGetter<number>,
) => {
  const queryClient = useQueryClient();
  const queryKey = getGroupMembersQueryOptions(groupId).queryKey;

  const { mutate, isPending } = useMutation({
    mutationFn: (memberIds: number[]) =>
      reorderMembers(toValue(groupId), memberIds),
    onSettled: () => {
      // Reconcile with the server's payout_order once the request lands,
      // success or failure — the component handles the failure-case
      // rollback itself since it owns the pre-drag snapshot.
      queryClient.invalidateQueries({ queryKey });
    },
  });

  return { mutate, isPending };
};
