<script setup lang="ts">
import {
    Dialog,
    DialogDescription,
    DialogHeader,
    DialogScrollContent,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
import { ChevronsUpDown, HandCoins, PiggyBank, Trophy, Wallet } from '@lucide/vue'
import { useQuery } from '@tanstack/vue-query';
import { getGroupDetailQueryOptions } from '@/features/group/query.ts';
import { computed, ref } from 'vue';
import { useGroupDetail } from '@/features/group/composables/use-group.ts';
import { formatDate } from 'date-fns';
import { ContributionDialogSkeleton } from '.';
import { Separator } from '@/components/ui/separator';
import { Progress } from '@/components/ui/progress';
import ContributionMemberList from '../ContributionMemberList.vue';
import DisburseCycleDialog from '@/components/cycles/DisburseCycleDialog.vue';

const { groupId } = defineProps<{
    groupId: number
}>()
const dialog = ref(false)
const emit = defineEmits<{ submit: [payload: { member_id: number; amount: number }[]] }>()

const { data, isPending } = useQuery(getGroupDetailQueryOptions(() => groupId, dialog))
const group = computed(() => data.value?.data)
const { nextCycle, name, cyclesCount, members } = useGroupDetail(group)

const collectedPercent = computed(() => {
    if (!nextCycle.value) return 0;

    return nextCycle.value.collected_total > 0 ? Math.min((nextCycle.value.collected_total / nextCycle.value.expected_total) * 100, 100) : 0;
})
</script>

<template>
    <Dialog v-model:open="dialog">
        <DialogTrigger as-child>
            <slot>
                <Button>
                    <HandCoins data-icon="inline-start" />
                    Record payments
                </Button>
            </slot>
        </DialogTrigger>

        <ContributionDialogSkeleton v-if="isPending" />
        <DialogScrollContent class="sm:max-w-lg" v-else @interact-outside="e => e.preventDefault()">
            <DialogHeader>
                <DialogTitle class="font-heading">{{ name }}</DialogTitle>
                <DialogDescription>
                    Cycle {{ nextCycle?.cycle_number }}<span> of {{ cyclesCount }}</span> · due <span
                        v-if="nextCycle?.due_date">
                        {{
                            formatDate(nextCycle?.due_date, "MMMM dd, yyyy")
                        }}
                    </span>
                </DialogDescription>
            </DialogHeader>

            <div class="flex flex-col gap-3 rounded-lg border border-border bg-muted/40 p-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-1.5 text-muted-foreground">
                        <Trophy class="size-3.5" />
                        This cycle's pot goes to
                    </span>
                    <span class="font-medium text-foreground">
                        {{ nextCycle?.recipient.name || 'No recipient assigned' }}
                    </span>
                </div>

                <Separator />

                <template v-if="nextCycle?.reserve_balance && nextCycle.reserve_balance > 0">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-1.5 text-muted-foreground">
                            <PiggyBank class="size-3.5" />
                            On hand right now
                        </span>
                        <span class="font-medium text-foreground">
                            ₱{{ nextCycle.reserve_balance }}
                        </span>
                    </div>
                    <Separator />
                </template>


                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-1.5 text-muted-foreground">
                            <Wallet class="size-3.5" />
                            Collected so far
                        </span>
                        <span class="font-mono font-medium text-foreground">
                            ₱{{ nextCycle?.collected_total?.toLocaleString('en-PH') }} / ₱{{
                                nextCycle?.expected_total?.toLocaleString('en-PH') }}
                        </span>
                    </div>
                    <Progress :model-value="collectedPercent" />
                </div>

                <div class="mt-4 space-y-2">
                    <p class="text-muted-foreground text-sm">
                        Click on the
                        <span
                            class="mx-1 inline-flex size-4 items-center justify-center rounded-md bg-accent align-text-bottom">
                            <ChevronsUpDown class="size-3" />
                        </span>
                        button to begin adding contribution payments for each member in this cycle.
                    </p>
                    <ContributionMemberList v-if="group" :members="members" :group-detail="group" />
                </div>

            </div>

            <div>
                <DisburseCycleDialog v-if="nextCycle" :collected-total="nextCycle.collected_total"
                    :cycle-id="nextCycle.id" :disbursed-amount="+(nextCycle.disbursed_amount || 0)"
                    :disbursed-at="nextCycle.disbursed_at" :expected-total="nextCycle.expected_total"
                    :group-id="groupId" :recipient-name="nextCycle.recipient.name"
                    :recommended-disbursement="nextCycle.recommended_disbursement"
                    :reserve-balance="nextCycle.reserve_balance" @recorded="dialog = false">
                    <Button class="w-full">
                        <HandCoins />
                        Disburse payout
                    </Button>
                </DisburseCycleDialog>
            </div>
        </DialogScrollContent>
    </Dialog>
</template>