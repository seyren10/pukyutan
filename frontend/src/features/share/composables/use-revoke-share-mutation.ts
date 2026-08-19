import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { toast } from "vue-sonner";
import { revokeShareRequest } from "../api";
import type { LaravelError } from "@/types/common";

export const useRevokeShareMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: revokeShareRequest,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["groups"] });
      queryClient.invalidateQueries({ queryKey: ["share-requests"] });
    },
    onError: (err: LaravelError) => {
      toast.error("Couldn't remove access", {
        description: err.response?.data?.message,
      });
    },
  });
};
