<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { User } from '@lucide/vue'
import { useNavItems } from '@/composables/use-nav-items'

const route = useRoute()
const navItems = useNavItems()

// Bottom nav gets a Profile tab of its own — on desktop that link lives in
// the avatar dropdown instead, since there's a dedicated avatar trigger to
// hang it off there and no such affordance in a bottom tab bar.
const tabs = computed(() => [
    ...navItems.map((item) => ({
        label: item.label === 'Shared Groups' ? 'Shared' : item.label === 'Activities' ? 'Activity' : item.label,
        to: item.to,
        icon: item.icon,
        active: route.matched.some((record) => record.name === (item.to as { name?: string }).name || item.matchNames?.includes(String(record.name))),
    })),
    {
        label: 'Profile',
        to: { name: 'profile' },
        icon: User,
        active: route.name === 'profile',
    },
])
</script>

<template>
    <nav
        class="fixed inset-x-0 bottom-0 z-40 border-t border-border bg-card/95 backdrop-blur-sm md:hidden"
        style="padding-bottom: env(safe-area-inset-bottom)" aria-label="Primary">
        <div class="mx-auto flex max-w-6xl items-stretch justify-around">
            <RouterLink v-for="tab in tabs" :key="tab.label" :to="tab.to"
                class="flex min-w-16 flex-1 flex-col items-center gap-1 py-2.5 text-muted-foreground transition-colors"
                :class="tab.active ? 'text-primary' : 'hover:text-foreground'">
                <component :is="tab.icon" class="size-5.5" :stroke-width="tab.active ? 2.5 : 2" />
                <span class="text-[11px] font-medium leading-none">{{ tab.label }}</span>
            </RouterLink>
        </div>
    </nav>
</template>