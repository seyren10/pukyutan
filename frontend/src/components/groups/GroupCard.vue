<script setup lang="ts">
import { Card, CardHeader, CardContent, CardFooter } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Wallet, CalendarClock, Eye, ArrowRight, Hexagon } from '@lucide/vue'

type CycleState = 'disbursed' | 'due' | 'upcoming'

withDefaults(
    defineProps<{
        name: string
        status: 'draft' | 'active' | 'completed'
        cycles?: CycleState[]
        contributionAmount: number
        frequencyLabel: string
        nextDueLabel?: string
        memberInitials?: string[]
        sharedByName?: string
    }>(),
    {
        cycles: () => [],
        memberInitials: () => [],
        nextDueLabel: undefined,
        sharedByName: undefined,
    },
)
</script>

<template>
    <Card class="transition-shadow hover:shadow-md" :class="status === 'draft' ? 'border-dashed' : ''">
        <CardHeader class="flex flex-row items-start justify-between gap-2">
            <div class="flex flex-col gap-1">
                <span class="font-heading text-base font-semibold text-foreground">{{ name }}</span>
                <div class="flex -space-x-2">
                    <Avatar v-for="(initials, i) in memberInitials.slice(0, 4)" :key="i"
                        class="size-6 border-2 border-card">
                        <AvatarFallback class="bg-accent text-[10px] text-accent-foreground">
                            {{ initials }}
                        </AvatarFallback>
                    </Avatar>
                    <div v-if="memberInitials.length > 4"
                        class="flex size-6 items-center justify-center rounded-full border-2 border-card bg-muted text-[10px] text-muted-foreground">
                        +{{ memberInitials.length - 4 }}
                    </div>
                </div>
            </div>

            <Badge v-if="sharedByName" variant="outline" class="shrink-0 gap-1">
                <Eye class="size-3" />
                View only
            </Badge>
            <Badge v-else-if="status === 'active'" variant="success" class="shrink-0">Active</Badge>
            <Badge v-else-if="status === 'draft'" variant="accent" class="shrink-0">Draft</Badge>
            <Badge v-else variant="secondary" class="shrink-0">Completed</Badge>
        </CardHeader>

        <CardContent class="flex flex-col gap-3">
            <div class="flex gap-1">
                <Hexagon v-for="(cycle, i) in cycles" :key="i" class="size-[22px] shrink-0" :class="{
                    'fill-primary text-primary': cycle === 'disbursed',
                    'fill-transparent text-accent-foreground': cycle === 'due',
                    'fill-transparent text-border': cycle === 'upcoming',
                }" :stroke-width="cycle === 'due' ? 2.5 : 1.5" />
            </div>

            <div class="flex items-center justify-between font-mono text-xs text-muted-foreground">
                <span class="flex items-center gap-1.5">
                    <Wallet class="size-3.5" />
                    ₱{{ contributionAmount.toLocaleString() }} · {{ frequencyLabel }}
                </span>
                <span v-if="nextDueLabel" class="flex items-center gap-1.5">
                    <CalendarClock class="size-3.5" />
                    {{ nextDueLabel }}
                </span>
            </div>

            <p v-if="sharedByName" class="text-xs text-muted-foreground">
                Owned by {{ sharedByName }}
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