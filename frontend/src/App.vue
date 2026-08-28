<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
import 'vue-sonner/style.css'
import { Toaster } from './components/ui/sonner';
import { TooltipProvider } from './components/ui/tooltip';
import { useBootstrapStore } from './stores/bootstrap';
import { storeToRefs } from 'pinia';
import AppSplashScreen from './components/app/AppSplashScreen.vue';
import { useColorMode } from '@vueuse/core';
import { Analytics } from '@vercel/analytics/vue';

useColorMode()
const bootstrapStore = useBootstrapStore()
const { isBootstrapping } = storeToRefs(bootstrapStore)
</script>

<template>
  <AppSplashScreen v-if="isBootstrapping" />
  <div v-else>
    <TooltipProvider>
      <RouterView />
    </TooltipProvider>
    <Toaster rich-colors />
    <VueQueryDevtools />
    <Analytics />
  </div>
</template>
