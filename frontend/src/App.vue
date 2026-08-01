<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
import 'vue-sonner/style.css'
import { Toaster } from './components/ui/sonner';
import { TooltipProvider } from './components/ui/tooltip';
</script>

<template>
  <div>
    <TooltipProvider>
      <RouterView />
    </TooltipProvider>
    <Toaster />
    <VueQueryDevtools />
  </div>
</template>
