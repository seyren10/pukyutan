<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { storeToRefs } from 'pinia';
import { Avatar, AvatarFallback } from '../ui/avatar';
import { FieldDescription, FieldLabel } from '../ui/field/index.ts';
import { MousePointerClick } from '@lucide/vue';
import AppAvatar from '../app/AppAvatar.vue';
import { useMutation } from '@tanstack/vue-query';
import { deleteDicebearSeed, seedDicebear } from '@/features/auth/api.ts';



const userStore = useUserStore()
const { user } = storeToRefs(userStore)

const { mutate: seedMutate } = useMutation({ mutationFn: seedDicebear })
const { mutate: unseedMutate } = useMutation({ mutationFn: deleteDicebearSeed })

const handleSeed = (cb: () => void) => {
    seedMutate(undefined, {
        onSuccess: (user) => userStore.setUser(user),
        onSettled: () => cb()
    })
}

const handleUnseed = (cb: () => void) => {
    unseedMutate(undefined, {
        onSuccess: (user) => userStore.setUser(user),
        onSettled: () => cb()
    })
}
</script>

<template>
    <Avatar v-if="!user">
        <AvatarFallback>-</AvatarFallback>
    </Avatar>
    <div v-else class="space-y-2">
        <FieldLabel>Avatar</FieldLabel>

        <div class="flex items-center gap-4">
            <AppAvatar editable :google-avatar="user.avatar" :fallback="user.name" :seed="user.dicebear_seed"
                @seed="handleSeed" @unseed="handleUnseed" />

            <div class="text-muted-foreground flex items-center gap-2">
                <MousePointerClick class="size-4" />
                <FieldDescription class="text-xs">Tap your avatar to change it</FieldDescription>
            </div>
        </div>
    </div>
</template>


<style scoped></style>