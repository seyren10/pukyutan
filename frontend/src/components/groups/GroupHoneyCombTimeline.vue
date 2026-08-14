<script setup lang="ts">
import { computed } from 'vue'
import { format } from 'date-fns'
import { Hexagon } from '@lucide/vue'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import { cn } from '@/lib/utils'
import { getInitials } from '@/lib/helpers'
import type { CycleSummary } from '@/features/cycle/type'
import type { Member } from '@/features/members/type'

const { cycles, members, nextCycleId, expectedPerCycle } = defineProps<{
    cycles: CycleSummary[]
    members: Member[]
    /** The currently-open cycle's id, if any — everything else is either already disbursed or still upcoming. */
    nextCycleId?: number
    /** contribution_amount × member count — same approximation LedgerCalculatorService uses, so the tooltip's "collected so far" figure lines up with the summary card. */
    expectedPerCycle?: number
}>()

const emit = defineEmits<{
    /** Fired when a hexagon is clicked — parent decides what to do (e.g. open a contributions dialog). */
    select: [cycle: CycleSummary]
}>()

type CycleVisualState = 'disbursed' | 'current' | 'future'

// Fixed legend — matches the mini dashboard-strip version (GroupCardCycleVisual)
// and the design doc's section 5. Don't invent new states or colors here.
const stateClass: Record<CycleVisualState, string> = {
    disbursed: 'text-primary',
    current: 'text-accent-foreground',
    future: 'text-border',
}

// Only readable at this larger size — kept separate from stateClass since
// the label needs to sit legibly on top of a filled vs. outlined cell.
const labelClass: Record<CycleVisualState, string> = {
    disbursed: 'text-primary-foreground',
    current: 'text-accent-foreground',
    future: 'text-muted-foreground',
}

const memberById = computed(() => new Map(members.map((member) => [member.id, member])))

function stateFor(cycle: CycleSummary): CycleVisualState {
    if (cycle.disbursed_at) return 'disbursed'
    if (cycle.id === nextCycleId) return 'current'
    return 'future'
}

function recipientName(cycle: CycleSummary): string {
    return memberById.value.get(cycle.recipient_member_id)?.name ?? 'Unknown member'
}

function collectedFor(cycle: CycleSummary): number {
    return Number(cycle.contributions_sum_amount ?? 0)
}

// Grouped by round — a real fact about the content (a new rotation with
// the same members starting over), not decoration. Only shown as separate
// labeled rows once a group has actually had more than one round.
const rounds = computed(() => {
    const grouped = new Map<number, CycleSummary[]>()

    for (const cycle of cycles) {
        const list = grouped.get(cycle.round_number) ?? []
        list.push(cycle)
        grouped.set(cycle.round_number, list)
    }

    return [...grouped.entries()]
        .sort(([a], [b]) => a - b)
        .map(([roundNumber, roundCycles]) => ({
            roundNumber,
            cycles: [...roundCycles].sort((a, b) => a.cycle_number - b.cycle_number),
        }))
})
</script>

<template>
    <div class="flex flex-col gap-5">
        <div v-for="round in rounds" :key="round.roundNumber" class="flex flex-col gap-2">
            <p v-if="rounds.length > 1" class="text-sm text-muted-foreground">Round {{ round.roundNumber }}</p>

            <div class="flex flex-wrap gap-3">
                <Tooltip v-for="cycle in round.cycles" :key="cycle.id">
                    <TooltipTrigger as-child>
                        <button type="button" @click="emit('select', cycle)"
                            class="relative shrink-0 rounded-sm transition-transform hover:scale-110 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                            <Hexagon class="size-11" :class="stateClass[stateFor(cycle)]"
                                :fill="stateFor(cycle) === 'disbursed' ? 'currentColor' : 'none'"
                                :stroke-width="stateFor(cycle) === 'future' ? 1.5 : 2.5" />
                            <span :class="cn(
                                'pointer-events-none absolute inset-0 flex items-center justify-center font-mono text-[10px] font-medium',
                                labelClass[stateFor(cycle)],
                            )">
                                {{ getInitials(recipientName(cycle)) }}
                            </span>
                        </button>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p class="font-medium">Cycle {{ cycle.cycle_number }} · {{ recipientName(cycle) }}</p>
                        <p class="text-background/70">
                            {{ cycle.disbursed_at
                                ? `Disbursed ${format(new Date(cycle.disbursed_at), 'MMM d, yyyy')}`
                                : `Due ${format(new Date(cycle.due_date), 'MMM d, yyyy')}` }}
                        </p>
                        <p v-if="expectedPerCycle" class="font-mono text-background/70">
                            ₱{{ collectedFor(cycle).toLocaleString() }} / ₱{{ expectedPerCycle.toLocaleString() }} collected
                        </p>
                        <p class="text-[10px] text-background/50">Click for the full list</p>
                    </TooltipContent>
                </Tooltip>
            </div>
        </div>
    </div>
</template>