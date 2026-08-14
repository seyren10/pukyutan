<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
    <div class="flex items-start gap-3 py-3">
        <Skeleton class="size-8 shrink-0 rounded-full" />
        <div class="flex flex-1 flex-col gap-1.5">
            <Skeleton class="h-3.5 w-3/4" />
            <Skeleton class="h-3 w-16" />
        </div>
    </div>
</template>