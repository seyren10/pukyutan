<script setup lang="ts">
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import { ChevronDown } from '@lucide/vue'
import { ref } from 'vue'

defineProps<{
    question: string
}>()

const open = ref(false)
</script>

<template>
    <Collapsible v-model:open="open" class="overflow-hidden rounded-xl border border-border bg-card">
        <CollapsibleTrigger
            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-heading text-base font-medium text-foreground transition-colors hover:bg-muted/40">
            {{ question }}
            <ChevronDown class="size-4 shrink-0 text-muted-foreground transition-transform duration-200"
                :class="{ 'rotate-180': open }" />
        </CollapsibleTrigger>
        <CollapsibleContent class="px-5 pb-4 text-sm text-muted-foreground">
            <slot />
        </CollapsibleContent>
    </Collapsible>
</template>