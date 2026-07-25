<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import AppModeToggler from '@/components/app/AppModeToggler.vue';
import Logo from '@/assets/mascot.png'
</script>

<template>
    <div class="min-h-screen bg-background">
        <header class="border-b border-border">
            <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
                <RouterLink to="/" class="flex items-center gap-2">
                    <img :src="Logo" class="size-7 text-primary" fill="currentColor" :stroke-width="0" />
                    <span class="font-heading text-xl font-semibold text-foreground">Pukyutan</span>
                </RouterLink>

                <div class="flex items-center gap-2">
                    <AppModeToggler />
                    <Avatar>
                        <AvatarFallback class="bg-primary text-primary-foreground">
                            RV
                        </AvatarFallback>
                    </Avatar>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-5xl px-6 py-8">
            <RouterView />
        </main>
    </div>
</template>