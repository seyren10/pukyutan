<script setup lang="ts">
import { Hexagon } from '@lucide/vue';

const { currentCycle, cyclesCount } = defineProps<{
    cyclesCount: number,
    currentCycle: number
}>()
</script>
<template>
    <div class="flex gap-1">
        <Hexagon v-for="cycle in cyclesCount" :key="cycle" class="size-5.5 shrink-0" :class="{
            'fill-primary text-primary': cycle < currentCycle,
            'fill-transparent text-accent-foreground': cycle === currentCycle,
            'fill-transparent text-border': cycle > currentCycle,
        }" :stroke-width="cycle === currentCycle ? 2.5 : 1.5" />
    </div>
</template>

<style scoped></style>