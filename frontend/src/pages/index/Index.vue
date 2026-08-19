<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Layers, Wallet, Users, Inbox, Plus } from '@lucide/vue'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import UnverifiedAlert from './components/UnverifiedAlert.vue'
import { useQuery } from '@tanstack/vue-query'
import { getGroupsQueryOptions, } from '@/features/group/query.ts'
import { getPendingShareRequestsQueryOptions } from '@/features/share/query'
import { computed, ref } from 'vue'
import { GroupCard, GroupCardEmpty, GroupCardSkeleton } from '@/components/groups/GroupCard'
import CreateGroupDialog from './components/CreateGroupDialog.vue'
import AddMemberDialog from './components/AddMemberDialog.vue'
import AppPaginationBar from '@/components/app/AppPaginationBar.vue'
import { useRouteQuery } from '@vueuse/router'
import type { Group } from '@/features/group/type'

const page = useRouteQuery('page', null, {
    transform: Number,
})
const { data, isPending } = useQuery(getGroupsQueryOptions(() => ({ page: page.value })))
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
                <CreateGroupDialog @add-members="handleGroupCreated" v-if="isEmailVerified">
                    <Button size="sm">
                        <Plus data-icon="inline-start" />
                        New group
                    </Button>
                </CreateGroupDialog>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" v-if="isPending">
                <GroupCardSkeleton />
                <GroupCardSkeleton />
                <GroupCardSkeleton />
            </div>
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

        <!-- SHARED GROUPS
        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-lg font-semibold text-foreground">Shared with you</h2>
                <JoinGroupDialog>
                    <Button size="sm" variant="outline">
                        <Ticket data-icon="inline-start" />
                        Join a group
                    </Button>
                </JoinGroupDialog>
            </div>

            <Card v-if="isSharedGroupsPending">
                <CardContent class="flex items-center gap-3 py-4">
                    <Skeleton class="size-9 shrink-0 rounded-md" />
                    <div class="flex flex-1 flex-col gap-1.5">
                        <Skeleton class="h-3.5 w-1/3" />
                        <Skeleton class="h-3 w-1/2" />
                    </div>
                </CardContent>
            </Card>
            <Card v-else>
                <CardContent class="flex items-center justify-between gap-4 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex size-9 shrink-0 items-center justify-center rounded-md bg-accent text-accent-foreground">
                            <Users2 class="size-4" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-foreground">
                                {{ sharedGroupsCount > 0
                                    ? `${sharedGroupsCount} ${sharedGroupsCount === 1 ? 'group' : 'groups'} shared with you`
                                    : 'No groups shared with you yet' }}
                            </span>
                            <span class="text-xs text-muted-foreground">
                                Groups other people have given you view access to.
                            </span>
                        </div>
                    </div>
                    <Button as-child size="sm" variant="outline">
                        <RouterLink :to="{ name: 'shared-groups.index' }">
                            View all
                            <ChevronRight data-icon="inline-end" />
                        </RouterLink>
                    </Button>
                </CardContent>
            </Card>
        </div> -->
    </div>
</template>
