<script setup lang="ts">
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { UserPlus } from '@lucide/vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { registrationSchema } from '@/features/auth/schema'
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form'
import type { RegistrationPayload } from '@/features/auth/type'
import { useAuthMutations } from '@/features/auth/mutations'
import type { LaravelError } from '@/types/common'
import { isAxiosError } from 'axios'
import AppGoogleLoginButton from '@/components/app/AppGoogleLoginButton.vue'
import { Marker, MarkerContent } from '@/components/ui/marker'


const { handleSubmit, setErrors } = useForm({
    validationSchema: toTypedSchema(registrationSchema)
})

const { registerMutation } = useAuthMutations()
const { mutate } = registerMutation
const submit = handleSubmit((values) => {
    mutate(values, {
        onError: (error) => {
            if (isAxiosError(error)) {
                const axiosErorr = error as LaravelError<RegistrationPayload>
                setErrors(axiosErorr.response?.data?.errors!)
            }
        }
    })
})
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-xl">Create an account</CardTitle>
            <CardDescription>Start tracking your paluwagan groups.</CardDescription>
        </CardHeader>

        <CardContent class="space-y-4">
            <form @submit="submit" class="space-y-4">
                <FormField #="{ componentField }" name="name">
                    <FormItem>
                        <FormLabel>Name</FormLabel>
                        <FormControl>
                            <Input placeholder="Juan Dela Cruz" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>

                </FormField>
                <FormField #="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="email" placeholder="name@example.com" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>

                </FormField>
                <FormField #="{ componentField }" name="password">
                    <FormItem>
                        <FormLabel>Password</FormLabel>
                        <FormControl>
                            <Input type="password" v-bind="componentField" />
                        </FormControl>
                        <FormDescription>At least 8 characters.</FormDescription>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <FormField #="{ componentField }" name="password_confirmation">
                    <FormItem>
                        <FormLabel>Password Confirmation</FormLabel>
                        <FormControl>
                            <Input type="password" v-bind="componentField" />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>
                <Button type="submit" class="w-full">
                    <UserPlus data-icon="inline-start" />
                    Create account
                </Button>
            </form>

            <Marker variant="separator">
                <MarkerContent>OR</MarkerContent>
            </Marker>

            <AppGoogleLoginButton class="w-full" />
        </CardContent>

        <CardFooter class="justify-center text-sm text-muted-foreground">
            Already have an account?
            <RouterLink :to="{ name: 'login' }" class="ml-1 text-foreground hover:underline">
                Log in
            </RouterLink>
        </CardFooter>
    </Card>
</template>