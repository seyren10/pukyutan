import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";
import { leaveGroup } from "../api";
import type { LaravelError } from "@/types/common";

export const useLeaveGroupMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: leaveGroup,
    onSuccess: () => {
      // queryClient.invalidateQueries({ queryKey: ["groups"] });
      queryClient.invalidateQueries({ queryKey: ["groups-shared"] });
    },
    onError: (err: LaravelError) => {
      toast.error("Couldn't leave the group", {
        description: err.response?.data?.message,
      });
    },
  });
};
