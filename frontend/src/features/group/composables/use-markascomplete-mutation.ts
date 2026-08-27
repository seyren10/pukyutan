import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { markGroupAsComplete } from "../api";
import { toast } from "vue-sonner";
import type { LaravelError } from "@/types/common";

export const useMarkascompleteMutation = () => {
  const queryClient = useQueryClient();
  const { mutate, isPending } = useMutation({
    mutationFn: markGroupAsComplete,
    onSuccess: () => {
      toast.info("Group is marked as completed");
    },
    onError: (err: LaravelError) => {
      toast.error(
        err.response?.data?.message || "Something went wrong please try again",
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
