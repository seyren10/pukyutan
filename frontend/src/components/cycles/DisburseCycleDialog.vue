<script setup lang="ts">
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue'
import { Button } from '@/components/ui/button'
import { DialogClose, DialogFooter } from '@/components/ui/dialog'
import { format } from 'date-fns'
import DisburseCycleForm from './DisburseCycleForm.vue'

const emit = defineEmits<{
    (e: 'recorded'): void
}>()
const {
    cycleId,
    groupId,
    recipientName,
    expectedTotal,
    reserveBalance,
    recommendedDisbursement,
    disbursedAt,
    disbursedAmount,
} = defineProps<{
    cycleId: number
    groupId: number
    recipientName: string
    expectedTotal: number
    reserveBalance: number
    recommendedDisbursement: number
    disbursedAt: string | null
    disbursedAmount: number | null
}>()

const open = defineModel<boolean>('open', { default: false })

const title = disbursedAt ? 'Payout already sent' : "Disburse this cycle's payout"
const description = disbursedAt
    ? `This cycle was disbursed on ${format(new Date(disbursedAt), 'MMMM dd, yyyy')} and can't be disbursed again.`
    : `Send the pot to ${recipientName}. This can only be done once per cycle.`

const handleDisburseCycleRecorded = () => {
    emit('recorded')
    open.value = false;
}
</script>

<template>
    <AppResponsiveDialog v-model:open="open" :title="title" title-class="font-heading" :description="description"
        content-class="sm:max-w-md">
        <slot />

        <template #body>
            <template v-if="disbursedAt">
                <div class="flex items-center justify-between rounded-lg border border-border bg-muted/40 p-3 text-sm">
                    <span class="text-muted-foreground">Amount disbursed</span>
                    <span class="font-mono font-medium text-foreground">
                        ₱{{ disbursedAmount?.toLocaleString('en-PH') }}
                    </span>
                </div>

                <DialogFooter>
                    <DialogClose as-child>
                        <Button variant="outline">Close</Button>
                    </DialogClose>
                </DialogFooter>
            </template>

            <template v-else>
                <div class="flex flex-col gap-2 rounded-lg border border-border bg-muted/40 p-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">This cycle needs</span>
                        <span class="font-mono font-medium text-foreground">
                            ₱{{ expectedTotal.toLocaleString('en-PH') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-muted-foreground">On hand right now</span>
                        <span class="font-mono font-medium text-foreground">
                            ₱{{ reserveBalance.toLocaleString('en-PH') }}
                        </span>
                    </div>
                </div>

                <DisburseCycleForm :cycle-id="cycleId" :group-id="groupId"
                    :recommended-disbursement="recommendedDisbursement" @recorded="handleDisburseCycleRecorded" />
            </template>
        </template>
    </AppResponsiveDialog>
</template>

<style scoped></style>