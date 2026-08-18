<script setup lang="ts">
import { computed, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useQuery, useQueryClient } from '@tanstack/vue-query'
import { ArrowLeft, Rocket, UserPlus2, FolderX, Wallet, HandCoins, CheckCircle2, Users, Hexagon, Component } from '@lucide/vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription, EmptyContent } from '@/components/ui/empty'
import { useUserStore } from '@/stores/user'
import { getGroupDetailQueryOptions } from '@/features/group/query'
import { GroupCycleSummaryCard } from '@/components/groups/GroupCard'
import GroupHoneyCombTimeline from '@/components/groups/GroupHoneyCombTimeline.vue'
import GroupMemberLedgerList from '@/components/groups/GroupMemberLedgerList.vue'
import CycleContributionsDialog from '@/components/contributions/CycleContributionsDialog.vue'
import AddMemberDialog from '@/pages/index/components/AddMemberDialog.vue'
import ActivateGroupDialog from '@/pages/index/components/ActivateGroupDialog.vue'
import { useGroupActivateMutation } from '@/features/group/composables/use-group-activate-mutation.ts'
import GroupDetailHeader from './components/GroupDetailHeader.vue'
import GroupDetailSkeleton from './components/GroupDetailSkeleton.vue'
import GroupActivityFeed from './components/GroupActivityFeed.vue'
import type { MemberWithLedger } from '@/features/members/type'
import type { CycleSummary } from '@/features/cycle/type'

const { groupId } = defineProps<{
    groupId: number
}>()

const queryClient = useQueryClient()

const { data, isPending, isError } = useQuery(getGroupDetailQueryOptions(() => groupId))
const group = computed(() => data.value?.data)

const { user: currentUser } = storeToRefs(useUserStore())
const isOwner = computed(() => currentUser.value?.id === group.value?.user_id)
const membersCount = computed(() => group.value?.members.length ?? 0)

// Same approximation LedgerCalculatorService uses server-side (contribution
// amount × current member count) — kept here so the timeline tooltip, the
// contributions dialog, and the stats strip all agree with each other and
// with GroupCycleSummaryCard's own numbers.
const expectedPerCycle = computed(() => Number(group.value?.contribution_amount ?? 0) * membersCount.value)

// The next cycle's members already carry each member's expected/paid/balance
// for that cycle (see LedgerCalculatorService) — reshaped here by id so the
// ledger list can look each member up in O(1).
const ledgerByMemberId = computed(() => {
    const cycleMembers = group.value?.next_cycle?.members
    if (!cycleMembers) return undefined

    return Object.fromEntries(
        cycleMembers.map((member: MemberWithLedger) => [
            member.id,
            { expected_total: member.expected_total, paid_total: member.paid_total, balance: member.balance },
        ]),
    )
})

const paidThisCycle = computed(() => {
    const ledger = ledgerByMemberId.value
    if (!ledger) return null

    const entries = Object.values(ledger)
    return { paid: entries.filter((entry) => entry.balance <= 0).length, total: entries.length }
})

// Lifetime totals across every cycle this group has ever run — a quick
// "how's this group doing overall" glance that neither the summary card
// (this cycle only) nor the timeline (per-cycle) gives you on its own.
const stats = computed(() => {
    const cycles = group.value?.cycles ?? []
    const totalCollected = cycles.reduce((sum, cycle) => sum + Number(cycle.contributions_sum_amount ?? 0), 0)
    const totalDisbursed = cycles.reduce((sum, cycle) => sum + Number(cycle.disbursed_amount ?? 0), 0)

    return [
        { label: 'Collected all-time', value: `₱${totalCollected.toLocaleString('en-PH')}`, icon: Wallet },
        { label: 'Disbursed all-time', value: `₱${totalDisbursed.toLocaleString('en-PH')}`, icon: HandCoins },
        paidThisCycle.value
            ? { label: 'Paid this cycle', value: `${paidThisCycle.value.paid}/${paidThisCycle.value.total}`, icon: CheckCircle2 }
            : { label: 'Members', value: String(membersCount.value), icon: Users },
    ]
})

const { mutate: activateGroupMutate, isPending: isActivatePending } = useGroupActivateMutation()

const handleActivate = () => {
    if (!group.value) return

    activateGroupMutate(group.value.id, {
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: getGroupDetailQueryOptions(groupId).queryKey })
        },
    })
}

// A single dialog instance shared by every hexagon in the timeline, rather
// than one per cycle — `selectedCycle` doubles as its open state.
const selectedCycle = ref<CycleSummary | null>(null)
const cycleDialogOpen = computed({
    get: () => selectedCycle.value !== null,
    set: (isOpen) => { if (!isOpen) selectedCycle.value = null },
})
</script>

<template>
    <RouterView #="{ Component }">
        <component :is="Component" v-if="Component" />

        <template v-else>
            <GroupDetailSkeleton v-if="isPending" />

            <Empty v-else-if="isError || !group">
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <FolderX />
                    </EmptyMedia>
                    <EmptyTitle>Group not found</EmptyTitle>
                    <EmptyDescription>
                        This group doesn't exist, or you don't have access to it.
                    </EmptyDescription>
                </EmptyHeader>
                <EmptyContent>
                    <Button as-child variant="outline" size="sm">
                        <RouterLink :to="{ name: 'dashboard' }">
                            <ArrowLeft data-icon="inline-start" />
                            Back to your groups
                        </RouterLink>
                    </Button>
                </EmptyContent>
            </Empty>

            <div v-else class="flex flex-col gap-6">
                <Button as-child variant="link" class="w-min" size="sm">
                    <RouterLink :to="{ name: 'dashboard' }">
                        <ArrowLeft class="size-3.5" />
                        Back to your groups
                    </RouterLink>
                </Button>


                <GroupDetailHeader :group="group" :is-owner="isOwner" />

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

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                    <div class="flex flex-col gap-6 lg:col-span-2">
                        <GroupCycleSummaryCard v-if="group.status !== 'draft'" :group="group" />

                        <Card v-else>
                            <CardContent class="flex flex-col items-center gap-3 py-10 text-center">
                                <div
                                    class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                                    <Rocket class="size-5" />
                                </div>
                                <div class="flex flex-col gap-1">
                                    <p class="font-heading text-base font-semibold text-foreground">
                                        This group hasn't started yet
                                    </p>
                                    <p class="max-w-sm text-sm text-muted-foreground">
                                        {{ membersCount > 0
                                            ? "Add more members or activate the group to generate its first round."
                                            : "Add members to build the payout order, then activate to get started." }}
                                    </p>
                                </div>
                                <div v-if="isOwner" class="flex flex-wrap items-center justify-center gap-2">
                                    <AddMemberDialog :group="group">
                                        <Button variant="outline" size="sm">
                                            <UserPlus2 data-icon="inline-start" />
                                            Add members
                                        </Button>
                                    </AddMemberDialog>
                                    <ActivateGroupDialog v-if="membersCount > 0" :group="group"
                                        :members-count="membersCount" :loading="isActivatePending"
                                        @confirm="handleActivate" />
                                </div>
                            </CardContent>
                        </Card>

                        <Card v-if="group.cycles.length > 0">
                            <CardContent class="flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <h2 class="font-heading text-lg font-semibold text-foreground">Cycle timeline</h2>
                                    <span class="text-xs text-muted-foreground flex items-center gap-2">
                                        <span class="flex">
                                            <Hexagon class="size-4 fill-primary stroke-none" /> = disbursed
                                        </span>
                                        <span class="flex">
                                            <Hexagon class="size-4 stroke-4 stroke-accent-foreground" /> = due next
                                        </span>
                                    </span>
                                </div>
                                <GroupHoneyCombTimeline :cycles="group.cycles" :members="group.members"
                                    :next-cycle-id="group.next_cycle?.id" :expected-per-cycle="expectedPerCycle"
                                    @select="cycle => selectedCycle = cycle" />
                                <p class="text-xs text-muted-foreground">Click any cycle to see who's paid.</p>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="flex flex-col gap-6">
                        <Card>
                            <CardContent class="flex flex-col gap-4">
                                <div class="flex flex-col gap-0.5">
                                    <h2 class="font-heading text-lg font-semibold text-foreground">Members</h2>
                                    <p class="text-sm text-muted-foreground">Payout order and current balances.</p>
                                </div>
                                <GroupMemberLedgerList :members="group.members"
                                    :ledger-by-member-id="ledgerByMemberId" />
                            </CardContent>
                        </Card>

                        <GroupActivityFeed :group-id="group.id" />
                    </div>
                </div>

                <CycleContributionsDialog v-if="selectedCycle" v-model:open="cycleDialogOpen" :cycle="selectedCycle"
                    :members="group.members" :expected-total="expectedPerCycle" />
            </div>
        </template>

    </RouterView>
</template>