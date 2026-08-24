<script setup lang="ts">
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { LockIcon, ShieldCheckIcon } from '@lucide/vue'

import { Button } from '@/components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import {
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Spinner } from '@/components/ui/spinner'
import { updatePasswordSchema } from '@/features/auth/schema'
import { useMutation } from '@tanstack/vue-query'
import { updatePassword } from '@/features/auth/api'
import { isAxiosError } from 'axios'
import type { LaravelError } from '@/types/common'
import { toast } from 'vue-sonner'
import { useLogout } from '@/composables/use-logout'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import { Badge } from '../ui/badge'

const { execute } = useLogout()
const userStore = useUserStore()
const { isGoogleLoggedIn } = storeToRefs(userStore)
const formSchema = toTypedSchema(updatePasswordSchema)

const { handleSubmit, setErrors } = useForm({
    validationSchema: formSchema,
    initialValues: {
        current_password: '',
        password: '',
        password_confirmation: '',
    },
})

const { mutate, isPending } = useMutation({
    mutationFn: updatePassword,
    onError: (error) => {
        if (isAxiosError(error)) {
            const err = error as LaravelError;

            if (err.response?.status === 422)
                setErrors(err.response?.data.errors)
            else
                toast.warning(err.response?.data?.message || 'Something went wrong')
        }
        else
            toast.error(error.message);
    },
    onSuccess: async () => await execute({
        password_reset: '1'
    })
})
const submit = handleSubmit((v) => mutate(v))

</script>

<template>
    <Card v-if="!isGoogleLoggedIn">
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <LockIcon class="size-4 text-muted-foreground" />
                Update password
            </CardTitle>
            <CardDescription>
                Ensure your account is using a long, random password to stay secure.
            </CardDescription>
        </CardHeader>

        <form @submit="submit" class="space-y-4">
            <CardContent>
                <div class="flex flex-col gap-6">
                    <FormField v-slot="{ componentField }" name="current_password">
                        <FormItem>
                            <FormLabel>Current password</FormLabel>
                            <FormControl>
                                <Input type="password" autocomplete="current-password" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="password">
                        <FormItem>
                            <FormLabel>New password</FormLabel>
                            <FormControl>
                                <Input type="password" autocomplete="new-password" v-bind="componentField" />
                            </FormControl>
                            <FormDescription>
                                At least 8 characters. Mix letters, numbers, and symbols for a stronger password.
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    </FormField>

                    <FormField v-slot="{ componentField }" name="password_confirmation">
                        <FormItem>
                            <FormLabel>Confirm new password</FormLabel>
                            <FormControl>
                                <Input type="password" autocomplete="new-password" v-bind="componentField" />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    </FormField>
                </div>
            </CardContent>
            <CardFooter>
                <Button type="submit" :disabled="isPending">
                    <Spinner v-if="isPending" data-icon="inline-start" />
                    Update password
                </Button>
            </CardFooter>
        </form>
    </Card>
    <Card v-else>

        <div class="flex items-center justify-center gap-2">
            <ShieldCheckIcon class="size-4 text-muted-foreground" />
            <p class="text-sm text-muted-foreground">Signed in with</p>
            <Badge variant="secondary">Google</Badge>
        </div>

    </Card>
</template>