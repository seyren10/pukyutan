<script setup lang="ts">
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { isAxiosError } from 'axios'
import { toast } from 'vue-sonner'
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue'
import { Button } from '@/components/ui/button'
import { DialogFooter } from '@/components/ui/dialog'
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Ticket, LogIn } from '@lucide/vue'
import { joinGroupSchema } from '@/features/share/schema'
import { useJoinGroupMutation } from '@/features/share/composables/use-join-group-mutation'
import type { LaravelError } from '@/types/common'

const dialog = defineModel<boolean>({ default: false })

const { handleSubmit, resetForm, setErrors } = useForm({
    validationSchema: toTypedSchema(joinGroupSchema),
})

const { mutate: joinGroupMutate, isPending } = useJoinGroupMutation()

const onSubmit = handleSubmit((payload) => {
    joinGroupMutate(payload, {
        onSuccess: () => {
            resetForm()
            dialog.value = false
            toast.success('Request sent', {
                description: "You'll be notified once the group owner responds.",
            })
        },
        onError: (error) => {
            if (isAxiosError(error)) {
                const laravelError = error as LaravelError
                if (laravelError.response?.status === 404)
                    setErrors({
                        invite_code: 'Invalid invitation code',
                    })
                else {
                    setErrors({
                        invite_code: laravelError.response?.data?.message ?? 'Something went wrong',
                    })
                }
            }
        },
    })
})
</script>

<template>
    <AppResponsiveDialog v-model:open="dialog" title="Join a group" title-class="font-heading"
        description="Enter the 6-character invite code shared by the group owner. They'll need to approve your request before you can view it.">
        <slot />

        <template #icon>
            <div class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                <Ticket class="size-5" />
            </div>
        </template>

        <template #body>
            <form class="flex flex-col gap-4" @submit="onSubmit">
                <FormField v-slot="{ componentField }" name="invite_code" :validate-on-blur="false">
                    <FormItem>
                        <FormLabel>Invite code</FormLabel>
                        <FormControl>
                            <Input placeholder="AB12CD" class="font-mono uppercase tracking-widest" maxlength="6"
                                v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <DialogFooter>
                    <Button type="submit" :disabled="isPending">
                        <LogIn data-icon="inline-start" />
                        {{ isPending ? 'Sending request...' : 'Send request' }}
                    </Button>
                </DialogFooter>
            </form>
        </template>
    </AppResponsiveDialog>
</template>