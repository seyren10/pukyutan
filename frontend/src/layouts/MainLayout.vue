<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu'
import { Hexagon, Search, Bell, User, LogOut } from '@lucide/vue'
import AppModeToggler from '@/components/app/AppModeToggler.vue'
import Logo from '@/assets/mascot.png'
withDefaults(
  defineProps<{
    userName?: string
    userInitials?: string
  }>(),
  {
    userName: 'Roy',
    userInitials: 'RG',
  },
)

const navItems = [
  { label: 'Dashboard', to: '/' },
  { label: 'Activity', to: '/activity' },
  { label: 'Settings', to: '/settings' },
]
</script>

<template>
  <div class="min-h-svh bg-muted/40">
    <div class="mx-auto max-w-6xl px-4 py-4 md:px-6">
      <header
        class="flex items-center justify-between gap-4 rounded-2xl border border-border bg-card px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2">
          <img :src="Logo" class="size-6 fill-primary text-primary" />
          <span class="font-heading text-lg font-semibold text-foreground">Pukyutan</span>
        </div>

        <nav class="hidden items-center gap-6 md:flex">
          <RouterLink v-for="item in navItems" :key="item.to" :to="item.to"
            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
            active-class="text-foreground font-bold!">
            {{ item.label }}
          </RouterLink>
        </nav>

        <div class="flex items-center gap-3">
          <div class="relative hidden lg:block">
            <Search class="absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
            <Input placeholder="Search groups..." class="w-56 pl-8" />
          </div>

          <Button variant="ghost" size="icon" class="rounded-full">
            <Bell />
          </Button>
          <AppModeToggler />

          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <button class="rounded-full">
                <Avatar class="size-8">
                  <AvatarFallback class="bg-accent text-accent-foreground">
                    {{ userInitials }}
                  </AvatarFallback>
                </Avatar>
              </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-48">
              <DropdownMenuItem>
                <User data-icon="inline-start" />
                Profile
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem variant="destructive">
                <LogOut data-icon="inline-start" />
                Log out
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </header>

      <main class="mt-4">
        <RouterView />
      </main>
    </div>
  </div>
</template>