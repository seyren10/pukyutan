<script setup lang="ts">
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useQuery } from '@tanstack/vue-query'
import { formatDistanceToNow } from 'date-fns'
import {
    DropdownMenu,
    DropdownMenuTrigger,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu'
import { Button } from '@/components/ui/button'
import { ScrollArea } from '@/components/ui/scroll-area'
import { Empty, EmptyHeader, EmptyMedia, EmptyTitle, EmptyDescription } from '@/components/ui/empty'
import { Bell, BellOff } from '@lucide/vue'
import { getNotificationsQueryOptions } from '@/features/notification/query'
import { useMarkNotificationReadMutation } from '@/features/notification/composables/use-mark-notification-read-mutation'
import { useMarkAllNotificationsReadMutation } from '@/features/notification/composables/use-mark-all-notifications-read-mutation'
import { describeNotification } from '@/features/notification/notificationVisual'
import type { AppNotification } from '@/features/notification/type'

const router = useRouter()
const route = useRoute()

const { data } = useQuery(getNotificationsQueryOptions())
const notifications = computed(() => data.value?.data ?? [])
const unreadCount = computed(() => notifications.value.filter((n) => !n.read_at).length)

const { mutate: markReadMutate } = useMarkNotificationReadMutation()
const { mutate: markAllReadMutate, isPending: isMarkingAll } = useMarkAllNotificationsReadMutation()

const handleSelect = (notification: AppNotification) => {
    if (!notification.read_at) markReadMutate(notification.id)

    const { to } = describeNotification(notification)
    if (to) {
        if (route.matched.some(m => m.name === to.name)) {
            router.replace(to)
        } else
            router.push(to)
    }
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" size="icon" class="relative rounded-full">
                <Bell />
                <span v-if="unreadCount > 0"
                    class="absolute -right-0.5 -top-0.5 flex size-4 items-center justify-center rounded-full bg-primary font-mono text-[10px] font-medium text-primary-foreground">
                    {{ unreadCount > 9 ? '9+' : unreadCount }}
                </span>
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end" class="w-80 p-0">
            <div class="flex items-center justify-between px-3 py-2.5 sticky top-0 bg-background z-10">
                <DropdownMenuLabel class="p-0 font-heading">Notifications</DropdownMenuLabel>
                <Button v-if="unreadCount > 0" variant="link" size="sm" class="h-auto p-0 text-xs"
                    :disabled="isMarkingAll" @click="markAllReadMutate()">
                    Mark all as read
                </Button>
            </div>
            <DropdownMenuSeparator class="m-0" />

            <Empty v-if="!notifications.length" class="py-8">
                <EmptyHeader>
                    <EmptyMedia variant="icon">
                        <BellOff />
                    </EmptyMedia>
                    <EmptyTitle>No notifications</EmptyTitle>
                    <EmptyDescription>You're all caught up.</EmptyDescription>
                </EmptyHeader>
            </Empty>

            <ScrollArea v-else>
                <ul class="max-h-80">
                    <DropdownMenuItem v-for="notification in notifications" :key="notification.id"
                        class="items-start gap-2.5 whitespace-normal py-2.5"
                        :class="{ 'bg-accent/25': !notification.read_at }" @select="handleSelect(notification)">
                        <div
                            class="mt-0.5 flex size-7 shrink-0 items-center justify-center rounded-full bg-accent text-accent-foreground">
                            <component :is="describeNotification(notification).icon" class="size-3.5" />
                        </div>
                        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <p class="text-sm leading-snug text-foreground">{{ describeNotification(notification).text
                                }}
                            </p>
                            <span class="font-mono text-xs text-muted-foreground">
                                {{ formatDistanceToNow(new Date(notification.created_at), { addSuffix: true }) }}
                            </span>
                        </div>
                        <span v-if="!notification.read_at" class="mt-1.5 size-1.5 shrink-0 rounded-full bg-primary" />
                    </DropdownMenuItem>
                </ul>
            </ScrollArea>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
