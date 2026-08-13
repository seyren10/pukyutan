<script setup lang="ts">
import PuyoSplash from '@/assets/puyo_splash.webp'
import { LoaderCircle } from '@lucide/vue'
</script>

<template>
    <div role="status" aria-live="polite" class="grid h-dvh place-content-center gap-4 bg-background px-4 text-center">
        <div class="flex flex-col items-center gap-4">
            <img :src="PuyoSplash" alt="" class="w-32 animate-bob md:w-44" />

            <h1 class="font-heading text-3xl font-semibold text-foreground md:text-5xl">
                puyo
            </h1>

            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <LoaderCircle class="size-4 animate-spin" />
                <span>Getting things ready</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes bob {

    0%,
    100% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-6px);
    }
}

.animate-bob {
    animation: bob 2.5s ease-in-out infinite;
}
</style>