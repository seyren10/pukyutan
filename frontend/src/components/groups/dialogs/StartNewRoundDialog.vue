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

const { completedRoundNumber,
    groupName,
    isStartingRound,
    memberCount,
    membersWithOutstandingBalance,
    nextRoundFirstDueLabel,
    nextRoundNumber,
    totalCollectedLastRound,
    totalExpectedLastRound
} = defineProps<{
    groupName: string
    completedRoundNumber: number
    nextRoundNumber: number
    memberCount: number
    nextRoundFirstDueLabel: string
    totalCollectedLastRound: number
    totalExpectedLastRound: number
    membersWithOutstandingBalance: number
    isStartingRound?: boolean
}>()

const emit = defineEmits<{ confirm: [] }>()

const rules = computed(() => [
    {
        icon: Lock,
        text: 'The payout order stays exactly the same — no changes to who goes when.',
    },
    {
        icon: CalendarCheck,
        text: `New cycles are generated right away, starting ${nextRoundFirstDueLabel} — there's no draft step for a new round.`,
    },
])
</script>

<template>
    <AlertDialog>
        <AlertDialogTrigger as-child>
            <Button>
                <RefreshCw data-icon="inline-start" />
                Start round {{ nextRoundNumber }}
            </Button>
        </AlertDialogTrigger>

        <AlertDialogContent>
            <AlertDialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                    <Trophy class="size-5" />
                </div>
                <AlertDialogTitle class="font-heading">Round {{ completedRoundNumber }} is complete</AlertDialogTitle>
                <AlertDialogDescription>
                    Every cycle in round {{ completedRoundNumber }} of "{{ groupName }}" has been disbursed. Start round
                    {{ nextRoundNumber }} for the same {{ memberCount }} members?
                </AlertDialogDescription>
            </AlertDialogHeader>

            <div class="flex flex-col gap-3 rounded-lg border border-border bg-muted/40 p-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">Collected in round {{ completedRoundNumber }}</span>
                    <span class="font-mono font-medium text-foreground">
                        ₱{{ totalCollectedLastRound.toLocaleString() }} / ₱{{ totalExpectedLastRound.toLocaleString() }}
                    </span>
                </div>

                <template v-if="membersWithOutstandingBalance > 0">
                    <Separator />
                    <div class="flex items-start gap-2 text-sm text-accent-foreground">
                        <CircleAlert class="mt-0.5 size-4 shrink-0" />
                        <span>
                            {{ membersWithOutstandingBalance }}
                            {{ membersWithOutstandingBalance === 1 ? 'member still owes' : 'members still owe' }} a
                            balance from
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
                <AlertDialogAction :disabled="isStartingRound" @click="emit('confirm')">
                    {{ isStartingRound ? 'Starting...' : `Start round ${nextRoundNumber}` }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>