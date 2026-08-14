<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { formatDistanceToNow } from 'date-fns'
import { History, HandCoins } from '@lucide/vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { getRecentGroupActivitiesQueryOptions } from '@/features/group/query'
import type { GroupActivity } from '@/features/group/type'

const RECENT_LIMIT = 5

const { groupId } = defineProps<{
    groupId: number
}>()

const { data, isPending } = useQuery(getRecentGroupActivitiesQueryOptions(() => groupId, RECENT_LIMIT))

const activities = computed(() => data.value?.data ?? [])

// Cycle-scoped logs (currently just disbursements — see CycleDisburseController)
// are mixed in alongside group-level ones, so they get their own icon rather
// than reading as unexplained duplicates of the group's own history.
function iconFor(activity: GroupActivity) {
    return activity.subject_type?.endsWith('Cycle') ? HandCoins : History
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-lg">Recent activity</CardTitle>
        </CardHeader>

        <CardContent class="flex flex-col gap-4">
            <div v-if="isPending" class="flex flex-col gap-4">
                <div v-for="i in 3" :key="i" class="flex items-start gap-3">
                    <Skeleton class="size-6 shrink-0 rounded-full" />
                    <div class="flex flex-1 flex-col gap-1.5">
                        <Skeleton class="h-3.5 w-3/4" />
                        <Skeleton class="h-3 w-20" />
                    </div>
                </div>
            </div>

            <Empty v-else-if="activities.length === 0" class="py-4">
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <History />
                    </EmptyMedia>
                    <EmptyTitle>No activity yet</EmptyTitle>
                    <EmptyDescription>Actions taken in this group will show up here.</EmptyDescription>
                </EmptyHeader>
            </Empty>

            <ul v-else class="flex flex-col divide-y divide-border">
                <li v-for="activity in activities" :key="activity.id"
                    class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
                    <div
                        class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-accent text-accent-foreground">
                        <component :is="iconFor(activity)" class="size-3.5" />
                    </div>
                    <div class="flex flex-1 flex-col gap-0.5">
                        <p class="text-sm text-foreground">{{ activity.description }}</p>
                        <span class="font-mono text-xs text-muted-foreground">
                            {{ formatDistanceToNow(new Date(activity.created_at), { addSuffix: true }) }}
                        </span>
                    </div>
                </li>
            </ul>
        </CardContent>
    </Card>
</template>