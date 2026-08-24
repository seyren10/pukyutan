<script setup lang="ts">
import { watchEffect } from 'vue'
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { isAxiosError } from 'axios'
import AppResponsiveDialog from '@/components/app/AppResponsiveDialog.vue'
import { DialogFooter } from '@/components/ui/dialog'
import { FormField, FormItem, FormLabel, FormControl, FormDescription, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import AppButtonLoaderSwap from '@/components/app/AppButtonLoaderSwap.vue'
import { Check } from '@lucide/vue'
import { memberSchema } from '@/features/members/schema'
import type { Member } from '@/features/members/type'
import { useEditMemberMutation } from '@/features/members/composables/edit-member-mutation'
import type { LaravelError } from '@/types/common'

const { member } = defineProps<{
    member: Member
}>()

const dialog = defineModel<boolean>({ default: false })

const { handleSubmit, setErrors, setFieldValue, values } = useForm({
    validationSchema: toTypedSchema(memberSchema),
    initialValues: {
        name: member.name,
        email: member.email,
    }
})

const { editMemberMutate, isEditMemberPending } = useEditMemberMutation()

const onSubmit = handleSubmit((payload) => {
    editMemberMutate({
        memberId: member.id,
        groupId: member.group_id,
        payload,
    }, {
        onError: (error) => {
            if (isAxiosError(error)) {
                const formError = error as LaravelError
                if (formError.response?.data?.errors)
                    setErrors(formError.response?.data?.errors)
            }
        },
        onSuccess: () => {
            dialog.value = false
        },
    })
})

// Same "blank email clears the field instead of failing validation" behavior
// as the add-member form.
watchEffect(() => {
    if (values.email === '') {
        setFieldValue('email', null)
    }
})

</script>

<template>
    <AppResponsiveDialog v-model:open="dialog" title="Edit member"
        :description="`Update ${member.name}'s name and email address.`">
        <template #body>
            <form class="space-y-4" @submit="onSubmit">
                <FormField v-slot="{ componentField }" name="name" :validate-on-blur="false">
                    <FormItem>
                        <FormLabel>Name</FormLabel>
                        <FormControl>
                            <Input placeholder="Juan Dela Cruz" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email <span class="font-normal text-muted-foreground">(optional)</span>
                        </FormLabel>
                        <FormControl>
                            <Input type="email" placeholder="juan@example.com" v-bind="componentField" />
                        </FormControl>
                        <FormDescription>Used to send payment reminders. Leave blank if they don't have one.
                        </FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <DialogFooter>
                    <Button type="submit" :disabled="isEditMemberPending">
                        <AppButtonLoaderSwap :loading="isEditMemberPending">
                            <Check data-icon="inline-start" />
                        </AppButtonLoaderSwap>
                        Save changes
                    </Button>
                </DialogFooter>
            </form>
        </template>
    </AppResponsiveDialog>
</template>