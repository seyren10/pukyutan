<script setup lang="ts">
import { computed, useTemplateRef } from 'vue'
import { useInfiniteQuery, useQuery } from '@tanstack/vue-query'
import { ArrowLeft, History } from '@lucide/vue'
import { Card, CardContent } from '@/components/ui/card'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { useGroupedActivities } from '@/features/activity/composables/use-grouped-activities.ts'
import ActivityRow from '@/components/activity/ActivityRow.vue'
import ActivityRowSkeleton from '@/components/activity/ActivityRowSkeleton.vue'
import { useIntersectionObserver } from '@vueuse/core'
import { getGroupActivitiesInfiniteQueryOptions, getGroupDetailQueryOptions } from '@/features/group/query'
import { useGroupDetail } from '@/features/group/composables/use-group'
import { Button } from '@/components/ui/button'


const { groupId } = defineProps<{
    groupId: number
}>()

const { data, isPending, fetchNextPage, hasNextPage, isFetchingNextPage } = useInfiniteQuery(getGroupActivitiesInfiniteQueryOptions(() => groupId))
const { data: groupData } = useQuery(getGroupDetailQueryOptions(() => groupId, groupId !== null))
const groupDetail = computed(() => groupData.value?.data)
const { name: groupName } = useGroupDetail(groupDetail)
const activities = computed(() => data.value?.pages?.flatMap((page) => page.data))
const dayGroups = useGroupedActivities(activities)


const sentinel = useTemplateRef('sentinel')
useIntersectionObserver(sentinel, ([entry]) => {
    if (entry?.isIntersecting && hasNextPage.value && !isFetchingNextPage.value) {
        fetchNextPage()
    }
}, {
    rootMargin: '200px'
})

</script>

<template>
    <div class="flex flex-col gap-6">
        <Button @click="$router.back()" variant="link" class="w-min" size="sm">
            <ArrowLeft class="size-3.5" />
            Back
        </Button>
        <div class="flex flex-col gap-1">
            <h1 class="font-heading text-xl font-semibold text-foreground capitalize">{{ groupName }} Activity</h1>
            <p class="text-sm text-muted-foreground">
                Everything that's happened this group.
            </p>
        </div>

        <Card>
            <CardContent class="py-2">
                <div v-if="isPending" class="flex flex-col divide-y divide-border">
                    <ActivityRowSkeleton v-for="i in 5" :key="i" />
                </div>

                <Empty v-else-if="!activities?.length" class="py-8">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <History />
                        </EmptyMedia>
                        <EmptyTitle>No activity yet</EmptyTitle>
                        <EmptyDescription>
                            Actions taken across to {{ groupName }} will show up here.
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>

                <div v-else class="flex flex-col">
                    <div v-for="group in dayGroups" :key="group.label" class="flex flex-col">
                        <p
                            class="sticky top-0 -mx-6 border-b border-border bg-card px-6 py-2 font-mono text-xs font-medium uppercase tracking-wide text-muted-foreground">
                            {{ group.label }}
                        </p>
                        <ul class="flex flex-col divide-y divide-border px-0">
                            <ActivityRow v-for="activity in group.items" :key="activity.id" :activity="activity" />
                        </ul>
                    </div>

                    <div ref="sentinel" class="h-px w-full" />

                    <div v-if="isFetchingNextPage" class="flex flex-col divide-y divide-border">
                        <ActivityRowSkeleton v-for="i in 3" :key="i" />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>