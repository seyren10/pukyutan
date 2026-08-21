<script setup lang="ts">
import { PawPrint } from '@lucide/vue'

const { prompt, class: className = '' } = defineProps<{
    /** The AI image-generation prompt to use for this spot. Shown as a caption. */
    prompt: string
    class?: string
}>()
</script>

<template>
    <div :class="[
        'flex flex-col items-center justify-center gap-3 rounded-3xl border-2 border-dashed border-accent-foreground/25 bg-accent/30 p-6 text-center',
        className
    ]">
        <PawPrint class="size-8 text-accent-foreground/50" />
        <div class="space-y-1">
            <p class="text-xs font-medium tracking-wide text-accent-foreground/70 uppercase">Mascot placeholder</p>
            <p class="mx-auto max-w-xs font-mono text-xs text-muted-foreground">{{ prompt }}</p>
        </div>
    </div>
</template>