<script setup lang="ts">
import { DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Skeleton } from '@/components/ui/skeleton'
import { Separator } from '@/components/ui/separator'
</script>

<template>
    <DialogContent class="sm:max-w-lg">
        <DialogHeader>
            <DialogTitle class="sr-only">Loading cycle details</DialogTitle>
            <DialogDescription class="sr-only">Please wait while we load the cycle details.</DialogDescription>
            <Skeleton class="h-6 w-40" />
            <Skeleton class="h-4 w-56" />
        </DialogHeader>

        <div class="flex flex-col gap-3 rounded-lg border border-border bg-muted/40 p-3">
            <div class="flex items-center justify-between">
                <Skeleton class="h-4 w-36" />
                <Skeleton class="h-4 w-24" />
            </div>

            <Separator />

            <div class="flex flex-col gap-2">
                <div class="flex items-center justify-between">
                    <Skeleton class="h-4 w-28" />
                    <Skeleton class="h-4 w-32" />
                </div>
                <Skeleton class="h-2 w-full rounded-full" />
            </div>
        </div>
    </DialogContent>
</template>

<style scoped></style>