<script setup lang="ts">
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
    <Card>
        <CardHeader class="flex flex-row items-start justify-between gap-2">
            <div class="flex flex-col gap-2">
                <Skeleton class="h-5 w-28" />
                <div class="flex -space-x-2">
                    <Skeleton v-for="i in 3" :key="i" class="size-6 rounded-full border-2 border-card" />
                </div>
            </div>
            <Skeleton class="h-5 w-14 shrink-0 rounded-full" />
        </CardHeader>

        <CardContent class="flex flex-col gap-3">
            <div class="flex gap-1">
                <Skeleton v-for="i in 5" :key="i"
                    class="size-5.5 shrink-0 [clip-path:polygon(25%_0%,75%_0%,100%_50%,75%_100%,25%_100%,0%_50%)]" />
            </div>

            <div class="flex items-center justify-between">
                <Skeleton class="h-4 w-24" />
                <Skeleton class="h-4 w-20" />
            </div>
        </CardContent>

        <CardFooter>
            <Skeleton class="ml-auto h-8 w-24" />
        </CardFooter>
    </Card>
</template>