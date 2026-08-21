<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuTrigger,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu'
import { Search, User, LogOut } from '@lucide/vue'
import AppModeToggler from '@/components/app/AppModeToggler.vue'
import AppNotificationBell from '@/components/app/AppNotificationBell.vue'
import GroupSearchDialog from '@/components/groups/dialogs/GroupSearchDialog.vue'
import Logo from '@/assets/logo.svg'
import { useUserStore } from '@/stores/user'
import { storeToRefs } from 'pinia'
import { useMutation } from '@tanstack/vue-query'
import { logout } from '@/features/auth/api'
import { useLogout } from '@/composables/use-logout'
import { ref } from 'vue'
import { Kbd } from '@/components/ui/kbd'

const showSearchDialog = ref(false)

const userStore = useUserStore()
const { userInitials, user, isLoggedIn } = storeToRefs(userStore)

const navItems = [
  { label: 'Dashboard', to: { name: 'groups.index' } },
  { label: 'Shared Groups', to: { name: 'shared-groups.index' } },
  { label: 'Activities', to: { name: 'activities.index' } },
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
          <RouterLink v-for="(item, idx) in navItems" :key="idx" :to="item.to"
            class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground"
            active-class="text-foreground font-bold! text-foreground!">
            {{ item.label }}
          </RouterLink>
        </nav>

        <div class="flex items-center gap-3">
          <button type="button" @click="showSearchDialog = true"
            class="hidden items-center gap-2 rounded-md border border-input bg-background px-3 py-1.5 text-sm text-muted-foreground shadow-xs transition-colors hover:bg-accent/40 hover:text-foreground lg:flex lg:w-56">
            <Search class="size-4 shrink-0" />
            <span class="flex-1 text-left">Search groups...</span>
            <Kbd class="hidden xl:flex">
              ⌘K
            </Kbd>
          </button>
          <button type="button" @click="showSearchDialog = true" aria-label="Search groups"
            class="flex size-9 items-center justify-center rounded-md text-muted-foreground transition-colors hover:bg-accent/40 hover:text-foreground lg:hidden">
            <Search class="size-4.5" />
          </button>

          <AppNotificationBell />
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

    <GroupSearchDialog v-model="showSearchDialog" />
  </div>
</template>