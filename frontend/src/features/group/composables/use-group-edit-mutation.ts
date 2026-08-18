import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { editGroup } from "../api";
import { toast } from "vue-sonner";
import type { LaravelError } from "@/types/common";
import type { EditGroupSchema } from "../type";

export const useGroupEditMutation = () => {
  const queryClient = useQueryClient();
  const { mutate, isPending } = useMutation({
    mutationFn: ({
      groupId,
      payload,
    }: {
      groupId: number;
      payload: EditGroupSchema;
    }) => editGroup(groupId, payload),
    onSuccess: () => {
      toast.success("Group has been updated.");
    },
    onError: (error: LaravelError) => {
      toast.error(
        error.response?.data.message ??
          "An error occurred while editing the group",
      );
    },
    onSettled: () => {
      queryClient.invalidateQueries({ queryKey: ["groups"] });
    },
  });

  return {
    mutate,
    isPending,
  };
};
