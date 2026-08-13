import { defineStore } from "pinia";
import { useUserStore } from "./user";
import { httpClient } from "@/services/axios/axios";
import type { User } from "@/features/auth/type";
import { ref } from "vue";

export const useBootstrapStore = defineStore("boostrap", () => {
  const userStore = useUserStore();
  const isBootstrapping = ref(true);

  let bootstrapPromise: Promise<void> | null = null;

  const bootstrap = () => {
    if (bootstrapPromise) return bootstrapPromise;

    bootstrapPromise = httpClient
      .get<User>("/api/user")
      .then(({ data }) => {
        userStore.setUser(data);
      })
      .catch(() => {
        userStore.setUser(null);
      })
      .finally(() => {
        isBootstrapping.value = false;
      });

    return bootstrapPromise;
  };

  return {
    bootstrap,
    isBootstrapping,
  };
});
