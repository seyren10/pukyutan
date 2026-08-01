import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { activateGroup } from "../api";
import { toast } from "vue-sonner";
import { getGroupsInfiniteQueryOptions } from "../query";
import type { LaravelError } from "@/types/common";

export const useGroupActivateMutation = () => {
  const queryClient = useQueryClient();
  const { mutate, isPending } = useMutation({
    mutationFn: activateGroup,
    onSuccess: () => {
      toast.success("Group activated successfully");
    },
    onError: (error: LaravelError) => {
      toast.error(
        error.response?.data.message ??
          "An error occurred while activating the group",
      );
    },
    onSettled: () => {
      const queryKey = getGroupsInfiniteQueryOptions().queryKey;
      queryClient.invalidateQueries({ queryKey });
    },
  });

  return {
    mutate,
    isPending,
  };
};
