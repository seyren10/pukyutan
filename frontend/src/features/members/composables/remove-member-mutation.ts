import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { removeMember } from "../api";
import { getGroupMembersQueryOptions } from "../query";
import type { MaybeRefOrGetter } from "vue";

export const useRemoveMemberMutation = (groupId: MaybeRefOrGetter<number>) => {
  const queryClient = useQueryClient();
  const queryKey = getGroupMembersQueryOptions(groupId).queryKey;

  const { mutate, isPending } = useMutation({
    mutationFn: (memberId: number) => removeMember(memberId),

    onMutate: async (memberId) => {
      await queryClient.cancelQueries({ queryKey });

      const previousData = queryClient.getQueryData(queryKey);
      const existingMembers = [...(previousData?.data ?? [])];
      const index = existingMembers.findIndex((m) => m.id === memberId);

      // Nothing to optimistically remove — leave cache untouched.
      if (index === -1) return { previousData };

      existingMembers.splice(index, 1);
      queryClient.setQueryData(queryKey, {
        ...previousData,
        data: existingMembers,
      });

      return { previousData };
    },

    onError: (_err, _memberId, context) => {
      if (!context) return;
      queryClient.setQueryData(queryKey, context.previousData);
    },

    onSettled: () => {
      queryClient.invalidateQueries({ queryKey });
      // Removing a member changes the group's members_count/recent_members,
      // which the group list/detail cards render — keep them in sync too.
      queryClient.invalidateQueries({ queryKey: ["groups"] });
    },
  });

  return { mutate, isPending };
};
