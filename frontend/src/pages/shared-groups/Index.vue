<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { useRouteQuery } from '@vueuse/router'
import { Ticket } from '@lucide/vue'
import { Button } from '@/components/ui/button'
import AppPaginationBar from '@/components/app/AppPaginationBar.vue'
import { GroupCard, GroupCardEmpty, GroupCardSkeleton } from '@/components/groups/GroupCard'
import { getSharedGroupsQueryOptions } from '@/features/group/query'
import JoinGroupDialog from '@/pages/groups/index/components/JoinGroupDialog.vue'

const page = useRouteQuery('page', null, { transform: Number })

const { data, isPending } = useQuery(getSharedGroupsQueryOptions(() => ({ page: page.value })))
const sharedGroups = computed(() => data.value?.data)
</script>

<template>
    <div class="flex flex-col gap-6">
        <div class="flex flex-col gap-1">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex min-w-0 flex-col gap-1">
                    <h1 class="font-heading text-xl font-semibold text-foreground">Shared with you</h1>
                    <p class="text-sm text-muted-foreground">
                        Groups other people have given you view access to.
                    </p>
                </div>
                <JoinGroupDialog>
                    <Button size="sm" variant="outline" class="shrink-0">
                        <Ticket data-icon="inline-start" />
                        Join a group
                    </Button>
                </JoinGroupDialog>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3" v-if="isPending">
            <GroupCardSkeleton />
            <GroupCardSkeleton />
            <GroupCardSkeleton />
        </div>

        <GroupCardEmpty v-else-if="!sharedGroups?.length" title="No shared groups yet"
            description="Ask a group owner for their invite code, then join to see it here.">
            <template #action>
                <JoinGroupDialog>
                    <Button>
                        <Ticket data-icon="inline-start" />
                        Join a group
                    </Button>
                </JoinGroupDialog>
            </template>
        </GroupCardEmpty>

        <template v-else>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <GroupCard v-for="group in sharedGroups" :key="group.id" :group="group" />
            </div>

            <AppPaginationBar :meta="data?.meta" v-if="data" v-model="page" />
        </template>
    </div>
</template>