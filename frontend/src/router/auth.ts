import AuthLayout from "@/layouts/AuthLayout.vue";
import type { RouteRecordRaw } from "vue-router";

export const authRoutes: RouteRecordRaw = {
  path: "/auth",
  component: AuthLayout,
  redirect: { name: "login" },
  children: [
    {
      path: "login",
      name: "login",
      component: () => import("@/pages/auth/Login.vue"),
    },
    {
      path: "register",
      name: "register",
      component: () => import("@/pages/auth/Register.vue"),
    },
    {
      path: "verify-email",
      name: "verify-email",
      component: () => import("@/pages/auth/VerifyEmail.vue"),
    },
    {
      path: "forgot-password",
      name: "forgot-password",
      component: () => import("@/pages/auth/ForgotPassword.vue"),
    },
  ],
};
