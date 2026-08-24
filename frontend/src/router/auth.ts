import AuthLayout from "@/layouts/AuthLayout.vue";
import { useUserStore } from "@/stores/user";
import { storeToRefs } from "pinia";
import type { RouteRecordRaw } from "vue-router";
import { toast } from "vue-sonner";

export const authRoutes: RouteRecordRaw = {
  path: "/auth",
  component: AuthLayout,
  redirect: { name: "login" },
  children: [
    {
      path: "login",
      name: "login",
      component: () => import("@/pages/auth/Login.vue"),
      meta: {
        requiresGuest: true,
      },
      beforeEnter: (to, from) => {
        if (from.name === "profile" && to.query.password_reset) {
          toast.info("Your password has been reset", {
            description: "Log in with your new password to continue",
          });

          return {
            path: to.path,
            query: {},
            has: to.hash,
          };
        }
      },
    },
    {
      path: "register",
      name: "register",
      component: () => import("@/pages/auth/Register.vue"),
      meta: {
        requiresGuest: true,
      },
    },
    {
      path: "verify-email",
      name: "verify-email",
      component: () => import("@/pages/auth/VerifyEmail.vue"),
      meta: {
        requiresAuth: true,
      },
      beforeEnter: () => {
        const userStore = useUserStore();
        const { isEmailVerified } = storeToRefs(userStore);

        if (isEmailVerified.value)
          return {
            name: "app",
          };
      },
    },
    {
      path: "forgot-password",
      name: "forgot-password",
      component: () => import("@/pages/auth/ForgotPassword.vue"),
      meta: {
        requiresGuest: true,
      },
    },
    {
      path: "password-reset/:token",
      name: "password-reset",
      component: () => import("@/pages/auth/ResetPassword.vue"),
      meta: { requiresGuest: true },
      props: true,
    },
  ],
};
