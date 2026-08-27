<script setup lang="ts">
import { AccordionContent, AccordionItem, AccordionTrigger } from '@/components/ui/accordion';

defineProps<{
    question: string
}>()

</script>

<template>
    <AccordionItem :value="question">
        <AccordionTrigger>
            {{ question }}
        </AccordionTrigger>
        <AccordionContent class="px-5 pb-4 text-sm text-muted-foreground">
            <slot />
        </AccordionContent>
    </AccordionItem>
</template>