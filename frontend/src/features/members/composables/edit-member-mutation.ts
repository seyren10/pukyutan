import { useMutation, useQueryClient } from "@tanstack/vue-query";
import type { Member, UpdateMemberPayload } from "../type";
import { updateMember } from "../api";
import { getGroupMembersQueryOptions } from "../query";
import { toast } from "vue-sonner";
import type { LaravelError } from "@/types/common";

export const useEditMemberMutation = () => {
  const queryClient = useQueryClient();

  const { mutate: editMemberMutate, isPending: isEditMemberPending } =
    useMutation({
      mutationFn: ({
        memberId,
        payload,
      }: {
        memberId: number;
        // Not sent to the API — only used to target the right cache entry below.
        groupId: number;
        payload: UpdateMemberPayload;
      }) => updateMember(memberId, payload),

      onMutate: async ({ memberId, groupId, payload }) => {
        const queryKey = getGroupMembersQueryOptions(() => groupId).queryKey;
        await queryClient.cancelQueries({ queryKey });

        const previousData = queryClient.getQueryData(queryKey);

        queryClient.setQueryData(
          queryKey,
          (old: { data: Member[] } | undefined) =>
            old
              ? {
                  ...old,
                  data: old.data.map((member) =>
                    member.id === memberId
                      ? { ...member, ...payload }
                      : member,
                  ),
                }
              : old,
        );

        // Passed to onError/onSettled as the third argument.
        return { previousData, queryKey };
      },

      onError: (err: LaravelError, _variables, context) => {
        if (!context) return;
        queryClient.setQueryData(context.queryKey, context.previousData);
        toast.error("Something went wrong", {
          description: err.response?.data?.message,
        });
      },

      onSuccess: () => {
        toast.success("Member updated");
      },

      onSettled: (_data, _error, _variables, context) => {
        if (!context) return;
        queryClient.invalidateQueries({ queryKey: context.queryKey });
        // Name changes surface in the group's recent_members avatars too.
        queryClient.invalidateQueries({ queryKey: ["groups"] });
      },
    });

  return {
    editMemberMutate,
    isEditMemberPending,
  };
};
