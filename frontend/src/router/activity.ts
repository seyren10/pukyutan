import type { RouteRecordRaw } from "vue-router";

export const activityRoute: RouteRecordRaw = {
  path: "activities",
  children: [
    {
      path: "",
      name: "activities.index",
      component: () => import("@/pages/activity/Index.vue"),
    },
  ],
};
