<script setup lang="ts">
import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
import { PackageOpen } from '@lucide/vue';

const { title = 'No groups yet', description = 'Create your first paluwagan group to get started.' } = defineProps<{
    title?: string
    description?: string
}>()
</script>
<template>
    <Empty>
        <EmptyHeader>
            <EmptyMedia variant="icon">
                <slot name="icon">
                    <PackageOpen />
                </slot>
            </EmptyMedia>
            <EmptyTitle>{{ title }}</EmptyTitle>
            <EmptyDescription>{{ description }}</EmptyDescription>
        </EmptyHeader>
        <EmptyContent>
            <slot name="action" />
        </EmptyContent>
    </Empty>
</template>


<style scoped></style>
