import type { RouteRecordRaw } from "vue-router";

export const shareRequestsRoute: RouteRecordRaw = {
  path: "requests",
  children: [
    {
      path: "",
      name: "share-requests.index",
      component: () => import("@/pages/share-requests/Index.vue"),
    },
  ],
};
