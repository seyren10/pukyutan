<script setup lang="ts">

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'

import { Input } from '@/components/ui/input'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '../ui/form'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { userInfoSchema } from '@/features/auth/schema'
import UnverifiedAlert from '@/pages/groups/index/components/UnverifiedAlert.vue'
import { Check, ShieldCheckIcon } from '@lucide/vue'
import { useAuthMutations } from '@/features/auth/mutations'
import type { LaravelError } from '@/types/common'
import AppButtonLoaderSwap from '../app/AppButtonLoaderSwap.vue'

const userStore = useUserStore()
const { user, isEmailVerified, isGoogleLoggedIn } = storeToRefs(userStore)
const { updateProfileMutation } = useAuthMutations()
const { mutate, isPending } = updateProfileMutation

const { handleSubmit, setErrors } = useForm({
    validationSchema: toTypedSchema(userInfoSchema),
    initialValues: {
        email: user.value?.email,
        name: user.value?.name
    }
})

const submit = handleSubmit((values) => mutate(values, {
    onError: (error) => {
        const err = error as LaravelError
        setErrors(err.response?.data.errors);
    }
}))
</script>

<template>
    <form @submit="submit" class="space-y-4">
        <FormField #="{ componentField }" name="name">
            <FormItem>
                <FormLabel>Name</FormLabel>
                <FormControl>
                    <Input v-bind="componentField" autocomplete="name" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <FormField #="{ componentField }" name="email" v-if="!isGoogleLoggedIn">
            <FormItem>
                <div class="flex items-center gap-2">
                    <FormLabel>Email</FormLabel>
                    <Badge v-if="isEmailVerified" variant="success">
                        <Check />
                        Verified
                    </Badge>
                </div>
                <FormControl>
                    <Input v-bind="componentField" type="email" />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>

        <div v-else class="flex flex-wrap items-center justify-between gap-2 rounded-lg border p-4">
            <div class="min-w-0 truncate font-medium text-xs">
                {{ user?.email }}
            </div>
            <div class="flex shrink-0 items-center gap-2">
                <ShieldCheckIcon class="size-4 text-muted-foreground" />
                <p class="text-sm text-muted-foreground">Signed in with</p>
                <Badge variant="secondary">Google</Badge>
            </div>
        </div>

        <UnverifiedAlert :verified="isEmailVerified" class="mt-4" />

        <Button type="submit">
            <AppButtonLoaderSwap :loading="isPending" />
            Save changes
        </Button>
    </form>
</template>