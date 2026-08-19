import type { RouteRecordRaw } from "vue-router";

export const sharedGroupsRoute: RouteRecordRaw = {
  path: "shared",
  children: [
    {
      path: "",
      name: "shared-groups.index",
      component: () => import("@/pages/shared-groups/Index.vue"),
    },
  ],
};
