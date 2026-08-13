import { useUserStore } from "@/stores/user";
import { useQueryClient } from "@tanstack/vue-query";
import { useRouter } from "vue-router";

export const useLogout = () => {
  const queryClient = useQueryClient();
  const router = useRouter();
  const userStore = useUserStore();

  const execute = () => {
    console.log("boom");

    userStore.setUser(null);
    queryClient.clear();
    router.replace({ name: "login" });
  };

  return {
    execute,
  };
};
