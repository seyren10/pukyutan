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
        {
          path: "members",
          name: "groups.detail.members.index",
          component: () => import("@/pages/groups/show/members/Index.vue"),
          props: (route) => ({ groupId: Number(route.params.id) }),
        },
        {
          // Same page as above — opening a member's ledger accordion pushes
          // here instead of toggling local state, so the expanded row is
          // deep-linkable and survives a refresh/back button.
          path: "members/:memberId/ledger",
          name: "groups.detail.members.ledger",
          component: () => import("@/pages/groups/show/members/Index.vue"),
          props: (route) => ({
            groupId: Number(route.params.id),
            memberId: Number(route.params.memberId),
          }),
        },
      ],
    },
  ],
};
