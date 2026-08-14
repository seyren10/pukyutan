<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { formatDistanceToNow } from 'date-fns'
import { History, HandCoins, Coins, ArrowRight } from '@lucide/vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Skeleton } from '@/components/ui/skeleton'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { getRecentGroupActivitiesQueryOptions } from '@/features/group/query'
import type { GroupActivity } from '@/features/group/type'
import { Button } from '@/components/ui/button'

const RECENT_LIMIT = 5

const { groupId } = defineProps<{
    groupId: number
}>()

const { data, isPending } = useQuery(getRecentGroupActivitiesQueryOptions(() => groupId, RECENT_LIMIT))

const activities = computed(() => data.value?.data ?? [])

// subject_type is the fully-qualified model class (e.g. "App\Models\Cycle").
// Map by the class's short name rather than string-matching the FQCN, so
// adding a new auditable model (Member, GroupShare, ...) is a one-line
// addition here instead of another endsWith() branch.
const ICON_BY_SUBJECT: Record<string, typeof History> = {
    Cycle: HandCoins,
    Contribution: Coins,
}

function iconFor(activity: GroupActivity) {
    const shortName = activity.subject_type?.split('\\').pop()
    return (shortName && ICON_BY_SUBJECT[shortName]) ?? History
}
</script>

<template>
    <Card>
        <CardHeader class="flex items-center justify-between">
            <CardTitle class="font-heading text-lg">Recent activity</CardTitle>
            <RouterLink :to="{ name: 'groups.detail.activities.index' }">
                <Button variant="link">
                    <ArrowRight class="size-4" />
                    View all
                </Button>
            </RouterLink>
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