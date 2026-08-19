<script setup lang="ts">
import { computed, ref } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { UserRoundCheck, Hourglass, Users2, ArrowLeft } from '@lucide/vue'
import { Card, CardContent } from '@/components/ui/card'
import { ToggleGroup, ToggleGroupItem } from '@/components/ui/toggle-group'
import { Badge } from '@/components/ui/badge'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { Skeleton } from '@/components/ui/skeleton'
import PendingRequestRow from '@/components/share/PendingRequestRow.vue'
import CollaboratorRow from '@/components/share/CollaboratorRow.vue'
import { getGroupShareRequestsQueryOptions } from '@/features/share/query'
import type { GroupShareStatus } from '@/features/share/type'
import { Button } from '@/components/ui/button'

const { groupId } = defineProps<{
    groupId: number
}>()

const tab = ref<Extract<GroupShareStatus, 'pending' | 'accepted'>>('pending')

// reka-ui's single-select toggle group deselects to an empty string if you
// click the already-active item — this tab should always have one selected.
const tabModel = computed({
    get: () => tab.value,
    set: (value: string) => {
        if (value === 'pending' || value === 'accepted') tab.value = value
    },
})

const { data, isPending } = useQuery(
    getGroupShareRequestsQueryOptions(() => groupId, () => tab.value),
)
const shares = computed(() => data.value?.data)
</script>

<template>
    <div class="flex flex-col gap-4">
        <Button variant="link" size="sm" as-child class="w-fit">
            <RouterLink :to="{ name: 'groups.detail', params: { id: groupId } }" replace>
                <ArrowLeft />
                Back
            </RouterLink>
        </Button>
        <div class="flex items-center justify-between gap-3">
            <div class="flex flex-col gap-0.5">
                <h2 class="font-heading text-lg font-semibold text-foreground">Access</h2>
                <p class="text-sm text-muted-foreground">Review requests and manage who can view this group.</p>
            </div>

            <ToggleGroup v-model="tabModel" type="single" variant="outline">
                <ToggleGroupItem value="pending" class="gap-1.5 px-3">
                    <Hourglass class="size-3.5" />
                    Pending
                </ToggleGroupItem>
                <ToggleGroupItem value="accepted" class="gap-1.5 px-3">
                    <Users2 class="size-3.5" />
                    Collaborators
                </ToggleGroupItem>
            </ToggleGroup>
        </div>

        <Card>
            <CardContent>
                <div v-if="isPending" class="flex flex-col divide-y divide-border">
                    <div v-for="i in 3" :key="i" class="flex items-center gap-3 py-3">
                        <Skeleton class="size-9 shrink-0 rounded-full" />
                        <div class="flex flex-1 flex-col gap-1.5">
                            <Skeleton class="h-3.5 w-1/3" />
                            <Skeleton class="h-3 w-1/2" />
                        </div>
                    </div>
                </div>

                <Empty v-else-if="!shares?.length" class="py-8">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <component :is="tab === 'pending' ? Hourglass : UserRoundCheck" />
                        </EmptyMedia>
                        <EmptyTitle>
                            {{ tab === 'pending' ? 'No pending requests' : 'No collaborators yet' }}
                        </EmptyTitle>
                        <EmptyDescription>
                            {{ tab === 'pending'
                                ? "Share your invite code and requests to join will show up here."
                                : "Accepted requests will show up here — you'll be able to remove access anytime." }}
                        </EmptyDescription>
                    </EmptyHeader>
                </Empty>

                <ul v-else class="flex flex-col divide-y divide-border gap-2">
                    <li v-for="share in shares" :key="share.id" class="pb-2">
                        <PendingRequestRow v-if="tab === 'pending'" :share="share" />
                        <CollaboratorRow v-else :share="share" />
                    </li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
