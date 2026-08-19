import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";
import { acceptShareRequest } from "../api";
import type { LaravelError } from "@/types/common";

export const useAcceptShareMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: acceptShareRequest,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["groups"] });
      queryClient.invalidateQueries({ queryKey: ["share-requests"] });
    },
    onError: (err: LaravelError) => {
      toast.error("Couldn't accept the request", {
        description: err.response?.data?.message,
      });
    },
  });
};
