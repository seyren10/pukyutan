import MainLayout from "@/layouts/MainLayout.vue";
import PublicLayout from "@/layouts/PublicLayout.vue";
import { createRouter, createWebHistory } from "vue-router";
import { authRoutes } from "./auth";
import { useUserStore } from "@/stores/user";
import { storeToRefs } from "pinia";
import { useBootstrapStore } from "@/stores/bootstrap";
import { activityRoute } from "./activity";
import { shareRequestsRoute } from "./share-requests";
import { sharedGroupsRoute } from "./shared-groups";
import { groupRoutes } from "./group";
import { profileRoutes } from "./profile";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      // The public marketing site owns the root path — logged-in visitors
      // are bounced straight to /dashboard by the requiresGuest guard below.
      path: "/",
      component: PublicLayout,
      children: [
        {
          path: "",
          name: "landing",
          component: () => import("@/pages/landing/Index.vue"),
          meta: {
            requiresGuest: true,
          },
        },
      ],
    },
    {
      path: "/app",
      name: "app",
      alias: ["/dashboard"],
      component: MainLayout,
      redirect: { name: "groups.index" },
      meta: {
        requiresAuth: true,
      },
      children: [
        groupRoutes,
        activityRoute,
        shareRequestsRoute,
        sharedGroupsRoute,
        profileRoutes,
      ],
    },
    authRoutes,
    {
      path: "/:pathMatch(.*)*",
      name: "not-found",
      component: () => import("@/components/app/AppNotFound.vue"),
    },
  ],
  scrollBehavior: () => {
    return {
      top: 0,
      behavior: "smooth",
    };
  },
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
    return { name: "groups.index" };
  }
});

export default router;
