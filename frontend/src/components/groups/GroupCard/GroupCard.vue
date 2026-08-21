<script setup lang="ts">
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Eye, Wallet, CalendarClock, HandCoins, MoreHorizontal, UserRound, Rocket } from '@lucide/vue'
import type { Group } from '@/features/group/type'
import { computed, ref } from 'vue'
import { formatFrequencyLabel, getInitials } from '@/lib/helpers'
import { GroupCycleVisual, type GroupCardDropdownEvent } from '.'
import { format } from 'date-fns'
import AddMemberDialog from '@/pages/groups/index/components/AddMemberDialog.vue'
import GroupCardDropdown from './GroupCardDropdown.vue'
import { useGroup } from '@/features/group/composables/use-group.ts'
import { ContributionDialog } from '@/components/contributions/ContributionDialog/index.ts'
import ActivateGroupDialog from '@/pages/groups/index/components/ActivateGroupDialog.vue'
import { useGroupActivateMutation } from '@/features/group/composables/use-group-activate-mutation.ts'
import StartNewRoundDialog from '../dialogs/StartNewRoundDialog.vue'
import EditGroupDialog from '@/pages/groups/index/components/EditGroupDialog.vue'
import RenameGroupDialog from '@/pages/groups/index/components/RenameGroupDialog.vue'
import DeleteGroupDialog from '../dialogs/DeleteGroupDialog.vue'

const { group } = defineProps<{
    group: Group
}>()

const { contributionAmount,
    cyclesCount,
    frequencyInterval,
    frequencyUnit,
    membersCount,
    name,
    nextCycle,
    recentMembers,
    status,
    user } = useGroup(() => group)

const showEditGroupDialog = ref(false)
const showRenameGroupDialog = ref(false)
const showAddMemberDialog = ref(false)
const showDeleteGroupDialog = ref(false)

const isShared = computed(() => user.value?.name)
// Mirrors GroupDetailHeader's isOwner check — a shared/view-only collaborator
// never gets group.user populated the way the "shared" and detail endpoints
// do, so isShared doubles as the inverse of ownership here.
const isOwner = computed(() => !isShared.value)

const { isPending: isGroupActivePending, mutate: groupActivateMutate } = useGroupActivateMutation()

const handleGroupDropdownEvent = (e: GroupCardDropdownEvent) => {
    switch (e) {
        case 'add-member': {
            showAddMemberDialog.value = true
            return;
        }
        case 'edit-group': {
            showEditGroupDialog.value = true
            return;
        }
        case 'rename-group': {
            showRenameGroupDialog.value = true
            return;
        }
        case 'delete-group': {
            showDeleteGroupDialog.value = true
            return;
        }
    }
}
</script>

<template>
    <Card class="transition-shadow hover:shadow-md" :class="status === 'draft' ? 'border-dashed' : ''">
        <CardHeader class="flex flex-row items-start justify-between gap-2">
            <div class="flex flex-col gap-1">
                <RouterLink :to="{ name: 'groups.detail', params: { id: group.id } }"
                    class="w-fit font-heading text-base font-semibold text-foreground capitalize hover:underline">
                    {{ name }}
                </RouterLink>
                <div class="flex -space-x-2">
                    <Avatar v-for="(member, i) in recentMembers.slice(0, 4)" :key="i"
                        class="size-6 border-2 border-card">
                        <AvatarFallback class="bg-accent text-[10px] text-accent-foreground">
                            {{ getInitials(member.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div v-if="membersCount > 4"
                        class="flex size-6 items-center justify-center rounded-full border-2 border-card bg-muted text-[10px] text-muted-foreground">
                        +{{ membersCount - 4 }}
                    </div>
                </div>
            </div>

            <Badge v-if="isShared" variant="outline" class="shrink-0 gap-1">
                <Eye class="size-3" />
                View only
            </Badge>
            <Badge v-else-if="status === 'active'" variant="success" class="shrink-0">Active</Badge>
            <Badge v-else-if="status === 'draft'" variant="accent" class="shrink-0">Draft</Badge>
            <Badge v-else variant="secondary" class="shrink-0">Completed</Badge>
        </CardHeader>

        <CardContent class="flex flex-col gap-3 grow">
            <GroupCycleVisual :current-cycle="nextCycle?.cycle_number" :cycles-count="cyclesCount" />

            <div class="flex items-center justify-between font-mono text-xs text-muted-foreground">
                <span class="flex items-center gap-1.5" v-if="frequencyUnit && frequencyInterval">
                    <Wallet class="size-3.5" />
                    ₱{{ contributionAmount }} · {{
                        formatFrequencyLabel(frequencyUnit, frequencyInterval) }}
                </span>
                <span v-if="nextCycle" class="flex items-center gap-1.5">
                    <CalendarClock class="size-3.5" />
                    Cycle {{ nextCycle.cycle_number }} · {{ format(nextCycle.due_date, 'MMM d') }}
                </span>
            </div>

            <p v-if="isShared" class="text-xs text-muted-foreground">
                Owned by {{ user?.name }}
            </p>
        </CardContent>

        <CardFooter class="gap-2">
            <template v-if="isOwner">
                <ContributionDialog :group-id="group.id" v-if="status === 'active' && !group.is_round_completed">
                    <Button variant="outline" size="xs">
                        <HandCoins />
                        Add Contribution
                    </Button>
                </ContributionDialog>
                <AddMemberDialog :group="group" v-else-if="status === 'draft'">
                    <Button variant="outline" size="xs">
                        <UserRound data-icon="inline-end" />
                        Manage Members
                    </Button>
                </AddMemberDialog>
                <StartNewRoundDialog v-else-if="group.is_round_completed" :group="group"
                    #="{ props: { nextRoundNumber } }">
                    <Button size="xs">
                        <Rocket /> Start Round {{ nextRoundNumber }}
                    </Button>
                </StartNewRoundDialog>
                <ActivateGroupDialog :group="group" :members-count="group.members_count"
                    v-if="group.members_count && group.status === 'draft'" @confirm="groupActivateMutate(group.id)"
                    :loading="isGroupActivePending">
                    <Button size="xs">
                        <Rocket /> Activate Group
                    </Button>
                </ActivateGroupDialog>
            </template>
            <!-- View-only collaborators get no mutation actions here — every branch
                 above ends up owner-gated server-side anyway (Gate::authorize('update', ...)),
                 so surfacing them would just be a dead end that 403s on click. -->
            <Button v-else as-child variant="outline" size="sm">
                <RouterLink :to="{ name: 'groups.detail', params: { id: group.id } }">
                    <Eye data-icon="inline-start" />
                    View Group
                </RouterLink>
            </Button>
            <GroupCardDropdown :group="group" :is-owner="isOwner" @select="handleGroupDropdownEvent" v-if="isOwner">
                <Button variant="ghost" class="ml-auto" size="icon-sm">
                    <MoreHorizontal />
                </Button>
            </GroupCardDropdown>
        </CardFooter>

        <!-- Controlled-only instance (no trigger slot) so the dropdown's "Manage
             Members" item can open this even when the footer's own AddMemberDialog
             above isn't rendered (e.g. the group already has members). -->
        <AddMemberDialog :group="group" v-model="showAddMemberDialog" v-if="isOwner && showAddMemberDialog" />
        <EditGroupDialog :group="group" v-model="showEditGroupDialog" v-if="showEditGroupDialog" />
        <RenameGroupDialog :group="group" v-model="showRenameGroupDialog" v-if="showRenameGroupDialog" />
        <DeleteGroupDialog :group="group" v-model="showDeleteGroupDialog" v-if="showDeleteGroupDialog" />
    </Card>
</template>