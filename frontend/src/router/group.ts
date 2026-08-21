import type { RouteRecordRaw } from "vue-router";

export const groupRoutes: RouteRecordRaw = {
  path: "groups",
  name: "groups",
  children: [
    {
      path: "",
      name: "groups.index",
      component: () => import("@/pages/groups/index/Index.vue"),
    },
    {
      path: ":id",
      name: "groups.detail",
      component: () => import("@/pages/groups/show/Index.vue"),
      props: (route) => ({ groupId: Number(route.params.id) }),
      children: [
        {
          path: "activities",
          name: "groups.detail.activities.index",
          component: () => import("@/pages/groups/show/activity/Index.vue"),
          props: (route) => ({ groupId: Number(route.params.id) }),
        },
        {
          path: "access",
          name: "groups.detail.access.index",
          component: () => import("@/pages/groups/show/access/Index.vue"),
          props: (route) => ({ groupId: Number(route.params.id) }),
        },
      ],
    },
  ],
};
