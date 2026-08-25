<script setup lang="ts">
import { computed } from 'vue'
import { useQuery } from '@tanstack/vue-query'
import { formatDate } from 'date-fns'
import type { AxiosError } from 'axios'
import { History, ShieldAlert, AlertTriangle } from '@lucide/vue'
import { Skeleton } from '@/components/ui/skeleton'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { getMemberLedgerQueryOptions } from '@/features/members/query'

const { memberId } = defineProps<{
    memberId: number
}>()

const { data, isPending, isError, error } = useQuery(getMemberLedgerQueryOptions(() => memberId))

// The ledger endpoint is owner-only (see MemberPolicy::view) — a
// view-only collaborator can still see the balance summary in the row
// above, but a 403 here just means "not your ledger to drill into",
// not a real failure.
const isForbidden = computed(() => (error.value as AxiosError | null)?.response?.status === 403)
</script>

<template>
    <div class="flex flex-col gap-2 px-1">
        <div v-if="isPending" class="flex flex-col gap-2">
            <Skeleton v-for="i in 2" :key="i" class="h-16 w-full rounded-lg" />
        </div>

        <Empty v-else-if="isForbidden" class="py-6">
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <ShieldAlert />
                </EmptyMedia>
                <EmptyTitle>Owner only</EmptyTitle>
                <EmptyDescription>Only the group owner can view a member's full ledger.</EmptyDescription>
            </EmptyHeader>
        </Empty>

        <Empty v-else-if="isError" class="py-6">
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <AlertTriangle />
                </EmptyMedia>
                <EmptyTitle>Couldn't load this ledger</EmptyTitle>
                <EmptyDescription>Something went wrong fetching this member's history. Try again in a bit.</EmptyDescription>
            </EmptyHeader>
        </Empty>

        <Empty v-else-if="!data?.length" class="py-6">
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <History />
                </EmptyMedia>
                <EmptyTitle>No cycles yet</EmptyTitle>
                <EmptyDescription>Nothing's been due for this member yet — cycles show up here once they are.</EmptyDescription>
            </EmptyHeader>
        </Empty>

        <div v-else class="flex flex-col gap-2">
            <div v-for="cycle in data" :key="cycle.cycle_number" class="rounded-lg border border-border p-3">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-sm font-medium text-foreground">Cycle {{ cycle.cycle_number }}</span>
                        <span class="text-xs text-muted-foreground">
                            Round {{ cycle.round_number }} · Due {{ formatDate(new Date(cycle.due_date), 'MMM dd, yyyy') }}
                        </span>
                    </div>

                    <div class="shrink-0 text-right text-xs">
                        <div class="font-mono text-foreground">
                            ₱{{ cycle.paid.toLocaleString('en-PH') }}
                            <span class="text-muted-foreground">/ ₱{{ cycle.expected.toLocaleString('en-PH') }}</span>
                        </div>
                        <div :class="cycle.running_balance <= 0 ? 'text-success' : 'text-accent-foreground'">
                            {{ cycle.running_balance <= 0 ? 'Settled' : `Owes ₱${cycle.running_balance.toLocaleString('en-PH')}` }}
                        </div>
                    </div>
                </div>

                <ul v-if="cycle.contributions.length" class="mt-2 flex flex-col gap-1 border-t border-dashed border-border pt-2">
                    <li v-for="contribution in cycle.contributions" :key="contribution.id"
                        class="flex items-center justify-between text-xs text-muted-foreground">
                        <span>{{ contribution.paid_at ? formatDate(new Date(contribution.paid_at), 'MMM dd, yyyy') : 'No date on file' }}</span>
                        <span class="font-mono text-foreground">₱{{ contribution.amount.toLocaleString('en-PH') }}</span>
                    </li>
                </ul>
                <p v-else class="mt-2 text-xs text-muted-foreground">No payments recorded for this cycle.</p>
            </div>
        </div>
    </div>
</template>
