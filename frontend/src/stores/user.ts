import type { User } from "@/features/auth/type";
import { getInitials } from "@/lib/helpers";
import { defineStore } from "pinia";
import { computed, readonly, ref } from "vue";

export const useUserStore = defineStore("user", () => {
  const user = ref<User | null>(null);

  const isEmailVerified = computed(() => !!user.value?.email_verified_at);
  const isLoggedIn = computed(() => !!user.value);
  const isGoogleLoggedIn = computed(() => !!user.value?.google_id);
  const userInitials = computed(() =>
    isLoggedIn.value ? getInitials(user.value?.name || "NA") : "NA",
  );

  const setUser = (payload: User | null) => (user.value = payload);

  return {
    user: readonly(user),
    setUser,
    isEmailVerified,
    isLoggedIn,
    userInitials,
    isGoogleLoggedIn,
  };
});
