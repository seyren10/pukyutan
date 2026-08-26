<script setup lang="ts">
import { useQuery } from '@tanstack/vue-query'
import { formatDate } from 'date-fns'
import { History, AlertTriangle, FileText } from '@lucide/vue'
import { Skeleton } from '@/components/ui/skeleton'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { getMemberLedgerQueryOptions } from '@/features/members/query'
import { Button } from '@/components/ui/button'
import { useDownloadMemberLedgerPdfMutation } from '@/features/members/composables/use-download-member-ledger-pdf-mutation'
import AppButtonLoaderSwap from '@/components/app/AppButtonLoaderSwap.vue'

const { memberId, memberName } = defineProps<{
    memberId: number,
    memberName: string
}>()

const { data, isPending, isError } = useQuery(getMemberLedgerQueryOptions(() => memberId))

const { mutate, isPending: isPdfDownloading } = useDownloadMemberLedgerPdfMutation()
</script>

<template>
    <div class="flex flex-col gap-2 px-1">
        <div v-if="isPending" class="flex flex-col gap-2">
            <Skeleton v-for="i in 2" :key="i" class="h-16 w-full rounded-lg" />
        </div>
        <Empty v-else-if="isError" class="py-6">
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <AlertTriangle />
                </EmptyMedia>
                <EmptyTitle>Couldn't load this ledger</EmptyTitle>
                <EmptyDescription>Something went wrong fetching this member's history. Try again in a bit.
                </EmptyDescription>
            </EmptyHeader>
        </Empty>

        <Empty v-else-if="!data?.length" class="py-6">
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <History />
                </EmptyMedia>
                <EmptyTitle>No cycles yet</EmptyTitle>
                <EmptyDescription>Nothing's been due for this member yet — cycles show up here once they are.
                </EmptyDescription>
            </EmptyHeader>
        </Empty>

        <div v-else class="flex flex-col gap-2">
            <div v-for="cycle in data" :key="cycle.cycle_number" class="rounded-lg border border-border p-3">
                <div class="flex items-center justify-between gap-3">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-sm font-medium text-foreground">Cycle {{ cycle.cycle_number }}</span>
                        <span class="text-xs text-muted-foreground">
                            Round {{ cycle.round_number }} · Due {{ formatDate(new Date(cycle.due_date), 'MMM dd, yyyy')
                            }}
                        </span>
                    </div>

                    <div class="shrink-0 text-right text-xs">
                        <div class="font-mono text-foreground">
                            ₱{{ cycle.paid.toLocaleString('en-PH') }}
                            <span class="text-muted-foreground">/ ₱{{ cycle.expected.toLocaleString('en-PH') }}</span>
                        </div>
                        <div :class="cycle.running_balance <= 0 ? 'text-success' : 'text-accent-foreground'">
                            {{ cycle.running_balance <= 0 ? 'Settled' : `Owes
                                ₱${cycle.running_balance.toLocaleString('en-PH')}` }} </div>
                        </div>
                    </div>

                    <ul v-if="cycle.contributions.length"
                        class="mt-2 flex flex-col gap-1 border-t border-dashed border-border pt-2">
                        <li v-for="contribution in cycle.contributions" :key="contribution.id"
                            class="flex items-center justify-between text-xs text-muted-foreground">
                            <span>{{ contribution.paid_at ? formatDate(new Date(contribution.paid_at), 'MMM dd, yyyy') :
                                'No date on file' }}</span>
                            <span class="font-mono text-foreground">₱{{ contribution.amount.toLocaleString('en-PH')
                            }}</span>
                        </li>
                    </ul>
                    <p v-else class="mt-2 text-xs text-muted-foreground">No payments recorded for this cycle.</p>
                </div>
            </div>
            <Button class="self-end" size="sm" @click="mutate({
                memberId,
                memberName
            })" :disabled="isPdfDownloading">
                <AppButtonLoaderSwap :loading="isPdfDownloading">
                    <FileText />
                </AppButtonLoaderSwap> Download Ledger
            </Button>
        </div>
</template>
