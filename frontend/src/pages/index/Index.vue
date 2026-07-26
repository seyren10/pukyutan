<script setup lang="ts">
import GroupCard from '@/components/groups/GroupCard.vue'
import { Card, CardContent } from '@/components/ui/card'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription, EmptyContent } from '@/components/ui/empty'
import { Layers, Wallet, Users, Inbox, Plus, PackageOpen } from '@lucide/vue'

// Sample data — replace with real store/query data. Shapes shown here
// are what GroupCard and the stats row expect.
const stats = [
    { label: 'Active groups', value: '2', icon: Layers },
    { label: 'Collected this cycle', value: '₱3,200', icon: Wallet },
    { label: 'Members across groups', value: '11', icon: Users },
]

const pendingRequestCount = 2

const ownedGroups = [
    {
        name: 'Pamilya Fund',
        status: 'active' as const,
        cycles: ['disbursed', 'disbursed', 'disbursed', 'due', 'upcoming'] as const,
        contributionAmount: 500,
        frequencyLabel: 'weekly',
        nextDueLabel: 'Cycle 4 · Jul 28',
        memberInitials: ['RG', 'CS', 'EM', 'AL', 'HD'],
    },
    {
        name: 'Office Squad',
        status: 'active' as const,
        cycles: ['disbursed', 'due', 'upcoming', 'upcoming'] as const,
        contributionAmount: 1000,
        frequencyLabel: 'monthly',
        nextDueLabel: 'Cycle 2 · Aug 1',
        memberInitials: ['RG', 'JT', 'MP', 'KD'],
    },
    {
        name: 'Barkada Savings',
        status: 'draft' as const,
        cycles: [],
        contributionAmount: 750,
        frequencyLabel: 'weekly',
        memberInitials: ['RG', 'BT', 'NC', 'JL'],
    },
]

const sharedGroups = [
    {
        name: "Tita Corine's Paluwagan",
        status: 'active' as const,
        cycles: ['disbursed', 'disbursed', 'due', 'upcoming'] as const,
        contributionAmount: 500,
        frequencyLabel: 'weekly',
        nextDueLabel: 'Cycle 3',
        memberInitials: ['CS', 'RG', 'LT'],
        sharedByName: 'Corine S.',
    },
]
</script>

<template>
    <div class="flex flex-col gap-6">
        <Alert v-if="pendingRequestCount > 0" variant="accent">
            <Inbox />
            <AlertTitle>{{ pendingRequestCount }} pending join requests</AlertTitle>
            <AlertDescription class="flex items-center justify-between gap-4">
                <span>Someone wants to view one of your groups. Review before they get access.</span>
                <Button size="sm" variant="outline">Review requests</Button>
            </AlertDescription>
        </Alert>

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

        <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-lg font-semibold text-foreground">Your groups</h2>
                <Button size="sm">
                    <Plus data-icon="inline-start" />
                    New group
                </Button>
            </div>

            <div v-if="ownedGroups.length" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <GroupCard v-for="group in ownedGroups" :key="group.name" v-bind="group" />
            </div>

            <Empty v-else>
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <PackageOpen />
                    </EmptyMedia>
                    <EmptyTitle>No groups yet</EmptyTitle>
                    <EmptyDescription>Create your first paluwagan group to get started.</EmptyDescription>
                </EmptyHeader>
                <EmptyContent>
                    <Button>
                        <Plus data-icon="inline-start" />
                        Create a group
                    </Button>
                </EmptyContent>
            </Empty>
        </div>

        <div v-if="sharedGroups.length" class="flex flex-col gap-3">
            <h2 class="font-heading text-lg font-semibold text-foreground">Shared with you</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                <GroupCard v-for="group in sharedGroups" :key="group.name" v-bind="group" />
            </div>
        </div>
    </div>
</template>