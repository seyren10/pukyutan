<script setup lang="ts">
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Hexagon } from '@lucide/vue';
import { computed } from 'vue';

const { currentCycle, cyclesCount, maxCycleDisplay = 5 } = defineProps<{
    cyclesCount: number,
    currentCycle?: number,
    maxCycleDisplay?: number
}>()

const finalCycleCount = computed(() => Math.min(maxCycleDisplay, cyclesCount))
const currentCycleWithDefault = computed(() => currentCycle ?? finalCycleCount.value + 1)

const remainingCyclesCount = computed(() => {
    return cyclesCount - finalCycleCount.value
})
</script>
<template>
    <div class="flex gap-1 flex-wrap">
        <Hexagon v-for="cycle in finalCycleCount" :key="cycle" class="size-5.5 shrink-0" :class="{
            'fill-primary text-primary': cycle < currentCycleWithDefault,
            'fill-transparent text-accent-foreground': cycle === currentCycleWithDefault,
            'fill-transparent text-border': cycle > currentCycleWithDefault,
        }" :stroke-width="cycle === currentCycleWithDefault ? 2.5 : 1.5" />
        <Popover v-if="remainingCyclesCount">
            <PopoverTrigger as-child>
                <div class="relative">
                    <Hexagon class="fill-transparent stroke-border" />
                    <span
                        class="pointer-events-none absolute inset-0 flex items-center justify-center font-mono text-[10px] font-medium">+{{
                            remainingCyclesCount }}</span>
                </div>
            </PopoverTrigger>
            <PopoverContent class="max-w-fit">
                <GroupCardCycleVisual :current-cycle="currentCycleWithDefault - finalCycleCount"
                    :cycles-count="remainingCyclesCount" :max-cycle-display="remainingCyclesCount" />
            </PopoverContent>
        </Popover>
    </div>
</template>

<style scoped></style>