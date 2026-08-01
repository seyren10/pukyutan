<script setup lang="ts">
import { computed } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { isAxiosError } from 'axios'
import { toast } from 'vue-sonner'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter, DialogTrigger, DialogClose } from '@/components/ui/dialog'
import { FormField, FormItem, FormLabel, FormControl, FormDescription, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert'
import { HandCoins, CircleCheck, TriangleAlert } from '@lucide/vue'
import { format } from 'date-fns'
import type { LaravelError } from '@/types/common'
import { useDisburseCycleMutation } from '@/features/cycle/composables/use-disburse-cycle-mutation'
import { disburseCycleSchema } from '@/features/cycle/schema'

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

const { mutate, isPending } = useDisburseCycleMutation({ cycleId, groupId })

const { handleSubmit, resetForm, setErrors, values } = useForm({
    validationSchema: toTypedSchema(disburseCycleSchema),
    initialValues: {
        // The safe default: whatever this cycle needs, capped by what's
        // actually in reserve. Not raw collected — that would ignore
        // held-back surplus, or worse, offer to spend it before it's due.
        disbursed_amount: recommendedDisbursement > 0 ? recommendedDisbursement : undefined,
    },
})

// A soft nudge, not a block: going above the recommended amount is a
// deliberate leader decision (e.g. "give it all to Ana this month") and
// should stay possible — it just draws down reserve that's backing
// other members' credit, so it's worth surfacing before they confirm.
const exceedsRecommended = computed(() => {
    const amount = Number(values.disbursed_amount)
    return !Number.isNaN(amount) && amount > recommendedDisbursement
})

const onSubmit = handleSubmit((payload) => {
    mutate(payload, {
        onError: (error) => {
            if (isAxiosError(error)) {
                const formError = error as LaravelError
                if (formError.response?.status === 422 && formError.response.data?.errors) {
                    setErrors(formError.response.data.errors)
                    return
                }
                // The reserve-cap rejection lands here — a plain message,
                // no per-field errors, since it isn't about one field being
                // malformed, it's "this amount doesn't exist."
                toast.error(formError.response?.data?.message ?? 'Something went wrong.')
            }
        },
        onSuccess: () => {
            resetForm()
            toast('Payout recorded')
            open.value = false
        },
    })
})
</script>

<template>
    <Dialog v-model:open="open">
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>

        <DialogContent class="sm:max-w-md">
            <template v-if="disbursedAt">
                <DialogHeader>
                    <DialogTitle class="font-heading">Payout already sent</DialogTitle>
                    <DialogDescription>
                        This cycle was disbursed on {{ format(new Date(disbursedAt), 'MMMM dd, yyyy') }} and can't be
                        disbursed again.
                    </DialogDescription>
                </DialogHeader>

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
                <DialogHeader>
                    <DialogTitle class="font-heading">Disburse this cycle's payout</DialogTitle>
                    <DialogDescription>
                        Send the pot to {{ recipientName }}. This can only be done once per cycle.
                    </DialogDescription>
                </DialogHeader>

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

                <form class="flex flex-col gap-4" @submit="onSubmit">
                    <FormField v-slot="{ componentField }" name="disbursed_amount">
                        <FormItem>
                            <FormLabel>Amount to disburse</FormLabel>
                            <FormControl>
                                <Input type="number" step="0.01" min="0" inputmode="decimal" placeholder="0.00"
                                    class="font-mono" v-bind="componentField" />
                            </FormControl>
                            <FormDescription>
                                Defaults to what this cycle needs — any extra collected stays held for next cycle.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <Alert v-if="exceedsRecommended" variant="warning">
                        <TriangleAlert />
                        <AlertTitle>This uses more than this cycle needs</AlertTitle>
                        <AlertDescription>
                            The extra comes out of reserve that may be backing another member's overpayment credit.
                        </AlertDescription>
                    </Alert>
                    <Alert v-else variant="accent">
                        <TriangleAlert />
                        <AlertTitle>You won't be able to undo this</AlertTitle>
                        <AlertDescription>Double-check the amount before confirming.</AlertDescription>
                    </Alert>

                    <DialogFooter>
                        <DialogClose as-child>
                            <Button type="button" variant="outline" :disabled="isPending">Cancel</Button>
                        </DialogClose>
                        <Button type="submit" :disabled="isPending">
                            <Spinner v-if="isPending" data-icon="inline-start" />
                            <HandCoins v-else data-icon="inline-start" />
                            Confirm disbursement
                        </Button>
                    </DialogFooter>
                </form>
            </template>
        </DialogContent>
    </Dialog>
</template>

<style scoped></style>