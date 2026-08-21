import router from "@/router";
import { useUserStore } from "@/stores/user";
import axios, { AxiosError } from "axios";
import { queryClient } from "../tanstack-query/query-client";
import { toast } from "vue-sonner";
import { authRoutes } from "@/router/auth";

export const httpClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  withCredentials: true,
  withXSRFToken: true,
  headers: {
    Accept: "application/json",
  },
});

httpClient.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    const isAuthRoute = location.pathname.startsWith(authRoutes.path);
    const isLandingPage = location.pathname === "/";

    if (error.response?.status === 401 && !isAuthRoute && !isLandingPage) {
      useUserStore().setUser(null);
      queryClient.clear();
      router.replace({ name: "login" });

      toast.warning("Session Expired. Please login to continue");
    }

    return Promise.reject(error);
  },
);
