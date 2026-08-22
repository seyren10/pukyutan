<script setup lang="ts">
import { useUserStore } from '@/stores/user';
import { storeToRefs } from 'pinia';
import { ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '../ui/avatar';
import { Field, FieldDescription, FieldLabel, FieldSet } from '../ui/field/index.ts';
import { Loader, MousePointerClick, Shuffle } from '@lucide/vue';

const userStore = useUserStore()
const { user, userInitials } = storeToRefs(userStore)

const avatarUploading = ref(false)
</script>

<template>
    <Avatar v-if="!user">
        <AvatarFallback>-</AvatarFallback>
    </Avatar>
    <div v-else class="space-y-2">
        <FieldLabel>Avatar</FieldLabel>

        <div class="flex items-center gap-4">
            <div class="group relative rounded-full overflow-hidden">
                <Avatar class="size-16">
                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="user.name" />
                    <AvatarFallback>{{ userInitials }}</AvatarFallback>
                </Avatar>

                <span
                    class="absolute inset-0 bg-background/50 backdrop-blur-sm invisible group-hover:visible grid place-content-center">
                    <Shuffle class="size-4" />
                </span>
            </div>
            <div class="text-muted-foreground flex items-center gap-2">
                <MousePointerClick class="size-4" />
                <FieldDescription class="text-xs">Tap your avatar to change it</FieldDescription>
            </div>
        </div>
    </div>
</template>


<style scoped></style>