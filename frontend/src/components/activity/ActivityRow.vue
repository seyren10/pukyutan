<script setup lang="ts">
import { computed } from 'vue'
import { format } from 'date-fns'
import { Badge } from '@/components/ui/badge'
import { visualForEvent } from '../../pages/activity/event-visuals'
import type { UserActivity } from '@/features/activity/type'

const { activity } = defineProps<{
    activity: UserActivity
}>()

const visual = computed(() => visualForEvent(activity.event))
const time = computed(() => format(new Date(activity.created_at), 'h:mm a'))
</script>

<template>
    <li class="flex items-start gap-3 py-3 last:pb-0">
        <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-full" :class="visual.class">
            <component :is="visual.icon" class="size-4" />
        </div>

        <div class="flex flex-1 flex-col gap-1">
            <p class="text-sm text-foreground">{{ activity.description }}</p>
            <div class="flex items-center gap-2">
                <Badge v-if="activity.group_name" variant="outline" class="font-normal">
                    {{ activity.group_name }}
                </Badge>
                <span class="font-mono text-xs text-muted-foreground">{{ time }}</span>
            </div>
        </div>
    </li>
</template>