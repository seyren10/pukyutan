<script setup lang="ts">
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader } from '@/components/ui/card'
import GroupCardCycleVisual from '@/components/groups/GroupCard/GroupCardCycleVisual.vue'
import { HandCoins, CalendarClock } from '@lucide/vue'

const members = ['Aling Nena', 'Kuya Jun', 'Ate Rose', 'Tito Boy', 'Mang Dodong']
</script>

<template>
    <Card class="w-full max-w-sm shadow-lg transition-shadow duration-300 hover:shadow-xl">
        <CardHeader class="flex items-start justify-between gap-2">
            <div class="flex min-w-0 flex-col gap-1">
                <span class="font-heading text-base font-semibold text-foreground">Barangay Savers</span>
                <span class="text-xs text-muted-foreground">₱500 · Weekly</span>
            </div>
            <Badge variant="success" class="shrink-0">Active</Badge>
        </CardHeader>

        <CardContent class="flex flex-col gap-4">
            <div class="flex -space-x-2">
                <Avatar v-for="member in members" :key="member" class="size-8 border-2 border-card">
                    <AvatarFallback class="bg-accent text-[10px] text-accent-foreground">
                        {{member.split(' ').map(w => w[0]).join('')}}
                    </AvatarFallback>
                </Avatar>
                <div
                    class="flex size-8 items-center justify-center rounded-full border-2 border-card bg-muted text-[10px] font-medium text-muted-foreground">
                    +3
                </div>
            </div>

            <div class="flex items-center justify-between rounded-lg border border-border bg-muted/40 px-3 py-2">
                <span class="flex items-center gap-1.5 text-xs text-muted-foreground">
                    <CalendarClock class="size-3.5" />
                    Cycle progress
                </span>
                <GroupCardCycleVisual :current-cycle="4" :cycles-count="8" :max-cycle-display="8" />
            </div>

            <div class="flex items-center justify-between rounded-lg border border-dashed border-primary/40 bg-primary/10 px-3 py-2">
                <span class="flex items-center gap-1.5 text-xs font-medium text-foreground">
                    <HandCoins class="size-3.5" />
                    Next payout
                </span>
                <span class="text-xs font-medium text-foreground">Ate Rose · Fri</span>
            </div>
        </CardContent>
    </Card>
</template>