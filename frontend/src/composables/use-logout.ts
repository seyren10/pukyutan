import { useUserStore } from "@/stores/user";
import { useQueryClient } from "@tanstack/vue-query";
import { useRouter } from "vue-router";

export const useLogout = () => {
  const queryClient = useQueryClient();
  const router = useRouter();
  const userStore = useUserStore();

  const execute = async (query?: Record<string, string>) => {
    userStore.setUser(null);
    queryClient.clear();
    await router.replace({ name: "login", query: query });
  };

  return {
    execute,
  };
};
