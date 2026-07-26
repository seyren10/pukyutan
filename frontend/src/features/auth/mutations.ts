import { useMutation } from "@tanstack/vue-query";
import type { LoginCredentials } from "./type";
import {
  forgotPassword,
  login,
  resetPassword,
  sendEmailVerification,
} from "./api";
import { toast } from "vue-sonner";
import { useRoute, useRouter } from "vue-router";
import { useUserStore } from "@/stores/user";
import type { LaravelError } from "@/types/common";

export const useAuthMutations = () => {
  const router = useRouter();
  const route = useRoute();
  const userStore = useUserStore();

  const errorCallback = (err: Error) => {
    const error = err as LaravelError;
    toast.warning(error.response?.data?.message || "Something went wrong");
  };

  const loginMutation = useMutation({
    mutationFn: (payload: LoginCredentials) => login(payload),
    onSuccess: async (user) => {
      toast.info("Successfully logged in", {
        position: "top-center",
      });
      userStore.setUser(user);
      router.replace((route.query.redirect as string) || { name: "dashboard" });
    },
  });

  const verifyEmailMutation = useMutation({
    mutationFn: sendEmailVerification,
    onError: errorCallback,
  });

  const forgotPasswordMutation = useMutation({
    mutationFn: (payload: Pick<LoginCredentials, "email">) =>
      forgotPassword(payload),
    onError: errorCallback,
  });

  const passwordResetMutation = useMutation({
    mutationFn: resetPassword,
    onSuccess: async () => {
      toast.info("Password reset successfully", {
        description: "Please login with your new password to continue",
        position: "top-center",
      });
      router.replace({ name: "login" });
    },
    onError: errorCallback,
  });

  return {
    loginMutation,
    verifyEmailMutation,
    forgotPasswordMutation,
    passwordResetMutation,
  };
};
