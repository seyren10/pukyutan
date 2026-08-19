import { useMutation, useQueryClient } from "@tanstack/vue-query";
import { joinGroup } from "../api";

export const useJoinGroupMutation = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: joinGroup,
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["share-requests"] });
    },
  });
};
