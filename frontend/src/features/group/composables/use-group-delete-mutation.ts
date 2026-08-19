import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { deleteGroup } from "../api";
import { toast } from "vue-sonner";
import type { LaravelError } from "@/types/common";
import { useRoute, useRouter } from "vue-router";

export const useGroupDeleteMutation = () => {
  const queryClient = useQueryClient();
  const route = useRoute();
  const router = useRouter();

  const { mutate, isPending } = useMutation({
    mutationFn: (groupId: number) => deleteGroup(groupId),
    onSuccess: () => {
      toast.success("Group deleted");
      if (route.name !== "dashboard") router.back();
    },
    onError: (error: LaravelError) => {
      toast.error(
        error.response?.data.message ??
          "An error occurred while deleting the group",
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
