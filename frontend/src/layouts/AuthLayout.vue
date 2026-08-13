<script setup lang="ts">
import Logo from '@/assets/logo.svg'
</script>


<template>
  <div class="min-h-svh flex flex-col items-center justify-center gap-8 bg-background px-4">
    <div class="flex items-center gap-2">
      <img :src="Logo" class="size-10" />
      <span class="font-heading text-xl font-semibold text-foreground">Puyo</span>
    </div>

    <div class="w-full max-w-sm">
      <RouterView />
    </div>
  </div>
</template>