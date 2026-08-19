import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";
import { rejectShareRequest } from "../api";
import type { LaravelError } from "@/types/common";

export const useRejectShareMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: rejectShareRequest,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["groups"] });
      queryClient.invalidateQueries({ queryKey: ["share-requests"] });
    },
    onError: (err: LaravelError) => {
      toast.error("Couldn't decline the request", {
        description: err.response?.data?.message,
      });
    },
  });
};
