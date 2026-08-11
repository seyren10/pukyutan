<script setup lang="ts">
import { useDisburseCycleMutation } from '@/features/cycle/composables/use-disburse-cycle-mutation'
import { disburseCycleSchema } from '@/features/cycle/schema'
import type { LaravelError } from '@/types/common'
import { toTypedSchema } from '@vee-validate/zod'
import { isAxiosError } from 'axios'
import { useForm } from 'vee-validate'
import { computed } from 'vue'
import { toast } from 'vue-sonner'
import { FormField } from '../ui/form'
import FormItem from '../ui/form/FormItem.vue'
import FormLabel from '../ui/form/FormLabel.vue'
import FormControl from '../ui/form/FormControl.vue'
import Input from '../ui/input/Input.vue'
import FormDescription from '../ui/form/FormDescription.vue'
import FormMessage from '../ui/form/FormMessage.vue'
import { HandCoins, TriangleAlert } from '@lucide/vue'
import { AlertDescription, AlertTitle, Alert } from '../ui/alert/index.ts'
import { DialogClose, DialogFooter } from '../ui/dialog'
import { Button } from '../ui/button/index.ts'
import { Spinner } from '../ui/spinner/index.ts'

const emit = defineEmits<{
    (e: 'recorded'): void
}>()
const { cycleId, groupId, recommendedDisbursement } = defineProps<{
    cycleId: number,
    groupId: number
    recommendedDisbursement: number
}>()
const { mutate, isPending } = useDisburseCycleMutation({ cycleId, groupId })

const { handleSubmit, resetForm, setErrors, values } = useForm({
    validationSchema: toTypedSchema(disburseCycleSchema),
    initialValues: {
        // The safe default: whatever this cycle needs, capped by what's
        // actually in reserve. Not raw collected — that would ignore
        // held-back surplus, or worse, offer to spend it before it's due.
        disbursed_amount: recommendedDisbursement > 0 ? recommendedDisbursement : undefined,
    },
    keepValuesOnUnmount: true
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
            emit('recorded')
        },
    })
})

</script>
<template>
    <form class="flex flex-col gap-4" @submit="onSubmit">
        <FormField v-slot="{ componentField }" name="disbursed_amount">
            <FormItem>
                <FormLabel>Amount to disburse</FormLabel>
                <FormControl>
                    <Input type="number" step="0.01" min="0" inputmode="decimal" placeholder="0.00" class="font-mono"
                        v-bind="componentField" />
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



<style scoped></style>