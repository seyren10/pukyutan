<script setup lang="ts">
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Eye, Wallet, CalendarClock, UserPlus2Icon, ChevronDown, HandCoins } from '@lucide/vue'
import type { Group } from '@/features/group/type'
import { computed } from 'vue'
import { formatFrequencyLabel, getInitials } from '@/lib/helpers'
import { GroupCycleVisual } from '.'
import { format } from 'date-fns'
import AddMemberDialog from '@/pages/index/components/AddMemberDialog.vue'
import GroupCardDropdown from './GroupCardDropdown.vue'
import { useGroup } from '@/features/group/composables/use-group.ts'
import { ContributionDialog } from '@/components/contributions/ContributionDialog/index.ts'

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

const isShared = computed(() => user.value?.name)
</script>

<template>
    <Card class="transition-shadow hover:shadow-md" :class="status === 'draft' ? 'border-dashed' : ''">
        <CardHeader class="flex flex-row items-start justify-between gap-2">
            <div class="flex flex-col gap-1">
                <span class="font-heading text-base font-semibold text-foreground capitalize">{{ name }}</span>
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
            <GroupCycleVisual v-if="nextCycle" :current-cycle="nextCycle.cycle_number" :cycles-count="cyclesCount" />

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
            <AddMemberDialog :group="group" v-if="status === 'draft'">
                <Button variant="outline" size="sm">
                    <UserPlus2Icon data-icon="inline-end" />
                    Add Members
                </Button>
            </AddMemberDialog>

            <ContributionDialog :group-id="group.id" v-if="status === 'active'">
                <Button variant="outline" size="sm">
                    <HandCoins />
                    Add Contribution
                </Button>
            </ContributionDialog>

            <GroupCardDropdown>
                <Button variant="ghost" class="ml-auto">
                    More
                    <ChevronDown />
                </Button>
            </GroupCardDropdown>
        </CardFooter>
    </Card>
</template>