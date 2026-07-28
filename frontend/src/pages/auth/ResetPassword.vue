<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { Card, CardHeader, CardTitle, CardDescription, CardContent, CardFooter } from '@/components/ui/card'
import { FormField, FormItem, FormLabel, FormControl, FormDescription, FormMessage } from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { KeyRound, ArrowLeft } from '@lucide/vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { resetPasswordSchema } from '@/features/auth/schema'
import { useAuthMutations } from '@/features/auth/mutations'
import AppButtonLoaderSwap from '@/components/app/AppButtonLoaderSwap.vue'

const { token } = defineProps<{
    token: string
}>()
const route = useRoute()

const { passwordResetMutation } = useAuthMutations()
const { mutate, isPending } = passwordResetMutation
const email = computed(() => (route.query.email as string) ?? '')

const { handleSubmit } = useForm({
    validationSchema: toTypedSchema(resetPasswordSchema),
    initialValues: {
        email: email.value,
        token
    }
})

const submit = handleSubmit((v) => mutate(v))
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="font-heading text-xl">Set a new password</CardTitle>
            <CardDescription>Choose a new password for your account.</CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit="submit" class="flex flex-col gap-6">
                <FormField v-slot="{ componentField }" name="email">
                    <FormItem>
                        <FormLabel>Email</FormLabel>
                        <FormControl>
                            <Input type="email" :model-value="email" readonly disabled v-bind="componentField" />
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
                        <FormDescription>At least 8 characters.</FormDescription>
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

                <Button type="submit" class="w-full" :disabled="isPending">
                    <AppButtonLoaderSwap :loading="isPending">
                        <KeyRound data-icon="inline-start" />
                    </AppButtonLoaderSwap>
                    Reset password
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
</template>