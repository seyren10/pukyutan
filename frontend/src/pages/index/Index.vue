<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Layers, Wallet, Users, Inbox, Plus } from '@lucide/vue'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import UnverifiedAlert from './components/UnverifiedAlert.vue'
import { useInfiniteQuery, useQuery } from '@tanstack/vue-query'
import { getGroupsQueryOptions, getSharedGroupsInfiniteQueryOptions } from '@/features/group/query.ts'
import { computed, ref } from 'vue'
import { GroupCard, GroupCardEmpty, GroupCardSkeleton } from '@/components/groups/GroupCard'
import CreateGroupDialog from './components/CreateGroupDialog.vue'
import AddMemberDialog from './components/AddMemberDialog.vue'
import AppPaginationBar from '@/components/app/AppPaginationBar.vue'
import { useRouteQuery } from '@vueuse/router'

const page = useRouteQuery('page', null, {
    transform: Number
})
const { data, isPending } = useQuery(getGroupsQueryOptions(() => ({ page: page.value })))
const groups = computed(() => data.value?.data);

const { data: sharedGroupsData, isPending: isSharedGroupsPending } = useInfiniteQuery(getSharedGroupsInfiniteQueryOptions())
const sharedGroups = computed(() => sharedGroupsData.value?.pages?.flatMap(g => g.data))

const showAddMemberDialog = ref(false)


const stats = [
    { label: 'Active groups', value: '2', icon: Layers },
    { label: 'Collected this cycle', value: '₱3,200', icon: Wallet },
    { label: 'Members across groups', value: '11', icon: Users },
]

const pendingRequestCount = 2

const userStore = useUserStore()
const { isEmailVerified } = storeToRefs(userStore)

</script>

<template>
    <div class="flex flex-col gap-6">
        <UnverifiedAlert :verified="isEmailVerified" />

        <Alert v-if="pendingRequestCount > 0" variant="accent">
            <Inbox />
            <AlertTitle>{{ pendingRequestCount }} pending join requests</AlertTitle>
            <AlertDescription class="flex items-center justify-between gap-4">
                <span>Someone wants to view one of your groups. Review before they get access.</span>
                <Button size="sm" variant="outline">Review requests</Button>
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
                <CreateGroupDialog #="{ group }" @add-members="showAddMemberDialog = true">
                    <Button size="sm" v-if="isEmailVerified">
                        <Plus data-icon="inline-start" />
                        New group
                    </Button>

                    <AddMemberDialog v-model="showAddMemberDialog" :group="group" v-if="group" />
                </CreateGroupDialog>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" v-if="isPending">
                <GroupCardSkeleton />
                <GroupCardSkeleton />
                <GroupCardSkeleton />
            </div>
            <GroupCardEmpty v-else-if="!groups?.length" />
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <GroupCard v-for="group in groups" :key="group.id" :group="group" />

                <div class="col-span-full">
                    <AppPaginationBar :meta="data?.meta" v-if="data" v-model="page" />
                </div>
            </div>
        </div>

        <!-- SHARED GROUPS -->
        <div class="flex flex-col gap-3">
            <h2 class="font-heading text-lg font-semibold text-foreground">Shared with you</h2>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" v-if="isSharedGroupsPending">
                <GroupCardSkeleton />
                <GroupCardSkeleton />
                <GroupCardSkeleton />
            </div>
            <GroupCardEmpty v-else-if="!groups?.length" />
            <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <GroupCard v-for="group in sharedGroups" :key="group.id" :group="group" />
            </div>
        </div>
    </div>
</template>