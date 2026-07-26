<script setup lang="ts">
import { Hexagon } from '@lucide/vue'
import { useColorMode } from '@vueuse/core';
useColorMode()
</script>


<template>
  <div class="min-h-svh flex flex-col items-center justify-center gap-8 bg-background px-4">
    <div class="flex items-center gap-2">
      <Hexagon class="size-6 fill-primary text-primary" />
      <span class="font-heading text-xl font-semibold text-foreground">pukyutan</span>
    </div>

    <div class="w-full max-w-sm">
      <RouterView />
    </div>
  </div>
</template>