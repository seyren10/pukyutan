import type { User } from "@/features/auth/type";
import { defineStore } from "pinia";
import { computed, readonly, ref } from "vue";

export const useUserStore = defineStore("user", () => {
  const user = ref<User | null>(null);

  const isEmailVerified = computed(() => !!user.value?.email_verified_at);
  const isLoggedIn = computed(() => !!user.value);
  const setUser = (payload: User | null) => (user.value = payload);



  return {
    user: readonly(user),
    setUser,
    isEmailVerified,
    isLoggedIn,
  };
});
