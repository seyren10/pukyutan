import type { RouteRecordRaw } from "vue-router";

export const profileRoutes: RouteRecordRaw = {
  path: "profile",
  name: "profile",
  component: () => import("@/pages/profile/Index.vue"),
};
