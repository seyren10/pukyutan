import MainLayout from "@/layouts/MainLayout.vue";
import { createRouter, createWebHistory } from "vue-router";
import { authRoutes } from "./auth";
import { useUserStore } from "@/stores/user";
import { storeToRefs } from "pinia";
import { useBootstrapStore } from "@/stores/bootstrap";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: MainLayout,
      meta: {
        requiresAuth: true,
      },
      children: [
        {
          path: "",
          name: "dashboard",
          alias: "dashboard",
          component: () => import("@/pages/index/Index.vue"),
        },
        {
          path: "groups/:id",
          name: "group-detail",
          component: () => import("@/pages/groups/show/Index.vue"),
          props: (route) => ({ groupId: Number(route.params.id) }),
        },
      ],
    },
    authRoutes,
  ],
});

router.beforeEach(async (to) => {
  const userStore = useUserStore();
  const bootstrapStore = useBootstrapStore();
  const { isLoggedIn } = storeToRefs(userStore);

  await bootstrapStore.bootstrap();

  if (to.meta.requiresAuth && !isLoggedIn.value) {
    return { name: "login", query: { redirect: to.fullPath } };
  }

  if (to.meta.requiresGuest && isLoggedIn.value) {
    return { name: "dashboard" };
  }
});

export default router;