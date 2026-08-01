<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { BadgeInfo, CheckCircle2, ChevronsUpDownIcon, CirclePlus, CoinsIcon, PiggyBank } from '@lucide/vue'
import type { MemberWithLedger } from '@/features/members/type'
import { getInitials } from '@/lib/helpers'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '../ui/collapsible'
import ContributionForm from './ContributionForm.vue'
import type { GroupDetail } from '@/features/group/type.ts'
import { useGroupDetail } from '@/features/group/composables/use-group.ts'
import { Tooltip, TooltipContent, TooltipTrigger } from '../ui/tooltip/index.ts'

const {
    groupDetail
} = defineProps<{
    loading?: boolean
    groupDetail: GroupDetail
}>()
const emit = defineEmits<{ submit: [payload: { member_id: number; amount: number }[]] }>()
const { contributionAmount, members, nextCycle } = useGroupDetail(() => groupDetail)

const remainingFor = (member: MemberWithLedger) => {
    return Math.max(+contributionAmount.value - member.paid_total, 0)
}

</script>

<template>
    <div class="flex flex-col gap-2 py-1">
        <Collapsible v-for="member in members" :key="member.id" class="rounded-lg border border-border p-2.5"
            #="{ open }">
            <div class="flex items-center gap-4">
                <Avatar class="size-8 shrink-0">
                    <AvatarFallback class="bg-accent text-xs text-accent-foreground">
                        {{ getInitials(member.name) }}
                    </AvatarFallback>
                </Avatar>

                <div class="flex min-w-0 flex-1 flex-col">
                    <span class="truncate text-sm font-medium text-foreground">{{ member.name }}</span>

                    <div v-if="member.paid_total >= +contributionAmount" class="flex gap-2 text-xs text-success">
                        <span class="flex items-center gap-1 ">
                            <CheckCircle2 class="size-3" />
                            Fully paid
                        </span>
                        <Tooltip v-if="member.balance < 0">
                            <TooltipTrigger>
                                <span class="flex items-center gap-1">
                                    <CirclePlus class="size-3" />
                                    ₱{{ Math.abs(member.balance) }}
                                </span>
                            </TooltipTrigger>
                            <TooltipContent class="max-w-xs">
                                <p>
                                    An excess payment of ₱{{ Math.abs(member.balance) }} has been recorded and will be
                                    applied as a credit to next cycle.
                                </p>
                            </TooltipContent>
                        </Tooltip>
                    </div>
                    <span v-else-if="member.paid_total > 0" class="text-xs text-accent-foreground">
                        Paid ₱{{ member.paid_total.toLocaleString() }} · ₱{{ remainingFor(member).toLocaleString() }}
                        left
                    </span>
                    <span v-else class="text-xs text-muted-foreground">Not yet paid</span>
                </div>

                <CollapsibleTrigger as-child>
                    <Button :variant="open ? 'secondary' : 'ghost'" size="icon">
                        <ChevronsUpDownIcon />
                    </Button>
                </CollapsibleTrigger>
            </div>

            <CollapsibleContent class="p-4" v-if="nextCycle">
                <ContributionForm :cycle-id="nextCycle.id" :remaining-amount="remainingFor(member)"
                    :group-id="groupDetail.id" :member-id="member.id" />
            </CollapsibleContent>
        </Collapsible>
    </div>
</template>