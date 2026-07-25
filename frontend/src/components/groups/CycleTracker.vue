<script setup lang="ts">
import { Hexagon } from '@lucide/vue'
import { cn } from '@/lib/utils'

// Fixed legend — see design doc section 5. Don't add new states here.
export type CycleState = 'disbursed' | 'current' | 'future'

withDefaults(
  defineProps<{
    cycles: { state: CycleState }[]
    /** "mini" = dashboard card strip. "full" = group detail page. */
    size?: 'mini' | 'full'
  }>(),
  {
    size: 'mini',
  },
)

const stateClass: Record<CycleState, string> = {
  disbursed: 'text-primary',
  current: 'text-accent-foreground',
  future: 'text-border',
}
</script>

<template>
  <div :class="cn('flex items-center', size === 'mini' ? 'gap-1' : 'gap-2')">
    <Hexagon
      v-for="(cycle, index) in cycles"
      :key="index"
      :class="cn(size === 'mini' ? 'size-5.5' : 'size-10', stateClass[cycle.state])"
      :fill="cycle.state === 'disbursed' ? 'currentColor' : 'none'"
      :stroke-width="cycle.state === 'future' ? 1.5 : 2"
    />
  </div>
</template>