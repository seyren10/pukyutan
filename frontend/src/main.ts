import { createApp } from "vue";
import App from "./App.vue";
import {
  VueQueryPlugin,
  type VueQueryPluginOptions,
} from "@tanstack/vue-query";
import { queryClient } from "./services/tanstack-query/query-client";
import "./style.css";
import { createPinia } from "pinia";
import router from "./router";
import { inject } from "@vercel/analytics";

const vueQueryOptions: VueQueryPluginOptions = {
  queryClient,
};

const pinia = createPinia();
const app = createApp(App);

app.use(VueQueryPlugin, vueQueryOptions);
app.use(router);
app.use(pinia);
inject();
app.mount("#app");
