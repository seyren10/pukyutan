<script setup lang="ts">
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useQuery } from '@tanstack/vue-query'
import { ArrowLeft, Mail, CheckCircle2, CirclePlus, Users2 } from '@lucide/vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import { Tooltip, TooltipContent, TooltipTrigger } from '@/components/ui/tooltip'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { Accordion, AccordionItem, AccordionTrigger, AccordionContent } from '@/components/ui/accordion'
import AppAvatar from '@/components/app/AppAvatar.vue'
import { getGroupDetailQueryOptions } from '@/features/group/query'
import { useGroupDetail } from '@/features/group/composables/use-group'
import { getGroupMembersWithSummaryQueryOptions } from '@/features/members/query'
import MemberLedgerPanel from './components/MemberLedgerPanel.vue'

const { groupId, memberId } = defineProps<{
    groupId: number
    memberId?: number
}>()

const router = useRouter()

const { data: groupData } = useQuery(getGroupDetailQueryOptions(() => groupId))
const groupDetail = computed(() => groupData.value?.data)
const { name: groupName } = useGroupDetail(groupDetail)

const { data, isPending, isError } = useQuery(getGroupMembersWithSummaryQueryOptions(() => groupId))
const members = computed(() => data.value?.data ?? [])

// The accordion is fully route-driven rather than local state — whichever
// member is expanded is whatever :memberId the current route says it is,
// so a refresh, a shared link, or the browser back button all land on the
// same open item instead of a reset, collapsed list.
const openValue = computed(() => (memberId ? String(memberId) : ''))

const handleOpenChange = (value: string | string[]) => {
    const nextId = typeof value === 'string' && value ? Number(value) : null

    if (nextId) {
        router.push({ name: 'groups.detail.members.ledger', params: { id: groupId, memberId: nextId } })
    } else {
        router.push({ name: 'groups.detail.members.index', params: { id: groupId } })
    }
}
</script>

<template>
    <div class="flex flex-col gap-6">
        <Button variant="link" size="sm" class="w-fit" @click="$router.back()">
            <ArrowLeft class="size-3.5" />
            Back
        </Button>

        <div class="flex flex-col gap-0.5">
            <h1 class="font-heading text-xl font-semibold capitalize text-foreground">{{ groupName }} Members</h1>
            <p class="text-sm text-muted-foreground">
                Payout order, lifetime balances, and each member's full contribution history.
            </p>
        </div>

        <Card>
            <CardContent class="py-2">
                <div v-if="isPending" class="flex flex-col divide-y divide-border">
                    <div v-for="i in 4" :key="i" class="flex items-center gap-3 py-3">
                        <Skeleton class="size-8 shrink-0 rounded-full" />
                        <div class="flex flex-1 flex-col gap-1.5">
                            <Skeleton class="h-3.5 w-1/3" />
                            <Skeleton class="h-3 w-1/2" />
                        </div>
                        <Skeleton class="h-3.5 w-16 shrink-0" />
                    </div>
                </div>

                <Empty v-else-if="isError" class="py-8">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <Users2 />
                        </EmptyMedia>
                        <EmptyTitle>Couldn't load members</EmptyTitle>
                        <EmptyDescription>Something went wrong loading this group's roster. Try again in a bit.</EmptyDescription>
                    </EmptyHeader>
                </Empty>

                <Empty v-else-if="!members.length" class="py-8">
                    <EmptyHeader>
                        <EmptyMedia variant="icon">
                            <Users2 />
                        </EmptyMedia>
                        <EmptyTitle>No members yet</EmptyTitle>
                        <EmptyDescription>Add members to this group to start building the payout order.</EmptyDescription>
                    </EmptyHeader>
                </Empty>

                <Accordion v-else type="single" collapsible :model-value="openValue"
                    @update:model-value="handleOpenChange">
                    <AccordionItem v-for="(member, index) in members" :key="member.id" :value="String(member.id)">
                        <AccordionTrigger>
                            <div class="flex min-w-0 flex-1 items-center gap-3">
                                <span
                                    class="flex size-7 shrink-0 items-center justify-center rounded-full bg-muted font-mono text-xs text-muted-foreground">
                                    {{ index + 1 }}
                                </span>

                                <AppAvatar class="size-8 shrink-0" :fallback="member.name" :seed="member.dicebear_seed" />

                                <div class="flex min-w-0 flex-1 flex-col text-left">
                                    <span class="truncate text-sm font-medium text-foreground">{{ member.name }}</span>
                                    <span class="flex items-center gap-1 text-xs text-muted-foreground">
                                        <Mail class="size-3 shrink-0" />
                                        <span class="truncate">{{ member.email || 'No email on file' }}</span>
                                    </span>
                                </div>

                                <div class="shrink-0 text-right text-xs">
                                    <div v-if="member.summary.balance <= 0"
                                        class="flex items-center justify-end gap-1 text-success">
                                        <CheckCircle2 class="size-3" />
                                        All caught up
                                    </div>

                                    <Tooltip v-if="member.summary.balance < 0">
                                        <TooltipTrigger as-child>
                                            <span class="flex items-center justify-end gap-1 text-muted-foreground">
                                                <CirclePlus class="size-3" />
                                                ₱{{ Math.abs(member.summary.balance).toLocaleString('en-PH') }} credit
                                            </span>
                                        </TooltipTrigger>
                                        <TooltipContent class="max-w-xs">
                                            <p>Automatically applied to whatever they owe next.</p>
                                        </TooltipContent>
                                    </Tooltip>
                                    <span v-else-if="member.summary.balance > 0" class="text-accent-foreground">
                                        Owes ₱{{ member.summary.balance.toLocaleString('en-PH') }}
                                    </span>
                                </div>
                            </div>
                        </AccordionTrigger>

                        <AccordionContent>
                            <MemberLedgerPanel v-if="openValue === String(member.id)" :member-id="member.id" />
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </CardContent>
        </Card>
    </div>
</template>
