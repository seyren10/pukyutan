import MainLayout from "@/layouts/MainLayout.vue";
import { createRouter, createWebHistory } from "vue-router";
import { authRoutes } from "./auth";
import { useUserStore } from "@/stores/user";
import { storeToRefs } from "pinia";

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
      ],
    },
    authRoutes,
  ],
});

router.beforeEach((to) => {
  const userStore = useUserStore();
  const { isLoggedIn } = storeToRefs(userStore);

  if (to.meta.requiresAuth && !isLoggedIn.value) {
    return { name: "login", query: { redirect: to.fullPath } };
  }

  if (to.meta.requiresGuest && isLoggedIn.value) {
    return { name: "dashboard" };
  }
});

export default router;
