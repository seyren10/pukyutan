import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { startNewRound } from "../api";
import { toast } from "vue-sonner";
import type { LaravelError } from "@/types/common";

export const useStartNewRoundMutation = () => {
  const queryClient = useQueryClient();

  const { mutate, isPending } = useMutation({
    mutationFn: (groupId: number) => startNewRound(groupId),
    onSuccess: () => {
      toast.success("New round has been started");
    },
    onSettled: () => {
      queryClient.invalidateQueries({ queryKey: ["groups"] });
    },
    onError: (error: LaravelError) => {
      toast.error(
        error.response?.data.message ??
          "An error occurred while starting a new round",
      );
    },
  });

  return {
    mutate,
    isPending,
  };
};
