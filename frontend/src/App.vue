<script setup lang="ts">
import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
import 'vue-sonner/style.css'
import { Toaster } from './components/ui/sonner';
</script>

<template>
  <div>
    <RouterView />
    <Toaster />
    <VueQueryDevtools />
  </div>
</template>
