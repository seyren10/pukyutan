<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { useRouteQuery } from '@vueuse/router'
import { Inbox, ArrowLeft } from '@lucide/vue'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { Skeleton } from '@/components/ui/skeleton'
import AppPaginationBar from '@/components/app/AppPaginationBar.vue'
import PendingRequestRow from '@/components/share/PendingRequestRow.vue'
import { getPendingShareRequestsQueryOptions } from '@/features/share/query'

const page = useRouteQuery('page', null, { transform: Number })

const { data, isPending } = useQuery(getPendingShareRequestsQueryOptions(page))
const requests = computed(() => data.value?.data)
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <Button variant="link" class="w-min px-0" size="sm" @click="$router.back()">
                <ArrowLeft class="size-3.5" />
                Back
            </Button>
            <h1 class="font-heading text-xl font-semibold text-foreground">Pending requests</h1>
            <p class="text-sm text-muted-foreground">
                People asking to join a group you own — across every group at once.
            </p>
        </div>

        <Card>
            <CardHeader>
                <CardTitle class="font-heading text-lg">Requests</CardTitle>
                <CardDescription>Accepting grants view access right away.</CardDescription>
            </CardHeader>
            <CardContent>
                <div v-if="isPending" class="flex flex-col divide-y divide-border">
                    <div v-for="i in 4" :key="i" class="flex items-center gap-3 py-3">
                        <Skeleton class="size-9 shrink-0 rounded-full" />
                        <div class="flex flex-1 flex-col gap-1.5">
                            <Skeleton class="h-3.5 w-1/3" />
                            <Skeleton class="h-3 w-1/2" />
                        </div>
                    </div>
                </div>

                <Empty v-else-if="!requests?.length" class="py-8">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <Inbox />
                        </EmptyMedia>
                        <EmptyTitle>Nothing to review</EmptyTitle>
                        <EmptyDescription>
                            You're all caught up — no one's waiting on a response.
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>

                <ul v-else class="flex flex-col divide-y divide-border gap-2">
                    <li v-for="share in requests" :key="share.id" class="pb-2">
                        <PendingRequestRow :share="share" show-group />
                    </li>
                </ul>
            </CardContent>
        </Card>

        <AppPaginationBar v-if="data" :meta="data.meta" v-model="page" />
    </div>
</template>
