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
import { Search, Bell, User, LogOut } from '@lucide/vue'
import AppModeToggler from '@/components/app/AppModeToggler.vue'
import Logo from '@/assets/logo.svg'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import { useMutation } from '@tanstack/vue-query'
import { logout } from '@/features/auth/api'
import { useLogout } from '@/composables/use-logout'

const userStore = useUserStore()
const { userInitials, user, isLoggedIn } = storeToRefs(userStore)

const navItems = [
  { label: 'Dashboard', to: '/' },
  { label: 'Activity', to: '/activities' },
  { label: 'Settings', to: '/settings' },
]


const { execute } = useLogout()
const { mutate } = useMutation({
  mutationFn: () => logout(),
  onSuccess: () => {
    execute()
  }
})
</script>

<template>
  <div class="min-h-svh bg-muted/40">
    <div class="mx-auto max-w-6xl px-4 py-4 md:px-6">
      <header
        class="flex items-center justify-between gap-4 rounded-2xl border border-border bg-card px-4 py-3 shadow-sm">
        <div class="flex items-center gap-2">
          <img :src="Logo" class="size-10 fill-primary text-primary" />
          <span class="font-heading text-lg font-semibold text-foreground">Puyo</span>
        </div>

        <nav class="hidden items-center gap-6 md:flex">
          <RouterLink v-for="item in navItems" :key="item.to" :to="item.to"
            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
            exact-active-class="text-foreground font-bold!">
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

          <DropdownMenu v-if="isLoggedIn">
            <DropdownMenuTrigger as-child>
              <button class="rounded-full">
                <Avatar class="size-8">
                  <AvatarFallback class="bg-accent text-accent-foreground">
                    {{ userInitials }}
                  </AvatarFallback>
                </Avatar>
              </button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" class="w-56">
              <div class="flex gap-3 items-center p-2">
                <Avatar class="size-8 shrink-0">
                  <AvatarFallback class="bg-accent text-accent-foreground">
                    {{ userInitials }}
                  </AvatarFallback>
                </Avatar>
                <div class="flex flex-col text-sm text-muted-foreground w-40">
                  <span class="truncate">
                    {{ user?.name }}
                  </span>
                  <span class="truncate font-medium">
                    {{ user?.email }}
                  </span>
                </div>
              </div>
              <DropdownMenuSeparator />
              <DropdownMenuItem>
                <User data-icon="inline-start" />
                Profile
              </DropdownMenuItem>
              <DropdownMenuSeparator />
              <DropdownMenuItem variant="destructive" @select="mutate">
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