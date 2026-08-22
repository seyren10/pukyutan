<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { CircleXIcon, Lock } from '@lucide/vue'
import { useAuthMutations } from '@/features/auth/mutations'
import { useForm } from 'vee-validate'
import { loginCredentialSchema } from '@/features/auth/schema'
import { toTypedSchema } from '@vee-validate/zod'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import AppButtonLoaderSwap from '@/components/app/AppButtonLoaderSwap.vue'
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert'
import type { LaravelError } from '@/types/common'
import AppGoogleLoginButton from '@/components/app/AppGoogleLoginButton.vue'
import { Marker, MarkerContent } from '@/components/ui/marker'

const { loginMutation } = useAuthMutations()
const { mutate, isPending, isError, error } = loginMutation
const laravelError = error as unknown as LaravelError;

const { handleSubmit } = useForm({
    validationSchema: toTypedSchema(loginCredentialSchema)
})

const submit = handleSubmit((v) => mutate(v))
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-xl">Log in</CardTitle>
            <CardDescription>Enter your email and password to continue.</CardDescription>
        </CardHeader>

        <CardContent class="space-y-4">
            <Alert variant="destructive" v-if="isError">
                <CircleXIcon />
                <AlertTitle>An error occured</AlertTitle>
                <AlertDescription>{{ laravelError.response?.data?.message }}</AlertDescription>
            </Alert>
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

                <FormField #="{ componentField }" name="password">
                    <FormItem>
                        <div class="flex items-center justify-between">
                            <FormLabel>Password</FormLabel>
                            <RouterLink :to="{ name: 'forgot-password' }"
                                class="text-sm text-muted-foreground hover:text-foreground">
                                Forgot password?
                            </RouterLink>
                        </div>

                        <FormControl>
                            <Input v-bind="componentField" type="password" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <Button type="submit" class="w-full" :disabled="isPending">
                    <AppButtonLoaderSwap :loading="isPending">
                        <Lock data-icon="inline-start" />
                    </AppButtonLoaderSwap>
                    Log in
                </Button>
            </form>
            <Marker variant="separator">
                <MarkerContent>OR</MarkerContent>
            </Marker>
            <AppGoogleLoginButton class="w-full" />
        </CardContent>
        <CardFooter class="justify-center text-sm text-muted-foreground">
            Don't have an account?
            <RouterLink :to="{ name: 'register' }" class="ml-1 text-foreground hover:underline">
                Create one
            </RouterLink>
        </CardFooter>
    </Card>
</template>