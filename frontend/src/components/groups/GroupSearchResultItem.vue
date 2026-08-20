<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { CommandItem } from '@/components/ui/command'
import { GROUP_STATUS_META } from '@/features/group/constant'
import { useGroup } from '@/features/group/composables/use-group'
import type { Group } from '@/features/group/type'
import { getInitials } from '@/lib/helpers'

const { group } = defineProps<{
    group: Group
}>()
const emit = defineEmits<{
    (e: 'select'): void
}>()

const { name, status, frequencyLabel, contributionAmount, membersCount } = useGroup(() => group)
</script>

<template>
    <CommandItem :value="`group-${group.id}`" class="items-center gap-3 py-2.5" @select="emit('select')">
        <Avatar class="size-8 shrink-0">
            <AvatarFallback class="bg-accent text-xs text-accent-foreground">
                {{ getInitials(name) }}
            </AvatarFallback>
        </Avatar>

        <div class="flex min-w-0 flex-1 flex-col">
            <span class="truncate text-sm font-medium text-foreground">{{ name }}</span>
            <span class="truncate text-xs text-muted-foreground">
                ₱{{ contributionAmount }} · {{ frequencyLabel }} · {{ membersCount }} {{ membersCount === 1 ?
                    'member' : 'members' }}
            </span>
        </div>

        <Badge v-if="status" :variant="GROUP_STATUS_META[status].badgeVariant" class="shrink-0">
            {{ GROUP_STATUS_META[status].label }}
        </Badge>
    </CommandItem>
</template>