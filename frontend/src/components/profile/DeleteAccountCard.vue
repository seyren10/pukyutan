<script setup lang="ts">
import { ref, watchEffect } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { useMutation, } from '@tanstack/vue-query'
import { toast } from 'vue-sonner'
import { TrashIcon } from '@lucide/vue' // swap for your project's iconLibrary if not lucide

import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardFooter,
} from '@/components/ui/card'
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { FieldGroup } from '@/components/ui/field'
import { FormField, FormItem, FormLabel, FormControl, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { deleteAccountSchema } from '@/features/auth/schema'
import { deleteAccount } from '@/features/auth/api'
import { useLogout } from '@/composables/use-logout'
import { isAxiosError } from 'axios'
import type { LaravelError } from '@/types/common'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'

const { execute } = useLogout()
const userStore = useUserStore()
const { isGoogleLoggedIn, user } = storeToRefs(userStore)
const open = ref(false)

const { handleSubmit, setErrors, setFieldValue } = useForm({
    validationSchema: toTypedSchema(deleteAccountSchema),
})


const { mutate: deleteAccountMutate, isPending } = useMutation({
    mutationFn: deleteAccount,
    onSuccess: async () => {
        await execute()
    },
    onError: (error) => {
        if (isAxiosError(error)) {
            const err = error as LaravelError
            if (err.response?.status == 422)
                setErrors(err.response?.data?.errors)
            toast.error(err.response?.data?.message || 'Something went wrong')
        }
        else
            toast.error(error.message)
    },
})

const onSubmit = handleSubmit((values) => deleteAccountMutate(values))

watchEffect(() => {
    if (isGoogleLoggedIn.value) setFieldValue('password', user.value?.email || '_')
})

</script>

<template>
    <Card class="border-destructive/50">
        <CardHeader>
            <CardTitle>Delete Account</CardTitle>
            <CardDescription>
                Permanently delete your account and all associated data.
            </CardDescription>
        </CardHeader>
        <CardFooter>
            <Button variant="destructive" @click="open = true">
                <TrashIcon data-icon="inline-start" />
                Delete Account
            </Button>
        </CardFooter>
    </Card>

    <AlertDialog v-model:open="open">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                    This will permanently delete your account. This will remove your profile, group memberships,
                    contribution history, and
                    notifications. Groups you own will be permanently deleted and has no way to recover it.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <form id="delete-account-form" @submit="onSubmit">
                <FieldGroup v-if="!isGoogleLoggedIn">
                    <FormField v-slot="{ componentField }" name="password">
                        <FormItem>
                            <FormLabel>Password</FormLabel>
                            <FormControl>
                                <Input type="password" autocomplete="current-password" placeholder="Enter your password"
                                    v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </FieldGroup>
            </form>

            <AlertDialogFooter>
                <AlertDialogCancel :disabled="isPending">Cancel</AlertDialogCancel>
                <Button type="submit" form="delete-account-form" variant="destructive" :disabled="isPending">
                    <Spinner v-if="isPending" data-icon="inline-start" />
                    {{ isPending ? 'Deleting...' : 'Delete Account' }}
                </Button>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>