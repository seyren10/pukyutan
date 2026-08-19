<script setup lang="ts">
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { isAxiosError } from 'axios'
import { toast } from 'vue-sonner'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
    DialogTrigger,
    DialogFooter,
} from '@/components/ui/dialog'
import { Button } from '@/components/ui/button'
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
                setErrors({
                    invite_code: laravelError.response?.data?.message ?? 'Something went wrong.',
                })
            }
        },
    })
})
</script>

<template>
    <Dialog v-model:open="dialog" unmount-on-hide>
        <DialogTrigger as-child>
            <slot />
        </DialogTrigger>

        <DialogContent>
            <DialogHeader>
                <div class="flex size-10 items-center justify-center rounded-full bg-accent text-accent-foreground">
                    <Ticket class="size-5" />
                </div>
                <DialogTitle class="font-heading">Join a group</DialogTitle>
                <DialogDescription>
                    Enter the 6-character invite code shared by the group owner. They'll need to approve your
                    request before you can view it.
                </DialogDescription>
            </DialogHeader>

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
        </DialogContent>
    </Dialog>
</template>
