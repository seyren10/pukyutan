<script setup lang="ts">
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { format } from 'date-fns'
import { toast } from 'vue-sonner'
import { FormField, FormItem, FormLabel, FormControl, FormDescription, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { CircleCheck } from '@lucide/vue'
import { useAddContributionMutation } from '@/features/contribution/composables/use-add-contribution-mutation'
import { contributionSchema } from '@/features/contribution/schema'

const { cycleId, groupId, memberId, remainingAmount } = defineProps<{
    cycleId: number
    groupId: number
    memberId: number
    remainingAmount: number
}>()

const emit = defineEmits<{
    (e: 'recorded'): void
}>()

const { mutate, isPending } = useAddContributionMutation({ cycleId, groupId })

const { handleSubmit, resetForm } = useForm({
    validationSchema: toTypedSchema(contributionSchema),
    initialValues: {
        member_id: memberId,
        // Prefill the remaining balance so a full payment is a single
        // tap. Leave blank when nothing's owed — an overpayment/extra
        // note is a deliberate entry, not something to default for them.
        amount: remainingAmount > 0 ? remainingAmount : undefined,
        paid_at: format(new Date(), 'yyyy-MM-dd'),
        notes: '',
    },
})

const onSubmit = handleSubmit((payload) => {
    mutate(payload, {
        onSuccess: () => {
            resetForm()
            toast('Payment recorded')
            emit('recorded')
        },
    })
})
</script>

<template>
    <form class="flex flex-col gap-4" @submit="onSubmit">
        <div class="grid gap-4 sm:grid-cols-2">
            <FormField v-slot="{ componentField }" name="amount">
                <FormItem>
                    <FormLabel>Amount paid</FormLabel>
                    <FormControl>
                        <Input type="number" step="0.01" min="0" inputmode="decimal" placeholder="0.00"
                            class="font-mono" v-bind="componentField" />
                    </FormControl>
                    <FormDescription v-if="remainingAmount > 0">
                        ₱{{ remainingAmount.toLocaleString('en-PH') }} left for this cycle.
                    </FormDescription>
                    <FormMessage />
                </FormItem>
            </FormField>

            <FormField v-slot="{ componentField }" name="paid_at">
                <FormItem class="self-start">
                    <FormLabel>Date paid</FormLabel>
                    <FormControl>
                        <Input type="date" class="font-mono" v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                </FormItem>
            </FormField>
        </div>

        <FormField v-slot="{ componentField }" name="notes">
            <FormItem>
                <FormLabel>Notes <span class="font-normal text-muted-foreground">(optional)</span></FormLabel>
                <FormControl>
                    <Textarea placeholder="Paid in cash, sent via GCash, etc." rows="2" v-bind="componentField" />
                </FormControl>
                <FormDescription>Anything worth remembering about this payment.</FormDescription>
                <FormMessage />
            </FormItem>
        </FormField>

        <Button type="submit" size="sm" class="self-end" :disabled="isPending">
            <Spinner v-if="isPending" data-icon="inline-start" />
            <CircleCheck v-else data-icon="inline-start" />
            Record payment
        </Button>
    </form>
</template>

<style scoped></style>