<script setup lang="ts">
import { computed } from 'vue'
import { storeToRefs } from 'pinia'
import { Trophy, PiggyBank, Wallet, HandCoins, PartyPopper, CheckCircle2 } from '@lucide/vue'
import { Card, CardContent } from '@/components/ui/card'
import { Separator } from '@/components/ui/separator'
import { Progress } from '@/components/ui/progress'
import { Button } from '@/components/ui/button'
import { ContributionDialog } from '@/components/contributions/ContributionDialog'
import DisburseCycleDialog from '@/components/cycles/DisburseCycleDialog.vue'
import StartNewRoundDialog from '../dialogs/StartNewRoundDialog.vue'
import { useUserStore } from '@/stores/user'
import type { GroupDetail } from '@/features/group/type'

const { group } = defineProps<{
    group: GroupDetail
}>()

const { user: currentUser } = storeToRefs(useUserStore())
const isOwner = computed(() => currentUser.value?.id === group.user_id)

const nextCycle = computed(() => group.next_cycle)

const collectedPercent = computed(() => {
    if (!nextCycle.value || nextCycle.value.expected_total <= 0) return 0
    return Math.min((nextCycle.value.collected_total / nextCycle.value.expected_total) * 100, 100)
})
</script>

<template>
    <Card v-if="nextCycle">
        <CardContent class="flex flex-col gap-4">
            <div class="flex items-center justify-between">
                <h2 class="font-heading text-lg font-semibold text-foreground">This cycle</h2>
                <span class="font-mono text-xs text-muted-foreground">
                    Cycle {{ nextCycle.cycle_number }} of {{ group.cycles_count }}
                </span>
            </div>

            <div class="flex flex-col gap-3 rounded-lg border border-border bg-muted/40 p-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="flex items-center gap-1.5 text-muted-foreground">
                        <Trophy class="size-3.5" />
                        This cycle's pot goes to
                    </span>
                    <span class="font-medium text-foreground">{{ nextCycle.recipient.name }}</span>
                </div>

                <Separator />

                <template v-if="nextCycle.reserve_balance > 0">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-1.5 text-muted-foreground">
                            <PiggyBank class="size-3.5" />
                            On hand right now
                        </span>
                        <span class="font-mono font-medium text-foreground">
                            ₱{{ nextCycle.reserve_balance.toLocaleString('en-PH') }}
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
                            ₱{{ nextCycle.collected_total.toLocaleString('en-PH') }} / ₱{{
                                nextCycle.expected_total.toLocaleString('en-PH') }}
                        </span>
                    </div>
                    <Progress :model-value="collectedPercent" />
                </div>
            </div>

            <div v-if="isOwner" class="flex flex-col gap-2 sm:flex-row">
                <ContributionDialog :group-id="group.id">
                    <Button class="flex-1">
                        <HandCoins data-icon="inline-start" />
                        Record payments
                    </Button>
                </ContributionDialog>
                <DisburseCycleDialog :cycle-id="nextCycle.id" :group-id="group.id"
                    :recipient-name="nextCycle.recipient.name" :expected-total="nextCycle.expected_total"
                    :reserve-balance="nextCycle.reserve_balance"
                    :recommended-disbursement="nextCycle.recommended_disbursement"
                    :disbursed-at="nextCycle.disbursed_at" :disbursed-amount="+(nextCycle.disbursed_amount || 0)">
                    <Button variant="outline" class="flex-1">
                        <HandCoins data-icon="inline-start" />
                        Disburse payout
                    </Button>
                </DisburseCycleDialog>
            </div>
        </CardContent>
    </Card>

    <Card v-else-if="group.is_round_completed && group.status !== 'completed'">
        <CardContent class="flex flex-col items-center gap-3 py-8 text-center">
            <div class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                <PartyPopper class="size-5" />
            </div>
            <div class="flex flex-col gap-1">
                <p class="font-heading text-base font-semibold text-foreground">This round is complete</p>
                <p class="text-sm text-muted-foreground">Every member has received their payout.</p>
            </div>
            <StartNewRoundDialog v-if="isOwner" :group="group" />
        </CardContent>
    </Card>

    <Card v-else-if="group.status === 'completed'">
        <CardContent class="flex flex-col items-center gap-3 py-8 text-center">
            <div class="flex size-10 items-center justify-center rounded-full bg-success/15 text-success">
                <CheckCircle2 class="size-5" />
            </div>
            <div class="flex flex-col gap-1">
                <p class="font-heading text-base font-semibold text-foreground">This group is complete</p>
                <p class="text-sm text-muted-foreground">Every round has finished — this is now a closed record.</p>
            </div>
        </CardContent>
    </Card>
</template>