<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Layers, Wallet, Users, Inbox, Plus, ListFilter, X } from '@lucide/vue'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import UnverifiedAlert from './components/UnverifiedAlert.vue'
import { useQuery } from '@tanstack/vue-query'
import { getGroupsQueryOptions, } from '@/features/group/query.ts'
import { getPendingShareRequestsQueryOptions } from '@/features/share/query'
import { GROUP_SORT_OPTIONS } from '@/features/group/constant'
import { computed, ref, watch } from 'vue'
import { GroupCard, GroupCardEmpty, GroupCardSkeleton } from '@/components/groups/GroupCard'
import CreateGroupDialog from './components/CreateGroupDialog.vue'
import AddMemberDialog from './components/AddMemberDialog.vue'
import GroupListFilters from './components/GroupListFilters.vue'
import AppPaginationBar from '@/components/app/AppPaginationBar.vue'
import { useRouteQuery } from '@vueuse/router'
import type { Group, GroupStatus } from '@/features/group/type'

const page = useRouteQuery('page', 1, {
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
const hasActiveFilters = computed(() => status.value !== 'all')

const { data, isPending } = useQuery(getGroupsQueryOptions(() => ({
    page: page.value,
    status: status.value !== 'all' ? (status.value as GroupStatus) : undefined,
    sort_by: sortParams.value.sort_by,
    sort_dir: sortParams.value.sort_dir,
})))
const groups = computed(() => data.value?.data);

const clearFilters = () => {
    status.value = 'all'
    sort.value = GROUP_SORT_OPTIONS[0].value
}

const showAddMemberDialog = ref(false)
const pendingNewGroup = ref<Group | null>(null)

const handleGroupCreated = (group: Group, { addMembers }: { addMembers: boolean }) => {
    if (!addMembers) return
    pendingNewGroup.value = group
    showAddMemberDialog.value = true
}


const stats = [
    { label: 'Active groups', value: '2', icon: Layers },
    { label: 'Collected this cycle', value: '₱3,200', icon: Wallet },
    { label: 'Members across groups', value: '11', icon: Users },
]

// per_page: 1 — this call only ever reads meta.total, the same endpoint the
// full inbox page (/requests) later fetches for real.
const { data: pendingRequestsData } = useQuery(getPendingShareRequestsQueryOptions(1, 1))
const pendingRequestCount = computed(() => pendingRequestsData.value?.meta.total ?? 0)

const userStore = useUserStore()
const { isEmailVerified } = storeToRefs(userStore)

</script>

<template>
    <div class="flex flex-col gap-6">
        <UnverifiedAlert :verified="isEmailVerified" />

        <Alert v-if="pendingRequestCount > 0" variant="accent">
            <Inbox />
            <AlertTitle>{{ pendingRequestCount }} pending join {{ pendingRequestCount === 1 ? 'request' : 'requests' }}
            </AlertTitle>
            <AlertDescription class="flex items-center justify-between gap-4">
                <span>Someone wants to view one of your groups. Review before they get access.</span>
                <Button as-child size="sm" variant="outline">
                    <RouterLink :to="{ name: 'share-requests.index' }">Review requests</RouterLink>
                </Button>
            </AlertDescription>
        </Alert>

        <!-- STATS -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <Card v-for="stat in stats" :key="stat.label">
                <CardContent class="flex items-center gap-3 py-4">
                    <div
                        class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent text-accent-foreground">
                        <component :is="stat.icon" class="size-4" />
                    </div>
                    <div class="flex flex-col">
                        <span class="font-mono text-lg font-medium text-foreground">{{ stat.value }}</span>
                        <span class="text-xs text-muted-foreground">{{ stat.label }}</span>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- YOUR GROUPS -->
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-lg font-semibold text-foreground">Your groups</h2>
                <CreateGroupDialog @created="handleGroupCreated" v-if="isEmailVerified">
                    <Button size="sm">
                        <Plus data-icon="inline-start" />
                        New group
                    </Button>
                </CreateGroupDialog>
            </div>

            <!-- Stays visible even when the current filter matches zero groups
                 (as long as some groups exist at all) so the person can switch
                 straight to a different status instead of hitting a dead end. -->
            <GroupListFilters v-model:status="status" v-model:sort="sort"
                v-if="!isPending && (groups?.length || hasActiveFilters)" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" v-if="isPending">
                <GroupCardSkeleton />
                <GroupCardSkeleton />
                <GroupCardSkeleton />
            </div>
            <!-- Filters narrowed a real, non-empty group list down to nothing —
                 distinct from having no groups at all, so this points at
                 clearing the filter instead of creating a group. -->
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
                    <CreateGroupDialog @created="handleGroupCreated" v-if="isEmailVerified">
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

            <!-- Lives at this root level (a sibling of the empty/populated
                 branches above), not nested inside either — so it survives
                 the empty state being swapped out for the populated grid the
                 instant the groups list refetches after creation. -->
            <AddMemberDialog v-model="showAddMemberDialog" :group="pendingNewGroup" v-if="pendingNewGroup" />
        </div>
    </div>
</template>