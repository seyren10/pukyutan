import type { User } from "@/features/auth/type";
import { defineStore } from "pinia";
import { computed, readonly, ref } from "vue";
import { getUser } from "@/features/auth/api";

export const useUserStore = defineStore("user", () => {
  const user = ref<User | null>(null);

  const isEmailVerified = computed(() => !!user.value?.email_verified_at);
  const isLoggedIn = computed(() => !!user.value);
  const setUser = (payload: User | null) => (user.value = payload);

  const bootstrap = async () => {
    try {
      const resUser = await getUser();
      if (resUser) user.value = resUser;
    } catch (_) {
      user.value = null;
    }
  };

  return {
    user: readonly(user),
    setUser,
    isEmailVerified,
    bootstrap,
    isLoggedIn,
  };
});
