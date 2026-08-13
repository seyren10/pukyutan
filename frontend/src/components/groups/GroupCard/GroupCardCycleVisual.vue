<script setup lang="ts">
import { Hexagon } from '@lucide/vue';
import { computed } from 'vue';

const { currentCycle, cyclesCount } = defineProps<{
    cyclesCount: number,
    currentCycle?: number
}>()

const currentCycleWithDefault = computed(() => currentCycle || cyclesCount + 1)
</script>
<template>
    <div class="flex gap-1 flex-wrap">
        <Hexagon v-for="cycle in cyclesCount" :key="cycle" class="size-5.5 shrink-0" :class="{
            'fill-primary text-primary': cycle < currentCycleWithDefault,
            'fill-transparent text-accent-foreground': cycle === currentCycleWithDefault,
            'fill-transparent text-border': cycle > currentCycleWithDefault,
        }" :stroke-width="cycle === currentCycleWithDefault ? 2.5 : 1.5" />
    </div>
</template>

<style scoped></style>