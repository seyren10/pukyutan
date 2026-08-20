import { useMutation, useQueryClient } from "@tanstack/vue-query";
import type { CreateMemberPayload, Member } from "../type";
import { addMember } from "../api";
import { getGroupMembersQueryOptions } from "../query";
import { toast } from "vue-sonner";
import type { LaravelError } from "@/types/common";

export const useAddMemberMutation = () => {
  const queryClient = useQueryClient();
  const { mutate: addMemberMutate, isPending: isAddMemberPending } =
    useMutation({
      mutationFn: ({
        groupId,
        payload,
      }: {
        groupId: number;
        payload: CreateMemberPayload;
      }) => addMember(groupId, payload),

      onMutate: async ({ groupId, payload }) => {
        const queryKey = getGroupMembersQueryOptions(() => groupId).queryKey;
        // Stop any in-flight refetch from overwriting our optimistic write.
        await queryClient.cancelQueries({ queryKey });

        const previousData = queryClient.getQueryData(queryKey);

        // Negative IDs can never collide with a real (positive, autoincrement)
        // DB id — makes the temp row trivially identifiable for reconciliation.
        const tempId = -Date.now();

        const existingMembers =
          (previousData as { data: Member[] } | undefined)?.data ?? [];

        const nextPayoutOrder = (existingMembers.at(-1)?.payout_order ?? 0) + 1;

        const optimisticMember: Member = {
          id: tempId,
          name: payload.name,
          email: payload.email,
          payout_order: nextPayoutOrder,
          deleted_at: null,
          group_id: groupId,
          created_at: new Date().toISOString(),
          updated_at: new Date().toISOString(),
        };

        queryClient.setQueryData(
          queryKey,
          (old: { data: Member[] } | undefined) =>
            old ? { ...old, data: [...old.data, optimisticMember] } : old,
        );

        // Passed to onError/onSuccess/onSettled as the third argument.
        return { previousData, tempId, queryKey };
      },

      onError: (err: LaravelError, _variables, context) => {
        if (!context) return;
        // Something went wrong — put the cache back exactly as it was,
        // the optimistic row disappears.
        queryClient.setQueryData(context.queryKey, context.previousData);
        toast.error("Something went wrong", {
          description: err.response?.data?.message,
        });
      },

      onSuccess: (realMember, _variables, context) => {
        if (!context) return;
        // Swap the temp row for the server's real row — real id, and
        // crucially, the real payout_order if it differs from our guess.
        queryClient.setQueryData(
          context.queryKey,
          (old: { data: Member[] } | undefined) =>
            old
              ? {
                  ...old,
                  data: old.data.map((member) =>
                    member.id === context.tempId ? realMember : member,
                  ),
                }
              : old,
        );
      },

      onSettled: (_data, _error, _variables, context) => {
        if (!context) return;
        queryClient.invalidateQueries({ queryKey: ["groups"] });
      },
    });

  return {
    addMemberMutate,
    isAddMemberPending,
  };
};
