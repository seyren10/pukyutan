<script setup lang="ts">
import { computed } from 'vue'
import {
    AlertDialog,
    AlertDialogTrigger,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { RefreshCw, Trophy, Lock, CalendarCheck, CircleAlert } from '@lucide/vue'
import type { Group } from '@/features/group/type'
import { useGroup } from '@/features/group/composables/use-group'
import { useRoundSummaryHelper } from '@/features/group/composables/use-round-summary-helper'
import { useQuery } from '@tanstack/vue-query'
import { getGroupRoundSummaryQueryOptions } from '@/features/group/query'
import { formatNextRoundFirstDueLabel } from '@/lib/date'
import { useStartNewRoundMutation } from '@/features/group/composables/use-start-new-round-mutation'

const { group } = defineProps<{
    group: Group
}>()

const dialog = defineModel<boolean>({ default: false })

const lastCycleRoundNumber = computed(() => group.cycles[group.cycles.length - 1]?.round_number ?? 0)

const { data } = useQuery(getGroupRoundSummaryQueryOptions(() => group.id, lastCycleRoundNumber, dialog))
const summary = computed(() => data.value)
const { roundNumber, totalCollected, totalExpected } = useRoundSummaryHelper(summary)
const { name: groupName, membersCount } = useGroup(() => group)
const { isPending: isStartingNewRound, mutate: startNewRoundMutate } = useStartNewRoundMutation()

const nextRoundNumber = computed(() => {
    return lastCycleRoundNumber.value + 1;
});
const nextRoundFirstDueLabel = computed(() => {
    const lastCycle = group.cycles[group.cycles.length - 1]
    if (!lastCycle) return 'No cycle Created'

    return formatNextRoundFirstDueLabel(lastCycle.due_date, group.frequency_unit, group.frequency_interval)
}

)
const rules = computed(() => [
    {
        icon: Lock,
        text: 'The payout order stays exactly the same — no changes to who goes when.',
    },
    {
        icon: CalendarCheck,
        text: `New cycles are generated right away, starting ${nextRoundFirstDueLabel.value} — there's no draft step for a new round.`,
    },
])

</script>

<template>
    <AlertDialog v-model:open="dialog">
        <AlertDialogTrigger as-child>
            <Button size="sm">
                <RefreshCw data-icon="inline-start" />
                Start round {{ nextRoundNumber }}
            </Button>
        </AlertDialogTrigger>

        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                    <Trophy class="size-5" />
                </div>
                <AlertDialogTitle class="font-heading">Round {{ roundNumber }} is complete</AlertDialogTitle>
                <AlertDialogDescription>
                    Every cycle in round {{ roundNumber }} of "{{ groupName }}" has been disbursed. Start round
                    {{ nextRoundNumber }} for the same {{ membersCount }} members?
                </AlertDialogDescription>
            </AlertDialogHeader>

            <div class="flex flex-col gap-3 rounded-lg border border-border bg-muted/40 p-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">Collected in round {{ roundNumber }}</span>
                    <span class="font-mono font-medium text-foreground">
                        ₱{{ totalCollected }} / ₱{{ totalExpected }}
                    </span>
                </div>

                <template v-if="summary && summary.members_with_outstanding_balance > 0">
                    <Separator />
                    <div class="flex items-start gap-2 text-sm text-accent-foreground">
                        <CircleAlert class="mt-0.5 size-4 shrink-0" />
                        <span>
                            {{ summary.members_with_outstanding_balance }}
                            {{
                                summary.members_with_outstanding_balance === 1 ? 'member still owes' : 'members still owe'
                            }}
                            a balance from
                            this round. Starting a new round won't clear it — you can still follow up and record
                            payments toward
                            it anytime.
                        </span>
                    </div>
                </template>
            </div>

            <ul class="flex flex-col gap-3 py-2">
                <li v-for="rule in rules" :key="rule.text" class="flex items-start gap-3 text-sm text-muted-foreground">
                    <component :is="rule.icon" class="mt-0.5 size-4 shrink-0 text-foreground" />
                    <span>{{ rule.text }}</span>
                </li>
            </ul>

            <AlertDialogFooter>
                <AlertDialogCancel>Not yet</AlertDialogCancel>
                <AlertDialogAction :disabled="isStartingNewRound" @click="startNewRoundMutate(group.id)">
                    {{ isStartingNewRound ? 'Starting...' : `Start round ${nextRoundNumber}` }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>