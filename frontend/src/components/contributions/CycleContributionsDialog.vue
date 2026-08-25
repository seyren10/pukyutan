<script setup lang="ts">
import { computed } from 'vue'
import { useInfiniteQuery } from '@tanstack/vue-query'
import { format } from 'date-fns'
import { Wallet, ChevronDown, HandCoins, NotepadText } from '@lucide/vue'
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue'
import { Button } from '@/components/ui/button'
import { Progress } from '@/components/ui/progress'
import { Skeleton } from '@/components/ui/skeleton'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import type { CycleSummary } from '@/features/cycle/type'
import type { Member } from '@/features/members/type'
import { getCycleContributionsInfiniteQueryOptions } from '@/features/contribution/query'
import { Popover, PopoverContent, PopoverTrigger } from '../ui/popover'
import { ScrollArea } from '../ui/scroll-area'
import ContributionUndoDialog from './ContributionUndoDialog.vue'
import AppAvatar from '../app/AppAvatar.vue'

const open = defineModel<boolean>('open', { default: false })

const { cycle, members, expectedTotal, disableUndo = true } = defineProps<{
    cycle: CycleSummary
    members: Member[]
    /** contribution_amount × member count — passed down so the progress bar matches the timeline tooltip. */
    expectedTotal: number,
    disableUndo: boolean
}>()

const memberById = computed(() => new Map(members.map((member) => [member.id, member])))
const recipientName = computed(() => memberById.value.get(cycle.recipient_member_id)?.name ?? 'Unknown member')
const collectedTotal = computed(() => Number(cycle.contributions_sum_amount ?? 0))
const collectedPercent = computed(() =>
    expectedTotal > 0 ? Math.min((collectedTotal.value / expectedTotal) * 100, 100) : 0,
)

const { data, isPending, hasNextPage, isFetchingNextPage, fetchNextPage } = useInfiniteQuery(
    getCycleContributionsInfiniteQueryOptions(() => cycle.id, open),
)

const contributions = computed(() => data.value?.pages.flatMap((page) => page.data) ?? [])

const description = computed(() => cycle.disbursed_at
    ? `Disbursed ${format(new Date(cycle.disbursed_at), 'MMMM d, yyyy')}`
    : `Due ${format(new Date(cycle.due_date), 'MMMM d, yyyy')}`)
</script>

<template>
    <AppResponsiveDialog v-model:open="open" title-class="font-heading"
        :title="`Cycle ${cycle.cycle_number} · ${recipientName}`" :description="description"
        content-class="sm:max-w-md">
        <template #body>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-1.5 text-muted-foreground">
                            <Wallet class="size-3.5" />
                            Collected
                        </span>
                        <span class="font-mono font-medium text-foreground">
                            ₱{{ collectedTotal.toLocaleString('en-PH') }} / ₱{{ expectedTotal.toLocaleString('en-PH')
                            }}
                        </span>
                    </div>
                    <Progress :model-value="collectedPercent" />
                </div>

                <div class="flex flex-col gap-3">
                    <div v-if="isPending" class="flex flex-col divide-y divide-border">
                        <div v-for="i in 3" :key="i" class="flex items-center gap-3 py-3 first:pt-0">
                            <Skeleton class="size-8 shrink-0 rounded-full" />
                            <div class="flex flex-1 flex-col gap-1.5">
                                <Skeleton class="h-3.5 w-24" />
                                <Skeleton class="h-3 w-16" />
                            </div>
                            <Skeleton class="h-4 w-14" />
                        </div>
                    </div>

                    <Empty v-else-if="contributions.length === 0" class="py-4">
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <HandCoins />
                            </EmptyMedia>
                            <EmptyTitle>No payments yet</EmptyTitle>
                            <EmptyDescription>Nobody has contributed to this cycle so far.</EmptyDescription>
                        </EmptyHeader>
                    </Empty>

                    <ScrollArea v-else class="flex max-h-72 flex-col divide-y divide-border overflow-y-auto">
                        <div class="pr-4">
                            <div v-for="contribution in contributions" :key="contribution.id"
                                class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                                <AppAvatar class="size-8 shrink-0"
                                    :fallback="contribution.member?.name || memberById.get(contribution.member_id)?.name || '?'"
                                    :seed="memberById.get(contribution.member_id)?.dicebear_seed || '?'" />

                                <div class="flex min-w-0 flex-1 flex-col">
                                    <span class="truncate text-sm font-medium text-foreground">
                                        {{
                                            contribution.member?.name ?? memberById.get(contribution.member_id)?.name ??
                                            'Unknown member'
                                        }}
                                    </span>
                                    <span class="text-xs text-muted-foreground">
                                        {{
                                            contribution.paid_at ?
                                                format(new Date(contribution.paid_at), 'MMM d, yyyy') :
                                                'No date on file'
                                        }}
                                    </span>
                                </div>
                                <Popover v-if="contribution.notes">
                                    <PopoverTrigger as-child>
                                        <Button variant="ghost" size="icon-sm"
                                            class="rounded-full text-accent-foreground">
                                            <NotepadText />
                                        </Button>
                                    </PopoverTrigger>
                                    <PopoverContent align="end"
                                        class="bg-foreground text-background text-xs text-balance border-none w-auto">
                                        {{ contribution.notes }}
                                    </PopoverContent>

                                </Popover>
                                <span class="shrink-0 font-mono text-sm font-medium text-foreground">
                                    ₱{{ Number(contribution.amount).toLocaleString('en-PH') }}
                                </span>
                                <ContributionUndoDialog :contribution="contribution" v-if="!disableUndo" />
                            </div>
                        </div>
                    </ScrollArea>

                    <Button v-if="hasNextPage" variant="ghost" size="sm" class="self-center"
                        :disabled="isFetchingNextPage" @click="fetchNextPage()">
                        <ChevronDown data-icon="inline-start" />
                        {{ isFetchingNextPage ? 'Loading...' : 'Load more' }}
                    </Button>
                </div>
            </div>
        </template>
    </AppResponsiveDialog>
</template>