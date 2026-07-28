<script setup lang="ts">
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Eye, ArrowRight, Wallet, CalendarClock } from '@lucide/vue'
import type { Group } from '@/features/group/type'
import { computed } from 'vue'
import { formatFrequencyLabel, getInitials } from '@/lib/helpers'
import { GroupCycleVisual } from '.'
import { format } from 'date-fns'

const { group } = defineProps<{
    group: Group
}>()

const { status,
    name,
    recent_members,
    members_count,
    user,
    contribution_amount,
    cycles_count,
    next_cycle,
    frequency_unit,
    frequency_interval
} = group

const isShared = computed(() => user?.name)
</script>

<template>
    <Card class="transition-shadow hover:shadow-md" :class="status === 'draft' ? 'border-dashed' : ''">
        <CardHeader class="flex flex-row items-start justify-between gap-2">
            <div class="flex flex-col gap-1">
                <span class="font-heading text-base font-semibold text-foreground capitalize">{{ name }}</span>
                <div class="flex -space-x-2">
                    <Avatar v-for="(member, i) in recent_members.slice(0, 4)" :key="i"
                        class="size-6 border-2 border-card">
                        <AvatarFallback class="bg-accent text-[10px] text-accent-foreground">
                            {{ getInitials(member.name) }}
                        </AvatarFallback>
                    </Avatar>
                    <div v-if="members_count > 4"
                        class="flex size-6 items-center justify-center rounded-full border-2 border-card bg-muted text-[10px] text-muted-foreground">
                        +{{ members_count - 4 }}
                    </div>
                </div>
            </div>

            <Badge v-if="isShared" variant="outline" class="shrink-0 gap-1">
                <Eye class="size-3" />
                View only
            </Badge>
            <Badge v-else-if="status === 'active'" variant="success" class="shrink-0">Active</Badge>
            <Badge v-else-if="status === 'draft'" variant="accent" class="shrink-0">Draft</Badge>
            <Badge v-else variant="secondary" class="shrink-0">Completed</Badge>
        </CardHeader>

        <CardContent class="flex flex-col gap-3 grow">
            <GroupCycleVisual v-if="next_cycle" :current-cycle="next_cycle.cycle_number" :cycles-count="cycles_count" />

            <div class="flex items-center justify-between font-mono text-xs text-muted-foreground">
                <span class="flex items-center gap-1.5">
                    <Wallet class="size-3.5" />
                    ₱{{ contribution_amount.toLocaleString() }} · {{
                        formatFrequencyLabel(frequency_unit, frequency_interval) }}
                </span>
                <span v-if="next_cycle" class="flex items-center gap-1.5">
                    <CalendarClock class="size-3.5" />
                    Cycle {{ next_cycle.cycle_number }} · {{ format(next_cycle.due_date, 'MMM d') }}
                </span>
            </div>

            <p v-if="isShared" class="text-xs text-muted-foreground">
                Owned by {{ user?.name }}
            </p>
        </CardContent>

        <CardFooter>
            <Button variant="ghost" size="sm" class="ml-auto">
                View group
                <ArrowRight data-icon="inline-end" />
            </Button>
        </CardFooter>
    </Card>
</template>