<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { ArrowLeft, MailCheck, Send } from '@lucide/vue'
import { useForm } from 'vee-validate'
import { loginCredentialSchema } from '@/features/auth/schema'
import { toTypedSchema } from '@vee-validate/zod'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import AppButtonLoaderSwap from '@/components/app/AppButtonLoaderSwap.vue'
import { useAuthMutations } from '@/features/auth/mutations'
import { Empty, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty'

const { handleSubmit } = useForm({
    validationSchema: toTypedSchema(loginCredentialSchema.pick({ email: true }))
})
const { forgotPasswordMutation } = useAuthMutations()
const { mutate, isPending, isSuccess } = forgotPasswordMutation

const submit = handleSubmit((v) => mutate(v))
</script>

<template>

    <Card v-if="!isSuccess">
        <CardHeader>
            <CardTitle class="font-heading text-xl">Reset your password</CardTitle>
            <CardDescription>
                Enter your email and we'll send you a link to reset your password.
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit="submit" class="space-y-4">
                <FormField #="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input v-bind="componentField" type="email" placeholder="name@example.com" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <Button type="submit" class="w-full" :disabled="isPending">
                    <AppButtonLoaderSwap :loading="isPending">
                        <Send data-icon="inline-start" />
                    </AppButtonLoaderSwap>
                    Send reset link
                </Button>
            </form>

        </CardContent>

        <CardFooter class="justify-center text-sm text-muted-foreground">
            <RouterLink :to="{ name: 'login' }" class="flex items-center gap-1 text-foreground hover:underline">
                <ArrowLeft class="size-4" />
                Back to log in
            </RouterLink>
        </CardFooter>
    </Card>

    <Card v-else>
        <Empty>
            <EmptyHeader>
                <EmptyMedia variant="icon">
                    <MailCheck />
                </EmptyMedia>
                <EmptyTitle class="font-heading">Check your email</EmptyTitle>
                <EmptyDescription>
                    We have emailed your password reset link. Click the link to reset your password.
                </EmptyDescription>
            </EmptyHeader>
        </Empty>
    </Card>
</template>