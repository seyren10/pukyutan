<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { toTypedSchema } from '@vee-validate/zod'
import { useForm } from 'vee-validate'
import { toast } from 'vue-sonner'
import { LockIcon } from '@lucide/vue'
import * as z from 'zod'

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

const passwordSchema = z
    .object({
        current_password: z.string().min(1, 'Enter your current password.'),
        password: z.string().min(8, 'Password must be at least 8 characters.'),
        password_confirmation: z.string(),
    })
    .refine((data) => data.password === data.password_confirmation, {
        message: 'Passwords do not match.',
        path: ['password_confirmation'],
    })

const formSchema = toTypedSchema(passwordSchema)

const form = useForm({
    validationSchema: formSchema,
    initialValues: {
        current_password: '',
        password: '',
        password_confirmation: '',
    },
})

const processing = ref(false)

const submit = form.handleSubmit(async (values) => {
    processing.value = true

    try {
        await axios.put('/api/user/password', values)

        toast.success('Password updated', {
            description: 'Your password has been changed successfully.',
        })
        form.resetForm()
    } catch (error) {
        if (axios.isAxiosError(error) && error.response?.status === 422) {
            const validationErrors = error.response.data.errors as Record<string, string[]>

            // Map server field errors (e.g. current_password) onto the vee-validate form.
            form.setErrors(
                Object.fromEntries(
                    Object.entries(validationErrors).map(([key, messages]) => [key, messages[0]]),
                ),
            )
        } else {
            toast.error('Something went wrong', {
                description: 'Could not update your password. Please try again.',
            })
        }
    } finally {
        processing.value = false
    }
})
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <LockIcon class="size-4 text-muted-foreground" />
                Update password
            </CardTitle>
            <CardDescription>
                Ensure your account is using a long, random password to stay secure.
            </CardDescription>
        </CardHeader>

        <form @submit="submit">
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

            <CardFooter class="justify-end border-t">
                <Button type="submit" :disabled="processing">
                    <Spinner v-if="processing" data-icon="inline-start" />
                    Update password
                </Button>
            </CardFooter>
        </form>
    </Card>
</template>