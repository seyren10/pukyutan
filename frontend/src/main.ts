import { createApp } from "vue";
import App from "./App.vue";
import {
  VueQueryPlugin,
  type VueQueryPluginOptions,
} from "@tanstack/vue-query";
import { queryClient } from "./services/tanstack-query/query-client";
import "./style.css";
import { createPinia } from "pinia";
import { useUserStore } from "./stores/user.ts";
import router from "./router";

const vueQueryOptions: VueQueryPluginOptions = {
  queryClient,
};

const pinia = createPinia();
const app = createApp(App);

app.use(VueQueryPlugin, vueQueryOptions);
app.use(pinia);

useUserStore()
  .bootstrap()
  .finally(async () => {
    app.use(router);
    await router.isReady();
    app.mount("#app");
  });
