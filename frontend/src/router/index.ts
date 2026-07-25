import MainLayout from "@/layouts/MainLayout.vue";
import { createRouter, createWebHistory } from "vue-router";
import { authRoutes } from "./auth";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: MainLayout,
      children: [
        {
          path: "",
          name: "dashboard",
          component: () => import("@/pages/index/Index.vue"),
        },
      ],
    },
    authRoutes,
  ],
});

export default router;
