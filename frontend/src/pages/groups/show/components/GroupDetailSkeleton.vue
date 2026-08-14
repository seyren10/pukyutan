<script setup lang="ts">
import { Card, CardContent, CardHeader } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-5 rounded-2xl border border-border bg-card p-5 shadow-sm sm:flex-row sm:items-start sm:justify-between sm:p-6">
            <div class="flex items-start gap-4">
                <Skeleton class="size-14 shrink-0 [clip-path:polygon(25%_0%,75%_0%,100%_50%,75%_100%,25%_100%,0%_50%)] sm:size-16" />
                <div class="flex flex-col gap-3">
                    <Skeleton class="h-7 w-40" />
                    <div class="flex gap-3">
                        <Skeleton class="h-4 w-28" />
                        <Skeleton class="h-4 w-24" />
                        <Skeleton class="h-4 w-20" />
                    </div>
                </div>
            </div>
            <Skeleton class="h-8 w-28 rounded-full" />
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card v-for="i in 3" :key="i">
                <CardContent class="flex items-center gap-3 py-4">
                    <Skeleton class="size-9 shrink-0 rounded-md" />
                    <div class="flex flex-col gap-1.5">
                        <Skeleton class="h-5 w-16" />
                        <Skeleton class="h-3 w-20" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="flex flex-col gap-6 lg:col-span-2">
                <Card>
                    <CardContent class="flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <Skeleton class="h-5 w-24" />
                            <Skeleton class="h-4 w-16" />
                        </div>
                        <Skeleton class="h-28 w-full rounded-lg" />
                        <div class="flex gap-2">
                            <Skeleton class="h-9 flex-1" />
                            <Skeleton class="h-9 flex-1" />
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <Skeleton class="h-5 w-32" />
                        <Skeleton class="h-4 w-48" />
                    </CardHeader>
                    <CardContent class="flex flex-wrap gap-3">
                        <Skeleton v-for="i in 8" :key="i"
                            class="size-11 shrink-0 [clip-path:polygon(25%_0%,75%_0%,100%_50%,75%_100%,25%_100%,0%_50%)]" />
                    </CardContent>
                </Card>
            </div>

            <div class="flex flex-col gap-6">
                <Card>
                    <CardHeader>
                        <Skeleton class="h-5 w-20" />
                        <Skeleton class="h-4 w-36" />
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div v-for="i in 4" :key="i" class="flex items-center gap-3">
                            <Skeleton class="size-7 shrink-0 rounded-full" />
                            <Skeleton class="size-8 shrink-0 rounded-full" />
                            <div class="flex flex-1 flex-col gap-1.5">
                                <Skeleton class="h-3.5 w-24" />
                                <Skeleton class="h-3 w-32" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <Skeleton class="h-5 w-28" />
                        <Skeleton class="h-4 w-40" />
                    </CardHeader>
                    <CardContent class="flex flex-col gap-4">
                        <div v-for="i in 3" :key="i" class="flex items-start gap-3">
                            <Skeleton class="size-6 shrink-0 rounded-full" />
                            <div class="flex flex-1 flex-col gap-1.5">
                                <Skeleton class="h-3.5 w-3/4" />
                                <Skeleton class="h-3 w-20" />
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>