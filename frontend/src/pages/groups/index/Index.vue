<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { Progress } from '@/components/ui/progress'
import { Layers, Wallet, Users, Inbox, Plus, Trophy, CalendarClock, ListFilter, X } from '@lucide/vue'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import UnverifiedAlert from './components/UnverifiedAlert.vue'
import { useQuery } from '@tanstack/vue-query'
import { getGroupsQueryOptions, } from '@/features/group/query.ts'
import { getPendingShareRequestsQueryOptions } from '@/features/share/query'
import { getDashboardStatsQueryOptions } from '@/features/dashboard/query'
import { computed, ref, watch } from 'vue'
import { formatDate } from 'date-fns'
import { GroupCard, GroupCardEmpty, GroupCardSkeleton } from '@/components/groups/GroupCard'
import CreateGroupDialog from './components/CreateGroupDialog.vue'
import AddMemberDialog from './components/AddMemberDialog.vue'
import AppPaginationBar from '@/components/app/AppPaginationBar.vue'
import { useRouteQuery } from '@vueuse/router'
import type { Group, GroupStatus } from '@/features/group/type'
import GroupListFilters from './components/GroupListFilters.vue'
import { GROUP_SORT_OPTIONS } from '@/features/group/constant.ts'

const page = useRouteQuery('page', null, {
    transform: Number
})

const status = useRouteQuery<string>('status', 'all')
const sort = useRouteQuery<string>('sort', GROUP_SORT_OPTIONS[0].value)

// Reset back to page 1 whenever the filter/sort criteria change — staying
// on, say, page 3 after narrowing the list down would likely land on a
// page that no longer exists.
watch([status, sort], () => {
    page.value = 1
})

const sortParams = computed(() => {
    const match = GROUP_SORT_OPTIONS.find((option) => option.value === sort.value) ?? GROUP_SORT_OPTIONS[0]
    return { sort_by: match.sort_by, sort_dir: match.sort_dir }
})
const hasActiveFilters = computed(() => status.value !== 'all' || page.value > 1)

const { data, isPending } = useQuery(getGroupsQueryOptions(() => ({
    page: page.value,
    status: status.value !== 'all' ? (status.value as GroupStatus) : undefined,
    sort_by: sortParams.value.sort_by,
    sort_dir: sortParams.value.sort_dir,
})))

const groups = computed(() => data.value?.data);

// Owns the post-create "add members" hand-off for both the header's "New
// group" trigger and the empty-state's "Create a group" trigger below.
// Deliberately rendered outside the isPending/empty/grid branches further
// down, since the freshly-created group flips that branch (empty -> grid)
// as soon as the list refetches — nesting this dialog inside either branch
// would unmount it mid-flow before the user ever sees it.
const showAddMemberDialog = ref(false)
const createdGroup = ref<Group | null>(null)
const handleGroupCreated = (group: Group) => {
    createdGroup.value = group
    showAddMemberDialog.value = true
}

const clearFilters = () => {
    status.value = 'all'
    sort.value = GROUP_SORT_OPTIONS[0].value
    page.value = 1;
}

const { data: statsData, isPending: isStatsPending } = useQuery(getDashboardStatsQueryOptions())
const stats = computed(() => statsData.value?.data)

const collectedPercent = computed(() => {
    const cycle = stats.value?.current_cycle
    if (!cycle || cycle.expected_total <= 0) return 0
    return Math.min((cycle.collected_total / cycle.expected_total) * 100, 100)
})

const nextPayoutDueLabel = computed(() => {
    const dueDate = stats.value?.next_payout?.due_date
    return dueDate ? formatDate(new Date(dueDate), 'MMMM dd, yyyy') : ''
})

const userStore = useUserStore()
const { isEmailVerified } = storeToRefs(userStore)

const { data: pendingRequestsData } = useQuery(getPendingShareRequestsQueryOptions(() => ({ page: 1, per_page: 1 }), isEmailVerified.value))
const pendingRequestCount = computed(() => pendingRequestsData.value?.meta.total ?? 0)



</script>

<template>
    <div class="flex flex-col gap-6">
        <UnverifiedAlert :verified="isEmailVerified" />

        <Alert v-if="pendingRequestCount > 0" variant="accent">
            <Inbox />
            <AlertTitle>{{ pendingRequestCount }} pending join {{ pendingRequestCount === 1 ? 'request' : 'requests' }}
            </AlertTitle>
            <AlertDescription class="flex flex-wrap items-center justify-between gap-3">
                <span>Someone wants to view one of your groups. Review before they get access.</span>
                <Button as-child size="sm" variant="outline">
                    <RouterLink :to="{ name: 'share-requests.index' }">Review requests</RouterLink>
                </Button>
            </AlertDescription>
        </Alert>

        <!-- STATS -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3" v-if="isStatsPending">
            <Card v-for="n in 3" :key="n">
                <CardContent class="flex items-center gap-3 py-4">
                    <Skeleton class="size-9 shrink-0 rounded-md" />
                    <div class="flex flex-1 flex-col gap-1.5">
                        <Skeleton class="h-5 w-1/3" />
                        <Skeleton class="h-3 w-2/3" />
                    </div>
                </CardContent>
            </Card>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3" v-else-if="stats">
            <Card>
                <CardContent class="flex items-center gap-3 py-4">
                    <div
                        class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent text-accent-foreground">
                        <Layers class="size-4" />
                    </div>
                    <div class="flex flex-col">
                        <span class="font-mono text-lg font-medium text-foreground">{{ stats.groups.active }}</span>
                        <span class="text-xs text-muted-foreground">
                            Active {{ stats.groups.active === 1 ? 'group' : 'groups' }}
                            <template v-if="stats.groups.draft > 0"> · {{ stats.groups.draft }} in draft</template>
                        </span>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardContent class="flex flex-col gap-2.5 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent text-accent-foreground">
                            <Wallet class="size-4" />
                        </div>
                        <div class="flex flex-col">
                            <span class="font-mono text-lg font-medium text-foreground" v-if="stats.current_cycle">
                                ₱{{ stats.current_cycle.collected_total.toLocaleString('en-PH') }}
                                <span class="text-sm font-normal text-muted-foreground">/ ₱{{
                                    stats.current_cycle.expected_total.toLocaleString('en-PH') }}</span>
                            </span>
                            <span class="font-mono text-lg font-medium text-foreground" v-else>—</span>
                            <span class="text-xs text-muted-foreground">Collected this cycle</span>
                        </div>
                    </div>
                    <Progress v-if="stats.current_cycle" :model-value="collectedPercent" class="h-1.5" />
                </CardContent>
            </Card>

            <Card>
                <CardContent class="flex items-center gap-3 py-4">
                    <div
                        class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent text-accent-foreground">
                        <Users class="size-4" />
                    </div>
                    <div class="flex flex-col">
                        <span class="font-mono text-lg font-medium text-foreground">{{ stats.members_total }}</span>
                        <span class="text-xs text-muted-foreground">Members across groups</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- NEXT PAYOUT — the one thing most worth surfacing above everything else:
             who gets paid next, and when, across every active group. -->
        <Card v-if="stats?.next_payout" class="border-accent bg-accent/30">
            <CardContent class="flex flex-wrap items-center justify-between gap-4 py-4">
                <div class="flex items-center gap-3">
                    <div
                        class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent-foreground/10 text-accent-foreground">
                        <Trophy class="size-4" />
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-foreground">
                            {{ stats.next_payout.recipient_name }} is next up in "{{ stats.next_payout.group_name }}"
                        </span>
                        <span class="flex items-center gap-1.5 text-xs text-muted-foreground">
                            <CalendarClock class="size-3" />
                            ₱{{ stats.next_payout.amount.toLocaleString('en-PH') }} due {{ nextPayoutDueLabel }}
                        </span>
                    </div>
                </div>
                <Button as-child size="sm" variant="outline">
                    <RouterLink :to="{ name: 'groups.detail', params: { id: stats.next_payout.group_id } }">
                        View group
                    </RouterLink>
                </Button>
            </CardContent>
        </Card>

        <!-- YOUR GROUPS -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-lg font-semibold text-foreground">Your groups</h2>
                <CreateGroupDialog @add-members="handleGroupCreated" v-if="isEmailVerified">
                    <Button size="sm">
                        <Plus data-icon="inline-start" />
                        New group
                    </Button>
                </CreateGroupDialog>
            </div>

            <GroupListFilters v-model:status="status" v-model:sort="sort"
                v-if="!isPending && (groups?.length || hasActiveFilters)" />


            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" v-if="isPending">
                <GroupCardSkeleton />
                <GroupCardSkeleton />
                <GroupCardSkeleton />
            </div>
            <GroupCardEmpty v-else-if="!groups?.length && hasActiveFilters" title="No groups match this filter"
                description="Try a different status, or clear the filter to see all your groups.">
                <template #icon>
                    <ListFilter />
                </template>
                <template #action>
                    <Button variant="outline" @click="clearFilters">
                        <X data-icon="inline-start" />
                        Clear filter
                    </Button>
                </template>
            </GroupCardEmpty>
            <GroupCardEmpty v-else-if="!groups?.length">
                <template #action>
                    <CreateGroupDialog @add-members="handleGroupCreated" v-if="isEmailVerified">
                        <Button>
                            <Plus data-icon="inline-start" />
                            Create a group
                        </Button>
                    </CreateGroupDialog>
                </template>
            </GroupCardEmpty>
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <GroupCard v-for="group in groups" :key="group.id" :group="group" />

                <div class="col-span-full">
                    <AppPaginationBar :meta="data?.meta" v-if="data" v-model="page" />
                </div>
            </div>

            <AddMemberDialog v-model="showAddMemberDialog" :group="createdGroup" v-if="createdGroup" />
        </div>
    </div>
</template>