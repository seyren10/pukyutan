<script setup lang="ts">
import { Button } from '@/components/ui/button'
import AppModeToggler from '@/components/app/AppModeToggler.vue'
import Logo from '@/assets/logo.svg'
</script>

<template>
    <div class="flex min-h-svh flex-col bg-background">
        <header class="sticky top-0 z-40 border-b border-border/60 bg-background/80 backdrop-blur-sm">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <RouterLink :to="{ name: 'landing' }" class="flex items-center gap-2">
                    <img :src="Logo" class="size-8" alt="Puyo" />
                    <span class="font-heading text-lg font-semibold text-foreground">Puyo</span>
                </RouterLink>

                <nav class="hidden items-center gap-8 md:flex">
                    <a href="#features"
                        class="text-sm text-muted-foreground transition-colors hover:text-foreground">Features</a>
                    <a href="#how-it-works"
                        class="text-sm text-muted-foreground transition-colors hover:text-foreground">How it
                        works</a>
                    <a href="#faq" class="text-sm text-muted-foreground transition-colors hover:text-foreground">FAQ</a>
                </nav>

                <div class="flex items-center gap-2">
                    <AppModeToggler />
                    <Button as-child variant="ghost" size="sm" class="hidden sm:inline-flex">
                        <RouterLink :to="{ name: 'login' }">Log in</RouterLink>
                    </Button>
                    <Button as-child size="sm">
                        <RouterLink :to="{ name: 'register' }">Get started</RouterLink>
                    </Button>
                </div>
            </div>
        </header>

        <main class="flex-1">
            <RouterView />
        </main>

        <footer class="border-t border-border/60">
            <div
                class="mx-auto flex max-w-6xl flex-col items-center gap-4 px-4 py-8 text-sm text-muted-foreground sm:flex-row sm:justify-between sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <img :src="Logo" class="size-5" alt="Puyo" />
                    <span class="font-heading font-medium text-foreground">Puyo</span>
                </div>
                <p>&copy; {{ new Date().getFullYear() }} Puyo. Built for paluwagan groups everywhere.</p>
            </div>
        </footer>
    </div>
</template>